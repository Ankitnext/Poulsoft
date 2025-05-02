<?php
//broiler_add_rearingcharge.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['rearingcharge'];
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
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'GC-AvgWeight' AND `field_function` LIKE 'Avg Body Weight Incentive' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); if($count > 0){ $gcAvgWt_flag = 1; } else{ $gcAvgWt_flag = 0; }
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Winter Incentive' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $winter_incv_flag = mysqli_num_rows($query);
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Shortage Max Allowed Birds' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $shortage_maxbirds_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'FCR Rearing Charge Master' AND `field_function` LIKE 'FCR Based GC' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $fcr_gc_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Schema selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $schema_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'COP on Avg.BodyWeight AND FCR' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $copabwfcr_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'SI on between Avg. body weight std' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $sibabws_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Loyalty Incentive' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $loyalinc_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Summer Incentive on Body Weight' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $smrincbdw_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'COP based Incentive and Decentive calculations' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $copbincdec_flag = mysqli_num_rows($query);
                
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Standard GC based on between Avg.Weights' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $stdgconavgwt_flag = mysqli_num_rows($query);
                
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Mortality Incentives Based on grades' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $mibong_flag = mysqli_num_rows($query);
                
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Maize Cost Field' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $mcf_flag = mysqli_num_rows($query);
        
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Sales Incentives Based on grades' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $sibong_flag = mysqli_num_rows($query); if((int)$sibong_flag == 1){ $sibong_view =  'style="visibility:visible;"'; } else{ $sibong_view =  'style="visibility:hidden;"'; }
        
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Seasonal Incentive' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $seasoninc_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'CFCR Based GC' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $cfcr_gc_flag = mysqli_num_rows($query);


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
            font-size: 13px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Rearing Charge</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_rearingcharge.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Region<b style="color:red;">&nbsp;*</b></label>
                                                <select name="region_code" id="region_code" class="form-control select2" style="width: 100%;" onchange="fetch_branch_details(this.id)" onfocus="focus_selection(this.id);">
                                                    <option value="select">select</option>
                                                    <?php
                                                    $sql = "SELECT * FROM `location_region` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Branch<b style="color:red;">&nbsp;*</b></label>
							                    <select name="branch_code" id="branch_code" class="form-control select2" style="width: 100%;"><option value="all">-All-</option></select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>From Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="from_date" id="from_date" class="form-control rc_datepicker" value="<?php echo date('d.m.Y'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>To Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="to_date" id="to_date" class="form-control rc_datepicker" value="<?php echo date('d.m.Y'); ?>">
                                            </div>
                                        </div>
                                        <?php if($schema_flag == 1){ ?>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Schema Name<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="schema_name" id="schema_name" class="form-control" value="" onkeyup="validatename(this.id);">
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-1"></div>
                                    </div>
                                    <div class="col-md-12" id="std_gc">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" align="center"><label style="font-weight:bold;color:red;">Standard Growing Charge</label></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="chick_cost">Chick Cost</label></div>
                                            <div class="col-md-1"><input type="text" id="chick_cost" name="chick_cost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="feed_cost">Feed Cost</label></div>
                                            <div class="col-md-1"><input type="text" id="feed_cost" name="feed_cost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <?php if((int)$mcf_flag == 1){ ?>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="maize_cost">Maize Cost</label></div>
                                            <div class="col-md-1"><input type="text" id="maize_cost" name="maize_cost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <?php } ?>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="medicine_cost">Medicine Cost</label></div>
                                            <div class="col-md-1" style="width:30px;"><label for="medicine_cost1">Actual</label><input type="radio" id="medicine_cost1" name="medicine_cost" class="form-control1" value="A" style="width:30px;" onclick="check_costing(this.id)" checked /></div>
                                            <div class="col-md-1" style="width:30px;"><label for="medicine_cost2">Master</label><input type="radio" id="medicine_cost2" name="medicine_cost" class="form-control1" value="M" style="width:30px;" onclick="check_costing(this.id)" /></div>
                                            <div class="col-md-1" style="width:30px;"><label for="medicine_cost3">Fixed </label><input type="radio" id="medicine_cost3" name="medicine_cost" class="form-control1" value="F" style="width:30px;" onclick="check_costing(this.id)" /></div>
                                            <div class="col-md-1" id="med_value" style="width:90px;visibility:hidden;"><label for="fixed_cost">Rate</label><input type="text" id="fixed_cost" name="fixed_cost" class="form-control" style="width:90px;" /></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="admin_cost">Farmer Admin Cost</label></div>
                                            <div class="col-md-1"><input type="text" id="admin_cost" name="admin_cost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="admin_cost">Management Admin Cost</label></div>
                                            <div class="col-md-1"><input type="text" id="mgmt_admin_cost" name="mgmt_admin_cost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="standard_prod_cost">Std Production Cost</label></div>
                                            <div class="col-md-1"><input type="text" id="standard_prod_cost" name="standard_prod_cost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="standard_cost">Standard GC Cost</label></div>
                                            <div class="col-md-1"><input type="text" id="standard_cost" name="standard_cost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-1" <?php if($gcAvgWt_flag == 0){ echo "style='visibility:hidden;'"; } ?>><label for="avgwt_upto">AvgWt Upto</label></div>
                                            <div class="col-md-1" <?php if($gcAvgWt_flag == 0){ echo "style='visibility:hidden;'"; } ?>><input type="text" id="avgwt_upto" name="avgwt_upto" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-1" <?php if($gcAvgWt_flag == 0){ echo "style='visibility:hidden;'"; } ?>><label for="avgwt_gccost">GC Cost</label></div>
                                            <div class="col-md-1" <?php if($gcAvgWt_flag == 0){ echo "style='visibility:hidden;'"; } ?>><input type="text" id="avgwt_gccost" name="avgwt_gccost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="minimum_cost">Minimum GC Cost</label></div>
                                            <div class="col-md-1"><input type="text" id="minimum_cost" name="minimum_cost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="standard_fcr">Standard FCR</label></div>
                                            <div class="col-md-1"><input type="text" id="standard_fcr" name="standard_fcr" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="standard_mortality">Standard Mortality</label></div>
                                            <div class="col-md-1"><input type="text" id="standard_mortality" name="standard_mortality" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right"><label for="gcm_unl_charge">Unloading Charges</label></div>
                                            <div class="col-md-1"><input type="text" id="gcm_unl_charge" name="gcm_unl_charge" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <?php
                                    if($stdgconavgwt_flag == 1){
                                    ?>
                                    <div class="col-md-12" id="inc_val">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" align="center"><label style="font-weight:bold;color:red;">Standard GC Costing</label></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row justify-content-center align-items-center">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;">Sl. No.</th>
                                                        <th style="text-align:center;" colspan="2">Avg. Body Weight</th>
                                                        <th style="text-align:center;">STD. GC Cost</th>
                                                        <th style="width:110px;"></th>
                                                    </tr>
                                                    <tr>
                                                        <th></th>
                                                        <th>From</th>
                                                        <th>To</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="sgc_row_body">
                                                    <tr>
                                                        <td style="text-align:center;width:70px;">1</td>
                                                        <td><input type="text" id="sgc_from_avgwt[]" name="sgc_from_avgwt[0]" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>
                                                        <td><input type="text" id="sgc_to_avgwt[]" name="sgc_to_avgwt[0]" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>
                                                        <td><input type="text" id="sgc_std_cost[]" name="sgc_std_cost[0]" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>
                                                        <td id="sgc_action[0]"><a href="javascript:void(0);" id="add_sgc_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if($copabwfcr_flag == 1){
                                    ?>
                                    <div class="col-md-12" id="inc_val">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" align="center"><label style="font-weight:bold;color:red;">COP Standards</label></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row justify-content-center align-items-center">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;">Sl. No.</th>
                                                        <th style="text-align:center;" colspan="2">Avg. Body Weight</th>
                                                        <th style="text-align:center;" colspan="2">FCR</th>
                                                        <th style="text-align:center;">COP</th>
                                                        <th style="width:110px;"></th>
                                                    </tr>
                                                    <tr>
                                                        <th></th>
                                                        <th>From</th>
                                                        <th>To</th>
                                                        <th>From</th>
                                                        <th>To</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cop_row_body">
                                                    <tr>
                                                        <td style="text-align:center;width:70px;">1</td>
                                                        <td><input type="text" id="copabw_from_val[]" name="copabw_from_val[0]" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>
                                                        <td><input type="text" id="copabw_to_val[]" name="copabw_to_val[0]" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>
                                                        <td><input type="text" id="copfcr_from_val[]" name="copfcr_from_val[0]" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>
                                                        <td><input type="text" id="copfcr_to_val[]" name="copfcr_to_val[0]" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>
                                                        <td><input type="text" id="copfcr_std_val[]" name="copfcr_std_val[0]" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>
                                                        <td id="cop_action[0]"><a href="javascript:void(0);" id="add_cop_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if($fcr_gc_flag == 1){
                                    ?>
                                    <div class="col-md-12" id="inc_val">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" align="center"><label style="font-weight:bold;color:red;">FCR Standards</label></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>From FCR Cost</label></div>
                                            <div class="col-md-2"><label>To FCR Cost</label></div>
                                            <div class="col-md-2"><label>Std. Cost</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="fcrs_from_val[]" name="fcrs_from_val[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="fcrs_to_val[]" name="fcrs_to_val[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="std_rates[]" name="std_rates[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="fcrs_cost_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_fcrs_cost_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="fcrs_cost_row_body"></div>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if($cfcr_gc_flag == 1){
                                    ?>
                                    <div class="col-md-12" id="inc_val">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" align="center"><label style="font-weight:bold;color:red;">CFCR Standards</label></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2"><label>From CFCR</label></div>
                                            <div class="col-md-2"><label>To CFCR</label></div>
                                            <div class="col-md-1"><label>GC Cost</label></div>
                                            <div class="col-md-2"><label>From Weight</label></div>
                                            <div class="col-md-2"><label>To Weight</label></div>
                                            <div class="col-md-2"><label>Small bird Cost</label></div>
                                            <div class="col-md-1"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="cfcrs_from_val[]" name="cfcrs_from_val[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="cfcrs_to_val[]" name="cfcrs_to_val[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="text" id="ngc_rate[]" name="ngc_rate[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="cfcr_from_wht[]" name="cfcr_from_wht[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="cfcr_to_wht[]" name="cfcr_to_wht[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="sbgc_rate[]" name="sbgc_rate[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="cfcrs_cost_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_cfcrs_cost_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="cfcrs_cost_row_body"></div>
                                    <?php
                                    }
                                    ?>
                                    <div class="col-md-12" id="inc_val">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" align="center"><label style="font-weight:bold;color:red;">Incentives</label></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Production Cost Incentives</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <?php
                                            if((int)$copbincdec_flag == 1){
                                            ?>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-1"><label>Std. COP</label></div>
                                            <?php
                                            }
                                            else{
                                            ?>
                                            <div class="col-md-3"></div>
                                            <?php
                                            }
                                            ?>
                                            <div class="col-md-2"><label>From Production Cost</label></div>
                                            <div class="col-md-2"><label>To Production Cost</label></div>
                                            <div class="col-md-2"><label>Rate %</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <?php
                                            if((int)$copbincdec_flag == 1){
                                            ?>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-1"><div class="form-group"><input type="text" id="prod_inc_sdtcop[]" name="prod_inc_sdtcop[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <?php
                                            }
                                            else{
                                            ?>
                                            <div class="col-md-3"></div>
                                            <?php
                                            }
                                            ?>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="prod_from_inc[]" name="prod_from_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="prod_to_inc[]" name="prod_to_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="rate_inc[]" name="rate_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="prod_inc_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_prod_inc_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="prod_inc_row_body"></div>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Sales Incentives</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-2" <?php echo $sibong_view; ?>><label>Incentives Grade</label></div>
                                            <div class="col-md-2"><label>Sale Rate From</label></div>
                                            <div class="col-md-2"><label>Sale Rate To</label></div>
                                            <div class="col-md-2"><label>Sales Incentives</label></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-2" <?php echo $sibong_view; ?>><div class="form-group"><input type="text" id="sales_inc_grade[]" name="sales_inc_grade[0]" class="form-control" onkeyup="validatename(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="sales_from_inc[]" name="sales_from_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="sales_to_inc[]" name="sales_to_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="sales_rate_inc[]" name="sales_rate_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="sales_inc_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_sales_inc_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="sales_inc_row_body"></div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-3" align="right"><b>Maximum Prod. Cost</b></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="max_prod_cost" name="max_prod_cost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2" align="right"><b>Maximum Rate Incentive</b></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="sales_max_rate" name="sales_max_rate" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>

                                    <?php
                                    if($sibabws_flag == 1){
                                    ?>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Sale Incentives Avg. Weights</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>From Avg. Wt.</label></div>
                                            <div class="col-md-2"><label>To Avg. Wt.</label></div>
                                            <div class="col-md-2"><label>Calculate Weight Value</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="si_from_avgwt[]" name="si_from_avgwt[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="si_to_avgwt[]" name="si_to_avgwt[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="avgwt_value[]" name="avgwt_value[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="si_avgwt_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_si_avgwt_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="si_avgwt_row_body"></div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-3" align="right"><b>Maximum Prod. Cost</b></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="awti_max_prod_cost" name="awti_max_prod_cost" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2" align="right" style="visibility:hidden;"><b>Maximum Rate Incentive</b></div>
                                            <div class="col-md-2" style="visibility:hidden;"><div class="form-group"><input type="text" id="awti_sales_max_rate" name="awti_sales_max_rate" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Mortality Incentives</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <?php if($mibong_flag == 1){ ?>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="mi_grades">Mortality Incentive Grades</label>
                                                    <select name="mi_grades[]" id="mi_grades" class="form-control select2" multiple="multiple" data-placeholder="Select Grade" data-dropdown-css-class="select2-purple" onfocus="focus_selection(this.id);">
                                                        <option value="A">A</option>
                                                        <option value="B">B</option>
                                                        <option value="C">C</option>
                                                        <option value="D">D</option>
                                                        <option value="E">E</option>
                                                        <option value="F">F</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>From Mortality %</label></div>
                                            <div class="col-md-2"><label>To Mortality %</label></div>
                                            <div class="col-md-2"><label>Incentive Value</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="mort_from_inc[]" name="mort_from_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="mort_to_inc[]" name="mort_to_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="mort_rate_inc[]" name="mort_rate_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="mort_inc_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_mort_inc_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="mort_inc_row_body"></div>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">FCR Incentives</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>CFCR Limit</label></div>
                                            <div class="col-md-2"><label>Body Weight</label></div>
                                            <div class="col-md-2"><label>Incentive Value</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="fcr_limit_inc[]" name="fcr_limit_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="body_weight_inc[]" name="body_weight_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="fcr_rate_inc[]" name="fcr_rate_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="fcr_inc_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_fcr_inc_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="fcr_inc_row_body"></div>
                                    <?php if($winter_incv_flag == 1){ ?>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Winter Incentives</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>Min. Production Cost</label><input type="text" name="wi_min_prod_cost" id="wi_min_prod_cost" class="form-control" /></div>
                                            <div class="col-md-2"><label>MAx. Production Cost</label><input type="text" name="wi_max_prod_cost" id="wi_max_prod_cost" class="form-control" /></div>
                                            <div class="col-md-2"><label>Incentive On</label><select name="wi_incentive_on" id="wi_incentive_on" class="form-control select2"><option value="placed_birds">Placed Birds</option><option value="sold_birds" selected >Sold Birds</option><option value="sold_weight">Sold Weight</option></select></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>From Production Cost</label></div>
                                            <div class="col-md-2"><label>To Production Cost</label></div>
                                            <div class="col-md-2"><label>Incentive Rate</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="wi_from_prod_cost[]" name="wi_from_prod_cost[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="wi_to_prod_cost[]" name="wi_to_prod_cost[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="wi_rate_inc[]" name="wi_rate_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="wi_inc_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_wi_inc_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="wi_inc_row_body"></div>
                                    <?php } ?>
                                    <?php $smr_incv_flag = 1; if($smr_incv_flag == 1 && $smrincbdw_flag == 0){ ?>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Summer Incentives</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>Min. Production Cost</label><input type="text" name="si_min_prod_cost" id="si_min_prod_cost" class="form-control" /></div>
                                            <div class="col-md-2"><label>MAx. Production Cost</label><input type="text" name="si_max_prod_cost" id="si_max_prod_cost" class="form-control" /></div>
                                            <div class="col-md-2"><label>Incentive On</label><select name="si_incentive_on" id="si_incentive_on" class="form-control select2"><option value="placed_birds">Placed Birds</option><option value="sold_birds" selected >Sold Birds</option><option value="sold_weight">Sold Weight</option></select></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>From Production Cost</label></div>
                                            <div class="col-md-2"><label>To Production Cost</label></div>
                                            <div class="col-md-2"><label>Incentive Rate</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="si_from_prod_cost[]" name="si_from_prod_cost[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="si_to_prod_cost[]" name="si_to_prod_cost[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="si_rate_inc[]" name="si_rate_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="si_inc_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_si_inc_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="si_inc_row_body"></div>
                                    <?php } ?>

                                    <?php if($smrincbdw_flag == 1){ ?>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Summer Incentives</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-2">
                                                <label>Grade to Consider</label>
                                                <select name="smri_grades_to_consider[]" id="smri_grades_to_consider" class="form-control select2" multiple="multiple" data-placeholder="Select Grade" data-dropdown-css-class="select2-purple" onfocus="focus_selection(this.id);">
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                    <option value="D">D</option>
                                                    <option value="E">E</option>
                                                    <option value="F">F</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Incentive On</label>
                                                <select name="smri_incentive_on" id="smri_incentive_on" class="form-control select2">
                                                    <option value="placed_birds">Placed Birds</option>
                                                    <option value="sold_birds">Sold Birds</option>
                                                    <option value="sold_weight" selected>Sold Weight</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>From Avg. Body Weight</label></div>
                                            <div class="col-md-2"><label>To Avg. Body Weight</label></div>
                                            <div class="col-md-2"><label>Incentive Rate</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="smri_from_avgbd_wt[]" name="smri_from_avgbd_wt[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="smri_to_avgbd_wt[]" name="smri_to_avgbd_wt[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="smri_rate_inc[]" name="smri_rate_inc[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="smri_inc_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_smri_inc_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="smri_inc_row_body"></div>
                                    <?php } ?>

                                    <?php if($loyalinc_flag == 1){ ?>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Loyalty Incentives</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-2"><label>No.of Batches</label><input type="text" name="nof_old_batches" id="nof_old_batches" class="form-control" /></div>
                                            <div class="col-md-2">
                                                <label>Grade to Consider</label>
                                                <select name="loyalty_grades_to_consider[]" id="loyalty_grades_to_consider" class="form-control select2" multiple="multiple" data-placeholder="Select Grade" data-dropdown-css-class="select2-purple" onfocus="focus_selection(this.id);">
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                    <option value="D">D</option>
                                                    <option value="E">E</option>
                                                    <option value="F">F</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Incentive On</label>
                                                <select name="loyalty_incentive_on" id="loyalty_incentive_on" class="form-control select2">
                                                    <option value="placed_birds">Placed Birds</option>
                                                    <option value="sold_birds">Sold Birds</option>
                                                    <option value="sold_weight" selected>Sold Weight</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Incentive Rate</label>
                                                    <input type="text" id="loyalty_inc_rate" name="loyalty_inc_rate" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" />
                                                </div>
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <?php if($seasoninc_flag == 1){ ?>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Seasonal Incentive</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-2"><label>Max. Prod.Cost</label><input type="text" name="season_max_prod_cost" id="season_max_prod_cost" class="form-control" /></div>
                                            <div class="col-md-2">
                                                <label>Grade to Consider</label>
                                                <select name="season_grades_to_consider[]" id="season_grades_to_consider[]" class="form-control select2" multiple="multiple" data-placeholder="Select Grade" data-dropdown-css-class="select2-purple" onfocus="focus_selection(this.id);">
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                    <option value="D">D</option>
                                                    <option value="E">E</option>
                                                    <option value="F">F</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Incentive On</label>
                                                <select name="season_incentive_on" id="season_incentive_on" class="form-control select2">
                                                    <option value="placed_birds">Placed Birds</option>
                                                    <option value="sold_birds">Sold Birds</option>
                                                    <option value="sold_weight" selected>Sold Weight</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Incentive Rate</label>
                                                    <input type="text" id="season_inc_rate" name="season_inc_rate" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" />
                                                </div>
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="col-md-12" id="dec_val">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" align="center"><label style="font-weight:bold;color:red;">Decentives or Penalty</label></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Production Cost Decentives</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <?php
                                            if((int)$copbincdec_flag == 1){
                                            ?>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-1"><label>Std. COP</label></div>
                                            <?php
                                            }
                                            else{
                                            ?>
                                            <div class="col-md-3"></div>
                                            <?php
                                            }
                                            ?>
                                            <div class="col-md-2"><label>From Production Cost</label></div>
                                            <div class="col-md-2"><label>To Production Cost</label></div>
                                            <div class="col-md-2"><label>Rate %</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <?php
                                            if((int)$copbincdec_flag == 1){
                                            ?>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-1"><div class="form-group"><input type="text" id="prod_dec_sdtcop[]" name="prod_dec_sdtcop[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <?php
                                            }
                                            else{
                                            ?>
                                            <div class="col-md-3"></div>
                                            <?php
                                            }
                                            ?>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="prod_from_dec[]" name="prod_from_dec[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="prod_to_dec[]" name="prod_to_dec[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="prod_rate_dec[]" name="prod_rate_dec[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="prod_dec_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_prod_dec_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="prod_dec_row_body"></div>

                                    
                                    
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Mortality Decentives</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><label for="week1_limit">1st Week Mortality Exceeds</label><input type="text" id="week1_limit" name="week1_limit" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><label for="week1_above">Overall Above</label><input type="text" id="week1_above" name="week1_above" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><label for="week1_rate">1st Week Mortality Exceeds</label><input type="text" id="week1_rate" name="week1_rate" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-3"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>From Mortality %</label></div>
                                            <div class="col-md-2"><label>To Mortality %</label></div>
                                            <div class="col-md-2"><label>Decentive Value</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="mort_from_dec[]" name="mort_from_dec[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="mort_to_dec[]" name="mort_to_dec[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="mort_rate_dec[]" name="mort_rate_dec[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="mort_dec_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_mort_dec_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="mort_dec_row_body"></div>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">Shortage</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-2" align="center"><label>Std Production Cost<b style="color:red;">&nbsp;*</b></label><input type="radio" name="short_flag" id="short_flag1" class="form-control" value="standard_production_cost" style="transform: scale(.7);"></div>
                                            <div class="col-md-2" align="center"><label>Production Cost<b style="color:red;">&nbsp;*</b></label><input type="radio" name="short_flag" id="short_flag2" class="form-control" value="production_cost" style="transform: scale(.7);"></div>
                                            <div class="col-md-2" align="center"><label>Avg. Sale Rate<b style="color:red;">&nbsp;*</b></label><input type="radio" name="short_flag" id="short_flag3" class="form-control" value="sale_rate" style="transform: scale(.7);"></div>
                                            <div class="col-md-2" align="center"><label>Max. Sale Rate<b style="color:red;">&nbsp;*</b></label><input type="radio" name="short_flag" id="short_flag4" class="form-control" value="max_sale_rate" style="transform: scale(.7);"></div>
                                            <div class="col-md-2" align="center"><label>Which is Higher<b style="color:red;">&nbsp;*</b></label><input type="radio" name="short_flag" id="short_flag5" class="form-control" value="which_is_high" style="transform: scale(.7);" checked ></div>
                                            <?php if($shortage_maxbirds_flag == 1){ ?> <div class="col-md-2" align="center"><label>Max. Allowed Birds<b style="color:red;">&nbsp;*</b></label><input type="text" name="shortage_allowed_maxbirds" id="shortage_allowed_maxbirds" class="form-control" style="width:90px;" /></div>
                                            <?php } else{ ?> <div class="col-md-2"></div><?php } ?>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-4"><label style="font-weight:bold;color:blue;">FCR Recovery</label></div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>CFCR Limit</label></div>
                                            <div class="col-md-2"><label>Production Limit</label></div>
                                            <div class="col-md-2"><label>Recovery Rate</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="fcr_limit_dec[]" name="fcr_limit_dec[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="prod_limit_dec[]" name="prod_limit_dec[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="fcr_rate_dec[]" name="fcr_rate_dec[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-1" id="fcr_dec_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_fcr_dec_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="fcr_dec_row_body"></div>

                                    
                                    <div class="col-md-12" id="fmr_cls">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-4" align="center"><label style="font-weight:bold;color:red;">Farmer Classifications</label></div>
                                            <div class="col-md-4"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><label>Production Cost From</label></div>
                                            <div class="col-md-2"><label>Production To</label></div>
                                            <div class="col-md-2"><label>Grade</label></div>
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3"></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="prod_from_classify[]" name="prod_from_classify[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="prod_to_classify[]" name="prod_to_classify[0]" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>
                                            <div class="col-md-2"><div class="form-group"><input type="text" id="grade_classify[]" name="grade_classify[0]" class="form-control" placeholder="A-Z" onkeyup="validatename(this.id)" /></div></div>
                                            <div class="col-md-1" id="fclassify_action[0]"><div class="form-group"><a href="javascript:void(0);" id="add_fclassify_row[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="fclassify_row_body"></div>

                                    <div class="col-12" style="visibility:hidden;">
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="sgc_incr" name="sgc_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="cop_incr" name="cop_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="fcrs_cost_incr" name="fcrs_cost_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="cfcrs_cost_incr" name="cfcrs_cost_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="prod_inc_incr" name="prod_inc_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="sales_inc_incr" name="sales_inc_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="mort_inc_incr" name="mort_inc_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="si_avgwt_incr" name="si_avgwt_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="fcr_inc_incr" name="fcr_inc_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="wi_inc_incr" name="wi_inc_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="si_inc_incr" name="si_inc_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="smri_inc_incr" name="smri_inc_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="prod_dec_incr" name="prod_dec_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="mort_dec_incr" name="mort_dec_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="fcr_dec_incr" name="fcr_dec_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-1"><div class="form-group"><input type="number" id="fclassify_incr" name="fclassify_incr" class="form-control" value="0" /></div></div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:20px;">
                                            <label for="">EB</label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
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
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_rearingcharge.php?ccid='+ccid;
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var field_type = b[0]; var html = '';
                var fcr_gc_flag = '<?php echo $fcr_gc_flag; ?>';
                var cfcr_gc_flag = '<?php echo $cfcr_gc_flag; ?>';
                var copabwfcr_flag = '<?php echo $copabwfcr_flag; ?>';
                var stdgconavgwt_flag = '<?php echo $stdgconavgwt_flag; ?>';
                var sibabws_flag = '<?php echo $sibabws_flag; ?>';
                var smrincbdw_flag = '<?php echo $smrincbdw_flag; ?>';
                if(fcr_gc_flag == 1 && field_type.match("add_fcrs_cost_row")){
                    document.getElementById("fcrs_cost_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("fcrs_cost_incr").value = d;
                    
                    html+= '<div class="row" id="fcrs_cost_row_no['+d+']">';
                    html+= '<div class="col-md-3"></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="fcrs_from_val[]" id="fcrs_from_val['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="fcrs_to_val[]" id="fcrs_to_val['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="std_rates[]" id="std_rates['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="fcrs_cost_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_fcrs_cost_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_fcrs_cost_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#fcrs_cost_row_body').append(html); $('.select2').select2();
                }
                else if(cfcr_gc_flag == 1 && field_type.match("add_cfcrs_cost_row")){
                    document.getElementById("cfcrs_cost_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("cfcrs_cost_incr").value = d;
                    
                    html+= '<div class="row" id="cfcrs_cost_row_no['+d+']">';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="cfcrs_from_val[]" id="cfcrs_from_val['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="cfcrs_to_val[]" id="cfcrs_to_val['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-1"><div class="form-group"><input type="text" name="ngc_rate[]" id="ngc_rate['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="cfcr_from_wht[]" id="cfcr_from_wht['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="cfcr_to_wht[]" id="cfcr_to_wht['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="sbgc_rate[]" id="sbgc_rate['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-1" id="cfcrs_cost_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_cfcrs_cost_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_cfcrs_cost_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '</div>';

                    $('#cfcrs_cost_row_body').append(html); $('.select2').select2();
                }
                else if(copabwfcr_flag == 1 && field_type.match("add_cop_row")){
                    document.getElementById("cop_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("cop_incr").value = d;
                    var slno = 0; slno = d + 1;
                    html += '<tr id="cop_row_no['+d+']">';
                    html += '<td style="text-align:center;">'+slno+'</td>';
                    html += '<td><input type="text" name="copabw_from_val[]" id="copabw_from_val['+d+']" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>';
                    html += '<td><input type="text" name="copabw_to_val[]" id="copabw_to_val['+d+']" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>';
                    html += '<td><input type="text" name="copfcr_from_val[]" id="copfcr_from_val['+d+']" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>';
                    html += '<td><input type="text" name="copfcr_to_val[]" id="copfcr_to_val['+d+']" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>';
                    html += '<td><input type="text" name="copfcr_std_val[]" id="copfcr_std_val['+d+']" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>';
                    html += '<td id="cop_action['+d+']"><a href="javascript:void(0);" id="add_cop_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_cop_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';

                    $('#cop_row_body').append(html);
                }
                else if(stdgconavgwt_flag == 1 && field_type.match("add_sgc_row")){
                    document.getElementById("sgc_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("sgc_incr").value = d;
                    var slno = 0; slno = d + 1;
                    html += '<tr id="sgc_row_no['+d+']">';
                    html += '<td style="text-align:center;">'+slno+'</td>';
                    html += '<td><input type="text" name="sgc_from_avgwt[]" id="sgc_from_avgwt['+d+']" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>';
                    html += '<td><input type="text" name="sgc_to_avgwt[]" id="sgc_to_avgwt['+d+']" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>';
                    html += '<td><input type="text" name="sgc_std_cost[]" id="sgc_std_cost['+d+']" class="form-control" style="width:110px;" onkeyup="validatenum(this.id)" /></td>';
                    html += '<td id="sgc_action['+d+']"><a href="javascript:void(0);" id="add_sgc_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_sgc_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';

                    $('#sgc_row_body').append(html);
                }
                else if(sibabws_flag == 1 && field_type.match("add_si_avgwt_row")){
                    document.getElementById("si_avgwt_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("si_avgwt_incr").value = d;

                    html+= '<div class="row" id="si_avgwt_row_no['+d+']">';
                    html+= '<div class="col-md-3"></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="si_from_avgwt[]" id="si_from_avgwt['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="si_to_avgwt[]" id="si_to_avgwt['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="avgwt_value[]" id="avgwt_value['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="si_avgwt_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_si_avgwt_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_si_avgwt_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#si_avgwt_row_body').append(html); $('.select2').select2();
                }
                else if(smrincbdw_flag == 1 && field_type.match("add_smri_inc_row")){
                    document.getElementById("smri_inc_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("smri_inc_incr").value = d;

                    html+= '<div class="row" id="smri_inc_row_no['+d+']">';
                    html+= '<div class="col-md-3"></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="smri_from_avgbd_wt[]" id="smri_from_avgbd_wt['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="smri_to_avgbd_wt[]" id="smri_to_avgbd_wt['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="smri_rate_inc[]" id="smri_rate_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="smri_inc_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_smri_inc_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_smri_inc_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#smri_inc_row_body').append(html); $('.select2').select2();
                }
                else if(field_type.match("add_prod_inc_row")){
                    document.getElementById("prod_inc_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("prod_inc_incr").value = d;
                    
                    html+= '<div class="row" id="prod_inc_row_no['+d+']">';
                    <?php
                    if((int)$copbincdec_flag == 1){
                    ?>
                    html+= '<div class="col-md-2"></div>';
                    html+= '<div class="col-md-1"><div class="form-group"><input type="text" id="prod_inc_sdtcop[]" name="prod_inc_sdtcop['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    <?php
                    }
                    else{
                    ?>
                    html+= '<div class="col-md-3"></div>';
                    <?php
                    }
                    ?>
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="prod_from_inc[]" id="prod_from_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="prod_to_inc[]" id="prod_to_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="rate_inc[]" id="rate_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="prod_inc_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_prod_inc_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_prod_inc_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#prod_inc_row_body').append(html); $('.select2').select2();
                }
                else if(field_type.match("add_sales_inc_row")){
                    document.getElementById("sales_inc_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("sales_inc_incr").value = d;

                    html+= '<div class="row" id="sales_inc_row_no['+d+']">';
                    html+= '<div class="col-md-2"></div>';
                    html+= '<div class="col-md-2" <?php echo $sibong_view; ?>><div class="form-group"><input type="text" name="sales_inc_grade[]" id="sales_inc_grade['+d+']" class="form-control" onkeyup="validatename(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="sales_from_inc[]" id="sales_from_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="sales_to_inc[]" id="sales_to_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="sales_rate_inc[]" id="sales_rate_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="sales_inc_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_sales_inc_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_sales_inc_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '</div>';

                    $('#sales_inc_row_body').append(html); $('.select2').select2();
                }
                else if(field_type.match("add_mort_inc_row")){
                    document.getElementById("mort_inc_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("mort_inc_incr").value = d;

                    html+= '<div class="row" id="mort_inc_row_no['+d+']">';
                    html+= '<div class="col-md-3"></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="mort_from_inc[]" id="mort_from_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="mort_to_inc[]" id="mort_to_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="mort_rate_inc[]" id="mort_rate_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="mort_inc_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_mort_inc_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_mort_inc_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#mort_inc_row_body').append(html); $('.select2').select2();
                }
                else if(field_type.match("add_fcr_inc_row")){
                    document.getElementById("fcr_inc_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("fcr_inc_incr").value = d;

                    html+= '<div class="row" id="fcr_inc_row_no['+d+']">';
                    html+= '<div class="col-md-3"></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="fcr_limit_inc[]" id="fcr_limit_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="body_weight_inc[]" id="body_weight_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="fcr_rate_inc[]" id="fcr_rate_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="fcr_inc_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_fcr_inc_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_fcr_inc_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#fcr_inc_row_body').append(html); $('.select2').select2();
                }
                else if(field_type.match("add_wi_inc_row")){
                    document.getElementById("wi_inc_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("wi_inc_incr").value = d;

                    html+= '<div class="row" id="wi_inc_row_no['+d+']">';
                    html+= '<div class="col-md-3"></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="wi_from_prod_cost[]" id="wi_from_prod_cost['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="wi_to_prod_cost[]" id="wi_to_prod_cost['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="wi_rate_inc[]" id="wi_rate_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="wi_inc_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_wi_inc_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_wi_inc_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#wi_inc_row_body').append(html); $('.select2').select2();
                }
                else if(field_type.match("add_si_inc_row")){
                    document.getElementById("si_inc_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("si_inc_incr").value = d;

                    html+= '<div class="row" id="si_inc_row_no['+d+']">';
                    html+= '<div class="col-md-3"></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="si_from_prod_cost[]" id="si_from_prod_cost['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="si_to_prod_cost[]" id="si_to_prod_cost['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="si_rate_inc[]" id="si_rate_inc['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="si_inc_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_si_inc_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_si_inc_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#si_inc_row_body').append(html); $('.select2').select2();
                }
                else if(field_type.match("add_prod_dec_row")){
                    document.getElementById("prod_dec_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("prod_dec_incr").value = d;

                    html+= '<div class="row" id="prod_dec_row_no['+d+']">';
                    <?php
                    if((int)$copbincdec_flag == 1){
                    ?>
                    html+= '<div class="col-md-2"></div>';
                    html+= '<div class="col-md-1"><div class="form-group"><input type="text" id="prod_dec_sdtcop[]" name="prod_dec_sdtcop['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    <?php
                    }
                    else{
                    ?>
                    html+= '<div class="col-md-3"></div>';
                    <?php
                    }
                    ?>
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="prod_from_dec[]" id="prod_from_dec['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="prod_to_dec[]" id="prod_to_dec['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="prod_rate_dec[]" id="prod_rate_dec['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="prod_dec_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_prod_dec_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_prod_dec_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#prod_dec_row_body').append(html); $('.select2').select2();
                }
                else if(field_type.match("add_mort_dec_row")){
                    document.getElementById("mort_dec_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("mort_dec_incr").value = d;

                    html+= '<div class="row" id="mort_dec_row_no['+d+']">';
                    html+= '<div class="col-md-3"></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="mort_from_dec[]" id="mort_from_dec['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="mort_to_dec[]" id="mort_to_dec['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="mort_rate_dec[]" id="mort_rate_dec['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="mort_dec_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_mort_dec_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_mort_dec_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#mort_dec_row_body').append(html); $('.select2').select2();
                }
                else if(field_type.match("add_fcr_dec_row")){
                    document.getElementById("fcr_dec_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("fcr_dec_incr").value = d;

                    html+= '<div class="row" id="fcr_dec_row_no['+d+']">';
                    html+= '<div class="col-md-3"></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="fcr_limit_dec[]" id="fcr_limit_dec['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="prod_limit_dec[]" id="prod_limit_dec['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="fcr_rate_dec[]" id="fcr_rate_dec['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2" id="fcr_dec_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_fcr_dec_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_fcr_dec_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#fcr_dec_row_body').append(html); $('.select2').select2();
                }
                else if(field_type.match("add_fclassify_row")){
                    document.getElementById("fclassify_action["+d+"]").style.visibility = "hidden"; d++;
                    document.getElementById("fclassify_incr").value = d;

                    html+= '<div class="row" id="fclassify_row_no['+d+']">';
                    html+= '<div class="col-md-3"></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="prod_from_classify[]" id="prod_from_classify['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="prod_to_classify[]" id="prod_to_classify['+d+']" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" /></div></div>';
                    html+= '<div class="col-md-2"><div class="form-group"><input type="text" name="grade_classify[]" id="grade_classify['+d+']" class="form-control" placeholder="A-Z" /></div></div>';
                    html+= '<div class="col-md-2" id="fclassify_action['+d+']"><div class="form-group"><a href="javascript:void(0);" id="add_fclassify_row['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deduct_fclassify_row['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div></div>';
                    html+= '<div class="col-md-2"></div>';
                    html+= '</div>';

                    $('#fclassify_row_body').append(html); $('.select2').select2();
                }
                else{
                    alert("Wrong Info");
                }
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var field_type = b[0];
                var fcr_gc_flag = '<?php echo $fcr_gc_flag; ?>';
                var cfcr_gc_flag = '<?php echo $cfcr_gc_flag; ?>';
                var copabwfcr_flag = '<?php echo $copabwfcr_flag; ?>';
                var stdgconavgwt_flag = '<?php echo $stdgconavgwt_flag; ?>';
                var sibabws_flag = '<?php echo $sibabws_flag; ?>';
                var smrincbdw_flag = '<?php echo $smrincbdw_flag; ?>';
                if(fcr_gc_flag == 1 && field_type.match("deduct_fcrs_cost_row")){
                    document.getElementById("fcrs_cost_row_no["+d+"]").remove(); d--;
                    document.getElementById("fcrs_cost_incr").value = d;
                    document.getElementById("fcrs_cost_action["+d+"]").style.visibility = "visible";
                }
                else if(cfcr_gc_flag == 1 && field_type.match("deduct_cfcrs_cost_row")){
                    document.getElementById("cfcrs_cost_row_no["+d+"]").remove(); d--;
                    document.getElementById("cfcrs_cost_incr").value = d;
                    document.getElementById("cfcrs_cost_action["+d+"]").style.visibility = "visible";
                }
                else if(copabwfcr_flag == 1 && field_type.match("deduct_cop_row")){
                    document.getElementById("cop_row_no["+d+"]").remove(); d--;
                    document.getElementById("cop_incr").value = d;
                    document.getElementById("cop_action["+d+"]").style.visibility = "visible";
                }
                else if(stdgconavgwt_flag == 1 && field_type.match("deduct_sgc_row")){
                    document.getElementById("sgc_row_no["+d+"]").remove(); d--;
                    document.getElementById("sgc_incr").value = d;
                    document.getElementById("sgc_action["+d+"]").style.visibility = "visible";
                }
                else if(field_type.match("deduct_prod_inc_row")){
                    document.getElementById("prod_inc_row_no["+d+"]").remove(); d--;
                    document.getElementById("prod_inc_incr").value = d;
                    document.getElementById("prod_inc_action["+d+"]").style.visibility = "visible";
                }
                else if(field_type.match("deduct_sales_inc_row")){
                    document.getElementById("sales_inc_row_no["+d+"]").remove(); d--;
                    document.getElementById("sales_inc_incr").value = d;
                    document.getElementById("sales_inc_action["+d+"]").style.visibility = "visible";
                }
                else if(field_type.match("deduct_mort_inc_row")){
                    document.getElementById("mort_inc_row_no["+d+"]").remove(); d--;
                    document.getElementById("mort_inc_incr").value = d;
                    document.getElementById("mort_inc_action["+d+"]").style.visibility = "visible";
                }
                else if(sibabws_flag == 1 && field_type.match("deduct_si_avgwt_row")){
                    document.getElementById("si_avgwt_row_no["+d+"]").remove(); d--;
                    document.getElementById("si_avgwt_incr").value = d;
                    document.getElementById("si_avgwt_action["+d+"]").style.visibility = "visible";
                }
                else if(smrincbdw_flag == 1 && field_type.match("deduct_smri_inc_row")){
                    document.getElementById("smri_inc_row_no["+d+"]").remove(); d--;
                    document.getElementById("smri_inc_incr").value = d;
                    document.getElementById("smri_inc_action["+d+"]").style.visibility = "visible";
                }
                else if(field_type.match("deduct_fcr_inc_row")){
                    document.getElementById("fcr_inc_row_no["+d+"]").remove(); d--;
                    document.getElementById("fcr_inc_incr").value = d;
                    document.getElementById("fcr_inc_action["+d+"]").style.visibility = "visible";
                }
                
                else if(field_type.match("deduct_wi_inc_row")){
                    document.getElementById("wi_inc_row_no["+d+"]").remove(); d--;
                    document.getElementById("wi_inc_incr").value = d;
                    document.getElementById("wi_inc_action["+d+"]").style.visibility = "visible";
                }
                else if(field_type.match("deduct_si_inc_row")){
                    document.getElementById("si_inc_row_no["+d+"]").remove(); d--;
                    document.getElementById("si_inc_incr").value = d;
                    document.getElementById("si_inc_action["+d+"]").style.visibility = "visible";
                }
                else if(field_type.match("deduct_prod_dec_row")){
                    document.getElementById("prod_dec_row_no["+d+"]").remove(); d--;
                    document.getElementById("prod_dec_incr").value = d;
                    document.getElementById("prod_dec_action["+d+"]").style.visibility = "visible";
                }
                else if(field_type.match("deduct_mort_dec_row")){
                    document.getElementById("mort_dec_row_no["+d+"]").remove(); d--;
                    document.getElementById("mort_dec_incr").value = d;
                    document.getElementById("mort_dec_action["+d+"]").style.visibility = "visible";
                }
                else if(field_type.match("deduct_fcr_dec_row")){
                    document.getElementById("fcr_dec_row_no["+d+"]").remove(); d--;
                    document.getElementById("fcr_dec_incr").value = d;
                    document.getElementById("fcr_dec_action["+d+"]").style.visibility = "visible";
                }
                else if(field_type.match("deduct_fclassify_row")){
                    document.getElementById("fclassify_row_no["+d+"]").remove(); d--;
                    document.getElementById("fclassify_incr").value = d;
                    document.getElementById("fclassify_action["+d+"]").style.visibility = "visible";
                }
            }
            function fetch_branch_details(a){
                var reg_code = document.getElementById(a).value;
                if(!reg_code.match("select")){
                    removeAllOptions(document.getElementById("branch_code"));
                    myselect1 = document.getElementById("branch_code");
                    theOption1=document.createElement("OPTION");
                    theText1=document.createTextNode("-All-");
                    theOption1.value = "all"; 
                    theOption1.appendChild(theText1); 
                    myselect1.appendChild(theOption1);
                    <?php
                        $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $r_code = $row['region_code'];
                            echo "if(reg_code == '$r_code'){";
                    ?>
                        theOption1=document.createElement("OPTION");
						theText1=document.createTextNode("<?php echo $row['description']; ?>");
						theOption1.value = "<?php echo $row['code']; ?>";
						theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                    <?php
                        echo "}";
                        }
                    ?>
                }
            }
            function checkval(){
                document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var l = true;
                var schema_flag = '<?php echo $schema_flag; ?>';
                var region_code = document.getElementById("region_code").value;
                var branch_code = document.getElementById("branch_code").value;
                var from_date = document.getElementById("from_date").value;
                var to_date = document.getElementById("to_date").value;
                
                if(region_code == "select"){
                    alert("Please select Region");
                    document.getElementById("region_code").focus();
                    l = false;
                }
                else if(branch_code == "select"){
                    alert("Please select Branch");
                    document.getElementById("branch_code").focus();
                    l = false;
                }
                else if(from_date == ""){
                    alert("Enter enter/select From Date");
                    document.getElementById("from_date").focus();
                    l = false;
                }
                else if(to_date == ""){
                    alert("Enter enter/select To Date");
                    document.getElementById("to_date").focus();
                    l = false;
                }
                else if(schema_flag == 1 || schema_flag == "1"){
                    var schema_name = document.getElementById("schema_name").value;
                    if(schema_name == ""){
                        alert("Enter enter Schema Name");
                        document.getElementById("schema_name").focus();
                        l = false;
                    }
                    else{
                        l = true;
                    }
                }
                else {
                    l = true;
                }

                if(l == true){
                    var x = confirm("Are you sure, you want to save the Growing charge master?");
                    if(x == true){
                        return true;
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function check_costing(a){
                if(a.match("medicine_cost3")){
                    document.getElementById("med_value").style.visibility = "visible";
                }
                else{
                    document.getElementById("med_value").style.visibility = "hidden";
                }
            }
            function validatename(x) {
                expr = /^[a-zA-Z0-9 (.&)_-]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 50){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, '');
                }
                document.getElementById(x).value = a;
            }
            
            function focus_selection(a){
                document.querySelector(a+' .select2-search__field').focus();
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot2.php"; ?>
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