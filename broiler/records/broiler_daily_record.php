<?php
//broiler_daily_record.php
$requested_data = json_decode(file_get_contents('php://input'),true);


    
session_start();
    
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){

    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

    include "header_head.php";

    $user_code = $_SESSION['userid'];
}else{

    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
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
if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1'  ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2."  ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'  ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_farmer[$row['code']] = $row['farmer_code'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_gcflag[$row['code']] = $row['gc_flag']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }

$sql = "SELECT * FROM `broiler_farmer`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_name[$row['code']] = $row['name']; $farmer_mobile1[$row['code']] = $row['mobile1']; $farmer_mobile2[$row['code']] = $row['mobile2']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; $bird_name = $row['description']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; }
/*
$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }
*/
$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = $farms = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $farms = $_POST['farms'];
    $supervisors = $_POST['supervisors'];

    $farm_list = "";
    if($farms != "all"){
        $farm_query = " AND a.farm_code = '$farms'";
    }
    else if($supervisors != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else if($lines != "all"){
        foreach($farm_code as $fcode){
            if($farm_line[$fcode] == $lines){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else if($branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
    else{
        foreach($farm_code as $fcode){
            if($farm_list == ""){
                $farm_list = $fcode;
            }
            else{
                $farm_list = $farm_list."','".$fcode;
            }
        }
        $farm_query = " AND a.farm_code IN ('$farm_list')";
    }
	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/DayRecordReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&branch=".$branches."&line=".$lines."&supervisor=".$supervisors."&farm=".$farms;
}
else{
    $url = "";
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
        <?php
        if($excel_type == "print"){
            echo '<style>body { padding:10px;text-align:center; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { display:none;background-image: linear-gradient(#D5D8DC,#D5D8DC); }
            .thead2_empty_row { display:none; }
            .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
            .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
        }
        else{
            echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
            table.tbl { left:0;margin-right: auto;visibility:visible; }
            table.tbl2 { left:0;margin-right: auto; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
            .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
            .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
        }
        ?>
    </head>
    <body>
        <table class="tbl" style="width:auto;">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="10" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Live Batch Summary Report</h5></th>
                    <th colspan="16" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_daily_record.php" method="post">
                <?php } else { ?>
                <form action="broiler_daily_record.php?db=<?php echo $db; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:auto;">
                    <tr>
                        <th colspan="24">
                            <div class="row">
                                <!--<div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php //echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php //echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>-->
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2">
                                        <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
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
            <thead class="thead3" align="center">
                <tr align="center">
                    <th style="width: 50px;">S.No.</th>
                    <!--<th>Farm Code</th>-->
                    <th>Farm Name</th>
                    <th>Batch</th>
                    <th>Supervisor</th>
                    <th>Latest Entry Date</th>
                    <th>Age</th>
                    <th>Feed Opening</th>
                    <th>Feed In</th>
                    <th>Feed Out</th>
                    <th>Feed Consumption</th>
                    <th>Feed Stock</th>
                    <th>Cum. Feed</th>
                    <th>Body Wt</th>
                    <th>F.C.R</th>
                    <th>Placed Birds</th>
                    <th>Mortality</th>
                    <th>Mort%</th>
                    <th>Culls</th>
                    <th>Lifted</th>
                    <th>Balance Birds</th>
                    <th>Line</th>
                    <th>Branch</th>
                    <?php /*?><th>Farmer Name</th><?php */?>
                    <th>Contact</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
                $batch_sql = "SELECT * FROM `broiler_batch` WHERE gc_flag = '0' AND active = '1' AND dflag = '0'"; $batch_query = mysqli_query($conn,$batch_sql);
                $batch_all = "";
                while($row = mysqli_fetch_assoc($batch_query)){
                    if($batch_all == ""){
                        $batch_all = $row['code'];
                    }
                    else{
                        $batch_all = $batch_all."','".$row['code'];
                    }
                }
                $batch_sql = "SELECT a.code as batch_code,a.description as batch_name,a.farm_code as farm_code,b.description as farm_name,MAX(c.brood_age) as age,MAX(c.avg_wt) as avg_wt FROM broiler_batch a,broiler_farm b,broiler_daily_record c WHERE a.farm_code = b.code AND a.farm_code = c.farm_code".$farm_query." AND a.code IN ('$batch_all') AND c.batch_code = a.code AND a.gc_flag = '0' AND a.active = '1' AND a.dflag = '0' AND c.active = '1' AND c.dflag = '0' GROUP BY b.code ORDER BY age DESC"; $batch_query = mysqli_query($conn,$batch_sql);
                $i = 0; while($batch_row = mysqli_fetch_assoc($batch_query)){
                    $i++;
                    $batch_list[$i] = $batch_row['batch_code'];
                    $batch_age[$batch_row['batch_code']] = $batch_row['age'];
                    $batch_avg_wt[$batch_row['batch_code']] = ($batch_row['avg_wt'] / 1000);
                    $batch_farm[$batch_row['batch_code']] = $batch_row['farm_code'];
                    if($batch1 == ""){
                        $batch1 = $batch_row['batch_code'];
                    }
                    else{
                        $batch1 = $batch1."','".$batch_row['batch_code'];
                    }
                }
                $sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `code` NOT IN ('$batch1')"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $i++;
                    $batch_list[$i] = $row['code'];
                    $batch_age[$row['code']] = 0;
                    $batch_farm[$row['code']] = $row['farm_code'];
                }
                $slno = 0;
				$total_feeds_open = $total_feed_consumed = $total_feed_stock = $total_feed_cumulate = $total_bbirds = 0;
				$total_feeds_in = 0;
				$total_feeds_out = 0;
				$total_feeds_consumed = 0;
				$display_feed_stock = 0;
				$total_feed_cumulate = 0;
				$total_obirds = 0;
				$total_mort = 0;
				$total_culls = 0;
				$total_lifted = 0;
				$display_bbirds = 0;
				$bag_size = 50;
                //while($batch_row = mysqli_fetch_assoc($batch_query)){
                    foreach($batch_list as $batches){
                    //$batches = $batch_row['batch_code'];
                    //$brood_age = $batch_row['age'];
                    $brood_age = $batch_age[$batches];
                    $brood_weight = $batch_avg_wt[$batches];
                    $fetch_fcode = $batch_farm[$batches];
                    if($batches != ""){
                        $start_date = $end_date = $dend_date = $dstart_date = "";
                        $pur_qty = $sale_qty = $sold_birds = $trin_qty = $trout_qty = $medvac_qty = array();
                        $pur_chicks = $sale_chicks = $trin_chicks = $trout_chicks = $dentry_chicks = $medvac_chicks = array();

                        $sql_record = "SELECT * FROM `broiler_purchases` WHERE `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['icode']."@".$i;
                            $pur_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `broiler_sales` WHERE `farm_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['icode']."@".$i;
                            $sold_birds[$key_code] = $row['birds'];
                            $sale_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `item_stocktransfers` WHERE `to_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['code']."@".$i;
                            $trin_qty[$key_code] = $row['quantity'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `item_stocktransfers` WHERE `from_batch` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['code']."@".$i; 
                            $trout_qty[$key_code] = $row['quantity'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$i;
                            $dentry_chicks[$key_code] = $row['trnum']."@".$row['supervisor_code']."@".$row['date']."@".$row['farm_code']."@".$row['batch_code']."@".$row['brood_age']."@".$row['mortality']."@".$row['culls']."@".$row['item_code1']."@".$row['kgs1']."@".$row['item_code2']."@".$row['kgs2']."@".$row['avg_wt']."@".$row['remarks']."@".$row['addedemp']."@".$row['addedtime'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                            if($dstart_date == ""){ $dstart_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $dstart_date){ $dstart_date = strtotime($row['date']); } }
                            if($dend_date == ""){ $dend_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $dend_date){ $dend_date = strtotime($row['date']); } }
                        }
                        $sql_record = "SELECT * FROM `broiler_medicine_record` WHERE `batch_code` = '$batches' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                        $query = mysqli_query($conn,$sql_record); $i = 1;
                        while($row = mysqli_fetch_assoc($query)){
                            $key_code = $row['date']."@".$row['item_code']."@".$i;
                            $medvac_qty[$key_code] = $row['quantity'];
                            $i++;
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                    ?>
                        <tbody class="tbody1" id="myTable">
                            <?php
                            $pur_count = sizeof($pur_qty); $trin_count = sizeof($trin_qty);
                            $dentry_count = sizeof($dentry_chicks); $medvac_count = sizeof($medvac_qty);
                            $sale_count = sizeof($sold_birds); $trout_count = sizeof($trout_qty);

                            $today = date("Y-m-d");
                            $opening_date = strtotime($today."-1 days"); $close_date = strtotime($today);

                            $currentDate = $age = $open_chicks_in = $open_feeds_in = $open_culls_consume = $present_culls_consume = $open_mort_consume = $present_mort_consume = $open_feed_consume = $open_medvacs_in = $present_chicks_in = $present_feeds_in = $present_feed_consume = $present_medvacs_in = $present_birds_trout = $present_feeds_trout = $present_medvacs_trout = $open_birds_trout = $open_feeds_trout = $open_medvacs_trout = $open_birds_sale = $open_birdswt_sale = $present_birdswt_sale = $open_feeds_sale = $open_medvacs_sale = $present_medvacs_sale = $present_feeds_sale = $present_birds_sale = $present_medvacs_consume = $open_medvacs_consume = 0;
                            for ($currentDate = ((int)$start_date); $currentDate <= ((int)$end_date); $currentDate += (86400)) { $age++;
                                $prev_date = date("Y-m-d",((int)$currentDate));
                                //Purchased Quantity
                                for($i = 1;$i <= $pur_count;$i++){
                                    if($currentDate <= $opening_date){

                                        //Opening Balances
                                        if(!empty($pur_qty[$prev_date."@".$chick_code."@".$i])){
                                            $open_chicks_in = $open_chicks_in + $pur_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($pur_qty[$prev_date."@".$fcodes."@".$i])){
                                                $open_feeds_in = $open_feeds_in + $pur_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        /*foreach($medvac_code as $mvcodes){
                                            if($pur_qty[$prev_date."@".$mvcodes."@".$i] != ""){
                                                $open_medvacs_in = $open_medvacs_in + $pur_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }*/
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's Balances
                                        if(!empty($pur_qty[$prev_date."@".$chick_code."@".$i])){
                                            $present_chicks_in = $present_chicks_in + $pur_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($pur_qty[$prev_date."@".$fcodes."@".$i])){
                                                $present_feeds_in = $present_feeds_in + $pur_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        /*foreach($medvac_code as $mvcodes){
                                            if($pur_qty[$prev_date."@".$mvcodes."@".$i] != ""){
                                                $present_medvacs_in = $present_medvacs_in + $pur_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }*/
                                    }
                                    else{ }
                                }
                                //Transferred In Quantity
                                for($i = 1;$i <= $trin_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening Balances
                                        if(!empty($trin_qty[$prev_date."@".$chick_code."@".$i])){
                                            $open_chicks_in = $open_chicks_in + $trin_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($trin_qty[$prev_date."@".$fcodes."@".$i])){
                                                $open_feeds_in = $open_feeds_in + $trin_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        /*foreach($medvac_code as $mvcodes){
                                            if($trin_qty[$prev_date."@".$mvcodes."@".$i] != ""){
                                                $open_medvacs_in = $open_medvacs_in + $trin_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }*/
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's  Balances
                                        if(!empty($trin_qty[$prev_date."@".$chick_code."@".$i])){
                                            $present_chicks_in = $present_chicks_in + $trin_qty[$prev_date."@".$chick_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($trin_qty[$prev_date."@".$fcodes."@".$i])){
                                                $present_feeds_in = $present_feeds_in + $trin_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        /*foreach($medvac_code as $mvcodes){
                                            if($trin_qty[$prev_date."@".$mvcodes."@".$i] != ""){
                                                $present_medvacs_in = $present_medvacs_in + $trin_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }*/
                                    }
                                    else{ }
                                }
                                //Consume Day Record Quantity
                                for($i = 1;$i <= $dentry_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening Consumption
                                        if(!empty($dentry_chicks[$prev_date."@".$i])){
                                            $day_dtails = explode("@",$dentry_chicks[$prev_date."@".$i]);

                                            $open_mort_consume = $open_mort_consume + $day_dtails[6];
                                            $open_culls_consume = $open_culls_consume + $day_dtails[7];
                                            $open_feed_consume = $open_feed_consume + ($day_dtails[9] + $day_dtails[11]);
                                        }
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's  Balances
                                        if(!empty($dentry_chicks[$prev_date."@".$i])){
                                            $day_dtails = explode("@",$dentry_chicks[$prev_date."@".$i]);

                                            $present_mort_consume = $present_mort_consume + $day_dtails[6];
                                            $present_culls_consume = $present_culls_consume + $day_dtails[7];
                                            $present_feed_consume = $present_feed_consume + ($day_dtails[9] + $day_dtails[11]);
                                        }
                                    }
                                    else{ }
                                }
                                //Consume MedVac Record Quantity
                                /*for($i = 1;$i <= $medvac_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening MedVac Consume
                                        foreach($medvac_code as $mvcodes){
                                            if($medvac_qty[$prev_date."@".$mvcodes."@".$i] != ""){
                                                $open_medvacs_consume = $open_medvacs_consume + $medvac_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's  Balances
                                        foreach($medvac_code as $mvcodes){
                                            if($medvac_qty[$prev_date."@".$mvcodes."@".$i] != ""){
                                                $present_medvacs_consume = $present_medvacs_consume + $medvac_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }
                                    }
                                    else{ }
                                }*/
                                //Sale Quantity
                                for($i = 1;$i <= $sale_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening Sale
                                        if(!empty($sold_birds[$prev_date."@".$bird_code."@".$i])){
                                            $open_birds_sale = $open_birds_sale + $sold_birds[$prev_date."@".$bird_code."@".$i];
                                        }
                                        if(!empty($sale_qty[$prev_date."@".$bird_code."@".$i])){
                                            $open_birdswt_sale = $open_birdswt_sale + $sale_qty[$prev_date."@".$bird_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($sale_qty[$prev_date."@".$fcodes."@".$i])){
                                                $open_feeds_sale = $open_feeds_sale + $sale_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        /*foreach($medvac_code as $mvcodes){
                                            if($sale_qty[$prev_date."@".$mvcodes."@".$i] != ""){
                                                $open_medvacs_sale = $open_medvacs_sale + $sale_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }*/
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's Balances
                                        if(!empty($sold_birds[$prev_date."@".$bird_code."@".$i])){
                                            $present_birds_sale = $present_birds_sale + $sold_birds[$prev_date."@".$bird_code."@".$i];
                                        }
                                        if(!empty($sale_qty[$prev_date."@".$bird_code."@".$i])){
                                             $open_birdswt_sale = $present_birdswt_sale = $present_birdswt_sale + $sale_qty[$prev_date."@".$bird_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($sale_qty[$prev_date."@".$fcodes."@".$i])){
                                                $present_feeds_sale = $present_feeds_sale + $sale_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        /*foreach($medvac_code as $mvcodes){
                                            if($sale_qty[$prev_date."@".$mvcodes."@".$i] != ""){
                                                $present_medvacs_sale = $present_medvacs_sale + $sale_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }*/
                                    }
                                    else{ }
                                }
                                //Trout Quantity
                                for($i = 1;$i <= $trout_count;$i++){
                                    if($currentDate <= $opening_date){
                                        //Opening Sale
                                        if(!empty($trout_qty[$prev_date."@".$bird_code."@".$i])){
                                            $open_birds_trout = $open_birds_trout + $trout_qty[$prev_date."@".$bird_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($trout_qty[$prev_date."@".$fcodes."@".$i])){
                                                $open_feeds_trout = $open_feeds_trout + $trout_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        /*foreach($medvac_code as $mvcodes){
                                            if($trout_qty[$prev_date."@".$mvcodes."@".$i] != ""){
                                                $open_medvacs_trout = $open_medvacs_trout + $trout_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }*/
                                    }
                                    else if($currentDate == $close_date){
                                        //Today's Balances
                                        if(!empty($trout_qty[$prev_date."@".$bird_code."@".$i])){
                                            $present_birds_trout = $present_birds_trout + $trout_qty[$prev_date."@".$bird_code."@".$i];
                                        }
                                        foreach($feed_code as $fcodes){
                                            if(!empty($trout_qty[$prev_date."@".$fcodes."@".$i])){
                                                $present_feeds_trout = $present_feeds_trout + $trout_qty[$prev_date."@".$fcodes."@".$i];
                                            }
                                        }
                                        /*foreach($medvac_code as $mvcodes){
                                            if($trout_qty[$prev_date."@".$mvcodes."@".$i] != ""){
                                                $present_medvacs_trout = $present_medvacs_trout + $sale_qty[$prev_date."@".$mvcodes."@".$i];
                                            }
                                        }*/
                                    }
                                    else{ }
                                }
                            }
                            $display_farmcode = $farm_ccode[$fetch_fcode];
                            $display_farmname = $farm_name[$fetch_fcode];
                            $display_farbatch = $batch_name[$batches];
                            $display_supervisor = $supervisor_name[$farm_supervisor[$fetch_fcode]];
                            $display_farmer = $farmer_name[$farm_farmer[$fetch_fcode]];
                            $display_age = $display_age1 = 0;
                            /*$display_age1 = ((int)$dend_date) - ((int)$dstart_date);
                            $display_age = ($display_age1 / (60 * 60 * 24)) + 1;*/
                            $display_age = $brood_age;
                            //Display Feed Section
                            $display_feeds_open = $open_feeds_in - $open_feed_consume - $open_feeds_sale - $open_feeds_trout;
                            $display_feeds_in = $present_feeds_in;
                            $display_feed_consume = $present_feed_consume;
                            $display_feed_out = $present_feeds_sale + $present_feeds_trout;
                            $display_feed_stock = (($display_feeds_open + $display_feeds_in) - ($display_feed_consume + $display_feed_out));
                            $display_feed_cumulate = $open_feed_consume + $present_feed_consume;
                            $display_bodyWt = $brood_weight;
                            $display_obirds = $open_chicks_in + $present_chicks_in;
                            $display_mort = $open_mort_consume + $present_mort_consume;
                            if($display_obirds > 0){
                                $display_mortper = (($display_mort / $display_obirds) * 100);
                            }
                            else{
                                $display_mortper = 0;
                            }
                            $display_culls = $open_culls_consume + $present_culls_consume;
                            $display_lifted = $open_birds_sale + $present_birds_sale;
                            $display_liftedwt = $open_birdswt_sale + $present_birdswt_sale;
                            $display_bbirds = $display_obirds - $display_mort - $display_culls - $display_lifted;
                            
                            $display_fcr = (($display_feed_cumulate) / ($display_liftedwt + ($display_bodyWt *  $display_bbirds)));

                            $display_line = $line_name[$farm_line[$fetch_fcode]];
                            $display_place = $branch_name[$farm_branch[$fetch_fcode]];
                            $display_contact = $farmer_mobile1[$farm_farmer[$fetch_fcode]];
                            if($display_obirds > 0 || $present_chicks_in > 0 || $display_feeds_open > 0 || $display_feeds_in > 0 || $display_feed_stock > 0){ $slno++;
                                $total_feeds_open = $total_feeds_open + $display_feeds_open;
                                $total_feeds_in = $total_feeds_in + $display_feeds_in;
                                $total_feed_consumed += $display_feed_consume;
                                $total_feed_stock = $total_feed_stock + $display_feed_stock;
                                $total_feed_cumulate = $total_feed_cumulate + $display_feed_cumulate;
                                $total_obirds =  $total_obirds + $display_obirds;
                                $total_mort = $total_mort + $display_mort;
                                $total_culls = $total_culls + $display_culls;
                                $total_lifted = $total_lifted + $display_lifted;
                                $total_bbirds = $total_bbirds + $display_bbirds;
                            ?>
                            <tr>
                                <td title="Sl.No." style="width:50px;text-align:center;"><?php echo $slno; ?></td>
                                <!--<td title="Farm Code"><?php //echo $display_farmcode; ?></td>-->
                                <td title="Farm Name"><?php echo $display_farmname; ?></td>
                                <td title="Farm Name"><?php echo $display_farbatch; ?></td>
                                <td title="Supervisor"><?php echo $display_supervisor; ?></td>
                                <td title="Latest Entry Date"><?php if(date("d.m.Y",((int)$dend_date)) == "01.01.1970"){ echo "<b style='color:red'>Not Started</b>"; } else{ echo date("d.m.Y",((int)$dend_date)); } ?></td>
                                <td style="text-align:center;" title="Age"><?php if(date("d.m.Y",((int)$dend_date)) == "01.01.1970"){ echo "0"; } else{ echo round($display_age); } ?></td>

                                <td style="text-align:right;" title="Feed Opening"><?php echo number_format_ind(round($display_feeds_open,2)); ?></td>
                                <td style="text-align:right;" title="Feed In"><?php echo number_format_ind(round($display_feeds_in,2)); ?></td>
                                <td style="text-align:right;" title="Feed Out"><?php echo number_format_ind(round($display_feed_out,2)); ?></td>
                                <td style="text-align:right;" title="Feed Consumed"><?php echo number_format_ind(round($display_feed_consume,2)); ?></td>
                                <td style="text-align:right;" title="Feed Stock"><?php echo number_format_ind(round($display_feed_stock,2)); ?></td>
                                <td style="text-align:right;" title="Cumulative Feed"><?php echo number_format_ind(round($display_feed_cumulate,2)); ?></td>

                                <td style="text-align:right;" title="Body Weight"><?php echo number_format_ind(round($display_bodyWt,2)); ?></td>
                                <td style="text-align:right;" title="F.C.R"><?php echo number_format_ind(round($display_fcr,2)); ?></td>
                                <td style="text-align:right;" title="Opening Birds"><?php echo str_replace(".00","",number_format_ind(round($display_obirds,2))); ?></td>
                                <td style="text-align:right;" title="Mortality"><?php echo str_replace(".00","",number_format_ind(round($display_mort,2))); ?></td>
                                <td style="text-align:right;" title="Mortality %"><?php echo number_format_ind(round($display_mortper,2)); ?></td>
                                <td style="text-align:right;" title="Culls"><?php echo str_replace(".00","",number_format_ind(round($display_culls,2))); ?></td>
                                <td style="text-align:right;" title="Lifted"><?php echo str_replace(".00","",number_format_ind(round($display_lifted,2))); ?></td>
                                <td style="text-align:right;" title="Balance Birds"><?php echo str_replace(".00","",number_format_ind(round($display_bbirds,2))); ?></td>
                                
                                <td title="Line"><?php echo $display_line; ?></td>
                                <td title="Place"><?php echo $display_place; ?></td>
                                <?php /*?><td title="Place"><?php echo $display_farmer; ?></td><?php */?>
                                <td title="Contact"><?php echo $display_contact; ?></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    <?php
                    }
                }
				
            ?>
            
            <tr class="thead4">
                <th colspan="6" style="text-align:center;">Total</th>
				<th style="text-align:right;"><?php echo number_format_ind($total_feeds_open); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_feeds_in); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($display_feed_out); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_feed_consumed); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_feed_stock); ?></th>
				<th style="text-align:right;"><?php echo number_format_ind($total_feed_cumulate); ?></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_obirds)); ?></th>
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_mort)); ?></th>
				<th style="text-align:left;"></th>
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_culls)); ?></th>
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_lifted)); ?></th>
				<th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_bbirds)); ?></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
				<th style="text-align:left;"></th>
            </tr>
        <?php
            }
        ?>
        </table>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>