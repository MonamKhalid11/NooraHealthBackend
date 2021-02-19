<?php 
include "header.php";
$entry_id='';
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
$city_name="";
$query1= "SELECT * FROM ".$table_nurse." where ID='$entry_id' limit 1";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
		    $First_Name=$row1['First_Name'];
        $Last_Name=$row1['Last_Name'];
        $Mobile_Number=$row1['Mobile_Number'];
    $Trainer_Name = $row1['Trainer'];
    $CCP_Condition_ID = $row1['CCP_Condition_ID'];
  

        foreach($badge as $key=>$value)
        {
			if($key==$row1["Badge_Level"])
			$Noora_Badge=$value;
          
        }

        $Profile=$row1["profile_image"];
        $Graduating_Year=$row1["Graduating_Year"];
        $Joining_date= "";
		$dateJoin = $row1["Hospital_Joining_Date"];
		if($dateJoin!=""){
			$Joining_date= date('d M, Y',strtotime($dateJoin));
		}
        $Hospital_Name=$row1["Hospital_ID"];
        $Hospital_Condition_Array=explode(',', $row1['Hospital_Condition_ID']);
        
        // print_r(count($Hospital_Condition));

        if($Hospital_Name>0)
        {
          $query="Select Name,Medical_Condition from ".$table_noora_hospital." LEFT JOIN ".$table_noora_hospital_medical_condition." ON ".$table_noora_hospital.".ID=
          ".$table_noora_hospital_medical_condition.".Hospital_ID WHERE ".$table_noora_hospital.".ID=".$Hospital_Name;
          // echo $query;
          // exit();
          $res= mysqli_query($link,$query);
          if(mysqli_num_rows($res)>0)
          {
            while($row = mysqli_fetch_array($res))
            {
              $Hospital_Name=$row["Name"];
              // $Hospital_Condition=$row["Modical_Condition"];
            }
          }
        }else{
			$Hospital_Name = "";
		}
        $Hospital_Condition="";
        if(count($Hospital_Condition_Array)>0)
        {
          for ($x = 0; $x < count($Hospital_Condition_Array); $x++) {
            // echo $Hospital_Condition[$x];
            $query="select Medical_Condition from ".$table_noora_hospital_medical_condition." where id=".$Hospital_Condition_Array[$x]." and status!=3 ";
            // echo $query;
            $res= mysqli_query($link,$query);
            if(mysqli_num_rows($res)>0)
            {
              // echo mysqli_num_rows($res);
              while($row = mysqli_fetch_array($res))
              {
                $Hospital_Condition.=$row["Medical_Condition"];
                if($x < (count($Hospital_Condition_Array)-1))
                {
                  $Hospital_Condition.=", ";
                }
                // $Hospital_Condition=$row["Modical_Condition"];
              }
            }
        }
        }
        // echo $Hospital_Condition_Text;
        // exit();
        $Designation=$row1["Designation"];
		$CCP_Date="";
		$ccpDate = $row1["TOT_Date"];
		if($ccpDate!=""){
			$CCP_Date=date('d-m-Y',strtotime($ccpDate));
		}
        $Mentor_Name=$row1["CCP_Mentor"];
        $Booster_Training=$row1["Booster_Training"];
        $CityID=$row1["District_ID"];
        if($CityID>0)
        {
            $query2="SELECT ".$table_noora_state.".ID as State_ID,".$table_noora_state.".Name as State_Name,".$table_noora_country.".ID as Country_Id,".$table_noora_country.".Name as Country_Name,".$table_noora_district.".Name  from ".$table_noora_district." LEFT JOIN ".$table_noora_state." ON ".$table_noora_state.".ID=".$table_noora_district.".State_ID
            LEFT JOIN ".$table_noora_country." ON ".$table_noora_country.".ID=".$table_noora_state.".Country_ID WHERE ".$table_noora_district.".ID=".$CityID;

            $res2= mysqli_query($link,$query2);
            if(mysqli_num_rows($res2)>0)
            {
                while($row2 = mysqli_fetch_array($res2))
	            {
                    $state_id=$row2["State_ID"];
                    $state_name=$row2["State_Name"];
                    $country_id=$row2["Country_Id"];
                    $country_name=$row2["Country_Name"];
                    $city_name=$row2["Name"];
                }
            }

        }
	}
}

