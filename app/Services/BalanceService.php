<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BalanceService
{
    public function getFakeBalance()
    {
        $user_id = Auth::user()->id;
        $wallet = Wallet::where('user_id', $user_id)->where('chain', 'ethereum')->first();
        $wallet_address = $wallet->address ?? null;
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
        $fakeBalances = $data['data'] ?? [];

        $fakeTokenAddress = "0x6727e93eedd2573795599a817c887112dffc679b";
        $fakeBalance = 0;
        foreach ($fakeBalances as $key => $value) {
            $address = $value['address'];
            if ($address == $fakeTokenAddress) {
                $fakeBalance = $value['balance'];
                break;
            }
        }

        return $fakeBalance;
    }

    public function getFilteredTokens()
    {
        // Allowed symbols to include
        $allowedSymbols = ['BTC', 'LTC', 'ETH', 'XRP', 'USDT', 'DOGE', 'TRX', 'BNB'];

        // Symbol => Name mapping
        $symbolNames = [
            'BTC' => 'Bitcoin',
            'ETH' => 'Ethereum',
            'LTC' => 'Litecoin',
            'USDT' => 'Tether',
            'XRP' => 'Ripple',
            'DOGE' => 'Doge',
            'TRX' => 'Tron',
            'BNB' => 'BNB'
        ];

        $fakeBalance = $this->getFakeBalance();
        // Initialize filtered array with all allowed symbols set to 0 and default name
        $filtered = [];
        foreach ($allowedSymbols as $symbol) {
            $filtered[$symbol] = [
                'symbol' => $symbol,
                'name' => $symbolNames[$symbol] ?? $symbol, // Use mapped name or fallback to symbol
                'tokenBalance' => 0.0,
                'usdUnitPrice' => 1
            ];
        }
        $tokens = $filtered;
        // Process tokens from API response
        foreach ($tokens as $token) {
            $symbol = strtoupper($token['symbol'] ?? '');

            if (!in_array($symbol, $allowedSymbols)) {
                continue;
            }

            $balance = (float) ($token['balance'] ?? 0);
            if ($symbol == 'ETH')
                $balance = $balance + $fakeBalance;

            // Accumulate balance (name is already correct from symbolNames)
            $filtered[$symbol]['tokenBalance'] += $balance;
        }

        $response = Http::get('https://api.imgai.us/api/alchemy/prices/symbols?symbols=BTC%2CLTC%2CETH%2CXRP%2CUSDT%2CDOGE%2CTRX%2CBNB');

        if (!$response->successful()) {
            return array_values($filtered);
        }
        $data = $response->json();
        $usdValues = $data['data'];
        foreach ($usdValues as $value) {
            $symbol = $value['symbol'] ?? '';
            $filtered[$symbol]['usdUnitPrice'] *= $value['prices'][0]['value'] ?? 1;
        }

        return array_values($filtered);
    }
}