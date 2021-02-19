<?php
include "header.php";
// $entry_id=$_GET['id'];
$page = $_GET['page'];
// echo $_GET['id'];
// exit();
$entry_id = $_COOKIE['login_adminid'];
$First_Name = '';
$Last_Name = '';
$Email = '';
$Mobile_Number = '';
$Role_ID = '';
$Password = '';
$Profile_img = '';
$query1 = "SELECT * FROM " . $table_admin . " where ID='$entry_id' limit 1";

$res1 = mysqli_query($link, $query1);
if (mysqli_num_rows($res1) > 0) {
    while ($row1 = mysqli_fetch_array($res1)) {
        $First_Name = $row1['First_Name'];
        $Last_Name = $row1['Last_Name'];
        $Email = $row1['Email'];
        $Mobile_Number = $row1['Mobile_Number'];
        $Role_ID = $row1['Role_ID'];
        $Profile_img = $row1['profile_image'];
    }
}

require_once("csrf.class.php");
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);

?>
<style type="text/css">
    .profile-pic {
        max-width: 200px;
        max-height: 200px;
        display: block;
    }

    .file-upload {
        display: none;
    }

    .circle {
        border-radius: 1000px !important;
        overflow: hidden;
        width: 128px;
        height: 128px;
        border: 8px solid rgba(255, 255, 255, 0.7);
        position: relative;
        top: 72px;
    }

    img {
        max-width: 100%;
        height: auto;
    }

    .p-image {
        position: absolute;
        top: 167px;
        right: 30px;
        color: #666666;
        transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
    }

    .p-image:hover {
        transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
    }

    .upload-button {
        font-size: 1.2em;
    }

    .upload-button:hover {
        transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
        color: #999;
    }

    .inner-addon {
        position: relative;
    }

    .inner-addon .fa {
        position: absolute;
        padding: 10px;
        // pointer-events: none;
    }

    .left-addon .fa {
        left: 0px;
    }

    .right-addon .fa {
        right: 0px;
    }

    .left-addon input {
        padding-left: 30px;
    }

    .right-addon input {
        padding-right: 30px;
    }

    .pointor-cousor {
        cursor: pointer;
    }

    .font-karla {
        font-family: 'Karla' !important;
        color: #3b3e47 !important;
    }

    .modal-open .modal {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="topbar">

    <!-- LOGO -->
    <div class="topbar-left">
    </div>

    <div class="navbar navbar-default" role="navigation">
        <div class="container">

            <!-- Page title -->
            <ul class="nav navbar-nav navbar-left">
                <li>
                    <button class="button-menu-mobile open-left">
                        <i class="zmdi zmdi-menu"></i>
                    </button>
                </li>
                <li>
                    <h4 class="page-title font-karla">Update Your Profile</h4>
                </li>
            </ul>



        </div><!-- end container -->
    </div><!-- end navbar -->
</div>
<div class="content-page">
    <!-- Start content -->
    <div class="content">

        <div class="container">


            <div class="row">
                <div class="col-lg-2 col-xs-0"></div>
                <div class="col-lg-8 screen">
                    <div class="card-box">


                        <form enctype="multipart/form-data" id="db_entry_form" class="form-horizontal form-align" method="post" data-parsley-validate novalidate>
                            <input type="hidden" name="<?= $token_id; ?>" value="<?= $token_value; ?>" />
                            <input type="hidden" id="entry_id" name="entry_id" value="<?= $entry_id; ?>">

                            <div class="row" style="padding-bottom: 20px;">
                                <div class="col-lg-4"></div>
                                <div class="col-lg-3">

                                    <div id="pf_foto" name="profileImage" style="margin:0 auto;background-image: url('uploads/ProfileImages/<?= $Profile_img ?>');">
                                    </div>


                                    <label class="btn-bs-file btn btn-light mobile-icon upload-icon" style="padding:6px 12px;">
                                        <input type='file' id='verborgen_file' name="profileImage" value="<?= $Profile_img ?>" data-max-file-size="1M" accept="image/x-png,image/gif,image/jpeg" />
                                        <i class="fa fa-pencil fa-1" aria-hidden="true" id="uploadButton"></i>

                                    </label>
                                </div>
                                <div class="col-lg-4"></div>
                            </div>
                            <div class="row">

                                <div class="col-lg-5">
                                    <div class="form-group font-karla">
                                        <label for="userName">First Name<span class="text-red">*</span></label>
                                        <input type="text" name="First_Name" parsley-trigger="change" required placeholder="First Name" class="form-control" id="First_Name" value="<?= $First_Name; ?>" onkeyup="validateName(this.value)">
                                        <span id="errName"></span>
                                    </div>
                                </div>
                                <div class="col-lg-1"></div>
                                <div class="col-lg-5">
                                    <div class="form-group font-karla">
                                        <label for="userName">Last Name<span class="text-red">*</span></label>
                                        <input type="text" name="Last_Name" parsley-trigger="change" required placeholder="Last Name" class="form-control" id="Last_Name" value="<?= $Last_Name; ?>" onkeyup="validateSurname(this.value)">
                                        <span id="errLastName"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5">
                                    <div class="form-group font-karla">
                                        <label for="emailAddress">Email<span class="text-red">*</span></label>
                                        <input type="email" name="Email" parsley-trigger="change" required placeholder="Email" class="form-control" id="Email" value="<?= $Email; ?>" onkeyup="validateEmail(this.value)">
                                        <span id="errEmail"></span>
                                    </div>
                                </div>
                                <div class="col-lg-1"></div>
                                <div class="col-lg-5">
                                    <div class="form-group font-karla">
                                        <label class="control-label" for="example-input1-group3">Mobile Number<span class="text-red">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <button type="button" class="btn waves-effect waves-light btn-default dropdown-toggle" data-toggle="dropdown" style="overflow: hidden; position: relative;">+91 </button>
                                            </div>
                                            <input type="text" id="example-input1-group3" name="Mobile_Number" class="form-control" parsley-trigger="change" required onkeyup="validatePhoneNumber(this.value);distinctNumber(this.value)" placeholder="Mobile Number here" value="<?= $Mobile_Number; ?>" name="mobile_number">
                                        </div>
                                        <span id="errPhone"></span>
                                        <span id="distictnumber"></span>

                                    </div>
                                </div>
                            </div>

                            <div class="row ">
                                <div class="col-lg-11">
                                    <div class="form-group font-karla">
                                        <label class="pointor-cousor" for="changePassword" onclick="showPassword()" style="text-decoration:underline">Change Password</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="passwords" style="display:none">
                                <div class="col-lg-5">
                                    <div class="form-group font-karla">
                                        <label for="pass1">Password<span class="text-red">*</span></label>
                                        <div class="inner-addon right-addon">
                                            <i class="fa fa-eye" onclick="VisiblePassword()"></i>
                                            <input type="password" id="pass1" autocomplete="off" name="Password" onkeyup="validatePassword(this.value);" placeholder="Password" class="form-control" name="password" value="" minlength="6" />
                                        </div>
                                        <span id="errPassword"></span>
                                    </div>
                                </div>
                                <div class="col-lg-1"></div>
                                <div class="col-lg-5">
                                    <div class="form-group font-karla">
                                        <label for="passWord2">Confirm Password<span class="text-red">*</span></label>
                                        <div class="inner-addon right-addon">
                                            <i class="fa fa-eye" onclick="VisibleConfirmPassword()"></i>
                                            <input type="password" autocomplete="off" data-parsley-equalto="#pass1" type="password" placeholder="Confirm Password" class="form-control" id="passWord2" value="" name="confirm_password" onkeyup="confirmPassword(this.value);" />
                                        </div>
                                        <span id="errCPassword"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="font-karla form-group text-center m-b-0 p-t-10 m-t-15">
                                <button class="btn btn-purple w-md waves-effect waves-light" onclick="navigate(event)">
                                    Save
                                </button>
                                <button onclick="cancel()" type="reset" class="btn btn-default waves-effect waves-light m-l-15" style="padding:6px 32px">
                                    Cancel
                                </button>

                            </div>

                        </form>
                    </div>
                </div><!-- end col -->
            </div>




        </div> <!-- container -->

    </div> <!-- content -->

    <footer class="footer text-right">
        <?php echo date('Y'); ?> © Noora.
    </footer>

</div>


<?php
include "footer.php";
?>
<script type="text/javascript">
    $(document).ready(function() {
        $("#Email").attr("disabled", "disabled");
    });
    $('#verborgen_file').show();
    $('#uploadButton').on('click', function() {
        //bootbox.alert({show: false});
        $('#verborgen_file').change();

    });

    $('#verborgen_file').change(function() {
        //bootbox.alert({show: true});
        var file = this.files[0];
        var fileSize = this.files[0].size;
        var size = fileSize / 1000;
        let type = this.files[0].type;
        var reader = new FileReader();
        if (size < 576 && (type == "image/jpeg" || type == "image/png")) {
            reader.onloadend = function() {
                $(this).attr("pf_foto", "noora/assets/images/gallery/1.jpg");
                $('#pf_foto').css('background-image', 'url("' + reader.result + '")');
            }
            if (file) {
                reader.readAsDataURL(file);
            } else {}
        } else {
            //bootbox.alert({show: true});
            $('#verborgen_file').val('')
            bootbox.alert({
                message: "Please check the size and the type of the file",
                size: "small"
            });

            // return false;
        }

    });

    var errname = document.getElementById("errName");

    function validateName(name) {
        var mailformat = /^[a-zA-Z ]{1,25}$/;
        if (name == '') {
            errname.innerHTML = "Name cannot be empty";
            errname.style.color = "red";
        } else if (!name.match(mailformat)) {
            errname.innerHTML = "Name cannot contain special characters";
            errname.style.color = "red";
        } else {
            errname.innerHTML = "";
            errname.style.color = "none";
        }
    }
    var errlname = document.getElementById("errLastName");

    function validateSurname(lname) {
        var mailformat = /^[a-zA-Z ]{1,25}$/;
        if (lname == '') {
            errlname.innerHTML = "Last Name cannot be empty";
            errlname.style.color = "red";
        } else if (!lname.match(mailformat)) {
            errlname.innerHTML = "Last Name cannot contain special characters";
            errlname.style.color = "red";
        } else {
            errlname.innerHTML = "";
            errlname.style.color = "none";
        }
    }

    var errpassword = document.getElementById("errPassword");

    function validatePassword(password) {
        var ev = /^[a-zA-Z0-9!@#$%^&*`'":.,()?/{}]{6,25}$/;
        if (password == '') {
            errpassword.innerHTML = "password cannot be empty";
            errpassword.style.color = "red";
        } else if (!password.match(ev)) {
            errpassword.innerHTML = "Password should contain at least 6 characters";
            errpassword.style.color = "red";
        } else {
            errpassword.innerHTML = "";
            errpassword.style.color = "none";
        }
    }
    var errcpassword = document.getElementById("errCPassword");

    function confirmPassword(password) {
        var password1 = $('#pass1').val();
        var ev = /^[a-zA-Z0-9!@#$%^&*]{6,25}$/;
        if (password == '') {
            errcpassword.innerHTML = "password cannot be empty";
            errcpassword.style.color = "red";
        } else if (!password.match(ev)) {
            errcpassword.innerHTML = "Password should contain at least 6 characters";
            errcpassword.style.color = "red";
        } else if (password1 != password) {
            errcpassword.innerHTML = "Not matching with Password";
            errcpassword.style.color = "red";
        } else {
            errcpassword.innerHTML = "";
            errcpassword.style.color = "none";
        }
    }

    function VisibleConfirmPassword() {
        var x = document.getElementById("passWord2");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    function VisiblePassword() {
        var x = document.getElementById("pass1");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
    var errNumber = document.getElementById("errPhone");

    function validatePhoneNumber(number) {
        var phoneno = /^\d{10}$/;
        if (number == '') {
            errNumber.innerHTML = "Phone Number cannot be empty";
            errNumber.style.color = "red";
        } else if (!number.match(phoneno)) {
            errNumber.innerHTML = "Enter correct 10 digit phone number";
            errNumber.style.color = "red";
        } else {
            errNumber.innerHTML = "";
            errNumber.style.color = "none";
        }
    }
    var errdistinctNumber = document.getElementById("distictnumber");

    function distinctNumber(number) {
        let id = document.getElementById("entry_id").value;
        let table = "noora_nurse";
        $.ajax({
            type: "POST",
            url: "./check_Unique.php",
            data: {
                id: id,
                email: number,
                tableName: table
            },
            success: function(result) {
                if (result != "") {
                    if (result == 1) {
                        errdistinctNumber.innerHTML = 'Mobile Number already been used';
                        errdistinctNumber.style.color = 'red';
                    } else {
                        errdistinctNumber.innerHTML = '';
                        errdistinctNumber.style.color = '';
                    }
                }
            }
        });

    }

    function showPassword() {
        var x = document.getElementById("passwords");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

    function navigate(event) {
        event.preventDefault();
        var x = document.getElementById('passwords').style.display



        var db_entry_form = $("#db_entry_form");
        if (x == "block" && $('#pass1').val() == "" && $('#passWord2').val() == "") {

            bootbox.alert("Please fill Password fields");
            return false
        } else if (x == "block" && $('#pass1').val() != "" && $('#passWord2').val() == "") {
            bootbox.alert("Please fill Confirm Password");
            return false
        } else if (x == "block" && $('#pass1').val() == "" && $('#passWord2').val() != "") {
            bootbox.alert("Please fill Password");
            return false
        } else if (!(errname.innerHTML == "" && errNumber.innerHTML == "" && errlname.innerHTML == "" && errNumber.innerHTML == "" && errdistinctNumber.innerHTML == "")) {
            bootbox.alert("Please fill all the details correctly");
        } else if (db_entry_form.parsley().isValid() != true) {
            bootbox.alert("Please fill all the fields");
        } else {
            if (x == "none") {
                $('#pass1').val('')
                $('#passWord2').val('')
            }


            document.cookie = "selectedItem=" + 2;
            //action="profile_entry.php"
            $('#db_entry_form').attr('action', 'profile_entry.php')
            $("#db_entry_form").submit();

            //window.location.href = "admin_manager_list.php";

        }


    }

    function cancel() {
        location.reload();
    }
    var erremail = document.getElementById("errEmail");

    function validateEmail(email) {
        var mailformat = /^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,3})$/;
        if (email == '') {
            erremail.innerHTML = "Email cannot be empty";
            erremail.style.color = "red";
        } else if (!email.match(mailformat)) {
            erremail.innerHTML = "Please enter a valid email";
            erremail.style.color = "red";
        } else {
            erremail.innerHTML = "";
            erremail.style.color = "none";
        }
    }
</script>

<script type="text/javascript" src="assets/plugins/parsleyjs/dist/parsley.min.js"></script>