<?php
//broiler_display_rearingcharge.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['rearingcharge'] = $cid; } else{ $cid = $_SESSION['rearingcharge']; }
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
    </head>
    <body class="m-0 hold-transition sidebar-mini">
        <?php
        if($acount == 1){
            /*Check for Table Availability*/
            $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
            $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
            if(in_array("broiler_gc_cop_standards", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_cop_standards LIKE poulso6_admin_broiler_broilermaster.broiler_gc_cop_standards;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_fcr_decentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_fcr_decentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_fcr_decentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_fcr_incentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_fcr_incentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_fcr_incentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_fcr_production", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_fcr_production LIKE poulso6_admin_broiler_broilermaster.broiler_gc_fcr_production;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_fcr_standards", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_fcr_standards LIKE poulso6_admin_broiler_broilermaster.broiler_gc_fcr_standards;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_cfcr_standards", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_cfcr_standards LIKE poulso6_admin_broiler_broilermaster.broiler_gc_cfcr_standards;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_loyalty_incentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_loyalty_incentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_loyalty_incentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_mi_decentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_mi_decentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_mi_decentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_mi_incentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_mi_incentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_mi_incentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_pc_decentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_pc_decentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_pc_decentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_pc_incentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_pc_incentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_pc_incentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_seasonal_incentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_seasonal_incentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_seasonal_incentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_sgc_standards", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_sgc_standards LIKE poulso6_admin_broiler_broilermaster.broiler_gc_sgc_standards;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_si_incentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_si_incentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_si_incentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_si_prodcost_range", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_si_prodcost_range LIKE poulso6_admin_broiler_broilermaster.broiler_gc_si_prodcost_range;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_smrbdw_incentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_smrbdw_incentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_smrbdw_incentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_smr_incentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_smr_incentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_smr_incentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_standard", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_standard LIKE poulso6_admin_broiler_broilermaster.broiler_gc_standard;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_st_decentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_st_decentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_st_decentive;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_gc_wi_incentive", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_gc_wi_incentive LIKE poulso6_admin_broiler_broilermaster.broiler_gc_wi_incentive;"; mysqli_query($conn,$sql1); }
            
            //Fetch Column From Generator Table
            $sql='SHOW COLUMNS FROM `broiler_gc_si_incentive`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            //Add Columns to Generator Table
            if(in_array("max_prod_cost", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_si_incentive` ADD `max_prod_cost` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Maximun Production Cost' AFTER `sales_max_rate`"; mysqli_query($conn,$sql); }

            $sql='SHOW COLUMNS FROM `broiler_gc_pc_incentive`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            //Add Columns to Generator Table
            if(in_array("std_prod_cost", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_pc_incentive` ADD `std_prod_cost` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `std_code`"; mysqli_query($conn,$sql); }

            $sql='SHOW COLUMNS FROM `broiler_gc_pc_decentive`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            //Add Columns to Generator Table
            if(in_array("std_prod_cost", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_pc_decentive` ADD `std_prod_cost` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `std_code`"; mysqli_query($conn,$sql); }

            $sql='SHOW COLUMNS FROM `broiler_gc_mi_incentive`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            //Add Columns to Generator Table
            if(in_array("mi_grades", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_mi_incentive` ADD `mi_grades` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `std_code`"; mysqli_query($conn,$sql); }

            $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Shortage Max Allowed Birds' AND `user_access` LIKE 'all' AND `flag` = '1'";
            $query = mysqli_query($conn,$sql); $shortage_maxbirds_flag = mysqli_num_rows($query);
            if($shortage_maxbirds_flag == 1){
                $sql='SHOW COLUMNS FROM `broiler_gc_st_decentive`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                if(in_array("shortage_allowed_maxbirds", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_st_decentive` ADD `shortage_allowed_maxbirds` INT(100) NULL DEFAULT '0' COMMENT 'Shortage Max Allowed Birds'"; mysqli_query($conn,$sql); }
            }

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

            //Schema selection
            $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Rearing Charge Master' AND `field_function` LIKE 'Schema selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
            $query = mysqli_query($conn,$sql); $schema_flag = mysqli_num_rows($query);
            if($schema_flag == 1){
                $sql='SHOW COLUMNS FROM `broiler_gc_standard`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                if(in_array("schema_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_gc_standard` ADD `schema_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Schema name' AFTER `branch_code`"; mysqli_query($conn,$sql); }
                
                $sql='SHOW COLUMNS FROM `broiler_rearingcharge`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                if(in_array("schema_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_rearingcharge` ADD `schema_name` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Schema name' AFTER `branch_code`"; mysqli_query($conn,$sql); }
            }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                           <div class="float-left"><h3 class="card-title">rearingcharge</h3></div>
                            <div class="float-right">
                            <?php if($add_flag == 1){ ?>
                                <button type="button" class="btn bg-purple" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>From Date</th>
										<th>To Date</th>
										<th>Branch</th>
                                        <?php if($schema_flag == 1){ echo "<th>Schema Name</th>"; } ?>
										<th>Chick Cost</th>
										<th>Feed Cost</th>
										<th>Med  Cost</th>
										<th>Admin Cost</th>
										<th>Std Cost</th>
										<th>Min Cost</th> 
										<th>Std FCR</th>
										<th>Std Mort</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                       $sql = "SELECT * FROM `location_branch` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
                                       while($row = mysqli_fetch_assoc($query)){
                                       $branch_name[$row['code']] = $row['description'];
                                       }
                                       $sql = "SELECT * FROM `broiler_gc_standard` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['id'];
                                            $edit_url = $edit_link."?utype=edit&id=".$id;
                                            $copy_url = "broiler_copy_rearingcharge.php?utype=copy&id=".$id;
                                            $delete_url = $delete_link."?utype=delete&id=".$id;
                                            $print_url = $print_link."?utype=print&id=".$id;
                                            $authorize_url = $update_link."?utype=authorize&id=".$id;
                                            if($row['active'] == 1){ $update_url = $update_link."?utype=pause&id=".$id; }
                                            else{ $update_url = $update_link."?utype=activate&id=".$id; }
                                            
                                            $print_url1 = "print/Examples/broiler_gc_master_print1.php?utype=print&id=".$id;
                                    ?>
                                    <tr>
                                       
                                        <td data-sort="<?= strtotime($row['from_date']) ?>"><?= date('d.m.Y',strtotime($row['from_date'])) ?></td>
                                        <td data-sort="<?= strtotime($row['to_date']) ?>"><?= date('d.m.Y',strtotime($row['to_date'])) ?></td>
										<td><?php echo $branch_name[$row['branch_code']]; ?></td>
                                        
                                        <?php if($schema_flag == 1){ echo "<td>".$row['schema_name']."</td>"; } ?>
										<td><?php echo $row['chick_cost']; ?></td>
										<td><?php echo $row['feed_cost']; ?></td>
										<td><?php echo $row['medicine_cost']; ?></td>
										<td><?php echo $row['admin_cost']; ?></td>
										<td><?php echo $row['standard_cost']; ?></td>
										<td><?php echo $row['minimum_cost']; ?></td>
										<td><?php echo $row['standard_fcr']; ?></td>
										<td><?php echo $row['standard_mortality']; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>&ensp;&ensp;";
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($add_flag == 1){
                                                    echo "<a href='".$copy_url."'><i class='fa fa-clipboard' style='color:green;' title='Copy'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1){
                                                   // echo "<a href='".$delete_url."'><i class='fa fa-trash' style='color:red;' title='delete'></i></a>&ensp;&ensp;";
                                                   ?> <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'><i class='fa fa-trash' style='color:red;' title='delete'></i></a>&ensp;&ensp; <?php

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
                                            if($print_flag == 1){
                                                echo "<a href='".$print_url1."' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Print'></i></a>&ensp;&ensp;";
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

        <?php
            }
            else{
        ?>
        <script>
            var x = confirm("You don't have access to this file\folder \n Kindly contact your admin for more details\support");
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
            function checkdelete(a){

// var a1 = a.split("@");

            var b = "<?php echo  $delete_link.'?utype=delete&id='; ?>"+a;

            var c = confirm("are you sure you want to delete the Rearing Charge: "+a+" ?");

            if(c == true){

                window.location.href = b;

            }

            else{ }

            }
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