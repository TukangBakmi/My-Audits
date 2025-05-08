<!DOCTYPE html>
<html lang="en">
<?php
    $titleDoc = "Admin | Manage User";
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
        <h1 class="text-center mt-3">Manage Position</h1>
        <!-- Button trigger modal -->
        <button id="btnNew" type="button" class="btn floating-action-button green-button" data-bs-toogle="modal">
            Add Position
        </button>
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th scope="col" style="text-align:center;">Id</th>
                    <th scope="col" style="text-align:center;">Position</th>
                    <th scope="col" style="text-align:center;" style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody id="positionTable">
            </tbody>
        </table>
    </div>

    <!-- Modal Add -->
    <div class="modal fade text-black" tabindex="-1" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <?php include_once '../views/components/modal_header.php'; ?>
                <div class="modal-body">
                    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST" enctype="multipart/form-data" id="formAdd">
                        <input type="hidden" name="currentId">
                        <div class="form-outline mb-4" id="idInput">
                            <input type="text" class="form-control" id="id" name="id" placeholder="Id">
                            <small class="text-danger ml-5 errorInput" id="idError"></small>
                        </div>
                        <div class="form-outline mb-4" id="positionNameInput">
                            <input type="text" class="form-control" id="positionName" name="positionName" placeholder="Position Name">
                            <small class="text-danger ml-5 errorInput" id="positionNameError"></small>
                        </div>
                    </form>
                </div>
                <?php include_once '../views/components/modal_footer.php'; ?>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    $("#positions").addClass("nav-admin-active");

    function getData(type, id = 1) {
        
        // Show the progress bar Swal
        loadingSwal("Loading Data...");

        let content = "";
        $.ajax({
            type: 'GET',
            url: '../backend/admin/kelola_position.php',
            data: {
                type: type,
                id: id
            },
            dataType: 'JSON',
            success: function(response) {
                // Close progress bar
                Swal.close();

                if (type == 'position') {
                    $.each(response.data, function(idx, el) {
                        content += `<tr>
                                    <td style="text-align:center;">${el.id}</td>
                                    <td style="text-align:center;">${el.position}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <button class="btn yellow-button action-button m-1" id="btnEdit" data-id="${el.id}" data-position="${el.position}">
                                                Edit
                                            </button>
                                            <button class="btn red-button action-button m-1" id="btnDelete" data-id="${el.id}" data-position="${el.position}">
                                                Delete
                                            </button>
                                        </div>
                                    </td></tr>`;
                    })
                    $('#positionTable').html(content);
                    $('.table').DataTable({
                        scrollCollapse: true,
                        order: [[0, 'asc']],
                        columnDefs: [
                            {
                                targets: [0],
                                width: '12%',
                                orderable: false
                            },
                            {
                                targets: [1],
                                width: '56%'
                            },
                            {
                                targets: [2],
                                width: '32%',
                                orderable: false
                            }
                        ],
                        lengthMenu: [[10, 25, 100, -1], [10, 25, 100, "All"]],
                    });
                }

                if (type == 'position_record') {
                    $.each(response.data, function(idx, el) {
                        // Mengisi input-an
                        $('input[name="id"]').val(el.id);
                        $('input[name="positionName"]').val(el.position);
                    });
                }
            }
        })
    }

    $(document).ready(function() {
        getData('position')

        // Handle the download button
        $('#btnDownload').click(function() { downloadingData('kelola_position.php') });

        // Handle the click event of the Add Data
        $('#btnNew').click(function() {
            Swal.fire({
                title: 'Add Position',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Save',
                input: 'text',
                inputPlaceholder: "Insert Position Name",
                allowOutsideClick: false,
                preConfirm: (value) => {
                    if (!value) {
                        Swal.showValidationMessage('Position is required');
                    } else {
                        return value;
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // Show the progress bar Swal modal
                    loadingSwal("Adding Position...");

                    $.ajax({
                        type: 'POST',
                        url: '../backend/admin/kelola_position.php',
                        data: {
                            type: 'add',
                            position: result.value
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
                                    window.location = "positions";
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
                                text: 'An error occurred: ' + error
                            });
                        }
                    });
                }
            })
        });

        // Save Data
        $('#btnSave').click(function () {
            const formData = new FormData($('#formAdd')[0]);
            const form = $('#formAdd');
            formData.append('type', 'edit');

            // Show the progress bar Swal modal
            loadingSwal('Saving Data...');

            $.ajax({
                type: 'POST',
                url: '../backend/admin/kelola_position.php',
                data: formData,
                dataType: "JSON",
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.error_status == true) {
                        form.find('small').text('');
                        for (let key in response) {
                            let errorContainer = form.find(`#${key}Error`);
                            if (errorContainer.length !== 0) {
                                errorContainer.show()
                                errorContainer.html(response[key]);
                            }
                        }
                    } else if (response.status == 'success') {
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            text: response.msg,
                        }).then(function() {
                            window.location = "positions";
                        })
                    } else {
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            text: response.msg,
                        })
                    }
                }
            });
        });

        // Handle the click event of the Edit Data
        $(document).on('click', '#btnEdit', function() {
            var id = $(this).data("id");
            var position = $(this).data("position");
            $('input[name="currentId"]').val(id);
           
            document.getElementById("formAdd").reset();
            $('#addModal').modal('show');
            $('#modalTitle').text('Edit Position');
            $(".errorInput").hide();

            // Get user data and populate the input fields
            getData('position_record', id);
        })

        // Handle the click event of the Delete Data
        $(document).on('click', '#btnDelete', function() {
            var id = $(this).data("id");
            var position = $(this).data("position");
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    // Show the progress bar Swal modal
                    progressBar('Deleting Position...');

                    $.ajax({
                        type: 'POST',
                        url: '../backend/admin/kelola_position.php',
                        data: {
                            type: 'delete',
                            id: id
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
                                window.location = "positions";
                            })
                        }
                    });
                }
            })
        })
    })
</script>