<?php 
include "header.php";
$query1="SELECT COUNT(ID) as Admins from ".$table_admin." where Role_ID=2 AND Status != '3'";
$count_admin=0;
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
    while($row1 = mysqli_fetch_array($res1))
    {
        $count_admin=$row1["Admins"];
    }
}
$query2="SELECT COUNT(ID) as Manager from ".$table_admin." where Role_ID=3 AND Status != '3'";
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
<style>
.font-karla{
font-family:'Karla' !important;
color:#3b3e47 !important;
}
table.dataTable thead > tr > th {
    padding-left: 8px;
    padding-right: 30px;
    font-weight: bold;
    font-size: 16px;
}
tbody > tr{
    font-size: 14px;
    
}
#datatable-ajax a {
    padding: 5px;
}
tbody {
    color: #3b3e47 !important;
}
div.dataTables_length label {
    font-family:'Karla' !important;
    font-weight: 600 !important;
    color:#666666 !important;
}
.dataTables_filter label{
    font-family:'Karla' !important;
    font-weight: 600 !important;
    color:#666666 !important;  
}
.dataTables_info{
    font-family:'Karla' !important;
    font-weight: 600 !important;
    color:#666666 !important;  
}
.pagination{
    font-family:'Karla' !important;
    font-weight: 600 !important;
    color:#666666 !important;    
}
.pagination > .active > a{
    background-color:#5b69bc !important;
    border-color:#5b69bc !important;

}
.clr-white{
    color:#ffffff !important;
}
th:first-child,
td:first-child {
  display: none;
}
/* tr :hover {
          cursor:pointer
        } */
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

                        <ul class="nav navbar-nav navbar-left">
                            <li>
                                <button class="button-menu-mobile open-left">
                                    <i class="zmdi zmdi-menu"></i>
                                </button>
                            </li>
                            <li>
                                <h4 class="page-title font-karla">Admin and Manager List</h4>
                            </li>
                        </ul>

                      
                    </div><!-- end container -->
                </div><!-- end navbar -->
            </div>

            <div class="content-page">
                <div class="content">
                    <div class="container">

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <div class="row">
                                         <div class="col-lg-2 col-md-6 col-xs-6">
                                            <div class=" widget-user">
                                                <div>
                                                    <img src="assets/images/manager.svg" class="img-responsive m-r-10" alt="user">
                                                    <div class="wid-u-info">
                                                        <h3 class="m-t-0 m-b-0 font-600 font-karla"><?=$count_admin?></h3>
                                                        <p class="text-muted m-b-5 font-karla" ><b>Total Admin</b></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->

                                        <div class="col-lg-3 col-md-6 col-xs-6">
                                            <div class=" widget-user">
                                                <div>
                                                    <img src="assets/images/decision-making.svg" class="img-responsive m-r-10" alt="user">
                                                    <div class="wid-u-info">
                                                        <h3 class="m-t-0 m-b-0 font-600 font-karla"> <?=$count_manager?></h3>
                                                        <p class="text-muted m-b-5 font-karla" ><b>Total Manager</b></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- end col -->

                                       
                                        <a href="admin_manager.php" class="clr-white font-karla btn btn-purple w-md waves-effect waves-light m-b-20 pull-right">
                                        Add New Entry
                                        </a>
                                        
                                        
                                    </div><!-- end col -->
                               <br>
                               
                                <table id="datatable-ajax" data-url="admin_manger_detail_grid.php" 
                                class="font-karla table table-striped dataTable no-footer" 
                                cellspacing="0" width="100%" 
                                data-search_placeholder="Name/Email/Mobile/District/Role">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>District</th>
                                                <th>Role</th>
                                                <th>Last&nbsp;Login</th>
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
<script src="assets/plugins/delete_box/bootstrap-confirm-delete.js"></script>

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
                url: "./delete",   
                data:{id:id,status:"Admin_Manager"},
                success: function (result) {
                    console.log(result)
                if(result==1)
                {
                    location.reload();
                }
                else
                {bootbox.alert({ size: "small",message:"Error"})}
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
        window.location.href = "admin_manager.php?id="+data[0];
    } );
} ); 
    

</script>
