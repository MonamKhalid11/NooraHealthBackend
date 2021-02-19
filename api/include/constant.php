<?php
include("setting.php");

// Toggle between Live and Dev mode
$DEBUG 		= false;


// =========-------SERVER---DETAILS-----------------------===============

$PATH_SERVER_URL = "http://unexploredgoa.com";

 // live
$PATH_SERVER_URL = "http://34.94.112.24";

$IMAGE_FOLDER 		= "/noora/uploads";// live	
if($THIS_SERVER_TYPE==$SERVER_TYPE_TEST) {
	$IMAGE_FOLDER 		= "/demo_noora/uploads"; 	//test
}

$DEFAULT_USER_IMAGE		= "defaultUser.png";
$IMAGE_UPLOAD_PATH = $IMAGE_FOLDER;
$PATH_IMAGE_FOLDER = $PATH_SERVER_URL.$IMAGE_FOLDER;

$CLASS_FOLDER 		= "/ClassImages/";
$CONTENT_FOLDER 	= "/Content_Attachments/";
$NURSE_FOLDER 		= "/NurseImage/";
$ADMIN_FOLDER 		= "/ProfileImages/";

$CLASS_IMAGE_PATH 		= $PATH_IMAGE_FOLDER.$CLASS_FOLDER;
$CONTENT_IMAGE_PATH 	= $PATH_IMAGE_FOLDER.$CONTENT_FOLDER;
$NURSE_IMAGE_PATH 		= $PATH_IMAGE_FOLDER.$NURSE_FOLDER;
$ADMIN_IMAGE_PATH 		= $PATH_IMAGE_FOLDER.$ADMIN_FOLDER;

$CLASS_PATH_TYPE 	= 1;
$CONTENT_PATH_TYPE 	= 2;
$NURSE_PATH_TYPE 	= 3;
$ADMIN_PATH_TYPE 	= 4;

//HISTORY
$HISTORY_TYPE_LOGIN 				= 1;
$HISTORY_TYPE_LOGOUT 				= 2;
$HISTORY_TYPE_CLASS_ADD 			= 3;
$HISTORY_TYPE_CONTENT_LIKE 			= 4;
$HISTORY_TYPE_CONTENT_COMMENT 		= 5;


$TAG_APP_KEY = "app_key";
$TAG_API_TYPE = "APITYPE"; // IF HAS ENCRYPTION OR NOT
$TAG_API_TOKEN = "TOKEN"; //SEND DEVICE ID

$APP_TOKEN = "j56sugRk029Po5DB";


// #######-------GLOBAL---CONSTANTS-----------------------###########

$MAX_ATTEMPT_ALLOWD = 3;
$OTP_LENGTH = 4;

$STATUS_DISABLED = 0;
$STATUS_ACTIVE = 1;
$STATUS_FAILURE = 2;
$STATUS_DELETED = 3;
$STATUS_VERIFIED = 4;
$STATUS_INACTIVE = 0;

$TAG_CODE 		= "code";
$TAG_SUCCESS 	= "success";
$TAG_MESSAGE 	= "message";
$TAG_DETAILS 	= "details";
$TAG_DATA 		= "data";
$TAG_HEADER 	= "header";
$TAG_TOKEN 		= "token";
$TAG_QUERY 		= "query";
$TAG_APPUSER_ID = "appuser_id";

//SETTINGS
$TAG_VERSION_CODE = "version_code";
$TAG_UPDATE_TYPE = "update_type";
$TAG_MASTER_OTP = "master_otp";

// #######--------- PARAM CONSTANTS ---------------------###########
$TAG_NURSE			= "nurse"; 	
$TAG_NURSE_PROFILE			= "nurse_profile"; 	
$TAG_USER			= "user";
$TAG_NURSE_ID		= "nurse_id"; 
$TAG_NURSE_SEARCH="search_term";	

$TAG_ID				= "id";
$TAG_NAME			= "name"; 	 
$TAG_CREATED_DATE	= "created_date";
$TAG_STATUS			= "status";
$TAG_UPDATED_DATE	= "updated_date";

