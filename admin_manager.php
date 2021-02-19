<?php 
include "header.php";
$entry_id=0;
if(isset($_GET['id']))
{
	$entry_id=$_GET['id'];
}
$First_Name='';
$Last_Name='';
$Email='';
$Mobile_Number='';
$Role_ID='';
$state_id=0;
$state_name="";
$country_id=0;
$country_name="";
$CityID=0;
if($entry_id>0)
{
$query= "SELECT * FROM ".$table_admin." WHERE ID=".$entry_id;
$res= mysqli_query($link,$query);
if(mysqli_num_rows($res)>0)
{
	while($row = mysqli_fetch_array($res))
	{
        $id=$row['ID'];
        $First_Name=$row['First_Name'];
        $Last_Name=$row['Last_Name'];
        $Email=$row['Email'];
        $Mobile_Number=$row['Mobile_Number'];
        $Role_ID=$row['Role_ID'];
        $CityID=$row["District_ID"];
        if($CityID>0)
        {
            $query2="SELECT ".$table_noora_state.".ID as State_ID,".$table_noora_state.".Name as State_Name,
			".$table_noora_country.".ID as Country_Id,".$table_noora_country.".Name as Country_Name from noora_district LEFT JOIN ".$table_noora_state." ON ".$table_noora_state.".ID=noora_district.State_ID
            LEFT JOIN ".$table_noora_country." ON ".$table_noora_country.".ID=".$table_noora_state.".Country_ID WHERE noora_district.ID=".$CityID;
            $res2= mysqli_query($link,$query2);
            if(mysqli_num_rows($res2)>0)
            {
                while($row2 = mysqli_fetch_array($res2))
	            {
                    $state_id=$row2["State_ID"];
                    $state_name=$row2["State_Name"];
                    $country_id=$row2["Country_Id"];
                    $country_name=$row2["Country_Name"];
                }
            }

        }
	
	}
}
}
$country_list=array();
$state_list=array();
$city_list=array();
if($country_id>0)
{
    $query1= "SELECT * FROM ".$table_noora_state." WHERE Country_ID=".$country_id;
    $res1= mysqli_query($link,$query1);
    if(mysqli_num_rows($res1)>0)
    {
        while($row1 = mysqli_fetch_array($res1))
        {
            $state_list[]=$row1;
        }
    }
}
if($state_id>0)
{
    $query1= "SELECT * FROM ".$table_noora_district." WHERE State_ID=".$state_id;
    $res1= mysqli_query($link,$query1);
    if(mysqli_num_rows($res1)>0)
    {
        while($row1 = mysqli_fetch_array($res1))
        {
            $city_list[]=$row1;
        }
    }
}

$query1= "SELECT * FROM ".$table_noora_country." where Status !=3";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $country_list[]=$row1;
    }
   
    
}

