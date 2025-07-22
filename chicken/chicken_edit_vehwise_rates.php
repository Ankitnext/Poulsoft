<?php
//chicken_edit_vehwise_rates.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Closing Stock"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }


     $sql = "SELECT distinct type from `dataentry_daterange` WHERE `active`='1'";
    $query = mysqli_query($conn,$sql); $type_list = array();
    while($row = mysqli_fetch_assoc($query)){
        $type_list[$row['type']] = $row['type'];
    }

    $sql = "SELECT * FROM `log_useraccess` WHERE `dflag`='0'";
    $query = mysqli_query($conns,$sql); $username = $empcode = array();
    while($row = mysqli_fetch_assoc($query)){
         $username[$row['username']] = $row['username'];
         $empcode[$row['username']] = $row['empcode'];
    }
    
    $sql = "SELECT * FROM `main_transactionfields` WHERE `field` LIKE 'Closing Stock' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; }
    if($jals_flag == "" || $jals_flag == NULL){ $jals_flag = 0; } if($birds_flag == "" || $birds_flag == NULL){ $birds_flag = 0; }

     $sql = "SELECT * FROM `inv_sectors` WHERE `flag`='0'";
    $query = mysqli_query($conn,$sql); $wcode = $wname = array();
    while($row = mysqli_fetch_assoc($query)){
        $wcode[$row['code']] = $row['code'];
        $wname[$row['code']] = $row['description'];
    }
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['id'];
            $sql = "SELECT * FROM `chicken_vehicle_kmw_rate` WHERE `id`='$ids'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
               $date = $row['date'];
               $warehouse = $row['warehouse'];
               $rate = $row['rate'];
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Vehicle KM wise amount</div>
                <form action="chicken_modify_vehwise_rates.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                       <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Vehicle<b style="color:red;">&nbsp;*</b></th>
                                        <th>Rate<b style="color:red;">&nbsp;*</b></th>
					
                                        <th style="visibility:hidden;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date" id="date" value="<?php echo $date; ?>" class="form-control datepickers" style="min-width:100px;"></td>
                                        <td><select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;"><?php foreach($wcode as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $warehouse){ echo "selected"; } ?>><?php echo $wname[$scode]; ?></option><?php } ?></select></td>

                                        <td><input type="text" name="rate" id="rate" value="<?php echo $rate; ?>" class="form-control" style="min-width:100px;"></td>

                                    
                                   
                                     
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
                    window.location.href = "chicken_display_vehwise_rates.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var birds_flag = '<?php echo (int)$birds_flag; ?>';

                    var date = document.getElementById("date").value;
                    var warehouse = document.getElementById("warehouse").value;
                    var code = document.getElementById("code").value;
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
                    else if(code == "" || code == "select"){
                        alert("Please select Item");
                        document.getElementById("code").focus();
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
                        alert("Please enter Birds/Price");
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
                    var birds_flag = '<?php echo (int)$birds_flag; ?>';
                    if(parseInt(birds_flag) == 1){ var birds = document.getElementById("birds").value; if(birds == ""){ birds = 0; } }
                    var quantity = document.getElementById("quantity").value; if(quantity == ""){ quantity = 0; }
                    var price = document.getElementById("price").value; if(price == ""){ price = 0; }

                    if(parseInt(birds_flag) == 1){ var amount = parseFloat(birds) * parseFloat(price); if(amount == ""){ amount = 0; } }
                    else{ amount = parseFloat(quantity) * parseFloat(price); if(amount == ""){ amount = 0; } }
                    document.getElementById("amount").value = parseFloat(amount).toFixed(0);
                }


                function fetch_dateentry_details(a){
                    //var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var type_val = document.getElementById("type").value;
                    var users_val = document.getElementById("users").value;
                    var selectElement = document.getElementById(a);
                    var selectedValues = [];  
                    for (var i = 0; i < selectElement.options.length; i++) {
                        if (selectElement.options[i].selected) {
                        selectedValues.push(selectElement.options[i].value);
                        }
                    }  
                    var empcodes = selectedValues.join(',');
                    var fetch_fltrs = new XMLHttpRequest();
                    var method = "GET";
                    var url = "fetch_dateentry_details.php?type="+type_val+"&empcode="+empcodes//window.open(url);
                    var asynchronous = true;
                    fetch_fltrs.open(method, url, asynchronous);
                    fetch_fltrs.send();
                    fetch_fltrs.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){  
                            if(this.responseText == "EXIST"){
                                alert('Combination already exist');
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