$TAG_FIRST_NAME		= "first_name";
$TAG_LAST_NAME		= "last_name";
$TAG_CITY_ID		= "city_id";
$TAG_COUNTRY_CODE	= "country_code";
$TAG_MOBILE_NUMBER	= "mobile_number";
$TAG_PROFILE_IMAGE	= "profile_image";
$TAG_BADGE_LEVEL	= "badge_level";
$TAG_GRADUATING_YEAR	= "graduating_year";
$TAG_HOSPITAL_JOINING_DATE	= "hospital_joining_date";
$TAG_HOSPITAL_ID			= "hospital_id";
$TAG_HOSPITAL_NAME			= "hospital_name";
$TAG_HOSPITAL_CONDITION_ID	= "hospital_condition_id";
$TAG_DESIGNATION			= "designation";
$TAG_TOT_DATE				= "tot_date";
$TAG_CCP_MENTOR				= "ccp_mentor";
$TAG_CCP_CONDITION_ID = "ccp_condition_id";
$TAG_TRAINER                = "trainer";
$TAG_BOOSTER_TRAINING		= "booster_training";
$TAG_LOGIN_USER_ID			= "login_user_id";
$TAG_ENTRY_TIME				= "entry_time";
$TAG_EDIT_TIME				= "edit_time";

$TAG_TITLE			= "title";
$TAG_DESCRIPTION	= "description";
$TAG_GROUP_ID		= "group_id";
$TAG_CONTENT_TYPE	= "content_type";
$TAG_ATTACHMENT		= "attachment";
$TAG_ATTACHMENT_LIST	= "attachment_list";
$TAG_GROUP		= "group";

$TAG_USER_ID			= "user_id";
$TAG_DEVICE_ID			= "device_id";
$TAG_DEVICE_NAME		= "device_name";
$TAG_DEVICE_OS			= "device_os";
$TAG_DEVICE_OS_VERSION	= "device_os_version";
$TAG_APP_VERSION		= "app_version";
$TAG_APP_VERSION_NAME	= "app_version_name";
$TAG_GCM_TOKEN			= "gcm_token";
$TAG_LAST_SEEN			= "last_seen";
$TAG_ROLE			= "role";

$TAG_CONTENT_ID		= "content_id";
$TAG_COMMENT		= "comment";
$TAG_MENTIONED_USER_ID="mentioned_user_id";
$TAG_LIKE			= "like";
$TAG_VIEW			= "view";
$TAG_LIKE_COUNT		= "like_count";
$TAG_COMMENT_COUNT	= "comment_count";
$TAG_LAST_COMMENT	= "last_comment";
$TAG_LAST_LIKE	= "last_like";

$TAG_HOSPITAL			= "hospital";
$TAG_HOSPITAL_CONDITION	= "hospital_condition";
$TAG_CLASS_ID		= "class_id";
$TAG_CLASS_TYPE		= "class_type";
$TAG_CLASS_TYPE_ID	= "class_type_id";
$TAG_IMAGE			= "image";
$TAG_CLASS_DATE		= "class_date";
$TAG_CLASS_TIME		= "class_time";
$TAG_NO_OF_PEOPLE	= "no_of_people";
$TAG_NO_OF_FAMILY	= "no_of_family";
$TAG_OTP			= "otp";
$TAG_ATTEMPT		= "attempt";
$TAG_KEY_EXPIRY		= "key_expiry";
$TAG_DOB			= "dob";
$TAG_LOCATION		= "location";
$TAG_COUNTRY_ID		= "country_id";
$TAG_STATE_ID		= "state_id";
$TAG_DISTRICT_ID				= "district_id";
$TAG_COUNTRY_NAME	= "country_name";
$TAG_STATE_NAME		= "state_name";
$TAG_CITY_NAME		= "city_name";
$TAG_DISTRICT_NAME	= "district_name";
$TAG_SESSION_ID		= "session_id";
$TAG_WARD                = "ward";
$TAG_NOTES               = "notes";
$TAG_SESSION_CONDUCTED   = "session_conducted";

$TAG_PARTNER		= "partner";
$TAG_PARTNER_ID			= "partner_id";
$TAG_LANGUAGE_ID		= "language_id";
$TAG_LANGUAGE		= "language";
$TAG_TRAINING_URL			= "training_url";
$TAG_URL			= "url";
$TAG_TYPE_ID		= "type_id";
$TAG_TYPE		= "type";
$TAG_TOGGLE		= "toggle";

