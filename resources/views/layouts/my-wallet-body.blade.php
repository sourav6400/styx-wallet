<div class="myWallet_body">
    <div class="myWallet_balance bitcoin">
        @foreach ($tokens as $token)
            @if ($token['symbol'] == strtoupper($symbol))
                {{-- Token Icon --}}
                @php
                    $iconMap = [
                        'BTC' => 'icon5.svg',
                        'LTC' => 'icon6.svg',
                        'ETH' => 'icon7.svg',
                        'XRP' => 'icon8.svg',
                        'USDT' => 'tether.svg',
                        'DOGE' => 'dodge.svg',
                        'TRX' => 'trx.svg',
                        'BNB' => 'icon_bnb.svg',
                    ];

                    $upperSymbol = strtoupper($symbol);
                    $icon = $iconMap[$upperSymbol] ?? null;
                @endphp

                @if ($icon)
                    <img src="{{ asset('images/icon/' . $icon) }}" alt="{{ $upperSymbol }} icon">
                @endif

                {{-- Balance & USD Value Calculation --}}
                @php
                    $tokenBalanceRaw = $token['tokenBalance'] ?? 0;
                    $unitPriceRaw = $token['usdUnitPrice'] ?? 0;

                    $tokenBalance = is_numeric($tokenBalanceRaw) ? (float) $tokenBalanceRaw : 0;
                    $usdUnitPrice = is_numeric($unitPriceRaw) ? (float) $unitPriceRaw : 0;

                    $formattedTokenBalance = number_format((float) $tokenBalance, 2, '.', ',');
                    $usdValue = $tokenBalance * $usdUnitPrice;
                    $formattedUsdValue = number_format((float) $usdValue, 2, '.', ',');
                @endphp

                <h2 class="balance">
                    {{ $formattedTokenBalance }} {{ $upperSymbol }}
                </h2>
                <h6 class="usd_balance">{{ $formattedUsdValue }} USD</h6>
            @endif
        @endforeach

        <ul>
            <a href="{{ url('send/' . $symbol) }}">
                <li><img src="{{ asset('images/icon/icon11.svg') }}" alt="">Send</li>
            </a>

            <a href="{{ url('receive/' . $symbol) }}">
                <li><img src="{{ asset('images/icon/icon12.svg') }}" alt=""> Receive</li>
            </a>
        </ul>

        <!-- transaction content here -->
        <div class="transaction_body_wrapper">
            <div class="transaction_title">
                <h3>Transactions</h3>
            </div>
            <!-- dynamic data here -->

            <div class="coinAssetTable_wrapper">
                <div class="coinAsset_table">
                    <div class="mt-4 mb-4">
                        <table id="dataTable" class="">
                            <thead>
                                <tr>
                                    <th><b>SN#</b></th>
                                    <th><b>Transaction Hash</b></th>
                                    <th><b>Block</b></th>
                                    <th><b>From</b></th>
                                    <th><b>To</b></th>
                                    <th><b>Amount</b></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($transfers as $key => $value)
                                    <tr>
                                        <td>
                                            <div class="value_data">
                                                <h5>{{ $key + 1 }}</h5>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $hash_full = $value['hash'];
                                                $hash_short =
                                                    substr($hash_full, 0, 10) . '...' . substr($hash_full, -8);
                                            @endphp
                                            <div class="value_data">
                                                <div
                                                    style="display: flex; justify-content: center; align-items: center; gap: 6px;">
                                                    <h5 style="align-items: center;">
                                                        {{ $hash_short }}</h5>
                                                    <button onclick="copyToClipboard('{{ $hash_full }}', this)"
                                                        style="border: none; background: none; padding: 0; cursor: pointer;"
                                                        title="Copy full address">
                                                        <i class="fas fa-copy" style="color: #ffc107;"></i>
                                                    </button>
                                                    <span class="copy-alert"
                                                        style="display: none; color: green; font-size: 0.8em;">Copied!</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="value_data">
                                                <h5>{{ $value['blockNum'] }}</h5>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $from_full = $value['from'];
                                                $from_short =
                                                    substr($from_full, 0, 10) . '...' . substr($from_full, -8);
                                            @endphp
                                            <div class="value_data">
                                                <div
                                                    style="display: flex; justify-content: center; align-items: center; gap: 6px;">
                                                    <h5 style="align-items: center;">
                                                        {{ $from_short }}</h5>
                                                    <button onclick="copyToClipboard('{{ $from_full }}', this)"
                                                        style="border: none; background: none; padding: 0; cursor: pointer;"
                                                        title="Copy full address">
                                                        <i class="fas fa-copy" style="color: #ffc107;"></i>
                                                    </button>
                                                    <span class="copy-alert"
                                                        style="display: none; color: green; font-size: 0.8em;">Copied!</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $to_full = $value['to'];
                                                $to_short = substr($to_full, 0, 10) . '...' . substr($to_full, -8);
                                            @endphp
                                            <div class="value_data">
                                                <div
                                                    style="display: flex; justify-content: center; align-items: center; gap: 6px;">
                                                    <h5 style="align-items: center;">
                                                        {{ $to_short }}</h5>
                                                    <button onclick="copyToClipboard('{{ $to_full }}', this)"
                                                        style="border: none; background: none; padding: 0; cursor: pointer;"
                                                        title="Copy full address">
                                                        <i class="fas fa-copy" style="color: #ffc107;"></i>
                                                    </button>
                                                    <span class="copy-alert"
                                                        style="display: none; color: green; font-size: 0.8em;">Copied!</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="value_data">
                                                <h5>{{ $value['value'] }} {{ $value['asset'] }}</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
