<!DOCTYPE html>
<html lang="en">
<?php
    $titleDoc = "Maybank Finance | Reset Password";
    include_once '../static/head.php';
    include_once '../static/session_resetPassword.php';
?>

<link rel="stylesheet" href="../static/css/index.css">

<body class="dashboard-user bg-secondary text-black">
    <div class="container h-100">
        <div id="expiredBody" class="card-dashboard p-5 text-start" style="text-align: justify;" hidden>
            <strong class="fs-3 fw-bold text-black lh-1">Request Expired</strong>
            <h3 class="fs-6 text-black lh-2 mt-3">Oops.. Looks like your request to reset your password has expired. If you still wish to reset your password, please resubmit your request and ensure you have clicked the link in your most recent email.</h3>
        </div>
        <div id="requestBody" class="card bg-light bg-gradient text-black" hidden>
            <div class="card-body p-5 text-center">
                <h2 class="fw-bold text-center">Password Reset Request</h2>
                <form id="formResetPass" class="m-4">
                    <input type="hidden" name="token" value="<?= $token ?>">
                    <div class="form-outline mb-2" id="passwordInput">
                        <div class="input-group">
                            <input type="password" id="password" name="password" class="form-control" placeholder="New Password">
                            <div class="btn btn-outline-secondary eye-toogle align-items-center d-flex" id="showPasswordToggle" onclick="togglePasswordVisibility('password','showPasswordToggle')">
                                <i class="fas fa-eye-slash"></i>
                            </div>
                        </div>
                        <small class="text-danger ml-5 errorInput" id="passwordError"></small>
                    </div>
                    <div class="form-outline mb-4" id="cPasswordInput">
                        <div class="input-group">
                            <input type="password" id="cPassword" name="cPassword" class="form-control" placeholder="Confirm Password">
                            <div class="btn btn-outline-secondary eye-toogle align-items-center d-flex" id="cShowPasswordToggle" onclick="togglePasswordVisibility('cPassword','cShowPasswordToggle')">
                                <i class="fas fa-eye-slash"></i>
                            </div>
                        </div>
                        <small class="text-danger ml-5 errorInput" id="cPasswordError"></small>
                    </div>
                    <button id="btnResetPass" type="button" class="btn btn-outline-dark w-50">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    $(document).ready(function() {
        if ('<?= $tokenInfo ?>' == 'expired') {
            $('#expiredBody').removeAttr('hidden');
            $('#requestBody').attr('hidden', true);
        } else {
            $('#expiredBody').attr('hidden', true);
            $('#requestBody').removeAttr('hidden');
        }

        $('#btnResetPass').click(function() {
            const passwordPattern = /^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/;
            const data = new FormData($('#formResetPass')[0]);

            const newPass = data.get('password');
            const newPassConfirm = data.get('cPassword');

            // Validate new password and confirm password
            if (!newPass || !newPassConfirm) {
                Swal.fire('Error', 'Both fields are required', 'error');
            } else if (!passwordPattern.test(newPass)) {
                Swal.fire('Error','Password must contain at least one digit, one uppercase letter, and be 8-20 characters long', 'error');
            } else if (newPass !== newPassConfirm) {
                Swal.fire('Error','Passwords do not match', 'error');
            } else {
                $.ajax({
                    type: 'POST',
                    url: '../backend/func/changePassword.php',
                    data: {
                        id: '<?= $id ?>',
                        password: newPass,
                        role: '<?= $role ?>',
                        token: '<?= $token ?>'
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire({
                                icon: response.status,
                                title: response.title,
                                text: response.msg,
                            }).then(function() {
                                window.location = "../";
                            })
                        } else {
                            Swal.fire({
                                icon: response.status,
                                title: response.title,
                                text: response.msg,
                            })
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle AJAX errors and close the Swal modal
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred: ' + error
                        });
                    }
                });
            }
        });
    });

</script>