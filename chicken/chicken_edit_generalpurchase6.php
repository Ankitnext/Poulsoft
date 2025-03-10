<?php
//chicken_edit_generalpurchase6.php
//if Layer Birds, price calculations on birds instead of weight added
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql);$jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = (int)$row['jals_flag']; $birds_flag = (int)$row['birds_flag']; $tweight_flag = (int)$row['tweight_flag']; $eweight_flag = (int)$row['eweight_flag']; }

    $sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%S%' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sgrp_code = $sgrp_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sgrp_code[$row['code']] = $row['code']; $sgrp_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; $sup_group[$row['code']] = $row['groupcode']; }
    
    $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
            <style>
                label{
                    font-weight:bold;
                }
            </style>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            $sql = "SELECT * FROM `pur_purchase` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0' AND `trlink` = 'chicken_display_generalpurchase6.php' ORDER BY `date`,`invoice`,`id` ASC";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $date = $row['date'];
                $bookinvoice = $row['bookinvoice'];
                $vendorcode = $row['vendorcode'];
                $itemcode = $row['itemcode'];
                $jals = round($row['jals'],5);
                $birds = round($row['birds'],5);
                $totalweight = round($row['totalweight'],5);
                $emptyweight = round($row['emptyweight'],5);
                $netweight = round($row['netweight'],5);
                $itemprice = round($row['itemprice'],5);
                $totalamt = round($row['totalamt'],5);
                $warehouse = $row['warehouse'];
                $remarks = $row['remarks'];
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Purchase</div>
                <form action="chicken_modify_generalpurchase6.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date<b style="color:red;">&nbsp;*</b></label>
                                <input type="text" name="date" id="date" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" readonly />
                            </div>
                            <div class="form-group" style="width:190px;">
                                <label for="warehouse">Warehouse/Vehicle<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;">
                                    <option value="select">select</option>
                                    <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $warehouse){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                </select>
                            </div>
                            <div class="form-group" style="width:120px;">
                                <label for="bookinvoice">Invoice No.</label>
                                <input type="text" name="bookinvoice" id="bookinvoice" class="form-control" value="<?php echo $bookinvoice; ?>" style="width:110px;" />
                            </div>
                        </div>
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width:100px;">Supplier<b style="color:red;">&nbsp;*</b></th>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <?php if((int)$jals_flag == 1){ echo "<th>Jals</th>"; } ?>
                                        <?php if((int)$birds_flag == 1){ echo "<th>Birds</th>"; } ?>
                                        <?php if((int)$tweight_flag == 1){ echo "<th>T. Weight</th>"; } ?>
                                        <?php if((int)$eweight_flag == 1){ echo "<th>E. Weight</th>"; } ?>
                                        <th>N. Weight</th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                        <th style="visibility:hidden;">Action</th>
                                        <th style="visibility:hidden;">On Bird</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="vcode" id="vcode" class="form-control select2" style="width:250px;"><option value="select">-select-</option><?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $vendorcode){ echo "selected"; } ?>><?php echo $sup_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="itemcode" id="itemcode" class="form-control select2" style="width:180px;" onchange="update_row_fields();"><option value="select">select</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $itemcode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="jals" id="jals" class="form-control text-right" value="'.$jals.'" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="birds" id="birds" class="form-control text-right" value="'.$birds.'" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                                        if((int)$tweight_flag == 1){ echo '<td><input type="text" name="tweight" id="tweight" class="form-control text-right" value="'.$totalweight.'" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                                        if((int)$eweight_flag == 1){ echo '<td><input type="text" name="eweight" id="eweight" class="form-control text-right" value="'.$emptyweight.'" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>'; }
                                        ?>
                                        <td><input type="text" name="nweight" id="nweight" class="form-control text-right" value="<?php echo $netweight; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price" id="price" class="form-control text-right" value="<?php echo $itemprice; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="item_amt" id="item_amt" class="form-control text-right" value="<?php echo $totalamt; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td><textarea name="remarks" id="remarks" class="form-control" style="width:150px;height:25px;"><?php echo $remarks; ?></textarea></td>
                                        <td style="visibility:hidden"><input type="checkbox" name="prc_obrd" id="prc_obrd" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><br/>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
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
                    window.location.href = "chicken_display_generalpurchase6.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var date = document.getElementById("date").value;
                    var warehouse = document.getElementById("warehouse").value;
                    var vcode = document.getElementById("vcode").value;
                    var itemcode = document.getElementById("itemcode").value;
                    /*var nweight = document.getElementById("nweight").value; if(nweight == ""){ nweight = 0; }*/
                    var price = document.getElementById("price").value; if(price == ""){ price = 0; }
                    var item_amt = document.getElementById("item_amt").value; if(item_amt == ""){ item_amt = 0; }

                    if(date == ""){
                        alert("Please select date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(warehouse == "select"){
                        alert("Please select Warehouse");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(vcode == "select"){
                        alert("Please select Supplier");
                        document.getElementById("vcode").focus();
                        l = false;
                    }
                    else if(itemcode == "select"){
                        alert("Please select Item");
                        document.getElementById("itemcode").focus();
                        l = false;
                    }
                    /*else if(parseFloat(nweight) == 0){
                        alert("Please enter net weight");
                        document.getElementById("nweight").focus();
                        l = false;
                    }*/
                    else if(parseFloat(price) == 0){
                        alert("Please enter price");
                        document.getElementById("price").focus();
                        l = false;
                    }
                    else if(parseFloat(item_amt) == 0){
                        alert("Please enter price/Weight");
                        document.getElementById("item_amt").focus();
                        l = false;
                    }
                    else{ }
                    
                    if(l == true){
                        return true;
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
                function calculate_total_amt(){
                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    /*Total Calculations*/
                    var jals = birds = tweight = eweight = nweight = price = item_amt = prc_obrd = 0;

                    var icode = iname = "";
                    icode = document.getElementById("itemcode");
                    iname = icode.options[icode.selectedIndex].text;
                    bird_flag = iname.search(/Birds/i);

                    //Price calculation on bird flag
                    document.getElementById("prc_obrd").checked = false;
                    if(iname == "Layer Birds" && parseInt(birds_flag) == 1){ document.getElementById("prc_obrd").checked = true; prc_obrd = 1; }

                    if(parseInt(jals_flag) == 1){ jals = document.getElementById("jals").value; } if(jals == ""){ jals = 0; }
                    if(parseInt(birds_flag) == 1){ birds = document.getElementById("birds").value; } if(birds == ""){ birds = 0; }
                    if(parseInt(tweight_flag) == 1){ tweight = document.getElementById("tweight").value; } if(tweight == ""){ tweight = 0; }
                    if(parseInt(eweight_flag) == 1){ eweight = document.getElementById("eweight").value; } if(eweight == ""){ eweight = 0; }
                    if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1 && parseInt(bird_flag) > 0){
                        nweight = parseFloat(tweight) - parseFloat(eweight);
                        document.getElementById("nweight").value = parseFloat(nweight).toFixed(2);
                    }
                    else{
                        nweight = document.getElementById("nweight").value; if(nweight == ""){ nweight = 0; }
                    }

                    var price = document.getElementById("price").value; if(price == ""){ price = 0; }
                    if(parseInt(prc_obrd) == 1 && parseInt(birds_flag) == 1){ var item_amt = parseFloat(birds) * parseFloat(price); }
                    else{ var item_amt = parseFloat(nweight) * parseFloat(price); }
                    document.getElementById("item_amt").value = parseFloat(item_amt).toFixed(2);
                }
                function update_row_fields(){
                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';
                    var tweight_flag = '<?php echo $tweight_flag; ?>';
                    var eweight_flag = '<?php echo $eweight_flag; ?>';

                    var icode = iname = "";
                    icode = document.getElementById("itemcode");
                    iname = icode.options[icode.selectedIndex].text;
                    bird_flag = iname.search(/Birds/i);

                    if(parseInt(bird_flag) > 0){
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals").style.visibility = "visible"; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("tweight").style.visibility = "visible"; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("eweight").style.visibility = "visible"; }
                        if(parseInt(tweight_flag) == 1 && parseInt(eweight_flag) == 1){ document.getElementById("nweight").readOnly = true; }
                    }
                    else{
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals").style.visibility = "hidden"; document.getElementById("jals").value = ""; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds").style.visibility = "hidden"; document.getElementById("birds").value = ""; }
                        if(parseInt(tweight_flag) == 1){ document.getElementById("tweight").style.visibility = "hidden"; document.getElementById("tweight").value = ""; }
                        if(parseInt(eweight_flag) == 1){ document.getElementById("eweight").style.visibility = "hidden"; document.getElementById("eweight").value = ""; }
                        document.getElementById("nweight").readOnly = false;
                    }
                    calculate_total_amt();
                }
                calculate_total_amt();
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
