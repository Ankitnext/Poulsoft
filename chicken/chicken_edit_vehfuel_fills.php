<?php
//chicken_edit_vehfuel_fills.php
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

        $sql = "SELECT * FROM `vehicle_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC";
        $query = mysqli_query($conn, $sql); $vno_code = $vno_name = array();
        while ($row = mysqli_fetch_assoc($query)) { $vno_code[$row['trnum']] = $row['trnum']; $vno_name[$row['trnum']] = $row['vno']; }

        $sql = "SELECT * FROM `driver_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC";
        $query = mysqli_query($conn, $sql); $drv_code = $drv_name = array();
        while ($row = mysqli_fetch_assoc($query)) { $drv_code[$row['code']] = $row['code']; $drv_name[$row['code']] = $row['name']; }

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

          
            $sql = "SELECT * FROM `vehicle_fuelfilling` WHERE `id` = '$ids' AND `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC";
            $query = mysqli_query($conn,$sql); $c = 0; $file_url = array();
            while($row = mysqli_fetch_assoc($query)){
                $id_alist[$c] = $row['id'];
                $date = date("d.m.Y",strtotime($row['date']));
                $vno[$c] = $row['vno'];
                $driver[$c] = $row['driver'];
                $reading[$c] = $row['reading'];
                $cur_reading[$c] = $row['cur_reading'];
                $fueltype[$c] = $row['fueltype'];
                $fuel_lt[$c] = $row['fuel_lt'];
                $price[$c] = $row['price'];
                $amount[$c] = $row['amount'];
                $remarks[$c] = $row['remarks'];
                $c++;
            } $incr = $c - 1;
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Vehicle Fuel Filling</div>
                <form action="chicken_modify_vehfuel_fills.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    
                                    <tr>
                                       <th style="text-align:center;"><label>Date<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Vehicle No.<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Driver Name<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Meter P. Reading<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Meter Current Reading<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Fuel Type<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Fuel In Ltrs<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Price<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Remarks<b style="color:red;">&nbsp;*</b></label></th>
					
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
                                        <td><input type="text" name="date[]" id="date[<?php echo $c; ?>]" class="form-control datepickers" value="<?php echo $date; ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><select name="vno[]" id="vno[<?php echo $c; ?>]" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($vno_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $vno[$c]) { echo "selected"; } ?>><?php echo $vno_name[$ucode]; ?></option><?php } ?></select></td>
                                        <td><select name="drname[]" id="drname[<?php echo $c; ?>]" class="form-control select2" style="width:190px;"><option value="all">-All-</option><?php foreach($drv_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $driver[$c]) { echo "selected"; } ?>><?php echo $drv_name[$ucode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="mp_read[]" id="mp_read[<?php echo $c; ?>]" class="form-control text-right" style="width:90px;" value="<?php echo $reading[$c]; ?>" onkeyup="validate_count(this.id);" /></td>
                                        <td><input type="text" name="mc_read[]" id="mc_read[<?php echo $c; ?>]" class="form-control text-right" style="width:90px;" value="<?php echo $cur_reading[$c]; ?>" onkeyup="validate_count(this.id);" /></td>
                                         <td>
                                            <select name="ful_typ[]" id="ful_typ[<?php echo $c; ?>]" class="form-control select2" style="width:180px;" onchange="">
                                                <option value="select" <?php if($fueltype[$c] == "select"){ echo "selected";} ?>>-select-</option>
                                                <option value="Diesel" <?php if($fueltype[$c] == "Diesel"){ echo "selected";} ?>>-Diesel-</option>
                                                <option value="Petrol" <?php if($fueltype[$c] == "Petrol"){ echo "selected";} ?>>-Petrol-</option>
                                                <option value="CNG" <?php if($fueltype[$c] == "CNG"){ echo "selected";} ?>>-CNG-</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="ful_ltrs[]" id="ful_ltrs[<?php echo $c; ?>]" class="form-control text-right" style="width:90px;" value="<?php echo $fuel_lt[$c]; ?>" onkeyup="validate_count(this.id);" /></td>
                                        <td><input type="text" name="price[]" id="price[<?php echo $c; ?>]" class="form-control text-right" style="width:90px;" value="<?php echo $price[$c]; ?>" onkeyup="validate_count(this.id);" /></td>
                                        <td><input type="text" name="amount[]" id="amount[<?php echo $c; ?>]" class="form-control text-right" style="width:90px;" value="<?php echo $amount[$c]; ?>" onkeyup="validate_count(this.id);" /></td>
                                        <td><textarea name="remark[]" id="remark[<?php echo $c; ?>]" class="form-control text-right" style="width:90px;"><?php echo $remarks[$c]; ?></textarea></td>                                        
                                        
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
                    window.location.href = "chicken_display_vehfuel_fills.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var date = vno = drname = ful_typ = ""; var mp_read = mc_read = ful_typ = ful_ltrs = price = amount = c = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            vno = document.getElementById("vno["+d+"]").value;
                            drname = document.getElementById("drname["+d+"]").value;
                            mp_read = document.getElementById("mp_read["+d+"]").value; if(mp_read == ""){ mp_read = 0; }
                            mc_read = document.getElementById("mc_read["+d+"]").value; if(mc_read == ""){ mc_read = 0; }
                            ful_typ = document.getElementById("ful_typ["+d+"]").value; 
                            ful_ltrs = document.getElementById("ful_ltrs["+d+"]").value; if(ful_ltrs == ""){ ful_ltrs = 0; }
                            price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                           
                            
                            if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(vno == "" || vno == "select"){
                                alert("Please select Vehicle Number in row: "+c);
                                document.getElementById("vno["+d+"]").focus();
                                l = false;
                            }
                            else if(drname == "" || drname == "select"){
                                alert("Please select Driver Name in row: "+c);
                                document.getElementById("drname["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(mp_read) == 0){
                                alert("Please enter Meter P. Reading in row: "+c);
                                document.getElementById("mp_read["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(mc_read) == 0){
                                alert("Please enter Meter Current Reading in row: "+c);
                                document.getElementById("mc_read["+d+"]").focus();
                                l = false;
                            }
                            else if(ful_typ == "" || ful_typ == "select"){
                                alert("Please select Fuel Type in row: "+c);
                                document.getElementById("ful_typ["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(ful_ltrs) == 0){
                                alert("Please enter Fuel in Liters in row: "+c);
                                document.getElementById("ful_ltrs["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(price) == 0){
                                alert("Please enter Price in row: "+c);
                                document.getElementById("price["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Birds/Price in row: "+c);
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
                        if (event) event.preventDefault();
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
                html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="" readonly /></td>';
                html += '<td><select name="vno[]" id="vno['+d+']" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($vno_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $vno_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><select name="drname[]" id="drname['+d+']" class="form-control select2" style="width:190px;"><option value="all">-All-</option><?php foreach($drv_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $drv_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="mp_read[]" id="mp_read['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td><input type="text" name="mc_read[]" id="mc_read['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td><select name="ful_typ[]" id="ful_typ[0]" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><option value="Diesel">-Diesel-</option><option value="Petrol">-Petrol-</option><option value="CNG">-CNG-</option></select></td>';
                html += '<td><input type="text" name="ful_ltrs[]" id="ful_ltrs['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td><input type="text" name="price[]" id="price['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td><textarea name="remark[]" id="remark['+d+']" class="form-control text-right" style="width:90px;"></textarea></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="dup_flag[]" id="dup_flag['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                html += '</tr>';
                $('#tbody').append(html);
                $('.select2').select2();
                var rng_mdate = '<?php echo $rng_mdate; ?>';
                var today = '<?php echo $today; ?>';
                $('.sale_datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: rng_mdate,maxDate: today,autoclose: true });
                  
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
