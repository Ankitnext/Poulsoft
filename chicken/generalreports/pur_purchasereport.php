<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
		
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){
		include "../config.php";
		include "header_head.php";
		include "number_format_ind.php";
	}
	else{
		//include "../newConfig.php";
		include "APIconfig.php";
		include "header_head.php";
		include "number_format_ind.php";
	}
		
	$sql='SHOW COLUMNS FROM customer_sales'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE customer_sales ADD link_trnum VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }
		
	$sql='SHOW COLUMNS FROM pur_purchase'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
	while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
	if(in_array("link_trnum", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE pur_purchase ADD link_trnum VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `invoice`"; mysqli_query($conn,$sql); }
	
	$today = date("Y-m-d");
	$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; }
	$idisplay = ''; $ndisplay = 'style="display:none;"';
	$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $pname[$row['code']] = $row['name']; $cus_group[$row['code']] = $row['groupcode']; }
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $itemname[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $officename[$row['code']] = $row['description']; }
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
	if($fromdate == ""){ $fromdate = $todate = $today; } else { $fromdate = $_POST['fromdate']; $todate = $_POST['todate']; }
	$cname = $_POST['cname']; $iname = $_POST['iname']; $wname = $_POST['wname'];
	if($cname == "all") { $cnames = ""; } else { $cnames = " AND `vendorcode` = '$cname'"; }
	if($iname == "all") { $inames = ""; } else { $inames = " AND `itemcode` = '$iname'"; }
	if($wname == "all") { $wnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; }

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'pur_purchasereport.php' AND `field_function` LIKE 'Purchase Sale Sorting' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $sltr_flag = mysqli_num_rows($query); //$avou_flag = 1;
	// if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
	if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

	//echo "<script> alert('$ifjbwen'); </script>";
	$sql = "SELECT * FROM `main_reportfields` WHERE `field` LIKE 'Purchase Report' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $lg_count = mysqli_num_rows($query);
	if($lg_count > 0){ while($row = mysqli_fetch_assoc($query)){ $prate_flag = $row['prate']; $vehicle_flag = $row['vehicle_flag']; } } else{ $prate_flag = $vehicle_flag = 0; }
	if($vehicle_flag  == "" || $vehicle_flag == 0){ $vehicle_flag  = 0; }
	if($prate_flag == 1 || $prate_flag == "1"){
		$fdate = date("Y-m-d",strtotime($fromdate));
		$tdate = date("Y-m-d",strtotime($todate));
		$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $prates[$row['date']."@".$row['code']] = $row['new_price']; }
	}
	$exoption = "displaypage"; $customers = "all";
	if(isset($_POST['submit'])) { $excel_type = $_POST['export']; } else{ $excel_type = "displaypage"; }
	if(isset($_POST['submit']) == true){
		$exl_fdate = $_POST['fromdate']; $exl_tdate = $_POST['todate']; $exl_cname = $_POST['cname']; $exl_iname = $_POST['iname']; $exl_wname = $_POST['wname'];
		$upload_status = $_POST['upload_status'];
		if($upload_status == "all"){ $upload_filter = ""; } else if($upload_status == "1"){ $upload_filter = " AND `purchase_image` IS NOT NULL AND `purchase_image` != '' "; }else if($upload_status == "0"){ $upload_filter = " AND ( `purchase_image` IS  NULL OR `purchase_image` = '' )"; }
		$customers = $_POST['customers'];
	}
	else{
		$exl_fdate = $exl_tdate = $today; $exl_cname = $exl_iname = $exl_wname = $exl_user = "all";
	}
	$url = "../PHPExcel/Examples/PurchaseReport-Excel.php?fromdate=".$exl_fdate."&todate=".$exl_tdate."&ccode=".$exl_cname."&item=".$exl_iname."&sector=".$exl_wname;
	
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css"href="reportstyle.css">
		<script>
			var exptype = '<?php echo $excel_type; ?>';
			var url = '<?php echo $url; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
		</script>
		<style>
			.thead2 th {
 			 	top: 0;
 				position: sticky;
 				background-color: #98fb98;
			}
		</style>
		<style>
			body{
				color: black;
			}
			.formcontrol {
				height: 23px;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				padding-right: 5px;
				text-align: right;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
				if($dlogo_flag > 0) { ?>
					<td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
				<?php }
				else{ 
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Purchase Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } }?>
					<td align="center">
						<h3>Purchase Report</h3><?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Supplier:</b>&nbsp;<?php echo $pname[$cname]; ?></label><br/>
						<?php
							}
						?>
						<label class="reportheaderlabel"><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fromdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label class="reportheaderlabel"><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($todate)); ?></label>
					</td>
					<td>
					
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
				<?php if($db == ''){?>
				<form action="pur_purchasereport.php" method="post">
					<?php } else { ?>
					<form action="pur_purchasereport.php?db=<?php echo $db; ?>" method="post">
					<?php } ?>
						<table class="table1" style="min-width:100%;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="22">
										<label class="reportselectionlabel">From date</label>&nbsp;
										<input type="text" name="fromdate" id="datepickers" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($fromdate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="todate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($todate)); ?>"/>
									&ensp;&ensp;
										<label class="reportselectionlabel">Supplier</label>&nbsp;
										<select name="cname" id="cname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($cname == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Customer</label>&nbsp;
										<select name="customers" id="customers" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option value="<?php echo $row['code']; ?>" <?php if($customers == $row['code']) { echo 'selected'; } ?>><?php echo $row['name']; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Item</label>&nbsp;
										<select name="iname" id="iname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($iname == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
											<?php
												}
											?>
										</select>
									&ensp;&ensp;
										<label class="reportselectionlabel">Warehouse</label>&nbsp;
										<select name="wname" id="wname" class="form-control select2">
											<option value="all">-All-</option>
											<?php
												$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option <?php if($wname == $row['code']) { echo 'selected'; } ?> value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
											<?php
												}
											?>
										</select><br/>
										<label class="reportselectionlabel">Upload Status</label>&nbsp;
										<select name="upload_status" id="upload_status" class="form-control select2">
                                        <option value="all" <?php if($upload_status == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="1" <?php if($upload_status == "1"){ echo "selected"; } ?>>-Uploaded-</option>
                                        <option value="0" <?php if($upload_status == "0"){ echo "selected"; } ?>>-Not Uploaded-</option>
                                    </select>&ensp;&ensp;
										<label class="reportselectionlabel">Export To</label>&nbsp;
										<select name="export" id="export" class="form-control select2">
											<option <?php if($exoption == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
											<option <?php if($exoption == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
											<option <?php if($exoption == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
										</select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
									</td>
								</tr>
							</thead>
						<?php } ?>
							<thead class="thead2" style="background-color: #98fb98;">
								<th>Sl No.</th>
								<th>Date</th>
								<th>Customer</th>
								<th>Supplier</th>
								<th>Invoice</th>
								<th>Book Invoice</th>
								<th>Item</th>
								<?php if($ifjbwen == 1 || $ifjbw == 1){ ?><th>Jals</th><?php } ?>
								<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?><th>Birds</th><?php } ?>
								<?php if($ifjbwen == 1){ ?> <th>Total Weight</th><th>Empty Weight</th> <?php } ?>
								<th>Net Weight</th>
								<?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ ?><th>Avg.Weight</th><?php } ?>
								<?php if($prate_flag == 1 || $prate_flag == "1"){ echo "<th>Paper Rate</th>"; } ?>
								<th>Price</th>
								<th>Discount</th>
								<th>Tax</th>
								<th>TDS</th>
								<th>Amount</th>
								<?php if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<th>Vehicle No.</th>"; } ?>
								<th>Warehouse</th>
								<th>Narration</th>
								<th>Upload Status</th>
							</thead>
							<tbody class="tbody1" style="background-color: #f4f0ec;">
							<?php
								$client = $_SESSION['client'];
								$fromdate = date("Y-m-d",strtotime($fromdate));
								$todate = date("Y-m-d",strtotime($todate));
								$cus_fltr = "";
								if($customers != "all"){
									$sql = "SELECT * FROM `customer_sales` WHERE `customercode` IN ('$customers') ORDER BY `date`,`invoice` ASC";
									$query = mysqli_query($conn,$sql); $cus_alist = array();
									while($row = mysqli_fetch_assoc($query)){ $cus_alist[$row['invoice']] = $row['invoice']; }
									$ptrno_list = implode("','", $cus_alist);
									if(sizeof($cus_alist) > 0){ $cus_fltr = " AND `link_trnum` IN ('$ptrno_list')"; } else{ $cus_fltr = " AND `link_trnum` IN ('none')"; }
								}

								$sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fromdate' AND `date` <= '$todate'".$cus_fltr." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
								$query = mysqli_query($conn,$sql); $link_trnum = array();
								while($row = mysqli_fetch_assoc($query)){ if($row['link_trnum'] != ""){ $link_trnum[$row['link_trnum']] = $row['link_trnum']; } }
	
								if(sizeof($link_trnum) > 0){
									$ltno_list = implode("','", $link_trnum);
									$sql = "SELECT * FROM `customer_sales` WHERE `invoice` IN ('$ltno_list') ORDER BY `date`,`invoice` ASC";
									$query = mysqli_query($conn,$sql); $ltno_vname = array();
									while($row = mysqli_fetch_assoc($query)){ $ltno_vname[$row['invoice']] = $pname[$row['customercode']]; }
								}	

								$sql = "SELECT * FROM `customer_sales` WHERE `trtype` IN ('PST') ORDER BY `date`,`invoice` ASC";
								$query = mysqli_query($conn,$sql); $sl_trnum = array();
								while($row = mysqli_fetch_assoc($query)){ $sl_trnum[$row['invoice']] = $row['invoice']; }
								$sl_trlist = implode("','", $sl_trnum);

								$tbirds = $tjals = $ttotalweight = $temptyweight = $tnetweight = $tdiscountamt = $ttaxamount = $ttcdsamt = $ttotalamt = 0; $old_inv = ""; $sl = 1;
								if($sltr_flag > 0 ){
								$sequence = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fromdate' AND `date` <= '$todate'".$cus_fltr." AND `link_trnum` NOT IN ('$sl_trlist')";
								} else {
								$sequence = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fromdate' AND `date` <= '$todate'".$cus_fltr;
								}
								$flags = " AND `active` = '1'  AND `tdflag` = '0' AND `pdflag` = '0' $upload_filter  ORDER BY `date`,`invoice` ASC";
								$sql = $sequence."".$cnames."".$inames."".$wnames."".$flags;
								$query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$file_count = 0;


									if($row['purchase_image'] != ""){
										$file_count++;
										$link = ""; $link = "https://".$_SERVER['SERVER_NAME']."/AndroidApp_API/clientimages/".$client.'/purchaseimages/'.str_replace(' ', '%20',$row['purchase_image']); $file_list[$link] = $link;
									}
									echo "<tr>";
									echo "<td style='text-align:left;'>".$sl++."</td>";
									echo "<td>".date("d.m.Y",strtotime($row['date']))."</td>";
									echo "<td style='text-align:left;'>".$ltno_vname[$row['link_trnum']]."</td>";
									echo "<td style='text-align:left;'>".$pname[$row['vendorcode']]."</td>";
									echo "<td style='text-align:left;'>".$row['invoice']."</td>";
									echo "<td style='text-align:left;'>".$row['bookinvoice']."</td>";
									echo "<td style='text-align:left;'>".$itemname[$row['itemcode']]."</td>";
									if($ifjbwen == 1 || $ifjbw == 1){ echo "<td>".str_replace(".00","",number_format_ind($row['jals']))."</td>"; } else {  }
									if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo "<td>".str_replace(".00","",number_format_ind($row['birds']))."</td>"; } else {  }
									if($ifjbwen == 1){ echo "<td>".number_format_ind($row['totalweight'])."</td>"; } else {  }
									if($ifjbwen == 1){ echo "<td>".number_format_ind($row['emptyweight'])."</td>"; } else {  }
									echo "<td>".number_format_ind($row['netweight'])."</td>";
									if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){
										if($row['birds'] == 0 || $row['birds'] == "0.00" || $row['birds'] == ".00" || $row['birds'] == "0"){
											$avg_wt = 0;
										}
										else{
											$avg_wt = $row['netweight'] / $row['birds'];
										}
										echo "<td>".number_format_ind($avg_wt)."</td>";
									}
									if($prate_flag == 1 || $prate_flag == "1"){
										$prate_index = $row['date']."@".$row['itemcode'];
										echo "<td>".number_format_ind($prates[$prate_index])."</td>";
									}
									echo "<td>".number_format_ind($row['itemprice'])."</td>";
									echo "<td>".number_format_ind($row['discountamt'])."</td>";
									echo "<td>".number_format_ind($row['taxamount'])."</td>";
									if($old_inv != $row['invoice']){
										echo "<td>".number_format_ind($row['tcdsamt'])."</td>";
										echo "<td>".number_format_ind($row['totalamt'] + $row['tcdsamt'])."</td>";
										
										$ttcdsamt = $ttcdsamt + $row['tcdsamt'];
										$ttotalamt = $ttotalamt + $row['totalamt'] + $row['tcdsamt'];
										$old_inv = $row['invoice'];
									}
									else{
										echo "<td>0.00</td>";
										echo "<td>".number_format_ind($row['totalamt'])."</td>";
										//$ttcdsamt = $ttcdsamt + $row['tcdsamt'];
										$ttotalamt = $ttotalamt + $row['totalamt'];
									}
									if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td>".$row['vehiclecode']."</td>"; }
									echo "<td style='text-align:left;'>".$officename[$row['warehouse']]."</td>";
									echo "<td style='text-align:left;'>".$row['remarks']."</td>";
									
									if((int)$file_count > 0){ 
								
										echo "<td title='Upload Status' style='text-align:left;' >Uploaded</td>";
									} else{ 
										echo "<td title='Upload Status' style='text-align:left;' >Not Uploaded</td>";
									}
									echo "</tr>";
									
									$tbirds = $tbirds + $row['birds'];
									$tjals = $tjals + $row['jals'];
									$ttotalweight = $ttotalweight + $row['totalweight'];
									$temptyweight = $temptyweight + $row['emptyweight'];
									$tnetweight = $tnetweight + $row['netweight'];
									$tdiscountamt = $tdiscountamt + $row['discountamt'];
									$ttaxamount = $ttaxamount + $row['taxamount'];
									$tavgprice = 0; if((float)$tnetweight != 0){ $tavgprice = $ttotalamt / $tnetweight; }
								}
							?>
								<tr class="foottr" style="background-color: #98fb98;">
									<td colspan="7" align="center"><b>Grand Total</b></td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><?php echo str_replace(".00","",number_format_ind($tjals)); ?></td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><?php echo str_replace(".00","",number_format_ind($tbirds)); ?></td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><?php echo number_format_ind($ttotalweight); ?></td>
									<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><?php echo number_format_ind($temptyweight); ?></td>
									<td><?php echo number_format_ind($tnetweight); ?></td>
									<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><?php if($tnetweight > 0 && $tbirds > 0){ echo number_format_ind($tnetweight / $tbirds); } else{ echo "0"; }?></td>
									<?php if($prate_flag == 1 || $prate_flag == "1"){ echo "<td></td>"; } ?>
									<td><?php echo number_format_ind($tavgprice); ?></td>
									<td><?php echo number_format_ind($tdiscountamt); ?></td>
									<td><?php echo number_format_ind($ttaxamount); ?></td>
									<td><?php echo number_format_ind($ttcdsamt); ?></td>
									<td><?php echo number_format_ind($ttotalamt); ?></td>
									<?php if($vehicle_flag == 1 || $vehicle_flag == "1"){ echo "<td></td>"; } ?>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>

							<?php if($file_list != null){
            
								?>
								<tr>
									<td colspan="21">
										<div class="row">
											<div class="form-group">
												<input type="text" name="download_dt" id="download_dt" value="<?php echo implode("@$&",$file_list); ?>" style="visibility:hidden;" readonly />
												<button type="button" class="btn btn-sm btn-success" onclick="download_files();">Download All</button>
											</div>
										</div>
									</td>
								</tr>
							<?php } ?>
						</table>
					</form>
				</div>
		</section>
		
		<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer><?php } ?>
		<script> 
	function download_files(){
                var download_dt = document.getElementById("download_dt").value;
                var download_list = download_dt.split("@$&");

                //alert(download_list.length);

                var string_val = ""; var file_dt = []; var file_id = 0;
                for(var i = 0;i < download_list.length;i++){

                    console.log( i + download_list[i]);
                    
                   /* var temporaryDownloadLink = document.createElement("a");
                    document.body.appendChild( temporaryDownloadLink );
                    temporaryDownloadLink.setAttribute( 'href', download_list[i] );
                    file_dt = []; file_dt = download_list[i].split("/");
                    file_id = 0; file_id = file_dt.length - 1;
                    temporaryDownloadLink.setAttribute( 'download', file_dt[file_id]);
                    temporaryDownloadLink.click();
                    document.body.removeChild( temporaryDownloadLink );*/

                    file_dt = []; file_dt = download_list[i].split("/");
                    file_id = 0; file_id = file_dt.length - 1;

                    downFn(download_list[i],file_dt[file_id]);
                    
                }
            }
            function downFn(url,filename) {
                const pattern = /^(ftp|http|https):\/\/[^ "]+$/;
                if (!pattern.test(url)) {
                   // errMsg.textContent = "Wrong URL Entered";
                    //dBtn.innerText = "Download File";
                    alert("Wrong URL Entered");
                    return;
                }
                //errMsg.textContent = "";
                fetch(url,{mode: 'no-cors'})
                    .then((res) => {
                    if (!res.ok) {
                        throw new Error("Network Problem");
                    }
                    return res.blob();
                    })
                    .then((file) => {
                    const ex = extFn(url);
                    let tUrl = URL.createObjectURL(file);
                    const tmp1 = document.createElement("a");
                    tmp1.href = tUrl;
                    tmp1.download = filename;
                    document.body.appendChild(tmp1);
                    tmp1.click();
                    //dBtn.innerText = "Download File";
                    URL.revokeObjectURL(tUrl);
                    tmp1.remove();
                    })
                    .catch(() => {
                   /* errMsg.textContent = 
                        "Cannot Download Restricted Content!";
                    dBtn.innerText = "Download File";*/
                    console.log("Cannot Download Restricted Content!");
                    });
            }
            function extFn(url) {
            const match = url.match(/\.[0-9a-z]+$/i);
            return match ? match[0].slice(1) : "";
            }
	
	</script>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