// CCP CONDTION TYPE
$query1= "SELECT * FROM ".$table_ccp_class_type."";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
  while($row1 = mysqli_fetch_array($res1))
	{
    if($row1['ID'] ==  $CCP_Condition_ID )
    {
       $CCP_Condition_Area = $row1['Class_Type'];
    }
  }
}

$Hospital=array();
$query1= "SELECT * FROM ".$table_noora_hospital."";
$res1= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
	while($row1 = mysqli_fetch_array($res1))
	{
        $Hospital[]=$row1;
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

$Attandence_count=0;
$query1="SELECT COUNT(".$table_attendance.".ID) as attandence_count FROM ".$table_attendance." 
LEFT JOIN ".$table_ccp_class_type." ON ".$table_attendance.".Class_Type_ID = ".$table_ccp_class_type.".ID 
WHERE 1=1 AND ".$table_attendance.".Status != '3' AND ".$table_attendance.".Login_User_ID=".$entry_id;
$res2= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
    while($row1 = mysqli_fetch_array($res2))
    {
        $Attandence_count=$row1["attandence_count"];
    }
}
require_once("csrf.class.php");
$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_value = $csrf->get_token($token_id);

$Nurse_History_Array=array();

$query1="SELECT ".$table_nurse_history.".*,DATE_FORMAT(".$table_nurse_history.".Entry_Time,'%H:%i:%S') AS TIME,".$table_noora_history_type.".Title, ".$table_noora_history_type.".Image, 
".$table_noora_history_type.".Description FROM `".$table_nurse_history."` LEFT JOIN  ".$table_noora_history_type." ON 
".$table_nurse_history.".History_Type_Id =  ".$table_noora_history_type.".Id where ".$table_nurse_history.".NurseID=".$entry_id." 
order by ".$table_nurse_history.".Entry_Time Desc,Time ASC";
//echo $query1;
$res2= mysqli_query($link,$query1);
if(mysqli_num_rows($res1)>0)
{
    while($row1 = mysqli_fetch_array($res2))
    {
     
      $data = array();
      $Entry_Date = date('d M, Y',strtotime($row1['Entry_Time']));
      $Entry_Time= date("g:i:s A", strtotime($row1['Entry_Time']));
      $data[] = $Entry_Date;
      $data[] = $Entry_Time;
      //$data[] = $row1['Session_Id'];
      $data[] = $row1['Title'];
      $data[] = $row1['Description'];
      $data[] = $row1['Image'];
      $Nurse_History_Array[]= $data;

    }
}

?>
  <link href="assets/plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<style type="text/css">

.status-container {
  width: 90%;
  max-width: 1170px;
  margin: 0 auto;
}
.status-container::after {
  content: '';
  display: table;
  clear: both;
}

@media only screen and (min-width: 1170px) {
  header {
    height: 300px;
    line-height: 300px;
  }
  header h1 {
    font-size: 24px;
    font-size: 1.5rem;
  }
}

#status-timeline {
  position: relative;
  padding: 2em 0;
  margin-top: 2em;
  margin-bottom: 2em;
}
#status-timeline::before {
  content: '';
  position: absolute;
  top: 0;
  left: 18px;
  height: 100%;
  width: 4px;
  background: #d7e4ed;
}
@media only screen and (min-width: 1170px) {
  #status-timeline {
    margin-top: 3em;
    margin-bottom: 3em;
  }
  #status-timeline::before {
    left: 50%;
    margin-left: -2px;
  }
}

.status-timeline-block {
  position: relative;
  margin: 2em 0;
}
.status-timeline-block:after {
  content: "";
  display: table;
  clear: both;
}
.status-timeline-block:first-child {
  margin-top: 0;
}
.status-timeline-block:last-child {
  margin-bottom: 0;
}
@media only screen and (min-width: 1170px) {
  .status-timeline-block {
    margin: 4em 0;
  }
  .status-timeline-block:first-child {
    margin-top: 0;
  }
  .status-timeline-block:last-child {
    margin-bottom: 0;
  }
}

