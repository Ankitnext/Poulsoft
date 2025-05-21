<?php
//chicken_add_daterange1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = $today = date("d.m.Y");
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
                <div class="card-header">Add daterange</div>
                <form action="chicken_save_daterange1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th style="text-align:center;"><label>User<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>File Name<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Start days<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>End days<b style="color:red;">&nbsp;*</b></label></th>
                                            
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

                                        <td><select name="user_code[]" id="user_code[0]" class="form-control select2" style="width:190px;"><option value="all">-All-</option><?php foreach($emp_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $emp_name[$ucode]; ?></option><?php } ?></select></td>
                                        <td><select name="file_name[]" id="file_name[0]" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($file_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $file_name[$ucode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="min_days[]" id="min_days[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>
                                        <td><input type="text" name="max_days[]" id="max_days[0]" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>
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
                    window.location.href = "chicken_display_daterange1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var date = code = mtype = ccode = ""; var birds = price = amount = c = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            code = document.getElementById("code["+d+"]").value;
                            birds = document.getElementById("birds["+d+"]").value; if(birds == ""){ birds = 0; }
                            price = document.getElementById("price["+d+"]").value; if(price == ""){ price = 0; }
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            mtype = document.getElementById("mtype["+d+"]").value;
                            ccode = document.getElementById("ccode["+d+"]").value;
                            
                            if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(code == "" || code == "select"){
                                alert("Please select Item in row: "+c);
                                document.getElementById("code["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(birds) == 0){
                                alert("Please enter Birds in row: "+c);
                                document.getElementById("birds["+d+"]").focus();
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
                            else if(mtype == "" || mtype == "select"){
                                alert("Please select Mortality On in row: "+c);
                                document.getElementById("mtype["+d+"]").focus();
                                l = false;
                            }
                            else if(ccode == "" || ccode == "select"){
                                alert("Please select Customer / Warehouse in row: "+c);
                                document.getElementById("ccode["+d+"]").focus();
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
                function create_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("action["+d+"]").style.visibility = "hidden";

                    d++; var html = '';
                    document.getElementById("incr").value = d;
                    var jals_flag = '<?php echo (int)$jals_flag; ?>';
                    var birds_flag = '<?php echo (int)$birds_flag; ?>';
                    html += '<tr id="row_no['+d+']">';
                    html += '<td><select name="user_code[]" id="user_code['+d+']" class="form-control select2" style="width:190px;"><option value="all">-All-</option><?php foreach($emp_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $emp_name[$ucode]; ?></option><?php } ?></select></td>';
                    html += '<td><select name="file_name[]" id="file_name['+d+']" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($file_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $file_name[$ucode]; ?></option><?php } ?></select></td>';
                    html += '<td><input type="text" name="min_days[]" id="min_days['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                    html += '<td><input type="text" name="max_days[]" id="max_days['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                    
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '<td style="visibility:hidden;"><input type="text" name="dup_flag[]" id="dup_flag['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                    var rng_mdate = '<?php echo $rng_mdate; ?>';
                    var today = '<?php echo $today; ?>';
                    $('.cst_datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: rng_mdate,maxDate: today,autoclose: true });
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
