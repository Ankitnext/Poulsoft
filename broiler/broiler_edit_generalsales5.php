<?php
//broiler_edit_generalsales5.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['generalsales5'];
date_default_timezone_set("Asia/Kolkata");
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['editaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; } else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; } else{ $user_type = "N"; }
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access'];
    }
    if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; } else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }
    if($user_type == "S"){ $acount = 1; } else{ foreach($alink as $edit_access_flag){ if($edit_access_flag == $link_childid){ $acount = 1; } } }
    if($acount == 1){
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
		
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'  ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `tax_details` WHERE `active` = '1' ORDER BY `value` ASC"; $query = mysqli_query($conn,$sql);
        $jcount = mysqli_num_rows($query); $gst_code = $gst_name = $gst_value = array();
		while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_name[$row['code']] = $row['gst_type']; $gst_value[$row['code']] = $row['value']; }

		$sql = "SELECT * FROM `broiler_tcds_master` WHERE `type` = 'TCS' AND `active` = '1' AND `dflag` = '0' ORDER BY `value` ASC";
		$query = mysqli_query($conn,$sql); $tcds_code = $tcds_name = $tcds_value = array();
        while($row = mysqli_fetch_assoc($query)){ $tcds_code[$row['code']] = $row['code']; $tcds_name[$row['code']] = $row['description']; $tcds_value[$row['code']] = $row['value']; }
		
		$sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
			
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'General Sale-1' AND `field_function` LIKE 'Stock Check' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $stockcheck_flag = mysqli_num_rows($query);

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Broiler sales:broiler_display_generalsales5.php' AND `field_function` LIKE 'Batch No manual entry' AND `flag` = '1' AND `user_access` = 'all'";
        $query = mysqli_query($conn,$sql); $bnme_flag = mysqli_num_rows($query);	
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Broiler sales:broiler_display_generalsales5.php' AND `field_function` LIKE 'Manufacturing Date manual entry' AND `flag` = '1' AND `user_access` = 'all'";
        $query = mysqli_query($conn,$sql); $mdme_flag = mysqli_num_rows($query);	
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Broiler sales:broiler_display_generalsales5.php' AND `field_function` LIKE 'Expiry Date manual entry' AND `flag` = '1' AND `user_access` = 'all'";
        $query = mysqli_query($conn,$sql); $edme_flag = mysqli_num_rows($query);
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Broiler sales:broiler_display_generalsales5.php' AND `field_function` LIKE 'PO No manual entry' AND `flag` = '1' AND `user_access` = 'all'";
        $query = mysqli_query($conn,$sql); $pnme_flag = mysqli_num_rows($query);
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Broiler sales:broiler_display_generalsales5.php' AND `field_function` LIKE 'PO Date manual entry' AND `flag` = '1' AND `user_access` = 'all'";
        $query = mysqli_query($conn,$sql); $pdme_flag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Customer Sale Price' AND `field_function` LIKE 'Fetch Customer Price from Master' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $CusMastPrc_flag = mysqli_num_rows($query);
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
            padding-left: 2px;
            padding-right: 0px;
        }
        .form-group{
            margin: 0 3px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $ids = $_GET['trnum'];
        $sql = "SELECT * FROM `broiler_sales` WHERE `trnum` = '$ids' AND (`sale_type` = 'generalsales5' OR `trlink` = 'broiler_display_generalsales5.php')";
        $query = mysqli_query($conn,$sql); $c = 0;
        while($row = mysqli_fetch_assoc($query)){
            $date = $row['date'];
            $vcode = $row['vcode'];
            $billno = $row['billno'];
            $fbatch_no[$c] = $row['fbatch_no'];
            $fmake_date[$c] = $row['fmake_date'];
            $fexp_date[$c] = $row['fexp_date'];
            $icodes[$c] = $row['icode'];
            $sale_pono = $row['sale_pono'];
            $sale_podate = $row['sale_podate'];
            $rcd_qty[$c] = round($row['rcd_qty'],5);
            $rate[$c] = round($row['rate'],5);
            $amount1[$c] = round($row['amount1'],5);
            $dis_per[$c] = round($row['dis_per'],5);
            $dis_amt[$c] = round($row['dis_amt'],5);
            $tax_code[$c] = $row['gst_code'];
            $tax_per[$c] = round($row['gst_per'],5);
            $tax_amt[$c] = round($row['gst_amt'],5);
            $item_tamt[$c] = round($row['item_tamt'],5);
            $remarks = $row['remarks'];
            $warehouse = $row['warehouse'];
            $vehicle_code = $row['vehicle_code'];
            $driver_code = $row['driver_code'];
            $dmobile_no = $row['dmobile_no'];
            $finl_amt = round($row['finl_amt'],2);
            $remarks = $row['remarks'];
            $tcdscode = $row['tcds_code'];
            $tcds_type1 = $row['tcds_type1'];
            $tcds_amt = round($row['tcds_amt'],5);
            $avg_price[$c] = round($row['avg_price'],5);
            $avg_amount[$c] = round($row['avg_amount'],5);
            $round_off = $row['round_off'];
            $c++;
        } $c = $c - 1;
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Sales</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_modify_generalsales5.php" method="post" role="form" enctype="multipart/form-data" onSubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($date)); ?>" style="width:80px;">
                                        </div>
                                        <div class="form-group">
                                            <label>Transaction No.</label>
                                            <input type="text" name="trno" id="trno" class="form-control" value="<?php echo $ids; ?>" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Bill No.</label>
                                            <input type="text" name="billno" id="billno" class="form-control" value="<?php echo $billno; ?>" style="width:60px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Customer<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode" id="vcode" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($vcode == $scode){ echo "selected"; } ?>><?php echo $ven_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Stock Point / Feed Mill<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse" id="warehouse" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($warehouse == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php if((int)$pnme_flag == 1) { ?>
                                        <div class="form-group">
                                            <label>PO No.</label>
                                            <input type="text" name="sale_pono" id="sale_pono" class="form-control" value="<?php echo $sale_pono; ?>" style="width:120px;" />
                                        </div><?php } ?>
                                        <?php if((int)$pdme_flag == 1) { ?>
                                        <div class="form-group">
                                            <label>PO Date</label>
                                            <input type="text" name="sale_podate" id="sale_podate" class="form-control rc_datepicker" value="<?php echo $sale_podate; ?>" style="width:120px;" onkeyup="validatename(this.id);" readonly/>
                                        </div><?php } ?>
                                    </div><br/>
                                    <table>
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>Item</th>
                                                <th>Qty (In Kgs)</th>
                                                <th>Sale Rate</th>
                                                <th>Amount</th>
                                                <th>Disc. %</th>
                                                <th>Disc. &#8377</th>
                                                <th>GST</th>
                                                <th>Item Amount</th>
                                                <th></th>
                                                <?php if((int)$bnme_flag == 1) { ?><th>Batch No.</th><?php } ?></php>
                                                <?php if((int)$mdme_flag == 1) { ?><th>M. Date</th><?php } ?>
                                                <?php if((int)$edme_flag == 1) { ?><th>E. Date</th><?php } ?>
                                                <th style="visibility:hidden;">AS</th>
                                                <th style="visibility:hidden;">AP</th>
                                                <th style="visibility:hidden;">AA</th>
                                                <th style="visibility:hidden;">GA</th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                        <?php $i = $c; for($c = 0;$c <= $i;$c++){ ?>
                                            <tr id="row_no[<?php echo $c; ?>]">
                                                <td><select name="icode[]" id="icode[<?php echo $c; ?>]" class="form-control select2" style="width:180px;" onchange="fetch_stock_master(this.id);fetch_customer_pricemaster(this.id);"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>" <?php if($icodes[$c] == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="rcd_qty[]" id="rcd_qty[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $rcd_qty[$c]; ?>" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="rate[]" id="rate[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $rate[$c]; ?>" placeholder="0.00" style="width:90px;" onkeyup="calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="amount1[]" id="amount1[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $amount1[$c]; ?>" placeholder="0.00" style="width:90px;" readonly ></td>
                                                <td><input type="text" name="dis_per[]" id="dis_per[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $dis_per[$c]; ?>" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="dis_amt[]" id="dis_amt[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $dis_amt[$c]; ?>" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><select name="gst_val[]" id="gst_val[<?php echo $c; ?>]" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:120px;"><option value="select">select</option><?php foreach($gst_code as $gsts){ $gst_cval = $gsts."@".$gst_value[$gsts]; ?><option value="<?php echo $gst_cval; ?>" <?php if($gsts == $tax_code[$c]){ echo "selected"; } ?>><?php echo $gst_name[$gsts]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="item_tamt[]" id="item_tamt[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $item_tamt[$c]; ?>" placeholder="0.00" style="width:90px;" readonly ></td>
                                                <?php
                                                if($c == $i){ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:visible;">'; }
                                                else{ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:hidden;">'; }
                                                echo '<a href="javascript:void(0);" id="addrow['.$c.']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;';
                                                if($c > 0){ echo '<a href="javascript:void(0);" id="deductrow['.$c.']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a>'; }
                                                echo '</td>';
                                                ?>
                                                <?php if((int)$bnme_flag == 1) { ?><td><input type="text" name="fbatch_no[]" id="fbatch_no[<?php echo $c; ?>]" class="form-control" value="<?php echo $fbatch_no[$c]; ?>" style="width:100px;" /></td><?php } ?>
                                                <?php if((int)$mdme_flag == 1) { ?><td><input type="text" name="fmake_date[]" id="fmake_date[<?php echo $c; ?>]" class="form-control rc_datepicker" value="<?php echo date("d.m.Y",strtotime($fmake_date[$c])); ?>" style="width:100px;" readonly /></td><?php } ?>
                                                <?php if((int)$edme_flag == 1) { ?><td><input type="text" name="fexp_date[]" id="fexp_date[<?php echo $c; ?>]" class="form-control rc_datepicker" value="<?php echo date("d.m.Y",strtotime($fexp_date[$c])); ?>" style="width:100px;" readonly /></td><?php } ?>
                                                <td style="visibility:hidden;"><input type="text" name="available_stock[]" id="available_stock[<?php echo $c; ?>]" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="avg_price[]" id="avg_price[<?php echo $c; ?>]" value="<?php echo $avg_price[$c]; ?>" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="avg_amount[]" id="avg_amount[<?php echo $c; ?>]" value="<?php echo $avg_amount[$c]; ?>" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="gst_amt[]" id="gst_amt[<?php echo $c; ?>]" value="<?php echo $tax_amt[$c]; ?>" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th style="text-align:right;">Total</th>
                                                <th><input type="text" name="tot_rqty" id="tot_rqty" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th></th>
                                                <th><input type="text" name="tot_amt1" id="tot_amt1" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th></th>
                                                <th><input type="text" name="tot_damt" id="tot_damt" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th><input type="text" name="tot_gamt" id="tot_gamt" class="form-control text-right" style="width:120px;" readonly /></th>
                                                <th><input type="text" name="tot_ramt" id="tot_ramt" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                            </tr>
                                            <tr>
                                                <th colspan="7">
                                                    <div class="row justify-content-right align-items-right">
                                                        <div class="form-group" style="text-align:left;">
                                                            <label>TCS</label>
                                                            <select name="tcds_code" id="tcds_code" class="form-control select2" style="width:180px;" onchange="calculate_final_total_amount();">
                                                                <option value="none">None</option>
                                                                <?php foreach($tcds_code as $tcode){ ?><option value="<?php echo $tcode; ?>" <?php if($tcdscode == $tcode){ echo "selected"; } ?>><?php echo $tcds_name[$tcode]; ?></option><?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" style="text-align:left;">
                                                            <label>Type</label>
                                                            <select name="tcds_type1" id="tcds_type1" class="form-control select2" style="width:180px;" onchange="calculate_final_total_amount();">
                                                                <option value="add" <?php if($tcds_type1 == "add"){ echo "selected"; } ?>>Add</option>
                                                                <option value="deduct" <?php if($tcds_type1 == "deduct"){ echo "selected"; } ?>>Deduct</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th><div class="form-group"><label style="visibility:hidden;">Amount</label><input type="text" name="tcds_amt" id="tcds_amt" class="form-control text-right" value="<?php echo $tcds_amt; ?>" style="width:90px;" readonly /></div></th>
                                            </tr>
                                            <tr>
                                                <th colspan="7">
                                                    <div class="form-group" style="text-align:right;">
                                                        <label>Round-Off</label>
                                                </th>
                                                <th><input type="text" name="round_off" id="round_off" class="form-control text-right" value="<?php echo $round_off; ?>" style="width:90px;" readonly /></th>
                                            </tr>
                                            <tr>
                                                <th colspan="7">
                                                    <div class="form-group" style="text-align:right;">
                                                        <label>Net Amount</label>
                                                </th>
                                                <th><input type="text" name="finl_amt" id="finl_amt" class="form-control text-right" value="<?php echo $finl_amt; ?>" style="width:90px;" readonly /></th>
                                            </tr>
                                        </tfoot>
                                    </table><br/><br/>

                                    <div class="row">
                                        <div class="form-group">
                                                <label>Vehicle</label>
                                                <input type="text" name="vehicle_code" id="vehicle_code" class="form-control" value="<?php echo $vehicle_code; ?>" style="width:120px;" />
                                        </div>
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <input type="text" name="driver_code" id="driver_code" class="form-control" value="<?php echo $driver_code; ?>" style="width:120px;" onkeyup="validatename(this.id);" />
                                        </div>
                                        <div class="form-group">
                                            <label>Driver Mobile</label>
                                            <input type="text" name="dmobile_no" id="dmobile_no" class="form-control" value="<?php echo $dmobile_no; ?>" style="width:120px;" onkeyup="validatename(this.id);" />
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:3px;">
                                        <div class="col-md-4 form-group"></div>
                                        <div class="col-md-4 form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"><?php echo $remarks; ?></textarea>
                                        </div>
                                        <div class="col-md-4 form-group"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Reference Document-1</label>
                                                <input type="file" name="prod_doc_1" id="prod_doc_1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Reference Document-2</label>
                                                <input type="file" name="prod_doc_2" id="prod_doc_2" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Reference Document-3</label>
                                                <input type="file" name="prod_doc_3" id="prod_doc_3" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-1"></div>
                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:20px;">
                                            <label>ID</label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:20px;">
                                            <label>IN</label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $i; ?>" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:20px;">
                                            <label>EB</label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Update</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onClick="return_back()">Cancel</button>
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
                window.location.href = 'broiler_display_generalsales5.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incrs = document.getElementById("incr").value; var qty = price = total_amt = c = d = stock = 0; var icode = "";
                var l = true;
                //Re-calculate Item Amount
                var rcd_qty = rate = amount1 = dis_amt = item_tamt = gst_per = gst_amt = 0; var gst_val = ""; var gst_val2 = [];
                for(d = 0;d <= incrs;d++){
                    rcd_qty = rate = amount1 = dis_amt = item_tamt = gst_per = gst_amt = 0; gst_val = ""; gst_val2 = [];
                    rcd_qty = document.getElementById("rcd_qty["+d+"]").value; if(rcd_qty == ""){ rcd_qty = 0; }
                    rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }

                    amount1 = parseFloat(rcd_qty) * parseFloat(rate);
                    document.getElementById("amount1["+d+"]").value = amount1.toFixed(2);
                    
                    //Discount
                    dis_amt = document.getElementById("dis_amt["+d+"]").value; if(dis_amt == ""){ dis_amt = 0; }
                    item_tamt = parseFloat(amount1) - parseFloat(dis_amt);

                    //GST
                    gst_val = document.getElementById("gst_val["+d+"]").value;
                    if(gst_val != "select"){
                        gst_val2 = gst_val.split("@"); gst_per = gst_val2[1]; if(gst_per == ""){ gst_per = 0; }
                        if(parseFloat(gst_per) > 0){
                            gst_amt = ((parseFloat(gst_per) / 100) * item_tamt);
                            document.getElementById("gst_amt["+d+"]").value = gst_amt.toFixed(2);
                        }
                    }
                    if(gst_amt == ""){ gst_amt = 0; }
                    item_tamt = parseFloat(item_tamt) + parseFloat(gst_amt);
                    document.getElementById("item_tamt["+d+"]").value = item_tamt.toFixed(2);
                }
                calculate_final_total_amount();

                var date = document.getElementById("date").value;
                var vcode = document.getElementById("vcode").value;
                var warehouse = document.getElementById("warehouse").value;
                if(date == ""){
                    alert("Kindly enter/select appropriate date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(vcode.match("select")){
                    alert("Kindly select appropriate Customer");
                    document.getElementById("vcode").focus();
                    l = false;
                }
                else if(warehouse.match("select")){
                    alert("Kindly select appropriate Warehouse");
                    document.getElementById("warehouse").focus();
                    l = false;
                }
                else{
                    //Stock Check
                    var stockcheck_flag = '<?php echo $stockcheck_flag; ?>';
                    if(stockcheck_flag == 1){
                        for(d = 0;d <= incrs;d++){
                            if(l == true){
                                c = d + 1;
                                qty = document.getElementById("rcd_qty["+d+"]").value;
                                stock = document.getElementById("available_stock["+d+"]").value;
                                if(parseFloat(qty) > parseFloat(stock)){
                                    alert("Stock not Available in row: "+c);
                                    document.getElementById("rcd_qty["+d+"]").focus();
                                    l = false;
                                }
                            }
                        }
                    }
                    else{ }

                    //Check Item Details
                    for(d = 0;d <= incrs;d++){
                        if(l == true){
                            c = d + 1;
                            icode = document.getElementById("icode["+d+"]").value;
                            qty = document.getElementById("rcd_qty["+d+"]").value;
                            price = document.getElementById("rate["+d+"]").value;
                            if(icode.match("select")){
                                alert("Kindly select appropriate Item in row: "+c);
                                document.getElementById("icode["+d+"]").focus();
                                l = false;
                            }
                            else if(qty == "" || qty == "0.00" || qty == 0){
                                alert("Kindly enter Quantity in row: "+c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            } 
                            // else if(price == "" || price == "0.00" || price == 0){
                            //     alert("Kindly enter Rate in row: "+c);
                            //     document.getElementById("rate["+d+"]").focus();
                            //     l = false;
                            // }
                        }
                    }
                }
                
                if(l == true){
                    var answer = window.confirm("Are You Sure! You want to Save The Transaction.");
                    if (answer) {
                        //some code
                        return true;
                    }
                    else {
                        //some code
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
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';

                var bnme_flag = '<?php echo $bnme_flag ?>';
                var mdme_flag = '<?php echo $mdme_flag ?>';
                var edme_flag = '<?php echo $edme_flag ?>';

                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="icode[]" id="icode['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_stock_master(this.id);fetch_customer_pricemaster(this.id);"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="rcd_qty[]" id="rcd_qty['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="rate[]" id="rate['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="amount1[]" id="amount1['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" readonly ></td>';
                html += '<td><input type="text" name="dis_per[]" id="dis_per['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="dis_amt[]" id="dis_amt['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><select name="gst_val[]" id="gst_val['+d+']" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:120px;"><option value="select">select</option><?php foreach($gst_code as $gsts){ $gst_cval = $gsts."@".$gst_value[$gsts]; ?><option value="<?php echo $gst_cval; ?>"><?php echo $gst_name[$gsts]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="item_tamt[]" id="item_tamt['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" readonly ></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                if(parseInt(bnme_flag) == 1) { html += '<td><input type="text" name="fbatch_no[]" id="fbatch_no['+d+']" class="form-control" style="width:100px;" /></td>'; }
                if(parseInt(mdme_flag) == 1) { html += '<td><input type="text" name="fmake_date[]" id="fmake_date['+d+']" class="form-control rc_datepicker" style="width:100px;" value="<?php echo date('d.m.Y'); ?>" readonly /></td>'; }
                if(parseInt(edme_flag) == 1) { html += '<td><input type="text" name="fexp_date[]" id="fexp_date['+d+']" class="form-control rc_datepicker" style="width:100px;" value="<?php echo date('d.m.Y'); ?>" readonly /></td>'; }
                html += '<td style="visibility:hidden;"><input type="text" name="available_stock[]" id="available_stock['+d+']" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="avg_price[]" id="avg_price['+d+']" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="avg_amount[]" id="avg_amount['+d+']" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="gst_amt[]" id="gst_amt['+d+']" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>';
                html += '</tr>';
                $('#row_body').append(html);
                $('.select2').select2();
                $( ".rc_datepicker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });

            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_final_total_amount();
            }
            function fetch_discount_amount(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var rcd_qty = document.getElementById("rcd_qty["+d+"]").value; if(rcd_qty == ""){ rcd_qty = 0; }
                var rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                var amount1 = parseFloat(rcd_qty) * parseFloat(rate); if(amount1 == ""){ amount1 = 0; }

                if(b[0].match("dis_per")){
                    var dis_per = document.getElementById("dis_per["+d+"]").value; if(dis_per == ""){ dis_per = 0; }
                    if(parseFloat(dis_per) > 0 && parseFloat(amount1) > 0){
                        var dis_amt = ((parseFloat(dis_per) / 100) * amount1); if(dis_amt == "NaN" || dis_amt.length == 0 || dis_amt == 0){ dis_amt = ""; }
                        document.getElementById("dis_amt["+d+"]").value = dis_amt.toFixed(2);
                        calculate_total_amt(a);
                    }
                }
                else{
                    var dis_amt = document.getElementById("dis_amt["+d+"]").value; if(dis_amt == ""){ dis_amt = 0; }
                    if(parseFloat(dis_amt) > 0 && parseFloat(amount1) > 0){
                        var dis_per = ((parseFloat(dis_amt) * 100) / amount1); if(dis_per == "NaN" || dis_per.length == 0 || dis_per == 0){ dis_per = ""; }
                        document.getElementById("dis_per["+d+"]").value = dis_per.toFixed(2);
                        calculate_total_amt(a);
                    }
                }
            }
            function calculate_total_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var rcd_qty = document.getElementById("rcd_qty["+d+"]").value; if(rcd_qty == ""){ rcd_qty = 0; }
                var rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }

                var amount1 = parseFloat(rcd_qty) * parseFloat(rate);
                document.getElementById("amount1["+d+"]").value = amount1.toFixed(2);

                //Discount
                var dis_amt = document.getElementById("dis_amt["+d+"]").value; if(dis_amt == ""){ dis_amt = 0; }
                var item_tamt = parseFloat(amount1) - parseFloat(dis_amt);

                //GST
                var gst_per = gst_amt = 0; var gst_val2 = [];
                var gst_val = document.getElementById("gst_val["+d+"]").value;
                if(gst_val != "select"){
                    gst_val2 = gst_val.split("@"); gst_per = gst_val2[1]; if(gst_per == ""){ gst_per = 0; }
                    if(parseFloat(gst_per) > 0){
                        gst_amt = ((parseFloat(gst_per) / 100) * item_tamt);
                        document.getElementById("gst_amt["+d+"]").value = gst_amt.toFixed(2);
                    }
                }
                if(gst_amt == ""){ gst_amt = 0; }
                item_tamt = parseFloat(item_tamt) + parseFloat(gst_amt);
                document.getElementById("item_tamt["+d+"]").value = item_tamt.toFixed(2);

                var avg_price = document.getElementById("avg_price["+d+"]").value; if(avg_price == ""){ avg_price = 0; }
                var avg_amount = parseFloat(rcd_qty) * parseFloat(avg_price);
                document.getElementById("avg_amount["+d+"]").value = avg_amount.toFixed(2);
                calculate_final_total_amount();
            }
            function calculate_final_total_amount(){
                var incr = document.getElementById("incr").value; var rcd_qty = amount1 = dis_amt = gst_amt = item_tamt = tot_rqty = tot_amt1 = tot_damt = tot_gamt = tot_ramt = 0;
                for(var d = 0;d <= incr;d++){
                    rcd_qty = document.getElementById("rcd_qty["+d+"]").value; if(rcd_qty == ""){ rcd_qty = 0; }
                    tot_rqty = parseFloat(tot_rqty) + parseFloat(rcd_qty);
                    amount1 = document.getElementById("amount1["+d+"]").value; if(amount1 == ""){ amount1 = 0; }
                    tot_amt1 = parseFloat(tot_amt1) + parseFloat(amount1);
                    dis_amt = document.getElementById("dis_amt["+d+"]").value; if(dis_amt == ""){ dis_amt = 0; }
                    tot_damt = parseFloat(tot_damt) + parseFloat(dis_amt);
                    gst_amt = document.getElementById("gst_amt["+d+"]").value; if(gst_amt == ""){ gst_amt = 0; }
                    tot_gamt = parseFloat(tot_gamt) + parseFloat(gst_amt);
                    item_tamt = document.getElementById("item_tamt["+d+"]").value; if(item_tamt == ""){ item_tamt = 0; }
                    tot_ramt = parseFloat(tot_ramt) + parseFloat(item_tamt);
                }
                document.getElementById("tot_rqty").value = tot_rqty.toFixed(2);
                document.getElementById("tot_amt1").value = tot_amt1.toFixed(2);
                document.getElementById("tot_damt").value = tot_damt.toFixed(2);
                document.getElementById("tot_gamt").value = tot_gamt.toFixed(2);
                document.getElementById("tot_ramt").value = tot_ramt.toFixed(2);
                //TCS Calculations
                var tcds_per = tcds_amt = net_amt = 0;
                var tcds_code = document.getElementById("tcds_code").value;
                var tcds_type1 = document.getElementById("tcds_type1").value;
                if(tcds_code != "none"){
                    <?php
                        foreach($tcds_code as $tcode){
                            $tvalue = $tcds_value[$tcode];
                            echo "if(tcds_code == '$tcode'){";
                            ?>
                            tcds_per = '<?php echo $tvalue; ?>';
                            <?php
                            echo "}";
                        }
                    ?>
                    tcds_amt = ((parseFloat(tcds_per) / 100) * tot_ramt).toFixed(2);
                    document.getElementById("tcds_amt").value = tcds_amt;
                }
                if(tcds_type1 == "deduct"){
                    net_amt = parseFloat(tot_ramt) - parseFloat(tcds_amt);
                }
                else{
                    net_amt = parseFloat(tot_ramt) + parseFloat(tcds_amt);
                }
                

                //Round-Off
                var round_off = finl_amt = 0;
                //finl_amt = parseFloat(tot_ramt).toFixed(0);
                finl_amt = parseFloat(net_amt).toFixed(0);
                round_off = parseFloat(finl_amt) - parseFloat(net_amt);
                document.getElementById("round_off").value = parseFloat(round_off).toFixed(2);
                

                document.getElementById("finl_amt").value = parseFloat(finl_amt).toFixed(2);
            }
            function fetch_stock_master(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var date = document.getElementById("date").value;
                var sector = document.getElementById("warehouse").value;
                var item_code = document.getElementById(a).value;
                var fetch_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&trtype=Sale";
                //window.open(url);
				var asynchronous = true;
				fetch_items.open(method, url, asynchronous);
				fetch_items.send();
				fetch_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_price = this.responseText;
                        if(item_price.length > 0){
                            var item_details = item_price.split("@");
                            if(parseFloat(item_details[1]) < 0){ item_details[1] = 0; }
                            document.getElementById("available_stock["+d+"]").value = item_details[0];
                            document.getElementById("avg_price["+d+"]").value = item_details[1];
                        }
                        else{
                            //alert("Item Stock not available, Kindly check before saving ...!");
                            document.getElementById("available_stock["+d+"]").value = 0;
                            document.getElementById("avg_price["+d+"]").value = 0;
                        }
                    }
                }
            }
            function fetch_customer_pricemaster(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var cprc_flag = '<?php echo $CusMastPrc_flag; ?>';
                if(parseInt(cprc_flag) == 1){
                    var date = document.getElementById("date").value;
                    var vcode = document.getElementById("vcode").value;
                    var icode = document.getElementById("icode["+d+"]").value;
                    
                    var fetch_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_customer_pricemaster.php?vendor="+vcode+"&item_code="+icode+"&date="+date+"&row_count="+d+"&trtype=add";
                    //window.open(url);
                    var asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var cprc_dt1 = this.responseText;
                            var cprc_dt2 = cprc_dt1.split("@");
                            document.getElementById("rate["+d+"]").value = parseFloat(cprc_dt2[0]).toFixed(2);
                            calculate_total_amt(a);
                        }
                    }
                }
            }
            function fetch_multiple_item_stock_master(){
                var incr = document.getElementById("incr").value;
                var date = document.getElementById("date").value;
                var sector = document.getElementById("warehouse").value;
                var item_code = fetch_items = method = url = asynchronous = item_price = item_details = ""; var rcd_qty = avg_amount = 0;
                var trnum = '<?php echo $ids; ?>';
                for(var d = 0;d <= incr;d++){
                    item_code = document.getElementById("icode["+d+"]").value;
                    fetch_items = new XMLHttpRequest();
                    method = "GET";
                    url = "broiler_fetch_itemstockmaster_lsfi.php?sector="+sector+"&item_code="+item_code+"&date="+date+"&id="+trnum+"&row_count="+d;
                    //window.open(url);
                    asynchronous = true;
                    fetch_items.open(method, url, asynchronous);
                    fetch_items.send();
                    fetch_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            item_price = this.responseText;
                            if(item_price.length > 0){
                                item_details = item_price.split("@");
                                if(parseFloat(item_details[1]) < 0){ item_details[1] = 0; }
                                if(parseFloat(item_details[3]) < 0){ item_details[3] = 0; }
                                document.getElementById("available_stock["+item_details[3]+"]").value = item_details[0];
                                document.getElementById("avg_price["+item_details[3]+"]").value = item_details[1];
                                rcd_qty = document.getElementById("rcd_qty["+item_details[3]+"]").value;
                                avg_amount = parseFloat(rcd_qty) * parseFloat(item_details[1]);
                                document.getElementById("avg_amount["+item_details[3]+"]").value = parseFloat(avg_amount).toFixed(2);
                                calculate_final_total_amount();
                            }
                            else{
                                alert("Item Stock not available, Kindly check before saving ...!");
                                document.getElementById("available_stock["+d+"]").value = 0;
                                document.getElementById("avg_price["+d+"]").value = 0;
                                document.getElementById("avg_amount["+d+"]").value = 0;
                            }
                        }
                    }
                }
            }
            fetch_multiple_item_stock_master();
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){ if(window.screen.availWidth <= 400){ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; } } else{ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; } } }, 1000);
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