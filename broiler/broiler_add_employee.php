<?php
//broiler_add_employee.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['employee'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $href = $uri[1];
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
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Item Transfer' AND `field_function` LIKE 'Employee-Stock Transfer Expense' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $este_dflag = mysqli_num_rows($query);
        if((int)$este_dflag == 1){
            $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `dflag` = '0' AND `este_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $ecoa_code[$row['code']] = $row['code']; $ecoa_name[$row['code']] = $row['description']; }
        }

        $sql = "SELECT MAX(id) as incr FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
        if ($row = mysqli_fetch_assoc($query)) {
            // Store the maximum id value in a PHP variable
            $incr = $row['incr'];
            
            // Increase the value by 1 and store it in a new variable
            $incrs = $incr + 1;
            if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
            $new_incr = "EMP-".$incrs;
        
        }
		
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
                            <div class="float-left"><h3 class="card-title">Add Employee</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_employee.php" method="post" role="form" onsubmit="return checkval()" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Title<b style="color:red;">&nbsp;*</b></label>
							                    <select name="title" id="title" class="form-control select2">
                                                    <option value="select">select</option>
                                                    <option value="Mr.">Mr.</option>
                                                    <option value="Mrs.">Mrs.</option>
                                                    <option value="Miss.">Miss</option>
                                                    <option value="Master.">Master.</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>employee Name<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="name" id="name" class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>employee ID<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="emp_id" id="emp_id" class="form-control" placeholder="Enter description..." value="<?php echo $new_incr ?>" onkeyup="validatename(this.id)" >
                                            </div>
                                        </div>
                                        <div class="col-md-3"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Mobile</label>
							                <input type="text" name="mobile" id="mobile" class="form-control" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" name="email" id="email" class="form-control" onkeyup="validateemail(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <select name="gender" id="gender" class="form-control select2">
                                                    <option value="select">select</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Designation<b style="color:red;">&nbsp;*</b></label>
							                    <select name="desig_code" id="desig_code" class="form-control select2">
                                                    <option value="select">-select-</option>
                                                    <?php
                                                        $sql = "SELECT * FROM `broiler_designation` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Birth Date</label>
							                <input type="text" name="birth_date" id="birth_date" class="form-control datepicker" value="<?php echo date("d.m.Y"); ?>" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Join Date</label>
							                <input type="text" name="join_date" id="join_date" class="form-control datepicker" value="<?php echo date("d.m.Y"); ?>" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-3"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label>Gross Salary</label>
                                                    <input type="text" name="gross_salary" id="gross_salary" class="form-control" />
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Sector<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="warehouse" id="warehouse" class="form-control select2" style="width:160px;">
                                                        <option value="select">select</option>
                                                        <?php foreach($sector_code as $wcode){ ?><option value="<?php echo $wcode; ?>"><?php echo $sector_name[$wcode]; ?></option><?php } ?>
                                                    </select>
                                                </div>
                                                <?php if((int)$este_dflag == 1){ ?>
                                                <div class="col-md-4 form-group">
                                                    <label for="este_code">Emp. CoA<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="este_code" id="este_code" class="form-control select2" style="width:160px;">
                                                        <option value="select">select</option>
                                                        <?php foreach($ecoa_code as $wcode){ ?><option value="<?php echo $wcode; ?>"><?php echo $ecoa_name[$wcode]; ?></option><?php } ?>
                                                    </select>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>PAN No.</label>
                                                    <input type="text" name="pan_no" id="pan_no" class="form-control" />
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Aadhar No.</label>
                                                    <input type="text" name="aadhar_no" id="aadhar_no" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>UAN No.</label>
                                                    <input type="text" name="uan_no" id="uan_no" class="form-control" />
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>ESI No.</label>
                                                    <input type="text" name="esi_no" id="esi_no" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>Account No.</label>
                                                    <input type="text" name="bank_acc_no" id="bank_acc_no" class="form-control" />
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>IFSC Code.</label>
                                                    <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>Bank Name</label>
                                                    <input type="text" name="bank_name" id="bank_name" class="form-control" />
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Branch</label>
                                                    <input type="text" name="bank_branch_name" id="bank_branch_name" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                            <label>Street</label>
							                <input type="text" name="street_name" id="street_name" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>City</label>
							                <input type="text" name="city_name" id="city_name" class="form-control" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>State</label>
							                <select name="state_code" id="state_code" class="form-control select2">
                                                <option value="select">select</option>
                                                <?php
                                                $sql = "SELECT * FROM `country_states` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                                while($row = mysqli_fetch_assoc($query)){
                                                ?>
                                                <option value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Pincode</label>
							                <input type="text" name="pincode" id="pincode" class="form-control" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Country</label>
							                <input type="text" name="country" id="country" class="form-control" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="col-md-12" align="center">
                                        <div class="row" align="center">
                                            <div class="col-md-1"></div>
                                            <div class="col-md-2">
                                                <div class="form-group1">
                                                    <label>Employee Photo</label>
                                                    <input type="file" name="emp_photo_path" id="emp_photo_path" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                    <label>Reference-1</label>
                                                    <input type="file" name="file_path_1" id="file_path_1" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group1">
                                                    <label>Reference-2</label>
                                                    <input type="file" name="file_path_2" id="file_path_2" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group1">
                                                    <label>Reference-3</label>
                                                    <input type="file" name="file_path_3" id="file_path_4" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group1">
                                                    <label>Reference-4</label>
                                                    <input type="file" name="file_path_4" id="file_path_4" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                            <label>Note</label>
							                <textarea name="remarks" id="remarks" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="form-group col-md-1" style="visibility:hidden;">
                                                <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                            </div>
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
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_employee.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var a = document.getElementById("name").value;
                var b = document.getElementById("desig_code").value;
                var l = true;
                if(a.length == 0){
                    alert("Please enter employee Name ..!");
                    document.getElementById("name").focus();
                    l = false;
                }
                else if(b.match("select")){
                    alert("Please select employee Designation ..!");
                    document.getElementById("desig_code").focus();
                    l = false;
                }
                else{
                    l = true;
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
            function validatename(x) {
                expr = /^[a-zA-Z0-9 (.&)_-]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 50){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, '');
                }
                document.getElementById(x).value = a;
            }
            function validateemail(x) {
                expr = /^[a-zA-Z0-9 (.&@)_-]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 50){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^a-zA-Z0-9 (.&@)_-]/g, '');
                }
                document.getElementById(x).value = a;
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
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