<?php
//chicken_edit_generalpurchase8.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql);$jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = (int)$row['jals_flag']; $birds_flag = (int)$row['birds_flag']; $tweight_flag = (int)$row['tweight_flag']; $eweight_flag = (int)$row['eweight_flag']; }

        $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Feed%' AND `active` = '1'";
        $query = mysqli_query($conn,$sql); $fccode = array();
        while($row = mysqli_fetch_assoc($query)){
            $fccode[$row['code']] = $row['code'];
        }
        $feedcodes = implode("','",$fccode);


    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `category` IN ('$feedcodes') ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }
    
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'chicken_display_generalpurchase8.php' AND `field_function` = 'Amount' AND `flag` = '1' ORDER BY `id` ASC";
    $query = mysqli_query($conn,$sql); $amt_flag = mysqli_num_rows($query);

    //Fetch Column From CoA Table
    $sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `flag`"; mysqli_query($conn,$sql); }
    if(in_array("transport_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `transport_flag` INT(100) NOT NULL DEFAULT '0' AFTER `mobile_no`"; mysqli_query($conn,$sql); }

    $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `transport_flag` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $transporter_code = $transporter_name = array();
    while($row = mysqli_fetch_assoc($query)){ $transporter_code[$row['code']] = $row['code']; $transporter_name[$row['code']] = $row['description']; }

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            $sql = "SELECT * FROM `pur_purchase` WHERE `invoice` = '$ids' AND `trlink` = 'chicken_display_generalpurchase8.php' ORDER BY `id` ASC";
            $query = mysqli_query($conn,$sql); $c = 0; $totalamt = array();
            while($row = mysqli_fetch_assoc($query)){
                $date = $row['date'];
                $invoice = $row['invoice'];
                $bookinvoice = $row['bookinvoice'];
                $vendorcode = $row['vendorcode'];
                $itemcode[$c] = $row['itemcode'];
                $jals[$c] = round($row['jals'],2);
                $birds[$c] = round($row['birds'],2);
                $totalweight[$c] = round($row['totalweight'],2);
                $emptyweight[$c] = round($row['emptyweight'],2);
                $netweight[$c] = round($row['netweight'],2);
                $itemprice[$c] = round($row['itemprice'],2);
                $totalamt[$c] = round($row['totalamt'],2);
                $warehouse[$c] = $row['warehouse'];
                $tcdsper = round($row['tcdsper'],2);
                $tcds_type1 = $row['tcds_type1'];
                $tcds_type2 = $row['tcds_type2'];
                $tcdsamt = round($row['tcdsamt'],2);
                $net_amt1 = round($row['net_amt1'],2);
                $tport_code = $row['transporter_code'];
                $freight_amount = round($row['freight_amount'],2);
                $roundoff_type1 = $row['roundoff_type1'];
                $roundoff_type2 = $row['roundoff_type2'];
                $roundoff = round($row['roundoff'],2);
                $finaltotal = round($row['finaltotal'],2);
                $remarks = $row['remarks'];
                $vehiclecode = $row['vehiclecode'];
                $drivercode = $row['drivercode'];
                $nof_bags[$c] = $row['nof_bags'];
                $bags_kg[$c] = $row['bags_kg'];
                $c++;
            } $c = $c - 1;

            $tcds_per = 0;
            $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $tcds_per = $row['tcds']; }
            
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Purchase</div>
                <form action="chicken_modify_generalpurchase8.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row justify-content-center align-items-center">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="date" id="date" class="form-control pur_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per(this.id);" readonly />
                            </div>
                            <div class="form-group" style="width:130px;">
                                <label for="trnum">Invoice</label>
                                <input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $invoice; ?>" style="width:120px;" readonly />
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="bookinvoice">DC No.</label>
                                <input type="text" name="bookinvoice" id="bookinvoice" class="form-control" value="<?php echo $bookinvoice; ?>" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:290px;">
                                <label for="vcode">Supplier<b style="color:red;">&nbsp;*</b></label>
                                <select name="vcode" id="vcode" class="form-control select2" style="width:280px;" onchange="fetch_supplier_outstanding();">
                                    <option value="select">-select-</option>
                                    <?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($vendorcode == $scode){ echo "selected"; } ?>><?php echo $sup_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="vehiclecode">Vehicle</label>
                                <input type="text" name="vehiclecode" id="vehiclecode" value="<?php echo $vehiclecode; ?>" class="form-control" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:110px;">
                                <label for="drivercode">Driver</label>
                                <input type="text" name="drivercode" id="drivercode" value="<?php echo $drivercode; ?>" class="form-control" style="width:100px;" />
                            </div>
                            <div class="form-group" style="width:140px;visibility:hidden;">
                                <label for="out_balance">Balance</label>
                                <input type="text" name="out_balance" id="out_balance" class="form-control text-right" style="width:130px;" readonly />
                            </div>
                        </div>
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <th>Bags<b style="color:red;">&nbsp;*</b></th>
                                        <th>Kgs</th>
                                        <th>Rate<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <th>Warehouse<b style="color:red;">&nbsp;*</b></th>
                                        <th></th>
                                        <th>BPKG</th>
                                        
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php $incr = $c; for($c = 0;$c <=$incr;$c++){ ?>
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="icode[]" id="icode[<?php echo $c; ?>]" class="form-control select2" style="width:180px;" onchange="update_row_fields(this.id);"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($itemcode[$c] == $scode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>

                                         <td><input type="text" name="nof_bags[]" id="nof_bags[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $nof_bags[$c]; ?>" style="width:90px;" onkeyup="validate_num(this.id);calkgs(this.id);" onchange="validate_amount(this.id);" /></td>
                                       
                                        <td><input type="text" name="kgs[]" id="kgs[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $totalweight[$c]; ?>" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" readonly></td>

                                        <td><input type="text" name="rate[]" id="rate[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $itemprice[$c];  ?>" style="width:90px;" onkeyup="validate_num(this.id);calafterdiscamt(this.id);" onchange="validate_amount(this.id);" /></td>

                                        <td><input type="text" name="amt[]" id="amt[<?php echo $c; ?>]" value="<?php echo $totalamt[$c];  ?>" class="form-control text-right" style="width:80px;" onkeyup="validatenum(this.id);" readonly></td>
                                       

                                        <td><select name="sector[]" id="sector[<?php echo $c; ?>]" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($warehouse[$c] == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>

                                        <?php
                                        if($c == $i){ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:visible;">'; }
                                        else{ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:hidden;">'; }
                                        echo '<a href="javascript:void(0);" id="addrow['.$c.']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;';
                                        if($c > 0){ echo '<a href="javascript:void(0);" id="deductrow['.$c.']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a>'; }
                                        echo '</td>';
                                        ?>

                                        <td ><input type="text" name="bperkg[]" id="bperkg[<?php echo $c; ?>]" value="<?php echo $bags_kg[$c];  ?>" class="form-control text-right" style="width:20px;" readonly ></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="text-align:right;">Total</th>
                                        <?php
                                        $colspan = 8;
                                       
                                        ?>
                                        <th><input type="text" name="tot_bags_qty" id="tot_bags_qty" class="form-control text-right" style="width:80px;" readonly /></th>
                                        <th></th>
                                        <th></th>
                                        <th><input type="text" name="tot_item_amt" id="tot_item_amt" class="form-control text-right" style="width:90px;" readonly /></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="<?php echo $colspan; ?>" style="padding-top:20px;text-align:right;">
                                            <table align="right">
                                                <tr>
                                                    <th>
                                                        <div class="form-group">
                                                            <label for="tcds_chk">TCS</label>
                                                            <input type="checkbox" name="tcds_chk" id="tcds_chk" <?php if((float)$tcdsamt > 0){ echo "checked"; } ?> onchange="calculate_tcds_amt()" />
                                                            <input type="text" name="tcds_per" id="tcds_per" value="<?php echo $tcds_per; ?>" style="width:10px;visibility:hidden;" readonly />
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="form-group">
                                                            <label for="tcds_type1">Type-1</label>
                                                            <select name="tcds_type1" id="tcds_type1" class="form-control select2" style="width:90px;" onchange="calculate_tcds_amt()">
                                                                <option value="auto" <?php if((float)$tcds_type1 == "auto"){ echo "selected"; } ?>>Auto</option>
                                                                <option value="manual" <?php if((float)$tcds_type1 == "manual"){ echo "selected"; } ?>>Manual</option>
                                                            </select>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="form-group">
                                                            <label for="tcds_type2">Type-2</label>
                                                            <select name="tcds_type2" id="tcds_type2" class="form-control select2" style="width:90px;" onchange="calculate_tcds_amt()">
                                                                <option value="add" <?php if((float)$tcds_type2 == "add"){ echo "selected"; } ?>>Add</option>
                                                                <option value="deduct" <?php if((float)$tcds_type2 == "deduct"){ echo "selected"; } ?>>Deduct</option>
                                                            </select>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </table>
                                        </th>
                                        <th><input type="text" name="tcds_amt" id="tcds_amt" class="form-control text-right" value="<?php echo $tcdsamt; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" readonly /></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <?php if($jals_flag == 1){ echo '<th></th>'; } ?>
                                        <?php if($birds_flag == 1){ echo '<th></th>'; } ?>
                                        <?php if($tweight_flag == 1){ echo '<th></th>'; } ?>
                                        <?php if($eweight_flag == 1){ echo '<th></th>'; } ?>
                                        <th></th>
                                        <?php if($birds_flag == 1){ echo '<th></th>'; } ?>
                                        <th>Net Amount</th>
                                        <th><input type="text" name="net_amt1" id="net_amt1" class="form-control text-right" value="<?php echo $net_amt1; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt(this.id);" onchange="validate_amount(this.id);" readonly /></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="<?php echo $colspan; ?>" style="padding-top:20px;text-align:right;">
                                            <table align="right">
                                                <tr>
                                                    <th>
                                                        <div class="form-group">
                                                            <label>Freight: </label>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="form-group">
                                                            <label for="transporter_code">Transporter</label>
                                                            <select name="transporter_code" id="transporter_code" style="width: 230px;"  class="form-control select2">
                                                                <option value="select">-select-</option>
                                                                <?php foreach($transporter_code as $tcode){ ?><option value="<?php echo $tcode;?>" <?php if($tport_code == $tcode){ echo "selected"; } ?>><?php echo $transporter_name[$tcode];?></option><?php } ?>
                                                            </select>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </table>
                                        </th>
                                        <th><input type="text" name="freight_amt" id="freight_amt" class="form-control text-right" value="<?php echo $freight_amount; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" /></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="<?php echo $colspan; ?>" style="padding-top:20px;text-align:right;">
                                            <table align="right">
                                                <tr>
                                                    <th>
                                                        <div class="form-group">
                                                            <label>Round-Off</label>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="form-group">
                                                            <label for="roundoff_type1">Type-1</label>
                                                            <select name="roundoff_type1" id="roundoff_type1" class="form-control select2" style="width:90px;" onchange="calculate_final_totalamt()">
                                                                <option value="auto" <?php if($roundoff_type1 == "auto"){ echo "selected"; } ?>>Auto</option>
                                                                <option value="manual" <?php if($roundoff_type1 == "manual"){ echo "selected"; } ?>>Manual</option>
                                                            </select>
                                                        </div>
                                                    </th>
                                                    <th>
                                                        <div class="form-group">
                                                            <label for="roundoff_type2">Type-2</label>
                                                            <select name="roundoff_type2" id="roundoff_type2" class="form-control select2" style="width:90px;" onchange="calculate_final_totalamt()">
                                                                <option value="add" <?php if($roundoff_type2 == "add"){ echo "selected"; } ?>>Add</option>
                                                                <option value="deduct" <?php if($roundoff_type2 == "deduct"){ echo "selected"; } ?>>Deduct</option>
                                                            </select>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </table>
                                        </th>
                                        <th><input type="text" name="roundoff_amt" id="roundoff_amt" class="form-control text-right" value="<?php echo $roundoff;?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_final_totalamt();" onchange="validate_amount(this.id);" readonly /></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <?php if($jals_flag == 1){ echo '<th></th>'; } ?>
                                        <?php if($birds_flag == 1){ echo '<th></th>'; } ?>
                                        <?php if($tweight_flag == 1){ echo '<th></th>'; } ?>
                                        <?php if($eweight_flag == 1){ echo '<th></th>'; } ?>
                                        <th></th>
                                        <?php if($birds_flag == 1){ echo '<th></th>'; } ?>
                                        <th>Net Amount-2</th>
                                        <th><input type="text" name="net_amt2" id="net_amt2" class="form-control text-right" value="<?php echo $finaltotal;?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt(this.id);" onchange="validate_amount(this.id);" readonly /></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div><br/>
                        <div class="row" style="margin-bottom:3px;">
                            <div class="col-md-4 form-group"></div>
                            <div class="col-md-4 form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" style="height:75px;"><?php echo $remarks; ?></textarea>
                            </div>
                            <div class="col-md-4 form-group"></div>
                        </div>
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
                    window.location.href = "chicken_display_generalpurchase8.php";
                }
                   function calkgs(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var bpkg = document.getElementById("bperkg["+d+"]").value;
                var bag = document.getElementById("nof_bags["+d+"]").value; if(bag == ""){ bag = 0; }
                document.getElementById("kgs["+d+"]").value = (bag * bpkg);
            }

             function calafterdiscamt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                var noofbgs = document .getElementById("nof_bags["+d+"]").value;
                var amt_flag = "<?php echo $amt_flag ?>";
                if(amt_flag > 0){
                    var tamt = (rate * noofbgs).toFixed(2);
                    document .getElementById("amt["+d+"]").value = tamt;
                } else { 
                var d1 = rate - (rate * 0.10);
                var d2 = d1 - (d1 * 0.02);
                var d3 = d2 - (d2 * 0.02);
                var d4 = d3 - (d3 * 0.02);
                console.log(d4);
                var tamt = (d4 * noofbgs).toFixed(2);
                document .getElementById("amt["+d+"]").value = tamt;

                var kgs = document.getElementById("kgs["+d+"]").value;
                var amtperkg = tamt / kgs;
                }
               // document.getElementById("prc_kg["+d+"]").value = amtperkg.toFixed(2);
               // calculate_final_total_amount();
               calculate_final_totalamt();
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
                        alert("Please select supplier");
                        document.getElementById("vcode").focus();
                        l = false;
                    }
                    else{
                        var icode = sector = ""; var c = nweight = price = item_amt = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                icode = document.getElementById("icode["+d+"]").value;
                                nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                                price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                                item_amt = document.getElementById("item_amt["+d+"]").value; if(item_amt == ""){ item_amt = 0; }
                                sector = document.getElementById("sector["+d+"]").value;

                                if(icode == "select"){
                                    alert("Please select Item in row: "+c);
                                    document.getElementById("icode["+d+"]").focus();
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
                                else if(parseFloat(item_amt) == 0){
                                    alert("Please enter price/Weight in row: "+c);
                                    document.getElementById("item_amt["+d+"]").focus();
                                    l = false;
                                }
                                else if(sector == "select"){
                                    alert("Please select sector in row: "+c);
                                    document.getElementById("sector["+d+"]").focus();
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
                function calculate_tcds_amt(){
                    var tcds_chk = document.getElementById("tcds_chk");
                    var tcds_amt = 0;
                    if(tcds_chk.checked == true){
                        var tcds_per = document.getElementById("tcds_per").value;
                        var tcds_type1 = document.getElementById("tcds_type1").value;
                        if(tcds_type1 == "auto"){
                            document.getElementById("tcds_amt").readOnly = true;
                            var tot_item_amt = document.getElementById("tot_item_amt").value; if(tot_item_amt == ""){ tot_item_amt = 0; }
                            tcds_amt = ((parseFloat(tcds_per) / 100) * parseFloat(tot_item_amt));
                            document.getElementById("tcds_amt").value = parseFloat(tcds_amt).toFixed(2);
                            calculate_final_totalamt();
                        }
                        else{
                            document.getElementById("tcds_amt").readOnly = false;
                            calculate_final_totalamt();
                        }
                    }
                    else{
                        document.getElementById("tcds_amt").value = 0;
                        calculate_final_totalamt();
                    }
                }
                function fetch_tcds_per(){
                    var date = document.getElementById("date").value;
                    var tdsper = new XMLHttpRequest();
                    var method = "GET";
                    var url = "main_gettcdsvalue.php?type=TDS&cdate="+date;
                    var asynchronous = true;
                    tdsper.open(method, url, asynchronous);
                    tdsper.send();
                    tdsper.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var tds = this.responseText;
                            if(tds != ""){
                                document.getElementById("tcds_per").value = tds;
                            }
                        }
                    }
                }
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";
                    d++; var html = '';
                    document.getElementById("incr").value = d;

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    html += '<tr id="row_no['+d+']">';
                    html += '<td><select name="icode[]" id="icode['+d+']" class="form-control select2" style="width:180px;" onchange="update_row_fields(this.id);"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>';
                    if(parseInt(jals_flag) == 1){ html += '<td><input type="text" name="jals[]" id="jals['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt(this.id);" /></td>'; }
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="birds[]" id="birds['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt(this.id);" /></td>'; }
                    if(parseInt(tweight_flag) == 1){ html += '<td><input type="text" name="tweight[]" id="tweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt(this.id);" onchange="validate_amount(this.id);" /></td>'; }
                    if(parseInt(eweight_flag) == 1){ html += '<td><input type="text" name="eweight[]" id="eweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt(this.id);" onchange="validate_amount(this.id);" /></td>'; }
                    html += '<td><input type="text" name="nweight[]" id="nweight['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt(this.id);" onchange="validate_amount(this.id);" /></td>';
                    if(parseInt(birds_flag) == 1){ html += '<td><input type="text" name="avg_prc[]" id="avg_prc['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt(this.id);" onchange="validate_amount(this.id);" readonly /></td>'; }
                    html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt(this.id);" onchange="validate_amount(this.id);" /></td>';
                    html += '<td><input type="text" name="item_amt[]" id="item_amt['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt(this.id);" onchange="validate_amount(this.id);" readonly /></td>';
                    html += '<td><select name="sector[]" id="sector['+d+']" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
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
                 function calculate_final_totalamt(){
                    var incr = document.getElementById("incr").value;
                    var amt_flag = "<?php echo $amt_flag ?>";

                    var jals = birds = tweight = eweight = nweight = avg_wt = price = item_amt = 0;
                    var tot_jals = tot_birds = tot_tweight = tot_eweight = tot_nweight = tot_item_amt = bird_flag = 0;
                    var icode = iname = "";
                    var totalamt = 0; var bags = 0;
                    for(var d = 0;d <= incr;d++){
                        var price = document.getElementById("amt["+d+"]").value; if(price == ""){ price = 0; }
                        var nof_bags = document.getElementById("nof_bags["+d+"]").value; if(nof_bags == ""){ nof_bags = 0; }
                        totalamt = parseFloat(totalamt) + parseFloat(price);
                        bags = parseFloat(bags) + parseFloat(nof_bags);
                    }
                    // if(parseInt(jals_flag) == 1){ document.getElementById("tot_jals").value = parseFloat(tot_jals).toFixed(0); }
                    // if(parseInt(birds_flag) == 1){ document.getElementById("tot_birds").value = parseFloat(tot_birds).toFixed(0); }
                    // if(parseInt(tweight_flag) == 1){ document.getElementById("tot_tweight").value = parseFloat(tot_tweight).toFixed(2); }
                    // if(parseInt(eweight_flag) == 1){ document.getElementById("tot_eweight").value = parseFloat(tot_eweight).toFixed(2); }
                    // document.getElementById("tot_nweight").value = parseFloat(tot_nweight).toFixed(2);
                     document.getElementById("tot_item_amt").value = parseFloat(totalamt).toFixed(2);
                     document.getElementById("tot_bags_qty").value = parseFloat(bags).toFixed(2);
                    /*calculate TCDS to Billing Amount*/
                    var tcds_chk = document.getElementById("tcds_chk");
                    var tcds_amt = net_amt1 = net_amt2 = 0; var tcds_type2 = "";
                    if(tcds_chk.checked == true){
                        tcds_type2 = document.getElementById("tcds_type2").value;
                        tcds_amt = document.getElementById("tcds_amt").value;
                        if(tcds_type2 == "add"){
                            net_amt1 = parseFloat(totalamt) + parseFloat(tcds_amt);
                        }
                        else{
                            net_amt1 = parseFloat(totalamt) - parseFloat(tcds_amt);
                        }
                    }
                    else{
                        net_amt1 = parseFloat(totalamt);
                    }
                    document.getElementById("net_amt1").value = parseFloat(net_amt1).toFixed(2);

                    /*Freiht to Total Invoice*/
                    var freight_amt = document.getElementById("freight_amt").value; if(freight_amt == ""){ freight_amt = 0; }
                    var total_bags = parseFloat(bags); 
                    if(amt_flag > 0){
                        var net_amt2 = parseFloat(net_amt1) + (parseFloat(freight_amt) * parseFloat(total_bags) );
                     } else { 
                        var net_amt2 = parseFloat(net_amt1) + parseFloat(freight_amt);
                    }

                    /*Round-Off Calculations*/
                    var rf_type1 = document.getElementById("roundoff_type1").value;
                    if(rf_type1 == "auto"){
                        document.getElementById("roundoff_amt").readOnly = true;
                        var net_amt1 = document.getElementById("net_amt1").value; if(net_amt1 == ""){ net_amt1 = 0; }
                        var t_amt = parseFloat(net_amt1).toFixed(0);
                        var roundoff_amt = parseFloat(t_amt) - parseFloat(net_amt1);
                        if(roundoff_amt > 0){
                            $('#roundoff_type2').select2();
                            document.getElementById("roundoff_type2").value = "add";
                            $('#roundoff_type2').select2();
                        }
                        else{
                            $('#roundoff_type2').select2();
                            document.getElementById("roundoff_type2").value = "deduct";
                            $('#roundoff_type2').select2();
                        }
                        document.getElementById("roundoff_amt").value = parseFloat(roundoff_amt).toFixed(2);
                        var net_amt2 = parseFloat(net_amt2) + parseFloat(roundoff_amt);
                    }
                    else{
                        document.getElementById("roundoff_amt").readOnly = false;
                        var roundoff_amt = document.getElementById("roundoff_amt").value; if(roundoff_amt == ""){ roundoff_amt = 0; }
                        var rf_type2 = document.getElementById("roundoff_type2").value;
                        if(rf_type2 == "add"){
                            var net_amt2 = parseFloat(net_amt2) + parseFloat(roundoff_amt);
                        }
                        else{
                            var net_amt2 = parseFloat(net_amt2) - parseFloat(roundoff_amt);
                        }
                    }
                    
                    document.getElementById("net_amt2").value = parseFloat(net_amt2).toFixed(2);
                }
                function calculate_total_amt(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var icode = document.getElementById("icode["+d+"]");
                    var iname = icode.options[icode.selectedIndex].text;
                    var bird_flag = iname.search(/Birds/i);

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    var jals = birds = tweight = eweight = nweight = price = item_amt = 0;
                    if(parseInt(jals_flag) == 1){ jals = document.getElementById("jals["+d+"]").value; } if(jals == ""){ jals = 0; }
                    if(parseInt(birds_flag) == 1){ birds = document.getElementById("birds["+d+"]").value; } if(birds == ""){ birds = 0; }
                    if(parseInt(tweight_flag) == 1){ tweight = document.getElementById("tweight["+d+"]").value; } if(tweight == ""){ tweight = 0; }
                    if(parseInt(eweight_flag) == 1){ eweight = document.getElementById("eweight["+d+"]").value; } if(eweight == ""){ eweight = 0; }

                    if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1 && parseInt(bird_flag) > 0){
                        nweight = parseFloat(tweight) - parseFloat(eweight);
                        document.getElementById("nweight["+d+"]").value = parseFloat(nweight).toFixed(2);
                    }
                    else{
                        nweight = document.getElementById("nweight["+d+"]").value; if(nweight == ""){ nweight = 0; }
                    }
                    var price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                    var item_amt = parseFloat(nweight) * parseFloat(price);
                    document.getElementById("item_amt["+d+"]").value = parseFloat(item_amt).toFixed(2);
                    calculate_final_totalamt();
                }
                function update_row_fields(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var icode = document.getElementById("icode["+d+"]");
                    var iname = icode.options[icode.selectedIndex].text;
                    var bird_flag = iname.search(/Birds/i);

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    if(parseInt(bird_flag) > 0){
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "visible"; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "visible"; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("avg_wt["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("tweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("eweight["+d+"]").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1){ document.getElementById("nweight["+d+"]").readOnly = true; }
                    }
                    else{
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "hidden"; document.getElementById("jals["+d+"]").value = ""; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "hidden"; document.getElementById("birds["+d+"]").value = ""; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("avg_wt["+d+"]").style.visibility = "hidden"; document.getElementById("avg_wt["+d+"]").value = ""; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("tweight["+d+"]").style.visibility = "hidden"; document.getElementById("tweight["+d+"]").value = ""; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("eweight["+d+"]").style.visibility = "hidden"; document.getElementById("eweight["+d+"]").value = ""; }
                        document.getElementById("nweight["+d+"]").readOnly = false;
                    }
                    calculate_total_amt(a);
                }
                function update_all_row_fields(){
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        var icode = document.getElementById("icode["+d+"]");
                        var iname = icode.options[icode.selectedIndex].text;
                        var bird_flag = iname.search(/Birds/i);

                        var jals_flag = '<?php echo $jals_flag; ?>';
                        var birds_flag = '<?php echo $birds_flag; ?>';
                        var tweight_flag = '<?php echo $tweight_flag; ?>';
                        var eweight_flag = '<?php echo $eweight_flag; ?>';

                        if(parseInt(bird_flag) > 0){
                            if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "visible"; }
                            if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "visible"; }
                            if(parseInt(birds_flag) == 1){ document.getElementById("avg_wt["+d+"]").style.visibility = "visible"; }
                            if(parseInt(tweight_flag) == 1){ document.getElementById("tweight["+d+"]").style.visibility = "visible"; }
                            if(parseInt(eweight_flag) == 1){ document.getElementById("eweight["+d+"]").style.visibility = "visible"; }
                            if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1){ document.getElementById("nweight["+d+"]").readOnly = true; }
                        }
                        else{
                            if(parseInt(jals_flag) == 1){ document.getElementById("jals["+d+"]").style.visibility = "hidden"; document.getElementById("jals["+d+"]").value = ""; }
                            if(parseInt(birds_flag) == 1){ document.getElementById("birds["+d+"]").style.visibility = "hidden"; document.getElementById("birds["+d+"]").value = ""; }
                            if(parseInt(birds_flag) == 1){ document.getElementById("avg_wt["+d+"]").style.visibility = "hidden"; document.getElementById("avg_wt["+d+"]").value = ""; }
                            if(parseInt(tweight_flag) == 1){ document.getElementById("tweight["+d+"]").style.visibility = "hidden"; document.getElementById("tweight["+d+"]").value = ""; }
                            if(parseInt(eweight_flag) == 1){ document.getElementById("eweight["+d+"]").style.visibility = "hidden"; document.getElementById("eweight["+d+"]").value = ""; }
                            document.getElementById("nweight["+d+"]").readOnly = false;
                        }
                    }
                    //calculate_final_totalamt();
                }
                function fetch_supplier_outstanding(){
                    var vcode = document.getElementById("vcode").value;
                    if(!vcode.match("select")){
                        var inv_items = new XMLHttpRequest();
                        var method = "GET";
                        var url = "supplier_fetch_balance.php?pname="+vcode;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var dval = this.responseText;
                                document.getElementById("out_balance").value = dval;
                            }
                        }
                    }
                    else{
                        document.getElementById("out_balance").value = "";
                    }
                }
                update_all_row_fields();
                calculate_final_totalamt();
                fetch_supplier_outstanding();
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
