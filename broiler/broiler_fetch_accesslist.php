<?php
//broiler_fetch_accesslist.php
if(!isset($_SESSION)){ session_start(); }
$access_database = $_SESSION['dbase'];
$access_usrcode = $_SESSION['userid'];

global $acs_add_flag; global $acs_add_url;
global $acs_edit_flag; global $acs_edit_url;
global $acs_delete_flag; global $acs_delete_url;
global $acs_update_flag; global $acs_update_url;
global $acs_print_flag; global $acs_print_url;
global $acslist_error_flag;
global $access_error_msg;

$acs_add_flag = $acs_edit_flag = $acs_delete_flag = $acs_update_flag = $acs_print_flag = $acslist_error_flag = 0;
$acs_add_url = $acs_edit_url = $acs_delete_url = $acs_update_url = $acs_print_url = $access_error_msg = "";

if($access_database != ""){
    $acs_conn = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB",$access_database);
    if($acs_conn){
        //Fetching File Type and assigning link tale column names
        $acslink_colname = $acslink_type = $acs_cid = ""; $link1 = array();
        $link1 = explode("_", $ufile_name); $acslink_type = $link1[1];
        if(strtolower($acslink_type) == "display"){
            $acs_sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '%$ufile_name%' AND `active` = '1'";
            $acs_query = mysqli_query($acs_conn,$acs_sql); $acs_cnt = mysqli_num_rows($acs_query);
            if($acs_cnt > 0){
                while($acs_row = mysqli_fetch_array($acs_query)){ $acs_cid = $acs_row['childid']; }
                if($acs_cid != ""){
                    $acs_sql = "SELECT * FROM `main_access` WHERE `empcode` = '$access_usrcode' AND `active` = '1'";
                    $acs_query = mysqli_query($acs_conn,$acs_sql); $dlink = $alink = $elink = $ulink = "";
                    while($acs_row = mysqli_fetch_array(result: $acs_query)){
                        $dlink = $acs_row['displayaccess']; $alink = $acs_row['addaccess']; $elink = $acs_row['editaccess']; $rlink = $acs_row['deleteaccess']; $ulink = $acs_row['otheraccess']; $plink = $acs_row['printaccess'];
                        if((int)$acs_row['supadmin_access'] == 1){ $_SESSION['usr_atype'] = "S"; }
                        else if((int)$acs_row['admin_access'] == 1){ $_SESSION['usr_atype'] = "A"; }
                        else if((int)$acs_row['normal_access'] == 1){ $_SESSION['usr_atype'] = "N"; }
                        else{ }
                    }
                    
                    $acs_sql = "SELECT * FROM `main_linkdetails` WHERE `parentid` LIKE '$acs_cid' AND `active` = '1'";
                    $acs_query = mysqli_query($acs_conn,$acs_sql); $acs_cnt = mysqli_num_rows($acs_query);
                    while($acs_row = mysqli_fetch_array($acs_query)){
                        if(strtolower($acs_row['name']) == "add"){ if(str_contains($alink, $acs_row['childid'])){ $acs_add_flag = 1; $acs_add_url = $acs_row['href']; } }
                        else if(strtolower($acs_row['name']) == "edit"){ if(str_contains($elink, $acs_row['childid'])){ $acs_edit_flag = 1; $acs_edit_url = $acs_row['href']; } }
                        else if(strtolower($acs_row['name']) == "delete"){ if(str_contains($rlink, $acs_row['childid'])){ $acs_delete_flag = 1; $acs_delete_url = $acs_row['href']; } }
                        else if(strtolower($acs_row['name']) == "update"){ if(str_contains($ulink, $acs_row['childid'])){ $acs_update_flag = 1; $acs_update_url = $acs_row['href']; } }
                        else if(strtolower($acs_row['name']) == "print"){ if(str_contains($plink, $acs_row['childid'])){ $acs_print_flag = 1; $acs_print_url = $acs_row['href']; } }
                        else{ }
                        $acs_print_flag = 1;
                    }
                }
                else{ $acslist_error_flag = 1; $access_error_msg = "Error: File Child-Id Not available."; }
            }
            else{ $acslist_error_flag = 1; $access_error_msg = "Error: File link not available."; }
        }
        else{ }
    }
    else{ $acslist_error_flag = 1; $access_error_msg = "Error: connecting Database."; }
}
else{ $acslist_error_flag = 1; $access_error_msg = "Error: Internal Server Error"; }
if(isset($acs_conn)){ mysqli_close($acs_conn); }
