<?php
//broiler_edit_salesorder.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['salesorder'];
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
        $today = date("Y-m-d"); $fdate = date("d.m.Y",strtotime($today));
        
        $ven_code = $ven_name = $item_code = $item_name = $sector_code = $sector_name = $gst_code = $gst_name = $gst_value = array();
		$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $ven_code[$row['code']] = $row['code']; $ven_name[$row['code']] = $row['name']; }
				
		$sql = "SELECT * FROM `broker_details` WHERE `btype` IN ('CB','BB') AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $brk_code[$row['code']] = $row['code']; $brk_name[$row['code']] = $row['name']; }
				
		$sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		
        while($row = mysqli_fetch_assoc($query)){ $item_code_li[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
				
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
		
        $sql = "SELECT * FROM `broiler_batch` WHERE `gc_flag` = '0' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $farm_list = "";
        while($row = mysqli_fetch_assoc($query)){ if($farm_list == ""){ $farm_list = $row['farm_code']; } else{ $farm_list = $farm_list."','".$row['farm_code']; } }
        
		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `dflag` = '0' AND `code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
				
		$sql = "SELECT * FROM `tax_details` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $gst_code[$row['code']] = $row['code']; $gst_name[$row['code']] = $row['gst_type']; $gst_value[$row['code']] = $row['value']; }
	
        $so_brkselect_flag = $so_brkmdt_flag = 0;
		$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE '%Sale Cycle%' AND `field_function` LIKE '%SO:%'";
        $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
        if($jcount > 0){
            while($row = mysqli_fetch_assoc($query)){
                if($row['field_function'] == "SO:Broker selection"){ $so_brkselect_flag = $row['flag']; }
                else if($row['field_function'] == "SO:Broker Mandatory"){ $so_brkmdt_flag = $row['flag']; }
            }
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
            font-size: 15px;
        }
        .form-control{
            padding-left: 2px;
            padding-right: 0px;
        }
        .form-group{
            margin: 0 3px;
        }
        thead tr th {
            font-size: 15px;
        }
        .num_field input[type=text]{
            padding: 0;
            padding-right:2px;
            text-align:right;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $ids = $_GET['trnum'];
        $id = $incr = $prefix = $trnum = $link_trnum = $link_id = $date = $sup_code = $b_code = $billno = $item_code = $req_qty = $rcvd_qty = $free_qty = $rate = $amount = $disc_per = $disc_amt = $gst_code = $gst_per = $gst_amt = $item_amt = $inv_amt = $warehouse = $batch_code = $remarks = $vehicle_code = $driver_code = $driver_mobile = $aut_flag = $aut_emp = $aut_time = $avl_qty = $wpi_flag = $ge_flag = $flag = $active = $dflag = $trlink = $addedemp = $addedtime = array();
        $sql = "SELECT * FROM `broiler_sc_saleorder` WHERE `trnum` = '$ids' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $i = 0;
        while($row = mysqli_fetch_assoc($query)){
            //$key = $row['link_id']."@".$row['link_trnum']."@".$row['item_code'];
            $id[$i] = $row['id'];
            $incr[$i] = $row['incr'];
            $prefix[$i] = $row['prefix'];
            $trnum[$i] = $row['trnum'];
            $date[$i] = $row['date'];
            $sup_code[$i] = $row['vcode'];
            $itm_code[$i] = $row['item_code'];
            $box_qty[$i] = round($row['box_crate_qty'],5);
            $rcvd_qty[$i] = round($row['rcvd_qty'],5);
            $ddate_li[$i] = $row['delivery_date'];
            $remarks[$i] = $row['remarks'];
            $i++;
        }
        $i = $i - 1;
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Sale Order</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_modify_salesorder_box_crate.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        
                                        <div class="form-group">
                                            <label>trnum</label>
                                            <input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $trnum[0]; ?>" style="width:130px;" readonly >
                                        </div>&ensp;
                                       
                                        <div class="form-group">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control " value="<?php echo date('d.m.Y',strtotime($date[0])); ?>" style="width:90px;" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <?php if($so_brkselect_flag == 1){ ?>
                                        <div class="form-group" style="width:290px;">
                                            <label>Broker<?php if($so_brkmdt_flag == 1){ ?><b style="color:red;">&nbsp;*</b><?php } ?></label>
                                            <select name="bcode" id="bcode" class="form-control select2" style="width:280px;">
                                                <option value="none">-None-</option>
                                                <?php
                                                foreach($brk_code as $bcode){
                                                ?>
                                                <option value="<?php echo $bcode; ?>" <?php if($b_code[0] == $bcode){ echo "selected"; } ?>><?php echo $brk_name[$bcode]; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>&ensp;
                                        <?php } ?>
                                    </div>
                                    <br/>
                                    <div class="p-0 row">
                                        <table>
                                            <thead id="row_head">
                                                <tr id="row_head">
                                                    <th style="width: 200px;padding-left:30px;">Customer<b style="color:red;">&nbsp;*</b></th>
                                                    <th style="width: 200px;padding-left:30px;">Item<b style="color:red;">&nbsp;*</b></th>
                                                    <th>Box/Crates<b style="color:red;">&nbsp;*</b></th>
                                                    <th>Order Qty<b style="color:red;">&nbsp;*</b></th>
                                                     <th style="width: 200px;padding-left:30px;">Sector<b style="color:red;">&nbsp;*</b></th>
                                                    <th><label>Delivery Date<b style="color:red;">&nbsp;*</b></label></th>
												    
                                                   
                                                </tr>
                                            </thead>
                                            <tbody id="row_body">
                                            <?php
                                                for($c = 0;$c <= $i;$c++){
                                                ?>
                                                <tr id="row_no[<?php echo $c; ?>]" class="num_field">
                                                <td style="width: 200px;padding-left:30px;">
                                                <select name="vcode[]" id="vcode[<?php echo $c; ?>]" class="form-control select2" style="width:200px;" >
                                                <option value="select">select</option>
                                                <?php foreach($ven_code as $vcode){ ?>
                                                    <option value="<?php echo $vcode; ?>" <?php if($sup_code[$c] == $vcode){ echo "selected"; } ?>><?php echo $ven_name[$vcode]; ?></option>
                                                <?php } ?>
                                                </select></td>
                                                
                                                    <td style="width: 200px;padding-left:30px;">
                                                    <select name="item_code[]" id="item_code[<?php echo $c; ?>]" class="form-control select2" style="width:200px;">
                                                    <option value="select">select</option>
                                                    <?php foreach($item_code_li as $icode){ ?>
                                                        <option value="<?php echo $icode; ?>" <?php if($itm_code[$c] == $icode){ echo "selected"; } ?>><?php echo $item_name[$icode]; ?></option>
                                                    <?php } ?>
                                                </select></td>
                                                    <td style="width:85px;"><input type="text" name="box_qty[]" id="box_qty[<?php echo $c; ?>]" class="form-control" value="<?php echo $box_qty[$c]; ?>" onkeyup="validatenum(this.id);" style="width:80px;" /></td>
                                                    <td style="width:85px;"><input type="text" name="rcvd_qty[]" id="rcvd_qty[<?php echo $c; ?>]" class="form-control" value="<?php echo $rcvd_qty[$c]; ?>" onkeyup="validatenum(this.id);calculate_total_qty(this.id);" style="width:80px;" /></td>
                                                     <td style="width: 200px;padding-left:30px;"><select name="warehouse[]" id="warehouse[0]" class="form-control select2" style="width:200px;"><option value="select">select</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td> 
                                                    <td style="width:120px;"><input type="date" name="ddate[]" id="ddate[<?php echo $c; ?>]" class="form-control" value="<?php echo date('Y-m-d',strtotime($ddate_li[$c])); ?>" style="width:120px;"/></td>
                                                   
                                                </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row" style="margin-bottom:3px;">
                                        <div class="col-md-4 form-group"></div>
                                        <div class="col-md-4 form-group">
                                            <label>Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"><?php echo $remarks[0]; ?></textarea>
                                        </div>
                                        <div class="col-md-4 form-group"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>Id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:visible;"-->
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
                window.location.href = 'broiler_display_salesorder.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; 
                document.getElementById("submit").style.visibility = "hidden";
                var l = true;
                
                
                    var incrs = document.getElementById("incr").value;
                    var vcod = slno = icode = rcvd_qty = rate = warehouse = ""; var amount1 = item_amt = 0;
                    for(var d = 0;d <= incrs;d++){
                        if(l == true){
                            icode = document.getElementById("item_code["+d+"]").value;
                            rcvd_qty = document.getElementById("rcvd_qty["+d+"]").value;
                            box_qty = document.getElementById("box_qty["+d+"]").value;
                            
                            vcode = document.getElementById("vcode["+d+"]").value;
                            ddate = document.getElementById("ddate["+d+"]").value;
                            
                            if(vcode.match("select")){
                                alert("Please select Customer");
                                document.getElementById("vcode["+d+"]").focus();
                                l = false;
                            }
                            else if(icode.match("select")){
                                alert("Please select Item");
                                document.getElementById("item_code["+d+"]").focus();
                                l = false;
                            }
                            
                            else if((box_qty.length == 0 || box_qty == 0 || box_qty == "") &&
                             (rcvd_qty.length == 0 || rcvd_qty == 0 || rcvd_qty == "")){
                                alert("Please enter either Box/Crates Qty or Order Qty.");
                                document.getElementById("box_qty["+d+"]").focus();
                                l = false;
                            }
                            
                            else if(ddate.length == 0 || ddate == 0 || ddate == ""){
                                alert("Please select Delivery Date");
                                document.getElementById("ddate["+d+"]").focus();
                                l = false;
                            }
                            
                            else{ }
                        }
                    
                }
                
                if(l == true){
                    document.getElementById("submit").disabled = true;
                    return true;
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
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']" class="num_field">';
                html += '<td style="width:160px;"><select name="item_code[]" id="item_code['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></td>';
                html += '<td style="width:85px;"><input type="text" name="rcvd_qty[]" id="rcvd_qty['+d+']" class="form-control" onkeyup="validatenum(this.id);calculate_total_qty(this.id);" style="width:80px;" /></td>';
                html += '<td style="width:65px;"><input type="text" name="free_qty[]" id="free_qty['+d+']" class="form-control" onkeyup="validatenum(this.id);calculate_total_qty(this.id);" style="width:60px;" /></td>';
                html += '<td style="width:85px;"><input type="text" name="rate[]" id="rate['+d+']" class="form-control" onkeyup="validatenum(this.id);calculate_total_qty(this.id);" style="width:80px;" /></td>';
                html += '<td style="width:85px;"><input type="text" name="amount1[]" id="amount1['+d+']" class="form-control" style="width:80px;" readonly /></td>';
                html += '<td style="width:45px;"><input type="text" name="disc_per[]" id="disc_per['+d+']" class="form-control" onkeyup="validatenum(this.id);calculate_discount_amt(this.id);" style="width:40px;" /></td>';
                html += '<td style="width:85px;"><input type="text" name="disc_amt[]" id="disc_amt['+d+']" class="form-control" onkeyup="validatenum(this.id);calculate_discount_amt(this.id);" style="width:80px;" /></td>';
                html += '<td style="width:105px;"><select name="gst_per[]" id="gst_per['+d+']" class="form-control select2" onchange="calculate_total_qty(this.id)" style="width:100px;"><option value="select">select</option><?php foreach($gst_code as $gsts){ $gst_cval = $gsts."@".$gst_value[$gsts]; ?><option value="<?php echo $gst_cval; ?>"><?php echo $gst_name[$gsts]; ?></option><?php } ?></select></td>';
                html += '<td style="width:85px;"><input type="text" name="gst_amt[]" id="gst_amt['+d+']" class="form-control" style="width:80px;" readonly /></td>';
                html += '<td style="width:85px;"><input type="text" name="item_amt[]" id="item_amt['+d+']" class="form-control" style="width:80px;" readonly /></td>';
                html += '<td style="width:160px;"><select name="warehouse[]" id="warehouse['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '</tr>';
                $('#row_body').append(html); $('.select2').select2(); $('.datepicker').datepicker();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_netpay();
            }
            function calculate_total_qty(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var rcvd_qty = document.getElementById("rcvd_qty["+d+"]").value; if(rcvd_qty == ""){ rcvd_qty = 0; }
                var rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                var disc_amt = document.getElementById("disc_amt["+d+"]").value; if(disc_amt == ""){ disc_amt = 0; }
                var amount1 = parseFloat(rcvd_qty) * parseFloat(rate);
                var item_amt = parseFloat(amount1) - parseFloat(disc_amt);
                var gst_per1 = document.getElementById("gst_per["+d+"]").value;
                if(!gst_per1.match("select")){
                    var gst_per2 = gst_per1.split("@");
                    var gst_per = gst_per2[1];
                }
                else{
                    var gst_per = 0; 
                }
                if(gst_per == ""){ gst_per = 0; }
                var gst_amt = 0;
                if(gst_per > 0){
                    gst_amt = ((parseFloat(gst_per) / 100) * item_amt);
                    item_amt = parseFloat(item_amt) + parseFloat(gst_amt);
                }
                document.getElementById("amount1["+d+"]").value = parseFloat(amount1).toFixed(2);
                document.getElementById("gst_amt["+d+"]").value = parseFloat(gst_amt).toFixed(2);
                document.getElementById("item_amt["+d+"]").value = parseFloat(item_amt).toFixed(2);
            }
            
            function calculate_discount_amt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var qty = document.getElementById("rcvd_qty["+d+"]").value; if(qty == ""){ qty = 0; }
                var price = document.getElementById("rate["+d+"]").value; if(price == ""){ price = 0; }
                var amt = parseFloat(qty) * parseFloat(price); if(amt == ""){ amt = 0; }
                
                var disc_per = disc_amt = 0;

                if(b[0].match("disc_per")){
                    disc_per = document.getElementById("disc_per["+d+"]").value; if(disc_per == ""){ disc_per = 0; }
                    var disc_amt = ((parseFloat(disc_per) / 100) * amt); if(disc_amt == ""){ disc_amt = 0; }
                    document.getElementById("disc_amt["+d+"]").value = parseFloat(disc_amt).toFixed(2);
                    calculate_total_qty(a);
                }
                else{
                    disc_amt = document.getElementById("disc_amt["+d+"]").value; if(disc_amt == ""){ disc_amt = 0; }
                    if(parseFloat(amt) > 0){ disc_per = ((parseFloat(disc_amt) * 100) / amt); } if(disc_per == ""){ disc_per = 0; }
                    document.getElementById("disc_per["+d+"]").value = parseFloat(disc_per).toFixed(2);
                    calculate_total_qty(a);
                }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){
                // window.screen.availHeight window.screen.availWidth
                if(window.screen.availWidth <= 400){
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