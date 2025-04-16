<?php
//broiler_display_pc_purchaseorder4.php
include "newConfig.php";
include "broiler_create_print_links.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['pc_purchaseorder4'] = $cid; } else{ $cid = $_SESSION['pc_purchaseorder4']; }
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `master_form_tableaccess` WHERE `href` = '$href' AND `active` = '1'"; $query = mysqli_query($sconn,$sql);
while($row = mysqli_fetch_assoc($query)){ $table_name = $row['table_name']; } $table_session = $cid."tbl_access"; $_SESSION[$table_session] = $table_name;
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
            $database_name = $_SESSION['dbase'];
            $table_head = "Tables_in_".$database_name;
            $sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'broiler_pc_purchaseorder';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
            if($tcount > 0){ }
            else{
                $sql1 = "CREATE TABLE $database_name.broiler_pc_purchaseorder LIKE poulso6_admin_broiler_broilermaster.broiler_pc_purchaseorder;";
                mysqli_query($conn,$sql1);
            }
            $sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'broker_details';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
            if($tcount > 0){ }
            else{
                $sql1 = "CREATE TABLE $database_name.broker_details LIKE poulso6_admin_broiler_broilermaster.broker_details;";
                mysqli_query($conn,$sql1);
            }
            $sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'broiler_pc_purchaseindent';"; $query1 = mysqli_query($conn,$sql1); $tcount = mysqli_num_rows($query1);
            if($tcount > 0){ }
            else{
                $sql1 = "CREATE TABLE $database_name.broiler_pc_purchaseindent LIKE poulso6_admin_broiler_broilermaster.broiler_pc_purchaseindent;";
                mysqli_query($conn,$sql1);
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

            $sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Pause/Active','Authorize') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Pause/Active','Authorize') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Pause/Active"){ $paflag = $row['flag']; } else if($row['field_name'] == "Authorize"){ $autflag = $row['flag']; } else{ } }
            if($paflag == "" || $paflag == NULL || $paflag == 0){ $paflag = 0; }
            if($autflag == "" || $autflag == NULL || $autflag == 0){ $autflag = 0; }

            $fsdate = $cid."-fdate"; $tsdate = $cid."-tdate";
            if(isset($_POST['submit']) == true){
                $fdate = date("Y-m-d",strtotime($_POST['fdate']));
                $tdate = date("Y-m-d",strtotime($_POST['tdate']));
                $_SESSION[$fsdate] = $fdate;
                $_SESSION[$tsdate] = $tdate;
            }
            else {
                $fdate = $tdate = date("Y-m-d");
                if(!empty($_SESSION[$fsdate])){ $fdate = date("Y-m-d",strtotime($_SESSION[$fsdate])); }
                if(!empty($_SESSION[$tsdate])){ $tdate = date("Y-m-d",strtotime($_SESSION[$tsdate])); }
            }


             //Check Print-View Linksmodule
             $module = "Purchase Order"; $field_name = "Purchase Order-1"; $file_name = "broiler_display_pc_purchaseorder4.php"; $print_path = "/print/Examples/broiler_print_pc_purchaseorder.php";
             $sort_order = "1"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "black"; $target = "_BLANK";
             $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
             $print_dt = ""; $print_dt = json_encode($msg1);
             broiler_create_print_links($print_dt);
             $module = "Purchase Order"; $field_name = "Purchase Order-2"; $file_name = "broiler_display_pc_purchaseorder4.php"; $print_path = "/print/Examples/broiler_print_pc_purchaseorder3.php";
             $sort_order = "2"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "blue"; $target = "_BLANK";
             $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
             $print_dt = ""; $print_dt = json_encode($msg1);
             broiler_create_print_links($print_dt);
             $module = "Purchase Order"; $field_name = "Purchase Order-3"; $file_name = "broiler_display_pc_purchaseorder4.php"; $print_path = "/print/Examples/broiler_print_pc_purchaseorder4.php";
             $sort_order = "3"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "brown"; $target = "_BLANK";
             $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
             $print_dt = ""; $print_dt = json_encode($msg1);
             broiler_create_print_links($print_dt);
             $module = "Purchase Order"; $field_name = "Purchase Order-5"; $file_name = "broiler_display_pc_purchaseorder4.php"; $print_path = "/print/Examples/broiler_print_pc_purchaseorder4_goels.php";
             $sort_order = "4"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "orange"; $target = "_BLANK";
             $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
             $print_dt = ""; $print_dt = json_encode($msg1);
             broiler_create_print_links($print_dt);
             $module = "Purchase Order"; $field_name = "Purchase Order-4"; $file_name = "broiler_display_pc_purchaseorder4.php"; $print_path = "/print/Examples/broiler_print_pc_purchaseorder3.php";
             $sort_order = "5"; $icon_type = "icon"; $icon_path = "fa-brands fa-whatsapp"; $icon_color = "green"; $target = "_BLANK";
             $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
             $print_dt = ""; $print_dt = json_encode($msg1);
             broiler_create_print_links($print_dt);
             $module = "Purchase Order"; $field_name = "Purchase Order-6"; $file_name = "broiler_display_pc_purchaseorder4.php"; $print_path = "/print/Examples/broiler_print_pc_purchaseorder6.php";
             $sort_order = "6"; $icon_type = "icon"; $icon_path = "fa fa-print"; $icon_color = "red"; $target = "_BLANK";
             $msg1 = array(); $msg1 = array("module"=>$module, "field_name"=>$field_name, "file_name"=>$file_name, "print_path"=>$print_path, "sort_order"=>$sort_order, "icon_type"=>$icon_type, "icon_path"=>$icon_path, "icon_color"=>$icon_color, "target"=>$target);
             $print_dt = ""; $print_dt = json_encode($msg1);
             broiler_create_print_links($print_dt);
 
             //Fetch Print-View from Print Master
             $i = $pc = 0; $field_name = $print_path = $icon_type = $icon_path = $icon_color = $target = array();
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
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                        <form action="<?php echo $url; ?>" method="post">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group"><label for="fdate">From Date: </label><input type="text" class="form-control datepicker" name="fdate" id="fdate" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>"></div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group"><label for="tdate">To Date: </label><input type="text" class="form-control datepicker" name="tdate" id="tdate" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>"></div>
                                    </div>
                                    <div class="col-md-2"><br/>
                                        <button type="submit" name="submit" id="submit" class="btn btn-success btn-sm">Submit</button>
                                    </div>
                                    <div class="col-md-6" align="right">
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
                                        <th>PO</th>
                                        <th>PI</th>
                                        <th>Supplier</th>
										<th>Item</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Amount</th>
										<th>Sector</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $item_name = $vendor_name = $sector_name = array();
                                        $sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
                                        
                                        $sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $vendor_name[$row['code']] = $row['name']; }

                                        $sql = "SELECT * FROM `inv_sectors`"; $query = mysqli_query($conn,$sql); 
                                        while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

                                        $sql = "SELECT * FROM `broiler_farm`"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

                                        $sql = "SELECT * FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `trlink` LIKE 'broiler_display_pc_purchaseorder4.php' AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['trnum'];
                                            $edit_url = $edit_link."?utype=edit&trnum=".$id;
                                            $delete_url = $delete_link."?utype=delete&trnum=".$id;
                                            $print_url = $print_link."?utype=print&trnum=".$id;
                                            $print_url2 = "print/Examples/purchase_order1.php?utype=print&trnum=".$id;
                                            $print_url3 = "print/Examples/broiler_print_pc_purchaseorder.php?utype=print&trnum=".$id;
                                            $print_url4 = "print/Examples/broiler_print_pc_purchaseorder3.php?utype=print&trnum=".$id;
                                            $print_url6 = "print/Examples/broiler_print_pc_purchaseorder4.php?utype=print&trnum=".$id;
                                            $print_url5 = "print/Examples/broiler_print_pc_purchaseorder3.php?utype=print&trnum=".$id."&view_type=send_pdf";
                                            $delivernote_url = "print/Examples/purchase_delivery_note1.php?trnum=".$row['trnum']."&sector=".$row['warehouse'];

                                            $print_dt = ""; $print_dt = "?utype=print&trnum=".$id;
                                    ?>
                                    <tr>
										<td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])) ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $row['link_trnum']; ?></td>
										<td><?php echo $vendor_name[$row['vcode']]; ?></td>
										<td><?php echo $item_name[$row['item_code']]; ?></td>
										<td><?php echo $row['rcvd_qty']; ?></td>
										<td><?php echo $row['rate']; ?></td>
										<td><?php echo round(($row['amount']),2); ?></td>
										<td><?php echo $sector_name[$row['warehouse']]; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['gr_flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Completed'></i></a>&ensp;&ensp;";
                                            } 
                                            else if ($row['gr_flag'] == 0){
                                                echo "<i class='fa-solid fa-circle-xmark' style='color:red;' title='Pending'></i></a>&ensp;&ensp;";
                                            }
                                            else{ }
                                            if($row['gr_flag'] == 1 || $row['flag'] == 1){
                                                echo "<i class='fa fa-lock' style='color:gray;' title='Authorized'></i></a>&ensp;&ensp;";
                                                if($print_flag == 1){
                                                    echo "<a href='".$print_url2."' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Print'></i></a>&ensp;&ensp;";
                                                }
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1){
                                                    if($row['qt_flag'] == 0){
                                                        echo "<a href='".$delete_url."'><i class='fa fa-trash' style='color:red;' title='delete'></i></a>&ensp;&ensp;";
                                                    }else{
                                                        echo "<i class='fa fa-lock' style='color:grayred;' title='can't Delete Test has done'></i>&ensp;&ensp;";
                                                    }
                                                    
                                                }
                                                if($paflag == 1){
                                                    if($row['active'] == 1){
                                                        echo "<a href='".$update_url."'><i class='fa fa-pause' style='color:blue;' title='Activate'></i></a>&ensp;&ensp;";
                                                    }
                                                    else{
                                                        echo "<a href='".$update_url."'><i class='fa fa-play' style='color:blue;' title='Pause'></i></a>&ensp;&ensp;";
                                                    }
                                                }
                                                if($autflag == 1){
                                                    echo "<a href='".$authorize_url."'><i class='fa fa-lock-open' style='color:orange;' title='Authorize'></i></a>&ensp;&ensp;";
                                                }
                                            }
                                            if($print_flag == 1){
                                               // echo "<a href='".$print_url3."' target='_BLANK'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                               // echo "<a href='".$print_url4."' target='_BLANK'><i class='fa fa-print' style='color:blue;' title='Print'></i></a>&ensp;&ensp;";
                                               // echo "<a href='".$print_url6."' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Print'></i></a>&ensp;&ensp;";
                                               
                                               $printv_list = "";
                                               for($p = 0;$p <= $pc;$p++){
                                                   if($icon_path[$p] == "fa-brands fa-whatsapp"){ $ppath = ""; $ppath = $print_path[$p]."".$print_dt."&view_type=send_pdf"; }
                                                   else{ $ppath = ""; $ppath = $print_path[$p]."".$print_dt; }
                                                   
                                                   if($icon_type[$p] == "image"){
                                                       $printv_list .= '<a href="'.$ppath.'" target="'.$target[$p].'"><img src="'.$icon_path[$p].'" style="width:15px;height:15px;" title="'.$field_name[$p].'" /></a>&ensp;&ensp;';
                                                   }
                                                   else if($icon_type[$p] == "icon"){
                                                       $printv_list .= '<a href="'.$ppath.'" target="'.$target[$p].'"><i class="'.$icon_path[$p].'" style="color:'.$icon_color[$p].';" title="'.$field_name[$p].'"></i></a>&ensp;&ensp;';
                                                   }
                                               }
                                               echo $printv_list;
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