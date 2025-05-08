// -------------------------------------------------- LOADING SWAL -------------------------------------------------- //
// Function to show a progress bar using swal
function loadingSwal(title){
    Swal.fire({
        title: title,
        allowOutsideClick: false,
        showConfirmButton: false,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// Function to show a progress bar using swal
function progressBar(title){
    Swal.fire({
        title: title,
        html: '<div id="progress-container"><div id="custom-progress" class="progress"><div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div></div></div>',
        allowOutsideClick: false,
        showConfirmButton: false,
    });
}



// ----------------------------------------------- EMAIL AND PASSWORD ----------------------------------------------- //
// Function to censor email address
function censorEmail(email) {
    // Split the email address into parts: username and domain
    var parts = email.split('@');
    var username = parts[0];
    var domain = parts[1];
    
    // Determine how many characters to keep visible at the beginning and end of the username
    var visibleChars = Math.min(3, username.length - 2); // Keep the first and last character visible
    
    // Keep the first `visibleChars` characters of the username visible and replace the rest with '*'
    var visibleUsername = username.substr(0, 1) + '*'.repeat(visibleChars) + username.substr(-1);
    
    // Construct the censored email address
    var censoredEmail = visibleUsername + '@' + domain;
    
    return censoredEmail;
}

// Function to change email address
function changeEmail(email) {
    Swal.fire({
        title: 'Change Email',
        text: 'Your current email is: ' + email,
        input: 'email',
        inputPlaceholder: 'Insert your new email',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Change Email',
        allowOutsideClick: false,
    }).then((result) => {
        if (result.isConfirmed) {
            
            // Show the progress bar Swal modal
            loadingSwal("Changing Email...");

            $.ajax({
                type: 'POST',
                url: '../backend/func/changeEmail.php',
                data: {
                    email: result.value.toLowerCase()
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.status == 'success') {
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            text: response.msg,
                        }).then(function() {
                            window.location.reload();
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
    })
}

// Function to set password visibility
function togglePasswordVisibility(textId = 'password', iconId = 'showPasswordToggle') {
    var passwordInput = document.getElementById(textId);
    var showPasswordToggle = document.getElementById(iconId);

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        showPasswordToggle.innerHTML = '<i class="fas fa-eye"></i>';
    } else {
        passwordInput.type = 'password';
        showPasswordToggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
    }
}

// Function to handle forgot password in index (admin or user) page
function forgotPassword(baseUrl, role) {
    Swal.fire({
        title: 'Forgot Password?',
        input: 'email',
        inputPlaceholder: 'Enter your email address',
        showCancelButton: false,
        confirmButtonText: 'Reset Password',
        showLoaderOnConfirm: true,
        preConfirm: (email) => {
            return fetch(baseUrl + 'resetPasswordMailer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email, role: role })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.status == 'error') {
                    throw new Error(data.msg);
                }
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed - ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire('Password Reset Email Sent!', 'Please check your email for instructions on how to reset your password.', 'success');
        }
    });
}

// Function to change password
function changePassword(email, role) {
    Swal.fire({
        icon: 'warning',
        title: 'Change Password',
        text: 'Do you want to send a password reset link to this ' + role + ' via email?',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Continue',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            
            // Show the progress bar Swal modal
            loadingSwal("Sending Email...");

            $.ajax({
                type: 'POST',
                url: '../backend/func/resetPasswordMailer.php',
                data: {
                    email: email,
                    role: role
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.status == 'success') {
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            text: response.msg,
                        }).then(function() {
                            window.location = role;
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
    })
}


// ----------------------------------------------- LOGIN USER & ADMIN ----------------------------------------------- //
// Function to login user and admin
function login(url, redirect) {

    const data = new FormData($('#loginForm')[0]);
    const formLogin = $('#loginForm');
    formLogin.find('#login').attr('disabled', true);

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "JSON",
        processData: false,
        contentType: false,
        success: function(response) {
            formLogin.find('#login').attr('disabled', false);
            if (response.error_status == true) {
                formLogin.find('small').text('');
                for (let key in response) {
                    let errorContainer = formLogin.find(`#${key}Error`);
                    if (errorContainer.length !== 0) {
                        errorContainer.show()
                        errorContainer.html(response[key]);
                    }
                }
            } else if (response.status == 'success') {
                $(".errorInput").hide();
                Swal.fire({
                    icon: response.status,
                    title: response.title,
                    text: response.msg,
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location = response.level == 'Admin Uploader' ? 'file' : redirect;
                });
            } else {
                $(".errorInput").hide();
                Swal.fire({
                    icon: response.status,
                    title: response.title,
                    text: response.msg,
                })
            }
        }
    });
}



// -------------------------------------------------- CHECK BOXES -------------------------------------------------- //
// Function to get selected id of file
function getSelectedIds() {
    var selectedIds = [];
    $('.item-checkbox:checked').each(function() {
        selectedIds.push($(this).closest('tr').find('.item-checkbox').data('id'));
    });
    return selectedIds;
}

// Function to get selected directory of file
function getSelectedDir() {
    var selectedDirs = [];
    $('.item-checkbox:checked').each(function() {
        selectedDirs.push($(this).closest('tr').find('.item-checkbox').data('dir'));
    });
    return selectedDirs;
}

// Function to get selected name of file
function getSelectedName() {
    var selectedNames = [];
    $('.item-checkbox:checked').each(function() {
        selectedNames.push($(this).closest('tr').find('.item-checkbox').data('name'));
    });
    return selectedNames;
}

// Function to update the "Check All" checkbox state
function updateCheckAllCheckbox() {
    var allChecked = $('.item-checkbox:checked').length > 0;
    $('#checkAll').prop('indeterminate', allChecked);
    $('#checkAll').prop('checked', allChecked);
}

// Function to add the 'fa-bounce' class when at least one row is checked
function updateTrashIconClass() {
    var isChecked = $('.item-checkbox:checked').length > 0;
    if (isChecked) {
        $('#deleteSelected').addClass('fa-bounce');
    } else {
        $('#deleteSelected').removeClass('fa-bounce');
    }
}

// Function to add the 'fa-bounce' class when at least one row is checked
function updateClearButton() {
    var isChecked = $('.item-checkbox:checked').length > 0;
    if (isChecked) {
        $("#btnClear").removeClass("hidden");
        $("#btnClear").prop("disabled", false);
    } else {
        $("#btnClear").addClass("hidden");
        $("#btnClear").prop("disabled", true);
    }
}

// Function to delete files that is checked by user
function deleteSelected() {
    // Get the status of the "Check All" checkbox
    var isChecked = $('#checkAll').prop('checked');
    var selectedIds = getSelectedIds();
    var selectedDirs = getSelectedDir();
    var selectedNames = getSelectedName();

    // If the "Check All" checkbox is checked, perform your delete action here
    if (isChecked && selectedIds.length > 0) {
        Swal.fire({
            title: 'Are you sure?',
            html: "You won\'t be able to revert this!<br><p style='color: #cc0000;'>" + selectedIds.length + " data will be deleted</p>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                
                // Show the progress bar Swal modal
                progressBar('Deleting Data...');
                
                // Send the selected IDs to the server for deletion
                $.ajax({
                    type: 'POST',
                    url: '../backend/func/clearFiles.php',
                    data: { selectedIds: selectedIds, selectedDirs: selectedDirs, selectedNames: selectedNames},
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
                        if (response.status == 'success') {
                            Swal.fire({
                                icon: response.status,
                                title: response.title,
                                text: response.msg,
                            }).then(function() {
                                window.location = "file";
                            });
                        } else {
                            Swal.fire({
                                icon: response.status,
                                title: response.title,
                                text: response.msg,
                            });
                        }
                    }
                });
            }
        });
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'No Data Selected',
            text: 'Please select the Data you want to delete'
        });
    }
}



// -------------------------------------------------- DOWNLOAD DATA BUTTON -------------------------------------------------- //
// Function to handle download data
function downloadingData(endpoint) {
    url = "../backend/admin/" + endpoint;
    window.location.href = url + '?download=true';
    Swal.fire({
        icon: 'success',
        title: 'Download Success',
        html: 'Please wait, your download will start in a moment',
    })
}


function downloadExcel(fileId, fileName, filePath) {

    // Show the progress bar Swal
    loadingSwal("Preparing Download...");

    // Send an AJAX request to the server to initiate the download
    $.ajax({
        url: '../backend/user/download.php',
        type: 'POST',
        data: { 
            filePath: filePath,
            fileId: fileId
        },
        xhrFields: {
            responseType: 'arraybuffer' // Set the response type to arraybuffer for large file download
        },
        success: function(response, status, xhr) {
            // Check if the response is an arraybuffer
            if (response instanceof ArrayBuffer) {
                // Create a Blob from the arraybuffer
                var blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });

                // Create a download link and trigger the download
                var url = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);

                Swal.fire({
                    icon: 'success',
                    title: 'Download Successful!'
                });
            } else {
                // Handle unexpected response
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unexpected response from the server.'
                });
            }
        },
        error: function(xhr, status, error) {
            // Handle AJAX errors
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to download the file.'
            });
        }
    });
}



