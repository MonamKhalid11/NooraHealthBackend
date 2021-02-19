<?php 
include "header.php";
$query1="SELECT COUNT(ID) as Admins from ".$table_admin." where Role_ID=2 and Status!=3";
$count_admin=0;
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
    while($row1 = mysqli_fetch_array($res1))
    {
        $count_admin=$row1["Admins"];
    }
}
$query2="SELECT COUNT(ID) as Manager from ".$table_admin." where Role_ID=3 and Status!=3";
$count_manager=0;
$res2= mysqli_query($link,$query2);
if(mysqli_num_rows($res1)>0)
{
    while($row1 = mysqli_fetch_array($res2))
    {
        $count_manager=$row1["Manager"];
    }
}
 
?>
 <!-- DataTables -->
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
                                <h4 class="page-title">Edit Your Profile</h4>
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
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
									<div class="row">
										 <div class="col-lg-3 col-md-6">
											<div class="card-box widget-user">
												<div>
													<img src="assets/images/manager.png" class="img-responsive " alt="user">
													<div class="wid-u-info">
														<h4 class="m-t-0 m-b-5 font-600"><?=$count_admin?></h4>
														<p class="text-muted m-b-5 font-13">Total Admin</p>
													</div>
												</div>
											</div>
										</div><!-- end col -->

										<div class="col-lg-3 col-md-6">
											<div class="card-box widget-user">
												<div>
													<img src="assets/images/decision-making.png" class="img-responsive img-circle" alt="user">
													<div class="wid-u-info">
														<h4 class="m-t-0 m-b-5 font-600"> <?=$count_manager?></h4>
														<p class="text-muted m-b-5 font-13">Total Manager</p>
													</div>
												</div>
											</div>
										</div><!-- end col -->
                                        <a href="profile.php" type="button" class="btn btn-primary waves-effect w-md waves-light m-b-5 pull-right">Add New User</a>
										
										
									</div><!-- end col -->


                                    <table id="datatable-ajax" data-url="profile_detail_grid_data.php" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>City</th>
                                                <th>Role</th>
                                                <th>Last&nbsp;Login</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div><!-- end col -->
                        </div>
                      

                    </div> <!-- container -->

                </div> <!-- content -->

               <footer class="footer">
                    2019 © Noora.
                </footer>

<?php 
include "footer.php";
?>
<!-- Datatables-->
<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>


<script src="assets/pages/datatables.ajax.js"></script>