.status-timeline-img {
  position: absolute;
  top: 0;
  left: 0;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  box-shadow: 0 0 0 4px white, inset 0 2px 0 rgba(0, 0, 0, 0.08), 0 3px 0 4px rgba(0, 0, 0, 0.05);
}
.status-timeline-img img {
  display: block;
  width: 24px;
  height: 24px;
  position: relative;
  left: 50%;
  top: 50%;
  margin-left: -12px;
  margin-top: -12px;
}
.status-timeline-img.status-picture {
  background: #75ce66;
}
.status-timeline-img.status-movie {
  background: #c03b44;
}
.status-timeline-img.status-location {
  background: #f0ca45;
}
@media only screen and (min-width: 1170px) {
  .status-timeline-img {
    width: 60px;
    height: 60px;
    left: 50%;
    margin-left: -30px;
    -webkit-transform: translateZ(0);
    -webkit-backface-visibility: hidden;
  }
  .cssanimations .status-timeline-img.is-hidden {
    visibility: hidden;
  }
  .cssanimations .status-timeline-img.bounce-in {
    visibility: visible;
    -webkit-animation: status-bounce-1 0.6s;
    -moz-animation: status-bounce-1 0.6s;
    animation: status-bounce-1 0.6s;
  }
}

@-webkit-keyframes status-bounce-1 {
  0% {
    opacity: 0;
    -webkit-transform: scale(0.5);
  }

  60% {
    opacity: 1;
    -webkit-transform: scale(1.2);
  }

  100% {
    -webkit-transform: scale(1);
  }
}
@-moz-keyframes status-bounce-1 {
  0% {
    opacity: 0;
    -moz-transform: scale(0.5);
  }

  60% {
    opacity: 1;
    -moz-transform: scale(1.2);
  }

  100% {
    -moz-transform: scale(1);
  }
}
@keyframes status-bounce-1 {
  0% {
    opacity: 0;
    -webkit-transform: scale(0.5);
    -moz-transform: scale(0.5);
    -ms-transform: scale(0.5);
    -o-transform: scale(0.5);
    transform: scale(0.5);
  }

  60% {
    opacity: 1;
    -webkit-transform: scale(1.2);
    -moz-transform: scale(1.2);
    -ms-transform: scale(1.2);
    -o-transform: scale(1.2);
    transform: scale(1.2);
  }

  100% {
    -webkit-transform: scale(1);
    -moz-transform: scale(1);
    -ms-transform: scale(1);
    -o-transform: scale(1);
    transform: scale(1);
  }
}
.status-timeline-content {
  position: relative;
  margin-left: 60px;
  background: white;
  border-radius: 0.25em;
  padding: 1em;
  box-shadow: 0 3px 0 #d7e4ed;
}
.status-timeline-content:after {
  content: "";
  display: table;
  clear: both;
}
.status-timeline-content h2 {
  color: #303e49;
}
.status-timeline-content p, .status-timeline-content ul, .status-timeline-content .status-read-more, .status-timeline-content .status-date {
  font-size: 13px;
  font-size: 0.8125rem;
}
.status-timeline-content .status-read-more, .status-timeline-content .status-date {
  display: inline-block;
}
.status-timeline-content p {
  margin: 1em 0;
  line-height: 1.6;
}
.status-timeline-content ul {
	margin: 1em 0;
 	line-height: 1.6;
}
.status-timeline-content .status-read-more {
  float: right;
  padding: .8em 1em;
  background: #acb7c0;
  color: white;
  border-radius: 0.25em;
}
.no-touch .status-timeline-content .status-read-more:hover {
  background-color: #bac4cb;
}
.status-timeline-content .status-date {
  float: left;
  padding: .8em 0;
  opacity: .7;
}
.status-timeline-content::before {
  content: '';
  position: absolute;
  top: 16px;
  right: 100%;
  height: 0;
  width: 0;
  border: 7px solid transparent;
  border-right: 7px solid white;
}
@media only screen and (min-width: 768px) {
  .status-timeline-content h2 {
    font-size: 20px;
    font-size: 1.25rem;
  }
  .status-timeline-content p {
    font-size: 16px;
    font-size: 1rem;
  }
  .status-timeline-content .status-read-more, .status-timeline-content .status-date {
    font-size: 14px;
    font-size: 0.875rem;
  }
}
@media only screen and (min-width: 1170px) {
  .status-timeline-content {
    margin-left: 0;
    padding: 1.6em;
    width: 45%;
  }
  .status-timeline-content::before {
    top: 24px;
    left: 100%;
    border-color: transparent;
    border-left-color: white;
  }
  .status-timeline-content .status-read-more {
    float: left;
  }
  .status-timeline-content .status-date {
    position: absolute;
    width: 100%;
    left: 122%;
    top: 6px;
    font-size: 16px;
    font-size: 1rem;
  }
  .status-timeline-block:nth-child(even) .status-timeline-content {
    float: right;
  }
  .status-timeline-block:nth-child(even) .status-timeline-content::before {
    top: 24px;
    left: auto;
    right: 100%;
    border-color: transparent;
    border-right-color: white;
  }
  .status-timeline-block:nth-child(even) .status-timeline-content .status-read-more {
    float: right;
  }
  .status-timeline-block:nth-child(even) .status-timeline-content .status-date {
    left: auto;
    right: 122%;
    text-align: right;
  }
  .cssanimations .status-timeline-content.is-hidden {
    visibility: hidden;
  }
  .cssanimations .status-timeline-content.bounce-in {
    visibility: visible;
    -webkit-animation: status-bounce-2 0.6s;
    -moz-animation: status-bounce-2 0.6s;
    animation: status-bounce-2 0.6s;
  }
}