require_once("csrf.class.php");
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);
if($Role_ID=="")
{
    $Role_ID="2";
}
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
.font-karla{
font-family:'Karla' !important;
color:#3b3e47 !important;
}
.edit-clr{
  color:#4BB75E;
}
.clearfix{
    clear:both;
}
.btn-styles{
  border-radius:4px;
  border:1px solid #CAD1D6;
  color:#858C91;
}
.modal-open .modal {
    display: flex !important;
    align-items: center;
    justify-content: center;
}
</style>
             <div class="topbar">

                <div class="topbar-left">
                </div>

                <!-- Button mobile view to collapse sidebar menu -->
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
                                <h4 class="page-title font-karla">Add/Edit Admin and Manager</h4>
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
                            <div class="col-lg-8 screen" >
                                <div class="card-box">
                    
                                <button id="delete" onclick="deleteUser('<?=$entry_id?>')" class="m-l-15 pull-right btn btn-light btn-trans waves-effect 
                                    waves-light m-b-5 font-karla btn-styles" style="background:#ffffff;">
                                    <i class="fa fa-trash-o text-danger" ></i> Delete </button> 
                                <button id="edit" class="btn btn-trans waves-effect waves-light
                                 m-b-5 font-karla pull-right btn-styles" style="background:#ffffff;">
                                <i class="fa fa-pencil m-r-5 edit-clr" ></i> <span>Edit&nbsp;&nbsp;</span> 
                                </button> 

                                
                                    <div class="clearfix"></div>
									<form id="db_entry_form"  action="admin_manager_entry.php" class="form-horizontal form-align" method="post" data-parsley-validate novalidate>
										<input type="hidden" name="<?= $token_id; ?>" value="<?= $token_value; ?>" />
										<input type="hidden" id="entry_id" name="entry_id" value="<?= $entry_id;?>">  
											<div class="row">

												<div class="col-lg-5">
													<div class="form-group">
														<label class="font-karla" for="userName">First Name<span class="text-red">*</span></label>
														<input type="text" name="First_Name" parsley-trigger="change" required
                                                               placeholder="First Name" class="inputDisabled form-control" id="First_Name" 
                                                               onkeyup="validateName(this.value)" disabled value="<?=$First_Name;?>">
                                                        <span id="errName"></span>                                                   

													</div>
												</div>
												<div class="col-lg-1"></div>
												<div class="col-lg-5">
													<div class="form-group font-karla">
														<label for="userName">Last Name<span class="text-red">*</span></label>
														<input type="text" name="Last_Name" parsley-trigger="change" required
                                                               placeholder="Last Name" class="inputDisabled form-control" id="Last_Name" 
                                                               value="<?= $Last_Name;?>" disabled onkeyup="validateSurname(this.value)" >
                                                               <span id="errLastName"></span>                                                       
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-5">
													<div class="form-group font-karla">
														<label for="emailAddress">Email<span class="text-red">*</span></label>
														<input type="email" name="Email" parsley-trigger="change" required
														onkeyup="validateEmail(this.value);distinctEmail(this.value)"
                                                         placeholder="Email" class="inputDisabled form-control" disabled id="Email" value="<?= $Email;?>"
                                                        >
                                                        <span id="errEmail"></span>
                                                        <span id="distictEmail"></span>
                                                    </div>
												</div>
                                                <div class="col-lg-1"></div>

                                                <div class="col-lg-5">
													<div class="form-group font-karla">
														<label class="control-label" for="example-input1-group3">Mobile Number<span class="text-red">*</span></label>
														<div class="input-group">
															<div class="input-group-btn">												
																<button type="button" class="btn waves-effect waves-light btn-default dropdown-toggle" data-toggle="dropdown" style="overflow: hidden; position: relative;">+91 <!--span class="caret"></span--></button>
																
															</div>
                                                            <input type="text" id="example-input1-group3" name="Mobile_Number" parsley-trigger="change" required class="inputDisabled form-control" 
                                                            placeholder="Mobile Number" disabled value="<?= $Mobile_Number;?>" onkeyup="validatePhoneNumber(this.value);distinctNumber(this.value)">
                                                        </div>
                                                        <span id="errPhone"></span>
                                                        <span id="distictnumber"></span>
                                                       
													</div>
                                                 </div>

											</div>
                                             <div class="row">
                                                    <div class="col-lg-3">
                                                        <div class="form-group font-karla">
                                                            <label for="country">Country<span class="text-red">*</span></label>
                                                            <select class="form-control select2 inputDisabled" name="country" id="country" disabled required="" onChange="getState(this.value)">
                                                                <!-- <option value="0" selected >Select</option> -->
                                                              <?php  foreach($country_list as $key=>$value)
                                                                    {?>
                                                                        <option <?php if($value["ID"]==$country_id) echo 'selected="selected"'?> value="<?=$value["ID"]?>" ><?=$value["Name"]?></option>
                                                                  <?php  }?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1"></div>
                                                      <div class="col-lg-3">
                                                        <div class="form-group font-karla">
                                                            <label for="state">State<span class="text-red">*</span></label>
                                                            <select class="form-control select2 inputDisabled" required="" disabled name="state" id="state" onChange="getCity(this.value)">
                                                                <!-- <option value="0" selected >Select</option> -->
                                                                <?php 
                                                                if(!empty($state_list)){
                                                                 foreach($state_list as $key=>$value)
                                                                    {?>
                                                                        <option <?php if($value["ID"]==$state_id) echo 'selected="selected"'?> value="<?=$value["ID"]?>" ><?=$value["Name"]?></option>
                                                                  <?php  }}?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                     <div class="col-lg-1"></div>
                                                      <div class="col-lg-3">
                                                        <div class="form-group font-karla">
                                                            <label for="city">District<span class="text-red">*</span></label>
                                                            <select class="form-control select2 inputDisabled" required="" disabled name="city" id="city" >
                                                            <option value="0" selected >Select</option>
                                                            <?php 
                                                                if(!empty($city_list)){
                                                                 foreach($city_list as $key=>$value)
                                                                    {?>
                                                                        <option <?php if($value["ID"]==$CityID) echo 'selected="selected"'?> value="<?=$value["ID"]?>" ><?=$value["Name"]?></option>
                                                                  <?php  }}?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                        <div class=" font-karla">
                                                            <label for="pass1">Role<span class="text-red">*</span></label>
                                                        
                                                        </div>
                                             
                                                <div class="row">
                                                    <div class="col-lg-1">
                                                        <div class="radio radio-purple font-karla">
                                                            <input type="radio" class="inputDisabled" disabled name="role" value="2" id="admin" <?php if($Role_ID=="2") echo 'checked="checked"';?> required>
                                                            <label for="admin">
                                                                Admin
                                                            </label>
                                                        </div>
                                                   
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="radio radio-purple font-karla">
                                                            <input type="radio" class="inputDisabled" disabled name="role" value="3" <?php if($Role_ID=="3") echo 'checked="checked"';?> id="manager">
                                                            <label for="manager">
                                                                Manager
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>                                                    
                                        <div class="form-group text-center m-b-0 font-karla p-t-10 m-t-15">
                                      
                                                <?php if($entry_id==0){?>  <button class="btn btn-purple w-md waves-effect waves-light " 
                                                type="button"  onclick="saveData()">   Save
                                            </button><?php }
                                                    else{?><button disabled class="btn inputDisabled btn-purple w-md waves-effect waves-light "
                                                     type="button"  onclick="saveData()">   Update
                                                        </button><?php }?>

                                            <a href="admin_manager_list.php" class="btn  btn-default waves-effect waves-light m-l-15" style="padding:6px 32px">
                                                Cancel
                                            </a>
                                             
                                        </div>

                                    </form>
                                </div>
                            </div><!-- end col -->
                         </div>




                    </div> <!-- container -->

                </div> <!-- content -->

                <footer class="footer text-right">
                    <?php echo date('Y');?> © Noora.
                </footer>

            </div>

