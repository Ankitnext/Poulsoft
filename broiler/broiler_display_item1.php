<?php
//broiler_display_item1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['item1'] = $cid; } else{ $cid = $_SESSION['item1']; }
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
    </head>
    <body class="m-0 hold-transition sidebar-mini">
        <?php
        if($acount == 1){
            /*Check for Table Availability*/
            $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
            $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
            if(in_array("item_subcategory", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_subcategory LIKE poulso6_admin_broiler_broilermaster.item_subcategory;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_farm", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_farm LIKE poulso6_admin_broiler_broilermaster.broiler_farm;"; mysqli_query($conn,$sql1); }
            if(in_array("inv_sectors", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.inv_sectors LIKE poulso6_admin_broiler_broilermaster.inv_sectors;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_ebill_item_units", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_ebill_item_units LIKE poulso6_admin_broiler_broilermaster.broiler_ebill_item_units;"; mysqli_query($conn,$sql1); }
            
            /*Check Column Availability*/
            $sql='SHOW COLUMNS FROM `item_details`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("sub_category", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_details` ADD `sub_category` VARCHAR(300) NULL DEFAULT NULL AFTER `category`"; mysqli_query($conn,$sql); }
            if(in_array("sector_access", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_details` ADD `sector_access` VARCHAR(1500) NULL DEFAULT NULL AFTER `cunits`"; mysqli_query($conn,$sql); }
            if(in_array("einv_units", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `item_details` ADD `einv_units` VARCHAR(300) NULL DEFAULT NULL AFTER `cunits`"; mysqli_query($conn,$sql); }
            
            $sql='SHOW COLUMNS FROM `extra_access`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("field_value", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `extra_access` ADD `field_value` VARCHAR(300) NULL DEFAULT NULL AFTER `field_function`"; mysqli_query($conn,$sql); }
            
            //Sub-Category Access Flag
            $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Item Master' AND `field_function` = 'Sub-Category Access Flag' AND (`user_access` = '$user_code' OR `user_access` = 'all')";
            $query = mysqli_query($conn,$sql); $sc_count = mysqli_num_rows($query); $scat_aflag = 0;
            if($sc_count > 0){ while($row = mysqli_fetch_assoc($query)){ $scat_aflag = $row['flag']; } }
            else{ $sql = "INSERT INTO `extra_access` (`field_name`,`field_function`,`field_value`,`user_access`,`flag`) VALUES ('Item Master','Sub-Category Access Flag',NULL,'all','0');"; mysqli_query($conn,$sql); }
            if($scat_aflag == ""){ $scat_aflag = 0; }
            
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

            $sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
            while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

            $icats = "all"; $ficats = $cid."-icats";
            if(isset($_POST['icats']) == true){ $icats = $_POST['icats']; $_SESSION[$ficats] = $icats; } else{ if(!empty($_SESSION[$ficats])){ $icats = $_SESSION[$ficats]; } }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-11">
                                    <form action="<?php echo $href; ?>" method="post">
                                        <div class="row justify-content-center align-items-center">
                                            <div class="form-group">
                                                <label for="icats">Category</label>
                                                <select name="icats" id="icats" class="form-control select2" style="width:200px;" onchange="this.form.submit();">
                                                    <option value="all" <?php if($icats == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <?php foreach($icat_code as $icode){ ?><option value="<?php echo $icode; ?>" <?php if($icats == $icode){ echo "selected"; } ?>><?php echo $icat_name[$icode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-1">
                                    <?php if($add_flag == 1){ ?>
                                    <button type="button" class="btn bg-purple" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <?php if((int)$scat_aflag == 1){ echo "<th>Sub-Category</th>"; } ?>
										<th>Code</th>
										<th>Description</th>
										<th>Units</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if((int)$scat_aflag == 1){
                                            $sql = "SELECT * FROM `item_subcategory` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                                            $query = mysqli_query($conn,$sql); $iscat_code = $iscat_name = array();
                                            while($row = mysqli_fetch_assoc($query)){ $iscat_code[$row['code']] = $row['code']; $iscat_name[$row['code']] = $row['description']; }
                                        }
                                        if($icats == "all"){ $icat_filter = ""; } else{ $icat_filter = " AND `category` IN ('$icats')"; }
                                        $delete_url = $delete_link."?utype=delete&id=";
                                        $sql = "SELECT * FROM `item_details` WHERE `dflag` = '0'".$icat_filter." ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['id'];
                                            $edit_url = $edit_link."?utype=edit&id=".$id;
                                            //$delete_url = $delete_link."?utype=delete&id=".$id;
                                            $print_url = $print_link."?utype=print&id=".$id;
                                            $authorize_url = $update_link."?utype=authorize&id=".$id;
                                            if($row['active'] == 1){
                                                $update_url = $update_link."?utype=pause&id=".$id;
                                            }
                                            else{
                                                $update_url = $update_link."?utype=activate&id=".$id;
                                            }
                                            $val = ""; $val = $row['id']."@".$row['description'];
                                    ?>
                                    <tr>
                                        <td><?php echo $icat_name[$row['category']]; ?></td>
                                        <?php if((int)$scat_aflag == 1){ echo "<td>".$iscat_name[$row['sub_category']]."</td>"; } ?>
										<td><?php echo $row['code']; ?></td>
										<td><?php echo $row['description']; ?></td>
										<td><?php echo $row['sunits']; ?></td>
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
                                                if($print_flag == 1){
                                                    echo "<a href='".$print_url."'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
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
        <script>
			function checkdelete(x){
                var val1 = x.split("@"); var a = val1[0]; var val = val1[1];
                if(a != ""){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_check_itemdetails.php?id="+a;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var count = this.responseText;
                            if(parseFloat(count) > 0){
                                alert("You can't delete the Item: "+val+", as Item is already in use!");
                            }
                            else{
                                var b = "<?php echo $delete_url; ?>"+a;
                                var c = confirm("are you sure you want to delete the Item: "+val+"?");
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