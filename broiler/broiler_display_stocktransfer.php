<?php
//broiler_display_stocktransfer.php
include "newConfig.php";
include "number_format_ind.php";
// $user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
// if($cid != ""){ $_SESSION['stocktransfer'] = $cid; } else{ $cid = $_SESSION['stocktransfer']; }
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "broiler_check_accessmaster.php";

$sql = "SELECT * FROM `master_form_tableaccess` WHERE `href` = '$href' AND `active` = '1'"; $query = mysqli_query($sconn,$sql);
while($row = mysqli_fetch_assoc($query)){ $table_name = $row['table_name']; } $table_session = $cid."tbl_access"; $_SESSION[$table_session] = $table_name;

if($access_error_flag == 0){
    include "broiler_fetch_accesslist.php";
?>
<html lang="en">
    <head>
    <?php include "header_head2.php"; ?>
    <!-- Datepicker -->
    <!-- <link href="datepicker/jquery-ui.css" rel="stylesheet"> -->
    </head>
    <body class="m-0 hold-transition sidebar-mini">
        <?php
       
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

            //Fetch Print-View from Print Master
            $i = $pc = 0; $field_name = $field_name = $field_name = $field_name = $field_name = $field_name = array();
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

            $sector_code = $sector_name = array();
            $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `dflag` = '0' ".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); 
            while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; }
            $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; }
            if(sizeof($sector_code) > 0){ $sector_list = implode("','",$sector_code); $cond_assigned = " AND (`fromwarehouse` IN ('$sector_list') OR `towarehouse` IN ('$sector_list'))"; } else{ $cond_assigned = ""; }

            $sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

            $sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }
             // Driver  Category 
            $sql = "SELECT * FROM `broiler_designation` WHERE `description` LIKE '%driver%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $desig_code = "";
            while($row = mysqli_fetch_assoc($query)){ if($desig_code == ""){ $desig_code = $row['code']; } else{ $desig_code = $desig_code."','".$row['code']; } }
             // Driver Name            
            $sql = "SELECT * FROM `broiler_employee` WHERE `desig_code` IN ('$desig_code') AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
            while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
          
            $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <form action="<?php echo $href; ?>" method="post">
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
                                        <?php if($acs_add_flag == 1){ ?>
                                            <button type="button" class="btn" id="addpage" style="background-color:rgb(102, 41, 200); color: white;" value="<?php echo $acs_add_url; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <!-- <table id="example1" class="table table-bordered table-striped"> -->
                            <table id="example" class="display" style="width:100%">
                                <thead>
                                    <tr>
										<th>Date</th>
                                        <th>Trnum</th>
                                        <th>Dc No.</th>
                                        <th>From Location</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Driver</th>
										<th>Quantity</th>
										<th>To Location</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        
                                        $delete_url = $acs_delete_url."?utype=delete&trnum=";
                                        $sql = "SELECT * FROM `".$table_name."` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND (`trtype` NOT LIKE '%ChickTransfer%' OR `trtype` != '' OR `trtype` IS NULL) AND `dflag` = '0' $from_warehouse_condition $to_warehouse_condition $cond_assigned ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){

                                        $id = $row['code'];
                                        $edit_link = $acs_edit_url."?utype=edit&trnum=".$id;
                                        $authorize_url = $acs_update_url."?utype=authorize&trnum=".$id;
                                        if($row['active'] == 1){ $update_link = $acs_update_url."?utype=pause&trnum=".$id; }
                                        else{ $update_link = $acs_update_url."?utype=activate&trnum=".$id; }
                                        $val = $id;
                                    ?>
                                    <tr>
                                        <td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])) ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $row['dcno']; ?></td>
										<td><?php echo $sector_name[$row['fromwarehouse']]; ?></td>
										<td><?php echo $row['code']; ?></td>
										<td><?php echo $item_name[$row['code']]; ?></td>
										<td><?php echo $emp_name[$row['driver_code']]; ?></td>
										<td><?php echo $row['quantity']; ?></td>
										<td><?php echo $sector_name[$row['towarehouse']]; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>";
                                            }
                                            else {
                                                if($acs_edit_flag == 1){
                                                    echo "<a href='".$acs_edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($acs_delete_flag == 1 ){
                                                    ?>
                                                    <a href='javascript:void(0)' id='<?php echo $val; ?>' value='<?php echo $val; ?>' onclick='checkdelete(this.id)'>
                                                    <i class='fa fa-trash' style='color:red;' title='delete'></i>
                                                    </a>&ensp;&ensp;
                                                <?php
                                                }
                                                if($acs_update_flag == 1){
                                                    if($row['active'] == 1){
                                                        echo "<a href='".$acs_update_url."'><i class='fa fa-pause' style='color:blue;' title='Activate'></i></a>&ensp;&ensp;";
                                                    }
                                                    else{
                                                        echo "<a href='".$acs_update_url."'><i class='fa fa-play' style='color:blue;' title='Pause'></i></a>&ensp;&ensp;";
                                                    }
                                                    echo "<a href='".$acs_update_url."'><i class='fa fa-lock-open' style='color:orange;' title='Authorize'></i></a>&ensp;&ensp;";
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
        <!-- Datepicker -->
        <!-- <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script> -->
        <script>
            function checkdelete(a){
                
                var link_key = a;
				var main_link = "<?php echo $delete_url; ?>"+link_key;
				var c = confirm("are you sure you want to delete the transaction with transaction No: "+trnum);
				if(c == true){
					window.location.href = main_link;
				}
				else{ }
			}
			// function checkdelete(x){
            //     var val1 = x.split("@"); var a = val1[0]; var val = val1[1];
            //     if(a != ""){
            //         var inv_items = new XMLHttpRequest();
            //         var method = "GET";
            //         var url = "broiler_check_stocktransfer.php?id="+a;
            //         //window.open(url);
            //         var asynchronous = true;
            //         inv_items.open(method, url, asynchronous);
            //         inv_items.send();
            //         inv_items.onreadystatechange = function(){
            //             if(this.readyState == 4 && this.status == 200){
            //                 var count = this.responseText;
            //                 if(parseFloat(count) > 0){
            //                     alert("You can't delete the Item: "+val+", as Item is already in use!");
            //                 }
            //                 else{
            //                     var b = "<?php //echo $delete_url; ?>"+a;
            //                     var c = confirm("are you sure you want to delete the Item: "+val+"?");
            //                     if(c == true){
            //                         window.location.href = b;
            //                     }
            //                     else{ }
            //                 }
            //             }
            //         }
            //     }
			// }
        </script>
       
        <script>
			function add_page(a){ var b = document.getElementById(a).value; window.location.href = b; }
		</script>
    <?php include "header_foot3.php"; ?>
    </body>
</html>
<?php
}
else{
     header('location:index.php');
}
?>