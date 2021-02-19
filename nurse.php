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
$CityID=0;
$Mobile_Number='';
$Role_ID='';
$state_id=0;
$state_name="";
$country_id=0;
$country_name="";
$query1= "SELECT * FROM ".$table_nurse." where ID='$entry_id' limit 1";
// echo $query1;exit();
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{

	while($row1 = mysqli_fetch_array($res1))
	{
		$First_Name=$row1['First_Name'];
        $Last_Name=$row1['Last_Name'];
        $Mobile_Number=$row1['Mobile_Number'];
        $Noora_Badge=$row1["Badge_Level"];
        $Graduating_Year=$row1["Graduating_Year"];
        $Date_Join="";//date('d-m-Y',strtotime($row1["Hospital_Joining_Date"]));
		$dateJoin = $row1["Hospital_Joining_Date"];
		if($dateJoin!=""){
			$Date_Join= date('d-m-Y',strtotime($dateJoin));
		}
        $Hospital_ID=$row1["Hospital_ID"];

        // $Hospital_Condition=$row1["Hospital_Condition_ID"];
        // echo $row1["Hospital_Condition_ID"];
        $Hospital_Condition = explode(',', $row1['Hospital_Condition_ID']);
        $Designation=$row1["Designation"];
        $CCP_Date="";//date('d-m-Y',strtotime($row1["TOT_Date"]));
		$ccpDate = $row1["TOT_Date"];
		if($ccpDate!=""){
			$CCP_Date= date('d-m-Y',strtotime($ccpDate));
		}
        $Mentor_Name=$row1["CCP_Mentor"];
		$Trainer_Name = $row1['Trainer'];
        $Booster_Training=$row1["Booster_Training"];
        $CCP_Condition_ID = $row1["CCP_Condition_ID"];
        $CityID=$row1["District_ID"];
        $DOB=date('d-m-Y',strtotime($row1['DOB']));
        // echo $Hospital_Condition;exit();
        if($CityID>0)
        {
            $query2="SELECT ".$table_noora_state.".ID as State_ID,".$table_noora_state.".Name as State_Name,".$table_noora_country.".ID as Country_Id,".$table_noora_country.".Name as Country_Name from ".$table_noora_district." LEFT JOIN ".$table_noora_state." ON ".$table_noora_state.".ID=".$table_noora_district.".State_ID
            LEFT JOIN ".$table_noora_country." ON ".$table_noora_country.".ID=".$table_noora_state.".Country_ID WHERE ".$table_noora_district.".ID=".$CityID;
            // echo $query2;exit();
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
$country_list=array();

$query1= "SELECT * FROM ".$table_noora_country." where Status !=3";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $country_list[]=$row1;
    }
   
}
$Hospital=array();
$query1= "SELECT * FROM ".$table_noora_hospital." where id=".$Hospital_ID;
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $Hospital_Name=$row1["Name"];
    }    
}
$Hospital_Condition_Array=array();
$query1= "SELECT * FROM ".$table_noora_hospital_medical_condition."";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $Hospital_Condition_Array[]=$row1;
    }
  
}

$Booster_Training_Array=array();
$query1= "SELECT * FROM ".$table_noora_booster_training."";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $Booster_Training_Array[]=$row1;
    }
}

$CCP_Class_Type_Array=array();
$query1= "SELECT * FROM ".$table_ccp_class_type."";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $CCP_Class_Type_Array[]=$row1;
    }
}

require_once("csrf.class.php");
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);

?>
<style type="text/css">
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
.font-karla{
font-family:'Karla' !important;
color:#3b3e47 !important;
}

