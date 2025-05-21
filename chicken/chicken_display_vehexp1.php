<?php
//chicken_display_vehexp1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    include "chicken_fetch_accesslist.php";
    if($acslist_error_flag == 0){
        $fd_id = $href."fdate"; $td_id = $href."tdate";
        if(isset($_POST['bdates']) == true){
            $pbdates = $_POST['bdates']; $bdates = explode(" - ",$_POST['bdates']);
            $fdate = date("Y-m-d",strtotime($bdates[0]));
            $tdate = date("Y-m-d",strtotime($bdates[1]));
            $_SESSION[$fd_id] = $fdate;
            $_SESSION[$td_id] = $tdate;
        }
        else {
            $fdate = $tdate = date("Y-m-d");
            if(!empty($_SESSION[$fd_id])){ $fdate = $_SESSION[$fd_id]; }
            if(!empty($_SESSION[$td_id])){ $tdate = $_SESSION[$td_id]; }
            $pbdates = date("d.m.Y",strtotime($fdate))." - ".date("d.m.Y",strtotime($tdate));
        }
        
        if($_SESSION['usr_atype'] = "S" || $_SESSION['usr_atype'] = "A"){
            $idate = "2001-01-01";
            $from_date = date('d.m.Y', strtotime($idate));
        }
        else{
            $sql = "SELECT * FROM `dataentry_daterange` WHERE `type` = 'Sales' OR `type` = 'all' AND `active` = '1' ORDER BY `type` ASC";
            $query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ $days = $row['days']; }
            if($days == ""){ $from_date = date('d.m.Y'); } else{ $from_date = date('d.m.Y', strtotime('-'.$days.' days')); }
        }
    ?>
    <html>
        <head>
            <?php include "xendorheadlink.php"; ?>
        </head>
        <body>
		<section class="content-header">
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Display Sales</li>
			</ol>
			<h1>Vehicle Expence</h1>
		</section><br/>
            <div class="row" style="margin: 10px 10px 0 10px;">
                <form action="<?php echo $href; ?>" method="post">
                    <div align="left" class="col-md-6">
                        <div class="input-group"><div class="input-group-addon">Date: </div><input type="text" class="form-control pull-right" name="bdates" id="reservation" value="<?php echo $pbdates; ?>"></div>
                    </div>
                    <div align="left" class="col-md-2">
                        <div class="input-group"><button class="btn btn-success btn-sm" name="submit" id="submit" type="submit">Submit</button></div>
                    </div>
                </form>
                
                <div align="right">
                    <?php if($edt_flag == 1){ ?><button type="button" class="btn btn-info" id="editpage" value="chicken_edit_purchasesm.php" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> Edit Multiple</button><?php } ?>
                    <?php if($acs_add_flag == 1){ ?><button type="button" class="btn btn-warning" id="addpage" value="<?php echo $acs_add_url; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button><?php } ?>
                </div>
            </div>
            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
									<tr>
										<th>Date</th>
										<th>Invoice</th>
										<th>Mode</th>
										<th>Vehicle No.</th>
										<th>Amount</th>
										<th>Remarks</th>
										<th>Action</th>
									</tr>
								</thead>
                                <tbody>
                                <?php
                                    /*Fetch Column Availability*/
                                    $sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                                    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                                    if(in_array("tcds_type1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `tcds_type1` VARCHAR(300) NULL DEFAULT NULL AFTER `tcdsper`"; mysqli_query($conn,$sql); }
                                    if(in_array("tcds_type2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `tcds_type2` VARCHAR(300) NULL DEFAULT NULL AFTER `tcds_type1`"; mysqli_query($conn,$sql); }
                                    if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL AFTER `pdflag`"; mysqli_query($conn,$sql); }
                                    if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL AFTER `trtype`"; mysqli_query($conn,$sql); }
                                    if(in_array("dressing_charge", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `dressing_charge` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `tcdsamt`"; mysqli_query($conn,$sql); }
                                    if(in_array("transporter_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `transporter_code` VARCHAR(300) NULL DEFAULT NULL AFTER `dressing_charge`"; mysqli_query($conn,$sql); }
                                    if(in_array("freight_amount", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `freight_amount` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `transporter_code`"; mysqli_query($conn,$sql); }
                                    if(in_array("roundoff_type1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `roundoff_type1` VARCHAR(300) NULL DEFAULT NULL AFTER `freight_amount`"; mysqli_query($conn,$sql); }
                                    if(in_array("roundoff_type2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `roundoff_type2` VARCHAR(300) NULL DEFAULT NULL AFTER `roundoff_type1`"; mysqli_query($conn,$sql); }
                                    
                                    $sql='SHOW COLUMNS FROM `acc_vouchers`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                                    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                                    if(in_array("mode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_vouchers` ADD `mode` VARCHAR(300) NULL DEFAULT NULL AFTER `tcoa`"; mysqli_query($conn,$sql); }
                                    if(in_array("doc1_path", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_vouchers` ADD `doc1_path` VARCHAR(300) NULL DEFAULT NULL AFTER `mode`"; mysqli_query($conn,$sql); }
                                    if(in_array("doc2_path", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_vouchers` ADD `doc2_path` VARCHAR(300) NULL DEFAULT NULL AFTER `doc1_path`"; mysqli_query($conn,$sql); }
                                    if(in_array("doc3_path", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_vouchers` ADD `doc3_path` VARCHAR(300) NULL DEFAULT NULL AFTER `doc2_path`"; mysqli_query($conn,$sql); }
                                    
                                    /*Check for Table Availability*/
                                    $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
                                    $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
                                    if(in_array("main_contactdetails", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_contactdetails LIKE poulso6_admin_chickenmaster.main_contactdetails;"; mysqli_query($conn,$sql1); }
                                    if(in_array("main_dailypaperrate", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_dailypaperrate LIKE poulso6_admin_chickenmaster.main_dailypaperrate;"; mysqli_query($conn,$sql1); }
                                    if(in_array("customer_price", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.customer_price LIKE poulso6_admin_chickenmaster.customer_price;"; mysqli_query($conn,$sql1); }
                                    if(in_array("broiler_printview_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_printview_master LIKE poulso6_admin_chickenmaster.broiler_printview_master;"; mysqli_query($conn,$sql1); }
                                    if(in_array("master_generator", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_generator LIKE poulso6_admin_chickenmaster.master_generator;"; mysqli_query($conn,$sql1); }
                                    if(in_array("prefix_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.prefix_master LIKE poulso6_admin_chickenmaster.prefix_master;"; mysqli_query($conn,$sql1); }
                                    if(in_array("image_deletion_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.image_deletion_details LIKE poulso6_admin_chickenmaster.image_deletion_details;"; mysqli_query($conn,$sql1); }
                                    
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
                                    
                                    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
                                    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
                                    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

                                    $sql = "SELECT * FROM `item_details` ORDER BY `description` ASC";
                                    $query = mysqli_query($conn,$sql); $item_name = array();
                                    while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

                                    $sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){
                                        $acode[$row['code']] = $row['code'];
                                        $adesc[$row['code']] = $row['description'];
                                    }

                                    $delete_link = $acs_delete_url;
                                    $sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `tdflag` = '0' AND `pdflag` = '0' AND `trlink` LIKE '$href' ORDER BY `date` ASC";
                                    $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){
                                        $id = $row['trnum'];
                                        $edit_link = $acs_edit_url."?utype=edit&trnum=".$id;
                                        $authorize_url = $acs_update_url."?utype=authorize&trnum=".$id;
                                        if($row['active'] == 1){ $update_link = $acs_update_url."?utype=pause&trnum=".$id; }
                                        else{ $update_link = $acs_update_url."?utype=activate&trnum=".$id; }
                                        $print_dt = "?id=".$id;
                                    ?>
                                    <tr>
										<td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $adesc[$row['mode']]; ?></td>
										<td><?php echo $sector_name[$row['warehouse']]; ?></td>
										<td><?php echo $row['amount']; ?></td>
										<td><?php echo $row['remarks']; ?></td>
										<td style="width:15%;" align="left">
                                        <?php
                                        if($row['flag'] == 2){ echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>&ensp;"; }
                                        else{
                                            if($acs_edit_flag == 1){
                                                echo "<a href='".$edit_link."'><i class='fa fa-pencil' style='color:brown;' title='Edit'></i></a>&ensp;";
                                            }
                                            if($acs_delete_flag == 1){
                                                ?>
                                                <a href='javascript:void(0)' id='<?php echo $id; ?>' value='<?php echo $id; ?>' onclick='checkdelete(this.id)'>
                                                <i class='fa fa-close' style='color:red;' title='delete'></i>
                                                </a>&ensp;
                                                <?php
                                            }
                                            if($paflag == 1 && $acs_update_flag == 1){
                                                if($row['active'] == 1){
                                                    echo "<a href='".$update_link."'><i class='fa fa-pause' style='color:blue;' title='Activate'></i></a>&ensp;";
                                                }
                                                else{
                                                    echo "<a href='".$update_link."'><i class='fa fa-play' style='color:blue;' title='Pause'></i></a>&ensp;";
                                                }
                                            }
                                            if($autflag == 1 && $acs_update_flag == 1){
                                                echo "<a href='".$authorize_url."'><i class='fa fa-lock-open' style='color:orange;' title='Authorize'></i></a>&ensp;";
                                            }
                                        }
                                        if($acs_print_flag == 1){
                                            $printv_list = "";
                                            for($p = 0;$p <= $pc;$p++){
                                                $ppath = ""; $ppath = $print_path[$p]."".$print_dt;
                                                if($icon_type[$p] == "image"){
                                                    $printv_list .= '<a href="'.$ppath.'" target="'.$target[$p].'"><img src="'.$icon_path[$p].'" style="width:15px;height:15px;" title="'.$field_name[$p].'" /></a>&ensp;';
                                                }
                                                else if($icon_type[$p] == "icon"){
                                                    $printv_list .= '<a href="'.$ppath.'" target="'.$target[$p].'"><i class="'.$icon_path[$p].'" style="color:'.$icon_color[$p].';" title="'.$field_name[$p].'"></i></a>&ensp;';
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
            <script>
                function add_page(a){ var b = document.getElementById(a).value; window.location.href = b; }
                function checkdelete(a){
                    var b = "<?php echo $delete_link.'?page=delete&trnum='; ?>"+a;
                    var c = confirm("are you sure you want to delete the transaction "+a+" ?");
                    if(c == true){
                        window.location.href = b;
                    }
                    else{ }
                }
            </script>
            <?php include "xendorfootlink.php"; ?>
        </body>
    </html>
    <?php
    }
    else{ include "chicken_error_popup.php"; }
}
else{ include "chicken_error_popup.php"; }