$TAG_CATEGORY_ID			= "category_id";
$TAG_AVAILABLE			= "available";

$TAG_MATERIAL_TYPE			= "material_type";
$TAG_MATERIAL_LANGUAGE			= "material_language";
$TAG_MATERIAL_CATEGORY			= "material_category";
$TAG_MATERIAL_DATA			= "material_data";

//----------------- TABLES ------------------------------------------------------

$TABLE_SUFFIX = "noora_";

//moora nurse table


//USER_APP_KEY TABLE  	
$USER_APP_KEY_TABLE			= $TABLE_SUFFIX."user_app_key"; //TABLE NAME
$USER_APP_KEY_ID			= "id";
$USER_APP_KEY_DEVICE_ID		= "device_id"; 	 
$USER_APP_KEY_APP_KEY		= "app_key";
$USER_APP_KEY_KEY_EXPIRY	= "key_expiry";
$USER_APP_KEY_CREATED_DATE	= "created_date";

//USER_DEVICE TABLE
$USER_DEVICE_TABLE			= $TABLE_SUFFIX."user_device"; //TABLE NAME
$USER_DEVICE_ID					= "id";
$USER_DEVICE_USER_ID			= "user_id";
$USER_DEVICE_DEVICE_ID			= "device_id";
$USER_DEVICE_DEVICE_NAME		= "device_name";
$USER_DEVICE_DEVICE_OS			= "device_os";
$USER_DEVICE_DEVICE_OS_VERSION	= "device_os_version";
$USER_DEVICE_APP_VERSION		= "app_version";
$USER_DEVICE_APP_VERSION_NAME	= "app_version_name";
$USER_DEVICE_GCM_TOKEN			= "gcm_token";
$USER_DEVICE_LAST_SEEN			= "last_seen";
$USER_DEVICE_CREATED_DATE		= "created_date";
$USER_DEVICE_STATUS 			= "status";
$USER_DEVICE_UPDATED_DATE		= "updated_date";

//APP_SETTING TABLE
$APP_SETTING_TABLE			= $TABLE_SUFFIX."app_setting"; //TABLE NAME
$APP_SETTING_ID				= "id";
$APP_SETTING_NAME			= "name";
$APP_SETTING_VALUE			= "value";
$APP_SETTING_ENTRY_TIME		= "entry_time";

//NURSE TABLE
$NURSE_TABLE					= $TABLE_SUFFIX."nurse"; //TABLE NAME
$NURSE_ID						= "ID";
$NURSE_FIRST_NAME				= "First_Name";
$NURSE_LAST_NAME				= "Last_Name";
$NURSE_DOB						= "DOB";
$NURSE_CITY_ID					= "City_ID";
$NURSE_DISTRICT_ID				= "District_ID";
$NURSE_COUNTRY_CODE				= "Country_Code";
$NURSE_MOBILE_NUMBER			= "Mobile_Number";
$NURSE_PROFILE_IMAGE			= "profile_image";
$NURSE_BADGE_LEVEL				= "Badge_Level";
$NURSE_GRADUATING_YEAR			= "Graduating_Year";
$NURSE_HOSPITAL_JOINING_DATE	= "Hospital_Joining_Date";
$NURSE_HOSPITAL_ID				= "Hospital_ID";
$NURSE_HOSPITAL_CONDITION_ID	= "Hospital_Condition_ID";
$NURSE_DESIGNATION				= "Designation";
$NURSE_TOT_DATE					= "TOT_Date";
$NURSE_CCP_MENTOR                = "CCP_Mentor";
$NURSE_CCP_CONDITION_ID           = "CCP_Condition_ID";
$NURSE_TRAINER                = "Trainer";
$NURSE_BOOSTER_TRAINING			= "Booster_Training";
$NURSE_LOGIN_USER_ID			= "Login_User_ID";
$NURSE_STATUS					= "Status";
$NURSE_ENTRY_TIME				= "Entry_Time";
$NURSE_EDIT_TIME				= "Edit_Time";

//CONTENT TABLE	
$CONTENT_TABLE			= $TABLE_SUFFIX."content"; //TABLE NAME
$CONTENT_ID				= "ID";
$CONTENT_TITLE			= "Title";
$CONTENT_DESCRIPTION	= "Description";
$CONTENT_ROLE			= "Role";
$CONTENT_CONTENT_TYPE	= "Content_Type";
$CONTENT_ATTACHMENT		= "Attachment";
$CONTENT_LOGIN_USER_ID	= "Login_User_ID";
$CONTENT_STATUS			= "Status";
$CONTENT_ENTRY_TIME		= "Entry_Time";
$CONTENT_EDIT_TIME		= "Edit_Time";

//GROUP TABLE	
$GROUP_TABLE			= $TABLE_SUFFIX."group"; //TABLE NAME
$GROUP_ID				= "ID";
$GROUP_GROUP_MEMBERS	= "Group_Members";
$GROUP_STATUS			= "Status";
$GROUP_ENTRY_TIME		= "Entry_Time";
$GROUP_EDIT_TIME		= "Edit_Time";

//GROUP_USER TABLE	
$GROUP_USER_TABLE			= $TABLE_SUFFIX."group_user"; //TABLE NAME
$GROUP_USER_ID				= "ID";
$GROUP_USER_GROUP_ID		= "Group_ID";
$GROUP_USER_USER_ID			= "User_ID";
$GROUP_USER_STATUS			= "Status";
$GROUP_USER_ENTRY_TIME		= "Entry_Time";
$GROUP_USER_EDIT_TIME		= "Edit_Time";

//CONTENT_GROUP TABLE	
$CONTENT_GROUP_TABLE			= $TABLE_SUFFIX."content_group"; //TABLE NAME
$CONTENT_GROUP_ID				= "ID";
$CONTENT_GROUP_CONTENT_ID		= "Content_ID";
$CONTENT_GROUP_GROUP_ID			= "Group_ID";
$CONTENT_GROUP_STATUS			= "Status";
$CONTENT_GROUP_ENTRY_TIME		= "Entry_Time";
$CONTENT_GROUP_EDIT_TIME		= "Edit_Time";

//ADMIN_USER TABLE	
$ADMIN_USER_TABLE			= $TABLE_SUFFIX."admin_user"; //TABLE NAME
$ADMIN_USER_ID				= "ID";
$ADMIN_USER_FIRST_NAME		= "First_Name";
$ADMIN_USER_LAST_NAME		= "Last_Name";
$ADMIN_USER_CITY_ID			= "City_ID";
$ADMIN_USER_COUNTRY_CODE	= "Country_Code";
$ADMIN_USER_MOBILE_NUMBER	= "Mobile_Number";
$ADMIN_USER_PROFILE_IMAGE	= "profile_image";
$ADMIN_USER_ROLE_ID			= "Role_ID";
$ADMIN_USER_EMAIL			= "Email";
$ADMIN_USER_PASSWORD		= "Password";
$ADMIN_USER_LAST_LOGIN		= "Last_Login";
$ADMIN_USER_LOGIN_USER_ID	= "Login_User_ID";
$ADMIN_USER_STATUS			= "Status";
$ADMIN_USER_ENTRY_TIME		= "Entry_Time";
$ADMIN_USER_EDIT_TIME		= "Edit_Time";

//CONTENT_LIKE TABLE	
$CONTENT_LIKE_TABLE			= $TABLE_SUFFIX."content_like"; //TABLE NAME
$CONTENT_LIKE_ID			= "ID";
$CONTENT_LIKE_CONTENT_ID	= "Content_ID";
$CONTENT_LIKE_LOGIN_USER_ID	= "Login_User_ID";
$CONTENT_LIKE_STATUS		= "Status";
$CONTENT_LIKE_ENTRY_TIME	= "Entry_Time";
$CONTENT_LIKE_EDIT_TIME		= "Edit_Time";

//CONTENT_COMMENT TABLE	
$CONTENT_COMMENT_TABLE			= $TABLE_SUFFIX."content_comment"; //TABLE NAME
$CONTENT_COMMENT_ID				= "ID";
$CONTENT_COMMENT_CONTENT_ID		= "Content_ID";
$CONTENT_COMMENT_COMMENT		= "Comment";
$CONTENT_COMMENT_LOGIN_USER_ID	= "Login_User_ID";
$CONTENT_COMMENT_STATUS			= "Status";
$CONTENT_COMMENT_ENTRY_TIME		= "Entry_Time";
$CONTENT_COMMENT_EDIT_TIME		= "Edit_Time";

//CCP_ATTENDANCE TABLE	
$CCP_ATTENDANCE_TABLE			    = $TABLE_SUFFIX."ccp_attendance"; //TABLE NAME
$CCP_ATTENDANCE_ID				    = "ID";
$CCP_ATTENDANCE_CLASS_TYPE_ID	    = "Class_Type_ID";
$CCP_ATTENDANCE_IMAGE			    = "Image";
$CCP_ATTENDANCE_CLASS_DATE		    = "Class_Date";
$CCP_ATTENDANCE_CLASS_TIME		    = "Class_Time";
$CCP_ATTENDANCE_NO_OF_PEOPLE	    = "No_of_People";
$CCP_ATTENDANCE_NO_OF_FAMILY	    = "No_of_Family";
$CCP_ATTENDANCE_LOGIN_USER_ID       = "Login_User_ID";
$CCP_ATTENDANCE_WARD                = "Ward";
$CCP_ATTENDANCE_NOTES               = "Notes";
$CCP_ATTENDANCE_SESSION_CONDUCTED   = "Session_Conducted";
$CCP_ATTENDANCE_STATE_ID				= "State_ID";
$CCP_ATTENDANCE_DISTRICT_ID			= "District_ID";
$CCP_ATTENDANCE_HOSPITAL_ID			= "Hospital_ID";
$CCP_ATTENDANCE_STATUS			    = "Status";
$CCP_ATTENDANCE_ENTRY_TIME		    = "Entry_Time";
$CCP_ATTENDANCE_EDIT_TIME		    = "Edit_Time";

//CCP_CLASS_TYPE TABLE	
$CCP_CLASS_TYPE_TABLE			= $TABLE_SUFFIX."ccp_class_type"; //TABLE NAME
$CCP_CLASS_TYPE_ID				= "ID";
$CCP_CLASS_TYPE_CLASS_TYPE		= "Class_Type";
$CCP_CLASS_TYPE_LOGIN_USER_ID	= "Login_User_ID";
$CCP_CLASS_TYPE_STATUS			= "Status";
$CCP_CLASS_TYPE_ENTRY_TIME		= "Entry_Time";
$CCP_CLASS_TYPE_EDIT_TIME		= "Edit_Time";

//HOSPITAL TABLE	
$HOSPITAL_TABLE			= $TABLE_SUFFIX."hospital"; //TABLE NAME
$HOSPITAL_ID				= "ID";
$HOSPITAL_NAME				= "Name";
$HOSPITAL_STATE_ID				= "State_ID";
$HOSPITAL_STATUS			= "Status";
$HOSPITAL_ENTRY_TIME		= "Entry_Time";
$HOSPITAL_EDIT_TIME			= "Edit_Time";

//HOSPITAL_PARTNER TABLE	
$HOSPITAL_PARTNER_TABLE			= $TABLE_SUFFIX."hospital_partner"; //TABLE NAME
$HOSPITAL_PARTNER_ID				= "ID";
$HOSPITAL_PARTNER_NAME				= "Name";
$HOSPITAL_PARTNER_STATUS			= "Status";
$HOSPITAL_PARTNER_ENTRY_TIME		= "Entry_Time";
$HOSPITAL_PARTNER_EDIT_TIME			= "Edit_Time";

//HOSPITAL_PARTNER_MAPPING TABLE	
$HOSPITAL_PARTNER_MAPPING_TABLE			= $TABLE_SUFFIX."hospital_partner_mapping"; //TABLE NAME
$HOSPITAL_PARTNER_MAPPING_ID				= "ID";
$HOSPITAL_PARTNER_MAPPING_HOSPITAL_ID			= "Hospital_ID";
$HOSPITAL_PARTNER_MAPPING_PARTNER_ID			= "Partner_ID";

//HOSPITAL_MEDICAL_CONDITION TABLE	
$HOSPITAL_MEDICAL_CONDITION_TABLE			= $TABLE_SUFFIX."hospital_medical_condition"; //TABLE NAME
$HOSPITAL_MEDICAL_CONDITION_ID				= "ID";
$HOSPITAL_MEDICAL_CONDITION_HOSPITAL_ID		= "Hospital_ID";
$HOSPITAL_MEDICAL_CONDITION_MODICAL_CONDITION	= "Medical_Condition";
$HOSPITAL_MEDICAL_CONDITION_STATUS			= "Status";
$HOSPITAL_MEDICAL_CONDITION_ENTRY_TIME		= "Entry_Time";
$HOSPITAL_MEDICAL_CONDITION_EDIT_TIME		= "Edit_Time";

//USER_OTP TABLE	
$USER_OTP_TABLE			= $TABLE_SUFFIX."user_otp"; //TABLE NAME
$USER_OTP_ID			= "ID";
$USER_OTP_USER_ID		= "User_ID";
$USER_OTP_OTP			= "Otp";
$USER_OTP_ATTEMPT		= "Attempt";
$USER_OTP_KEY_EXPIRY	= "Key_Expiry";
$USER_OTP_STATUS		= "Status";
$USER_OTP_ENTRY_TIME	= "Entry_Time";
$USER_OTP_EDIT_TIME		= "Edit_Time";


//USER_MOBILE_ATTEMPT TABLE	
$USER_MOBILE_ATTEMPT_TABLE				= $TABLE_SUFFIX."user_mobile_attempt"; //TABLE NAME
$USER_MOBILE_ATTEMPT_ID					= "ID";
$USER_MOBILE_ATTEMPT_MOBILE				= "mobile";
$USER_MOBILE_ATTEMPT_DEVICE_ID			= "device_id";
$USER_MOBILE_ATTEMPT_DEVICE_NAME		= "device_name";
$USER_MOBILE_ATTEMPT_DEVICE_OS			= "device_os";
$USER_MOBILE_ATTEMPT_DEVICE_OS_VERSION	= "device_os_version";
$USER_MOBILE_ATTEMPT_APP_VERSION		= "app_version";
$USER_MOBILE_ATTEMPT_APP_VERSION_NAME	= "app_version_name";
$USER_MOBILE_ATTEMPT_STATUS				= "Status";
$USER_MOBILE_ATTEMPT_ENTRY_TIME			= "Entry_Time";
$USER_MOBILE_ATTEMPT_EDIT_TIME			= "Edit_Time";

//CONTENT_ATTACHMENT TABLE	
$CONTENT_ATTACHMENT_TABLE		= $TABLE_SUFFIX."content_attachment"; //TABLE NAME
$CONTENT_ATTACHMENT_ID			= "ID";
$CONTENT_ATTACHMENT_CONTENT_ID	= "Content_ID";
$CONTENT_ATTACHMENT_ATTACHMENT	= "Attachment";
$CONTENT_ATTACHMENT_STATUS		= "Status";
$CONTENT_ATTACHMENT_ENTRY_TIME	= "Entry_Time";
$CONTENT_ATTACHMENT_EDIT_TIME	= "Edit_Time";

//CONTENT_VIEWS TABLE	
$CONTENT_VIEWS_TABLE			= $TABLE_SUFFIX."content_views"; //TABLE NAME
$CONTENT_VIEWS_ID				= "ID";
$CONTENT_VIEWS_CONTENT_ID		= "Content_ID";
$CONTENT_VIEWS_LOGIN_USER_ID	= "Login_User_ID";
$CONTENT_VIEWS_STATUS			= "Status";
$CONTENT_VIEWS_ENTRY_TIME		= "Entry_Time";
$CONTENT_VIEWS_EDIT_TIME		= "Edit_Time";

//NURSE_HISTORY TABLE	 	 	 	 
$NURSE_HISTORY_TABLE			= $TABLE_SUFFIX."nurse_history"; //TABLE NAME
$NURSE_HISTORY_ID				= "ID";
$NURSE_HISTORY_NURSEID			= "NurseID";
$NURSE_HISTORY_CONTENT_ID		= "Content_ID";
$NURSE_HISTORY_HISTORY_TYPE_ID	= "History_Type_Id";
$NURSE_HISTORY_ENTRY_TIME		= "Entry_Time";
$NURSE_HISTORY_EDIT_TIME		= "Edit_Time";
$NURSE_HISTORY_SESSION_ID		= "session_id";

//COUNTRY TABLE 	 
$COUNTRY_TABLE			= $TABLE_SUFFIX."country"; //TABLE NAME
$COUNTRY_ID				= "ID";
$COUNTRY_NAME			= "Name";
$COUNTRY_STATUS			= "Status";
$COUNTRY_ENTRY_TIME		= "Entry_Time";
$COUNTRY_EDIT_TIME		= "Edit_Time";

//STATE TABLE 	 
$STATE_TABLE		= $TABLE_SUFFIX."state"; //TABLE NAME
$STATE_ID			= "ID";
$STATE_COUNTRY_ID	= "Country_ID";
$STATE_NAME			= "Name";
$STATE_STATUS		= "Status";
$STATE_ENTRY_TIME	= "Entry_Time";
$STATE_EDIT_TIME	= "Edit_Time";

//CITY TABLE
$CITY_TABLE			= $TABLE_SUFFIX."city"; //TABLE NAME
$CITY_ID			= "ID";
$CITY_STATE_ID		= "State_ID";
$CITY_NAME			= "Name";
$CITY_STATUS		= "Status";
$CITY_ENTRY_TIME	= "Entry_Time";
$CITY_EDIT_TIME		= "Edit_Time";

//DISTRICT TABLE
$DISTRICT_TABLE			= $TABLE_SUFFIX."district"; //TABLE NAME
$DISTRICT_ID			= "ID";
$DISTRICT_STATE_ID		= "State_ID";
$DISTRICT_NAME			= "Name";
$DISTRICT_STATUS		= "Status";
$DISTRICT_ENTRY_TIME	= "Entry_Time";
$DISTRICT_EDIT_TIME		= "Edit_Time";

//ONLINE_TRAINING_LANGUAGE TABLE
$ONLINE_TRAINING_LANGUAGE_TABLE			= $TABLE_SUFFIX."online_training_language"; //TABLE NAME
$ONLINE_TRAINING_LANGUAGE_ID			= "ID";
$ONLINE_TRAINING_LANGUAGE_NAME			= "Name";
$ONLINE_TRAINING_LANGUAGE_STATUS		= "Status";
$ONLINE_TRAINING_LANGUAGE_ENTRY_TIME	= "Entry_Time";
$ONLINE_TRAINING_LANGUAGE_EDIT_TIME		= "Edit_Time";

//ONLINE_TRAINING_COURSES TABLE
$ONLINE_TRAINING_COURSES_TABLE			= $TABLE_SUFFIX."online_training_courses"; //TABLE NAME
$ONLINE_TRAINING_COURSES_ID			= "ID";
$ONLINE_TRAINING_COURSES_LANGUAGE_ID		= "Language_ID";
$ONLINE_TRAINING_COURSES_NAME			= "Name";
$ONLINE_TRAINING_COURSES_TRAINING_URL			= "Training_URL";
$ONLINE_TRAINING_COURSES_STATUS		= "Status";
$ONLINE_TRAINING_COURSES_ENTRY_TIME	= "Entry_Time";
$ONLINE_TRAINING_COURSES_EDIT_TIME		= "Edit_Time";

//CCP_LETS_PLAY TABLE
$CCP_LETS_PLAY_TABLE			= $TABLE_SUFFIX."ccp_lets_play"; //TABLE NAME
$CCP_LETS_PLAY_ID					= "ID";
$CCP_LETS_PLAY_NAME			= "Name";
$CCP_LETS_PLAY_URL			= "Play_URL";
$CCP_LETS_PLAY_STATUS		= "Status";
$CCP_LETS_PLAY_ENTRY_TIME	= "Entry_Time";
$CCP_LETS_PLAY_EDIT_TIME	= "Edit_Time";

//CCP_TOOL_TYPE TABLE
$CCP_TOOL_TYPE_TABLE			= $TABLE_SUFFIX."ccp_tool_type"; //TABLE NAME
$CCP_TOOL_TYPE_ID			= "ID";
$CCP_TOOL_TYPE_NAME			= "Name";
$CCP_TOOL_TYPE_TYPE			= "Type";
$CCP_TOOL_TYPE_URL			= "Material_URL";
$CCP_TOOL_TYPE_STATUS		= "Status";
$CCP_TOOL_TYPE_ENTRY_TIME	= "Entry_Time";
$CCP_TOOL_TYPE_EDIT_TIME		= "Edit_Time";

//CCP_TOOL_MATERIAL TABLE
$CCP_TOOL_MATERIAL_TABLE			= $TABLE_SUFFIX."ccp_tool_material"; //TABLE NAME
$CCP_TOOL_MATERIAL_ID						= "ID";
$CCP_TOOL_MATERIAL_TYPE_ID		= "Type_ID";
$CCP_TOOL_MATERIAL_NAME			= "Name";
$CCP_TOOL_MATERIAL_URL			= "Material_URL";
$CCP_TOOL_MATERIAL_TYPE		= "Type";
$CCP_TOOL_MATERIAL_STATUS		= "Status";
$CCP_TOOL_MATERIAL_ENTRY_TIME	= "Entry_Time";
$CCP_TOOL_MATERIAL_EDIT_TIME		= "Edit_Time";


//CCP_MATERIAL_TYPE TABLE
$CCP_MATERIAL_TYPE_TABLE			= $TABLE_SUFFIX."ccp_material_type"; //TABLE NAME
$CCP_MATERIAL_TYPE_ID						= "ID";
$CCP_MATERIAL_TYPE_NAME			= "Name";
$CCP_MATERIAL_TYPE_STATUS		= "Status";
$CCP_MATERIAL_TYPE_ENTRY_TIME	= "Entry_Time";
$CCP_MATERIAL_TYPE_EDIT_TIME		= "Edit_Time";


//CCP_MATERIAL_LANGUAGE TABLE
$CCP_MATERIAL_LANGUAGE_TABLE			= $TABLE_SUFFIX."ccp_material_language"; //TABLE NAME
$CCP_MATERIAL_LANGUAGE_ID					= "ID";
$CCP_MATERIAL_LANGUAGE_NAME			= "Name";
$CCP_MATERIAL_LANGUAGE_AVAILABLE			= "Available";
$CCP_MATERIAL_LANGUAGE_STATUS		= "Status";
$CCP_MATERIAL_LANGUAGE_ENTRY_TIME	= "Entry_Time";
$CCP_MATERIAL_LANGUAGE_EDIT_TIME		= "Edit_Time";


//CCP_MATERIAL_CATEGORY TABLE
$CCP_MATERIAL_CATEGORY_TABLE			= $TABLE_SUFFIX."ccp_material_category"; //TABLE NAME
$CCP_MATERIAL_CATEGORY_ID						= "ID";
$CCP_MATERIAL_CATEGORY_NAME			= "Name";
$CCP_MATERIAL_CATEGORY_AVAILABLE			= "Available";
$CCP_MATERIAL_CATEGORY_STATUS		= "Status";
$CCP_MATERIAL_CATEGORY_ENTRY_TIME	= "Entry_Time";
$CCP_MATERIAL_CATEGORY_EDIT_TIME		= "Edit_Time";


//CCP_MATERIAL_DATA TABLE
$CCP_MATERIAL_DATA_TABLE			= $TABLE_SUFFIX."ccp_material_data"; //TABLE NAME
$CCP_MATERIAL_DATA_ID						= "ID";
$CCP_MATERIAL_DATA_TYPE_ID			= "Type_ID";
$CCP_MATERIAL_DATA_LANGUAGE_ID			= "Language_ID";
$CCP_MATERIAL_DATA_CATEGORY_ID			= "Category_ID";
$CCP_MATERIAL_DATA_MATERIAL_URL			= "Material_URL";
$CCP_MATERIAL_DATA_AVAILABLE			= "Available";
$CCP_MATERIAL_DATA_STATUS		= "Status";
$CCP_MATERIAL_DATA_ENTRY_TIME	= "Entry_Time";
$CCP_MATERIAL_DATA_EDIT_TIME		= "Edit_Time";

//----------------------------------------------------------------------------------------




?>	
	
