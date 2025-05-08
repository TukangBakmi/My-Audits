<?php
    include_once '../static/session_user.php';
?>

<header>
    <nav class="navbar navbar-user navbar-expand-md px-5 py-2">
        <div class="container-fluid p-2">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse nav-user justify-content-end" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a id="dashboard" href="dashboard" class="mx-2 btn nav-link text-decoration-none fw-bolder">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a id="download" href="download" class="mx-2 btn nav-link text-decoration-none fw-bolder">
                            Download
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a id="username" class="mx-2 btn nav-link dropdown-toggle text-decoration-none fw-bolder" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false"></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a id="btnChangeEmail" class="btn dropdown-item">Change Email</a></li>
                            <li><a id="btnLogoutUser" class="btn dropdown-item">Logout</a></li>
                            <li><a class="dropdown-item no-hover border-top border-gray mt-2 pt-2 text-black">User</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<script>
    // Remove class
    document.getElementById("dashboard").classList.remove("nav-user-active");
    document.getElementById("download").classList.remove("nav-user-active");

    // When the document is ready, attach the click event handler to the "Logout" link
    $(document).ready(function() {
        $('#username').text("<?= $_SESSION["data"]['namaLengkap'] ?>");
        
        $('#btnChangeEmail').click(function() {
            changeEmail("<?= $_SESSION["data"]['email'] ?>");
        });

        $('#btnLogoutUser').click(function() {
            // Show a confirmation popup using SweetAlert
            Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Logout',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                // If the user confirms the logout
                if (result.isConfirmed) {
                    // Redirect to the logout page
                    window.location.href = '../backend/user/logout.php';
                }
            });
        });

        // Check session status every minute (adjust the interval as needed)
        setInterval(function() {
            $.ajax({
                url: '../backend/func/checkSession.php',
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'expired') {
                        // Session has expired, show SweetAlert and redirect to the login page
                        Swal.fire({
                            title: 'Session Expired',
                            text: 'Your session has timed out. Please log in again.',
                            icon: 'warning',
                            showConfirmButton: false,
                            timer: 10000,
                            timerProgressBar: true,
                        }).then(function() {
                            // Redirect to the login page
                            window.location.href = '../backend/user/logout.php';
                        });
                    }
                }
            });
        }, 60000); // Check session status in ms
    });
</script>