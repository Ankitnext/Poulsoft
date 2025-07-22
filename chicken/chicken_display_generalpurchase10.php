<?php
//chicken_display_generalpurchase10.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

header("Access-Control-Allow-Origin: https://chicken.poulsoft.org");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

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
            <!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        </head>
        <body>
		<section class="content-header">
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Display Purchase</li>
			</ol>
			<h1>Purchase</h1>
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
										<th>Supplier</th>
										<th>Invoice</th>
										<th>Item</th>
										<th>Quantity</th>
										<th>Price</th>
										<th>Amount</th>
										<th>Total Amount</th>
										<th>Action</th>
									</tr>
								</thead>
                                <tbody>
                                <?php
                                    /*Fetch Column Availability*/
                                    $sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                                    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                                    if(in_array("trtype", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trtype` VARCHAR(300) NULL DEFAULT NULL AFTER `pdflag`"; mysqli_query($conn,$sql); }
                                    if(in_array("trlink", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `trlink` VARCHAR(300) NULL DEFAULT NULL AFTER `trtype`"; mysqli_query($conn,$sql); }
                                    if(in_array("roundoff_type1", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `roundoff_type1` VARCHAR(300) NULL DEFAULT NULL AFTER `freight_amount`"; mysqli_query($conn,$sql); }
                                    if(in_array("roundoff_type2", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `customer_sales` ADD `roundoff_type2` VARCHAR(300) NULL DEFAULT NULL AFTER `roundoff_type1`"; mysqli_query($conn,$sql); }
                                    
                                    /*Fetch Column Availability*/
                                    $sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
                                    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
                                    if(in_array("gst", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `gst` VARCHAR(300) NULL DEFAULT NULL AFTER `itemcode`"; mysqli_query($conn,$sql); }
                                    
                                    /*Check for Table Availability*/
                                    $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
                                    $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
                                    if(in_array("main_contactdetails", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_contactdetails LIKE poulso6_admin_chickenmaster.main_contactdetails;"; mysqli_query($conn,$sql1); }
                                    if(in_array("main_dailypaperrate", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_dailypaperrate LIKE poulso6_admin_chickenmaster.main_dailypaperrate;"; mysqli_query($conn,$sql1); }
                                    if(in_array("customer_price", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.customer_price LIKE poulso6_admin_chickenmaster.customer_price;"; mysqli_query($conn,$sql1); }
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
                                    
                                    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' ORDER BY `name` ASC";
                                    $query = mysqli_query($conn,$sql); $cus_name = array();
                                    while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; }

                                    $sql = "SELECT * FROM `item_details` ORDER BY `description` ASC";
                                    $query = mysqli_query($conn,$sql); $item_name = array();
                                    while($row = mysqli_fetch_assoc($query)){ $item_name[$row['code']] = $row['description']; }

                                    $delete_link = $acs_delete_url;
                                    $sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `tdflag` = '0' AND `pdflag` = '0' AND `trlink` LIKE '$href' ORDER BY `date` ASC";
                                    $query = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($query)){
                                        $id = $row['invoice'];
                                        $edit_link = $acs_edit_url."?utype=edit&trnum=".$id;
                                        $authorize_url = $acs_update_url."?utype=authorize&trnum=".$id;
                                        if($row['active'] == 1){ $update_link = $acs_update_url."?utype=pause&trnum=".$id; }
                                        else{ $update_link = $acs_update_url."?utype=activate&trnum=".$id; }
                                        $print_dt = "?id=".$id;

                                        $ref_docs = "";
                                        if($row['purchase_image'] != ""){
                                            $ref_docs .= '<a href="'.$row["purchase_image"].'" title="URL-1" download><i class="fa-solid fa-angles-down" style="font-size:15px;"></i></a>&ensp;';
                                        }
                                    ?>
                                    <tr>
										<td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
										<td><?php echo $cus_name[$row['vendorcode']]; ?></td>
										<td><?php echo $row['invoice']; ?></td>
										<td><?php echo $item_name[$row['itemcode']]; ?></td>
										<td><?php echo $row['netweight']; ?></td>
										<td><?php echo $row['itemprice']; ?></td>
										<td><?php echo $row['totalamt']; ?></td>
										<td><?php echo $row['finaltotal']; ?></td>
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
                                        echo '<a href="javascript:void(0)" data-toggle="modal" data-target="#modal-12" id="'.$id.'" onclick="fetch_ticket_comments(this.id);"><i class="fa-regular fa-paste" style="color:green;"></i></a>&ensp;&ensp;';
                                        echo $ref_docs;
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
                    <div class="card-body">
                        <div class="modal fade" id="modal-12" role="dialog" aria-labelledby="modal-12">
                            <div class="modal-dialog modal-dialog-centered modal-min" role="document">
                                <div class="modal-content">
                                    <div class="modal-body text-left">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <form action="chicken_upload_purchase_lsfi.php" method="post" enctype="multipart/form-data">
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
                                                                    <label for="file_path1">Remarks</label>
                                                                    <textarea name="file_comment" id="file_comment" placeholder="Type Message ..." class="form-control" style="height:38px;"></textarea>
                                                                </div> 
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
            </section>
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
                    var url = "chicken_fetch_purfileupload.php?trnum="+trnum;
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
                                    if(file_dt[1] != ""){ document.getElementById("file_comment").value = file_dt[1]; }
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
            }
            </script>
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