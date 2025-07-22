<?php
//broiler_customerledger_masterreportpdf.php
$requested_data = json_decode(file_get_contents('php://input'),true);

if(!isset($_SESSION)){ session_start(); }
if(!empty($_GET['db'])){ $db = $_SESSION['db'] = $_GET['db']; } else { $db = ''; }
if($db == ''){
    include "../newConfig.php";
    
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
    global $page_title; $page_title = "Customer Ledger PDF Report";
    include "header_head.php";
}
else{
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    global $page_title; $page_title = "Customer Ledger PDF Report";
    include "header_head.php";
}

include "../broiler_check_tableavailability.php";

/*Master Report Format*/
$href = explode("/", $_SERVER['REQUEST_URI']); $field_href = explode("?", $href[2]); $user_code = $_SESSION['userid'];
$col_names_all = $act_col_numbs = $nac_col_numbs = array(); $i = $col_count = 0; $key_id = "";
if($count64 > 0){
    $sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1);
    while($row1 = mysqli_fetch_assoc($query1)){
        if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
        else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
    }
    $sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
    $query2 = mysqli_query($conn,$sql2); $c1 = mysqli_num_rows($query2); $fbc = 999;
    if($c1 > 0){
        while($row2 = mysqli_fetch_assoc($query2)){
            foreach($col_names_all as $cna){
                $fas_details = explode(":",$row2[$cna]);
                if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] > 0){
                    $key_id = $row2[$cna];
                    $act_col_numbs[$key_id] = $cna;
                    //echo "<br/>".$act_col_numbs[$key_id];
                    if($cna == "customer_credit"){ if($fbc > $fas_details[2]){ $fbc = $fas_details[2]; } }
                    if($cna == "customer_debit"){ if($fbc > $fas_details[2]){ $fbc = $fas_details[2]; } }
                    if($cna == "customer_runningbalance"){ if($fbc > $fas_details[2]){ $fbc = $fas_details[2]; } }
                }
                else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] > 0){
                    $key_id = $row2[$cna];
                    $nac_col_numbs[$key_id] = $cna;
                }
                else{ }
            }
            $col_count = $row2['column_count'];
        }
    }
}
$farm_region = $farm_branch = array();
if($count103 > 0){
$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $group_name[$row['code']] = $row['description']; $group_code[$row['code']] = $row['code']; }
}
$fdate = $tdate = date("Y-m-d"); $groups = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $groups = $_POST['groups'];

	$excel_type = $_POST['export'];
	$url = "../PHPExcel/Examples/CustomerHistoyMasterReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&vendors=".$vendors;
}
else{
    $url = "";
}
if($groups == "all"){ $group_filter = ""; } else{ $group_filter = " AND `groupcode` IN ('$groups')"; }
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script src="../../col/jquery-3.5.1.js"></script>
        <script src="../../col/jquery.dataTables.min.js"></script>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
            .col-md-6 {
                position: relative;  left: 200px;
                max-width: 0%;
            }
            .col-md-5{
                position: relative;  left: 200px;
            }
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left;
            }
            table thead,
            table tfoot {
                position: sticky;
            }
            table thead {
            inset-block-start: 0; /* "top" */
            }
            table tfoot {
            inset-block-end: 0; /* "bottom" */
            }
        </style>
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
               .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
				
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5);}
                .thead2_empty_row { display:none; }
                .tbl_toggle { display:none; }
                .dataTables_filter { display:none; }
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
        <table class="tbl" align="center"   width="1300px">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></th>
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Customer Ledger PDF Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <?php if($db == ''){?>
            <form action="broiler_customerledger_masterreportpdf.php" method="post">
            <?php } else { ?>
            <form action="broiler_customerledger_masterreportpdf.php?db=<?php echo $db; ?>" method="post">
            <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                        <th colspan="16">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Customer Group</label>
                                    <select name="groups" id="groups" class="form-control select2">
                                        <option value="all" <?php if($groups == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($group_code as $gcode){ if($group_name[$gcode] != ""){ ?>
                                        <option value="<?php echo $gcode; ?>" <?php if($groups == $gcode){ echo "selected"; } ?>><?php echo $group_name[$gcode]; ?></option>
                                        <?php } } ?>
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
        </table>
        <table class="tbl_toggle" style="position: relative;  left: 35px;">
            <tr><td><br></td></tr> 
            <tr>
                <td>
                <div id='control_sh'>
                    <?php
                        for($i = 1;$i <= $col_count;$i++){
                            $key_id = "A:1:".$i; $key_id1 = "A:0:".$i;
                            if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_trnum"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_trnum" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Transaction No.</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_link_trnum"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_link_trnum" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Linked Transaction</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Customer Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_billno"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_billno" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Doc No.</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_itemname"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_itemname" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Item Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_birds"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_birds" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Birds</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_snt_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_snt_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sent Qty</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_mort_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_mort_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mort Qty</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_cull_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_cull_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cull Qty</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_rcd_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_rcd_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Received Qty</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_fre_qty"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_fre_qty" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Free Qty</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_itemprice"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_itemprice" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Item Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_dis_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_dis_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Discount %</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_dis_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_dis_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Discount Amt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_gst_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_gst_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>GST %</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_gst_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_gst_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>GST Amt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_tcds_per"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_tcds_per" onclick="update_masterreport_status(this.id);" '.$checked.'><span>TCS %</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_tcds_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_tcds_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>TCS Amt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_itemamount"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_itemamount" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Item Amount</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_freight_type"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_freight_type" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Type</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_freight_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_freight_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Amt</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_freight_pay_type"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_freight_pay_type" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Pay Type</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_freight_pay_acc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_freight_pay_acc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Pay Account</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_freight_acc"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_freight_acc" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Freight Account</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_round_off"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_round_off" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Round Off</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_finl_amt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_finl_amt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Invoice Amount</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_avg_price"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_avg_price" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Avg Price</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_avg_wt"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_avg_wt" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Avg Weight</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_profit"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_profit" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Profit</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_remarks"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_remarks" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Remarks</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_warehouse"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_warehouse" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sector/Warehouse</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_farm_batch"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_farm_batch" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_supervisor_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_supervisor_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Supervisor Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_bag_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_bag_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Bag Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_bag_count"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_bag_count" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Bag Count</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_batch_no"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_batch_no" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Batch No</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_exp_date"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_exp_date" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Expiry Date</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_vehicle_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_vehicle_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Vehicle No.</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_driver_code"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_driver_code" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Driver Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_sale_type"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_sale_type" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sale Type</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_gc_flag"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_gc_flag" onclick="update_masterreport_status(this.id);" '.$checked.'><span>GC Flag</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_addedemp"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_addedemp" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Added By</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_addedtime"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_addedtime" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Added Time</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_updatedemp"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_updatedemp" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Edited By</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_updatedtime"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_updatedtime" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Edited Time</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_latitude"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_latitude" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sale Latitude</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_longitude"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_longitude" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sale Longitude</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "Customer_sale_location"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="Customer_sale_location" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sale Location</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_imei"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_imei" onclick="update_masterreport_status(this.id);" '.$checked.'><span>IMEI</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_mob_flag"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_mob_flag" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Mobile Flag</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_sale_image"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_sale_image" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Sale Image</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "region_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="region_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Region Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "branch_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="branch_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Branch Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "line_name"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="line_name" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Line Name</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_credit"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_credit" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Cr</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_debit"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_debit" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Dr</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_runningbalance"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="customer_runningbalance" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Balance</span>'; }
                            else if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "transaction_type"){ if(!empty($act_col_numbs[$key_id])){ $checked = "checked"; } else{ $checked = ""; } echo '<input type="checkbox" class="hide_show" id="transaction_type" onclick="update_masterreport_status(this.id);" '.$checked.'><span>Type</span>'; }
                            else{ }
                        }
                    ?>
                </div>
                </td>
            </tr>
            <tr><td><br></td></tr>
        </table>
        <table class="tbl" align="center">
            <form action="..\print\Examples\broiler_customerledger_masterreportpdfs.php" method="post" target="_BLANK" onsubmit="return checkval();">
                <thead class="thead3" align="center">
                    <tr align="center">
                        <th colspan="16">
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
                                    <label>Select All</label>
                                    <input type="checkbox" name="checkall" id="checkall" class="form-control" style="transform: scale(.7);" onchange="checkedall()" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Type</label>
                                    <select name="send_type" id="send_type" class="form-control select2">
                                        <option value="send_pdf" selected >-Send PDF-</option>
                                        <option value="view_normal_print">-View Normal Print-</option>
                                        <option value="view_pdf_print">-View PDF Print-</option>
                                    </select>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
						<th>Sl.No.</th>
						<th>Selection</th>
                        <th>Name</th>
                        <th>Mobile No.</th>
                    </tr>
                    <?php
                    if($count98 > 0){
                        $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'C' AND `dflag` = '0'".$group_filter." ORDER BY `name` ASC";
                        $query = mysqli_query($conn,$sql); $c = 0;
                        while($row = mysqli_fetch_assoc($query)){
                            $c++;
                            $code = $row['code'];
                            $name = $row['name'];
                            $mobe = $row['mobile1'];

                            echo "<tr>";
                            echo "<td style='width:100px;text-align:center;'>".$c."</td>";
                            echo "<td style='width:100px;text-align:center;'><input type='checkbox' name='ccode[$c]' id='ccode[$c]' value='$code' /></td>";
                            echo "<td style='padding-left:10px;text-align:left;'><input type='text' name='cusname[$c]' id='cusname[$c]' class='form-control' value='$name' style='padding:0; padding-left:1px;width:250px;height:16px;border:none;background:inherit;text-decoration:none;font-size:13px;' readonly /></td>";
                            echo "<td style='padding-left:10px;text-align:left;'><input type='text' name='cmobile[$c]' id='cmobile[$c]' class='form-control' value='$mobe' style='padding:0; padding-left:1px;width:450px;height:16px;border:none;background:inherit;text-decoration:none;font-size:13px;' readonly /></td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                    <tr class="thead3">
                        <th colspan="4" style="text-align:center;">
                            <button type="submit" name="send_cuspdf" id="send_cuspdf" class="btn btn-sm btn-success">Send PDF</button>
                        </th>
                    </tr>
                </tbody>
            </form>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
        <script>
            function checkval(){
                var incr = '<?php echo $c; ?>';
                var l = true; var c = d = ""; var a = g = 0; var e = [];
                send_type = document.getElementById("send_type").value;
                for(var b = 1;b <= incr;b++){
                    if(l == true){
                        c = document.getElementById("ccode["+b+"]");
                        if(c.checked == true){
                            a++;
                            if(send_type == "send_pdf"){
                                d = document.getElementById("cmobile["+b+"]").value;
                                if(d.match(",")){
                                    e = d.split(",");
                                    g = 0;
                                    for(var f = 0;f < e.length;f++){ if(e[f].length == 10){ g++; } }
                                    if(g == 0){
                                        alert("Please enter appropriate Mobile No to send PDF");
                                        document.getElementById("cusname["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").focus();
                                        l = false;
                                    }
                                }
                                else{
                                    d = document.getElementById("cmobile["+b+"]").value;
                                    if(d.length != 10){
                                        alert("Please enter appropriate Mobile No to send PDF");
                                        document.getElementById("cusname["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").style.color = "red";
                                        document.getElementById("cmobile["+b+"]").focus();
                                        l = false;
                                    }
                                }
                            }
                        }
                    }
				}
                if(l == true){
                    if(a > 0){
                        l = true;
                    }
                    else{
                        alert("select atlest one customer to send/Display Ledger Report");
                        l = false;
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
			function checkedall(){
                var incr = '<?php echo $c; ?>';
				var a = document.getElementById("checkall");
                send_type = document.getElementById("send_type").value;
                var c = d = ""; var e = []; var g = 0;
				if(a.checked == true){
					for(var b = 1;b <= incr;b++){
					    c = document.getElementById("ccode["+b+"]");
						
                        if(send_type == "send_pdf"){
                            d = document.getElementById("cmobile["+b+"]").value;
                            if(d.match(",")){
                                e = d.split(",");
                                g = 0;
                                for(var f = 0;f < e.length;f++){ if(e[f].length == 10){ g++; } }
                                if(g == 0){ }
                                else{
                                    c.checked = true;
                                }
                            }
                            else{
                                d = document.getElementById("cmobile["+b+"]").value;
                                if(d.length != 10){ }
                                else{
                                    c.checked = true;
                                }
                            }
                        }
                        else{
                            c.checked = true;
                        }
					}
				}
				else{
					for(var b = 1;b <= incr;b++){
					    c = document.getElementById("ccode["+b+"]");
						c.checked = false;
					}
				}
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
                            //alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.");
                        }
                        else{
                            alert("Invalid request \n Kindly check and try again ...!");
                        }
                    }
                }
            }
        $(document).ready(function(){
            var table =  $('#mine').DataTable({
                paging: false,
            });
            
            $("#hide_show_all").on("change",function(){
                var hide = $(this).is(":checked");
                $(".hide_show").prop("checked", hide);
                if(hide){
                    $('#mine tr th').hide(100);
                    $('#mine tr td').hide(100);
                }else{
                    $('#mine tr th').show(100);
                    $('#mine tr td').show(100);
                }
            });

            $(".hide_show").on("change",function(){
                var hide = $(this).is(":checked");
                
                var all_ch = $(".hide_show:checked").length == $(".hide_show").length;

                $("#hide_show_all").prop("checked", all_ch);
                
                var ti = $(this).index(".hide_show");
                
                $('#mine tr').each(function(){
                    if(hide){
                        $('td:eq(' + ti + ')',this).hide(100);
                        $('th:eq(' + ti + ')',this).hide(100);
                    }else{
                        $('td:eq(' + ti + ')',this).show(100);
                        $('th:eq(' + ti + ')',this).show(100);
                    }
                });

            });
            //$('#mine tfoot th').each( function () {
                //var title = $(this).text();
                //$(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //} );

            $('#myInput').keyup( function() {
                    table.draw();
                } );
                $('input.column_filter').on( 'keyup click', function () {
                    filterColumn( $(this).parents('tr').attr('data-column') );
                });
            
            });
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>