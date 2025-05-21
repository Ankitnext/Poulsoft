<?php
//chicken_display_shop_investment1.php
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
            $vtype = $_POST['vtype'];
            $_SESSION[$fd_id] = $fdate;
            $_SESSION[$td_id] = $tdate;
        }
        else {
            $fdate = $tdate = date("Y-m-d"); $vtype = "customer";
            if(!empty($_SESSION[$fd_id])){ $fdate = $_SESSION[$fd_id]; }
            if(!empty($_SESSION[$td_id])){ $tdate = $_SESSION[$td_id]; }
            $pbdates = date("d.m.Y",strtotime($fdate))." - ".date("d.m.Y",strtotime($tdate));
        }
        
        if($_SESSION['usr_atype'] = "S" || $_SESSION['usr_atype'] = "A"){
            $idate = "2001-01-01";
            $from_date = date('d.m.Y', strtotime($idate));
        }
        else{
            $sql = "SELECT * FROM `dataentry_daterange` WHERE `type` = 'Stock Adjustment' OR `type` = 'all' AND `active` = '1' ORDER BY `type` ASC";
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
				<li><a href="#">Transactions</a></li>
				<li class="active">Display Stock Investment</li>
			</ol>
			<h1>Stock Investment</h1>
		</section><br/>
            <div class="row" style="margin: 10px 10px 0 10px;">
                <form action="<?php echo $href; ?>" method="post">
                    <div align="left" class="col-md-6">
                        <div class="input-group"><div class="input-group-addon">Date: </div><input type="text" class="form-control pull-right" name="bdates" id="reservation" value="<?php echo $pbdates; ?>"></div>
                    </div>
                    <!--<div align="left" class="col-md-3">
                        <div class="input-group">
                            <div class="input-group-addon">Vendor Type: </div>
                            <select name="vtype" id="vtype" class="form-control select2" style="width:150px;">
                                <option value="customer" <?php //if($vtype == "customer"){ echo "selected"; } ?>>Customer</option>
                                <option value="supplier" <?php //if($vtype == "supplier"){ echo "selected"; } ?>>Supplier</option>
                            </select>
                        </div>
                    </div>-->
                    <div align="left" class="col-md-2">
                        <div class="input-group"><button class="btn btn-success btn-sm" name="submit" id="submit" type="submit">Submit</button></div>
                    </div>
                </form>
                
                <div align="right">
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
										<th>Transaction No.</th>
										<th>Customer</th>
										<th>Item</th>
										<th>Amount</th>
										<th>Remarks</th>
										<th>Action</th>
									</tr>
								</thead>
                                <tbody>
                                <?php
                                    /*Check for Table Availability*/
                                    $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
                                    $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
                                    if(in_array("item_stock_adjustment", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.item_stock_adjustment LIKE poulso6_admin_chickenmaster.item_stock_adjustment;"; mysqli_query($conn,$sql1); }
                                    if(in_array("broiler_printview_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_printview_master LIKE poulso6_admin_chickenmaster.broiler_printview_master;"; mysqli_query($conn,$sql1); }
                                    if(in_array("master_generator", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_generator LIKE poulso6_admin_chickenmaster.master_generator;"; mysqli_query($conn,$sql1); }
                                    if(in_array("prefix_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.prefix_master LIKE poulso6_admin_chickenmaster.prefix_master;"; mysqli_query($conn,$sql1); }
                                    
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

                                    $sql = "SELECT * FROM `inv_sectors` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){ $sec_name[$row['code']] = $row['description']; }

                                    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; 
                                    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
                                    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
                                
                                    $sql = "SELECT * FROM `item_details` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

                                    $delete_link = $acs_delete_url;
                                    $sql = "SELECT * FROM `vendor_shop_investment` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `trlink` LIKE '$href' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                                    $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){
                                        $id = $row['trnum'];
                                        $edit_link = $acs_edit_url."?utype=edit&trnum=".$id;
                                        $authorize_url = $acs_update_url."?utype=authorize&trnum=".$id;
                                        if($row['active'] == 1){ $update_link = $acs_update_url."?utype=pause&trnum=".$id; }
                                        else{ $update_link = $acs_update_url."?utype=activate&trnum=".$id; }
                                    ?>
                                    <tr>
										<td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $cus_name[$row['vcode']]; ?></td>
										<td><?php echo $item_name[$row['icode']]; ?></td>
										<td><?php echo $row['amount']; ?></td>
										<td><?php echo $row['remarks']; ?></td>
										<td style="width:15%;" align="left">
                                        <?php
                                        if($row['flag'] == 1){ echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>&ensp;"; }
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