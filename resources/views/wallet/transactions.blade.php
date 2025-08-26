@extends('layouts.app')
@section('content')
    <div class="dashboardRightMain_body">
        <div class="transaction_body_wrapper">
            <div class="transaction_title">
                <h3>Transactions</h3>
            </div>
            {{-- <div class="transaction_content_wrapper">
                <div class="transaction_header">
                    <div class="row m-0 g-3">
                        <div class="col-12">
                            <div class="transaction_search">
                                <input type="search" placeholder="Search by address, amount, or ID">
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

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
                                            $hash_short = substr($hash_full, 0, 10) . '...' . substr($hash_full, -8);
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
                                            $from_short = substr($from_full, 0, 10) . '...' . substr($from_full, -8);
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

    <script>
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