@media only screen and (min-width: 1170px) {
  .cssanimations .status-timeline-block:nth-child(even) .status-timeline-content.bounce-in {
    -webkit-animation: status-bounce-2-inverse 0.6s;
    -moz-animation: status-bounce-2-inverse 0.6s;
    animation: status-bounce-2-inverse 0.6s;
  }
}
@-webkit-keyframes status-bounce-2 {
  0% {
    opacity: 0;
    -webkit-transform: translateX(-100px);
  }

  60% {
    opacity: 1;
    -webkit-transform: translateX(20px);
  }

  100% {
    -webkit-transform: translateX(0);
  }
}
@-moz-keyframes status-bounce-2 {
  0% {
    opacity: 0;
    -moz-transform: translateX(-100px);
  }

  60% {
    opacity: 1;
    -moz-transform: translateX(20px);
  }

  100% {
    -moz-transform: translateX(0);
  }
}
@keyframes status-bounce-2 {
  0% {
    opacity: 0;
    -webkit-transform: translateX(-100px);
    -moz-transform: translateX(-100px);
    -ms-transform: translateX(-100px);
    -o-transform: translateX(-100px);
    transform: translateX(-100px);
  }

  60% {
    opacity: 1;
    -webkit-transform: translateX(20px);
    -moz-transform: translateX(20px);
    -ms-transform: translateX(20px);
    -o-transform: translateX(20px);
    transform: translateX(20px);
  }

  100% {
    -webkit-transform: translateX(0);
    -moz-transform: translateX(0);
    -ms-transform: translateX(0);
    -o-transform: translateX(0);
    transform: translateX(0);
  }
}
@-webkit-keyframes status-bounce-2-inverse {
  0% {
    opacity: 0;
    -webkit-transform: translateX(100px);
  }

  60% {
    opacity: 1;
    -webkit-transform: translateX(-20px);
  }

  100% {
    -webkit-transform: translateX(0);
  }
}
@-moz-keyframes status-bounce-2-inverse {
  0% {
    opacity: 0;
    -moz-transform: translateX(100px);
  }

  60% {
    opacity: 1;
    -moz-transform: translateX(-20px);
  }

  100% {
    -moz-transform: translateX(0);
  }
}
@keyframes status-bounce-2-inverse {
  0% {
    opacity: 0;
    -webkit-transform: translateX(100px);
    -moz-transform: translateX(100px);
    -ms-transform: translateX(100px);
    -o-transform: translateX(100px);
    transform: translateX(100px);
  }

  60% {
    opacity: 1;
    -webkit-transform: translateX(-20px);
    -moz-transform: translateX(-20px);
    -ms-transform: translateX(-20px);
    -o-transform: translateX(-20px);
    transform: translateX(-20px);
  }

  100% {
    -webkit-transform: translateX(0);
    -moz-transform: translateX(0);
    -ms-transform: translateX(0);
    -o-transform: translateX(0);
    transform: translateX(0);
  }
}
.history-section img {
    width: 43px;
    height: 43px;
    margin: auto;    
    display: block;
}
.nurse-profile{
  border-radius:50%;
  width:100px;
  height:100px;
}
.font-karla{
font-family:'Karla' !important;
color:#3b3e47 !important;
}

