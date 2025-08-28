<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\TransactionLog;
use App\Models\WalletEnv;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function create_wallet_env()
    {
        $chainNames = [
            'bitcoin',
            'ethereum',
            'litecoin',
            'tron',
            'bsc',
            'dogecoin'
        ];

        foreach ($chainNames as $chain) {
            $env = WalletEnv::where('chain', $chain)->first();

            if (!$env) {
                try {
                    $response = Http::timeout(10) // max 10s wait
                        ->retry(3, 200)           // retry 3 times with 200ms gap
                        ->get("https://styx.pibin.workers.dev/api/tatum/v3/{$chain}/wallet");

                    if ($response->successful()) {
                        $data = $response->json();

                        // make sure required fields exist
                        $mnemonic = $data['mnemonic'] ?? null;
                        $xpub = $data['xpub'] ?? null;

                        if ($mnemonic && $xpub) {
                            $WalletEnv = new WalletEnv();
                            $WalletEnv->chain = $chain;
                            $WalletEnv->xpub = $xpub;
                            $WalletEnv->mnemonic = $mnemonic;
                            $WalletEnv->save();
                        } else {
                            Log::error("Wallet API response missing data for chain {$chain}");
                        }
                    } else {
                        Log::error("Wallet API responded with error for chain {$chain}");
                    }
                } catch (\Throwable $e) {
                    Log::error("Wallet API request failed for chain {$chain}: " . $e->getMessage());
                    continue; // move on to next chain
                }
            }
        }
    }

    public function test()
    {
        $wallet_address = '0x6840BFF96C33161BA0eD7d2c765555a1d6751b57';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://sns_erp.pibin.workers.dev/api/alchemy/tokens/human', [
            'addresses' => [
                [
                    'address' => $wallet_address,
                    'networks' => [
                        'eth-mainnet'
                    ]
                ]
            ]
        ]);

        // Output or use the response
        $data = $response->json();
        dd($data);
    }

    public function generateRandomWord($length = 4)
    {
        $letters = 'abcdefghijklmnopqrstuvwxyz';
        $word = '';
        for ($i = 0; $i < $length; $i++) {
            $word .= $letters[rand(0, strlen($letters) - 1)];
        }
        return $word;
    }

    public function printWord()
    {
        $words = [];
        for ($i = 0; $i < 12; $i++) {
            $words[] = $this->generateRandomWord();
        }

        print_r($words);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Wallet Selection";
        return view('guest.wallet-selection', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = "Wallet Selection";
        return view('guest.create-new-wallet', compact('title'));
    }

    public function wallet_pin_set(Request $request)
    {
        $title = "Wallet PIN Set";
        $wallet_name = $request->wallet_name;
        return view('guest.wallet-pin-set', compact('wallet_name', 'title'));
    }

    public function wallet_pin_confirm(Request $request)
    {
        $title = "Wallet PIN Confirm";
        $wallet_pin = $request->wallet_pin;
        return view('guest.wallet-pin-set-confirm', compact('wallet_pin', 'title'));
    }

    public function word_seed_phrase(Request $request)
    {
        $wallet_pin = $request->wallet_pin;
        $wallet_pin_confirm = $request->wallet_pin_confirm;

        if ($wallet_pin == $wallet_pin_confirm) {
            try {
                $response = Http::timeout(10) // max 10s
                    ->retry(3, 200)           // retry 3 times, 200ms gap
                    ->get('https://sns_erp.pibin.workers.dev/api/mnemonic/new');

                if ($response->successful()) {
                    $data = $response->json();

                    $mnemonic12 = $data['mnemonic12'] ?? null;
                    $mnemonic24 = $data['mnemonic24'] ?? null;

                    if ($mnemonic12 && $mnemonic24) {
                        $title = "Wallet Seed Phrase";
                        $words = explode(" ", $mnemonic12);
                        return view('guest.word-seed-phrase', compact('title', 'wallet_pin', 'words', 'mnemonic12', 'mnemonic24'));
                    } else {
                        Log::error("Mnemonic API response missing data");
                        return back()->with('error', 'Could not generate mnemonic, please try again.');
                    }
                } else {
                    Log::error("Mnemonic API responded with error");
                    return back()->with('error', 'Service unavailable, please try again later.');
                }
            } catch (\Throwable $e) {
                Log::error("Mnemonic API request failed: " . $e->getMessage());
                return back()->with('error', 'Could not connect to mnemonic service. Please try again later.');
            }
        }

        // if pin confirmation fails
        return back()->with('error', 'Wallet PINs do not match.');
    }

    public function download_seed_phrase(Request $request)
    {
        $title = "Download Phrase";
        $wallet_pin = $request->wallet_pin;
        $phrase = $request->phrase;
        return view('guest.download-phrase', compact('title', 'wallet_pin', 'phrase'));
    }

    public function store(Request $request)
    {
        $lastUsername = User::where('username', 'like', 'user_%')
            ->orderByRaw("CAST(SUBSTRING(username, 6) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->value('username');

        $nextNumber = 1;

        if ($lastUsername) {
            $numberPart = intval(substr($lastUsername, 5));
            $nextNumber = $numberPart + 1;
        }

        $username = 'user_' . $nextNumber;
        $wallet_pin = $request->wallet_pin;
        $user = User::create([
            'username' => $username,
            'password' => Hash::make('12345678'),
            'pin_hash' => Hash::make($wallet_pin), // 6-digit PIN
            'phrase12' => $request->phrase12,
            'phrase24' => $request->phrase24
        ]);

        if (isset($user->id)) {
            Auth::login($user, true);
            return redirect('/dashboard');
        }

        return response()->json(['error' => 'Something Went Wrong! Try again later.'], 500);
    }

    public function forward_to_restore_wallet(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/wallet-restore');
    }

    public function forward_to_create_wallet(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/wallet-pin-set');
    }

    public function restore(Request $request)
    {
        $title = "Wallet Restore";
        return view('guest.wallet-restore', compact('title'));
    }

    public function restorePost(Request $request)
    {
        $phrase = $request->wallet_phrase;
        $user = User::where('phrase12', $phrase)->first();

        if ($user) {
            Auth::login($user, true);
            return redirect()->route('dashboard');
        } else {
            return back()->withErrors([
                'not_found' => 'Your Wallet phrase is incorrect.',
            ]);
        }
    }

    public function dashboard(BalanceService $balanceService)
    {
        $title = "Dashboard";
        $tokens = $balanceService->getFilteredTokens();
        $totalUsd = 0;
        $totalCoin = 0;
        foreach ($tokens as $key => $token) {
            $totalCoin = $totalCoin + $token['tokenBalance'];
            $totalUsd = $totalUsd + $token['tokenBalance'] * $token['usdUnitPrice'];
        }
        return view('wallet.dashboard', compact('title', 'tokens', 'totalCoin', 'totalUsd'));
    }

    /**
     * Display the specified resource.
     */
    public function my_wallet(BalanceService $balanceService, $symbol = null)
    {
        $tokens = $balanceService->getFilteredTokens();

        if ($symbol == null)
            $symbol = "btc";

        $title = "My Wallet";
        $transfers = $this->get_transactions();
        return view('wallet.my-wallet', compact('title', 'tokens', 'symbol', 'transfers'));
    }

    public function send_view(BalanceService $balanceService, $symbol)
    {
        $this->wallet_info_update($symbol);
        $tokens = $balanceService->getFilteredTokens();
        $title = "Send Token";

        $gasPriceGwei = 0;
        $gasPriceUsd = 0;

        try {
            $response = Http::timeout(10) // max 10s
                ->retry(3, 200)           // retry 3 times, 200ms gap
                ->get("https://sns_erp.pibin.workers.dev/api/tatum/fees");

            if ($response->successful()) {
                $gasPrice = $response->json();
                $token = strtoupper($symbol);

                if (isset($gasPrice[$token])) {
                    $gasPriceGwei = $gasPrice[$token]['slow']['native'] ?? 0;
                    $gasPriceUsd = $gasPrice[$token]['slow']['usd'] ?? 0;
                }
            } else {
                Log::error("Tatum fees API responded with error for token {$symbol}");
            }
        } catch (\Throwable $e) {
            Log::error("Tatum fees API request failed for token {$symbol}: " . $e->getMessage());
        }

        // Render the view with gas price defaults even if API fails
        return view('wallet.send-token', compact('title', 'tokens', 'symbol', 'gasPriceGwei', 'gasPriceUsd'));
    }

    public function wallet_info_update($token)
    {
        $user_id = Auth::user()->id;
        $upperSymbol = strtoupper($token);

        $chainNames = [
            'BTC' => 'bitcoin',
            'ETH' => 'ethereum',
            'LTC' => 'litecoin',
            'USDT' => 'ethereum',
            'XRP' => 'xrp',
            'DOGE' => 'dogecoin',
            'TRX' => 'tron',
            'BNB' => 'bsc'
        ];

        $chain = $chainNames[$upperSymbol] ?? null;

        if (!$chain) {
            Log::error("Unknown token symbol: {$token}");
            return null; // or handle as needed
        }

        $wallet = Wallet::where('user_id', $user_id)
            ->where('chain', $chain)
            ->first();

        if ($wallet === null) {
            try {
                if ($chain === 'xrp') {
                    $response = Http::timeout(10)->retry(3, 200)
                        ->get("https://styx.pibin.workers.dev/api/tatum/v3/xrp/account");

                    if ($response->successful()) {
                        $data = $response->json();
                        $address = $data['address'] ?? null;
                        $private_key = $data['secret'] ?? null;
                    } else {
                        Log::error("XRP account API responded with error for user {$user_id}");
                        return null;
                    }
                } else {
                    $env = WalletEnv::where('chain', $chain)->first();

                    if (!$env) {
                        Log::error("Wallet environment not found for chain {$chain}");
                        return null;
                    }

                    $xpub = $env->xpub;
                    $response = Http::timeout(10)->retry(3, 200)
                        ->get("https://styx.pibin.workers.dev/api/tatum/v3/{$chain}/address/{$xpub}/{$user_id}");

                    if ($response->successful()) {
                        $data = $response->json();
                        $address = $data['address'] ?? null;
                    } else {
                        Log::error("Address API responded with error for chain {$chain}, user {$user_id}");
                        return null;
                    }

                    $mnemonic = $env->mnemonic;
                    $response = Http::timeout(10)->retry(3, 200)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post("https://styx.pibin.workers.dev/api/tatum/v3/{$chain}/wallet/priv", [
                            "index" => $user_id,
                            "mnemonic" => $mnemonic
                        ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $private_key = $data['key'] ?? null;
                    } else {
                        Log::error("Wallet priv API responded with error for chain {$chain}, user {$user_id}");
                        return null;
                    }
                }

                // Save wallet if both address and private key exist
                if ($address && $private_key) {
                    $newWallet = new Wallet();
                    $newWallet->user_id = $user_id;
                    $newWallet->name = $upperSymbol . " Wallet";
                    $newWallet->chain = $chain;
                    $newWallet->address = $address;
                    $newWallet->private_key = $private_key;
                    $newWallet->save();
                } else {
                    Log::error("Wallet creation failed for user {$user_id}, chain {$chain}: missing data");
                }
            } catch (\Throwable $e) {
                Log::error("Wallet API request failed for chain {$chain}, user {$user_id}: " . $e->getMessage());
            }
        }
    }
    public function send_token(Request $request, BalanceService $balanceService)
    {
        $token = $request->token;
        $chainNames = [
            'BTC' => 'bitcoin',
            'ETH' => 'ethereum',
            'LTC' => 'litecoin',
            'USDT' => 'ethereum',
            'XRP' => 'xrp',
            'DOGE' => 'dogecoin',
            'TRX' => 'tron',
            'BNB' => 'bsc'
        ];

        $chain = $chainNames[$token] ?? null;

        if (!$chain) {
            Log::error("Unknown token symbol: {$token}");
            return back()->with('error', 'Unknown token symbol.');
        }

        $user_id = Auth::user()->id;
        $wallet = Wallet::where('user_id', $user_id)
            ->where('chain', $chain)
            ->first();

        $sender_address = $wallet->address ?? null;
        $private_key = $wallet->private_key ?? null;
        $receiver_address = $request->token_address;
        $amount = $request->amount;

        $responseData = [];
        $status = 'error';
        $message = 'Service unavailable';
        $details = '';
        $db_res = '';

        try {
            $response = Http::timeout(10) // max 10 seconds
                ->retry(3, 200)           // retry 3 times, 200ms apart
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://sns_erp.pibin.workers.dev/api/quicknode/send', [
                    'from' => $sender_address,
                    'to' => $receiver_address,
                    'amount' => $amount,
                    'token' => $token,
                    'privateKey' => $private_key,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (!empty($responseData['error'])) {
                    $status = 'error';
                    $message = $responseData['error'];
                    $details = $responseData['details'] ?? '';
                    $db_res = $details;
                } elseif (!empty($responseData['transactionHash'])) {
                    $status = $responseData['status'] ?? 'success';
                    $message = $responseData['message'] ?? 'Transaction completed';
                    $details = '';
                    $db_res = $message;
                }
            } else {
                Log::error("Token send API responded with error for token {$token}, user {$user_id}");
            }
        } catch (\Throwable $e) {
            Log::error("Token send API request failed for token {$token}, user {$user_id}: " . $e->getMessage());
        }

        // Log transaction (even if API failed)
        $log = new TransactionLog();
        $log->wallet_id = $wallet->id ?? null;
        $log->type = "Send";
        $log->from = $sender_address;
        $log->to = $receiver_address;
        $log->token = $token;
        $log->chain = $chain;
        $log->amount = $amount;
        $log->status = $status;
        $log->response = $db_res;
        $log->save();

        // Render response view
        $tokens = $balanceService->getFilteredTokens();
        $symbol = $token;
        $title = "Token Send Response";

        return view('wallet.send-response', compact('title', 'amount', 'status', 'message', 'details', 'tokens', 'symbol'));
    }

    public function receive_token($symbol, BalanceService $balanceService)
    {
        $this->wallet_info_update($symbol);
        $upperSymbol = strtoupper($symbol);
        $chainNames = [
            'BTC' => 'bitcoin',
            'ETH' => 'ethereum',
            'LTC' => 'litecoin',
            'USDT' => 'ethereum',
            'XRP' => 'xrp',
            'DOGE' => 'dogecoin',
            'TRX' => 'tron',
            'BNB' => 'bsc'
        ];
        $chain = $chainNames[$upperSymbol];
        $user_id = Auth::user()->id;
        $wallet = Wallet::where('user_id', $user_id)->where('chain', $chain)->first();
        $wallet_address = $wallet->address ?? null;
        $tokens = $balanceService->getFilteredTokens();
        $title = "Receive Token";
        return view('wallet.receive-token', compact('title', 'symbol', 'tokens', 'wallet_address'));
    }

    public function transactions(BalanceService $balanceService)
    {
        $title = "Transactions";
        $tokens = $balanceService->getFilteredTokens();
        $transfers = $this->get_transactions();
        return view('wallet.transactions', compact('title', 'tokens', 'transfers'));
    }

    public function get_transactions()
    {
        $user_id = Auth::user()->id;
        $wallet_addresses = Wallet::where('user_id', $user_id)
            ->pluck('address') // only fetch "address" column
            ->toArray();

        $allTransfers = [];

        foreach ($wallet_addresses as $address) {
            $url = "https://sns_erp.pibin.workers.dev/api/alchemy/" . $address;

            try {
                $response = Http::timeout(10) // wait max 10 seconds
                    ->retry(3, 200)           // retry 3 times, wait 200ms between
                    ->get($url);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['result']['transfers'])) {
                        $allTransfers = array_merge(
                            $allTransfers,
                            $data['result']['transfers']
                        );
                    }
                } else {
                    Log::error("Alchemy transfers API responded with error for address {$address}");
                }
            } catch (\Throwable $e) {
                // Catch server down, timeout, connection issues etc.
                Log::error("Alchemy transfers API failed for address {$address}: " . $e->getMessage());
                continue; // move on to next wallet
            }
        }

        // $allTransfers now contains merged transfers from all wallets (even if some failed)
        return $allTransfers;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wallet $wallet)
    {
        //
    }
}
