@extends('layouts.app')
@section('content')
<div class="dashboardRight_main">
	<div class="dashboardRightMain_body p-0">
		<div class="settingsMain_wrapper">
			<div class="settingsMain_header">
				<ul>
					<li><a href="{{ route('settings.backup_seed') }}" class="active">Private Keys</a></li>
					<li><a href="{{ route('settings.change_pin_view') }}">Security</a></li>
				</ul>
			</div>
			<div class="settingsFaq_wrapper privateKeyForm_wrapper">
				<p>Never share your 12-word backup phrase or private keys with anyone. Avoid entering your information on any web wallets, online forms, or websites impersonating STYX Wallet. Sharing this information puts your funds at risk of permanent loss.</p>
				<div class="createAnAccount_body">
					<form action="">
						<div class="form_input position-relative pt-4 mb-5">
							<input class="mb-0" type="password" id="password" value="" placeholder="PIN" minlength="6" maxlength="6" pattern="\d{6}" >
							<i class="toggle-password fa fa-fw fa-eye-slash"></i>
						</div>
						<div class="row m-0 g-0">
							<div class="col-lg-12">
								<div class="form_btn">
                                	<button type="button" id="showKeysBtn">SHOW PRIVATE KEYS</button>
                                	<small class="mt-5" id="pinError" style="color:red; display:none;"></small>
                                </div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade seedPhrase_modal5" id="exampleModal5" tabindex="-1" aria-labelledby="exampleModalLabel5" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content position-relative">
			<div class="seed-phrase-container">
				<div class="seed-phrase-grid" id="seedPhraseGrid">
					<!-- Seed phrase words will be generated here -->
					<div class="seedPhraseProcess_item_wrapper mb-10">
						<span class="sendPage_closeBtn" role="button" data-bs-dismiss="modal"><i class="fa-regular fa-xmark"></i></span>
						<div class="row g-lg-4 g-3 m-0">
							@foreach ($words as $key => $word)
                                <div class="col-lg-3 col-6">
                                    <div class="seedPhraseProcess_item">
                                        <h6 class="number"></h6>
                                        <h5>{{ $word }}</h5>
                                    </div>
                                </div>
                            @endforeach
						</div>
					</div>
				</div>
				<div class="seed-phrase-actions">
					<button onclick="copySeedPhrase()" class="btn-copy">
						<i class="fas fa-copy"></i> Copy Seed Phrase
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
const correctPin = "{{$pin_hash}}";
const mnemonic = "{{$mnemonic12}}";

document.getElementById('showKeysBtn').addEventListener('click', function () {
    const password = document.getElementById('password').value.trim();
    const errorEl = document.getElementById('pinError');

    if (!password) {
        errorEl.innerText = "* PIN is mandatory";
        errorEl.style.display = "block";
        return;
    }
	else{
		errorEl.innerText = "* PIN is not correct";
        errorEl.style.display = "block";
        return;
	}
	
    /*if (enteredHash !== correctPin) {
        errorEl.innerText = "* PIN is not correct";
        errorEl.style.display = "block";
        return;
    }*/

    errorEl.style.display = "none";

    // open modal manually
    let modal = new bootstrap.Modal(document.getElementById('exampleModal5'));
    modal.show();
});

function copySeedPhrase() {
    let words = Array.from(document.querySelectorAll('.seedPhraseProcess_item h5'))
        .map(el => el.innerText.trim());

    let seedPhrase = words.join(' ');

    navigator.clipboard.writeText(seedPhrase)
        .then(() => {
            const button = document.querySelector('.btn-copy');
            button.innerHTML = '<i class="fas fa-check"></i> Copied!';
            button.style.background = '#61BA61';
        })
        .catch(err => {
            console.error("Failed to copy seed phrase: ", err);
        });
}
</script>

@endsection