// --------------------------------------------------- UPLOAD FILES BUTTON -------------------------------------------------- //
// Function to handle to check file name when user uploading a files
function checkingFiles() {
    const formData = new FormData($('#formAdd')[0]);
    formData.append('type', 'check');
    formData.append('selectedValue', $('#sharedUser').val());
    
    // Show the progress bar Swal modal
    progressBar('Checking Files...');

    // Use AJAX to send the files to the server for processing
    $.ajax({
        type: 'POST',
        url: '../backend/admin/kelola_file.php',
        data: formData,
        contentType: false,
        processData: false,
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
        success: function (response) {
            response = JSON.parse(response);
            
            if(response.status == 'confirmation'){
                Swal.fire({
                    title: "File Exists!",
                    html: response.msg,
                    icon: "warning",
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Replace the Files in Destination",
                    denyButtonText: "Skip These Files",
                    cancelButtonText: "Cancel",
                    denyButtonColor: "#e6b543",
                    cancelButtonColor: "#d33",
                    customClass: {
                        confirmButton: 'swal-button-column',
                        denyButton: 'swal-button-column',
                        cancelButton: 'swal-button-column'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        uploadingFiles(formData, 'update');
                    } else if (result.isDenied) {
                        uploadingFiles(formData, 'insert');
                    } else {
                        Swal.fire("Cancelled", "File uploads were cancelled.", "info");
                    }
                });
            } else if(response.status == 'insert all'){
                uploadingFiles(formData, 'insert');
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

// Function to uploading files
function uploadingFiles(formData, type) {
    formData.set('type', type);

    // Show the progress bar Swal modal
    progressBar('Uploading Files...');

    $.ajax({
        type: 'POST',
        url: '../backend/admin/kelola_file.php',
        data: formData,
        contentType: false,
        processData: false,
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
        success: function (response) {
            response = JSON.parse(response);
            Swal.fire({
                icon: response.status,
                title: response.title,
                html: response.msg,
            }).then(function() {
                window.location = "file";
            });
        }
    });
}



// ------------------------------------------------------ DELETE BUTTON ----------------------------------------------------- //
// Function to handle button delete in Kelola Admin and Kelola User
function buttonDelete(id, level, name, role) {
    const txtRole = role == 'admin' ? 'Admin' : 'User';
    Swal.fire({
        title: 'Remove ' + level,
        text: "Are you sure you want to delete " + name + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {

            // Show the progress bar Swal modal
            progressBar('Deleting ' + txtRole + '...');

            $.ajax({
                type: 'POST',
                url: '../backend/admin/kelola_' + role + '.php',
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
                        window.location = role;
                    })
                }
            });
        }
    })
}



// -------------------------------------------------------- DASHBOARD ------------------------------------------------------- //
const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];
const currentDate = new Date();
const currentYear = currentDate.getFullYear();
const currentMonth = monthNames[currentDate.getMonth()];

var dateChart;

// Function to get current date 
function getCurrentDate() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0
    var yyyy = today.getFullYear();

    return yyyy + '-' + mm + '-' + dd;
}

