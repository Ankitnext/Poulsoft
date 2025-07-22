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
    global $page_title; $page_title = "Hatchery Hatch Summary Report";
    include "header_head.php";
    $form_path = "broiler_hatchery_hatchsummary1.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    global $page_title; $page_title = "Hatchery Hatch Summary Report";
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

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bfarms_list = $row['bfarms_list']; $bunits_list = $row['bunits_list']; $bsheds_list = $row['bsheds_list']; $bbatch_list = $row['bbatch_list']; $bflock_list = $row['bflock_list']; }
if($bfarms_list == "all" || $bfarms_list == ""){ $bfarms_fltr1 = $bfarms_fltr2 = ""; } else{ $bfarms_list1 = implode("','", explode(",",$bfarms_list)); $bfarms_fltr1 = " AND `code` IN ('$bfarms_list1')"; $bfarms_fltr2 = " AND `farm_code` IN ('$bfarms_list1')"; }
if($bunits_list == "all" || $bunits_list == ""){ $bunits_fltr1 = $bunits_fltr2 = ""; } else{ $bunits_list1 = implode("','", explode(",",$bunits_list)); $bunits_fltr1 = " AND `code` IN ('$bunits_list1')"; $bunits_fltr2 = " AND `unit_code` IN ('$bunits_list1')"; }
if($bsheds_list == "all" || $bsheds_list == ""){ $bsheds_fltr1 = $bsheds_fltr2 = ""; } else{ $bsheds_list1 = implode("','", explode(",",$bsheds_list)); $bsheds_fltr1 = " AND `code` IN ('$bsheds_list1')"; $bsheds_fltr2 = " AND `shed_code` IN ('$bsheds_list1')"; }
if($bbatch_list == "all" || $bbatch_list == ""){ $bbatch_fltr1 = $bbatch_fltr2 = ""; } else{ $bbatch_list1 = implode("','", explode(",",$bbatch_list)); $bbatch_fltr1 = " AND `code` IN ('$bbatch_list1')"; $bbatch_fltr2 = " AND `batch_code` IN ('$bbatch_list1')"; }
if($bflock_list == "all" || $bflock_list == ""){ $bflock_fltr1 = $bflock_fltr2 = ""; } else{ $bflock_list1 = implode("','", explode(",",$bflock_list)); $bflock_fltr1 = " AND `code` IN ('$bflock_list1')"; $bflock_fltr2 = " AND `flock_code` IN ('$bflock_list1')"; }

$sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0'".$bunits_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $unit_code = $unit_name = array();
while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0'".$bfarms_fltr2."".$bunits_fltr2."".$bsheds_fltr2."".$bbatch_fltr2."".$bflock_fltr1." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $flock_code = $flock_name = $flock_sdate = $flock_sage = $flock_batch = array();
while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['description']; $flock_sdate[$row['code']] = $row['start_date']; $flock_sage[$row['code']] = $row['start_age']; $flock_batch[$row['code']] = $row['batch_code']; }

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
//$e_cnt = sizeof($egg_code); foreach($egg_code as $eggs){ $nhtml .= '<th>'.$egg_name[$eggs].'</th>'; $fhtml .= '<th id="order_num">'.$egg_name[$eggs].'</th>'; }
$begg_list = implode("','",$egg_code);

//Fetch All Stock Received From Details
$sql = "SELECT * FROM `inv_sectors` WHERE `type` NOT IN ('$hatchery_list') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $csv_code = $ven_code = $ven_name = array();
while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['description']; $ven_type[$row['code']] = "W"; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $csv_code[$row['code']] = $row['code']; $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; $ven_type[$row['code']] = $row['contacttype']; }
asort($ven_name);

//Reject Items
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%reject%' AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $rcat_alist = array();
while($row = mysqli_fetch_assoc($query)){ $rcat_alist[$row['code']] = $row['code']; }
$rcat_list = implode("','",$rcat_alist);
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$rcat_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
$query = mysqli_query($conn,$sql); $ritem_code = $ritem_name = array();
while($row = mysqli_fetch_assoc($query)){ $ritem_code[$row['code']] = $row['code']; $ritem_name[$row['code']] = $row['description']; }

