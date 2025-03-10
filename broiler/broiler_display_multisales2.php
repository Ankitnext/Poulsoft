<?php
//broiler_display_multisales2.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['multisales2'] = $cid; } else{ $cid = $_SESSION['multisales2']; }
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
        $sale_multiple_edit_flag = $row['sale_multiple_edit_flag'];
        $sale_multiple_delete_flag = $row['sale_multiple_delete_flag'];
        $broiler_display_multisales2 = $row['broiler_display_multisales2'];
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
        $cond_assigned = " AND  warehouse IN ($assigned_farms) ";
    }else{
        $cond_assigned = "";
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
    <style>
        body{
            //font-size:11px;
        }
    </style>
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

            $sale_print1 = 0;
            $sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'broiler_display_multisales2.php' AND `field_function` = 'Sale Invoice Format-1' AND `user_access` LIKE 'all' AND `flag` = '1'";
            $query = mysqli_query($conn,$sql); $sale_print1 = mysqli_num_rows($query);

            $sql='SHOW COLUMNS FROM `extra_access`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
            while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
            if(in_array("field_value", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `extra_access` ADD `field_value` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `field_function`"; mysqli_query($conn,$sql); }

            $wcond_flag = 0;
            $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'SalewithRCTAutoWapp:broiler_display_multisales2.php' AND `field_function` LIKE 'WhatsApp Manual Message (1-With Balance, 2-Without Balance) 1,1,1,1-Birds,Weight,Avg.Wt,Price' AND `user_access` LIKE 'all' AND `flag` = '1'";
            $query = mysqli_query($conn,$sql); $wcond_flag = mysqli_num_rows($query);
            if($wcond_flag > 0){ }
            else{
                $wcond_flag = 0;
                $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'SalewithRCTAutoWapp:broiler_display_multisales2.php'";
                $query = mysqli_query($conn,$sql); $wcond_flag = mysqli_num_rows($query);
                if($wcond_flag > 0){
                    $sql = "UPDATE `extra_access` SET `user_access` = 'all',`field_value` = '1,1,1,1',`field_function` = 'WhatsApp Manual Message (1-With Balance, 2-Without Balance) 1,1,1,1-Birds,Weight,Avg.Wt,Price' WHERE `field_name` LIKE 'SalewithRCTAutoWapp:broiler_display_multisales2.php';";
                    mysqli_query($conn,$sql);
                }
                else{
                    $sql = "INSERT INTO `extra_access` (`field_name`,`field_function`,`field_value`,`user_access`,`flag`) VALUES ('SalewithRCTAutoWapp:broiler_display_multisales2.php','WhatsApp Manual Message (1-With Balance, 2-Without Balance) 1,1,1,1-Birds,Weight,Avg.Wt,Price','1,1,1,1','all','0');";
                    mysqli_query($conn,$sql);
                }
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
                                        <?php if($sale_multiple_delete_flag == 1){ ?>
                                        <div class="form-group">
                                            <button type="button" class="btn bg-danger" id="deletepage" value="broiler_delete_multisales2flb.php" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> Delete-Multiple</button>&ensp;
                                        </div>
                                        <?php } ?>
                                        <?php if($sale_multiple_edit_flag == 1){ ?>
                                        <div class="form-group">
                                            <button type="button" class="btn bg-success" id="editpage" value="broiler_edit_multisales2flb.php" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> Edit-Multiple</button>&ensp;
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
                                        <th>DC No</th>
                                        <th>Customer</th>
										<th>Birds</th>
										<th>Quantity</th>
										<th>Avg Wt</th>
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
                                        $sql = "SELECT * FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate' $cond_assigned AND `dflag` = '0' AND `sale_type` IN ('FormMBSale','CusMBSale') ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0; //
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['trnum'];
                                            $edit_url = $edit_link."?utype=edit&trnum=".$id;
                                            //$delete_url = $delete_link."?utype=delete&trnum=".$id;
                                           // $print_url = $print_link."?utype=print&trnum=".$id;
                                           $print_url = "print/Examples/broiler_print_multisales3.php?id=".$row['date']."@".$row['vcode']."@inv";
                                           $print_url2 = "print/Examples/broiler_saleinvoice.php?id=".$row['trnum']."@inv";
                                    ?>
                                    <tr>
										<td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])) ?></td>
										<td><?php echo $row['billno']; ?></td>
										<td><?php if($row['sale_type'] == "FormMBSale"){ echo $sector_name[$row['warehouse']]; } else{ echo $vendor_name[$row['vcode']]; } ?></td>
										<td><?php echo str_replace(".00","",$row['birds']); ?></td>
										<td><?php echo $row['rcd_qty']; ?></td>
                                        <td><?php
                                        
                                        if(round($row['birds']) > 0){
                                            echo round(($row['rcd_qty'] / $row['birds']),2);
                                        }
                                        else{
                                            echo "0";
                                        }
                                        ?></td>
										<td><?php echo $row['rate']; ?></td>
										<td><?php echo $row['item_tamt']; ?></td>
										<td><?php echo $sector_name[$row['warehouse']]; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>&ensp;&ensp;";
                                                if($print_flag == 1){
                                                    echo "<a href='".$print_url."' target='_BLANK'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                                }
                                                if($sale_print1 > 0){
                                                    echo "<a href='".$print_url2."' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Print'></i></a>&ensp;&ensp;";
                                                }
                                            }
                                            else if($row['gc_flag'] == 1){
                                                echo "<i class='fa fa-lock' style='color:gray;' title='GC processed'></i></a>&ensp;&ensp;";
                                                if($print_flag == 1){
                                                    echo "<a href='".$print_url."' target='_BLANK'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                                }
                                                if($sale_print1 > 0){
                                                    echo "<a href='".$print_url2."' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Print'></i></a>&ensp;&ensp;";
                                                }
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
                                                    echo "<a href='".$print_url."' target='_BLANK'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                                }
                                                if($sale_print1 > 0){
                                                    echo "<a href='".$print_url2."' target='_BLANK'><i class='fa fa-print' style='color:brown;' title='Print'></i></a>&ensp;&ensp;";
                                                }
                                            }
                                        ?>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                    
                                </tbody>
                                <tfoot>
                                    <tr class="thead2">
                                        <th colspan="3">Total</th>
                                        <th id="sum-birds"></th>
                                        <th id="sum-quantity"></th>
                                        <th id="sum-avg-wt"></th>
                                        <th id="sum-price"></th>
                                        <th id="sum-amount"></th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                                
                            </table>
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
			function checkdelete(a){
				var b = "<?php echo $delete_url; ?>"+a;
				var c = confirm("are you sure you want to delete the transaction "+a+" ?");
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
           $(document).ready(function() {
                // Function to calculate and display sum amounts for each page
                function updateSums() {
                    var sumBirds = 0;
                    var sumQuantity = 0;
                    var sumAvgWt = 0;
                    var sumPrice = 0;
                    var sumAmount = 0;

                    $('#example1 tbody tr').each(function() {
                        var birds = $(this).find('td:nth-child(4)').text();
                        var quantity = $(this).find('td:nth-child(5)').text();
                        var avgWt = $(this).find('td:nth-child(6)').text();
                        var price = $(this).find('td:nth-child(7)').text();
                        var amount = $(this).find('td:nth-child(8)').text();

                        birds = parseFloat(birds) || 0;
                        quantity = parseFloat(quantity) || 0;
                        avgWt = parseFloat(avgWt) || 0;
                        price = parseFloat(price) || 0;
                        amount = parseFloat(amount) || 0;

                        sumBirds += birds;
                        sumQuantity += quantity;
                        sumAvgWt += avgWt;
                        sumPrice += price;
                        sumAmount += amount;
                    });

                    $('#sum-birds').text(sumBirds.toFixed(2));
                    $('#sum-quantity').text(sumQuantity.toFixed(2));
                    $('#sum-avg-wt').text(sumAvgWt.toFixed(2));
                    $('#sum-price').text(sumPrice.toFixed(2));
                    $('#sum-amount').text(sumAmount.toFixed(2));
                }

                // Initial sum amounts calculation
                updateSums();

                // Update sum amounts on page change
                $('#example1').on('draw.dt', function() {
                    updateSums();
                });
            });


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