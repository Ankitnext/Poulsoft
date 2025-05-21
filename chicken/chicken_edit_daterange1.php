<?php
//chicken_edit_daterange1.php
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

    $database_name = $_SESSION['dbase'];
        $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$database_name' AND `flag` = '1' AND `dflag` = '0' ORDER BY `username` ASC";
        $query = mysqli_query($conns, $sql); $emp_code = $emp_name = array();
        while ($row = mysqli_fetch_assoc($query)) { $emp_code[$row['empcode']] = $row['empcode']; $emp_name[$row['empcode']] = $row['username']; }

        $sql = "SELECT * FROM `date_range_master` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($sconn,$sql); $file_url = array();
        while($row = mysqli_fetch_assoc($query)){ $file_url[$row['file_name']] = $row['file_name']; }
        $file_list = implode("','",$file_url);

        $sql = "SELECT * FROM `main_linkdetails` WHERE `href` IN ('$file_list') AND `activate` = '1' ORDER BY `name` ASC";
        $query = mysqli_query($conn,$sql); $file_code = $file_name = array();
        while($row = mysqli_fetch_assoc($query)){ $file_code[$row['href']] = $row['href']; $file_name[$row['href']] = $row['name']; }
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['id'];
            // $sql = "SELECT * FROM `dataentry_daterange` WHERE `id`='$ids'";
            // $query = mysqli_query($conn,$sql);
            // while($row = mysqli_fetch_assoc($query)){
            //    $type = $row['type'];
            //    $days = $row['days'];
            //    $users = $row['users'];
            // }

            $sql = "SELECT * FROM `dataentry_daterange_master` WHERE `id` = '$ids' AND `flag` = '0' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $ucode = $row['user_code']; }
            $sql = "SELECT * FROM `dataentry_daterange_master` WHERE `user_code` = '$ucode' AND `flag` = '0' AND `dflag` = '0' ORDER BY `id` ASC";
            $query = mysqli_query($conn,$sql); $c = 0; $file_url = array();
            while($row = mysqli_fetch_assoc($query)){
                $id_alist[$c] = $row['id'];
                $file_url[$c] = $row['file_name'];
                $min_days[$c] = $row['min_days'];
                $max_days[$c] = $row['max_days'];
                $c++;
            } $incr = $c - 1;
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Closing Stock</div>
                <form action="chicken_modify_daterange1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th colspan="4">
                                            <div class="row">
                                                <div class="form-group" style="width:200px;">
                                                    <label for="user_code">User<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="user_code" id="user_code" class="form-control select2" style="width:190px;">
                                                        <option value="<?php echo $ucode; ?>"><?php echo $emp_name[$ucode]; ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;"><label>File Name<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Start days<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>End days<b style="color:red;">&nbsp;*</b></label></th>
					
                                        <th style="visibility:hidden;">Action</th>
                                        <th style="visibility:hidden;"><label>ID</label></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                   <?php
                                        $i = $incr;
                                        for($c = 0;$c <= $i;$c++){
                                    ?>
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><select name="file_name[]" id="file_name[<?php echo $c; ?>]" class="form-control select2" style="width:190px;"><option value="<?php echo $file_url[$c]; ?>" selected><?php echo $file_name[$file_url[$c]]; ?></option></select></td>
                                        <td><input type="text" name="min_days[]" id="min_days[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $min_days[$c]; ?>" style="width:90px;" onkeyup="validate_count(this.id);" /></td>
                                        <td><input type="text" name="max_days[]" id="max_days[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $max_days[$c]; ?>" style="width:90px;" onkeyup="validate_count(this.id);" /></td>
                                        <td style="visibility:hidden;"><input type="text" name="id_alist[]" id="id_alist[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $id_alist[$c]; ?>" value="0" style="padding:0;width:30px;" readonly /></td>
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
                    window.location.href = "chicken_display_daterange1.php";
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

                function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="file_name[]" id="file_name['+d+']" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($file_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $file_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="min_days[]" id="min_days['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td><input type="text" name="max_days[]" id="max_days['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="dup_flag[]" id="dup_flag['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                html += '</tr>';
                $('#tbody').append(html);
                $('.select2').select2();
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
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
