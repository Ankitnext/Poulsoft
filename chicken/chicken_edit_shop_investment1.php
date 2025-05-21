<?php
//chicken_edit_shop_investment1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Stock Adjustment"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
   
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; 
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
	while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `item_category` WHERE `description` LIKE '%Investment' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $icat_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; }
  
    $icat_list =  implode("','", $icat_alist);
    $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' ORDER BY `description` ASC";
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
            $sql = "SELECT * FROM `vendor_shop_investment` WHERE `trnum` = '$ids' AND `trlink` = 'chicken_display_shop_investment1.php' AND `dflag` = '0'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $date = date("d.m.Y",strtotime($row['date']));
                $customer = $row['vcode'];
                $itemcode = $row['icode'];
                $amount = round($row['amount'],2);
                $remarks = $row['remarks'];
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Stock Adjustment</div>
                <form action="chicken_modify_shop_investment1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Customer<b style="color:red;">&nbsp;*</b></th>
                                        <th>Item<b style="color:red;">&nbsp;*</b></th>
                                        <th>Amount<b style="color:red;">&nbsp;*</b></th>
                                        <th>Remarks</th>
                                        <th style="visibility:hidden;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date" id="date" class="form-control sale_datepickers" value="<?php echo $date; ?>" style="min-width:100px;"></td>
                                        <td><select name="customer" id="customer" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($cus_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $customer){ echo "selected"; } ?>><?php echo $cus_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="itemcode" id="itemcode" class="form-control select2" style="width:180px;"><option value="select">-select-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $itemcode){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="amount" id="amount" class="form-control text-right" value="<?php echo $amount; ?>" style="width:90px;" /></td>
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
                    window.location.href = "chicken_display_shop_investment1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var date = a_type = warehouse = itemcode = ""; var quantity = price = amount = c = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            customer = document.getElementById("customer["+d+"]").value;
                            itemcode = document.getElementById("itemcode["+d+"]").value;
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            
                            if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(customer == "" || customer == "select"){
                                alert("Please select Customer in row: "+c);
                                document.getElementById("customer["+d+"]").focus();
                                l = false;
                            }
                            else if(itemcode == "" || itemcode == "select"){
                                alert("Please select Item in row: "+c);
                                document.getElementById("itemcode["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Quantity/Price in row: "+c);
                                document.getElementById("amount["+d+"]").focus();
                                l = false;
                            }
                            else{ }
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
               
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
