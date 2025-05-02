<?php
//chicken_display_multiplesale3.php
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

        //Check Import Flag
        $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'chicken_display_multiplesale3.php' AND `field_function` LIKE 'Import Sales' AND `user_access` LIKE 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $iport_sflag = mysqli_num_rows($query);
    ?>
    <html>
        <head>
            <?php include "xendorheadlink.php"; ?>
        </head>
        <body>
		<section class="content-header">
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">CoA</a></li>
				<li class="active">Display Multiple-Sales</li>
			</ol>
			<h1>Multiple-Sales</h1>
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
                    <?php if($iport_sflag == 1){ ?><button type="button" class="btn btn-info" id="editpage" value="chicken_import_multiplesale3.php" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> Import</button><?php } ?>
                    <?php if($edt_flag == 1){ ?><button type="button" class="btn btn-info" id="editpage" value="chicken_edit_multiplesale3m.php" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> Edit Multiple</button><?php } ?>
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
										<th>Customer</th>
										<th>Item</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Amount</th>
										<th>Action</th>
									</tr>
								</thead>
                                <tbody>
                                <?php
                                    /*Fetch Column Availability*/
                                    $sql='SHOW COLUMNS FROM `customer_sales`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                                    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                                    if(in_array("cmn_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `cmn_trnum` VARCHAR(300) NULL DEFAULT NULL AFTER `invoice`"; mysqli_query($conn,$sql); }
                                    if(in_array("freight_amt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `freight_amt` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `totalamt`"; mysqli_query($conn,$sql); }
                                    if(in_array("tcdsper", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `tcdsper` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `freight_amt`"; mysqli_query($conn,$sql); }
                                    if(in_array("tcds_type1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `tcds_type1` VARCHAR(300) NULL DEFAULT NULL AFTER `tcdsper`"; mysqli_query($conn,$sql); }
                                    if(in_array("tcds_type2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `tcds_type2` VARCHAR(300) NULL DEFAULT NULL AFTER `tcds_type1`"; mysqli_query($conn,$sql); }
                                    if(in_array("tcdsamt", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `tcdsamt` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `tcds_type2`"; mysqli_query($conn,$sql); }
                                    if(in_array("roundoff_type1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `roundoff_type1` VARCHAR(300) NULL DEFAULT NULL AFTER `tcdsamt`"; mysqli_query($conn,$sql); }
                                    if(in_array("roundoff_type2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `roundoff_type2` VARCHAR(300) NULL DEFAULT NULL AFTER `roundoff_type1`"; mysqli_query($conn,$sql); }
                                    if(in_array("roundoff", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `roundoff` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `roundoff_type2`"; mysqli_query($conn,$sql); }
                                    if(in_array("finaltotal", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `finaltotal` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `roundoff`"; mysqli_query($conn,$sql); }
                                    if(in_array("balance", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `balance` DECIMAL(20,5) NOT NULL DEFAULT '0' AFTER `finaltotal`"; mysqli_query($conn,$sql); }
                                    if(in_array("warehouse", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `warehouse` VARCHAR(300) NULL DEFAULT NULL AFTER `balance`"; mysqli_query($conn,$sql); }
                                    if(in_array("remarks", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `remarks` VARCHAR(1500) NULL DEFAULT NULL AFTER `warehouse`"; mysqli_query($conn,$sql); }
                                    if(in_array("drivercode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `drivercode` VARCHAR(300) NULL DEFAULT NULL AFTER `remarks`"; mysqli_query($conn,$sql); }
                                    if(in_array("vehiclecode", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `vehiclecode` VARCHAR(300) NULL DEFAULT NULL AFTER `drivercode`"; mysqli_query($conn,$sql); }
                                    if(in_array("flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `flag` INT(100) NOT NULL DEFAULT '0' AFTER `vehiclecode`"; mysqli_query($conn,$sql); }
                                    if(in_array("active", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `active` INT(100) NOT NULL DEFAULT '1' AFTER `flag`"; mysqli_query($conn,$sql); }
                                    if(in_array("authorization", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `authorization` INT(100) NOT NULL DEFAULT '0' AFTER `active`"; mysqli_query($conn,$sql); }
                                    if(in_array("tdflag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `tdflag` INT(100) NOT NULL DEFAULT '0' AFTER `authorization`"; mysqli_query($conn,$sql); }
                                    if(in_array("pdflag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `pdflag` INT(100) NOT NULL DEFAULT '0' AFTER `tdflag`"; mysqli_query($conn,$sql); }
                                    if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL AFTER `pdflag`"; mysqli_query($conn,$sql); }
                                    if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL AFTER `trtype`"; mysqli_query($conn,$sql); }
                                    if(in_array("sale_type", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `sale_type` VARCHAR(300) NULL DEFAULT NULL AFTER `trlink`"; mysqli_query($conn,$sql); }
                                    
                                    $sql='SHOW COLUMNS FROM `customer_receipts`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                                    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                                    if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_receipts` ADD `link_trnum` VARCHAR(300) NULL DEFAULT NULL AFTER `trnum`"; mysqli_query($conn,$sql); }
                                    
                                    /*Check for Table Availability*/
                                    $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
                                    $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
                                    if(in_array("whatsapp_keygenerate_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.whatsapp_keygenerate_master LIKE poulso6_admin_chickenmaster.whatsapp_keygenerate_master;"; mysqli_query($conn,$sql1); }
                                    if(in_array("employee_sale_wages", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.employee_sale_wages LIKE poulso6_admin_chickenmaster.employee_sale_wages;"; mysqli_query($conn,$sql1); }
                                    if(in_array("broiler_printview_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_printview_master LIKE poulso6_admin_chickenmaster.broiler_printview_master;"; mysqli_query($conn,$sql1); }
                                    if(in_array("master_generator", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_generator LIKE poulso6_admin_chickenmaster.master_generator;"; mysqli_query($conn,$sql1); }
                                    if(in_array("prefix_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.prefix_master LIKE poulso6_admin_chickenmaster.prefix_master;"; mysqli_query($conn,$sql1); }
                                    if(in_array("extra_access", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.extra_access LIKE poulso6_admin_chickenmaster.extra_access;"; mysqli_query($conn,$sql1); }
                                    
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
                                    
                                    $sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC";
                                    $query = mysqli_query($conn,$sql); $cus_name = array();
                                    while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; }

                                    $sql = "SELECT * FROM `item_details` ORDER BY `description` ASC";
                                    $query = mysqli_query($conn,$sql); $item_name = array();
                                    while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

                                    $delete_link = $acs_delete_url;
                                    $sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `trlink` LIKE '$href' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date` ASC";
                                    $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){
                                        $id = $row['invoice'];
                                        $edit_link = $acs_edit_url."?utype=edit&trnum=".$id;
                                        $authorize_url = $acs_update_url."?utype=authorize&trnum=".$id;
                                        if($row['active'] == 1){ $update_link = $acs_update_url."?utype=pause&trnum=".$id; }
                                        else{ $update_link = $acs_update_url."?utype=activate&trnum=".$id; }
                                        $print_dt = "?trnum=".$id."&date=".$row['date']."&warehouse=".$row['warehouse']."&vcode=".$row['customercode'];
                                    ?>
                                    <tr>
										<td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
										<td><?php echo $row['invoice']; ?></td>
										<td><?php echo $cus_name[$row['customercode']]; ?></td>
										<td><?php echo $item_name[$row['itemcode']]; ?></td>
										<td><?php echo $row['netweight']; ?></td>
										<td><?php echo $row['itemprice']; ?></td>
										<td><?php echo $row['totalamt']; ?></td>
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
                $(document).ready(function(){
                    var from_d = '<?php echo $from_date; ?>';
                    //alert(from_d+''+to_d);
                    $('#datepickers').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: from_d});
                })
                $(document).ready(function(){
                    $('#datepickers1').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true});
                })
                function displaycalendor(){
                    var from_d = '<?php echo $from_date; ?>';
                    $('.dates').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: from_d});
                }
                $(document).ready(function(){
                    var from_d = '<?php echo $from_date; ?>';
                    $('.dates').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true,minDate: from_d});
                })
                function checkdelete(a){
                    var b = "<?php echo $delete_link.'?page=delete&id='; ?>"+a;
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