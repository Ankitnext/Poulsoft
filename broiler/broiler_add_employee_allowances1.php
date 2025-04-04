<?php
//broiler_add_employee_allowances1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['employee_allowances1'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['addaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $today = date("d.m.Y");
        $sql = "SELECT * FROM `broiler_designation` WHERE `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $desig_code = $desig_name = array();
        while($row = mysqli_fetch_assoc($query)){ $desig_code[$row['code']] = $row['code']; $desig_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $branch_code = $branch_name = array();
        while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: auto;
        }
        .form-control{
            font-size: 13px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Employee Allowances</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_employee_allowances1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <table class="table1" style="width:auto;">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:center;"><label>From Date<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>To Date<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Designation<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Branch<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Per KM Cost<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>D.A.<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>T.A.<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:left;"><label></label></th>
                                                    <th style="visibility:hidden;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">
                                                <tr>
                                                    <td><input type="text" name="fdate[]" id="fdate[0]" class="form-control rc_datepicker" style="width:100px;" value="<?php echo $today; ?>" readonly /></td>
                                                    <td><input type="text" name="tdate[]" id="tdate[0]" class="form-control rc_datepicker" style="width:100px;" value="<?php echo $today; ?>" readonly /></td>
                                                    <td><select name="desig_code[]" id="desig_code[0]" class="form-control select2" onchange="check_duplicate(this.id);"><option value="select">-select-</option><?php foreach($desig_code as $dcode){ ?><option value="<?php echo $dcode; ?>"><?php echo $desig_name[$dcode]; ?></option><?php } ?></select></td>
                                                    <td><select name="branch_code[]" id="branch_code[0]" class="form-control select2" onchange="check_duplicate(this.id);"><option value="select">-select-</option><?php foreach($branch_code as $dcode){ ?><option value="<?php echo $dcode; ?>"><?php echo $branch_name[$dcode]; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="per_km_rate[]" id="per_km_rate[0]" class="form-control text-right" style="width:120px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                    <td><input type="text" name="daily_allowance[]" id="daily_allowance[0]" class="form-control text-right" style="width:120px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                    <td><input type="text" name="travel_allowance[]" id="travel_allowance[0]" class="form-control text-right" style="width:120px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                    <td id="action[0]" style="width:80px;text-align:center;"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                    <td style="visibility:hidden;"><input type="text" name="dupflag[]" id="dupflag[0]" class="form-control text-right" value="0" style="width:20px;" readonly /></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><br/>
                                    <div class="row">
                                        <div class="form-group" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
                                        </div>
                                        <div class="form-group" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
				var incr = document.getElementById("incr").value;
                var fdate = tdate = desig_code = branch_code = dupflag = rsfdate = rstdate = rsdcode = rsbcode = ""; var e = 0;
				var l = true;
				for(var d = 0;d <= incr;d++){
					if(l == true){
						e = d + 1;
						fdate = document.getElementById("fdate["+d+"]").value;
						tdate = document.getElementById("tdate["+d+"]").value;
						desig_code = document.getElementById("desig_code["+d+"]").value;
						branch_code = document.getElementById("branch_code["+d+"]").value;
						dupflag = document.getElementById("dupflag["+d+"]").value;
						
						if(desig_code == "select"){
							alert("Please select Designation in row: "+e);
							document.getElementById("desig_code["+d+"]").focus();
							l = false;
						}
						else if(branch_code == "select"){
							alert("Please select Branch in row: "+e);
							document.getElementById("branch_code["+d+"]").focus();
							l = false;
						}
						else if(parseInt(dupflag) == 1){
							alert("This Allowance is already available, Kindly check and try again in row: "+e);
							document.getElementById("desig_code["+d+"]").focus();
							l = false;
						}
						else{
                            for(var c = 0;c <= incr;c++){
                                if(l == true){
                                    if(c == d){ }
                                    else{
                                        rsfdate = document.getElementById("fdate["+c+"]").value;
                                        rstdate = document.getElementById("tdate["+c+"]").value;
                                        rsdcode = document.getElementById("desig_code["+c+"]").value;
                                        rsbcode = document.getElementById("branch_code["+c+"]").value;
                                        if(fdate == rsfdate && tdate == rstdate && desig_code == rsdcode && branch_code == rsbcode){
                                            e = c + 1;
                                            alert("Same Allowance combination available in row: "+e);
                                            document.getElementById("desig_code["+d+"]").focus();
                                            l = false;
                                        }
                                    }
                                }
                            } 
                        }
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
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_employee_allowances1.php?ccid='+ccid;
            }
			function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                var slno = d + 1;
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><input type="text" name="fdate[]" id="fdate['+d+']" class="form-control rc_datepicker" style="width:100px;" value="<?php echo $today; ?>" readonly /></td>';
                html += '<td><input type="text" name="tdate[]" id="tdate['+d+']" class="form-control rc_datepicker" style="width:100px;" value="<?php echo $today; ?>" readonly /></td>';
                html += '<td><select name="desig_code[]" id="desig_code['+d+']" class="form-control select2" onchange="check_duplicate(this.id);"><option value="select">-select-</option><?php foreach($desig_code as $dcode){ ?><option value="<?php echo $dcode; ?>"><?php echo $desig_name[$dcode]; ?></option><?php } ?></select></td>';
                html += '<td><select name="branch_code[]" id="branch_code['+d+']" class="form-control select2" onchange="check_duplicate(this.id);"><option value="select">-select-</option><?php foreach($branch_code as $dcode){ ?><option value="<?php echo $dcode; ?>"><?php echo $branch_name[$dcode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="per_km_rate[]" id="per_km_rate['+d+']" class="form-control text-right" style="width:120px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="daily_allowance[]" id="daily_allowance['+d+']" class="form-control text-right" style="width:120px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="travel_allowance[]" id="travel_allowance['+d+']" class="form-control text-right" style="width:120px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="dupflag[]" id="dupflag['+d+']" class="form-control text-right" value="0" style="width:20px;" /></td>';
				html += '</tr>';
				$('#tbody').append(html);
				$('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove(); d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
			function check_duplicate(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var fdate = document.getElementById("fdate["+d+"]").value;
				var tdate = document.getElementById("tdate["+d+"]").value;
				var desig_code = document.getElementById("desig_code["+d+"]").value;
				var branch_code = document.getElementById("branch_code["+d+"]").value;
                document.getElementById("dupflag["+d+"]"). value = 0;

				var type = "add";
				if(desig_code != "" && branch_code != ""){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_emp_allowance_duplicates.php?fdate="+fdate+"&tdate="+tdate+"&desig_code="+desig_code+"&branch_code="+branch_code+"&type="+type+"&row_count="+d;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_dt = this.responseText;
                            var dup_info = dup_dt.split("@");
                            var row = dup_info[1];
							if(parseInt(dup_info[0]) == 0){
								document.getElementById("dupflag["+row+"]"). value = 0;
							}
							else {
								alert("Employee Allowance available with the same Combinations.\n Kindly check once.");
								document.getElementById("dupflag["+row+"]"). value = 1;
							}
						}
					}
				}
				else { }
			}
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
		</script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more information"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more information";
}
?>