<?php
//broiler_farmwise_feedstock2.php
$requested_data = json_decode(file_get_contents('php://input'),true);
session_start();
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Farm Wise Feed Stock Report";
    include "header_head.php";
    $user_code = $_SESSION['userid'];
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Farm Wise Feed Stock Report";
    include "header_head.php";
    $user_code = $_GET['userid'];
}

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $branch_access_code = $row['branch_code'];
    $line_access_code = $row['line_code'];
    $farm_access_code = $row['farm_code'];
    $sector_access_code = $row['loc_access'];
}
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
$sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ".$line_access_filter1."".$branch_access_filter2."  ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
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

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_code = $icat_iac = $icat_wip = array();
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_iac[$row['code']] = $row['iac']; $icat_wip[$row['code']] = $row['wpac']; }
$icat_list = implode("','",$icat_code);

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
$query = mysqli_query($conn,$sql); $feed_code = $feed_name = $feed_category = array();
while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; $feed_name[$row['code']] = $row['description']; $feed_category[$row['code']] = $row['category']; }

$fdate = $tdate = date("Y-m-d");
$branches = $lines = $supervisors = $farms = "all";
$items = array(); $items["all"] = "all";
$excel_type = "display"; $url = ""; $hcol_size = "35";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];

    $items = array(); foreach($_POST['items'] as $ilist){ $items[$ilist] = $ilist; }

    $excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/FarmWiseFeedStock2-Excel.php?fdate=".$fdate."&tdate=".$tdate."&branches=".$branches."&lines=".$lines."&supervisors=".$supervisors."&farms=".$farms;
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
                    <td colspan="2" id="img_head" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="33" id="cname_head" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Farm Wise Feed Stock Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_farmwise_feedstock2.php" method="post"  onsubmit="return checkval()">
                 <?php } else { ?>
                <form action="broiler_farmwise_feedstock2.php?db=<?php echo $db; ?>" method="post" onsubmit="return checkval()">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:auto;">
                    <tr style="padding:10px;">
                        <th id="flt_head">
                            <div class="row">&ensp;&ensp;
                                <div class="form-group" style="width:120px;">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="form-group" style="width:120px;">
                                    <label>To Date</label>
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
                                <div class="form-group" style="width:300px;">
                                    <label for="items">Item</label>
                                    <select name="items[]" id="items" class="form-control select2" style="width:290px;" multiple>
                                        <option value="all" <?php foreach($items as $ilist){ if($ilist == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($feed_code as $icode){ if($feed_name[$icode] != ""){ ?>
                                        <option value="<?php echo $icode; ?>" <?php foreach($items as $ilist){ if($ilist == $icode){ echo "selected"; } } ?>><?php echo $feed_name[$icode]; ?></option>
                                        <?php } } ?>
                                    </select>
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
                $batch_code = $batch_farm = $key_arr_codes = $stk_cr_qty = $stk_dr_qty = $stk_in_qty = $stk_cons_qty = 
                $stk_out_qty = $stk_bal_qty = $batch_arr_list = array();
                $feed_slist = $feed_list = $stkcoa_list = $farm_list = $batch_list = "";

                $fall_flag = 0;
                foreach($items as $ilist){ if($ilist == "all"){ $fall_flag = 1; } if($feed_slist == ""){ $feed_slist = $ilist; } else{ $feed_slist .= "','".$ilist; }}
                if($fall_flag == 1 || $feed_slist == ""){ $feed_filter = ""; } else{ $feed_filter = " AND `item_code` IN ('$feed_slist')"; }
                $gc_flag = 0;
                $sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` LIKE '$gc_flag'".$farm_filter." AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_farm[$row['code']] = $row['farm_code']; }
                
                
                $stkcoa_list = implode("','",array_unique($icat_iac)); $feed_list = implode("','",$feed_code); $farm_list = implode("','",$batch_farm); $batch_list = implode("','",$batch_code);
                //Daily Entry
                $sql = "SELECT MAX(brood_age) as age,batch_code FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch_list') AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' AND gc_flag = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC";
                $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $chick_age[$row['batch_code']] = ((int)$row['age']); }

                $sql = "SELECT * FROM `account_summary` WHERE `coa_code` IN ('$stkcoa_list') AND `item_code` IN ('$feed_list')".$feed_filter." AND `location` IN ('$farm_list') AND `batch` IN ('$batch_list') AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `location`,`batch`,`coa_code`,`item_code`,`crdr` ASC";
                $query = mysqli_query($conn,$sql); $bach_alist = $item_alist = $opn_stk_qty = array(); $i = 0;
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['location']."@".$row['batch']."@".$row['item_code'];
                    $bach_alist[$key] = $row['batch'];
                    $item_alist[$key] = $row['item_code'];

                    if(strtotime($row['date']) < strtotime($fdate)){
                        if(empty($opn_stk_qty[$key]) || $opn_stk_qty[$key] == ""){ $opn_stk_qty[$key] = 0; }
                        if(!empty($icat_iac[$feed_category[$row['item_code']]]) && $icat_iac[$feed_category[$row['item_code']]] == $row['coa_code'] && $row['crdr'] == "DR"){
                            $opn_stk_qty[$key] += (float)$row['quantity'];
                        }
                        else if(!empty($icat_iac[$feed_category[$row['item_code']]]) && $icat_iac[$feed_category[$row['item_code']]] == $row['coa_code'] && $row['crdr'] == "CR"){
                            $opn_stk_qty[$key] -= (float)$row['quantity'];
                        }
                        else{ }
                        
                    }
                    else if(strtotime($row['date']) < strtotime($fdate)){
                        if(str_contains($row['etype'], 'Purchase') && !empty($icat_iac[$feed_category[$row['item_code']]]) && $icat_iac[$feed_category[$row['item_code']]] == $row['coa_code'] && $row['crdr'] == "DR"){
                            $pur_stk_qty[$key] += (float)$row['quantity'];
                        }
                        else if(str_contains($row['etype'], 'Transfer') && !empty($icat_iac[$feed_category[$row['item_code']]]) && $icat_iac[$feed_category[$row['item_code']]] == $row['coa_code'] && $row['crdr'] == "DR"){
                            $tin_stk_qty[$key] += (float)$row['quantity'];
                        }
                        else if((str_contains($row['etype'], 'Transfer')) || (str_contains($row['etype'], 'Sale')) && !empty($icat_iac[$feed_category[$row['item_code']]]) && $icat_iac[$feed_category[$row['item_code']]] == $row['coa_code'] && $row['crdr'] == "CR"){
                            $tout_stk_qty[$key] += (float)$row['quantity'];
                        }
                        else if(!empty($icat_iac[$feed_category[$row['item_code']]]) && $icat_iac[$feed_category[$row['item_code']]] == $row['coa_code'] && $row['crdr'] == "CR"){
                            $con_stk_qty[$key] += (float)$row['quantity'];
                        }
                        else if(!empty($icat_iac[$feed_category[$row['item_code']]]) && $icat_iac[$feed_category[$row['item_code']]] == $row['coa_code'] && $row['crdr'] == "DR"){
                            $pen_dt[$i] .= $row['date']."@".$row['trnum']."@".$row['quantity']."@".$row['etype']; $i++;
                        }
                    }
                }
                
                $farm_list = $batch_list = $item_list = "";
                $batch_list = implode("','",$bach_alist);
                $sql = "SELECT * FROM `broiler_batch` WHERE `code` IN ('$batch_list') AND `gc_flag` LIKE '$gc_flag' AND `active` = '1' AND `dflag` = '0'";
                $query = mysqli_query($conn,$sql); $batch_name = $batch_farms = $batch_codes = array();
                while($row = mysqli_fetch_assoc($query)){
                    $batch_name[$row['code']] = $row['description'];
                    $batch_farms[$row['code']] = $row['farm_code'];
                    if(empty($batch_codes[$row['farm_code']]) || $batch_codes[$row['farm_code']] == ""){ $batch_codes[$row['farm_code']] = $row['code']; }
                    else{ $batch_codes[$row['farm_code']] = ",".$row['code']; }
                }

                $farm_list = implode("','",$batch_farms);
                $sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$farm_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $farms_code = $farms_name = array();
                while($row = mysqli_fetch_assoc($query)){ $farms_code[$row['code']] = $row['code']; $farms_name[$row['code']] = $row['description']; }

                $item_list = implode("','",$item_alist);
                $sql = "SELECT * FROM `item_details` WHERE `code` IN ('$item_list') AND `category` IN ('$icat_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC";
                $query = mysqli_query($conn,$sql); $feeds_code = $feeds_name = array();
                while($row = mysqli_fetch_assoc($query)){ $feeds_code[$row['code']] = $row['code']; $feeds_name[$row['code']] = $row['description']; }
                $feed_count = sizeof($feeds_code);
                $hcol_size = ((sizeof($feeds_code) * 7) + 4);
            ?>
            <thead>
                <tr class="thead3" align="center">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <?php
                    echo '<th colspan="'.$feed_count.'">Opening stock</th>';
                    echo '<th colspan="'.$feed_count.'">Feed Purchase</th>';
                    echo '<th colspan="'.$feed_count.'">Feed Transfer In</th>';
                    echo '<th colspan="'.$feed_count.'">Feed Transfer Out</th>';
                    echo '<th colspan="'.$feed_count.'">Total Feed</th>';
                    echo '<th colspan="'.$feed_count.'">Feed Consumpation</th>';
                    echo '<th colspan="'.$feed_count.'">Closing Stock</th>';
                    ?>
                </tr>
                <tr class="thead3" align="center">
                    <th id="order_num">Sl.No.</th>
                    <th id="order">Farm</th>
                    <th id="order">Batch</th>
                    <th id="order_num">Age</th>
                    <?php
                    for($i = 1;$i <= 7;$i++){
                        foreach($feeds_code as $icode){
                            echo '<th id="order_num">'.$feeds_name[$icode].'</th>';
                        }
                    }
                    ?>
                </tr>
            </thead>
            <tbody class="tbody1" id="tbody1">
            <?php
            $html = ''; $slno = 0;
            $topn_stk_qty = $tpur_stk_qty = $ttin_stk_qty = $ttout_stk_qty = $tot_stk_qty = $taf_stk_qty = $ttaf_stk_qty = $tcon_stk_qty = $tcf_stk_qty = $ttcf_stk_qty = array();
            foreach($farms_code as $fcode){
                $bcode1 = $batch_codes[$fcode];
                $bcode2 = array();
                $bcode2 = explode(",",$bcode1);

                foreach($bcode2 as $bcode){
                    $slno++;
                    $html .= '<tr>';
                    $html .= '<td>'.$slno.'</td>';
                    $html .= '<td>'.$farms_name[$fcode].'</td>';
                    $html .= '<td>'.$batch_name[$bcode].'</td>';
                    $html .= '<td style="text-align:right;">'.str_replace(".00","",number_format_ind(round($chick_age[$bcode]))).'</td>';
                    //Opening Feed Details
                    foreach($feeds_code as $icode){
                        $key = $fcode."@".$bcode."@".$icode;
                        if(empty($opn_stk_qty[$key]) || $opn_stk_qty[$key] == ""){ $opn_stk_qty[$key] = 0; }
                        $html .= '<td style="text-align:right;">'.number_format_ind(round($opn_stk_qty[$key],2)).'</td>';
                        $topn_stk_qty[$icode] += (float)$opn_stk_qty[$key];
                    }
                    //Purchased Feed Details
                    foreach($feeds_code as $icode){
                        $key = $fcode."@".$bcode."@".$icode;
                        if(empty($pur_stk_qty[$key]) || $pur_stk_qty[$key] == ""){ $pur_stk_qty[$key] = 0; }
                        $html .= '<td style="text-align:right;">'.number_format_ind(round($pur_stk_qty[$key],2)).'</td>';
                        $tpur_stk_qty[$icode] += (float)$pur_stk_qty[$key];
                    }
                    //Transfer-In Feed Details
                    foreach($feeds_code as $icode){
                        $key = $fcode."@".$bcode."@".$icode;
                        if(empty($tin_stk_qty[$key]) || $tin_stk_qty[$key] == ""){ $tin_stk_qty[$key] = 0; }
                        $html .= '<td style="text-align:right;">'.number_format_ind(round($tin_stk_qty[$key],2)).'</td>';
                        $ttin_stk_qty[$icode] += (float)$tin_stk_qty[$key];
                    }
                    //Transfer-Out Feed Details
                    foreach($feeds_code as $icode){
                        $key = $fcode."@".$bcode."@".$icode;
                        if(empty($tout_stk_qty[$key]) || $tout_stk_qty[$key] == ""){ $tout_stk_qty[$key] = 0; }
                        $html .= '<td style="text-align:right;">'.number_format_ind(round($tout_stk_qty[$key],2)).'</td>';
                        $ttout_stk_qty[$icode] += (float)$tout_stk_qty[$key];
                    }
                    //Total Feed Details
                    foreach($feeds_code as $icode){
                        $key = $fcode."@".$bcode."@".$icode;
                        if(empty($opn_stk_qty[$key]) || $opn_stk_qty[$key] == ""){ $opn_stk_qty[$key] = 0; }
                        if(empty($pur_stk_qty[$key]) || $pur_stk_qty[$key] == ""){ $pur_stk_qty[$key] = 0; }
                        if(empty($tin_stk_qty[$key]) || $tin_stk_qty[$key] == ""){ $tin_stk_qty[$key] = 0; }
                        if(empty($tout_stk_qty[$key]) || $tout_stk_qty[$key] == ""){ $tout_stk_qty[$key] = 0; }
                        $taf_stk_qty[$key] = 0;
                        $taf_stk_qty[$key] = (((float)$opn_stk_qty[$key] + (float)$pur_stk_qty[$key] + (float)$tin_stk_qty[$key]) - ((float)$tout_stk_qty[$key]));
                        $html .= '<td style="text-align:right;">'.number_format_ind(round($taf_stk_qty[$key],2)).'</td>';
                        $ttaf_stk_qty[$icode] += (float)$taf_stk_qty[$key];
                    }
                    //Consumed Feed Details
                    foreach($feeds_code as $icode){
                        $key = $fcode."@".$bcode."@".$icode;
                        if(empty($con_stk_qty[$key]) || $con_stk_qty[$key] == ""){ $con_stk_qty[$key] = 0; }
                        $html .= '<td style="text-align:right;">'.number_format_ind(round($con_stk_qty[$key],2)).'</td>';
                        $tcon_stk_qty[$icode] += (float)$con_stk_qty[$key];
                    }
                    //Closing Feed Details
                    foreach($feeds_code as $icode){
                        $key = $fcode."@".$bcode."@".$icode;
                        if(empty($taf_stk_qty[$key]) || $taf_stk_qty[$key] == ""){ $taf_stk_qty[$key] = 0; }
                        if(empty($con_stk_qty[$key]) || $con_stk_qty[$key] == ""){ $con_stk_qty[$key] = 0; }
                        $tcf_stk_qty[$key] = 0;
                        $tcf_stk_qty[$key] = (((float)$opn_stk_qty[$key] + (float)$pur_stk_qty[$key] + (float)$tin_stk_qty[$key]) - ((float)$tout_stk_qty[$key]));
                        $html .= '<td style="text-align:right;">'.number_format_ind(round($tcf_stk_qty[$key],2)).'</td>';
                        $ttcf_stk_qty[$icode] += (float)$tcf_stk_qty[$key];
                    }
                    $html .= '</tr>';
                }
            }
            echo $html;
            ?>
            </tbody>
            <thead>
                <tr class="thead3" align="center">
                    <th colspan="4">Total</th>
                    <?php
                    foreach($feeds_code as $icode){ echo '<th style="text-align:right;">'.number_format_ind(round($topn_stk_qty[$icode],2)).'</th>'; }
                    foreach($feeds_code as $icode){ echo '<th style="text-align:right;">'.number_format_ind(round($tpur_stk_qty[$icode],2)).'</th>'; }
                    foreach($feeds_code as $icode){ echo '<th style="text-align:right;">'.number_format_ind(round($ttin_stk_qty[$icode],2)).'</th>'; }
                    foreach($feeds_code as $icode){ echo '<th style="text-align:right;">'.number_format_ind(round($ttout_stk_qty[$icode],2)).'</th>'; }
                    foreach($feeds_code as $icode){ echo '<th style="text-align:right;">'.number_format_ind(round($ttaf_stk_qty[$icode],2)).'</th>'; }
                    foreach($feeds_code as $icode){ echo '<th style="text-align:right;">'.number_format_ind(round($tcon_stk_qty[$icode],2)).'</th>'; }
                    foreach($feeds_code as $icode){ echo '<th style="text-align:right;">'.number_format_ind(round($ttcf_stk_qty[$icode],2)).'</th>'; }
                    ?>
                </tr>
            </thead>
            <?php
            }
            ?>
        </table>
        <script>
            function checkval(){
                var tdate = document.getElementById("tdate").value;
                if(tdate == ""){
                    alert('Please Enter Date');
                    document.getElementById("tdate").focus();
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