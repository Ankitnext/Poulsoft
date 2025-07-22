<?php
//breeder_display_dailyentry1.php
include "newConfig.php";
include "number_format_ind.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['dailyentry1'] = $cid; } else{ $cid = $_SESSION['dailyentry1']; }
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC";
$query = mysqli_query($conn,$sql); $link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $cname = $row['name']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $dlink = $alink = $elink = $rlink = $plink = $ulink = $flink = array(); $sector_access = $cgroup_access = $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $dlink = str_replace(",","','",$row['displayaccess']);
        $alink = str_replace(",","','",$row['addaccess']);
        $elink = str_replace(",","','",$row['editaccess']);
        $rlink = str_replace(",","','",$row['deleteaccess']);
        $plink = str_replace(",","','",$row['printaccess']);
        $ulink = str_replace(",","','",$row['otheraccess']);
        $sector_access = $row['loc_access'];
        $cgroup_access = $row['cgroup_access'];
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; } else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; } else{ $user_type = "N"; }
    }
    $aid = 0;
    $flink = explode("','",$dlink); $acount = 0; foreach($flink as $flinks){ if($flinks == $cid){ $aid = 1; } }
    if($user_type == "S"){ $acount = 1; }
    else if($aid == 1){ $acount = 1; }
    else{ $acount = 0; }
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    </head>
    <body class="m-0 hold-transition sidebar-mini">
        <?php
        if($acount == 1){
            /*Check for Table Availability*/
            $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
            $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
            if(in_array("breeder_farms", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_farms LIKE poulso6_admin_broiler_broilermaster.breeder_farms;"; mysqli_query($conn,$sql1); }
            if(in_array("breeder_units", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_units LIKE poulso6_admin_broiler_broilermaster.breeder_units;"; mysqli_query($conn,$sql1); }
            if(in_array("breeder_sheds", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_sheds LIKE poulso6_admin_broiler_broilermaster.breeder_sheds;"; mysqli_query($conn,$sql1); }
            if(in_array("breeder_batch", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_batch LIKE poulso6_admin_broiler_broilermaster.breeder_batch;"; mysqli_query($conn,$sql1); }
            if(in_array("breeder_shed_allocation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_shed_allocation LIKE poulso6_admin_broiler_broilermaster.breeder_shed_allocation;"; mysqli_query($conn,$sql1); }
            if(in_array("breeder_dayentry_consumed", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_dayentry_consumed LIKE poulso6_admin_broiler_broilermaster.breeder_dayentry_consumed;"; mysqli_query($conn,$sql1); }
            if(in_array("breeder_dayentry_produced", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_dayentry_produced LIKE poulso6_admin_broiler_broilermaster.breeder_dayentry_produced;"; mysqli_query($conn,$sql1); }
            if(in_array("breeder_extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.breeder_extra_access LIKE poulso6_admin_broiler_broilermaster.breeder_extra_access;"; mysqli_query($conn,$sql1); }
            
            /*Check for Column Availability*/
            $sql='SHOW COLUMNS FROM `item_category`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("main_category", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `main_category` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `description`"; mysqli_query($conn,$sql); }
            if(in_array("bfeed_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bfeed_flag` INT(100) NOT NULL DEFAULT '0' AFTER `main_category`"; mysqli_query($conn,$sql); }
            if(in_array("begg_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `begg_flag` INT(100) NOT NULL DEFAULT '0' AFTER `bfeed_flag`"; mysqli_query($conn,$sql); }
            if(in_array("bmv_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_category` ADD `bmv_flag` INT(100) NOT NULL DEFAULT '0' AFTER `begg_flag`"; mysqli_query($conn,$sql); }
            
            $gp_id = $gc_id = $gp_name = $gp_link = $gp_link = $p_id = $c_id = $p_name = $p_link = array();
            $sql = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $gp_id = $row['parentid'];
                $gc_id[$row['childid']] = $row['childid'];
                $gp_name[$row['childid']] = $row['name'];
                $gp_link[$row['childid']] = $row['href'];
            }
            $add_link_acc = $edt_link_acc = $del_link_acc = $pnt_link_acc = $upd_link_acc = "";
            $alink = explode("','",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; }
            $elink = explode("','",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; }
            $rlink = explode("','",$rlink); foreach($rlink as $rlink1){ $del_acc[$rlink1] = $rlink1; }
            $plink = explode("','",$plink); foreach($plink as $plink1){ $pnt_acc[$plink1] = $plink1; $pnt_link_acc = $pnt_link_acc.",".$plink1; }
            $ulink = explode("','",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; }
            if(!empty($add_acc[$gp_id."A"])){ $add_flag = 1; $add_link = $gp_link[$gp_id."A"]; } else { $add_link = ""; $add_flag = 0; }
            if(!empty($edt_acc[$gp_id."E"])){ $edit_flag = 1; $edit_link = $gp_link[$gp_id."E"]; } else { $edit_link = ""; $edit_flag = 0; }
            if(!empty($del_acc[$gp_id."R"])){ $delete_flag = 1; $delete_link = $gp_link[$gp_id."R"]; } else { $delete_link = ""; $delete_flag = 0; }
            if(!empty($pnt_acc[$gp_id."P"])){ $print_flag = 1; $print_link = $gp_link[$gp_id."P"]; } else { $print_link = ""; $print_flag = 0; }
            if(!empty($upd_acc[$gp_id."U"])){ $update_flag = 1; $update_link = $gp_link[$gp_id."U"]; } else { $update_link = ""; $update_flag = 0; }

            $fsdate = $cid."-fdate"; $tsdate = $cid."-tdate"; $tsunit = $cid."-units"; $units = "all";
            if(isset($_POST['submit']) == true){
                $fdate = date("Y-m-d",strtotime($_POST['fdate']));
                $tdate = date("Y-m-d",strtotime($_POST['tdate']));
                $units = $_POST['units'];
                $_SESSION[$fsdate] = $fdate;
                $_SESSION[$tsdate] = $tdate;
                $_SESSION[$tsunit] = $units;
            }
            else {
                $fdate = $tdate = date("Y-m-d"); $units = "all";
                if(!empty($_SESSION[$fsdate])){ $fdate = date("Y-m-d",strtotime($_SESSION[$fsdate])); }
                if(!empty($_SESSION[$tsdate])){ $tdate = date("Y-m-d",strtotime($_SESSION[$tsdate])); }
                if(!empty($_SESSION[$tsunit])){ $units = $_SESSION[$tsunit]; }
            }

            $sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $unit_code = $unit_name = array();
            while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description']; }

            $sql = "SELECT *  FROM `breeder_extra_access` WHERE `field_name` LIKE 'Breeder Daily Entry' AND `field_function` LIKE 'Import Daily Entries' AND `user_access` LIKE 'all' AND `flag` = '1'";
            $query = mysqli_query($conn,$sql); $bde_iflag = mysqli_num_rows($query);

            //Fetch Print-View from Print Master
            $i = $pc = 0; $field_name = $field_name = $field_name = $field_name = $field_name = $field_name = array();
            $psql = "SELECT * FROM `broiler_printview_master` WHERE `file_name` LIKE '$href' AND `active` = '1' AND `dflag` = '0' ORDER BY `sort_order`,`id` ASC";
            $pquery = mysqli_query($conn,$psql);
            while($prow = mysqli_fetch_array($pquery)){
                $field_name[$i] = $prow['field_name'];
                $print_path[$i] = $prow['print_path'];
                $icon_type[$i] = $prow['icon_type'];
                $icon_path[$i] = $prow['icon_path'];
                $icon_color[$i] = $prow['icon_color'];
                $target[$i] = $prow['target'];
                $i++;
            }
            $pc = $i - 1;
            $unit_fltr = ""; if($units != 'all'){ $unit_fltr = " AND `unit_code` LIKE '$units'"; }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <form action="<?php echo $href; ?>" method="post">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row" align="left">
                                            <div class="form-group" style="width:100px;">
                                                <label for="fdate">From Date: </label>
                                                <input type="text" name="fdate" id="fdate" class="form-control rc_datepicker" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:90px;">
                                            </div>
                                            <div class="form-group" style="width:100px;">
                                                <label for="tdate">To Date: </label>
                                                <input type="text" name="tdate" id="tdate" class="form-control rc_datepicker" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="width:90px;">
                                            </div>
                                            <div class="form-group" style="width:100px;">
                                                <label for="units">Units </label>
                                                    <select name="units" id="units" class="form-control select2">
                                                        <option value="all" <?php if($units == "all"){ echo "selected"; } ?>>All</option>
                                                        <?php foreach($unit_code as $ucode){ if($unit_name[$ucode] != ""){ ?>
                                                        <option value="<?php echo $ucode; ?>" <?php if($units == $ucode){ echo "selected"; } ?>><?php echo $unit_name[$ucode]; ?></option>
                                                        <?php } } ?>
                                                    </select>
                                            </div>
                                            <div class="form-group" style="width:100px;margin-left:10px;">
                                                <br/><button type="submit" name="submit" id="submit" class="btn btn-success btn-sm">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4" align="right">
                                        <?php if($bde_iflag == 1){ ?>
                                            <button type="button" class="btn bg-info" id="addpage" value="breeder_import_dailyentry1.php" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> Import</button>
                                        <?php } ?>
                                        <?php if($add_flag == 1){ ?>
                                            <button type="button" class="btn bg-purple" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
										<th>Date</th>
										<th>Trnum</th>
										<th>Flock</th>
										<th>Age</th>
										<th>F.Mort</th>
										<th>F.Feed</th>
										<th>M.Mort</th>
										<th>M.Feed</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT * FROM `breeder_shed_allocation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                                        $query = mysqli_query($conn,$sql); $bflk_code = $bflk_name = $bflk_alist = array();
                                        while($row = mysqli_fetch_assoc($query)){
                                            $bflk_code[$row['code']] = $row['code']; $bflk_name[$row['code']] = $row['description'];
                                            if((int)$row['cls_flag'] == 0){ $bflk_alist[$row['code']] = $row['code']; }
                                        }
                                        $bflk_list = implode("','", $bflk_alist);
                                        $sql = "SELECT MAX(breed_age) as breed_age,flock_code FROM `breeder_dayentry_consumed` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$unit_fltr." AND `flock_code` IN ('$bflk_list') AND `dflag` = '0' GROUP BY `flock_code` ORDER BY `id` DESC";
                                        $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $flk_ages[$row['flock_code']] = $row['breed_age']; }

                                        $delete_url = $delete_link."?utype=delete&trnum=";
                                        $sql = "SELECT * FROM `breeder_dayentry_consumed` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$unit_fltr." AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['trnum'];
                                            $edit_url = $edit_link."?utype=edit&trnum=".$id;
                                            $print_url = $print_link."?utype=print&trnum=".$id;
                                            $authorize_url = $update_link."?utype=authorize&trnum=".$id;
                                            if($row['active'] == 1){
                                                $update_url = $update_link."?utype=pause&trnum=".$id;
                                            }
                                            else{
                                                $update_url = $update_link."?utype=activate&trnum=".$id;
                                            }
                                            $val = ""; $val = $row['trnum']."@".$bflk_name[$row['flock_code']]."@".round($row['age'],5);
                                    ?>
                                    <tr>
                                        <td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])) ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $bflk_name[$row['flock_code']]; ?></td>
										<td style="text-align:right;"><?php echo round($row['breed_wage'],5); ?></td>
										<td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($row['fmort_qty'])); ?></td>
										<td style="text-align:right;"><?php echo number_format_ind($row['ffeed_qty1']); ?></td>
										<td style="text-align:right;"><?php echo str_replace(".00","",number_format_ind($row['mmort_qty'])); ?></td>
										<td style="text-align:right;"><?php echo number_format_ind($row['mfeed_qty1']); ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>";
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1 && $flk_ages[$row['flock_code']] == $row['breed_age']){
                                                    ?>
                                                    <a href='javascript:void(0)' id='<?php echo $val; ?>' value='<?php echo $val; ?>' onclick='checkdelete(this.id)'>
                                                    <i class='fa fa-trash' style='color:red;' title='delete'></i>
                                                    </a>&ensp;&ensp;
                                                <?php
                                                }
                                                if($update_flag == 1){
                                                    if($row['active'] == 1){
                                                        echo "<a href='".$update_url."'><i class='fa fa-pause' style='color:blue;' title='Activate'></i></a>&ensp;&ensp;";
                                                    }
                                                    else{
                                                        echo "<a href='".$update_url."'><i class='fa fa-play' style='color:blue;' title='Pause'></i></a>&ensp;&ensp;";
                                                    }
                                                    echo "<a href='".$authorize_url."'><i class='fa fa-lock-open' style='color:orange;' title='Authorize'></i></a>&ensp;&ensp;";
                                                }
                                            }
                                        ?>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
			function checkdelete(x){
                var val1 = x.split("@");
                var trnum = val1[0];
                var flock = val1[1];
                var age = val1[2];
                var vname = val1[3];
                if(trnum != ""){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "breeder_check_dailyentry1.php?trnum="+trnum+"&flock="+flock+"&age="+age+"&vname="+vname;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var count = this.responseText;
                            if(parseFloat(count) > 0){
                                alert("You can't delete the Daily Entry with Flock: "+flock+", Age: "+age+". As Daily Entry is already in use!");
                            }
                            else{
                                var b = "<?php echo $delete_url; ?>"+trnum;
                                var c = confirm("are you sure you want to delete the Daily Entry with Flock: "+flock+", Age: "+age+" ?");
                                if(c == true){
                                    window.location.href = b;
                                }
                                else{ }
                            }
                        }
                    }
                }
			}
        </script>
        <?php
            }
            else{
        ?>
        <script>
            var x = confirm("You don't have access to this file\folder \n Kindly contact your admin for more details.");
            if(x == true){
                window.location.href="logout.php";
            }
            else{
                window.location.href="logout.php";
            }
        </script>
        <?php
            }
        ?>
        <script>
			function add_page(a){ var b = document.getElementById(a).value; window.location.href = b; }
		</script>
    <?php include "header_foot.php"; ?>
    </body>
</html>
<?php
}
else{
     header('location:index.php');
}
?>