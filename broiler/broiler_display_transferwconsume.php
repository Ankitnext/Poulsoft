<?php
//broiler_display_transferwconsume.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['transferwconsume'] = $cid; } else{ $cid = $_SESSION['transferwconsume']; }
$href = explode("/", $_SERVER['REQUEST_URI']); $url = $href[1]; $file_name = explode("?", $href[1]);
$sql = "SELECT * FROM `master_form_tableaccess` WHERE `href` = '$file_name[0]' AND `active` = '1'"; $query = mysqli_query($sconn,$sql);
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
        $sector_code[$row['code']] = $row['code'];
        $sector_name[$row['code']] = $row['description'];

        if($ab == 0) {
            $assigned_farms = "'".$row['code']."'";
        }else{
            $assigned_farms .= ",'".$row['code']."'";
        }

        $ab++;
       
    }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){ 
        $sector_code[$row['code']] = $row['code'];
        $sector_name[$row['code']] = $row['description'];

        if($ab == 0) {
            $assigned_farms = "'".$row['code']."'";
        }else{
            $assigned_farms .= ",'".$row['code']."'";
        }
        
        $ab++;
    }

    if($assigned_farms != '' ){
        $cond_assigned = " AND ( fromwarehouse IN ($assigned_farms) OR towarehouse IN ($assigned_farms) )";
    }else{
        $cond_assigned = "";
    }


    $aid = 0;
    $flink = explode("','",$dlink); $acount = 0; foreach($flink as $flinks){ if($flinks == $cid){ $aid = 1; } }
    if($user_type == "S"){ $acount = 1; }
    else if($aid == 1){ $acount = 1; }
    else{ $acount = 0; }

    //check and fetch date range
    global $drng_cday; $drng_cday = 1; global $drng_furl; $drng_furl = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    include "poulsoft_fetch_daterange_master.php";
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

            $sql1 = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'MedVac Transfer' AND `field_function` LIKE 'Stock return with consumed deletetion' AND `user_access` LIKE '%$user_code%'";
            $query1 = mysqli_query($conn,$sql1); $count1 = mysqli_num_rows($query1);
            $sql2 = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'MedVac Transfer' AND `field_function` LIKE 'Stock return with consumed deletetion' AND `user_access` LIKE 'all'";
            $query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2);
            if($count1 > 0){ while($row1 = mysqli_fetch_assoc($query1)){ $rtn_flag = $row1['flag']; } }
            else if($count2 > 0){ while($row2 = mysqli_fetch_assoc($query2)){ $rtn_flag = $row2['flag']; } } else{ $rtn_flag = 0; }
            if($rtn_flag == "" || $rtn_flag == 0){ $rtn_flag = 0; }

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

                $from_warehouse = $_POST['from_warehouse'];
                $to_warehouse = $_POST['to_warehouse'];
                if($from_warehouse != 'all'){
                    $from_warehouse_condition = " AND fromwarehouse = '$from_warehouse' ";
                }
                if($to_warehouse != 'all'){
                    $to_warehouse_condition = " AND towarehouse = '$to_warehouse' ";
                }
            }
            else {
                $fdate = $tdate = date("Y-m-d");
                if(!empty($_SESSION[$fsdate])){ $fdate = date("Y-m-d",strtotime($_SESSION[$fsdate])); }
                if(!empty($_SESSION[$tsdate])){ $tdate = date("Y-m-d",strtotime($_SESSION[$tsdate])); }

                $from_warehouse_condition = "";
                $to_warehouse_condition = "";
            }

            $sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                        <form action="<?php echo $url; ?>" method="post">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row" align="left">
                                            <div class="form-group" style="width:100px;">
                                                <label for="fdate">From Date: </label>
                                                <input type="text" name="fdate" id="fdate" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:90px;">
                                            </div>
                                            <div class="form-group" style="width:100px;">
                                                <label for="tdate">To Date: </label>
                                                <input type="text" name="tdate" id="tdate" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="width:90px;">
                                            </div>
                                            <div class="form-group" style="width:250px;">
                                                <label for="tdate">From Warehouse: </label>
                                                <select name="from_warehouse" id="from_warehouse" class="form-control select2" style="width:240px;">
                                                    <option value="all" <?php if($from_warehouse == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <?php foreach($sector_code as $fcode){ ?><option value="<?php echo $fcode; ?>" <?php if($from_warehouse == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group" style="width:250px;">
                                                <label for="tdate">To Warehouse: </label>
                                                <select name="to_warehouse" id="to_warehouse" class="form-control select2" style="width:240px;">
                                                    <option value="all" <?php if($to_warehouse == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <?php foreach($sector_code as $fcode){ ?><option value="<?php echo $fcode; ?>" <?php if($to_warehouse == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group" style="width:100px;">
                                                <br/>
                                                <button type="submit" name="submit" id="submit" class="btn btn-success btn-sm">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4" align="right">
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
                                        <th>Dc No.</th>
                                        <th>From Location</th>
                                        <th>Description</th>
										<th>Quantity</th>
										<th>To Location</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }
                                                
                                        $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

                                        $sql = "SELECT * FROM `item_details`"; $query = mysqli_query($conn,$sql); $item_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }
                                        
                                        $delete_url = $delete_link."?utype=delete&trnum=";
                                        $sql = "SELECT * FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate' $from_warehouse_condition $to_warehouse_condition $cond_assigned AND `link_trnum` IS NOT NULL AND `dflag` = '0' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['trnum'];
                                            $id2 = $row['date']."@".$row['trnum']."@".$row['link_trnum']."@".$row['code']."@".$row['towarehouse']."@".$row['to_batch'];
                                            $edit_url = $edit_link."?utype=edit&trnum=".$id;
                                            $rtn_url = "broiler_return_transferwconsume.php?utype=return&trnum=".$id;
                                            $delete_key = $id2;
                                            //$print_url = $print_link."?utype=print&trnum=".$id;
                                            $print_url = $print_link."?id=".$id;
                                            $print_url2 = "/print/Examples/broiler_stock_transfer_datewise.php?id=".$row['date']."@".$item_category[$row['code']]."@".$row['towarehouse'];
                                            $print_url3 = "/print/Examples/broiler_stock_transfer_datewise3.php?id=".$row['date']."@".$item_category[$row['code']]."@".$row['towarehouse'];
                                            $authorize_url = $update_link."?utype=authorize&trnum=".$id;
                                            if($row['active'] == 1){
                                                $update_url = $update_link."?utype=pause&trnum=".$id;
                                            }
                                            else{
                                                $update_url = $update_link."?utype=activate&trnum=".$id;
                                            }
                                    ?>
                                    <tr>
										<td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])) ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $row['dcno']; ?></td>
										<td><?php echo $sector_name[$row['fromwarehouse']]; ?></td>
										<td><?php echo $item_name[$row['code']]; ?></td>
										<td><?php echo $row['quantity']; ?></td>
										<td><?php echo $sector_name[$row['towarehouse']]; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1 || $row['link_trnum'] == ""){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>&ensp;&ensp;";
                                            }
                                            else if($row['gc_flag'] == 1){
                                                echo "<i class='fa fa-lock' style='color:gray;' title='GC processed'></i></a>&ensp;&ensp;";
                                            }
                                            else if(strtotime($row['date']) < strtotime($rng_sdate) || strtotime($row['date']) > strtotime($rng_edate)){
                                                echo "<i class='fa fa-check' style='color:green;' title='Date Entry Range Closed'></i></a>&ensp;&ensp;";
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1){
                                                    ?>
                                                    <a href='javascript:void(0)' id='<?php echo $delete_key; ?>' value='<?php echo $delete_key; ?>' onclick='checkdelete(this.id)'>
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
                                                if($edit_flag == 1 && $rtn_flag == 1){
                                                    echo "<a href='".$rtn_url."'><i class='fa-solid fa-right-left' style='color:red;' title='Return'></i></a>&ensp;&ensp;";
                                                }
                                                if($autflag == 1){
                                                    echo "<a href='".$authorize_url."'><i class='fa fa-lock-open' style='color:orange;' title='Authorize'></i></a>&ensp;&ensp;";
                                                }
                                            }
                                            if($print_flag == 1){
                                                echo "<a href='".$print_url."' target='_BLANK'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                                echo "<a href='".$print_url2."' target='_BLANK'><i class='fa fa-print' style='color:green;' title='Print'></i></a>&ensp;&ensp;";
                                                echo "<a href='".$print_url3."' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Print'></i></a>&ensp;&ensp;";
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
			function checkdelete(a){
                var s1 = a.split("@");
                var trnum = s1[1];
				var b = "<?php echo $delete_url; ?>"+a;
				var c = confirm("are you sure you want to delete the transaction "+trnum+" ?");
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