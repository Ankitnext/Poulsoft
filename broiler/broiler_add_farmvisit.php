<?php
//broiler_add_farmvisit.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['farmvisit'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $href = $uri[1];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
$link_active_flag = 1;
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['addaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; 
    }
    if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
    else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; }
    else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
    else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
    else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $date = date("d.m.Y");
        $sector_code = $sector_name = $emp_code = $emp_name = $db_emp_code = array();
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

		$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

        $sql = "SELECT * FROM `main_access` ORDER BY `empcode`,`active` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $db_emp_code[$row['empcode']] = $row['db_emp_code']; }

        
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
            padding-left: 1px;
            padding-right: 1px;
            margin-right: 10px;
            height: 25px;
        }
    </style>
    </head>
    <body class="m-0 p-0 hold-transition">
   
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content"> 
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Farm Visit</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_farmvisit.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="p-1 row">
                                        <div class="form-group" style="width:90px;">
                                            <label for="date">Date</label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" style="width:80px;" value="<?php echo $date; ?>" readonly />
                                        </div>
                                        <div class="form-group" style="width:160px;">
                                            <label for="added_empcode">Emp. Name</label>
                                            <select name="added_empcode" id="added_empcode" class="form-control select2" style="width:150px;">
                                                <?php foreach($emp_code as $ecode){ ?><option value="<?php echo $ecode; ?>" ><?php echo $emp_name[$ecode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:120px;">
                                            <label for="vch_number">Vehicle No.</label>
                                            <input type="text" name="vch_number" id="vch_number" class="form-control" style="width:110px;" value="" />
                                        </div>
                                    </div>
                                    <div class="p-1 row row_body2" style="margin-bottom:3px;">
                                        <table class="p-1">
                                            <thead>
                                                <tr style="text-align:center;">
                                                    <th><label>Trip Type<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th><label>Location</label></th>
                                                    <th><label>Meter Reading<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th><label>Total Km</label></th>
                                                    <th><label>Remarks</label></th>
                                                </tr>
                                            </thead>
                                            <tbody id="row_body">
                                               <tr id="row_no1">
                                                    <td><select name="trip_type" id="trip_type" class="form-control select2" style="width:130px;"><option value="Start" selected>Start</option><option value="Continue">Continue</option><option value="End">End</option></select></td>
                                                    <td><select name="farm_code" id="farm_code" class="form-control select2" style="width:200px;"><option value="select" >select</option><option value="Home" >Home</option><option value="Office" >Office</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" ><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="meter_reading" id="meter_reading" class="form-control text-right" value="" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_kms2();" /></td>
                                                    <td><input type="text" name="total_km" id="total_km" class="form-control text-right" value="" style="width:120px;" readonly /></td>
                                                    <td><textarea name="remarks" id="remarks" class="form-control" style="padding:0;padding-left:2px;width:100px;height:25px;"><?php echo $remarks; ?></textarea></td>
                                                </tr>
                                               <tr id="row_no2">
                                                    <td><select name="trip_type2" id="trip_type2" class="form-control select2" style="width:130px;"><option value="Start" >Start</option><option value="Continue">Continue</option><option value="End" selected>End</option></select></td>
                                                    <td><select name="farm_code2" id="farm_code2" class="form-control select2" style="width:200px;"><option value="select" >select</option><option value="Home" >Home</option><option value="Office" >Office</option><?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" ><?php echo $sector_name[$scode]; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="meter_reading2" id="meter_reading2" class="form-control text-right" value="" style="width:120px;" onkeyup="validatenum(this.id);calculate_total_kms2();" /></td>
                                                    <td><input type="text" name="total_km2" id="total_km2" class="form-control text-right" value="" style="width:120px;" readonly /></td>
                                                    <td><textarea name="remarks2" id="remarks2" class="form-control" style="padding:0;padding-left:2px;width:100px;height:25px;"><?php echo $remarks; ?></textarea></td>
                                                </tr>
                                            </tbody>
                                        </table><br/>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $i; ?>">
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
                window.location.href = 'broiler_display_farmvisit.php?ccid='+ccid;
            }
            function checkval(){
                document.getElementById("submit").style.visibility = "hidden";
                item = document.getElementById("code").value;
                var l = true;
                if(item.match("select")){
                    alert("Kindly select Item in row: "+c);
                    document.getElementById("code").focus();
                    l = false;
                }
                else{
                    l = true;
                }
                if(l == true){
                    document.getElementById("submit").style.visibility = "hidden";
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
                    return false;
                }
            }
       
            function calculate_total_kms(){
                var incr = document.getElementById("incr").value;
                var d = c = mread = fread = tread = 0;
                for(d = 0;d <= incr;d++){
                    if(d > 0){
                        mread = document.getElementById("meter_reading["+d+"]").value; if(mread == ""){ mread = 0; }
                        c = d - 1;
                        fread = document.getElementById("meter_reading["+c+"]").value; if(fread == ""){ fread = 0; }
                        tread = parseFloat(mread) - parseFloat(fread);
                        document.getElementById("total_km["+d+"]").value = parseFloat(tread).toFixed(0);
                    }
                    else{
                        document.getElementById("total_km["+d+"]").value = 0;
                    }
                }
            }
            function calculate_total_kms2(){
                var mr1 = document.getElementById("meter_reading").value; if(mr1 == ""){ mr1 = 0; }
                var mr2 = document.getElementById("meter_reading2").value; if(mr2 == ""){ mr2 = 0; }
                var tread = parseFloat(mr2) - parseFloat(mr1);

                document.getElementById("total_km2").value = parseFloat(tread).toFixed(0);
            }
            setInterval(function(){
                // window.screen.availHeight window.screen.availWidth
                if(window.screen.availWidth <= 400){
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; }
                }
                else{
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; }
                }
            }, 1000);
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validatemobile(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 10){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more infarmvisition"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more infarmvisition";
}
?>