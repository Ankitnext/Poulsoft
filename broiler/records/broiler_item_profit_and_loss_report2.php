<?php
//broiler_item_profit_and_loss_report2.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "P &amp; L Report";
include "header_head.php";

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_category[$row['code']] = $row['category']; }

$fdate = $tdate = date("Y-m-d"); $sector_array_list = array(); $sector_array_list["all"] = "all";
$sector_list = ""; $sec_all_flag = 0; $excel_type = "display"; $sector_filter = ""; $slist_count = 0;


if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));

    $sector_array_list = array();
    foreach($_POST["sectors"] as $scode){
        $slist_count++;
        if($sector_list == ""){ $sector_list = $scode; } else{ $sector_list = $sector_list."','".$scode; }
        if($sector_item_list == ""){ $sector_item_list = $scode; } else{ $sector_item_list = $sector_item_list."@".$scode; }
        $sector_array_list[$scode] = $scode;
        if($scode == "all"){ $sec_all_flag = 1; } else{ }
    }
    if($sec_all_flag == 1){
        $sector_filter = "";
        $slist_count = 1;
        $sec_code = "all";
        $acc_link = "https://".$_SERVER['HTTP_HOST']."/records/broiler_account_ledger.php?fdate=".$fdate."&tdate=".$tdate."&sectors=".$sec_code."&coas=";
    }
    else{
        $sector_filter = " AND `location` IN ('$sector_list')";
        if($slist_count == 1){
            $sec_code = $sector_list;
            $acc_link = "https://".$_SERVER['HTTP_HOST']."/records/broiler_account_ledger.php?fdate=".$fdate."&tdate=".$tdate."&sectors=".$sec_code."&coas=";
        }
        else{
            $slist_count = 0;
            $acc_link = "javascript:void(0)";
        }
    }
    
}
else{ }
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
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
        <table class="tbl" style="width:auto;" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="4" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>P &amp; L Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_item_profit_and_loss_report2.php" method="post" onsubmit="return checkval()">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="6">
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
                                    <label>Farm/Sector</label>
                                    <select name="sectors[]" id="sectors[]" class="form-control select2" style="width:250px;" multiple >
                                        <option value="all" <?php foreach($sector_array_list as $sectors){ if($sectors == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($sector_code as $fcode){ if($sector_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php foreach($sector_array_list as $sectors){ if($sectors == $fcode){ echo "selected"; } } ?>><?php echo $sector_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group"><br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <thead class="thead3" align="center">
                <tr align="center">
                    <th colspan="3">Expense</th>
                    <th colspan="3">Revenue</th>
                </tr>
                <tr align="center">
                    <th>Code</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_POST['submit_report']) == true || isset($_GET['icat']) == true){
                $acc_list = $exp_type = "";
                $sql = "SELECT * FROM `acc_types` WHERE `description` LIKE 'Expense'"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $exp_type = $row['code']; }
                
                $sql = "SELECT * FROM `acc_coa` WHERE `type` LIKE '$exp_type' AND `description` NOT LIKE '%cogs%' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $exp_coa_arr_codes[$row['code']] = $row['code'];
                    $exp_coa_arr_names[$row['code']] = $row['description'];
                    if($acc_list == ""){ $acc_list = $row['code']; }else{ $acc_list = $acc_list."','".$row['code']; }
                }
                $cogs_codes = $sac_codes = array();
                $sql = "SELECT * FROM `item_category`"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $cogs_codes[$row['cogsac']] = $row['cogsac'];
                    $sac_codes[$row['sac']] = $row['sac'];
                    if($acc_list == ""){ $acc_list = $row['cogsac']; }else{ $acc_list = $acc_list."','".$row['cogsac']; }
                    if($acc_list == ""){ $acc_list = $row['sac']; }else{ $acc_list = $acc_list."','".$row['sac']; }
                }

                $cogs_coa_arr_codes = $cogs_coa_arr_name = array(); $coa_list = ""; $coa_list = implode("','", $cogs_codes);
                $sql = "SELECT * FROM `acc_coa` WHERE `code` IN ('$coa_list') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $cogs_coa_arr_codes[$row['code']] = $row['code']; $cogs_coa_arr_name[$row['code']] = $row['description']; }

                $sac_coa_arr_codes = $sac_coa_arr_name = array(); $coa_list = ""; $coa_list = implode("','", $sac_codes);
                $sql = "SELECT * FROM `acc_coa` WHERE `code` IN ('$coa_list') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){ $sac_coa_arr_codes[$row['code']] = $row['code']; $sac_coa_arr_name[$row['code']] = $row['description']; }

                $sql = "SELECT crdr,coa_code,SUM(amount) as amount FROM `account_summary` WHERE `date` >= '$fdate' AND  `date` <= '$tdate'".$sector_filter."".$item_filter." AND `coa_code` IN ('$acc_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `coa_code`,`crdr` ORDER BY `coa_code`,`crdr` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    if(strtotime($row['date']) < strtotime($fdate)){
                        if($row['crdr'] == "CR"){ $coa_cr_amt[$row['coa_code']] += (float)$row['amount']; }
                        else if($row['crdr'] == "DR"){ $coa_dr_amt[$row['coa_code']] += (float)$row['amount']; }
                        else{ }
                    }
                }
                $td_col1_val = $td_col2_val = $td_col3_val = $td_col4_val = $td_col5_val = $td_col6_val = array(); $a = $b = $tot_cogs_amt = $tot_sac_amt = 0;
                //Calculate and fetch COGS Accounts
                foreach($cogs_coa_arr_codes as $cgs){
                    if(!empty($coa_cr_amt[$cgs]) && (float)$coa_cr_amt[$cgs] != 0 || !empty($coa_dr_amt[$cgs]) && (float)$coa_dr_amt[$cgs] != 0){
                        $final_amt = 0;
                        $final_amt = (float)$coa_dr_amt[$cgs] - (float)$coa_cr_amt[$cgs];
                        if((float)$final_amt != 0){
                            $a++;
                            if($slist_count == 1){
                                $td_col1_val[$a] = '<td style="text-align:left;"><a href="'.$acc_link.''.$cgs.'" class="acc_link" target="_BLANK">'.$cgs.'</a></td>';
                            }
                            else{
                                $td_col1_val[$a] = '<td style="text-align:left;">'.$cgs.'</td>';
                            }
                            $td_col2_val[$a] = '<td style="text-align:left;">'.$cogs_coa_arr_name[$cgs].'</td>';
                            $td_col3_val[$a] = '<td style="text-align:right;">'.number_format_ind(round($final_amt,2)).'</td>';
                            $tot_cogs_amt += (float)round($final_amt,2);
                        }
                    }
                }
                $a++;
                $tot_cogs_amt = round($tot_cogs_amt,2);
                $td_col1_val[$a] = '<th style="text-align:left;" colspan="2">Total COGS</th>';
                $td_col2_val[$a] = '';
                $td_col3_val[$a] = '<th style="text-align:right;">'.number_format_ind($tot_cogs_amt).'</th>';

                //Calculate and fetch SAC Accounts
                foreach($sac_coa_arr_codes as $slc){
                    if(!empty($coa_cr_amt[$slc]) && (float)$coa_cr_amt[$slc] != 0 || !empty($coa_dr_amt[$slc]) && (float)$coa_dr_amt[$slc] != 0){
                        $final_amt = 0;
                        $final_amt = (float)$coa_cr_amt[$slc] - $coa_dr_amt[$slc];
                        if((float)$final_amt != 0){
                            $b++;
                            if($slist_count == 1){
                                $td_col4_val[$b] = '<td style="text-align:left;"><a href="'.$acc_link.''.$slc.'" class="acc_link" target="_BLANK">'.$slc.'</a></td>';
                            }
                            else{
                                $td_col4_val[$b] = '<td style="text-align:left;">'.$slc.'</td>';
                            }
                            $td_col5_val[$b] = '<td style="text-align:left;">'.$sac_coa_arr_name[$slc].'</td>';
                            $td_col6_val[$b] = '<td style="text-align:right;">'.number_format_ind(round($final_amt,2)).'</td>';
                            $tot_sac_amt += (float)round($final_amt,2);
                        }
                    }
                }
                $b++;
                $tot_sac_amt = round($tot_sac_amt,2);
                $td_col4_val[$b] = '<th style="text-align:left;" colspan="2">Total Sales</th>';
                $td_col5_val[$b] = '';
                $td_col6_val[$b] = '<th style="text-align:right;">'.number_format_ind($tot_sac_amt).'</th>';

                if($a > $b){ $c = $a - $b; for($d = 1;$d <= $c;$d++){ $b++; $td_col4_val[$b] = '<td></td>'; $td_col5_val[$b] = '<td></td>'; $td_col6_val[$b] = '<td></td>'; } }
                else if($a < $b){ $c = $b - $a; for($d = 1;$d <= $c;$d++){ $a++; $td_col1_val[$a] = '<td></td>'; $td_col2_val[$a] = '<td></td>'; $td_col3_val[$a] = '<td></td>'; } }

                if((float)$tot_cogs_amt > (float)$tot_sac_amt){
                    $gprofit_flag = $gprofit_amount = 0;
                    $gross_name1 = "Gross Loss"; $gross_name2 = "Gross Loss C/D";
                    $gprofit_amount = (float)$tot_cogs_amt - (float)$tot_sac_amt;
                }
                else{
                    $gprofit_flag = 1; $gprofit_amount = 0;
                    $gross_name1 = "Gross Profit"; $gross_name2 = "Gross Profit C/D";
                    $gprofit_amount = (float)$tot_sac_amt - (float)$tot_cogs_amt;
                }

                $a++;
                $td_col1_val[$a] = '<th style="text-align:left;"></th>';
                $td_col2_val[$a] = '<th style="text-align:left;">'.$gross_name1.'</th>';
                $td_col3_val[$a] = '<th style="text-align:right;">'.number_format_ind($gprofit_amount).'</th>';

                $b++;
                $td_col4_val[$b] = '<th style="text-align:left;"></th>';
                $td_col5_val[$b] = '<th style="text-align:left;">'.$gross_name2.'</th>';
                $td_col6_val[$b] = '<th style="text-align:right;">'.number_format_ind($gprofit_amount).'</th>';

                $a++;
                $td_col1_val[$a] = '<th style="text-align:center;color:red;" colspan="3">Expenses</th>';
                $td_col2_val[$a] = '';
                $td_col3_val[$a] = '';

                $b++;
                $td_col4_val[$b] = '<th></th>';
                $td_col5_val[$b] = '<th></th>';
                $td_col6_val[$b] = '<th></th>';

                //Calculate and fetch Expenses Accounts
                foreach($exp_coa_arr_codes as $exp){
                    if(!empty($coa_cr_amt[$exp]) && (float)$coa_cr_amt[$exp] != 0 || !empty($coa_dr_amt[$exp]) && (float)$coa_dr_amt[$exp] != 0){
                        $final_amt = 0;
                        $final_amt = (float)$coa_dr_amt[$exp] - $coa_cr_amt[$exp];
                        if((float)$final_amt != 0){
                            $a++;
                            if($slist_count == 1){
                                $td_col1_val[$a] = '<td style="text-align:left;"><a href="'.$acc_link.''.$exp.'" class="acc_link" target="_BLANK">'.$exp.'</a></td>';
                            }
                            else{
                                $td_col1_val[$a] = '<td style="text-align:left;">'.$exp.'</td>';
                            }
                            $td_col2_val[$a] = '<td style="text-align:left;">'.$exp_coa_arr_names[$exp].'</td>';
                            $td_col3_val[$a] = '<td style="text-align:right;">'.number_format_ind(round($final_amt,2)).'</td>';
                            $tot_exp_amt += (float)round($final_amt,2);
                        }
                    }
                }
                $a++;
                $tot_exp_amt = round($tot_exp_amt,2);
                $td_col1_val[$a] = '<th style="text-align:left;" colspan="2">Total Expenses</th>';
                $td_col2_val[$a] = '';
                $td_col3_val[$a] = '<th style="text-align:right;">'.number_format_ind($tot_exp_amt).'</th>';

                if($a > $b){ $c = $a - $b; for($d = 1;$d <= $c;$d++){ $b++; $td_col4_val[$b] = '<td></td>'; $td_col5_val[$b] = '<td></td>'; $td_col6_val[$b] = '<td></td>'; } }
                else if($a < $b){ $c = $b - $a; for($d = 1;$d <= $c;$d++){ $a++; $td_col1_val[$a] = '<td></td>'; $td_col2_val[$a] = '<td></td>'; $td_col3_val[$a] = '<td></td>'; } }

                if($gprofit_flag == 1){
                    if((float)$gprofit_amount > (float)$tot_exp_amt){
                        $nprofit_flag = 1;
                        $nprofit_amount = (float)$gprofit_amount - (float)$tot_exp_amt;
                    }
                    else{
                        $nprofit_flag = 0;
                        $nprofit_amount = (float)$tot_exp_amt - (float)$gprofit_amount;
                    }
                }
                else{
                    $nprofit_flag = 0;
                    $nprofit_amount = (float)$gprofit_amount + (float)$tot_exp_amt;
                }

                if($nprofit_flag == 1){
                    $net_name = "Net Profit";
                    $nprofit_amount = round($nprofit_amount,2);
                    $final_cogs_amt = (float)$tot_cogs_amt + (float)$tot_exp_amt + (float)$nprofit_amount;
                    $final_sac_amt = (float)$tot_sac_amt;
                    $a++;
                    $td_col1_val[$a] = '<th style="text-align:left;"></th>';
                    $td_col2_val[$a] = '<th style="text-align:left;">'.$net_name.'</th>';
                    $td_col3_val[$a] = '<th style="text-align:right;">'.number_format_ind($nprofit_amount).'</th>';
    
                    $b++;
                    $td_col4_val[$b] = '<th></th>';
                    $td_col5_val[$b] = '<th></th>';
                    $td_col6_val[$b] = '<th></th>';
                }
                else{
                    $net_name = "Net Loss";
                    $nprofit_amount = round($nprofit_amount,2);
                    $final_cogs_amt = (float)$tot_cogs_amt + (float)$tot_exp_amt;
                    $final_sac_amt = (float)$tot_sac_amt + (float)$nprofit_amount;
                    
                    $a++;
                    $td_col1_val[$a] = '<th></th>';
                    $td_col2_val[$a] = '<th></th>';
                    $td_col3_val[$a] = '<th></th>';

                    $b++;
                    $td_col4_val[$b] = '<th style="text-align:left;"></th>';
                    $td_col5_val[$b] = '<th style="text-align:left;">'.$net_name.'</th>';
                    $td_col6_val[$b] = '<th style="text-align:right;">'.number_format_ind($nprofit_amount).'</th>';

                }

                $a++;
                $td_col1_val[$a] = '<th class="thead4" style="text-align:left;"></th>';
                $td_col2_val[$a] = '<th class="thead4" style="text-align:left;">Final Total</th>';
                $td_col3_val[$a] = '<th class="thead4" style="text-align:right;">'.number_format_ind($final_cogs_amt).'</th>';

                $b++;
                $td_col4_val[$b] = '<th class="thead4" style="text-align:left;"></th>';
                $td_col5_val[$b] = '<th class="thead4" style="text-align:left;">Final Total</th>';
                $td_col6_val[$b] = '<th class="thead4" style="text-align:right;">'.number_format_ind($final_sac_amt).'</th>';

                $html = "";
                for($m = 1;$m <= $a;$m++){
                    $html .= "<tr>";
                    $html .= $td_col1_val[$m]."".$td_col2_val[$m]."".$td_col3_val[$m]."".$td_col4_val[$m]."".$td_col5_val[$m]."".$td_col6_val[$m];
                    $html .= "</tr>";
                }
                echo $html;
            }
        ?>
            </tbody>
        </table>
        <script>
            function checkval(){
                var sectors = document.getElementById("sectors").value;
                if(sectors.match("select")){
                    alert("Please select Farm/Sector");
                    document.getElementById("sectors").focus();
                    return false;
                }
                else{
                    return true;
                }
            }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>