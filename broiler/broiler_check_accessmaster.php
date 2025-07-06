<?php
//broiler_check_accessmaster.php
if(!isset($_SESSION)){ session_start(); }
$access_database = $_SESSION['dbase'];
$access_usrcode = $_SESSION['userid'];

global $access_error_flag;
global $access_error_msg;
$access_error_flag = 0; $access_error_msg = "";

if($access_database != ""){
    $acs_conn = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB",$access_database);
    if($acs_conn){
        //Fetching File Type and assigning link tale column names
        $acslink_colname = $acslink_type = $acs_cid = ""; $link1 = array();
        $link1 = explode("_", $ufile_name); $acslink_type = $link1[1];
        if(strtolower($acslink_type) == "display"){ $acslink_colname = "displayaccess"; }
        else if(strtolower($acslink_type) == "add"){ $acslink_colname = "addaccess"; }
        else if(strtolower($acslink_type) == "edit"){ $acslink_colname = "editaccess"; }
        else if(strtolower($acslink_type) == "delete"){ $acslink_colname = "deleteaccess"; }
        else if(strtolower($acslink_type) == "update"){ $acslink_colname = "otheraccess"; }
        else if(strtolower($acslink_type) == "print"){ $acslink_colname = "printaccess"; }
        else{ }

        //Fetch Link Details
        if($acslink_colname != ""){
            $acs_sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '%$ufile_name%' AND `active` = '1'";
            $acs_query = mysqli_query($acs_conn,$acs_sql); $acs_cnt = mysqli_num_rows($acs_query);
            if($acs_cnt > 0){
                while($acs_row = mysqli_fetch_array($acs_query)){ $acs_cid = $acs_row['childid']; }
                if($acs_cid != ""){
                    //Fetch Access Details
                    $acs_sql = "SELECT * FROM `main_access` WHERE `$acslink_colname` LIKE '%$acs_cid%' AND `empcode` = '$access_usrcode' AND `active` = '1'";
                    $acs_query = mysqli_query($acs_conn,$acs_sql); $acs_cnt = mysqli_num_rows($acs_query);
                    if($acs_cnt > 0){
                        //File Access Types
                        $file_access_type = "normal"; /*otp,password*/
                    }
                    else{ $access_error_flag = 1; $access_error_msg = "Error: Access not available."; }
                }
                else{ $access_error_flag = 1; $access_error_msg = "Error: File Child-Id Not available."; }
            }
            else{ $access_error_flag = 1; $access_error_msg = "Error: File link not available."; }
        }
        else{ $access_error_flag = 1; $access_error_msg = "Error: Unable to fetch link access type."; }
    }
    else{ $access_error_flag = 1; $access_error_msg = "Error: connecting Database."; }
}
else{ $access_error_flag = 1; $access_error_msg = "Error: Internal Server Error"; }
if(isset($acs_conn)){ mysqli_close($acs_conn); }