$fdate = $tdate = date("Y-m-d"); $hatcheries = $fetch_type = $vendors = "all"; $excel_type = "display"; $units = $flocks = array();
$units["all"] = $flocks["all"] = "all"; $u_aflag = $fl_aflag = 0;
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $hatcheries = $_POST['hatcheries'];
    $fetch_type = $_POST['fetch_type'];
    $vendors = $_POST['vendors'];

    $units = $flocks = array();
    foreach($_POST['units'] as $t1){ $units[$t1] = $t1; }           foreach($units as $t1){ if($t1 == "all"){ $u_aflag = 1; } }
    foreach($_POST['flocks'] as $t1){ $flocks[$t1] = $t1; }         foreach($flocks as $t1){ if($t1 == "all"){ $fl_aflag = 1; } }
    $excel_type = $_POST['export'];
}
$unit_fltr = $flock_fltr = "";
if($u_aflag == 0){ $unit_list = implode("','",$units); $unit_fltr = " AND `unit_code` IN ('$unit_list')"; }
if($fl_aflag == 0){ $flock_list = implode("','",$flocks); $flock_fltr = " AND `code` IN ('$flock_list')"; }

$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0'".$unit_fltr."".$flock_fltr." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $flock_alist = $unit_alist = $flock_unit1 = array();
while($row = mysqli_fetch_assoc($query)){ $flock_alist[$row['code']] = $row['code']; $unit_alist[$row['unit_code']] = $row['unit_code']; $flock_unit1[$row['unit_code']] = $row['code']; }

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
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
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
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="units">Unit</label>
                                    <select name="units[]" id="units" class="form-control select2" style="width:220px;" multiple onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php foreach($units as $t1){ if($t1 == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($unit_code as $bcode){ if($unit_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php foreach($units as $t1){ if($t1 == $bcode){ echo "selected"; } } ?>><?php echo $unit_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width:230px;">
                                    <label for="flocks">Flock</label>
                                    <select name="flocks[]" id="flocks" class="form-control select2" style="width:220px;" multiple onchange="fetch_flock_details(this.id);">
                                        <option value="all" <?php foreach($flocks as $t1){ if($t1 == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($flock_code as $bcode){ if($flock_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php foreach($flocks as $t1){ if($t1 == $bcode){ echo "selected"; } } ?>><?php echo $flock_name[$bcode]; ?></option>
                                        <?php } } ?>
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
            //$nhtml .= '<th>Company</th>'; $fhtml .= '<th id="order">Company</th>';
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
            foreach($ritem_code as $rcode){
                $nhtml .= '<th>'.$ritem_name[$rcode].'</th>'; $fhtml .= '<th id="order_num">'.$ritem_name[$rcode].'</th>';
                $nhtml .= '<th>'.$ritem_name[$rcode].' %</th>'; $fhtml .= '<th id="order_num">'.$ritem_name[$rcode].' %</th>';
            }
            $nhtml .= '<th>Overdue Eggs</th>'; $fhtml .= '<th id="order_num">Overdue Eggs</th>';
            $nhtml .= '<th>Hatch Date</th>'; $fhtml .= '<th id="order_num">Hatch Date</th>';
            $nhtml .= '</tr>';
            $fhtml .= '</tr>';

            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                $flk_fltr = ""; if(sizeof($flock_alist) > 0){ $flk_list = implode("','",$flock_alist); $flk_fltr = " AND `code` IN ('$flk_list')"; }
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
                        $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `unit_code` IN ('$unit_list')".$flk_fltr." AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
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
                        $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `unit_code` IN ('$unit_list')".$flk_fltr." AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $flock_alist[$row['code']] = $row['code']; }
                        $flock_list = implode("','",$flock_alist);
                    }
                }
                
                if($u_aflag == 0 || $fl_aflag == 0){ $rcvd_flag = $purt_flag = 0; }
                //Fetching Data
                $atrno_alist = $bflk_alist = $btrno_alist = $bitem_alist = $ccus_alist = $ctrno_alist = $citem_alist = $psup_alist = $ptrno_alist = $pitem_alist = array();
                if((int)$brdt_flag == 1){
                    $sql = "SELECT * FROM `item_stocktransfers` WHERE `date` <= '$tdate' AND `code` IN ('$begg_list') AND `from_flock` IN ('$flock_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $bin_oqty = $bin_bqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['from_flock']."@".$row['trnum']."@".$row['code']; $bflk_alist[$row['from_flock']] = $row['from_flock']; $atrno_alist[$row['trnum']] = $row['trnum']; $btrno_alist[$row['trnum']] = $row['trnum']; $bitem_alist[$row['code']] = $row['code'];
                        if(strtotime($row['date']) < strtotime($fdate)){ $bin_oqty[$key1] += (float)$row['quantity']; }
                        else { $bin_bqty[$key1] += (float)$row['quantity']; }
                    }
                }
                if((int)$rcvd_flag == 1){
                    $ven_fltr = ""; if(sizeof($ven_alist) > 0){ $ven_list = implode("','",$ven_alist); $ven_fltr = " AND `vcode` IN ('$ven_list')"; }
                    $sql = "SELECT * FROM `broiler_hatchery_stkreceivein` WHERE `date` <= '$tdate' AND `icode` IN ('$begg_list')".$ven_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $rin_oqty = $rin_bqty = $riu_code = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['vcode']."@".$row['trnum']."@".$row['icode']; $bflk_alist[$row['vcode']] = $row['vcode']; $atrno_alist[$row['trnum']] = $row['trnum']; $btrno_alist[$row['trnum']] = $row['trnum']; $bitem_alist[$row['icode']] = $row['icode'];
                        if(strtotime($row['date']) < strtotime($fdate)){ $bin_oqty[$key1] += (float)$row['rcvd_qty']; }
                        else { $bin_bqty[$key1] += (float)$row['rcvd_qty']; $riu_code[$row['vcode']] = $row['billno']; }
                    }
                }
                if((int)$purt_flag == 1){
                    $ven_fltr = ""; if(sizeof($ven_alist) > 0){ $ven_list = implode("','",$ven_alist); $ven_fltr = " AND `vcode` IN ('$ven_list')"; }
                    $sql = "SELECT * FROM `broiler_purchases` WHERE `date` <= '$tdate' AND `icode` IN ('$begg_list')".$ven_fltr." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn,$sql); $pin_oqty = $pin_bqty = array();
                    while($row = mysqli_fetch_assoc($query)){
                        $key1 = $row['vcode']."@".$row['trnum']."@".$row['icode']; $bflk_alist[$row['vcode']] = $row['vcode']; $atrno_alist[$row['trnum']] = $row['trnum']; $btrno_alist[$row['trnum']] = $row['trnum']; $bitem_alist[$row['icode']] = $row['icode'];
                        if(strtotime($row['date']) < strtotime($fdate)){ $bin_oqty[$key1] += ((float)$row['rcvd_qty'] + (float)$row['fre_qty']); }
                        else { $bin_bqty[$key1] += ((float)$row['rcvd_qty'] + (float)$row['fre_qty']); }
                    }
                }
                $atrno_list = implode("','",$atrno_alist);
                $sql = "SELECT * FROM `broiler_tray_settings` WHERE `link_trnum` IN ('$atrno_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `setting_date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $rmvd_oqty = $tseg_oqty = $tsde_bqty = $tsbe_bqty = $tseg_bqty = $heg_pqty = $hep_date = $tstrno_alist = $sktrno_alist = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key1 = $row['vcode']."@".$row['link_trnum']."@".$row['item_code']; $tstrno_alist[$row['trnum']] = $row['trnum']; $sktrno_alist[$row['trnum']] = $key1;
                    if(strtotime($row['setting_date']) < strtotime($fdate)){
                        $rmvd_oqty[$key1] += (float)$row['damaged_eggs'];
                        $rmvd_oqty[$key1] += (float)$row['broken_eggs'];
                        $tseg_oqty[$key1] += (float)$row['nof_egg_set'];
                    }
                    else{
                        $tsde_bqty[$key1] += (float)$row['damaged_eggs'];
                        $tsbe_bqty[$key1] += (float)$row['broken_eggs'];
                        $tseg_bqty[$key1] += (float)$row['nof_egg_set'];
                    }

                    //Check Pending for Hatch <= Today
                    if((int)$row['hatch_flag'] == 0 && strtotime($row['hatch_date']) <= strtotime($tdate)){
                        $heg_pqty[$row['vcode']] += (float)$row['nof_egg_set'];
                        $hep_date[$row['vcode']] = (float)$row['hatch_date'];
                    }
                }

                $atrno_list = implode("','",$tstrno_alist);
                $sql = "SELECT * FROM `broiler_hatchentry` WHERE `link_trnum` IN ('$atrno_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `setting_date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql); $heeg_bqty = $hecu_bqty = $hecup_bqty = $hesc_bqty = $hescp_bqty = $holtrno_alist = $hodate_alist = $hotno_alist = $hotno_vcode = array();
                while($row = mysqli_fetch_assoc($query)){
                    $key1 = $sktrno_alist[$row['link_trnum']];
                    if(strtotime($row['hatch_date']) < strtotime($fdate)){
                        $rmvd_oqty[$key1] += (float)$row['nof_egg_set'];
                    }
                    else if(strtotime($row['hatch_date']) >= strtotime($fdate) && strtotime($row['hatch_date']) <= strtotime($tdate)){
                        $heeg_bqty[$key1] += (float)$row['nof_egg_set'];
                        $hecu_bqty[$key1] += (float)$row['culls'];
                        $hecup_bqty[$key1] += (float)$row['culls_per'];
                        $hesc_bqty[$key1] += (float)$row['saleable_chicks'];
                        $hescp_bqty[$key1] += (float)$row['saleable_chicks_per'];
                    }

                    $holtrno_alist[$row['trnum']] = $row['link_trnum'];
                    $hodate_alist[$row['trnum']] = $row['hatch_date'];
                    $hotno_alist[$row['trnum']] = $row['trnum'];
                    if(empty($hotno_vcode[$row['trnum']]) && $row['vcode'] != ""){ $hotno_vcode[$row['trnum']] = $row['vcode']; }
                }
                //Hatch reject details
                $hotno_list = implode("','",$hotno_alist);
                $sql = "SELECT * FROM `broiler_hatchery_rejectitems` WHERE `link_trnum` IN ('$hotno_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `link_trnum` ASC";
                $query = mysqli_query($conn,$sql); $hrej_bqty = $hrej_bper = array();
                while($row = mysqli_fetch_assoc($query)){
                    $hatch_date = $hodate_alist[$row['link_trnum']];
                    $key1 = $sktrno_alist[$holtrno_alist[$row['link_trnum']]]."@".$row['rejection_type'];
                    if(strtotime($hatch_date) < strtotime($fdate)){
                        $rmvd_oqty[$key1] += (float)$row['reject_egg_nos'];
                    }
                    else if(strtotime($hatch_date) >= strtotime($fdate) && strtotime($hatch_date) <= strtotime($tdate)){
                        $hrej_bqty[$key1] += (float)$row['reject_egg_nos'];
                        $hrej_bper[$key1] += (float)$row['reject_egg_per'];
                    }
                }

                //calculations
                if((int)$brdt_flag == 1 || (int)$rcvd_flag == 1 || (int)$purt_flag == 1){
                    $opn_qty = $rcv_qty = $dmg_qty = $bkn_qty = $set_qty = $hot_qty = $bal_qty = array();
                    $hsc_qty = $hscp_qty = $hcu_qty = $hcup_qty = $hrej_qty = $hrej_per = $flock_alist = array();
                    foreach($bflk_alist as $fcode){
                        foreach($btrno_alist as $tcode){
                            foreach($bitem_alist as $icode){
                                $key1 = $fcode."@".$tcode."@".$icode;
                                
                                /*Opening*/
                                $i_qty = 0; if(!empty($bin_oqty[$key1]) && $bin_oqty[$key1] != ""){ $i_qty = $bin_oqty[$key1]; }
                                $o_qty = 0; if(!empty($rmvd_oqty[$key1]) && $rmvd_oqty[$key1] != ""){ $o_qty = $rmvd_oqty[$key1]; }
                                $opn_qty[$fcode] += (float)$i_qty - (float)$o_qty;
                                //echo "<br/>$key1@@@(float)$i_qty - (float)$o_qty@@@".(float)$i_qty - (float)$o_qty;
                                /*Received*/
                                $i_qty = 0; if(!empty($bin_bqty[$key1]) && $bin_bqty[$key1] != ""){ $i_qty = $bin_bqty[$key1]; }
                                $rcv_qty[$fcode] += (float)$i_qty;
                                
                                /*Damaged*/
                                $o_qty = 0; if(!empty($tsde_bqty[$key1]) && $tsde_bqty[$key1] != ""){ $o_qty = $tsde_bqty[$key1]; }
                                $dmg_qty[$fcode] += (float)$o_qty;
                                
                                /*Broken*/
                                $o_qty = 0; if(!empty($tsbe_bqty[$key1]) && $tsbe_bqty[$key1] != ""){ $o_qty = $tsbe_bqty[$key1]; }
                                $bkn_qty[$fcode] += (float)$o_qty;
                                
                                /*Tray Set*/
                                $o_qty = 0; if(!empty($tseg_bqty[$key1]) && $tseg_bqty[$key1] != ""){ $o_qty = $tseg_bqty[$key1]; }
                                $set_qty[$fcode] += (float)$o_qty;
                                
                                /*Hatch Out*/
                                $o_qty = 0; if(!empty($heeg_bqty[$key1]) && $heeg_bqty[$key1] != ""){ $o_qty = $heeg_bqty[$key1]; }
                                $hot_qty[$fcode] += (float)$o_qty;
                                
                                /*Balance*/
                                $bal_qty[$fcode] = ((float)$opn_qty[$fcode] + (float)$rcv_qty[$fcode]) - ((float)$dmg_qty[$fcode] + (float)$bkn_qty[$fcode] + (float)$hot_qty[$fcode]);
                                
                                
                                /*Chicks*/
                                $o_qty = 0; if(!empty($hesc_bqty[$key1]) && $hesc_bqty[$key1] != ""){ $o_qty = $hesc_bqty[$key1]; }
                                $hsc_qty[$fcode] += (float)$o_qty;

                                /*Chicks Per*/
                                $o_qty = 0; if(!empty($hescp_bqty[$key1]) && $hescp_bqty[$key1] != ""){ $o_qty = $hescp_bqty[$key1]; }
                                $hscp_qty[$fcode] += (float)$o_qty;

                                /*Culls*/
                                $o_qty = 0; if(!empty($hecu_bqty[$key1]) && $hecu_bqty[$key1] != ""){ $o_qty = $hecu_bqty[$key1]; }
                                $hcu_qty[$fcode] += (float)$o_qty;

                                /*Culls per*/
                                $o_qty = 0; if(!empty($hecup_bqty[$key1]) && $hecup_bqty[$key1] != ""){ $o_qty = $hecup_bqty[$key1]; }
                                $hcup_qty[$fcode] += (float)$o_qty;

                                /*Rejections*/
                                foreach($ritem_code as $rcode){
                                    $key2 = $key1."@".$rcode;
                                    $o_qty = 0; if(!empty($hrej_bqty[$key2]) && $hrej_bqty[$key2] != ""){ $o_qty = $hrej_bqty[$key2]; }
                                    $hrej_qty[$fcode."@".$rcode] += (float)$o_qty;
                                    $o_qty = 0; if(!empty($hrej_bper[$key2]) && $hrej_bper[$key2] != ""){ $o_qty = $hrej_bper[$key2]; }
                                    $hrej_per[$fcode."@".$rcode] += (float)$o_qty;
                                }

                                if((float)$opn_qty[$fcode] != 0 || (float)$rcv_qty[$fcode] != 0 || (float)$dmg_qty[$fcode] != 0 || (float)$bkn_qty[$fcode] != 0 || (float)$hot_qty[$fcode] != 0 || (float)$bal_qty[$fcode] != 0){
                                    $flock_alist[$fcode] = $fcode;
                                }
                            }
                        }
                    }
                    $flock_list = implode("','",$flock_alist);
                    $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `code` IN ('$flock_list') AND `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $flock_code = $flock_name = $flock_unit = array(); 
                    while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['description']; $flock_unit[$row['code']] = $row['unit_code']; }
                    
                    $sql = "SELECT * FROM `main_contactdetails` WHERE `code` IN ('$flock_list') AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['name']; }
                    
                    $sql = "SELECT * FROM `inv_sectors` WHERE `code` IN ('$flock_list') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['description']; }
                    asort($flock_name);

                    $sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
                    $query = mysqli_query($conn,$sql); $unit_name = array(); 
                    while($row = mysqli_fetch_assoc($query)){ $unit_name[$row['code']] = $row['description']; }
                    
                    $slno = 0; $trej_qty = array();
                    foreach($flock_name as $fcode => $fname){
                        $slno++;
                        $key1 = $fcode;
                        $ucode = $flock_unit[$fcode];
                        $uname = $unit_name[$ucode];

                        $o_qty = 0; if(!empty($opn_qty[$key1]) && $opn_qty[$key1] != ""){ $o_qty = $opn_qty[$key1]; }
                        $r_qty = 0; if(!empty($rcv_qty[$key1]) && $rcv_qty[$key1] != ""){ $r_qty = $rcv_qty[$key1]; }
                        $d_qty = 0; if(!empty($dmg_qty[$key1]) && $dmg_qty[$key1] != ""){ $d_qty = $dmg_qty[$key1]; }
                        $b_qty = 0; if(!empty($bkn_qty[$key1]) && $bkn_qty[$key1] != ""){ $b_qty = $bkn_qty[$key1]; }
                        $t_qty = 0; if(!empty($set_qty[$key1]) && $set_qty[$key1] != ""){ $t_qty = $set_qty[$key1]; }
                        $h_qty = 0; if(!empty($hot_qty[$key1]) && $hot_qty[$key1] != ""){ $h_qty = $hot_qty[$key1]; }
                        $a_qty = 0; if(!empty($bal_qty[$key1]) && $bal_qty[$key1] != ""){ $a_qty = $bal_qty[$key1]; }

                        $s_qty = 0; if(!empty($hsc_qty[$key1]) && $hsc_qty[$key1] != ""){ $s_qty = $hsc_qty[$key1]; }
                        $sp_qty = 0; if(!empty($hscp_qty[$key1]) && $hscp_qty[$key1] != ""){ $sp_qty = $hscp_qty[$key1]; }

                        $c_qty = 0; if(!empty($hcu_qty[$key1]) && $hcu_qty[$key1] != ""){ $c_qty = $hcu_qty[$key1]; }
                        $cp_qty = 0; if(!empty($hcup_qty[$key1]) && $hcup_qty[$key1] != ""){ $cp_qty = $hcup_qty[$key1]; }
                        
                        $odh_pqty = $heg_pqty[$fcode];
                        $odh_date = ""; if(!empty($hep_date[$fcode]) && $hep_date[$fcode] != ""){ $odh_date = date("d.m.Y",strtotime($hep_date[$fcode])); }

                        $html .= '<tr>';
                        $html .= '<td style="text-align:center;">'.$slno.'</td>';
                        if($uname != ""){
                            $html .= '<td>'.$uname.'</td>'; $html .= '<td>'.$fname.'</td>';
                        }
                        else{
                            $html .= '<td>'.$fname.'</td>'; $html .= '<td>'.$riu_code[$fcode].'</td>';
                        }
                        //if(!empty($csv_code[$fcode])){ $html .= '<td>'.$fname.'</td>'; $html .= '<td>'.$riu_code[$fcode].'</td>'; $html .= '<td></td>'; } else{ $html .= '<td></td>'; $html .= '<td>'.$uname.'</td>'; $html .= '<td>'.$fname.'</td>'; }
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($o_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($r_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($d_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($b_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($t_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($h_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($a_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($s_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($sp_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($c_qty)).'</td>';
                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($cp_qty)).'</td>';

                        foreach($ritem_code as $rcode){
                            $key2 = $fcode."@".$rcode;
                            $rj_qty = 0; if(!empty($hrej_qty[$key2]) && $hrej_qty[$key2] != ""){ $rj_qty = $hrej_qty[$key2]; }
                            $rjp_qty = 0; if(!empty($hrej_per[$key2]) && $hrej_per[$key2] != ""){ $rjp_qty = $hrej_per[$key2]; }

                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($rj_qty)).'</td>';
                            $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($rjp_qty)).'</td>';

                            $trej_qty[$rcode] += (float)$rj_qty;
                        }

                        $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind($odh_pqty)).'</td>';
                        $html .= '<td style="text-align:left;">'.$odh_date.'</td>';
                        $html .= '</tr>';

                        $to_qty += (float)$o_qty;
                        $tr_qty += (float)$r_qty;
                        $td_qty += (float)$d_qty;
                        $tb_qty += (float)$b_qty;
                        $tt_qty += (float)$t_qty;
                        $th_qty += (float)$h_qty;
                        $ta_qty += (float)$a_qty;

                        $ts_qty += (float)$s_qty;
                        $tc_qty += (float)$c_qty;
                        $ttdis_qty += (float)$tdis_qty;
                        $ttgis_qty += (float)$tgis_qty;
                        $ttinf_qty += (float)$tinf_qty;
                        $ttbls_qty += (float)$tbls_qty;
                        $todh_pqty += (float)$odh_pqty;
                    }
                }
            }
            $html .= '<tr class="thead2">';
            $html .= '<th colspan="3">Total</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($to_qty)).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tr_qty)).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($td_qty)).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tb_qty)).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tt_qty)).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($th_qty)).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ta_qty)).'</th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($ts_qty)).'</th>';
            $html .= '<th style="text-align:right;"></th>';
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tc_qty)).'</th>';
            $html .= '<th style="text-align:right;"></th>';
            foreach($ritem_code as $rcode){
                $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($trej_qty[$rcode])).'</th>';
                $html .= '<th style="text-align:right;"></th>';
            }
            $html .= '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($todh_pqty)).'</th>';
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
            function fetch_flock_details(a){
                var f_aflag = s_aflag = b_aflag = 1; var u_aflag = fl_aflag = 0;
                var farms = sheds = batches = "all"; var units = flocks = "";
                for(var option of document.getElementById("units").options){ if(option.selected){ if(option.value == "all"){ u_aflag = 1; } else{ if(units == ""){ units = option.value; } else{ units = units+"@"+option.value; } } } }
                for(var option of document.getElementById("flocks").options){ if(option.selected){ if(option.value == "all"){ fl_aflag = 1; } else{ if(flocks == ""){ flocks = option.value; } else{ flocks = flocks+"@"+option.value; } } } }
                if(u_aflag == 1){ units = ""; units = "all"; }
                if(fl_aflag == 1){ flocks = ""; flocks = "all"; }

                var user_code = '<?php echo $user_code; ?>';
                var ff_flag = uf_flag = sf_flag = bf_flag = fl_flag = 0;
                if(a == "units"){ uf_flag = 1; }
                else if(a == "flocks"){ fl_flag = 1; }
                else{ ff_flag = 1; }
                
                var fetch_fltrs = new XMLHttpRequest();
                var method = "GET";
                var url = "breeder_fetch_flock_filter_master.php?farms="+farms+"&units="+units+"&sheds="+sheds+"&batches="+batches+"&flocks="+flocks+"&ff_flag="+ff_flag+"&uf_flag="+uf_flag+"&sf_flag="+sf_flag+"&bf_flag="+bf_flag+"&fl_flag="+fl_flag+"&user_code="+user_code+"&fetch_type=multiple";
                //window.open(url);
                var asynchronous = true;
                fetch_fltrs.open(method, url, asynchronous);
                fetch_fltrs.send();
                fetch_fltrs.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var fltr_dt1 = this.responseText;
                        var fltr_dt2 = fltr_dt1.split("[@$&]");
                        var farm_list = fltr_dt2[0];
                        var unit_list = fltr_dt2[1];
                        var shed_list = fltr_dt2[2];
                        var batch_list = fltr_dt2[3];
                        var flock_list = fltr_dt2[4];

                        if(uf_flag == 1){
                            removeAllOptions(document.getElementById("flocks"));
                            $('#flocks').append(flock_list);
                        }
                        else{ }
                    }
                }
            }
            var f_cnt = 0;
            function set_auto_selectors(){
                if(f_cnt == 0){
                    var u_aflag = '<?php echo $u_aflag; ?>';
                    var u_val = ulist = "";
                    if(parseInt(u_aflag) == 0){
                        $('#units').select2();
                        for(var option of document.getElementById("units").options){
                            option.selected = false;
                            u_val = option.value;
                            <?php
                            foreach($units as $ulist){
                            ?>
                            ulist = ''; ulist = '<?php echo $ulist; ?>';
                            if(u_val == ulist){ option.selected = true; }
                            <?php } ?>
                        }
                        $('#units').select2();
                    }
                    var fx = "units"; fetch_flock_details(fx); f_cnt = f_cnt + 1;
                }
                else if(f_cnt == 2){
                    var fl_aflag = '<?php echo $fl_aflag; ?>';
                    var fl_val = fllist = "";
                    if(parseInt(fl_aflag) == 0){
                        $('#flocks').select2();
                        for(var option of document.getElementById("flocks").options){
                            option.selected = false;
                            fl_val = option.value;
                            <?php
                            foreach($flocks as $fllist){
                            ?>
                            fllist = ''; fllist = '<?php echo $fllist; ?>';
                            if(fl_val == fllist){ option.selected = true; }
                            <?php } ?>
                        }
                        $('#flocks').select2();
                    }
                    f_cnt = f_cnt + 1;
                }
                else{ }
                
                if(f_cnt <= 2){ setTimeout(set_auto_selectors, 300); }
            }
            set_auto_selectors();
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>