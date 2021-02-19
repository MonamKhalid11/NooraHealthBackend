<?php 
include "header.php";
require_once("csrf.class.php");
$entry_id=0;
if(isset($_GET['id']))
{
	$entry_id=$_GET['id'];
}
$Group_Name='';
$Group_Description='';
$Group_Members='';
$Hospital_ID=0;

$query1= "SELECT * FROM ".$table_group." where ID='$entry_id' limit 1";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
		$Group_Name=$row1['Name'];
		$Group_Description=$row1['Description'];
        $Group_Members=$row1['Group_Members'];
        $country_ID=$row1['CountryID'];
        $state_ID=$row1['StateID'];
        $City_ID=$row1['District_ID'];
	}
}


$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);
    if($Group_Members=='')
    {
        $Group_Members=1;
    }
 
$query1= "SELECT * FROM ".$table_noora_country." where Status != 3";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $country_list[]=$row1;
    }   
}
?>
<style>
.font-karla{
font-family:'Karla' !important;
color:#3b3e47 !important;
}

.clr-white{
    color:#ffffff !important;
}
.list-para{
    width: 110px;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
.clearfix{
    clear:both;
}
.edit-clr{
  color:#4BB75E;
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
textarea {
  resize: none;
}
</style>
<link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    
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
                                <h4 class="page-title font-karla">Add / Edit  New Group</h4>
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
                        			
                                <button id="delete" onclick="deleteGroup('<?=$entry_id?>')" class="m-l-15 btn btn-trans waves-effect waves-light
                                    m-b-5 font-karla pull-right btn-styles" style="background:#ffffff;">
                                    <i class="fa fa-trash-o text-danger" ></i> Delete </button> 

                                    
                                <button id="edit" class="pull-right btn btn-light btn-trans waves-effect 
                                    waves-light m-b-5 font-karla btn-styles" style="background:#ffffff;">
                                <i class="fa fa-pencil m-r-5 edit-clr" ></i> <span>Edit&nbsp;&nbsp;</span> 
                                </button> 


                                    <div class="clearfix"></div>

									<form enctype="multipart/form-data" id="db_entry_form" action="group_entry.php" class="form-horizontal form-align" method="post" data-parsley-validate novalidate>
										<input type="hidden" name="<?= $token_id; ?>" value="<?= $token_value; ?>" />
										<input type="hidden" id="entry_id" name="entry_id" value="<?= $entry_id;?>">  

											<div class="row">

												<div class="col-lg-11">
													<div class="form-group font-karla">
														<label for="userName">Name Of Group<span class="text-red">*</span></label>
														<input type="text" name="Group_Name" parsley-trigger="change" required
														onkeyup="validateGroupName(this.value);checkUniqueGroupName(this.value)" placeholder="Group Name" disabled
                                                         class="inputDisabled form-control" id="Group_Name" value="<?= $Group_Name;?>">
                                                        <span id="errGrpName"></span>
                                                        <span id="distictName"></span>
                                                    </div>
												</div>
												
											</div>
											<div class="row">
												<div class="col-lg-11">
													<div class="form-group font-karla">
														<label for="emailAddress">Group Description<span class="text-red">*</span></label>
                                                        <textarea disabled class="inputDisabled form-control" name="Group_Description" parsley-trigger="change" 
                                                        placeholder="Group Description" required rows="3"><?=$Group_Description?></textarea>
													</div>
												</div>
											</div>

                                                        <div class="form-group m-b-0 font-karla">
                                                            <label for="pass1">Group Members<span class="text-red">*</span></label>
                                                        </div>
                                            

                                            <div class="row m-b-15 font-karla">
                                                    <div class="col-lg-2">
                                                        <div class="radio radio-purple">
                                                            <input type="radio" disabled class="inputDisabled" name="Group_Members" value="1" id="all" <?php if($Group_Members=="1") echo 'checked="checked"';?> required>
                                                            <label for="all"   value="1" <?php if($Group_Members=="1") echo 'checked="checked"';?>>
                                                            All Users
                                                            </label>
                                                        </div>
                                                   
                                                    </div>
                                                    <div class="col-lg-2">
                                                    <div class="radio radio-purple">
                                                        <input type="radio" disabled class="inputDisabled" name="Group_Members" value="2" <?php if($Group_Members=="2") echo 'checked="checked"';?> id="specific">
                                                        <label for="specific" value="2" <?php if($Group_Members=="2") echo 'checked="checked"';?>>
                                                        Specific Users
                                                        </label>
                                                    </div>
                                                   
                                                    </div>
                                                    <div class="col-lg-7" id="select-all">
                                                        <div class="pull-right" >
                                                            <div class="checkbox checkbox-purple">
                                                                <input id="checkbox6a" type="checkbox" disabled class="inputDisabled check-all">
                                                                <label for="checkbox6a">
                                                                    Select All
                                                                </label>
                                                            </div>  
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div  id="showTable" <?php if($Group_Members==2) echo  'style="display:block;"'; else echo  'style="display:none;"';?> >
                                                
                                                <div class="row" id="checkedNurses">
                                                </div>
                                              
                                                <label for="filter" style="margin-top:8px" id="filter-section">
                                                            Filter By
                                                        </label>
                                                <div class="row" id="filter">
                                                  
                                                        <div class="col-sm-3">
                                                            <div class="form-group font-karla">
                                                                <select disabled class="inputDisabled form-control select2" name="country" id="country"
                                                                 onChange="getState(this.value);getNurseData()">
                                                                    <!-- <option value="0" selected >Select Country</option> -->
                                                                <?php  foreach($country_list as $key=>$value)
                                                                        {?>
                                                                            <option <?php if($value["ID"]==$country_id) echo 'selected="selected"'?> value="<?=$value["ID"]?>" ><?=$value["Name"]?></option>
                                                                    <?php  }?>
                                                                </select>
                                                                </div>
                                                        </div>
                                                            <div class="col-sm-1"></div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group font-karla">                                                                 
                                                                    <select disabled class="inputDisabled form-control select2"  name="state" id="state" 
                                                                    onChange="getCity(this.value);getNurseData()">
                                                                        <option value="0" selected >Select State</option>
                                                                        <?php 
                                                                        if(!empty($state_list)){
                                                                        foreach($state_list as $key=>$value)
                                                                            {?>
                                                                                <option <?php if($value["ID"]==$state_id) echo 'selected="selected"'?> value="<?=$value["ID"]?>" ><?=$value["Name"]?></option>
                                                                        <?php  }}?>
                                                                    </select>
                                                                </div>
                                                        </div>
                                                        <div class="col-sm-1"></div>

                                                    <div class="col-sm-3">
                                                        <div class="form-group font-karla">
                                                                <select disabled class="inputDisabled form-control select2"  name="city" id="city" 
                                                                 onChange="getNurseData()"
                                                                 >
                                                                <option value="0" selected >Select City</option>
                                                                <?php 
                                                                    if(!empty($city_list)){
                                                                    foreach($city_list as $key=>$value)
                                                                        {?>
                                                                            <option <?php if($value["ID"]==$City_ID) echo 'selected="selected"'?> value="<?=$value["ID"]?>" ><?=$value["Name"]?></option>
                                                                    <?php  }}?>
                                                                </select>
                                                            </div>
                                                    </div>
                                                </div>
                                             
                                                <div class="row" id="nursedata">
                                               
                                            </div>
                                        </div>
                                      
                                       


                                        <div class="form-group text-center m-b-0 font-karla p-t-10 m-t-15">
                                           
                                            <?php if($entry_id==0){?>  <button class="btn btn-purple w-md waves-effect waves-light " 
                                            type="button"  onclick="saveData()">   Save
                                            </button><?php }
                                            else{?><button disabled class="inputDisabled btn btn-purple w-md waves-effect waves-light"
                                             type="button" onclick="saveData()">   Update
                                                </button><?php }?>
                                            <a href="group_list.php" class="btn btn-default waves-effect waves-light m-l-15" style="padding:6px 32px">
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
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="assets/pages/datatables.ajax.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        let ID="<?php echo $entry_id;?>";
        // getState(1);
        // $('#country').select2().select2('val',value) 
        if(ID==0)
        {
                $('.inputDisabled').removeAttr("disabled");
                $('#edit').hide();
                $('#delete').hide();
        }
        let countryID="<?php echo $country_ID;?>";
      
        $('#country').val(countryID).change();

         let groupMember=<?php echo $Group_Members;?>;
        if(groupMember==1)
        {
            $("#select-all").hide()
        }
        else{
            $("#select-all").show()
        }
        // let id
        if(countryID)
        {
            id=countryID; 
            getState(id);
            $('#country').select2().select2('val',id)
        }
        else{
            id=1;
            $('#country').select2().select2('val',id) 
            getState(id);
        }
        let value=""
        $.ajax({
       type: "POST",
       url: "./get_state.php",   
       data:{country_id:id},
       success: function (result) {
        if(result!="")
        {
            // console.log(result);
            $('#state').html(result);
            value= "<?php echo $state_ID; ?>" ;
            if(value=="")
            {
                value=3;
            }
            $('#state').select2().select2('val',value)    
            getCity(value);     
        }
       }
  });
});
var result=[];
$(".select2").select2();

