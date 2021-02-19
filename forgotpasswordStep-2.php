<?php
include 'functions.php'; 
// echo $_COOKIE['userContactNo'];
if (!(isset($_COOKIE['userContactNo']))) 
{
	header("Location: index.php");
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

        /* Firefox */
        input[type=number] {
        -moz-appearance:textfield;
        }
/* 
        .inner-addon {
            position: relative;
        }
        .inner-addon .fa {
        position: absolute;
        padding: 10px;            
        }
        .left-addon .fa  { left:  0px;}
        .right-addon .fa { right: 0px;}

        .left-addon input  { padding-left:  30px; }
        .right-addon input { padding-right: 30px; }

        .pointor-cousor{
            cursor:pointer;
        } */
        </style>
    </head>
    <body>

        <div class="clearfix"></div>
        <div class="wrapper-page" style="overflow-y:hidden">
        
        	<div class="card-box">
                
                <div class="panel-body">
                    <form class="form-horizontal" action="resetpassword.php" method="post" data-parsley-validate novalidate>
                        <div class="text-center">
                            <img src="assets/images/logo.png" class="logo-img">
                        </div>      
                        
                        <div class="form-group m-t-20">
                            <div class="col-xs-12">
								 <label for="exampleInputPassword" class="login-lbl-color" >Password</label>
                                 <!-- <input id="pass1" type="password" placeholder="Password" required
                                    class="form-control"> -->
                                    <!-- <div class="inner-addon right-addon">
                                        <i class="fa fa-eye" onclick="VisiblePassword()"></i> -->
                                        <input type="password" id="pass1" autocomplete="off" 
                                        name="Password" 
                                        placeholder="Password" 
                                        class="form-control" name="password" minlength="8"/>
                                    <!-- </div> -->
                            </div>                            
                        </div>   

                        <div class="form-group m-t-20">
                            <div class="col-xs-12">
								 <label for="exampleInputconfirm" class="login-lbl-color" >Confirm Password</label>
                                 <!-- <input data-parsley-equalto="#pass1" type="password" required
                                    placeholder="Password" class="form-control" id="passWord2"> -->
                                    <!-- <div class="inner-addon right-addon">
                                        <i class="fa fa-eye"  onclick="VisibleConfirmPassword()"></i> -->
                                        <input type="password" autocomplete="off"  
                                        data-parsley-equalto="#pass1" type="password" minlength="8"
                                        placeholder="Confirm Password" class="form-control" id="passWord2"/>
                                    <!-- </div> -->
                            </div>                           
                        </div>   


						
                        <div class="form-group text-center m-t-30">
                            <div class="col-xs-12">
                                <button class="btn btn-custom btn-block waves-effect waves-light" 
                                type="submit">Reset Password</button>
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
        <script type="text/javascript" src="assets/plugins/parsleyjs/dist/parsley.min.js"></script>
		<script >
            function checkform(){
                if(document.getElementById('code').value!="")
                {
                    document.getElementById('load').disabled = false;
                }
				else{
					document.getElementById('load').disabled = true;
				}
            };
            // function VisibleConfirmPassword()
            // {
            //     var x = document.getElementById("passWord2");
            //         if (x.type === "password") {
            //             x.type = "text";
            //         } else {
            //             x.type = "password";
            //         }
            // }
            // function VisiblePassword()
            // {
            //     var x = document.getElementById("pass1");
            //         if (x.type === "password") {
            //             x.type = "text";
            //         } else {
            //             x.type = "password";
            //         }
            // }
        </script>
	</body>
</html>