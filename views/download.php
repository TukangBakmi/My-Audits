<!DOCTYPE html>
<html lang="en">
    
<?php
$titleDoc = "Maybank | Download";
include_once '../static/head.php';
include_once '../static/navbar.php';
?>

<link rel="stylesheet" href="../static/css/index.css">

<body class="bg-user text-black">
    <div class="container">
        <h1 class="text-center mb-2">Download File</h1>
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th scope="col" style="text-align:center;">File Name</th>
                    <th scope="col" style="text-align:center;">File Size</th>
                    <th scope="col" style="text-align:center;">Date Uploaded</th>
                    <th scope="col" style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody id="downloadData">
            </tbody>
        </table>
    </div>
</body>

</html>

<script>
    $("#download").addClass("nav-user-active");

    function getData() {
        
        // Show the progress bar Swal
        loadingSwal("Loading Data...");

        let content = "";
        $.ajax({
            type: 'GET',
            url: '../backend/admin/kelola_file.php',
            dataType: 'JSON',
            success: function(response) {
                // Close progress bar
                Swal.close();

                $.each(response.data, function(idx, el) {
                    var rawDateUpdate = new Date(el.date_uploaded);
                    var dateUploaded = rawDateUpdate.getDate() + " " +
                                        rawDateUpdate.toLocaleString('default', { month: 'long' }) + " " +
                                        rawDateUpdate.getFullYear() + "<br>" +
                                        rawDateUpdate.toLocaleTimeString();
                    content += `<tr>
                                    <td>${el.nama}</td>
                                    <td style="text-align:center;">${el.formatted_size}</td>
                                    <td style="text-align:center;">${dateUploaded}</td>
                                    <td style="text-align:center">
                                        <button class="btn yellow-button action-button m-1" id="btnDownload" data-id="${el.id}" data-dir="${el.directory}" data-name="${el.nama}">
                                            Download
                                        </button>
                                    </td>
                                </tr>`;
                })
                $('#downloadData').html(content);
                $('.table').DataTable({
                    scrollCollapse: true,
                    order: [[2, 'desc']],
                    columnDefs: [
                        {
                            targets: [0],
                            width: '40%'
                        },
                        {
                            targets: [1],
                            width: '16%',
                            type: 'file-size'
                        },
                        {
                            targets: [2],
                            width: '24%',
                            type: 'custom-date'
                        },
                        {
                            targets: [3],
                            width: '20%',
                            orderable: false
                        }
                    ],
                    lengthMenu: [[50, 150, 500, 2000, -1], [50, 150, 500, 2000, "All"]],
                });
            }
        })
    }

    $(document).ready(function() {

        getData();
        // Handle the click event of the Download Button
        $(document).on('click', '#btnDownload', function() {
            var fileId = $(this).data("id");
            var fileName = $(this).data("name");
            var filePath = $(this).data("dir");

            downloadExcel(fileId, fileName, filePath);
        })
    })
</script>