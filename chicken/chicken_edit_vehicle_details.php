<?php
//chicken_edit_vehicle_details.php
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
            // } $today = date("d.m.Y");

           
            $sql = "SELECT * FROM `vehicle_details` WHERE `id` = '$ids' AND `active` = '1' AND `dflag` = '0' ORDER BY `id` ASC";
            $query = mysqli_query($conn,$sql); $c = 0; $file_url = array();
            while($row = mysqli_fetch_assoc($query)){
                $id_alist[$c] = $row['id'];
                $vtypes[$c] = $row['vtype'];
                $company[$c] = $row['company'];
                $vno[$c] = $row['vno'];
                $date = date("d.m.Y",strtotime($row['date']));
                $make_year = date("d.m.Y",strtotime($row['make_year']));
                $chassisno[$c] = $row['chassisno'];
                $engineno[$c] = $row['engineno'];
                $pur_date = date("d.m.Y",strtotime($row['pur_date']));
                $fc_date = date("d.m.Y",strtotime($row['fc_date']));
                $inu_date = date("d.m.Y",strtotime($row['inu_date']));
                $polu_date = date("d.m.Y",strtotime($row['polu_date']));
                $remarks[$c] = $row['remarks'];
               
                $c++;
            } $incr = $c - 1;
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Vehicle Details</div>
                <form action="chicken_modify_vehicle_details.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th style="text-align:center;"><label>Vehicle Type<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Vehicle Company<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Vehicle No.<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Make Year<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Chassis No.<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Engine No.<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Purchase Date<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>FC Upto<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Insurance Upto<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Pollution Upto<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Remarks</label></th>
                                      
					
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
                                        <td>
                                            <select name="vtype[]" id="vtype[<?php echo $c; ?>]" class="form-control select2" style="width:180px;" onchange="">
                                                <option value="select" <?php if($vtypes[$c] == "select"){ echo "selected";} ?>>-select-</option>
                                                <option value="2 Wheeler" <?php if($vtypes[$c] == "2 Wheeler"){ echo "selected";} ?>>-2 Wheeler-</option>
                                                <option value="4 Wheeler" <?php if($vtypes[$c] == "4 Wheeler"){ echo "selected";} ?>>-4 Wheeler-</option>
                                                <option value="6 Wheeler" <?php if($vtypes[$c] == "6 Wheeler"){ echo "selected";} ?>>-6 Wheeler-</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="vcomp[]" id="vcomp[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $company[$c]; ?>" style="width:90px;" /></td>
                                        <td><input type="text" name="vno[]" id="vno[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $vno[$c]; ?>" style="width:90px;" /></td>
                                        <td><input type="text" name="myear[]" id="myear[<?php echo $c; ?>]" class="form-control datepickers" value="<?php echo $make_year; ?>" style="min-width:100px;" readonly></td>
                                        <td><input type="text" name="chsno[]" id="chsno[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $chassisno[$c]; ?>" style="width:90px;" /></td>
                                        <td><input type="text" name="engno[]" id="engno[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $engineno[$c]; ?>" style="width:90px;" /></td>
                                        <td><input type="text" name="pdate[]" id="pdate[<?php echo $c; ?>]" class="form-control datepickers" value="<?php echo $pur_date; ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><input type="text" name="fcupto[]" id="fcupto[<?php echo $c; ?>]" class="form-control datepickers" value="<?php echo $fc_date; ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><input type="text" name="insupto[]" id="insupto[<?php echo $c; ?>]" class="form-control datepickers" value="<?php echo $inu_date; ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><input type="text" name="polupto[]" id="polupto[<?php echo $c; ?>]" class="form-control datepickers" value="<?php echo $polu_date; ?>" style="width:100px;" onchange="" readonly /></td>
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
                    window.location.href = "chicken_display_vehicle_details.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var vtype = vcomp = vno = myear = pdate = chsno = engno = fcupto = insupto = polupto = ""; 
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            vtype = document.getElementById("vtype["+d+"]").value;
                            vcomp = document.getElementById("vcomp["+d+"]").value;
                            vno = document.getElementById("vno["+d+"]").value;
                            myear = document.getElementById("myear["+d+"]").value;
                            pdate = document.getElementById("pdate["+d+"]").value;
                            chsno = document.getElementById("chsno["+d+"]").value;
                            engno = document.getElementById("engno["+d+"]").value;
                            fcupto = document.getElementById("fcupto["+d+"]").value;
                            insupto = document.getElementById("insupto["+d+"]").value;
                            polupto = document.getElementById("polupto["+d+"]").value;
                            // birds = document.getElementById("birds["+d+"]").value; if(birds == ""){ birds = 0; }
                            // price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                            // amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            
                            if(vtype == "" || vtype == "select"){
                                alert("Please select Vehicle Type in row: "+c);
                                document.getElementById("code["+d+"]").focus();
                                l = false;
                            } 
                            else if(vcomp == ""){
                                alert("Please select Company in row: "+c);
                                document.getElementById("vcomp["+d+"]").focus();
                                l = false;
                            }
                            else if(vno == ""){
                                alert("Please select Vehicle Number in row: "+c);
                                document.getElementById("vno["+d+"]").focus();
                                l = false;
                            } 
                            else if(myear == ""){
                                alert("Please select Make Year Date in row: "+c);
                                document.getElementById("myear["+d+"]").focus();
                                l = false;
                            }  
                            else if(chsno == ""){
                                alert("Please select Chassis Number in row: "+c);
                                document.getElementById("chsno["+d+"]").focus();
                                l = false;
                            } 
                            else if(engno == ""){
                                alert("Please select Engine Number in row: "+c);
                                document.getElementById("engno["+d+"]").focus();
                                l = false;
                            }
                            else if(pdate == ""){
                                alert("Please select Purchase Date in row: "+c);
                                document.getElementById("pdate["+d+"]").focus();
                                l = false;
                            }
                            else if(fcupto == ""){
                                alert("Please select FC Date in row: "+c);
                                document.getElementById("fcupto["+d+"]").focus();
                                l = false;
                            }
                            else if(insupto == ""){
                                alert("Please select Insurance Date in row: "+c);
                                document.getElementById("insupto["+d+"]").focus();
                                l = false;
                            }
                            else if(polupto == ""){
                                alert("Please select Pollution Date in row: "+c);
                                document.getElementById("polupto["+d+"]").focus();
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
                html += '<td><select name="vtype[]" id="vtype['+d+']" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><option value="2 Wheeler">-2 Wheeler-</option><option value="4 Wheeler">-4 Wheeler-</option><option value="6 Wheeler">-6 Wheeler-</option></select></td>';
                html += '<td><input type="text" name="vcomp[]" id="vcomp['+d+']" class="form-control text-right" style="width:90px;" /></td>';
                html += '<td><input type="text" name="vno[]" id="vno['+d+']" class="form-control text-right" style="width:90px;" /></td>';
                html += '<td><input type="text" name="myear[]" id="myear['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($myear)); ?>" style="width:100px;" onchange="" readonly /></td>';
                html += '<td><input type="text" name="chsno[]" id="chsno['+d+']" class="form-control text-right" style="width:90px;" /></td>';
                html += '<td><input type="text" name="engno[]" id="engno['+d+']" class="form-control text-right" style="width:90px;" /></td>';
                html += '<td><input type="text" name="pdate[]" id="pdate['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($pdate)); ?>" style="width:100px;" onchange="" readonly /></td>';
                html += '<td><input type="text" name="fcupto[]" id="fcupto['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:100px;" onchange="" readonly /></td>';
                html += '<td><input type="text" name="insupto[]" id="insupto['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($idate)); ?>" style="width:100px;" onchange="" readonly /></td>';
                html += '<td><input type="text" name="polupto[]" id="polupto['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($pldate)); ?>" style="width:100px;" onchange="" readonly /></td>';
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
