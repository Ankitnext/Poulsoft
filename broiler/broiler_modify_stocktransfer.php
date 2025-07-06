<?php
//broiler_modify_stocktransfer.php
session_start();
include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['stocktransfer'];

$tblchk_dbname = $_SESSION['dbase'];
$tblchk_tblname = "Tables_in_".$tblchk_dbname;
$sqlt = "SHOW TABLES;"; $queryt = mysqli_query($conn,$sqlt);
while($rowt = mysqli_fetch_array($queryt)){
    if($rowt[$tblchk_tblname] == "acc_category"){$count1 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_coa"){$count2 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_controltype"){$count3 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_modes"){$count4 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_schedules"){$count5 = 1; }
    else if($rowt[$tblchk_tblname] == "acc_types"){$count6 = 1; }
    else if($rowt[$tblchk_tblname] == "account_contranotes"){$count7 = 1; }
    else if($rowt[$tblchk_tblname] == "account_summary"){$count8 = 1; }
    else if($rowt[$tblchk_tblname] == "account_vouchers"){$count9 = 1; }
    else if($rowt[$tblchk_tblname] == "app_permissions"){$count10 = 1; }
    else if($rowt[$tblchk_tblname] == "authorize"){$count11 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_batch"){$count12 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_batch_bkp"){$count13 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_batch_bkp1"){$count14 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_breed"){$count15 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_breedstandard"){$count16 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_crdrnote"){$count17 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_daily_record"){$count18 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_daily_record_unsaved"){$count19 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_designation"){$count20 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_diseases"){$count21 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_doctorvisit"){$count22 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_egg_grading_consume"){$count23 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_egg_grading_produce"){$count24 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_employee"){$count25 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farm"){$count26 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farmer"){$count27 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farmer_classify"){$count28 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_farmergroup"){$count29 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_consumed"){$count30 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_expense"){$count31 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_formula"){$count32 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_production"){$count33 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_feed_silos"){$count34 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_fcr_decentive"){$count35 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_fcr_incentive"){$count36 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_fcr_production"){$count37 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_mi_decentive"){$count38 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_mi_incentive"){$count39 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_pc_decentive"){$count40 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_pc_incentive"){$count41 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_si_incentive"){$count42 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_st_decentive"){$count43 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_gc_standard"){$count44 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchentry"){$count45 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery"){$count46 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_consumed"){$count47 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_expense"){$count48 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_hatcher"){$count49 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_hatchery_setter"){$count50 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_inv_adjustment"){$count51 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_inv_intermediate_issued"){$count52 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_inv_intermediate_received"){$count53 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_itemreturns"){$count54 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_lab_results"){$count55 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_max_values"){$count56 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_medicine_record"){$count57 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_openings"){$count58 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_payments"){$count59 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_placementplan"){$count60 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_purchases"){$count61 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_rearingcharge"){$count62 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_receipts"){$count63 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_reportfields"){$count64 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_sales"){$count65 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_tray_settings"){$count66 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_vaccineschedule"){$count67 = 1; }
    else if($rowt[$tblchk_tblname] == "broiler_vehicle"){$count68 = 1; }
    else if($rowt[$tblchk_tblname] == "company_names"){$count69 = 1; }
    else if($rowt[$tblchk_tblname] == "company_price_list"){$count70 = 1; }
    else if($rowt[$tblchk_tblname] == "country_states"){$count71 = 1; }
    else if($rowt[$tblchk_tblname] == "customer_price"){$count72 = 1; }
    else if($rowt[$tblchk_tblname] == "customer_sales"){$count73 = 1; }
    else if($rowt[$tblchk_tblname] == "dataentry_daterange"){$count74 = 1; }
    else if($rowt[$tblchk_tblname] == "employee_sal_generator"){$count75 = 1; }
    else if($rowt[$tblchk_tblname] == "employee_sal_payment"){$count76 = 1; }
    else if($rowt[$tblchk_tblname] == "employee_salary_components"){$count77 = 1; }
    else if($rowt[$tblchk_tblname] == "extra_access"){$count78 = 1; }
    else if($rowt[$tblchk_tblname] == "farm_check_list_record"){$count79 = 1; }
    else if($rowt[$tblchk_tblname] == "farmer_item_price"){$count80 = 1; }
    else if($rowt[$tblchk_tblname] == "feed_bagcapacity"){$count81 = 1; }
    else if($rowt[$tblchk_tblname] == "feedindent"){$count82 = 1; }
    else if($rowt[$tblchk_tblname] == "feedmill_expenses_parameters"){$count83 = 1; }
    else if($rowt[$tblchk_tblname] == "gateway_masters"){$count84 = 1; }
    else if($rowt[$tblchk_tblname] == "gateway_paymentlinks"){$count85 = 1; }
    else if($rowt[$tblchk_tblname] == "inv_sectors"){$count86 = 1; }
    else if($rowt[$tblchk_tblname] == "item_category"){$count87 = 1; }
    else if($rowt[$tblchk_tblname] == "item_closingstock"){$count88 = 1; }
    else if($rowt[$tblchk_tblname] == "item_details"){$count89 = 1; }
    else if($rowt[$tblchk_tblname] == "item_qty_conversion"){$count90 = 1; }
    else if($rowt[$tblchk_tblname] == "item_stocktransfers"){$count91 = 1; }
    else if($rowt[$tblchk_tblname] == "item_units"){$count92 = 1; }
    else if($rowt[$tblchk_tblname] == "location_branch"){$count93 = 1; }
    else if($rowt[$tblchk_tblname] == "location_line"){$count94 = 1; }
    else if($rowt[$tblchk_tblname] == "location_region"){$count95 = 1; }
    else if($rowt[$tblchk_tblname] == "main_access"){$count96 = 1; }
    else if($rowt[$tblchk_tblname] == "main_companyprofile"){$count97 = 1; }
    else if($rowt[$tblchk_tblname] == "main_contactdetails"){$count98 = 1; }
    else if($rowt[$tblchk_tblname] == "main_dailypaperrate"){$count99 = 1; }
    else if($rowt[$tblchk_tblname] == "main_disclaimer"){$count100 = 1; }
    else if($rowt[$tblchk_tblname] == "Tables_in_poulso6_admin_broiler_broilermaster"){$count101 = 1; }
    else if($rowt[$tblchk_tblname] == "main_financialyear"){$count102 = 1; }
    else if($rowt[$tblchk_tblname] == "main_groups"){$count103 = 1; }
    else if($rowt[$tblchk_tblname] == "main_jals"){$count104 = 1; }
    else if($rowt[$tblchk_tblname] == "main_linkdetails"){$count105 = 1; }
    else if($rowt[$tblchk_tblname] == "main_mortality"){$count106 = 1; }
    else if($rowt[$tblchk_tblname] == "main_officetypes"){$count107 = 1; }
    else if($rowt[$tblchk_tblname] == "main_tcds"){$count108 = 1; }
    else if($rowt[$tblchk_tblname] == "main_terms"){$count109 = 1; }
    else if($rowt[$tblchk_tblname] == "master_dashboard_links"){$count110 = 1; }
    else if($rowt[$tblchk_tblname] == "master_farm_checklist"){$count111 = 1; }
    else if($rowt[$tblchk_tblname] == "master_formfields"){$count112 = 1; }
    else if($rowt[$tblchk_tblname] == "master_generator"){$count113 = 1; }
    else if($rowt[$tblchk_tblname] == "master_item_parameter"){$count114 = 1; }
    else if($rowt[$tblchk_tblname] == "master_itemfields"){$count115 = 1; }
    else if($rowt[$tblchk_tblname] == "master_parameters"){$count116 = 1; }
    else if($rowt[$tblchk_tblname] == "master_reportfields"){$count117 = 1; }
    else if($rowt[$tblchk_tblname] == "message_master"){$count118 = 1; }
    else if($rowt[$tblchk_tblname] == "mobile_user_rights"){$count119 = 1; }
    else if($rowt[$tblchk_tblname] == "prefix_master"){$count120 = 1; }
    else if($rowt[$tblchk_tblname] == "price_master"){$count121 = 1; }
    else if($rowt[$tblchk_tblname] == "pur_purchase"){$count122 = 1; }
    else if($rowt[$tblchk_tblname] == "sms_count"){$count123 = 1; }
    else if($rowt[$tblchk_tblname] == "sms_details"){$count124 = 1; }
    else if($rowt[$tblchk_tblname] == "sms_master"){$count125 = 1; }
    else if($rowt[$tblchk_tblname] == "tax_details"){$count126 = 1; }
    else if($rowt[$tblchk_tblname] == "trip_sheet"){$count127 = 1; }
    else if($rowt[$tblchk_tblname] == "upi_types"){$count128 = 1; }
    else{ }
}

$sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $icat_iac[$row['code']] = $row['iac'];
    $icat_pvac[$row['code']] = $row['pvac'];
    $icat_pdac[$row['code']] = $row['pdac'];
    $icat_cogsac[$row['code']] = $row['cogsac'];
    $icat_wpac[$row['code']] = $row['wpac'];
    $icat_sac[$row['code']] = $row['sac'];
    $icat_srac[$row['code']] = $row['srac'];
}

$sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $este_code = array();
while($row = mysqli_fetch_assoc($query)){ $este_code[$row['code']] = $row['este_code']; }

$date = date("Y-m-d",strtotime($_POST['date']));
$dcno = $_POST['dcno'];
$code = $_POST['code'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];
$amount = $quantity * $price;
$fromwarehouse = $_POST['fromwarehouse'];
$towarehouse = $_POST['towarehouse'];
$vehicle_code = $_POST['vehicle_code'];
$driver_code = $_POST['driver_code'];
$driver_mobile = $_POST['driver_mobile'];
$emp_code = $_POST['emp_code'];
$emp_bcoa = $_POST['emp_bcoa'];
$emp_eamt = $_POST['emp_eamt'];
$remarks = $_POST['remarks'];
$id = $_POST['idvalue'];

//Store Previous Data before change
include_once("poulsoft_store_chngmaster.php");
$chng_type = "Edit";
$edit_file = "broiler_modify_stocktransfer.php";
$mtbl_name = "item_stocktransfers";
$tno_cname = "id";
$msg1 = array("file"=>$edit_file, "trnum"=>$id, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
$message = json_encode($msg1);
store_modified_details($message);

//Fetch Feed Details and Feed in Bags Flag
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_feed_code[$row['code']] = $row['code']; $item_feed_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Stock Transfer' AND `field_function` LIKE 'Bags' AND `flag` = 1"; $query = mysqli_query($conn,$sql); $bag_access_flag = mysqli_num_rows($query);

$fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$fromwarehouse' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $fquery = mysqli_query($conn,$fsql); $fcount = mysqli_num_rows($fquery);
if($fcount > 0){ while($frow = mysqli_fetch_assoc($fquery)){ $from_batch = $frow['code']; } } else{ $from_batch = ''; }

$tsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$towarehouse' AND `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $tquery = mysqli_query($conn,$tsql); $tcount = mysqli_num_rows($tquery);
if($tcount > 0){ while($trow = mysqli_fetch_assoc($tquery)){ $to_batch = $trow['code']; } } else{ $to_batch = ''; }

$farmer_price = 0;
if($count80 > 0){
    $sql = "SELECT * FROM `farmer_item_price` WHERE `itemcode` = '$code' AND `active` = '1' AND `date` IN (SELECT MAX(date) as date FROM `farmer_item_price` WHERE `date` <= '$date' AND `itemcode` = '$code' AND `active` = '1')"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $farmer_price = $row['rate']; }
    if($farmer_price == "" || $farmer_price == 0 || $farmer_price == 0.00){ $farmer_price = "0.00"; }
}
if($quantity == "" || $quantity == NULL || $quantity == 0 || $quantity == "0.00"){ $quantity = 0; }
if($price == "" || $price == NULL || $price == 0 || $price == "0.00"){ $price = 0; }
if($emp_eamt == "" || $emp_eamt == NULL || $emp_eamt == 0 || $emp_eamt == "0.00"){ $emp_eamt = 0; }

