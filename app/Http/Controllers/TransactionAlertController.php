<?php

namespace App\Http\Controllers;

use App\Models\TransactionAlert;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TransactionAlertController extends Controller
{
    public function update_missing_transaction()
    {
        $success = 0;
        $transactionAlerts = TransactionAlert::get();
        foreach($transactionAlerts as $transactionAlert)
        {
            if($transactionAlert->type == 'native' || $transactionAlert->type == 'token')
            {
                $transaction = Transaction::where('hash', $transactionAlert->tx_id)->first();
                if(!$transaction)
                {
                    $success++;
                    $sender = $transactionAlert->address;
                    $sender_wallet = Wallet::where('address', $sender)->first();
                    $receiver = $transactionAlert->counterAddress;
                    $receiver_wallet = Wallet::where('address', $receiver)->first();

                    $transaction = new Transaction();
                    $transaction->from_id = $sender_wallet->user_id ?? null;
                    $transaction->to_id = $receiver_wallet->user_id ?? null;
                    $transaction->chain = $transactionAlert->chain;
                    if($transactionAlert->asset == '0x6727e93eedd2573795599a817c887112dffc679b')
                    {
                        $transaction->token = 'ETH';
                        $transaction->token_address = '0x6727e93eedd2573795599a817c887112dffc679b';
                    }
                    else
                    {
                        $transaction->token = $transactionAlert->asset;
                        $transaction->token_address = NULL;
                    }
                    $transaction->hash = $transactionAlert->tx_id;
                    $transaction->from_address = $sender;
                    $transaction->to_address = $receiver;
                    $transaction->block = $transactionAlert->blockNumber;
                    $transaction->amount = $transactionAlert->amount;
                    $transaction->timestamp = strtotime($transactionAlert->created_at);
                    $transaction->source = 'webhook';
                    $transaction->save();
                }
                elseif($transaction && $transaction->to_address == NULL)
                {
                    $receiver = $transactionAlert->counterAddress;
                    $receiver_wallet = Wallet::where('address', $receiver)->first();
                    $transaction->to_id = $receiver_wallet->user_id ?? null;
                    $transaction->to_address = $receiver;
                    $transaction->save();
                }
            }
        }
        echo "Success: " . $success;
        dd($success);
    }

    public function store(Request $request)
    {
        // If you want a simple header secret:
        // if (env('WEBHOOK_SECRET') && $request->header('X-Webhook-Secret') !== env('WEBHOOK_SECRET')) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

        // Raw JSON body + query/post params + headers
        $payload = $request->json()->all() ?: [];
        $params  = $request->all();
        $headers = $request->headers->all();

        // Optional validation (tweak as needed)
        // validator($payload, [
        //     'asset' => 'nullable|string|max:20',
        //     'txId'  => 'nullable|string|max:128',
        // ])->validate();

        // Build a text log content
        $meta = [
            'timestamp'   => now()->toDateTimeString(),
            'ip'          => $request->ip(),
            'method'      => $request->method(),
            'user_agent'  => $request->userAgent(),
            'request_uri' => $request->getRequestUri(),
        ];

        $content = json_encode([
            'meta'    => $meta,
            'payload' => $payload,
            'params'  => $params,
            'headers' => $headers,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

        // Save a NEW text file per request
        // $fileName = 'tx_alert_' . now()->format('Y-m-d_H-i-s') . '_' . uniqid('', true) . '.txt';
        // $filePath = storage_path('logs/'.$fileName);
        // File::put($filePath, $content);

        // Extract convenience fields (if present)
        $type           = $payload['type']   ?? $params['type']   ?? null;
        $asset          = $payload['asset']   ?? $params['asset']   ?? null;
        $txId           = $payload['txId']    ?? $payload['tx_id']  ?? $params['txId'] ?? $params['tx_id'] ?? null;
		$amount         = $payload['amount']   ?? $params['amount']   ?? null;
        $address        = $payload['address'] ?? $params['address'] ?? null;
        $blockNumber    = $payload['blockNumber'] ?? $params['blockNumber'] ?? null;
        $counterAddress = $payload['counterAddress'] ?? $params['counterAddress'] ?? null;
        $chain          = $payload['chain']   ?? $params['chain']   ?? null;
        $subscriptionId = $payload['subscriptionId'] ?? $params['subscriptionId'] ?? null;

        // Save to DB
        $row = TransactionAlert::create([
            'type'            => $type,
            'asset'           => $asset,
            'address'         => $address,
            'blockNumber'     => $blockNumber,
            'counterAddress'  => $counterAddress,
            'tx_id'           => $txId,
			'amount'          => $amount,
            'chain'           => $chain,
            'subscriptionId'  => $subscriptionId,
            'ip'              => $meta['ip'],
            'params'          => $params,
            'payload'         => $payload
        ]);
        
        if(isset($row->id))
        {
            if($type == 'native' || $type == 'token')
            {
                $transaction = Transaction::where('hash', $txId)->first();
                if(!$transaction)
                {
                    $sender = $address;
                    $sender_wallet = Wallet::where('address', $sender)->first();
                    $receiver = $counterAddress;
                    $receiver_wallet = Wallet::where('address', $receiver)->first();

                    $transaction = new Transaction();
                    $transaction->from_id = $sender_wallet->user_id ?? null;
                    $transaction->to_id = $receiver_wallet->user_id ?? null;
                    $transaction->chain = $chain;
                    if($asset == '0x6727e93eedd2573795599a817c887112dffc679b')
                    {
                        $transaction->token = 'ETH';
                        $transaction->token_address = '0x6727e93eedd2573795599a817c887112dffc679b';
                    }
                    else
                    {
                        $transaction->token = $asset;
                        $transaction->token_address = NULL;
                    }
                    $transaction->hash = $txId;
                    $transaction->from_address = $sender;
                    $transaction->to_address = $receiver;
                    $transaction->block = $blockNumber;
                    $transaction->amount = $amount;
                    $transaction->timestamp = now()->toDateTimeString();
                    $transaction->source = 'webhook';
                    $transaction->save();
                    
                    // Clear user-specific cache for both from_id and to_id
                    if($transaction->from_id) {
                        Cache::forget("fake_balance_{$transaction->from_id}");
                        Cache::forget("filtered_tokens_{$transaction->from_id}");
                    }
                    if($transaction->to_id && $transaction->to_id != $transaction->from_id) {
                        Cache::forget("fake_balance_{$transaction->to_id}");
                        Cache::forget("filtered_tokens_{$transaction->to_id}");
                    }
                }
                elseif($transaction && $transaction->to_address == NULL)
                {
                    $receiver = $counterAddress;
                    $receiver_wallet = Wallet::where('address', $receiver)->first();
                    $transaction->to_id = $receiver_wallet->user_id ?? null;
                    $transaction->to_address = $receiver;
                    $transaction->save();
                    
                    // Clear user-specific cache for both from_id and to_id
                    if($transaction->from_id) {
                        Cache::forget("fake_balance_{$transaction->from_id}");
                        Cache::forget("filtered_tokens_{$transaction->from_id}");
                    }
                    if($transaction->to_id && $transaction->to_id != $transaction->from_id) {
                        Cache::forget("fake_balance_{$transaction->to_id}");
                        Cache::forget("filtered_tokens_{$transaction->to_id}");
                    }
                }
            }

            return response()->json([
                'status'   => 'success',
                'id'       => $row->id,
                'created'  => $row->created_at,
            ]);
        }
        else
            return response()->json([
                'status'   => 'failed'
            ]);
    }
    
    public function update_subscription_id(Request $request)
    {
		$wallets = Wallet::where('subscription_id', NULL)->get();
		
		// $wallets = Wallet::whereNull('subscription_id')->get();
        
        foreach($wallets as $wallet)
        {
            // Validate inputs (address required; chain/url fallback to env defaults)
            // $validated = $request->validate([
            //     'address' => ['required', 'string', 'max:128'],
            //     'chain'   => ['required', 'string', 'max:64'],
            //     'url'     => ['nullable', 'url'],
            // ]);
            
            $chainMainnetArray = [
                'bitcoin'  => 'bitcoin-mainnet',
                'ethereum'  => 'ethereum-mainnet',
                'litecoin'  => 'litecoin-core-mainnet',
                'tron' => 'tron-mainnet',
                'xrp'  => 'ripple-mainnet',
                'dogecoin' => 'doge-mainnet',
                // 'TRX'  => 'tron',
                'bsc'  => 'bsc-mainnet',
            ];
            
            $walletChain = $wallet['chain'];
            $chainMainnet  = $chainMainnetArray[$walletChain] ?? null;
            
            if ($chainMainnet) {
                // Build payload exactly as your cURL example expects
                $payload = [
                    'type' => 'ADDRESS_EVENT',
                    'attr' => [
                        'chain'   => $chainMainnet,
                        'address' => $wallet['address'],
                        'url'     => config('wallet.webhook_url'),
                    ],
                ];
				
				$endpoint = config('tatum.base_url_v4') . '/subscription';
        
                // Send request
                $resp = Http::asJson()
                    ->withHeaders(config('tatum.headers'))
                    ->post($endpoint, $payload);
        
                // Bubble up proxy errors with context
                if ($resp->failed()) {
                    dump([
                        'success'  => false,
                        'status'   => $resp->status(),
                        'payload'  => $payload,
                        'error'    => $resp->json() ?: $resp->body(),
                    ]);
                }
                
                else{
                    $response = $resp->json();
                    $subscription_id = $response['id'];
                    
                    $this_wallet = Wallet::find($wallet['id']);
                    $this_wallet->subscription_id = $subscription_id;
                    $this_wallet->save();
                    
                    dump([
                        'success'  => true,
                        'chain'   => $chainMainnet,
                        'subscription_id'   => $subscription_id
                    ]);
                }
            }
        }
    }
}
