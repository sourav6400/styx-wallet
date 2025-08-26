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
                            <h3>Send Ethereum <span>ETH</span> <img src="{{ asset('images/icon/icon7.svg') }}"
                                    alt=""></h3>
                            <img class="vector" src="{{ asset('images/vector/vector8.png') }}" alt="">
                            <span>Sucessfully sent {{ $amount }} ETH</span>
                            <span>{{ $message }}</span>
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
