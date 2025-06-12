<?php
//chicken_add_vehicle_details.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = $today = $myear = $pdate = $fdate = $kdate = $pldate = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Closing Stock"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT distinct `type` from `dataentry_daterange` WHERE `active`='1'";
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

    $database_name = $_SESSION['dbase'];
    $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$database_name' AND `flag` = '1' AND `dflag` = '0' ORDER BY `username` ASC";
    $query = mysqli_query($conns, $sql); $emp_code = $emp_name = array();
    while ($row = mysqli_fetch_assoc($query)) { $emp_code[$row['empcode']] = $row['empcode']; $emp_name[$row['empcode']] = $row['username']; }

    $sql = "SELECT * FROM `date_range_master` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($sconn,$sql); $file_url = array();
    while($row = mysqli_fetch_assoc($query)){  $file_url[$row['file_name']] = $row['file_name']; }
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
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Vehicle Details</div>
                <form action="chicken_save_vehicle_details.php" method="post" onsubmit="return checkval();">
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
                                        <th style="visibility:hidden;"><label>DF</label></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr id="row_no[0]" style="margin:5px 0px 5px 0px;">

                                        <!-- <td><select name="type[]" id="type[0]" class="form-control select2" style="width:180px;" onchange="fetch_dateentry_details(this.id)"><option value="select">-select-</option><?php foreach($type_list as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $scode; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="days[]" id="days[0]" class="form-control" style="min-width:100px;"></td>
                                        <td><select name="users[0][]" id="users[0]" class="form-control select2" style="width:180px;" multiple onchange="fetch_dateentry_details(this.id)"><option value="select">-select-</option><?php foreach($username as $ucode){ ?><option value="<?php echo $empcode[$ucode]; ?>"><?php echo $ucode; ?></option><?php } ?></select></td>
                                         -->

                                        <td><select name="vtype[]" id="vtype[0]" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><option value="2 Wheeler">-2 Wheeler-</option><option value="4 Wheeler">-4 Wheeler-</option><option value="6 Wheeler">-6 Wheeler-</option></select></td>
                                        <td><input type="text" name="vcomp[]" id="vcomp[0]" class="form-control text-right" style="width:90px;" /></td>
                                        <td><input type="text" name="vno[]" id="vno[0]" class="form-control text-right" style="width:90px;" /></td>
                                        <td><input type="text" name="myear[]" id="myear[0]" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($myear)); ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><input type="text" name="chsno[]" id="chsno[0]" class="form-control text-right" style="width:90px;" /></td>
                                        <td><input type="text" name="engno[]" id="engno[0]" class="form-control text-right" style="width:90px;" /></td>
                                        <td><input type="text" name="pdate[]" id="pdate[0]" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($pdate)); ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><input type="text" name="fcupto[]" id="fcupto[0]" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><input type="text" name="insupto[]" id="insupto[0]" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($kdate)); ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><input type="text" name="polupto[]" id="polupto[0]" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($pldate)); ?>" style="width:100px;" onchange="" readonly /></td>
                                        <td><textarea name="remark[]" id="remark[0]" class="form-control text-right" style="width:90px;"></textarea></td>
                                        <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                        <td style="visibility:hidden;"><input type="text" name="dup_flag[]" id="dup_flag[0]" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>
                                    </tr>
                                </tbody>
    
                            </table>
                        </div><br/>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
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
               function checkval() {
                    document.getElementById("ebtncount").value = "1";
                    document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var incr = parseInt(document.getElementById("incr").value); // Ensure it's a number
                    for (var d = 0; d <= incr; d++) {
                        if (l == true) {
                            var c = d + 1;
                            var vtype = document.getElementById("vtype["+d+"]").value;
                            var vcomp = document.getElementById("vcomp["+d+"]").value;
                            var vno = document.getElementById("vno["+d+"]").value;
                            var myear = document.getElementById("myear["+d+"]").value;
                            var pdate = document.getElementById("pdate["+d+"]").value;
                            var chsno = document.getElementById("chsno["+d+"]").value;
                            var engno = document.getElementById("engno["+d+"]").value;
                            var fcupto = document.getElementById("fcupto["+d+"]").value;
                            var insupto = document.getElementById("insupto["+d+"]").value;
                            var polupto = document.getElementById("polupto["+d+"]").value;

                            if (vtype === "" || vtype === "select") {
                                alert("Please select Vehicle Type in row: " + c);
                                document.getElementById("vtype["+d+"]").focus();
                                l = false;
                            } 
                            else if (vcomp === "") {
                                alert("Please select Company in row: " + c);
                                document.getElementById("vcomp["+d+"]").focus();
                                l = false;
                            }
                            else if (vno === "") {
                                alert("Please select Vehicle Number in row: " + c);
                                document.getElementById("vno["+d+"]").focus();
                                l = false;
                            } 
                            else if (myear === "") {
                                alert("Please select Make Year Date in row: " + c);
                                document.getElementById("myear["+d+"]").focus();
                                l = false;
                            }  
                            else if (chsno === "") {
                                alert("Please select Chassis Number in row: " + c);
                                document.getElementById("chsno["+d+"]").focus();
                                l = false;
                            } 
                            else if (engno === "") {
                                alert("Please select Engine Number in row: " + c);
                                document.getElementById("engno["+d+"]").focus();
                                l = false;
                            }
                            else if (pdate === "") {
                                alert("Please select Purchase Date in row: " + c);
                                document.getElementById("pdate["+d+"]").focus();
                                l = false;
                            }
                            else if (fcupto === "") {
                                alert("Please select FC Date in row: " + c);
                                document.getElementById("fcupto["+d+"]").focus();
                                l = false;
                            }
                            else if (insupto === "") {
                                alert("Please select Insurance Date in row: " + c);
                                document.getElementById("insupto["+d+"]").focus();
                                l = false;
                            }
                            else if (polupto === "") {
                                alert("Please select Pollution Date in row: " + c);
                                document.getElementById("polupto["+d+"]").focus();
                                l = false;
                            }

                            if (l === false) break; // Stop loop on first validation failure
                        }
                    }

                    if (l === false) {
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false; // Stop form submission
                    }

                    return true; // Allow submission
                }

                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";

                    d++; var html = '';
                    document.getElementById("incr").value = d;
                    var jals_flag = '<?php echo (int)$jals_flag; ?>';
                    var birds_flag = '<?php echo (int)$birds_flag; ?>';
                    html += '<tr id="row_no['+d+']">';
                    html += '<td><select name="vtype[]" id="vtype['+d+']" class="form-control select2" style="width:180px;" onchange=""><option value="select">-select-</option><option value="2 Wheeler">-2 Wheeler-</option><option value="4 Wheeler">-4 Wheeler-</option><option value="6 Wheeler">-6 Wheeler-</option></select></td>';
                    html += '<td><input type="text" name="vcomp[]" id="vcomp['+d+']" class="form-control text-right" style="width:90px;" /></td>';
                    html += '<td><input type="text" name="vno[]" id="vno['+d+']" class="form-control text-right" style="width:90px;" /></td>';
                    html += '<td><input type="text" name="myear[]" id="myear['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($myear)); ?>" style="width:100px;" onchange="" readonly /></td>';
                    html += '<td><input type="text" name="chsno[]" id="chsno['+d+']" class="form-control text-right" style="width:90px;" /></td>';
                    html += '<td><input type="text" name="engno[]" id="engno['+d+']" class="form-control text-right" style="width:90px;" /></td>';
                    html += '<td><input type="text" name="pdate[]" id="pdate['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($pdate)); ?>" style="width:100px;" onchange="" readonly /></td>';
                    html += '<td><input type="text" name="fcupto[]" id="fcupto['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:100px;" onchange="" readonly /></td>';
                    html += '<td><input type="text" name="insupto[]" id="insupto['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($kdate)); ?>" style="width:100px;" onchange="" readonly /></td>';
                    html += '<td><input type="text" name="polupto[]" id="polupto['+d+']" class="form-control sale_datepickers" value="<?php echo date("d.m.Y",strtotime($pldate)); ?>" style="width:100px;" onchange="" readonly /></td>';
                    html += '<td><textarea name="remark[]" id="remark['+d+']" class="form-control text-right" style="width:90px;"></textarea></td>';
                    
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="dup_flag[]" id="dup_flag['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                    var rng_mdate = '<?php echo $rng_mdate; ?>';
                    var today = '<?php echo $today; ?>';
                    $('.sale_datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: rng_mdate,maxDate: today,autoclose: true });
                    // $('.cst_datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: rng_mdate,maxDate: today,autoclose: true });
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                    calculate_final_total_amount();
                }
               

                function fetch_dateentry_details(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    var type_val = document.getElementById("type["+d+"]").value;
                    var users_val = document.getElementById("users["+d+"]").value;
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
            function validate_count(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
