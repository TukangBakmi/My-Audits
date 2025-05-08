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
        <h1 class="text-center mt-3">Manage User</h1>
        <!-- Button trigger modal -->
        <button id="btnNew" type="button" class="btn floating-action-button green-button" data-bs-toogle="modal">
            Add User
        </button>
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th scope="col" style="text-align:center;">NPK</th>
                    <th scope="col" style="text-align:center;">Full Name</th>
                    <th scope="col" style="text-align:center;">Email</th>
                    <th scope="col" style="text-align:center;">Position</th>
                    <th scope="col" style="text-align:center;">Date Joined</th>
                    <th scope="col" style="text-align:center;" style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody id="userTable">
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
                        <input type="hidden" name="id">
                        <div class="form-outline mb-4" id="npkInput">
                            <input type="number" class="form-control" id="npk" name="npk" placeholder="NPK">
                            <small class="text-danger ml-5 errorInput" id="npkError"></small>
                        </div>
                        <div class="form-outline mb-4" id="nameInput">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Full Name">
                            <small class="text-danger ml-5 errorInput" id="nameError"></small>
                        </div>
                        <div class="form-outline mb-4" id="emailInput">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                            <small class="text-danger ml-5 errorInput" id="emailError"></small>
                        </div>
                        <div class="form-outline mb-4" id="passwordInput">
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password">
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
                        <div class="form-outline mb-4" id="positionInput">
                            <select class="form-select" id="position" name="position">
                            </select>
                        </div>
                        <?php include_once '../views/components/modal_footer.php'; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<script>
    $("#user").addClass("nav-admin-active");

    // Function to set up modal because modal is used to edit and add data
    function setupModal(title, classButton) {
        document.getElementById("formAdd").reset();
        
        var passwordInput = document.getElementById('password');
        var showPasswordToggle = document.getElementById('showPasswordToggle');
        passwordInput.type = 'password';
        showPasswordToggle.innerHTML = '<i class="fas fa-eye-slash"></i>';

        if(classButton == 'edit'){
            $('#passwordInput, #cPasswordInput, #email').hide();
        } else if(classButton == 'add'){
            $('.form-outline, #email').show();
        }
        
        $('#addModal').modal('show');
        $('#modalTitle').text(title);
        $("#btnSave").removeClass('add edit').addClass(classButton);
        $(".errorInput").hide();
    }

    // Function to get data to datatable
    function getData(type, id = 1) {
        
        // Show the progress bar Swal
        loadingSwal("Loading Data...");

        let content = "";
        $.ajax({
            type: 'GET',
            url: '../backend/admin/kelola_user.php',
            data: {
                type: type,
                id: id
            },
            dataType: 'JSON',
            success: function(response) {
                // Close progress bar
                Swal.close();

                if (type == 'user') {
                    $.each(response.data, function(idx, el) {
                        var rawDate = new Date(el.date_joined);
                        var date = rawDate.getDate() + " " +
                                        rawDate.toLocaleString('default', { month: 'long' }) + " " + rawDate.getFullYear()
                        content += `<tr>
                                    <td style="text-align:center;">${el.npk}</td>
                                    <td>${el.nama_lengkap}</td>
                                    <td>${censorEmail(el.email)}</td>
                                    <td style="text-align:center;">${el.position}</td>
                                    <td style="text-align:center;">${date}</td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <div class="d-flex">
                                                <button class="btn yellow-button action-button m-1 w-50" id="btnEdit" data-id="${el.id}" data-position="${el.id_position}" data-level="${el.id_level}">
                                                    Edit
                                                </button>
                                                <button class="btn red-button action-button m-1 w-50" id="btnDelete" data-id="${el.id}" data-name="${el.nama_lengkap}" data-level="${el.level}">
                                                    Delete
                                                </button>
                                            </div>
                                            <div class="d-flex">
                                                <button class="btn blue-button action-button m-1 w-100" id="btnChgPass" data-email="${el.email}">
                                                    Change Password
                                                </button>
                                            </div>
                                        </div>
                                    </td></tr>`;
                    })
                    $('#userTable').html(content);
                    $('.table').DataTable({
                        scrollCollapse: true,
                        order: [[4, 'desc']],
                        columnDefs: [
                            {
                                targets: [0],
                                width: '10%'
                            },
                            {
                                targets: [1],
                                width: '18%'
                            },
                            {
                                targets: [2],
                                width: '24%'
                            },
                            {
                                targets: [3],
                                width: '16%'
                            },
                            {
                                targets: [4],
                                width: '12%',
                                type: 'custom-date'
                            },
                            {
                                targets: [5],
                                width: '20%',
                                orderable: false
                            }
                        ],
                        lengthMenu: [[10, 25, 100, -1], [10, 25, 100, "All"]],
                    });
                }
                if (type == 'user_record') {
                    $.each(response.data, function(idx, el) {
                        // Mengisi input-an
                        $('input[name="npk"]').val(el.npk);
                        $('input[name="name"]').val(el.nama_lengkap);
                        $('input[name="email"]').val(el.email);
                    });
                }
                if (type == 'position') {
                    $.each(response.data, function(idx, el) {
                        if (el.id == id) {
                            content += "<option value='" + el.id + "' selected >" + el.position + "</option>";
                        } else {
                            content += "<option value='" + el.id + "' >" + el.position + "</option>";
                        }
                    })
                    $('#position').html(content);
                }
            }
        })
    }
    
    $(document).ready(function() {
        getData('user');
        
        // Handle the download button
        $('#btnDownload').click(function() { downloadingData('kelola_user.php') });

        // Handle the click event of the Add User
        $('#btnNew').click(function() {
            setupModal("Add User", "add");
            getData('position', 6);
        })
        
        // Save Data (Tambah, Edit)
        $('#btnSave').click(function () {
            const formData = new FormData($('#formAdd')[0]);
            const form = $('#formAdd');

            if($('#btnSave').hasClass('add')){
                formData.append('type', 'add');
            } else {
                formData.append('type', 'edit');
            }

            // Show the progress bar Swal modal
            loadingSwal('Saving Data...');

            $.ajax({
                type: 'POST',
                url: '../backend/admin/kelola_user.php',
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
                            window.location = "user";
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

        // Handle the click event of the Edit User
        $(document).on('click', '#btnEdit', function() {
            var id = $(this).data("id");
            var idLevel = $(this).data("level");
            var idPosition = $(this).data("position");
            $('input[name="id"]').val(id);
            
            setupModal("Edit User", "edit");
            // Get user data and populate the input fields
            getData('user_record', id);
            getData('position', idPosition);
        })

        // Handle the click event of the Change Password
        $(document).on('click', '#btnChgEmail', function() {
            var id = $(this).data("id");
            changeEmail(id, 'user');
        })

        // Handle the click event of the Change Password
        $(document).on('click', '#btnChgPass', function() {
            var email = $(this).data("email");
            changePassword(email, 'user');
        })

        // Handle the click event of the Delete User
        $(document).on('click', '#btnDelete', function() {
            var id = $(this).data("id");
            var level = $(this).data("level");
            var name = $(this).data("name");
            buttonDelete(id, level, name, 'user');
        })
    })
</script>