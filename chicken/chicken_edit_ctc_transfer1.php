<?php
//chicken_edit_ctc_transfer1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $fdate = date("Y-m-d"); $tcds_per = 0;
    $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$fdate' AND `tdate` >= '$fdate' AND `type` = 'TCS' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcds_per = $row['tcds']; }
    
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql); $ppzflag = $ifwt = $ifbw = $ifjbw = $ifjbwen = $jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $ppzflag = $row['ppzflag']; $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; }
    if($ifjbwen == 1 || $ifjbw == 1){ $jals_flag = 1; } if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ $birds_flag = 1; } if($ifjbwen == 1){ $tweight_flag = $eweight_flag = 1; } if($ppzflag == ""){ $ppzflag = 0; }

    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
    $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `transport_flag` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $tport_code = $tport_name = array();
    while($row = mysqli_fetch_assoc($query)){ $tport_code[$row['code']] = $row['code']; $tport_name[$row['code']] = $row['description']; }

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            $sql = "SELECT * FROM `chicken_ctc_stktransfer` WHERE `trnum` = '$ids' AND `flag` = '0' AND `dflag` = '0'  AND `trlink` = 'chicken_display_ctc_transfer1.php'";
            $query = mysqli_query($conn,$sql); $c = 0;
            while($row = mysqli_fetch_assoc($query)){
                $date = $row['date'];
                $trnum = $row['trnum'];
                $bookinvoice = $row['bill_no'];

                $vcode[$c] = $row['from_vcode'];
                $vcode2[$c] = $row['to_vcode'];
                $itemcode[$c] = $row['item'];
                $jals[$c] = round($row['jals'],5);
                $birds[$c] = round($row['birds'],5);
                $totalweight[$c] = round($row['tweight'],5);
                $emptyweight[$c] = round($row['eweight'],5);
                $netweight[$c] = round($row['nweight'],5);
                $price[$c] = round($row['from_price'],5);
                $price2[$c] = round($row['to_price'],5);
                $amount[$c] = round($row['from_amt'],5);
                $amount2[$c] = round($row['to_amt'],5);
                $remarks[$c] = $row['remarks'];
                
                
                $warehouse = $row['warehouse'];
                $driver = $row['driver'];
                $vehicle = $row['vehicle'];
                $c++;
            } $c = $c - 1;
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Sales</div>
                <form action="chicken_modify_ctc_transfer1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="date" id="date" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per(this.id);" readonly />
                            </div>
                            <!-- <div class="form-group" style="width:290px;">
                                <label for="vcode">Customer<b style="color:red;">&nbsp;*</b></label>
                                <select name="vcode[]" id="vcode[<?php echo $c; ?>]" class="form-control select2" style="width:280px;" onchange=""><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($vcode[$c] == $scode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?></select>
                            </div> -->
                            <div class="form-group" style="width:110px;">
                                <label for="bookinvoice">Dc No.</label>
                                <input type="text" name="bookinvoice" id="bookinvoice" class="form-control" value="<?php echo $bookinvoice; ?>" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:290px;">
                                <label for="">Sector</label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;">
                                    <option value="select">-select-</option>
                                    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($warehouse == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="vehicle">Vehicle</label>
                                <input type="text" name="vehicle" id="vehicle" class="form-control" value="<?php echo $vehicle; ?>" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="driver">Driver</label>
                                <input type="text" name="driver" id="driver" class="form-control" value="<?php echo $driver; ?>" style="width:100px;" />
                            </div>
                          
                        </div>
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>From Customer<b style="color:red;">&nbsp;*</b></th>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<th>Jals</th>'; }
                                        if((int)$birds_flag == 1){ echo '<th>Birds</th>'; }
                                        if((int)$tweight_flag == 1){ echo '<th>T. Weight</th>'; }
                                        if((int)$eweight_flag == 1){ echo '<th>E. Weight</th>'; }
                                        ?>
                                        <th>N. Weight<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th>To Customer<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                        <th style="width:70px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php $incr = $c; for($c = 0;$c <= $incr;$c++){  ?>
                                    <tr style="margin:5px 0px 5px 0px;" id="row_no[<?php echo $c; ?>]">
                                        <td><select name="vcode[]" id="vcode[<?php echo $c; ?>]" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($vcode[$c] == $scode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="itemcode[]" id="itemcode[<?php echo $c; ?>]" class="form-control select2" style="width:180px;" onchange="update_row_fields(this.id);fetch_latest_customer_paperrate(this.id);"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($itemcode[$c] == $scode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="jals[]" id="jals['.$c.']" class="form-control text-right" value="'.$jals[$c].'" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="birds[]" id="birds['.$c.']" class="form-control text-right" value="'.$birds[$c].'" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$tweight_flag == 1){ echo '<td><input type="text" name="tweight[]" id="tweight['.$c.']" class="form-control text-right" value="'.$totalweight[$c].'" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$eweight_flag == 1){ echo '<td><input type="text" name="eweight[]" id="eweight['.$c.']" class="form-control text-right" value="'.$emptyweight[$c].'" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>'; }
                                        ?>
                                        <td><input type="text" name="nweight[]" id="nweight[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $netweight[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $price[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="amount[]" id="amount[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $amount[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculaterate(this.id);" onchange="validate_amount(this.id);calculaterate(this.id);" /></td>
                                        <td><select name="vcode2[]" id="vcode2[<?php echo $c; ?>]" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($vcode2[$c] == $scode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="price2[]" id="price2[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $price2[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="amount2[]" id="amount2[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $amount2[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculaterate(this.id);" onchange="validate_amount(this.id);calculaterate(this.id);" /></td>
                                        <td><textarea name="remark[]" id="remark[<?php echo $c; ?>]" class="form-control text-right" style="width:90px;" ><?php echo $remarks[$c]; ?></textarea></td>  
                                       
                                    </tr>
                                    <?php } ?>
                                </tbody>
                               
                            </table>
                        </div><br/>
                        
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $incr; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Update</button>&ensp;
                                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                function return_back(){
                    window.location.href = "chicken_display_ctc_transfer1.php";
                }
                function calculaterate(id){
			 	var ab = id.split("["); var ad = ab[1].split("]"); var ac = ad[0];
			 	var tamt = document.getElementById("amount["+ac+"]").value;
			 	var nwal = document.getElementById("nweight["+ac+"]").value;
                if(tamt == "" ){ tamt = 0; }
                if(nwal == "" ){ nwal = 0; }
                if(tamt != 0 && nwal != 0){
                    var rate = parseFloat(tamt) / parseFloat(nwal);
                    console.log(rate);
                    document.getElementById("price["+ac+"]").value = parseFloat(rate).toFixed(2);
                    
                }
			 	
			    

			 }
			 	
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var date = document.getElementById("date").value;
                    var vcode = document.getElementById("vcode").value;
                    var l = true;

                    if(date == ""){
                        alert("Please select date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(vcode == "select"){
                        alert("Please select Customer");
                        document.getElementById("vcode").focus();
                        l = false;
                    }
                    else{
                        var itemcode = warehouse = ""; var c = nweight = price = amount = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                itemcode = document.getElementById("itemcode["+d+"]").value;
                                nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                                price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                                amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                                warehouse = document.getElementById("warehouse["+d+"]").value;

                                if(itemcode == "select"){
                                    alert("Please select Item in row: "+c);
                                    document.getElementById("itemcode["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(nweight) == 0){
                                    alert("Please enter net weight in row: "+c);
                                    document.getElementById("nweight["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(price) == 0){
                                    alert("Please enter price in row: "+c);
                                    document.getElementById("price["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(amount) == 0){
                                    alert("Please enter price/Weight in row: "+c);
                                    document.getElementById("amount["+d+"]").focus();
                                    l = false;
                                }
                                else if(warehouse == "select"){
                                    alert("Please select Warehouse in row: "+c);
                                    document.getElementById("warehouse["+d+"]").focus();
                                    l = false;
                                }
                                else{ }
                            }
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
                function update_row_fields(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var icode = document.getElementById("itemcode["+d+"]");
                    var iname = icode.options[icode.selectedIndex].text;
                    var bird_flag = iname.search(/Birds/i);

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    if(parseInt(bird_flag) > 0){
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "visible"; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("tweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("eweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1){ document.getElementById("nweight["+d+"]").readOnly = true; }
                    }
                    else{
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "hidden"; document.getElementById("jals["+d+"]").value = ""; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "hidden"; document.getElementById("birds["+d+"]").value = ""; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("tweight["+d+"]").style.visibility = "hidden"; document.getElementById("tweight["+d+"]").value = ""; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("eweight["+d+"]").style.visibility = "hidden"; document.getElementById("eweight["+d+"]").value = ""; }
                        document.getElementById("nweight["+d+"]").readOnly = false;
                    }
                    
                }
                
                function fetch_latest_customer_paperrate(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var date = document.getElementById("date").value;
                    var vcode = document.getElementById("vcode").value;
                    var itemcode = document.getElementById("itemcode["+d+"]").value;
                    document.getElementById("price["+d+"]").value = "";

                    if(date == ""){
                        alert("Please select Date");
                        document.getElementById("date").focus();
                    }
                    else if(vcode == "select"){
                        alert("Please select Customer");
                        document.getElementById("vcode").focus();
                    }
                    else if(itemcode == "select"){
                        alert("Please select Item");
                        document.getElementById("itemcode["+d+"]").focus();
                    }
                    else{
                        var inv_items = new XMLHttpRequest();
                        var method = "GET";
                        var url = "chicken_fetch_latest_cuspaperrate.php?date="+date+"&vcode="+vcode+"&icode="+itemcode+"&row_cnt="+d;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var cus_dt1 = this.responseText;
                                var cus_dt2 = cus_dt1.split("@");
                                var price = cus_dt2[0];
                                var rows = cus_dt2[1];
                                if(price != ""){
                                    document.getElementById("price["+rows+"]").value = parseFloat(price).toFixed(2);
                                }
                            }
                        }
                    }
                }
                
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
