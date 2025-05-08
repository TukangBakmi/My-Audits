<?php
	session_start();
	$titleDoc = "Login Admin";
	include_once '../static/head.php';
?>

<link rel="stylesheet" href="<?= $rootURL ?>static/css/index.css">

<!DOCTYPE html>
<html lang="en">

<body class='bg-login'>
	<div class="container h-100">
		<div class="card bg-gradient text-white" style="background-color: rgba(16,16,16,1);">
			<div class="card-body p-5 text-center">
				<h2 class="fw-bold text-center">Login Admin</h2>
				<form id="loginForm" class="m-4">
					<div class="form-outline mb-2">
						<input type="number" name="npk" id="npk" class="form-control" placeholder="NPK" />
						<small class="text-danger errorInput" id="npkError"></small>
					</div>
					<div class="form-outline">
						<div class="input-group">
							<input type="password" id="password" name="password" class="form-control" placeholder="password"/>
							<div class="btn btn-outline-secondary eye-toogle align-items-center d-flex" id="showPasswordToggle" onclick="togglePasswordVisibility()">
								<i class="fas fa-eye-slash"></i>
							</div>
						</div>
						<small class="text-danger errorInput" id="passwordError" style="color: rgba(255,80,80,1);"></small>
					</div>
					<p class="text-decoration-underline fst-italic fw-semibold text-end" style="color: rgba(160,160,255,1); cursor: pointer; font-size:13px; margin-top: 2px">
						<a id="forgotPasswordLink">Forgot Password?</a>
					</p>
					<button class="btn btn-outline-light w-50" id="login_admin" type="button">Login</button>
				</form>
				<div class="w-100 text-center">Mau melihat sisi user? <a class="fw-bold" href="<?= $rootURL ?>" style="color: rgba(160,160,255,1);">Login sebagai user!</a></div>
			</div>
		</div>
	</div>
</body>

</html>

<script>

    $(document).ready(function() {

		if ('<?= isset($_SESSION["AUTH_ADMIN"]) ?>') {
			// Display a SweetAlert indicating that the session is already created
			Swal.fire({
				title: 'Session Already Created',
				text: 'You are already logged in. Continue with your session.',
				icon: 'info',
				confirmButtonText: 'OK'
			}).then(function() {
				window.location = "dashboard";
			})
		}

		// Add an event listener for the 'keydown' event on the first input
		$('#npk, #password').keydown(function(event) {
			if (event.keyCode === 13) {
				login('<?= $rootURL ?>backend/admin/login.php', '<?= $rootURL ?>admin/dashboard');
			}
		});

        // Handle the click event of the Forgot Password
		$(document).on('click', '#forgotPasswordLink', function() {
            forgotPassword('<?= $rootURL ?>backend/func/', 'admin');
		});

		// Handle the click event of the Login Admin
		$('#login_admin').click(function() {
			login('<?= $rootURL ?>backend/admin/login.php', '<?= $rootURL ?>admin/dashboard');
		})
    })

</script>