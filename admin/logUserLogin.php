<!DOCTYPE html>
<html lang="en">
<?php
    $titleDoc = "Admin | Login User Logs";
    include_once '../static/head.php';
    include_once '../static/session_admin.php';
    include_once '../static/navbar_admin.php';
?>

<link rel="stylesheet" href="../static/css/index.css">

<body class="bg-secondary text-white">
    <div class="container">
        <div class="d-flex justify-content-end">
            <button class="btn download-button btn-outline-light px-4 py-2" id="btnDownload">
                Download Data
            </button>
        </div>
        <h1 class="text-center mb-2">Login User Logs</h1>
        <button class="btn floating-action-button red-button hidden" id="btnClear" disabled>
            Clear Logs
        </button>
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th scope="col" style="text-align:center;">Full Name</th>
                    <th scope="col" style="text-align:center;">Date Login</th>
                    <th scope="col" style="text-align:center;">Date Logout</th>
                    <th scope="col" style="text-align:center;">Logged Out</th>
                </tr>
            </thead>
            <tbody id="logTable">
            </tbody>
        </table>
    </div>
</body>

</html>

<script>
    $("#logs").addClass("nav-admin-active");
    $("#logUserLogin").addClass("nav-admin-active");

    // Function to load data from database as a table
    function getData() {
        
        // Show the progress bar Swal
        loadingSwal("Loading Data...");

        let content = "";
        $.ajax({
            type: 'GET',
            url: '../backend/admin/kelola_log_user.php',
            dataType: 'JSON',
            success: function(response) {
                // Close progress bar
                Swal.close();

                $.each(response.data, function(idx, el) {
                    
                    var rawDateLogin = new Date(el.date_logout);
                    var dateLogin = rawDateLogin.getDate() + " " +
                                        rawDateLogin.toLocaleString('default', { month: 'long' }) + " " +
                                        rawDateLogin.getFullYear() + "<br>" +
                                        rawDateLogin.toLocaleTimeString();

                    var rawDateLogout = new Date(el.date_login);
                    var dateLogout = rawDateLogout.getDate() + " " +
                                        rawDateLogout.toLocaleString('default', { month: 'long' }) + " " +
                                        rawDateLogout.getFullYear() + "<br>" +
                                        rawDateLogout.toLocaleTimeString();
                    content += `<tr>
                                    <td>${el.full_name} (${el.npk_user})</td>
                                    <td style="text-align:center;">${dateLogout}</td>
                                    <td style="text-align:center;">${dateLogin}</td>
                                    <td style="text-align:center;">${el.logged_out}</td>
                                </tr>`;
                })
                $('#logTable').html(content);
                $('.table').DataTable({
                    scrollCollapse: true,
                    order: [[1, 'desc']],
                    columnDefs: [
                        {
                            targets: [0],
                            width: '32%'
                        },
                        {
                            targets: [1],
                            width: '24%',
                            type: 'custom-date'
                        },
                        {
                            targets: [2],
                            width: '24%',
                            type: 'custom-date'
                        },
                        {
                            targets: [3],
                            width: '20%',
                        },
                    ],
                    lengthMenu: [[50, 150, 500, 2000, -1], [50, 150, 500, 2000, "All"]],
                });
            }
        })
    }

    $(document).ready(function() {
        getData()
        
        // Handle the download button
        $('#btnDownload').click(function() { downloadingData('kelola_log_user.php') });
    })
</script>