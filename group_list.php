<?php 
include "header.php";
$query1="SELECT * from ".$table_group." where Status!=3";

$count_admin=0;
$array=array();
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
    while($row1 = mysqli_fetch_array($res1))
    {
       
      if($row1["Group_Members"]==2)
      {
        $query2="SELECT COUNT(ID) as Users from ".$table_group_user." where  Group_ID=".$row1["ID"]." AND Status!=3";
        $res2= mysqli_query($link,$query2);
       // echo mysqli_num_rows($res2);
        if(mysqli_num_rows($res2)>0)
                {
                  
                    while($row = mysqli_fetch_array($res2))
                    {
                        if($row["Users"]!=0)
                        {
                           
                             $count_admin++;
                        }
                    } 
                  
                }
    }
    if($row1["Group_Members"]==1)
    {
        $count_admin++;
    }
        
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
th:first-child,
td:first-child {
  display: none;
}
        table.dataTable tbody tr:hover {
            background-color:#c7cce8 !important;
            cursor:pointer;
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

                        <!-- Page title -->
                        <ul class="nav navbar-nav navbar-left">
                            <li>
                                <button class="button-menu-mobile open-left">
                                    <i class="zmdi zmdi-menu"></i>
                                </button>
                            </li>
                            <li>
                                <h4 class="page-title font-karla">Groups</h4>
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
                                         <div class="col-lg-3 col-md-6 col-xs-6">
                                            <div class=" widget-user">
                                                <div>
                                                    <img src="assets/images/team.svg" class="img-responsive m-r-10" alt="user">
                                                    <div class="wid-u-info">
                                                        <h3 class="m-t-0 m-b-0 font-600 font-karla"><?=$count_admin?></h3>
                                                        <p class="text-muted m-b-5 font-karla"><b>Groups Added</b></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->

                                        <a href="group.php" type="button" class="clr-white font-karla btn btn-purple w-md waves-effect waves-light m-b-20 pull-right">Add New Entry</a>
                                        
                                        
                                    </div><!-- end col -->


                                    <br>
                                    <table id="datatable-ajax" data-url="group_detail_grid.php"
                                     class="font-karla table table-striped table-bordered dt-responsive nowrap" 
                                     cellspacing="0" width="100%"
                                     data-search_placeholder="Name/Group Description">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Group Description</th>
                                                <th>Group Members</th>
                                          
                                                
                                            </tr>
                                        </thead>
                                    </table>
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
$(document).on('click','.delete_btn',function()
{
    let id=this.rel;
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
});

    $(document).ready(function() {
    var table = $('#datatable-ajax').DataTable();
     
    $('#datatable-ajax tbody').on('click', 'tr', function () {
        var data = table.row( this ).data();
  
        window.location.href = "group.php?id="+data[0];
    } );
} );
</script>
