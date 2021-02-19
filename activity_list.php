<?php 
include "header.php";
$entry_id='';
$session_id='';
if(isset($_GET['id']))
{
	$entry_id=$_GET['id'];
}
if(isset($_GET['session']))
{
	$session_id=$_GET['session'];
}
$history_type=array();
$query= "SELECT * FROM ".$table_noora_history_type." WHERE Title!='Nurse Log out' && Title!='Nurse Log in'" ;
$res= mysqli_query($link,$query);
if(mysqli_num_rows($res)>0)
{
	while($row = mysqli_fetch_array($res))
	{
        $history_type[]=$row;
    }
}
?>


<!-- <style>
.font-karla{
font-family:'Karla' !important;
color:#3b3e47 !important;
}

.clr-white{
    color:#ffffff !important;
}
th:first-child,
td:first-child {
  display: none;
}
tr:hover {
          cursor:pointer
        }
</style> -->
        <link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
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
                                <h4 class="page-title font-karla">Activity List</h4>
                            </li>
                        </ul>

                      
                    </div><!-- end container -->
                </div><!-- end navbar -->
            </div>

            <div class="content-page">
                <!-- Start content -->
                <div class="content">
                    <div class="container">
                    <?php  $typeId= $_COOKIE['type']; 
                   // echo "type".$typeId;
                                  
                                   ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
									<div class="row">
                                    <div class="col-sm-3">
                  
                                    <select class="form-control select2 " name="type" id="type">
                                    <option value="0" selected>Select</option> 
                                    <?php if(is_array($history_type) && !empty($history_type))
                                    {
    
                                        foreach($history_type as $key=>$value)
                                        {
                                            ?>
                                            <option <?php if($typeId==$value["ID"]) echo "selected='selected'";?> value="<?=$value["ID"]?>"  ><?=$value["Title"]?></option> 
                                            <?php
                                        }
                                    }?>
     
                                                                 
                                                               
                                                               
                                                               
                                    </select>
                                    <script>
                                    $('#type').change(function()
                                    {
                                        var id=$(this).val()
                                      //  alert(id);
                                        document.cookie = "type = " + id
                                        location.reload()
                                        
                                    })
                                    </script>
                                    </div>
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-6">
                                    <a href="nurse_details.php?id=<?php echo $entry_id; ?>" type="button" class="clr-white font-karla btn btn-purple w-md waves-effect waves-light m-b-20 pull-right">
                                        Go To History</a>
                                    </div>
									
                                       
                                       
										
									</div><!-- end col -->

                                    <br>
                                  
                                    <div class="card-box table-responsive">
                                  
                                                  <table id="datatable-ajax" data-url="activity_details_list.php?id=<?php echo $entry_id ?>&session=<?php echo $session_id ?>&type=<?php echo $typeId ?>"  class="font-karla table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"     data-search_placeholder="Activity/Time">
                                        <thead>
                                            <tr>
                                            <th>Activity</th>
                                            <th>Time</th>
                                                
                                                
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                </div>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->


                    </div> <!-- container -->

                </div> <!-- content -->

               <footer class="footer">
                    <?php echo date('Y');?> © Noora.
                </footer>

<?php 
include "footer.php";
?>
<!-- Datatables-->
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>

<script src="assets/pages/datatables.ajax.js"></script>
<script type="text/javascript">

$(document).ready(function()
{
    // document.cookie = "type =0 "
})
   
</script>