<?php 
include "footer.php";
?>
<!-- Validation js (Parsleyjs) -->
<script type="text/javascript">
$(document).ready(function() {
let country_ID = <?php echo $country_id; ?> ;
        if(country_ID=="")
        {
            country_ID=1;
        }
    $.ajax({
       type: "POST",
       url: "./get_state.php",   
       data:{country_id:country_ID},
       success: function (result) {
        if(result!="")
        {
            $('#state').html(result);
            let value= <?php echo $state_id; ?> ;
            if(value=="")
            {
                value=3;
            }
            $('#state').select2().select2('val',value)   
             $.ajax({
                type: "POST",
                url: "./get_district.php",   
                data:{state_id:value},
                success: function (result) {
                if(result!="")
                {
                    let city_id = <?php echo $CityID; ?> ;
                    if(city_id=="")
                    {
                        city_id=1;
                    }
                    $('#city').select2().select2('val',city_id)  
                }
                }
            });      
        }
       }
  });

});



 $(".select2").select2();

$(".select2-limiting").select2({
  maximumSelectionLength: 2
});

$('#verborgen_file').show();
        $('#uploadButton').on('click', function () {
              $('#verborgen_file').click();
        });

        $('#verborgen_file').change(function () {
            var file = this.files[0];
            var reader = new FileReader();
            reader.onloadend = function () {
			   $(this).attr("pf_foto", "noora/assets/images/gallery/1.jpg");
               $('#pf_foto').css('background-image', 'url("' + reader.result + '")');
            }
            if (file) {
                reader.readAsDataURL(file);
            } else {
            }
        });  