$feed_item =  $code;
if(!empty($item_feed_name[$feed_item]) && !empty($quantity) && $bag_access_flag > 0){
    $bcount = $ibag_flag1 = 0;
    
    if($count81 > 0){
        $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE '$code' AND `active` = '1' AND `dflag` = '0'";
        $bquery = mysqli_query($conn,$bsql); $bcount = $ibag_flag1 = mysqli_num_rows($bquery);
    }
    if($bcount > 0){
        if($ibag_flag1 > 0 && $bag_access_flag > 0){
            while($brow = mysqli_fetch_assoc($bquery)){
                if($brow['code'] != "all"){
                    $quantity = $quantity * $brow['bag_size'];
                    $price = $price / $brow['bag_size'];
                }
                else{
                    $quantity = $quantity * $brow['bag_size'];
                    $price = $price / $brow['bag_size'];
                }
            }
        }
    }
    else{
        $ibag_flag1 = 0;
        if($count81 > 0){
            $bsql = "SELECT * FROM `feed_bagcapacity` WHERE `code` LIKE 'all' AND `active` = '1' AND `dflag` = '0'";
            $bquery = mysqli_query($conn,$bsql); $ibag_flag1 = mysqli_num_rows($bquery);
        }
        if($ibag_flag1 > 0 && $bag_access_flag > 0){
            while($brow = mysqli_fetch_assoc($bquery)){
                if($brow['code'] != "all"){
                    $quantity = $quantity * $brow['bag_size'];
                    $price = $price / $brow['bag_size'];
                }
                else{
                    $quantity = $quantity * $brow['bag_size'];
                    $price = $price / $brow['bag_size'];
                }
            }
        }
    }
}
$emp_ecoa = $este_code[$emp_code];
$sql = "UPDATE `item_stocktransfers` SET `date` = '$date',`dcno` = '$dcno',`fromwarehouse` = '$fromwarehouse',`from_batch` = '$from_batch',`code` = '$code',`quantity` = '$quantity',`price` = '$price',`amount` = '$amount',`farmer_price` = '$farmer_price',`towarehouse` = '$towarehouse',`to_batch` = '$to_batch',`vehicle_code` = '$vehicle_code',`driver_code` = '$driver_code',`driver_mobile` = '$driver_mobile',`emp_code` = '$emp_code',`emp_ecoa` = '$emp_ecoa',`emp_bcoa` = '$emp_bcoa',`emp_eamt` = '$emp_eamt',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
else {
    $sql = "SELECT trnum FROM `item_stocktransfers` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $trnum = $row['trnum']; }

    $sql = "DELETE FROM `account_summary` WHERE `trnum` = '$trnum'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
    else {
        $coa_Dr = $coa_Cr = $icat_iac[$icat_code[$code]];
        $from_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,updatedemp,updatedtime) 
        VALUES ('CR','$coa_Cr','$date','$dcno','$trnum','$code','$quantity','$price','$amount','$fromwarehouse','$from_batch','$vehicle_code','$driver_code','$remarks','0','0','1','0','$addedemp','$addedtime')";
        if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); }
        else{
            $to_post = "INSERT INTO `account_summary` (crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,updatedemp,updatedtime) 
            VALUES ('DR','$coa_Dr','$date','$dcno','$trnum','$code','$quantity','$price','$amount','$towarehouse','$to_batch','$vehicle_code','$driver_code','$remarks','0','0','1','0','$addedemp','$addedtime')";
            if(!mysqli_query($conn,$to_post)){ die("Error:-".mysqli_error($conn)); } else{ }
        }

        //Employee Stock transfr Expense
        if((float)$emp_eamt > 0){
            
            $from_post = "INSERT INTO `account_summary` (emp_code,crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedtime) 
            VALUES ('$emp_code','CR','$emp_ecoa','$date','$dcno','$trnum','$code','$quantity','0','$emp_eamt','$fromwarehouse','$from_batch','$vehicle_code','$driver_code','$remarks','0','0','1','0','Employee Stock Transfer Cost','$addedemp','$addedtime','$addedtime')";
            if(!mysqli_query($conn,$from_post)){ die("Error:-".mysqli_error($conn)); }
            else{
                $to_post = "INSERT INTO `account_summary` (emp_code,crdr,coa_code,date,dc_no,trnum,item_code,quantity,price,amount,location,batch,vehicle_code,driver_code,remarks,gc_flag,flag,active,dflag,etype,addedemp,addedtime,updatedtime) 
                VALUES ('$emp_code','DR','$emp_bcoa','$date','$dcno','$trnum','$code','$quantity','0','$emp_eamt','$towarehouse','$to_batch','$vehicle_code','$driver_code','$remarks','0','0','1','0','Employee Stock Transfer Cost','$addedemp','$addedtime','$addedtime')";
                if(!mysqli_query($conn,$to_post)){ die("Error:-".mysqli_error($conn)); }
                else{ }
            }
        }
    }
}

header('location:broiler_display_stocktransfer.php?ccid='.$ccid);
?>