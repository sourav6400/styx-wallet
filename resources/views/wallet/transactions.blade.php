@extends('layouts.app')
@section('content')
    @php
        $upperSymbol = strtoupper($symbol);
        
        // Helper functions
        function formatAddress($address) {
            return substr($address, 0, 10) . '...' . substr($address, -8);
        }
        
        function formatTimestamp($timestamp) {
			// If not numeric, assume it's a date string like "2025-11-17 04:22:17"
			if (!is_numeric($timestamp)) {
				$time = strtotime($timestamp);
				return date('M d, Y h:i A', $time);
			}

			// If numeric, check ms or sec
			$timestampSec = strlen((string)$timestamp) > 10 ? $timestamp / 1000 : $timestamp;

			return date('M d, Y h:i A', $timestampSec);
		}
    @endphp
    <div class="dashboardRightMain_body">
        
        <div class="transaction_body_wrapper">
			<div class="transaction_title v3">
				<h3>{{ $upperSymbol }} Transactions</h3>
				<select class="transaction_dropdown_v3" id="transactionFilter" onchange="handleFilterChange(this)">
				    <option value="" disabled selected>Filter By</option>
					<option value="btc">Bitcoin</option>
					<option value="eth">Ethereum</option>
					<option value="ltc">Litecoin</option>
					<option value="usdt">Tron</option>
					<option value="xrp">XRP</option>
					<option value="doge">Dogecoin</option>
					<option value="trx">Tron</option>
					<option value="bnb">BNB</option>
				</select>
			</div>
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

    <style>
        .transaction_dropdown_v3 {
            background: #1B1D2D;
            border: 0;
            color: #fff;
            font-size: 16px;
            padding: 14px 10px;
            border-radius: 5px;
            width: 100%;
            max-width: 140px;
            cursor: pointer;
        }
        .transaction_title.v3 {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        #dataTable {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            /* each column adjusts to its content */
        }

        #dataTable thead th {
            /*background: #f8f9fa;*/
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
            /* same as your inline */
        }
    </style>

    <script>
        function handleFilterChange(selectElement) {
            const filterValue = selectElement.value;
            const currentSymbol = '{{ $upperSymbol }}';
            
            // Construct the URL with the filter parameter
            // Adjust the route name according to your Laravel routes
            const url = `{{ url('transactions') }}/${filterValue}`;
            
            // Redirect to the new URL
            window.location.href = url;
        }

        // Set the selected filter on page load
        // document.addEventListener('DOMContentLoaded', function() {
        //     const urlParams = new URLSearchParams(window.location.search);
        //     const filterParam = urlParams.get('filter') || 'all';
        //     const selectElement = document.getElementById('transactionFilter');
            
        //     if (selectElement) {
        //         selectElement.value = filterParam;
        //     }
        // });

        function copyToClipboard(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const alertSpan = btn.parentElement.querySelector('.copy-alert');
                alertSpan.style.display = 'inline';
                setTimeout(() => {
                    alertSpan.style.display = 'none';
                }, 1500);
            });
        }
    </script>
@endsection