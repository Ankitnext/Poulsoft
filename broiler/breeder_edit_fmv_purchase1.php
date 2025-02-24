<?php
//breeder_delete_fmv_purchase1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['generalpurchase2'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['editaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){ 
        $today = date("d.m.Y"); $date = date("Y-m-d"); 
        //Generate Transaction No. 
        $incr = 0; $prefix = $trnum = $fyear = ""; 
        $trno_dt1 = generate_transaction_details($date,"inventorytransfer9","ITF","display",$_SESSION['dbase']); 
        $trno_dt2 = explode("@",$trno_dt1); 
        $incr = $trno_dt2; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3]; 
         
        $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_cunit[$row['code']] = $row['cunits']; }

        $sql = "SELECT * FROM `item_category` WHERE `active` = '1' AND `bfeed_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $cat_code[$row['code']] = $row['code']; $cat_name[$row['code']] = $row['description']; $cat_cunit[$row['code']] = $row['cunits']; }

        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$bcodes') AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $bag_code = $bag_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bag_code[$row['code']] = $row['code']; $bag_name[$row['code']] = $row['description']; }
 				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $to_sector_code[$row['code']] = $row['code']; $to_sector_name[$row['code']] = $row['description']; }
        
		$farms = array();
		$sql = "SELECT * FROM `breeder_batch` WHERE `dflag` = '0' AND `gc_flag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $farms[$row['code']] = $row['farm_code'];$farms_batch[$row['farm_code']] = $row['description']; }
        $farm_list = implode("','", $farms); 
        
        $farms = array();
		$sql = "SELECT * FROM `breeder_sheds` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $bsheds[$row['code']] = $row['code'];$bsheds_name[$row['code']] = $row['description']; }
        $farm_list = implode("','", $farms); 
				
        $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $ocoa_code = $ocoa_name = array();
        while($row = mysqli_fetch_assoc($query)){ $ocoa_code[$row['code']] = $row['code']; $ocoa_name[$row['code']] = $row['description']; }
	    
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Purchase TDS' AND `field_function` LIKE 'after 50L TDS Auto' AND `flag` = '1' AND (`user_access` LIKE '%$user_code%' || `user_access` LIKE 'all');";
        $query = mysqli_query($conn,$sql); $auto_tds_flag = mysqli_num_rows($query);
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: hidden;
        }
        .form-control{
            font-size: 13px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $ids = $_GET['id'];
        $sql = "SELECT * FROM `breeder_purchases` WHERE `id` = '$ids' AND `dflag` = '0' AND `trlink` = 'breeder_delete_fmv_purchase1.php'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){          
            $date = $row['date'];
            $transportor_name = $row['transportor_name'];
            $billno = $row['billno'];
            $trnum = $row['trnum'];
            $vehicle_code = $row['vehicle_code'];
            $driver_code = $row['driver_code'];
            $driver_mobile = $row['driver_mobile'];
            $item_codee = $row['item_code'];
            $uom = $row['uom'];
            $sn_qty = $row['sn_qty'];
            $rcd_qty = $row['rcd_qty'];
            $fre_qty = $row['fre_qty'];
            $rate = $row['rate'];
            $farmer_price = $row['farmer_price'];
            $dis_per = $row['dis_per'];
            $dis_price = $row['dis_price'];
            $gst_per = $row['gst_per'];
            $amount = $row['amount'];
            $gst_per = $row['gst_per'];
            $shed_code = $row['shed_code'];
            $batch = $row['batch'];
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit General Purchase</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="breeder_modify_generalpurchase2.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <table class="table1" style="width:auto;">
                                            <thead>
                                                <tr>   
                                                    <th style="text-align:center;"><label>Date<b style="color:red;">&nbsp;*</b></label></th>  
                                                    <th style="text-align:center;"><label>Supplier<b style="color:red;">&nbsp;*</b></label></th>                 
                                                    <th style="text-align:center;"><label>Bill No.<b style="color:red;">&nbsp;*</b></label></th>                 
                                                    <th style="text-align:center;"><label>trnum<b style="color:red;">&nbsp;*</b></label></th>                 
                                                    <th style="text-align:center;"><label>Vehicle<b style="color:red;">&nbsp;*</b></label></th>                 
                                                    <th style="text-align:center;"><label>Driver<b style="color:red;">&nbsp;*</b></label></th>                 
                                                    <th style="text-align:center;"><label>Driver Mobile<b style="color:red;">&nbsp;*</b></label></th>                 
                                                    <th style="text-align:center;"><label>Item <b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>UOM</label></th>
                                                    <th style="text-align:center;"><label>Sent Qty<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Recieve Qty<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Free Qty</label></th>
                                                    <th style="text-align:center;"><label>Rate<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Farmer Rate</label></th>
                                                    <th style="text-align:center;"><label>Disc. %</label></th>
                                                    <th style="text-align:center;"><label>Disc. ₹</label></th>
                                                    <th style="text-align:center;"><label>GST</label></th>
                                                    <th style="text-align:center;"><label>Amount</label></th>
                                                    <th style="text-align:center;"><label>Shed<b style="color:red;">&nbsp;*</b></label></th>                                                           
                                                    <th style="text-align:center;"><label>Batch<b style="color:red;">&nbsp;*</b></label></th>                                                           
                                                    <th style="visibility:hidden;"></th>
                                                </tr>
                                            </thead> 
                                            <tbody id="tbody"> 
                                                <tr>
                                                    <td><input type="text" name="date" id="date" class="form-control" style="width:210px;"  value="<?php echo $date; ?>" onchange="(this.id);" readonly /></td>
                                                    <td><select name="transportor_name" id="transportor_name" class="form-control select2" style="width:160px;" onchange="fetch_edit_stock_master();"><option value="select">select</option> <?php foreach($to_sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>" <?php if($transportor_name == $whouse_code){ echo "selected"; } ?>><?php echo $to_sector_name[$row['code']]; ?></option><?php } ?> </select></td>
                                                    <td><input type="text" name="billno" id="billno" class="form-control" style="width:210px;"  value="<?php echo $billno; ?>" onchange="(this.id);" readonly /></td>
                                                    <td><input type="text" name="trnum" id="trnum" class="form-control" style="width:210px;"  value="<?php echo $trnum; ?>" onchange="(this.id);" readonly /></td>
                                                    <td><input type="text" name="vehicle_code" id="vehicle_code" class="form-control" style="width:210px;"  value="<?php echo $vehicle_code; ?>" onchange="(this.id);" readonly /></td>
                                                    <td><input type="text" name="driver_code" id="driver_code" class="form-control" style="width:210px;"  value="<?php echo $driver_code; ?>" onchange="(this.id);" /></td>
                                                    <td><input type="text" name="driver_mobile" id="driver_mobile" class="form-control" style="width:210px;"  value="<?php echo $driver_mobile; ?>" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                    <td><select name="item_code" id="item_code" class="form-control select2" style="width:130px;" onchange="fetch_stock_master(this.id);check_medvac_masterprices(this.id);fetch_itemuom(this.id);"><option value="select">select</option><?php foreach($cat_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($item_codee == $prod_code){ echo "selected"; } ?>><?php echo $item_name[$prod_code]; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="uom" id="uom" class="form-control" style="width:80px;" value = "<?php echo $uom; ?>" readonly /></td>
                                                    <td><input type="text" name="sn_qty" id="sn_qty" class="form-control text-right" style="width:90px;" value = "<?php echo $sn_qty; ?>" onkeyup="validate_num(this.id);calculate_total_amt3();" onchange="validateamount(this.id);" /></td>
                                                    <td><input type="text" name="rcd_qty" id="net_qty" class="form-control text-right" style="width:90px;" value = "<?php echo $rcd_qty; ?>" onkeyup="validate_num(this.id);calculate_total_amt3();" onchange="validateamount(this.id);" /></td>
                                                    <td><input type="text" name="fre_qty" id="fre_qty" class="form-control text-right" style="width:90px;" value = "<?php echo $fre_qty; ?>" onkeyup="validate_num(this.id);();" onchange="validateamount(this.id);" /></td>
                                                    <td><input type="text" name="rate" id="rate" class="form-control text-right" style="width:90px;" value = "<?php echo $rate; ?>" onkeyup="validate_num(this.id);calculate_total_amt3();" onchange="validateamount(this.id);" /></td>
                                                    <td><input type="text" name="farmer_price" id="farmer_price" class="form-control text-right" style="width:90px;" value = "<?php echo $farmer_price; ?>" onkeyup="validate_num(this.id);();" onchange="validateamount(this.id);" /></td>
                                                    <td><input type="text" name="dis_per" id="dis_per" class="form-control" style="width:80px;" value = "<?php echo $dis_per; ?>" onkeyup="validate_num(this.id);calculate_total_amt3();" onchange="validateamount(this.id);"/></td>
                                                    <td><input type="text" name="dis_price" id="dis_price" class="form-control" style="width:80px;" value = "<?php echo $dis_price; ?>" /></td>
                                                    <td><select name="gst_per" id="gst_per" class="form-control select2" style="width:200px;" onchange="fetch_stock_master1(this.id);check_medvac_masterprices(this.id);" ><option value="select">select</option><?php foreach($from_sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>" <?php if($item_codee == $prod_code){ echo "selected"; } ?>><?php echo $cat; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="item_tamt" id="item_tamt" class="form-control text-right" style="width:90px;" value = "<?php echo $amount; ?>" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validateamount(this.id);" readonly /></td>
                                                    <td><select name="shed_code" id="shed_code" class="form-control select2" style="width:200px;" onchange="fetch_stock_master2(this.id);check_medvac_masterprices(this.id);" ><option value="select">select</option><?php foreach($bsheds as $b_code){ ?><option value="<?php echo $b_code; ?>" <?php if($shed_code == $b_code){ echo "selected"; } ?>><?php echo $bsheds_name[$row['code']]; ?></option><?php } ?></select></td>
                                                    <td><select name="batch" id="batch" class="form-control select2" style="width:160px;" onchange="fetch_edit_stock_master();"><option value="select">select</option> <?php foreach($farms as $fcode){ ?><option value="<?php echo $fcode; ?>" <?php if($batch == $fcode){ echo "selected"; } ?>><?php echo $farms_batch[$row['farm_code']]; ?></option><?php } ?> </select></td>
                                                </tr>
                                            </tbody>
                                        </table> 
                                    </div><br/> 
                                    <div class="row">
                                        <div class="form-group">
                                                    <label>Freight Type</label>
                                                    <select name="freight_type" id="freight_type" class="form-control select2" onchange="calculate_netpay()">
                                                        <option value="select">select</option>
                                                        <option value="include">Include</option>
                                                        <option value="exclude">Exclude</option>
                                                        <option value="inbill">In Bill</option>
                                                    </select>
                                                </div>
                                        <div class="form-group">
                                            <label>Payment Option</label>
                                            <div>
                                                <label for="paylater">
                                                    <input type="radio" name="paymentOption" id="paylater" value="paylater" /> Paylater
                                                </label>
                                                <label for="paybill">
                                                    <input type="radio" name="paymentOption" id="paybill" value="paybill" /> Pay Bill
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Pay Account</label>
                                                    <select name="freight_pay_acc" id="freight_pay_acc" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php
                                                        $sql = "SELECT * FROM `acc_coa` WHERE `transporter_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                        ?><option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Freight Account</label>
                                                    <select name="freight_acc" id="freight_acc" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php
                                                        $sql = "SELECT * FROM `acc_coa` WHERE `freight_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                        ?><option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Bag Type</label>
                                                    <select name="bag_code" id="bag_code" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php foreach($bag_code as $carrier){ ?><option value="<?php echo $carrier; ?>"><?php echo $bag_name[$carrier]; ?></option><?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>No.of Bags</label>
                                                    <input type="text" name="bag_count" id="bag_count" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label>Batch No.</label>
                                                    <input type="text" name="batch_no" id="batch_no" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Expiry Date</label>
                                                    <input type="text" name="exp_date" id="exp_date" class="form-control datepicker" value="<?php echo date('d.m.Y'); ?>" style="width:100px;"/>
                                                </div>
                                            </div>
                                            <div class="col-md-2"></div>
                                            <div class="col-md-1" style="visibility:hidden;"><input type="text" name="mnu_tds_edit" id="mnu_tds_edit" value="0"/></div>
                                            <div class="col-md-1">
                                                <div class="form-group" align="center">
                                                    <label>TCS</label>
                                                    <input type="checkbox" name="tcds_per" id="tcds_per" class="form-control" value="<?php echo $tdsper; ?>" style="transform: scale(.7);" <?php if($auto_tds_flag == "1"){ echo 'onchange="manual_uncheck();"'; } else{ echo 'onchange="calculate_netpay();"'; } ?> />
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>TCS Amount</label>
                                                    <input type="text" name="tcds_amount" id="tcds_amount" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id);update_manualtds_flag();" onchange="validateamount(this.id);" <?php if($tds_eflag == 0){ echo "readonly"; } ?> />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Other Charges</label>
                                                    <select name="ocharge_coa" id="ocharge_coa" class="form-control select2">
                                                        <option value="select">select</option>
                                                        <?php foreach($ocoa_code as $ocode){ ?><option value="<?php echo $ocode; ?>"><?php echo $ocoa_name[$ocode]; ?></option><?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Amount</label>
                                                    <input type="text" name="ocharge_amt" id="ocharge_amt" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id);calculate_netpay();" onchange="validateamount(this.id);" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-10"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Roundoff</label>
                                                    <input type="text" name="round_off" id="round_off" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-10"></div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Net Amount</label>
                                                    <input type="text" name="finl_amt" id="finl_amt" class="form-control" placeholder="0.00" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" readonly />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="row" style="margin-bottom:3px;">
                                        <div class="col-md-4 form-group"></div>
                                        <div class="col-md-4 form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"></textarea>
                                        </div>
                                        <div class="col-md-4 form-group"></div>
                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:30px;">
                                            <label>ID<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>EB<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Update</button>&ensp;
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
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
				var incr = document.getElementById("incr").value;
                var date = transportor_name = billno = vehicle_code = driver_code = driver_mobile = item_code =  uom =  shed_code =  batch = ""; var rcd_qty = fre_qty =  rate =  farmer_price =  dis_per =  dis_price = gst_per =  amount = 0;
                var l = true;
                date = document.getElementById("date").value; 
                transportor_name = document.getElementById("transportor_name").value; 
                billno = document.getElementById("billno").value; 
                vehicle_code = document.getElementById("vehicle_code").value; 
                driver_code = document.getElementById("driver_code").value; 
                driver_mobile = document.getElementById("driver_mobile").value; 
                item_code = document.getElementById("item_code").value; 
                uom = document.getElementById("uom").value; 
                rcd_qty = document.getElementById("rcd_qty").value; if(rcd_qty == ""){ rcd_qty = 0; } 
                fre_qty = document.getElementById("fre_qty").value; if(fre_qty == ""){ fre_qty = 0; } 
                rate = document.getElementById("rate").value; if(rate == ""){ rate = 0; } 
                farmer_price = document.getElementById("farmer_price").value; if(farmer_price == ""){ farmer_price = 0; } 
                dis_per = document.getElementById("dis_per").value; if(dis_per == ""){ dis_per = 0; } 
                dis_price = document.getElementById("dis_price").value; if(dis_price == ""){ dis_price = 0; } 
                gst_per = document.getElementById("gst_per").value; if(gst_per == ""){ gst_per = 0; } 
                amount = document.getElementById("amount").value; if(amount == ""){ amount = 0; } 
                shed_code = document.getElementById("shed_code").value; 
                batch = document.getElementById("batch").value; 
                
                if(date == ""){
                    alert("Please Enter Date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(transportor_name == "select"){
                    alert("Please select From Supplier");
                    document.getElementById("transportor_name").focus();
                    l = false;
                }
                else if(billno == ""){
                    alert("Please Enter Bill No.");
                    document.getElementById("from_batch").focus();
                    l = false;
                }
                else if(vehicle_code == ""){
                    alert("Please Enter Vehicle Code");
                    document.getElementById("vehicle_code").focus();
                    l = false;
                }
                else if(driver_code == ""){
                    alert("Please Enter From Batch ");
                    document.getElementById("driver_code").focus();
                    l = false;
                }
                else if(driver_mobile == ""){
                    alert("Please enter Item Code ");
                    document.getElementById("driver_mobile").focus();
                    l = false;
                }
                else if(item_code == "select"){
                    alert("Please select Item Name ");
                    document.getElementById("item_code").focus();
                    l = false;
                } else if(uom == ""){
                    alert("Please Enter uom Name ");
                    document.getElementById("uom").focus();
                    l = false;
                }
                else if(parseFloat(rcd_qty) == 0){
                    alert("Please enter Net Quantity ");
                    document.getElementById("rcd_qty").focus();
                    l = false;
                }else if(parseFloat(fre_qty) == 0){
                    alert("Please enter Free Quantity ");
                    document.getElementById("fre_qty").focus();
                    l = false;
                }else if(parseFloat(rate) == 0){
                    alert("Please enter Rate ");
                    document.getElementById("rate").focus();
                    l = false;
                }else if(parseFloat(farmer_price) == 0){
                    alert("Please Enter Farmer Price");
                    document.getElementById("farmer_price").focus();
                    l = false;
                }
                else if(parseFloat(dis_per) == 0){
                    alert("Please Enter Disc Percentage ");
                    document.getElementById("dis_per").focus();
                    l = false;
                }else if(parseFloat(dis_price) == 0){
                    alert("Please Enter Disc Price ");
                    document.getElementById("dis_price").focus();
                    l = false;
                }else if(parseFloat(gst_per) == 0){
                    alert("Please Enter Gst Percentage ");
                    document.getElementById("gst_per").focus();
                    l = false;
                }else if(parseFloat(amount) == 0){
                    alert("Please Enter Amount");
                    document.getElementById("amount").focus();
                    l = false;
                }else if(shed_code == "select"){
                    alert("Please select Sector ");
                    document.getElementById("shed_code").focus();
                    l = false;
                }else if(batch == "select"){
                    alert("Please select Batch ");
                    document.getElementById("batch").focus();
                    l = false;
                }
                else{ }
                if(l == true){
                    return true;
                }
                else {
                    document.getElementById("submit").style.visibility = "visible";
                    document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function update_manualtds_flag(){
                document.getElementById("mnu_tds_edit").value = 1;
                calculate_netpay();
            }
            function fetch_freight_coa_account(a){
                removeAllOptions(document.getElementById("freight_pay_acc"));
                myselect = document.getElementById("freight_pay_acc"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(a.match("pay_type1")){
                    <?php
					$sql="SELECT * FROM `acc_coa` WHERE `transporter_flag` = '1' AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
                }
                else{
                    <?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
                }
            }
            function calculate_netpay(){ 
                var incr = parseInt(document.getElementById("incr").value); 
                var total_amount = 0; var tamt = 0; var net_amount = 0; 
                for(var d = 0; d <= incr; d++){ 
                    tamt = document.getElementById("item_tamt["+d+"]").value; 
                    if(tamt == "" || tamt == "0" || tamt.length == 0 || tamt == "0.00"){ tamt = 0; } 
                    total_amount = parseFloat(total_amount) + parseFloat(tamt); 
                } 
                // Freight Amount
                var freight_amount = document.getElementById("freight_amount").value; 
                if(freight_amount == "" || freight_amount == "0" || freight_amount.length == 0 || freight_amount == "0.00"){ freight_amount = 0; }
                if(freight_amount > 0){ 
                    var freight_type = document.getElementById("freight_type").value; 
                    if(!freight_type.match("select")){ 
                        if(freight_type.match("include")){ 
                            net_amount = parseFloat(total_amount) - parseFloat(freight_amount); 
                        }
                        else if(freight_type.match("exclude")){
                            net_amount = parseFloat(total_amount);
                        }
                        else if(freight_type.match("inbill")){
                            net_amount = parseFloat(total_amount) + parseFloat(freight_amount);
                        }
                        else{
                            net_amount = parseFloat(total_amount);
                        }
                    }
                    else{
                        net_amount = total_amount;
                    }
                }
                else{
                    net_amount = total_amount;
                }
                //TDS Calculations 
                var mnu_tds_edit = document.getElementById("mnu_tds_edit").value; 
                if(parseInt(mnu_tds_edit) == 1){ 
                    var tcds_amount = document.getElementById("tcds_amount").value; 
                    net_amount = parseFloat(net_amount) + parseFloat(tcds_amount); 
                } 
                else{ 
                    var auto_tds_flag = '<?php echo $auto_tds_flag; ?>'; 
                    if(auto_tds_flag == 1 || auto_tds_flag == "1"){ 
                        var mnu_tds_uchk = document.getElementById("mnu_tds_uchk").value; 
                        if(mnu_tds_uchk == 1 || mnu_tds_uchk == "1"){ 
                            var rqty = rprc = ftamt = total_item_amount = 0; 
                            for(d = 0; d <= incr; d++){ 
                                /*rqty = document.getElementById("rcd_qty["+d+"]").value;
                                rprc = document.getElementById("rate["+d+"]").value;
                                if(rqty == "" || rqty == "0" || rqty.length == 0 || rqty == "0.00"){ rqty = 0; }
                                if(rprc == "" || rprc == "0" || rprc.length == 0 || rprc == "0.00"){ rprc = 0; }
                                if(rqty > 0 && rprc > 0){
                                    total_item_amount = (parseFloat(total_item_amount) + (parseFloat(rqty) * parseFloat(rprc)));
                                }*/
                                ftamt = document.getElementById("item_tamt["+d+"]").value;
                                total_item_amount = parseFloat(total_item_amount) + parseFloat(ftamt); 
                            }
                            var ven_pur_totamt = document.getElementById("ven_pur_totamt").value; if(ven_pur_totamt == ""){ ven_pur_totamt = 0; } 
                            var ptot_amt = parseFloat(total_item_amount) + parseFloat(ven_pur_totamt); 
                            var tcds_flag = document.getElementById("tcds_per"); 
                            var tdsper = '<?php echo $tdsper; ?>'; if(tdsper == ""){ tdsper = 0; } 
                            if(parseFloat(ven_pur_totamt) > 5000000 || parseFloat(ptot_amt) > 5000000){ 
                                tcds_flag.checked = true; 
                                
                                if(parseFloat(tdsper) > 0){ var out_ptotamt = parseFloat(ptot_amt) - 5000000; var tcds_amount = ((parseFloat(tdsper) / 100) * out_ptotamt); }
                                else{ var tcds_amount = 0; }

                                document.getElementById("tcds_amount").value = parseFloat(tcds_amount).toFixed(2);
                                net_amount = parseFloat(net_amount) + parseFloat(tcds_amount);
                            }
                            else{
                                tcds_flag.checked = false;
                                net_amount = parseFloat(net_amount);
                                document.getElementById("tcds_amount").value = 0;
                            }
                        }
                        else{
                            net_amount = parseFloat(net_amount);
                            document.getElementById("tcds_amount").value = 0;
                        }
                    }
                    else{ 
                        var tcds_flag = document.getElementById("tcds_per"); 
                        if(tcds_flag.checked == true){ 
                            var rqty = rprc = ftamt = item_amount = 0; 
                            for(d = 0; d <= incr; d++){ 
                                /*
                                rqty = document.getElementById("rcd_qty["+d+"]").value;
                                rprc = document.getElementById("rate["+d+"]").value;
                                if(rqty == "" || rqty == "0" || rqty.length == 0 || rqty == "0.00"){ rqty = 0; }
                                if(rprc == "" || rprc == "0" || rprc.length == 0 || rprc == "0.00"){ rprc = 0; }
                                if(rqty > 0 && rprc > 0){
                                    item_amount = (parseFloat(item_amount) + (parseFloat(rqty) * parseFloat(rprc)));
                                }
                                */
                                ftamt = document.getElementById("item_tamt["+d+"]").value;
                                item_amount = parseFloat(item_amount) + parseFloat(ftamt);
                            }
                            var tcds_per = document.getElementById("tcds_per").value;
                            var tcds_amount = ((parseFloat(tcds_per) / 100) * item_amount).toFixed(2);
                            document.getElementById("tcds_amount").value = tcds_amount;
                            net_amount = parseFloat(net_amount) + parseFloat(tcds_amount);
                        }
                        else{
                            document.getElementById("tcds_amount").value = "";
                        }
                    }
                }
                var ocharge_amt = document.getElementById("ocharge_amt").value; if(ocharge_amt == ""){ ocharge_amt = 0; }
                net_amount = parseFloat(net_amount) + parseFloat(ocharge_amt);

                var final_amt = net_amount.toFixed(0);
                var roundoff = parseFloat(final_amt) - parseFloat(net_amount);

                document.getElementById("round_off").value = roundoff.toFixed(2);
                document.getElementById("finl_amt").value = final_amt;
            }
            function manual_uncheck(){
                var tcds_flag = document.getElementById("tcds_per");
                if(tcds_flag.checked == true){
                    document.getElementById("mnu_tds_uchk").value = 1;
                    calculate_netpay();
                }
                else{
                    document.getElementById("mnu_tds_uchk").value = 0;
                    calculate_netpay();
                }
            }
            function calculate_total_amt(){ 
                    /*Total Calculations*/ 
                    // var incr = document.getElementById("incr").value; 
                    var quantity = price = amount = 0; 

                    // var icode = iname = ""; 
                    // for(var d = 0;d <= incr;d++){ 
                    // quantity = price = amount = 0; 
                        quantity = document.getElementById("rcd_qty["+d+"]").value; if(quantity == ""){ quantity = 0; } 
                        price = document.getElementById("rate["+d+"]").value; if(price == ""){ price = 0; } 
                        amount = parseFloat(quantity) * parseFloat(price); 
                        document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(2); 
                    // }
                }
                function calculate_total_amt2(){
                    /*Total Calculations*/
                    // var incr = document.getElementById("incr").value;
                    var quantity = price = amount =  amount2 = disc = disc_per = 0;

                    // var icode = iname = "";
                    // for(var d = 0;d <= incr;d++){
                        // quantity = price = disc =  amount =  disc_per =  amount2 = 0;
                        quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                        price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                        disc = document.getElementById("dis_per["+d+"]").value; if(disc == ""){ disc = 0; }
                        amount = parseFloat(quantity) * parseFloat(price);
                        disc_per = ((disc/100) * amount );
                        amount2 = amount - disc_per;
                        document.getElementById("amount2["+d+"]").value = parseFloat(amount2).toFixed(2);
                    // }
                }
                function calculate_total_amt3(){
                    /*Total Calculations*/
                    var incr = document.getElementById("incr").value;
                    var quantity = price = amount = amount2 = amount3 = disc = disc_per = gst = gst_per = 0;

                    // var icode = iname = "";
                    // for(var d = 0;d <= incr;d++){
                    //     quantity = price = amount = 0;
                        quantity = document.getElementById("quantity["+d+"]").value; if(quantity == ""){ quantity = 0; }
                        price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                        disc = document.getElementById("dis_per["+d+"]").value; if(disc == ""){ disc = 0; }
                        gst = document.getElementById("gst_per["+d+"]").value; if(gst == ""){ gst = 0; }
                        amount = parseFloat(quantity) * parseFloat(price);
                        disc_per = ((disc/100) * amount );
                        amount2 = amount - disc_per;
                        gst_per = ((gst/100) * amount2);
                        amount3 = amount2 + gst_per;
                        document.getElementById("amount3["+d+"]").value = parseFloat(amount3).toFixed(2);
                    // }
                }
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'breeder_delete_fmv_purchase1.php?ccid='+ccid;
            }
            document.addEventListener("keydown", (e) => { if(e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validate_count(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
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