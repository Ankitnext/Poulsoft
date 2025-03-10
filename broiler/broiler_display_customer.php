<?php
//broiler_display_customer.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['customer'] = $cid; } else{ $cid = $_SESSION['customer']; }
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
            $sql='SHOW COLUMNS FROM `main_contactdetails`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("processing_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `processing_flag` INT(100) NOT NULL DEFAULT '0' COMMENT 'Plant Access Flag' AFTER `dflag`"; mysqli_query($conn,$sql); }
            if(in_array("cus_ccode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_contactdetails` ADD `cus_ccode` VARCHAR(100) NOT NULL DEFAULT NULL COMMENT 'Customer Code' AFTER `code`"; mysqli_query($conn,$sql); }
            
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
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                           <div class="float-left"><h3 class="card-title">Customer</h3></div>
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
                                        <th>Code</th>
										<th>Name</th>
										<th>Phone/Mobile</th>
										<th>Type</th>
										<th>Group</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Customer Master' AND `field_function` LIKE 'Upload documents' AND `user_access` LIKE 'all' AND `flag` = '1'";
                                        $query = mysqli_query($conn,$sql); $updoc_flag = mysqli_num_rows($query);

                                        $sql = "SELECT * FROM `main_groups` ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $group_name[$row['code']] = $row['description']; }
                                       
                                        $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['id'];
                                            $edit_url = $edit_link."?utype=edit&id=".$id;
                                            $delete_url = $delete_link."?utype=delete&id=".$id;
                                            $print_url = $print_link."?utype=print&id=".$id;
                                            $authorize_url = $update_link."?utype=authorize&id=".$id;
                                            if($row['active'] == 1){
                                                $update_url = $update_link."?utype=pause&id=".$id;
                                            }
                                            else{
                                                $update_url = $update_link."?utype=activate&id=".$id;
                                            }

                                            $id1 = $row['id']."@".$row['name'];
                                            
                                            //Check for document Asscess
                                            $upd_links = "";
                                            if((int)$updoc_flag == 1){
                                                if($row['vdoc_path1'] != ""){ $upd_links .= '<a href="'.$row["vdoc_path1"].'" title="Document-1" target="_BLANK"><i class="fa-solid fa-file" style="font-size:15px;color:#00d7a3;"></i></a>&ensp;'; }
                                                if($row['vdoc_path2'] != ""){ $upd_links .= '<a href="'.$row["vdoc_path2"].'" title="Document-2" target="_BLANK"><i class="fa-solid fa-file" style="font-size:15px;color:#00d7a3;"></i></a>&ensp;'; }
                                                if($row['vdoc_path3'] != ""){ $upd_links .= '<a href="'.$row["vdoc_path3"].'" title="Document-3" target="_BLANK"><i class="fa-solid fa-file" style="font-size:15px;color:#00d7a3;"></i></a>&ensp;'; }
                                                if($row['vdoc_path4'] != ""){ $upd_links .= '<a href="'.$row["vdoc_path4"].'" title="Document-4" target="_BLANK"><i class="fa-solid fa-file" style="font-size:15px;color:#00d7a3;"></i></a>&ensp;'; }
                                                if($row['vdoc_path5'] != ""){ $upd_links .= '<a href="'.$row["vdoc_path5"].'" title="Document-5" target="_BLANK"><i class="fa-solid fa-file" style="font-size:15px;color:#00d7a3;"></i></a>&ensp;'; }
                                            }
                                    ?>
                                    <tr>
                                        <td><?php echo $row['code']; ?></td>
										<td><?php echo $row['name']; ?></td>
										<td><?php echo $row['mobile1']; ?></td>
										<td><?php echo $row['contacttype']; ?></td>
										<td><?php echo $group_name[$row['groupcode']]; ?></td>
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
                                                    //cho "<a href='".$delete_url."'><i class='fa fa-trash' style='color:red;' title='delete'></i></a>&ensp;&ensp;";
                                                    ?> <a href='javascript:void(0)' id='<?php echo $id1; ?>' value='<?php echo $id1; ?>' onclick='checkdelete(this.id)'><i class='fa fa-trash' style='color:red;' title='delete'></i></a> <?php


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
                                                if((int)$updoc_flag == 1){ echo "&ensp;".$upd_links; }
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

var a1 = a.split("@");

var b = "<?php echo  $delete_link.'?utype=delete&id='; ?>"+a1[0];

var c = confirm("are you sure you want to delete the Customer: "+a1[1]+" ?");

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