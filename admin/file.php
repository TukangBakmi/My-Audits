<!DOCTYPE html>
<html lang="en">
<?php
    $titleDoc = "Admin | Manage File";
    include_once '../static/head.php';
    include_once '../static/session_admin.php';
    include_once '../static/navbar_admin.php';

    require_once '../config/dbconn.php';
    $users = $conn->query("SELECT user.id, nama_lengkap, position FROM user LEFT JOIN position ON position.id = id_position");
    if ($users) {
        while($row=$users->fetch_assoc()){
            $user[] = $row;
        }
    }
    $conn->close();
?>

<link rel="stylesheet" href="../static/css/index.css">

<body class="bg-secondary text-white">
    <div class="container">
        <div class="d-flex justify-content-end">
            <button class="btn download-button btn-outline-light px-4 py-2" id="btnDownload">
                Download Data
            </button>
        </div>
        <h1 class="text-center mb-2">Manage File</h1>
        <!-- Button trigger modal -->
        <button id="btnNew" type="button" class="btn floating-action-button green-button" data-bs-toogle="modal">
            Add File
        </button>
        <table class="table table-stripped">
            <thead>
                <tr>
                    <?php if ($_SESSION["data"]['level'] != 'Admin Uploader') { ?>
                        <th scope="col" style="text-align:center;"><input type="checkbox" id="checkAll" class="mx-2"></th>
                    <?php }; ?>
                    <th scope="col" style="text-align:center;">File Name</th>
                    <th scope="col" style="text-align:center;">Uploaded By</th>
                    <th scope="col" style="text-align:center;">Date Uploaded</th>
                    <th scope="col" style="text-align:center;">
                        <?php if ($_SESSION["data"]['level'] != 'Admin Uploader') { ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <div></div>
                                Action
                                <div id="deleteSelected" style="cursor: pointer; width: auto;">
                                    <i class="fa-solid fa-trash fa-lg"></i>
                                </div>
                            </div>
                        <?php } else { ?>
                            Action
                        <?php }; ?>
                    </th>
                </tr>
            </thead>
            <tbody id="data">
            </tbody>
        </table>
    </div>

    <!-- Modal Add -->
    <div class="modal fade text-black" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="">
        <div class="modal-dialog">
            <div class="modal-content">
                <?php include_once '../views/components/modal_header.php'; ?>
                <div class="modal-body">
                    <form id="formAdd" enctype="multipart/form-data">
                        <input type="file" id="excel_file" name="excel_file[]" accept=".xlsx, .xls" multiple>
                        <p class="mt-4 mb-0">Select the people you want to share these files with</p>
                        <select class="js-states form-control" id="sharedUser" multiple="multiple">
                            <option value="everyone">Everyone</option>
                            <?php for ($i = 0; $i < count($user); $i++) { ?>
                                <option value="<?= $user[$i]['id'] ?>"><?= $user[$i]['nama_lengkap'] ?></option>
                            <?php }; ?>
                        </select>
                    </form>
                </div>
                <?php include_once '../views/components/modal_footer.php'; ?>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    $("#file").addClass("nav-admin-active");

    // Function to load data from database as a table
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
                    var rawDateUpload = new Date(el.date_uploaded);
                    var dateUploaded = rawDateUpload.getDate() + " " +
                                        rawDateUpload.toLocaleString('default', { month: 'long' }) + " " +
                                        rawDateUpload.getFullYear()
                    content += `<tr>
                                    <?php if ($_SESSION["data"]['level'] != 'Admin Uploader') { ?>
                                        <td style="text-align:center;"><input type="checkbox" class="item-checkbox" data-id="${el.id}" data-dir="${el.directory}" data-name ="${el.nama}"></td>
                                    <?php }; ?>
                                    <td><a id="adminDownloadFile" style="cursor: pointer" class=""data-id="${el.id}" data-dir="${el.directory}" data-name="${el.nama}">${el.nama}</a></td>
                                    <td style="text-align:center;">${el.uploaded_by}</td>
                                    <td style="text-align:center;">${dateUploaded}</td>
                                    
                                    <td style="text-align:center">
                                    
                                        <?php if ($_SESSION["data"]['level'] != 'Admin Uploader') { ?>
                                            <button class="btn yellow-button action-button m-1" id="btnEdit" data-id="${el.id}" data-name ="${el.nama}">
                                                Rename
                                            </button>
                                        <?php }; ?>
                                        <button class="btn blue-button action-button m-1" id="btnDetails" data-id="${el.id}" data-name ="${el.nama}">
                                            Details
                                        </button>
                                        <?php if ($_SESSION["data"]['level'] != 'Admin Uploader') { ?>
                                            <button class="btn red-button action-button m-1" id="btnDelete" data-id="${el.id}" data-dir="${el.directory}" data-name ="${el.nama}">
                                                Delete
                                            </button>
                                        <?php }; ?>
                                    </td>
                                </tr>`;
                })
                $('#data').html(content);

                // Pengaturan untuk Admin Uploader
                let index = 2;
                let columnDefs = [
                    {
                        targets: [2],
                        type: 'custom-date',
                    },
                    {
                        targets: [3],
                        orderable: false
                    }
                ]
                // Pengaturan untuk Administrator dan Super Admin
                <?php if ($_SESSION["data"]['level'] != 'Admin Uploader') { ?>
                    index = 3;
                    columnDefs = [
                        {
                            targets: [3],
                            type: 'custom-date'
                        },
                        {
                            targets: [4],
                            orderable: false
                        }
                    ]
                <?php }; ?>
                // Declare Data Table
                $('.table').DataTable({
                    scrollCollapse: true,
                    order: [[index, 'desc']],
                    lengthMenu: [[50, 150, 500, 2000, -1], [50, 150, 500, 2000, "All"]],
                    columnDefs: columnDefs
                });
            }
        })
    }

    $(document).ready(function() {
        getData()

        // Initial check when the page loads
        updateCheckAllCheckbox();
        updateTrashIconClass();

        // Monitor changes in individual checkboxes
        $(document).on('change', '.item-checkbox', function() {
            updateCheckAllCheckbox();
            updateTrashIconClass();
        });

        // Handle the "Check All" checkbox
        $('#checkAll').click(function() {
            var isChecked = $(this).prop('checked');
            $('.item-checkbox').prop('checked', isChecked);
            updateTrashIconClass();
        });

        // Handle the click event of the Trash Icon
        $('#deleteSelected').click(deleteSelected);

        // Handle the download button
        $('#btnDownload').click(function() { downloadingData('kelola_file.php') });

        // Handle the click event of the Add File
        $('#btnNew').click(function() {
            document.getElementById("formAdd").reset();
            $('#sharedUser').val(null).trigger('change');
            $('#addModal').modal('show');
            $('#modalTitle').text('Upload File');
        })
        
        // Handle the click event of the Save Button
        $('#btnSave').click(checkingFiles);
        
        // Handle the click event of the Edit File
        $(document).on('click', '#btnEdit', function() {
            var itemId = $(this).data("id");
            var itemName = $(this).data("name");
            Swal.fire({
                title: 'Edit File',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Change Name',
                input: 'text',
                inputValue: itemName.split('.').slice(0, -1).join('.'),
                inputPlaceholder: "Insert File Name",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // Show the progress bar Swal modal
                    loadingSwal("Editing Data...");
                    var newName = result.value + '.' + itemName.split('.').pop();

                    $.ajax({
                        type: 'POST',
                        url: '../backend/admin/kelola_file.php',
                        data: {
                            type: 'edit',
                            id: itemId,
                            name: itemName,
                            newName: newName
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            Swal.close();
                            if(response.status == 'success'){
                                Swal.fire({
                                    icon: response.status,
                                    title: response.title,
                                    html: response.msg,
                                }).then(function() {
                                    window.location = "file";
                                });
                            } else{
                                Swal.fire({
                                    icon: response.status,
                                    title: response.title,
                                    html: response.msg,
                                })
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle AJAX errors and close the Swal modal
                            Swal.close();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred during file upload: ' + error
                            });
                        }
                    });
                }
            })
        });

        // Handle the click event of the Detail File
        $(document).on('click', '#btnDetails', function() {
            var itemId = $(this).data("id");
            var itemName = $(this).data("name");

            // Show the progress bar Swal modal
            loadingSwal("Loading Data...");

            $.ajax({
                type: 'GET',
                url: '../backend/admin/kelola_file.php',
                data: {
                    type: 'details',
                    id: itemId,
                    name: itemName
                },
                dataType: 'JSON',
                success: function(response) {
                    Swal.close();
                    if(response.status == 'details'){
                        var rawDateUpload = new Date(response.msg.date);
                        var dateUploaded = rawDateUpload.getDate() + " " +
                                            rawDateUpload.toLocaleString('default', { month: 'long' }) + " " +
                                            rawDateUpload.getFullYear() + " - " +
                                            rawDateUpload.toLocaleTimeString();

                        Swal.fire({
                            title: response.title,
                            html: `
                                <div style="text-align: left; font-size: 16px;">
                                    <strong>File Information:</strong>
                                    <ul>
                                        <li><strong>File Name:</strong> ${response.msg.name}</li>
                                        <li><strong>File Size:</strong> ${response.msg.size}</li>
                                        <li><strong>Uploaded By:</strong> ${response.msg.uploader}</li>
                                        <li><strong>Date Uploaded:</strong> ${dateUploaded}</li>
                                        <li><strong>Shared To:</strong> ${response.msg.shared && response.msg.shared.length > 0 ? response.msg.shared.map(user => `${user.nama_lengkap} ${user.npk}`).join(', ') : 'Everyone'} </li>
                                    </ul>
                                </div>
                            `
                        });
                    } else{
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            html: response.msg,
                        })
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors and close the Swal modal
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred during file upload: ' + error
                    });
                }
            });
        });

        // Handle the click event of the Delete File
        $(document).on('click', '#btnDelete', function() {
            var itemId = $(this).data("id");
            var itemDir = $(this).data("dir");
            var itemName = $(this).data("name");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // Show the progress bar Swal modal
                    progressBar('Deleting File...');

                    $.ajax({
                        type: 'POST',
                        url: '../backend/admin/kelola_file.php',
                        data: {
                            type: 'delete',
                            id: itemId,
                            dir: itemDir,
                            name: itemName
                        },
                        dataType: 'JSON',
                        xhr: function() {
                            var xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function(evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = (evt.loaded / evt.total) * 100;
                                    $('#custom-progress .progress-bar').css('width', percentComplete + '%');
                                    $('#custom-progress .progress-bar').attr('aria-valuenow', percentComplete);
                                    $('#custom-progress .progress-bar').text(percentComplete.toFixed(2) + '%');
                                }
                            }, false);
                            return xhr;
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: response.status,
                                title: response.title,
                                text: response.msg,
                            }).then(function() {
                                window.location = "file";
                            });
                        }
                    });
                }
            })
        })

        // Handle the click event of the Download Button
        $(document).on('click', '#adminDownloadFile', function() {
            var fileId = $(this).data("id");
            var fileName = $(this).data("name");
            var filePath = $(this).data("dir");

            downloadExcel(fileId, fileName, filePath);
        })

        $('#sharedUser').select2()

    })
</script>