// Function to change yyyy-mm-dd to dd mmmm yyyy
function changeDateFormat(inputDate) {

    // Parse the input date as a JavaScript Date object
    var parsedDate = new Date(inputDate);

    // Get day, month, and year from the parsed date
    var day = parsedDate.getDate();
    var monthIndex = parsedDate.getMonth(); // Month index (0-11)
    var year = parsedDate.getFullYear();

    // Format the date as "dd mmmm yyyy"
    var formattedDate = day + " " + monthNames[monthIndex] + " " + year;
    return formattedDate;
}

// Function to display date range of Graph Date
function displayDateRange(text) {
    document.getElementById("dateRangeText").textContent = text;
}

// Function for chart plugin
function plugin() {
    return {
        legend: {
            display: true,
            position: 'bottom'
        }
    }
}

// Function for chart options
function options(type) {
    if (type == 'vertical') {
        return {
            scales: { y: { beginAtZero: true, maxTicksLimit: 5 } },
            aspectRatio: 2.5,
            plugins: plugin()
        };
    } else if (type === 'pie') {
        return {
            aspectRatio: 2,
            plugins: {
                legend: {
                    position: 'right',
                },
                datalabels: {
                    color: '#fff', // Label text color
                    anchor: 'end', // Label anchor position
                    align: 'start', // Label alignment
                },
            },
        };
    } else {
        return {
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } },
            aspectRatio: 2,
            plugins: plugin()
        };
    }
}

