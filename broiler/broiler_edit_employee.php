<?php
//broiler_edit_employee.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['employee'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['editaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
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
        <?php
		$id = $_GET['id'];
		$sql = "SELECT * FROM `broiler_employee` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$title = $row['title'];
            $name = $row['name'];
            $emp_id = $row['emp_id'];
            $mobile = $row['mobile'];
            $emc_no = $row['emg_cmobile'];
            $email = $row['email'];
            $gender = $row['gender'];
            $desig_code = $row['desig_code'];
            $este_code = $row['este_code'];
            $birth_date = $row['birth_date'];
            $join_date = $row['join_date'];

            $gross_salary = $row['gross_salary'];
            $warehouse = $row['warehouse'];
            $pan_no = $row['pan_no'];
            $aadhar_no = $row['aadhar_no'];
            $uan_no = $row['uan_no'];
            $esi_no = $row['esi_no'];
            $bank_acc_no = $row['bank_acc_no'];
            $bank_ifsc_code = $row['bank_ifsc_code'];
            $bank_name = $row['bank_name'];
            $bank_branch_name = $row['bank_branch_name'];
            
            $street_name = $row['street_name'];
            $city_name = $row['city_name'];
            $state_code = $row['state_code'];
            $pincode = $row['pincode'];
            $country = $row['country'];
            $remarks = $row['remarks'];
            $vehicle = $row['vehicle'];

		}
		?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Employee</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_employee.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Title<b style="color:red;">&nbsp;*</b></label>
							                    <select name="title" id="title" class="form-control select2">
                                                    <option value="select">select</option>
                                                    <option value="Mr." <?php if($title == "Mr."){ echo "selected"; } ?>>Mr.</option>
                                                    <option value="Mrs." <?php if($title == "Mrs."){ echo "selected"; } ?>>Mrs.</option>
                                                    <option value="Miss." <?php if($title == "Miss."){ echo "selected"; } ?>>Miss</option>
                                                    <option value="Master." <?php if($title == "Master."){ echo "selected"; } ?>>Master.</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>employee Name<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="name" id="name" class="form-control" value="<?php echo $name; ?>" placeholder="Enter description..." onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>employee ID<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="emp_id" id="emp_id" class="form-control" value="<?php echo $emp_id; ?>" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-3"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Mobile</label>
							                <input type="text" name="mobile" id="mobile" class="form-control" value="<?php echo $mobile; ?>" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="text" name="email" id="email" class="form-control" value="<?php echo $email; ?>" onkeyup="validateemail(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <select name="gender" id="gender" class="form-control select2">
                                                    <option value="select">select</option>
                                                    <option value="Male" <?php if($gender == "Male"){ echo "selected"; } ?>>Male</option>
                                                    <option value="Female" <?php if($gender == "Female"){ echo "selected"; } ?>>Female</option>
                                                    <option value="Other" <?php if($gender == "Other"){ echo "selected"; } ?>>Other</option>
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
                                                    <option value="<?php echo $row['code']; ?>" <?php if($desig_code == $row['code']){ echo "selected"; } ?>><?php echo $row['description']; ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Birth Date</label>
							                <input type="text" name="birth_date" id="birth_date" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($birth_date)); ?>" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Join Date</label>
							                <input type="text" name="join_date" id="join_date" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($join_date)); ?>" onkeyup="validatename(this.id)">
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
                                                    <input type="text" name="gross_salary" id="gross_salary" class="form-control" value="<?php echo $gross_salary; ?>" />
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Sector<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="warehouse" id="warehouse" class="form-control select2" style="width:160px;">
                                                        <option value="select">select</option>
                                                        <?php foreach($sector_code as $wcode){ ?><option value="<?php echo $wcode; ?>" <?php if($warehouse == $wcode){ echo "selected"; } ?>><?php echo $sector_name[$wcode]; ?></option><?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label>Emp. CoA<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="este_code" id="este_code" class="form-control select2" style="width:160px;">
                                                        <option value="select">select</option>
                                                        <?php foreach($ecoa_code as $wcode){ ?><option value="<?php echo $wcode; ?>" <?php if($este_code == $wcode){ echo "selected"; } ?>><?php echo $ecoa_name[$wcode]; ?></option><?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>PAN No.</label>
                                                    <input type="text" name="pan_no" id="pan_no" class="form-control" value="<?php echo $pan_no; ?>" />
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Aadhar No.</label>
                                                    <input type="text" name="aadhar_no" id="aadhar_no" class="form-control" value="<?php echo $aadhar_no; ?>" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>UAN No.</label>
                                                    <input type="text" name="uan_no" id="uan_no" class="form-control" value="<?php echo $uan_no; ?>" />
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>ESI No.</label>
                                                    <input type="text" name="esi_no" id="esi_no" class="form-control" value="<?php echo $esi_no; ?>" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>Account No.</label>
                                                    <input type="text" name="bank_acc_no" id="bank_acc_no" class="form-control" value="<?php echo $bank_acc_no; ?>" />
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>IFSC Code.</label>
                                                    <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control" value="<?php echo $bank_ifsc_code; ?>" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label>Bank Name</label>
                                                    <input type="text" name="bank_name" id="bank_name" class="form-control" value="<?php echo $bank_name; ?>" />
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label>Branch</label>
                                                    <input type="text" name="bank_branch_name" id="bank_branch_name" class="form-control" value="<?php echo $bank_branch_name; ?>" />
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
							                <input type="text" name="street_name" id="street_name" value="<?php echo $street_name; ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Emergency Number</label>
							                <input type="number" name="emc_no" id="emc_no" value="<?php echo $emc_no; ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>City</label>
							                <input type="text" name="city_name" id="city_name" class="form-control" value="<?php echo $city_name; ?>" onkeyup="validatename(this.id)">
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
                                                <option value="<?php echo $row['code']; ?>" <?php if($state_code == $row['code']){ echo "selected"; } ?>><?php echo $row['name']; ?></option>
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
							                <input type="text" name="pincode" id="pincode" class="form-control" value="<?php echo $pincode; ?>" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Country</label>
							                <input type="text" name="country" id="country" class="form-control" value="<?php echo $country; ?>" onkeyup="validatename(this.id)">
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
							                <textarea name="remarks" id="remarks" class="form-control"><?php echo $remarks; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="form-group col-md-1" style="visibility:hidden;">
                                                <label>id<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" style="width:auto;" class="form-control" name="idvalue" id="idvalue"  value="<?php echo $id; ?>">
                                            </div>
                                            <div class="form-group col-md-1" style="visibility:hidden;">
                                                <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Update</button>&ensp;
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