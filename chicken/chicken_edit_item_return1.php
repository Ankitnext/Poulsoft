<?php
//chicken_edit_item_return1.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Sales"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
    $query = mysqli_query($conn,$sql);$jals_flag = $birds_flag = $tweight_flag = $eweight_flag = 0;
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = (int)$row['jals_flag']; $birds_flag = (int)$row['birds_flag']; $tweight_flag = (int)$row['tweight_flag']; $eweight_flag = (int)$row['eweight_flag']; }

    $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%MACHINE%' OR `description` LIKE '%SHOP INVESTMENT%' OR `description` LIKE '%SCALE%' OR `description` LIKE '%BOARD%' OR `description` LIKE '%CASH%' OR `description` LIKE '%OTHERS%') AND `active` = '1' ORDER BY `id`";
    $query = mysqli_query($conn,$sql); $cat_alist = array();
    while($row = mysqli_fetch_assoc($query)) { $cat_alist[$row['code']] = $row['code']; }
    $cat_list = implode("','",$cat_alist);

    $sql = "SELECT * FROM `item_details` WHERE `category` NOT IN ('$cat_list') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

    //check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";
    
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
            $sql = "SELECT * FROM `main_itemreturns` WHERE `trnum` = '$ids' AND `dflag` = '0' AND `trlink` = 'chicken_display_item_return1.php' ORDER BY `date`,`trnum`,`id` ASC";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $date = $row['date'];
                $vcode = $row['vcode'];
                $itemcode = $row['itemcode'];
                $jals = round($row['jals'],5);
                $birds = round($row['birds'],5);
                $quantity = round($row['quantity'],5);
                $price = round($row['price'],5);
                $amount = round($row['amount'],5);
                $warehouse = $row['warehouse'];
                $remarks = $row['remarks'];
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Sales Return</div>
                <form action="chicken_modify_item_return1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                    <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Customer<b style="color:red;">&nbsp;*</b></th>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <?php if((int)$jals_flag == 1){ echo "<th>Jals</th>"; } ?>
                                        <?php if((int)$birds_flag == 1){ echo "<th>Birds</th>"; } ?>
                                        <th>Weight<b style="color:red;">&nbsp;*</b></th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount</th>
                                        <th>Warehouse<b style="color:red;"></b></th>
                                        <th>Remarks</th>
                                        <th style="width:70px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date" id="date" class="form-control range_picker" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" readonly /></td>
                                        <td><select name="vcode" id="vcode" class="form-control select2" style="width:350px;"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $vcode){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="itemcode" id="itemcode" class="form-control select2" style="width:180px;" onchange="update_row_fields();"><option value="select">select</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $itemcode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php
                                        if((int)$jals_flag == 1){ echo '<td><input type="text" name="jals" id="jals" class="form-control text-right" value="'.$jals.'" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                                        if((int)$birds_flag == 1){ echo '<td><input type="text" name="birds" id="birds" class="form-control text-right" value="'.$birds.'" style="width:90px;" onkeyup="validate_count(this.id);calculate_total_amt();" /></td>'; }
                                        ?>
                                        <td><input type="text" name="quantity" id="quantity" class="form-control text-right" value="<?php echo $quantity; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price" id="price" class="form-control text-right" value="<?php echo $price; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="amount" id="amount" class="form-control text-right" value="<?php echo $amount; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_total_amt();" onchange="validate_amount(this.id);" readonly /></td>
                                        <td><select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;"><option value="select">select</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $warehouse){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><textarea name="remarks" id="remarks" class="form-control" style="width:150px;height:25px;"><?php echo $remarks; ?></textarea></td>
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
                 //Date Range selection
                var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
                function return_back(){
                    window.location.href = "chicken_display_item_return1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var date = document.getElementById("date").value;
                    var vcode = document.getElementById("vcode").value;
                    var itemcode = document.getElementById("itemcode").value;
                    var quantity = document.getElementById("quantity").value; if(quantity == ""){ quantity = 0; }
                    var price = document.getElementById("price").value; if(price == ""){ price = 0; }
                    var amount = document.getElementById("amount").value; if(amount == ""){ amount = 0; }
                    var warehouse = document.getElementById("warehouse").value;

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
                    else if(itemcode == "select"){
                        alert("Please select Item");
                        document.getElementById("itemcode").focus();
                        l = false;
                    }
                    else if(parseFloat(quantity) == 0){
                        alert("Please enter Quantity");
                        document.getElementById("quantity").focus();
                        l = false;
                    }
                    else if(parseFloat(price) == 0){
                        alert("Please enter Price");
                        document.getElementById("price").focus();
                        l = false;
                    }
                    else if(parseFloat(amount) == 0){
                        alert("Please enter Quantity/Price");
                        document.getElementById("amount").focus();
                        l = false;
                    }
                    // else if(warehouse == "select"){
                    //     alert("Please select Warehouse");
                    //     document.getElementById("warehouse").focus();
                    //     l = false;
                    // }
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
                    /*Total Calculations*/
                    var quantity = price = amount = 0;
                    quantity = document.getElementById("quantity").value; if(quantity == ""){ quantity = 0; }
                    price = document.getElementById("price").value; if(price == ""){ price = 0; }
                    amount = parseFloat(quantity) * parseFloat(price);
                    document.getElementById("amount").value = parseFloat(amount).toFixed(2);
                }
                function update_row_fields(){
                    var icode = iname = "";
                    icode = document.getElementById("itemcode");
                    iname = icode.options[icode.selectedIndex].text;
                    bird_flag = iname.search(/Birds/i);

                    var jals_flag = '<?php echo $jals_flag; ?>';
                    var birds_flag = '<?php echo $birds_flag; ?>';

                    if(parseInt(bird_flag) > 0){
                         if(parseInt(jals_flag) == 1){ document.getElementById("jals").style.visibility = "visible"; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds").style.visibility = "visible"; }
                    }
                    else{
                        if(parseInt(jals_flag) == 1){ document.getElementById("jals").style.visibility = "hidden"; document.getElementById("jals").value = ""; }
                        if(parseInt(birds_flag) == 1){ document.getElementById("birds").style.visibility = "hidden"; document.getElementById("birds").value = ""; }
                    }
                    calculate_total_amt();
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
            <script src="handle_ebtn_as_tbtn.js"></script>
            <script>
                //Date Range selection
                $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            </script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
