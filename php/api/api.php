<?php
//STRICT_TRANS_TABLES
//DEBUG reporting
// error_reporting(0);
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('vah_class.php');
require_once('db_class.php');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token,Deviceid,Devicetype,Authorization');

function api_request($request, $api_id, $api_secret, $data, $files) {

    global $VAH, $DB, $mailer_css, $isr;


    /* ---------------------------------------------------------------------
      Validate API user credentials
      ---------------------------------------------------------------------- */

    if($request == "login" || $request == "sign_up" || $request == "forgot_password" || $request == "verify_code") {
      $res = $DB->select("SELECT * FROM tblapi_mst WHERE api_id='$api_id' AND api_secret='$api_secret'");
      if (!$res || count($res) <= 0) {
          return $VAH->error_api();
      }
    }  
    //extract($data);


    if($data != '' ){
        
      extract($data);

    }

    switch ($request) {

      // a415409bafe2d626e27145901c6fbf41

        //----------------------- GENERAL SERVICES -------------------------------
        
        case "sign_up":

        
          if (!isset($data['first_name']) || $data['first_name'] == '') {
            return $VAH->error_invalid('Firstname');
          }
          if (!isset($data['last_name']) || $data['last_name'] == '') {
            return $VAH->error_invalid('Lastname');
          }
          if (!isset($data['email_id']) || $data['email_id'] == '') {
            return $VAH->error_invalid('Email id');
          }
          if (!isset($data['password']) || $data['password'] == '') {
            return $VAH->error_invalid('Password');
          }         
          if (!isset($data['secret']) || $data['secret'] == '') {
            return $VAH->error_invalid('Secret');
          }
          if (!isset($data['time_zone']) || $data['time_zone'] == '') {
            //return $VAH->error_invalid('time_zone');
          }
           
          //--------------- AUTHENTICAT SECRET --------------------//

          $generated_secret= secret_generator($email_id,$password);
          if($generated_secret != $secret){
            return $VAH->error_denied("Invalid secret.");
          }

          //--------------- CHECK EMAIL ID AVAILABILITY --------------------//
        
          $dataQ = $DB->SELECT("SELECT * 
                              FROM  tblusers 
                              WHERE email_id = '".$email_id."'");
        
          if(empty($dataQ))
          {
            //--------------- CHECK EMAIL ID VALIDATION --------------------//

            if(preg_match('/\s/',$email_id) > 0){
              return $VAH->error_denied('Invalid Email');
            }

            $photo = "";
            if(!empty($_FILES)){
            
              $key = 'profile_pic';
              if (file_exists($_FILES[$key]['tmp_name']) || is_uploaded_file($_FILES[$key]['tmp_name'])) {
                if (!file_exists('../uploads')) {
                  mkdir('../uploads', 0777, true);
                }
                if (!file_exists('../uploads/user_profile')) {
                  mkdir('../uploads/user_profile', 0777, true);
                }
                $photo =  "uploads/user_profile/".strtotime(date("Y-m-d H:i:s"))."".uniqid() ."_userpic1.png";
                if (file_exists("../$photo")) {
                        unlink("../$photo");
                }

                move_uploaded_file($_FILES[$key]['tmp_name'], "../$photo");
                
              }
            }    

            //--------------- ADD USER --------------------//
         
            $AddUser = $DB->INSERT("INSERT INTO tblusers
                                    SET  first_name = '".$first_name."',
                                        last_name = '".$last_name."',
                                        email_id = '".$email_id."',
                                        password = '".$password."',
                                        profile_pic = '".$photo."',
                                        time_zone = '".@$time_zone."',
                                        created_at = '".date("Y-m-d H:i:s")."',
                                        updated_at = '".date("Y-m-d H:i:s")."'");

            //--------------- USER NOT CREATED --------------------//

            if(!isset($AddUser))
            {
                return $VAH->error_denied('Try After Some Time later.');
            }

            //--------------- API SUCCESS RESPONSE --------------------//

            $data=$DB->SELECT("SELECT * 
                               FROM  tblusers 
                               WHERE  email_id ='". $email_id ."' 
                                      AND user_id = $AddUser");

            //--------------- GENARE ACCESS TOKEN FOR API SESSION ---------------//

            $access_token = access_token();
            // $access_session = date("Y-m-d H:i:s", strtotime("+7 day"));
            // $access_session = strtotime($access_session);
            // $headers = apache_request_headers();
           
            // $headers['Devicetype'] = $headers['Devicetype'];
            // $headers['Deviceid'] = $headers['Deviceid'];
            // if(isset($headers['Devicetype']) && isset($headers['Deviceid'])){
             
            //   $get_api_session = $DB->select("SELECT * 
            //                                   FROM tblapi_session
            //                                   WHERE user_id = '".$data[0]['user_id']."' 
            //                                         AND device_type = '".$headers['Devicetype']."'
            //                                         AND device_id = '".$headers['Deviceid']."' ");
              
            //   if(count($get_api_session) > 0){

            //     $updateQ=$DB->UPDATE("UPDATE tblapi_session
            //                           SET  access_token ='".$access_token."',
            //                                access_session ='".$access_session."',
            //                                updated_at ='".date("Y-m-d H:i:s")."'
            //                                WHERE  user_id = '".$data[0]['user_id']."'
            //                                   AND device_type = '".$headers['Devicetype']."'
            //                                   AND device_id = '".$headers['Deviceid']."'");
                                        
                                              
            //   }else{
                
            //     $inserQ=$DB->INSERT("INSERT INTO tblapi_session
            //                          SET  access_token ='".$access_token."',
            //                               access_session ='".$access_session."',
            //                               created_at ='".date("Y-m-d H:i:s")."',
            //                               updated_at='".date("Y-m-d H:i:s")."',
            //                               user_id= '".$data[0]['user_id']."',
            //                               device_type = '".$headers['Devicetype']."',
            //                               device_id = '".$headers['Deviceid']."'");
                                          
                                       
            //   }

            // }else{
            //   return $VAH->error_denied("Invalid device_type or device_id.");
            // }    
            $resArr = array();
            $resArr['user_id'] = $data[0]['user_id'];
            $resArr['first_name'] = $data[0]['first_name'];
            $resArr['last_name'] = $data[0]['last_name'];
            $resArr['email_id'] = (isset($data[0]['email_id']) && $data[0]['email_id'] != "") ? $data[0]['email_id'] : "";                     
            $resArr['profile_pic'] = (isset($data[0]['profile_pic']) && $data[0]['profile_pic'] != "") ? SITE_URL_REMOTE .'/'. $data[0]['profile_pic'] : "";
            $resArr['status']=$data[0]['status']; 
            $resArr['access_token']= (isset($access_token) && $access_token !="")?$access_token : "";              
            $resArr['created_at']=$data[0]['created_at'];
            $resArr['updated_at']=$data[0]['updated_at'];

            return $VAH->api_success($resArr, "Thank you for signing up.");

          }else{
            return $VAH->error_denied('Email address already in use. Please try another email.');
          }
      
        break;


        case 'login':
          
          if (!isset($data['email_id']) || $data['email_id'] == '') {
              return $VAH->error_invalid('email_id');
          }
          if (!isset($data['password']) || $data['password'] == '') {
              return $VAH->error_invalid('password');
          }
          if (!isset($data['secret']) || $data['secret'] == '') {
              return $VAH->error_invalid('secret');
          }

          //--------------- AUTHENTICAT SECRET ---------------//

          $generated_secret= secret_generator($email_id,$password);
          // echo $generated_secret;die;
          if($generated_secret != $secret){
            return $VAH->error_denied("Invalid secret.");
          }

          //--------------- AUTHENTICAT USER ---------------//
              
          $data = $DB->select("SELECT * 
                               FROM tblusers
                               WHERE email_id = '" . $email_id . "'  
                                     AND password = '".$password. "' 
                                     AND status=1");

           
        
          if (count($data) == 0) {
              return $VAH->error_denied("Invalid username or password.");
          } 

          //--------------- GENARE ACCESS TOKEN FOR API SESSION ---------------//

          $access_token = access_token();
          $access_session = date("Y-m-d H:i:s", strtotime("+365 day"));
          $access_session = strtotime($access_session);
          $headers = apache_request_headers();
          $headers['Devicetype'] = $headers['Devicetype'];
          $headers['Deviceid'] = $headers['Deviceid'];
          if(isset($headers['Devicetype']) && isset($headers['Deviceid'])){
           
            $get_api_session = $DB->select("SELECT * 
                                            FROM tblapi_session
                                            WHERE user_id = '".$data[0]['user_id']."' 
                                                  AND device_type = '".$headers['Devicetype']."'
                                                  AND device_id = '".$headers['Deviceid']."' ");


            
            if(count($get_api_session) > 0){

          
              $updateQ=$DB->UPDATE("UPDATE tblapi_session
                                    SET  access_token ='".$access_token."',
                                         access_session ='".$access_session."',
                                         updated_at ='".date("Y-m-d H:i:s")."'
                                    WHERE  user_id = '".$data[0]['user_id']."'
                                            AND device_type = '".$headers['Devicetype']."'
                                            AND device_id = '".$headers['Deviceid']."'");
            }else{
              
              $inserQ=$DB->INSERT("INSERT INTO tblapi_session
                                   SET  access_token ='".$access_token."',
                                        access_session ='".$access_session."',
                                        created_at ='".date("Y-m-d H:i:s")."',
                                        updated_at='".date("Y-m-d H:i:s")."',
                                        user_id= '".$data[0]['user_id']."',
                                        device_type = '".$headers['Devicetype']."',
                                        device_id = '".$headers['Deviceid']."'");
            }

          }else{
            return $VAH->error_denied("Invalid device_type or device_id.");
          }    

          //--------------- API SUCCESS RESPONSE ---------------//

          $resArr = array();
          $resArr['user_id'] = $data[0]['user_id'];
          $resArr['first_name'] = $data[0]['first_name'];
          $resArr['last_name'] = $data[0]['last_name'];
          $resArr['email_id'] = (isset($data[0]['email_id']) && $data[0]['email_id'] != "") ? $data[0]['email_id'] : "";                     
          // $resArr['date_of_birth'] = (isset($data[0]['date_of_birth']) && $data[0]['date_of_birth'] != "") ? $data[0]['date_of_birth'] : ""; 
          // $resArr['profile_pic'] = (isset($data[0]['profile_pic']) && $data[0]['profile_pic'] != "") ? SITE_URL_REMOTE .'/'. $data[0]['profile_pic'] : "";
          $resArr['status']=$data[0]['status'];
          // $resArr['gender']=$data[0]['gender'];
          // $resArr['profile_privacy'] = (isset($data[0]['profile_privacy']) && $data[0]['profile_privacy'] != "") ? $data[0]['profile_privacy'] : "";  
          // $resArr['post_view']=$data[0]['post_view'];    
          // $resArr['post_distance']=$data[0]['post_distance'];    
          // $resArr['interested_tag']=$data[0]['interested_tag'];                    
          $resArr['created_at']=$data[0]['created_at'];
          $resArr['updated_at']=$data[0]['updated_at'];
          $resArr['access_token']=$access_token;

          return $VAH->api_success($resArr,'Login Successfully');

        break;
       
       
        case 'change_password':

          if (!isset($data['email_id']) || $data['email_id'] == '') {
            return $VAH->error_invalid('email_id');
          }

          if (!isset($data['password']) || $data['password'] == '') {
            return $VAH->error_invalid('password');
          }

          //--------------- UPDATE PASSWORD --------------------//
            
          $updateQ = $DB->UPDATE("UPDATE tblusers
                                  SET  password = '".$password."'
                                  WHERE  email_id = '". $email_id ."'");
          
          $resArr = array();
          return $VAH->api_success($resArr, 'Password updated now.');

        break;


        //----------------------- GENERAL SERVICES END -------------------------------

        //----------------------- USER PROFILE & SETTINGS SERVICES --------------------

       

        case 'logout_user':

          if (!isset($data['user_id']) || $data['user_id'] == '') {
              return $VAH->error_invalid('user_id');
          }
          if (!isset($data['device_type']) || $data['device_type'] == '') {
              return $VAH->error_invalid('device_type');
          }

          $user = getUserData($user_id);
          if (count($user) == 0) {
              return $VAH->error_denied('User not available.');
          }

          $resVal = $DB->select("SELECT * 
                                 FROM tblpush_user 
                                 WHERE user_id='".$user_id."' 
                                     AND device_type = '".$device_type."' 
                                     AND status=1");

          if (count($resVal) > 0) {
            $DB->delete("DELETE 
                         FROM tblpush_user 
                         WHERE push_user_id = '".$resVal[0]['push_user_id']."'");
          }
            $DB->DELETE("DELETE FROM tblpush_user WHERE  user_id='".$user_id."' 
                                     AND device_token = '".$device_token."' 
                                     AND status=1");
          $resArr = array();
          return $VAH->api_success($resArr,'Logout Successfully.');

        break;

        //----------------------- USER PROFILE & SETTINGS SERVICES END -------------------------------

        //----------------------- NOTIFICATION -------------------------------

        case 'register_for_push':
           
          if (!isset($data['user_id']) && !isset($data['user_id'])) {
              return $VAH->error_invalid('user_id');
          }
          if (!isset($data['device_token']) || $data['device_token'] == '') {
              return $VAH->error_invalid('device_token');
          }
          if (!isset($data['certificate_type']) || $data['certificate_type'] == '') {
              return $VAH->error_invalid('certificate_type');
          }

          $user = getUserData($user_id);
          if (count($user) == 0) {
              return $VAH->error_denied('User not available.');
          }

          $headers = apache_request_headers();
          $headers['Devicetype'] = $headers['Devicetype'];
          $headers['Deviceid'] = $headers['Deviceid'];
          $pushData = $DB->select("SELECT * 
                                   FROM tblpush_user 
                                   WHERE user_id='".$user_id."' 
                                         AND device_type = '".$headers['Devicetype']."' 
                                         AND status=1");

          if (count($pushData) > 0) {
            $DB->delete("DELETE 
                         FROM tblpush_user 
                         WHERE push_user_id = '" . $pushData[0]['push_user_id'] . "'");
          }

          $insertPush = $DB->insert("INSERT INTO tblpush_user
                                     SET user_id = '".$user_id."',
                                         device_id = '" . $headers['Deviceid'] . "',
                                         device_token = '" . $device_token . "',
                                         device_type = '" . $headers['Devicetype'] . "',
                                         certificate_type = '" . $certificate_type . "',
                                         status = '1',
                                         created_at = '" . date("Y-m-d H:i:s") . "',
                                         updated_at = '" . date("Y-m-d H:i:s") . "'");
          
          $resArr = array();
          return $VAH->api_success($resArr,'Register For Push Successfully.');

        break; 
      
      //----------------------- GET CHAT OF PERTICULAR USER -------------------------------//

        case 'get_user':
           
          if (!isset($data['user_id']) && !isset($data['user_id'])) {
              return $VAH->error_invalid('user_id');
          }
         

          $user = getUserData($user_id);
          if (count($user) == 0) {
              return $VAH->error_denied('User not available.');
          }

         
          $getData = $DB->select("SELECT * 
                                   FROM tblusers 
                                   WHERE status=1");

          
          $resArr = array();
          $i='0';
          foreach ($getData as  $value) {

            $resArr[$i]['user_id'] = (isset($value['user_id']) && $value['user_id'] != "") ? $value['user_id'] : "";          
            $resArr[$i]['first_name'] =(isset($value['first_name']) && $value['first_name'] != "") ? $value['first_name'] : ""; 
            $resArr[$i]['last_name'] =(isset($value['last_name']) && $value['last_name'] != "") ? $value['last_name'] : "";
            $resArr[$i]['email_id'] = (isset($value['email_id']) && $value['email_id'] != "") ? $value['email_id'] : "";
            $resArr[$i]['created_at'] = (isset($value['created_at']) && $value['created_at'] != "") ? $value['created_at'] : "";//

           
            $i++;
          }
          return $VAH->api_success($resArr,'Get users Successfully.');

        break; 
     
        //--------------- BAD API REQUEST ---------------//          
        
        default:
            return $VAH->api_error('BAD_REQUEST', 'Bad API request.');

        //--------------- BAD API REQUEST END ---------------//     
    }
}

//--------------- GET API REQUEST ---------------//     

$myData=$_POST;

// $myData=$_POST;
// $myData=$_POST['send_data'];
// $myData=json_decode($myData,true);


if (isset($myData['api_request']) && $myData['api_request'] != '') {


    global $VAH, $DB;

    $data = NULL;
    if (isset($myData['data'])) {
        $data = $myData['data'];
    }
    if (!is_array($data)) {
        $data = json_decode($data, true);
    }
    if (isset($data['language'])) {
        $data['language'] = "ES";
    }

    //--------------- GET REQUEST HEADER ---------------//  

    $headers = apache_request_headers();

    //--------------- EXCLUDE SERVICE FROM CHECKING API SESSION  ---------------//  

    if($myData['api_request'] != "login" && 
       $myData['api_request'] != "sign_up" &&
       $myData['api_request'] != "forgot_password" &&
       $myData['api_request'] != "change_password" &&
       $myData['api_request'] != "logout"){

      //--------------- CHECK AVALABILITY OF AUTHORIZATION & DEVICE TYPE & DEVICE ID ---------------//  

      if(isset($headers['Authorization']) && $headers['Authorization'] !=""  && isset($headers['Devicetype']) && $headers['Devicetype'] !="" && isset($headers['Deviceid']) && $headers['Deviceid'] !=""){

        //--------------- GET EXPLODE AUTHORIZATION ---------------//  

        $access_header = explode(' ',$headers['Authorization']);

        //--------------- GET ACCESS KEY ---------------//
        
        if(!isset($access_header[0])){
          $retval =  $VAH->error_invalid('access key');
          die(json_encode($retval));
        }
        $access_key =$access_header[0];

        //--------------- GET ACCESS TOKEN ---------------//
        
        if(!isset($access_header[1])){
          $retval =  $VAH->error_invalid('access token');
          die(json_encode($retval));  
        }
        $access_token =$access_header[1];

        //--------------- GET DEVICE TYPE & DEVICE ID ---------------//

        $device_type =$headers['Devicetype'];
        $device_id =$headers['Deviceid'];

        //--------------- VALIDET ACCESS KEY ---------------//

        if($access_key !="tiger"){
          $retval =  $VAH->error_invalid('access key');
          die(json_encode($retval));
        }

        //--------------- CHECK API SESSION ---------------//

        $retval = authenticat_api_call($data['user_id'],$access_token,$device_type,$device_id);

        if(count($retval) == 0){
           $retval =  $VAH->error_denied('User session expire.Please login again.');
           die(json_encode($retval));
        }

      }else{
        $retval =  $VAH->error_api();
        die(json_encode($retval));
      }  
      
    }

    //--------------- API CALL ---------------//

    $retval = api_request($myData['api_request'], @$myData['api_id'], @$myData['api_secret'], $data, $_FILES);

    //--------------- API RESPONSE ---------------//

    die(json_encode($retval));
    
}else{

  echo "else";exit;
}

//--------------- GENERAT SECRET ---------------//

function secret_generator($email_id,$password){
  $email_id = strrev($email_id);
  $password = strrev($password);

  return md5($email_id.$password);
}

//--------------- CHECK API SESSION ---------------//

function authenticat_api_call($user_id,$access_token,$device_type,$device_id) {
    global $VAH, $DB;
    $access_session = strtotime(date('Y-m-d H:i:s'));
    $userData = $DB->select("SELECT * 
                             FROM tblapi_session 
                             WHERE user_id = '".$user_id ."'
                                   AND access_token = '".$access_token."'
                                   AND device_type = '".$device_type."'
                                   AND device_id = '".$device_id."'
                                   AND access_session >= '".$access_session."'");

    return $userData;
}

//--------------- GENERAT ACCESS TOKEN ---------------//

function access_token($length = 50) {
    $characters = '@0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

//--------------- CHECK USER AVAILBILITY ---------------//

function getUserData($user_id) {
    global $VAH, $DB;
    $userData = $DB->select("SELECT * 
                             FROM tblusers WHERE user_id = '".$user_id."' 
                                  AND status=1");
    return $userData;
}

function array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;

}

?>

