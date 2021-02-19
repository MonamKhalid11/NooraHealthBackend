<?php 
include "header.php";
$entry_id=0;
if(isset($_GET['id']))
{
	$entry_id=$_GET['id'];
}
$Content_Title='';
$Content_Description='';
$Group_Members='';
$img_uploaded='';
$url_attachment='';
$url_display='';
$text_content='';
$posted_by=$_COOKIE['login_postedby'];
$bulk_images=array();
$query1= "SELECT ".$table_content.".*,".$table_admin.".First_Name,".$table_admin.".Last_Name FROM ".$table_content." LEFT JOIN ".$table_admin." ON "
 .$table_admin.".ID=".$table_content.".Login_User_ID where ".$table_content.".ID='$entry_id' limit 1";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
		$Content_Title=$row1['Title'];
		$Content_Description=$row1['Description'];
        $Group_Members=$row1['Role'];
        $posted_by=$row1["First_Name"]." ".$row1["Last_Name"] ;
        $Content_Type=$row1["Content_Type"];
        if($Content_Type==1)
        {
            $query2="SELECT Attachment FROM ".$table_content_attachment." where Content_ID='$entry_id' and Status!=3";
            echo $query2;
            $res2= mysqli_query($link,$query2);
            if(mysqli_num_rows($res2)>0)
            {
                while($row1 = mysqli_fetch_array($res2))
	            {
                    $img_uploaded=$row1['Attachment'];                   
                }
            }  
 
        }
        else if($Content_Type==2)
        {
            $url_attachment=$row1['Attachment'];
			
			if(strpos($url_attachment,"/")){
				//ITS A URL
				$url_display = $url_attachment;
				//1. Contains watch
				if(strpos($url_attachment,"watch")){
					$url_display=str_replace('watch?v=','embed/', $url_attachment);
				}
				//2. youtu.be
				if(strpos($url_attachment,"youtu.be")){
					$url_display=str_replace('youtu.be','youtube.com/embed/', $url_attachment);
				}

			}else{
				//ITS ID
				$url_display = "https://www.youtube.com/embed/".$url_attachment;
			}
			
        }

        else if($Content_Type==3)
        {
            $bulk_images = array();
            $query2="SELECT Attachment FROM ".$table_content_attachment." where Content_ID='$entry_id' and Status!=3";         
           $res2= mysqli_query($link,$query2);
           if(mysqli_num_rows($res2)>0)
            {
                while($row1 = mysqli_fetch_array($res2))
	            {
                    array_push($bulk_images,$row1['Attachment']);
                }
            }
        }
        else if($Content_Type==4)
        {
            $text_content=$row1['Attachment'];
        }

	}
}

require_once("csrf.class.php");
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);
if($Group_Members=='')
{
$Group_Members=1;
}

$group_Member_value=$Group_Members;

if($entry_id<0)
 {   
	$query1 = "SELECT ".$table_group.".* FROM ".$table_group." WHERE 1=1 AND ".$table_group.".Status != '3' ";
	$res1= mysqli_query($link,$query1);
	$totalData = mysqli_num_rows($res1);
	$query1 = "SELECT ".$table_group.".* FROM ".$table_group." WHERE 1=1 AND ".$table_group.".Status != '3' ";
 }
 else
 {
	$query1 = "SELECT DISTINCT ".$table_group." .*, ".$table_content_group. ".Group_ID FROM ".$table_group." LEFT JOIN ".$table_content_group." ON ( ".$table_group." .ID = ".$table_content_group."
	.Group_ID AND ".$table_content_group." .Content_ID= ".$entry_id." )  WHERE 1=1 AND ".$table_group.".Status != '3' ";
	$res1= mysqli_query($link,$query1);
	$totalData = mysqli_num_rows($res1);
	$query1 = "SELECT DISTINCT ".$table_group." .*, ".$table_content_group. ".Group_ID FROM ".$table_group." LEFT JOIN ".$table_content_group." ON ( ".$table_group." .ID = ".$table_content_group."
	.Group_ID AND ".$table_content_group." .Content_ID= ".$entry_id." )  WHERE 1=1 AND ".$table_group.".Status != '3' ";
 }

