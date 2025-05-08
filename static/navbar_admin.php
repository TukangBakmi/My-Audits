<?php
    include_once '../static/session_admin.php';
    
    // Define the accessed pages based on user role
    $accessedPages = [];
    if ($_SESSION["data"]['level'] == "Administrator") {
        $accessedPages = ['dashboard', 'file', 'logs', 'logDownload', 'logPassReset', 'logUserLogin', 'user', 'positions'];
    } elseif ($_SESSION["data"]['level'] == "Admin Uploader") {
        $accessedPages = ['file'];
    } elseif ($_SESSION["data"]['level'] == "Super Admin") {
        $accessedPages = ['dashboard', 'file', 'logs', 'logDownload', 'logPassReset', 'logUserLogin', 'user', 'admin', 'positions'];
    }
    
    // Get the current endpoint
    $currentEndpoint = getEndpoint();
    
    // Check if the current endpoint is allowed
    if (!in_array($currentEndpoint, $accessedPages)) {
        // Redirect immediately
        header("Location: " . $accessedPages[0]);
        exit();
    }
    
    function getEndpoint() {
        // Get the current pathname from the URL
        $currentPathname = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
        // Split the pathname by '/'
        $pathParts = explode('/', $currentPathname);
    
        // Filter out empty parts (due to leading/trailing slashes)
        $pathParts = array_filter($pathParts, function($part) {
            return $part !== '';
        });
    
        // Get the last part (after the last '/')
        return end($pathParts);
    }
?>

<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark px-5 py-2">
        <div class="container-fluid p-2">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse nav-admin justify-content-between" id="navbarSupportedContent">
                <ul class="navbar-nav">

                    <?php if (in_array('dashboard', $accessedPages)): ?>
                        <li class="nav-item align-self-center">
                            <a id="dashboard" href="dashboard" class="mx-2 btn nav-link text-decoration-none fw-bolder">
                                Dashboard
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('file', $accessedPages)): ?>
                        <li class="nav-item align-self-center">
                            <a id="file" href="file" class="mx-2 btn nav-link text-decoration-none fw-bolder">
                                Files
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('logs', $accessedPages)): ?>
                        <li class="nav-item dropdown">
                            <a id="logs" class="mx-2 btn nav-link dropdown-toggle text-decoration-none fw-bolder" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                Logs
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">
                                <li class="nav-item">
                                    <a id="logDownload" href="logDownload" class="btn dropdown-item">
                                        Download Logs
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a id="logPassReset" href="logPassReset" class="btn dropdown-item">
                                        Password Logs
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a id="logUserLogin" href="logUserLogin" class="btn dropdown-item">
                                        User Logs
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('user', $accessedPages)): ?>
                        <li class="nav-item align-self-center">
                            <a id="user" href="user" class="mx-2 btn nav-link text-decoration-none fw-bolder">
                                Users
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('admin', $accessedPages)): ?>
                        <li class="nav-item align-self-center">
                            <a id="admin" href="admin" class="mx-2 btn nav-link text-decoration-none fw-bolder">
                                Admins
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array('positions', $accessedPages)): ?>
                        <li class="nav-item align-self-center">
                            <a id="positions" href="positions" class="mx-2 btn nav-link text-decoration-none fw-bolder">
                                Positions
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a id="username" class="btn nav-link dropdown-toggle text-decoration-none fw-bolder" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false"></a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a id="btnChangeEmail" class="btn dropdown-item">Change Email</a></li>
                            <li><a id="btnLogoutAdmin" class="btn dropdown-item">Logout</a></li>
                            <li><a class="dropdown-item no-hover border-top mt-2 pt-2 text-white"><?= $_SESSION["data"]['level'] ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<script>
    // Remove class
    $("#dashboard").removeClass("nav-admin-active");
    $("#file").removeClass("nav-admin-active");
    $("#logs").removeClass("nav-admin-active");
    $("#logDownload").removeClass("nav-admin-active");
    $("#logPassReset").removeClass("nav-admin-active");
    $("#logUserLogin").removeClass("nav-admin-active");
    $("#user").removeClass("nav-admin-active");
    $("#admin").removeClass("nav-admin-active");
    $("#positions").removeClass("nav-admin-active");

    // When the document is ready, attach the click event handler to the "Logout" link
    $(document).ready(function() {
        $('#username').text("<?= $_SESSION["data"]['namaLengkap'] ?>");

        $('#btnChangeEmail').click(function() {
            changeEmail("<?= $_SESSION["data"]['email'] ?>");
        });

        $('#btnLogoutAdmin').click(function() {
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
                    window.location.href = '../backend/admin/logout.php';
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
                            window.location.href = '../backend/admin/logout.php';
                        });
                    }
                }
            });
        }, 60000); // Check session status in ms
    });
</script>