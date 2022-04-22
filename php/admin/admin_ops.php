<?php
ob_start();

header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

include_once '../api/db_connection.php';
require_once('../api/db_class.php');
global $DB,$conn;

$post_data = file_get_contents("php://input");


$data = json_decode($post_data);
extract($data);
$action=$data->action;



if(isset($action) && $action == "login_admin") {

	$email=$data->email;
	$password=$data->password;

	$data = $DB->select("SELECT * FROM tblmaster_admin WHERE status=1 AND email_id='".$email."' AND password='".md5($password)."'");

	$resArr = array();
	if (count($data) > 0) {
	   session_start();

		$resArr['angular_admin_user_id'] = (isset($data[0]['admin_user_id']) && $data[0]['admin_user_id'] != '') ? $data[0]['admin_user_id'] : '';
	    $resArr['angular_admin_email_id'] = (isset($data[0]['email_id']) && $data[0]['email_id'] != '') ? $data[0]['email_id'] : '';
	    $resArr['angular_admin_user_type'] = (isset($data[0]['user_type']) && $data[0]['user_type'] != '') ? $data[0]['user_type'] : '';
	    $resArr['flag'] = 'true';

		echo json_encode($resArr);
		
	} else {
		$resArr['flag'] = 'false';

	    echo json_encode($resArr);
	}

}elseif (isset($action) && $action == "get_user") {

	$data = $DB->select("SELECT * FROM tblusers WHERE status=1");
	$resArr = array();
	$i='0';
	if (count($data) > 0) {

		foreach ($data as $value) {

			$resArr[$i]['user_id'] = $value['user_id'];
			$resArr[$i]['first_name'] = $value['first_name'];
			$resArr[$i]['last_name'] = $value['last_name'];
			$resArr[$i]['email_id'] = (isset($value['email_id']) && $value['email_id'] != "") ? $value['email_id'] : ""; 
			$resArr[$i]['phone'] = $value['phone'];
			$resArr[$i]['country'] = (isset($value['country']) && $value['country'] != "") ? $value['country'] : "";
			$resArr[$i]['time_zone'] = (isset($value['time_zone']) && $value['time_zone'] != "") ? $value['time_zone'] : "";                      
			$resArr[$i]['created_at']=$value['created_at'];
			$resArr[$i]['modified_at']=$value['modified_at'];
			$resArr[$i]['flag'] = 'true';

			$i++;
		}
	   
		echo json_encode($resArr);
	} else {
		$resArr['flag'] = 'false';

	    echo json_encode($resArr);
	}

}elseif (isset($action) && $action == "addEdit_user") {

	$first_name=$data->first_name;
	$last_name=$data->last_name;
	$email_id=$data->email_id;
	$phone=$data->phone;
	$user_id=$data->user_id;

	if($user_id=='0'){

		$addUser =$DB->insert("INSERT INTO `tblusers` 
	                           SET `first_name` = '" . $first_name . "',
	                            `last_name` = '".$last_name."',
	                            `email_id` = '" . $email_id . "',
	                            `phone` = '" . $phone . "',
	                            `status` = '1',
	                            `created_at` = '" . date("Y-m-d H:i:s") . "',
	                            `updated_at` = '" . date("Y-m-d H:i:s") . "'");
	}else{

		$editUser=mysqli_query($conn,"UPDATE tblusers 
	                                 SET first_name='".$first_name ."',
	                                     last_name='".$last_name."',
	                                     email_id='".$email_id."',
	                                     phone='".$phone."',
	                                     updated_at='". date("Y-m-d H:i:s") ."' 
	                                     WHERE user_id = '".$user_id."' ");
	   
	}




	echo "true";

}elseif (isset($action) && $action == "edit_user") {


}elseif (isset($action) && $action == "delete_user") {

	$user_id=$data->user_id;

	$deleteQ=mysqli_query($conn," DELETE  FROM tblusers WHERE user_id='". $user_id ."'");

	echo "true";

}elseif (isset($action) && $action == "get_user_byId") {

	$user_id=$data->user_id;

	$data = $DB->select("SELECT * FROM tblusers WHERE status=1 AND user_id='".$user_id."'");

	$resArr = array();
	if (count($data) > 0) {
	   
		$resArr[0]['user_id'] = $data[0]['user_id'];
		$resArr[0]['first_name'] = $data[0]['first_name'];
		$resArr[0]['last_name'] = $data[0]['last_name'];
		$resArr[0]['email_id'] = (isset($data[0]['email_id']) && $data[0]['email_id'] != "") ? $data[0]['email_id'] : ""; 
		$resArr[0]['phone'] = $data[0]['phone'];
		$resArr[0]['country'] = (isset($data[0]['country']) && $data[0]['country'] != "") ? $data[0]['country'] : "";
		$resArr[0]['time_zone'] = (isset($data[0]['time_zone']) && $data[0]['time_zone'] != "") ? $data[0]['time_zone'] : "";                      
		$resArr[0]['created_at']=$data[0]['created_at'];
		$resArr[0]['modified_at']=$data[0]['modified_at'];
		$resArr[0]['flag'] = 'true';

		echo json_encode($resArr);
	} else {
		$resArr['flag'] = 'false';

	    echo json_encode($resArr);
	}

} elseif (isset($action) && $action == "edit_group") {


}


?>
