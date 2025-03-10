<?php
//chicken_edit_mortality1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Sales"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Birds' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            $sql = "SELECT * FROM `main_mortality` WHERE `code` = '$ids' AND `trlink` = 'chicken_display_mortality1.php' AND `dflag` = '0'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $date = date("d.m.Y",strtotime($row['date']));
                $mtype = $row['mtype'];
                $ccode = $row['ccode'];
                $itemcode = $row['itemcode'];
                $birds = round($row['birds'],2);
                $quantity = round($row['quantity'],2);
                $price = round($row['price'],2);
                $amount = round($row['amount'],2);
                $remarks = $row['remarks'];
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Mortality</div>
                <form action="chicken_modify_mortality1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th> 
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <th>Birds<b style="color:red;">&nbsp;*</b></th>
                                        <th>Weight</th>
                                        <th>Price<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <th>Mortality On<b style="color:red;">&nbsp;*</b></th>
                                        <th>Customer / Warehouse<b style="color:red;">&nbsp;*</b></th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date" id="date" class="form-control sale_datepickers" value="<?php echo $date; ?>" style="min-width:100px;"></td>
                                        <td><select name="itemcode" id="itemcode" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $itemcode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="birds" id="birds" class="form-control text-right" value="<?php echo $birds; ?>" style="width:90px;" onkeyup="validate_count(this.id);calculate_row_amt();" /></td>
                                        <td><input type="text" name="quantity" id="quantity" class="form-control text-right" value="<?php echo $quantity; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="price" id="price" class="form-control text-right" value="<?php echo $price; ?>" style="width:90px;" onkeyup="validate_num(this.id);calculate_row_amt();" onchange="validate_amount(this.id);" /></td>
                                        <td><input type="text" name="amount" id="amount" class="form-control text-right" value="<?php echo $amount; ?>" style="width:90px;" readonly /></td>
                                        <td><select name="mtype" id="mtype" class="form-control select2" style="width: 180px;" onchange="setgroup();"><option value="select">select</option><option value="customer" <?php if($mtype == "customer"){ echo "selected"; } ?>>Customer</option><option value="supplier" <?php if($mtype == "supplier"){ echo "selected"; } ?>>Supplier</option><option value="sector" <?php if($mtype == "sector"){ echo "selected"; } ?>>Warehouse</option></select></td>
										<td>
                                            <select name="ccode" id="ccode" class="form-control select2"style="width:180px;">
                                                <option value="select">select</option>
                                                <?php if($mtype == "customer"){ foreach($cus_code as $scode){ ?> <option value="<?php echo $scode; ?>" <?php if($ccode == $scode){ echo 'selected'; } ?>><?php echo $cus_name[$scode]; ?></option> <?php } } ?>
                                                <?php if($mtype == "supplier"){ foreach($sup_code as $scode){ ?> <option value="<?php echo $scode; ?>" <?php if($ccode == $scode){ echo 'selected'; } ?>><?php echo $sup_name[$scode]; ?></option> <?php } } ?>
                                                <?php if($mtype == "sector"){ foreach($sector_code as $scode){ ?> <option value="<?php echo $scode; ?>" <?php if($ccode == $scode){ echo 'selected'; } ?>><?php echo $sector_name[$scode]; ?></option> <?php } } ?>
                                            </select>
                                        </td>
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
                    window.location.href = "chicken_display_mortality1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var date = document.getElementById("date").value;
                    var itemcode = document.getElementById("itemcode").value;
                    var birds = document.getElementById("birds").value; if(birds == ""){ birds = 0; }
                    var price = document.getElementById("price").value; if(price == ""){ price = 0; }
                    var amount = document.getElementById("amount").value; if(amount == ""){ amount = 0; }
                    var mtype = document.getElementById("mtype").value;
                    var ccode = document.getElementById("ccode").value;
                            
                    if(date == ""){
                        alert("Please select Date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(itemcode == "" || itemcode == "select"){
                        alert("Please select Item");
                        document.getElementById("itemcode").focus();
                        l = false;
                    }
                    else if(parseFloat(birds) == 0){
                        alert("Please enter Birds");
                        document.getElementById("birds").focus();
                        l = false;
                    }
                    else if(parseFloat(price) == 0){
                        alert("Please enter Price");
                        document.getElementById("price").focus();
                        l = false;
                    }
                    else if(parseFloat(amount) == 0){
                        alert("Please enter Birds/Price");
                        document.getElementById("amount").focus();
                        l = false;
                    }
                    else if(mtype == "" || mtype == "select"){
                        alert("Please select Mortality On");
                        document.getElementById("mtype").focus();
                        l = false;
                    }
                    else if(ccode == "" || ccode == "select"){
                        alert("Please select Customer / Warehouse");
                        document.getElementById("ccode").focus();
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
                    var birds = document.getElementById("birds").value; if(birds == ""){ birds = 0; }
                    var quantity = document.getElementById("quantity").value; if(quantity == ""){ quantity = 0; }
                    var price = document.getElementById("price").value; if(price == ""){ price = 0; }

                    var amount = parseFloat(birds) * parseFloat(price); if(amount == ""){ amount = 0; }
                    document.getElementById("amount").value = parseFloat(amount).toFixed(0);
                }
                function setgroup(){
                    var mtype = document.getElementById("mtype").value;
                    removeAllOptions(document.getElementById("ccode"));

                    var select1 = document.getElementById("ccode"); 
                    var option1 = document.createElement("OPTION"); 
                    var text1 = document.createTextNode("select"); 
                    option1.value = "select"; 
                    option1.appendChild(text1); 
                    select1.appendChild(option1);

                    if(mtype == "customer"){
                        <?php
                            foreach($cus_code as $icode){
                        ?>
                        option1 = document.createElement("OPTION");
                        text1 = document.createTextNode("<?php echo $cus_name[$icode]; ?>");
                        option1.value = "<?php echo $icode; ?>";
                        option1.appendChild(text1);
                        select1.appendChild(option1);
                        <?php
                            }
                        ?>
                    }
                    else if(mtype == "supplier"){
                        <?php
                            foreach($sup_code as $icode){
                        ?>
                        option1 = document.createElement("OPTION");
                        text1 = document.createTextNode("<?php echo $sup_name[$icode]; ?>");
                        option1.value = "<?php echo $icode; ?>";
                        option1.appendChild(text1);
                        select1.appendChild(option1);
                        <?php
                            }
                        ?>
                    }
                    else if(mtype == "sector"){
                        <?php
                            foreach($sector_code as $icode){
                        ?>
                        option1 = document.createElement("OPTION");
                        text1 = document.createTextNode("<?php echo $sector_name[$icode]; ?>");
                        option1.value = "<?php echo $icode; ?>";
                        option1.appendChild(text1);
                        select1.appendChild(option1);
                        <?php
                            }
                        ?>
                    }
                    else{ }
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
