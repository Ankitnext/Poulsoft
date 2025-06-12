<?php
//broiler_save_useraccess.php
session_start(); include "newConfig.php";
$client = $_SESSION['client'];
$dblist = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['useraccess'];

$sql='SHOW COLUMNS FROM `main_access`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("sale_multiple_edit_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `sale_multiple_edit_flag` INT(100) NOT NULL DEFAULT '1' COMMENT 'Sale Multiple Edit' AFTER `normal_access`"; mysqli_query($conn,$sql); }
if(in_array("sale_multiple_delete_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `sale_multiple_delete_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Sale Multiple Delete' AFTER `sale_multiple_edit_flag`"; mysqli_query($conn,$sql); }
if(in_array("display_dashboard_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `display_dashboard_flag` INT(100) NOT NULL DEFAULT '1' COMMENT 'Dashboard Visibility' AFTER `sale_multiple_delete_flag`"; mysqli_query($conn,$sql); }
if(in_array("nisan_submit_sales", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_access` ADD `nisan_submit_sales` INT(100) NOT NULL DEFAULT '1' COMMENT 'Nisan fetch weightment Save Button Visible'"; mysqli_query($conn,$sql); }

$empname = $_POST['empname'];
$username = $_POST['uname'];
$password = $_POST['upass'];
$mobileno = $_POST['umobile'];
$utype = $_POST['uaccess'];
$sale_multiple_edit_flag = $_POST['sale_multiple_edit_flag'];
$sale_multiple_delete_flag = $_POST['sale_multiple_delete_flag'];
$display_dashboard_flag = $_POST['display_dashboard_flag'];
$login_type = $_POST['login_type'];
$expdate = $_SESSION['expdate'];
$nisan_submit_sales = 0; if($_POST['nisan_submit_sales'] == true || $_POST['nisan_submit_sales'] == "on" || $_POST['nisan_submit_sales'] == "1"){ $nisan_submit_sales = 1; }

$msg_type = "sms";

if($utype == "S"){ $sa = 1; $aa = 0; $na = 0; }
else if($utype == "A"){ $sa = 0; $aa = 1; $na = 0; }
else { $sa = 0; $aa = 0; $na = 1; }

//Locality Access
$branch_arr_list = $line_arr_list = $farm_arr_list = $sector_arr_list = $cgroup_arr_list = array(); $branch_code = $line_code = $farm_code = $warehouse = $cgroups = "";
foreach($_POST['branch_code'] as $ln){ $branch_arr_list[$ln] = $ln; }
foreach($_POST['line_code'] as $ln){ $line_arr_list[$ln] = $ln; }
foreach($_POST['farm_code'] as $ln){ $farm_arr_list[$ln] = $ln; }
foreach($_POST['warehouse'] as $ln){ $sector_arr_list[$ln] = $ln; }
foreach($_POST['cgroup'] as $ln){ $cgroup_arr_list[$ln] = $ln; }

$branch_code = implode(",",$branch_arr_list);
$line_code = implode(",",$line_arr_list);
$farm_code = implode(",",$farm_arr_list);
$warehouse = implode(",",$sector_arr_list);
$cgroups = implode(",",$cgroup_arr_list);

//User Web Screen Access
$display_access = $add_access = $edit_access = $delete_access = $print_access = $other_access = array();
foreach($_POST['displays'] as $alist){ $display_access[$alist] = $alist; }
foreach($_POST['adds'] as $alist){ $add_access[$alist] = $alist; }
foreach($_POST['edits'] as $alist){ $edit_access[$alist] = $alist; }
foreach($_POST['deletes'] as $alist){ $delete_access[$alist] = $alist; }
foreach($_POST['prints'] as $alist){ $print_access[$alist] = $alist; }
foreach($_POST['updates'] as $alist){ $other_access[$alist] = $alist; }

$dlinks = str_replace(",,",",",implode(",",array_unique(explode(",",implode(",",$display_access)))));
$alinks = str_replace(",,",",",implode(",",array_unique(explode(",",implode(",",$add_access)))));
$elinks = str_replace(",,",",",implode(",",array_unique(explode(",",implode(",",$edit_access)))));
$deletes = str_replace(",,",",",implode(",",array_unique(explode(",",implode(",",$delete_access)))));
$prints = str_replace(",,",",",implode(",",array_unique(explode(",",implode(",",$print_access)))));
$ulinks = str_replace(",,",",",implode(",",array_unique(explode(",",implode(",",$other_access)))));

foreach($_POST['add_mobile'] as $ln){ if($add_flag == ""){ $add_flag = $ln; } else { $add_flag = $add_flag.",".$ln; } $sid[$ln] = $ln; }
foreach($_POST['edit_mobile'] as $ln){ if($edit_flag == ""){ $edit_flag = $ln; } else { $edit_flag = $edit_flag.",".$ln; } $sid[$ln] = $ln; }
foreach($_POST['delete_mobile'] as $ln){ if($delete_flag == ""){ $delete_flag = $ln; } else { $delete_flag = $delete_flag.",".$ln; } $sid[$ln] = $ln; }
foreach($_POST['view_mobile'] as $ln){ if($view_flag == ""){ $view_flag = $ln; } else { $view_flag = $view_flag.",".$ln; } $sid[$ln] = $ln; }

$sql = "SELECT * FROM `app_permissions` ORDER BY `app_permissions`.`id` ASC"; $query = mysqli_query($conn,$sql); $tr_val = $rpt_val = "";
while($row = mysqli_fetch_assoc($query)){
    if(!empty($sid[$row['id']])){
        if($row['type'] == "Transaction" || $row['type'] == "transaction"){
            if($tr_val == ""){ $tr_val = $row['screens']; } else{ $tr_val = $tr_val.",".$row['screens']; }
        }
        else if($row['type'] == "Report" || $row['type'] == "report"){
            if($rpt_val == ""){ $rpt_val = $row['screens']; } else{ $rpt_val = $rpt_val.",".$row['screens']; }
        }
    }
}

//Breeder Accesses
$farms = $units = $sheds = $batches = $flocks = array(); $f_aflag = $u_aflag = $s_aflag = $b_aflag = $fl_aflag = 0;
foreach($_POST['farms'] as $t1){ $farms[$t1] = $t1; }           foreach($farms as $t1){ if($t1 == "all"){ $f_aflag = 1; } }
foreach($_POST['units'] as $t1){ $units[$t1] = $t1; }           foreach($units as $t1){ if($t1 == "all"){ $u_aflag = 1; } }
foreach($_POST['sheds'] as $t1){ $sheds[$t1] = $t1; }           foreach($sheds as $t1){ if($t1 == "all"){ $s_aflag = 1; } }
foreach($_POST['batches'] as $t1){ $batches[$t1] = $t1; }       foreach($batches as $t1){ if($t1 == "all"){ $b_aflag = 1; } }
foreach($_POST['flocks'] as $t1){ $flocks[$t1] = $t1; }         foreach($flocks as $t1){ if($t1 == "all"){ $fl_aflag = 1; } }

//Arrange Filters
$bfarms_list = $bunits_list = $bsheds_list = $bbatch_list = $bflock_list = "all";
if($f_aflag == 0){ $bfarms_list = implode(",",$farms); }
if($u_aflag == 0){ $bunits_list = implode(",",$units); }
if($s_aflag == 0){ $bsheds_list = implode(",",$sheds); }
if($b_aflag == 0){ $bbatch_list = implode(",",$batches); }
if($fl_aflag == 0){ $bflock_list = implode(",",$flocks); }

$sql = "SELECT MAX(incr) as incr FROM `log_useraccess` WHERE `dblist` = '$dblist'"; $query = mysqli_query($conns,$sql); while($row = mysqli_fetch_assoc($query)) { $incr = $row['incr']; $incr = $incr + 1; }
if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { } $empcode = $client."-".$incr;

$ip = $_SERVER['REMOTE_ADDR'];

$sql = "INSERT INTO `log_useraccess` (incr,username,password,dblist,account_access,screens,screenstwo,iaddress,mobileno,expdate,empcode,flag,mobileflag,ipflag,addedempcode,createddatetime,updatedempcode,updateddatetime,client,logintype,msg_type) 
VALUES ('$incr','$username','$password','$dblist','BTS','$tr_val','$rpt_val','$ip','$mobileno','$expdate','$empcode','1','0','0','$addedemp','$addedtime',NULL,'$addedtime','$client','$login_type','$msg_type')";
if(!mysqli_query($conns,$sql)) { echo die("Error Log user:- ".mysqli_error($conns)); }
else {

    $sql = "INSERT INTO `main_access` (nisan_submit_sales,db_emp_code,empcode,displayaccess,addaccess,editaccess,deleteaccess,printaccess,otheraccess,supadmin_access,admin_access,normal_access,sale_multiple_edit_flag,sale_multiple_delete_flag,display_dashboard_flag,branch_code,line_code,farm_code,loc_access,cgroup_access,bfarms_list,bunits_list,bsheds_list,bbatch_list,bflock_list,add_flag,edit_flag,delete_flag,view_flag,addedemp,addedtime,updatedemp,updatedtime) 
    VALUES ('$nisan_submit_sales','$empname','$empcode','$dlinks','$alinks','$elinks','$deletes','$prints','$ulinks','$sa','$aa','$na','$sale_multiple_edit_flag','$sale_multiple_delete_flag','$display_dashboard_flag','$branch_code','$line_code','$farm_code','$warehouse','$cgroups','$bfarms_list','$bunits_list','$bsheds_list','$bbatch_list','$bflock_list','$add_flag','$edit_flag','$delete_flag','$view_flag','$addedemp','$addedtime',NULL,'$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else {
    header('location:broiler_display_useraccess.php?ccid='.$ccid);
    }
}
?>
