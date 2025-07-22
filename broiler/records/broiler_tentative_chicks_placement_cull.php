<?php
//broiler_batch_closed.php
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Tentative Chicks Placement Cull Report";
    include "header_head.php";
    $user_code = $_SESSION['userid'];
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Tentative Chicks Placement Cull Report";
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


$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row  = mysqli_fetch_assoc($query)){ 
    $branch_code[$row['code']] = $row['code']; 
    $branch_name[$row['code']] = $row['description']; 
}
$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code'];
	$farm_capacity[$row['code']] = $row['farm_capacity'];
}

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_gcflag[$row['code']] = $row['gc_flag']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `dflag` = '0' and gc_flag  = 0 ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $curr_batch_code[$row['farm_code']] = $row['code']; $curr_batch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_breedstandard` WHERE `dflag` = '0' ORDER BY `age` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bstd_body_weight[$row['age']] = $row['body_weight']; $bstd_daily_gain[$row['age']] = $row['daily_gain']; $bstd_avg_daily_gain[$row['age']] = $row['avg_daily_gain']; $bstd_fcr[$row['age']] = $row['fcr']; $bstd_cum_feed[$row['age']] = $row['cum_feed']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%super%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }

$sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `main_access`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; }

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

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ if( $item_cat = ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_codes[$row['code']] = $row['code']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler bird%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_codes[$row['code']] = $row['code']; }

$chick_placed_date = array();

//Transfer IN From Warehouse to Farm
$sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,MIN(date) as sdate,MAX(date) as edate,code,to_batch FROM `item_stocktransfers` WHERE  `active` = '1' AND `dflag` = '0' GROUP BY `to_batch`,`code` ORDER BY `date`,`trnum` ASC"; $query = mysqli_query($conn,$sql_record);
while($row = mysqli_fetch_assoc($query)){
    $key_code = $row['to_batch']."@".$row['code'];
    if(!empty($chick_codes[$row['code']]) || !empty($bird_codes[$row['code']])){
        $sector_trin_bird_qty[$key_code] = $row['quantity'];
        if(empty($chick_placed_date[$row['to_batch']])){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['to_batch']]){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); } }
    }
}

//Transfer IN From Farm to Farm
$sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,MIN(date) as sdate,MAX(date) as edate,code,to_batch FROM `item_stocktransfers` WHERE  `active` = '1' AND `dflag` = '0' GROUP BY `to_batch`,`code` ORDER BY `date`,`trnum` ASC"; $query = mysqli_query($conn,$sql_record);
while($row = mysqli_fetch_assoc($query)){
$key_code = $row['to_batch']."@".$row['code'];
    if(!empty($chick_codes[$row['code']]) || !empty($bird_codes[$row['code']])){
        $farm_trin_bird_qty[$key_code] = $row['quantity'];
        if(empty($chick_placed_date[$row['to_batch']])){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['to_batch']]){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); } }
    }
}

$sql_record = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as item_tamt,MIN(date) as sdate,MAX(date) as edate,icode,farm_batch FROM `broiler_purchases` WHERE  `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch`,`icode` ORDER BY `date`,`trnum` ASC"; $query = mysqli_query($conn,$sql_record);
while($row = mysqli_fetch_assoc($query)){
$key_code = $row['farm_batch']."@".$row['icode'];
    if(!empty($chick_codes[$row['icode']])){
        $pur_chick_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
        if(empty($chick_placed_date[$row['farm_batch']])){ $chick_placed_date[$row['farm_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['farm_batch']]){ $chick_placed_date[$row['farm_batch']] = strtotime($row['sdate']); } }
    }
}
//Day record
$sql_record = "SELECT SUM(mortality) as mortality,SUM(culls) as culls,SUM(kgs1) as kgs1,SUM(kgs2) as kgs2,MIN(date) as sdate,MAX(date) as edate,MAX(brood_age) as brood_age,batch_code,supervisor_code FROM `broiler_daily_record` WHERE `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY brood_age DESC"; $query = mysqli_query($conn,$sql_record); $i = 1;
while($row = mysqli_fetch_assoc($query)){
    $dentry_min_date[$key_code] = $row['sdate'];
}

$fdate = $tdate = date("Y-m-d"); $branches = $lines = $supervisors = $farms = "all"; $excel_type = "display"; $report_view = "hd";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $report_view = $_POST['report_view'];
    $farmstatus = $_POST['farmstatus'];

    //echo "Branch:".$farm_branch['FRM-0128'];
    /*$farm_list = "";
    if($farms != "all"){
        $farm_list = $farms;
        $farm_query = " AND farm_code IN ('$farm_list')";
    }
    else if($supervisors != "all" && $lines != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors && $farm_line[$fcode] == $lines){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND farm_code IN ('$farm_list')";
    }
    else if($supervisors != "all" && $branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors && $farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND farm_code IN ('$farm_list')";
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
        $farm_query = " AND farm_code IN ('$farm_list')";
    }
    else if($lines != "all" && $branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_line[$fcode] == $lines && $farm_branch[$fcode] == $branches){
                if($farm_list == ""){
                    $farm_list = $fcode;
                }
                else{
                    $farm_list = $farm_list."','".$fcode;
                }
            }
        }
        $farm_query = " AND farm_code IN ('$farm_list')";
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
        $farm_query = " AND farm_code IN ('$farm_list')";
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
        $farm_query = " AND farm_code IN ('$farm_list')";
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
        $farm_query = " AND farm_code IN ('$farm_list')";
    }*/

    if($branches != "all"){
        $farm_query = " AND branch_code IN ('$branches') ";
    }
    else{
        $farm_query = "";
    }
    if($lines != "all"){
        $farm_query .= " AND line_code IN ('$lines') ";
    }
    else{
        $farm_query .= "";
    }
    if($farms != "all"){
        $farm_query .= " AND farm_code IN ('$farms') ";
    }
    else{
        $farm_query .= "";
    }
    $excel_type = $_POST['export'];
    $url = "../PHPExcel/Examples/BroilerWeeklyReport-Excel.php?branches=".$branches."&lines=".$lines."&supervisors=".$supervisors."&farms=".$farms;
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
            echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
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
        <table class="tbl" style="width:auto;">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="7" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Tentative Chicks Placement Cull Report</h5></th>
                    <th colspan="2" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_tentative_chicks_placement_cull.php" method="post">
                 <?php } else { ?>
                <form action="broiler_tentative_chicks_placement_cull.php?db=<?php echo $db; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="11">
                            <div class="row">
                               <!--  <div class="m-2 form-group">
                                    <label>Report View</label>
                                    <select name="report_view" id="report_view" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="hd" <?php if($report_view == "hd"){ echo "selected"; } ?>>Housed Date</option>
                                        <option value="ld" <?php if($report_view == "ld"){ echo "selected"; } ?>>Liquidation Date</option>
                                        <option value="gd" <?php if($report_view == "gd"){ echo "selected"; } ?>>GC Saved Date</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div> -->
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if(!empty($branch_name[$bcode])){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2" onChange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if(!empty($line_name[$lcode])){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <!-- <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($supervisors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($supervisor_code as $scode){ if($supervisor_name[$scode] != "" && !empty($farm_svr[$scode])){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($supervisors == $scode){ echo "selected"; } ?>><?php echo $supervisor_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div> -->
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
                                    <label>Farm Stats</label>
                                    <select name="farmstatus" id="farmstatus" class="form-control select2">
                                        <option value="all" <?php if($farmstatus == "all"){ echo "selected"; } ?>>-All-</option>
										<option value="New Flock is running" <?php if($farmstatus == "New Flock is running"){ echo "selected"; } ?>>New Flock is running</option>
										<option value="Waiting For New Flock" <?php if($farmstatus == "Waiting For New Flock"){ echo "selected"; } ?>>Waiting For New Flock</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
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
                    <th id="order_num">Sl.No.</th>  
                    <th id="order">Branch</th>  
                    <th id="order">Line</th>  
                    <th id="order">Supervisor</th>  
                    <th id="order">Farm Name</th>                  
                    <th id="order">Batch</th> 
                    <th id="order_date">Placement Date</th>
                    <th id="order_date">Liquadation Date</th>
                    <th id="order_num">Gap Days</th>
					<th id="order_num">Farm Capacity</th>
                    <th id="order_num">Farm Status</th>
					
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
                        $display_placement_date = "";
                    $sql1 = "SELECT DISTINCT(farm_code) FROM `broiler_rearingcharge` WHERE `active` = '1' AND `dflag` = '0' $farm_query ";
                    $query1 = mysqli_query($conn,$sql1);
                    while($row1 = mysqli_fetch_assoc($query1)){
                         $sql = "SELECT * FROM `broiler_rearingcharge` WHERE `active` = '1' AND `dflag` = '0' and farm_code = '$row1[farm_code]' $farm_query ORDER BY liquid_date DESC LIMIT 0,1 ";
                        $query = mysqli_query($conn,$sql);
                    ?>
                    <tbody class="tbody1" id="tbody1">
                        <?php
                         while($row = mysqli_fetch_assoc($query)){ 
                                if(date("d.m.Y",$chick_placed_date[$row['batch_code']]) != "01.01.1970"){
                                    $display_placement_date = $chick_placed_date[$row['batch_code']];
                                }
                                else if(date("d.m.Y",strtotime($dentry_min_date[$row['batch_code']])) != "01.01.1970"){
                                    $display_placement_date = strtotime($dentry_min_date[$row['batch_code']]);
                                }
                                else{
                                    $display_placement_date = strtotime($dentry_min_date[$row['batch_code']]);
                                }
                                if(date("d.m.Y",strtotime($dentry_max_date[$batches])) != "01.01.1970"){
                                    $display_gap_days = ((strtotime() - strtotime($dentry_max_date[$batches])) / 60 / 60 / 24);
                                }
                                $start_date = date("Y-m-d",strtotime($row['liquid_date']));
                                $end_date = date("Y-m-d");
                                $date1 = date_create($start_date );
                                $date2 = date_create($end_date);
                                $diff1 = date_diff($date1,$date2);
                                $daysdiff = $diff1->format("%R%a");
                                $daysdiff = abs($daysdiff);
                                $curr_batch =  $curr_batch_code[$row['farm_code']];
                               if($curr_batch != ''){
                                    $cur_code = explode("-",$curr_batch);
                                    $cur_code1 = $cur_code[1];
                                    $pre_code = explode("-",$row['batch_code']);
                                    $pre_code1 = $pre_code[1];
                                    if($cur_code1 > $pre_code1){
                                        $farm_status = 'New Flock is running';
                                    }else{
                                        $farm_status = 'Waiting For New Flock';
                                   }
                               }else{
                                    $farm_status = 'Waiting For New Flock';
                               }
                               if($farm_code[$row['farm_code']] != ''){
                               if($farmstatus == 'all'){
                                $slno++; 
                            ?>
                        <tr>
                            <td title="Sl.No."><?php echo $slno; ?></td>
                            <td><?php echo $branch_name[$row['branch_code']]; ?></td>
                            <td><?php echo $line_name[$row['line_code']]; ?></td>
                            <td><?php echo $supervisor_name[$farm_supervisor[$row['farm_code']]]; ?></td>
                            <td><?php echo $farm_name[$row['farm_code']]; ?></td>
                            <td><?php echo $batch_name[$row['batch_code']]; ?></td>
                            <td title="Placement Date"><?php if(date("d.m.Y",((int)$display_placement_date)) == "01.01.1970"){ echo ""; } else{ echo date("d.m.Y",((int)$display_placement_date)); } ?></td>
                            <td><?php echo date("d.m.Y",strtotime($row['liquid_date'])); ?></td>
                            <td><?php echo $daysdiff; ?></td>
							<td><?php echo $farm_capacity[$row['farm_code']]; ?></td>
                             <?php if($farm_status == 'New Flock is running'){ ?>
                                <td style="text-align:right;color:green;"><b><?php echo $farm_status; ?></b></td>
                            <?php }else { ?>
                                <td style="text-align:right;color:red;"><b><?php echo $farm_status; ?></b></td>
                            <?php } ?>
                        </tr>
                        <?php }else if($farmstatus == $farm_status){
                        $slno++;
                        ?>
						<tr>
                            <td title="Sl.No."><?php echo $slno; ?></td>
                            <td><?php echo $branch_name[$row['branch_code']]; ?></td>
                            <td><?php echo $line_name[$row['line_code']]; ?></td>
                            <td><?php echo $supervisor_name[$farm_supervisor[$row['farm_code']]]; ?></td>
                            <td><?php echo $farm_name[$row['farm_code']]; ?></td>
                            <td><?php echo $batch_name[$row['batch_code']]; ?></td>
                            <td title="Placement Date"><?php if(date("d.m.Y",((int)$display_placement_date)) == "01.01.1970"){ echo ""; } else{ echo date("d.m.Y",((int)$display_placement_date)); } ?></td>
                            <td><?php echo date("d.m.Y",strtotime($row['liquid_date'])); ?></td>
                            <td><?php echo $daysdiff; ?></td>
							<td><?php echo $farm_capacity[$row['farm_code']]; ?></td>
                             <?php if($farm_status == 'New Flock is running'){ ?>
                                <td style="text-align:right;color:green;"><b><?php echo $farm_status; ?></b></td>
                            <?php }else { ?>
                                <td style="text-align:right;color:red;"><b><?php echo $farm_status; ?></b></td>
                            <?php } ?>
                        </tr>
						<?php }
                            }
                         }
                        } ?>
                </tbody>
        <?php
            }
        ?>
        </table>
        <script>
            function fetch_farms_details(a){
                var branches = document.getElementById("branches").value;
                var lines = document.getElementById("lines").value;
                //var supervisors = document.getElementById("supervisors").value;
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
                      /*  removeAllOptions(document.getElementById("supervisors"));
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
                        ?>*/
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
                      /*  removeAllOptions(document.getElementById("supervisors"));
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

                        ?>*/

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

                       /* removeAllOptions(document.getElementById("supervisors"));

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

                        ?>*/

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

                       /* removeAllOptions(document.getElementById("supervisors"));

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

                        ?>*/

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

                       /* removeAllOptions(document.getElementById("supervisors"));

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

                        ?>*/

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

        <script src="../datepicker/jquery/jquery.js"></script>

        <script src="../datepicker/jquery-ui.js"></script>

    </body>

</html>

<?php

include "header_foot.php";

?>