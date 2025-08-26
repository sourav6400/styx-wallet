@extends('layouts.app')
@section('content')
<div class="dashboardRightMain_body">
	<div class="support_body_wrapper">
		<h2>STYX Wallet Support</h2>
		<p>Keep your wallet secure and up to date. <br> Download the latest version now:</p>
		<a href="#">styxwallet.io/downloads</a>
		<div class="support_btn" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
			<img src="{{ asset('images/vector/vector12.png') }}" alt="">
			<span>Contact Support</span>
		</div>
		<h6>Anonymous STYX ID</h6>
		<h5>9cc46939a6c4f6...95933fe7ab3676</h5>
	</div>
</div>

<div class="modal newAccount fade supportForm_wrapper" id="staticBackdrop" data-bs-backdrop="static"
	data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="newAccount_popup_wrapper position-relative">
				<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><img
						src="images/icon/icon17.svg" alt=""></button>
				<div class="newAccountPopup_header">
					<h3>Contact Support</h3>
				</div>
				<form action="">
					<div class="row g-0 m-0">
						<div class="col-12">
							<div class="form_input">
								<input type="email" placeholder="Your Email">
							</div>
							<div class="form_input">
								<select>
									<option hidden>Select Subjects</option>
									<option value="Balance Issue">Balance Issue</option>
									<option value="Transaction issue (Deposit / Withdrawal)">Transaction issue (Deposit / Withdrawal)</option>
									<option value="Exchange">Exchange </option>
									<option value="Buy Crypto">Buy Crypto</option>
									<option value="Staking">Staking </option>
									<option value="Fee Question">Fee Question</option>
									<option value="Backup & Recovery">Backup & Recovery </option>
									<option value="Report a Bug, Security Issue, or Scam">Report a Bug, Security Issue, or Scam</option>
									<option value="Memes / Smart Contracts">Memes / Smart Contracts</option>
									<option value="Other Issues">Other Issues</option>
								</select>
							</div>
							<div class="form_input">
								<textarea placeholder="Describe your issues or share your ideas"></textarea>
							</div>
						</div>
						<div class="col-12">
							<div class="form_btn">
								<button type="button" class="changeName">Send</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection