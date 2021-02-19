<?php

if (isset($_POST["regId"]) && isset($_POST["message"])) {
    $regId = $_POST["regId"];
    $message = $_POST["message"];
    
    include_once 'GCM.php';
    
    $gcm = new GCM();
	
    $registration_ids = array($regId);
    $message = array("message" => $message);
	
	var_dump($registration_ids);
	var_dump( $message);
	
    $result = $gcm->send_notification($registration_ids, $message);
	
	echo "Result Next : ";
    echo $result;
}

?>

<html>
<body>
  <form action="<?php $_PHP_SELF ?>" method="POST">
  regId: <input type="text" name="regId" />
   message: <input type="text" name="message" />
  <input type="submit" />
  </form>
</body>
</html>