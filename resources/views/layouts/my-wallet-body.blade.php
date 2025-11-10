{{-- Extract reusable variables --}}
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
    $currentToken = collect($tokens)->firstWhere('symbol', $upperSymbol);
    
    // Helper functions
    function formatAddress($address) {
        return substr($address, 0, 10) . '...' . substr($address, -8);
    }
    
    function formatTimestamp($timestamp) {
        // Check if timestamp is in milliseconds (13 digits) or seconds (10 digits)
        $timestampSec = strlen((string)$timestamp) > 10 ? $timestamp / 1000 : $timestamp;
        return date('M d, Y h:i A', $timestampSec);
    }
@endphp

<div class="myWallet_body">
    <div class="myWallet_balance bitcoin">
        @if($currentToken)
            {{-- Token Icon --}}
            @if(isset($iconMap[$upperSymbol]))
                <img src="{{ asset('images/icon/' . $iconMap[$upperSymbol]) }}" alt="{{ $upperSymbol }} icon">
            @endif

            {{-- Balance & USD Value --}}
            @php
                $tokenBalance = (float) ($currentToken['tokenBalance'] ?? 0);
                $usdUnitPrice = (float) ($currentToken['usdUnitPrice'] ?? 0);
                $usdValue = $tokenBalance * $usdUnitPrice;
            @endphp

            <h2 class="balance">
                {{ number_format($tokenBalance, 4, '.', ',') }} {{ $upperSymbol }}
            </h2>
            <h6 class="usd_balance">{{ number_format($usdValue, 4, '.', ',') }} USD</h6>
        @endif

        <ul>
            <a href="{{ url('send/' . $symbol) }}">
                <li><img src="{{ asset('images/icon/icon11.svg') }}" alt="">Send</li>
            </a>
            <a href="{{ url('receive/' . $symbol) }}">
                <li><img src="{{ asset('images/icon/icon12.svg') }}" alt=""> Receive</li>
            </a>
        </ul>

        {{-- Transactions Section --}}
        <div class="transaction_body_wrapper">
            <div class="transaction_title">
                <h3>Transactions</h3>
            </div>

            <div class="coinAssetTable_wrapper">
                <div class="coinAsset_table">
                    <div class="mt-4 mb-4">
                        <table id="dataTable">
                            <thead>
                                <tr>
                                    <th>SL#</th>
                                    <th>Transaction Hash</th>
                                    <th>Block</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Amount</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $key=>$value)
									@php
										$hashShort = formatAddress($value->hash);
										$fromShort = formatAddress($value->from_address);
										$toShort = formatAddress($value->to_address);
										$dateTime = formatTimestamp($value->timestamp);

										// If you have variables like $hash, $from, $to, $blockNumber, etc.,
										// assign them from the object as well:
										$hash = $value->hash ?? '';
										$from = $value->from_address ?? '';
										$to = $value->to_address ?? '';
										$blockNumber = $value->block_number ?? '';
										$type = $value->type ?? '';
									@endphp
									<tr>
										<td><div class="value_data"><h5>{{ $key + 1 }}</h5></div></td>
										<td>
											<div class="value_data">
												<div class="flex-center">
													<h5>{{ $hashShort }}</h5>
													<button onclick="copyToClipboard('{{ $hash }}', this)" class="copy-btn" title="Copy full address">
														<i class="fas fa-copy"></i>
													</button>
													<span class="copy-alert">Copied!</span>
												</div>
											</div>
										</td>
										<td><div class="value_data"><h5>{{ $value->block }}</h5></div></td>
										<td>
											<div class="value_data">
												<div class="flex-center">
													<h5>{{ $fromShort }}</h5>
													<button onclick="copyToClipboard('{{ $from }}', this)" class="copy-btn" title="Copy full address">
														<i class="fas fa-copy"></i>
													</button>
													<span class="copy-alert">Copied!</span>
												</div>
											</div>
										</td>
										<td>
											<div class="value_data">
												<div class="flex-center">
													<h5>{{ $toShort }}</h5>
													<button onclick="copyToClipboard('{{ $to }}', this)" class="copy-btn" title="Copy full address">
														<i class="fas fa-copy"></i>
													</button>
													<span class="copy-alert">Copied!</span>
												</div>
											</div>
										</td>
										<td><div class="value_data"><h5>{{ number_format($value->amount, 6, '.', '') }} {{ $symbol }}</h5></div></td>
										<td><div class="value_data"><h5>{{ $dateTime }}</h5></div></td>
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

<style>
    #dataTable {
        width: 100%;
        border-collapse: collapse;
        table-layout: auto;
    }

    #dataTable thead th {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        padding: 10px;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }

    #dataTable tbody td {
        padding: 8px 10px;
        text-align: center;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
        white-space: nowrap;
    }

    #dataTable tbody tr:hover {
        background: #f1f3f5;
    }

    .value_data h5 {
        margin: 0;
        font-size: 14px;
        font-weight: 500;
        align-items: center;
    }

    .flex-center {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
    }

    .copy-alert {
        display: none;
        color: green;
        font-size: 0.8em;
    }

    .copy-btn {
        border: none;
        background: none;
        padding: 0;
        cursor: pointer;
    }

    .copy-btn i {
        color: #ffc107;
    }
</style>

<script>
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const alertSpan = btn.parentElement.querySelector('.copy-alert');
            alertSpan.style.display = 'inline';
            setTimeout(() => alertSpan.style.display = 'none', 1500);
        });
    }
</script>