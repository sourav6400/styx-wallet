@extends('layouts.app')
@section('content')
    <div class="dashboardRightMain_body p-0">
        <div class="myWallet_wrapper">
            <div class="myWallet_sidebar">
                @include('layouts.my-wallet-sidebar')
            </div>

            <div class="myWallet_body sendPopup1 ">
                @if ($status != 'error')
                    <div class="newAccount_popup_wrapper position-relative">
                        <div class="sucessfully_sent">
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
                                $icon = $iconMap[$token] ?? null;
                            @endphp
                            <h3>Send {{ $tokenName }} <span>{{ $token }}</span>
                                @if ($icon)
                                    <img src="{{ asset('images/icon/' . $icon) }}" alt="{{ $token }} icon">
                                @endif
                            </h3>
                            <img class="vector" src="{{ asset('images/vector/vector8.png') }}" alt="">
                            <span>Sucessfully sent {{ $amount }} {{ $token }}</span>
                            <span>Transaction ID: {{ $message }}</span>
                        </div>
                    </div>
                @else
                    <div class="newAccount_popup_wrapper position-relative">
                        <div class="sucessfully_sent">
                            <h3>{{ $message }}</h3>
                            <br>
                            <p>{{ $details }}</p>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