.clr-white{
    color:#ffffff !important;
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
                <div class="navbar navbar-default" role="navigation">
                    <div class="container">
                        <ul class="nav navbar-nav navbar-left">
                            <li>
                                <button class="button-menu-mobile open-left">
                                    <i class="zmdi zmdi-menu"></i>
                                </button>
                            </li>
                            <li>
                                <h4 class="page-title font-karla">Add / Edit  Nurse</h4>
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
                            <form id="db_entry_form"  enctype="multipart/form-data" class="form-horizontal " method="post" data-parsley-validate novalidate>
                                <div class="card-box box-padding">
                        			<h4 class="nurse-heading font-karla">Personal Details</h4>						
										<input type="hidden" name="<?= $token_id; ?>" value="<?= $token_value; ?>" />
										<input type="hidden" id="entry_id" name="entry_id" value="<?= $entry_id;?>">  
                                    		<div class="row">
                                           
												<div class="col-lg-5">
													<div class="form-group font-karla">
														<label for="userName">First Name<span class="text-red">*</span></label>
														<input type="text" name="First_Name" parsley-trigger="change" data-parsley-required="true"	
															   placeholder="First Name" class="form-control" id="First_Name"
                                                               onkeyup="validateName(this.value)" value="<?= $First_Name;?>">
                                                               <span id="errName"></span> 
													</div>
												</div>
												<div class="col-lg-1"></div>
												<div class="col-lg-5">
													<div class="form-group font-karla">
														<label for="userName">Last Name<span class="text-red">*</span></label>
														<input type="text" name="Last_Name" parsley-trigger="change" required
															   placeholder="Last Name" class="form-control" id="Last_Name"
                                                               onkeyup="validateSurname(this.value)"  value="<?= $Last_Name;?>">
                                                        <span id="errLastName"></span>
													</div>
												</div>
											</div>
											
											<div class="row">
												<div class="col-lg-5">
													<div class="form-group font-karla">
														<label class="control-label" for="example-input1-group3">Mobile Number<span class="text-red">*</span></label>
														<div class="input-group">
															<div class="input-group-btn">												
																<button type="button" class="btn waves-effect waves-light btn-default dropdown-toggle" data-toggle="dropdown" style="overflow: hidden; position: relative;">+91 </button>
															
															</div>
															<input parsley-trigger="change" type="number" id="MobileNumber"  name="Mobile_Number"
                                                             class="form-control" placeholder="Mobile Number" required
                                                             onkeyup="validatePhoneNumber(this.value);distinctNumber(this.value)" value="<?= $Mobile_Number;?>">
														</div>
                                                        <span id="errPhone"></span>
                                                        <span id="distictnumber"></span>
													</div>
                                                 </div>
                                                 <div class="col-lg-1"></div>

                                                 <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label class="control-label" for="example-input1-group3">Noora Badge</label>
                                                        <select class="form-control select2" name="Noora_Badge" required="">
                                                                <?php if(!empty($badge))
                                                                {
                                                                    foreach($badge as $key=>$value)
                                                                    {
                                                                    ?>
                                                                      <option value="<?=$key?>" <?php if($Noora_Badge==$key) echo 'selected="selected"'?>><?=$value?></option>
                                                                    <?php
                                                                } }?>
                                                                
                                                            </select>
                                                      
                                                    </div>
                                                 </div>
											</div>
                                            <div class="row">
												<div class="col-lg-5">
													<div class="form-group font-karla">
														<label class="control-label" for="example-input1-group3">Profile Image</label>
													
                                                      
                                                            <input type='file' id='verborgen_file' 
                                                            name="Profile"  data-max-file-size="1M"
                                                            accept="image/x-png,image/gif,image/jpeg"/>
														
													</div>
                                                 </div>
                                                 <div class="col-lg-1"></div>
                                                 <div class="col-lg-5">
                                                 <div class="form-group font-karla">
                                                        <label for="userName">Date Of Birth<span class="text-red">*</span></label>
                                                        <div class="input-group">
																<input type="text"  parsley-trigger="change focusout"  required class="form-control datepicker-autoclose1"
                                                                 name="DOB" placeholder="dd/mm/yyyy" id="DOB" value="<?=$DOB?>" onChange="validateDOB(this.value)">
																<span class="input-group-addon bg-purple b-0 text-white"><i class="ti-calendar"></i></span>
															</div>
                                                            <span id="errDOB"></span>
                                                            
                                                    </div>
                                                 </div>
											</div>
                                            <div class="row">
                                                    <div class="col-lg-3">
                                                        <div class="form-group font-karla">
                                                            <label for="country">Country<span class="text-red">*</span></label>
                                                            <select class="form-control select2" name="country" id="country" required="" onChange="getState(this.value)">
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
                                                            <select class="form-control select2" required=""  name="state" id="state" onChange="getCity(this.value)">
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
                                                            <select class="form-control select2" required="" name="city" id="city" >
                                                            <!-- <option value="0" selected >Select</option> -->
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
                                                
                                      
                                
                                </div>
                                  <div class="card-box box-padding" >
                                    <h4 class="nurse-heading">Professional Details</h4>
 
                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Graduating Year<span class="text-red">*</span></label>
                                                        <input type="number" min="0" name="Graduating_Year"
                                                               placeholder="Graduating Year" class="form-control" id="Graduating_Year"
                                                               onkeyup="validateGraduationYear(this.value)" value="<?= $Graduating_Year;?>">
                                                        <span id="errYear"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Date of Joining Hospital<span class="text-red">*</span></label>
                                                        <div class="input-group">
																<input type="text" parsley-trigger="change"   class="form-control datepicker-autoclose1"
                                                                 name="Date_Join" placeholder="dd/mm/yyyy"   id="Date_Join" value="<?=$Date_Join?>" >
                                            
																<span class="input-group-addon bg-purple b-0 text-white"><i class="ti-calendar"></i></span>
															</div>
                                                            <span id="errDateJoin"></span>
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        
                                            <div class="row">
                                               <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Name of Hospital<span class="text-red">*</span></label>

                                              
                                                            <input type="text" name="Hospital_Name" parsley-trigger="change" 
                                                               placeholder="Name of Hospital" class="form-control" id="Hospital_Name"
                                                                value="<?= $Hospital_Name;?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Medical Condition Area<span class="text-red">*</span></label>
                                                         <select multiple="" class="form-control chosen-select" 
                                                         name="Hospital_Condition[]" id="Hospital_Condition">
                                                         <?php  foreach($Hospital_Condition_Array as $key=>$value)
                                                         
                                                                    {?>
                                                                    <option <?php if(in_array($value["ID"], $Hospital_Condition)) echo 'selected="selected"'?> value="<?=$value["ID"]?>" ><?=$value["Medical_Condition"]?></option>
                                                                    <?php  }?>                  
                                                            </select>
                                                    </div>
                                                </div>
                                            </div>
                                             
                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Designation of Nurse<span class="text-red">*</span></label>
                                                        <input type="text" name="Designation" parsley-trigger="change" 
                                                               placeholder="Designation in Hospital" class="form-control" id="Designation"
                                                               onkeyup="validateDesignation(this.value)" value="<?= $Designation;?>">
                                                        <span id="errDesignation"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">CCP TOT Date<span class="text-red">*</span></label>
                                                         <div class="input-group">
																<input type="text" parsley-trigger="change"  class="form-control datepicker-autoclose1" placeholder="dd/mm/yyyy" 
                                                                id="CCP_Date" name="CCP_Date" value="<?=$CCP_Date?>">
																<span class="input-group-addon bg-purple b-0 text-white"><i class="ti-calendar"></i></span>
															</div>
                                                    </div>
                                                </div>
                                            </div>
                                       
                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="emailAddress">Name of CCP Mentor<span class="text-red">*</span></label>
                                                        <input type="text" name="Mentor_Name" parsley-trigger="change" 
                                                               placeholder="CCP Mentor of Nurse" class="form-control"
                                                               onkeyup="validateMentor(this.value)" id="Mentor_Name" value="<?= $Mentor_Name;?>">
                                                               <span id="errMentor"></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="pass1">Total Booster Training<span class="text-red">*</span></label>
                                                         <select class="form-control select2" name="Booster_Training" id="Booster_Training">
                                                            <?php 
                                                                if(!empty($Booster_Training_Array)){
                                                                 foreach($Booster_Training_Array as $key=>$value)
                                                                    {?>
                                                                        <option <?php if($value["ID"]==$Booster_Training) echo 'selected="selected"'?> value="<?=$value["ID"]?>" ><?=$value["training_value"]?></option>
                                                                  <?php  }}?>
                                                            </select>
                                                    </div>
                                                </div>
                                                
                                            </div>  

                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="trainer">Trainer<span class="text-red">*</span></label>
                                                        <input type="text" name="Trainer_Name" parsley-trigger="change" 
                                                               placeholder="Trainer" class="form-control" maxlength="6"
                                                               onkeyup="validateTrainer(this.value)" id="Trainer_Name" value="<?= $Trainer_Name;?>">
                                                               <span id="errTrainer"></span>
                                                    </div>
                                                </div>
                                                    <div class="col-lg-1"></div>
                                                <div class="col-lg-5">
                                                        <div class="form-group font-karla">
                                                            <label for="pass1">Condition Area for CPP<span class="text-red">*</span></label>
                                                            <select class="form-control select2" name="CCP_Condition_Type" id="CCP_Condition_Type">
                                                                <?php 
                                                                    if(!empty($CCP_Class_Type_Array)){
                                                                       
                                                                    foreach($CCP_Class_Type_Array as $key=>$value)
                                                                        {?>
                                                                            <option <?php if($value["ID"]==$CCP_Condition_ID) echo 'selected="selected"'?> value="<?=$value["ID"]?>" ><?=$value["Class_Type"]?></option>
                                                                    <?php  }}?>
                                                                </select>
                                                        </div>
                                                </div>
                                                
                                            </div>  											

                                        <div class="form-group text-center m-b-0 font-karla p-t-10 m-t-15"> 
                         
                                            <?php if($entry_id==0){?> 
                                             <button class="btn btn-purple w-md waves-effect waves-light" type="submit" 
                                            style="padding:6px 20px"   onclick="saveData(event,'submit_op')">   Save
                                            </button><?php }
                                            else{?><button class="btn btn-purple w-md waves-effect waves-light"
                                             type="submit"  onclick="saveData(event,'update')" >   Update
                                            </button><?php }?>

                                         <a href="nurse_list.php" class="btn btn-default waves-effect waves-light m-l-15 " style="padding:6px 32px">
                                                Cancel
                                         </a>
                                        </div>

                                  
                                </div>
                                </form>
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
<script type="text/javascript">
$(document).ready(function() {

    $.ajax({
       type: "POST",
       url: "./get_hospital.php",   
       success: function (result) {
        if(result!="")
        {
            $( "#Hospital_Name" ).autocomplete({
            source: JSON.parse(result)
            });
        }
       }
  });

    $('#verborgen_file').change(function () {
            var file = this.files[0];
            var fileSize = this.files[0].size;          
            var size=fileSize/1000;
            let type=this.files[0].type;
            var reader = new FileReader();
            if(size<576 )
            {
                if(type!="image/jpeg" && type!="image/png")
                {
                    bootbox.alert("Upload an image file.");
                    $('#verborgen_file').val('')
                }
                reader.onloadend = function () {
			   $(this).attr("pf_foto", "noora/assets/images/gallery/1.jpg");
               $('#pf_foto').css('background-image', 'url("' + reader.result + '")');
                }
                if (file) {
                    reader.readAsDataURL(file);
                }  
            }
            else{
                bootbox.alert("image size is too large.");
                $('#verborgen_file').val('')
            }
         
        });  
        let ID="<?php echo $entry_id;?>";
        if(ID>0)
        {
            $("#MobileNumber").attr("disabled","disabled");
        }

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
                    $('#city').html(result)
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
  let stateID=<?php echo $state_id; ?>;
  if(stateID==0)
  {
    stateID=3;
  }
  $.ajax({
       type: "POST",
       url: "./get_district.php",   
       data:{state_id:stateID},
       success: function (result) {
        if(result!="")
        {
            $('#city').html(result);
            let value= <?php echo $CityID; ?> ;
            if(value=="")
            {
                value=3;
            }
            $('#city').select2().select2('val',value)         
        }
       }
  });

    });
            jQuery('.datepicker-autoclose1').datepicker({
                autoclose: true,
                todayHighlight: true,
                onClose: function () {
        $(this).parsley().validate();
        }
            });
 $(".select2").select2();

      
function getState(country)
{
    $('#state').val(0);
    $('#state').select2().trigger('change');
    $('#city').val(0);
    $('#city').select2().trigger('change');

    $.ajax({
       
      type: "POST",
      url: "./get_state.php",   
      data:{country_id:country},
      success: function (result) {
       if(result!="")
       {
        //$('#state').empty();
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
function getMedicalCondition(hospital)
{
    $('#Hospital_Condition').val(0);
    $('#Hospital_Condition').select2().trigger('change');

     $.ajax({
      type: "POST",
      url: "./get_medical_condition.php",   
      data:{hospital:hospital},
      success: function (result) {
       if(result!="")
       {
           $('#Hospital_Condition').html(result)
       }
      }
 }); 
}
var errname = document.getElementById("errName");
            function validateName(name)
			{
                var mailformat = /^[a-zA-Z ]{1,25}$/;  
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
                var mailformat = /^[a-zA-Z ]{1,25}$/;  
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
 
            var errNumber = document.getElementById("errPhone");
            function validatePhoneNumber(number)
            {
                var phoneno = /^\d{10}$/;
                if(number=='')
                {
                    errNumber.innerHTML	= "Phone Number cannot be empty";
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

            var errYear = document.getElementById("errYear");
            function validateGraduationYear(year)
            {
                var today = new Date();
                //var years = year.substring(6, 10);
                var date = today.getFullYear();
               // console.log(year,",",date)
                var yearPattern=/^\d{4}$/;
                if(year==''){
					//errYear.innerHTML	= "Year cannot be empty";
					//errYear.style.color = "red";
					errYear.innerHTML	= ""; 
					errYear.style.color = "none";
				}
				else if(year=='0000' || year<'1950'){
					errYear.innerHTML	= "Enter correct year";
					errYear.style.color = "red";		
				}
				else if(!year.match(yearPattern)){
					errYear.innerHTML	= "Enter correct year";
					errYear.style.color = "red";					
				}
				else if(date-year<0){
					errYear.innerHTML	= "Graduating year should be less than "+date;
					errYear.style.color = "red";
                }
				else{
					errYear.innerHTML	= ""; 
					errYear.style.color = "none";
				}

            }

            var errdesignation = document.getElementById("errDesignation");
            function validateDesignation(designation)
            {
                        var Pattern= /^[a-zA-Z ]{1,25}$/ ;
                        if(designation=='')
                        {
                            errdesignation.innerHTML	= "Designation cannot be empty";
                            errdesignation.style.color = "red";					
                        }
                        else if(!designation.match(Pattern))
                            {
                                errdesignation.innerHTML	= "Designation cannot contain special character";
                                errdesignation.style.color = "red";					
                            }
                        else
                            {
                                errdesignation.innerHTML	= ""; 
                                errdesignation.style.color = "none";
                            }
            }

            var errmentor = document.getElementById("errMentor");
            function validateMentor(mentor)
            {
                        var Pattern= /^[a-zA-Z ]{1,50}$/ ;
                        if(mentor=='')
                        {
                            errmentor.innerHTML	= "Mentor Name cannot be empty";
                            errmentor.style.color = "red";
                        }
                        else if(!mentor.match(Pattern))
                            {
                                errmentor.innerHTML	= "Mentor Name cannot contain special character";
                                errmentor.style.color = "red";					
                            }
                        else
                            {
                                errmentor.innerHTML	= ""; 
                                errmentor.style.color = "none";
                            }
            }

            var errDOB = document.getElementById("errDOB");
            function validateDOB(birthdate)
            {
                console.log("dob",birthdate)
                var today = new Date();
                var year = birthdate.substring(6, 10);
                var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                if(!((today.getFullYear()-18)>=year))
                {
                    errDOB.innerHTML="Age should be greater than 18 Years";
                    errDOB.style.color='red'; 
                }
                else{
                    errDOB.innerHTML="";
                    errmentor.style.color = "none";
                }
            }
            var errdistinctNumber = document.getElementById("distictnumber");
            function distinctNumber(number){
                    let id=document.getElementById("entry_id").value;
                    let table="noora_nurse";
                $.ajax({
                    type: "POST",
                    url: "./check_Unique.php",   
                    data:{id:id,email:number,tableName:table},
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
              
            $(document).on('change','#Date_Join',function(){
                var value=$('#Date_Join').val()
                if(value!="")
                {
                var parsley_id=$('#Date_Join').attr("data-parsley-id")
                var instance = $('#Date_Join').parsley();
                if(instance.isValid()===true)
                {
                    //console.log("if",id)
                    $('#Date_Join').prop('required',false)
                    $('#Date_Join').removeClass('parsley-error')
                    $('#parsley-id-'+parsley_id).hide()
                   
                }
                }
                else
                {
                    //$('#Date_Join').prop('required',true)
                    $('#Date_Join').addClass('parsley-error')
                }
});       
            $(document).on('change','#DOB',function(){
                var value=$('#DOB').val()
                if(value!="")
                {
                var parsley_id=$('#DOB').attr("data-parsley-id")
                var instance = $('#DOB').parsley();
                if(instance.isValid()===true)
                {
                    //console.log("if",id)
                    $('#DOB').prop('required',false)
                    $('#DOB').removeClass('parsley-error')
                    $('#parsley-id-'+parsley_id).hide()
                   
                }
                }
                else
                {
                    $('#DOB').prop('required',true)
                    $('#DOB').addClass('parsley-error')
                }
});
            $(document).on('change','#CCP_Date',function(){
                var value=$('#CCP_Date').val()
                if(value!="")
                {
                var parsley_id=$('#CCP_Date').attr("data-parsley-id")
                var instance = $('#CCP_Date').parsley();
                console.log("first",instance.isValid())
                if(instance.isValid()===true)
                {
                    //console.log("if",id)
                    $('#CCP_Date').prop('required',false)
                    $('#CCP_Date').removeClass('parsley-error')
                    $('#parsley-id-'+parsley_id).hide()
                   
                }
                }
                else
                {
                    //$('#CCP_Date').prop('required',true)
                    $('#CCP_Date').addClass('parsley-error')
                }
});
            function saveData(event,op)
			{
                $('#db_entry_form').parsley().validate()
              
                let country=$("#country").val();
                let state=$("#state").val();
                let city=$("#city").val();
               
				var db_entry_form = $("#db_entry_form");
                // alert(db_entry_form);
                let email=$('#MobileNumber').val();
                let id=$('#entry_id').val()
				let table='noora_nurse';
                var type_op=op;
                if(country==0 || state==0 || city==0 || city==null)
                {
                   
                  
                    bootbox.alert({message:"Please select country state and District", size: 'small'});
                    event.preventDefault();
                    return false
                }
                 else if(!(errname.innerHTML=="" && errlname.innerHTML=="" && errNumber.innerHTML==""
                && errYear.innerHTML=="" && errdesignation.innerHTML=="" && errmentor.innerHTML==""
                && errDOB.innerHTML=="" && errdistinctNumber.innerHTML=="" ))
                {
                    bootbox.alert("Please fill all the details correctly");
                    event.preventDefault();
                    return false
                }
                else{

                    if ($('#db_entry_form').parsley().isValid()==true){  
                       console.log(type_op);
                     if(type_op=="submit_op")  
                    { 
                        /*  console.log("type",type_op)
                        $("#db_entry_form").attr('action','nurse_entry.php');
                        $("#db_entry_form").submit();   */
                      
                          $.ajax({
                    type: "POST",
                    url: "./check_Unique.php",   
                    async:false,
                    contentType:false,
                    processData:false,
                    dataType:"json",
                    data:{id:id,email:email,tableName:table},
                    success: function (result) {
                        console.log("result",result);
                            if(result != null)
                            {
                                console.log("result1",result)
                                if(result == 1)
                                {
                                   
                                    bootbox.alert({
                                       message: "Mobile Number has already been used",
                                        size: 'small',
                                       
                                    });
                                    event.preventDefault();
                                    return false;
                                }
                                if(result == 0)
                                {

                       
                                    $("#db_entry_form").attr('action','./nurse_entry.php');
                                 
                    }
                            }
                    }
                });  
                      /*   console.log("Submit")
                         $("#db_entry_form").attr('action','nurse_entry.php');
                        $("#db_entry_form").submit();  */
                    }
                    else
                    {
                        console.log("update")
                         $("#db_entry_form").attr('action','nurse_entry.php');
                        $("#db_entry_form").submit(); 
                    } 
				}
                }
			
			}	
			
			var errtrainer = document.getElementById("errTrainer");
            function validateTrainer(trainer)
            {
                        var Pattern= /^[a-zA-Z0-9 ]{1,6}$/ ;
                        if(trainer=='')
                        {
                            errtrainer.innerHTML	= "Trainer Id cannot be empty";
                            errtrainer.style.color = "red";
                        }
                        else if(!trainer.match(Pattern))
                            {
                                errtrainer.innerHTML	= "Trainer Id cannot contain special character and maximim limit is 6 characters";
                                errtrainer.style.color = "red";					
                            }
                        else
                            {
                                errtrainer.innerHTML	= ""; 
                                errtrainer.style.color = "none";
							}
			}
               
			
			
//             $("select").on("click", "option", function (event) {
//     if (2 <= $(this).siblings(":selected").length) {
//         $(this).removeAttr("selected");
//     }
// });
</script>
<script type="text/javascript" src="assets/plugins/parsleyjs/dist/parsley.min.js"></script>
       