$res1= mysqli_query($link,$query1);
$totalFiltered = mysqli_num_rows($res1); 
$res1=mysqli_query($link,$query1);
$count_admin=0;
$sr_no=0;
$Block_Status_Code = 2;
$data = array();
$count=0;
while($row1=mysqli_fetch_array($res1)) 
{  // preparing an array
	
	$nestedData=array(); 
	$State="";
	$User_ID_Group=$row1["Group_ID"];
    $ID=$row1['ID'];
	$Group_Members=$row1['Group_Members'];
    if($ID>0)
    {
        if($Group_Members==2)
        {
            $query="SELECT COUNT(ID) as Users from ".$table_group_user." where Group_ID=".$ID." AND Status!='3'";
        }
        else
        {
            $query="SELECT COUNT(ID) as Users from ".$table_nurse."  where Status!='3' ";
        }
	
                $res= mysqli_query($link,$query);
                if(mysqli_num_rows($res)>0)
                {
                    while($row = mysqli_fetch_array($res))
                    {
						$count_admin=$row["Users"];
				
                    }
                }
    }
	$Name=$row1['Name'];
	$Description=$row1['Description'];	
	
	$Last_Login=$row1['Last_Login'];	
	$Status=$row1['Status'];	
	$Last_Login_Time = "";
	if(date('Y',strtotime($Last_Login))>2000)
	{
		$Last_Login_Time = date('d-m-Y',strtotime($Last_Login));
	}
	

		if($count_admin>0){
		$count++;
		$nestedData[] = $count;
		$nestedData[] = $Name;
		$nestedData[] = $count_admin;
		

		$action="";
			// if($Admin_Role_ID == $Super_Admin_Role_ID)
			// {
				if($User_ID_Group==NULL)
				{
					$action.= ' <div class="checkbox checkbox-purple checkbox-single">
								<input type="checkbox" id="singleCheckbox2" name="group[]" id="'.$ID.'" value="'.$ID.'">
									<label></label>
								</div>';

				}
				else if($User_ID_Group==$ID)
				{
					$action.= '<div class="checkbox checkbox-purple checkbox-single">
					<input type="checkbox" id="singleCheckbox2" name="group[]" id="'.$ID.'" value="'.$ID.'" checked>
						<label></label>
					</div>';
				}
				else 
				{
					$action.= '<div class="checkbox checkbox-purple checkbox-single">
					<input type="checkbox" id="singleCheckbox2" name="group[]" id="'.$ID.'" value="'.$ID.'">
						<label></label>
					</div>';
				}	
			// }
	
		$nestedData[] = $action;		
		$data[] = $nestedData;
    }
}
$Group_Members=$group_Member_value;

