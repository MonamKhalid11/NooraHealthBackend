<?php
include 'functions.php';
// unset($_COOKIE['userContactNo']);
setcookie("userContactNo", "", time() - 3600); 
if (loggedin()) 
{
    $id=$_COOKIE['login_adminid'];
	header("Location: profile.php?id=".$id);
	exit();
}
$error_login = "";
if(ISSET($_GET['failed'])){
	if($_GET['failed']==1){
		$error_login = "Incorrect Password.";
	}else if($_GET['failed']==2){
		$error_login = "Admin Account Suspended.";
	}if($_GET['failed']==3){
		$error_login = "Admin User Not Registered.";
	}if($_GET['failed']==4){
		$error_login = "Email and Password required.";
	}
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
        
    </head>
    <body>

        <div class="clearfix"></div>
        <div class="wrapper-page" style="overflow-y:hidden">
        
        	<div class="card-box">
                
                <div class="panel-body">
                    <form class="form-horizontal " id="login_form" method="post">
                        <div class="text-center">
                            <img src="assets/images/logo.png" class="logo-img">
                        </div>
                        <div class="form-group m-t-20">
                            <div class="col-xs-12">
								 <label for="exampleInputEmail1" class="login-lbl-color" >Email</label>
                                <input class="form-control" type="text" required="" name="login_username" id="email"
                                onkeyup="validateEmail(this.value);checkform();" >
                            </div>
                            <span class="m-l-15" id="errEmail"></span>
                        </div>

                        <div class="form-group">
                            <div class="col-xs-12">
								<label for="exampleInputPassword1" class="login-lbl-color" >Password</label>
                                <input class="form-control" type="password" required="" name="login_password" id="password"
                                onkeyup="checkform();/*validatePassword(this.value)*/">
                            </div>
                            <span class="m-l-15" id="errPassword"></span>
                        </div>

                       <!-- <div class="form-group m-t-10 m-b-0">
                            <div class="col-sm-12 ">
                                <a href="forgotpassword.php" class="login-lbl-color pull-right">
                                    Forgot password?
                                </a>
                            </div>
                        </div>-->
                        
						<p class="text-red"><?php echo $error_login;?></p>
                        <div class="form-group text-center m-t-10">
                            <div class="col-xs-12">
                                <button disabled class="btn btn-custom btn-block waves-effect waves-light" 
                                type="submit" id="load"  data-loading-text="<i class='fa fa-spinner fa-spin '>
                                </i> Signing In">Login</button>
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
         <!-- bootbox code -->
         <script src="assets/js/bootbox.min.js"></script>
        <script src="assets/js/bootbox.locales.min.js"></script>
		<script >
  
            var erremail = document.getElementById("errEmail");
            function validateEmail(email)
			{
                var mailformat = /^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,3})$/;     
				if(!email.match(mailformat))
					{
						erremail.innerHTML	= "Please enter a valid email";
						erremail.style.color = "red";
					}
				else
					{
						erremail.innerHTML	= ""; 
						erremail.style.color = "none";
					}
            }
			
			var errpassword = document.getElementById("errPassword");
			/* function validatePassword(password)
			{
                var ev = /^[a-zA-Z0-9!@#$%^&*]{6,25}$/;
			    if(!password.match(ev))
					{
						//errpassword.innerHTML	= "Password should contain at least 6 characters";
						//errpassword.style.color = "red";
					}
				else
					{
						errpassword.innerHTML	= " "; 
						errpassword.style.color = "none";
					}	
			} */

            function checkform(){
                console.log("erremail.innerHTML",erremail.innerHTML)
                if(document.getElementById('email').value!='' && document.getElementById('password').value!='' && erremail.innerHTML=="" )
                {
                    document.getElementById('load').disabled = false;
                }
				else {
					document.getElementById('load').disabled = true;
				}
           
            };
            $('.btn').on('click', function() {

                
            if(erremail.innerHTML=="")
            {
                var self = $(this);
                self.button('loading');
                setTimeout(function() {
                    self.button('reset');
            }, 8000);
                $('#login_form').attr("action",'login_entry.php'); 
            }
            else
            {
                bootbox.aler("hai")
            }
                
            });

        </script>
	</body>
</html>