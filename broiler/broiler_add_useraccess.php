<?php
//broiler_add_useraccess.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['useraccess'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $access_list = str_replace(",","','",$row['displayaccess'].",".$row['addaccess'].",".$row['editaccess'].",".$row['deleteaccess'].",".$row['printaccess'].",".$row['otheraccess']);
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
        $admin_title = $_GET['title']; if($admin_title == ""){ $admin_title = 0; }

        $sql = "SELECT * FROM `broiler_nisan_credentials` WHERE `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $nisan_cred_count = mysqli_num_rows($query);

        //Check Breeder Access
        $sql1 = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '%breeder_display_shedallocate1.php%' AND `active` = '1'";
        $query1 = mysqli_query($conn,$sql1); $brd_aflag = mysqli_num_rows($query1);
        
        if((int)$brd_aflag > 0){
            $sql = "SELECT * FROM `breeder_farms` WHERE `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $farm_code = $farm_name = array();
            while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

            $sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $unit_code = $unit_name = array();
            while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description']; }

            $sql = "SELECT * FROM `breeder_sheds` WHERE `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $shed_code = $shed_name = array();
            while($row = mysqli_fetch_assoc($query)){ $shed_code[$row['code']] = $row['code']; $shed_name[$row['code']] = $row['description']; }

            $sql = "SELECT * FROM `breeder_batch` WHERE `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $batch_code = $batch_name = array();
            while($row = mysqli_fetch_assoc($query)){ $batch_code[$row['code']] = $row['code']; $batch_name[$row['code']] = $row['description']; }

            $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $flock_code = $flock_name = array();
            while($row = mysqli_fetch_assoc($query)){ $flock_code[$row['code']] = $row['code']; $flock_name[$row['code']] = $row['description']; }
        }
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <style>
        body{
            height:100%;
            overflow: auto;
            zoom:0.9;
        }
        .form-control{
            /*font-size: 20px;*/
        }
        //table { white-space: nowrap; }
        
        .item ul, .nav .cbox {
            display: none;
        }
        .multi-level, .item input:checked ~ ul {
            display: block;
        }
        /*Styles*/
        label:hover {
            cursor: pointer;
        }
        label {
            width: 100%;
            display: block;
            z-index: 3;
            position: relative;
        }
        .nav {
            width: 100%;
            background-color: white;
            overflow-x: hidden;
            /*border-bottom: 1px solid #CFD8DC;*/
        }

        .nav ul, .nav li, label {
            line-height: 30px;
            margin: 0;
            padding: 0 16px;
            list-style: none;
            text-decoration: none;
            color: black;
            font-weight: 100;
            width: 100%;
        }
        .item ul {
            padding: 0 4px;
        }
        .name-menu{
            text-align:center;
            width: 290px;
        }
        .mymenu {
            text-align:center;
            width: 50px;
        }
        .nav li .name-menu {
            line-height: 30px;
            margin: 0;
            padding: 0 32px;
            width: 250px;
            list-style: none;
            text-decoration: none;
            text-align:left;
            color: black;
            font-weight: 100;
        }
        .nav li .rname-menu {
            line-height: 30px;
            margin: 0;
            padding: 0 32px;
            width: 270px;
            list-style: none;
            text-decoration: none;
            text-align:left;
            color: black;
            font-weight: 100;
        }
        .nav li .sname-menu {
            line-height: 30px;
            margin: 0;
            padding: 0 32px;
            width: 270px;
            list-style: none;
            text-decoration: none;
            text-align:left;
            color: black;
            font-weight: 100;
        }
        .nav li .sub-menu input{
            background-color: red;
            border-color: red;
        }
        .card-title{
            font-size: 25px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <!--<div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add users</h3></div>
                        </div>-->
                        <div class="m-0 p-0 card-body">
                            <form action="broiler_save_useraccess.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="m-0 p-0 row margin">
                                    <div class="m-0 p-0 col-md-5">
                                        <div class="card card-purple" style="height: 550px;">
                                            <div class="card-header">
                                                <h3 class="card-title">User Details</h3>
                                            </div>
                                            <div class="card-body table-responsive">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Employee<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="empname" id="empname" class="form-control select2">
                                                                <option value="select">-Select-</option>
                                                                <?php
                                                                    $sql = "SELECT * FROM `main_access` WHERE `active` = '1' ORDER BY `db_emp_code` ASC"; $query = mysqli_query($conn,$sql); $existemp_list = "";
                                                                    while($row = mysqli_fetch_assoc($query)){ if($existemp_list == ""){ $existemp_list = $row['db_emp_code']; } else{ $existemp_list = $existemp_list."','".$row['db_emp_code']; } }
                                                                    $sql = "SELECT * FROM `broiler_employee` WHERE `active` = '1' AND `code` NOT IN ('$existemp_list') ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
                                                                    while($row = mysqli_fetch_assoc($query)){
                                                                ?>
                                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Mobile<b style="color:red;">&nbsp;*</b></label>
                                                            <input type="text" name="umobile" id="umobile" class="form-control" placeholder="" onkeyup="validatenum(this.id)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Username<b style="color:red;">&nbsp;*</b></label>
                                                            <input type="text" name="uname" id="uname" class="form-control" placeholder="" value="" onkeyup="validatename(this.id)" onchange="check_duplicate_user(this.id);">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Password<b style="color:red;">&nbsp;*</b></label>
                                                            <input type="password" name="upass" id="upass" class="form-control" value="" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Branch<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="branch_code[]" id="branch_code[]" class="form-control select2" multiple >
                                                                <option value="all" selected >All</option>
                                                                <?php
                                                                    $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                                    while($row = mysqli_fetch_assoc($query)){
                                                                ?>
                                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Line<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="line_code[]" id="line_code[]" class="form-control select2" multiple >
                                                                <option value="all" selected >All</option>
                                                                <?php
                                                                    $sql = "SELECT * FROM `location_line` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                                    while($row = mysqli_fetch_assoc($query)){
                                                                ?>
                                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Farm<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="farm_code[]" id="farm_code[]" class="form-control select2" multiple >
                                                                <option value="all" selected >All</option>
                                                                <?php
                                                                    $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                                    while($row = mysqli_fetch_assoc($query)){
                                                                ?>
                                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Sector<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="warehouse[]" id="warehouse[]" class="form-control select2" multiple >
                                                                <option value="all" selected >All</option>
                                                                <?php
                                                                    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                                    while($row = mysqli_fetch_assoc($query)){
                                                                ?>
                                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Customer Group Access<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="cgroup[]" id="cgroup[]" multiple class="form-control select2">
                                                                <option value="all" selected >All</option>
                                                                <?php
                                                                    $sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                                    while($row = mysqli_fetch_assoc($query)){
                                                                ?>
                                                                    <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <table class="w-100 table-bordered">
                                                        <tr>
                                                            <th style="width:230px;"><label>Access Type<b style="color:red;">&nbsp;*</b></label></th>
                                                            <td><label for="uaccess1"><input type="radio" name="uaccess" id="uaccess1" value="A" onclick="adminaccess(this.id)" />&nbsp;Admin</label></td>
                                                            <td><label for="uaccess2"><input type="radio" name="uaccess" id="uaccess2" value="N" onclick="adminaccess(this.id)" checked />&nbsp;Sub-Admin</label></td>
                                                        </tr>
                                                        <tr>
                                                            <th><label>Login Type<b style="color:red;">&nbsp;*</b></label></th>
                                                            <td><label for="login_type1"><input type="radio" name="login_type" id="login_type1" value="normal" checked />&nbsp;Password</label></td>
                                                            <td><label for="login_type2"><input type="radio" name="login_type" id="login_type2" value="otp" />&nbsp;OTP</label></td>
                                                        </tr>
                                                        <tr>
                                                            <th><label>Sale(Multiple-Edit)<b style="color:red;">&nbsp;*</b></label></th>
                                                            <td><label for="sale_multiple_edit_flag1"><input type="radio" name="sale_multiple_edit_flag" id="sale_multiple_edit_flag1" value="1" />&nbsp;Yes</label></td>
                                                            <td><label for="sale_multiple_edit_flag2"><input type="radio" name="sale_multiple_edit_flag" id="sale_multiple_edit_flag2" value="0" checked />&nbsp;No</label></td>
                                                        </tr>
                                                        <tr>
                                                            <th><label>Sale(Multiple-Delete)<b style="color:red;">&nbsp;*</b></label></th>
                                                            <td><label for="sale_multiple_delete_flag1"><input type="radio" name="sale_multiple_delete_flag" id="sale_multiple_delete_flag1" value="1" />&nbsp;Yes</label></td>
                                                            <td><label for="sale_multiple_delete_flag2"><input type="radio" name="sale_multiple_delete_flag" id="sale_multiple_delete_flag2" value="0" checked />&nbsp;No</label></td>
                                                        </tr>
                                                        <tr>
                                                            <th><label>Dashboard<b style="color:red;">&nbsp;*</b></label></th>
                                                            <td><label for="display_dashboard_flag1"><input type="radio" name="display_dashboard_flag" id="display_dashboard_flag1" value="1" />&nbsp;Yes</label></td>
                                                            <td><label for="display_dashboard_flag2"><input type="radio" name="display_dashboard_flag" id="display_dashboard_flag2" value="0" checked />&nbsp;No</label></td>
                                                        </tr>
                                                        <?php if((int)$nisan_cred_count > 0){ ?>
                                                        <tr>
                                                            <th><label>Nisan Submit Sales<b style="color:red;">&nbsp;*</b></label></th>
                                                            <td><label for="nisan_submit_sales1"><input type="radio" name="nisan_submit_sales" id="nisan_submit_sales1" value="1" />&nbsp;Yes</label></td>
                                                            <td><label for="nisan_submit_sales2"><input type="radio" name="nisan_submit_sales" id="nisan_submit_sales2" value="0" checked />&nbsp;No</label></td>
                                                        </tr>
                                                        <?php } ?>
                                                    </table>
                                                </div>
                                                <?php if((int)$brd_aflag > 0){ ?><br/><br/>
                                                    <div class="row">
                                                        <table class="w-100 table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th colspan="2" class="text-center bg-danger">Breeder Access</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <label for="farms">Farm</label>
                                                                        <select name="farms[]" id="farms[]" class="form-control select2" style="width:270px;" multiple onchange="fetch_flock_details(this.id);">
                                                                            <option value="all">-All-</option>
                                                                            <?php foreach($farm_code as $bcode){ if($farm_name[$bcode] != ""){ ?>
                                                                            <option value="<?php echo $bcode; ?>"><?php echo $farm_name[$bcode]; ?></option>
                                                                            <?php } } ?>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <label for="units">Unit</label>
                                                                        <select name="units[]" id="units[]" class="form-control select2" style="width:270px;" multiple onchange="fetch_flock_details(this.id);">
                                                                            <option value="all">-All-</option>
                                                                            <?php foreach($unit_code as $bcode){ if($unit_name[$bcode] != ""){ ?>
                                                                            <option value="<?php echo $bcode; ?>"><?php echo $unit_name[$bcode]; ?></option>
                                                                            <?php } } ?>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <label for="sheds">Shed</label>
                                                                        <select name="sheds[]" id="sheds[]" class="form-control select2" style="width:270px;" multiple onchange="fetch_flock_details(this.id);">
                                                                            <option value="all">-All-</option>
                                                                            <?php foreach($shed_code as $bcode){ if($shed_name[$bcode] != ""){ ?>
                                                                            <option value="<?php echo $bcode; ?>"><?php echo $shed_name[$bcode]; ?></option>
                                                                            <?php } } ?>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <label for="batches">Batch</label>
                                                                        <select name="batches[]" id="batches[]" class="form-control select2" style="width:270px;" multiple onchange="fetch_flock_details(this.id);">
                                                                            <option value="all">-All-</option>
                                                                            <?php foreach($batch_code as $bcode){ if($batch_name[$bcode] != ""){ ?>
                                                                            <option value="<?php echo $bcode; ?>"><?php echo $batch_name[$bcode]; ?></option>
                                                                            <?php } } ?>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <label for="flocks">Flock</label>
                                                                        <select name="flocks[]" id="flocks[]" class="form-control select2" style="width:270px;" multiple>
                                                                            <option value="all">-All-</option>
                                                                            <?php foreach($flock_code as $bcode){ if($flock_name[$bcode] != ""){ ?>
                                                                            <option value="<?php echo $bcode; ?>"><?php echo $flock_name[$bcode]; ?></option>
                                                                            <?php } } ?>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="card card-purple" style="height: 550px;">
                                            <div class="card-header">
                                                <h3 class="card-title">Web-Access Details</h3>
                                            </div>
                                            <div class="p-0 card-body table-responsive">
                                                <div class="row sticky-top" style="background: #F9E8E4;padding-bottom:15px;">
                                                    <div class="name-menu">Link<br/><input type='checkbox' name='link' id='link' value='0' onclick='check_all_access(this.id)'/></div>
                                                    <div class="mymenu view-menu">View<br/><input type='checkbox' name='display' id='display' value='1' onclick='checkall(this.id)'/></div>
                                                    <div class="mymenu add-menu">Add<br/><input type='checkbox' name='add' id='add' value='2' onclick='checkall(this.id)'/></div>
                                                    <div class="mymenu edit-menu">Edit<br/><input type='checkbox' name='edit' id='edit' value='3' onclick='checkall(this.id)'/></div>
                                                    <div class="mymenu delete-menu">Delete<br/><input type='checkbox' name='delete' id='delete' value='4' onclick='checkall(this.id)'/></div>
                                                    <div class="mymenu print-menu">Print<br/><input type='checkbox' name='print' id='print' value='5' onclick='checkall(this.id)'/></div>
                                                    <div class="mymenu update-menu">Update<br/><input type='checkbox' name='other' id='other' value='6' onclick='checkall(this.id)'/></div>
                                                </div>
                                                <div class="nav">
                                                    <div class="multi-level">
                                                        
                                                <?php
                                                $s1 = $s2 = $s3 = $s4 = $s5 = 0;
                                                $r1 = $r2 = $r3 = $r4 = $r5 = 0;
                                                $t1 = $t2 = $t3 = $t4 = 0;
                                                $sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE 'javascript:void(0)' AND `childid` IN ('$access_list') AND `active` = '1' ORDER BY `parentid`,`sortorder` ASC"; $query = mysqli_query($conn,$sql);
                                                while($row = mysqli_fetch_assoc($query)){
                                                    $level1_name = $level1_pid = $level1_cid = $level1_key = $module = $id_name1 = $id_name1a = "";
                                                    $level1_name = $row['name']; $level1_pid = $row['parentid']; $level1_cid = $row['childid'];
                                                    $level1_key = $level1_pid.",".$level1_cid; $module = $row['module']; $r1++; $id_name1 = "level1_".$r1; $t1++; $id_name1a = "level1_".$r1."_t".$t1;
                                                    
                                                    /*Title Flag*/ $mtitle = ""; if((int)$admin_title == 1){ $mtitle = $id_name1; } else{ $mtitle = $level1_name; }
                                                    echo "<div class='item'><input type='checkbox' id='$id_name1a' class='cbox' /><label for='$id_name1a'><input type='checkbox' name='links[]' id='$id_name1' class='checkbox' title='$mtitle' onclick='check_report_child_id(this.id);check_trans_child_id4(this.id,1);' />&nbsp;$level1_name</label>";
                                                    echo "<ul>";
                                                    $level2_name = $level2_pid = $level2_cid = $display2_href = $level2_key = $id_name2 = $id_name3 = "";
                                                    if(substr_count($module, 'Report') > 0 || substr_count($module, 'report') > 0){
                                                        $sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$level1_cid' AND `childid` IN ('$access_list') AND `active` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
                                                        while($row1 = mysqli_fetch_assoc($query1)){
                                                            $level2_name = $level2_pid = $level2_cid = $display2_href = $level2_key = $id_name2 = $id_name3 = "";
                                                            $level2_name = $row1['name']; $level2_pid = $row1['parentid']; $level2_cid = $row1['childid']; $display2_href = $row1['href'];
                                                            $level2_key = $level1_key.",".$level2_pid.",".$level2_cid;

                                                            $r2++; $id_name2 = ""; $id_name2 = $id_name1."_".$r2;
                                                            $r3++; $id_name3 = ""; $id_name3 = $id_name2."_".$r3;

                                                            /*Title Flag*/ $mtitle = ""; if((int)$admin_title == 1){ $mtitle = "$level2_name-$level2_key-$display2_href"; } else{ $mtitle = $level2_name; }
                                                            echo "<li><div class='row'><div class='rname-menu'><input type='checkbox' name='links[]' id='$id_name2' onclick='check_report_child_id(this.id);' />&nbsp;$level2_name</div><div class='mymenu view-menu'><input type='checkbox' name='displays[]' id='$id_name3' value='$level2_key' title='$mtitle' style='margin:5px;' /></div>";
                                                                
                                                        }
                                                    }
                                                    else{
                                                        $level2_name = $level2_pid = $level2_cid = $level2_key = $id_name2 = $id_name2a = "";
                                                        $sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$level1_cid' AND `childid` IN ('$access_list') AND `active` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
                                                        while($row1 = mysqli_fetch_assoc($query1)){
                                                            $level2_name = $level2_pid = $level2_cid = $level2_key = $id_name2 = $id_name2a = "";
                                                            $level2_name = $row1['name']; $level2_pid = $row1['parentid']; $level2_cid = $row1['childid'];
                                                            $level2_key = $level1_key.",".$level2_pid.",".$level2_cid;

                                                            $s2++; $id_name2 = $id_name1."_".$s2; $t2++; $id_name2a = $id_name2."_r".$t2;

                                                            /*Title Flag*/ $mtitle = ""; if((int)$admin_title == 1){ $mtitle = $id_name2; } else{ $mtitle = $level2_name; }
                                                            echo "<li><div class='sub-item'><input type='checkbox' id='$id_name2a' class='cbox' /><label for='$id_name2a'><input type='checkbox' name='links[]' id='$id_name2' title='$mtitle' class='checkbox' onclick='check_report_child_id(this.id);check_trans_child_id4(this.id,2);' />&nbsp;$level2_name</label>";
                                                            $id_name2D = $id_name2."&D";
                                                            $id_name2A = $id_name2."&A";
                                                            $id_name2E = $id_name2."&E";
                                                            $id_name2R = $id_name2."&R";
                                                            $id_name2P = $id_name2."&P";
                                                            $id_name2U = $id_name2."&U";
                                                            
                                                            /*Title Flag*/
                                                            $mtitleD = $mtitleA = $mtitleE = $mtitleR = $mtitleP = $mtitleU = "";
                                                            if((int)$admin_title == 1){
                                                                $mtitleD = $id_name2D;
                                                                $mtitleA = $id_name2A;
                                                                $mtitleE = $id_name2E;
                                                                $mtitleR = $id_name2R;
                                                                $mtitleP = $id_name2P;
                                                                $mtitleU = $id_name2U;
                                                            }

                                                            echo "<div class='row sub-menu'><div class='sname-menu'></div>";
                                                            echo "<div class='mymenu view-menu' style='background: #F9E8E4;padding-bottom:5px;'><input type='checkbox' name='disp_col[]' id='$id_name2D' title='$mtitleD' style='margin:5px;' onclick='check_trans_child_id3(this.id);' /></div>";
                                                            echo "<div class='mymenu add-menu' style='background: #F9E8E4;padding-bottom:5px;'><input type='checkbox' name='add_col[]' id='$id_name2A' title='$mtitleA' style='margin:5px;' onclick='check_trans_child_id3(this.id);' /></div>";
                                                            echo "<div class='mymenu edit-menu' style='background: #F9E8E4;padding-bottom:5px;'><input type='checkbox' name='edit_col[]' id='$id_name2E' title='$mtitleE' style='margin:5px;' onclick='check_trans_child_id3(this.id);' /></div>";
                                                            echo "<div class='mymenu delete-menu' style='background: #F9E8E4;padding-bottom:5px;'><input type='checkbox' name='delete_col[]' id='$id_name2R' title='$mtitleR' style='margin:5px;' onclick='check_trans_child_id3(this.id);' /></div>";
                                                            echo "<div class='mymenu print-menu' style='background: #F9E8E4;padding-bottom:5px;'><input type='checkbox' name='print_col[]' id='$id_name2P' title='$mtitleP' style='margin:5px;' onclick='check_trans_child_id3(this.id);' /></div>";
                                                            echo "<div class='mymenu update-menu' style='background: #F9E8E4;padding-bottom:5px;'><input type='checkbox' name='update_col[]' id='$id_name2U' title='$mtitleU' style='margin:5px;' onclick='check_trans_child_id3(this.id);' /></div>";
                                                            echo "</div>";
                                                            echo "<ul>";

                                                            $level3_name = $level3_pid = $level3_cid = $display_href = $level3_key = $level3_href = $add_link = $edit_link = $delete_link = $print_link = $update_link = ""; $l1 = array();
                                                            $sql2 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$level2_cid' AND `childid` IN ('$access_list') AND `active` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query2 = mysqli_query($conn,$sql2);
                                                            while($row2 = mysqli_fetch_assoc($query2)){
                                                                $level3_name = $level3_pid = $level3_cid = $display_href = $level3_key = $level3_href = $add_link = $edit_link = $delete_link = $print_link = $update_link = ""; $l1 = array();

                                                                $level3_name = $row2['name']; $level3_pid = $row2['parentid']; $level3_cid = $row2['childid']; $display_href = $row2['href'];
                                                                $level3_key = $level2_key.",".$level3_pid.",".$level3_cid;
                                                                $level3_href = $row2['href'];
                                                                $l1 = explode("_display_", $level3_href);
                                                                $add_link = $l1[0]."_add_".$l1[1];
                                                                $edit_link = $l1[0]."_edit_".$l1[1];
                                                                $delete_link = $l1[0]."_delete_".$l1[1];
                                                                $print_link = $l1[0]."_print_".$l1[1];
                                                                $update_link = $l1[0]."_update_".$l1[1];
                                                                
                                                                $add_id = $edit_id = $delete_id = $print_id = $update_id = "";
                                                                $add_id = $level3_cid."A";
                                                                $edit_id = $level3_cid."E";
                                                                $delete_id = $level3_cid."R";
                                                                $print_id = $level3_cid."P";
                                                                $update_id = $level3_cid."U";

                                                                $add_flag = $edit_flag = $delete_flag = $print_flag = $update_flag = 0;
                                                                $add_cid = $edit_cid = $delete_cid = $print_cid = $update_cid = "";
                                                                $add_href = $edit_href = $delete_href = $print_href = $update_href = "";
                                                                $level4_pid = $level4_cid = $level4_name = $level4_key = "";

                                                                $link_filter = " AND (`href` IN ('$add_link','$edit_link','$delete_link','$print_link','$update_link') OR `childid` IN ('$add_id','$edit_id','$delete_id','$print_id','$update_id'))";
                                                                $sql3 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$level3_cid' AND `childid` IN ('$access_list')".$link_filter." AND `active` ='1' ORDER BY `parentid`,`sortorder` ASC";
                                                                $query3 = mysqli_query($conn,$sql3);
                                                                while($row3 = mysqli_fetch_assoc($query3)){
                                                                    $level4_pid = $level4_cid = $level4_name = $level4_key = "";
                                                                    $level4_pid = $row3['parentid']; $level4_cid = $row3['childid']; $level4_name = $row3['name'];
                                                                    $level4_key = $level3_key.",".$level4_pid;
                                                                    if($row3['href'] == $add_link || $add_id == $level4_cid){ $add_flag = 1; $add_cid = $row3['childid']; $add_href = $row3['href']; }
                                                                    if($row3['href'] == $edit_link || $edit_id == $level4_cid){ $edit_flag = 1; $edit_cid = $row3['childid']; $edit_href = $row3['href']; }
                                                                    if($row3['href'] == $delete_link || $delete_id == $level4_cid){ $delete_flag = 1; $delete_cid = $row3['childid']; $delete_href = $row3['href']; }
                                                                    if($row3['href'] == $print_link || $print_id == $level4_cid){ $print_flag = 1; $print_cid = $row3['childid']; $print_href = $row3['href']; }
                                                                    if($row3['href'] == $update_link || $update_id == $level4_cid){ $update_flag = 1; $update_cid = $row3['childid']; $update_href = $row3['href']; }
                                                                }
                                                                
                                                                $s3++; $id_name3 = ""; $id_name3 = $id_name2."_".$s3;
                                                                $id_nameD = ""; $id_nameD = $id_name3."_D";

                                                                /*Title Flag*/ $mtitle = ""; if((int)$admin_title == 1){ $mtitle = "$level3_name-$level3_key-$display_href-($id_nameD)"; } else{ $mtitle = "Display"; }
                                                                echo "<li><div class='row'>
                                                                <div class='name-menu'><input type='checkbox' name='links[]' id='$id_name3' onclick='check_trans_child_id2(this.id);' />&nbsp;$level3_name</div><div class='mymenu view-menu'>
                                                                <input type='checkbox' name='displays[]' id='$id_nameD' value='$level3_key' onclick='check_trans_child_id2(this.id);' title='$mtitle' style='margin:5px;' /></div>";
                                                                
                                                                $id_nameA = ""; $id_nameA = $id_name3."_A";
                                                                $id_nameE = ""; $id_nameE = $id_name3."_E";
                                                                $id_nameR = ""; $id_nameR = $id_name3."_R";
                                                                $id_nameP = ""; $id_nameP = $id_name3."_P";
                                                                $id_nameU = ""; $id_nameU = $id_name3."_U";

                                                                /*Title Flag*/ $mtitle = ""; if((int)$admin_title == 1){ $mtitle = "$level4_name-$level4_key-$add_href-($id_nameA)"; } else{ $mtitle = "Add"; }
                                                                if($add_flag == 1){
                                                                    echo "<div class='mymenu add-menu'><input type='checkbox' name='adds[]' id='$id_nameA' value='$add_cid' title='$mtitle' style='margin:5px;' /></div>";
                                                                }
                                                                else{
                                                                    echo "<div class='mymenu add-menu'><input type='checkbox' name='adds[]' id='$id_nameA' value='$add_cid' title='$mtitle' style='margin:5px;' disabled /></div>";
                                                                }
                                                                /*Title Flag*/ $mtitle = ""; if((int)$admin_title == 1){ $mtitle = "$level4_name-$level4_key-$edit_href-($id_nameE)"; } else{ $mtitle = "Edit"; }
                                                                if($edit_flag == 1){
                                                                    echo "<div class='mymenu edit-menu'><input type='checkbox' name='edits[]' id='$id_nameE' value='$edit_cid' title='$mtitle' style='margin:5px;' /></div>";
                                                                }
                                                                else{
                                                                    echo "<div class='mymenu edit-menu'><input type='checkbox' name='edits[]' id='$id_nameE' value='$edit_cid' title='$mtitle' style='margin:5px;' disabled /></div>";
                                                                }
                                                                /*Title Flag*/ $mtitle = ""; if((int)$admin_title == 1){ $mtitle = "$level4_name-$level4_key-$delete_href-($id_nameR)"; } else{ $mtitle = "Delete"; }
                                                                if($delete_flag == 1){
                                                                    echo "<div class='mymenu delete-menu'><input type='checkbox' name='deletes[]' id='$id_nameR' value='$delete_cid' title='$mtitle' style='margin:5px;' /></div>";
                                                                }
                                                                else{
                                                                    echo "<div class='mymenu delete-menu'><input type='checkbox' name='deletes[]' id='$id_nameR' value='$delete_cid' title='$mtitle' style='margin:5px;' disabled /></div>";
                                                                }
                                                                /*Title Flag*/ $mtitle = ""; if((int)$admin_title == 1){ $mtitle = "$level4_name-$level4_key-$print_href-($id_nameP)"; } else{ $mtitle = "Print"; }
                                                                if($print_flag == 1){
                                                                    echo "<div class='mymenu print-menu'><input type='checkbox' name='prints[]' id='$id_nameP' value='$print_cid' title='$mtitle' style='margin:5px;' /></div>";
                                                                }
                                                                else{
                                                                    echo "<div class='mymenu print-menu'><input type='checkbox' name='prints[]' id='$id_nameP' value='$print_cid' title='$mtitle' style='margin:5px;' disabled /></div>";
                                                                }
                                                                /*Title Flag*/ $mtitle = ""; if((int)$admin_title == 1){ $mtitle = "$level4_name-$level4_key-$update_href-($id_nameU)"; } else{ $mtitle = "Update"; }
                                                                if($update_flag == 1){
                                                                    echo "<div class='mymenu update-menu'><input type='checkbox' name='updates[]' id='$id_nameU' value='$update_cid' title='$mtitle' style='margin:5px;' /></div>";
                                                                }
                                                                else{
                                                                    echo "<div class='mymenu update-menu'><input type='checkbox' name='updates[]' id='$id_nameU' value='$update_cid' title='$mtitle' style='margin:5px;' disabled /></div>";
                                                                }
                                                                echo "</div></li>";
                                                                
                                                            }
                                                            echo "</ul></div></li>";
                                                        }
                                                    }
                                                    echo "</ul></div>";
                                                }
                                                ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-8">
                                            <div class="card card-purple" style="height: 550px;">
                                                <div class="card-header">
                                                    <h3 class="card-title">Mobile Access</h3>
                                                </div>
                                                <div class="p-0 card-body table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <?php
                                                            echo "<tr>";
                                                            echo "<th rowspan='2' colspan='3' style='text-align:center;'><br/>All</th>";
                                                                echo "<th style='text-align:center;'>View</th>";
                                                                echo "<th style='text-align:center;'>Add</th>";
                                                                echo "<th style='text-align:center;'>Edit</th>";
                                                                echo "<th style='text-align:center;'>Delete</th>";
                                                            echo "</tr>";
                                                            echo "<tr>";
                                                                echo "<th><input type='checkbox' name='view_mobile' id='view_mobile' class='form-control' value='1' style='transform: scale(.5);text-align:center;' onclick='checkall_mobile(this.id)'/></th>";
                                                                echo "<th><input type='checkbox' name='add_mobile' id='add_mobile' class='form-control' value='2' style='transform: scale(.5);text-align:center;' onclick='checkall_mobile(this.id)' /></th>";
                                                                echo "<th><input type='checkbox' name='edit_mobile' id='edit_mobile' class='form-control' value='3' style='transform: scale(.5);text-align:center;' onclick='checkall_mobile(this.id)' /></th>";
                                                                echo "<th><input type='checkbox' name='delete_mobile' id='delete_mobile' class='form-control' value='4' style='transform: scale(.5);text-align:center;' onclick='checkall_mobile(this.id)' /></th>";
                                                            echo "</tr>";
                                                            $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
                                                            while($row = mysqli_fetch_assoc($query)){
                                                                $access_list = implode("','",array_unique(explode(",",$row['add_flag'].",".$row['edit_flag'].",".$row['delete_flag'].",".$row['view_flag'])));
                                                                $add_access_list = $row['add_flag'];
                                                                $edit_access_list = $row['edit_flag'];
                                                                $delete_access_list = $row['delete_flag'];
                                                                $view_access_list = $row['view_flag'];
                                                            }
                                                            $sql1 = "SELECT * FROM `app_permissions` WHERE `id` IN ('$access_list') AND `type` = 'Transaction' ORDER BY `type` DESC,`screens_position` ASC"; $query1 = mysqli_query($conn,$sql1);
                                                            while($row1 = mysqli_fetch_assoc($query1)){
                                                                $dname = $row1['display_name'];
                                                                $dvalue = $row1['id'];
                                                                echo "<tr>";
                                                                echo "<th colspan='3' style='width:auto;'>".$dname."</th>";
                                                                echo "<th><input type='checkbox' name='view_mobile[]' id='view_mobile[]' class='form-control' value='$dvalue' title='View' style='transform: scale(.5);' /></th>";
                                                                echo "<th><input type='checkbox' name='add_mobile[]' id='add_mobile[]' class='form-control' value='$dvalue' title='Add' style='transform: scale(.5);' /></th>";
                                                                echo "<th><input type='checkbox' name='edit_mobile[]' id='edit_mobile[]' class='form-control' value='$dvalue' title='Edit' style='transform: scale(.5);' /></th>";
                                                                echo "<th><input type='checkbox' name='delete_mobile[]' id='delete_mobile[]' class='form-control' value='$dvalue' title='Delete' style='transform: scale(.5);' /></th>";
                                                                echo "</tr>";
                                                            }
                                                            echo "<tr>";
                                                            echo "<th colspan='7' style='width:auto;text-align:center;' class='bg-purple'>Reports</th>";
                                                            echo "</tr>";
                                                            $sql1 = "SELECT * FROM `app_permissions` WHERE `id` IN ('$access_list') AND `type` = 'Report' ORDER BY `type` DESC,`screens_position` ASC"; $query1 = mysqli_query($conn,$sql1);
                                                            while($row1 = mysqli_fetch_assoc($query1)){
                                                                $dname = $row1['display_name'];
                                                                $dvalue = $row1['id'];
                                                                echo "<tr>";
                                                                echo "<th colspan='3' style='width:auto;'>".$dname."</th>";
                                                                echo "<th><input type='checkbox' name='view_mobile[]' id='view_mobile[]' class='form-control' value='$dvalue' title='View' style='transform: scale(.5);' /></th>";
                                                                echo "<th><input type='checkbox' name='add_mobile[]' id='add_mobile[]' class='form-control' value='$dvalue' title='Add' style='transform: scale(.5);' /></th>";
                                                                echo "<th><input type='checkbox' name='edit_mobile[]' id='edit_mobile[]' class='form-control' value='$dvalue' title='Edit' style='transform: scale(.5);' /></th>";
                                                                echo "<th><input type='checkbox' name='delete_mobile[]' id='delete_mobile[]' class='form-control' value='$dvalue' title='Delete' style='transform: scale(.5);' /></th>";
                                                                echo "</tr>";
                                                            }
                                                        ?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>Dup Flag<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="dup_flag" id="dup_flag" class="form-control" value="0" readonly />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_useraccess.php?ccid'+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var empname = document.getElementById("empname").value;
                var a = document.getElementById("uname").value;
                var b = document.getElementById("upass").value;
                var dup_flag = document.getElementById("dup_flag").value;
                var c = 0;
                var checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
                if(empname.match("select")){
                    alert("Select Employee ..!");
                    document.getElementById("empname").focus();
                    c = 0;
                }
                else if(a.length == 0){
                    alert("Enter Username ..!");
                    document.getElementById("uname").focus();
                    c = 0;
                }
                else if(b.length == 0){
                    alert("Enter Password ..!");
                    document.getElementById("upass").focus();
                    c = 0;
                }
                else if(dup_flag == 1 || dup_flag == "1"){
                    alert("Username already taken \n Kindly change username and try again ..!");
                    document.getElementById("uname").focus();
                    c = 0;
                }
                else if(checkboxes.length == 0){
                    alert("Please select user access details ..!");
                    c = 0;
                }
                else {
                    c = checkboxes.length;
                }
                if(c > 0){
                    return true;
                }
                else {
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function checkall_mobile(a){
                var c = document.getElementById(a).value;
                var selectallbox = document.getElementById(a);
                if(a.match("add_mobile")){
                    var checkboxes = document.querySelectorAll('input[name="add_mobile[]"]');
                }
                else if(a.match("edit_mobile")){
                    var checkboxes = document.querySelectorAll('input[name="edit_mobile[]"]');
                }
                else if(a.match("delete_mobile")){
                    var checkboxes = document.querySelectorAll('input[name="delete_mobile[]"]');
                }
                else if(a.match("view_mobile")){
                    var checkboxes = document.querySelectorAll('input[name="view_mobile[]"]');
                }
                else { }
                for (var i = 0; i < checkboxes.length; i++) {
                    if(selectallbox.checked == true){
                        checkboxes[i].checked = true;
                        //alert(i);
                    }
                    else{
                        checkboxes[i].checked = false;
                    }
                }
            }
            function checkall(a){
                var c = document.getElementById(a).value;
                var selectallbox = document.getElementById(a);
                if(a.match("display")){
                    var checkboxes = document.querySelectorAll('input[name="displays[]"]');
                }
                else if(a.match("add")){
                    var checkboxes = document.querySelectorAll('input[name="adds[]"]');
                }
                else if(a.match("edit")){
                    var checkboxes = document.querySelectorAll('input[name="edits[]"]');
                }
                else if(a.match("update")){
                    var checkboxes = document.querySelectorAll('input[name="updates[]"]');
                }
                else if(a.match("delete")){
                    var checkboxes = document.querySelectorAll('input[name="deletes[]"]');
                }
                else if(a.match("print")){
                    var checkboxes = document.querySelectorAll('input[name="prints[]"]');
                }
                else if(a.match("link")){
                    var checkboxes = document.querySelectorAll('input[name="links[]"]');
                    var cbox = document.querySelectorAll('input[class="cbox"]');
                    alert(cbox.length);
                }
                else {
                    var checkboxes = document.querySelectorAll('input[name="cbox[]"]');
                }
                for (var i = 0; i < checkboxes.length; i++) {
                    if(selectallbox.checked == true){
                        checkboxes[i].checked = true;
                        //alert(i);
                    }
                    else{
                        checkboxes[i].checked = false;
                    }
                }
            }
            function adminaccess(a){
                var c = document.getElementById(a).value;
                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                if(c.match("A")){
                    for (var i = 0; i < checkboxes.length; i++) {
                        checkboxes[i].checked = true;
                    }
                }
                else if(c.match("N")){
                    for (var i = 0; i < checkboxes.length; i++) {
                        checkboxes[i].checked = false;
                    }
                }
                else {
                    
                }
            }
            function check_all_access(a){
                var c = document.getElementById(a);
                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                if(c.checked == true){
                    for (var i = 0; i < checkboxes.length; i++) {
                        checkboxes[i].checked = true;
                    }
                }
                else{
                    for (var i = 0; i < checkboxes.length; i++) {
                        checkboxes[i].checked = false;
                    }
                }
            }
            function check_report_child_id(a){
                var id_name = a+"_";
                var mid_name = a+"_t";
                var mid_name2 = a+"_r";
                var cid_name1 = cid_name2 = cid_name3 = tree_select1 = tree_select2 = mid_name3 = "";
                for(var d = 0;d <= 500;d++){
                    cid_name1 = "";
                    cid_name1 = id_name+""+d;
                    tree_select1 = mid_name+""+d;
                    tree_select2 = mid_name2+""+d;
                    if(document.getElementById(tree_select1)){
                        if(document.getElementById(a).checked == true){
                            document.getElementById(tree_select1).checked = true;
                        }
                        else{
                            document.getElementById(tree_select1).checked = false;
                        } 
                    }
                    if(document.getElementById(tree_select2)){
                        if(document.getElementById(a).checked == true){
                            document.getElementById(tree_select2).checked = true;
                        }
                        else{
                            document.getElementById(tree_select2).checked = false;
                        } 
                    }
                    if(document.getElementById(cid_name1)){
                        if(document.getElementById(a).checked == true){
                            document.getElementById(cid_name1).checked = true;
                        }
                        else{
                            document.getElementById(cid_name1).checked = false;
                        }
                        check_trans_child_id2(cid_name1);
                        for(var e = 0;e <= 500;e++){
                            cid_name2 = "";
                            cid_name2 = cid_name1+"_"+e;
                            mid_name3 = cid_name1+"_r"+e;
                            if(document.getElementById(mid_name3)){
                                if(document.getElementById(a).checked == true){
                                    document.getElementById(mid_name3).checked = true;
                                }
                                else{
                                    document.getElementById(mid_name3).checked = false;
                                }
                            }
                            if(document.getElementById(cid_name2)){
                                if(document.getElementById(a).checked == true){
                                    document.getElementById(cid_name2).checked = true;
                                }
                                else{
                                    document.getElementById(cid_name2).checked = false;
                                }
                                check_trans_child_id2(cid_name2);
                                /*for(var f = 0;f <= 500;f++){
                                    cid_name3 = "";
                                    cid_name3 = cid_name2+"_"+f;
                                    if(document.getElementById(cid_name3)){
                                        if(document.getElementById(a).checked == true){
                                            document.getElementById(cid_name3).checked = true;
                                        }
                                        else{
                                            document.getElementById(cid_name3).checked = false;
                                        }
                                    }
                                }*/
                            }
                        }
                    }
                }
            }
            function check_trans_child_id2(a){
                var id_name = a;
                var cid_nameD = cid_nameA = cid_nameE = cid_nameR = cid_nameP = cid_nameU = "";
                cid_nameD = id_name+"_D";
                cid_nameA = id_name+"_A";
                cid_nameE = id_name+"_E";
                cid_nameR = id_name+"_R";
                cid_nameP = id_name+"_P";
                cid_nameU = id_name+"_U";
                if(document.getElementById(cid_nameD)){
                    if(document.getElementById(a).checked == true){ document.getElementById(cid_nameD).checked = true; } else{ document.getElementById(cid_nameD).checked = false; }
                }
                if(document.getElementById(cid_nameA)){
                    if(document.getElementById(a).checked == true){ document.getElementById(cid_nameA).checked = true; } else{ document.getElementById(cid_nameA).checked = false; }
                }
                if(document.getElementById(cid_nameE)){
                    if(document.getElementById(a).checked == true){ document.getElementById(cid_nameE).checked = true; } else{ document.getElementById(cid_nameE).checked = false; }
                }
                if(document.getElementById(cid_nameR)){
                    if(document.getElementById(a).checked == true){ document.getElementById(cid_nameR).checked = true; } else{ document.getElementById(cid_nameR).checked = false; }
                }
                if(document.getElementById(cid_nameP)){
                    if(document.getElementById(a).checked == true){ document.getElementById(cid_nameP).checked = true; } else{ document.getElementById(cid_nameP).checked = false; }
                }
                if(document.getElementById(cid_nameU)){
                    if(document.getElementById(a).checked == true){ document.getElementById(cid_nameU).checked = true; } else{ document.getElementById(cid_nameU).checked = false; }
                }
            }
            function check_trans_child_id3(a){
                var subm_dt = a.split("&");
                var childid = subm_dt[0];
                var idtype = subm_dt[1];

                var col_id = "";
                for(var d = 0;d <= 500;d++){
                    col_id = ""; col_id = childid+"_"+d+"_"+idtype;
                    if(document.getElementById(col_id)){
                        if(document.getElementById(a).checked == true){
                            document.getElementById(col_id).checked = true;
                        }
                        else{
                            document.getElementById(col_id).checked = false;
                        }
                    }
                }
            }
            function check_trans_child_id4(a,step){
                var col_id = "";
                if(step == 1){
                    if(document.getElementById(a).checked == true){
                        for(var d = 0;d <= 500;d++){
                            col_id = ""; col_id = a+"_"+d;
                            if(document.getElementById(col_id+"&D")){ document.getElementById(col_id+"&D").checked = true; }
                            if(document.getElementById(col_id+"&A")){ document.getElementById(col_id+"&A").checked = true; }
                            if(document.getElementById(col_id+"&E")){ document.getElementById(col_id+"&E").checked = true; }
                            if(document.getElementById(col_id+"&R")){ document.getElementById(col_id+"&R").checked = true; }
                            if(document.getElementById(col_id+"&P")){ document.getElementById(col_id+"&P").checked = true; }
                            if(document.getElementById(col_id+"&U")){ document.getElementById(col_id+"&U").checked = true; }
                        }
                    }
                    else{
                        for(var d = 0;d <= 500;d++){
                            col_id = ""; col_id = a+"_"+d;
                            if(document.getElementById(col_id+"&D")){ document.getElementById(col_id+"&D").checked = false; }
                            if(document.getElementById(col_id+"&A")){ document.getElementById(col_id+"&A").checked = false; }
                            if(document.getElementById(col_id+"&E")){ document.getElementById(col_id+"&E").checked = false; }
                            if(document.getElementById(col_id+"&R")){ document.getElementById(col_id+"&R").checked = false; }
                            if(document.getElementById(col_id+"&P")){ document.getElementById(col_id+"&P").checked = false; }
                            if(document.getElementById(col_id+"&U")){ document.getElementById(col_id+"&U").checked = false; }
                        }
                    }
                }
                else if(step == 2){
                    col_id = ""; col_id = a;
                    if(document.getElementById(a).checked == true){
                        if(document.getElementById(col_id+"&D")){ document.getElementById(col_id+"&D").checked = true; }
                        if(document.getElementById(col_id+"&A")){ document.getElementById(col_id+"&A").checked = true; }
                        if(document.getElementById(col_id+"&E")){ document.getElementById(col_id+"&E").checked = true; }
                        if(document.getElementById(col_id+"&R")){ document.getElementById(col_id+"&R").checked = true; }
                        if(document.getElementById(col_id+"&P")){ document.getElementById(col_id+"&P").checked = true; }
                        if(document.getElementById(col_id+"&U")){ document.getElementById(col_id+"&U").checked = true; }
                    }
                    else{
                        if(document.getElementById(col_id+"&D")){ document.getElementById(col_id+"&D").checked = false; }
                        if(document.getElementById(col_id+"&A")){ document.getElementById(col_id+"&A").checked = false; }
                        if(document.getElementById(col_id+"&E")){ document.getElementById(col_id+"&E").checked = false; }
                        if(document.getElementById(col_id+"&R")){ document.getElementById(col_id+"&R").checked = false; }
                        if(document.getElementById(col_id+"&P")){ document.getElementById(col_id+"&P").checked = false; }
                        if(document.getElementById(col_id+"&U")){ document.getElementById(col_id+"&U").checked = false; }
                    }
                }
            }
            function check_duplicate_user(a){
                var uname = document.getElementById(a).value; var ttype = "add";
                var fetch_dupflag = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_fetch_userduplicate_flag.php?uname="+uname+"&ttype="+ttype;
                //window.open(url);
                var asynchronous = true;
                fetch_dupflag.open(method, url, asynchronous);
                fetch_dupflag.send();
                fetch_dupflag.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var dup_flag = this.responseText;
                        if(dup_flag == 1){
                            alert("Username already exist \n Kindly create new Username ...!");
                            document.getElementById("uname").focus();
                        }
                        else{ }
                        document.getElementById("dup_flag").value = dup_flag;
                    }
                }
            }
            $('input[type=checkbox]').change(function(){
                // if is checked
                if(this.checked){
                    // check all children
                    var lenchk = $(this).closest('ul').find(':checkbox');
                    var lenchkChecked = $(this).closest('ul').find(':checkbox:checked');
                    //if all siblings are checked, check its parent checkbox
                    if (lenchk.length == lenchkChecked.length) {
                        $(this).closest('ul').siblings().find(':checkbox').prop('checked', true);
                    }
                    else{
                        $(this).closest('.checkbox').siblings().find(':checkbox').prop('checked', true);
                    }
                }
                else{
                    // uncheck all children
                    $(this).closest('.checkbox').siblings().find(':checkbox').prop('checked', false);
                    $(this).closest('ul').siblings().find(':checkbox').prop('checked', false);
                }
            });
            $(function() {
                $(":checkbox").change(function () {
                    $(this).children(':checkbox').attr('checked', this.checked);
                });
            });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
            function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
        </script>
        <?php include "header_foot.php"; ?>
        <script>
            $(document).on('select2:open', function(e) { document.querySelector(`[name="${e.target.id}"]`).focus(); });
        </script>
        
        <script>
            function fetch_flock_details(a){
                var f_aflag = u_aflag = s_aflag = b_aflag = fl_aflag = 0;
                var farms = units = sheds = batches = flocks = "";
                for(var option of document.getElementById("farms[]").options){ if(option.selected){ if(option.value == "all"){ f_aflag = 1; } else{ if(farms == ""){ farms = option.value; } else{ farms = farms+"@"+option.value; } } } }
                for(var option of document.getElementById("units[]").options){ if(option.selected){ if(option.value == "all"){ u_aflag = 1; } else{ if(units == ""){ units = option.value; } else{ units = units+"@"+option.value; } } } }
                for(var option of document.getElementById("sheds[]").options){ if(option.selected){ if(option.value == "all"){ s_aflag = 1; } else{ if(sheds == ""){ sheds = option.value; } else{ sheds = sheds+"@"+option.value; } } } }
                for(var option of document.getElementById("batches[]").options){ if(option.selected){ if(option.value == "all"){ b_aflag = 1; } else{ if(batches == ""){ batches = option.value; } else{ batches = batches+"@"+option.value; } } } }
                for(var option of document.getElementById("flocks[]").options){ if(option.selected){ if(option.value == "all"){ fl_aflag = 1; } else{ if(flocks == ""){ flocks = option.value; } else{ flocks = flocks+"@"+option.value; } } } }
                if(f_aflag == 1){ farms = ""; farms = "all"; }
                if(u_aflag == 1){ units = ""; units = "all"; }
                if(s_aflag == 1){ sheds = ""; sheds = "all"; }
                if(b_aflag == 1){ batches = ""; batches = "all"; }
                if(fl_aflag == 1){ flocks = ""; flocks = "all"; }

                var user_code = '<?php echo $user_code; ?>';
                var ff_flag = uf_flag = sf_flag = bf_flag = fl_flag = 0;
                if(a == "farms[]"){ ff_flag = 1; }
                else if(a == "units[]"){ uf_flag = 1; }
                else if(a == "sheds[]"){ sf_flag = 1; }
                else if(a == "batches[]"){ bf_flag = 1; }
                else if(a == "flocks[]"){ fl_flag = 1; }
                else{ ff_flag = 1; }
                
                var fetch_fltrs = new XMLHttpRequest();
                var method = "GET";
                var url = "records/breeder_fetch_flock_filter_master.php?farms="+farms+"&units="+units+"&sheds="+sheds+"&batches="+batches+"&flocks="+flocks+"&ff_flag="+ff_flag+"&uf_flag="+uf_flag+"&sf_flag="+sf_flag+"&bf_flag="+bf_flag+"&fl_flag="+fl_flag+"&user_code="+user_code+"&fetch_type=multiple";
                //window.open(url);
                var asynchronous = true;
                fetch_fltrs.open(method, url, asynchronous);
                fetch_fltrs.send();
                fetch_fltrs.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var fltr_dt1 = this.responseText;
                        var fltr_dt2 = fltr_dt1.split("[@$&]");
                        var farm_list = fltr_dt2[0];
                        var unit_list = fltr_dt2[1];
                        var shed_list = fltr_dt2[2];
                        var batch_list = fltr_dt2[3];
                        var flock_list = fltr_dt2[4];

                        if(ff_flag == 1){
                            removeAllOptions(document.getElementById("units[]"));
                            removeAllOptions(document.getElementById("sheds[]"));
                            removeAllOptions(document.getElementById("batches[]"));
                            removeAllOptions(document.getElementById("flocks[]"));
                            $('#units\\[\\]').append(unit_list);
                            $('#sheds\\[\\]').append(shed_list);
                            $('#batches\\[\\]').append(batch_list);
                            $('#flocks\\[\\]').append(flock_list);
                        }
                        else if(uf_flag == 1){
                            removeAllOptions(document.getElementById("sheds[]"));
                            removeAllOptions(document.getElementById("batches[]"));
                            removeAllOptions(document.getElementById("flocks[]"));
                            $('#sheds\\[\\]').append(shed_list);
                            $('#batches\\[\\]').append(batch_list);
                            $('#flocks\\[\\]').append(flock_list);
                        }
                        else if(sf_flag == 1){
                            removeAllOptions(document.getElementById("batches[]"));
                            removeAllOptions(document.getElementById("flocks[]"));
                            $('#batches\\[\\]').append(batch_list);
                            $('#flocks\\[\\]').append(flock_list);
                        }
                        else if(bf_flag == 1){
                            removeAllOptions(document.getElementById("flocks[]"));
                            $('#flocks\\[\\]').append(flock_list);
                        }
                        else{ }
                    }
                }
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
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