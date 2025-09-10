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
                    <div class="error_message">
                        <div class="error_icon">
                            <svg width="60" height="60" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" fill="#ff4444" stroke="#cc0000"
                                    stroke-width="2" />
                                <path d="M15 9l-6 6m0-6l6 6" stroke="white" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </div>
                        <h3 class="error_title">Transaction Failed</h3>
                        <div class="error_content">
                            <p class="error_message_text">{{ $message }}</p>
                            @if ($details)
                                <p class="error_details">{{ $details }}</p>
                            @endif
                        </div>
                        <div class="error_actions">
                            <a href="{{ route('wallet.send_token_s1', strtolower($token)) }}" class="btn btn-retry">
                                Try Again
                            </a>
                            <a href="{{ route('wallet.by_token', strtolower($token)) }}" class="btn btn-back">
                                Back to Wallet
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .error_message {
            text-align: center;
            padding: 40px 30px;
            background: linear-gradient(135deg, #ffebee 0%, #fce4ec 100%);
            border: 2px solid #ff5722;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(255, 68, 68, 0.15);
            max-width: 500px;
            margin: 0 auto;
        }

        .error_icon {
            margin-bottom: 20px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .error_title {
            color: #d32f2f;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .error_content {
            margin-bottom: 30px;
        }

        .error_message_text {
            color: #c62828;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .error_details {
            color: #666;
            font-size: 14px;
            line-height: 1.4;
            background: rgba(255, 255, 255, 0.7);
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #ff9800;
            margin-top: 15px;
        }

        .error_actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-retry {
            background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }

        .btn-retry:hover {
            background: linear-gradient(135deg, #ff5722 0%, #f57c00 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
            color: white;
        }

        .btn-back {
            background: #f5f5f5;
            color: #666;
            border: 2px solid #ddd;
        }

        .btn-back:hover {
            background: #e0e0e0;
            color: #333;
            transform: translateY(-1px);
        }

        /* Responsive design */
        @media (max-width: 480px) {
            .error_message {
                padding: 30px 20px;
                margin: 0 15px;
            }

            .error_actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection
