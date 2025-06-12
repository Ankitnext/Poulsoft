<?php
//broiler_agewiseavailable_stock_ta.php
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
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
    $user_code = $_GET['userid'];
}
$file_name = "Age wise available Birds Report";
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

$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Live Flock Summary') AND `field_function` LIKE 'Display Day Record Age' AND `user_access` LIKE 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $day_entryage_flag = mysqli_num_rows($query);

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

$sql = "SELECT * FROM `sms_master` WHERE `sms_type` = 'BB-AgeWiseStock' AND  `msg_type` IN ('WAPP') AND `active` = '1'"; $query = mysqli_query($conn,$sql); $mob_list = "";
while($row = mysqli_fetch_assoc($query)){ $mob_list = $row['numers']; }

$branches = $lines = $supervisors = $farms = "all"; $excel_type = $vis_type = "display"; $report_view = "hd"; $url = ""; $avgwt_search = $age_search = 0;
if(isset($_POST['submit_report']) == true){
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $supervisors = $_POST['supervisors'];
    $farms = $_POST['farms'];
    $vis_type = $_POST['vis_type'];
    $mob_list = $_POST['mob_list'];

    $avgwt_search = $_POST['body_weight'];
    $age_search = $_POST['age'];
}
if($age_search == ""){ $age_search = 0; } if($avgwt_search == ""){ $avgwt_search = 0; }

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
$wapp_link = "../print/Examples/broiler_agewiseavailable_stockwapp.php?branches=".$branches."&lines=".$lines."&supervisors=".$supervisors."&farms=".$farms."&vis_type=".$vis_type."&mob_list=".$mob_list."&ages=".$age_search."&avgwt=".$avgwt_search;
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
            var sendtype = '<?php echo $vis_type; ?>';
            var wapp_link = '<?php echo $wapp_link; ?>';
            if(sendtype.match("WhatsApp")){ window.open(wapp_link,"_BLANK"); }
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
                    <th colspan="2" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Age wise available Birds Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_agewiseavailable_stock_ta.php" method="post"  onsubmit="return checkval()">
                 <?php } else { ?>
                <form action="broiler_agewiseavailable_stock_ta.php?db=<?php echo $db; ?>" method="post" onsubmit="return checkval()">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:auto;">
                    <tr style="padding:10px;">
                        <th colspan="4">
                            <div class="row">&ensp;&ensp;
                                <div class="form-group" style="width:90px;">
                                    <label>Age</label>
                                    <input type="text" name="age" id="age" placeholder="Enter Age" value="<?php echo round($age_search); ?>" class="form-control" style="width:80px;" />
                                </div>
                                <div class="form-group" style="width:110px;">
                                    <label>Weight(Gms)</label>
                                    <input type="text" name="body_weight" id="body_weight" placeholder="Enter Body Weight" value="<?php echo round($avgwt_search,2); ?>" class="form-control" style="width:100px;" />
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
                                    <label for="vis_type">Type</label>
                                    <select name="vis_type" id="vis_type" class="form-control select2" style="width:110px;" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
                                        <option value="display" <?php if($vis_type == "display"){ echo "selected"; } ?>>Display</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="WhatsApp" <?php if($vis_type == "WhatsApp"){ echo "selected"; } ?>>WhatsApp</option>
                                    </select>
                                </div>
                                <div class="form-group" style="width:150px;">
                                    <label for="mob_list">Mobile</label>
                                    <input type="text" name="mob_list" id="mob_list" class="form-control" style="width:140px;" value="<?php echo $mob_list; ?>"/>
                                </div>&ensp;
                                <div class="form-group" style="width:100px;">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
        </table>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-5">
                    <table id="main_table" class="tbl" align="center">
                        <thead class="thead3" align="center">
                        
                        <tr align="center">
                                <th id="order_num">Age</th>
                                <th id="order_num">No. of Farms</th>
                                <th id="order_num">Placed Birds</th>
                                <th id="order_num">Available Birds</th>
                                <th id="order_num">Std. Avg Wt(gms)</th>
                                <th id="order_num">Actual Avg Wt(gms)</th>
                                <th id="order_num">Available Weight(Kgs)</th>
                                
                            </tr>
                        </thead>
                        <?php
                        if(isset($_POST['submit_report']) == true){
                            $display_placement_date = ""; $today = date("Y-m-d");
                            
                            $batch_code = $batch_name = $chick_in_qty = $chick_out_qty = $chick_placed_date = $chick_mort_qty = $chick_age = $avg_wt = array();
                            $sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0'".$farm_filter." AND `gc_flag` = '0'"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; }
                            $batch_list = ""; $batch_list = implode("','", $batch_code);
                            
                            $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%'"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }
                            $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler bird%'"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }
                            
                            //Daily Entry
                            $sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0'".$farm_query." AND gc_flag = '0' ORDER BY `batch_code`,`date` ASC";
                            $query = mysqli_query($conn,$sql); $b1 = array();
                            while($row = mysqli_fetch_assoc($query)){
                                if(empty($avg_wt[$row['batch_code']])){ $avg_wt[$row['batch_code']] = $row['avg_wt']; $b1[$row['batch_code']] = $row['batch_code']; }
                                else if($row['avg_wt'] > 0){ $avg_wt[$row['batch_code']] = $row['avg_wt']; $b1[$row['batch_code']] = $row['batch_code']; }
                                else{ }
                            }

                            //$batch_list = ""; $batch_list = implode("','", $b1);
                            //Daily Entry
                            $sql = "SELECT SUM(mortality) as mort,SUM(culls) as culls,MAX(brood_age) as age,batch_code FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' AND gc_flag = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC";
                            $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                $chick_mort_qty[$row['batch_code']] += ((float)$row['mort'] + (float)$row['culls']);
                                $chick_age[$row['batch_code']] = ((int)$row['age']);

                                $key_code = $row['batch_code'];
                                $dentry_mort[$key_code] = $row['mortality'];
                                $dentry_cull[$key_code] = $row['culls'];
                                $dentry_feed[$key_code] = $row['kgs1'] + $row['kgs2'];
                                $dentry_min_date[$key_code] = $row['sdate'];
                                $dentry_max_date[$key_code] = $row['edate'];
                                $dentry_max_age[$key_code] = $row['brood_age'];
                            }
                            //Chick Purchase
                            $sql = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,MIN(date) as sdate,icode,farm_batch FROM `broiler_purchases` WHERE `icode` = '$chick_code' AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `farm_batch` ASC";
                            $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['farm_batch'];
                                $chick_in_qty[$key_code] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                                if(empty($chick_placed_date[$row['farm_batch']])){ $chick_placed_date[$row['farm_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['farm_batch']]){ $chick_placed_date[$row['farm_batch']] = strtotime($row['sdate']); } }
                            }
                            //Chick Transfer IN
                            $sql_record = "SELECT SUM(quantity) as quantity,MIN(date) as sdate,code,to_batch FROM `item_stocktransfers` WHERE `code` = '$chick_code' AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch` ORDER BY `to_batch` ASC";
                            $query = mysqli_query($conn,$sql_record);
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['to_batch'];
                                $chick_in_qty[$key_code] += ((float)$row['quantity']);
                                if(empty($chick_placed_date[$row['to_batch']])){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['to_batch']]){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); } }
                            }

                            //Chick Sale
                            $sql_record = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,MIN(date) as sdate,icode,farm_batch FROM `broiler_sales` WHERE `icode` = '$bird_code' AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch` ORDER BY `farm_batch` ASC";
                            $query = mysqli_query($conn,$sql_record);
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['farm_batch'];
                                $chick_out_qty[$key_code] += ((float)$row['birds']);
                                //$chick_out_qty[$key_code] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                            }
                            //Chick Transfer Out
                            $sql_record = "SELECT SUM(quantity) as quantity,MIN(date) as sdate,code,from_batch FROM `item_stocktransfers` WHERE `code` = '$bird_code' AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch` ORDER BY `to_batch` ASC";
                            $query = mysqli_query($conn,$sql_record);
                            while($row = mysqli_fetch_assoc($query)){
                                $key_code = $row['from_batch'];
                                $chick_out_qty[$key_code] += ((float)$row['quantity']);
                            }
                            
                            //Purchase Return-Out
                            $sql = "SELECT * FROM `broiler_itemreturns` WHERE `itemcode` IN ('$chick_code','$bird_code') AND `farm_batch` IN ('$batch_list') AND `type` = 'Supplier' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`farm_batch` ASC";
                            $query = mysqli_query($conn,$sql); $pur_rtn_qty = array();
                            while($row = mysqli_fetch_array($query)){
                                $key = $row['farm_batch'];
                                $chick_prtn_qty[$key] += (float)$row['quantity'];
                            }
                            //In-House: Transfer-Out
                            $sql = "SELECT * FROM `broiler_bird_transferout` WHERE `item_code` IN ('$chick_code','$bird_code') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`from_batch` ASC";
                            $query = mysqli_query($conn,$sql); $chick_ppt_qty = array();
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['from_batch'];
                                $chick_ppt_qty[$key] += (float)$row['birds'];
                            }
                            $batch_farm1 = array();
                            $sql = "SELECT * FROM `broiler_batch` WHERE `code` IN ('$batch_list') AND `dflag` = '0' AND `gc_flag` = '0'"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ $batch_name[$row['code']] = $row['description']; $batch_farm1[$row['code']] = $row['farm_code']; $batch_farm2[$row['farm_code']] = $row['code']; }
                            $farm_list = ""; $farm_list = implode("','", $batch_farm1);

                            $f1 = array();
                            $sql = "SELECT * FROM `broiler_farm` WHERE `code` IN ('$farm_list') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){ $f1[$row['code']] = $row['code']; }
                        ?>
                        <tbody class="tbody1">
                            <?php
                            $age1 = round(((float)$avgwt_search / 1000),1);
                            $slno = $tot_noffrm = $tot_pbirds = $tot_birds = $tot_weight = $age = 0; $noff_cnt = $placed_birds = $brhage_wise_birds = $avail_age_birds = $avail_age_weights = $avail_wt_birds = $avail_wt_weights = $age_array_list = $age_title = $weight_array_list = array();
                            foreach($f1 as $fcode){
                                $bcode = ""; $bcode = $batch_farm2[$fcode];
                                if(!empty($chick_age[$bcode])){ $age = (INT)$chick_age[$bcode]; } else{ $age = 0; }
                                if(!empty($avg_wt[$bcode])){ $abody_wt = (float)$avg_wt[$bcode]; } else{ $abody_wt = 0; }

                                 if(!empty($chick_placed_date[$bcode]) && date("d.m.Y",$chick_placed_date[$bcode]) != "01.01.1970" ){ 
                                    if(date("d.m.Y",$chick_placed_date[$bcode]) != "01.01.1970"){
                                        $display_placement_date = $chick_placed_date[$bcode];
                                    }
                                    else if(date("d.m.Y",strtotime($dentry_min_date[$bcode])) != "01.01.1970"){
                                        $display_placement_date = strtotime($dentry_min_date[$bcode]);
                                    }
                                    else{
                                        $display_placement_date = strtotime($dentry_min_date[$bcode]);
                                    }
                                    // if($day_entryage_flag == 1){
                                    //     $display_age = $dentry_max_age[$bcode];
                                    // }
                                    // else{
                                    //     $display_age = ((strtotime($today) - $display_placement_date) / 60 / 60 / 24)+1;
                                    // }
                                // }
                                
                                if($age >= $age_search){
                                    $display_available_birds = 0; $display_available_birds = ((float)$chick_in_qty[$bcode] - ((float)$chick_mort_qty[$bcode] + (float)$chick_out_qty[$bcode] + (float)$pur_rtn_qty[$bcode] + (float)$chick_ppt_qty[$bcode]));
                                    $noff_cnt[$age] += 1;
                                    $placed_birds[$age] += (float)$chick_in_qty[$bcode];
                                    $avail_age_birds[$age] += (float)$display_available_birds;
                                    $avail_age_weights[$age] += round((((float)$display_available_birds * (float)$abody_wt) / 1000),2);
                                    $age_array_list[$age] = $age;
                                    $age_title[$age] .= $batch_name[$bcode];

                                     if($day_entryage_flag == 1){
                                        $display_age[$age] = $dentry_max_age[$bcode];
                                    }
                                    else{
                                        $display_age[$age] = ((strtotime($today) - $display_placement_date) / 60 / 60 / 24)+1;
                                    }

                                    $blcode = $farm_branch[$fcode];
                                    $blkey = $age."@".$blcode;
                                    $brhage_wise_birds[$blkey] += (float)$display_available_birds;
                                    /*echo "<br/>".$age."@".$bcode."@".$batch_name[$bcode]."@".$chick_in_qty[$bcode];
                                    if($age == 42){
                                        echo "<br/>".$age."@".$bcode."@".$batch_name[$bcode]."((float)$chick_in_qty[$bcode] - ((float)$chick_mort_qty[$bcode] + (float)$chick_out_qty[$bcode] + (float)$pur_rtn_qty[$bcode] + (float)$chick_ppt_qty[$bcode]))";
                                    }
                                    */
                                }
                            }
                                //Weight Wise Calculations
                                $wt_kgs = 0; $awt_kgs = 0; $awt_kgs = round(((float)$abody_wt / 1000),1);
                                if((float)$awt_kgs < 1.8){ $wt_kgs = 1.8; }
                                else if((float)$awt_kgs >= 1.8 && (float)$awt_kgs <= 1.9){ $wt_kgs = 1.9; }
                                else if((float)$awt_kgs > 1.9 && (float)$awt_kgs <= 2){ $wt_kgs = 2; }
                                else if((float)$awt_kgs > 2 && (float)$awt_kgs <= 2.1){ $wt_kgs = 2.1; }
                                else if((float)$awt_kgs > 2.1 && (float)$awt_kgs <= 2.2){ $wt_kgs = 2.2; }
                                else if((float)$awt_kgs > 2.2 && (float)$awt_kgs <= 2.3){ $wt_kgs = 2.3; }
                                else if((float)$awt_kgs > 2.3 && (float)$awt_kgs <= 2.4){ $wt_kgs = 2.4; }
                                else if((float)$awt_kgs > 2.4 && (float)$awt_kgs <= 2.5){ $wt_kgs = 2.5; }
                                else if((float)$awt_kgs > 2.5 && (float)$awt_kgs <= 2.6){ $wt_kgs = 2.6; }
                                else if((float)$awt_kgs > 2.6 && (float)$awt_kgs <= 2.7){ $wt_kgs = 2.7; }
                                else if((float)$awt_kgs > 2.7 && (float)$awt_kgs <= 2.8){ $wt_kgs = 2.8; }
                                else if((float)$awt_kgs > 2.8 && (float)$awt_kgs <= 2.9){ $wt_kgs = 2.9; }
                                if($wt_kgs >= $age1){
                                    //echo "<br/>".$wt_kgs."-".$age1;
                                    $display_available_birds = 0; $display_available_birds = ((float)$chick_in_qty[$bcode] - ((float)$chick_mort_qty[$bcode] + (float)$chick_out_qty[$bcode] + (float)$pur_rtn_qty[$bcode] + (float)$chick_ppt_qty[$bcode]));
                                    $weight_array_list["AW".$wt_kgs] = "AW".$wt_kgs;
                                    $avail_wt_birds["AW".$wt_kgs] += (float)$display_available_birds;
                                    $avail_wt_weights["AW".$wt_kgs] += round((((float)$display_available_birds * (float)$abody_wt) / 1000),2);
                                }
                            }
                            krsort($age_array_list);
                            foreach($age_array_list as $alist){
                                //if(number_format_ind($avail_age_birds[$alist]) != "0.00" && number_format_ind($avail_age_weights[$alist]) != "0.00"){
                                    $slno++;
                                ?>
                                <tr>
                                    <td title="<?php echo $age_title[$alist]; ?>" style="text-align:center;"><?php echo $display_age[$alist]; ?></td>
                                    <td title="No. of Farms" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($noff_cnt[$alist])); ?></td>
                                    <td title="Placed Birds" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($placed_birds[$alist])); ?></td>
                                    <td title="Available Birds" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($avail_age_birds[$alist])); ?></td>
                                    <td title="Std. Avg Wt(gms)" style="text-align:right;"><?php echo number_format_ind((float)$std_body_weight[$alist]); ?></td>
                                    <td title="Actual Avg Wt(gms)" style="text-align:right;">
                                    <?php
                                        if(!empty($avail_age_birds[$alist]) && (float)$avail_age_birds[$alist] != 0){
                                            $t1 = 0; $t1 = round((((float)$avail_age_weights[$alist] / (float)$avail_age_birds[$alist]) * 1000),2);
                                        }
                                        else{ $t1 = 0; }
                                        echo number_format_ind(($t1)); ?>
                                    </td>
                                    <td title="Available Weight(Kgs)" style="text-align:right;"><?php echo number_format_ind($avail_age_weights[$alist]); ?></td>
                                </tr>
                                <?php
                                    $tot_noffrm += (float)$noff_cnt[$alist];
                                    $tot_pbirds += (float)$placed_birds[$alist];
                                    $tot_birds += (float)$avail_age_birds[$alist];
                                    $tot_weight += (float)$avail_age_weights[$alist];
                                //}
                            }
                            ?>
                        </tbody>
                        <thead class="thead2">
                            <tr>
                                <td style="text-align:right;" title="total" colspan="1"><b>Total</b></td>
                                <td style="text-align:right;"><b><?php echo str_replace(".00","",number_format_ind($tot_noffrm)); ?></b></td>
                                <td style="text-align:right;"><b><?php echo str_replace(".00","",number_format_ind($tot_pbirds)); ?></b></td>
                                <td style="text-align:right;"><b><?php echo str_replace(".00","",number_format_ind($tot_birds)); ?></b></td>
                                <td style="text-align:right;"></td>
                                <td style="text-align:right;"></td>
                                <td style="text-align:right;"><b><?php echo number_format_ind($tot_weight); ?></b></td>
                            </tr>
                        </thead>
                    <?php
                        }
                    ?>
                    </table>
                </div>
                <div class="col-md-6">
                <table id="main_table" class="tbl" align="center">
                        <thead class="thead3" align="center">
                        
                        <tr align="center">
                                <th id="order_num">Avg Weight(kgs)</th>
                                <th id="order_num">Available Birds</th>
                                <th id="order_num">Available Weight(kgs)</th>
                                
                            </tr>
                        </thead>
                        <tbody class="tbody1">
                        <?php
                        if(isset($_POST['submit_report']) == true){
                            krsort($weight_array_list);
                            $slno = $tot_birds = $tot_weight = 0;
                            foreach($weight_array_list as $alist){
                                $slno++;
                            ?>
                            <tr>
                                <td title="Avg Weight(kgs)" style="text-align:right;"><?php $t1 = ""; $t1 = str_replace("AW","",number_format_ind($alist)); if($t1 == "1.80"){ echo "<"; } echo $t1; ?></td>
                                <td title="Available Birds" style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($avail_wt_birds[$alist])); ?></td>
                                <td title="Available Weight(kgs)" style="text-align:right;"><?php echo number_format_ind($avail_wt_weights[$alist]); ?></td>
                            </tr>
                            <?php
                                $tot_birds += (float)$avail_wt_birds[$alist];
                                $tot_weight += (float)$avail_wt_weights[$alist];
                            }
                            ?>
                        </tbody>
                        <thead class="thead2">
                            <tr>
                                <td style="text-align:right;" title="total" colspan="1"><b>Total</b></td>
                                <td style="text-align:right;"><b><?php echo str_replace(".00","",number_format_ind($tot_birds)); ?></b></td>
                                <td style="text-align:right;"><b><?php echo number_format_ind($tot_weight); ?></b></td>
                            </tr>
                        </thead>
                    <?php
                        }
                    ?>
                    </table><br/><br/>
                    <table id="main_table" class="tbl" align="center">
                        <thead class="thead3" align="center">
                            <tr align="center">
                                <th id="order_num">Age</th>
                                <?php foreach($branch_code as $bcode){ echo '<th id="order_num">'.$branch_name[$bcode].'</th>'; } ?>
                            </tr>
                        </thead>
                        <tbody class="tbody1">
                        <?php
                        if(isset($_POST['submit_report']) == true){
                            $tot_bage_birds = array();
                            foreach($age_array_list as $alist){
                            ?>
                            <tr>
                                <td title="<?php echo $age_title[$alist]; ?>" style="text-align:center;"><?php echo $display_age[$alist]; ?></td>
                                <?php
                                foreach($branch_code as $bcode){
                                    $key = $alist."@".$bcode;
                                    if(empty($brhage_wise_birds[$key]) || $brhage_wise_birds[$key] == ""){ $brhage_wise_birds[$key] = 0; }
                                    echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($brhage_wise_birds[$key])).'</th>';
                                    $tot_bage_birds[$bcode] += (float)$brhage_wise_birds[$key];
                                }
                                ?>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                        <thead class="thead2">
                            <tr>
                                <td style="text-align:right;" title="total" colspan="1"><b>Total</b></td>
                                <?php foreach($branch_code as $bcode){ echo '<th style="text-align:right;">'.str_replace(".00","",number_format_ind($tot_bage_birds[$bcode])).'</th>'; } ?>
                            </tr>
                        </thead>
                    <?php
                        }
                    ?>
                    </table>
                </div>
            </div>
        </div>
        <script>
            function checkval(){
                var vis_type = document.getElementById("vis_type").value;
                var mob_list = document.getElementById("mob_list").value;
                var l = true; var e = []; var g = 0;
                if(vis_type == "WhatsApp"){
                    if(mob_list == ""){
                        alert("Please enter mobile No to send Report/PDF");
                        document.getElementById("mob_list").focus();
                        l = false;
                    }
                    else{
                        if(mob_list.match(",")){
                            e = mob_list.split(",");
                            g = 0;
                            for(var f = 0;f < e.length;f++){ if(e[f].length == 10){ g++; } }
                            if(g == 0){
                                alert("Please enter appropriate Mobile No to send PDF");
                                document.getElementById("mob_list").focus();
                                l = false;
                            }
                        }
                        else{
                            if(mob_list.length != 10){
                                alert("Please enter appropriate Mobile No to send PDF");
                                document.getElementById("mob_list").focus();
                                l = false;
                            }
                        }
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    return false;
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
                    asc = !asc;
                    })
                });
            }

            table_sort();
            table_sort2();
            table_sort3();
        </script>
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    //  return function(table, name, filename, chosen) {
                
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    //window.location.href = uri + base64(format(template, ctx))
                    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    //}
                }
                else{ }
            }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>