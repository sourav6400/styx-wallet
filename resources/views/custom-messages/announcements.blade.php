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
		<title>Announcement - STYX</title>
		
		<!-- Fav Icon -->
		<link rel="icon" href="images/favicon.ico">
		
		<!-- Font Awesome Icon -->
		<link rel="stylesheet" href="./FontAwesome6Pro/css/all.min.css">

		<!-- Include Bootstrap -->
		<link rel="stylesheet" href="css/bootstrap.css">
		
		<!-- Main StyleSheet -->
		<link rel="stylesheet" href="style.css">	
		
		<!-- Responsive CSS -->
		<link rel="stylesheet" href="css/responsive.css">	
		
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
								<a href="#"><img src="images/logo/logo_main.svg" alt=""></a>
							</div>
							<div class="sideMenu_content">
								<ul>
									<li><a href="./dashboard.html"><i class="fa-solid fa-grid-2"></i> Dashboard</a></li>
									<li><a href="./my-wallet.html"><i class="fa-solid fa-wallet"></i> My wallet</a></li>
									<li><a href="./swap.html"><i class="fa-regular fa-shuffle"></i> swap</a></li>
									<li><a href="./transactions.html"><i class="fa-solid fa-file-invoice"></i> Transactions</a></li>
									<li><a href="./announcement.html" class="active"><i class="fa-solid fa-bullhorn"></i> Announcement</a></li>
									<li><a href="./alert.html"><i class="fa-solid fa-triangle-exclamation"></i> Alert</a></li>
									<li><a href="#"><i class="fa-solid fa-gear"></i> Settings</a></li>
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
											<div class="hamburger d-block d-lg-none align-self-center" id="hamburger-6" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
												<span class="line"></span>
												<span class="line"></span>
												<span class="line"></span>
											</div>
											<ul>
												<li><img class="logo" src="images/logo/logo_main.svg" alt=""></li>
												<li>
													<h6 class="name">Jhon Duo</h6>
													<h5 class="balance">0.00 USD</h5>
												</li>
											</ul>
											<button type="button" onclick="location.href='./wallet-selection.html'" class="drmh_addBtn d-none d-sm-block"><i class="fa-regular fa-plus"></i></button>
										</div>
									</div>
									<div class="col-md-5 col-2">
										<div class="dbrmh_right">
											<ul>
												<li>
													<div class="notification-container">
														<div class="notification-icon" id="notifBtn">
														<i class="fa-solid fa-bell"></i>
														<span class="notification-badge">4</span>
														</div>

														<div class="dropdown" id="notifDropdown">
														<div class="dropdown-header">Notifications</div>
														<div class="dropdown-content">

															<div class="notification-item">
																<div class="icon"><i class="fa-solid fa-wallet"></i></div>
																<div class="text">
																	<div class="title">You received 0.25 ETH</div>
																	<div class="time">2 mins ago</div>
																</div>
															</div>

															<div class="notification-item">
																<div class="icon"><i class="fa-solid fa-chart-line"></i></div>
																<div class="text">
																	<div class="title">BTC is up 5% today!</div>
																	<div class="time">15 mins ago</div>
																</div>
															</div>

															<div class="notification-item">
																<div class="icon"><i class="fa-solid fa-shield-halved"></i></div>
																<div class="text">
																	<div class="title">New login from Chrome (Dhaka)</div>
																	<div class="time">1 hour ago</div>
																</div>
															</div>

															<div class="notification-item">
																<div class="icon"><i class="fa-solid fa-coins"></i></div>
																<div class="text">
																	<div class="title">You staked 120 MATIC successfully</div>
																	<div class="time">3 hours ago</div>
																</div>
															</div>

														</div>
														<div class="dropdown-footer">View all</div>
														</div>
													</div>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
							
							<div class="dashboardRightMain_body">
								<div class="announcement-section">
									<div class="announcement-header">
										<h2>Announcements</h2>
										<button class="refresh-btn"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
									</div>

									<div class="announcement-list">

										<div class="announcement-card">
											<div class="icon"><i class="fa-solid fa-coins"></i></div>
											<div class="details">
												<h3>New Token Listing: ARX</h3>
												<p>We’re excited to announce that ARX Token is now live for trading on our platform. Trade pairs include ARX/USDT and ARX/BTC.</p>
												<span class="time">2 hours ago</span>
											</div>
										</div>

										<div class="announcement-card">
											<div class="icon"><i class="fa-solid fa-lock"></i></div>
											<div class="details">
												<h3>Security Update</h3>
												<p>We’ve upgraded our two-factor authentication system for enhanced wallet protection. Please re-login to activate the new settings.</p>
												<span class="time">Yesterday</span>
											</div>
										</div>

										<div class="announcement-card">
											<div class="icon"><i class="fa-solid fa-chart-line"></i></div>
											<div class="details">
												<h3>Market Insights – October 2025</h3>
												<p>Bitcoin shows strong bullish momentum while altcoins follow the uptrend. Check out our detailed market analysis inside the blog.</p>
												<span class="time">3 days ago</span>
											</div>
										</div>

										<div class="announcement-card">
											<div class="icon"><i class="fa-solid fa-gift"></i></div>
											<div class="details">
												<h3>Referral Bonus Extended!</h3>
												<p>Earn up to 30% commission on every friend’s trading fees until the end of this month. Invite now and boost your wallet balance!</p>
												<span class="time">5 days ago</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		
		<!-- offcanvas here -->
		<div class="offcanvas offcanvas-start" data-bs-scroll="false" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
			<aside class="mobile_sidebar">
				<div class="sideMenu_content pt-0">
					<ul>
						<li><a href="./dashboard.html"><i class="fa-solid fa-grid-2"></i> Dashboard</a></li>
						<li><a href="./my-wallet.html"><i class="fa-solid fa-wallet"></i> My wallet</a></li>
						<li><a href="./swap.html"><i class="fa-regular fa-shuffle"></i> swap</a></li>
						<li><a href="./transactions.html"><i class="fa-solid fa-file-invoice"></i> Transactions</a></li>
						<li><a href="./announcement.html" class="active"><i class="fa-solid fa-file-invoice"></i> Announcement</a></li>
						<li><a href="./alert.html"><i class="fa-solid fa-triangle-exclamation"></i> Alert</a></li>
						<li><a href="#"><i class="fa-solid fa-gear"></i> Settings</a></li>
					</ul>
				</div>
				<div class="drmhMobileBalace_content">
					<div class="dbrmh_right">
						<ul>
							<li class="d-none"><i class="fa-regular fa-bell"></i></li>
							<li>
								<div class="dropdown">
									<button class="dbrmhr_user" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
										<img class="icon" src="images/icon/icon2.svg" alt="">
										<span class="name">Jhon Duo</span>
									</button>
									<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
									  <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Change name</a></li>
									  <li><a class="dropdown-item" href="#">Settings</a></li>
									  <li><a class="dropdown-item logout" href="#">Logout <i class="fa-regular fa-right-from-bracket"></i></a></li>
									</ul>
								  </div>
							</li>
						</ul>
					</div>
				</div>
			</aside>	
		</div>

		<!-- popup 1 here -->
		<div class="modal newAccount fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="newAccount_popup_wrapper position-relative">
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><img src="images/icon/icon17.svg" alt=""></button>
						<div class="newAccountPopup_header">
							<img class="vector" src="images/vector/vector1.png" alt="">
							<h3>New account name</h3>
							<h6>Enter a new account name</h6>
						</div>
						<form action="">
							<div class="row g-2 m-0">
								<div class="col-12">
									<div class="form_input">
										<span>new account name</span>
										<input type="text" placeholder="Type a user name">
									</div>
								</div>
								<div class="col-6">
									<div class="form_btn">
										<button type="button" class="back" data-bs-dismiss="modal" aria-label="Close">Back</button>
									</div>
								</div>
								<div class="col-6">
									<div class="form_btn">
										<button type="button" class="changeName">Change name</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
  
		
		
		
		
		
		
		
		
		
		<!-- Main jQuery -->
		<script src="js/jquery-3.4.1.min.js"></script>
		
		<!-- Bootstrap.bundle Script -->
		<script src="js/bootstrap.bundle.min.js"></script>

		<!-- plugin script -->
		<script src="js/pie-chart.js"></script>
		<script src="js/apexcharts.js"></script>

		<!-- Custom jQuery -->
		<script src="js/scripts.js"></script>

		<script>
			function toggleTransactionDropdown(button) {
      const wrapper = button.closest('.transaction_filter');
      const dropdown = wrapper.querySelector('.dropdown-options');
      dropdown.classList.toggle('show');
    }

    function selectTransactionOption(option) {
      const wrapper = option.closest('.transaction_filter');
      const label = wrapper.querySelector('.filter-label');
      const button = wrapper.querySelector('.filter-button');
      const dropdown = wrapper.querySelector('.dropdown-options');

      label.textContent = option.textContent;

      wrapper.querySelectorAll('.dropdown-options div').forEach(opt => opt.classList.remove('active'));
      option.classList.add('active');

      button.classList.add('selected');

      dropdown.classList.remove('show');
    }

    document.addEventListener('click', function (e) {
      document.querySelectorAll('.transaction_filter').forEach(wrapper => {
        const dropdown = wrapper.querySelector('.dropdown-options');
        if (!wrapper.contains(e.target)) {
          dropdown.classList.remove('show');
        }
      });
    });


		</script>
	</body>
</html>