$(".select2-limiting").select2({
  maximumSelectionLength: 2
});
function getState(country)
{
    $('#state').val(0);
    $('#state').select2().trigger('change');
    $('#city').val(0);
    $('#city').select2().trigger('change');
   $('#city').html('');
   $('#state').html('');
       if(country){
                $.ajax({
                type: "POST",
                url: "./get_state.php",   
                data:{country_id:country},
                success: function (result) {
                if(result!="")
                {
                    $('#state').html(result);
                    let stateID="<?php echo $state_ID;?>";
                    if(stateID>0)
                    {$('#state').val(stateID).change();}
                }
                }
            });
       }
}
function getCity(state)
{
    
    $('#city').val(0);
    $('#city').select2().trigger('change');
   $('#city').val(0);
        if(state){
                $.ajax({
                type: "POST",
                url: "./get_district.php",   
                data:{state_id:state},
                success: function (result) {
                if(result!="")
                {
                    $('#city').html(result);
                    let cityID="<?php echo $City_ID;?>";
                    if(cityID>0)
                    {
                        $('#city').val(cityID).change();}
                    }
                }
            }); 
        }
}
$("#specific").change(function(){
        $("#select-all").show();
  });
  $(".check-all").change(function(){
    if($(this).prop('checked')){
        $(".check").prop('checked','checked');
      }
      else{
        $(".check").prop('checked', false);
      }
  });
  
