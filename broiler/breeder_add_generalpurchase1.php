<?php
//breeder_add_generalpurchase1.php
include "newConfig.php";
include "broiler_generate_trnum_details.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['generalpurchase1'];
date_default_timezone_set("Asia/Kolkata");
$uri = explode("/",$_SERVER['REQUEST_URI']); $href = $uri[1];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['addaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; } else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; } else{ $user_type = "N"; }
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access'];
    }
    if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; } else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }
    if($user_type == "S"){ $acount = 1; } else{ foreach($alink as $add_access_flag){ if($add_access_flag == $link_childid){ $acount = 1; } } }
    if($acount == 1){
        $date = date("Y-m-d");
        //Generate Transaction No.
        $incr = 0; $prefix = $trnum = $fyear = "";
        $trno_dt1 = generate_transaction_details($date,"generalpurchase1","BGP","display",$_SESSION['dbase']);
        $trno_dt2 = explode("@",$trno_dt1);
        $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];

		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
		
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `brd_sflag` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql);  $sector_code = $sector_name = array();
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
        
        //Breeder
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Module' AND `field_function` = 'Maintain Feed Stock in FARM/UNIT/SHED/BATCH/FLOCK' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bfeed_scnt = mysqli_num_rows($query);
        if((int)$bfeed_scnt > 0){
            $bfeed_stkon = ""; while($row = mysqli_fetch_assoc($query)){ $bfeed_stkon = $row['field_value']; } if($bfeed_stkon == ""){ $bfeed_stkon = "FLOCK"; }
            if($bfeed_stkon == "FARM"){
                $bsql = "SELECT * FROM `breeder_farms` WHERE `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "UNIT"){
                $bsql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "SHED"){
                $bsql = "SELECT * FROM `breeder_sheds` WHERE `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else if($bfeed_stkon == "BATCH"){
                $bsql = "SELECT * FROM `breeder_batch` WHERE `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
                while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
            }
            else{ }
        }
        
		$sql = "SELECT * FROM `breeder_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
		$sql = "SELECT * FROM `tax_details` WHERE `active` = '1' ORDER BY `value` ASC"; $query = mysqli_query($conn,$sql);
        $jcount = mysqli_num_rows($query); $gst_code = $gst_name = $gst_value = array();
		while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_name[$row['code']] = $row['gst_type']; $gst_value[$row['code']] = $row['value']; }

		$sql = "SELECT * FROM `broiler_tcds_master` WHERE `type` = 'TDS' AND `active` = '1' AND `dflag` = '0' ORDER BY `value` ASC";
		$query = mysqli_query($conn,$sql); $tcds_code = $tcds_name = $tcds_value = array();
        while($row = mysqli_fetch_assoc($query)){ $tcds_code[$row['code']] = $row['code']; $tcds_name[$row['code']] = $row['description']; $tcds_value[$row['code']] = $row['value']; }
		
		$sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%Breeder Bird%' OR `description` LIKE '%Female Bird%' OR `description` LIKE '%Male Bird%' OR `bffeed_flag` = '1' OR `bmfeed_flag` = '1' OR `begg_flag` = '1' OR `bmv_flag` = '1') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bbird_alist = array();
		while($row = mysqli_fetch_assoc($query)){ $bbird_alist[$row['code']] = $row['code']; }
		$bbird_list = implode("','", $bbird_alist);
		$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bbird_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $item_code = $item_name = $item_cunit = array();
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunit[$row['code']] = $row['cunits']; }
			
        $sql="SELECT * FROM `acc_coa` WHERE `transporter_flag` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $tport_code = $tport_name = array();
        while($row = mysqli_fetch_assoc($query)){ $tport_code[$row['code']] = $row['code']; $tport_name[$row['code']] = $row['description']; }
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
        .table1{
            transform: scale(0.8);
            transform-origin: top left;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Purchase</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body table1">
                            <div class="col-md-18">
                                <form action="breeder_save_generalpurchase1.php" method="post" role="form" onSubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:80px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Transaction No.</label>
                                            <input type="text" name="trno" id="trno" class="form-control" value="<?php echo $trnum; ?>" style="width:120px;" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label>Bill No.</label>
                                            <input type="text" name="billno" id="billno" class="form-control" style="width:60px;" >
                                        </div>
                                        <div class="form-group">
                                            <label>Supplier<b style="color:red;">&nbsp;*</b></label>
                                            <select name="vcode" id="vcode" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $ven_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Vehicle</label>
                                            <input type="text" name="vehicle_code" id="vehicle_code" class="form-control" style="width:120px;" />
                                        </div>
                                        <div class="form-group">
                                            <label>Driver</label>
                                            <input type="text" name="driver_code" id="driver_code" class="form-control" style="width:120px;" onkeyup="validatename(this.id);" />
                                        </div>
                                        <div class="form-group">
                                            <label>Driver Mobile</label>
                                            <input type="text" name="driver_mobile" id="driver_mobile" class="form-control" style="width:120px;" />
                                        </div>
                                    </div><br/>
                                    <table class="p-1" style="width:auto;">
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>Item<b style="color:red;">&nbsp;*</b></th>
                                                <th>UOM</th>
                                                <!--<th>Sent Qty</th>-->
                                                <th>Rcvd Qty<b style="color:red;">&nbsp;*</b></th>
                                                <th>Free Qty</th>
                                                <th>Rate<b style="color:red;">&nbsp;*</b></th>
                                                <th>Disc. %</th>
                                                <th>Disc. &#8377</th>
                                                <th>GST</th>
                                                <th>Item Amount</th>
                                                <th>Sector/Flock<b style="color:red;">&nbsp;*</b></th>
                                                <th style="visibility:hidden;">Action</th>
                                                <th style="visibility:hidden;">GA</th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <tr>
                                                <td><select name="icode[]" id="icode[0]" class="form-control select2" style="width:180px;" onchange="fetch_itemuom(this.id);"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="uom[]" id="uom[0]" class="form-control" style="width:90px;" readonly /></td>
                                                <!--<td><input type="text" name="snt_qty[]" id="snt_qty[0]" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>-->
                                                <td><input type="text" name="rcd_qty[]" id="rcd_qty[0]" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="fre_qty[]" id="fre_qty[0]" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="rate[]" id="rate[0]" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="dis_per[]" id="dis_per[0]" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><input type="text" name="dis_amt[]" id="dis_amt[0]" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></td>
                                                <td><select name="gst_val[]" id="gst_val[0]" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:120px;"><option value="select">select</option><?php foreach($gst_code as $gsts){ $gst_cval = $gsts."@".$gst_value[$gsts]; ?><option value="<?php echo $gst_cval; ?>"><?php echo $gst_name[$gsts]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="item_tamt[]" id="item_tamt[0]" class="form-control text-right" placeholder="0.00" style="width:90px;" readonly ></td>
                                                <td><select name="warehouse[]" id="warehouse[0]" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                                <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                <td style="visibility:hidden;"><input type="text" name="amount1[]" id="amount1[0]" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>
                                                <td style="visibility:hidden;"><input type="text" name="gst_amt[]" id="gst_amt[0]" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th style="text-align:right;" colspan="2">Total</th>
                                                <!--<th><input type="text" name="tot_sqty" id="tot_sqty" class="form-control text-right" style="width:90px;" readonly /></th>-->
                                                <th><input type="text" name="tot_rqty" id="tot_rqty" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th><input type="text" name="tot_fqty" id="tot_fqty" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th></th>
                                                <th></th>
                                                <th><input type="text" name="tot_damt" id="tot_damt" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th><input type="text" name="tot_gamt" id="tot_gamt" class="form-control text-right" style="width:120px;" readonly /></th>
                                                <th><input type="text" name="tot_ramt" id="tot_ramt" class="form-control text-right" style="width:90px;" readonly /></th>
                                                <th></th>
                                                <th></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                                <th style="visibility:hidden;"></th>
                                            </tr>
                                            <tr>
                                                <th colspan="8">
                                                    <div class="row justify-content-end align-items-end">
                                                        <div class="form-group" style="text-align:left;">
                                                            <label>TDS</label>
                                                            <select name="tcds_code" id="tcds_code" class="form-control select2" style="width:180px;" onchange="calculate_final_total_amount();">
                                                                <option value="none">None</option>
                                                                <?php foreach($tcds_code as $tcode){ ?><option value="<?php echo $tcode; ?>"><?php echo $tcds_name[$tcode]; ?></option><?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" style="text-align:left;">
                                                            <label>Type</label>
                                                            <select name="tcds_type1" id="tcds_type1" class="form-control select2" style="width:180px;" onchange="calculate_final_total_amount();">
                                                                <option value="add">Add</option>
                                                                <option value="deduct">Deduct</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th><div class="form-group"><label style="visibility:hidden;">Amount</label><input type="text" name="tcds_amt" id="tcds_amt" class="form-control text-right" style="width:90px;" readonly /></div></th>
                                            </tr>
                                            <tr>
                                                <th colspan="8">
                                                    <div class="row justify-content-end align-items-end">
                                                        <div class="form-group" style="text-align:left;">
                                                            <label>Transporter</label>
                                                            <select name="freight_pay_acc" id="freight_pay_acc" class="form-control select2" style="width:180px;" onchange="calculate_final_total_amount();">
                                                                <option value="none">None</option>
                                                                <?php foreach($tport_code as $tcode){ ?><option value="<?php echo $tcode; ?>"><?php echo $tport_name[$tcode]; ?></option><?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" style="text-align:left;">
                                                            <label>Type</label>
                                                            <select name="freight_type" id="freight_type" class="form-control select2" style="width:180px;" onchange="calculate_final_total_amount();">
                                                                <option value="add">Add</option>
                                                                <option value="deduct">Deduct</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </th>
                                                <th><div class="form-group"><label style="visibility:hidden;">Amount</label><input type="text" name="freight_amt" id="freight_amt" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);calculate_final_total_amount(this.id);" onchange="validateamount(this.id);" /></div></th>
                                            </tr>
                                            <tr>
                                                <th colspan="8">
                                                    <div class="form-group" style="text-align:right;">
                                                        <label>Round-Off</label>
                                                    </div>
                                                </th>
                                                <th><input type="text" name="round_off" id="round_off" class="form-control text-right" style="width:90px;" readonly /></th>
                                            </tr>
                                            <tr>
                                                <th colspan="8">
                                                    <div class="form-group" style="text-align:right;">
                                                        <label>Net Amount</label>
                                                    </div>
                                                </th>
                                                <th><input type="text" name="finl_amt" id="finl_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                            </tr>
                                        </tfoot>
                                    </table><br/><br/>
                                    <div class="row" style="margin-bottom:3px;">
                                        <div class="col-md-4 form-group"></div>
                                        <div class="col-md-4 form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"></textarea>
                                        </div>
                                        <div class="col-md-4 form-group"></div>
                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:20px;">
                                            <label>IN</label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:20px;">
                                            <label>EB</label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
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
                window.location.href = 'breeder_display_generalpurchase1.php?ccid='+ccid;
            }
            function checkval(){
				update_ebtn_status(1);
                var l = true;

                var date = document.getElementById("date").value;
                var vcode = document.getElementById("vcode").value;
                if(date == ""){
                    alert("Kindly select appropriate date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(vcode.match("select")){
                    alert("Kindly select appropriate Supplier");
                    document.getElementById("vcode").focus();
                    l = false;
                }
                else{
                    //Check Item Details
                    var incr = document.getElementById("incr").value; var qty = price = c = d = 0; var icode = warehouse = "";
                    for(d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            icode = document.getElementById("icode["+d+"]").value;
                            qty = document.getElementById("rcd_qty["+d+"]").value; if(qty == ""){ qty = 0; }
                            price = document.getElementById("rate["+d+"]").value; if(price == ""){ price = 0; }
                            warehouse = document.getElementById("warehouse["+d+"]").value;
                            if(icode == "" || icode == "select"){
                                alert("Kindly select appropriate Item in row: "+c);
                                document.getElementById("icode["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(qty) == 0){
                                alert("Kindly enter Quantity in row: "+c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            } 
                            else if(parseFloat(price) == 0){
                                alert("Kindly enter Rate in row: "+c);
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
                            if(warehouse == "" || warehouse == "select"){
                                alert("Kindly select appropriate Warehouse");
                                document.getElementById("warehouse["+d+"]").focus();
                                l = false;
                            }
                            else{ }
                        }
                    }
                }
                
                if(l == true){
                    var x = window.confirm("Are You Sure! You want to Save The Transaction.");
                    if(x == true){
                        return true;
                    }
                    else {
                        update_ebtn_status(0);
                        return false;
                    }
                }
                else{
                    update_ebtn_status(0);
                    return false;
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="icode[]" id="icode['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_itemuom(this.id);"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="uom[]" id="uom['+d+']" class="form-control" style="width:90px;" readonly /></td>';
                //html += '<td><input type="text" name="snt_qty[]" id="snt_qty['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="rcd_qty[]" id="rcd_qty['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="fre_qty[]" id="fre_qty['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="rate[]" id="rate['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="calculate_total_amt(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="dis_per[]" id="dis_per['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><input type="text" name="dis_amt[]" id="dis_amt['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" ></td>';
                html += '<td><select name="gst_val[]" id="gst_val['+d+']" class="form-control select2" onchange="calculate_total_amt(this.id)" style="width:120px;"><option value="select">select</option><?php foreach($gst_code as $gsts){ $gst_cval = $gsts."@".$gst_value[$gsts]; ?><option value="<?php echo $gst_cval; ?>"><?php echo $gst_name[$gsts]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="item_tamt[]" id="item_tamt['+d+']" class="form-control text-right" placeholder="0.00" style="width:90px;" readonly ></td>';
                html += '<td><select name="warehouse[]" id="warehouse['+d+']" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="amount1[]" id="amount1['+d+']" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="gst_amt[]" id="gst_amt['+d+']" class="form-control text-right" placeholder="0.00" style="width:20px;" readonly ></td>';
                html += '</tr>';
                $('#row_body').append(html);
                $('.select2').select2();
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
                    document.getElementById("dis_amt["+d+"]").value = "";
                    if(parseFloat(dis_per) > 0 && parseFloat(amount1) > 0){
                        var dis_amt = ((parseFloat(dis_per) / 100) * amount1); if(dis_amt == "NaN" || dis_amt.length == 0 || dis_amt == 0){ dis_amt = ""; }
                        document.getElementById("dis_amt["+d+"]").value = dis_amt.toFixed(2);
                    }
                    calculate_total_amt(a);
                }
                else{
                    var dis_amt = document.getElementById("dis_amt["+d+"]").value; if(dis_amt == ""){ dis_amt = 0; }
                    document.getElementById("dis_per["+d+"]").value = "";
                    if(parseFloat(dis_amt) > 0 && parseFloat(amount1) > 0){
                        var dis_per = ((parseFloat(dis_amt) * 100) / amount1); if(dis_per == "NaN" || dis_per.length == 0 || dis_per == 0){ dis_per = ""; }
                        document.getElementById("dis_per["+d+"]").value = dis_per.toFixed(2);
                    }
                    calculate_total_amt(a);
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
                document.getElementById("gst_amt["+d+"]").value = 0;
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
                calculate_final_total_amount();
            }
            function calculate_final_total_amount(){
                var incr = document.getElementById("incr").value; var snt_qty = rcd_qty = fre_qty = dis_amt = gst_amt = item_tamt = tot_sqty = tot_rqty = tot_fqty = tot_damt = tot_gamt = tot_ramt = 0;
                for(var d = 0;d <= incr;d++){
                    //snt_qty = document.getElementById("snt_qty["+d+"]").value; if(snt_qty == ""){ snt_qty = 0; }
                    //tot_sqty = parseFloat(tot_sqty) + parseFloat(snt_qty);
                    rcd_qty = document.getElementById("rcd_qty["+d+"]").value; if(rcd_qty == ""){ rcd_qty = 0; }
                    tot_rqty = parseFloat(tot_rqty) + parseFloat(rcd_qty);
                    fre_qty = document.getElementById("fre_qty["+d+"]").value; if(fre_qty == ""){ fre_qty = 0; }
                    tot_fqty = parseFloat(tot_fqty) + parseFloat(fre_qty);
                    dis_amt = document.getElementById("dis_amt["+d+"]").value; if(dis_amt == ""){ dis_amt = 0; }
                    tot_damt = parseFloat(tot_damt) + parseFloat(dis_amt);
                    gst_amt = document.getElementById("gst_amt["+d+"]").value; if(gst_amt == ""){ gst_amt = 0; }
                    tot_gamt = parseFloat(tot_gamt) + parseFloat(gst_amt);
                    item_tamt = document.getElementById("item_tamt["+d+"]").value; if(item_tamt == ""){ item_tamt = 0; }
                    tot_ramt = parseFloat(tot_ramt) + parseFloat(item_tamt);
                }
                //document.getElementById("tot_sqty").value = tot_sqty.toFixed(2);
                document.getElementById("tot_rqty").value = tot_rqty.toFixed(2);
                document.getElementById("tot_fqty").value = tot_fqty.toFixed(2);
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
                
                //Transporter Calculations
                var freight_type = document.getElementById("freight_type").value;
                var freight_amt = document.getElementById("freight_amt").value; if(freight_amt == ""){ freight_amt = 0; }
                if(freight_type == "deduct"){
                    net_amt = parseFloat(net_amt) - parseFloat(freight_amt);
                }
                else{
                    net_amt = parseFloat(net_amt) + parseFloat(freight_amt);
                }
                
                //Round-Off
                var round_off = finl_amt = 0;
                //finl_amt = parseFloat(tot_ramt).toFixed(0);
                finl_amt = parseFloat(net_amt).toFixed(0);
                round_off = parseFloat(finl_amt) - parseFloat(net_amt);
                document.getElementById("round_off").value = parseFloat(round_off).toFixed(2);
                
                document.getElementById("finl_amt").value = parseFloat(finl_amt).toFixed(2);
            }
            function fetch_itemuom(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var code = document.getElementById("icode["+d+"]").value;
                var uom = "";
                <?php
                foreach($item_code as $icode){
                    $uom = $item_cunit[$icode];
                    echo "if(code == '$icode'){";
                ?>
                uom = '<?php echo $uom; ?>';
                <?php
                    echo "}";
                }
                ?>
                document.getElementById("uom["+d+"]").value = uom;
            }
            function update_ebtn_status(a){
                if(parseInt(a) == 1){
                    document.getElementById("ebtncount").value = "1";
                    document.getElementById("submit").style.visibility = "hidden";
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                }
            }
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