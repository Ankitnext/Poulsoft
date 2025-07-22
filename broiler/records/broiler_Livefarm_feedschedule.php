<?php
//broiler_Livefarm_feedschedule.php
$requested_data = json_decode(file_get_contents('php://input'),true);

$db = '';

if(!isset($_SESSION)){ session_start(); }
if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_GET['db']; }
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Batch wise Feed Scheduling Report";
    include "header_head.php";
    //$user_code = $_SESSION['userid'];
    
    $database_name = $_SESSION['dbase'];
    $user_code = $_SESSION['userid'];
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Batch wise Feed Scheduling Report";
    include "header_head.php";
    //$user_code = $_GET['userid'];
    $database_name = $db;
    $user_code = $_GET['userid'];
}

//Check and create taable
$table_head = "Tables_in_".$database_name;
$sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'broiler_feedschedule_standard%';"; $query1 = mysqli_query($conn,$sql1); $tblc = mysqli_num_rows($query1);
if($tblc > 0){ }
else{
    $sql2 = "
    CREATE TABLE `broiler_feedschedule_standard` (
        `id` int(100) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `item_code` varchar(300) DEFAULT NULL,
        `quantity` decimal(20,5) DEFAULT NULL COMMENT 'Weight in Kgs',
        `flag` int NOT NULL DEFAULT '0',
        `active` int NOT NULL DEFAULT '1',
        `dflag` int NOT NULL DEFAULT '0',
        `addedemp` varchar(300) DEFAULT NULL,
        `addedtime` datetime DEFAULT NULL,
        `updatedemp` varchar(300) DEFAULT NULL,
        `updatedtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Feed Scheduling';";
      mysqli_query($conn,$sql2);
}


$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

$sql = "SELECT * FROM `location_region` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1'  ".$line_access_filter1."".$branch_access_filter2."  ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; $line_branch[$row['code']] = $row['branch_code']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `gc_flag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_book[$row['code']] = $row['book_num']; $batch_gcflag[$row['code']] = $row['gc_flag']; $batch_farm1[$row['code']] = $row['farm_code']; $batch_farm2[$row['farm_code']] = $row['code']; }

$afarms = implode("','",$batch_farm1);
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." AND `code` IN ('$afarms') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code'];
    $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_svr[$row['supervisor_code']] = $row['code'];
    $farm_farmer[$row['code']] = $row['farmer_code']; $farm_batch[$row['code']] = $batch_farm2[$row['code']];
}

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $supervisor_code[$row['code']] = $row['code']; $supervisor_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; } $item_list = implode("','",$icat_code);

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; } $item_code[$chick_code] = $chick_code;

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_list') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
$feed_items = implode("','",$item_code);

