<?php
//broiler_farmwise_stock.php
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../newConfig.php";
    include "../broiler_check_tableavailability.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Farm Wise Closing Stock Report";
    include "header_head.php";
    $user_code = $_SESSION['userid'];
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Farm Wise Closing Stock Report";
    include "header_head.php";
    $user_code = $_GET['userid'];
}

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$farm_code = $farm_ccode = $farm_name = $farm_branch = $farm_line = $farm_supervisor = $farm_svr = $farm_farmer = array();
$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
}
$branch_code = $branch_name = array();
$sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$line_code = $line_name = $line_branch = array();
$sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$supervisor_code = $supervisor_name = array();
$sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$farmer_code = $farmer_name = array();
$sql = "SELECT * FROM `broiler_farmer` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_code[$row['code']] = $row['code']; $farmer_name[$row['code']] = $row['name']; $frm_mobl[$row['code']] = $row['mobile1']; }

$std_age = $std_body_weight = $std_daily_gain = $std_avg_daily_gain = $std_fcr = $std_feed_consumed = $std_cum_feed = array();
$sql = "SELECT * FROM `broiler_breedstandard` WHERE `active` = '1' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $std_age[$row['age']] = $row['age'];
    $std_body_weight[$row['age']] = $row['body_weight'];
    $std_daily_gain[$row['age']] = $row['daily_gain'];
    $std_avg_daily_gain[$row['age']] = $row['avg_daily_gain'];
    $std_fcr[$row['age']] = $row['fcr'];
    $std_feed_consumed[$row['age']] = $row['feed_consumed'];
    $std_cum_feed[$row['age']] = $row['cum_feed'];
}

$tdate = date("Y-m-d"); $branches = $lines = $supervisors = $farms = "all"; $excel_type = "display"; $report_view = "hd"; $url = ""; $sale_price = 0; $hcol_size = "35";
if(isset($_POST['submit_report']) == true){
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $sale_price = $_POST['sale_price'];
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];

    $excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/FarmWiseStockValuation-Excel.php?todate=".$tdate."&sale_price=".$sale_price."&branches=".$branches."&lines=".$lines."&supervisors=".$supervisors."&farms=".$farms;
}
$farm_list = $farmer_list = "";
if($farms != "all"){
    $farm_filter = " AND farm_code = '$farms'";
}
else if($supervisors != "all"){
    foreach($farm_code as $fcode){ if($farm_supervisor[$fcode] == $supervisors){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
}
else if($lines != "all"){
    foreach($farm_code as $fcode){ if($farm_line[$fcode] == $lines){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
}
else if($branches != "all"){
    foreach($farm_code as $fcode){ if($farm_branch[$fcode] == $branches){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
}
else{
    foreach($farm_code as $fcode){ if($farm_list == ""){ $farm_list = $fcode; } else{ $farm_list = $farm_list."','".$fcode; } }
    $farm_filter = " AND farm_code IN ('$farm_list')";
}

?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
        .thead3 th {
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
 
            }
         
        </style>
       
        <?php
          if($excel_type == "print"){
            echo '<style>body { padding:10px;text-align:center; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead2_empty_row { display:none; }
            .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
        }
        else{
            echo '<style>body { left:0;width:auto;overflow:auto;text-align:center; } table { white-space: nowrap; }
            table.tbl { left:0;margin-right: auto;visibility:visible; }
            table.tbl2 { left:0;margin-right: auto; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
            
        }

       ?>
    </head>
    <body>
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:auto;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="33" id="cname_head" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Farm Wise Closing Stock Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_farmwise_stock.php" method="post"  onsubmit="return checkval()">
                 <?php } else { ?>
                <form action="broiler_farmwise_stock.php?db=<?php echo $db; ?>" method="post" onsubmit="return checkval()">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:auto;">
                    <tr style="padding:10px;">
                        <th colspan="35" id="flt_head">
                            <div class="row">&ensp;&ensp;
                                <div class="form-group" style="width:120px;">
                                    <label>Till Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="form-group" style="width:190px;">
                                    <label for="branches">Branch</label>
                                    <select name="branches" id="branches" class="form-control select2" style="width:180px;" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="width:190px;">
                                    <label for="lines">Line</label>
                                    <select name="lines" id="lines" class="form-control select2" style="width:180px;" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="width:190px;">
                                    <label for="supervisors">Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" style="width:180px;" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="width:300px;">
                                    <label for="farms">Farm</label>
                                    <select name="farms" id="farms" class="form-control select2" style="width:290px;">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="form-group" style="width:120px;">
                                    <label>Price</label>
                                    <input type="text" name="sale_price" id="sale_price" class="form-control" style="width:110px;" value="<?php echo $sale_price; ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                
                                &ensp;
                                <div class="form-group" style="width:100px;">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
                <?php
                if(isset($_POST['submit_report']) == true){
                    $icat_code = $feed_code = $batch_code = $batch_name = $chick_in_qty = $chick_out_qty = $chick_sale_weight = $chick_sale_birds = $chick_placed_date = $sale_max_date = 
                    $chickin_min_date = $feedin_min_date = $feed_in_rate = $feed_in_qty = $feed_in_qty2 = $feed_cons_qty = $feed_cons_qty2 = $feed_out_qty = $feed_out_qty2 = $dentry_max_date = $chick_mort_qty = $chick_age = $avg_wt = array();
                    $sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0'".$farm_filter; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; }
                    $batch_list = ""; $batch_list = implode("','", $batch_code);
                    
                    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }
                    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler bird%'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }
                    
                    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; }
                    $icat_list = implode("','",$icat_code);

                    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; }
                    $feed_list = implode("','",$feed_code);
                    $hcol_size = ((sizeof($feed_code) * 3) + 18);
                    $icat_code = array();
                    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Medicine%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; }
                    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Vaccine%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; }
                    $icat_list = implode("','",$icat_code);
                    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; $medvac_name[$row['code']] = $row['description']; }
                    $medvac_list = implode("','",$medvac_code);
                    
                    //Daily Entry
                    $sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch_list') AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0'".$farm_query." ORDER BY `batch_code`,`date` ASC";
                    $query = mysqli_query($conn,$sql); $b1 = array();
                    while($row = mysqli_fetch_assoc($query)){
                        if(empty($avg_wt[$row['batch_code']])){ $avg_wt[$row['batch_code']] = $row['avg_wt']; $b1[$row['batch_code']] = $row['batch_code']; }
                        else if($row['avg_wt'] > 0){ $avg_wt[$row['batch_code']] = $row['avg_wt']; $b1[$row['batch_code']] = $row['batch_code']; }
                        else{ }
                    }

                    //Daily Entry
                    $sql = "SELECT SUM(kgs1) as kgs1,SUM(kgs2) as kgs2,SUM(mortality) as mort,SUM(culls) as culls,MAX(brood_age) as age,MAX(date) as date,batch_code FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch_list') AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $dentry_max_date[$row['batch_code']] = $row['date'];
                        $chick_mort_qty[$row['batch_code']] += ((float)$row['mort'] + (float)$row['culls']);
                        $feed_cons_qty[$row['batch_code']] += ((float)$row['kgs1'] + (float)$row['kgs2']);
                        $chick_age[$row['batch_code']] = ((int)$row['age']);
                    }
                    $sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch_list') AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `batch_code` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $feed_cons_qty2[$row['batch_code']."@".$row['item_code1']] += (float)$row['kgs1'];
                        $feed_cons_qty2[$row['batch_code']."@".$row['item_code2']] += (float)$row['kgs2'];
                    }
                    //Chick Purchase
                    $sql = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,MIN(date) as sdate,icode,farm_batch FROM `broiler_purchases` WHERE `icode` = '$chick_code' AND `farm_batch` IN ('$batch_list') AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `farm_batch` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['farm_batch'];
                        $chick_in_qty[$key_code] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        $chickin_min_date[$key_code] = $row['sdate'];
                    }
                    //Chick Transfer IN
                    $sql_record = "SELECT SUM(quantity) as quantity,MIN(date) as sdate,code,to_batch FROM `item_stocktransfers` WHERE `code` = '$chick_code' AND `to_batch` IN ('$batch_list') AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch` ORDER BY `to_batch` ASC";
                    $query = mysqli_query($conn,$sql_record);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['to_batch'];
                        $chick_in_qty[$key_code] += ((float)$row['quantity']);
                        $chickin_min_date[$key_code] = $row['sdate'];
                    }

                    //Feed Purchase
                    $sql = "SELECT SUM(item_tamt) as amount,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,AVG(rate) as price,MIN(date) as sdate,icode,farm_batch FROM `broiler_purchases` WHERE `icode` IN ('$feed_list') AND `date` <= '$tdate' AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch`,`icode` ORDER BY `farm_batch`,`icode` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['farm_batch'];
                        $feed_in_qty[$key_code] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        $feed_in_rate[$key_code."@".$row['icode']] = ((float)$row['price']);
                        $feed_in_amt[$key_code."@".$row['icode']] += ((float)$row['amount']);
                        $feed_in_qty2[$key_code."@".$row['icode']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        $feedin_min_date[$key_code] = $row['sdate'];
                    }
                    //Feed Transfer IN
                    $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,AVG(price) as price,MIN(date) as sdate,code,to_batch FROM `item_stocktransfers` WHERE `code` IN ('$feed_list') AND `date` <= '$tdate' AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch`,`code` ORDER BY `to_batch`,`code` ASC";
                    $query = mysqli_query($conn,$sql_record);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['to_batch'];
                        $feed_in_qty[$key_code] += ((float)$row['quantity']);
                        $feed_in_rate[$key_code."@".$row['code']] = ((float)$row['price']);
                        $feed_in_amt[$key_code."@".$row['code']] += ((float)$row['amount']);
                        $feed_in_qty2[$key_code."@".$row['code']] += ((float)$row['quantity']);
                        $feedin_min_date[$key_code] = $row['sdate'];
                    }
                    //Medvac Purchase
                    $$medvac_in_qty = $$medvac_in_amt = array();
                    $sql = "SELECT SUM(item_tamt) as amount,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,AVG(rate) as price,MIN(date) as sdate,icode,farm_batch FROM `broiler_purchases` WHERE `icode` IN ('$medvac_list') AND `date` <= '$tdate' AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch`,`icode` ORDER BY `farm_batch`,`icode` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['farm_batch']."@".$row['icode'];
                        $medvac_in_amt[$key_code] += ((float)$row['amount']);
                        $medvac_in_qty[$key_code] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                    }
                    //Medvac Transfer IN
                    $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,AVG(price) as price,MIN(date) as sdate,code,to_batch FROM `item_stocktransfers` WHERE `code` IN ('$medvac_list') AND `date` <= '$tdate' AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch`,`code` ORDER BY `to_batch`,`code` ASC";
                    $query = mysqli_query($conn,$sql_record);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['to_batch']."@".$row['code'];
                        $medvac_in_amt[$key_code] += ((float)$row['amount']);
                        $medvac_in_qty[$key_code] += ((float)$row['quantity']);
                    }
                    //Medvac Consumption
                    $sql = "SELECT * FROM `broiler_medicine_record` WHERE `batch_code` IN ('$batch_list') AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `batch_code` ASC";
                    $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['batch_code']."@".$row['item_code']; $t1 = $t2 = 0;
                        if(!empty($medvac_in_qty[$key_code]) && (float)$medvac_in_qty[$key_code] != 0){
                            $t1 = (float)$medvac_in_amt[$key_code] / (float)$medvac_in_qty[$key_code];
                            $t2 = (float)$t1 * (float)$row['quantity'];
                        }
                        $medvac_cons_amt[$row['batch_code']] += (float)$t2;
                    }
                    //Chick Sale
                    $sql_record = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,MAX(date) as edate,icode,farm_batch FROM `broiler_sales` WHERE `icode` = '$bird_code' AND `date` <= '$tdate' AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `farm_batch` ASC";
                    $query = mysqli_query($conn,$sql_record);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['farm_batch'];
                        $chick_out_qty[$key_code] += ((float)$row['birds']);
                        $chick_sale_birds[$key_code] += ((float)$row['birds']);
                        $chick_sale_weight[$key_code] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        $sale_max_date[$key_code] = $row['edate'];
                    }
                     //Bird Sending
                     if($count131 > 0){
                        $sql_record = "SELECT SUM(birds) as birds,SUM(`weight`) as rcd_qty,MAX(date) as edate,item_code as icode,from_batch FROM `broiler_bird_transferout` WHERE `item_code` = '$bird_code' AND `date` <= '$tdate' AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch` ORDER BY `from_batch` ASC";
                        $query = mysqli_query($conn,$sql_record);
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['from_batch'];
                            $chick_out_qty[$key_code] = (float)$chick_out_qty[$key_code] + ((float)$row['birds']);
                            $chick_sale_birds[$key_code] = (float)$chick_sale_birds[$key_code] + ((float)$row['birds']);
                            $chick_sale_weight[$key_code] = (float)$chick_sale_weight[$key_code] + ((float)$row['rcd_qty']);
                            $sale_max_date[$key_code] = $row['edate'];
                        }
                     }
                     
                    //Chick Transfer Out
                    $sql_record = "SELECT SUM(quantity) as quantity,MIN(date) as sdate,code,from_batch FROM `item_stocktransfers` WHERE `code` = '$bird_code' AND `from_batch` IN ('$batch_list') AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch` ORDER BY `to_batch` ASC";
                    $query = mysqli_query($conn,$sql_record);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['from_batch'];
                        $chick_out_qty[$key_code] += ((float)$row['quantity']);
                    }
                    //Feed Sale
                    $sql_record = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,MAX(date) as edate,icode,farm_batch FROM `broiler_sales` WHERE `icode` IN ('$feed_list') AND `date` <= '$tdate' AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch`,`icode` ORDER BY `farm_batch`,`icode` ASC";
                    $query = mysqli_query($conn,$sql_record);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['farm_batch'];
                        $feed_out_qty[$key_code] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        $feed_out_qty2[$key_code."@".$row['code']] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                    }
                    //Feed Transfer Out
                    $sql_record = "SELECT SUM(quantity) as quantity,MAX(date) as edate,code,from_batch FROM `item_stocktransfers` WHERE `code` IN ('$feed_list') AND `from_batch` IN ('$batch_list') AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch`,`code` ORDER BY `from_batch`,`code` ASC";
                    $query = mysqli_query($conn,$sql_record);
                    while($row = mysqli_fetch_assoc($query)){
                        $key_code = $row['from_batch'];
                        $feed_out_qty[$key_code] += ((float)$row['quantity']);
                        $feed_out_qty2[$key_code."@".$row['code']] += ((float)$row['quantity']);
                        $feedout_max_date[$key_code] = $row['edate'];
                    }
                    $batch_farm1 = array();
                    $sql = "SELECT * FROM `broiler_batch` WHERE `code` IN ('$batch_list') AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; $batch_farm1[$row['code']] = $row['farm_code']; $batch_farm2[$row['farm_code']] = $row['code']; }
                    $farm_list = ""; $farm_list = implode("','", $batch_farm1);

                    $f1 = array();
                    $sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$farm_list') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($query)){ $f1[$row['code']] = $row['code']; }

                    $fitem_count = sizeof($feed_code);
                ?>
                <thead class="thead3" align="center">
                    <tr align="left">
                        <th colspan="15"></th>
                        <th colspan="<?php echo $fitem_count; ?>">Feed Consumed Amt</th>
                        <th colspan="<?php echo $fitem_count; ?>">Feed Stock in Bags</th>
                        <th colspan="<?php echo $fitem_count; ?>">Feed Rates</th>
                        <th colspan="3"></th>
                    </tr>
                </thead>
                <thead class="thead3" align="center">
                    <tr align="center">
                        <th id="order_num">Sl.No.</th>
                        <th id="order_date">Houese Date</th>
                        <th id="order">Branch Name</th>
                        <th id="order">Farm Name & Village</th>
                        <th id="order">Batch</th>
                        <th id="order_num">Age</th>
                        <th id="order_num">Housed Chicks</th>
                        <th id="order_num">Mortality</th>
                        <th id="order_num">Sale Birds</th>
                        <th id="order_num">Balance Birds</th>
                        <th id="order_num">Std. Weight</th>
                        <th id="order_num">Avg. Weight</th>
                        <th id="order_num">Total Weight</th>
                        <th id="order_num">Price</th>
                        <th id="order_num">Amount</th>
                        <?php
                        foreach($feed_code as $icode){ echo "<th id='order_num'>".$feed_name[$icode]."</th>"; }
                        foreach($feed_code as $icode){ echo "<th id='order_num'>".$feed_name[$icode]."</th>"; }
                        foreach($feed_code as $icode){ echo "<th id='order_num'>".$feed_name[$icode]."</th>"; }
                        ?>
                        <th id="order_num">Medicine Consumed Amt</th>
                        <th id="order_num">Amount</th>
                        <th id="order_num">Total Value</th>
                    </tr>
                </thead>
                <tbody class="tbody1" id="tbody1">
                <?php
                $slno = 0; $today = date("Y-m-d");
                foreach($f1 as $fcode){
                    $bcode = ""; $bcode = $batch_farm2[$fcode];
                    if(date("d.m.Y",strtotime($chickin_min_date[$bcode])) != "01.01.1970" || date("d.m.Y",strtotime($feedin_min_date[$bcode])) != "01.01.1970"){
                        if(!empty($dentry_max_date[$bcode])){ $last_dentry_date = date("d.m.Y",(strtotime($dentry_max_date[$bcode]))); } else{ $last_dentry_date = "01.01.1970"; }
                        if(!empty($sale_max_date[$bcode])){ $last_sale_date = date("d.m.Y",(strtotime($sale_max_date[$bcode]))); } else{ $last_sale_date = "01.01.1970"; }
                        if(!empty($feedout_max_date[$bcode])){ $last_feedout_date = date("d.m.Y",(strtotime($feedout_max_date[$bcode]))); } else{ $last_feedout_date = "01.01.1970"; }

                        $latest_date = ""; $latest_date = MAX(strtotime($last_dentry_date),strtotime($last_sale_date),strtotime($last_feedout_date));
                        $days = (INT)((strtotime($today) - (INT)($latest_date)) / 60 / 60 / 24);

                        if(empty($chick_in_qty[$bcode])){ $chick_in_qty[$bcode] = 0; } if(empty($chick_mort_qty[$bcode])){ $chick_mort_qty[$bcode] = 0; } if(empty($chick_out_qty[$bcode])){ $chick_out_qty[$bcode] = 0; }
                        if(empty($feed_in_qty[$bcode])){ $feed_in_qty[$bcode] = 0; } if(empty($feed_cons_qty[$bcode])){ $feed_cons_qty[$bcode] = 0; } if(empty($feed_out_qty[$bcode])){ $feed_out_qty[$bcode] = 0; }
                        if(empty($feed_out_qty2[$bcode])){ $feed_out_qty2[$bcode] = 0; }

                        $display_available_birds = ((float)$chick_in_qty[$bcode] - ((float)$chick_mort_qty[$bcode] + (float)$chick_out_qty[$bcode]));
                        if(!empty($chick_age[$bcode])){ $age = (INT)$chick_age[$bcode]; } else{ $age = 0; }
                        //if($days > 3 && (INT)$display_available_birds <= 100 && $age > 0){
                            $slno++;
                            
                            $display_available_feed = ((float)$feed_in_qty[$bcode] - ((float)$feed_cons_qty[$bcode] + (float)$feed_out_qty[$bcode]));
                            $title1 = ""; $title1 = "$display_available_birds = ((float)$chick_in_qty[$bcode] - ((float)$chick_mort_qty[$bcode] + (float)$chick_out_qty[$bcode]))";
                            $title2 = ""; $title2 = "$display_available_feed = ((float)$feed_in_qty[$bcode] - ((float)$feed_cons_qty[$bcode] + (float)$feed_out_qty[$bcode]))";
                            
                            if(!empty($avg_wt[$bcode])){ $abody_wt = round(((float)$avg_wt[$bcode] / 1000),2); } else{ $abody_wt = 0; }
                            $total_weight = (float)$display_available_birds * (float)$abody_wt;

                            $sbody_wt = round(((float)$std_body_weight[$age] / 1000),2);
                            $total_amount = 0; $total_amount = round(((float)$sale_price * ((float)$total_weight)),2);

                            if(date("d.m.Y",strtotime($chickin_min_date[$bcode])) != "01.01.1970"){
                                $housed_date = date("d.m.Y",strtotime($chickin_min_date[$bcode]));
                            }
                            else{
                                $housed_date = "";
                            }
                        ?>
                        <tr>
                            <td title="Sl.No." style="text-align:center;"><?php echo $slno; ?></td>
                            <td title="Houese Date"><?php echo $housed_date; ?></td>
                            <td title="Branch Name"><?php echo $branch_name[$farm_branch[$fcode]]; ?></td>
                            <td title="Farm Name & Village"><?php echo $farm_name[$fcode]." - ".$line_name[$farm_line[$fcode]]; ?></td>
                            <td title="Batch"><?php echo $batch_name[$bcode]; ?></td>
                            <td title="Age" style="text-align:center;"><?php echo $age; ?></td>
                            <td title="Housed Chicks" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($chick_in_qty[$bcode])); ?></td>
                            <td title="Mortality" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($chick_mort_qty[$bcode])); ?></td>
                            <td title="Sale Birds" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($chick_sale_birds[$bcode])); ?></td>
                            <td title="Balance Birds" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($display_available_birds)); ?></td>
                            <td title="Std. Weight" style="text-align:right;"><?php echo number_format_ind($sbody_wt); ?></td>
                            <td title="Avg. Weight" style="text-align:right;"><?php echo number_format_ind($abody_wt); ?></td>
                            <td title="Total Weight" style="text-align:right;"><?php echo number_format_ind($total_weight); ?></td>
                            <td title="Price" style="text-align:right;"><?php echo number_format_ind($sale_price); ?></td>
                            <td title="Amount" style="text-align:right;"><?php echo number_format_ind($total_amount); ?></td>
                            <?php
                            foreach($feed_code as $icode){
                                $key = ""; $key = $bcode."@".$icode; $t2 = 0;
                                if(!empty($feed_cons_qty2[$key]) && ((float)$feed_cons_qty2[$key]) != 0){
                                    $t2 = 0; $t2 = (float)$feed_cons_qty2[$key];
                                    $tot_feed_amt[$icode] += (float)$t2;
                                }
                            ?>
                                <td title="<?php echo $feed_name[$fcode]; ?>" style="text-align:right;"><?php echo number_format_ind($t2); ?></td>
                            <?php } ?>
                            <?php
                            foreach($feed_code as $icode){
                                $key = ""; $key = $bcode."@".$icode; $feed_stock = 0;
                                $feed_stock = round(((float)$feed_in_qty2[$key] - ((float)$feed_cons_qty2[$key] + (float)$feed_out_qty2[$key])),2);
                                $tot_feed_stock_qty[$icode] += (float)$feed_stock;
                            ?>
                                <td title="<?php echo $feed_name[$fcode]; ?>" style="text-align:right;"><?php echo number_format_ind($feed_stock); ?></td>
                            <?php } ?>
                            <?php
                            $batch_feed_total_amt = 0;
                            foreach($feed_code as $icode){
                                $key = ""; $key = $bcode."@".$icode; $feed_stock = $feed_rate = 0;
                                $feed_stock = round(((float)$feed_in_qty2[$key] - ((float)$feed_cons_qty2[$key] + (float)$feed_out_qty2[$key])),2);
                                $feed_rate = round($feed_in_rate[$key],2);
                                $batch_feed_total_amt += ((float)$feed_stock * (float)$feed_rate);

                            ?>
                                <td title="<?php echo $feed_name[$fcode]; ?>" style="text-align:right;"><?php echo number_format_ind($feed_rate); ?></td>
                            <?php
                            }
                            $rtotal_amt = 0;
                            $rtotal_amt = round((float)$total_amount + (float)$batch_feed_total_amt,2);
                            ?>
                            <td title="Amount" style="text-align:right;"><?php echo number_format_ind(round($medvac_cons_amt[$bcode],2)); ?></td>
                            <td title="Amount" style="text-align:right;"><?php echo number_format_ind(round($batch_feed_total_amt,2)); ?></td>
                            <td title="Total Value" style="text-align:right;color:green;font-weight:bold;"><?php echo number_format_ind($rtotal_amt); ?></td>
                        </tr>
                        <?php
                            $tot_feeds += (float)$display_available_feed;
                            $tot_birds += (float)$display_available_birds;
                        //}
                        
                        //$tcount = 17 + $fitem_count + $fitem_count;
                        $tot_in_chicks += (float)$chick_in_qty[$bcode];
                        $tot_mort_chicks += (float)$chick_mort_qty[$bcode];
                        $tot_sale_chicks += (float)$chick_sale_birds[$bcode];
                        $tot_avail_birds += (float)$display_available_birds;
                        $tot_bird_weight += (float)$total_weight;
                        $tot_bird_amount += (float)$total_amount;
                        $tot_medvac_amount += (float)$medvac_cons_amt[$bcode];
                        $tot_feed_amount += (float)$batch_feed_total_amt;
                        $tot_final_amount += (float)$rtotal_amt;
                    }
                }
                ?>
                </tbody>
                <thead class="thead2">
                    <tr>
                        <td style="text-align:right;" title="total" colspan="6"><b>Total</b></td>
                        <td style="text-align:right;"><b><?php echo str_replace(".00","",number_format_ind($tot_in_chicks)); ?></b></td>
                        <td style="text-align:right;"><b><?php echo str_replace(".00","",number_format_ind($tot_mort_chicks)); ?></b></td>
                        <td style="text-align:right;"><b><?php echo str_replace(".00","",number_format_ind($tot_sale_chicks)); ?></b></td>
                        <td style="text-align:right;"><b><?php echo str_replace(".00","",number_format_ind($tot_avail_birds)); ?></b></td>
                        <td style="text-align:right;"></b></td>
                        <td style="text-align:right;"></b></td>
                        <td style="text-align:right;"><b><?php echo number_format_ind($tot_bird_weight); ?></b></td>
                        <td style="text-align:right;"></b></td>
                        <td style="text-align:right;"><b><?php echo number_format_ind($tot_bird_amount); ?></b></td>
                        <?php foreach($feed_code as $icode){ ?><td style="text-align:right;"><b><?php echo number_format_ind(round($tot_feed_amt[$icode],2)); ?></b></td><?php } ?>
                        <?php foreach($feed_code as $icode){ $tot_feed_stock_qty[$icode] += (float)$feed_stock; ?><td style="text-align:right;"><b><?php echo number_format_ind($tot_feed_stock_qty[$icode]); ?></b></td><?php } ?>
                        <?php foreach($feed_code as $icode){ ?><td style="text-align:right;"></td><?php } ?>
                        <td style="text-align:right;"><b><?php echo number_format_ind($tot_medvac_amount); ?></b></td>
                        <td style="text-align:right;"><b><?php echo number_format_ind($tot_feed_amount); ?></b></td>
                        <td style="text-align:right;color:green;font-weight:bold;"><b><?php echo number_format_ind($tot_final_amount); ?></b></td>
                    </tr>
                </thead>
            <?php
                }
            ?>
        </table>
        <script>
            function checkval(){
                var body_weight = document.getElementById("body_weight").value;
                var age = document.getElementById("age").value;
                if(body_weight == '0' && age == '0' ){
                    alert('Please Enter Age/Body Weight');
                    return false;
                }
                else{
                    return true;
                }
            }

            function fetch_farms_details(a){
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                var supervisors = document.getElementById("supervisors").value;

                if(a.match("branches")){
                    if(!branches.match("all")){
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                                $b_code = $line_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $b_code = $farm_branch[$f_code];
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $fcode; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Line Details
                        removeAllOptions(document.getElementById("lines"));
                        myselect1 = document.getElementById("lines");
                        theOption1=document.createElement("OPTION");
                        theText1=document.createTextNode("-All-");
                        theOption1.value = "all"; 
                        theOption1.appendChild(theText1); 
                        myselect1.appendChild(theOption1);
                        <?php
                            foreach($line_code as $fcode){
                        ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $line_name[$fcode]; ?>");
                            theOption1.value = "<?php echo $line_code[$fcode]; ?>";
                            theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                        <?php
                            }
                        ?>
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode];
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("lines")){
                    if(!lines.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $l_code = $farm_line[$f_code];
                                echo "if(lines == '$l_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $l_code = $farm_line[$fcode];
                                echo "if(lines == '$l_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else if(!branches.match("all")){
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode]; $b_code = $farm_branch[$f_code];
                                echo "if(branches == '$b_code' && '$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                                $b_code = $farm_branch[$fcode];
                                echo "if(branches == '$b_code'){";
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            echo "}";
                            }
                        ?>
                    }
                    else{
                        //Update Supervisor Details
                        removeAllOptions(document.getElementById("supervisors"));
                        myselect2 = document.getElementById("supervisors");
                        theOption2=document.createElement("OPTION");
                        theText2=document.createTextNode("-All-");
                        theOption2.value = "all"; 
                        theOption2.appendChild(theText2); 
                        myselect2.appendChild(theOption2);
                        <?php
                            foreach($supervisor_code as $fcode){
                                $f_code = $farm_svr[$fcode];
                                echo "if('$f_code' != ''){";
                        ?>
                            theOption2=document.createElement("OPTION");
                            theText2=document.createTextNode("<?php echo $supervisor_name[$fcode]; ?>");
                            theOption2.value = "<?php echo $supervisor_code[$fcode]; ?>";
                            theOption2.appendChild(theText2); myselect2.appendChild(theOption2);
                        <?php
                            echo "}";
                            }
                        ?>
                        //Update Farm Details
                        removeAllOptions(document.getElementById("farms"));
                        myselect3 = document.getElementById("farms");
                        theOption3=document.createElement("OPTION");
                        theText3=document.createTextNode("-All-");
                        theOption3.value = "all"; 
                        theOption3.appendChild(theText3); 
                        myselect3.appendChild(theOption3);
                        <?php
                            foreach($farm_code as $fcode){
                        ?>
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                            theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                            theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                        <?php
                            }
                        ?>
                    }
                }
                else if(a.match("supervisors")){
                    if(!supervisors.match("all")){
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(lines == '$l_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode]; $s_code = $farm_supervisor[$fcode];
                                    echo "if(branches == '$b_code' && supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $s_code = $farm_supervisor[$fcode];
                                    echo "if(supervisors == '$s_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                    }
                    else{
                        if(!lines.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $l_code = $farm_line[$fcode];
                                    echo "if(lines == '$l_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else if(!branches.match("all")){
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                                    $b_code = $farm_branch[$fcode];
                                    echo "if(branches == '$b_code'){";
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                echo "}";
                                }
                            ?>
                        }
                        else{
                            //Update Farm Details
                            removeAllOptions(document.getElementById("farms"));
                            myselect3 = document.getElementById("farms");
                            theOption3=document.createElement("OPTION");
                            theText3=document.createTextNode("-All-");
                            theOption3.value = "all"; 
                            theOption3.appendChild(theText3); 
                            myselect3.appendChild(theOption3);
                            <?php
                                foreach($farm_code as $fcode){
                            ?>
                                theOption3=document.createElement("OPTION");
                                theText3=document.createTextNode("<?php echo $farm_name[$fcode]; ?>");
                                theOption3.value = "<?php echo $farm_code[$fcode]; ?>";
                                theOption3.appendChild(theText3); myselect3.appendChild(theOption3);
                            <?php
                                }
                            ?>
                        }
                    }
                }
                else{ }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>

<script>
            function table_sort() {
		        console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;
                        const b_val = b.children[index].innerText;
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }
            function convertDate(d){ var p = d.split("."); return (p[2]+p[1]+p[0]); }
            function table_sort3() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_date').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_date').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }

            function convertNumber(d) { var p = intval(d); return (p); }

            function table_sort2() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_num').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_num').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    
                    var arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;    
                        if(isNaN(a_val)){
                        a_val1 = a_val.split(',').join(''); }
                        else {
                            a_val1 = a_val; }
                        const b_val = b.children[index].innerText;
                        if(isNaN(b_val)){
                        b_val1 = b_val.split(',').join('');}
                        else {
                            b_val1 = b_val; }
                        return (asc) ? b_val1 - a_val1:  a_val1 - b_val1 
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
                
            }
            function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script>
            function update_colspan(){
                var hcol_size = '<?php echo $hcol_size; ?>';
                var head1 = parseFloat(hcol_size) - 2;
                var head2 = parseFloat(hcol_size);
                document.getElementById("cname_head").colSpan = head1;
                document.getElementById("flt_head").colSpan = head2;
            }
            update_colspan();
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>