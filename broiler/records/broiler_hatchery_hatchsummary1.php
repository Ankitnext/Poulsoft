<?php
//broiler_hatchery_hatchsummary1.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_hatchery_hatchsummary1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_hatchery_hatchsummary1.php?db=$db&userid=".$user_code;
}
$file_name = "Hatchery Hatch Summary Report";
include "decimal_adjustments.php";

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("main_officetypes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_officetypes LIKE poulso6_admin_broiler_broilermaster.main_officetypes;"; mysqli_query($conn,$sql1); }
if(in_array("inv_sectors", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.inv_sectors LIKE poulso6_admin_broiler_broilermaster.inv_sectors;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_secunit_mapping", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_secunit_mapping LIKE poulso6_admin_broiler_broilermaster.broiler_secunit_mapping;"; mysqli_query($conn,$sql1); }

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%hatch%' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $hatchery_alist = array();
while($row = mysqli_fetch_assoc($query)){ $hatchery_alist[$row['code']] = $row['code']; }

$hatchery_list = implode("','",$hatchery_alist);
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$hatchery_list') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

//Breeder Egg Details
$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND `begg_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $cegg_code = $icat_iac = array();
while($row = mysqli_fetch_assoc($query)){ $cegg_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; } $egg_list = implode("','", $cegg_code);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$egg_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql); $egg_code = $egg_name = array();
while($row = mysqli_fetch_assoc($query)){ $egg_code[$row['code']] = $row['code']; $egg_name[$row['code']] = $row['description']; }
$e_cnt = sizeof($egg_code); //foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; }

//Fetch All Stock Received From Details
$sql = "SELECT * FROM `inv_sectors` WHERE `type` NOT IN ('$hatchery_list') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $csv_code = $ven_code = $ven_name = array();
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['description']; $ven_type[$row['code']] = "W"; }
$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $csv_code[$row['code']] = $row['code']; $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; $ven_type[$row['code']] = $row['contacttype']; }
asort($ven_name);

$fdate = $tdate = date("Y-m-d"); $hatcheries = $fetch_type = $vendors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $hatcheries = $_POST['hatcheries'];
    $fetch_type = $_POST['fetch_type'];
    $vendors = $_POST['vendors'];
    $excel_type = $_POST['export'];
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table id="main_table" class="tbl" align="center">
            <thead class="thead3" align="center" width="auto">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="23" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="25">
                            <div class="row">
                                <!--<div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php //echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>-->
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Hatchery</label>
                                    <select name="hatcheries" id="hatcheries" class="form-control select2">
                                        <option value="all" <?php if($hatcheries == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $scode){ if($sector_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($hatcheries == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>From Type</label>
                                    <select name="fetch_type" id="fetch_type" class="form-control select2" onchange="fetch_vensector_details();">
                                        <option value="all" <?php if($fetch_type == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="purchased" <?php if($fetch_type == "purchased"){ echo "selected"; } ?>>-Purchased-</option>
                                        <option value="received" <?php if($fetch_type == "received"){ echo "selected"; } ?>>-Received-</option>
                                        <option value="transferred" <?php if($fetch_type == "transferred"){ echo "selected"; } ?>>-Transferred-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label title="Purchased/Received/Transferred">P/R/T</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php
                                        foreach($ven_name as $vcode => $vname){
                                            if($fetch_type == "all"){
                                                if($ven_name[$vcode] != ""){
                                                ?>
                                                <option value="<?php echo $vcode; ?>" <?php if($vendors == $vcode){ echo "selected"; } ?>><?php echo $vname; ?></option>
                                                <?php
                                                }
                                            }
                                            else if($fetch_type == "purchased" && !empty($ven_type[$vcode]) && ($ven_type[$vcode] == "S" || $ven_type[$vcode] == "S&C")){
                                                if($ven_name[$vcode] != ""){
                                                ?>
                                                <option value="<?php echo $vcode; ?>" <?php if($vendors == $vcode){ echo "selected"; } ?>><?php echo $vname; ?></option>
                                                <?php
                                                }
                                            }
                                            else if($fetch_type == "received" && !empty($ven_type[$vcode]) && ($ven_type[$vcode] == "C" || $ven_type[$vcode] == "S&C")){
                                                if($ven_name[$vcode] != ""){
                                                ?>
                                                <option value="<?php echo $vcode; ?>" <?php if($vendors == $vcode){ echo "selected"; } ?>><?php echo $vname; ?></option>
                                                <?php
                                                }
                                            }
                                            else if($fetch_type == "transferred" && !empty($ven_type[$vcode]) && $ven_type[$vcode] == "W"){
                                                if($ven_name[$vcode] != ""){
                                                ?>
                                                <option value="<?php echo $vcode; ?>" <?php if($vendors == $vcode){ echo "selected"; } ?>><?php echo $vname; ?></option>
                                                <?php
                                                }
                                            }
                                            else{ }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="download_to_excel2('main_table','<?php echo $file_name; ?>');">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="search_table">Search</label>
                                    <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <?php
            
            $html = $nhtml = $fhtml = '';
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';
            $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>';
            $nhtml .= '<th>Company</th>'; $fhtml .= '<th id="order">Company</th>';
            $nhtml .= '<th>Unit</th>'; $fhtml .= '<th id="order">Unit</th>';
            $nhtml .= '<th>Flock</th>'; $fhtml .= '<th id="order">Flock</th>';
            $nhtml .= '<th>Opening</th>'; $fhtml .= '<th id="order_num">Opening</th>';
            $nhtml .= '<th>Received</th>'; $fhtml .= '<th id="order_num">Received</th>';
            $nhtml .= '<th>Damaged</th>'; $fhtml .= '<th id="order_num">Damaged</th>';
            $nhtml .= '<th>Crack/Broken</th>'; $fhtml .= '<th id="order_num">Crack/Broken</th>';
            $nhtml .= '<th>Tray Set</th>'; $fhtml .= '<th id="order_num">Tray Set</th>';
            $nhtml .= '<th>Hatch Out</th>'; $fhtml .= '<th id="order_num">Hatch Out</th>';
            $nhtml .= '<th>Balance</th>'; $fhtml .= '<th id="order_num">Balance</th>';
            $nhtml .= '<th>Chicks</th>'; $fhtml .= '<th id="order_num">Chicks</th>';
            $nhtml .= '<th>Chicks %</th>'; $fhtml .= '<th id="order_num">Chicks %</th>';
            $nhtml .= '<th>Culls</th>'; $fhtml .= '<th id="order_num">Culls</th>';
            $nhtml .= '<th>Culls %</th>'; $fhtml .= '<th id="order_num">Culls %</th>';
            $nhtml .= '<th>DS</th>'; $fhtml .= '<th id="order_num">DS</th>';
            $nhtml .= '<th>DS %</th>'; $fhtml .= '<th id="order_num">DS %</th>';
            $nhtml .= '<th>GS</th>'; $fhtml .= '<th id="order_num">GS</th>';
            $nhtml .= '<th>GS %</th>'; $fhtml .= '<th id="order_num">GS %</th>';
            $nhtml .= '<th>Infirtile</th>'; $fhtml .= '<th id="order_num">Infirtile</th>';
            $nhtml .= '<th>Infirtile %</th>'; $fhtml .= '<th id="order_num">Infirtile %</th>';
            $nhtml .= '<th>Blasting</th>'; $fhtml .= '<th id="order_num">Blasting</th>';
            $nhtml .= '<th>Blasting %</th>'; $fhtml .= '<th id="order_num">Blasting %</th>';
            $nhtml .= '<th>Overdue Eggs</th>'; $fhtml .= '<th id="order_num">Overdue Eggs</th>';
            $nhtml .= '<th>Hatch Date</th>'; $fhtml .= '<th id="order_num">Hatch Date</th>';
            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                $brdt_flag = $rcvd_flag = $purt_flag = 0; $ven_alist = $unit_alist = $flock_alist = array(); $unit_list = $flock_list = "";
                if($vendors != "all"){
                    if(!empty($ven_type[$vendors]) && $ven_type[$vendors] == "C"){ $rcvd_flag = 1; $ven_alist[$vendors] = $vendors; }
                    else if(!empty($ven_type[$vendors]) && $ven_type[$vendors] == "S"){ $purt_flag = 1; $ven_alist[$vendors] = $vendors; }
                    else if(!empty($ven_type[$vendors]) && $ven_type[$vendors] == "S&C"){ $purt_flag = 1; $ven_alist[$vendors] = $vendors; }
                    else if(!empty($ven_type[$vendors]) && $ven_type[$vendors] == "W"){
                        $brdt_flag = 1;
                        $sql = "SELECT * FROM `broiler_secunit_mapping` WHERE `sector_code` LIKE '$vendors' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $unit_alist[$row['unit_code']] = $row['unit_code']; }
                        $unit_list = implode("','",$unit_alist);
                        $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `unit_code` IN ('$unit_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $flock_alist[$row['code']] = $row['code']; }
                        $flock_list = implode("','",$flock_alist);
                    }
                    else{ }
                }
                else{
                    foreach($ven_name as $vcode => $vname){
                        if($fetch_type == "all"){
                            $brdt_flag = $rcvd_flag = $purt_flag = 1; $ven_alist[$vcode] = $vcode;
                        }
                        else if($fetch_type == "purchased" && !empty($ven_type[$vcode]) && ($ven_type[$vcode] == "S" || $ven_type[$vcode] == "S&C")){
                            if($ven_name[$vcode] != ""){ $purt_flag = 1; $ven_alist[$vcode] = $vcode; }
                        }
                        else if($fetch_type == "received" && !empty($ven_type[$vcode]) && ($ven_type[$vcode] == "C" || $ven_type[$vcode] == "S&C")){
                            if($ven_name[$vcode] != ""){ $rcvd_flag = 1; $ven_alist[$vcode] = $vcode; }
                        }
                        else if($fetch_type == "transferred" && !empty($ven_type[$vcode]) && $ven_type[$vcode] == "W"){
                            if($ven_name[$vcode] != ""){ $brdt_flag = 1; $ven_alist[$vcode] = $vcode; }
                        }
                        else{ }
                    }
                    if((int)$brdt_flag == 1){
                        $sec_list = implode("','",$ven_alist);
                        $sql = "SELECT * FROM `broiler_secunit_mapping` WHERE `sector_code` IN ('$sec_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $unit_alist[$row['unit_code']] = $row['unit_code']; }
                        $unit_list = implode("','",$unit_alist);
                        $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `unit_code` IN ('$unit_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $flock_alist[$row['code']] = $row['code']; }
                        $flock_list = implode("','",$flock_alist);
                    }
                }
                //Fetching Data
                $atrno_alist = $bflk_alist = $btrno_alist = $bitem_alist = $ccus_alist = $ctrno_alist = $citem_alist = $psup_alist = $ptrno_alist = $pitem_alist = array();
                if((int)$brdt_flag == 1){
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `from_flock` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $bin_oqty = $bin_bqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['from_flock']."@".$row['trnum']."@".$row['code']; $bflk_alist[$row['from_flock']] = $row['from_flock']; $atrno_alist[$row['trnum']] = $row['trnum']; $btrno_alist[$row['trnum']] = $row['trnum']; $bitem_alist[$row['code']] = $row['code'];
                        if(strtotime($row['date']) < strtotime($fdate)){ $bin_oqty[$key1] += (float)$row['quantity']; }
                        else { $bin_bqty[$key1] += (float)$row['quantity']; }
                    }
                }
                if((int)$rcvd_flag == 1){
                    $sql = "SELECT * FROM `broiler_hatchery_stkreceivein` WHERE `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $rin_oqty = $rin_bqty = $riu_code = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['vcode']."@".$row['trnum']."@".$row['icode']; $bflk_alist[$row['vcode']] = $row['vcode']; $atrno_alist[$row['trnum']] = $row['trnum']; $btrno_alist[$row['trnum']] = $row['trnum']; $bitem_alist[$row['icode']] = $row['icode'];
                        if(strtotime($row['date']) < strtotime($fdate)){ $bin_oqty[$key1] += (float)$row['rcvd_qty']; }
                        else {
                            $bin_bqty[$key1] += (float)$row['rcvd_qty'];
                            $riu_code[$row['vcode']] = $row['billno'];
                        }
                    }
                }
                if((int)$purt_flag == 1){
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $pin_oqty = $pin_bqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['vcode']."@".$row['trnum']."@".$row['icode']; $bflk_alist[$row['vcode']] = $row['vcode']; $atrno_alist[$row['trnum']] = $row['trnum']; $btrno_alist[$row['trnum']] = $row['trnum']; $bitem_alist[$row['icode']] = $row['icode'];
                        if(strtotime($row['date']) < strtotime($fdate)){ $bin_oqty[$key1] += (float)$row['rcvd_qty']; }
                        else { $bin_bqty[$key1] += (float)$row['rcvd_qty']; }
                    }
                }
                $atrno_list = implode("','",$atrno_alist);
                $sql = "SELECT * FROM `broiler_tray_settings` WHERE `link_trnum` IN ('$atrno_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `setting_date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $tstrno_alist = $sktrno_alist = $tseg_oqty = $tseg_bqty = $tsde_bqty = $tsbe_bqty = $heg_pqty = $hep_date = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key1 = $row['vcode']."@".$row['link_trnum']."@".$row['item_code']; $tstrno_alist[$row['trnum']] = $row['trnum']; $sktrno_alist[$row['trnum']] = $key1;
                    if(strtotime($row['setting_date']) < strtotime($fdate)){
                        if((int)$row['hatch_flag'] == 0){ $tseg_oqty[$key1] += (float)$row['nof_egg_set']; }
                    }
                    else{
                        //echo "<br/>".$row['trnum']."@".$row['nof_egg_set']."@".$row['hatch_date']."@".$row['hatch_flag'];
                        $tseg_bqty[$key1] += (float)$row['nof_egg_set'];
                        $tsde_bqty[$key1] += (float)$row['damaged_eggs'];
                        $tsbe_bqty[$key1] += (float)$row['broken_eggs'];
                    }

                    //Check Pending for Hatch <= Today
                    if((int)$row['hatch_flag'] == 0 && strtotime($row['hatch_date']) <= strtotime($tdate)){
                        //echo "<br/>".$row['trnum']."@".$row['nof_egg_set']."@".$row['hatch_date']."@".$row['hatch_flag'];
                        $heg_pqty[$row['vcode']] += (float)$row['nof_egg_set'];
                        $hep_date[$row['vcode']] = (float)$row['hatch_date'];
                    }
                }
                $atrno_list = implode("','",$tstrno_alist);
                $sql = "SELECT * FROM `broiler_hatchentry` WHERE `link_trnum` IN ('$atrno_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `setting_date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $heeg_oqty = $he_eggset_qty = $he_dih_qty = $he_dihp_qty = $he_cull_qty = $he_cullp_qty = $he_schk_qty = 
                $he_schkp_qty = $he_inf_qty = $he_infp_qty = $he_bls_qty = $he_blsp_qty = $heatno_alist = $hktrno_alist = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key1 = $sktrno_alist[$row['link_trnum']];
                    if(strtotime($row['date']) < strtotime($fdate)){ $heeg_oqty[$key1] += (float)$row['nof_egg_set']; }
                    else{
                        $he_eggset_qty[$key1] += (float)$row['nof_egg_set'];
                        $he_dih_qty[$key1] += (float)$row['deathin_hatch_nos'];
                        $he_dihp_qty[$key1] += (float)$row['deathin_hatch_per'];
                        $he_inf_qty[$key1] += (float)$row['infertile'];
                        $he_infp_qty[$key1] = 0; if((float)$row['nof_egg_set'] != 0){ $he_infp_qty[$key1] += (((float)$row['infertile'] / (float)$row['nof_egg_set']) * 100); }
                        $he_bls_qty[$key1] += (float)$row['blasting'];
                        $he_blsp_qty[$key1] = 0; if((float)$row['nof_egg_set'] != 0){ $he_blsp_qty[$key1] += (((float)$row['blasting'] / (float)$row['nof_egg_set']) * 100); }
                        $he_cull_qty[$key1] += (float)$row['culls'];
                        $he_cullp_qty[$key1] += (float)$row['culls_per'];
                        $he_schk_qty[$key1] += (float)$row['saleable_chicks'];
                        $he_schkp_qty[$key1] += (float)$row['saleable_chicks_per'];
                    }
                    $heatno_alist[$row['trnum']] = $row['trnum'];
                    $hktrno_alist[$row['trnum']] = $key1;
                }
                $atrno_list = implode("','",$heatno_alist);
                $sql = "SELECT * FROM `broiler_hatchery_rejectitems` WHERE `link_trnum` IN ('$atrno_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `link_trnum` ASC";
                $query = mysqli_query($conn,$sql); $hrej_oqty = $hrej_bqty = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key1 = $hktrno_alist[$row['link_trnum']];
                    if(strtotime($row['date']) < strtotime($fdate)){ $hrej_oqty[$key1] += (float)$row['reject_egg_nos']; }
                    else{
                        $hrej_bqty[$key1] += (float)$row['reject_egg_nos'];
                    }
                }

                //calculations
                $slno = 0;
                if((int)$brdt_flag == 1){
                    $ots_tqty = $rcv_tqty = $rej_tqty = $tsde_qty = $tsbe_qty = $tse_qty = $he_tset = $sc_tqty = $scp_tqty = $cul_tqty = $culp_tqty = $ds_tqty = $dsp_tqty = $inf_tqty = $infp_tqty = $bls_qty = $blsp_qty = 
                    $flock_alist = array();
                    foreach($bflk_alist as $fcode){
                        foreach($btrno_alist as $tcode){
                            foreach($bitem_alist as $icode){
                                $key1 = $fcode."@".$tcode."@".$icode;
                                /*Received*/
                                $ots_qty = 0; if(!empty($tseg_oqty[$key1]) && $tseg_oqty[$key1] != ""){ $ots_qty = $tseg_oqty[$key1]; }
                                $ots_tqty[$fcode] += (float)$ots_qty;
                                /*Received*/
                                $frcv_qty = 0; if(!empty($bin_bqty[$key1]) && $bin_bqty[$key1] != ""){ $frcv_qty = $bin_bqty[$key1]; }
                                $rcv_tqty[$fcode] += (float)$frcv_qty;
                                /*Rejection*/
                                $rej_qty = 0; if(!empty($hrej_bqty[$key1]) && $hrej_bqty[$key1] != ""){ $rej_qty = $hrej_bqty[$key1]; }
                                $rej_tqty[$fcode] += (float)$rej_qty;
                                /*Hatch Out*/
                                $e_set = 0; if(!empty($he_eggset_qty[$key1]) && $he_eggset_qty[$key1] != ""){ $e_set = $he_eggset_qty[$key1]; }
                                $he_tset[$fcode] += (float)$e_set;
                                /*Damaged*/
                                $d_set = 0; if(!empty($tsde_bqty[$key1]) && $tsde_bqty[$key1] != ""){ $d_set = $tsde_bqty[$key1]; }
                                $tsde_qty[$fcode] += (float)$d_set;
                                /*Broken*/
                                $b_set = 0; if(!empty($tsbe_bqty[$key1]) && $tsbe_bqty[$key1] != ""){ $b_set = $tsbe_bqty[$key1]; }
                                $tsbe_qty[$fcode] += (float)$b_set;
                                /*Tray Set*/
                                $t_set = 0; if(!empty($tseg_bqty[$key1]) && $tseg_bqty[$key1] != ""){ $t_set = $tseg_bqty[$key1]; }
                                $tse_qty[$fcode] += (float)$t_set;
                                /*Chick Qty*/
                                $c_qty = 0; if(!empty($he_schk_qty[$key1]) && $he_schk_qty[$key1] != ""){ $c_qty = $he_schk_qty[$key1]; }
                                $sc_tqty[$fcode] += (float)$c_qty;
                                /*Chick %*/
                                $cp_qty = 0; if(!empty($he_schkp_qty[$key1]) && $he_schkp_qty[$key1] != ""){ $cp_qty = $he_schkp_qty[$key1]; }
                                $scp_tqty[$fcode] += (float)$cp_qty;
                                /*cull Qty*/
                                $cul_qty = 0; if(!empty($he_cull_qty[$key1]) && $he_cull_qty[$key1] != ""){ $cul_qty = $he_cull_qty[$key1]; }
                                $cul_tqty[$fcode] += (float)$cul_qty;
                                /*cull %*/
                                $culp_qty = 0; if(!empty($he_cullp_qty[$key1]) && $he_cullp_qty[$key1] != ""){ $culp_qty = $he_cullp_qty[$key1]; }
                                $culp_tqty[$fcode] += (float)$culp_qty;
                                /*DS Qty*/
                                $ds_qty = 0; if(!empty($he_dih_qty[$key1]) && $he_dih_qty[$key1] != ""){ $ds_qty = $he_dih_qty[$key1]; }
                                $ds_tqty[$fcode] += (float)$ds_qty;
                                /*DS %*/
                                $dsp_qty = 0; if(!empty($he_dihp_qty[$key1]) && $he_dihp_qty[$key1] != ""){ $dsp_qty = $he_dihp_qty[$key1]; }
                                $dsp_tqty[$fcode] += (float)$dsp_qty;
                                /*Inf Qty*/
                                $inf_qty = 0; if(!empty($he_inf_qty[$key1]) && $he_inf_qty[$key1] != ""){ $inf_qty = $he_inf_qty[$key1]; }
                                $inf_tqty[$fcode] += (float)$inf_qty;
                                /*Inf %*/
                                $infp_qty = 0; if(!empty($he_infp_qty[$key1]) && $he_infp_qty[$key1] != ""){ $infp_qty = $he_infp_qty[$key1]; }
                                $infp_tqty[$fcode] += (float)$infp_qty;
                                /*Bls Qty*/
                                $bs_qty = 0; if(!empty($he_bls_qty[$key1]) && $he_bls_qty[$key1] != ""){ $bs_qty = $he_bls_qty[$key1]; }
                                $bls_qty[$fcode] += (float)$bs_qty;
                                /*Bls %*/
                                $bsp_qty = 0; if(!empty($he_blsp_qty[$key1]) && $he_blsp_qty[$key1] != ""){ $bsp_qty = $he_blsp_qty[$key1]; }
                                $blsp_qty[$fcode] += (float)$bsp_qty;

                                $flock_alist[$fcode] = $fcode;
                            }
                        }
                    }
                    $flock_list = implode("','",$flock_alist);
                    $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `code` IN ('$flock_list') AND `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $flock_code = $flock_name = $flock_unit = array(); 
                    while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['description']; $flock_unit[$row['code']] = $row['unit_code']; }
                    
                    $sql = "SELECT * FROM `main_contactdetails` WHERE `code` IN ('$flock_list') AND `dflag` = '0' ORDER BY `name` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['name']; }
                    asort($flock_name);

                    $sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $unit_name = array(); 
                    while($row = mysqli_fetch_assoc($query)){ $unit_name[$row['code']] = $row['description']; }
                    
                    foreach($flock_name as $fcode => $fname){
                        $slno++;
                        $ucode = $flock_unit[$fcode];
                        $uname = $unit_name[$ucode];

                        $tob_qty = $ots_tqty[$fcode];
                        $trc_qty = $rcv_tqty[$fcode];
                        //$trj_qty = $rej_tqty[$fcode];
                        $tts_qty = $tse_qty[$fcode];
                        $ttsd_qty = $tsde_qty[$fcode];
                        $ttsb_qty = $tsbe_qty[$fcode];
                        $tho_qty = $he_tset[$fcode];
                        $tbl_qty = ((float)$tob_qty + (float)$tts_qty - (float)$tho_qty);

                        $schk_qty = $sc_tqty[$fcode];
                        $schk_per = $scp_tqty[$fcode];
                        $cull_qty = $cul_tqty[$fcode];
                        $cull_per = $culp_tqty[$fcode];
                        $tdis_qty = $ds_tqty[$fcode];
                        $tdis_per = $dsp_tqty[$fcode];
                        $tgis_qty = 0;
                        $tgis_per = 0;
                        $tinf_qty = $inf_tqty[$fcode];
                        $tinf_per = $infp_tqty[$fcode];
                        $tbls_qty = $bls_qty[$fcode];
                        $tbls_per = $blsp_qty[$fcode];

                        $odh_pqty = $heg_pqty[$fcode];
                        $odh_date = ""; if(!empty($hep_date[$fcode]) && $hep_date[$fcode] != ""){ $odh_date = date("d.m.Y",strtotime($hep_date[$fcode])); }

                        $html .= '<tr>';
                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                        if(!empty($csv_code[$fcode])){
                            $html .= '<td>'.$fname.'</td>';
                            $html .= '<td>'.$riu_code[$fcode].'</td>';
                            $html .= '<td></td>';
                        }
                        else{
                            $html .= '<td></td>';
                            $html .= '<td>'.$uname.'</td>';
                            $html .= '<td>'.$fname.'</td>';
                        }
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tob_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($trc_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($ttsd_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($ttsb_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tts_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tho_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tbl_qty)).'</td>';

                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($schk_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($schk_per)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cull_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cull_per)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tdis_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tdis_per)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tgis_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tgis_per)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tinf_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tinf_per)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tbls_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($tbls_per)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($odh_pqty)).'</td>';
                        $html .= '<td style="text-align:left;">'.$odh_date.'</td>';
                        $html .= '</tr>';

                        $ttob_qty += (float)$tob_qty;
                        $ttrc_qty += (float)$trc_qty;
                        $tttsd_qty += (float)$ttsd_qty;
                        $tttsb_qty += (float)$ttsb_qty;
                        $ttts_qty += (float)$tts_qty;
                        $ttho_qty += (float)$tho_qty;
                        $ttbl_qty += (float)$tbl_qty;
                        $tschk_qty += (float)$schk_qty;
                        $tcull_qty += (float)$cull_qty;
                        $ttdis_qty += (float)$tdis_qty;
                        $ttgis_qty += (float)$tgis_qty;
                        $ttinf_qty += (float)$tinf_qty;
                        $ttbls_qty += (float)$tbls_qty;
                        $todh_pqty += (float)$odh_pqty;
                    }
                }
            }
            $html .= '<tr class="thead2">';
            $html .= '<th colspan="4">Total</th>';
            $html .= '<th style="text-align:right;">'.$ttob_qty.'</th>';
            $html .= '<th style="text-align:right;">'.$ttrc_qty.'</th>';
            $html .= '<th style="text-align:right;">'.$tttsd_qty.'</th>';
            $html .= '<th style="text-align:right;">'.$tttsb_qty.'</th>';
            $html .= '<th style="text-align:right;">'.$ttts_qty.'</th>';
            $html .= '<th style="text-align:right;">'.$ttho_qty.'</th>';
            $html .= '<th style="text-align:right;">'.$ttbl_qty.'</th>';
            $html .= '<th style="text-align:right;">'.$tschk_qty.'</th>';
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;">'.$tcull_qty.'</th>';
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;">'.$ttdis_qty.'</th>';
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;">'.$ttgis_qty.'</th>';
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;">'.$ttinf_qty.'</th>';
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;">'.$ttbls_qty.'</th>';
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;">'.$todh_pqty.'</th>';
            $html .= '<th style="text-align:right;"></th>';
            $html .= '</tr>';
            $html .= '</tbody>';

            echo $html;
        ?>
        </table><br/><br/><br/>
        <script>
            function fetch_vensector_details(){
                var fetch_type = document.getElementById("fetch_type").value;
                removeAllOptions(document.getElementById("vendors"));
                
                var fetch_fltrs = new XMLHttpRequest();
                var method = "GET";
                var url = "breeder_fetch_vensec_master.php?fetch_type="+fetch_type;
                //window.open(url);
                var asynchronous = true;
                fetch_fltrs.open(method, url, asynchronous);
                fetch_fltrs.send();
                fetch_fltrs.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var fltr_dt1 = this.responseText;
                        $('#vendors').append(fltr_dt1);
                    }
                }
            }
        </script>
        <script type="text/javascript" src="table_sorting_wauto_slno.js"></script>
        <script type="text/javascript" src="table_search_fields.js"></script>
        <script type="text/javascript" src="table_download_excel.js"></script>
        <script type="text/javascript" src="table_column_date_format_change.js"></script>
        <script src="table_column_date_format_change.js"></script>
        <script type="text/javascript">
            function table_file_details1(){
                var dbname = '<?php echo $dbname; ?>';
                var fname = '<?php echo $wsfile_path; ?>';
                var wapp_msg = '<?php echo $file_name; ?>';
                var sms_type = '<?php echo $sms_type; ?>';
                return dbname+"[@$&]"+fname+"[@$&]"+wapp_msg+"[@$&]"+sms_type;
            }
            function table_heading_to_normal1(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_normal2(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $hhtml; ?>';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_standard_filters(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $fhtml; ?>';
                document.getElementById("head_names").innerHTML = html;
                    
                $('#export').select2();
                document.getElementById("export").value = "display";
                $('#export').select2();
                table_sort();
                table_sort2();
                table_sort3();
            }
        </script>
        <script>
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>