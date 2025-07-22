<?php
//broiler_itemwisererate.php
date_default_timezone_set("Asia/Kolkata");
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Rate Correction Report";
include "header_head.php";
$user_code = $_SESSION['userid'];

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

$sql = "SELECT * FROM `broiler_farm` WHERE 1 ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2.""; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'  ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $sector_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; $batch_farm[$row['code']] = $row['farm_code']; $batch_cflag[$row['code']] = $row['gc_flag']; }

$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler chick%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; }
$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler bird%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%feed%'"; $query = mysqli_query($conn,$sql); $item_cat = "";
while($row = mysqli_fetch_assoc($query)){
    $item_cat_code[$row['code']] = $row['code']; $item_cat_name[$row['code']] = $row['description']; $feed_cat[$row['code']] = $row['code'];
    $icat_iac[$row['code']] = $row['iac']; $icat_cogsac[$row['code']] = $row['cogsac']; $icat_wpac[$row['code']] = $row['wpac'];
    if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; }
}

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%medicine%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $item_cat_code[$row['code']] = $row['code']; $item_cat_name[$row['code']] = $row['description']; $medvac_cat[$row['code']] = $row['code'];
    $icat_iac[$row['code']] = $row['iac']; $icat_cogsac[$row['code']] = $row['cogsac']; $icat_wpac[$row['code']] = $row['wpac'];
    if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; }
}

$sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%vaccine%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $item_cat_code[$row['code']] = $row['code']; $item_cat_name[$row['code']] = $row['description']; $medvac_cat[$row['code']] = $row['code'];
    $icat_iac[$row['code']] = $row['iac']; $icat_cogsac[$row['code']] = $row['cogsac']; $icat_wpac[$row['code']] = $row['wpac'];
    if( $item_cat == ""){  $item_cat = $row['code'];} else{ $item_cat = $item_cat."','".$row['code']; }
}