// Function for chart data
function dataOpt(labels, values, chartType, color){
    if (chartType == 'line') {
        return {
            labels: labels,
            datasets: [{
                label: 'Downloads Count',
                data: values,
                backgroundColor: '#ff9a1712',
                borderColor: '#ff9a17',
                borderWidth: 2,
                fill: true,
                lineTension: 0.4,
            }]
        };
    } else if (chartType === 'pie') {
        return {
            labels: labels,
            datasets: [{
                data: values,
                label: 'Downloads Count',
                backgroundColor: generateRandomColors(values.length),
                borderColor: color,
                borderWidth: 1
            }]
        };
    } else {
        return {
            labels: labels,
            datasets: [{
                label:  'Downloads Count',
                data: values,
                backgroundColor: color + '12',
                borderColor: color,
                borderWidth: 1
            }]
        };
    }
}

// Function to generate random colors for pie charts
function generateRandomColors(count) {
    var colors = [];
    for (var i = 0; i < count; i++) {
        var randomColor = '#' + Math.floor(Math.random()*16777215).toString(16);
        colors.push(randomColor);
    }
    return colors;
}

// Function when user change an option (GRAPH OF TOTAL DOWNLOADS)
function updateOption() {

    const groupFilter = groupBy.value;
    var timeFilter = selectDate.value;

    // Below if else is for hide/unhide the option 'this month'
    const optionToHide = document.querySelector('#selectDate option[value="month"]');
    if (groupFilter == 'byDate') {
        optionToHide.style.display = '';
    }
    
    else if (groupFilter == 'byMonth') {
        optionToHide.style.display = 'none';
        if (timeFilter == 'month') {
            selectDate.selectedIndex = 0;
            fetchDataAndCreateChart(groupFilter, 'allTime');
            return;
        }
    }

    
    // Display date range
    if (groupFilter == 'byDate') {
        if (timeFilter == 'month') {
            var lastDayOfMonth = new Date(currentYear, currentDate.getMonth() + 1, 0).getDate();
            var date = '01 ' + currentMonth + ' ' + currentYear + ' - ' + lastDayOfMonth + ' ' + currentMonth + ' ' + currentYear;
            displayDateRange(date);
        } 
        else if (timeFilter == 'year') {
            var date = '01 January ' + currentYear + ' - 31 December ' + currentYear;
            displayDateRange(date);
        }
    }

    else if (groupFilter == 'byMonth') {
        if (timeFilter == 'year') {
            var date = 'January ' + currentYear + ' - December ' + currentYear;
            displayDateRange(date);
        }
    }


    // If not custom, update. If custom, create swal to input date range
    if (timeFilter != 'custom') {
        fetchDataAndCreateChart(groupFilter, timeFilter);
    } else if (timeFilter == 'custom') {

        if (groupFilter == 'byDate') {
            // Open Swal modal Date Input
            Swal.fire({
                title: 'Input Date Range',
                html: '<input type="date" id="dateStart" name="dateStart" class="date-input" max="' + getCurrentDate() + '"> - <input type="date" id="dateEnd" name="dateEnd" class="date-input" max="' + getCurrentDate() + '">',
                allowOutsideClick: false,
                focusConfirm: false,
                showCancelButton: true,
                preConfirm: () => {
                    const dateStartInput = document.getElementById('dateStart');
                    const dateEndInput = document.getElementById('dateEnd');

                    const startDate = dateStartInput.value;
                    const endDate = dateEndInput.value;

                    if (!startDate || !endDate) {
                        Swal.showValidationMessage('Please enter both start and end dates');
                        return false;
                    }

                    const startDateObj = new Date(startDate);
                    const endDateObj = new Date(endDate);

                    if (startDateObj > endDateObj) {
                        Swal.showValidationMessage('End date must be after start date');
                        return false;
                    }

                    return {
                        startDate: startDateObj.toISOString().slice(0, 10),
                        endDate: endDateObj.toISOString().slice(0, 10)
                    };
                }
            }).then(result => {
                if (result.value) {
                    
                    timeFilter = result.value.startDate + " | " + result.value.endDate;
                    fetchDataAndCreateChart('byDate', timeFilter);

                    var date = changeDateFormat(result.value.startDate) + " - " + changeDateFormat(result.value.endDate);
                    displayDateRange(date);

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // User clicked cancel, back to allTime
                    selectDate.selectedIndex = 0;
                    fetchDataAndCreateChart();
                }
            });
        }
        
        else if (groupFilter == 'byMonth') {
            // Open Swal modal Date Input
            Swal.fire({
                title: 'Input Month Range',
                html: '<input type="month" id="monthStart" name="monthStart" class="date-input" max="' + getCurrentDate().substring(0, 7) + '"> - <input type="month" id="monthEnd" name="monthEnd" class="date-input" max="' + getCurrentDate().substring(0, 7) + '">',
                allowOutsideClick: false,
                focusConfirm: false,
                showCancelButton: true,
                preConfirm: () => {
                    const monthStartInput = document.getElementById('monthStart');
                    const monthEndInput = document.getElementById('monthEnd');

                    const startmonth = monthStartInput.value;
                    const endmonth = monthEndInput.value;

                    if (!startmonth || !endmonth) {
                        Swal.showValidationMessage('Please enter both start and end months');
                        return false;
                    }

                    const startmonthObj = new Date(startmonth);
                    const endmonthObj = new Date(endmonth);
                    endmonthObj.setMonth(endmonthObj.getMonth() + 1, 0);

                    if (startmonthObj > endmonthObj) {
                        Swal.showValidationMessage('End month must be after start month');
                        return false;
                    }

                    return {
                        startmonth: startmonthObj.toISOString().slice(0, 10),
                        endmonth: endmonthObj.toISOString().slice(0, 10)
                    };
                }
            }).then(result => {
                if (result.value) {
                    
                    timeFilter = result.value.startmonth + " | " + result.value.endmonth;
                    fetchDataAndCreateChart('byMonth', timeFilter);

                    var date = changeDateFormat(result.value.startmonth) + " - " + changeDateFormat(result.value.endmonth);
                    displayDateRange(date);

                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // User clicked cancel, back to allTime
                    selectDate.selectedIndex = 0;
                    fetchDataAndCreateChart('byMonth');
                }
            });
        }
    }
}