$admin_id=$_COOKIE['login_adminid'];
$Profile_img='';
$query1= "SELECT * FROM ".$table_admin." where ID='$admin_id' limit 1";
// echo $query1;exit();
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $Profile_img=$row1['profile_image'];
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
.figcaption-display{
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
.modal-open .modal {
    display: flex !important;
    align-items: center;
    justify-content: center;
}

.video-container{
  height:30vh;
  overflow:hidden;
  position:relative;
}


.video-container iframe,{
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.video-container iframe, {
  pointer-events: none;
}
.video-container iframe{
  position: absolute;
  top: -60px;
  left: 0;
  width: 100%;
  height: calc(100% + 120px);
}
.video-foreground{
  pointer-events:none;
}
#show-video{
    display:none;
}
#show-image{
    display:none;
}
#img-uploaded{
    height:25vh;
    max-width: 25vw;
}
.font-assistant-bold{
    font-family:Assistant;
    font-weight:700;
    color:#181b1e;
}
.font-assistant-content-regular{
    font-family:Assistant;
    font-weight:400;
    color:#525752;
    word-break: break-all;
}
.font-assistant-id-color{
    color:#525752;
    font-family:Assistant;
}
.content-id{
    background:#E7EFE7;
    padding:5px 12px;
    color:#10c469;
    border-radius:3px;
    font-family:Assistant;
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
                                <h4 class="page-title font-karla">Add New Content </h4>
                            </li>
                        </ul>
						<ul class="nav navbar-nav navbar-right">                            
                            <li>
                                <h5 class="page-title font-karla">Posted By - <?=$posted_by?></h5>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="content-page">
                <div class="content">

                    <div class="container">


                        <div class="row">
                            <div class="col-lg-2 col-xs-0"></div>
                            <div class="col-lg-8 screen" >
                                <div class="card-box">
                        			
 
									<form id="db_entry_form" action="content_entry.php"  enctype="multipart/form-data" class="form-horizontal form-align"  
                                    method="post" 
                                    data-parsley-validate novalidate>
										<input type="hidden" name="<?= $token_id; ?>" value="<?= $token_value; ?>" />
										<input type="hidden" id="entry_id" name="entry_id" value="<?= $entry_id;?>">  

											<div class="row">

												<div class="col-lg-11">
													<div class="form-group font-karla">
														<label for="userName">Content Title<span class="text-red">*</span></label>
														<input type="text" name="Content_Title" parsley-trigger="change" required
                                                        onkeyup="validateContentTitle(this.value)" placeholder="Content Title" class="form-control" id="Group_Name" value="<?= $Content_Title;?>">
                                                        <span id="errContentTitle"></span> 
                                                    </div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-11">
													<div class="form-group font-karla">
														<label for="emailAddress">Content Description<span class="text-red">*</span></label>
                                                        <textarea class="form-control" name="Content_Description" id="text-description"
                                                        placeholder="Content Description"
                                                        parsley-trigger="change" style="resize:none" required rows="3"><?=$Content_Description?></textarea>
													</div>
												</div>
											</div>

                                                <div class="form-group m-b-0 font-karla">
                                                    <label for="pass1">Group Members<span class="text-red">*</span></label>
                                                </div> 
                                                
                                            <div class="row m-b-15">
                                                    <div class="col-lg-2 font-karla">
                                                    <div class="radio radio-purple">
                                                        <input type="radio"  name="Group_Members" value="1" id="all" <?php if($Group_Members=="1") echo 'checked="checked"';?> required>
                                                        <label for="all">
                                                        All Users
                                                        </label>
                                                    </div>
                                                    </div>
                                                    <div class="col-lg-3 font-karla">
                                                    <div class="radio radio-purple">
                                                        <input type="radio" name="Group_Members" value="2" <?php if($Group_Members=="2") echo 'checked="checked"';?> id="specific">
                                                        <label for="specific">
                                                        Specific Groups
                                                        </label>
                                                    </div>
                                                    </div>
                                                </div>
                                                <div class="card-box table-responsive" id="showTable" <?php if($Group_Members==2) echo  'style="display:block;"'; else echo  'style="display:none;"';?> >
                                       

                                    <table class="font-karla table table-striped table-bordered dt-responsive nowrap">
                                                    <thead>
                                                        <tr>
                                                        <th>#</th>
                                                        <th>Group Name</th>
                                                        <th>Number of Users</th>
                                                        <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php 
                                                    if(!empty($data)){
                                                    foreach($data as $key=>$value){
                                          
                                                        ?>  
                                                   <tr>
                                                        <th><?= $value["0"] ?></th>
                                                        <td><?= $value["1"] ?></td>
                                                        <td><?= $value["2"] ?> </td>
                                                        <td>
                                                        <?= $value["3"] ?> 
                                                        </td>
                                                    </tr>
                                                    <?php }}?>
                               
                                                    </tbody>
                                                </table>
                                </div>
										
                                            <div class="row">
                                                <div class="col-lg-11 font-karla">
                                                    <div class="form-group">
                                                        <label for="pass1">Content Type<span class="text-red">*</span></label>
                                                         <select class="form-control select2" name="Content_Type_Select" id="Content_Type_Select" required="required" >
                                                                <option  disabled>Select Content Type</option>
                                                                <option value="4" <?php if($Content_Type==4) echo 'selected="selected"'?> selected>Text</option>
                                                                <option value="1" <?php if($Content_Type==1) echo 'selected="selected"'?>>Image</option>
                                                                <option value="2" <?php if($Content_Type==2) echo 'selected="selected"'?>>Video</option>
                                                                <option value="3" <?php if($Content_Type==3) echo 'selected="selected"'?>>Bulk Images</option>
                                                            </select>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                          <div class="row" id="single-Image"<?php if($Content_Type==1 && $img_uploaded!='') echo 'style="display:block;"'; else  echo 'style="display:none;"'; ?>>
                                    
                                          <div class="col-lg-3 font-karla">
                                                <div class="form-group">
                                                <span onclick="RemoveImage('<?php echo $img_uploaded;?>')">
                                                <i class="fa fa-times" aria-hidden="true"
                                                title="Delete Image"                                              
                                                style="float:right;cursor:pointer"></i></span>
                                                <?php 
                                                $image="";
                                                if($img_uploaded!="")
                                                {
                                                $image='uploads/Content_Attachments/'.$img_uploaded;
                                                }
                                                ?>
                                                    <img src="<?php echo $image;?>" 
                                                    class="img-responsive uploaded-images" alt="">
                                                </div>
                                            </div>
                                          </div>


                                          <div class="row" <?php if($Content_Type==2 && $url_display!='') echo 'style="display:block;"'; else  echo 'style="display:none;"'; ?>>
                                            <div class="col-lg-12 font-karla">
                                                <div class="form-group">
                                                <iframe src="<?php echo $url_display?>">
                                                </iframe>
                                           
                                                </div>
                                            </div>
                                          </div>
                                       
                                        <div class="row m-b-30 font-karla" <?php if($Content_Type==3 ) echo 'style="display:block;"'; else  echo 'style="display:none;"'; ?>>
                                        <?php 
                                            if(!empty($bulk_images)){
                                            foreach($bulk_images as $key=>$value){?>

                                                <div class="col-sm-3 m-b-10">
                                                <span onclick="RemoveImage('<?php echo $value;?>')" value="<?= $value;?>">
                                                <i class="fa fa-times" aria-hidden="true"
                                                title="Delete Image"                                              
                                                style="float:right;cursor:pointer"></i></span>
                                                <img src="uploads/Content_Attachments/<?php echo $value;?>" 
                                                            class="img-responsive uploaded-images" alt="">
                                                    </div> 
                                                                                          
                                            <?php }}?>
                                        </div>
                                            <div class="row">
                                          
                                                <div class="col-lg-11 font-karla" id="images" <?php if($Content_Type==1) echo 'style="display:block;"'; else  echo 'style="display:none;"'; ?> >
                                                    <div class="form-group">
                                                  
                                                   <input type='file' id='verborgen_file' name="Content_Type_image"  data-max-file-size="1M" <?php if($Content_Type==1 && $img_uploaded=="") echo 'required="required;"'; ?>  />
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-lg-11" id="video" <?php if($Content_Type==2) echo 'style="display:block;"'; else  echo 'style="display:none;"'; ?>>
                                                    <div class="form-group">
                                                  <!--type="url"  parsley-type="url"-->
                                                    <input type="text" name="Video_URL"   id="Video_URL" <?php if($Content_Type==2) echo 'required="required;"'; ?>
															   placeholder="Video URL" class="form-control" value="<?php echo $url_attachment?>" >
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-11" id="bulk_images" <?php if($Content_Type==3) echo 'style="display:block;"'; else  echo 'style="display:none;"'; ?>>
                                                    <div class="form-group">
                                                    <?php 
                                                    $status=0;
                                                        if(!empty($bulk_images)){
                                                            $status=1;
                                                        foreach($bulk_images as $key=>$value){?>
                                                            <input type="hidden" value="<?= $value; ?>" name="Content_Type[]">
                                                    <?php }}?>
                                                        <input type="file" name="Content_Type[]" id='Content_Type_image' multiple  <?php if($Content_Type==3 && $status==0) echo 'required="required;"'; ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="form-group text-center font-karla m-b-0 p-t-10 m-t-15">
                                           
                                        <?php if($entry_id==0){?>  <button class=" btn btn-purple w-md waves-effect waves-light" 
                                              style="padding: 6px 20px;" data-target="#myModal">   Save
                                        </button><?php }
                                        else{?><button class="btn btn-purple w-md waves-effect waves-light" 
                                        data-target="#myModal">   Update
                                            </button><?php }?>
                                            <a href="content_list.php" class="btn btn-default waves-effect waves-light m-l-15"
                                            style="padding:6px 32px">
                                                Cancel
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                         </div>
	<!-- sample modal content -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" style="width:400px">
                                            <div class="modal-content" >
                                            
                                                <div class="modal-body">
                                                    <div class="row m-b-10 ">
                                                        <div class="col-md-6">
                                                            <div>
                                                            <?php
                                                            $show_image="";
                                                            if($Profile_img!="")
                                                            {
                                                                $show_image='uploads/ProfileImages/'.$Profile_img;
                                                            }
                                                            else
                                                            {
                                                                $show_image="uploads/Content_Attachments/avatar.png";
                                                            }
                                                            ?>
                                                            
                                                                <img src="<?= $show_image ?>" 
                                                                class="img-circle thumb-sm" alt="Profile Pic" />
                                                                <span class="m-l-10 font-assistant-bold"> <?=$_COOKIE['login_postedby']?></span>
                                                                <p style="margin-left: 45px;
    margin-top: -10px;color:#A5AFA7">Just now</p>
                                                            </div>
                                                        </div>



                                                        <div class="col-md-6 ">
                                                            <div class="pull-right content-id">
                                                          
                                                            NH-<?php echo sprintf('%03d', $entry_id) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="m-b-20 font-assistant-content-regular" id="text">
                                                     </p>
                                                        <div class="video-container m-b-15" id="show-video">
                                                                <div class="video-foreground">
                                                                    <iframe id="video-display"
                                                                    src="#"
                                                                    frameBorder="0" width="350" height="200"> 
                                                                    </iframe>
                                                                </div>             
                                                        </div>    
                                                        <div class="text-center m-b-15" id="show-image">
                                                        <?php if(empty($bulk_images)){?>
                                                        
                                                            <img id="img-uploaded" src="#" alt="your image" />
                                                        <?php }?> 
                                                        <?php if(!empty($bulk_images)){?>
                                                            <img src="uploads/Content_Attachments/<?= $bulk_images[0] ?>" id="img-uploaded" alt="image-uploaded">
                                                        <?php }?>

                                                        </div>
                                                        <input type="hidden" name="is_upload" id="is_upload" value="0">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                        <img src="assets/images/ic_like_empty.svg" alt="Like icon">
                                                            <span class="m-l-5">Like</span>    
                                                        </div>
                                                        <div class="col-md-6">
                                                            <img src="assets/images/ic_comment.svg" alt="Comment icon">
                                                            <span class="m-l-5">Comment</span>                                                               
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer ">
                                                    <div class="text-center">
                                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" onclick="discardData()">Cancel</button>
                                                        <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light" 
                                                        id="submit-data"
                                                        form="db_entry_form">Save</button>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                    </div> 
                </div> 
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
let popupshow=false;
$('#myModal').on('hide.bs.modal', function (e) {
    popupshow=false;
});
$(document).ready(function()
{
    $("#text_content").prop("required", true);
})
$('#verborgen_file').show();
        $('#uploadButton').on('click', function () {
              $('#verborgen_file').click();
        });

        $('#verborgen_file').change(function () {
            var parsley_id=$('#verborgen_file').attr("data-parsley-id")
            var file = this.files[0];
            var fileSize = this.files[0].size; 
            if(fileSize>0)
            {
                $('#verborgen_file').prop('required',false)
                $('#parsley-id-'+parsley_id).hide()
            } 
            var size=fileSize/1000;
            let type=this.files[0].type;
            var reader = new FileReader();
            if(size<576 )
            {
                if(type!="image/jpeg" && type!="image/png")
                {
                    bootbox.alert("Upload an image file.");
                    $('#verborgen_file').val('');
                    $('#verborgen_file').prop('required',true);
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
                $('#verborgen_file').val('');
                $('#verborgen_file').prop('required',true);

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
 $(function(){
$('#Content_Type_image').on('change', function() {
    var parsley_id=$('#Content_Type_image').attr("data-parsley-id")
    if ($('#Content_Type_image').get(0).files.length > 0) {
        $('#Content_Type_image').prop('required',false);
            $('#parsley-id-'+parsley_id).hide()
}
var items = $('#Content_Type_image').get(0).files;
var fileSize = 0;
var fileType='';
var lg = $('#Content_Type_image').get(0).files.length; // get length
           if (lg > 0) {
               for (var i = 0; i < lg; i++) {
                  
                   fileSize = fileSize+items[i].size; // get file size
                   fileType=items[i].type;
                   console.log("fileType",fileType)
               
               if(fileType=="image/jpeg" && fileType=="image/png" )
               {
                if(fileSize < 576) {
                    bootbox.alert("image size is too large.");
                    $('#Content_Type_image').val('');
                    $('#Content_Type_image').prop('required',true);
                   // return false;
                    }
               
               
               }
               else if(fileType!="image/jpeg" && fileType!="image/png"){
                    console.log("fileType",fileType)
                    //alert("Upload an image file")
                   bootbox.alert("Upload an image file.");
                    $('#Content_Type_image').val('');
                    $('#Content_Type_image').prop('required',true);
                   // return false;
                   
                }
                else
               {
                        if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        console.log("change1")
                        $('#img-uploaded').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
               } 
               }
               
               
               
           }
})
}); 
$(function(){
$('#Content_Type_Select').on('change', function() {
   if(this.value==1)
   {
       $('#images').show();
       $('#video-display').attr('src','');
       $('#verborgen_file').prop('required',true);
       $('#Video_URL').prop('required',false);
       $('#Video_URL').val('');
       $('#Content_Type_image').prop('required',false);
       $('#video').hide();
       $('#bulk_images').hide();
       $('#Content_Type_image').val('')
   }
   if(this.value==2)
   {
       $('#images').hide();
       $('#video').show();
       $('#verborgen_file').val('');
       $('#img-uploaded').attr('src','');
       $('#verborgen_file').prop('required',false);
       $('#Video_URL').prop('required',true);
       $('#Content_Type_image').prop('required',false);
       $('#bulk_images').hide();
       $('#Content_Type_image').val('')
   }
   if(this.value==3)
   {
       $('#images').hide();
       $('#video').hide();
       $('#bulk_images').show();
       $('#video-display').attr('src','');
       $('#Video_URL').val('');
       $('#verborgen_file').prop('required',false);
       $('#Video_URL').prop('required',false);
       $('#Content_Type_image').prop('required',true);

   }
   if(this.value==4)
   {
       $('#images').hide();
       $('#video').hide();
       $('#bulk_images').hide();
       $('#video-display').attr('src','');
       $('#img-uploaded').attr('src','');
       $('#verborgen_file').prop('required',false);
       $('#Video_URL').prop('required',false);
       $('#Content_Type_image').prop('required',false);
       $('#Content_Type_image').val('')
       $('#Video_URL').val('');
   }
});
});
var errtitle = document.getElementById("errContentTitle");
            function validateContentTitle(name)
			{
                var mailformat = /^[a-zA-Z0-9 ]{1,100}$/;   
                if(name=='')
                {
                    errtitle.innerHTML= "Content Title cannot be empty";
					errtitle.style.color = "red";
                }     
				else if(!name.match(mailformat))
					{
						errtitle.innerHTML	= "Content Title cannot contain special characters";
						errtitle.style.color = "red";
					}
				else
					{
						errtitle.innerHTML	= ""; 
						errtitle.style.color = "none";
					}
            }
           function RemoveImage(img)
            {
                document.getElementById('single-Image').style.display = "none"
                let content_id='<?php echo $entry_id ;?>';
                bootbox.confirm({ 
                    size: "small",
                    message: "Are you sure you want to delete this?",
                    callback: function(result){ 
                        if(result == true){
                            $.ajax({
                                type: "POST",
                                url: "./deleteImage.php",   
                                data:{Content_ID:content_id,Attachment:img},
                                success: function (result) {
                                if(result==1)
                                {
                                    location.reload();
                                }
                                else
                                {
                                    bootbox.alert({ size: "small",message:"Error"})}
                                   

                                }
                            });
                        }
                    }
                })
                }

               /*  function validateYouTubeUrl()
                {
                    var url = $('#Video_URL').val();
                        if (url != undefined || url != '') {
                            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
                           /* var match = url.match(regExp);
                            if (match && match[2].length == 11) { 
                            }
                            else {
                                {bootbox.alert({ size: "small",message:"Enter Valid Youtube URL"})}
                            }
                        }
                } */

            $( "#db_entry_form" ).submit(function( event ) {
                         console.log("update");
                    var e = document.getElementById("Content_Type_Select");
                    var strUser = e.options[e.selectedIndex].value;
                    var image=document.getElementById("img-uploaded").value;
                    var is_upload=document.getElementById("is_upload").value;
                    var type=$('input[name=Group_Members]:checked', '#db_entry_form').val()
                    console.log("type",type)
                     var numberOfChecked = $('input[name="group[]"]:checked').length;
                     console.log("count",numberOfChecked)

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
                    switch(strUser)
                    {
                        case "2":
							let url=document.getElementById("Video_URL").value;
						   let display = "";
						   if(url.indexOf("/")!=-1){
								//display = url.replace("watch?v=", "embed/");
								display = url;
								//1. Contains watch
								if(url.indexOf("watch")!=-1){
									display=url.replace("watch?v=", "embed/");
								}
								//2. youtu.be
								if(url.indexOf("youtu.be")!=-1){
									display=url.replace("youtu.be", "youtube.com/embed/");
								}
								
							}else{								
								display =  "https://www.youtube.com/embed/";
								display = display.concat(url);
							}
							let appendurl="?controls=1&showinfo=0&rel=0";
							display=display.concat(appendurl);
							$('#video-display').attr('src', display);
							$("#show-video").css("display", "block");
							$("#show-image").css("display", "none");
                        break;
                        case "1":
                            console.log("Image",is_upload)
                            if(is_upload==0)
                            {
                                console.log("Image",image)
                                 let imagename='<?php echo $img_uploaded;?>'
                                console.log("image",imagename)
                                if(imagename!="")
                                {
                                $('#img-uploaded').attr('src', 'uploads/Content_Attachments/'+imagename);
                                $("#show-image").css("display", "block");
                                }
                                $("#show-video").css("display", "none");
                            }
                            if(is_upload==1)
                            {
                                $("#show-video").css("display", "none");
                                $("#show-image").css("display", "block");
                            }
                           
                            break;
                        case "3":
                           
                            $("#show-image").css("display", "block");
                            $("#show-video").css("display", "none");
                        break;
                        default:
                            $("#show-video").css("display", "none");
                            $("#show-image").css("display", "none");
                        break;
                    }
                    var db_entry_form = $("#db_entry_form");
                    console.log("parsley",db_entry_form.parsley().isValid())
                    if(db_entry_form.parsley().isValid()==true && errtitle.innerHTML==""){
                        $("#myModal").modal("show");
                       if(popupshow==false)
                        {event.preventDefault();}
                        popupshow=true;
                    }
                    else{
                        // $("#myModal").modal("hide");
                        bootbox.alert("Please fill all the details correctly"); 
                        event.preventDefault(); 
                        return false;
                    }
                    document.getElementById("text").innerHTML=document.getElementById("text-description").value;
                    }
                });
                let imagename='<?php echo $img_uploaded; ?>'
            $("#verborgen_file").change(function(){
                if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                console.log("change")
                $('#is_upload').val(1);
                $('#img-uploaded').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
   /*  $("#Content_Type_image").change(function(){
                if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                console.log("change1")
                $('#img-uploaded').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    }); */
    function discardData()
    {
        popupshow=false;
    }
</script>
<script type="text/javascript" src="assets/plugins/parsleyjs/dist/parsley.min.js"></script>
       