<!DOCTYPE html>
<html lang="en">
<?php
    $titleDoc = "Admin | Download Logs";
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
        <h1 class="text-center mb-2">Download Logs</h1>
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th scope="col" style="text-align:center;">File Name</th>
                    <th scope="col" style="text-align:center;">Downloaded By</th>
                    <th scope="col" style="text-align:center;">Position</th>
                    <th scope="col" style="text-align:center;">File Size</th>
                    <th scope="col" style="text-align:center;">Date Downloaded</th>
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
    $("#logDownload").addClass("nav-admin-active");

    // Function to load data from database as a table
    function getData() {
        
        // Show the progress bar Swal
        loadingSwal("Loading Data...");

        let content = "";
        $.ajax({
            type: 'GET',
            url: '../backend/admin/kelola_log.php',
            dataType: 'JSON',
            success: function(response) {
                // Close progress bar
                Swal.close();

                $.each(response.data, function(idx, el) {
                    var additionalStyle = el.position == "Admin" ? "color: green; font-weight: bold;" : "";
                    var rawDate = new Date(el.date_downloaded);
                    var dateDownloaded = rawDate.getDate() + " " +
                                        rawDate.toLocaleString('default', { month: 'long' }) + " " +
                                        rawDate.getFullYear() + "<br>" +
                                        rawDate.toLocaleTimeString();
                    content += `<tr>
                                    <td>${el.file_name}</td>
                                    <td style="text-align:center;">${el.full_name} (${el.npk_user})</td>
                                    <td style="text-align:center; ${additionalStyle}">${el.position}</td>
                                    <td style="text-align:center;">${el.formatted_size}</td>
                                    <td style="text-align:center;">${dateDownloaded}</td>
                                </tr>`;
                })
                $('#logTable').html(content);
                $('.table').DataTable({
                    scrollCollapse: true,
                    order: [[4, 'desc']],
                    columnDefs: [
                        {
                            targets: [0],
                            width: '36%'
                        },
                        {
                            targets: [1],
                            width: '16%',
                        },
                        {
                            targets: [2],
                            width: '22%'
                        },
                        {
                            targets: [3],
                            width: '10%',
                            type: 'file-size'
                        },
                        {
                            targets: [4],
                            width: '16%',
                            type: 'custom-date'
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
        $('#btnDownload').click(function() { downloadingData('kelola_log.php') });
    })
</script>