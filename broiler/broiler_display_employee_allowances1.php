<?php
//broiler_display_employee_allowances1.php
include "newConfig.php";
include "number_format_ind.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['employee_allowances1'] = $cid; } else{ $cid = $_SESSION['employee_allowances1']; }
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` = '$href' AND `active` = '1' ORDER BY `sortorder` ASC";
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
            if(in_array("broiler_designation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_designation LIKE poulso6_admin_broiler_broilermaster.broiler_designation;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_emp_allowance_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_emp_allowance_master LIKE poulso6_admin_broiler_broilermaster.broiler_emp_allowance_master;"; mysqli_query($conn,$sql1); }
            
            //Check Column Availability
            $sql='SHOW COLUMNS FROM `broiler_emp_allowance_master`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("travel_allowance", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_emp_allowance_master` ADD `travel_allowance` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `daily_allowance`"; mysqli_query($conn,$sql); }
            
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

            $edesig = $cid."-edesig"; $ebrch = $cid."-ebrch";
            if(isset($_POST['submit']) == true){
                $emp_desigs = $_POST['emp_desigs'];
                $_SESSION[$edesig] = $emp_desigs;
                $emp_brch = $_POST['emp_brch'];
                $_SESSION[$ebrch] = $emp_brch;
            }
            else {
                $emp_desigs = $emp_brch = "all";
                if(!empty($_SESSION[$edesig])){ $emp_desigs = $_SESSION[$edesig]; }
                if(!empty($_SESSION[$ebrch])){ $emp_brch = $_SESSION[$ebrch]; }
            }

            $sql = "SELECT * FROM `broiler_designation` WHERE `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $desig_code = $desig_name = array();
            while($row = mysqli_fetch_assoc($query)){ $desig_code[$row['code']] = $row['code']; $desig_name[$row['code']] = $row['description']; }

            $sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $branch_code = $branch_name = array();
            while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

            $sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <form action="<?php echo $href; ?>" method="post">
                                <div class="row">
                                    <div class="col-md-10" align="left">
                                        <div class="row">
                                            <div class="form-group" style="width:260px;">
                                                <label for="emp_desigs">Designation</label>
                                                <select name="emp_desigs" id="emp_desigs" class="form-control select2" style="width:250px;">
                                                    <option value="all" <?php if($emp_desigs == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <?php foreach($desig_code as $dcode){ ?><option value="<?php echo $dcode; ?>" <?php if($emp_desigs == $dcode){ echo "selected"; } ?>><?php echo $desig_name[$dcode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group" style="width:260px;">
                                                <label for="emp_brch">Branch</label>
                                                <select name="emp_brch" id="emp_brch" class="form-control select2" style="width:250px;">
                                                    <option value="all" <?php if($emp_brch == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <?php foreach($branch_code as $dcode){ ?><option value="<?php echo $dcode; ?>" <?php if($emp_brch == $dcode){ echo "selected"; } ?>><?php echo $branch_name[$dcode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                            <div class="ml-2 form-group">
                                                <br/><button type="submit" name="submit" id="submit" class="btn btn-sm bg-success">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2" align="right">
                                    <?php if($add_flag == 1){ ?>
                                        <button type="button" name="addpage" id="addpage" class="btn bg-purple" value="<?php echo $add_link; ?>" onClick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>From Date</th>
										<th>To Date</th>
										<th>Designation</th>
										<th>Branch</th>
										<th>Per KM Cost</th>
										<th>Daily Allowance</th>
										<th>T.A.</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if($emp_desigs == "all"){ $desig_filter = ""; } else{ $desig_filter = " AND `desig_code` IN ('$emp_desigs')"; }
                                        if($emp_brch == "all"){ $branch_filter = ""; } else{ $branch_filter = " AND `branch_code` IN ('$emp_brch')"; }

                                        $delete_url = $delete_link."?utype=delete&id=";
                                        $sql = "SELECT * FROM `broiler_emp_allowance_master` WHERE `dflag` = '0'".$desig_filter."".$branch_filter." ORDER BY `id` ASC";
                                        $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['id'];
                                            $edit_url = $edit_link."?utype=edit&id=".$id;
                                            $print_url = $print_link."?utype=print&id=".$id;
                                            $authorize_url = $update_link."?utype=authorize&id=".$id; 
                                            if($row['active'] == 1){
                                                $update_url = $update_link."?utype=pause&id=".$id;
                                            }
                                            else{
                                                $update_url = $update_link."?utype=activate&id=".$id;
                                            }
                                            $val = ""; $val = $row['id']."@".$desig_name[$row['desig_code']]."@".$branch_name[$row['branch_code']];
                                    ?>
                                    <tr>
										<td><?php echo date("d.m.Y",strtotime($row['fdate'])); ?></td>
										<td><?php echo date("d.m.Y",strtotime($row['tdate'])); ?></td>
										<td><?php echo $desig_name[$row['desig_code']]; ?></td>
										<td><?php echo $branch_name[$row['branch_code']]; ?></td>
										<td style="text-align:right;"><?php echo number_format_ind($row['per_km_rate']); ?></td>
										<td style="text-align:right;"><?php echo number_format_ind($row['daily_allowance']); ?></td>
										<td style="text-align:right;"><?php echo number_format_ind($row['travel_allowance']); ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>";
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1){
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
                                            /*if($print_flag == 1){
                                                echo "<a href='".$print_url."'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                            }*/
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
        <script>
			function checkdelete(x){
                var val1 = x.split("@");
                var id = val1[0];
                var dname = val1[1];
                var bname = val1[2];
                
                if(id != "" && dname != "" && bname != ""){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_check_employee_allowances1.php?id="+id;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var count = this.responseText;
                            if(parseInt(count) > 0){
                                alert("You can't delete the Allowance Details as Allowance Details is already in use!");
                            }
                            else{
                                var b = "<?php echo $delete_url; ?>"+id;
                                var c = confirm("are you sure you want to delete the Allowance Details with Designation: "+dname+" and Branch: "+bname+"?");
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