function getNurseData(){
    let id=document.getElementById('entry_id').value;
    let ddSelected="";
    let ID;
    $('#nursedata').html('');
    let country=document.getElementById('country').value;
    let state=document.getElementById('state').value;
    let city=document.getElementById('city').value;

    ID=document.getElementById('country').value;

    if(country>0){
        ID=document.getElementById('country').value;
        ddSelected=1;
    }
     if(state>0)
    {
        ID=document.getElementById('state').value;
        ddSelected=2;
    }
    if(city>0){
        ID=document.getElementById('city').value;
        ddSelected=3;
    }
    $.ajax({
        type: "POST",
        url: "./get_nurse_data.php",   
        data:{country_id:ID,entry_id:id,dd:ddSelected},
        success: function (result) {
       if(result!="")
       {
        var text = "";
        var response=JSON.parse(result);
      
            for (i = 0; i < response.length; i++) {
                var name = response[i];
                text+=`<div class="col-sm-3">
                            <div class="card-box m-b-0 kanban-box">
                                    <div class="checkbox-wrapper">
                                        <div class="checkbox checkbox-purple checkbox-single ">`;
                if(name[2]==null)
                {
                    text+=` <input type="checkbox" class="inputDisabled check" id="singleCheckbox11" name="group[]" id="${name[3]}" value="${name[3]}">`;
                }
                else{
                    text+=` <input type="checkbox" class="inputDisabled check" id="singleCheckbox11" name="group[]" id="${name[3]}" value="${name[3]}" checked>`;
                }
                text+=` <label></label>
                            </div>
                        </div>
                            <div class="kanban-detail">
                                <p class="inbox-item-author font-karla m-b-0 list-para" title="${name[0]}">
                                <b>${name[0]}</b></p>
                                <p class="inbox-item-text">${name[1]}</p>
                            </div>
                        </div>
                    </div>`;
            
            }
            $('#nursedata').html(text);
      
       }

      }
 }); 
}


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
        $(function(){
  $('input[type="radio"]').click(function(){
    if ($(this).is(':checked'))
    {
        if($(this).val()==2)
        {
            $('#showTable').show();
        }
        if($(this).val()==1)
        {
            $('#showTable').hide();
        }
    }
  });
});
var errgrpName = document.getElementById("errGrpName");
            function validateGroupName(name)
			{
                var mailformat = /^[a-zA-Z ]{1,50}$/;  
                if(name=='')
                {
                    errgrpName.innerHTML= "Group Name cannot be empty";
					errgrpName.style.color = "red";
                }   
				else if(!name.match(mailformat))
					{
						errgrpName.innerHTML	= "Group Name cannot contain special characters";
						errgrpName.style.color = "red";
					}
				else
					{
						errgrpName.innerHTML	= ""; 
						errgrpName.style.color = "none";
					}
            }
              $("#edit").click(function(event){
    event.preventDefault();
    $('.inputDisabled').removeAttr("disabled")
});	  
function deleteGroup(groupId)
{
    let id=groupId;
    bootbox.confirm({ 
    size: "small",
    message: "Are you sure you want to delete this?",
    callback: function(result){ 
        if(result == true){
            $.ajax({
                type: "POST",
                url: "./delete.php",   
                data:{id:id,status:"Group"},
                success: function (result) {
                if(result==1)
                {
                    window.location.href = "group_list.php";
                }
                else
                {bootbox.alert({ size: "small",message:"Error"})}
                }
            });
        }
    }
})
}
var errdistinctName = document.getElementById("distictName");

        function checkUniqueGroupName(value)
        {
                    let table='noora_group';
                    let id=document.getElementById("entry_id").value;
                    $.ajax({
                        type: "POST",
                        url: "./check_Unique.php",   
                        data:{id:id,email:value,tableName:table},
                        success: function (result) {
                        if(result!="")
                        {
                            if(result==1)
                            {
                                errdistinctName.innerHTML='Group Name already been used';
                                errdistinctName.style.color='red';
                            }
                            else{
                                errdistinctName.innerHTML='';
                                errdistinctName.style.color='';
                            }
                        }
                        }
                    }); 
        }
        function saveData()
			{
				var db_entry_form = $("#db_entry_form");
                let email=$('#Group_Name').val();
                let id=$('#entry_id').val()
				 let table='noora_group';
               var type=$('input[name=Group_Members]:checked', '#db_entry_form').val()
                var numberOfChecked = $('input[name="group[]"]:checked').length;
                console.log("count",numberOfChecked)
				db_entry_form.parsley().validate();
    
               if(type==2 && numberOfChecked==0)
               {
                bootbox.alert({
                                        message: "Select atleast one group",
                                        size: 'small',
                                    });
                                    return false;
               }
               else
               {
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
                                        message: "Group Name has already been used",
                                        size: 'small',
                                    });
                                    return false;
                                }
                                else{
                                    if(errgrpName.innerHTML=='')
                                    {
                                        $("#db_entry_form").submit();
                                    }
                                    else{
                                        bootbox.alert({
                                        message: "Please fill all the details correctly",
                                        size: 'small',
                                    });
                                    }
                                }
                            
                            }
                    }
                }); 
           
				}
               }
			}	
</script>

<script type="text/javascript" src="assets/plugins/parsleyjs/dist/parsley.min.js"></script>
       
