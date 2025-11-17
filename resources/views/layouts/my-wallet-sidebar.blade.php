<div class="mobileWalletSidebar_toggle">
    <button type="button" class="walletSidebarToggleBtn" aria-expanded="false">
        <span>Assets</span>
        <i class="fa-solid fa-chevron-down toggle-icon"></i>
    </button>
</div>

<div class="myWallet_sidebar collapsible">
    {{-- <input type="text" placeholder="Search assets..."> --}}
    <div class="myWallet_sidebar_item">
        @foreach ($tokens as $token)
            <a href="{{ url('my-wallet/' . strtolower($token['symbol'])) }}">
                <ul  class="{{ $token['symbol'] === strtoupper($symbol) ? 'active' : '' }}">
                <li>
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
                        $tokenIcon = $iconMap[$token['symbol']] ?? null;
                    @endphp
                    @if ($tokenIcon)
                        <img src="{{ asset('images/icon/' . $tokenIcon) }}" alt="">
                    @endif
                </li>
                <li>
                    <h5>{{ $token['name'] }}</h5>
                    @php
                        $rawBalance = $token['tokenBalance'] ?? 0;
                        $numericBalance = is_numeric($rawBalance) ? (float) $rawBalance : 0;

                        if ($numericBalance < 1000) {
                            $displayBalance = number_format($numericBalance, 4, '.', '');
                        } else {
                            $units = ['K', 'M', 'B', 'T'];
                            $power = floor(log($numericBalance, 1000));
                            $unit = $units[$power - 1] ?? '';
                            $displayBalance = round($numericBalance / pow(1000, $power), 1) . $unit;
                        }
                    @endphp
                    <h6><span>{{ $displayBalance }}</span> {{ $token['symbol'] }}</h6>
                </li>
                </ul>
            </a>
        @endforeach

        {{-- <button type="button" class="addAssets"><img src="{{ asset('images/icon/icon9.svg') }}" alt=""> Add Asset</button> --}}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.myWallet_wrapper .myWallet_sidebar.collapsible');
        const toggleBtn = document.querySelector('.myWallet_wrapper .mobileWalletSidebar_toggle .walletSidebarToggleBtn');
        const toggleContainer = document.querySelector('.myWallet_wrapper .mobileWalletSidebar_toggle');

        if (!sidebar || !toggleBtn || !toggleContainer) {
            return;
        }

        const collapseSidebar = () => {
            sidebar.classList.add('collapsed');
            sidebar.classList.remove('expanded');
            toggleBtn.setAttribute('aria-expanded', 'false');
            toggleContainer.classList.remove('is-open');
        };

        const expandSidebar = () => {
            sidebar.classList.remove('collapsed');
            sidebar.classList.add('expanded');
            toggleBtn.setAttribute('aria-expanded', 'true');
            toggleContainer.classList.add('is-open');
        };

        const syncStateWithViewport = () => {
            if (window.innerWidth <= 767) {
                sidebar.classList.add('collapsible');
                if (!sidebar.classList.contains('expanded') && !sidebar.classList.contains('collapsed')) {
                    collapseSidebar();
                }
            } else {
                sidebar.classList.remove('collapsible', 'collapsed', 'expanded');
                toggleBtn.setAttribute('aria-expanded', 'true');
                toggleContainer.classList.remove('is-open');
            }
        };

        toggleBtn.addEventListener('click', () => {
            if (sidebar.classList.contains('collapsed')) {
                expandSidebar();
            } else {
                collapseSidebar();
            }
        });

        window.addEventListener('resize', syncStateWithViewport);
        syncStateWithViewport();
    });
</script>
