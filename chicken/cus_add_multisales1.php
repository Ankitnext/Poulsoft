<?php
    //cus_add_multisales1.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$emp_code = $_SESSION['userid'];
	$sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$emp_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cgroup_access = $row['cgroup_access']; $loc_access = $row['loc_access']; $slae_rate_edit_flag = $row['slae_rate_edit_flag']; }
	if($loc_access == "all" || $loc_access == "" || $loc_access == NULL){
		$warehouse_codes = "";
	}
	else{
		$whs_code = "";
		$crp_codes = explode(",",$loc_access);
		foreach($crp_codes as $whs){
			if($whs_code == ""){
				$whs_code = $whs;
			}
			else{
				$whs_code = $whs_code."','".$whs;
			}
		}
		if($whs_code != ""){
			$warehouse_codes = " AND `code` IN ('$whs_code')";
		}
		else{
			$warehouse_codes = "";
		}
	}
	if($cgroup_access == "all" || $cgroup_access == "" || $cgroup_access == NULL){
		$cgroup_codes = "";
	}
	else{
		$crp_code = "";
		$crp_codes = explode(",",$cgroup_access);
		foreach($crp_codes as $cgrps){
			if($crp_code == ""){
				$crp_code = $cgrps;
			}
			else{
				$crp_code = $crp_code."','".$cgrps;
			}
		}
		if($crp_code != ""){
			$cgroup_codes = " AND `groupcode` IN ('$crp_code')";
		}
		else{
			$cgroup_codes = "";
		}
	}
	if($slae_rate_edit_flag == "" || $slae_rate_edit_flag != 1){ $slae_rate_edit_flag = 0; }
	$sql = "SELECT * FROM `main_reportfields` WHERE `field` LIKE 'Add Multiple Sales' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	
	while($row = mysqli_fetch_assoc($query)){ $vehicle_flag = $row['vehicle_flag']; $vehicle_row_flag = $row['vehicle_row_flag']; }
	if($vehicle_flag == "" || $vehicle_flag == 0){ $vehicle_flag = 0; }
	if($vehicle_row_flag == "" || $vehicle_row_flag == 0){ $vehicle_row_flag = 0; }
	
	$box_count = $box_wt = 0;
	$sql = "SELECT * FROM `main_jals` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
	if($jcount > 0){ while($row = mysqli_fetch_assoc($query)){ $box_wt = $row['weight']; $box_count = $row['box_count']; } } else{ $empty_count = 0; }
	
	$field = $date_as = $inv_as = $cus_as = $binv_as = $item_as = $jals_as = $birds_as = $twt_as = $ewt_as = $nwt_as = $avgwt_as = $price_as = $tamt_as = $sector_as = $tcds_as = $vehicle_as = $driver_as = $famt_as = $remarks_as = $srct_as = $user_as = $basv_flag = "";
	$fname_sql = "SELECT * FROM `main_displayfieldname` WHERE `field` LIKE 'MSLS' AND `active` = '1'"; $fname_query = mysqli_query($conn,$fname_sql); $mcount = mysqli_num_rows($fname_query);
	while($row = mysqli_fetch_assoc($fname_query)){
		$field = $row['field'];
		$date_as = $row['date_as'];
		$inv_as = $row['inv_as'];
		$cus_as = $row['cus_as'];
		$binv_as = $row['binv_as'];
		$item_as = $row['item_as'];
		$jals_as = $row['jals_as'];
		$birds_as = $row['birds_as'];
		$twt_as = $row['twt_as'];
		$ewt_as = $row['ewt_as'];
		$nwt_as = $row['nwt_as'];
		$avgwt_as = $row['avgwt_as'];
		$price_as = $row['price_as'];
		$tamt_as = $row['tamt_as'];
		$sector_as = $row['sector_as'];
		$tcds_as = $row['tcds_as'];
		$vehicle_as = $row['vehicle_as'];
		$driver_as = $row['driver_as'];
		$famt_as = $row['famt_as'];
		$remarks_as = $row['remarks_as'];
		$srct_as = $row['srct_as'];
		$user_as = $row['user_as'];
		$basv_flag = $row['basv_flag'];
	}
	$sql = "SELECT * FROM `master_itemfields` WHERE `flag` = '1' AND (`sales_sms` = '1' || `sales_wapp` = '1')"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
    if($ccount > 0){ 
        $sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'WAPP-MSG' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
    }
	else{
		$sql = "SELECT * FROM `master_loadingscreen` WHERE `project` LIKE 'CTS' AND `type` LIKE 'SAVE-DATA' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $loading_title = $row['title']; $loading_stitle = $row['sub_title']; }
	}

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Sale Transaction' AND `field_function` LIKE 'Multiple Sale-1: DC No and Stock Fetch from Purchase' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $pur_dcf_flag = mysqli_num_rows($query);

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'cus_displaymultisales.php' AND `field_function` LIKE 'Display Customer Balance' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $bal_flag = mysqli_num_rows($query);

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Sale Transaction' AND `field_function` LIKE 'Display: Invoice Number'";
	$query = mysqli_query($conn,$sql); $cnt = mysqli_num_rows($query);
	if((int)$cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $dinv_flag = $row['flag']; } } else{ $sql = "INSERT INTO `extra_access` (`id`, `field_name`, `field_function`, `field_value`, `user_access`, `flag`) VALUES (NULL, 'Sale Transaction', 'Display: Invoice Number', NULL, 'all', '0');"; mysqli_query($conn,$sql); $dinv_flag = 0; }
	if($dinv_flag == ""){ $dinv_flag = 0; }

	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Sale Transaction' AND `field_function` LIKE 'Multiple Sale-1: Supplier Selection' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $msiss_flag = mysqli_num_rows($query);
	$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Multiple Sales' AND `field_function` LIKE 'Provide Transportation Charges as a crdr note' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $tcdr_flag = mysqli_num_rows($query);

	  $sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Multiple Sales' AND `field_function` LIKE 'calculate birds from jals' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $jbird_cflag = mysqli_num_rows($query);

	$sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%Broiler Bird%' AND `active` = '1'";
	$query = mysqli_query($conn,$sql); $bird_code = "";
	while($row = mysqli_fetch_assoc($query)){ $bird_code = $row['code']; }

    $sql = "SELECT * FROM `Item_wise_jbirds_count` WHERE `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $jbcnt = array(); $bb_cnt = 0;
    while($row = mysqli_fetch_assoc($query)){ if($bird_code == $row['item_code']){ $bb_cnt = $row['birds_cnt']; } $jbcnt[$row['item_code']] = $row['birds_cnt']; }
	