$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat')"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$fdate = $tdate = date("Y-m-d"); $farms = "select"; $itm_code = $item_cats = $batches = "all"; $status = $excel_type = "display"; $feed_sort = $medvac_sort = "ASC";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate'])); $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $farms = $_POST['farms']; $batches = $_POST['batches'];
    $itm_code = $_POST['items'];
    $item_cats = $_POST['item_cats'];
    $status = $_POST['status'];
    $feed_sort = $_POST['feed_sort'];
    $medvac_sort = $_POST['medvac_sort'];
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
    <body align="center">
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'purchases Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Rate Correction Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_itemwisererate.php" method="post" onsubmit="return checkval();">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="14">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Farm</label>
                                    <select name="farms" id="farms" class="form-control select2" onchange="fetch_farm_batch(this.id)">
                                        <option value="select" <?php if($farms == "select"){ echo "selected"; } ?>>-select-</option>
                                        <?php foreach($farm_code as $fcode){ if($farm_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Batch</label>
                                    <select name="batches" id="batches" class="form-control select2" style="width:160px;">
                                        <option value="select" <?php if($batches == "select"){ echo "selected"; } ?>>-Select-</option>
                                        <option value="all" <?php if($batches == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php
                                        foreach($batch_code as $bcode){
                                            if($farms == "all" || $farms == "select"){ }
                                            else{
                                                if($batch_farm[$bcode] == $farms){
                                                    if(!empty($batch_name[$bcode])){
                                        ?>
                                            <option value="<?php echo $bcode; ?>" <?php if($batches == $bcode){ echo "selected"; } ?>><?php echo $batch_name[$bcode]; ?></option>
                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Item Category</label>
                                    <select name="item_cats" id="item_cats" class="form-control select2" onchange="fetch_category_items();">
                                        <option value="all" <?php if($item_cats == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($item_cat_code as $fcode){ if($item_cat_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($item_cats == $fcode){ echo "selected"; } ?>><?php echo $item_cat_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Item</label>
                                    <select name="items" id="items" class="form-control select2">
                                        <option value="all" <?php if($itm_code == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php
                                        if($item_cats == "all"){
                                            foreach($item_code as $fcode){
                                                if($item_name[$fcode] != ""){
                                        ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($itm_code == $fcode){ echo "selected"; } ?>><?php echo $item_name[$fcode]; ?></option>
                                        <?php
                                                }
                                            }
                                        }
                                        else{
                                            foreach($item_code as $fcode){
                                                if($item_name[$fcode] != "" && $item_category[$fcode] == $item_cats){
                                        ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($itm_code == $fcode){ echo "selected"; } ?>><?php echo $item_name[$fcode]; ?></option>
                                        <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Status</label>
                                    <select name="status" id="status" class="form-control select2" style="width:160px;">
                                        <option value="display" <?php if($status == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="rerate" <?php if($status == "rerate"){ echo "selected"; } ?>>-Rerun-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Feed Sort</label>
                                    <select name="feed_sort" id="feed_sort" class="form-control select2" style="width:160px;">
                                        <option value="asc" <?php if($feed_sort == "asc"){ echo "selected"; } ?>>-Asending-</option>
                                        <option value="desc" <?php if($feed_sort == "desc"){ echo "selected"; } ?>>-Descending-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>MedVac Sort</label>
                                    <select name="medvac_sort" id="medvac_sort" class="form-control select2" style="width:160px;">
                                        <option value="asc" <?php if($medvac_sort == "asc"){ echo "selected"; } ?>>-Asending-</option>
                                        <option value="desc" <?php if($medvac_sort == "desc"){ echo "selected"; } ?>>-Descending-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Rerun</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                    /*Item Filter List*/
                    $item_list = ""; $coa_list = "";
                    if($itm_code != "all"){
                        $item_list = $itm_code;
                        if($coa_list == ""){ $coa_list = $icat_iac[$item_category[$itm_code]]."','".$icat_cogsac[$item_category[$itm_code]]."','".$icat_wpac[$item_category[$itm_code]]; }
                        else{ $coa_list = $coa_list."','".$icat_iac[$item_category[$itm_code]]."','".$icat_cogsac[$item_category[$itm_code]]."','".$icat_wpac[$item_category[$itm_code]]; }
                    }
                    else if($item_cats != "all"){
                        foreach($item_code as $icode){
                            if($item_cats == $item_category[$icode]){
                                if($item_list == ""){ $item_list = $icode; } else{ $item_list = $item_list."','".$icode; }
                                if($coa_list == ""){ $coa_list = $icat_iac[$item_category[$icode]]."','".$icat_cogsac[$item_category[$icode]]."','".$icat_wpac[$item_category[$icode]]; }
                                else{ $coa_list = $coa_list."','".$icat_iac[$item_category[$icode]]."','".$icat_cogsac[$item_category[$icode]]."','".$icat_wpac[$item_category[$icode]]; }
                            }
                        }
                    }
                    else{
                        foreach($item_code as $icode){
                            if($item_list == ""){ $item_list = $icode; } else{ $item_list = $item_list."','".$icode; }
                            if($coa_list == ""){ $coa_list = $icat_iac[$item_category[$icode]]."','".$icat_cogsac[$item_category[$icode]]."','".$icat_wpac[$item_category[$icode]]; }
                            else{ $coa_list = $coa_list."','".$icat_iac[$item_category[$icode]]."','".$icat_cogsac[$item_category[$icode]]."','".$icat_wpac[$item_category[$icode]]; }
                        }
                    }
                    $item_code_filter = " AND `item_code` IN ('$item_list')";
                    $coa_code_filter = " AND `coa_code` IN ('$coa_list')";
                    
                    if(!empty($sector_code[$farms])){
                        echo "<tr style='text-align:center;'>";
                        echo "<th colspan='9'>".$sector_name[$farms]."</th>";
                        echo "</tr>";

                        echo "<tr style='text-align:center;'>";
                        echo "<th colspan='9'>Transaction Details</th>";
                        echo "</tr>";
                        echo "<tr style='text-align:center;'>";
                        echo "<th>Date</th>";
                        echo "<th>Trnums</th>";
                        echo "<th>Type</th>";
                        echo "<th>Quantity</th>";
                        echo "<th>Rate</th>";
                        echo "<th>Amount</th>";
                        echo "<th>Stock Qty</th>";
                        echo "<th>Stock Rate</th>";
                        echo "<th>Stock Amount</th>";
                        echo "</tr>";
                        
                        $sl_no = $total_feedin_cost = $total_medvacin_cost = $total_feedconsumed_cost = $total_medvacconsumed_cost = $total_medvacout_cost = $total_feedout_cost = 0;
                        $total_feedin_qty = $total_medvacin_qty = $total_feedconsumed_qty = $total_medvacconsumed_qty = $total_medvacout_qty = $total_feedout_qty = 0;
                        $key_index = $start_date = $end_date = $date_key = $item_key = $tnum_key = $stk_key = "";
                        $acc_out_list = $acc_in_list = $items = $trnums = $stock_in_details = $stock_consumed_details = array();
                        
                        $sql = "SELECT * FROM `account_summary` WHERE `item_code` NOT IN ('$chick_code','$bird_code') AND `location` LIKE '$farms'".$item_code_filter."".$coa_code_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                        $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $key_index = $row['date']."@".$row['item_code']."@".$row['coa_code']."@".$row['trnum']."@".round($row['quantity'],5);
                            if($row['crdr'] == "CR"){
                                $acc_out_list[$key_index] = $row['id']."@".$row['trnum']."@".$row['date']."@".$row['item_code']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['location']."@".$row['batch']."@".$row['etype']."@cracc";
                            }
                            else{
                                $acc_in_list[$key_index] = $row['id']."@".$row['trnum']."@".$row['date']."@".$row['item_code']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['location']."@".$row['batch']."@".$row['etype']."@dracc";
                            }
                            
                            $items[$row['item_code']] = $row['item_code']; $trnums[$row['trnum']."@".round($row['quantity'],5)] = $row['trnum']."@".round($row['quantity'],5);
                            if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                            if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                        }
                        asort($items);// arsort($trnums);
                        foreach($items as $item_key){
                            echo "<tr>";
                            echo "<th colspan='9' style='text-align:center;color:red;font-weight:bold;'>".$item_name[$item_key]."</th>";
                            echo "</tr>";
                            //$stock_in_acc_size = sizeof($acc_in_list)."-".$stock_out_acc_size1 = sizeof($acc_out_list)."-".$stock_out_acc_size2 = sizeof($acc_out_list2);
                            $stk_key = $icat_iac[$item_category[$item_key]];
                            $cog_key = $icat_cogsac[$item_category[$item_key]];
                            $wps_key = $icat_wpac[$item_category[$item_key]];
                            
                            $avg_qty = $avg_price = $avg_amount = 0; $acc_sql = $currentDate = ""; 
                            for($currentDate = ((int)$start_date); $currentDate <= ((int)$end_date); $currentDate += (86400)){
                                $date_key = date("Y-m-d",((int)$currentDate));
                                foreach($trnums as $tnum_key){
                                    $key_index = $date_key."@".$item_key."@".$stk_key."@".$tnum_key;
                                    
                                    if(!empty($acc_in_list[$key_index])){
                                        $stock_in_details = explode("@",$acc_in_list[$key_index]);
                                        $sl_no++;

                                        if(!empty($feed_cat[$item_category[$stock_in_details[3]]])){
                                            $total_feedin_cost = $total_feedin_cost + $stock_in_details[6];
                                            $total_feedin_qty = $total_feedin_qty + $stock_in_details[4];
                                        }
                                        if(!empty($medvac_cat[$item_category[$stock_in_details[3]]])){
                                            $total_medvacin_cost = $total_medvacin_cost + $stock_in_details[6];
                                            $total_medvacin_qty = $total_medvacin_qty + $stock_in_details[4];
                                        }

                                        echo "<tr>";
                                            echo "<td>".date("d.m.Y",strtotime($date_key))."</td>";
                                            echo "<td>".$stock_in_details[1]."</td>";
                                            echo "<td>Stock In</td>";
                                            if(number_format_ind($stock_in_details[4]) != "0.00" && number_format_ind($stock_in_details[6]) == "0.00"){
                                                echo "<td style='color:red;'>".number_format_ind($stock_in_details[4])."</td>";

                                                if(!empty($stock_in_details[6]) && $stock_in_details[6] > 0 && !empty($stock_in_details[4]) && $stock_in_details[4] > 0){
                                                    echo "<td style='color:red;'>".number_format_ind($stock_in_details[6] / $stock_in_details[4])."</td>";
                                                }
                                                else{
                                                    echo "<td style='color:red;'>".number_format_ind(0)."</td>";
                                                }
                                                echo "<td style='color:red;'>".number_format_ind($stock_in_details[6])."</td>";
                                            }
                                            else{
                                                echo "<td>".number_format_ind($stock_in_details[4])."</td>";
                                                if(!empty($stock_in_details[6]) && $stock_in_details[6] > 0 && !empty($stock_in_details[4]) && $stock_in_details[4] > 0){
                                                    echo "<td>".number_format_ind($stock_in_details[6] / $stock_in_details[4])."</td>";
                                                }
                                                else{
                                                    echo "<td style='color:red;'>".number_format_ind(0)."</td>";
                                                }
                                                
                                                echo "<td>".number_format_ind($stock_in_details[6])."</td>";
                                            }

                                            $avg_qty = $avg_qty + $stock_in_details[4];
                                            $avg_amount = $avg_amount + $stock_in_details[6];
                                            if($avg_amount > 0 && $avg_qty > 0){
                                                $avg_price = $avg_amount / $avg_qty;
                                            }
                                            else{
                                                $avg_price = 0;
                                            }
                                            
                                            
                                            echo "<td>".number_format_ind($avg_qty)."</td>";
                                            echo "<td>".number_format_ind($avg_price)."</td>";
                                            echo "<td>".number_format_ind($avg_amount)."</td>";
                                        echo "</tr>";
                                    }
                                    if(!empty($acc_out_list[$key_index])){
                                        $stock_consumed_details = explode("@",$acc_out_list[$key_index]);
                                        $sl_no++;
                                        if(number_format_ind($avg_price) == "0.00"){ $avg_price = 0; }
                                        $cur_amt = $stock_consumed_details[4] * $avg_price; if(number_format_ind($cur_amt) == "0.00"){ $cur_amt = 0; }
                                        //echo "<br/>$stock_consumed_details[1]@$cur_amt = $stock_consumed_details[4] * $avg_price;";


                                        echo "<tr>";
                                            echo "<td>".date("d.m.Y",strtotime($date_key))."</td>";
                                            echo "<td>".$stock_consumed_details[1]."</td>";
                                            if($stock_consumed_details[9] == "DayEntryFeed" || $stock_consumed_details[9] == "DayEntryFeed2"){
                                                echo "<td>Feed Consumed</td>";
                                                if(!empty($feed_cat[$item_category[$stock_consumed_details[3]]])){
                                                    $total_feedconsumed_cost = $total_feedconsumed_cost + $cur_amt;
                                                    $total_feedconsumed_qty = $total_feedconsumed_qty + $stock_consumed_details[4];
                                                    $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                    if($status == "rerate"){ mysqli_query($conn,$squery); } else{ }
                                                }
                                            }
                                            else if($stock_consumed_details[9] == "MedVacEntry"){
                                                echo "<td>MedVac Consumed</td>";
                                                if(!empty($medvac_cat[$item_category[$stock_consumed_details[3]]])){
                                                    $total_medvacconsumed_cost = $total_medvacconsumed_cost + $cur_amt;
                                                    $total_medvacconsumed_qty = $total_medvacconsumed_qty + $stock_consumed_details[4];
                                                    $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                    if($status == "rerate"){ mysqli_query($conn,$squery); } else{ }
                                                }
                                            }
                                            else if($stock_consumed_details[9] == "Sales"){
                                                echo "<td>Sold</td>";
                                                if(!empty($feed_cat[$item_category[$stock_consumed_details[3]]])){
                                                    $total_feedout_cost = $total_feedout_cost + $cur_amt;
                                                    $total_feedout_qty = $total_feedout_qty + $stock_consumed_details[4];
                                                    $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                    if($status == "rerate"){ mysqli_query($conn,$squery); } else{ }
                                                }
                                                if(!empty($medvac_cat[$item_category[$stock_consumed_details[3]]])){
                                                    $total_medvacout_cost = $total_medvacout_cost + $cur_amt;
                                                    $total_medvacout_qty = $total_medvacout_qty + $stock_consumed_details[4];
                                                    $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                    if($status == "rerate"){ mysqli_query($conn,$squery); } else{ }
                                                }
                                            }
                                            else{
                                                echo "<td>Stock Out</td>";
                                                if(!empty($feed_cat[$item_category[$stock_consumed_details[3]]])){
                                                    $total_feedout_cost = $total_feedout_cost + $cur_amt;
                                                    $total_feedout_qty = $total_feedout_qty + $stock_consumed_details[4];
                                                    //$stock_consumed_details[4];
                                                }
                                                if(!empty($medvac_cat[$item_category[$stock_consumed_details[3]]])){
                                                    $total_medvacout_cost = $total_medvacout_cost + $cur_amt;
                                                    $total_medvacout_qty = $total_medvacout_qty + $stock_consumed_details[4];
                                                }
                                               $squery = "UPDATE `item_stocktransfers` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `active` = '1' AND `dflag` = '0';";
                                               if($status == "rerate"){ mysqli_query($conn,$squery); } else{ }
                                               $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `active` = '1' AND `dflag` = '0';";
                                               if($status == "rerate"){ mysqli_query($conn,$squery); } else{ }
                                            }
                                            

                                            echo "<td>".number_format_ind($stock_consumed_details[4])."</td>";
                                            if(!empty($stock_consumed_details[4]) && $stock_consumed_details[4] > 0 && $cur_amt > 0){
                                                echo "<td>".number_format_ind($cur_amt / $stock_consumed_details[4])."</td>";
                                            }
                                            else{
                                                echo "<td>".number_format_ind(0)."</td>";
                                            }
                                            
                                            echo "<td>".number_format_ind($cur_amt)."</td>";

                                            $avg_qty = $avg_qty - $stock_consumed_details[4];
                                            $avg_amount = $avg_amount - $cur_amt;
                                            if($avg_amount > 0 && $avg_qty > 0){
                                                $avg_price = $avg_amount / $avg_qty;
                                            }
                                            else{
                                                $avg_price = 0;
                                            }
                                            
                                            echo "<td>".number_format_ind($avg_qty)."</td>";
                                            echo "<td>".number_format_ind($avg_price)."</td>";
                                            echo "<td>".number_format_ind($avg_amount)."</td>";
                                        echo "</tr>";
                                    }
                                }
                            }
                            //if($status == "rerate"){ mysqli_query($conn,$acc_sql); } else{ }
                        }
                    }
                    else{
                        if($batches == "all"){
                            $batch_list = array();
                            $fsql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$farms' AND `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC"; $fquery = mysqli_query($conn,$fsql);
                            while($frow = mysqli_fetch_assoc($fquery)){ $batch_list[$frow['code']] = $frow['code']; }
                        }
                        else{
                            $batch_list = array(); $batch_list[] = $batches;
                        }
                        foreach($batch_list as $bhcode){
                            echo "<tr style='text-align:center;'>";
                            echo "<th colspan='9'>".$farm_name[$batch_farm[$bhcode]]." (".$batch_name[$bhcode].")</th>";
                            echo "</tr>";

                            echo "<tr style='text-align:center;'>";
                            echo "<th colspan='9'>Transaction Details</th>";
                            echo "</tr>";
                            echo "<tr style='text-align:center;'>";
                            echo "<th>Date</th>";
                            echo "<th>Trnums</th>";
                            echo "<th>Type</th>";
                            echo "<th>Quantity</th>";
                            echo "<th>Rate</th>";
                            echo "<th>Amount</th>";
                            echo "<th>Stock Qty</th>";
                            echo "<th>Stock Rate</th>";
                            echo "<th>Stock Amount</th>";
                            echo "</tr>";
                            $sl_no = $total_feedin_cost = $total_medvacin_cost = $total_feedconsumed_cost = $total_medvacconsumed_cost = $total_medvacout_cost = $total_feedout_cost = 0;
                            $total_feedin_qty = $total_medvacin_qty = $total_feedconsumed_qty = $total_medvacconsumed_qty = $total_medvacout_qty = $total_feedout_qty = 0;
                            $key_index = $start_date = $end_date = $date_key = $item_key = $tnum_key = $stk_key = "";
                            $acc_out_list = $acc_in_list = $items = $trnums = $mv_trnos = $stock_in_details = $stock_consumed_details = array();
                            
                            $sql = "SELECT * FROM `account_summary` WHERE `item_code` NOT IN ('$chick_code','$bird_code') AND `location` LIKE '$farms' AND `batch` LIKE '$bhcode'".$item_code_filter."".$coa_code_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC,`crdr` DESC";
                            $query = mysqli_query($conn,$sql);
                            while($row = mysqli_fetch_assoc($query)){
                                if($row['crdr'] == "DR"){ $sodrs = "s1"; }
                                else if($row['crdr'] == "CR" && $row['etype'] == "DayEntryFeed" || $row['crdr'] == "CR" && $row['etype'] == "DayEntryFeed2"){ $sodrs = "s2"; }
                                else if($row['crdr'] == "CR" && $row['etype'] == "MedVacEntry"){ $sodrs = "s2"; }
                                else if($row['crdr'] == "CR" && $row['etype'] == "Sales"){ $sodrs = "s2"; }
                                else if($row['crdr'] == "CR" && $row['etype'] == "Feed Consumed"){ $sodrs = "s2"; }
                                else if($row['crdr'] == "CR"){ $sodrs = "s3"; }
                                else{ $sodrs = "s"; }

                                $key_index = $row['date']."@".$row['item_code']."@".$row['coa_code']."@".$row['trnum'];
                                if($row['crdr'] == "CR"){
                                    $acc_out_list[$key_index] = $row['id']."@".$row['trnum']."@".$row['date']."@".$row['item_code']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['location']."@".$row['batch']."@".$row['etype']."@cracc";
                                }
                                else{
                                    $acc_in_list[$key_index] = $row['id']."@".$row['trnum']."@".$row['date']."@".$row['item_code']."@".$row['quantity']."@".$row['price']."@".$row['amount']."@".$row['location']."@".$row['batch']."@".$row['etype']."@dracc";
                                }
                                
                                $items[$row['item_code']] = $row['item_code']; $trnums[$row['trnum']] = $sodrs."@".$row['trnum']; $mv_trnos[$row['trnum']] = $row['trnum'];
                                if($start_date == ""){ $start_date = strtotime($row['date']); }else{ if(strtotime($row['date']) <= $start_date){ $start_date = strtotime($row['date']); } }
                                if($end_date == ""){ $end_date = strtotime($row['date']); }else{ if(strtotime($row['date']) >= $end_date){ $end_date = strtotime($row['date']); } }
                            }
                            asort($items);
                            if($feed_sort == "asc"){ asort($trnums); } else if($feed_sort == "desc"){ arsort($trnums); } else{ asort($trnums); }
                            if($medvac_sort == "asc"){ asort($mv_trnos); } else if($medvac_sort == "desc"){ arsort($mv_trnos); } else{ asort($mv_trnos); }
                            
                            foreach($items as $item_key){
                                echo "<tr>";
                                echo "<th colspan='9' style='text-align:center;color:red;font-weight:bold;'>".$item_name[$item_key]."</th>";
                                echo "</tr>";
                                //$stock_in_acc_size = sizeof($acc_in_list)."-".$stock_out_acc_size1 = sizeof($acc_out_list)."-".$stock_out_acc_size2 = sizeof($acc_out_list2);
                                $stk_key = $icat_iac[$item_category[$item_key]];
                                $cog_key = $icat_cogsac[$item_category[$item_key]];
                                $wps_key = $icat_wpac[$item_category[$item_key]];
                                $avg_qty = $avg_price = $avg_amount = 0; $acc_sql = ""; 
                                $sin_qty = $sin_amt = $sout_qty = $sout_amt = $scon_qty = $scon_amt = 0;
                                for($currentDate = ((int)$start_date); $currentDate <= ((int)$end_date); $currentDate += (86400)){
                                    $date_key = date("Y-m-d",((int)$currentDate));
                                    foreach($trnums as $trk1){
                                        $trk2 = explode("@",$trk1); $tnum_key = $trk2[1];
                                        $key_index = $date_key."@".$item_key."@".$stk_key."@".$tnum_key;
                                        
                                        if(!empty($acc_in_list[$key_index])){
                                            $stock_in_details = explode("@",$acc_in_list[$key_index]);
                                            $sl_no++;

                                            if(!empty($feed_cat[$item_category[$stock_in_details[3]]])){
                                                $total_feedin_cost = $total_feedin_cost + $stock_in_details[6];
                                                $total_feedin_qty = $total_feedin_qty + $stock_in_details[4];

                                                $sin_qty = $sin_qty + $stock_in_details[4];
                                                $sin_amt = $sin_amt + $stock_in_details[6];

                                                echo "<tr>";
                                                    echo "<td>".date("d.m.Y",strtotime($date_key))."</td>";
                                                    echo "<td>".$stock_in_details[1]."</td>";
                                                    echo "<td>Stock In</td>";
                                                    if(number_format_ind($stock_in_details[4]) != "0.00" && number_format_ind($stock_in_details[6]) == "0.00"){
                                                        echo "<td style='color:red;'>".number_format_ind($stock_in_details[4])."</td>";
                                                        
                                                        if(!empty($stock_in_details[6]) && $stock_in_details[6] > 0 && !empty($stock_in_details[4]) && $stock_in_details[4] > 0){
                                                            echo "<td style='color:red;'>".number_format_ind($stock_in_details[6] / $stock_in_details[4])."</td>";
                                                        }
                                                        else{
                                                            echo "<td style='color:red;'>".number_format_ind(0)."</td>";
                                                        }
                                                        
                                                        echo "<td style='color:red;'>".number_format_ind($stock_in_details[6])."</td>";
                                                    }
                                                    else{
                                                        echo "<td>".number_format_ind($stock_in_details[4])."</td>";
                                                        if(!empty($stock_in_details[6]) && $stock_in_details[6] > 0 && !empty($stock_in_details[4]) && $stock_in_details[4] > 0){
                                                            echo "<td>".number_format_ind($stock_in_details[6] / $stock_in_details[4])."</td>";
                                                        }
                                                        else{
                                                            echo "<td>".number_format_ind(0)."</td>";
                                                        }
                                                        
                                                        echo "<td>".number_format_ind($stock_in_details[6])."</td>";
                                                    }
                                                    
                                                    $avg_qty = $avg_qty + $stock_in_details[4];
                                                    $avg_amount = $avg_amount + $stock_in_details[6];
                                                    
                                                    if($avg_amount > 0 && $avg_qty > 0){
                                                        $avg_price = $avg_amount / $avg_qty;
                                                    }
                                                    else{
                                                        $avg_price = 0;
                                                    }

                                                    if($avg_qty > 0){
                                                        echo "<td>".number_format_ind($avg_qty)."</td>";
                                                        echo "<td>".number_format_ind($avg_price)."</td>";
                                                        echo "<td>".number_format_ind($avg_amount)."</td>";
                                                    }
                                                    else{
                                                        echo "<td style='color:red;'>".number_format_ind($avg_qty)."</td>";
                                                        echo "<td style='color:red;'>".number_format_ind($avg_price)."</td>";
                                                        echo "<td style='color:red;'>".number_format_ind($avg_amount)."</td>";
                                                    }
                                                    
                                                echo "</tr>";
                                            }
                                        }
                                        if(!empty($acc_out_list[$key_index])){
                                            $stock_consumed_details = explode("@",$acc_out_list[$key_index]);
                                            $sl_no++;
                                            if(!empty($feed_cat[$item_category[$stock_consumed_details[3]]])){
                                                if(number_format_ind($avg_price) == "0.00"){ $avg_price = 0; }
                                                $cur_amt = $stock_consumed_details[4] * $avg_price; if(number_format_ind($cur_amt) == "0.00"){ $cur_amt = 0; }

                                                echo "<tr>";
                                                echo "<td>".date("d.m.Y",strtotime($date_key))."</td>";
                                                echo "<td>".$stock_consumed_details[1]."</td>";
                                                if($stock_consumed_details[9] == "DayEntryFeed" || $stock_consumed_details[9] == "DayEntryFeed2"){
                                                    echo "<td>Feed Consumed</td>";
                                                    if(!empty($feed_cat[$item_category[$stock_consumed_details[3]]])){
                                                        $total_feedconsumed_cost = $total_feedconsumed_cost + $cur_amt;
                                                        $total_feedconsumed_qty = $total_feedconsumed_qty + $stock_consumed_details[4];
                                                        $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                        if($status == "rerate"){ mysqli_query($conn,$squery); }

                                                        $scon_qty = $scon_qty + $stock_consumed_details[4];
                                                        $scon_amt = $scon_amt + $cur_amt;
                                                    }
                                                }
                                                else if($stock_consumed_details[9] == "MedVacEntry"){
                                                    echo "<td>MedVac Consumed</td>";
                                                    if(!empty($medvac_cat[$item_category[$stock_consumed_details[3]]])){
                                                        $total_medvacconsumed_cost = $total_medvacconsumed_cost + $cur_amt;
                                                        $total_medvacconsumed_qty = $total_medvacconsumed_qty + $stock_consumed_details[4];
                                                        $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                        if($status == "rerate"){ mysqli_query($conn,$squery); }
                                                    }
                                                }
                                                else if($stock_consumed_details[9] == "Sales"){
                                                    echo "<td>Sold</td>";
                                                    if(!empty($feed_cat[$item_category[$stock_consumed_details[3]]])){
                                                        $total_feedout_cost = $total_feedout_cost + $cur_amt;
                                                        $total_feedout_qty = $total_feedout_qty + $stock_consumed_details[4];
                                                        $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                        if($status == "rerate"){ mysqli_query($conn,$squery); }

                                                        $sout_qty = $sout_qty + $stock_consumed_details[4];
                                                        $sout_amt = $sout_amt + $cur_amt;
                                                    }
                                                    if(!empty($medvac_cat[$item_category[$stock_consumed_details[3]]])){
                                                        $total_medvacout_cost = $total_medvacout_cost + $cur_amt;
                                                        $total_medvacout_qty = $total_medvacout_qty + $stock_consumed_details[4];
                                                        $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                        if($status == "rerate"){ mysqli_query($conn,$squery); }
                                                    }
                                                }
                                                else{
                                                    echo "<td>Stock Out</td>";
                                                    if(!empty($feed_cat[$item_category[$stock_consumed_details[3]]])){
                                                        $total_feedout_cost = $total_feedout_cost + $cur_amt;
                                                        $total_feedout_qty = $total_feedout_qty + $stock_consumed_details[4];
                                                        //$stock_consumed_details[4];

                                                        $sout_qty = $sout_qty + $stock_consumed_details[4];
                                                        $sout_amt = $sout_amt + $cur_amt;
                                                    }
                                                    if(!empty($item_category[$medvac_cat[$stock_consumed_details[3]]])){
                                                        $total_medvacout_cost = $total_medvacout_cost + $cur_amt;
                                                        $total_medvacout_qty = $total_medvacout_qty + $stock_consumed_details[4];
                                                    }
                                                    //$acc_sql .= "UPDATE `item_stocktransfers` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `code` = '$stock_consumed_details[3]' AND `active` = '1' AND `dflag` = '0';";
                                                    //$acc_sql .= "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";

                                                    $squery = "UPDATE `item_stocktransfers` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `active` = '1' AND `dflag` = '0';";
                                                    if($status == "rerate"){ mysqli_query($conn,$squery); }
                                                    $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `active` = '1' AND `dflag` = '0';";
                                                    if($status == "rerate"){ mysqli_query($conn,$squery); }
                                                }

                                                echo "<td>".number_format_ind($stock_consumed_details[4])."</td>";
                                                if(!empty($stock_consumed_details[4]) && $stock_consumed_details[4] > 0 && $cur_amt > 0){
                                                    echo "<td>".number_format_ind($cur_amt / $stock_consumed_details[4])."</td>";
                                                }
                                                else{
                                                    echo "<td>".number_format_ind(0)."</td>";
                                                }
                                                
                                                echo "<td>".number_format_ind($cur_amt)."</td>";

                                                $avg_qty = $avg_qty - $stock_consumed_details[4];
                                                $avg_amount = $avg_amount - $cur_amt;
                                                if($avg_amount > 0 && $avg_qty > 0){
                                                    $avg_price = $avg_amount / $avg_qty;
                                                }
                                                else{
                                                    $avg_price = 0;
                                                }
                                                
                                                
                                                if($avg_qty >= 0){
                                                    echo "<td>".number_format_ind($avg_qty)."</td>";
                                                    echo "<td>".number_format_ind($avg_price)."</td>";
                                                    echo "<td>".number_format_ind($avg_amount)."</td>";
                                                }
                                                else{
                                                    echo "<td style='color:red;'>".number_format_ind($avg_qty)."</td>";
                                                    echo "<td style='color:red;'>".number_format_ind($avg_price)."</td>";
                                                    echo "<td style='color:red;'>".number_format_ind($avg_amount)."</td>";
                                                }
                                            echo "</tr>";
                                            }
                                        }
                                    }
                                    foreach($mv_trnos as $tnum_key){
                                        $key_index = $date_key."@".$item_key."@".$stk_key."@".$tnum_key;
                                        
                                        if(!empty($acc_in_list[$key_index])){
                                            $stock_in_details = explode("@",$acc_in_list[$key_index]);
                                            $sl_no++;
                                            
                                            if(!empty($medvac_cat[$item_category[$stock_in_details[3]]])){
                                                $total_medvacin_cost = $total_medvacin_cost + $stock_in_details[6];
                                                $total_medvacin_qty = $total_medvacin_qty + $stock_in_details[4];

                                                echo "<tr>";
                                                    echo "<td>".date("d.m.Y",strtotime($date_key))."</td>";
                                                    echo "<td>".$stock_in_details[1]."</td>";
                                                    echo "<td>Stock In</td>";
                                                    if(number_format_ind($stock_in_details[4]) != "0.00" && number_format_ind($stock_in_details[6]) == "0.00"){
                                                        echo "<td style='color:red;'>".number_format_ind($stock_in_details[4])."</td>";
                                                        if(!empty($stock_in_details[6]) && $stock_in_details[6] > 0 && !empty($stock_in_details[4]) && $stock_in_details[4] > 0){
                                                            echo "<td style='color:red;'>".number_format_ind($stock_in_details[6] / $stock_in_details[4])."</td>";
                                                        }
                                                        else{
                                                            echo "<td style='color:red;'>".number_format_ind(0)."</td>";
                                                        }
                                                        
                                                        echo "<td style='color:red;'>".number_format_ind($stock_in_details[6])."</td>";
                                                    }
                                                    else{
                                                        echo "<td>".number_format_ind($stock_in_details[4])."</td>";
                                                        if(!empty($stock_in_details[6]) && $stock_in_details[6] > 0 && !empty($stock_in_details[4]) && $stock_in_details[4] > 0){
                                                            echo "<td>".number_format_ind($stock_in_details[6] / $stock_in_details[4])."</td>";
                                                        }
                                                        else{
                                                            echo "<td>".number_format_ind(0)."</td>";
                                                        }
                                                        
                                                        echo "<td>".number_format_ind($stock_in_details[6])."</td>";
                                                    }
                                                    
                                                    $avg_qty = $avg_qty + $stock_in_details[4];
                                                    $avg_amount = $avg_amount + $stock_in_details[6];
                                                    if($avg_amount > 0 && $avg_qty > 0){
                                                        $avg_price = $avg_amount / $avg_qty;
                                                    }
                                                    else{
                                                        $avg_price = 0;
                                                    }
    
                                                    if($avg_qty > 0){
                                                        echo "<td>".number_format_ind($avg_qty)."</td>";
                                                        echo "<td>".number_format_ind($avg_price)."</td>";
                                                        echo "<td>".number_format_ind($avg_amount)."</td>";
                                                    }
                                                    else{
                                                        echo "<td style='color:red;'>".number_format_ind($avg_qty)."</td>";
                                                        echo "<td style='color:red;'>".number_format_ind($avg_price)."</td>";
                                                        echo "<td style='color:red;'>".number_format_ind($avg_amount)."</td>";
                                                    }
                                                    
                                                echo "</tr>";
                                            }
                                        }
                                        if(!empty($acc_out_list[$key_index])){
                                            $stock_consumed_details = explode("@",$acc_out_list[$key_index]);
                                            $sl_no++;
                                            if(!empty($medvac_cat[$item_category[$stock_consumed_details[3]]])){
                                                if(number_format_ind($avg_price) == "0.00"){ $avg_price = 0; }
                                                $cur_amt = $stock_consumed_details[4] * $avg_price; if(number_format_ind($cur_amt) == "0.00"){ $cur_amt = 0; }
    
                                                echo "<tr>";
                                                    echo "<td>".date("d.m.Y",strtotime($date_key))."</td>";
                                                    echo "<td>".$stock_consumed_details[1]."</td>";
                                                    if($stock_consumed_details[9] == "DayEntryFeed" || $stock_consumed_details[9] == "DayEntryFeed2"){
                                                        echo "<td>Feed Consumed</td>";
                                                        if(!empty($feed_cat[$item_category[$stock_consumed_details[3]]])){
                                                            $total_feedconsumed_cost = $total_feedconsumed_cost + $cur_amt;
                                                            $total_feedconsumed_qty = $total_feedconsumed_qty + $stock_consumed_details[4];
                                                            $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                            if($status == "rerate"){ mysqli_query($conn,$squery); }
    
                                                            $scon_qty = $scon_qty + $stock_consumed_details[4];
                                                            $scon_amt = $scon_amt + $cur_amt;
                                                        }
                                                    }
                                                    else if($stock_consumed_details[9] == "MedVacEntry"){
                                                        echo "<td>MedVac Consumed</td>";
                                                        if(!empty($medvac_cat[$item_category[$stock_consumed_details[3]]])){
                                                            $total_medvacconsumed_cost = $total_medvacconsumed_cost + $cur_amt;
                                                            $total_medvacconsumed_qty = $total_medvacconsumed_qty + $stock_consumed_details[4];
                                                            $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                            if($status == "rerate"){ mysqli_query($conn,$squery); }
                                                        }
                                                    }
                                                    else if($stock_consumed_details[9] == "Sales"){
                                                        echo "<td>Sold</td>";
                                                        if(!empty($feed_cat[$item_category[$stock_consumed_details[3]]])){
                                                            $total_feedout_cost = $total_feedout_cost + $cur_amt;
                                                            $total_feedout_qty = $total_feedout_qty + $stock_consumed_details[4];
                                                            $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                            if($status == "rerate"){ mysqli_query($conn,$squery); }
    
                                                            $sout_qty = $sout_qty + $stock_consumed_details[4];
                                                            $sout_amt = $sout_amt + $cur_amt;
                                                        }
                                                        if(!empty($medvac_cat[$item_category[$stock_consumed_details[3]]])){
                                                            $total_medvacout_cost = $total_medvacout_cost + $cur_amt;
                                                            $total_medvacout_qty = $total_medvacout_qty + $stock_consumed_details[4];
                                                            $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
                                                            if($status == "rerate"){ mysqli_query($conn,$squery); }
                                                        }
                                                    }
                                                    else{
                                                        echo "<td>Stock Out</td>";
                                                        if(!empty($feed_cat[$item_category[$stock_consumed_details[3]]])){
                                                            $total_feedout_cost = $total_feedout_cost + $cur_amt;
                                                            $total_feedout_qty = $total_feedout_qty + $stock_consumed_details[4];
                                                            //$stock_consumed_details[4];
    
                                                            $sout_qty = $sout_qty + $stock_consumed_details[4];
                                                            $sout_amt = $sout_amt + $cur_amt;
                                                        }
                                                        if(!empty($item_category[$medvac_cat[$stock_consumed_details[3]]])){
                                                            $total_medvacout_cost = $total_medvacout_cost + $cur_amt;
                                                            $total_medvacout_qty = $total_medvacout_qty + $stock_consumed_details[4];
                                                        }
                                                        //$acc_sql .= "UPDATE `item_stocktransfers` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `code` = '$stock_consumed_details[3]' AND `active` = '1' AND `dflag` = '0';";
                                                        //$acc_sql .= "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `coa_code` IN ('$stk_key','$cog_key','$wps_key') AND `active` = '1' AND `dflag` = '0';";
    
                                                        $squery = "UPDATE `item_stocktransfers` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `active` = '1' AND `dflag` = '0';";
                                                        if($status == "rerate"){ mysqli_query($conn,$squery); }
                                                        $squery = "UPDATE `account_summary` SET `price`= '$avg_price',`amount` = '$cur_amt' WHERE `date` = '$date_key' AND `trnum` = '$stock_consumed_details[1]' AND `item_code` = '$stock_consumed_details[3]' AND `quantity` = '$stock_consumed_details[4]' AND `active` = '1' AND `dflag` = '0';";
                                                        if($status == "rerate"){ mysqli_query($conn,$squery); }
                                                    }
    
                                                    echo "<td>".number_format_ind($stock_consumed_details[4])."</td>";
                                                    if(!empty($stock_consumed_details[4]) && $stock_consumed_details[4] > 0 && $cur_amt > 0){
                                                        echo "<td>".number_format_ind($cur_amt / $stock_consumed_details[4])."</td>";
                                                    }
                                                    else{
                                                        echo "<td>".number_format_ind(0)."</td>";
                                                    }
                                                    
                                                    echo "<td>".number_format_ind($cur_amt)."</td>";
    
                                                    $avg_qty = $avg_qty - $stock_consumed_details[4];
                                                    $avg_amount = $avg_amount - $cur_amt;
                                                    if($avg_amount > 0 && $avg_qty > 0){
                                                        $avg_price = $avg_amount / $avg_qty;
                                                    }
                                                    else{
                                                        $avg_price = 0;
                                                    }
                                                    
                                                    
                                                    
                                                    if($avg_qty >= 0){
                                                        echo "<td>".number_format_ind($avg_qty)."</td>";
                                                        echo "<td>".number_format_ind($avg_price)."</td>";
                                                        echo "<td>".number_format_ind($avg_amount)."</td>";
                                                    }
                                                    else{
                                                        echo "<td style='color:red;'>".number_format_ind($avg_qty)."</td>";
                                                        echo "<td style='color:red;'>".number_format_ind($avg_price)."</td>";
                                                        echo "<td style='color:red;'>".number_format_ind($avg_amount)."</td>";
                                                    }
                                                    //echo "<td>".$squery."</td>";
                                                    //if($status == "rerate"){ if(!mysqli_query($conn,$squery)) { mysqli_error($conn)."-----".$squery; } }
                                                    
                                                echo "</tr>";
                                            }
                                        }
                                    }
                                }
                                if($sin_qty > 0){
                                echo "<tr style='text-align:center;color:green;'>";
                                echo "<th style='color:green;' colspan='3'>Feed In</th>";
                                echo "<th style='color:green;' colspan='3'>Feed Out</th>";
                                echo "<th style='color:green;' colspan='3'>Feed Consumed</th>";
                                //echo "<th colspan='3'>Feed Balance</th>";
                                echo "</tr>";
                                echo "<tr style='text-align:center;color:green;'>";
                                echo "<th style='color:green;'>Qty</th>";
                                echo "<th style='color:green;'>Price</th>";
                                echo "<th style='color:green;'>Amount</th>";
                                echo "<th style='color:green;'>Qty</th>";
                                echo "<th style='color:green;'>Price</th>";
                                echo "<th style='color:green;'>Amount</th>";
                                echo "<th style='color:green;'>Qty</th>";
                                echo "<th style='color:green;'>Price</th>";
                                echo "<th style='color:green;'>Amount</th>";
                                echo "</tr>";
                                echo "<tr style='font-weight:bold;'>";
                                echo "<td colspan='1'>".number_format_ind($sin_qty)."</td>";
                                if($sin_amt > 0 && $sin_qty > 0){
                                    echo "<td colspan='1'>".number_format_ind($sin_amt / $sin_qty)."</td>";
                                }
                                else{
                                    echo "<td colspan='1'>".number_format_ind(0)."</td>";
                                }
                                
                                echo "<td colspan='1'>".number_format_ind($sin_amt)."</td>";

                                echo "<td colspan='1'>".number_format_ind($sout_qty)."</td>";
                                if($sout_amt > 0 && $sout_qty > 0){
                                    echo "<td colspan='1'>".number_format_ind($sout_amt / $sout_qty)."</td>";
                                }
                                else{
                                    echo "<td colspan='1'>".number_format_ind(0)."</td>";
                                }
                                
                                echo "<td colspan='1'>".number_format_ind($sout_amt)."</td>";

                                
                                echo "<td colspan='1'>".number_format_ind($scon_qty)."</td>";
                                if($scon_amt > 0 && $scon_qty > 0){
                                    echo "<td colspan='1'>".number_format_ind($scon_amt / $scon_qty)."</td>";
                                }
                                else{
                                    echo "<td colspan='1'>".number_format_ind(0)."</td>";
                                }
                                
                                echo "<td colspan='1'>".number_format_ind($scon_amt)."</td>";

                                echo "</tr>";
                                }
                            }
                            echo "<tr>";
                            echo "<th colspan='9' style='text-align:center;color:green;'>Feed Details (Management Cost)</th>";
                            echo "</tr>";
                            echo "<tr style='text-align:center;'>";
                            echo "<th colspan='3'>Feed In</th>";
                            echo "<th colspan='3'>Feed Out</th>";
                            echo "<th colspan='3'>Feed Consumed</th>";
                            //echo "<th colspan='3'>Feed Balance</th>";
                            echo "</tr>";
                            echo "<tr style='text-align:center;'>";
                            echo "<th>Qty</th>";
                            echo "<th>Price</th>";
                            echo "<th>Amount</th>";
                            echo "<th>Qty</th>";
                            echo "<th>Price</th>";
                            echo "<th>Amount</th>";
                            echo "<th>Qty</th>";
                            echo "<th>Price</th>";
                            echo "<th>Amount</th>";
                            echo "</tr>";
                            echo "<tr>";
                            echo "<td colspan='1'>".number_format_ind($total_feedin_qty)."</td>";
                            if($total_feedin_cost > 0 && $total_feedin_qty > 0){
                                echo "<td colspan='1'>".number_format_ind($total_feedin_cost / $total_feedin_qty)."</td>";
                            }
                            else{
                                echo "<td colspan='1'>".number_format_ind(0)."</td>";
                            }
                            
                            echo "<td colspan='1'>".number_format_ind($total_feedin_cost)."</td>";

                            echo "<td colspan='1'>".number_format_ind($total_feedout_qty)."</td>";
                            if($total_feedout_cost > 0 && $total_feedout_qty > 0){
                                echo "<td colspan='1'>".number_format_ind($total_feedout_cost / $total_feedout_qty)."</td>";
                            }
                            else{
                                echo "<td colspan='1'>".number_format_ind(0)."</td>";
                            }
                            
                            echo "<td colspan='1'>".number_format_ind($total_feedout_cost)."</td>";

                            
                            echo "<td colspan='1'>".number_format_ind($total_feedconsumed_qty)."</td>";
                            if($total_feedconsumed_cost > 0 && $total_feedconsumed_qty > 0){
                                echo "<td colspan='1'>".number_format_ind($total_feedconsumed_cost / $total_feedconsumed_qty)."</td>";
                            }
                            else{
                                echo "<td colspan='1'>".number_format_ind(0)."</td>";
                            }
                            
                            echo "<td colspan='1'>".number_format_ind($total_feedconsumed_cost)."</td>";

                            $total_feedbal_qty = $total_feedin_qty - ($total_feedout_qty + $total_feedconsumed_qty);
                            $total_feedbal_cost = $total_feedin_cost - ($total_feedout_cost + $total_feedconsumed_cost);
                            //echo "<td colspan='1'>".number_format_ind($total_feedbal_qty)."</td>";
                            //echo "<td colspan='1'>".number_format_ind($total_feedbal_cost / $total_feedbal_qty)."</td>";
                            //echo "<td colspan='1'>".number_format_ind($total_feedbal_cost)."</td>";
                            echo "</tr>";

                            if(number_format_ind($total_medvacin_qty) != "0.00" || number_format_ind($total_medvacout_qty) != "0.00" || number_format_ind($total_medvacconsumed_qty) != "0.00"){
                            echo "<tr>";
                            echo "<th colspan='9' style='text-align:center;color:green;'>Medicine/Vaccine Details (Management Cost)</th>";
                            echo "</tr>";
                            echo "<tr style='text-align:center;'>";
                            echo "<th colspan='3'>Medicine/Vaccine In</th>";
                            echo "<th colspan='3'>Medicine/Vaccine Out</th>";
                            echo "<th colspan='3'>Medicine/Vaccine Consumed</th>";
                            //echo "<th colspan='3'>Medicine/Vaccine Balance</th>";
                            echo "</tr>";
                            echo "<tr style='text-align:center;'>";
                            echo "<th>Qty</th>";
                            echo "<th>Price</th>";
                            echo "<th>Amount</th>";
                            echo "<th>Qty</th>";
                            echo "<th>Price</th>";
                            echo "<th>Amount</th>";
                            echo "<th>Qty</th>";
                            echo "<th>Price</th>";
                            echo "<th>Amount</th>";
                            echo "</tr>";
                            echo "<tr>";
                            echo "<td colspan='1'>".number_format_ind($total_medvacin_qty)."</td>";
                            if($total_medvacin_cost > 0 && $total_medvacin_qty > 0){
                                echo "<td colspan='1'>".number_format_ind($total_medvacin_cost / $total_medvacin_qty)."</td>";
                            }
                            else{
                                echo "<td colspan='1'>".number_format_ind(0)."</td>";
                            }
                            
                            echo "<td colspan='1'>".number_format_ind($total_medvacin_cost)."</td>";

                            echo "<td colspan='1'>".number_format_ind($total_medvacout_qty)."</td>";
                            if($total_medvacout_cost > 0 && $total_medvacout_qty > 0){
                                echo "<td colspan='1'>".number_format_ind($total_medvacout_cost / $total_medvacout_qty)."</td>";
                            }
                            else{
                                echo "<td colspan='1'>".number_format_ind(0)."</td>";
                            }
                            
                            echo "<td colspan='1'>".number_format_ind($total_medvacout_cost)."</td>";

                            echo "<td colspan='1'>".number_format_ind($total_medvacconsumed_qty)."</td>";
                            if($total_medvacconsumed_cost > 0 && $total_medvacconsumed_qty > 0){
                                echo "<td colspan='1'>".number_format_ind($total_medvacconsumed_cost / $total_medvacconsumed_qty)."</td>";
                            }
                            else{
                                echo "<td colspan='1'>".number_format_ind(0)."</td>";
                            }
                            
                            echo "<td colspan='1'>".number_format_ind($total_medvacconsumed_cost)."</td>";

                            $total_medvacbal_qty = $total_medvacin_qty - ($total_medvacout_qty + $total_medvacconsumed_qty);
                            $total_medvacbal_cost = $total_medvacin_cost - ($total_medvacout_cost + $total_medvacconsumed_cost);
                            //echo "<td colspan='1'>".number_format_ind($total_medvacbal_qty)."</td>";
                            //echo "<td colspan='1'>".number_format_ind($total_medvacbal_cost / $total_medvacbal_qty)."</td>";
                            //echo "<td colspan='1'>".number_format_ind($total_medvacbal_cost)."</td>";
                            echo "</tr>";
                            }
                            
                            if(number_format_ind($batch_cflag[$bhcode]) != "0.00"){
                                if($item_cats == "all" && $itm_code == "all"){
                                    $squery = "UPDATE `broiler_rearingcharge` SET `actual_feed_cost` = '$total_feedconsumed_cost',`actual_medicine_cost` = '$total_medvacconsumed_cost' WHERE `batch_code` LIKE '$bhcode' AND `active` = '1' AND `dflag` = '0';";
                                }
                                else if(!empty($feed_cat[$item_cats]) && $itm_code == "all"){
                                    $squery = "UPDATE `broiler_rearingcharge` SET `actual_feed_cost` = '$total_feedconsumed_cost' WHERE `batch_code` LIKE '$bhcode' AND `active` = '1' AND `dflag` = '0';";
                                }
                                else if(!empty($medvac_cat[$item_cats]) && $itm_code == "all"){
                                    $squery = "UPDATE `broiler_rearingcharge` SET `actual_medicine_cost` = '$total_medvacconsumed_cost' WHERE `batch_code` LIKE '$bhcode' AND `active` = '1' AND `dflag` = '0';";
                                }
                                if($status == "rerate"){ if(!mysqli_query($conn,$squery)) { echo mysqli_error($conn); } }
                            } else{ }
                        }
                    }
                    
                    $sl_no = $avg_qty = $avg_price = $avg_amount = $cur_amt = 0;
                    $key_index = $start_date = $end_date = $date_key = $item_key = $tnum_key = $stk_key = "";
                    $acc_out_list = $acc_in_list = $items = $trnums = $stock_in_details = $stock_consumed_details = $item_name = array();
                ?>
                
            </tbody>
            <?php
            }
            ?>
        </table><br/><br/><br/>
        
        <script>
            function checkval(){
                var batches = document.getElementById("batches").value;
                if(batches.match("select")){
                    alert("Kindly select Farm Batch Description");
                    document.getElementById("batches").focus();
                    return false;
                }
                else{
                    return true;
                }
            }
            function fetch_category_items(){
                var item_cats = document.getElementById("item_cats").value;
                removeAllOptions(document.getElementById("items"));
                myselect = document.getElementById("items"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(item_cats == "all"){
                    <?php
                        foreach($item_code as $icode){
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        }
                    ?>
                }
                else{
                    <?php
                        foreach($item_code as $icode){
                            $iccode = $item_category[$icode];
                            echo "if(item_cats == '$iccode'){";
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icode]; ?>"); theOption1.value = "<?php echo $icode; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                            echo "}";
                        }
                    ?>
                }
            }
            function fetch_farm_batch(a){
                var fcode = document.getElementById(a).value;
                removeAllOptions(document.getElementById("batches"));
                if(fcode == "all" || fcode == "select"){
                    myselect = document.getElementById("batches"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-Select-"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                }
                else{
                    myselect = document.getElementById("batches"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-Select-"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                    theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                    <?php
                        foreach($batch_code as $batch_no){
                            $farm_codes = $batch_farm[$batch_no];
                            echo "if(fcode == '$farm_codes'){";
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $batch_name[$batch_no]; ?>"); theOption1.value = "<?php echo $batch_no; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                            echo "}";
                        }
                    ?>
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