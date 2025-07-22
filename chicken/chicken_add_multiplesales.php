<?php
	//chicken_add_multiplesales.php
	session_start();
    include "newConfig.php";
	include "header_head.php";
    $user_code = $_SESSION['userid'];
    
    $url = explode("?",basename($_SERVER['REQUEST_URI']));
	$sql = "SELECT * FROM `main_linkdetails` WHERE `href` = '$url[0]' AND `activate` = '1' ORDER BY `sortorder` ASC";
    $query = mysqli_query($conn,$sql); $ahref_flag = mysqli_num_rows($query);
    while($row = mysqli_fetch_assoc($query)){ $cid = $row['childid']; }

    $sql = "SELECT * FROM `main_access` WHERE `empcode` = '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($query)){
        $alink = $row['addaccess']; $cgroup_access = $row['cgroup_access']; $loc_access = $row['loc_access'];
    }

    if($ahref_flag > 0 && str_contains($alink, $cid)){
        if($loc_access == "all" || $loc_access == "" || $loc_access == NULL){ $warehouse_codes = ""; }
        else{
            $whs_code = ""; $crp_codes = explode(",",$loc_access);
            foreach($crp_codes as $whs){ if($whs_code == ""){ $whs_code = $whs; } else{ $whs_code = $whs_code."','".$whs; } }
            if($whs_code != ""){ $warehouse_codes = " AND `code` IN ('$whs_code')"; } else{ $warehouse_codes = ""; }
        }
        if($cgroup_access == "all" || $cgroup_access == "" || $cgroup_access == NULL){ $cgroup_codes = ""; }
        else{
            $crp_code = ""; $crp_codes = explode(",",$cgroup_access);
            foreach($crp_codes as $cgrps){ if($crp_code == ""){ $crp_code = $cgrps; } else{ $crp_code = $crp_code."','".$cgrps; } }
            if($crp_code != ""){ $cgroup_codes = " AND `groupcode` IN ('$crp_code')"; } else{ $cgroup_codes = ""; }
        }
        
        $sql = "SELECT * FROM `main_reportfields` WHERE `field` LIKE 'Add Multiple Sales' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $vehicle_flag = $row['vehicle_flag']; $vehicle_row_flag = $row['vehicle_row_flag']; }
        if($vehicle_flag == "" || $vehicle_flag == 0){ $vehicle_flag = 0; }
        if($vehicle_row_flag == "" || $vehicle_row_flag == 0){ $vehicle_row_flag = 0; }

        $box_count = $box_wt = 0;
        $sql = "SELECT * FROM `main_jals` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql); $jcount = mysqli_num_rows($query);
        if($jcount > 0){ while($row = mysqli_fetch_assoc($query)){ $box_wt = $row['weight']; $box_count = $row['box_count']; } } else{ $empty_count = 0; }

        $field = $date_as = $inv_as = $cus_as = $binv_as = $item_as = $jals_as = $birds_as = $twt_as = $ewt_as = $nwt_as = $avgwt_as = $price_as = $tamt_as = $sector_as = 
        $tcds_as = $vehicle_as = $driver_as = $famt_as = $remarks_as = $srct_as = $user_as = $basv_flag = "";
        $fname_sql = "SELECT * FROM `main_displayfieldname` WHERE `field` LIKE 'MSLS' AND `active` = '1'"; $fname_query = mysqli_query($conn,$fname_sql);
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
?>
<html>
	<head>
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
				$today = date("Y-m-d"); $fdate = date("d.m.Y",strtotime($today)); $branches = "";
                $item_code = $item_name = $sector_code = $sector_name = $cus_code = $cus_name = array();

				$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$cgroup_codes." AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

				$sql = "SELECT * FROM `main_officetypes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Warehouse"){ if($branches == ""){ $branches = $row['code']; } else{ $branches = $branches."','".$row['code']; } } }
				
                $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$warehouse_codes." AND `type` IN ('$branches') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

				$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$spzflag = $row['spzflag'];
					$jals_flag = $row['jals_flag'];
					$birds_flag = $row['birds_flag'];
					$eweight_flag = $row['eweight_flag'];
					$tweight_flag = $row['tweight_flag'];
					$nweight_flag = $row['nweight_flag'];
					$ifctype = $row['ctype'];
					$ejals_flag = $row['ejals_flag'];
					$msale_prate_flag = $row['msale_prate_flag'];
				}
				if($spzflag == "" || $spzflag == 0 || $spzflag == NULL){ $spzflag = 0; }

				$idisplay = ''; $ndisplay = 'style="display:none;';
			?>
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18">
								<form action="chicken_save_multiplesales.php" method="post" role="form" onsubmit="return checkval()">
									<div class="form-group col-md-1">
										<label><?php if($date_as != ""){ echo $date_as; } else { echo 'Date'; } ?><b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:100px;" class="form-control" name="pdate" value="<?php echo $fdate; ?>" id="slc_datepickers" readonly>
									</div>
									<div class="form-group col-md-2">
										<label><?php if($sector_as != ""){ echo $sector_as; } else { echo 'Warehouse'; } ?><b style="color:red;">&nbsp;*</b></label>
										<select name="wcodes" id="wcodes" class="form-control select2" style="width: 100%;"><?php foreach($sector_code as $it){ ?><option value="<?php echo $sector_code[$it]; ?>"><?php echo $sector_name[$it]; ?></option><?php } ?></select>
									</div>
									<div class="form-group col-md-2">
										<label><?php if($binv_as != ""){ echo $binv_as; } else { echo 'Invoice No.'; } ?><b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:130px;" class="form-control" name="binv" id="binv">
									</div>
									<?php if($vehicle_flag == 1){ ?>
									<div class="form-group col-md-2">
										<label><?php if($vehicle_as != ""){ echo $vehicle_as; } else { echo 'Vehicle No.'; } ?></label>
										<input type="text" style="width:130px;" class="form-control" name="vehicleno" id="vehicleno">
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
										<label>ECount<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
									</div>
									<div class="col-md-12">
										<table style="width:100%;line-height:30px;" id="tab3">
											<tr style="line-height:30px;">
												<th><label>Customer<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Item<b style="color:red;">&nbsp;*</b></label></th>
												<?php
													if($jals_flag == 1){
														echo "<th><label>Jals</label></th>";
													}
													if($birds_flag == 1){
														echo "<th><label>Birds</label></th>";
													}
													if($tweight_flag == 1){
														echo "<th><label>T. Weight<b style='color:red;'>&nbsp;*</b></label></th>";
													}
													if($eweight_flag == 1){
														echo "<th><label>E. Weight</label></th>";
													}
													if($nweight_flag == 1){
														echo "<th><label>N. Weight<b style='color:red;'>&nbsp;*</b></label></th>";
													}
												?>
												<th title="Price on farm weight"><label>Farm Wt.</label></th>
												<th><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount</label></th>
												<?php if($vehicle_row_flag == 1){ echo "<th><label>Vehicle</label></th>"; } ?>
												<th><label>Remarks</label></th>
												<th></th>
												<!--<th><label>Outstanding<b style="color:red;">&nbsp;*</b></label</th>>-->
											</tr>
											<tr style="margin:5px 0px 5px 0px;">
                                                <td style="width: 180px;padding-right:30px;"><select name="cnames[]" id="cnames[0]" class="form-control select2" style="width: 180px;" onchange="fetchoutstanding(this.id);fetchprice(this.id);"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
												<td style="width: 180px;padding-right:30px;"><select name="scat[]" id="scat[0]" class="form-control select2" style="width: 180px;" onchange="calculatetotal(this.id);fetchprice(this.id);"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select></td>
												<td <?php if($jals_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval[]" id="jval[0]" class="form-control" onchange="validatenum(this.id);calculatetotal(this.id);calfinaltotal();" onkeyup="emptyval()" /></td>
												<td <?php if($birds_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval[]" id="bval[0]" class="form-control" onchange="validatenum(this.id);calculatetotal(this.id);calfinaltotal();" /></td>
												<td <?php if($tweight_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval[]" id="wval[0]" class="form-control" onchange="validateamount(this.id)" onkeyup="calculatetotal(this.id);calfinaltotal();" /></td>
												<td <?php if($eweight_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="ewval[]" id="ewval[0]" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);calfinaltotal();" /></td>
												<td <?php if($nweight_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="nwval[]" id="nwval[0]" class="form-control" onchange="validateamount(this.id);" onkeyup="calculatetotal(this.id);calfinaltotal();" /></td>
												<td><input type="text" name="farm_weight[]" id="farm_weight[0]" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" onkeyup="calculatetotal(this.id);calfinaltotal();" /></td>
												<td><input type="text" name="iprice[]" id="iprice[0]" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" onkeyup="calculatetotal(this.id);calfinaltotal();"></td>
												<td><input type="text" name="tamt[]" id="tamt[0]" class="form-control" onchange="validateamount(this.id);calfinaltotal();" readonly></td>
												<?php if($vehicle_row_flag == 1){ echo '<td><input type="text" name="vehiclerno[]" id="vehiclerno[0]" class="form-control" /></td>'; } ?>
												<td style="width: auto;"><textarea name="narr[]" id="narr[0]" class="form-control" style="height:23px;"></textarea></td>
												<td style="width: 60px;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes(this.id)" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
												<!--<td style="visibility:hidden;"><input type="text" class="form-control" name="outstanding[]" id="outstanding[0]" style="width:50px;"></td>-->
											</tr>
										</table><br/>
										<table style="width:100%;line-height:30px;">
											<tr style="line-height:30px;">
												<th></th>
												<th></th>
												<?php if($jals_flag == 1){ echo "<th><label>Total Jals</label></th>"; } ?>
												<?php if($birds_flag == 1){ echo "<th><label>Total Birds</label></th>"; } ?>
												<?php if($tweight_flag == 1){ echo "<th><label>Total T. Weight</label></th>"; } ?>
												<?php if($eweight_flag == 1){ echo "<th><label>Total E. Weight</label></th>"; } ?>
												<?php if($nweight_flag == 1){ echo "<th><label>Total N. Weight</label></th>"; } ?>
												<th></th>
												<th><label>Avg. Price</label></th>
												<th><label><?php if($famt_as != ""){ echo $famt_as; } else { echo 'Total Amount'; } ?></label></th>
												<?php if($vehicle_row_flag == 1){ echo '<th></th>'; } ?>
												<th></th>
												<th></th>
											</tr>
											<tr style="margin:5px 0px 5px 0px;">
												<td></td>
												<td></td>
												<?php if($jals_flag == 1){ echo '<td><input type="text" name="tot_jval" id="tot_jval" class="form-control" readonly /></td>'; } ?>
												<?php if($birds_flag == 1){ echo '<td><input type="text" name="tot_bval" id="tot_bval" class="form-control" readonly /></td>'; } ?>
												<?php if($tweight_flag == 1){ echo '<td><input type="text" name="tot_wval" id="tot_wval" class="form-control" readonly /></td>'; } ?>
												<?php if($eweight_flag == 1){ echo '<td><input type="text" name="tot_ewval" id="tot_ewval" class="form-control" readonly /></td>'; } ?>
												<?php if($nweight_flag == 1){ echo '<td><input type="text" name="tot_nwval" id="tot_nwval" class="form-control" readonly /></td>'; } ?>
												<td></td>
												<td><input type="text" name="avg_price" id="avg_price" class="form-control" readonly></td>
												<td><input type="text" name="tot_tamt" id="tot_tamt" class="form-control" readonly></td>
												<?php if($vehicle_row_flag == 1){ echo '<td style="width: auto;"></td>'; } ?>
												<td style="width: auto;"></td>
												<td style="width: 60px;"></td>
												<!--<td style="visibility:hidden;"><input type="text" class="form-control" name="outstanding[]" id="outstanding[0]" style="width:50px;"></td>-->
											</tr>
										<table>
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
		</section>
		<?php include "header_foot.php"; ?>
		<script>
			function checkval(){
				var b = document.getElementById("incr").value;
				document.getElementById("ebtncount").value = "1";
				document.getElementById("submittrans").style.visibility = "hidden";
				var sale_price_flag = '<?php echo $spzflag; ?>';
				var farm_weight = 0;
				var aa = document.getElementById("amountbasedon").value;
				for(var ab=0;ab<=b;ab++){
					if(aa.match("B") || aa.match("b")){
						var ac = document.getElementById("bval["+ab+"]").value;
					}
					else {
						farm_weight = document.getElementById("farm_weight["+ab+"]").value;
						var ac = document.getElementById("nwval["+ab+"]").value;
					}
					var ad = document.getElementById("iprice["+ab+"]").value;
					if(farm_weight != "" && parseFloat(farm_weight) > 0){
						var ae = parseFloat(farm_weight) * parseFloat(ad);
					}
					else{
						var ae = parseFloat(ac) * parseFloat(ad);
					}
					
					document.getElementById("tamt["+ab+"]").value = ae.toFixed(2);
				}
				var l = true;
				for(var j=0;j<=b;j++){
					if(l == true){
						var c = document.getElementById("scat["+j+"]").value;
						var k = j; k++;
						var g = document.getElementById("nwval["+j+"]").value;
						farm_weight = document.getElementById("farm_weight["+j+"]").value;
						var h = document.getElementById("iprice["+j+"]").value;
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
						else if(g == "" && farm_weight == "" || parseFloat(g) == 0 && parseFloat(farm_weight) == 0){
							alert("Please Enter the net weight/Farm Weight in row: "+k);
							document.getElementById("nwval["+j+"]").focus();
							l = false;
						}
						else if(h.length == 0 && sale_price_flag == 0 || h == 0 && sale_price_flag == 0 || h == "" && sale_price_flag == 0){
							alert("Please Enter the price in row: "+k);
							document.getElementById("iprice["+j+"]").focus();
							l = false;
						}
						/*else if(r.length == 0 && sale_price_flag == 0 || r == 0 && sale_price_flag == 0 || r == "" && sale_price_flag == 0){
							alert("Please Re-Enter the price again to get the amount in row: "+k);
							document.getElementById("iprice["+j+"]").focus();
							l = false;
						}*/
						else {
							l = true;
						}
					}
					else {
						l = false;
					}
				}
				if(l == true){
					//document.getElementById("submittrans").disabled = true;
					return l;
				}
				else if(l == false){
					document.getElementById("ebtncount").value = "0";
					document.getElementById("submittrans").style.visibility = "visible";
					return l;
				}
				else {
					alert("Invalid");
					document.getElementById("ebtncount").value = "0";
					return false;
				}
			}
			function redirection_page(){
				window.location.href = "chicken_display_multiplesales.php";
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
				var html = '';
				html+= '<tr style="margin:5px 0px 5px 0px;" id="row_id['+c+']">';
				html+= '<td style="width: 180px;padding-right:30px;"><select name="cnames[]" id="cnames['+c+']" class="form-control select" style="width: 180px;" onchange="fetchoutstanding(this.id);fetchprice(this.id);"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>';
				html+= '<td style="width: 180px;padding-right:30px;"><select name="scat[]" id="scat['+c+']" class="form-control select" style="width: 180px;" onchange="calculatetotal(this.id);fetchprice(this.id);"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select></td>';
				html+= '<td <?php if($jals_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval[]" id="jval['+c+']" class="form-control" onchange="validatenum(this.id);calculatetotal(this.id);calfinaltotal();" onkeyup="emptyval()" /></td>';
				html+= '<td <?php if($birds_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval[]" id="bval['+c+']" class="form-control" onchange="validatenum();calculatetotal(this.id);calfinaltotal();" /></td>';
				html+= '<td <?php if($tweight_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval[]" id="wval['+c+']" class="form-control" onchange="validateamount(this.id)" onkeyup="calculatetotal(this.id);calfinaltotal();" /></td>';
				html+= '<td <?php if($eweight_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="ewval[]" id="ewval['+c+']" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);calfinaltotal();" /></td>';
				html+= '<td <?php if($nweight_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="nwval[]" id="nwval['+c+']" class="form-control" onchange="validateamount(this.id);" onkeyup="calculatetotal(this.id);calfinaltotal();" /></td>';
				html+= '<td><input type="text" name="farm_weight[]" id="farm_weight['+c+']" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" onkeyup="calculatetotal(this.id);calfinaltotal();" /></td>';
				html+= '<td><input type="text" name="iprice[]" id="iprice['+c+']" class="form-control" onchange="validateamount(this.id);calculatetotal(this.id);" onkeyup="calculatetotal(this.id);calfinaltotal();"></td>';
				html+= '<td><input type="text" name="tamt[]" id="tamt['+c+']" class="form-control" onchange="validateamount(this.id);calfinaltotal();" readonly></td>';
				html+= '<td <?php if($vehicle_row_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="vehiclerno[]" id="vehiclerno['+c+']" class="form-control" /></td>';
				html+= '<td style="width: auto;"><textarea name="narr[]" id="narr['+c+']" class="form-control" style="height:23px;" ></textarea></td>';
				html+= '<td style="width: 60px;"><a href="JavaScript:Void(0); "name="addval[]" id="addval['+c+']" onclick="rowgen(this.id)"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+c+']" class="delete" onclick="rowdes(this.id)" title="'+c+'"><i class="fa fa-minus" style="color:red;"></i></a></td>';
				//html+= '<td style="visibility:hidden;"><input type="text" class="form-control" name="outstanding[]" id="outstanding['+c+']" style="width:50px;"></td>';
				html += '</tr>';
				$('#tab3 tbody').append(html);
				var row = $('#row_cnt').val();
				$('#row_cnt').val(parseInt(row) + parseInt(1));
				var newtrlen = $('#tab3 tbody tr').length;
				if(newtrlen > 0){ $('#submit').show(); } else{ $('#submit').hide(); }
				document.getElementById("incr").value = c; $('.select').select2(); calfinaltotal();
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
				calculatetotal("addval["+d+"]"); calfinaltotal();
            }/*
			$(document).on('click','.delete',function(){	
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
			function calculatetotal(aa){
                var ab = aa.split("["); var ad = ab[1].split("]"); var ac = ad[0];
				var a = ac;
				var r = document.getElementById("itemfields").value;
				var b = document.getElementById("scat["+a+"]").value;
				var c = b.split("@");
				var d = c[1].search(/Birds/i);
				var j_flag = '<?php echo $jals_flag; ?>';
				var b_flag = '<?php echo $birds_flag; ?>';
				var t_flag = '<?php echo $tweight_flag; ?>';
				var e_flag = '<?php echo $eweight_flag; ?>';
				var n_flag = '<?php echo $nweight_flag; ?>';
				//alert(j_flag+"-"+b_flag+"-"+t_flag+"-"+e_flag+"-"+n_flag);
				if(d > 0){
					if(j_flag == 1 || j_flag == "1"){ document.getElementById("jval["+a+"]").style.visibility = "visible"; }
					if(b_flag == 1 || b_flag == "1"){ document.getElementById("bval["+a+"]").style.visibility = "visible"; }
					if(t_flag == 1 || t_flag == "1"){ document.getElementById("wval["+a+"]").style.visibility = "visible"; }
					if(e_flag == 1 || e_flag == "1"){ document.getElementById("ewval["+a+"]").style.visibility = "visible"; }
					if(t_flag == 1 && e_flag == 1){
						var ti_wt = document.getElementById("wval["+a+"]").value;
						var ei_wt = document.getElementById("ewval["+a+"]").value;
						var ni_wt = ti_wt - ei_wt; ni_wt = parseFloat(ni_wt).toFixed(2);
						document.getElementById("nwval["+a+"]").readOnly = true;
						document.getElementById("nwval["+a+"]").value = ni_wt;
					}
					var s = document.getElementById("incr").value;
					var t = document.getElementById("amountbasedon").value;
					var farm_weight = document.getElementById("farm_weight["+a+"]").value;
					if(t.match("B") || t.match("b")){
						var g = document.getElementById("bval["+a+"]").value;
					}
					else {
						if(farm_weight != "" && parseFloat(farm_weight) > 0){
							var g = document.getElementById("farm_weight["+a+"]").value;
						}
						else{
							var g = document.getElementById("nwval["+a+"]").value;
						}
						
					}
					var h = document.getElementById("iprice["+a+"]").value;
					var i = g * h;
					document.getElementById("tamt["+a+"]").value = i.toFixed(2);
				}
				else {
					if(j_flag == 1 || j_flag == "1"){ document.getElementById("jval["+a+"]").style.visibility = "hidden"; }
					if(b_flag == 1 || b_flag == "1"){ document.getElementById("bval["+a+"]").style.visibility = "hidden"; }
					if(t_flag == 1 || t_flag == "1"){ document.getElementById("wval["+a+"]").style.visibility = "hidden"; }
					if(e_flag == 1 || e_flag == "1"){ document.getElementById("ewval["+a+"]").style.visibility = "hidden"; }

					document.getElementById("nwval["+a+"]").readOnly = false;
					var s = document.getElementById("incr").value;

					var farm_weight = document.getElementById("farm_weight["+a+"]").value;
					if(farm_weight != "" && parseFloat(farm_weight) > 0){
						var g = document.getElementById("farm_weight["+a+"]").value;
					}
					else{
						var g = document.getElementById("nwval["+a+"]").value;
					}
					
					var h = document.getElementById("iprice["+a+"]").value;
					var i = g * h;
					document.getElementById("tamt["+a+"]").value = i.toFixed(2);
				}
			}
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
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
									document.getElementById("iprice["+d+"]").readOnly = false;
									calculatetotal("iprice["+d+"]");
								}
								else {
									if(k == "" || k == 0 || k == 0.00 || k == "0.00" || k == "0"){
										document.getElementById("iprice["+d+"]").readOnly = false;
										document.getElementById("iprice["+d+"]").value = "";
										calculatetotal("iprice["+d+"]");
									}
									else {
										document.getElementById("iprice["+d+"]").readOnly = false;
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
							if(f == "") {
								document.getElementById("outstanding["+d+"]").value = "0.00";
							}
							else {
								document.getElementById("outstanding["+d+"]").value = f;
							}
						}
					}
				}
				else { }
			}
			function calfinaltotal(){
				var a = document.getElementById("itemfields").value;
				var aa = document.getElementById("incr").value;
				var j_flag = '<?php echo $jals_flag; ?>';
				var b_flag = '<?php echo $birds_flag; ?>';
				var t_flag = '<?php echo $tweight_flag; ?>';
				var e_flag = '<?php echo $eweight_flag; ?>';
				var n_flag = '<?php echo $nweight_flag; ?>';
				if(j_flag == 1){
					var b = 0; var jal_val = 0;
					for(var j = 0;j <= aa;j++){
						b = document.getElementById("jval["+j+"]").value;
						if(b == "" || b == "0.00" || b == 0 || b == "NaN"){ b = 0; }
						jal_val = parseFloat(jal_val) + parseFloat(b);
					}
					if(jal_val == "" || jal_val == "0.00" || jal_val == 0 || jal_val == "NaN"){ jal_val = 0; }
					document.getElementById("tot_jval").value = jal_val;
				}
				if(b_flag == 1){
					var c = 0; var birds_val = 0;
					for(var j = 0;j <= aa;j++){
						c = document.getElementById("bval["+j+"]").value;
						if(c == "" || c == "0.00" || c == 0 || c == "NaN"){ c = 0; }
						birds_val = parseFloat(birds_val) + parseFloat(c);
					}
					if(birds_val == "" || birds_val == "0.00" || birds_val == 0 || birds_val == "NaN"){ birds_val = 0; }
					document.getElementById("tot_bval").value = birds_val;
				}
				if(t_flag == 1){
					var d = 0; var twht_val = 0;
					for(var j = 0;j <= aa;j++){
						d = document.getElementById("wval["+j+"]").value;
						if(d == "" || d == "0.00" || d == 0 || d == "NaN"){ d = 0; }
						twht_val = parseFloat(twht_val) + parseFloat(d);
					}
					if(twht_val == "" || twht_val == "0.00" || twht_val == 0 || twht_val == "NaN"){ twht_val = 0; }
					document.getElementById("tot_wval").value = twht_val.toFixed(2);
				}
				if(e_flag == 1){
					var e = 0; var ewht_val = 0;
					for(var j = 0;j <= aa;j++){
						e = document.getElementById("ewval["+j+"]").value;
						if(e == "" || e == "0.00" || e == 0 || e == "NaN"){ e = 0; }
						ewht_val = parseFloat(ewht_val) + parseFloat(e);
					}
					if(ewht_val == "" || ewht_val == "0.00" || ewht_val == 0 || ewht_val == "NaN"){ ewht_val = 0; }
					document.getElementById("tot_ewval").value = ewht_val.toFixed(2);
				}
				if(n_flag == 1){
					var f = 0; var nwht_val = 0;
					for(var j = 0;j <= aa;j++){
						f = document.getElementById("nwval["+j+"]").value;
						if(f == "" || f == "0.00" || f == 0 || f == "NaN"){ f = 0; }
						nwht_val = parseFloat(nwht_val) + parseFloat(f);
					}
					if(nwht_val == "" || nwht_val == "0.00" || nwht_val == 0 || nwht_val == "NaN"){ nwht_val = 0; }
					document.getElementById("tot_nwval").value = nwht_val.toFixed(2);
				}
				var g = 0; var tamt_val = 0;
				for(var j = 0;j <= aa;j++){
					g = document.getElementById("tamt["+j+"]").value;
					if(g == "" || g == "0.00" || g == 0 || g == "NaN"){ g = 0; }
					tamt_val = parseFloat(tamt_val) + parseFloat(g);
				}
				if(avgprice == "" || avgprice == "0.00" || avgprice == 0 || avgprice == "NaN"){ avgprice = 0; }
				if(tamt_val == "" || tamt_val == "0.00" || tamt_val == 0 || tamt_val == "NaN"){ tamt_val = 0; }
				var avgprice = tamt_val / nwht_val;
				document.getElementById("avg_price").value = avgprice.toFixed(2);
				document.getElementById("tot_tamt").value = tamt_val.toFixed(2);
			}
			/*document.addEventListener('keyup', function(event){
				var code = event.keyCode || event.which;
				if (code === 9){
					var a = '<?php //echo $ifjbwen; ?>';
					if(a == '1' || a == 1){
						document.getElementById("incrs").value = document.getElementById("incr").value;
					}
					else{
						document.getElementById("incrs").value = document.getElementById("incr").value;
					}
				}
			});*/
			function emptyval(){
				var a = document.getElementById("incrs").value;
				var j_flag = '<?php echo $jals_flag; ?>';
				var e_flag = '<?php echo $eweight_flag; ?>';
				var b = document.getElementById("scat["+a+"]").value;
				var c = b.split("@");
				var d = c[1].search(/Birds/i);
				var e = '<?php echo $jcount; ?>';
				var f = '<?php echo $ejals_flag; ?>';
				if(j_flag == 1 && e_flag == 1 && e == 1 && f == 1){
					var g = '<?php echo $box_wt; ?>';
					var h = '<?php echo $box_count; ?>';
					var i = document.getElementById("jval["+a+"]").value;
					var j = ((i / h) * g);
					document.getElementById("ewval["+a+"]").value = j.toFixed(2);
				}
			}
		</script>
	</body>
</html>
<?php
    }
    else{
    ?>
    <script>
        var c = confirm("Currently you don't have access to this file. \n Contact your admin for more information.");
        if(c == true){
            window.location.href = "controlpanel.php";
        }
        else{
            window.location.href = "controlpanel.php";
        }
    </script>
    <?php
    }
?>