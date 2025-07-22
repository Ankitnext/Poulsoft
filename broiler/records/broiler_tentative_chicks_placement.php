<?php
//broiler_daily_record4.php
$requested_data = json_decode(file_get_contents('php://input'),true);


    
session_start();
    
$db = $_SESSION['db'] = $_GET['db'];
if($db == ''){

    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Tentative Chicks Placement Report";
    include "header_head.php";
    $user_code = $_SESSION['userid'];
}else{

    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Tentative Chicks Placement Report";
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

$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `location_line` WHERE `active` = '1' ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $farm_code[$row['code']] = $row['code']; $farm_ccode[$row['code']] = $row['farm_code']; $farm_name[$row['code']] = $row['description'];
    $farm_branch[$row['code']] = $row['branch_code']; $farm_line[$row['code']] = $row['line_code']; $farm_supervisor[$row['code']] = $row['supervisor_code']; $farm_farmer[$row['code']] = $row['farmer_code']; $farm_capacity[$row['code']] = $row['farm_capacity'];
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

$fdate = $tdate = $today = date("Y-m-d"); $branches = $lines = $supervisors = $farms = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $branches = $_POST['branches'];

    $lines = $_POST['lines'];
    $farms = $_POST['farms'];
    $supervisors = $_POST['supervisors'];

    $cull_age = $_POST['cull_age'];
    $gap_days = $_POST['gap_days'];
    $farms =  "all";
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
                    <th colspan="8" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Tentative Chicks Placement Report</h5></th>
                    <th colspan="3" align="center" style="border-left:none;"></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_tentative_chicks_placement.php" method="post">
                <?php } else { ?>
                <form action="broiler_tentative_chicks_placement.php?db=<?php echo $db; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="13">
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
                                    <label>Batch Culling Age</label>
                                    <input type="text" name="cull_age" id="cull_age" placeholder="Enter Cull Age" value="<?php echo round($cull_age); ?>" class="form-control"  />
                                </div>
                                 <div class="m-2 form-group">
                                    <label>Gap Days</label>
                                    <input type="text" name="gap_days" id="gap_days" placeholder="Enter Gap Days" value="<?php echo round($gap_days); ?>" class="form-control"  />
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
                    <th id="order_num">Sl.No.</th>  
                    <th id="order_date">Tentative Placement Date</th>
                    <th id="order">Branch</th>  
                    <th id="order">Line</th>  
                    <th id="order">Supervisor</th>  
                    <th id="order">Farm Name</th>                  
                    <th id="order">Batch</th>
                    <th id="order_num">Placed Birds</th>
                    <th id="order_num">Age</th>  
                    <th id="order_date">Placement Date</th>
                    <th id="order_date">Last Entry Date</th>
                    <th id="order_date">Tentative Closing Date</th>
                    <th id="order_num">Farm Capacity</th>
                    
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
                $batch_all = $batch1 = $batch2 = $batch3 = "";
                $pur_qty = $sold_birds = $sale_qty = $sector_trin_qty = $farm_trin_qty = $farm_trout_qty = $sector_trout_qty = $medvac_qty = $dentry_mort = $dentry_feed = $dentry_age = $dentry_semp = array();
                $chick_placed_date = array();

                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $chick_codes[$row['code']] = $row['code']; }
                $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler bird%'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $bird_codes[$row['code']] = $row['code']; }

                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
                while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $feed_code[$row['code']] = $row['code']; }

                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
                while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }
            
                $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
                while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; } }
                $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $medvac_code[$row['code']] = $row['code']; }
            
                $batch_sql = "SELECT * FROM `broiler_batch` WHERE gc_flag = '0' AND active = '1' AND dflag = '0'"; $batch_query = mysqli_query($conn,$batch_sql);
                while($row = mysqli_fetch_assoc($batch_query)){ if($batch_all == ""){ $batch_all = $row['code']; } else{ $batch_all = $batch_all."','".$row['code']; } }
                
              //  $batch_sql = "SELECT a.code as batch_code,a.description as batch_name,a.farm_code as farm_code,b.description as farm_name,MAX(c.brood_age) as age FROM broiler_batch a,broiler_farm b,broiler_daily_record c WHERE a.farm_code = b.code AND a.farm_code = c.farm_code".$farm_query." AND a.code IN ('$batch_all') AND c.batch_code = a.code AND a.gc_flag = '0' AND a.active = '1' AND a.dflag = '0' AND c.active = '1' AND c.dflag = '0' GROUP BY b.code ORDER BY age DESC, farm_name ASC";
                  $batch_sql = "SELECT a.code as batch_code,a.description as batch_name,a.farm_code as farm_code,b.description as farm_name,MAX(c.brood_age) as age,MIN(c.date) as min_entrydate FROM broiler_batch a,broiler_farm b,broiler_daily_record c WHERE a.farm_code = b.code AND a.farm_code = c.farm_code".$farm_query." AND a.code IN ('$batch_all') AND c.batch_code = a.code AND a.gc_flag = '0' AND a.active = '1' AND a.dflag = '0' AND c.active = '1' AND c.dflag = '0' GROUP BY b.code ORDER BY `min_entrydate`  ASC";
                $batch_query = mysqli_query($conn,$batch_sql);
                 $i = 0;
                while($batch_row = mysqli_fetch_assoc($batch_query)){
                    $i++; 
                    $batch_list[$i] = $batch_row['batch_code'];
                     $batch_age[$batch_row['batch_code']] = $batch_row['age']; 
                     $batch_farm[$batch_row['batch_code']] = $batch_row['farm_code'];
                    if($batch1 == ""){ $batch1 = $batch_row['batch_code']; } else{ $batch1 = $batch1."','".$batch_row['batch_code']; }
                }
                 $sql = "SELECT a.code as code,a.farm_code as farm_code,b.description as farm_name FROM broiler_batch a,broiler_farm b,broiler_daily_record c  WHERE a.gc_flag = '0' AND a.farm_code = b.code AND a.code NOT IN ('$batch1')  $farm_query GROUP BY b.code ORDER BY c.brood_age DESC"; 
               
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $i++; $batch_list[$i] = $row['code']; $batch_age[$row['code']] = 0; $batch_farm[$row['code']] = $row['farm_code']; }
                
                $sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ if($batch3 == ""){ $batch3 = $row['code']; } else{ $batch3 = $batch3."','".$row['code']; } }

                foreach($batch_list as $batches){ if($batch2 == ""){ $batch2 = $batches; } else{ $batch2 = $batch2."','".$batches; } }
                $start_date = $end_date = array();
                //Purchases
                $sql_record = "SELECT SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as item_tamt,MIN(date) as sdate,MAX(date) as edate,icode,farm_batch FROM `broiler_purchases` WHERE `farm_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch`,`icode` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['farm_batch']."@".$row['icode'];
                    if(!empty($chick_codes[$row['icode']])){
                        $pur_chick_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                        if(empty($chick_placed_date[$row['farm_batch']])){ $chick_placed_date[$row['farm_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['farm_batch']]){ $chick_placed_date[$row['farm_batch']] = strtotime($row['sdate']); } }
                    }
                    else if(!empty($feed_code[$row['icode']])){
                        $pur_feed_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                    }
                    else if(!empty($medvac_code[$row['icode']])){
                        $pur_medvac_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                    }
                    else{
                        $pur_other_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                    }
                    if(empty($start_date[$row['farm_batch']])){ $start_date[$row['farm_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['farm_batch']]){ $start_date[$row['farm_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['farm_batch']])){ $end_date[$row['farm_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['farm_batch']]){ $end_date[$row['farm_batch']] = strtotime($row['edate']); } }
                    
                }
                //Sales
                $sql_record = "SELECT SUM(birds) as birds,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty,SUM(item_tamt) as item_tamt,MIN(date) as sdate,MAX(date) as edate,icode,farm_batch FROM `broiler_sales` WHERE `farm_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `farm_batch`,`icode` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['farm_batch']."@".$row['icode'];
                    if(!empty($chick_codes[$row['icode']]) || !empty($bird_codes[$row['icode']])){
                        $sale_bird_nos[$key_code] = $row['birds'];
                        $sale_bird_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                        if(empty($sale_start_date[$key_code])){ $sale_start_date[$key_code] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $sale_start_date[$key_code]){ $sale_start_date[$key_code] = strtotime($row['sdate']); } }
                    }
                    else if(!empty($feed_code[$row['icode']])){
                        $sale_feed_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                    }
                    else if(!empty($medvac_code[$row['icode']])){
                        $sale_medvac_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                    }
                    else{
                        $sale_other_qty[$key_code] = $row['rcd_qty'] + $row['fre_qty'];
                    }
                    if(empty($start_date[$row['farm_batch']])){ $start_date[$row['farm_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['farm_batch']]){ $start_date[$row['farm_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['farm_batch']])){ $end_date[$row['farm_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['farm_batch']]){ $end_date[$row['farm_batch']] = strtotime($row['edate']); } }
                }
                $sql = "SELECT * FROM `broiler_sales` WHERE `farm_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['date']."@".$row['icode']."@".$row['farm_batch'];
                    $sal_birds[$key] = $sal_birds[$key] + $row['birds'];
                }
                //Transfer IN From Farm to Farm
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,MIN(date) as sdate,MAX(date) as edate,code,to_batch FROM `item_stocktransfers` WHERE `from_batch` IN ('$batch3') AND `to_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch`,`code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['to_batch']."@".$row['code'];
                    if(!empty($chick_codes[$row['code']]) || !empty($bird_codes[$row['code']])){
                        $farm_trin_bird_qty[$key_code] = $row['quantity'];
                        if(empty($chick_placed_date[$row['to_batch']])){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['to_batch']]){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); } }
                    }
                    else if(!empty($feed_code[$row['code']])){
                        $farm_trin_feed_qty[$key_code] = $row['quantity'];
                    }
                    else if(!empty($medvac_code[$row['code']])){
                        $farm_trin_medvac_qty[$key_code] = $row['quantity'];
                    }
                    else{
                        $farm_trin_other_qty[$key_code] = $row['quantity'];
                    }
                    if(empty($start_date[$row['to_batch']])){ $start_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['to_batch']]){ $start_date[$row['to_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['to_batch']])){ $end_date[$row['to_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['to_batch']]){ $end_date[$row['to_batch']] = strtotime($row['edate']); } }
                }
                //Transfer IN From Warehouse to Farm
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,MIN(date) as sdate,MAX(date) as edate,code,to_batch FROM `item_stocktransfers` WHERE `from_batch` NOT IN ('$batch3') AND `to_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `to_batch`,`code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['to_batch']."@".$row['code'];
                    if(!empty($chick_codes[$row['code']]) || !empty($bird_codes[$row['code']])){
                        $sector_trin_bird_qty[$key_code] = $row['quantity'];
                        if(empty($chick_placed_date[$row['to_batch']])){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $chick_placed_date[$row['to_batch']]){ $chick_placed_date[$row['to_batch']] = strtotime($row['sdate']); } }
                    }
                    else if(!empty($feed_code[$row['code']])){
                        $sector_trin_feed_qty[$key_code] = $row['quantity'];
                    }
                    else if(!empty($medvac_code[$row['code']])){
                        $sector_trin_medvac_qty[$key_code] = $row['quantity'];
                    }
                    else{
                        $sector_trin_other_qty[$key_code] = $row['quantity'];
                    }
                    if(empty($start_date[$row['to_batch']])){ $start_date[$row['to_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['to_batch']]){ $start_date[$row['to_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['to_batch']])){ $end_date[$row['to_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['to_batch']]){ $end_date[$row['to_batch']] = strtotime($row['edate']); } }
                }
                //Transfer OUT From Farm to Farm
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,MIN(date) as sdate,MAX(date) as edate,code,from_batch FROM `item_stocktransfers` WHERE `to_batch` IN ('$batch3') AND `from_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch`,`code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['from_batch']."@".$row['code'];
                    if(!empty($chick_codes[$row['code']]) || !empty($bird_codes[$row['code']])){
                        $farm_trout_bird_qty[$key_code] = $row['quantity'];
                    }
                    else if(!empty($feed_code[$row['code']])){
                        $farm_trout_feed_qty[$key_code] = $row['quantity'];
                    }
                    else if(!empty($medvac_code[$row['code']])){
                        $farm_trout_medvac_qty[$key_code] = $row['quantity'];
                    }
                    else{
                        $farm_trout_other_qty[$key_code] = $row['quantity'];
                    }
                    if(empty($start_date[$row['from_batch']])){ $start_date[$row['from_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['from_batch']]){ $start_date[$row['from_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['from_batch']])){ $end_date[$row['from_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['from_batch']]){ $end_date[$row['from_batch']] = strtotime($row['edate']); } }
                }
                //Transfer OUT From Warehouse to Farm
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(amount) as amount,MIN(date) as sdate,MAX(date) as edate,code,from_batch FROM `item_stocktransfers` WHERE `to_batch` NOT IN ('$batch3') AND `from_batch` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `from_batch`,`code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record);
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['from_batch']."@".$row['code'];
                    if(!empty($chick_codes[$row['code']]) || !empty($bird_codes[$row['code']])){
                        $sector_trout_bird_qty[$key_code] = $row['quantity'];
                    }
                    else if(!empty($feed_code[$row['code']])){
                        $sector_trout_feed_qty[$key_code] = $row['quantity'];
                    }
                    else if(!empty($medvac_code[$row['code']])){
                        $sector_trout_medvac_qty[$key_code] = $row['quantity'];
                    }
                    else{
                        $sector_trout_other_qty[$key_code] = $row['quantity'];
                    }
                    if(empty($start_date[$row['from_batch']])){ $start_date[$row['from_batch']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['from_batch']]){ $start_date[$row['from_batch']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['from_batch']])){ $end_date[$row['from_batch']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['from_batch']]){ $end_date[$row['from_batch']] = strtotime($row['edate']); } }
                }
                //Day record
                $sql_record = "SELECT SUM(mortality) as mortality,SUM(culls) as culls,SUM(kgs1) as kgs1,SUM(kgs2) as kgs2,MIN(date) as sdate,MAX(date) as edate,MAX(brood_age) as brood_age,batch_code,supervisor_code FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY brood_age DESC";
                $query = mysqli_query($conn,$sql_record); $i = 1;
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['batch_code'];
                    $dentry_mort[$key_code] = $row['mortality'] + $row['culls'];
                    $dentry_feed[$key_code] = $row['kgs1'] + $row['kgs2'];
                    $dentry_age[$key_code] = $row['brood_age'];
                    $dentry_min_date[$key_code] = $row['sdate'];
                    $dentry_max_date[$key_code] = $row['edate'];
                    $dentry_semp[$key_code] = $row['supervisor_code'];
                    if(empty($start_date[$row['batch_code']])){ $start_date[$row['batch_code']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['batch_code']]){ $start_date[$row['batch_code']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['batch_code']])){ $end_date[$row['batch_code']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['batch_code']]){ $end_date[$row['batch_code']] = strtotime($row['edate']); } }
                }
                $sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' ORDER BY brood_age DESC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $day_ages[$row['date']."@".$row['batch_code']] = $row['brood_age'];
            
                }
                //Medicine Record
                $sql_record = "SELECT SUM(quantity) as quantity,SUM(batch_code) as batch_code,MIN(date) as sdate,MAX(date) as edate FROM `broiler_medicine_record` WHERE `batch_code` IN ('$batch2') AND `active` = '1' AND `dflag` = '0' GROUP BY `batch_code` ORDER BY `date`,`trnum` ASC";
                $query = mysqli_query($conn,$sql_record); $i = 1;
                while($row = mysqli_fetch_assoc($query)){
                    $key_code = $row['batch_code'];
                    $medvac_qty[$key_code] = $row['quantity'];
                    if(empty($start_date[$row['batch_code']])){ $start_date[$row['batch_code']] = strtotime($row['sdate']); }else{ if(strtotime($row['sdate']) <= $start_date[$row['batch_code']]){ $start_date[$row['batch_code']] = strtotime($row['sdate']); } }
                    if(empty($end_date[$row['batch_code']])){ $end_date[$row['batch_code']] = strtotime($row['edate']); }else{ if(strtotime($row['edate']) >= $end_date[$row['batch_code']]){ $end_date[$row['batch_code']] = strtotime($row['edate']); } }
                }
                $slno = $total_housed_chicks = $total_mort_chicks = $total_sold_chicks = $total_aval_chicks = $total_short_chicks = $total_exccess_chicks = $total_feedin_chicks = $total_feedin_farm_chicks = $total_feed_consumed_chicks = $total_feedout_farm_chicks = $total_feed_bal_chicks = 0;
                //Display section
                
                    foreach($batch_list as $batches){
                    $brood_age = $batch_age[$batches];
                    $fetch_fcode = $batch_farm[$batches];
                    if($batches != ""){
                    ?>
                        <tbody class="tbody1" id="tbody1">
                            <?php
                            //Chick or Bird Transactions
                            $purchase_chicks = $farm_transferin_chicks = $sector_transferin_chicks = $mortality_chicks = 0;
                            $sales_birds_nos = $sales_birds_qty = $farm_transferout_birds = $sector_transferout_birds = 0;
                            //Purchase or Transfer in
                            if(!empty($pur_chick_qty[$batches."@".$chick_code])){
                                $purchase_chicks = $purchase_chicks + $pur_chick_qty[$batches."@".$chick_code];
                            }
                            if(!empty($pur_chick_qty[$batches."@".$bird_code])){
                                $purchase_chicks = $purchase_chicks + $pur_chick_qty[$batches."@".$bird_code];
                            }
                            if(!empty($farm_trin_bird_qty[$batches."@".$chick_code])){
                                $farm_transferin_chicks = $farm_transferin_chicks + $farm_trin_bird_qty[$batches."@".$chick_code];
                            }
                            if(!empty($farm_trin_bird_qty[$batches."@".$bird_code])){
                                $farm_transferin_chicks = $farm_transferin_chicks + $farm_trin_bird_qty[$batches."@".$bird_code];
                            }
                            if(!empty($sector_trin_bird_qty[$batches."@".$chick_code])){
                                $sector_transferin_chicks = $sector_transferin_chicks + $sector_trin_bird_qty[$batches."@".$chick_code];
                            }
                            if(!empty($sector_trin_bird_qty[$batches."@".$bird_code])){
                                $sector_transferin_chicks = $sector_transferin_chicks + $sector_trin_bird_qty[$batches."@".$bird_code];
                            }
                            //Mortality
                            if(!empty($dentry_mort[$batches])){
                                $mortality_chicks = $mortality_chicks + $dentry_mort[$batches];
                            }
                            //Sale or Transfer Out
                            if(!empty($sale_bird_nos[$batches."@".$chick_code])){
                                $sales_birds_nos = $sales_birds_nos + $sale_bird_nos[$batches."@".$chick_code];
                            }
                            if(!empty($sale_bird_nos[$batches."@".$bird_code])){
                                $sales_birds_nos = $sales_birds_nos + $sale_bird_nos[$batches."@".$bird_code];
                            }
                            if(!empty($sale_bird_qty[$batches."@".$chick_code])){
                                $sales_birds_qty = $sales_birds_qty + $sale_bird_qty[$batches."@".$chick_code];
                            }
                            if(!empty($sale_bird_qty[$batches."@".$bird_code])){
                                $sales_birds_qty = $sales_birds_qty + $sale_bird_qty[$batches."@".$bird_code];
                            }
                            if(!empty($farm_trout_bird_qty[$batches."@".$chick_code])){
                                $farm_transferout_birds = $farm_transferout_birds + $farm_trout_bird_qty[$batches."@".$chick_code];
                            }
                            if(!empty($farm_trout_bird_qty[$batches."@".$bird_code])){
                                $farm_transferout_birds = $farm_transferout_birds + $farm_trout_bird_qty[$batches."@".$bird_code];
                            }
                            if(!empty($sector_trout_bird_qty[$batches."@".$chick_code])){
                                $sector_transferout_birds = $sector_transferout_birds + $sector_trout_bird_qty[$batches."@".$chick_code];
                            }
                            if(!empty($sector_trout_bird_qty[$batches."@".$bird_code])){
                                $sector_transferout_birds = $sector_transferout_birds + $sector_trout_bird_qty[$batches."@".$bird_code];
                            }
                            //Feed Transactions
                            $purchase_feeds = $farm_transferin_feeds = $sector_transferin_feeds = $consumed_feeds = $sales_feeds = $farm_transferout_feeds = $sector_transferout_feeds = 0;

                            foreach($feed_code as $fcode){
                                //Purchase or Transfer in
                                if(!empty($pur_feed_qty[$batches."@".$fcode])){
                                    $purchase_feeds = $purchase_feeds + $pur_feed_qty[$batches."@".$fcode];
                                }
                                if(!empty($farm_trin_feed_qty[$batches."@".$fcode])){
                                    $farm_transferin_feeds = $farm_transferin_feeds + $farm_trin_feed_qty[$batches."@".$fcode];
                                }
                                if(!empty($sector_trin_feed_qty[$batches."@".$fcode])){
                                    $sector_transferin_feeds = $sector_transferin_feeds + $sector_trin_feed_qty[$batches."@".$fcode];
                                }
                                
                                //Sale or Transfer Out
                                if(!empty($sale_feed_qty[$batches."@".$fcode])){
                                    $sales_feeds = $sales_feeds + $sale_feed_qty[$batches."@".$fcode];
                                }
                                if(!empty($farm_trout_feed_qty[$batches."@".$fcode])){
                                    $farm_transferout_feeds = $farm_transferout_feeds + $farm_trout_feed_qty[$batches."@".$fcode];
                                }
                                if(!empty($sector_trout_feed_qty[$batches."@".$fcode])){
                                    $sector_transferout_feeds = $sector_transferout_feeds + $sector_trout_feed_qty[$batches."@".$fcode];
                                }
                            }
                            //Feed Consumed
                            if(!empty($dentry_feed[$batches])){
                                $consumed_feeds = $consumed_feeds + $dentry_feed[$batches];
                            }
                            //Medicine & Vaccine Transactions
                            $purchase_medvacs = $farm_transferin_medvacs = $sector_transferin_medvacs = $consumed_medvacs = $sales_medvacs = $farm_transferout_medvacs = $sector_transferout_medvacs = 0;

                            foreach($medvac_code as $fcode){
                                //Purchase or Transfer in
                                if(!empty($pur_medvac_qty[$batches."@".$fcode])){
                                    $purchase_medvacs = $purchase_medvacs + $pur_medvac_qty[$batches."@".$fcode];
                                }
                                if(!empty($farm_trin_medvac_qty[$batches."@".$fcode])){
                                    $farm_transferin_medvacs = $farm_transferin_medvacs + $farm_trin_medvac_qty[$batches."@".$fcode];
                                }
                                if(!empty($sector_trin_medvac_qty[$batches."@".$fcode])){
                                    $sector_transferin_medvacs = $sector_transferin_medvacs + $sector_trin_medvac_qty[$batches."@".$fcode];
                                }
                                //Medicine Consumption
                                if(!empty($medvac_qty[$batches])){
                                    $consumed_medvacs = $consumed_medvacs + $medvac_qty[$batches];
                                }
                                //Sale or Transfer Out
                                if(!empty($sale_medvac_qty[$batches."@".$fcode])){
                                    $sales_medvacs = $sales_medvacs + $sale_medvac_qty[$batches."@".$fcode];
                                }
                                if(!empty($farm_trout_medvac_qty[$batches."@".$fcode])){
                                    $farm_transferout_medvacs = $farm_transferout_medvacs + $farm_trout_medvac_qty[$batches."@".$fcode];
                                }
                                if(!empty($sector_trout_medvac_qty[$batches."@".$fcode])){
                                    $sector_transferout_medvacs = $sector_transferout_medvacs + $sector_trout_medvac_qty[$batches."@".$fcode];
                                }
                            }

                            $display_age = $display_line = $display_farm_capacity = $display_supervisor = $display_farmbranch = $display_farmname = $display_farbatch = $display_placement_date = $display_lifting_start_date = $mean_age_total = $display_mean_age = $display_recent_entry_date = $display_gap_days = $display_housed_chicks = $display_mort = $display_mortper = $display_sold_birds = $display_available_birds = $display_availableavg_body_wt = $display_fcr = $display_cfcr = $display_eef = $display_shortage_birds = $display_feeds_transferred = $display_feeds_in_farm = $display_feeds_consumed = $display_feeds_out_farm = $display_feeds_balance = 0;
                            //$display_age = $brood_age;
                            $display_line = $line_name[$farm_line[$fetch_fcode]];
                            $display_farm_capacity = $farm_capacity[$fetch_fcode];
                            $display_supervisor = $supervisor_name[$farm_supervisor[$fetch_fcode]];
                            $display_farmbranch = $branch_name[$farm_branch[$fetch_fcode]];
                            $display_farmname = $farm_name[$fetch_fcode];
                            $display_farbatch = $batch_name[$batches];
                            if(!empty($display_farbatch) && date("d.m.Y",$chick_placed_date[$batches]) != "01.01.1970"){
                                if(date("d.m.Y",$chick_placed_date[$batches]) != "01.01.1970"){
                                    $display_placement_date = $chick_placed_date[$batches];
                                }
                                else if(date("d.m.Y",strtotime($dentry_min_date[$batches])) != "01.01.1970"){
                                    $display_placement_date = strtotime($dentry_min_date[$batches]);
                                }
                                else{
                                    $display_placement_date = strtotime($dentry_min_date[$batches]);
                                }
                                $display_age = ((strtotime($today) - $display_placement_date) / 60 / 60 / 24)+1;
                                $display_lifting_start_date = $sale_start_date[$batches."@".$bird_code];

                                $fdate = $start_date[$batches]; $tdate = $end_date[$batches];
                                for($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)){
                                    $present_date = date("Y-m-d",$currentDate);
                                    if(!empty($sal_birds[$present_date."@".$bird_code."@".$batches])){
                                        $mean_age_total = $mean_age_total + ($day_ages[$present_date."@".$batches] * $sal_birds[$present_date."@".$bird_code."@".$batches]);
                                    }
                                }
                                if($mean_age_total > 0 && $mean_age_total > 0){
                                    $display_mean_age = $mean_age_total / $sales_birds_nos;
                                }
                                else{
                                    $display_mean_age = 0;
                                }
                                
                                $display_recent_entry_date = strtotime($dentry_max_date[$batches]);
                                if(date("d.m.Y",strtotime($dentry_max_date[$batches])) != "01.01.1970"){
                                    $display_gap_days = ((strtotime(date("d.m.Y")) - strtotime($dentry_max_date[$batches])) / 60 / 60 / 24);
                                }
                                else{
                                    $display_gap_days = ((strtotime(date("d.m.Y")) - $chick_placed_date[$batches]) / 60 / 60 / 24);
                                }
                                $display_housed_chicks = $purchase_chicks + $farm_transferin_chicks + $sector_transferin_chicks;
                                $display_mort = $mortality_chicks;
                                if($display_mort > 0 && $display_housed_chicks > 0){
                                    $display_mortper = (($display_mort / $display_housed_chicks) * 100);
                                }
                                else{
                                    $display_mortper = 0;
                                }
                                
                                $display_sold_birds = $sales_birds_nos;
                                $display_available_birds = ($display_housed_chicks - ($display_mort + $display_sold_birds));
                                if($sales_birds_qty > 0 && $sales_birds_nos > 0){
                                    $display_availableavg_body_wt = ($sales_birds_qty / $sales_birds_nos);
                                }
                                else{
                                    $display_availableavg_body_wt = 0;
                                }
                                
                                if($sales_birds_qty > 0 && $consumed_feeds > 0) {
                                    $display_fcr = ($consumed_feeds / $sales_birds_qty);
                                }
                                else{
                                    $display_fcr = 0;
                                }
                                if($display_availableavg_body_wt > 0){
                                    $display_cfcr = (((2 - ($display_availableavg_body_wt)) / 4) + $display_fcr);
                                }
                                else{
                                    $display_cfcr = 0;
                                }
                                $t1 = 0; $t1 = ($display_housed_chicks - $display_mort);
                                $t2 = 0; $t2 = $display_housed_chicks;
                                $t3 = 0; $t3 = $display_availableavg_body_wt;
                                $t4 = 0; $t4 = ($display_fcr * $display_mean_age);
                                if($t1 > 0 && $t2 > 0 && $t3 > 0 && $t4 > 0){
                                    $display_eef = ((((($t1 / $t2) * 100) * $t3) * 100) / ($t4));
                                }
                                else{
                                    $display_eef = 0;
                                }
                                
                                if($display_available_birds > 0){ $display_shortage_birds = 0; $display_excess_birds = $display_available_birds; } else{ $display_shortage_birds = (($display_mort + $display_sold_birds) - $display_housed_chicks); $display_excess_birds = 0; }
                                $display_feeds_transferred = $purchase_feeds + $sector_transferin_feeds;
                                $display_feeds_in_farm = $farm_transferin_feeds;
                                $display_feeds_consumed = $consumed_feeds;
                                $display_feeds_out_farm = $farm_transferout_feeds;
                                $display_feeds_balance = (($display_feeds_transferred + $display_feeds_in_farm) - ($display_feeds_consumed + $display_feeds_out_farm + $sales_feeds + $sector_transferout_feeds));
                                
                                $slno++;
                                $total_housed_chicks = $total_housed_chicks + $display_housed_chicks;
                                $total_mort_chicks = $total_mort_chicks + $display_mort;
                                $total_sold_chicks = $total_sold_chicks + $display_sold_birds;
                                $total_aval_chicks = $total_aval_chicks + $display_available_birds;
                                $total_short_chicks = $total_short_chicks + $display_shortage_birds;
                                $total_exccess_chicks = $total_exccess_chicks + $display_excess_birds;
                                $total_feedin_chicks = $total_feedin_chicks + $display_feeds_transferred;
                                $total_feedin_farm_chicks = $total_feedin_farm_chicks + $display_feeds_in_farm;
                                $total_feed_consumed_chicks = $total_feed_consumed_chicks + $display_feeds_consumed;
                                $total_feedout_farm_chicks = $total_feedout_farm_chicks + $display_feeds_out_farm;
                                $total_feed_bal_chicks = $total_feed_bal_chicks + $display_feeds_balance;

                                if($cull_age != ''){

                                    $Date1 = date("Y-m-d",((int)$display_placement_date));
                                    if($Date1 == "1970-01-01"){
                                        $tentative_cull_date = $Date1;
                                    }
                                    else{
                                        $tentative_cull_date = date('Y-m-d', strtotime($Date1 . " + ".$cull_age." day"));
                                    }
                                }

                                 if($gap_days != ''){


                                    $Date1 = $tentative_cull_date;
                                    if($Date1 == "1970-01-01"){
                                        $tentative_chick_place_date = $Date1;
                                    }
                                    else{
                                        $tentative_chick_place_date = date('Y-m-d', strtotime($Date1 . " + ".$gap_days." day"));
                                    }
                                }

                                ?>
                                <tr>
                                    <td title="Sl.No." style="text-align:center;"><?php echo $slno; ?></td>
                                    <td title="ten placement date" style="text-align:left;"><?php if(date("d.m.Y",strtotime($tentative_chick_place_date)) == "01.01.1970"){ echo ""; } else{ echo date("d.m.Y",strtotime($tentative_chick_place_date)); }   ?></td>
                                    <td title="Branch"><?php echo $display_farmbranch; ?></td>
                                    <td title="Line"><?php echo $display_line; ?></td>
                                    <td title="Supervisor"><?php echo $display_supervisor; ?></td>
                                    <td title="Farm Name"><?php echo $display_farmname; ?></td>
                                    <td title="Batch"><?php echo $display_farbatch; ?></td>
                                    <td title="placed birds" style="text-align:right;"><?php echo number_format($display_housed_chicks); ?></td>
                                    <td style="text-align:center;" title="Age"><?php if(date("d.m.Y",((int)$display_placement_date)) == "01.01.1970"){ echo ""; } else{ echo round($display_age); } ?></td>
                                    <td title="Placement Date"><?php if(date("d.m.Y",((int)$display_placement_date)) == "01.01.1970"){ echo ""; } else{ echo date("d.m.Y",((int)$display_placement_date)); } ?></td>
                                    <td title="Last Entry Date"><?php if(date("d.m.Y",((int)$display_recent_entry_date)) == "01.01.1970"){ echo ""; } else{ echo date("d.m.Y",((int)$display_recent_entry_date)); } ?></td>
                                    <td title="Cull Date" style="text-align:left;"><?php if(date("d.m.Y",strtotime($tentative_cull_date)) == "01.01.1970"){ echo ""; } else{ echo date("d.m.Y",strtotime($tentative_cull_date)); }  ?></td>

                                    <td style="text-align:right;" title="farm capacity"><?php echo $display_farm_capacity; ?></td>
                                   
                                </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    <?php
                    }
                    ?>
                   <!--  <tfoot>
                    <tr class="thead4">
                        <th colspan="11" style="text-align:center;">Total</th>
                        <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_housed_chicks)); ?></th>
                        <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_mort_chicks)); ?></th>
                        <th style="text-align:left;"></th>
                        <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_sold_chicks)); ?></th>
                        <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_aval_chicks)); ?></th>
                        <th style="text-align:left;"></th>
                        <th style="text-align:left;"></th>
                        <th style="text-align:left;"></th>
                        <th style="text-align:left;"></th>
                        <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_short_chicks)); ?></th>
                        <th style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($total_exccess_chicks)); ?></th>
                        <th style="text-align:right;"><?php echo number_format_ind($total_feedin_chicks); ?></th>
                        <th style="text-align:right;"><?php echo number_format_ind($total_feedin_farm_chicks); ?></th>
                        <th style="text-align:right;"><?php echo number_format_ind($total_feed_consumed_chicks); ?></th>
                        <th style="text-align:right;"><?php echo number_format_ind($total_feedout_farm_chicks); ?></th>
                        <th style="text-align:right;"><?php echo number_format_ind($total_feed_bal_chicks); ?></th>
                    </tr>
                </tfoot> -->
                <?php
                }
				
            ?>
        </table>

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