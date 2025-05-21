<?php
//chicken_edit_stockadjustment1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Stock Adjustment"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Birds' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
    
    $sql = "SELECT * FROM `main_transactionfields` WHERE `field` LIKE 'Stock Adjustment' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; }
    if($jals_flag == "" || $jals_flag == NULL){ $jals_flag = 0; } if($birds_flag == "" || $birds_flag == NULL){ $birds_flag = 0; }
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            $sql = "SELECT * FROM `item_stock_adjustment` WHERE `trnum` = '$ids' AND `trlink` = 'chicken_display_stockadjustment1.php' AND `dflag` = '0'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $date = date("d.m.Y",strtotime($row['date']));
                $warehouse = $row['warehouse'];
                $a_type = $row['a_type'];
                $itemcode = $row['itemcode'];
                $jals = round($row['jals'],2);
                $birds = round($row['birds'],2);
                $quantity = round($row['nweight'],2);
                $price = round($row['price'],2);
                $amount = round($row['amount'],2);
                $remarks = $row['remarks'];
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Stock Adjustment</div>
                <form action="chicken_modify_stockadjustment1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Warehouse<b style="color:red;">&nbsp;*</b></th>
                                        <th>Type<b style="color:red;">&nbsp;*</b></th>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
										<?php if($jals_flag == 1) { echo '<th>Jals<b style="color:red;">&nbsp;*</b></th>'; } ?>
										<?php if($birds_flag == 1) { echo '<th>Birds<b style="color:red;">&nbsp;*</b></th>'; } ?>
                                        <th>Quantity</th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <th>Remarks</th>
                                        <th style="visibility:hidden;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date" id="date" class="form-control sale_datepickers" value="<?php echo $date; ?>" style="min-width:100px;"></td>
                                        <td><select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $warehouse){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="a_type" id="a_type" class="form-control select2" style="width:180px;"><option value="select">-select-</option><option value="add" <?php if($a_type == "add"){ echo "selected"; } ?>>-Add-</option><option value="deduct" <?php if($a_type == "deduct"){ echo "selected"; } ?>>-Deduct-</option></select></td>
                                        <td><select name="itemcode" id="itemcode" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $itemcode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <?php if($jals_flag == 1) { echo '<td><input type="text" name="jals" id="jals" class="form-control text-right" value="'.$jals.'" style="width:90px;" onkeyup="validate_count(this.id);calculate_row_amt();" /></td>'; } ?>
                                        <?php if($birds_flag == 1) { echo '<td><input type="text" name="birds" id="birds" class="form-control text-right" value="'.$birds.'" style="width:90px;" onkeyup="validate_count(this.id);calculate_row_amt();" /></td>'; } ?>
                                        <td><input type="text" name="quantity" id="quantity" class="form-control text-right" value="<?php echo $quantity; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price" id="price" class="form-control text-right" value="<?php echo $price; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="amount" id="amount" class="form-control text-right" value="<?php echo $amount; ?>" style="width:90px;" readonly /></td>
                                        <td><textarea name="remarks" id="remarks" class="form-control" style="height: 23px;"><?php echo $remarks; ?></textarea></td>
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
                    window.location.href = "chicken_display_stockadjustment1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var birds_flag = '<?php echo (int)$birds_flag; ?>';

                    var date = document.getElementById("date").value;
                    var warehouse = document.getElementById("warehouse").value;
                    var itemcode = document.getElementById("itemcode").value;
                    if(parseInt(birds_flag) == 1){ birds = document.getElementById("birds").value; if(birds == ""){ birds = 0; } } else{ var birds = 0; }
                    var quantity = document.getElementById("quantity").value; if(quantity == ""){ quantity = 0; }
                    var price = document.getElementById("price").value; if(price == ""){ price = 0; }
                    var amount = document.getElementById("amount").value; if(amount == ""){ amount = 0; }
                            
                    if(date == ""){
                        alert("Please select Date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(warehouse == "" || warehouse == "select"){
                        alert("Please select Warehouse");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(itemcode == "" || itemcode == "select"){
                        alert("Please select Item");
                        document.getElementById("itemcode").focus();
                        l = false;
                    }
                    else if(parseInt(birds_flag) == 1 && parseFloat(birds) == 0){
                        alert("Please enter Birds");
                        document.getElementById("birds").focus();
                        l = false;
                    }
                    else if(parseInt(birds_flag) == 0 && parseFloat(quantity) == 0){
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
                function calculate_row_amt(){
                    var quantity = document.getElementById("quantity").value; if(quantity == ""){ quantity = 0; }
                    var price = document.getElementById("price").value; if(price == ""){ price = 0; }

                    amount = parseFloat(quantity) * parseFloat(price); if(amount == ""){ amount = 0; }
                    document.getElementById("amount").value = parseFloat(amount).toFixed(0);
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
