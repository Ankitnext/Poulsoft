<?php
//broiler_add_farm.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['farm'];
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
        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Farm Master' AND `field_function` LIKE 'Farm Code Auto Generate' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $fcode_autoflag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'broiler_display_farm.php' AND `field_function` LIKE 'Own And Lease' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $fown_lease = mysqli_num_rows($query);
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                            <div class="float-left"><h3 class="card-title">Add Farm</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_farm.php" method="post" role="form" onsubmit="return checkval()" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Region<b style="color:red;">&nbsp;*</b></label>
                                                <select name="region_code" id="region_code" class="form-control select2" style="width: 100%;" onchange="fetch_branch_details(this.id)">
                                                    <option value="select">select</option>
                                                    <?php
                                                    $sql = "SELECT * FROM `location_region` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
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
                                                <label>Branch<b style="color:red;">&nbsp;*</b></label>
							                    <select name="branch_code" id="branch_code" class="form-control select2" style="width: 100%;" onchange="fetch_line_details(this.id)"><option value="select">select</option></select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Line<b style="color:red;">&nbsp;*</b></label>
							                    <select name="line_code" id="line_code" class="form-control select2" style="width: 100%;"><option value="select">select</option></select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Supervisor<b style="color:red;">&nbsp;*</b></label>
                                                <select name="supervisor_code" id="supervisor_code" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <?php
                                                    $sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%supervisor%' AND `active` = '1' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $desig_codes = "";
                                                    while($row = mysqli_fetch_assoc($query)){ if($desig_codes == ""){ $desig_codes = $row['code']; } else{ $desig_codes = $desig_codes."','".$row['code']; } }
                                                    
                                                    $sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_codes') AND `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                            <label>Farm Code<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="farm_code" id="farm_code" class="form-control" onkeyup="validatename(this.id)" onchange="check_farm_code()">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Farm Name<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="description" id="description" class="form-control" onkeyup="validatename(this.id)" onchange="check_duplicate();">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                            <label>Farm Pincode<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="farm_pincode" id="farm_pincode" class="form-control" onkeyup="validatenum(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                            <label>Farm Capacity<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="farm_capacity" id="farm_capacity" class="form-control" onkeyup="validatenum(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Farmer<b style="color:red;">&nbsp;*</b></label>
                                                <select name="farmer_code" id="farmer_code" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <?php
                                                    $sql = "SELECT * FROM `broiler_farmer` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['name']."(".$row['code'].")"; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-2"><br/>
                                            <label>Farm Type<b style="color:red;">&nbsp;*</b></label>
                                        </div>
                                        <div class="col-md-1" align="center">
                                            <label>Own<b style="color:red;">&nbsp;*</b></label>
                                            <input type="radio" name="farm_type" id="farm_type1" class="form-control" value="own" style="transform: scale(.7);">
                                        </div>
                                        <div class="col-md-1" align="center">
                                            <label>Ec Shed<b style="color:red;">&nbsp;*</b></label>
                                            <input type="radio" name="farm_type" id="farm_type2" class="form-control" value="ecs" style="transform: scale(.7);" checked>
                                        </div>
                                        <div class="col-md-1" align="center">
                                            <label>Integration<b style="color:red;">&nbsp;*</b></label>
                                            <input type="radio" name="farm_type" id="farm_type3" class="form-control" value="int" style="transform: scale(.7);" >
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <?php if($fown_lease > 0) { ?>
                                     <div class="row" id="additionalOptions" style="display: none;">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-1" align="center">
                                            <label>Own<b style="color:red;">&nbsp;*</b></label>
                                            <input type="radio" name="farm_types" id="farm_type4" class="form-control" value="own2" style="transform: scale(.7);">
                                        </div>
                                        <div class="col-md-1" align="center">
                                            <label>Lease<b style="color:red;">&nbsp;*</b></label>
                                            <input type="radio" name="farm_types" id="farm_type5" class="form-control" value="lease" style="transform: scale(.7);">
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <?php } ?>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>State</label>
                                                <select name="state_code" id="state_code" class="form-control select2" style="width: 100%;">
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
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>District</label>
							                <input type="text" name="district_name" id="district_name" class="form-control" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Area</label>
							                <input type="text" name="area_name" id="area_name" class="form-control" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Farm Address</label>
							                <textarea name="farm_address" id="farm_address" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Agreement Months</label>
                                                <input type="text" name="agreement_months" id="agreement_months" class="form-control" onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Agreement Copy</label>
                                                <input type="file" name="agreement_copy" id="agreement_copy" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Other Documents</label>
                                                <input type="file" name="other_doc" id="other_doc" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Security Cheque 1</label>
                                                <input type="text" name="security_cheque1" id="security_cheque1" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Security Cheque 2</label>
                                                <input type="text" name="security_cheque2" id="security_cheque2" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Security Cheque 3</label>
                                                <input type="text" name="security_cheque3" id="security_cheque3" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Security Cheque 4</label>
                                                <input type="text" name="security_cheque4" id="security_cheque4" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="col-md-5"></div>
                                        <div class="col-md-2">
                                            <label>Error Flag</label>
							                <input type="text" name="err_flag" id="err_flag" class="form-control" value="0" />
                                        </div>
                                        <div class="col-md-5"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5"></div>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Shed No</th>
                                                    <th>Dimentions</th>
                                                    <th>Sq Feet</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="row_body">
                                                <tr>
                                                    <td><input type="text" name="shed_no[]" id="shed_no[0]" class="form-control"  style="width:110px;margin-right: 10px;" /></td>
                                                    <td><input type="text" name="shed_dimentions[]" id="shed_dimentions[0]" class="form-control" style="width:110px;margin-right: 10px;" /></td>
                                                    <td><input type="text" name="shed_sqft[]" id="shed_sqft[0]" class="form-control" style="width:110px;" /></td>
                                                    <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <label>Remarks</label>
							                <textarea name="remarks" id="remarks" class="form-control"></textarea>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                        <div class="col-md-1" style="visibility:hidden;">
                                            <label>D-Flag<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="dupflag" id="dupflag" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12"><br/><br/>
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
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_farm.php?ccid='+ccid;
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<tr id="row_no['+d+']">';
                html += '<td><input type="text" name="shed_no[]" id="shed_no['+d+']" class="form-control"  style="width:110px;margin-right: 10px;" /></td>';
                html += '<td><input type="text" name="shed_dimentions[]" id="shed_dimentions['+d+']" class="form-control" style="width:110px;margin-right: 10px;" /></td>';
                html += '<td><input type="text" name="shed_sqft[]" id="shed_sqft['+d+']" class="form-control" style="width:110px;"  /></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '</tr>';
                $('#row_body').append(html);
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_final_totalamt();
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var a = document.getElementById("region_code").value;
                var b = document.getElementById("branch_code").value;
                var c = document.getElementById("line_code").value;
                var d = document.getElementById("supervisor_code").value;
                var e = document.getElementById("farm_code").value;
                var f = document.getElementById("description").value;
                var g = document.getElementById("farm_capacity").value;
                var h = document.getElementById("farmer_code").value;
                var err_flag = document.getElementById("err_flag").value;
                var dupflag = document.getElementById("dupflag").value;
                var l = true;
                if(a.match("select")){
                    alert("Please select Region ..!");
                    document.getElementById("region_code").focus();
                    l = false;
                }
                else if(b.match("select")){
                    alert("Please Select Branch ..!");
                    document.getElementById("branch_code").focus();
                    l = false;
                }
                else if(c.match("select")){
                    alert("Please select Line ..!");
                    document.getElementById("line_code").focus();
                    l = false;
                }
                else if(d.match("select")){
                    alert("Please select Supervisor ..!");
                    document.getElementById("supervisor_code").focus();
                    l = false;
                }
                else if(e.length == 0){
                    alert("Please enter Farm Code ..!");
                    document.getElementById("farm_code").focus();
                    l = false;
                }
                else if(err_flag == 1){
                    alert("Farm Code already exist kindly add new farm code ..!");
                    document.getElementById("err_flag").focus();
                    l = false;
                }
                else if(f.length == 0){
                    alert("Please enter Farm Description ..!");
                    document.getElementById("description").focus();
                    l = false;
                }
                else if(g.length == 0){
                    alert("Please enter Farm Capacity ..!");
                    document.getElementById("farm_capacity").focus();
                    l = false;
                }
                else if(h.match("select")){
                    alert("Please select Farmer ..!");
                    document.getElementById("farmer_code").focus();
                    l = false;
                }
                else{ }
                if(dupflag == 1 || dupflag == "1"){
                    alert("Farm Name already exist \n Please check and try again");
                    document.getElementById("description").focus();
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
            function fetch_branch_details(a){
                var reg_code = document.getElementById(a).value;
                if(!reg_code.match("select")){
                    removeAllOptions(document.getElementById("branch_code"));
                    myselect1 = document.getElementById("branch_code");
                    theOption1=document.createElement("OPTION");
                    theText1=document.createTextNode("select");
                    theOption1.value = "select"; 
                    theOption1.appendChild(theText1); 
                    myselect1.appendChild(theOption1);
                    <?php
                        /*Check User access Locations*/
                        $sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
                        if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
                        if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
                        if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

                        $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ".$branch_access_filter1." ORDER BY `description` ASC";
                        //$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; 
                        $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $r_code = $row['region_code'];
                            echo "if(reg_code == '$r_code'){";
                    ?>
                        theOption1=document.createElement("OPTION");
						theText1=document.createTextNode("<?php echo $row['description']; ?>");
						theOption1.value = "<?php echo $row['code']; ?>";
						theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                    <?php
                        echo "}";
                        }
                    ?>
                }
            }
            function fetch_line_details(a){
                var brh_code = document.getElementById(a).value;
                if(!brh_code.match("select")){
                    removeAllOptions(document.getElementById("line_code"));
                    myselect1 = document.getElementById("line_code");
                    theOption1=document.createElement("OPTION");
                    theText1=document.createTextNode("select");
                    theOption1.value = "select"; 
                    theOption1.appendChild(theText1); 
                    myselect1.appendChild(theOption1);
                    <?php
                        $sql = "SELECT * FROM `location_line` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $b_code = $row['branch_code'];
                            $b_prefix = $branch_prefix[$row['branch_code']];
                            echo "if(brh_code == '$b_code'){";
                    ?>
                        theOption1=document.createElement("OPTION");
						theText1=document.createTextNode("<?php echo $row['description']; ?>");
						theOption1.value = "<?php echo $row['code']; ?>";
						theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                    <?php
                        echo "}";
                        }
                    ?>
                    var fcode_autoflag = '<?php echo $fcode_autoflag; ?>';
                    if(fcode_autoflag == 1){
                        var farm_pfx = new XMLHttpRequest();
                        var method = "GET";
                        var url = "broiler_fetch_farmprefixcode_auto.php?branch_code="+brh_code;
                        //window.open(url);
                        var asynchronous = true;
                        farm_pfx.open(method, url, asynchronous);
                        farm_pfx.send();
                        farm_pfx.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var g = this.responseText;
                                document.getElementById("farm_code").value = g;
                            }
                            else{ }
                        }
                    }
                }
            }
            function check_farm_code(){
                var farm_code = document.getElementById("farm_code").value;
                if(farm_code.length > 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_validate_farmcode.php?farm_code="+farm_code;
					//window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var g = this.responseText;
							if(g.match("true")){
                                document.getElementById("err_flag").value = 1;
                                document.getElementById("farm_code").value = "";
                                document.getElementById("farm_code").focus();
                                alert("Farm Code Already Exist, kindly add new farm code");
                            }
                            else{
                                document.getElementById("err_flag").value = 0;
                            }
						}
						else{
							
						}
					}
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
			function check_duplicate(){
				var b = document.getElementById("description").value;
				var c = "add";
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_farm_duplicates.php?cname="+b+"&type="+c;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_count = this.responseText;
							if(parseFloat(dup_count) > 0){
								alert("Farm Name available with the same name.\n Kindly change the name");
								document.getElementById("dupflag"). value = 1;
							}
							else {
								document.getElementById("dupflag"). value = 0;
							}
						}
					}
				}
				else { }
			}
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            document.addEventListener('keydown', function (event) {
            if (event.keyCode === 13 && event.target.nodeName === 'INPUT') {
                var form = event.target.form;
                var index = Array.prototype.indexOf.call(form, event.target);
                form.elements[index + 1].focus();
                event.preventDefault();
            }
            });

            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validatemobile(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 10){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			// function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script>
            $(document).ready(function(){
                var fown_lease =  "<?php echo $fown_lease ?>";
                if(fown_lease > 0) {
                    $("input[name='farm_type']").change(function(){
                        if($("#farm_type3").is(":checked")) {
                            $("#additionalOptions").fadeIn(); // Show Own2 & Lease
                        } else {
                            $("#additionalOptions").fadeOut(); // Hide Own2 & Lease
                        }
                    });
                }
            });
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