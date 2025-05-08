<!DOCTYPE html>
<html lang="en">
<?php
    $titleDoc = "Admin | Password Reset Logs";
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
        <h1 class="text-center mb-2">Password Reset Logs</h1>
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th scope="col" style="text-align:center;">Full Name</th>
                    <th scope="col" style="text-align:center;">Role</th>
                    <th scope="col" style="text-align:center;">Email</th>
                    <th scope="col" style="text-align:center;">Token</th>
                    <th scope="col" style="text-align:center;">Date Expired</th>
                    <th scope="col" style="text-align:center;">Notes</th>
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
    $("#logPassReset").addClass("nav-admin-active");

    // Function to load data from database as a table
    function getData() {
        
        // Show the progress bar Swal
        loadingSwal("Loading Data...");

        let content = "";
        $.ajax({
            type: 'GET',
            url: '../backend/admin/kelola_log_pass.php',
            dataType: 'JSON',
            success: function(response) {
                // Close progress bar
                Swal.close();

                $.each(response.data, function(idx, el) {

                    // for date expired format
                    var rawDate = new Date(el.expiry_time);
                    var dateExpired = rawDate.getDate() + " " +
                                        rawDate.toLocaleString('default', { month: 'long' }) + " " +
                                        rawDate.getFullYear() + "<br>" +
                                        rawDate.toLocaleTimeString();

                    // for date last changed format
                    var rawDateUsed = new Date(el.date_used);
                    var dateUsed = rawDateUsed.getDate() + " " +
                                        rawDateUsed.toLocaleString('default', { month: 'long' }) + " " +
                                        rawDateUsed.getFullYear() + "<br>" +
                                        rawDateUsed.toLocaleTimeString();
                    var lastChanged = el.used == 0 ? '<span class="text-red">password has not been changed</span>' : '<span class="text-green">Last changed at<br>' + dateUsed + '</span>';

                    // for token format
                    var chunkSize = 32;
                    var formattedToken = "";
                    for (var i = 0; i < el.token.length; i += chunkSize) {
                        formattedToken += el.token.substr(i, chunkSize) + "<br>";
                    }
                    content += `<tr>
                                    <td>${el.full_name} (${el.npk_user})</td>
                                    <td style="text-align:center;">${el.role}</td>
                                    <td style="text-align:center;">${censorEmail(el.email)}</td>
                                    <td style="text-align:center;">${formattedToken}</td>
                                    <td style="text-align:center;">${dateExpired}</td>
                                    <td style="text-align:center;">${lastChanged}</td>
                                </tr>`;
                })
                $('#logTable').html(content);
                $('.table').DataTable({
                    scrollCollapse: true,
                    order: [[4, 'desc']],
                    columnDefs: [
                        {
                            targets: [0],
                            width: '16%'
                        },
                        {
                            targets: [1],
                            width: '8%',
                        },
                        {
                            targets: [2],
                            width: '12%'
                        },
                        {
                            targets: [3],
                            width: '32%',
                        },
                        {
                            targets: [4],
                            width: '16%',
                            type: 'custom-date'
                        },
                        {
                            targets: [5],
                            width: '16%',
                        }
                    ],
                    lengthMenu: [[50, 150, 500, 2000, -1], [50, 150, 500, 2000, "All"]],
                });
            }
        })
    }

    $(document).ready(function() {
        getData()
        
        // Handle the download button
        $('#btnDownload').click(function() { downloadingData('kelola_log_pass.php') });
    })
</script>