<?php
//broiler_edit_placementplanning.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['placementplanning'];
date_default_timezone_set("Asia/Kolkata");
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
        $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0'  ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ".$branch_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `location_line` WHERE `dflag` = '0' ".$line_access_filter1."".$branch_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $line_code[$row['code']] = $row['code']; $line_name[$row['code']] = $row['description']; }
        
        $sql = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
        

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
            $id = $_GET['trnum'];
            $sql = "SELECt * FROM `broiler_placementplan` WHERE `trnum` = '$id'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $week_no = $row['week_no'];
                $from_date = $row['from_date'];
                $to_date = $row['to_date'];
            }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Placement Planning Details</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_placementplanning.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Week No:<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="week_no" id="week_no" class="form-control" value="<?php echo $week_no; ?>" style="width:100px;">
                                        </div>&ensp;
                                        <div class="form-group">
                                            <label>From Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="from_date" id="from_date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($from_date)); ?>" style="width:100px;">
                                        </div>&ensp;
                                        <div class="form-group">
                                            <label>To Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="to_date" id="to_date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($to_date)); ?>" style="width:100px;">
                                        </div>&ensp;
                                    </div><br/>
                                    <div class="m-0 p-0 col-md-12">
                                        <table class="m-0 p-0 table-bordered">
                                            <thead class="bg-success">
                                                <tr style="text-align:center;">
                                                    <th rowspan="3">Farm</th>
                                                    <th rowspan="3">Branch</th>
                                                    <th rowspan="3">Village</th>
                                                    <th rowspan="3">Sq. Feet</th>
                                                    <th rowspan="3">Line Name</th>
                                                    <th rowspan="3">Supervisor Name</th>
                                                    <th colspan="18">Previous Performance</th>
                                                    <th rowspan="3">Remarks</th>
                                                    <th rowspan="3"></th>
                                                </tr>
                                                <tr style="text-align:center;">
                                                    <th colspan="6">Last Batch</th>
                                                    <th colspan="6">Before Last Batch</th>
                                                    <th colspan="6">Before Last Batch</th>
                                                </tr>
                                                <tr style="text-align:center;">
                                                    <th>FCR</th>
                                                    <th>CFCR</th>
                                                    <th>Mort%</th>
                                                    <th>Avg BodyWt</th>
                                                    <th>Mean Age</th>
                                                    <th>GC Date</th>
                                                    <th>FCR</th>
                                                    <th>CFCR</th>
                                                    <th>Mort%</th>
                                                    <th>Avg BodyWt</th>
                                                    <th>Mean Age</th>
                                                    <th>GC Date</th>
                                                    <th>FCR</th>
                                                    <th>CFCR</th>
                                                    <th>Mort%</th>
                                                    <th>Avg BodyWt</th>
                                                    <th>Mean Age</th>
                                                    <th>GC Date</th>
                                                </tr>
                                            </thead>
                                            <tbody id="row_body">
                                                <?php
                                                $sql = "SELECt * FROM `broiler_placementplan` WHERE `trnum` = '$id'";
                                                $query = mysqli_query($conn,$sql); $rcount = mysqli_num_rows($query); $c = 0;
                                                while($row = mysqli_fetch_assoc($query)){
                                                    $edit_farm_code = $row['farm_code'];
                                                    $edit_branch_code = $branch_name[$row['branch_code']];
                                                    $edit_village_code = $row['village_code'];
                                                    $edit_sq_feet = $row['sq_feet'];
                                                    $edit_line_code = $line_name[$row['line_code']];
                                                    $edit_supervisor_code = $emp_name[$row['supervisor_code']];

                                                    $edit_lb_batch_code = $row['lb_batch_code'];
                                                    $edit_lb_fcr = $row['lb_fcr'];
                                                    $edit_lb_cfcr = $row['lb_cfcr'];
                                                    $edit_lb_mort = $row['lb_mort'];
                                                    $edit_lb_avg_bodywt = $row['lb_avg_bodywt'];
                                                    $edit_lb_mean_age = $row['lb_mean_age'];
                                                    $edit_lb_gc_date = $row['lb_gc_date']; if($edit_lb_gc_date != ""){ $edit_lb_gc_date = date("d.m.Y",strtotime($edit_lb_gc_date)); }

                                                    $edit_blb_batch_code = $row['blb_batch_code'];
                                                    $edit_blb_fcr = $row['blb_fcr'];
                                                    $edit_blb_cfcr = $row['blb_cfcr'];
                                                    $edit_blb_mort = $row['blb_mort'];
                                                    $edit_blb_avg_bodywt = $row['blb_avg_bodywt'];
                                                    $edit_blb_mean_age = $row['blb_mean_age'];
                                                    $edit_blb_gc_date = $row['blb_gc_date']; if($edit_blb_gc_date != ""){ $edit_blb_gc_date = date("d.m.Y",strtotime($edit_blb_gc_date)); }

                                                    $edit_olb_batch_code = $row['olb_batch_code'];
                                                    $edit_olb_fcr = $row['olb_fcr'];
                                                    $edit_olb_cfcr = $row['olb_cfcr'];
                                                    $edit_olb_mort = $row['olb_mort'];
                                                    $edit_olb_avg_bodywt = $row['olb_avg_bodywt'];
                                                    $edit_olb_mean_age = $row['olb_mean_age'];
                                                    $edit_olb_gc_date = $row['olb_gc_date']; if($edit_olb_gc_date != ""){ $edit_olb_gc_date = date("d.m.Y",strtotime($edit_olb_gc_date)); }

                                                    $edit_remarks = $row['remarks'];
                                                ?>
                                                <tr id="row_no[<?php echo $c; ?>]">
                                                    <td><select name="farm_code[]" id="farm_code[<?php echo $c; ?>]" class="form-control select2" style="width:180px;" onchange="fetch_previous_batchdetails(this.id);"><option value="select">select</option><?php foreach($farm_code as $fcode){ ?><option value="<?php echo $fcode; ?>" <?php if($edit_farm_code == $fcode){ echo "selected"; } ?>><?php echo $farm_name[$fcode]; ?></option><?php } ?></select></td>
                                                    <td><input type="text" name="branches[]" id="branches[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_branch_code; ?>" readonly ></td>
                                                    <td><input type="text" name="villages[]" id="villages[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_village_code; ?>" readonly ></td>
                                                    <td><input type="text" name="sqfeet[]" id="sqfeet[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_sq_feet; ?>" readonly ></td>
                                                    <td><input type="text" name="lines[]" id="lines[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_line_code; ?>" readonly ></td>
                                                    <td><input type="text" name="supervisors[]" id="supervisors[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_supervisor_code; ?>" readonly ></td>
                                                    <td><input type="text" name="lb_fcr[]" id="lb_fcr[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_lb_fcr; ?>" readonly ></td>
                                                    <td><input type="text" name="lb_cfcr[]" id="lb_cfcr[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_lb_cfcr; ?>" readonly ></td>
                                                    <td><input type="text" name="lb_mort[]" id="lb_mort[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_lb_mort; ?>" readonly ></td>
                                                    <td><input type="text" name="lb_avg_bodywt[]" id="lb_avg_bodywt[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_lb_avg_bodywt; ?>" readonly ></td>
                                                    <td><input type="text" name="lb_mean_age[]" id="lb_mean_age[<?php echo $c; ?>]" class="form-control" style="width:80px;" value="<?php echo $edit_lb_mean_age; ?>" readonly ></td>
                                                    <td><input type="text" name="lb_gc_date[]" id="lb_gc_date[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_lb_gc_date; ?>" readonly ></td>
                                                    <td><input type="text" name="blb_fcr[]" id="blb_fcr[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_blb_fcr; ?>" readonly ></td>
                                                    <td><input type="text" name="blb_cfcr[]" id="blb_cfcr[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_blb_cfcr; ?>" readonly ></td>
                                                    <td><input type="text" name="blb_mort[]" id="blb_mort[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_blb_mort; ?>" readonly ></td>
                                                    <td><input type="text" name="blb_avg_bodywt[]" id="blb_avg_bodywt[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_blb_avg_bodywt; ?>" readonly ></td>
                                                    <td><input type="text" name="blb_mean_age[]" id="blb_mean_age[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_blb_mean_age; ?>" readonly ></td>
                                                    <td><input type="text" name="blb_gc_date[]" id="blb_gc_date[<?php echo $c; ?>]" class="form-control" style="width:80px;" value="<?php echo $edit_blb_gc_date; ?>" readonly ></td>

                                                    <td><input type="text" name="olb_fcr[]" id="olb_fcr[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_olb_fcr; ?>" readonly ></td>
                                                    <td><input type="text" name="olb_cfcr[]" id="olb_cfcr[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_olb_cfcr; ?>" readonly ></td>
                                                    <td><input type="text" name="olb_mort[]" id="olb_mort[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_olb_mort; ?>" readonly ></td>
                                                    <td><input type="text" name="olb_avg_bodywt[]" id="olb_avg_bodywt[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_olb_avg_bodywt; ?>" readonly ></td>
                                                    <td><input type="text" name="olb_mean_age[]" id="olb_mean_age[<?php echo $c; ?>]" class="form-control" style="width:70px;" value="<?php echo $edit_olb_mean_age; ?>" readonly ></td>
                                                    <td><input type="text" name="olb_gc_date[]" id="olb_gc_date[<?php echo $c; ?>]" class="form-control" style="width:80px;" value="<?php echo $edit_olb_gc_date; ?>" readonly ></td>

                                                    <td><textarea name="remarks[]" id="remarks[<?php echo $c; ?>]" class="form-control" style="width:120px;height:23px;"><?php echo $edit_remarks; ?></textarea></td>
                                                    <td id="action[<?php echo $c; ?>]" <?php if($rcount == $c + 1){ echo 'style="padding-top: 5px;"'; } else{ echo 'style="padding-top: 5px;visibility:hidden;"'; } ?> >
                                                        <?php if($rcount == 0){ echo '<br/>'; } ?>
                                                        <a href="javascript:void(0);" id="addrow[<?php echo $c; ?>]" onclick="create_row(this.id)" >
                                                            <i class="fa fa-plus"></i>
                                                        </a>
                                                            &ensp;
                                                        <a href="javascript:void(0);" id="deductrow[<?php echo $c; ?>]" onclick="destroy_row(this.id)" <?php if($c > 0 && $rcount  > 0){ } else{ echo 'style="display:none;"'; } ?> >
                                                            <i class="fa fa-minus" style="color:red;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
                                                    $c++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div><br/>
                                    <div class="row">
                                        <div class="form-group col-md-12" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $c - 1; ?>" >
                                        </div>
                                        <div class="form-group col-md-12" style="visibility:hidden;">
                                            <label>Id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>" >
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
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
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_placementplanning.php?ccid'+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var l = true;
                var week_no = document.getElementById("week_no").value;
                var from_date = document.getElementById("from_date").value;
                var to_date = document.getElementById("to_date").value;
                if(l == true){
                    if(week_no.length == 0 || week_no == ""){
                        alert("Enter Week No ...!");
                        document.getElementById("week_no").focus();
                        l = false;
                    }
                    else if(from_date.length == 0 || from_date == ""){
                        alert("Select/Enter From Date ...!");
                        document.getElementById("vcode").focus();
                        l = false;
                    }
                    else if(from_date.length == 0 || from_date == ""){
                        alert("Select/Enter To Date ...!");
                        document.getElementById("link_trnum").focus();
                        l = false;
                    }
                    else{ }
                }
                if(l == true){
                    var incr =document.getElementById("incr").value;
                    var farm_code = "";
                    for(var d = 0;d <=incr;d++){
                        if(l == true){
                            farm_code =document.getElementById("farm_code["+d+"]").value;
                            if(farm_code.match("select")){
                                alert("Please select Farm in row: "+ d-1);
                                l = false;
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
            function select_all_checkboxes(){
                var selectallbox = document.getElementById("check_all");
                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                if(selectallbox.checked == true){
                    for (var i = 0; i < checkboxes.length; i++){
                        checkboxes[i].checked = true;
                        document.getElementById("rate["+i+"]").readOnly = false;
                        document.getElementById("crtn_qty["+i+"]").readOnly = false;
                    }
                }
                else{
                    for (var i = 0; i < checkboxes.length; i++){
                        checkboxes[i].checked = false;
                        document.getElementById("rate["+i+"]").readOnly = true;
                        document.getElementById("crtn_qty["+i+"]").readOnly = true;
                    }
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="farm_code[]" id="farm_code['+d+']" class="form-control select2" style="width:180px;" onchange="fetch_previous_batchdetails(this.id);"><option value="select">select</option><?php foreach($farm_code as $fcode){ ?><option value="<?php echo $fcode; ?>"><?php echo $farm_name[$fcode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="branches[]" id="branches['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="villages[]" id="villages['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="sqfeet[]" id="sqfeet['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="lines[]" id="lines['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="supervisors[]" id="supervisors['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="lb_fcr[]" id="lb_fcr['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="lb_cfcr[]" id="lb_cfcr['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="lb_mort[]" id="lb_mort['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="lb_avg_bodywt[]" id="lb_avg_bodywt['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="lb_mean_age[]" id="lb_mean_age['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="lb_gc_date[]" id="lb_gc_date['+d+']" class="form-control" style="width:80px;" readonly ></td>';
                html += '<td><input type="text" name="blb_fcr[]" id="blb_fcr['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="blb_cfcr[]" id="blb_cfcr['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="blb_mort[]" id="blb_mort['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="blb_avg_bodywt[]" id="blb_avg_bodywt['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="blb_mean_age[]" id="blb_mean_age['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="blb_gc_date[]" id="blb_gc_date['+d+']" class="form-control" style="width:80px;" readonly ></td>';
                html += '<td><input type="text" name="olb_fcr[]" id="olb_fcr['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="olb_cfcr[]" id="olb_cfcr['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="olb_mort[]" id="olb_mort['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="olb_avg_bodywt[]" id="olb_avg_bodywt['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="olb_mean_age[]" id="olb_mean_age['+d+']" class="form-control" style="width:70px;" readonly ></td>';
                html += '<td><input type="text" name="olb_gc_date[]" id="olb_gc_date['+d+']" class="form-control" style="width:80px;" readonly ></td>';
                html += '<td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="width:120px;height:23px;"></textarea></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '</tr>';
                $('#row_body').append(html);
                $('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_final_total_amount();
            }
            function fetch_previous_batchdetails(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var farm_code = document.getElementById("farm_code["+d+"]").value;
                var inv_items = new XMLHttpRequest();
				var method = "GET";
				var url = "broiler_fetch_placementfarmdetails.php?farm_code="+farm_code+"&row_no="+d;
                //window.open(url);
				var asynchronous = true;
				inv_items.open(method, url, asynchronous);
				inv_items.send();
				inv_items.onreadystatechange = function(){
					if(this.readyState == 4 && this.status == 200){
						var item_list = this.responseText;
                        if(item_list.length > 0){
                            var list_val = item_list.split("@");
                            document.getElementById("branches["+list_val[0]+"]").value = list_val[1];
                            document.getElementById("villages["+list_val[0]+"]").value = list_val[2];
                            document.getElementById("sqfeet["+list_val[0]+"]").value = list_val[3];
                            document.getElementById("lines["+list_val[0]+"]").value = list_val[4];
                            document.getElementById("supervisors["+list_val[0]+"]").value = list_val[5];
                            document.getElementById("lb_fcr["+list_val[0]+"]").value = list_val[6];
                            document.getElementById("lb_cfcr["+list_val[0]+"]").value = list_val[14];
                            document.getElementById("lb_mort["+list_val[0]+"]").value = list_val[7];
                            document.getElementById("lb_avg_bodywt["+list_val[0]+"]").value = list_val[8];
                            document.getElementById("lb_mean_age["+list_val[0]+"]").value = list_val[9];
                            document.getElementById("lb_gc_date["+list_val[0]+"]").value = list_val[21];
                            document.getElementById("blb_fcr["+list_val[0]+"]").value = list_val[10];
                            document.getElementById("blb_fcr["+list_val[0]+"]").value = list_val[15];
                            document.getElementById("blb_mort["+list_val[0]+"]").value = list_val[11];
                            document.getElementById("blb_avg_bodywt["+list_val[0]+"]").value = list_val[12];
                            document.getElementById("blb_mean_age["+list_val[0]+"]").value = list_val[13];
                            document.getElementById("blb_gc_date["+list_val[0]+"]").value = list_val[22];
                            document.getElementById("olb_fcr["+list_val[0]+"]").value = list_val[16];
                            document.getElementById("olb_cfcr["+list_val[0]+"]").value = list_val[17];
                            document.getElementById("olb_mort["+list_val[0]+"]").value = list_val[18];
                            document.getElementById("olb_avg_bodywt["+list_val[0]+"]").value = list_val[19];
                            document.getElementById("olb_mean_age["+list_val[0]+"]").value = list_val[20];
                            document.getElementById("olb_gc_date["+list_val[0]+"]").value = list_val[23];
                        }
                        else{
                            alert("There are no Details available \n Kindly check and try again ...!");
                        }
                    }
                }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
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