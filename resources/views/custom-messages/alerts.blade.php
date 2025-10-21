@extends('layouts.app')
@section('content')
    <!-- DASHBOARD RIGHT SIDE HERE -->
    <div class="dashboardRight_main">
        <div class="dashboardRightMain_body p-0">
            <section class="alert-section">
                <div class="alert-header">
                    <h2>Alerts</h2>
                    <button class="clear-btn"><i class="fa-solid fa-trash"></i> Clear All</button>
                </div>

                <div class="alert-list">

                    <div class="alert-card warning">
                        <div class="icon"><i class="fa-solid fa-circle-exclamation"></i></div>
                        <div class="details">
                            <h3>Unusual Login Detected</h3>
                            <p>We noticed a login attempt from a new device (Chrome, Dhaka). If this
                                wasn’t you, please secure your wallet immediately.</p>
                            <span class="time">10 mins ago</span>
                        </div>
                    </div>

                    <div class="alert-card danger">
                        <div class="icon"><i class="fa-solid fa-lock"></i></div>
                        <div class="details">
                            <h3>2FA Disabled</h3>
                            <p>Your two-factor authentication has been turned off. We strongly recommend
                                enabling it for your account safety.</p>
                            <span class="time">1 hour ago</span>
                        </div>
                    </div>

                    <div class="alert-card info">
                        <div class="icon"><i class="fa-solid fa-bell"></i></div>
                        <div class="details">
                            <h3>Deposit Confirmed</h3>
                            <p>Your deposit of <b>0.15 ETH</b> has been successfully confirmed on the
                                blockchain.</p>
                            <span class="time">2 hours ago</span>
                        </div>
                    </div>

                    <div class="alert-card success">
                        <div class="icon"><i class="fa-solid fa-check"></i></div>
                        <div class="details">
                            <h3>Withdrawal Completed</h3>
                            <p>Your withdrawal of <b>150 USDT</b> has been processed successfully to
                                your connected wallet.</p>
                            <span class="time">Yesterday</span>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </div>
@endsection
