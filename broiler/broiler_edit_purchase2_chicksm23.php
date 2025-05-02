<?php
//broiler_edit_purchase2_chicksm23.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['purchase2_chicks23'];
date_default_timezone_set("Asia/Kolkata");
$href = "broiler_edit_purchase2_chicks23.php";
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $elink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $elink = explode(",",$row['editaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($elink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $today = date("Y-m-d");
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; $sec_flag[$row['code']] = 0; }
				
		$sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $batch_acode = $farm_acode = array(); $farm_list = $batch_list = "";
        while($row = mysqli_fetch_assoc($query)){ $batch_acode[$row['code']] = $row['code']; $farm_acode[$row['code']] = $row['farm_code']; }
        
        $farm_list = implode("','", $farm_acode); $batch_list = implode("','", $batch_acode);
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; $sec_flag[$row['code']] = 1; }
				
		$sql = "SELECT * FROM `tax_details` WHERE `active` = '1' ORDER BY `value` ASC"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
		while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_name[$row['code']] = $row['gst_type']; $gst_value[$row['code']] = $row['value']; }

        //Fetch Feed Details and Feed in Bags Flag
        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Broiler Chick%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $item_cat = "";
        while($row = mysqli_fetch_assoc($query)){ if($item_cat == ""){ $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $chick_code = $row['code']; $chick_name = $row['description']; }
        
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Decimal"){ $decimal_no = $row['flag']; } if($row['field_name'] == "Purchase Qty"){ $qty_on_sqty_flag = $row['flag']; } }

        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Purchase TDS' AND `field_function` LIKE 'after 50L TDS Auto' AND `flag` = '1' AND (`user_access` LIKE '%$user_code%' || `user_access` LIKE 'all');";
        $query = mysqli_query($conn,$sql); $auto_tds_flag = mysqli_num_rows($query);

        if(isset($_POST['fetch_submit']) == true){
            $farms = $_POST['farms'];
            $fdate = date("Y-m-d",strtotime($_POST['fdate']));
            $tdate = date("Y-m-d",strtotime($_POST['tdate']));
        }
        else{
            $fdate = $tdate = date("Y-m-d");
        }
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
        .form-control,label {
            padding-left: 2px;
            padding-right: 0px;
            height: 23px;
            font-size: 13px;
        }
        .form-group{
            margin: 0 3px;
        }
        .select2-results__option { 
            font-size: 13px;
            line-height: 15px !important;
        }
        .select2-selection__rendered{
            font-size:13px;
        }
.select2-container .select2-selection--single {
    height: 23px !important;
}
.select2-selection__arrow {
    height: 20px !important;
}
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Multiple-Purchases</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_edit_purchase2_chicksm23.php" method="post" role="form" onsubmit="return checkval2()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Farm</label>
                                            <select name="farms" id="farms" class="form-control select2" onchange="broiler_check_sectype();">
                                                <option value="all" <?php if($farms == "all"){ echo "selected"; } ?>>-All-</option>
                                                <?php foreach($sector_code as $fcode){ if(!empty($sector_name[$fcode])){ ?>
                                                <option value="<?php echo $fcode; ?>" <?php if($farms == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="fdate">Date: </label>
                                            <input type="text" class="form-control datepicker" name="fdate" id="fdate" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" onchange="checktodates()">
                                        </div>
                                        <div class="form-group">
                                            <label for="tdate">To Date: </label>
                                            <input type="text" class="form-control datepicker" name="tdate" id="tdate" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" onchange="checktodates()">
                                        </div>
                                        <div class="" style="width:15px;visibility:hidden;">
                                            <div class="form-group"><label for="sec_flag">sf: </label><input type="text" class="form-control" name="sec_flag" id="sec_flag" value="0" style="width:10px;" readonly></div>
                                        </div>
                                        <div class="" style="width:15px;visibility:hidden;">
                                            <div class="form-group"><label for="sec_flag">dc: </label><input type="text" class="form-control" name="dcount" id="dcount" value="0" style="width:10px;" readonly></div>
                                        </div>
                                        <div class="form-group" align="center">
                                            <br/>
                                            <button type="submit" name="fetch_submit" id="fetch_submit" class="btn btn-sm bg-purple">Fetch Details</button>&ensp;
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-18">
                                <?php
                                if(isset($_POST['fetch_submit']) == true){
                                ?>
                                <form action="broiler_modify_purchase2_chicksm23.php" method="post" role="form" onsubmit="return checkval()">
                                    <?php
                                        if($_POST['sec_flag'] == 1 || $_POST['sec_flag'] == "1"){
                                            $trno_list = "";
                                            $sql = "SELECT trnum,COUNT(trnum) as ccount FROM `broiler_purchases` WHERE `warehouse` IN ('$farms') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                                            $query = mysqli_query($conn,$sql);
                                            while($row = mysqli_fetch_array($query)){ if($row['ccount'] == 1){ if($trno_list == ""){ $trno_list = $row['trnum']; } else{ $trno_list = $trno_list."','".$row['trnum']; } } }

                                            $sql = "SELECT * FROM `broiler_purchases` WHERE `warehouse` IN ('$farms') AND `farm_batch` IN ('$batch_list') AND `trnum` IN ('$trno_list') AND `icode` = '$chick_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                                        }
                                        else if($_POST['sec_flag'] == 0 || $_POST['sec_flag'] == "0"){
                                            if($farms == "all"){ $farm_filter = ""; } else { $farm_filter = " AND `warehouse` = '$farms'"; }
                                            $trno_list = "";
                                            $sql = "SELECT trnum,COUNT(trnum) as ccount FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'".$farm_filter." AND `dflag` = '0' GROUP BY `trnum` ORDER BY `trnum` ASC";
                                            $query = mysqli_query($conn,$sql);
                                            while($row = mysqli_fetch_array($query)){ if($row['ccount'] == 1){ if($trno_list == ""){ $trno_list = $row['trnum']; } else{ $trno_list = $trno_list."','".$row['trnum']; } } }

                                            $sql = "SELECT * FROM `broiler_purchases` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `trnum` IN ('$trno_list') AND `icode` = '$chick_code' AND `active` = '1'".$farm_filter." AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                                        }
                                        $query = mysqli_query($conn, $sql); $i = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $trnum = $row['trnum'];
                                            $date = $row['date'];
                                            $vcode = $row['vcode'];
                                            $billno = $row['billno'];
                                            $icode = $row['icode'];
                                            $snt_qty = $row['snt_qty'];
                                            $fre_qper = $row['fre_qper'];
                                            $mnu_fqty_flag = $row['mnu_fqty_flag']; if($mnu_fqty_flag == ""){ $mnu_fqty_flag = 0; }
                                            $mortality = $row['mort'];
                                            $shortage = $row['shortage'];
                                            $weeks = $row['weeks'];
                                            $excess_qty = $row['excess_qty'];
                                            $rcd_qty = $row['rcd_qty'];
                                            $fre_qty = $row['fre_qty'];
                                            $rate = $row['rate'];
                                            $dis_per = $row['dis_per'];
                                            $dis_amt = $row['dis_amt'];
                                            $item_tamt = $row['item_tamt'];
                                            $round_off = $row['round_off'];
                                            $finl_amt = $row['finl_amt'];
                                            $remarks = $row['remarks'];
                                            $warehouse = $row['warehouse'];
                                            $vhlcode = $row['vehicle_code'];
                                            $dcode = $row['driver_code'];
                                            
                                    ?>
                                    <div class="row" style="margin-bottom:10px;" id="row_no[<?php echo $i; ?>]">
                                        <div class="form-group">
                                            <?php if($i == 0){ ?><input type="checkbox" name="checkall" id="checkall" class="form-control" style="transform: scale(0.9);" onchange="checkedall()"><?php } ?>
                                            <input type="checkbox" name="cnos[]" id="cnos[<?php echo $i; ?>]" class="form-control" <?php if($i == 0){ echo "style='margin-top:5px;transform: scale(.9);'"; } else{ echo "style='transform: scale(.9);'"; }?> value="<?php echo $i; ?>" >
                                        </div>
                                        <!--<div class="form-group">
                                            <label <?php //if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date[]" id="date[<?php //echo $i; ?>]" class="form-control datepicker" value="<?php //echo date('d.m.Y',strtotime($date)); ?>" style="width:90px;" readonly>
                                        </div>-->
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>trnum</label>
                                            <input type="text" name="trnum[]" id="trnum[<?php echo $i; ?>]" class="form-control" value="<?php echo $trnum; ?>" style="width:100px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Bill No.</label>
                                            <input type="text" name="billno[]" id="billno[<?php echo $i; ?>]" class="form-control" value="<?php echo $billno; ?>" style="width:60px;" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Sent Qty<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="snt_qty[]" id="snt_qty[<?php echo $i; ?>]" class="form-control" value="<?php echo $snt_qty; ?>" placeholder="0.00" style="width:60px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Free Qty %<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="fre_qper[]" id="fre_qper[<?php echo $i; ?>]" class="form-control" value="<?php echo $fre_qper; ?>" placeholder="0.00" style="width:80px;" onkeyup="validatenum(this.id);upd_mnhfree_qty_flag(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Mortality</label>
                                            <input type="text" name="mortality[]" id="mortality[<?php echo $i; ?>]" class="form-control" value="<?php echo $mortality; ?>"  placeholder="0.00" style="width:60px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Shortage</label>
                                            <input type="text" name="shortage[]" id="shortage[<?php echo $i; ?>]" class="form-control" value="<?php echo $shortage; ?>"  placeholder="0.00" style="width:60px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Weaks</label>
                                            <input type="text" name="weeks[]" id="weeks[<?php echo $i; ?>]" class="form-control" value="<?php echo $weeks; ?>"  placeholder="0.00" style="width:60px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Excess<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="excess_qty[]" id="excess_qty[<?php echo $i; ?>]" class="form-control" value="<?php echo $excess_qty; ?>" placeholder="0.00" style="width:60px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Rcv Qty<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rcd_qty[]" id="rcd_qty[<?php echo $i; ?>]" class="form-control" value="<?php echo $rcd_qty; ?>" placeholder="0.00" style="width:60px;" onkeyup="validatenum(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Free Qty</label>
                                            <input type="text" name="fre_qty[]" id="fre_qty[<?php echo $i; ?>]" class="form-control" value="<?php echo $fre_qty; ?>" placeholder="0.00" style="width:60px;" onkeyup="validatenum(this.id);upd_mnhfree_qty_flag(this.id);calculate_total_amt(this.id);" onchange="validateamount(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Rate<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="rate[]" id="rate[<?php echo $i; ?>]" class="form-control" value="<?php echo $rate; ?>" placeholder="0.00" style="width:60px;" onkeyup="validatenum5(this.id);calculate_total_amt(this.id);fetch_discount_amount(this.id);" onchange="validateamount5(this.id);" >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Disc. %</label>
                                            <input type="text" name="dis_per[]" id="dis_per[<?php echo $i; ?>]" class="form-control" value="<?php echo $dis_per; ?>" placeholder="%" style="width:60px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);">
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Disc. &#8377</label>
                                            <input type="text" name="dis_amt[]" id="dis_amt[<?php echo $i; ?>]" class="form-control" value="<?php echo $dis_amt; ?>" placeholder="&#8377" style="width:60px;" onkeyup="validatenum(this.id);fetch_discount_amount(this.id);" onchange="validateamount(this.id);">
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>GST</label>
                                            <select name="gst_per[]" id="gst_per[<?php echo $i; ?>]" class="form-control select2" onchange="calculate_total_amt(this.id)" style="padding:0;padding-left:2px;height:23px;width:90px;font-size:10px;">
                                                <option value="select">select</option>
                                                <?php foreach($gst_code as $gsts){ $gst_cval = $gsts."@".$gst_value[$gsts]; ?><option value="<?php echo $gst_cval; ?>"><?php echo $gst_name[$gsts]; ?></option><?php } ?>
                                        </select>
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Amount</label>
                                            <input type="text" name="item_tamt[]" id="item_tamt[<?php echo $i; ?>]" class="form-control" value="<?php echo $item_tamt; ?>" placeholder="0.00" style="width:90px;" readonly >
                                        </div>
                                        <div class="form-group">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>Sector/Farm<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse[]" id="warehouse[<?php echo $i; ?>]" class="form-control select2" style="width:120px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $whouse_code){ ?><option value="<?php echo $whouse_code; ?>" <?php if($warehouse == $whouse_code){ echo "selected"; } ?>><?php echo $sector_name[$whouse_code]; ?></option><?php } ?>
                                        </select>
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label <?php if($i > 0){ echo 'class="labelrow" style="display:none;"'; } ?>>D</label>
                                            <input type="text" name="tinfo[]" id="tinfo[<?php echo $i; ?>]" class="form-control" value="<?php echo $date."@".$vcode."@".$icode."@".$remarks."@".$vhlcode."@".$dcode; ?>" style="width:10px;" readonly >
                                        </div>
                                        <div class="form-group" style="visibility:hidden;">
                                            <label>MF</label>
                                            <input type="text" name="mnu_fqty_flag[]" id="mnu_fqty_flag[<?php echo $i; ?>]" class="form-control" value="<?php echo $mnu_fqty_flag; ?>" style="width:20px;" readonly />
                                        </div>
                                    </div>
                                    <?php
                                    $i++;
                                    }
                                    ?>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $i - 1; ?>">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Update</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                                }
                                ?>
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
                window.location.href = 'broiler_display_purchase2_chicks23.php?ccid='+ccid;
            }
            function checkval2(){
                var sec_flag = document.getElementById("sec_flag").value;
                var l = true;
                if(sec_flag == 0 || sec_flag == "0"){
                    var dcount = document.getElementById("dcount").value;
                    if(dcount == 1 || dcount == "1"){
                        l = false;
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    alert("To Date must be <= 10 days based on From Date");
                    return false;
                }
            }
			function checktodates(){
				var fdate = document.getElementById("fdate").value;
                var tdate = document.getElementById("tdate").value;
				var inv_items = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_check_dateallocation.php?fdate="+fdate+"&tdate="+tdate+"&type=ftgapdays&days=10";
                //window.open(url);
                var asynchronous = true;
                inv_items.open(method, url, asynchronous);
                inv_items.send();
                inv_items.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var fdvals = this.responseText;
                        if(fdvals == 1 || fdvals == "1"){
                            document.getElementById("dcount").value = 1;
                        }
                        else{
                            document.getElementById("dcount").value = 0;
                        }
                    }
                }
			}
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incrs = document.getElementById("incr").value;
                var item_code = warehouse = cnos = ""; var snt_qty = fre_qper = rcd_qty = rate = c = cnos_count = 0;
                var l = true;
                for(var d = 0;d <= incrs;d++){
                    if(l == true){
                        c = d + 1;
                        cnos = document.getElementById("cnos["+d+"]");
                        if(cnos.checked == true){
                            cnos_count++;
                            sup_code = document.getElementById("vcode["+d+"]").value;
                            item_code = document.getElementById("icode["+d+"]").value;
                            snt_qty = document.getElementById("snt_qty["+d+"]").value;
                            rcd_qty = document.getElementById("rcd_qty["+d+"]").value;
                            rate = document.getElementById("rate["+d+"]").value;
                            warehouse = document.getElementById("warehouse["+d+"]").value;
                            if(sup_code.match("select")){
                                alert("Please select Supplier in row:-"+c);
                                document.getElementById("vcode["+d+"]").focus();
                                l = false;
                            }
                            else if(item_code.match("select")){
                                alert("Please select Item in row:-"+c);
                                document.getElementById("icode["+d+"]").focus();
                                l = false;
                            }
                            else if(snt_qty == "" || snt_qty.length == 0 || snt_qty == "0" || snt_qty == 0 || snt_qty == "0.00"){
                                alert("Please enter Sent Qty in row:-"+c);
                                document.getElementById("snt_qty["+d+"]").focus();
                                l = false;
                            }
                            else if(rcd_qty == "" || rcd_qty.length == 0 || rcd_qty == "0" || rcd_qty == 0 || rcd_qty == "0.00"){
                                alert("Please enter Rcd Qty in row:-"+c);
                                document.getElementById("rcd_qty["+d+"]").focus();
                                l = false;
                            }
                            else if(rate == "" || rate.length == 0 || rate == "0" || rate == 0 || rate == "0.00"){
                                alert("Please enter Rate in row:-"+c);
                                document.getElementById("rate["+d+"]").focus();
                                l = false;
                            }
                            else if(warehouse.match("select")){
                                alert("Please select Sector/Farm in row:-"+c);
                                document.getElementById("warehouse["+d+"]").focus();
                                l = false;
                            }
                            else{ }
                        }
                    }
                }
                if(l == true){
                    if(cnos_count > 0){
                        l = true;
                    }
                    else{
                        alert("Please select atleast one checkbox to make changes");
                        l = false;
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function calculate_total_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var qty_on_sqty_flag = '<?php echo $qty_on_sqty_flag; ?>';

                var sent_qty = document.getElementById("snt_qty["+d+"]").value;
                var fre_qper = document.getElementById("fre_qper["+d+"]").value; if(fre_qper == ""){ fre_qper = 0; }
                var mortality_qty = document.getElementById("mortality["+d+"]").value;
                var shortage_qty = document.getElementById("shortage["+d+"]").value;
                var weeks_qty = document.getElementById("weeks["+d+"]").value;
                var excess_qty = document.getElementById("excess_qty["+d+"]").value;
                if(sent_qty == "" || sent_qty.length == 0 || sent_qty == "0.00" || sent_qty == "0"){ sent_qty = 0; }
                if(mortality_qty == "" || mortality_qty.length == 0 || mortality_qty == "0.00" || mortality_qty == "0"){ mortality_qty = 0; }
                if(shortage_qty == "" || shortage_qty.length == 0 || shortage_qty == "0.00" || shortage_qty == "0"){ shortage_qty = 0; }
                if(weeks_qty == "" || weeks_qty.length == 0 || weeks_qty == "0.00" || weeks_qty == "0"){ weeks_qty = 0; }
                if(excess_qty == "" || excess_qty.length == 0 || excess_qty == "0.00" || excess_qty == "0"){ excess_qty = 0; }

                var fre_qty = fqty_per = 0;
                var mnu_fqty_flag = document.getElementById("mnu_fqty_flag["+d+"]").value; if(mnu_fqty_flag == ""){ mnu_fqty_flag = 0; }
                if(parseInt(mnu_fqty_flag) == 1){
                    fre_qty = document.getElementById("fre_qty["+d+"]").value; if(fre_qty == ""){ fre_qty = 0; }
                }
                else{
                    if(parseFloat(fre_qper) > 0){
                        fqty_per = (parseFloat(fre_qper) / 100);
                        fre_qty = parseFloat((parseFloat(sent_qty) * parseFloat(fqty_per))).toFixed(0);  if(fre_qty == ""){ fre_qty = 0; }
                        document.getElementById("fre_qty["+d+"]").value = parseFloat(fre_qty).toFixed(2);
                    }
                }

                var tot_minusqty_qty = parseFloat(mortality_qty) + parseFloat(shortage_qty) + parseFloat(weeks_qty);
                var tot_rec_qty = parseFloat(sent_qty) - parseFloat(tot_minusqty_qty) + parseFloat(excess_qty) - parseFloat(fre_qty);
                if(tot_rec_qty == "" || tot_rec_qty.length == 0 || tot_rec_qty == "0.00" || tot_rec_qty == "0"){ tot_rec_qty = 0; }
                document.getElementById("rcd_qty["+d+"]").value = parseFloat(tot_rec_qty).toFixed(2);
                
                if(qty_on_sqty_flag == 1 || qty_on_sqty_flag == "1"){ var qty = document.getElementById("snt_qty["+d+"]").value; }
                else{ var qty = document.getElementById("rcd_qty["+d+"]").value; }
                
                var price = document.getElementById("rate["+d+"]").value;
                var dis_amt = document.getElementById("dis_amt["+d+"]").value;
                var gst_per1 = document.getElementById("gst_per["+d+"]").value;
                if(!gst_per1.match("select")){
                    var gst_per2 = gst_per1.split("@");
                    var gst_per = gst_per2[1];
                }
                else{
                    var gst_per = 0; 
                }

                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }
                if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){ dis_amt = 0; }
                if(gst_per == "" || gst_per.length == 0 || gst_per == "0.00" || gst_per == "0"){ gst_per = 0; }

                var total_amt = parseFloat(qty) * parseFloat(price);
                if(dis_amt > 0){
                    total_amt = parseFloat(total_amt) - parseFloat(dis_amt);
                }
                if(gst_per > 0){
                    var gst_value = ((parseFloat(gst_per) / 100) * total_amt);
                    total_amt = parseFloat(total_amt) + parseFloat(gst_value);
                }
                document.getElementById("item_tamt["+d+"]").value = total_amt;
                calculate_netpay();
            }
            function upd_mnhfree_qty_flag(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                if(b[0] == "fre_qper"){
                    document.getElementById("mnu_fqty_flag["+d+"]").value = 0;
                }
                else if(b[0] == "fre_qty"){
                    document.getElementById("mnu_fqty_flag["+d+"]").value = 1;
                }
                else{
                    document.getElementById("mnu_fqty_flag["+d+"]").value = 0;
                }
            }
            function fetch_discount_amount(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var qty_on_sqty_flag = '<?php echo $qty_on_sqty_flag; ?>';
                if(qty_on_sqty_flag == 1 || qty_on_sqty_flag == "1"){ var qty = document.getElementById("snt_qty["+d+"]").value; }
                else{ var qty = document.getElementById("rcd_qty["+d+"]").value; }
                
                var price = document.getElementById("rate["+d+"]").value;

                if(qty == "" || qty.length == 0 || qty == "0.00" || qty == "0"){ qty = 0; }
                if(price == "" || price.length == 0 || price == "0.00" || price == "0"){ price = 0; }

                var total_amt = parseFloat(qty) * parseFloat(price);

                if(total_amt == "" || total_amt.length == 0 || total_amt == "0.00" || total_amt == "0"){ total_amt = 0; }
                if(b[0].match("dis_per")){
                    var dis_per = document.getElementById("dis_per["+d+"]").value;
                    if(dis_per == "" || dis_per.length == 0 || dis_per == "0.00" || dis_per == "0"){ }
                    else{
                        var dis_value = ((parseFloat(dis_per) / 100) * total_amt);
                        if(dis_value == "NaN" || dis_value.length == 0 || dis_value == 0){ dis_value = ""; }
                        document.getElementById("dis_amt["+d+"]").value = dis_value;
                        calculate_total_amt(a);
                    }
                    
                }
                else{
                    var dis_amt = document.getElementById("dis_amt["+d+"]").value;
                    if(dis_amt == "" || dis_amt.length == 0 || dis_amt == "0.00" || dis_amt == "0"){ }
                    else{
                        var dis_per = ((parseFloat(dis_amt) * 100) / total_amt);
                        if(dis_per == "NaN" || dis_per.length == 0 || dis_per == 0){ dis_per = ""; }
                        document.getElementById("dis_per["+d+"]").value = dis_per.toFixed(2);
                        calculate_total_amt(a);
                    }
                }
            }
            function broiler_check_sectype(){
                var farms = document.getElementById("farms").value;
                var sec_flag = 0; var scode = "";
                <?php
                foreach($sector_code as $scode){
                ?>
                    var scode = '<?php echo $scode; ?>';
                    if(farms == scode){
                        sec_flag = '<?php echo $sec_flag[$scode]; ?>';
                    }
                <?php
                }
                ?>
                document.getElementById("sec_flag").value = sec_flag;
            }
			function checkedall(){
                var incr = '<?php echo $i - 1; ?>';
				var a = document.getElementById("checkall");
                var c = "";
				if(a.checked == true){
					for(var b = 0;b <= incr;b++){
					    c = document.getElementById("cnos["+b+"]");
                        c.checked = true;
					}
				}
				else{
					for(var b = 0;b <= incr;b++){
					    c = document.getElementById("cnos["+b+"]");
						c.checked = false;
					}
				}
			}
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function validatenum5(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount5(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(5); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){
                // window.screen.availHeight window.screen.availWidth
                //alert(window.screen.availWidth);
                if(window.screen.availWidth <= 850){
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; }
                }
                else{
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; }
                }
            }, 1000);
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