.clr-white{
    color:#ffffff !important;
}
.h4-styles{
  font-size:20px;
  display:inline !important;
}
.lbl-active{
  border-radius: 18px;
  font-size: 14px;
  padding: 4px 25px;
}
.btn-styles{
  /* left: 180px; */
  border-radius:4px;
  border:1px solid #CAD1D6;
  color:#858C91;
}
.edit-clr{
  color:#4BB75E;
}
.mt-3{
  margin-bottom: 3%;
}
.first-row{
  font-size: 18px;
  font-weight:600;
  overflow: hidden;
  text-overflow: ellipsis;
}
.second-row{
  font-size: 16px;
  font-weight:400;
}
.mt-21{
  margin-top:21px;
}
@media (max-width:480px)  { /* smartphones, iPhone, portrait 480x320 phones */ 
  .mt-21{
  margin-top:0px;
}
}
.log-date{
  color:#858C91;
  font-size:14px;
}
.m-auto{
  margin:auto;
}
.log-details-bg{
  background:#EBEFF2;
  border-radius:4px;
  box-shadow:0px 3px #000000 0.1;
}
.log-text{
  font-size: 16px;
}
.bg-muted {
    background-color: #ffffff !important;
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

                        <!-- Page title -->
                        <ul class="nav navbar-nav navbar-left">
                            <li>
                                <button class="button-menu-mobile open-left">
                                    <i class="zmdi zmdi-menu"></i>
                                </button>
                            </li>
                            <li>
                                <h4 class="page-title font-karla">View Nurse Details</h4>
                            </li>
                        </ul>

                       

                    </div><!-- end container -->
                </div><!-- end navbar -->
            </div>

            <div class="content-page">
                <!-- Start content -->
                <div class="content">

                    <div class="container">
                    <div class="card-box">

                        <div class="row">
                            <div class="col-lg-2 col-xs-0"></div>
                            <div class="col-lg-8 screen" >
                                                               
                                    <div class="row m-t-15 mt-3" >
								
									    <div class="col-lg-2 text-center">
									        <img src="uploads/NurseImage/<?=$Profile?>" <?php if($Profile!="")  echo  'style="display:block;"'; else echo  'style="display:none;"';?> 
                          class="nurse-profile">
                          <img src="assets/images/user.svg" <?php if($Profile=="")  echo  'style="display:block;"'; else echo  'style="display:none;"';?> 
                           class="nurse-profile">
                        </div>
									<div class="col-lg-2 font-karla m-t-15 ">
                                        <h4 class="first-row m-b-0" >
                                        <?=$First_Name ." ".$Last_Name?></h4>
                                        <p class="second-row">
                                        <b><?=$Mobile_Number?></b></p>
									</div>
									<div class="col-lg-2 font-karla m-t-15" >
                                        <h4 class="first-row m-b-0">
                                        <b>Noora Badge</b></h4>
                                        <p class="second-row">
                                        <b><?=$Noora_Badge?></b></p>
									</div>
									<div class="col-lg-2 font-karla m-t-15" >
                                        <h4 class="first-row m-b-0">
                                        Date Of Joining</h4>
                                        <p class="second-row" >
                                        <b><?=$Joining_date?></b></p>
									</div>
									<div class="col-lg-2 mt-21">
                    <a class="btn btn-trans waves-effect waves-light m-b-5 font-karla pull-right btn-styles"
                    href="nurse.php?id=<?=$entry_id?>">
                      <i class="fa fa-pencil m-r-5 edit-clr" ></i> <span>Edit&nbsp;&nbsp;</span> 
                    </a>  
                    </div>
									<div class="col-lg-2 mt-21">

                    <button onclick="deleteNurse('<?=$entry_id?>')" class="btn btn-light btn-trans waves-effect 
                    waves-light m-b-5 font-karla btn-styles" style="background:#ffffff;">
                    <i class="fa fa-trash-o text-danger" ></i> Delete </button> 
									</div>
                                </div>
                                    <form>
                                        <div id="basicwizard" class=" pull-in">
                                        <ul class="nav nav-pills ">
                                          <li class="nav-item">
                                            <a class="nav-link active font-karla" href="#tab1" data-toggle="tab">
                                            Personal Details</a>
                                          </li>
                                          <li class="nav-item">
                                            <a class="nav-link font-karla"  href="#tab2" data-toggle="tab">
                                            CCP Attendance</a>
                                          </li>
                                          <li class="nav-item">
                                            <a class="nav-link font-karla" href="#tab3" data-toggle="tab">
                                            History Log</a>
                                          </li>
                                          
                                        </ul>
                                       
                                            <div class="tab-content b-0 m-b-0">
                                                <div class="tab-pane m-t-10 fade" id="tab1">
                                                   
                                                
                                    <form id="db_entry_form1" action="nurse_entry.php" class="form-horizontal form-align" method="post" data-parsley-validate novalidate>
                                        <input type="hidden" name="<?= $token_id; ?>" value="<?= $token_value; ?>" />
                                        <input type="hidden" id="entry_id" name="entry_id" value="<?= $entry_id;?>">  
                                            <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Graduating Year*</label>
                                                        <input type="text" name="Graduating_Year" required
                                                               placeholder="Graduating Year" class="form-control" id="Graduating_Year" 
                                                               value="<?= $Graduating_Year;?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Date of Joining Hospital*</label>
                                                        <div class="input-group">
                                                          <input type="text" parsley-trigger="change"  required class="form-control datepicker-autoclose1"
                                                           name="Date_Join" placeholder="dd/mm/yyyy" id="Date_Join" value="<?=$Joining_date?>" disabled>
                                                          <span class="input-group-addon bg-purple b-0 text-white"><i class="ti-calendar"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                      
                                            <div class="row">
                                               <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Name of Hospital*</label>
                                                        <input type="text" name="Hospital_Name" required
                                                               placeholder="Hospital Name" class="form-control" id="Hospital_Name" 
                                                               value="<?= $Hospital_Name;?>" disabled>
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-lg-1"> </div>
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Medical Condition Area*</label>
                                                        <input type="text" name="Medical_Conditions" required
                                                               placeholder="Hospital Condition" class="form-control" id="Medical_Conditions"
                                                                value="<?= $Hospital_Condition;?>" disabled>
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                            
                                             <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Country*</label>
                                                        <input type="text" name="Country" required
                                                               placeholder="Country" class="form-control" id="Graduating_Year"
                                                                value="<?= $country_name;?>" disabled>
                                                         
                                                    </div>
                                                </div>
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">State*</label>
                                                        <input type="text" name="State" required
                                                               placeholder="State" class="form-control" 
                                                               id="Graduating_Year" value="<?= $state_name;?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                             <div class="row">
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">District*</label>
                                                        <input type="text" name="City" required
                                                               placeholder="City" class="form-control" 
                                                               id="Graduating_Year" value="<?= $city_name;?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Designation of Nurse*</label>
                                                        <input type="text" name="First_Name" parsley-trigger="change" required
                                                               placeholder="Designation" class="form-control" 
                                                               id="First_Name" value="<?= $Designation;?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                     
                                               <div class="row">
                                               <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="userName">Noora Badge(Level 1 - 5)</label>
                                                        <input type="text" name="Last_Name" parsley-trigger="change" required
                                                               placeholder="Noora Badge" class="form-control" id="Badge" 
                                                               value="<?= $Noora_Badge;?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="emailAddress">CCP TOT Date*</label>
                                                        <input type="email" name="CCP_Date" parsley-trigger="change" required
                                                               placeholder="CCP TOT Date" class="form-control" id="CCP_Date"
                                                                value="<?= $CCP_Date;?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
											
                                              <div class="row">
                                               <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="trainerName">Trainer</label>
                                                        <input type="text" name="Last_Name" parsley-trigger="change" required
                                                               placeholder="Trainer Name" class="form-control" id="Badge" 
                                                               value="<?= $Trainer_Name;?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1"></div>
                                                <div class="col-lg-5">
                                                    <div class="form-group font-karla">
                                                        <label for="">Condition Area for CPP*</label>
                                                        <input type="text" name="CCP_Condtion_Type" parsley-trigger="change" required
                                                               placeholder="CCP Condtion Type" class="form-control" id="CCP_Condtion_Type"
                                                                value="<?= $CCP_Condition_Area;?>" disabled>
                                                    </div>
                                                </div>
                        
                                            </div>
                                    </form>
                                                </div>
                                                <div class="tab-pane m-t-10 fade" id="tab2">
                                                <div class="row">
                                                  <div class="col-lg-3 col-md-6">
                                                    <div class=" widget-user">
                                                      <div>
                                                        <img src="assets/images/tick-icon.svg" class="img-responsive m-r-10" alt="user">
                                                        <div class="wid-u-info">
                                                          <h3 class="m-t-0 m-b-5 font-600"><?php echo $Attandence_count?></h3>
                                                          <p class="text-muted m-b-5 font-karla"><b>Attendance Added</b></p>
                                                        
														</div>
                                                       
                                                      </div>
                                                    </div>
                                                  </div><!-- end col -->
												  <div class="col-lg-9 col-md-6">
                                                   <div class="pull-right m-r-15">
                                                         <a href="excel.php?id=<?php echo $entry_id ?>">
                                                          <img src="assets/images/download.svg" title="Export Data" class="img-responsive " alt="download">
                                                          </a>
                                                        </div>
													</div>
                                                  </div>
                                                    <div class="card-box table-responsive">
                                                  <table id="datatable-ajax" data-url="ccp_attendance.php?id=<?php echo $entry_id ?>" class="font-karla table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%" data-search_placeholder="Name/Class Type/Hospital Name">
                                        <thead>
                                            <tr>
                                                <th>Day & Date</th>
                                                <th>Time</th>
                                                <th>Class Type</th>
                                                <th>Class Image</th>
                                                <th>Action</th>
                                                
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                                </div>

                                                <div class="tab-pane m-t-10 fade" id="tab3">
                                              <input type="hidden" id="data_id" value="<?=$entry_id?>">
                                                    <div class="card-box table-responsive">
                                                  <table id="datatable-ajax1" data-url="activity_list_grid.php?id=<?php echo $entry_id ?>" class="font-karla table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"  data-search_placeholder="Date(01 Jan 2000)">
                                        <thead>
                                            <tr>
                                                <th>Session Start</th>
                                                <th>Session End</th>
                                                <th>Activity</th>
                                                
                                                
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                                </div>




                                                
                                                
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
<script src="assets/pages/datatable.ajax1.js"></script>
<script>
 function deleteNurse(nurseid)
{
  let id=nurseid;
    bootbox.confirm({ 
    size: "small",
    message: "Are you sure you want to delete this?",
    callback: function(result){ 
        if(result == true){
            $.ajax({
                type: "POST",
                url: "./delete.php",   
                data:{id:id,status:"Nurse"},
                success: function (result) {
					console.log(result)
					if(result==1){
						window.location.href = "nurse_list.php";
					}
					else{
						bootbox.alert({ size: "small",message:"Error"})
					}
                }
            });
        }
    }
})
};
</script>
<script type="text/javascript">

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
          //  console.log(result)
           $('#Hospital_Condition').html(result)
       }
      }
 }); 
}
jQuery('.datepicker-autoclose1').datepicker({
                autoclose: true,
                todayHighlight: true
            });
 $(".select2").select2();

                $(".select2-limiting").select2({
                  maximumSelectionLength: 2
                });
 $('#basicwizard').bootstrapWizard({'tabClass': 'nav nav-tabs navtab-wizard nav-justified bg-muted'});



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
                data:{id:id,status:'Attendence'},
                success: function (result) {
                    // console.log(result)
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
$(document).on("click",".activities",function() {
  var session_id=$(this).attr("rel");
  var id=$(this).attr("data_id");
       // alert(session_id);
       window.location.href = "activity_list.php?id="+id+'&session='+session_id;
    });

</script>
<script type="text/javascript" src="assets/plugins/parsleyjs/dist/parsley.min.js"></script>
       