?>
<html>
	<head>
		<!--<link rel="stylesheet" type="text/css" href="loading_screen.css">-->
		<style>
			.select2-container .select2-selection--single{ box-sizing:border-box; cursor:pointer; display:block; height:23px; user-select:none; -webkit-user-select:none; }
			.select2-container--default .select2-selection--single{background-color:#fff;border:1px solid #aaa;border-radius:4px}
			.select2-container--default .select2-selection--single .select2-selection__rendered{color:#444;line-height:18px}
			.select2-container--default .select2-selection--single .select2-selection__clear{cursor:pointer;float:right;font-weight:bold}
			.select2-container--default .select2-selection--single .select2-selection__placeholder{color:#999}
			.select2-container--default .select2-selection--single .select2-selection__arrow{height:23px;position:absolute;top:1px;right:1px;width:20px}
			.select2-container--default .select2-selection--single .select2-selection__arrow b{border-color:#888 transparent transparent transparent;border-style:solid;border-width:5px 4px 0 4px;height:0;left:50%;margin-left:-4px;margin-top:-2px;position:absolute;top:50%;width:0}
			.form-control { width: 85%; height: 23px; }
			label { line-height: 20px; }
			.disabledbutton{ pointer-events: none; opacity: 0.4; }
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Create Multi-Sales</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Multi-Sales</a></li>
				<li class="active">Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$today = date("Y-m-d");
				if((int)$dinv_flag == 1){
					$sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$today' AND `tdate` >= '$today'"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }

					$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$today' AND `tdate` >= '$today' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ $sales = $row['sales']; } $iincr = $incr = $sales + 1;

					if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
					$code = "S".$pfx."-".$incr;
				}

				$fdate = date("d.m.Y",strtotime($today));
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$itype[$row['code']] = $row['code'];
					$itypes[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `main_officetypes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Warehouse"){ if($branches == ""){ $branches = $row['code']; } else{ $branches = $branches."','".$row['code']; } } }
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$warehouse_codes." AND `type` IN ('$branches') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$spzflag = $row['spzflag'];
					$ifwt = $row['wt'];
					$ifbw = $row['bw'];
					$ifjbw = $row['jbw'];
					$ifjbwen = $row['jbwen'];
					$ifctype = $row['ctype'];
					$ejals_flag = $row['ejals_flag'];
					$msale_prate_flag = $row['msale_prate_flag'];
					$sup_mnuname_flag = $row['description'];
				}
				if($sup_mnuname_flag == ""){ $sup_mnuname_flag = 0; }
				if($spzflag == "" || $spzflag == 0 || $spzflag == NULL){ $spzflag = 0; } else{ }

				$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%'".$user_cusgrp_filter." ORDER BY `description` ASC";
				$query = mysqli_query($conn,$sql); $grp_code = $grp_name = array();
				while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }
			
				$grp_list = implode("','",$grp_code);
				$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$cgroup_codes." AND `groupcode` IN ('$grp_list') AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$cus_code[$row['code']] = $row['code'];
					$cus_name[$row['code']] = $row['name'];
					$cus_group[$row['code']] = $row['groupcode'];
				}
				
				$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%'".$cgroup_codes." AND `active` = '1' ORDER BY `name` ASC";
				$query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
				while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }
				
				$idisplay = 'style="width: 80px;padding-right:10px;"';  $ndisplay = 'style="display:none;';
			?>
			
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18">
								<form action="cus_save_multisales1.php" method="post" role="form" onsubmit="return checkval()">
									<div class="form-group col-md-1"  style="width: 100px; text-align:Left;">
										<label><?php if($date_as != ""){ echo $date_as; } else { echo 'Date'; } ?><b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width: 90px; text-align:Left;" class="form-control" name="pdate" value="<?php echo $fdate; ?>" id="slc_datepickers" <?php if((int)$pur_dcf_flag == 1){ echo 'onchange="fetch_pur_dcnos();"'; } ?> readonly>
									</div>
									<div class="form-group col-md-2"  style="width:230px;text-align:Left;">
										<label><?php if($sector_as != ""){ echo $sector_as; } else { echo 'Warehouse/Vehicle'; } ?><b style="color:red;">&nbsp;*</b></label>
										<select name="wcodes" id="wcodes" class="form-control select2" style="width:220px;text-align:Left;" <?php if((int)$pur_dcf_flag == 1){ echo 'onchange="fetch_pur_dcnos();"'; } ?>>
											<?php foreach($wcode as $it){ ?><option value="<?php echo $wcode[$it]; ?>"><?php echo $wdesc[$it]; ?></option><?php } ?>
										</select>
									</div>
                                    <div class="form-group col-md-2">
                                        <label for="groups">Group</label>
                                        <select name="groups" id="groups" class="form-control select2" onchange="filter_group_customers(this.id);">
                                            <option value="all" <?php if($groups == "all"){ echo "selected"; } ?>>All</option>
											<?php foreach($grp_code as $gcode){ ?><option value="<?php echo $gcode; ?>" <?php if($groups == $gcode){ echo "selected"; } ?>><?php echo $grp_name[$gcode]; ?></option><?php } ?>
                                        </select>
                                    </div>
									<?php if((int)$msiss_flag == 1 && (int)$sup_mnuname_flag == 0){ ?>
									<div class="form-group col-md-2"  style="width:230px;text-align:Left;">
										<label>Supplier</label>
										<select name="sup_code" id="sup_code" class="form-control select2" style="width:220px;text-align:Left;">
											<option value="select">-select-</option>
											<?php foreach($sup_code as $scode){ ?><option value="<?php echo $scode; ?>"><?php echo $sup_name[$scode]; ?></option><?php } ?>
										</select>
									</div>
									<?php } else if((int)$sup_mnuname_flag == 1){ ?>
									<div class="form-group col-md-1" style="width: 230px; text-align:Left;">
										<label>Supplier</label>
										<input type="text" name="sup_mnu_name" id="sup_mnu_name" class="form-control" style="width: 220px;" />
									</div>
									<?php } ?>
									<?php if((int)$pur_dcf_flag == 1){ ?>
									<div class="form-group col-md-2"  style="width: 120px; text-align:Left;">
										<label><?php if($binv_as != ""){ echo $binv_as; } else { echo 'Invoice No.'; } ?></label>
										<select name="binv" id="binv" class="form-control select2" style="width: 110px; text-align:Left;" onchange="fetch_pur_stock();">
											<option value="select">-select-</option>
										</select>
									</div>
									<div class="form-group col-md-1" style="width: 100px; text-align:Left;">
										<label>Birds</label>
										<input type="text" style="width: 90px; text-align:Right;" name="avl_birds" id="avl_birds" class="form-control text-right" readonly />
									</div>
									<div class="form-group col-md-1" style="width: 100px; text-align:Left;">
										<label>Weight</label>
										<input type="text" style="width: 90px; text-align:Right;" name="avl_weight" id="avl_weight" class="form-control text-right" readonly />
									</div>
									<?php } else{ ?>
									<div class="form-group col-md-2" style="width: 120px; text-align:Left;">
										<label><?php if($binv_as != ""){ echo $binv_as; } else { echo 'Invoice No.'; } ?></label>
										<input type="text"  style="width: 110px; text-align:Left;"  class="form-control" name="binv" id="binv">
									</div>
									<?php } ?>
									<?php if($vehicle_flag == 1){ ?>
									<div class="form-group col-md-2"  style="width: 100px; text-align:Left;">
										<label><?php if($vehicle_as != ""){ echo $vehicle_as; } else { echo 'Vehicle No.'; } ?></label>
										<input type="text" style="width: 90px; text-align:Left;"  class="form-control" name="vehicleno" id="vehicleno">
									</div>
									<?php } ?>
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>incr<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>incrs<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="incrs" id="incrs" value="0">
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>Enter Count<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>II<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="iincr" id="iincr" value="<?php echo $iincr; ?>">
									</div>
									<div class="col-md-12">
										<table style="line-height:30px;" id="tab3">
											<tr style="line-height:30px;">
												<?php if((int)$dinv_flag == 1){ echo '<th style="width: 150px;padding-right:10px;"><label>Invoice</label></th>'; } ?>
												<th style="width: 150px;padding-right:10px;"><label>Customer<b style="color:red;">&nbsp;*</b></label></th>
												<?php if((int)$bal_flag == 1){ echo '<th style="width: 150px;padding-right:10px;"><label>Balance</label></th>'; } ?>
												<th style="width: 120px;padding-right:10px;"><label>Item<b style="color:red;">&nbsp;*</b></label></th>
												<?php
													if($ifjbwen == 1 || $ifjbw == 1){
														echo "<th style= 'width: 80px;padding-right:10px;'><label>Jals</label></th>";
													}
													if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){
														echo "<th style= 'width: 80px;padding-right:10px;'><label>Birds</label></th>";
													}
													if($ifjbwen == 1){
														echo "<th style= 'width: 80px;padding-right:10px;'><label>T. Weight<b style='color:red;'>&nbsp;*</b></label></th>";
														echo "<th style= 'width: 80px;padding-right:10px;'><label>E. Weight</label></th>";
													}
												?>
												<th  style="width: 80px;padding-right:10px;"><label>N. Weight<b style="color:red;">&nbsp;*</b></label></th>
												<th  style="width: 80px;padding-right:10px;"><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th  style="width: 100px;padding-right:10px;"><label>Amount</label></th>
												<?php if((int)$tcdr_flag > 0){ echo '<th  style="width: 100px;padding-right:10px;"><label>T. Cost</label></th>'; } ?>
												<?php if($vehicle_row_flag == 1){ echo "<th  style= 'width: 80px;padding-right:10px;'><label>Vehicle</label></th>"; } ?>
												<th  style="width: 80px;padding-right:10px;"> <label>Remarks</label></th>
												<th></th>
												<!--<th><label>Outstanding<b style="color:red;">&nbsp;*</b></label</th>>-->
											</tr>
											<tr style="margin:5px 0px 5px 0px;" id="row_id[0]">
												<?php if((int)$dinv_flag == 1){ echo '<td style="width: 150px;padding-right:10px;">'.$code.'</td>'; } ?>
												<td style="width: 150px;padding-right:10px;"><select name="cnames[]" id="cnames[0]" class="form-control select2"  style="width: 150px;" onchange="fetchoutstanding(this.id);fetchprice(this.id);fetchbalance(this.id);"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
												<?php if($bal_flag == 1){ echo '<td  style= "width: 80px;padding-right:10px;"><input type="text" style="width: 80px;" name="balc[]" id="balc[0]" class="form-control" /></td>'; } ?>
												<td style="width: 120px;padding-right:10px;"><select name="scat[]" id="scat[0]" class="form-control select2"  style="width: 120px;" onchange="calculatetotal(this.id);fetchprice(this.id);fetch_jbcnt(this.id);"><?php foreach($itype as $ic){ ?><option value="<?php echo $itype[$ic]."@".$itypes[$ic]; ?>"><?php echo $itypes[$ic]; ?></option><?php } ?></select></td>

												<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" style="width: 80px;" name="jval[]" id="jval[0]" value="" class="form-control" onchange="validatebirds(this.id);calculatetotal(this.id);calfinaltotal();emptyval();" onkeyup="validatebirds(this.id);calculate_birds(this.id);" /></td>

												<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" style="width: 80px;" name="bval[]" id="bval[0]" value="" class="form-control" onkeyup="validatebirds(this.id);" onchange="validatebirds(this.id);calculatetotal(this.id);calfinaltotal();" /></td>

												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" style="width: 80px;" name="wval[]" id="wval[0]" value="" class="form-control" onchange="validateamount(this.id)" onkeyup="validatenum(this.id);calculatetotal(this.id);calnetweight(this.id);calfinaltotal();" /></td>

												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" style="width: 80px;" name="ewval[]" id="ewval[0]" value="" class="form-control" onkeyup="validatenum(this.id);calnetweight(this.id);" onchange="validateamount(this.id);calculatetotal(this.id);calfinaltotal();" /></td>

												<td style="width: 80px;padding-right:10px;"><input type="text" name="nwval[]" id="nwval[0]" style="width: 80px;" value="" class="form-control" onchange="validateamount(this.id);" onkeyup="validatenum(this.id);calculatetotal(this.id);calfinaltotal();" /></td>
												<td style="width: 80px;padding-right:10px;"><input type="text" name="iprice[]" id="iprice[0]" style="width: 80px;" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" onkeyup="validatenum(this.id);calculatetotal(this.id);calfinaltotal();" <?php if($slae_rate_edit_flag == 0){ echo "readonly"; } ?> ></td>
												<td style="width: 100px;padding-right:10px;"><input type="text" name="tamt[]" id="tamt[0]" style="width: 100px;" class="form-control" onchange="validateamount(this.id);calfinaltotal();" readonly></td>
												<?php if((int)$tcdr_flag > 0){ ?><td style="width: 100px;padding-right:10px;"><input type="text" name="tcost[]" id="tcost[0]" style="width: 100px;" class="form-control"></td><?php } ?>
												<?php if($vehicle_row_flag == 1){ echo '<td  style= "width: 80px;padding-right:10px;"><input type="text" style="width: 80px;" name="vehiclerno[]" id="vehiclerno[0]" class="form-control" /></td>'; } ?>
												<td style="width: 80px;padding-right:10px;"><textarea style="width: 90px;height:23px;" name="narr[]" id="narr[0]" class="form-control" style="height:23px;"></textarea></td>
												<td style="width: 50px;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes(this.id)" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
												<td style="visibility:hidden;"><input type="text" class="form-control" name="jbird_cnt[]" id="jbird_cnt[0]" style="width:50px;" readonly /></td>
											</tr>
										</table><br/>
										
										<?php 
											  if ( $ifjbwen == 1 )
											  { ?>
											
										<div class="col-md-12" align="left" style="padding-left:290px">
											<?php }
											  else if ( $ifjbwen != 1 &&  $ifjbw == 1 &&  $ifbw != 1 ) 
											  { ?>
											
										<div class="col-md-12" align="left" style="padding-left:290px">
											<?php }
											  else if ( $ifjbwen != 1 &&  $ifjbw != 1 &&  $ifbw == 1 )
											  {  $bil_pad = $bil_pad + 110; ?>
											  
										<div class="col-md-12" align="left" style="padding-left:290px">
											
											
											<?php } 
											 else if ( $ifjbwen != 1 &&  $ifjbw == 1 &&  $ifbw == 1 )
											 {  ?>
											 
									   <div class="col-md-12" align="left" style="padding-left:290px">
										   
										   
										   <?php } 
											else { ?>
											
										<div class="col-md-12" align="left" style="padding-left:290px">
										<?php } ?>

										<table style="line-height:30px;"> 
											<tr style="line-height:30px;">
												<th></th>
												<th></th>
												<?php
												if($ifjbwen == 1 || $ifjbw == 1){ echo '<th><label>Total Jals</label></th>'; }
												if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo '<th><label>Total Birds</label></th>'; }
												if($ifjbwen == 1){ echo '<th><label>Total T. Weight</label></th>'; }
												if($ifjbwen == 1){ echo '<th><label>Total E. Weight</label></th>'; }
												?>
												<th><label>Total N. Weight</label></th>
												<th  style="width: 80px;padding-right:10px;"><label>Avg. Price</label></th>
												<th  style="width: 100px;padding-right:10px;"><label><?php if($famt_as != ""){ echo $famt_as; } else { echo 'Total Amount'; } ?></label></th>
												<?php if($vehicle_row_flag == 1){ echo '<th></th>'; } ?>
												<th></th>
												<th></th>
											</tr>
											<tr style="margin:5px 0px 5px 0px;">
												<td></td>
												<td></td>
												<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_jval" id="tot_jval" style="width: 80px;" class="form-control" readonly /></td>

												<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" style="width: 80px;" name="tot_bval" id="tot_bval" class="form-control" readonly /></td>

												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_wval" id="tot_wval" style="width: 80px;" class="form-control" readonly /></td>

												<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="tot_ewval" id="tot_ewval" style="width: 80px;" class="form-control" readonly /></td>
												<td  style="width: 80px;padding-right:10px;"><input type="text" name="tot_nwval" style="width: 80px;" id="tot_nwval" class="form-control" readonly /></td>
												<td  style="width: 80px;padding-right:10px;"><input type="text" name="avg_price" style="width: 80px;" id="avg_price" class="form-control" readonly></td>
												<td  style="width: 100px;padding-right:10px;"><input type="text" name="tot_tamt" style="width: 100px;" id="tot_tamt" class="form-control" readonly></td>
												<?php if($vehicle_row_flag == 1){ echo '<td style="width: auto;"></td>'; } ?>
												<td style="width: auto;"></td>
												<td style="width: 60px;"></td>
												<!--<td style="visibility:hidden;"><input type="text" class="form-control" name="outstanding[]" id="outstanding[0]" style="width:50px;"></td>-->
											</tr>
										<table>
											</div>
										<div class="col-md-12" align="left">
											<div class="col-md-4" style="width:auto;visibility:hidden;">
												<label>Item Field Type</label>
												<input type="text" name="itemfields" id="itemfields" class="form-control" value="<?php if($ifwt == 1){ echo "WT"; } else if($ifbw == 1){ echo "BAW"; } else if($ifjbw == 1){ echo "JBEW"; } else if($ifjbwen == 1){ echo "JBTEN"; } else { echo "WT"; } ?>" >
											</div>
											<div class="col-md-4" style="width:auto;visibility:hidden;">
												<label>Amount Based</label>
												<input type="text" name="amountbasedon" id="amountbasedon" class="form-control" value="<?php echo $ifctype; ?>" >
											</div>
										</div>
										<div class="box-body" align="center">
											<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">
												<i class="fa fa-save"></i> Save
											</button>&ensp;&ensp;&ensp;&ensp;
											<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
												<i class="fa fa-trash"></i> Cancel
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--<div class="ring"><?php //echo $loading_title; ?><span></span></div>
			<div class="ring_status" id = "disp_val"></div>-->
		</section>
		<?php include "header_foot.php"; ?>
		<script>
			function checkval(){
				var a = document.getElementById("itemfields").value;
				var b = document.getElementById("incr").value;
				document.getElementById("ebtncount").value = "1";
				document.getElementById("submittrans").style.visibility = "hidden";
				var sale_price_flag = '<?php echo $spzflag; ?>';
				
				var aa = document.getElementById("amountbasedon").value;
				for(var ab=0;ab<=b;ab++){
					if(aa.match("B") || aa.match("b")){
						var ac = document.getElementById("bval["+ab+"]").value;
					}
					else {
						var ac = document.getElementById("nwval["+ab+"]").value;
					}
					var ad = document.getElementById("iprice["+ab+"]").value; if(ad == ""){ ad = 0; }
					var ae = ac * ad;
					document.getElementById("tamt["+ab+"]").value = ae.toFixed(2);
				}
				var l = true;
					if(a.match("WT")){
						for(var j=0;j<=b;j++){
							if(l == true){
								var c = document.getElementById("scat["+j+"]").value;
								var k = j; k++;
								var g = document.getElementById("nwval["+j+"]").value; if(g == ""){ g = 0; }
								var h = document.getElementById("iprice["+j+"]").value; if(h == ""){ h = 0; }
								var n = document.getElementById("cnames["+j+"]").value;
								var r = document.getElementById("tamt["+j+"]").value;
								if(n.match("select")){
									alert("Please select Name in row: "+k);
									l = false;
								}
								else if(c.match("select")){
									alert("Please select Item description in row: "+k);
									document.getElementById("cnames["+j+"]").focus();
									l = false;
								}
								else if(g.length == 0 || g == 0 || g == ""){
									alert("Please Enter the net weight in row: "+k);
									document.getElementById("scat["+j+"]").focus();
									l = false;
								}
								else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
									alert("Please Enter the price in row: "+k);
									document.getElementById("iprice["+j+"]").focus();
									l = false;
								}
								else if(r.length == 0 && sale_price_flag == 0 || r == 0 && sale_price_flag == 0 || r == "" && sale_price_flag == 0){
									alert("Please Re-Enter the price again to get the amount in row: "+k);
									document.getElementById("iprice["+j+"]").focus();
									l = false;
								}
								else {
									l = true;
								}
							}
							else {
								l = false;
							}
						}
					}
					else if(a.match("BAW")){
						for(var j=0;j<=b;j++){
							if(l == true){
								var c = document.getElementById("scat["+j+"]").value;
								var k = j; k++;
								//var d = document.getElementById("bval["+j+"]").value;
								var g = document.getElementById("nwval["+j+"]").value; if(g == ""){ g = 0; }
								var h = document.getElementById("iprice["+j+"]").value; if(h == ""){ h = 0; }
								var r = document.getElementById("tamt["+j+"]").value;
								var m = c.search(/Birds/i);
								if(m > 0){
									var n = document.getElementById("cnames["+j+"]").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										document.getElementById("cnames["+j+"]").focus();
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										document.getElementById("scat["+j+"]").focus();
										l = false;
									}
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										document.getElementById("nwval["+j+"]").focus();
										l = false;
									}
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else if(r.length == 0 && sale_price_flag == 0 || r == 0 && sale_price_flag == 0 || r == "" && sale_price_flag == 0){
										alert("Please Re-Enter the price again to get the amount in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else {
										l = true;
									}
								}
								else {
									var n = document.getElementById("cnames["+j+"]").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										document.getElementById("cnames["+j+"]").focus();
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										document.getElementById("scat["+j+"]").focus();
										l = false;
									}
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										document.getElementById("nwval["+j+"]").focus();
										l = false;
									}
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else if(r.length == 0 && sale_price_flag == 0 || r == 0 && sale_price_flag == 0 || r == "" && sale_price_flag == 0){
										alert("Please Re-Enter the price again to get the amount in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else {
										l = true;
									}
								}
							}
							else {
								l = false;
							}
						}
					}
					else if(a.match("JBEW")){
						for(var j=0;j<=b;j++){
							if(l == true){
								var c = document.getElementById("scat["+j+"]").value;
								var k = j; k++;
								//var e = document.getElementById("jval["+j+"]").value;
								var g = document.getElementById("nwval["+j+"]").value; if(g == ""){ g = 0; }
								var h = document.getElementById("iprice["+j+"]").value; if(h == ""){ h = 0; }
								var r = document.getElementById("tamt["+j+"]").value;
								var m = c.search(/Birds/i);
								if(m > 0){
									var n = document.getElementById("cnames["+j+"]").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										document.getElementById("cnames["+j+"]").focus();
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										document.getElementById("scat["+j+"]").focus();
										l = false;
									}
									/*else if(e.length == 0 || e == ""){
										alert("Please select No. of Jals in row: "+k);
										document.getElementById("jval["+j+"]").focus();
										l = false;
									}*/
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										document.getElementById("nwval["+j+"]").focus();
										l = false;
									}
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else if(r.length == 0 && sale_price_flag == 0 || r == 0 && sale_price_flag == 0 || r == "" && sale_price_flag == 0){
										alert("Please Re-Enter the price again to get the amount in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else {
										l = true;
									}
								}
								else {
									var n = document.getElementById("cnames["+j+"]").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										document.getElementById("cnames["+j+"]").focus();
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										document.getElementById("scat["+j+"]").focus();
										l = false;
									}
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										document.getElementById("nwval["+j+"]").focus();
										l = false;
									}
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else if(r.length == 0 && sale_price_flag == 0 || r == 0 && sale_price_flag == 0 || r == "" && sale_price_flag == 0){
										alert("Please Re-Enter the price again to get the amount in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else {
										l = true;
									}
								}
							}
							else {
								l = false;
							}
						}
					}
					else if(a.match("JBTEN")){
						for(var j=0;j<=b;j++){
							if(l == true){
								var c = document.getElementById("scat["+j+"]").value;
								var k = j; k++;
								//var e = document.getElementById("jval["+j+"]").value;
								//var f = document.getElementById("wval["+j+"]").value;
								//var p = document.getElementById("ewval["+j+"]").value;
								var g = document.getElementById("nwval["+j+"]").value; if(g == ""){ g = 0; }
								var h = document.getElementById("iprice["+j+"]").value; if(h == ""){ h = 0; }
								var r = document.getElementById("tamt["+j+"]").value;
								var m = c.search(/Birds/i);
								if(m > 0){
									var n = document.getElementById("cnames["+j+"]").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										document.getElementById("cnames["+j+"]").focus();
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										document.getElementById("scat["+j+"]").focus();
										l = false;
									}
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										document.getElementById("nwval["+j+"]").focus();
										l = false;
									}
									/*else if(e.length == 0 || e == ""){
										alert("Please select No. of Jals in row: "+k);
										document.getElementById("jval["+j+"]").focus();
										l = false;
									}
									else if(f.length == 0 || f == 0 || f == ""){
										alert("Please Enter the Total weight in row: "+k);
										document.getElementById("wval["+j+"]").focus();
										l = false;
									}
									else if(p.length == 0 || p == ""){
										alert("Please Enter the Empty weight in row: "+k);
										document.getElementById("ewval["+j+"]").focus();
										l = false;
									}*/
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else if(r.length == 0 && sale_price_flag == 0 || r == 0 && sale_price_flag == 0 || r == "" && sale_price_flag == 0){
										alert("Please Re-Enter the price again to get the amount in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else {
										l = true;
									}
								}
								else {
									var n = document.getElementById("cnames["+j+"]").value;
									if(n.match("select")){
										alert("Please select Name in row: "+k);
										document.getElementById("cnames["+j+"]").focus();
										l = false;
									}
									else if(c.match("select")){
										alert("Please select Item description in row: "+k);
										document.getElementById("scat["+j+"]").focus();
										l = false;
									}
									else if(g.length == 0 || g == 0 || g == ""){
										alert("Please Enter the net weight in row: "+k);
										document.getElementById("nwval["+j+"]").focus();
										l = false;
									}
									else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
										alert("Please Enter the price in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else if(r.length == 0 && sale_price_flag == 0 || r == 0 && sale_price_flag == 0 || r == "" && sale_price_flag == 0){
										alert("Please Re-Enter the price again to get the amount in row: "+k);
										document.getElementById("iprice["+j+"]").focus();
										l = false;
									}
									else {
										l = true;
									}
								}
							}
							else {
								l = false;
							}
						}
					}
					else {
						return false;
					}
				
				if(l == true){
					/*document.getElementsByClassName("ring")[0].style.display = "block";
					document.getElementsByClassName("ring_status")[0].style.display = "block";
					document.getElementById("disp_val").innerHTML = '<?php //echo $loading_stitle; ?>';*/
					return true;
				}
				else{
					/*document.getElementsByClassName("ring")[0].style.display = "none";
					document.getElementsByClassName("ring_status")[0].style.display = "none";
					document.getElementById("disp_val").innerHTML = "";*/

					document.getElementById("ebtncount").value = "0"; document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
			}
			function redirection_page(){
				window.location.href = "cus_displaymultisales.php";
			}
			function validatename(x) {
				expr = /^[a-zA-Z0-9 (.&)_-]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 50){
					a = a.substr(0,a.length - 1);
				}
				if(!a.match(expr)){
					a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, '');
				}
				document.getElementById(x).value = a;
			}
			function chktype(){
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("stype["+a+"]").value;
				if(b.match("Birds")){
					document.getElementById("bval["+a+"]").value = '1';
					document.getElementById("wval["+a+"]").value = '1';
				}
				else {
					document.getElementById("qval["+c+"]").value = '1';
				}
			}
			function rowgen(a){
                var b = a.split("["); var d = b[1].split("]"); var c = d[0];
                if(parseInt(c) == 0){
                    document.getElementById("addval["+c+"]").style.visibility = "hidden";
                }
                else{
                    document.getElementById("addval["+c+"]").style.visibility = "hidden";
                    document.getElementById("rmval["+c+"]").style.visibility = "hidden";
                }
				c++;
                document.getElementById("incrs").value = c;

				//Display Invoice
				var dinv_flag = '<?php echo $dinv_flag; ?>';
				var iincr = document.getElementById("iincr").value;
				iincr = parseInt(iincr) + parseInt(c);
				var prx = '<?php echo $pfx; ?>';
				var bal_flag = '<?php echo $bal_flag; ?>';
				var tcdr_flag = '<?php echo $tcdr_flag; ?>'; if(tcdr_flag == ""){ tcdr_flag = 0; }
				if(parseInt(iincr) < 10){ iincr = '000'+parseInt(iincr); } else if(parseInt(iincr) >= 10 && parseInt(iincr) < 100){ iincr = '00'+parseInt(iincr); } else if(parseInt(iincr) >= 100 && parseInt(iincr) < 1000){ iincr = '0'+parseInt(iincr); } else { }
				var code = "S"+prx+"-"+iincr;

				var html = '';
				html+= '<tr style="margin:5px 0px 5px 0px;" id="row_id['+c+']">';
				if(parseInt(dinv_flag) == 1){
					html += '<td style="width: 150px;padding-right:10px;">'+code+'</td>'
				}
				html+= '<td style="width: 150px;padding-right:10px;"><select name="cnames[]" id="cnames['+c+']" class="form-control select" style="width: 150px;" onchange="fetchoutstanding(this.id);fetchprice(this.id);fetchbalance(this.id);"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>';

				if(parseInt(bal_flag) > 0){ html+= '<td style="width: 100px;padding-right:10px;"><input type="text" name="balc[]" id="balc['+c+']" style="width: 100px;" class="form-control"></td>'; }
				html+= '<td style="width: 120px;padding-right:10px;"><select name="scat[]" id="scat['+c+']" class="form-control select" style="width: 120px;" onchange="calculatetotal(this.id);fetchprice(this.id);fetch_jbcnt(this.id);"><?php foreach($itype as $ic){ ?><option value="<?php echo $itype[$ic]."@".$itypes[$ic]; ?>"><?php echo $itypes[$ic]; ?></option><?php } ?></select></td>';

				html+= '<td <?php if($ifjbwen == 1 || $ifjbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" style="width: 80px;"  name="jval[]" id="jval['+c+']" value="" class="form-control" onkeyup="validatebirds(this.id);calculate_birds(this.id);" onchange="validatebirds(this.id);calculatetotal(this.id);calfinaltotal();emptyval();" /></td>';

				html+= '<td <?php if($ifjbwen == 1 || $ifjbw == 1 || $ifbw == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" style="width: 80px;"  name="bval[]" id="bval['+c+']" value="" class="form-control" onkeyup="validatebirds(this.id);" onchange="validatebirds(this.id);calculatetotal(this.id);calfinaltotal();" /></td>';

				html+= '<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" style="width: 80px;"  name="wval[]" id="wval['+c+']" value="" class="form-control" onchange="validateamount(this.id);" onkeyup="validatenum(this.id);calculatetotal(this.id);calnetweight(this.id);calfinaltotal();" /></td>';

				html+= '<td <?php if($ifjbwen == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" style="width: 80px;"  name="ewval[]" id="ewval['+c+']" value="" class="form-control" onkeyup="validatenum(this.id);calnetweight(this.id);" onchange="validateamount(this.id);calculatetotal(this.id);calfinaltotal();" /></td>';

				html+= '<td style="width: 80px;padding-right:10px;"><input type="text" style="width: 80px;" name="nwval[]" id="nwval['+c+']" value="" class="form-control" onchange="validateamount(this.id);" onkeyup="calculatetotal(this.id);calfinaltotal();" /></td>';
				html+= '<td style="width: 80px;padding-right:10px;"><input type="text" style="width: 80px;" name="iprice[]" id="iprice['+c+']" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" onkeyup="validatenum(this.id);calculatetotal(this.id);calfinaltotal();" <?php if($slae_rate_edit_flag == 0){ echo "readonly"; } ?>></td>';
				html+= '<td style="width: 100px;padding-right:10px;"><input type="text" style="width: 100px;"  name="tamt[]" id="tamt['+c+']" class="form-control" onchange="validateamount(this.id);calfinaltotal();" readonly></td>';
				if(parseInt(tcdr_flag) > 0){ html+= '<td style="width: 100px;padding-right:10px;"><input type="text" name="tcost[]" id="tcost['+c+']" style="width: 100px;" class="form-control"></td>'; }
				html+= '<td <?php if($vehicle_row_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input style="width: 80px;" type="text" name="vehiclerno[]" id="vehiclerno['+c+']" class="form-control" /></td>';
				html+= '<td style="width: 80px;padding-right:10px;"><textarea name="narr[]" id="narr['+c+']" class="form-control" style="width: 90px;height:23px;" ></textarea></td>';
				html+= '<td style="width: 50px;"><a href="JavaScript:Void(0); "name="addval[]" id="addval['+c+']" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+c+']" class="delete" onclick="rowdes(this.id)" title="'+c+'"><i class="fa fa-minus" style="color:red;"></i></a></td>';
				//html+= '<td style="visibility:hidden;"><input type="text" class="form-control" name="outstanding[]" id="outstanding['+c+']" style="width:50px;"></td>';
				html += '<td style="visibility:hidden;"><input type="text" class="form-control" name="jbird_cnt[]" id="jbird_cnt['+c+']" style="width:50px;" readonly /></td>';
				html += '</tr>';
				$('#tab3 tbody').append(html);
				var row = $('#row_cnt').val();
				$('#row_cnt').val(parseInt(row) + parseInt(1));
				var newtrlen = $('#tab3 tbody tr').length;
				if(newtrlen > 0){ $('#submit').show(); } else{ $('#submit').hide(); }
				document.getElementById("incr").value = c; $('.select').select2();
				var x = "fltr_cus["+c+"]";
				filter_group_customers(x);
				fetch_jbcnt(x);
			}
			//$(document).on('click','tr',function(){	var index = $('tr').index(this); var newIndex = parseInt(index) - parseInt(1); document.getElementById("incrs").value = newIndex; });
            document.addEventListener("keydown", (e) => {
                var key_search = document.activeElement.id.includes("[");
                if(key_search == true){
                    var b = document.activeElement.id.split("["); var c = b[1].split("]"); var d = c[0];
                    //alert(e.key+"==="+document.activeElement.id+"==="+key_search+"==="+d);
                    document.getElementById("incrs").value = d;
                }
                /*if (e.key === "Tab"){ } else{ }*/
                if (e.key === "Enter"){
                    //alert(e.key+"==="+document.activeElement.id+"==="+key_search);
					var ebtncount = document.getElementById("ebtncount").value;
					if(ebtncount > 0){
						event.preventDefault();
					}
					else{
						$(":submit").click(function () {
							$('#submittrans').click();
						});
					}
                }
                else{ }
				
            });
            function rowdes(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_id["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
				if(d > 0){
					document.getElementById("rmval["+d+"]").style.visibility = "visible";
				}
				else {
					document.getElementById("rmval["+d+"]").style.visibility = "hidden";
				}
				document.getElementById("addval["+d+"]").style.visibility = "visible";
				calculatetotal("addval["+d+"]");
            }
			/*$(document).on('click','.delete',function(){	
			
				var index = $('.delete').index(this);
				
				var newIndex = parseInt(index) + parseInt(2);
				$('#tab3 > tbody > tr:eq('+newIndex+')').remove();
				
				var row = $('#row_cnt').val();
				var trlen = $('#tab3 > tbody > tr').length;
				
				var minusIndex = parseInt(trlen) - parseInt(1);
				
				if(trlen > 1){
					$('.add:eq('+minusIndex+')').removeClass('disabledbutton');
					$('#row_cnt').val(trlen);
				}else{
					$('.add:eq(0)').removeClass('disabledbutton');
					$('#row_cnt').val(1);
				}
				var a = document.getElementById("incr").value; a--;
				
				document.getElementById("incr").value = a;
				if(a > 0){
					document.getElementById("rmval["+a+"]").style.visibility = "visible";
				}
				else {
					document.getElementById("rmval["+a+"]").style.visibility = "hidden";
				}
				document.getElementById("addval["+a+"]").style.visibility = "visible";
			});*/
			function fetch_jbcnt(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var items = <?php echo json_encode($jbcnt); ?>;
                var jbird_cflag = '<?php echo $jbird_cflag; ?>';
                if(parseInt(jbird_cflag) == 1){
					var i1 = document.getElementById("scat["+d+"]").value;
					if(i1 == "" || i1 == "select"){ }
					else{
						var i2 = i1.split("@"); var icode = i2[0];
						if (Array.isArray(items[icode]) && items[icode].length === 0) { }
						else{
							var jbird_cnt = items[icode]; if(jbird_cnt == ""){ jbird_cnt = 0; }
							document.getElementById("jbird_cnt["+d+"]").value = parseFloat(jbird_cnt).toFixed(2);
						}
					}
				}
			}

            function calculate_birds(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var jbird_cflag = '<?php echo $jbird_cflag; ?>';
                if(parseInt(jbird_cflag) == 1){
					var jval = document.getElementById("jval["+d+"]").value; if(jval == ""){ jval = 0; }
					
					var jbird_cnt = document.getElementById("jbird_cnt["+d+"]").value; if(jbird_cnt == ""){ jbird_cnt = 0; }
					var bval = parseFloat(jval) * parseFloat(jbird_cnt); if(bval == ""){ bval = 0; }
					document.getElementById("bval["+d+"]").value = parseInt(bval);
                }
            }
			function calnetweight(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
               var wval = document.getElementById("wval["+d+"]").value;
			   var eval = document.getElementById("ewval["+d+"]").value;
			   console.log(wval,eval);
			   if(wval == ""){ wval = 0; }
			   if(eval == ""){ eval = 0; }
			   var res = parseFloat(wval) - parseFloat(eval);
			   document.getElementById("nwval["+d+"]").value = res;
            }
			function calculatetotal(aa){
                var ab = aa.split("["); var ad = ab[1].split("]"); var ac = ad[0];
				var a = ac;
				var r = document.getElementById("itemfields").value;
				var b = document.getElementById("scat["+a+"]").value;
				var c = b.split("@");
				var d = c[1].search(/Birds/i);
				if(r.match("WT")){
					var g = document.getElementById("nwval["+a+"]").value; if(g == ""){ g = 0; }
					var h = document.getElementById("iprice["+a+"]").value; if(h == ""){ h = 0; }
					var i = g * h;
					document.getElementById("tamt["+a+"]").value = i.toFixed(2);
				}
				else if(r.match("BAW")){
					if(d > 0){
						document.getElementById("bval["+a+"]").style.visibility = "visible";
						var t = document.getElementById("amountbasedon").value;
						if(t.match("B") || t.match("b")){
							var g = document.getElementById("bval["+a+"]").value;
						}
						else {
							var g = document.getElementById("nwval["+a+"]").value;
						}
						var h = document.getElementById("iprice["+a+"]").value; if(h == ""){ h = 0; }
						var i = g * h;
						document.getElementById("tamt["+a+"]").value = i.toFixed(2);
					}
					else {
						document.getElementById("bval["+a+"]").style.visibility = "hidden";
						var g = document.getElementById("nwval["+a+"]").value;
						var h = document.getElementById("iprice["+a+"]").value; if(h == ""){ h = 0; }
						var i = g * h;
						document.getElementById("tamt["+a+"]").value = i.toFixed(2);
					}
				}
				else if(r.match("JBEW")){
					if(d > 0){
						document.getElementById("jval["+a+"]").style.visibility = "visible";
						document.getElementById("bval["+a+"]").style.visibility = "visible";
						var t = document.getElementById("amountbasedon").value;
						if(t.match("B") || t.match("b")){
							var g = document.getElementById("bval["+a+"]").value;
						}
						else {
							var g = document.getElementById("nwval["+a+"]").value;
						}
						var h = document.getElementById("iprice["+a+"]").value; if(h == ""){ h = 0; }
						var i = g * h;
						document.getElementById("tamt["+a+"]").value = i.toFixed(2);
					}
					else {
						document.getElementById("jval["+a+"]").style.visibility = "hidden";
						document.getElementById("bval["+a+"]").style.visibility = "hidden";
						var g = document.getElementById("nwval["+a+"]").value;
						var h = document.getElementById("iprice["+a+"]").value; if(h == ""){ h = 0; }
						var i = g * h;
						document.getElementById("tamt["+a+"]").value = i.toFixed(2);
					}
				}
				else if(r.match("JBTEN")){
					if(d > 0){
						document.getElementById("jval["+a+"]").style.visibility = "visible";
						document.getElementById("bval["+a+"]").style.visibility = "visible";
						document.getElementById("wval["+a+"]").style.visibility = "visible";
						document.getElementById("ewval["+a+"]").style.visibility = "visible";
						
						var e = document.getElementById("wval["+a+"]").value;
						var f = document.getElementById("ewval["+a+"]").value;
						var g = e - f; var g = parseFloat(g).toFixed(2);
						document.getElementById("nwval["+a+"]").readOnly = true;
						document.getElementById("nwval["+a+"]").value = g;
						var h = document.getElementById("iprice["+a+"]").value; if(h == ""){ h = 0; }
						var i = g * h;
						document.getElementById("tamt["+a+"]").value = i.toFixed(2);
					}
					else {
						document.getElementById("jval["+a+"]").style.visibility = "hidden";
						document.getElementById("bval["+a+"]").style.visibility = "hidden";
						document.getElementById("wval["+a+"]").style.visibility = "hidden";
						document.getElementById("ewval["+a+"]").style.visibility = "hidden";
						document.getElementById("nwval["+a+"]").readOnly = false;
						var g = document.getElementById("nwval["+a+"]").value;
						var h = document.getElementById("iprice["+a+"]").value; if(h == ""){ h = 0; }
						var i = g * h;
						document.getElementById("tamt["+a+"]").value = i.toFixed(2);
					}
				}
				else {
					var g = document.getElementById("nwval["+a+"]").value;
					var h = document.getElementById("iprice["+a+"]").value; if(h == ""){ h = 0; }
					var i = g * h;
					document.getElementById("tamt["+a+"]").value = i.toFixed(2);
				}
			}
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validatebirds(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function fetchprice(a){
				var msale_prate_flag = '<?php echo $msale_prate_flag; ?>';
				if(msale_prate_flag == 1 || msale_prate_flag == "1"){
					var b = a.split("[");
					var c = b[1].split("]");
					var d = c[0];
					var e = document.getElementById("cnames["+d+"]").value;
					var f = e.split("@");
					var g = f[0];
					var h = document.getElementById("scat["+d+"]").value;
					var i = h.split("@");
					var j = i[0];
					var mdate = document.getElementById("slc_datepickers").value;
					if(!a.match("select")){
						var prices = new XMLHttpRequest();
						var method = "GET";
						//var url = "main_getitemprices.php?pname="+g+"&iname="+j;
						var url = "cus_papersaleprice.php?pname="+g+"&iname="+j+"&mdate="+mdate;
                    	//window.open(url);
						var asynchronous = true;
						prices.open(method, url, asynchronous);
						prices.send();
						prices.onreadystatechange = function(){
							if(this.readyState == 4 && this.status == 200){
								var k = this.responseText;
								if(k == "") {
									document.getElementById("iprice["+d+"]").value = ""; 
									//document.getElementById("iprice["+d+"]").readOnly = false;
									calculatetotal("iprice["+d+"]");
								}
								else {
									if(k == "" || k == 0 || k == 0.00 || k == "0.00" || k == "0"){
										//document.getElementById("iprice["+d+"]").readOnly = false;
										document.getElementById("iprice["+d+"]").value = "";
										calculatetotal("iprice["+d+"]");
									}
									else {
										//document.getElementById("iprice["+d+"]").readOnly = false;
										document.getElementById("iprice["+d+"]").value = k;
										calculatetotal("iprice["+d+"]");
									}
									
								}
								//alert(url);
							}
						}
					}
					else {
						alert("Please select Customer first ..!");
						document.getElementById("scat["+b+"]").value = "";
					}
				}
				else{ }
			}
			function fetchoutstanding(a){
				var b = a.split("[");
				var c = b[1].split("]");
				var d = c[0];
				var e = document.getElementById("cnames["+d+"]").value;
				var f = e.split("@");
				var g = f[0];
				if(!e.match("select")){
					var prices = new XMLHttpRequest();
					var method = "GET";
					var url = "cus_fetchoutstandingbal.php?cuscode="+g;
					var asynchronous = true;
					prices.open(method, url, asynchronous);
					prices.send();
					prices.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var f = this.responseText;
							if(f == null || f == "") {
								/*document.getElementById("outstanding["+d+"]").value = "0.00";*/
							}
							else {
								/*document.getElementById("outstanding["+d+"]").value = f;*/
							}
						}
					}
				}
				else { }
			}
			function fetchbalance(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var bal_flag = '<?php echo $bal_flag; ?>';
				if(parseFloat(bal_flag) > 0){
					var e = document.getElementById("cnames["+d+"]").value;
					var f = e.split("@");
					var g = f[0];
					if(!e.match("select")){
						var prices = new XMLHttpRequest();
						var method = "GET";
						var url = "chicken_customer_balances.php?vendors="+g+"&row_cnt="+d;
						var asynchronous = true;
						prices.open(method, url, asynchronous);
						prices.send();
						prices.onreadystatechange = function(){
							if(this.readyState == 4 && this.status == 200){
								var res = this.responseText;
								var info = res.split("[@$&]");
								var rows = info[0];
								var balance = info[1];
								//alert(res);  
								if(balance == null || balance == "") {
									document.getElementById("balc["+rows+"]").value = "0.00";
								}
								else {
									document.getElementById("balc["+rows+"]").value = balance;
								}
							}
						}
					}
					else { }
				}
			}
			function calfinaltotal(){
				var a = document.getElementById("itemfields").value;
				var aa = document.getElementById("incr").value;
				if(a.match("WT")){
					var f = 0; var g = 0;
					var nwht_val = 0; var tamt_val = 0;
					for(var j = 0;j <= aa;j++){
						f = document.getElementById("nwval["+j+"]").value; if(f == ""){ f = 0; }
						nwht_val = parseFloat(nwht_val) + parseFloat(f);
						g = document.getElementById("tamt["+j+"]").value; if(g == ""){ g = 0; }
						tamt_val = parseFloat(tamt_val) + parseFloat(g);
					}
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					var avgprice = parseFloat(tamt_val) / parseFloat(nwht_val); if(avgprice == ""){ avgprice = 0; }
					document.getElementById("avg_price").value = avgprice.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				}
				else if(a.match("BAW")){
					var c = 0; var f = 0; var g = 0;
					var birds_val = 0; var nwht_val = 0; var tamt_val = 0;
					for(var j = 0;j <= aa;j++){
						c = document.getElementById("bval["+j+"]").value; if(c == ""){ c = 0; }
						birds_val = parseFloat(birds_val) + parseFloat(c);
						f = document.getElementById("nwval["+j+"]").value; if(f == ""){ f = 0; }
						nwht_val = parseFloat(nwht_val) + parseFloat(f);
						g = document.getElementById("tamt["+j+"]").value; if(g == ""){ g = 0; }
						tamt_val = parseFloat(tamt_val) + parseFloat(g);
					}
					document.getElementById("tot_bval").value = birds_val;
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					var avgprice = parseFloat(tamt_val) / parseFloat(nwht_val); if(avgprice == ""){ avgprice = 0; }
					document.getElementById("avg_price").value = avgprice.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				}
				else if(a.match("JBEW")){
					var b = 0; var c = 0; var f = 0; var g = 0;
					var jal_val = 0; var birds_val = 0; var twht_val = 0; var ewht_val = 0; var nwht_val = 0; var tamt_val = 0;
					for(var j = 0;j <= aa;j++){
						b = document.getElementById("jval["+j+"]").value; if(b == ""){ b = 0; }
						jal_val = parseFloat(jal_val) + parseFloat(b);
						c = document.getElementById("bval["+j+"]").value; if(c == ""){ c = 0; }
						birds_val = parseFloat(birds_val) + parseFloat(c);
						f = document.getElementById("nwval["+j+"]").value; if(f == ""){ f = 0; }
						nwht_val = parseFloat(nwht_val) + parseFloat(f);
						g = document.getElementById("tamt["+j+"]").value; if(g == ""){ g = 0; }
						tamt_val = parseFloat(tamt_val) + parseFloat(g);
					}
					document.getElementById("tot_jval").value = jal_val;
					document.getElementById("tot_bval").value = birds_val;
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					var avgprice = parseFloat(tamt_val) / parseFloat(nwht_val); if(avgprice == ""){ avgprice = 0; }
					document.getElementById("avg_price").value = avgprice.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				}
				else if(a.match("JBTEN")){
					var b = 0; var c = 0; var d = 0; var e = 0; var f = 0; var g = 0;
					var jal_val = 0; var birds_val = 0; var twht_val = 0; var ewht_val = 0; var nwht_val = 0; var tamt_val = 0;
					for(var j = 0;j <= aa;j++){
						b = document.getElementById("jval["+j+"]").value; if(b == ""){ b = 0; }
						jal_val = parseFloat(jal_val) + parseFloat(b);
						c = document.getElementById("bval["+j+"]").value; if(c == ""){ c = 0; }
						birds_val = parseFloat(birds_val) + parseFloat(c);
						d = document.getElementById("wval["+j+"]").value; if(d == ""){ d = 0; }
						twht_val = parseFloat(twht_val) + parseFloat(d);
						e = document.getElementById("ewval["+j+"]").value; if(e == ""){ e = 0; }
						ewht_val = parseFloat(ewht_val) + parseFloat(e);
						f = document.getElementById("nwval["+j+"]").value; if(f == ""){ f = 0; }
						nwht_val = parseFloat(nwht_val) + parseFloat(f);
						g = document.getElementById("tamt["+j+"]").value; if(g == ""){ g = 0; }
						tamt_val = parseFloat(tamt_val) + parseFloat(g);
					}
					document.getElementById("tot_jval").value = jal_val;
					document.getElementById("tot_bval").value = birds_val;
					document.getElementById("tot_wval").value = twht_val.toFixed(2);
					document.getElementById("tot_ewval").value = ewht_val.toFixed(2);
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
					var avgprice = parseFloat(tamt_val) / parseFloat(nwht_val); if(avgprice == ""){ avgprice = 0; }
					document.getElementById("avg_price").value = avgprice.toFixed(2);
					document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
				}
				else{
					alert("Kindly contact Admin for any support..!");
				}
			}
			document.addEventListener('keyup', function(event){
				var code = event.keyCode || event.which;
				if (code === 9){
					var a = '<?php echo $ifjbwen; ?>';
					if(a == '1' || a == 1){
						document.getElementById("incrs").value = document.getElementById("incr").value;
					}
					else{
						document.getElementById("incrs").value = document.getElementById("incr").value;
					}
				}
			});
			function emptyval(){
				var a = document.getElementById("incrs").value;
				var r = document.getElementById("itemfields").value;
				var b = document.getElementById("scat["+a+"]").value;
				var c = b.split("@");
				var d = c[1].search(/Birds/i);
				var e = '<?php echo $jcount; ?>';
				var f = '<?php echo $ejals_flag; ?>';
				if(r.match("JBTEN") && e == 1 && f == 1){
					var g = '<?php echo $box_wt; ?>';
					var h = '<?php echo $box_count; ?>';
					var i = document.getElementById("jval["+a+"]").value;
					var j = ((i / h) * g);
					document.getElementById("ewval["+a+"]").value = j.toFixed(2);
					
				}
			}
			function fetch_pur_dcnos(){
				var date = document.getElementById("slc_datepickers").value;
				var wcode = document.getElementById("wcodes").value;
				removeAllOptions(document.getElementById("binv"));
				if(date == ""){
					alert("Please select Date");
					document.getElementById("slc_datepickers").focus();
				}
				else if(wcode == "" || wcode == "select"){
					alert("Please select Warehouse");
					document.getElementById("wcodes").focus();
				}
				else{
                    var dc_nos = new XMLHttpRequest();
                    var method = "GET";
                    var url = "chicken_fetch_purdcnos.php?date="+date+"&wcode="+wcode;
                    //window.open(url);
                    var asynchronous = true;
                    dc_nos.open(method, url, asynchronous);
                    dc_nos.send();
                    dc_nos.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var pur_info = this.responseText;
							if(pur_info.length > 0){
								$('#binv').append(pur_info);
							}
							else{
								alert("Active Purchase Dcnos are not available \n Kindly check and try again ...!");
							}
                        }
                    }
                }
			}
			function fetch_pur_stock(){
				var date = document.getElementById("slc_datepickers").value;
				var wcode = document.getElementById("wcodes").value;
				var dcnos = document.getElementById("binv").value;
				document.getElementById("avl_birds").value = "";
				document.getElementById("avl_weight").value = "";
				
				if(date == ""){
					alert("Please select Date");
					document.getElementById("slc_datepickers").focus();
				}
				else if(wcode == "" || wcode == "select"){
					alert("Please select Warehouse");
					document.getElementById("wcodes").focus();
				}
				else if(dcnos == "" || dcnos == "select"){
					alert("Please select Invoice No.");
					document.getElementById("binv").focus();
				}
				else{
                    var dc_nos = new XMLHttpRequest();
                    var method = "GET";
                    var url = "chicken_fetch_purdcstock.php?date="+date+"&wcode="+wcode+"&dcnos="+dcnos;
                    //window.open(url);
                    var asynchronous = true;
                    dc_nos.open(method, url, asynchronous);
                    dc_nos.send();
                    dc_nos.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var pur_dt1 = this.responseText;
							var pur_dt2 = pur_dt1.split("[@$&]");
							var birds = pur_dt2[0];
							var weight = pur_dt2[1];
							document.getElementById("avl_birds").value = parseFloat(birds).toFixed(0);
							document.getElementById("avl_weight").value = parseFloat(weight).toFixed(2);
                        }
                    }
                }
			}
            function filter_group_customers(a){
                if(a == "groups"){
					var incr = document.getElementById("incr").value;
					var groups = document.getElementById('groups').value;
					if(groups == "all"){
						for(var d = 0;d <= incr;d++){
							removeAllOptions(document.getElementById("cnames["+d+"]"));
							myselect = document.getElementById("cnames["+d+"]");
							theOption1=document.createElement("OPTION");
							theText1=document.createTextNode("-select-");
							theOption1.value = "select";
							theOption1.appendChild(theText1);
							myselect.appendChild(theOption1);

							<?php
							foreach($cus_code as $vcode){
							?>
								theOption1=document.createElement("OPTION");
								theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
								theOption1.value = "<?php echo $vcode; ?>";
								theOption1.appendChild(theText1);
								myselect.appendChild(theOption1);
							<?php
							}
							?>
						}
					}
					else{
						for(var d = 0;d <= incr;d++){
							removeAllOptions(document.getElementById("cnames["+d+"]"));
							myselect = document.getElementById("cnames["+d+"]");
							theOption1=document.createElement("OPTION");
							theText1=document.createTextNode("-select-");
							theOption1.value = "select";
							theOption1.appendChild(theText1);
							myselect.appendChild(theOption1);

							<?php
							foreach($cus_code as $vcode){
								$gcode = $cus_group[$vcode];
								echo "if(groups == '$gcode'){";
								?>
								theOption1=document.createElement("OPTION");
								theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
								theOption1.value = "<?php echo $vcode; ?>";
								theOption1.appendChild(theText1);
								myselect.appendChild(theOption1);
								<?php
								echo "}";
							}
							?>
						}
					}
				}
				else{
					var b = a.split("["); var c = b[1].split("]"); var d = c[0];
					var groups = document.getElementById('groups').value;

					removeAllOptions(document.getElementById("cnames["+d+"]"));
					myselect = document.getElementById("cnames["+d+"]");
					theOption1=document.createElement("OPTION");
					theText1=document.createTextNode("-select-");
					theOption1.value = "select";
					theOption1.appendChild(theText1);
					myselect.appendChild(theOption1);

					if(groups == "all"){
						<?php
						foreach($cus_code as $vcode){
						?>
							theOption1=document.createElement("OPTION");
							theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
							theOption1.value = "<?php echo $vcode; ?>";
							theOption1.appendChild(theText1);
							myselect.appendChild(theOption1);
						<?php
						}
						?>
					}
					else{
						<?php
						foreach($cus_code as $vcode){
							$gcode = $cus_group[$vcode];
							echo "if(groups == '$gcode'){";
							?>
							theOption1=document.createElement("OPTION");
							theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
							theOption1.value = "<?php echo $vcode; ?>";
							theOption1.appendChild(theText1);
							myselect.appendChild(theOption1);
							<?php
							echo "}";
						}
						?>
					}
				}
            }
			var x = "item[0]"; fetch_jbcnt(x);
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
		</script>
	</body>
</html>