function getState(country)
{
    $('#state').val(0);
    $('#state').select2().trigger('change');
    $('#city').val(0);
    $('#city').select2().trigger('change');
   $('#city').html('');
   $('#state').html('');
    $.ajax({
      type: "POST",
      url: "./get_state.php",   
      data:{country_id:country},
      success: function (result) {
       if(result!="")
       {
           $('#state').html(result)
           getCity();
          
       }
      }
 });
}
function getCity(state)
{
    $('#city').val(0);
    $('#city').select2().trigger('change');
    $('#city').empty();
    
   $('#city').val('0').change();
     $.ajax({
      type: "POST",
      url: "./get_district.php",   
      data:{state_id:state},
      success: function (result) {
       if(result!="")
       {
           $('#city').html(result)
       }
      }
 }); 
}
$('.delete_btn').click(function()
{
})
</script>
<script type="text/javascript" src="assets/plugins/parsleyjs/dist/parsley.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
        let ID="<?php echo $entry_id;?>";
        if(ID==0)
        {
                $('.inputDisabled').removeAttr("disabled");
                $('#edit').hide();
                $('#delete').hide();
        }
})
        var errname = document.getElementById("errName");
            function validateName(name)
			{
                var mailformat = /^[a-zA-Z]{1,25}$/;     
                if(name=='')
					{
						errname.innerHTML	= "Name cannot be empty";
						errname.style.color = "red";
					}
				else if(!name.match(mailformat))
					{
						errname.innerHTML	= "Name cannot contain special characters";
						errname.style.color = "red";
					}
				else
					{
						errname.innerHTML	= ""; 
						errname.style.color = "none";
					}
            }
            var errlname = document.getElementById("errLastName");
            function validateSurname(lname)
			{
                var mailformat = /^[a-zA-Z]{2,25}$/;     
                if(lname=='')
					{
						errlname.innerHTML	= "Last Name cannot be empty";
						errlname.style.color = "red";
					}
				else if(!lname.match(mailformat))
					{
						errlname.innerHTML	= "Last Name cannot contain special characters";
						errlname.style.color = "red";
					}
				else
					{
						errlname.innerHTML	= ""; 
						errlname.style.color = "none";
					}
            }
			
			var errpassword = document.getElementById("errPassword");
			function validatePassword(password)
			{
                var ev = /^[a-zA-Z0-9!@#$%^&*]{6,25}$/;
                if(password=='')
					{
						errpassword.innerHTML	= "Password cannot be empty";
						errpassword.style.color = "red";
						
					}
			    else if(!password.match(ev))
					{
						errpassword.innerHTML	= "Password should contain at least 6 characters";
						errpassword.style.color = "red";
						
					}
				else
					{
						errpassword.innerHTML	= ""; 
						errpassword.style.color = "none";
					}	
			}
           
            var errNumber = document.getElementById("errPhone");
            function validatePhoneNumber(number)
            {
                var phoneno = /^\d{10}$/;
                if(number=='')
					{
						errNumber.innerHTML	= "Phone number cannot be empty";
						errNumber.style.color = "red";					
					}
                else if(!number.match(phoneno))
					{
						errNumber.innerHTML	= "Enter correct 10 digit phone number";
						errNumber.style.color = "red";					
					}
				else
					{
						errNumber.innerHTML	= ""; 
						errNumber.style.color = "none";
					}
            }
            var errdistinctNumber = document.getElementById("distictnumber");
            function distinctNumber(number){
                    let id=document.getElementById("entry_id").value;
                    let table="noora_admin_user";
                $.ajax({
                    type: "POST",
                    url: "./check_Unique.php",   
                    data:{id:id,mobile:number,tableName:table},
                    success: function (result) {
                    if(result!="")
                    {
                        if(result==1)
                        {
                            errdistinctNumber.innerHTML='Mobile Number already been used';
                            errdistinctNumber.style.color='red';
                        }
                        else{
                            errdistinctNumber.innerHTML='';
                            errdistinctNumber.style.color='';
                        }
                    }
                    }
                }); 
                
            }
            var erremail = document.getElementById("errEmail");
            function validateEmail(email)
			{
                var mailformat = /^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,3})$/;     
				if(email=='')
					{
						erremail.innerHTML	= "Email cannot be empty";
						erremail.style.color = "red";
					}
                    else if(!email.match(mailformat))
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
            var errdistinctemail = document.getElementById("distictEmail");
            function distinctEmail(email){
				    let table='noora_admin_user';
                    let id=document.getElementById("entry_id").value;
                $.ajax({
                    type: "POST",
                    url: "./check_Unique.php",   
                    data:{id:id,email:email,tableName:table},
                    success: function (result) {
                    if(result!="")
                    {
                        if(result==1)
                        {
                            errdistinctemail.innerHTML='Email already been used';
                            errdistinctemail.style.color='red';
                        }
                        else{
                            errdistinctemail.innerHTML="";
                            errdistinctemail.style.color="";
                        }
                    }
                    }
                }); 
                
            }
			
			function saveData()
			{
				var db_entry_form = $("#db_entry_form");
                let table='noora_admin_user';
                let email=$('#Email').val();
                let id=$('#entry_id').val()
                let country=$("#country").val();
                let state=$("#state").val();
                let city=$("#city").val();
               console.log("country",country)
               console.log("state",state)
               console.log("city",city)
				db_entry_form.parsley().validate();
                if(country==0 || state==0 || city==0 || city==null)
                {
                    bootbox.alert({message:"Please select country state and District", size: 'small'});
                }
               else if(!(errname.innerHTML=="" && errlname.innerHTML=="" && errNumber.innerHTML==""
               && erremail.innerHTML=="" && errdistinctNumber.innerHTML==""))
               {
                bootbox.alert("Please fill all the details correctly");
               }
               else{
				if (db_entry_form.parsley().isValid()==true){
                     $.ajax({
                    type: "POST",
                    url: "./check_Unique.php",   
                    data:{id:id,email:email,tableName:table},
                    success: function (result) {
                            if(result!="")
                            {
                                if(result==1)
                                {
                                    bootbox.alert({
                                        message: "Email Id has already been used",
                                        size: 'small',
                                       
                                    });
                                    return false;
                                }
                                else{
                                    $("#db_entry_form").submit();
                                }
                            
                            }
                    }
                }); 
                }
           
				}
			}	
            $("#edit").click(function(event){
    event.preventDefault();
    $('.inputDisabled').removeAttr("disabled")
    $("#Email").attr("disabled","disabled");
});	
function deleteUser(userId)
{
    let id=userId;
    bootbox.confirm({ 
    size: "small",
    message: "Are you sure you want to delete this?",
   
    callback: function(result){ 
        if(result == true){
            $.ajax({
                type: "POST",
                url: "./delete.php",   
                data:{id:id,status:"Admin_Manager"},
                success: function (result) {
                if(result==1)
                {
                  window.location.href = "admin_manager_list.php";
                }
                else
                {bootbox.alert({ size: "small",message:"Error"})}
                }
            });
        }
    }
})
}

</script>