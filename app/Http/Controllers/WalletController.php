<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Log;
use App\Models\WalletEnv;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

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
                $response = Http::get("https://styx.imgai.us/api/tatum/v3/{$chain}/wallet");
                $data = $response->json();
                $mnemonic = $data['mnemonic'];
                $xpub = $data['xpub'];
                $WalletEnv = new WalletEnv();
                $WalletEnv->chain = $chain;
                $WalletEnv->xpub = $xpub;
                $WalletEnv->mnemonic = $mnemonic;
                $WalletEnv->save();
            }
        }
    }

    public function test()
    {
        $wallet_address = '0x6840BFF96C33161BA0eD7d2c765555a1d6751b57';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api.imgai.us/api/alchemy/tokens/human', [
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
        return view('guest.wallet-selection');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('guest.create-new-wallet');
    }

    public function wallet_pin_set(Request $request)
    {
        $wallet_name = $request->wallet_name;
        return view('guest.wallet-pin-set', compact('wallet_name'));
    }

    public function wallet_pin_confirm(Request $request)
    {
        $wallet_pin = $request->wallet_pin;
        return view('guest.wallet-pin-set-confirm', compact('wallet_pin'));
    }

    public function word_seed_phrase(Request $request)
    {
        $wallet_pin = $request->wallet_pin;
        $wallet_pin_confirm = $request->wallet_pin_confirm;

        if ($wallet_pin == $wallet_pin_confirm) {
            $response = Http::get('https://api.imgai.us/api/mnemonic/new');
            $data = $response->json(); // Decodes JSON into an array
            $mnemonic12 = $data['mnemonic12'];
            $mnemonic24 = $data['mnemonic24'];
            $words = explode(" ", $mnemonic12);
            return view('guest.word-seed-phrase', compact('wallet_pin', 'words', 'mnemonic12', 'mnemonic24'));
        }
    }

    public function download_seed_phrase(Request $request)
    {
        $wallet_pin = $request->wallet_pin;
        $phrase = $request->phrase;
        return view('guest.download-phrase', compact('wallet_pin', 'phrase'));
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
        return view('guest.wallet-restore');
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

        foreach ($tokens as $key => $token) {
            $totalUsd = $totalUsd + $token['tokenBalance'] * $token['usdUnitPrice'];
        }
        return view('wallet.dashboard', compact('title', 'tokens', 'totalUsd'));
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
        $response = Http::get("https://api.imgai.us/api/tatum/fees");
        $gasPrice = $response->json();
        $token = strtoupper($symbol);

        if (isset($gasPrice[$token])) {
            $gasPriceGwei = $gasPrice[$token]['slow']['native'];
            $gasPriceUsd = $gasPrice[$token]['slow']['usd'];
        } else {
            $gasPriceGwei = 0;
            $gasPriceUsd = 0;
        }

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
        $chain = $chainNames[$upperSymbol];
        $wallet = Wallet::where('user_id', $user_id)->where('chain', $chain)->first();
        if ($wallet == null) {
            if ($chain == 'xrp') {
                $response = Http::get("https://styx.pibin.workers.dev/api/tatum/v3/xrp/account");
                $data = $response->json();
                $address = $data['address'];
                $private_key = $data['secret'];
            } else {
                $env = WalletEnv::where('chain', $chain)->first();
                $xpub = $env->xpub;
                $response = Http::get("https://styx.imgai.us/api/tatum/v3/{$chain}/address/{$xpub}/{$user_id}");
                $data = $response->json();
                $address = $data['address'];

                $mnemonic = $env->mnemonic;
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://styx.pibin.workers.dev/api/tatum/v3/{$chain}/wallet/priv", [
                    "index" => $user_id,
                    "mnemonic" => $mnemonic
                ]);
                $data = $response->json();
                $private_key = $data['key'];
            }

            $newWallet = new Wallet();
            $newWallet->user_id = $user_id;
            $newWallet->name = $upperSymbol." Wallet";
            $newWallet->chain = $chain;
            $newWallet->address = $address;
            $newWallet->private_key = $private_key;
            $newWallet->save();
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
        $chain = $chainNames[$token];
        $user_id = Auth::user()->id;
        $wallet = Wallet::where('user_id', $user_id)->where('chain', $chain)->first();
        $sender_address = $wallet->address ?? null;
        $private_key = $wallet->private_key ?? null;
        $receiver_address = $request->token_address;
        $amount = $request->amount;
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api.imgai.us/api/quicknode/send', [
            'from' => $sender_address,
            'to' => $receiver_address,
            'amount' => $amount,
            'token' => $token,
            'privateKey' => $private_key,
        ]);

        $response = $response->json();

        $tokens = $balanceService->getFilteredTokens();
        $symbol = $token;
        $title = "Token Send Response";
        if (isset($response['error']) && $response['error'] != null) {
            $status = 'error';
            $details = $response['details'];
            $message = $response['error'];
            $db_res = $details;
        } elseif (isset($response['transactionHash']) && $response['transactionHash'] != null) {
            $status = $response['status'];
            $message = $response['message'];
            $details = '';
            $db_res = $message;
        }

        $log = new Log();
        $log->wallet_id = $wallet->id;
        $log->type = "Send";
        $log->from = $sender_address;
        $log->to = $receiver_address;
        $log->token = $token;
        $log->chain = $chain;
        $log->amount = $amount;
        $log->status = $status;
        $log->response = $db_res;
        $log->save();

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
            ->pluck('address') // only fetch the "address" column
            ->toArray();

        $allTransfers = [];

        foreach ($wallet_addresses as $address) {
            $url = "https://api.imgai.us/api/alchemy/" . $address;

            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['result']['transfers'])) {
                    $allTransfers = array_merge($allTransfers, $data['result']['transfers']);
                }
            }
        }

        // $allTransfers now contains merged transfers from all wallets
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
