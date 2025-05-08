<?php
session_start();
$titleDoc = "Maybank Login";
include_once '../static/head.php';
require_once '../config/dbconn.php';
?>

<link rel="stylesheet" href="<?= $rootURL ?>static/css/index.css">

<!DOCTYPE html>
<html lang="en">

<body class='bg-login'>
  	<div class="container h-100">
		<div class="card bg-light bg-gradient text-black">
			<div class="card-body p-5 text-center">
				<h2 class="fw-bold text-center">Login</h2>
					<form id="loginForm" class="m-4">
						<div class="form-outline mb-2">
							<input type="number" id="npk" class="form-control border border-secondary" name="npk" placeholder="NPK"/>
							<small class="text-danger errorInput" id="npkError"></small>
						</div>
						<div class="form-outline">
							<div class="input-group">
								<input type="password" id="password" name="password" class="form-control border border-secondary" placeholder="password"/>
								<div class="btn border border-secondary eye-toogle align-items-center d-flex" id="showPasswordToggle" onclick="togglePasswordVisibility()">
									<i class="fas fa-eye-slash"></i>
								</div>
							</div>
							<small class="text-danger errorInput" id="passwordError"></small>
						</div>
						<p class="text-decoration-underline fst-italic fw-semibold text-end" style="color: rgba(71,134,229,1); cursor: pointer; font-size:13px; margin-top: 2px">
							<a id="forgotPasswordLink">Forgot Password?</a>
						</p>
						<button class="btn btn-outline-dark w-50" id="login" type="button">Login</button>
					</form>
				<div class="w-100 text-center">Anda merupakan admin? <a href="<?= $rootURL; ?>admin/">Login sebagai admin</a></div>
			</div>
		</div>
  	</div>
</body>

</html>

<script>

	$(document).ready(function() {
		
		if ('<?= isset($_SESSION["AUTH_USER"]) ?>') {
			// Display a SweetAlert indicating that the session is already created
			Swal.fire({
				title: 'Session Already Created',
				text: 'You are already logged in. Continue with your session.',
				icon: 'info',
				confirmButtonText: 'OK'
			}).then(function() {
				window.location = "<?= $rootURL ?>views/dashboard";
			})
		}

		// Add an event listener for the 'keydown' event on the first input
		$('#npk, #password').keydown(function(event) {
			if (event.keyCode === 13) {
				login('<?= $rootURL ?>backend/user/login.php', '<?= $rootURL ?>views/dashboard');
			}
		});

		// Handle the click event of the Forgot Password
		$(document).on('click', '#forgotPasswordLink', function() {
			forgotPassword('<?= $rootURL ?>backend/func/', 'user');
		});

		// Handle the click event of the Login User
		$('#login').click(function() {
			login('<?= $rootURL ?>backend/user/login.php', '<?= $rootURL ?>views/dashboard');
		});
	})

</script>