// Function to load data from database as a table
function getDashboardLogDownloads() {
        
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
                                <td style="font-size: 14px;">${el.file_name}</td>
                                <td style="text-align:center; font-size: 14px;">${el.full_name} (${el.npk_user})</td>
                                <td style="text-align:center; font-size: 14px; ${additionalStyle}">${el.position}</td>
                                <td style="text-align:center; font-size: 14px;">${el.formatted_size}</td>
                                <td style="text-align:center; font-size: 14px;">${dateDownloaded}</td>
                            </tr>`;
            })
            $('#logTable').html(content);
            $('.table').DataTable({
                scrollCollapse: true,
                order: [[4, 'desc']],
                columnDefs: [
                    {
                        targets: [0],
                        width: '24%'
                    },
                    {
                        targets: [1],
                        width: '24%',
                    },
                    {
                        targets: [2],
                        width: '24%'
                    },
                    {
                        targets: [3],
                        width: '12%',
                        type: 'file-size'
                    },
                    {
                        targets: [4],
                        width: '16%',
                        type: 'custom-date'
                    }
                ],
                lengthMenu: [[5, 20, 100, -1], [5, 20, 100, "All"]],
            });
        }
    });
}

// Fetch data and create the initial chart (GRAPH OF TOTAL DOWNLOADS)
function fetchDataAndCreateChart(groupFilter = 'byDate', timeFilter = 'allTime') {
    // Get the canvas element
    var canvas = document.getElementById('graphDate');
    var ctx = canvas.getContext('2d');

    // Check if there's an existing chart associated with the canvas
    if (dateChart) {
        // Destroy the existing chart
        dateChart.destroy();
    }

    fetch('../backend/func/dashboard.php?graph=date&group=' + groupFilter + '&time=' + timeFilter)
        .then(response => response.json())
        .then(data => {
            // Extract labels and data from the JSON response
            var labels = data.map(item => item.date_downloaded.split(' ')[0]);
            const values = data.map(item => item.count);

            var startDate = changeDateFormat(labels[0]);
            var endDate = changeDateFormat(labels[labels.length - 1]);

            if (groupFilter == 'byDate' && timeFilter == 'allTime'){
                var date = startDate + ' - ' + endDate;
                displayDateRange(date);
            } 
            else if (groupFilter == 'byMonth'){
                labels = data.map(item => changeDateFormat(item.date_downloaded.split(' ')[0]).split(' ')[1] + ' ' + changeDateFormat(item.date_downloaded.split(' ')[0]).split(' ')[2]);

                if (timeFilter == 'allTime') {
                    var monthStart = startDate.split(' ')[1] + ' ' + startDate.split(' ')[2];
                    var monthEnd = endDate.split(' ')[1] + ' ' + endDate.split(' ')[2];
                    displayDateRange(monthStart + ' - ' + monthEnd);
                }
            }

            // Create a chart using Chart.js
            dateChart = new Chart(ctx, {
                type: 'line',
                data: dataOpt(labels, values, 'line', '#ff9a17'),
                options: options('vertical')
            });
    });
}

// Function to fetch data to graph
function fetchData() {
    // Fetch data from PHP script to create File Graph
    fetch('../backend/func/dashboard.php?graph=file')
        .then(response => response.json())
        .then(data => {
            // Extract labels and data from the JSON response
            const labels = data.map(item => item.file_name);
            const values = data.map(item => item.count);
            
            // Create a chart using Chart.js
            var ctx = document.getElementById('graphFile').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: dataOpt(labels, values, 'bar', '#0fe100'),
                options: options('bar')
            });
    });

    // Fetch data from PHP script to create User Graph
    fetch('../backend/func/dashboard.php?graph=user')
        .then(response => response.json())
        .then(data => {
            // Extract labels and data from the JSON response
            const labels = data.map(item => item.username);
            const values = data.map(item => item.count);
            
            // Create a chart using Chart.js
            var ctx = document.getElementById('graphUser').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'pie',
                data: dataOpt(labels, values, 'pie', '#00d5ed'),
                options: options('pie')
            });
    });
}