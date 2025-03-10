<?php
//broiler_display_purchase2_chicks.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['purchase2_chicks'] = $cid; } else{ $cid = $_SESSION['purchase2_chicks']; }
$href = explode("/", $_SERVER['REQUEST_URI']); $url = $href[1]; $file_name = explode("?", $href[1]);
$sql = "SELECT * FROM `master_form_tableaccess` WHERE `href` = '$file_name[0]' AND `active` = '1'"; $query = mysqli_query($sconn,$sql);
while($row = mysqli_fetch_assoc($query)){ $table_name = $row['table_name']; } $table_session = $cid."tbl_access"; $_SESSION[$table_session] = $table_name;

$sql='SHOW COLUMNS FROM `master_generator`'; $query= mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("chick_purchases", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `master_generator` ADD `chick_purchases` INT(100) NOT NULL DEFAULT '0' COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }

$sql='SHOW COLUMNS FROM `broiler_purchases`'; $query= mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("mort", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `mort` decimal(30,2) NULL DEFAULT '0' COMMENT '' AFTER `dflag`"; mysqli_query($conn,$sql); }
if(in_array("shortage", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `shortage` decimal(30,2) NULL DEFAULT '0' COMMENT '' AFTER `mort`"; mysqli_query($conn,$sql); }
if(in_array("weeks", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `weeks` decimal(30,2) NULL DEFAULT '0' COMMENT '' AFTER `shortage`"; mysqli_query($conn,$sql); }
if(in_array("excess_qty", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `excess_qty` decimal(30,2) NULL DEFAULT '0' COMMENT '' AFTER `weeks`"; mysqli_query($conn,$sql); }
if(in_array("chicks_pur", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `chicks_pur` INT(11) NULL DEFAULT '0' COMMENT 'Chick Purchase Flag'"; mysqli_query($conn,$sql); }

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
        $purchase_multiple_edit_flag = $row['purchase_multiple_edit_flag'];
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; } else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; } else{ $user_type = "N"; }
    
    
            
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

    $ab = 0;

    $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ 

        if($ab == 0) {
            $assigned_farms = "'".$row['code']."'";
        }else{
            $assigned_farms .= ",'".$row['code']."'";
        }

        $ab++;
       
    }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ 

        if($ab == 0) {
            $assigned_farms = "'".$row['code']."'";
        }else{
            $assigned_farms .= ",'".$row['code']."'";
        }
        
        $ab++;
    }

    if($assigned_farms != '' ){
        $cond_assigned = " AND warehouse IN ($assigned_farms)";
    }else{
        $cond_assigned = "";
    }

    if($purchase_multiple_edit_flag == ""){ $purchase_multiple_edit_flag = 0; } $purchase_multiple_edit_flag = 1;
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
            $sql='SHOW COLUMNS FROM `broiler_purchases`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("file_url1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `file_url1` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'File Upload-1' AFTER `dflag`"; mysqli_query($conn,$sql); }
            if(in_array("file_url2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `file_url2` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'File Upload-2' AFTER `file_url1`"; mysqli_query($conn,$sql); }
            if(in_array("file_url3", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `file_url3` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'File Upload-3' AFTER `file_url2`"; mysqli_query($conn,$sql); }
            if(in_array("file_remarks", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `file_remarks` VARCHAR(1200) NULL DEFAULT NULL COMMENT 'File Remarks' AFTER `file_url3`"; mysqli_query($conn,$sql); }
            if(in_array("ven_hat_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_purchases` ADD `ven_hat_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Vendor Hatchery Code' AFTER `vcode`"; mysqli_query($conn,$sql); }

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
                                    <div class="col-md-6">
                                    <div class="row float-right">
                                        <?php if($purchase_multiple_edit_flag == 1){ ?>
                                        <div class="form-group">
                                            <button type="button" class="btn bg-success" id="editpage" value="broiler_edit_purchase2_chicksm.php" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> Edit-Multiple</button>&ensp;
                                        </div>
                                        <?php } ?>
                                        <?php if($add_flag == 1){ ?>
                                        <div class="form-group" style="width:auto;">
                                            <button type="button" class="btn bg-purple" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>DC.No.</th>
                                        <th>Supplier</th>
										<th>Item</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Amount</th>
										<th>Farm/Warehouse</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $item_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
                                        $sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql); $vendor_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $vendor_name[$row['code']] = $row['name']; }
                                        $sql = "SELECT * FROM `inv_sectors`"; $query = mysqli_query($conn,$sql); $sector_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }
                                        $sql = "SELECT * FROM `broiler_farm`"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }
                                        
                                        $delete_url = $delete_link."?utype=delete&trnum=";
                                        $sql = "SELECT * FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `dflag` = '0' AND chicks_pur = '1' $cond_assigned  ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['trnum'];
                                            $edit_url = $edit_link."?utype=edit&trnum=".$id;
                                            //$delete_url = $delete_link."?utype=delete&trnum=".$id;
                                            $print_url = $print_link."?utype=print&trnum=".$id;
                                            $delivernote_url = "print/Examples/purchase_delivery_note1.php?trnum=".$row['trnum']."&sector=".$row['warehouse'];
                                            $id2 = $row['trnum'];

                                            $ref_docs = "";
                                            if($row['file_url1'] != ""){
                                                $ref_docs .= '<a href="'.$row["file_url1"].'" title="URL-1" download><i class="fa-solid fa-angles-down" style="font-size:15px;"></i></a>&ensp;';
                                            }
                                            if($row['file_url2'] != ""){
                                                $ref_docs .= '<a href="'.$row["file_url2"].'" title="URL-2" download><i class="fa-solid fa-angles-down" style="font-size:15px;"></i></a>&ensp;';
                                            }
                                            if($row['file_url3'] != ""){
                                                $ref_docs .= '<a href="'.$row["file_url3"].'" title="URL-3" download><i class="fa-solid fa-angles-down" style="font-size:15px;"></i></a>&ensp;';
                                            }
                                    ?>
                                    <tr>
										<td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])) ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $row['billno']; ?></td>
										<td><?php echo $vendor_name[$row['vcode']]; ?></td>
										<td><?php echo $item_name[$row['icode']]; ?></td>
										<td><?php echo $row['rcd_qty']; ?></td>
										<td><?php echo $row['rate']; ?></td>
										<td><?php echo $row['item_tamt']; ?></td>
										<td><?php echo $sector_name[$row['warehouse']]; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>";
                                            }
                                            else if($row['gc_flag'] == 1){
                                                echo "<i class='fa fa-lock' style='color:gray;' title='GC processed'></i></a>";
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1){
                                                ?>
                                                    <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'>
                                                    <i class='fa fa-trash' style='color:red;' title='delete'></i>
                                                    </a>&ensp;&ensp;
                                                <?php
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
                                                if($print_flag == 1){
                                                    echo "<a href='".$print_url."'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                                    echo "<a href='".$delivernote_url."' target='_BLANK'><i class='fa fa-print' style='color:green;' title='Purchase Delivery Note'></i></a>&ensp;&ensp;";
                                                }
                                            }
                                            echo '<a href="javascript:void(0)" data-toggle="modal" data-target="#modal-12" id="'.$id2.'" onclick="fetch_ticket_comments(this.id);"><i class="fa-regular fa-paste" style="color:green;"></i></a>&ensp;&ensp;';
                                            echo $ref_docs;
                                        ?>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body">
                            <div class="modal fade" id="modal-12" role="dialog" aria-labelledby="modal-12">
                                <div class="modal-dialog modal-dialog-centered modal-min" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body text-left">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <form action="broiler_upload_purchase2_chicks.php" method="post" onsubmit="return add_newcomment()" enctype="multipart/form-data">
                                                        <div class="card card-success card-outline direct-chat direct-chat-success">
                                                            <div class="card-header">
                                                                <h3 class="card-title"><input type="text" name="file_trnum" id="file_trnum" class="form-control" readonly /></h3>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="direct-chat-messages" id="comment_body">
                                                                    <div class="form-group">
                                                                        <label for="file_path1">File Upload-1</label>
                                                                        <input type="file" class="file-input" name="file_path1" id="file_path1" style="width:10px;visibility:hidden;" onchange="fetch_uploaded_filename(this.id);">
                                                                        <input type="text" class="form-control" name="file_name1" id="file_name1" style="width:250px;visibility:visible;" readonly />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="file_path2">File Upload-2</label>
                                                                        <input type="file" class="file-input" name="file_path2" id="file_path2" style="width:10px;visibility:hidden;" onchange="fetch_uploaded_filename(this.id);">
                                                                        <input type="text" class="form-control" name="file_name2" id="file_name2" style="width:250px;visibility:visible;" readonly />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="file_path3">File Upload-3</label>
                                                                        <input type="file" class="file-input" name="file_path3" id="file_path3" style="width:10px;visibility:hidden;" onchange="fetch_uploaded_filename(this.id);">
                                                                        <input type="text" class="form-control" name="file_name3" id="file_name3" style="width:250px;visibility:visible;" readonly />
                                                                    <label for="file_path1">Remarks</label>
                                                                    <textarea name="file_comment" id="file_comment" placeholder="Type Message ..." class="form-control" style="height:38px;"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="input-group">
                                                                    
                                                                    <span class="input-group-append">
                                                                        <button type="submit" class="btn btn-success">Send</button>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
            function fetch_ticket_comments(a){
                document.getElementById("file_trnum").value = "";
                document.getElementById("file_comment").value = "";
                //document.getElementById("comment_body").innerHTML = "";
                var b = a.split("@");
                document.getElementById("file_trnum").value = b[0];

                var Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });

                var trnum = b[0];
                if(trnum == ""){
                    Toast.fire({ icon: 'error', title: 'Select appropriate trnum.' });
                }
                else{
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_purfileupload.php?trnum="+trnum;
                        //window.open(url);
                        var asynchronous = true;
                        inv_items.open(method, url, asynchronous);
                        inv_items.send();
                        inv_items.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var item_list = this.responseText;
                                if(item_list != ""){
                                    var file_dt = item_list.split("@");
                                    if(file_dt[0] != ""){ document.getElementById("file_name1").value = file_dt[0]; }
                                    if(file_dt[1] != ""){ document.getElementById("file_name2").value = file_dt[1]; }
                                    if(file_dt[2] != ""){ document.getElementById("file_name3").value = file_dt[2]; }
                                    if(file_dt[3] != ""){ document.getElementById("file_comment").value = file_dt[3]; }
                                }
                                else{
                                    Toast.fire({ icon: 'success', title: 'No Comments!.' });
                                }
                            }
                        }
                }
            }
            function fetch_uploaded_filename(a){
                var fname = document.getElementById(a).files[0].name;
                if(a == "file_path1"){ document.getElementById("file_name1").value = fname; }
                if(a == "file_path2"){ document.getElementById("file_name2").value = fname; }
                if(a == "file_path3"){ document.getElementById("file_name3").value = fname; }
            }  
        </script>
        <script>
			function checkdelete(a){
				var b = "<?php echo $delete_url; ?>"+a;
				var c = confirm("are you sure you want to delete the transaction: "+a+"?");
				if(c == true){
					window.location.href = b;
				}
				else{ }
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