<?php
include 'functions.php'; 
if (loggedin()) 
{
    $id=$_COOKIE['login_adminid'];
	header("Location: profile.php?id=".$id);
	exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <meta name="author" content="Coderthemes">

        <!-- App Favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.png">

        <!-- App title -->
        <title>Admin</title>

        <!-- App CSS -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/custom.css" rel="stylesheet" type="text/css" />
        <script src="assets/js/modernizr.min.js"></script>
        <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        input[type=number] {
        -moz-appearance:textfield;
        }
        .alert-primary {
            color: #000000;
            background-color: #cce5ff;
            border-color: #b8daff;
        }
        </style>
    </head>
    <body>

        <div class="clearfix"></div>
        <div class="wrapper-page" style="overflow-y:hidden">        
        	<div class="card-box">               
                <div class="panel-body">
                    <form class="form-horizontal" method="post">
                        <div class="text-center">
                            <img src="assets/images/logo.png" class="logo-img">
                        </div>
                        <div class="alert alert-primary" role="alert" >                        
                        </div>
                        <div class="form-group m-t-20">
                            <div class="col-xs-12">
								 <label for="exampleInputNumber" class="login-lbl-color" >Contact Number</label>
                                <input class="form-control" type="text" required="" name="Contact Number"
                                 id="contact" maxlength="10"  pattern="[0-9]{10}" autocomplete="off"
                                 onkeyup="checkform()">
                            </div>
                            <span class="m-l-15" id="errNumber"></span>
                        </div>  
                        <div class="form-group m-t-20 code-display">
                            <div class="col-xs-12">
								 <label for="exampleInputNumber" class="login-lbl-color" >Code</label>
                                <input class="form-control" type="text" required="" 
                                 maxlength="4" pattern="[0-9]{4}" id="code" autocomplete="off">
                            </div>
                            <span class="m-l-15" id="errNumber"></span>
                        </div>   
                        <div class="m-b-10 pull-right timer" style="cursor:pointer">
                        <span id="resend" onclick="send_number()" class="load">Resend Code</span>
                        <span id="countdown"> 01:00 </span>
                        </div>				
                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button disabled type="button" class="load btn btn-custom btn-block waves-effect waves-light" 
                                  onclick="send_number()">Get Code</button>
                                 <button id="codesubmit" type="button" onclick="verifyCode()" class="btn btn-purple btn-block waves-effect waves-light">
                                    Submit
                                  </button>
                            </div>
                        </div>                     
                    </form>
                </div>
            </div>
    
        </div>
    
    	<script>
            var resizefunc = [];
        </script>

        <!-- jQuery  -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>
        <script src="assets/js/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/waves.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/jquery.nicescroll.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>

        <!-- App js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>
		<script >
        $( document ).ready(function() {
           $(".alert").hide();
           $(".code-display").hide();
           $(".timer").hide();
           $("#resend").hide();
           $('#codesubmit').hide();
        });
        // var contactValue = $("#contact").val();
            function checkform(){
                if(document.getElementById("contact").value.length==10)
                {
                    $(".load").attr("disabled", false);
                }
				else{
                    $(".load").attr("disabled", true);
				}
            };
    $(".load").click(function() {
        $(".timer").show();

        if(document.getElementById("contact").value.length==10)
        {
            var time = "01:00",
                parts = time.split(':'),
                minutes = +parts[0],
                seconds = +parts[1],
                span = $('#countdown');
                
                function correctNum(num) {
                return (num<10)? ("0"+num):num;
                }
            
                var timer = setInterval(function(){
                    seconds--;
                    if(seconds == -1) {
                        seconds = 59;
                        minutes--;
                    
                        if(minutes == -1) {
                            $("#resend").show();
                            $("#countdown").hide();

                            clearInterval(timer);
                            return;
                        }
                    }
                    span.text( correctNum(minutes) + ":" + correctNum(seconds));
                }, 1000);
        }
  }); 

            function send_number()
            {
                let contact=$("#contact").val();
                $("#countdown").show();
                
                            $.ajax({
                                type: "POST",
                                url: "./sendSMS.php",   
                                data:{Contact:contact},
                                success: function (result) {
                                    
                                    // console.log(result.trim())
                                    $(".alert").show();
                                    $(".alert").text(result);
                                    if(result.trim()=="Please check the Mobile Number")
                                    {
                                        $(".load").show();
                                        $("#resend").hide();
                                        $("#countdown").hide();
                                    }
                                    else{
                                    $(".code-display").show(1500);
                                    $(".load").hide();
                                    $('#codesubmit').show();
                                    }
                                  
                            }
                            });
                }

                function verifyCode()
                {
                    
                    let code=$("#code").val();
                    let contact=$("#contact").val();
                    $.ajax({
                                type: "POST",
                                url: "./verifyCode.php",   
                                data:{Contact:contact,Code:code},
                                success: function (result) {     
                                    // console.log(result.trim())
                                    if(result.trim()!="")
                                    {
                                        $(".alert").show();
                                        $(".alert").text(result);
                                    }
                                    else{
                                        $(".alert").hide();
                                    }
                                    if (result.trim()=="OTP verified Successfully.")
                                    {
                                        document.cookie = "userContactNo="+contact;
                                        window.location.href="forgotpasswordStep-2.php"
                                    }
                            }
                            });
                }
        </script>

	</body>
</html>