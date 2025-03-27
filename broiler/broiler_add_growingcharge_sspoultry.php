<?php
//broiler_add_growingcharge_sspoultry.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['growingcharge_sspoultry'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $href = $uri[1];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['addaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
    }
    if($user_type == "S"){ $acount = 1; } 
    else{
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Farmer TDS' AND `field_function` LIKE 'Deduction' AND `flag` = 1"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $tds_flag = $row['flag']; } if($tds_flag == 1 || $tds_flag == "1"){ $tds_flag = 1; } else{ $tds_flag = 0; }

        $farm_array_list = $farm_code = $farm_name = array();
        $sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0'".$farm_filter." AND `gc_flag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $farm_array_list[$row['farm_code']] = $row['farm_code']; }
        $farm_list = ""; $farm_list = implode("','", $farm_array_list);

        $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: auto;
        }
        .form-control{
            padding-left: 1px;
            padding-right: 1px;
            margin-right: 10px;
            height: 25px;
        }
        td label{
            margin-right: 10px;
        }
        .ring{
        display:none;
        position:fixed;
        top:50%;
        left:50%;
        transform:translate(-50%,-50%);
        width:150px;
        height:150px;
        background:#6A03BA;
        border:none;
        border-radius:50%;
        text-align:center;
        line-height:150px;
        font-family:sans-serif;
        font-size:20px;
        color:white;
        letter-spacing:4px;
        text-transform:uppercase;
        text-shadow:0 0 10px purple;
        /*box-shadow:0 0 20px rgba(0,0,0,.5);*/
        }
        .ring:before{
        content:'';
        position:fixed;
        top:0px;
        left:0px;
        width:100%;
        height:100%;
        border:10px solid transparent;
        border-top:10px solid white;
        border-right:10px solid white;
        border-radius:50%;
        animation:animateC 2s linear infinite;
        }
        @keyframes animateC{
        0%
        {
            transform:rotate(0deg);
        }
        100%
        {
            transform:rotate(360deg);
        }
        }
        @keyframes animate
        {
        0%
        {
            transform:rotate(45deg);
        }
        100%
        {
            transform:rotate(405deg);
        }
        }
    </style>
    </head>
    <body class="m-0 p-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Rearing Charges</h3></div>
                        </div>
                        <div class="p-0 pt-2 card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_growingcharge_sspoultry.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Farm<b style="color:red;">&nbsp;*</b></label>
                                                <select name="farm_code" id="farm_code" class="form-control select2" style="width:160px;" onchange="fetch_schemadetails()">
                                                    <option value="select">select</option>
                                                    <?php foreach($farm_code as $fcode){ ?><option value="<?php echo $fcode; ?>"><?php echo $farm_name[$fcode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Scheme Name<b style="color:red;">&nbsp;*</b></label>
                                                <select name="schema_id" id="schema_id" class="form-control select2" style="width:160px;" onchange="fetch_farmdetails()">
                                                    <option value="select">select</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Branch</label>
							                <input type="text" name="branch_code" id="branch_code" class="form-control" readonly />
                                        </div>
                                        </div>
                                        <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Line</label>
							                <input type="text" name="line_code" id="line_code" class="form-control" readonly />
                                        </div>
                                        </div>
                                        <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Batch</label>
							                <input type="text" name="batch_code" id="batch_code" class="form-control" readonly />
                                        </div>
                                        </div>
                                        <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Supervisor</label>
							                <input type="text" name="supervisor_code" id="supervisor_code" class="form-control" readonly />
                                        </div>
                                        </div>
                                        <div class="col-md-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group" style="color:blue;">
                                            <label>Placement Details</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Placement Date</label>
                                                <input type="text" name="start_date" id="start_date" class="form-control" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>GC Date</label>
                                                <input type="text" name="gc_date" id="gc_date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-8">
                                            <table>
                                                <tr style="color:blue;">
                                                    <td colspan="2" style="text-align:center;"><label>Bird Details</label></td>
                                                    <td colspan="2" style="text-align:center;"><label>Performance</label></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Placed Birds</label></td>
                                                    <td><input type="text" name="placed_birds" id="placed_birds" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>1st Week Mortality%</label></td>
                                                    <td><input type="text" name="days7_mort" id="days7_mort" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Mortality</label></td>
                                                    <td><input type="text" name="mortality" id="mortality" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>30 Days Mortality %</label></td>
                                                    <td><input type="text" name="days30_mort" id="days30_mort" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Sold Birds</label></td>
                                                    <td><input type="text" name="sold_birds" id="sold_birds" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>After 30 Days Mortality %</label></td>
                                                    <td><input type="text" name="daysge31_mort" id="daysge31_mort" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Sold Weight</label></td>
                                                    <td><input type="text" name="sold_weight" id="sold_weight" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>Total Mortality %</label></td>
                                                    <td><input type="text" name="total_mort" id="total_mort" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Excess</label></td>
                                                    <td><input type="text" name="excess" id="excess" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>FCR</label></td>
                                                    <td><input type="text" name="fcr" id="fcr" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Shortage</label></td>
                                                    <td><input type="text" name="shortage" id="shortage" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>CFCR</label></td>
                                                    <td><input type="text" name="cfcr" id="cfcr" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Liquidation Date</label></td>
                                                    <td><input type="text" name="liquid_date" id="liquid_date" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>Avg.wt</label></td>
                                                    <td><input type="text" name="avg_wt" id="avg_wt" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Sale Amount</label></td>
                                                    <td><input type="text" name="sale_amount" id="sale_amount" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>Mean Age</label></td>
                                                    <td><input type="text" name="mean_age" id="mean_age" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Sale Rate</label></td>
                                                    <td><input type="text" name="sale_rate" id="sale_rate" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>Day Gain</label></td>
                                                    <td><input type="text" name="day_gain" id="day_gain" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Age</label></td>
                                                    <td><input type="text" name="age" id="age" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>EEF</label></td>
                                                    <td><input type="text" name="eef" id="eef" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><label>Grade</label></td>
                                                    <td><input type="text" name="grade" id="grade" class="form-control" readonly /></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group" style="color:blue;">
                                            <label>Feed Details</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-8">
                                            <table>
                                                <tr style="color:blue;">
                                                    <td style="text-align:center;"><label></label></td>
                                                    <td style="text-align:center;"><label>KGS</label></td>
                                                    <td style="text-align:center;"><label>BAGS</label></td>
                                                    <td style="text-align:center;"><label>Med/Vaccine Details</label></td>
                                                    <td style="text-align:center;"><label>Quantity</label></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Feed In</label></td>
                                                    <td><input type="text" name="feed_in_kgs" id="feed_in_kgs" class="form-control" readonly /></td>
                                                    <td><input type="text" name="feed_in_bag" id="feed_in_bag" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>Transfer In</label></td>
                                                    <td><input type="text" name="transfer_in" id="transfer_in" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Feed Consumption</label></td>
                                                    <td><input type="text" name="feed_consume_kgs" id="feed_consume_kgs" class="form-control" readonly /></td>
                                                    <td><input type="text" name="feed_consume_bag" id="feed_consume_bag" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>Consumption</label></td>
                                                    <td><input type="text" name="consumption" id="consumption" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Feed Out</label></td>
                                                    <td><input type="text" name="feed_out_kgs" id="feed_out_kgs" class="form-control" readonly /></td>
                                                    <td><input type="text" name="feed_out_bag" id="feed_out_bag" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>Transfer Out</label></td>
                                                    <td><input type="text" name="transfer_out" id="transfer_out" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Feed Balance</label></td>
                                                    <td><input type="text" name="feed_bal_kgs" id="feed_bal_kgs" class="form-control" readonly /></td>
                                                    <td><input type="text" name="feed_bal_bag" id="feed_bal_bag" class="form-control" readonly /></td>
                                                    <td style="text-align:right;"><label>Closing</label></td>
                                                    <td><input type="text" name="closing" id="closing" class="form-control" readonly /></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group" style="color:blue;">
                                            <label>Costing Details</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-7">
                                            <table>
                                                <tr style="color:blue;">
                                                    <td style="text-align:center;"><label></label></td>
                                                    <td style="text-align:center;"><label>Amount</label></td>
                                                    <td style="text-align:center;"><label>Per unit</label></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Chick Cost</label></td>
                                                    <td><input type="text" name="chick_cost_amt" id="chick_cost_amt" class="form-control" readonly /></td>
                                                    <td><input type="text" name="chick_cost_unit" id="chick_cost_unit" class="form-control" readonly /></td>
                                                    <td style="width:60px;visibility:hidden;"><input type="text" name="mgmt_stkin_chick_prc" id="mgmt_stkin_chick_prc" class="form-control" readonly /></td>
                                                    <td style="width:90px;visibility:hidden;"><input type="text" name="mgmt_stkin_chick_amt" id="mgmt_stkin_chick_amt" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Feed Cost</label></td>
                                                    <td><input type="text" name="feed_cost_amt" id="feed_cost_amt" class="form-control" readonly /></td>
                                                    <td><input type="text" name="feed_cost_unit" id="feed_cost_unit" class="form-control" readonly /></td>
                                                    <td style="width:60px;visibility:hidden;"><input type="text" name="mgmt_total_feed_consumed_prc" id="mgmt_total_feed_consumed_prc" class="form-control" readonly /></td>
                                                    <td style="width:90px;visibility:hidden;"><input type="text" name="mgmt_total_feed_consumed_amt" id="mgmt_total_feed_consumed_amt" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Admin Cost</label></td>
                                                    <td><input type="text" name="admin_cost_amt" id="admin_cost_amt" class="form-control" readonly /></td>
                                                    <td><input type="text" name="admin_cost_unit" id="admin_cost_unit" class="form-control" readonly /></td>
                                                    <td style="width:60px;visibility:hidden;"><input type="text" name="mgmt_admin_prc" id="mgmt_admin_prc" class="form-control" readonly /></td>
                                                    <td style="width:90px;visibility:hidden;"><input type="text" name="mgmt_admin_amt" id="mgmt_admin_amt" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Medicine Cost</label></td>
                                                    <td><input type="text" name="medicine_cost_amt" id="medicine_cost_amt" class="form-control" readonly /></td>
                                                    <td><input type="text" name="medicine_cost_unit" id="medicine_cost_unit" class="form-control" readonly /></td>
                                                    <td style="width:60px;visibility:hidden;"><input type="text" name="mgmt_total_medvac_consumed_prc" id="mgmt_total_medvac_consumed_prc" class="form-control" readonly /></td>
                                                    <td style="width:90px;visibility:hidden;"><input type="text" name="mgmt_total_medvac_consumed_amt" id="mgmt_total_medvac_consumed_amt" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Total Cost</label></td>
                                                    <td><input type="text" name="total_cost_amt" id="total_cost_amt" class="form-control" readonly /></td>
                                                    <td><input type="text" name="total_cost_unit" id="total_cost_unit" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Standard Production Cost</label></td>
                                                    <td colspan="2"><input type="text" name="standard_prod_cost" id="standard_prod_cost" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Actual Production Cost</label></td>
                                                    <td colspan="2"><input type="text" name="actual_prod_cost" id="actual_prod_cost" class="form-control" readonly /></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group" style="color:blue;">
                                            <label>Rearing Charges</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-7">
                                            <table>
                                                <tr>
                                                    <td style="text-align:right;"></td>
                                                    <td><label>Rs.</label></td>
                                                    <td><label>Amount</label></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Standard Growing Charges</label></td>
                                                    <td><input type="text" name="standard_gc_prc" id="standard_gc_prc" class="form-control" readonly /></td>
                                                    <td><input type="text" name="standard_gc_amt" id="standard_gc_amt" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Actual Growing Charges</label></td>
                                                    <td><input type="text" name="actual_charge_exp_prc" id="actual_charge_exp_prc" class="form-control" readonly /></td>
                                                    <td><input type="text" name="actual_charge_exp_amt" id="actual_charge_exp_amt" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Growing charges and expenses <br/>paid per kg. of live weight</label></td>
                                                    <td><input type="text" name="grow_charge_exp_prc" id="grow_charge_exp_prc" class="form-control" readonly onkeyup="calculate_gc_amount()"/></td>
                                                    <td><input type="text" name="grow_charge_exp_amt" id="grow_charge_exp_amt" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Sales Incentives</label></td>
                                                    <td><input type="text" name="sales_incentive_prc" id="sales_incentive_prc" class="form-control" readonly /></td>
                                                    <td><input type="text" name="sales_incentive_amt" id="sales_incentive_amt" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Motality Incentives</label></td>
                                                    <td><!--<input type="text" name="mortality_incentive_prc" id="mortality_incentive_prc" class="form-control" />--></td>
                                                    <td><input type="text" name="mortality_incentive_amt" id="mortality_incentive_amt" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>FCR Incentives</label></td>
                                                    <td><input type="text" name="fcr_incentive_prc" id="fcr_incentive_prc" class="form-control" style="visibility:hidden;" /></td>
                                                    <td><input type="text" name="fcr_incentive_amt" id="fcr_incentive_amt" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Summer Incentives</label></td>
                                                    <td><!--<input type="text" name="summer_incentive_prc" id="summer_incentive_prc" class="form-control" />--></td>
                                                    <td><input type="text" name="summer_incentive_amt" id="summer_incentive_amt" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Other Incentives</label></td>
                                                    <td></td>
                                                    <td><input type="text" name="other_incentive" id="other_incentive" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>IFFT Charges</label></td>
                                                    <td></td>
                                                    <td><input type="text" name="ifft_charges" id="ifft_charges" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Total Incentives</label></td>
                                                    <td></td>
                                                    <td><input type="text" name="total_incentives" id="total_incentives" class="form-control" readonly /></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group" style="color:blue;">
                                            <label>Decentives</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-6">
                                            <table>
                                                <tr>
                                                    <td style="text-align:right;"><label>Birds Shortage Amount</label></td>
                                                    <td>
                                                        <div class="row">
                                                        <input type="text" name="birds_shortage_prc" id="birds_shortage_prc" class="form-control" placeholder="Rate" style="margin-left:7px;width:60px;" onkeyup="calculate_shortage_amount()" />
                                                        <input type="text" name="birds_shortage" id="birds_shortage" class="form-control" style="width:120px;"  onkeyup="calculate_amount_pay()" />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>FCR Deduction</label></td>
                                                    <td><input type="text" name="fcr_deduction" id="fcr_deduction" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Mortality Deduction</label></td>
                                                    <td><input type="text" name="mortality_deduction" id="mortality_deduction" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Total Deduction</label></td>
                                                    <td><input type="text" name="total_deduction" id="total_deduction" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Amount Payable</label></td>
                                                    <td><input type="text" name="amount_payable" id="amount_payable" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Farmer sales deduction</label></td>
                                                    <td><input type="text" name="farmer_sale_deduction" id="farmer_sale_deduction" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Farmer Receipt Amount</label></td>
                                                    <td><input type="text" name="farmer_receipt_deduction" id="farmer_receipt_deduction" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Feed Transfer Charges</label></td>
                                                    <td><input type="text" name="feed_transfer_charges" id="feed_transfer_charges" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Vaccinator Charges</label></td>
                                                    <td><input type="text" name="vaccinator_charges" id="vaccinator_charges" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Transportation Charges Addition</label></td>
                                                    <td><input type="text" name="transportation_charges" id="transportation_charges" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Other Deductions</label></td>
                                                    <td><input type="text" name="other_deduction" id="other_deduction" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Total Amount Payable</label></td>
                                                    <td><input type="text" name="total_amount_payable" id="total_amount_payable" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;" id="tds_lbl"><label>Tds@1.00%</label></td>
                                                    <td><input type="text" name="tds_amt" id="tds_amt" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                    <td><input type="text" name="tds_per" id="tds_per" class="form-control" style="width:50px;visibility:hidden;" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Equipment Charges</label></td>
                                                    <td><input type="text" name="equipment_charges" id="equipment_charges" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Advance Deductions</label></td>
                                                    <td><input type="text" name="advance_deduction" id="advance_deduction" class="form-control" onkeyup="calculate_amount_pay()" /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Farmer Payable</label></td>
                                                    <td><input type="text" name="farmer_payable" id="farmer_payable" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Per Bird Cost</label></td>
                                                    <td><input type="text" name="per_bird_price" id="per_bird_price" class="form-control" readonly /></td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><label>Remarks</label></td>
                                                    <td><textarea name="remarks" id="remarks" class="form-control" style="height:25px;"></textarea></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date[]" id="date[0]" class="form-control datepicker" style="width:100px;" value="<?php echo date('d.m.Y'); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="ring">Loading
  <span></span>
</div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_growingcharge_sspoultry.php?ccid='+ccid;
            }
            function checkval(){
                var feed_bal_kgs = document.getElementById("feed_bal_kgs").value;
                var feed_bal_bag = document.getElementById("feed_bal_bag").value;
                if(parseFloat(feed_bal_kgs) > 0 || parseFloat(feed_bal_bag) > 0){
                    alert("Feed Balance need to be empty to close the GC \n Kindly consume or Transfer the Balance Feed from the Batch");
                    l = false;
                }
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
            function fetch_farmdetails(){
                var farm_code = document.getElementById("farm_code").value;
                var schema = document.getElementById("schema_id");
                var schema_id = document.getElementById("schema_id").value;
                var schema_name = schema.options[schema.selectedIndex].text;
                
                if(!farm_code.match("select") && schema_id != "select"){
                    var fetchgc = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_rearingcharge_sspoultry1.php?farm_code="+farm_code+"&schema_id="+schema_id+"&schema_name="+schema_name;
                    //window.open(url);
					var asynchronous = true;
                    document.getElementsByClassName("ring")[0].style.display = "block";
					fetchgc.open(method, url, asynchronous);
					fetchgc.send();
					fetchgc.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var bbal = this.responseText;
							if(bbal == "") {
								alert("Active batch is not available for the selected Farm \n Kindly check and try again");
							}
							else {
								var farm_details = bbal.split("@");
                                document.getElementById("start_date").value = farm_details[0];
                                document.getElementById("placed_birds").value = farm_details[1];
                                document.getElementById("mortality").value = farm_details[2];
                                document.getElementById("sold_birds").value = farm_details[3];
                                document.getElementById("sold_weight").value = farm_details[4];
                                document.getElementById("excess").value = farm_details[5];
                                document.getElementById("shortage").value = farm_details[6];
                                document.getElementById("liquid_date").value = farm_details[7];
                                document.getElementById("gc_date").value = farm_details[7];
                                document.getElementById("sale_amount").value = farm_details[8];
                                document.getElementById("sale_rate").value = farm_details[9];
                                document.getElementById("age").value = farm_details[10];
                                document.getElementById("days7_mort").value = farm_details[11];
                                document.getElementById("days30_mort").value = farm_details[12];
                                document.getElementById("daysge31_mort").value = farm_details[13];
                                document.getElementById("total_mort").value = farm_details[14];
                                document.getElementById("fcr").value = farm_details[15];
                                document.getElementById("cfcr").value = farm_details[16];
                                document.getElementById("avg_wt").value = farm_details[17];
                                document.getElementById("mean_age").value = farm_details[18];
                                document.getElementById("day_gain").value = farm_details[19];
                                document.getElementById("eef").value = farm_details[20];
                                document.getElementById("grade").value = farm_details[21];
                                document.getElementById("feed_in_kgs").value = farm_details[22];
                                document.getElementById("feed_consume_kgs").value = farm_details[23];
                                document.getElementById("feed_out_kgs").value = farm_details[24];
                                document.getElementById("feed_bal_kgs").value = farm_details[25];
                                document.getElementById("feed_in_bag").value = farm_details[26];
                                document.getElementById("feed_consume_bag").value = farm_details[27];
                                document.getElementById("feed_out_bag").value = farm_details[28];
                                document.getElementById("feed_bal_bag").value = farm_details[29];
                                document.getElementById("transfer_in").value = farm_details[30];
                                document.getElementById("consumption").value = farm_details[31];
                                document.getElementById("transfer_out").value = farm_details[32];
                                document.getElementById("closing").value = farm_details[33];
                                document.getElementById("chick_cost_amt").value = farm_details[34];
                                document.getElementById("chick_cost_unit").value = farm_details[35];
                                document.getElementById("feed_cost_amt").value = farm_details[36];
                                document.getElementById("feed_cost_unit").value = farm_details[37];
                                document.getElementById("admin_cost_amt").value = farm_details[38];
                                document.getElementById("admin_cost_unit").value = farm_details[39];
                                document.getElementById("medicine_cost_amt").value = farm_details[40];
                                document.getElementById("medicine_cost_unit").value = farm_details[41];
                                document.getElementById("total_cost_amt").value = farm_details[42];
                                document.getElementById("total_cost_unit").value = farm_details[43];
                                document.getElementById("actual_prod_cost").value = farm_details[44];
                                document.getElementById("standard_gc_prc").value = farm_details[45];
                                document.getElementById("standard_gc_amt").value = farm_details[46];
                                document.getElementById("grow_charge_exp_prc").value = farm_details[47];
                                document.getElementById("grow_charge_exp_amt").value = farm_details[48];
                                document.getElementById("sales_incentive_prc").value = farm_details[49];
                                document.getElementById("sales_incentive_amt").value = farm_details[50];
                                //document.getElementById("mortality_incentive_prc").value = farm_details[51];
                                document.getElementById("mortality_incentive_amt").value = farm_details[52];
                                //document.getElementById("summer_incentive_prc").value = farm_details[53];
                                document.getElementById("summer_incentive_amt").value = farm_details[54];
                                document.getElementById("other_incentive").value = farm_details[55];
                                document.getElementById("ifft_charges").value = farm_details[56];
                                document.getElementById("total_incentives").value = farm_details[57];
                                document.getElementById("birds_shortage").value = farm_details[58];
                                document.getElementById("fcr_deduction").value = farm_details[59];
                                document.getElementById("mortality_deduction").value = farm_details[60];
                                document.getElementById("total_deduction").value = farm_details[61];
                                document.getElementById("amount_payable").value = farm_details[62];
                                document.getElementById("farmer_sale_deduction").value = farm_details[70];
                                document.getElementById("feed_transfer_charges").value = farm_details[59];
                                document.getElementById("vaccinator_charges").value = farm_details[60];
                                document.getElementById("transportation_charges").value = "";
                                document.getElementById("total_amount_payable").value = farm_details[71];
                                document.getElementById("tds_amt").value = farm_details[63];
                                document.getElementById("other_deduction").value = farm_details[64];
                                document.getElementById("farmer_payable").value = farm_details[65];
                                document.getElementById("branch_code").value = farm_details[66];
                                document.getElementById("line_code").value = farm_details[67];
                                document.getElementById("batch_code").value = farm_details[68];
                                document.getElementById("standard_prod_cost").value = farm_details[69];
                                document.getElementById("fcr_incentive_prc").value = farm_details[72];
                                document.getElementById("fcr_incentive_amt").value = farm_details[73];
                                document.getElementById("per_bird_price").value = farm_details[74];
                                document.getElementById("tds_per").value = farm_details[75];
                                document.getElementById("actual_charge_exp_prc").value = farm_details[77];
                                document.getElementById("actual_charge_exp_amt").value = farm_details[78];

                                document.getElementById("mgmt_stkin_chick_prc").value = farm_details[79];
                                document.getElementById("mgmt_stkin_chick_amt").value = farm_details[80];
                                document.getElementById("mgmt_total_feed_consumed_prc").value = farm_details[81];
                                document.getElementById("mgmt_total_feed_consumed_amt").value = farm_details[82];
                                document.getElementById("mgmt_total_medvac_consumed_prc").value = farm_details[83];
                                document.getElementById("mgmt_total_medvac_consumed_amt").value = farm_details[84];
                                document.getElementById("mgmt_admin_prc").value = farm_details[85];
                                document.getElementById("mgmt_admin_amt").value = farm_details[86];
                                document.getElementById("supervisor_code").value = farm_details[87];
                                document.getElementById("farmer_receipt_deduction").value = farm_details[88];

                                document.getElementById("tds_lbl").innerHTML = "TDS@"+farm_details[76].toFixed(2)+"%";
							}
						}
                        document.getElementsByClassName("ring")[0].style.display = "none";
					}
                }
            }
            function calculate_amount_pay(){
                var tds_flag = '<?php echo $tds_flag; ?>';
                var actual_grow_amt = document.getElementById("grow_charge_exp_amt").value;
                var sale_inc_amt = document.getElementById("sales_incentive_amt").value;
                var mort_inc_amt = document.getElementById("mortality_incentive_amt").value;
                var fcr_inc_amt = document.getElementById("fcr_incentive_amt").value;
                var summer_inc_amt = document.getElementById("summer_incentive_amt").value;
                var other_inc_amt = document.getElementById("other_incentive").value;
                var unload_amt = document.getElementById("ifft_charges").value;

                var bshort_ded_amt = document.getElementById("birds_shortage").value;
                var fcr_ded_amt = document.getElementById("fcr_deduction").value;
                var mort_ded_amt = document.getElementById("mortality_deduction").value;
                var fsale_ded_amt = document.getElementById("farmer_sale_deduction").value;
                var frct_ded_amt = document.getElementById("farmer_receipt_deduction").value;
                var feedtr_ded_amt = document.getElementById("feed_transfer_charges").value;
                var vacc_ded_amt = document.getElementById("vaccinator_charges").value;
                var transport_add_amt = document.getElementById("transportation_charges").value;
                var other_ded_amt = document.getElementById("other_deduction").value;
                var advance_ded_amt = document.getElementById("advance_deduction").value;
                var eqichrg_ded_amt = document.getElementById("equipment_charges").value;

                if(actual_grow_amt.length == 0){ actual_grow_amt = 0; }
                if(sale_inc_amt.length == 0){ sale_inc_amt = 0; }
                if(mort_inc_amt.length == 0){ mort_inc_amt = 0; }
                if(fcr_inc_amt.length == 0){ fcr_inc_amt = 0; }
                if(summer_inc_amt.length == 0){ summer_inc_amt = 0; }
                if(other_inc_amt.length == 0){ other_inc_amt = 0; }
                if(unload_amt.length == 0){ unload_amt = 0; }

                if(bshort_ded_amt.length == 0){ bshort_ded_amt = 0; }
                if(fcr_ded_amt.length == 0){ fcr_ded_amt = 0; }
                if(mort_ded_amt.length == 0){ mort_ded_amt = 0; }
                if(fsale_ded_amt.length == 0){ fsale_ded_amt = 0; }
                if(frct_ded_amt.length == 0){ frct_ded_amt = 0; }
                if(feedtr_ded_amt.length == 0){ feedtr_ded_amt = 0; }
                if(vacc_ded_amt.length == 0){ vacc_ded_amt = 0; }
                if(transport_add_amt.length == 0){ transport_add_amt = 0; }
                if(other_ded_amt.length == 0){ other_ded_amt = 0; }
                if(advance_ded_amt.length == 0){ advance_ded_amt = 0; }
                if(eqichrg_ded_amt.length == 0){ eqichrg_ded_amt = 0; }

                var total_inc_amt = total_ded_amt = amt_pay = famt_pay = farmer_pay = 0;

                total_inc_amt = parseFloat(actual_grow_amt) + parseFloat(sale_inc_amt) + parseFloat(mort_inc_amt) + parseFloat(fcr_inc_amt) + parseFloat(summer_inc_amt) + parseFloat(other_inc_amt) + parseFloat(unload_amt);
                document.getElementById("total_incentives").value = total_inc_amt.toFixed(0);

                total_ded_amt = parseFloat(bshort_ded_amt) + parseFloat(fcr_ded_amt) + parseFloat(mort_ded_amt);
                document.getElementById("total_deduction").value = total_ded_amt.toFixed(0);
                
                amt_pay = parseFloat(total_inc_amt) - parseFloat(total_ded_amt);
                document.getElementById("amount_payable").value = amt_pay.toFixed(0);
                
                famt_pay = (parseFloat(amt_pay) + parseFloat(transport_add_amt) + parseFloat(frct_ded_amt)) - (parseFloat(fsale_ded_amt) + parseFloat(feedtr_ded_amt) + parseFloat(vacc_ded_amt) + parseFloat(other_ded_amt));
                document.getElementById("total_amount_payable").value = famt_pay.toFixed(0);
                
                if(tds_flag == 1 || tds_flag == "1"){
                    var tds_per = document.getElementById("tds_per").value;
                    if(tds_per == ""){ tds_per = 0; }
                    else{

                    }
                    var tds_amt = (parseFloat(amt_pay) * parseFloat(tds_per)).toFixed(0);
                }
                else{
                    var tds_amt = 0;
                }
                document.getElementById("tds_amt").value = parseFloat(tds_amt).toFixed(0);

                farmer_pay = parseFloat(famt_pay) - parseFloat(tds_amt) - parseFloat(advance_ded_amt) - parseFloat(eqichrg_ded_amt);
                var sold_birds = per_bird_prc = 0;
                var sold_birds = document.getElementById("sold_birds").value;
                if(parseFloat(sold_birds) == 0 || sold_birds == ""){ sold_birds = per_bird_prc = 0; }
                else{
                    per_bird_prc = parseFloat(famt_pay) / parseFloat(sold_birds);
                }
                document.getElementById("farmer_payable").value = farmer_pay.toFixed(0);
                document.getElementById("per_bird_price").value = per_bird_prc.toFixed(2);
            }
            function fetch_schemadetails(){
                var farm_code = document.getElementById("farm_code").value;
                removeAllOptions(document.getElementById("schema_id"));
                var branch_code = "";
                if(farm_code != "select"){    
                    var fetchgc = new XMLHttpRequest();
				    var method = "GET";
					var url = "broiler_fetch_schemadetails.php?farm_code="+farm_code;
                    //window.open(url);
					var asynchronous = true;
					fetchgc.open(method, url, asynchronous);
					fetchgc.send();
					fetchgc.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var bbal = this.responseText;
                            if(bbal != ""){
                                $('#schema_id').append(bbal);
                            }
                        }
                    }
                }
            }
            function calculate_gc_amount(){
                var grow_charge_exp_prc = document.getElementById("grow_charge_exp_prc").value;
                var sold_weight = document.getElementById("sold_weight").value;
                var grow_charge_exp_amt = ((parseFloat(sold_weight,2) * parseFloat(grow_charge_exp_prc,2)));
                document.getElementById("grow_charge_exp_amt").value = grow_charge_exp_amt.toFixed(2);
                calculate_amount_pay();
            }
            function calculate_shortage_amount(){
                var birds_shortage_prc = document.getElementById("birds_shortage_prc").value;
                var shortage = document.getElementById("shortage").value;
                var avg_wt = document.getElementById("avg_wt").value;
                var shortage_amt = ((parseFloat(shortage,2) * parseFloat(avg_wt,2)) * parseFloat(birds_shortage_prc,2));
                document.getElementById("birds_shortage").value = shortage_amt.toFixed(2);
                calculate_amount_pay();
            }
            calculate_amount_pay();
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more information"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more information";
}
?>