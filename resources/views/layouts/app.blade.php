<!DOCTYPE html>
<html lang="en-US">

<head>
    <!-- Meta setup -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="">
    <meta name="decription" content="">
    <meta name="designer" content="">

    <!-- Title -->
    <title>{{ $title }} - STYX</title>

    <!-- Fav Icon -->
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="{{ asset('FontAwesome6Pro/css/all.min.css') }}">

    <!-- Include Bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">

    <!-- Main StyleSheet -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

</head>

<body>
    <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->

    <main class="dashboard_main">
        <div class="container-fluid m-0 p-0">
            <div class="row g-0 m-0">
                <!-- DASHBOARD ASIDE HERE -->
                <div class="col-3 aside_col">
                    <aside>
                        <div class="sideLogo">
                            <a href="#"><img src="{{ asset('images/logo/logo_main.svg') }}" alt=""></a>
                        </div>
                        <div class="sideMenu_content">
                            <ul>
                                <li>
                                    <a href="{{ route('dashboard') }}"
                                        class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                        <i class="fa-solid fa-grid-2"></i>
                                        Dashboard</a>
                                </li>
                                <li>
                                    <a href="{{ route('wallet.landing') }}"
                                        class="{{ request()->routeIs('wallet.*') ? 'active' : '' }}">
                                        <i class="fa-solid fa-wallet"></i> My wallet</a>
                                </li>
                                <li><a href="#"><i class="fa-regular fa-shuffle"></i> Swap</a></li>
                                <li><a href="{{ route('transactions') }}"
                                        class="{{ request()->routeIs('transactions') ? 'active' : '' }}"><i
                                            class="fa-solid fa-file-invoice"></i>
                                        Transactions</a></li>
                                <li><a href="{{ route('settings.backup_seed') }}"
                                        class="{{ request()->routeIs('settings.*') ? 'active' : '' }}"><i
                                            class="fa-solid fa-gear"></i> Settings</a></li>
                                <li><a href="{{ route('support') }}"
                                        class="{{ request()->routeIs('support') ? 'active' : '' }}">
                                        <i class="fa-solid fa-comment-question"></i> Support</a></li>
                            </ul>
                        </div>
                    </aside>
                </div>
                <!-- DASHBOARD RIGHT SIDE HERE -->
                <div class="col-9 dashboardRight_col">
                    <div class="dashboardRight_main">
                        <div class="dashboardRightMain_header">
                            <div class="row m-0 g-0 align-items-stretch">
                                <div class="col-md-7 col-10">
                                    <div class="dbrmh_left">
                                        <div class="hamburger d-block d-lg-none align-self-center" id="hamburger-6"
                                            data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling"
                                            aria-controls="offcanvasScrolling">
                                            <span class="line"></span>
                                            <span class="line"></span>
                                            <span class="line"></span>
                                        </div>
                                        <ul>
                                            <li><img class="logo" src="{{ asset('images/logo/logo_main.svg') }}"
                                                    alt=""></li>
                                            <li>
                                                <h6 class="name" id="username"></h6>
                                                @php
                                                    $totalUsd = 0;
                                                    foreach ($tokens as $key => $token) {
                                                        $totalUsd =
                                                            $totalUsd + $token['tokenBalance'] * $token['usdUnitPrice'];
                                                    }

                                                    $totalUsd = number_format((float) $totalUsd, 2, '.', ',');
                                                @endphp
                                                <h5 class="balance" id="totalBalance">
                                                    ${{ $totalUsd }}</h5>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-5 col-2">
                                    <div class="dbrmh_right">
                                        <ul>
                                            <!--<li><i class="fa-regular fa-bell"></i></li>-->
                                            <li class="d-none d-lg-flex">
                                                <div class="dropdown">
                                                    <button class="dbrmhr_user" type="button" id="dropdownMenuButton1"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <img class="icon" src="{{ asset('images/icon/icon2.svg') }}"
                                                            alt="">
                                                        {{-- <span class="name" id="username">{{ auth()->id() }}</span> --}}
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                        <li><a class="dropdown-item" href="#"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#staticBackdrop">Change Name</a></li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('settings.backup_seed') }}">Settings</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item logout" href="#"
                                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                                Logout <i class="fa-regular fa-right-from-bracket"></i>
                                                            </a>

                                                            <form id="logout-form" action="{{ route('logout') }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Main Content --}}
                        <div class="page_content">
                            @yield('content')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>


    <!-- offcanvas here -->
    <div class="offcanvas offcanvas-start" data-bs-scroll="false" data-bs-backdrop="false" tabindex="-1"
        id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
        <aside class="mobile_sidebar">
            <div class="sideMenu_content pt-0">
                <ul>
                    <li><a href="{{ route('dashboard') }}" class="active"><i class="fa-solid fa-grid-2"></i>
                            Dashboard</a></li>
                    <li><a href="{{ route('wallet.landing') }}"><i class="fa-solid fa-wallet"></i> My wallet</a></li>
                    <li><a href="#"><i class="fa-regular fa-shuffle"></i> Swap</a></li>
                    <li><a href="{{ route('transactions') }}"><i class="fa-solid fa-file-invoice"></i>
                            Transactions</a></li>
                    <li><a href="{{ route('settings.backup_seed') }}"><i class="fa-solid fa-gear"></i> Settings</a>
                    </li>
                </ul>
            </div>
            <div class="drmhMobileBalace_content">
                <div class="dbrmh_right">
                    <ul>
                        <li class="d-none"><i class="fa-regular fa-bell"></i></li>
                        <li>
                            <div class="dropdown">
                                <button class="dbrmhr_user" type="button" id="dropdownMenuButton1"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <img class="icon" src="images/icon/icon2.svg" alt="">
                                    {{-- <span class="name" id="username">{{ auth()->id() }}</span> --}}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop">Change Name</a></li>
                                    <li><a class="dropdown-item"
                                            href="{{ route('settings.backup_seed') }}">Settings</a></li>
                                    <li>
                                        <a class="dropdown-item logout" href="#"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Logout <i class="fa-regular fa-right-from-bracket"></i>
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
    </div>

    @if (session('unlocked'))
        <script>
            // Clear lock flag when PIN was just unlocked
            // localStorage.removeItem('app.locked');
        </script>
    @endif


    <!-- Main jQuery -->
    <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>

    <!-- Bootstrap.bundle Script -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <!-- plugin script -->
    <script src="{{ asset('js/pie-chart.js') }}"></script>

    <!-- Custom script -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    {{-- <script src="{{ asset('js/crypto-utils.js') }}"></script> --}}
    {{-- <script src="{{ asset('js/tatum-api.js') }}"></script> --}}
    {{-- <script src="{{ asset('js/main.js') }}"></script> --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
            $('#dataTable_filter input').attr('placeholder', 'Search here...');
        });

        // Lock Controll
        let isNavigating = false;

        // Detect when user clicks internal links
        document.addEventListener("click", function(e) {
            let target = e.target.closest("a");
            if (target && target.href && target.origin === window.location.origin) {
                isNavigating = true;
            }
        });

        // Detect when a form is submitted
        document.addEventListener("submit", function(e) {
            isNavigating = true;
        });

        window.addEventListener("beforeunload", function() {
            if (!isNavigating) {
                // Only lock if browser/tab is really closing
                localStorage.setItem('app.locked', '1');

                navigator.sendBeacon("{{ route('lock.store') }}", new URLSearchParams({
                    _token: "{{ csrf_token() }}"
                }));
            }
        });


        // Sleep Mode Controll
        // let isNavigating = false;

        // // Detect when user clicks internal links
        // document.addEventListener("click", function(e) {
        //     let target = e.target.closest("a");
        //     if (target && target.href && target.origin === window.location.origin) {
        //         isNavigating = true;
        //     }
        // });

        // window.addEventListener("beforeunload", function() {
        //     if (!isNavigating) {
        //         // Mark locked only if browser/tab is really closing
        //         localStorage.setItem('app.locked', '1');

        //         navigator.sendBeacon("{{ route('lock.store') }}", new URLSearchParams({
        //             _token: "{{ csrf_token() }}"
        //         }));
        //     }
        // });

        
        // (function() {
        //     const LOCK_KEY = 'app.locked';

        //     // Clear lock if just unlocked
        //     @if (session('unlocked'))
        //         localStorage.removeItem(LOCK_KEY);
        //     @endif

        //     // Check lock only if not just unlocked
        //     if (localStorage.getItem(LOCK_KEY) === '1') {
        //         window.location.href = "{{ route('lock.show') }}";
        //     }

        //     // Idle timer (optional)
        //     const IDLE_TIMEOUT = 5 * 60 * 1000; // 5 min
        //     let idleTimer;
        //     const resetIdle = () => {
        //         clearTimeout(idleTimer);
        //         idleTimer = setTimeout(() => {
        //             localStorage.setItem(LOCK_KEY, '1');
        //             fetch("{{ route('lock.store') }}", {
        //                 method: 'POST',
        //                 credentials: 'include',
        //                 keepalive: true,
        //                 headers: {
        //                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //                 }
        //             }).finally(() => {
        //                 window.location.href = "{{ route('lock.show') }}";
        //             });
        //         }, IDLE_TIMEOUT);
        //     };
        //     ['mousemove', 'keydown', 'scroll', 'touchstart'].forEach(evt => window.addEventListener(evt, resetIdle));
        //     resetIdle();

        //     // Cross-tab sync
        //     window.addEventListener('storage', function(e) {
        //         if (e.key === LOCK_KEY && e.newValue === '1') {
        //             window.location.href = "{{ route('lock.show') }}";
        //         }
        //     });
        // })();
    </script>

    <style>
        /*.form-select {*/
        /*    appearance: none;*/
        /*    -webkit-appearance: none;*/
        /*    -moz-appearance: none;*/

        /*    background-color: #151321;*/
        /*    color: white;*/

        /*    background-image: url("data:image/svg+xml;utf8,<svg fill='white' height='16' viewBox='0 0 24 24' width='16' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");*/
        /*    background-repeat: no-repeat;*/
        /*    background-position: right 0.75rem center;*/
        /*    background-size: 1rem;*/

        /*    padding-right: 2.5rem;*/
        /*    border: 1px solid #ccc;*/
        /*    border-radius: 4px;*/
        /*}*/

        /*#dataTable_filter label input {*/
        /*    background-color: #151321;*/
        /*    border: 1px solid #ccc;*/
        /*    color: white;*/
        /*    padding: 5px 10px;*/
        /*    border-radius: 4px;*/
        /*}*/

        #dataTable_previous a,
        #dataTable_next a {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .pagination .page-item.active .page-link {
            background-color: #3A326B;
            border-color: #ccc;
            color: white;
        }

        .pagination .page-item.active .page-link:hover {
            background-color: #45a049;
            border-color: #45a049;
            color: white;
        }

        #dataTable_previous.disabled a,
        #dataTable_next.disabled a {
            background-color: #151321;
            color: #ccc;
            border-color: #ccc;
        }
    </style>
</body>

</html>