$fdate = $tdate = $today = date("Y-m-d"); $branches = $lines = $supervisors = $farms = "all"; $excel_type = "display"; $excess_flag = 0;
if(isset($_POST['submit_report']) == true){
    $branches = $_POST['branches'];
    $lines = $_POST['lines'];
    $farms = $_POST['farms'];
    $supervisors = $_POST['supervisors'];
    $excess_flag = $_POST['excess_flag'];

    $farm_list = "";
    if($farms != "all"){
        $batches = $farm_batch[$farms];
        $batch_filter1 = " AND to_batch = '$batches'";
        $batch_filter2 = " AND farm_batch = '$batches'";
        $batch_filter3 = " AND batch_code = '$batches'";
        $batch_filter4 = " AND from_batch = '$batches'";
    }
    else if($supervisors != "all"){
        foreach($farm_code as $fcode){
            if($farm_supervisor[$fcode] == $supervisors){
                $batches = $farm_batch[$fcode];
                if($farm_list == ""){
                    $farm_list = $batches;
                }
                else{
                    $farm_list = $farm_list."','".$batches;
                }
            }
        }
        $batch_filter1 = " AND to_batch IN ('$farm_list')";
        $batch_filter2 = " AND farm_batch IN ('$farm_list')";
        $batch_filter3 = " AND batch_code IN ('$farm_list')";
        $batch_filter4 = " AND from_batch IN ('$farm_list')";
    }
    else if($lines != "all"){
        foreach($farm_code as $fcode){
            if($farm_line[$fcode] == $lines){
                $batches = $farm_batch[$fcode];
                if($farm_list == ""){
                    $farm_list = $batches;
                }
                else{
                    $farm_list = $farm_list."','".$batches;
                }
            }
        }
        $batch_filter1 = " AND to_batch IN ('$farm_list')";
        $batch_filter2 = " AND farm_batch IN ('$farm_list')";
        $batch_filter3 = " AND batch_code IN ('$farm_list')";
        $batch_filter4 = " AND from_batch IN ('$farm_list')";
    }
    else if($branches != "all"){
        foreach($farm_code as $fcode){
            if($farm_branch[$fcode] == $branches){
                $batches = $farm_batch[$fcode];
                if($farm_list == ""){
                    $farm_list = $batches;
                }
                else{
                    $farm_list = $farm_list."','".$batches;
                }
            }
        }
        $batch_filter1 = " AND to_batch IN ('$farm_list')";
        $batch_filter2 = " AND farm_batch IN ('$farm_list')";
        $batch_filter3 = " AND batch_code IN ('$farm_list')";
        $batch_filter4 = " AND from_batch IN ('$farm_list')";
    }
    else{
        foreach($farm_code as $fcode){
            $batches = $farm_batch[$fcode];
            if($farm_list == ""){
                $farm_list = $batches;
            }
            else{
                $farm_list = $farm_list."','".$batches;
            }
        }
        $batch_filter1 = " AND to_batch IN ('$farm_list')";
        $batch_filter2 = " AND farm_batch IN ('$farm_list')";
        $batch_filter3 = " AND batch_code IN ('$farm_list')";
        $batch_filter4 = " AND from_batch IN ('$farm_list')";
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
    <body align="center">
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="6" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Batch wise Feed Scheduling Report</h5></th>
                    <th colspan="292" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
                <form action="broiler_Livefarm_feedschedule.php" method="post" onsubmit="return chechval();">
            <?php } else { ?>
            <form action="broiler_Livefarm_feedschedule.php?db=<?php echo $db; ?>" method="post" onsubmit="return chechval();">
            <?php } ?>
            
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="15">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Branch</label>
                                    <select name="branches" id="branches" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($branches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($branch_code as $bcode){ if($branch_name[$bcode] != ""){ ?>
                                        <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Line</label>
                                    <select name="lines" id="lines" class="form-control select2" onchange="fetch_farms_details(this.id)">
                                        <option value="all" <?php if($lines == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($line_code as $lcode){ if($line_name[$lcode] != ""){ ?>
                                        <option value="<?php echo $lcode; ?>" <?php if($lines == $lcode){ echo "selected"; } ?>><?php echo $line_name[$lcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supervisor</label>
                                    <select name="supervisors" id="supervisors" class="form-control select2" onchange="fetch_farms_details(this.id)">
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
                                    <label>Excess Feed</label>
                                    <input type="checkbox" name="excess_flag" id="excess_flag" class="form-control" style='transform: scale(.5);' <?php if($excess_flag == 1 || $excess_flag == true || $excess_flag == "on"){ echo "checked"; } ?> />
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
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <thead class="thead3" align="center">
                <tr>
                    <th colspan="6"></th>
                    <th colspan="2">Scheduled Feed</th>
                    <th colspan="1">Purchase/Transfer In</th>
                    <th colspan="2">Consumption</th>
                    <th colspan="1">Feed Transfer Out</th>
                    <th colspan="1">Farm Stock</th>
                    <th colspan="2">Difference</th>
                </tr>
            </thead>
            <thead class="thead3" align="center">
                <tr>
                    <th>Sl.No</th>
                    <th>Farm</th>
                    <th>Batch</th>
                    <th>Age</th>
                    <th>Item</th>
                    <th>Placed Birds</th>
                    <th>Feed/Bird</th>
                    <th>Feed Qty</th>
                    <th>Feed In Qty</th>
                    <th>Feed/Bird</th>
                    <th>Consumed Qty</th>
                    <th>Transferred Out Qty</th>
                    <th>Available Qty</th>
                    <th>Feed/Bird</th>
                    <th>Feed/Kgs</th>
                </tr>
            </thead>
            <tbody class="tbody1">
                <?php
                $stk_in_qty = $stk_out_qty = $dentry_cons_qty = $$abatches = $aitems = array();
                $sql = "SELECT SUM(quantity) as quantity,code as item_code,to_batch as batch FROM `item_stocktransfers` WHERE `code` IN ('$feed_items') AND `active` = '1'".$batch_filter1." AND `dflag` = '0' GROUP BY `to_batch`,`item_code` ORDER BY `to_batch`,`item_code` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = ""; $key_code = $row['batch']."@".$row['item_code']; $stk_in_qty[$key_code] += (float)$row['quantity'];
                    $abatches[$row['batch']] = $row['batch']; $aitems[$row['item_code']] = $row['item_code'];
                }
                $sql = "SELECT SUM(rcd_qty) as quantity,icode as item_code,farm_batch as batch FROM `broiler_purchases` WHERE `icode` IN ('$feed_items') AND `active` = '1'".$batch_filter2." AND `dflag` = '0' GROUP BY `farm_batch`,`icode` ORDER BY `farm_batch`,`icode` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = ""; $key_code = $row['batch']."@".$row['item_code']; $stk_in_qty[$key_code] += (float)$row['quantity'];
                    $abatches[$row['batch']] = $row['batch']; $aitems[$row['item_code']] = $row['item_code'];
                }
                $sql = "SELECT * FROM `broiler_daily_record` WHERE `active` = '1'".$batch_filter3." AND `dflag` = '0' ORDER BY `batch_code` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code1 = $key_code2 = "";
                    $key_code1 = $row['batch_code']."@".$row['item_code1']; $dentry_cons_qty[$key_code1] += (float)$row['kgs1'];
                    $key_code2 = $row['batch_code']."@".$row['item_code2']; $dentry_cons_qty[$key_code2] += (float)$row['kgs2'];
                    if($row['avg_wt'] != "" && $row['avg_wt'] > 0){ $latest_avg_wt[$row['batch_code']] = $row['avg_wt']; }
                    $abatches[$row['batch_code']] = $row['batch_code']; $aitems[$row['item_code1']] = $row['item_code1']; $aitems[$row['item_code2']] = $row['item_code2'];
                }
                $sql = "SELECT MAX(brood_age) as brood_age,batch_code FROM `broiler_daily_record` WHERE `active` = '1'".$batch_filter3." AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `batch_code` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $age[$row['batch_code']] = $row['brood_age'];
                }
                $sql = "SELECT SUM(quantity) as quantity,code as item_code,from_batch as batch FROM `item_stocktransfers` WHERE `code` IN ('$feed_items') AND `active` = '1'".$batch_filter4." AND `dflag` = '0' GROUP BY `from_batch`,`item_code` ORDER BY `from_batch`,`item_code` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = ""; $key_code = $row['batch']."@".$row['item_code']; $stk_out_qty[$key_code] += (float)$row['quantity'];
                    $abatches[$row['batch']] = $row['batch']; $aitems[$row['item_code']] = $row['item_code'];
                }
                $sql = "SELECT * FROM `broiler_feedschedule_standard` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `item_code` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $std_feed_per_bird[$row['item_code']] = $row['quantity']; }

                foreach($abatches as $bcode){ if($bcode != ""){ $facodes[$batch_farm1[$bcode]] = $batch_farm1[$bcode]; } }

                $afarms = ""; $afarms = implode("','", $facodes);
                $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$afarms') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $afarm_code[$row['code']] = $row['code']; }

                $aitms = ""; $aitms = implode("','", $aitems);
                $sql = "SELECT * FROM `item_details` WHERE `code` IN ('$aitms') AND `dflag` = '0' ORDER BY `sort_order`,`description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $aitem_code[$row['code']] = $row['code']; }

                /*Fetch Details*/
                $slno = 0;
                foreach($afarm_code as $fcode){
                    $i = 0; 
                    foreach($aitem_code as $icode){
                        if(!empty($item_name[$icode])){
                            $bcode = $batch_farm2[$fcode];
                            $key = $bcode."@".$icode;

                            if(!empty($stk_in_qty[$key])){ $stk_in = $stk_in_qty[$key]; } else{ $stk_in = 0; }
                            if(!empty($stk_out_qty[$key])){ $stk_out = $stk_out_qty[$key]; } else{ $stk_out = 0; }
                            $stk_avail = 0; $stk_avail = $stk_in - $stk_out;
                            $std_feed_per_batch = 0; $std_feed_per_batch = (float)$std_feed_per_bird[$icode] * (float)$stk_in_qty[$bcode."@".$chick_code];

                            //echo "<br/>".$std_feed_per_batch."-".$dentry_cons_qty[$key]."-".$stk_avail;
                            if($excess_flag == 1 && (float)$std_feed_per_batch < (float)$dentry_cons_qty[$key] || $excess_flag == true && (float)$std_feed_per_batch < (float)$dentry_cons_qty[$key] || $excess_flag == "on" && (float)$std_feed_per_batch < (float)$dentry_cons_qty[$key] || $excess_flag == 0){
                                if($stk_in_qty[$key] != ""){
                                    $i++;
                                    echo "<tr>";
                                    if(!empty($stk_in_qty[$bcode."@".$chick_code]) && $stk_in_qty[$bcode."@".$chick_code] != 0){ $placed_birds = $stk_in_qty[$bcode."@".$chick_code]; }
                                    else { $placed_birds = 0; }
                                    if($i == 1){
                                        $slno++;
                                        echo "<td style='text-align:center;'>".$slno."</td>";
                                        echo "<td>".$farm_name[$fcode]."</td>";
                                        echo "<td>".$batch_name[$bcode]."</td>";
                                        echo "<td style='text-align:center;'>".$age[$bcode]."</td>";
                                        echo "<td>".$item_name[$icode]."</td>";
                                        echo "<td style='text-align:right;'>".str_replace('.00','',number_format_ind(round($placed_birds,2)))."</td>";
                                        $chickin_tqty += (float)$placed_birds;
                                    }
                                    else{
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td>".$item_name[$icode]."</td>";
                                        echo "<td></td>";
                                    }
                                    
                                    echo "<td style='text-align:right;'>".number_format_ind(round($std_feed_per_bird[$icode],2))."</td>";
                                    $std_feed_per_batch = 0; $std_feed_per_batch = (float)$std_feed_per_bird[$icode] * (float)$placed_birds;
                                    $tot_std_feed_qty += (float)$std_feed_per_batch;
                                    echo "<td style='text-align:right;'>".number_format_ind(round($std_feed_per_batch,2))."</td>";
                                    echo "<td style='text-align:right;'>".number_format_ind(round($stk_in_qty[$key],2))."</td>";

                                    $tot_feedin_qty += (float)$stk_in_qty[$key];
                                    $actual_feed_per_batch = 0; $actual_feed_per_batch = $dentry_cons_qty[$key];
                                    $tot_act_feed_qty += (float)$actual_feed_per_batch;
                                    $tot_sout_feed_qty += (float)round($stk_out_qty[$key],2);

                                    $cur_feed_stk_qty = (float)$stk_in_qty[$key] - ((float)$dentry_cons_qty[$key] + (float)round($stk_out_qty[$key],2));
                                    $tot_avail_stk_qty += $cur_feed_stk_qty;
                                    if(!empty($placed_birds) && $placed_birds != 0){
                                        $actual_feed_per_bird = (float)$actual_feed_per_batch / (float)$placed_birds;
                                    }
                                    else{
                                        $actual_feed_per_bird = 0;
                                    }
                                    $diff_feed_kgs = (((float)$std_feed_per_batch - (float)$actual_feed_per_batch));
                                    
                                    if($placed_birds > 0){
                                        $diff_feed_perbird = ($diff_feed_kgs / $placed_birds);
                                    }
                                    else{
                                        $diff_feed_perbird = 0;
                                    }
                                    if((float)$std_feed_per_bird[$icode] >= (float)$actual_feed_per_bird){
                                        echo "<td style='text-align:right;'>".number_format_ind(round($actual_feed_per_bird,2))."</td>";
                                        echo "<td style='text-align:right;'>".number_format_ind(round($actual_feed_per_batch,2))."</td>";
                                        echo "<td style='text-align:right;'>".number_format_ind(round($stk_out_qty[$key],2))."</td>";
                                        echo "<td style='text-align:right;'>".number_format_ind(round($cur_feed_stk_qty,2))."</td>";
                                        echo "<td style='color:green;text-align:right;'>".number_format_ind(round(((float)$diff_feed_perbird),2))."</td>";
                                        echo "<td style='color:green;text-align:right;'>".number_format_ind(round(((float)$diff_feed_kgs),2))."</td>";
                                    }
                                    else{
                                        echo "<td style='color:red;text-align:right;'>".number_format_ind(round($actual_feed_per_bird,2))."</td>";
                                        echo "<td style='color:red;text-align:right;'>".number_format_ind(round($actual_feed_per_batch,2))."</td>";
                                        echo "<td style='text-align:right;'>".number_format_ind(round($stk_out_qty[$key],2))."</td>";
                                        echo "<td style='text-align:right;'>".number_format_ind(round($cur_feed_stk_qty,2))."</td>";
                                        echo "<td style='color:green;text-align:right;'>".number_format_ind(round(((float)$diff_feed_perbird),2))."</td>";
                                        echo "<td style='color:green;text-align:right;'>".number_format_ind(round(((float)$diff_feed_kgs),2))."</td>";
                                    }
                                    $tot_diff_feed_qty += ((float)$std_feed_per_batch - (float)$actual_feed_per_batch);
                                    echo "</tr>";
                                }
                            }
                        }
                    }
                }
                ?>
            </tbody>
            <thead class="thead2">
                <tr>
                    <td style="text-align:right;" title="total" colspan="5"><b>Total</b></td>
                    <td style="text-align:right;"><b><?php echo number_format_ind($chickin_tqty); ?></b></td>
                    <td style="text-align:right;"><b>
                        <?php
                        if((float)$chickin_tqty != 0){
                            $t1 = (float)$tot_std_feed_qty / (float)$chickin_tqty;
                        }
                        else{
                            $t1 = 0;
                        }
                        echo number_format_ind($t1);
                        ?>
                    </b></td>
                    <td style="text-align:right;"><b><?php echo number_format_ind($tot_std_feed_qty); ?></b></td>
                    <td style="text-align:right;"><b><?php echo number_format_ind($tot_feedin_qty); ?></b></td>
                    <td style="text-align:right;"><b></b></td>
                    <td style="text-align:right;"><b><?php echo number_format_ind($tot_act_feed_qty); ?></b></td>
                    <td style="text-align:right;"><b><?php echo number_format_ind($tot_sout_feed_qty); ?></b></td>
                    <td style="text-align:right;"><b><?php echo number_format_ind($tot_avail_stk_qty); ?></b></td>
                    <td style="text-align:right;"><b>
                        <?php
                        if((float)$chickin_tqty != 0){
                            $diff_birds = (float)$tot_diff_feed_qty / (float)$chickin_tqty;
                        }
                        else{
                            $diff_birds = 0;
                        }
                        echo number_format_ind($diff_birds);
                        ?></b></td>
                    <td style="text-align:right;"><b><?php echo number_format_ind($tot_diff_feed_qty); ?></b></td>
                </tr>
            </thead>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script>
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
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                                
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
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
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
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_line[$fcode])){ $l_code = $farm_line[$fcode]; } else{ $l_code = ""; }
                                
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
                                if(!empty($farm_svr[$fcode])){ $f_code = $farm_svr[$fcode]; } else{ $f_code = ""; }
                                if(!empty($farm_branch[$fcode])){ $b_code = $farm_branch[$fcode]; } else{ $b_code = ""; }
                                
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
                                if(!empty($farm_svr[$fcode])){
                                    $f_code = $farm_svr[$fcode];
                                }
                                else{
                                    $f_code = "";
                                }
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
            function update_masterreport_status(a){
                var file_url = '<?php echo $field_href[0]; ?>';
                var user_code = '<?php echo $user_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url="+file_url+"&user_code="+user_code+"&field_name="+field_name;
                //window.open(url);
                var asynchronous = true;
                modify_col.open(method, url, asynchronous);
                modify_col.send();
                modify_col.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var item_list = this.responseText;
                        if(item_list == 0){
                            //alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.")
                        }
                        else{
                            alert("Invalid request \n Kindly check and try again ...!");
                        }
                    }
                }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>