<?php 
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	session_start(); include "newConfig.php";
	include "header_head.php";
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
			.form-control { width: 100%; height: 23px; }
			label { line-height: 20px; }
			.disabledbutton{ pointer-events: none; opacity: 0.4; }
			#tab3 tbody td {
				padding: 0px 5px;
			}
			th {
				width: 10%;
				text-align:center;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Edit Vouchers</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Vouchers Display</li>
				<li class="active">Edit</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$invoiceid = $_GET['id'];
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}
				$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$acode[$row['code']] = $row['code'];
					$adesc[$row['code']] = $row['description'];
				}
				$visibles = 'style="visibility:visible;"'; $nvisibles = 'style="visibility:hidden;"';
				$sql = "SELECT * FROM `dataentry_daterange` WHERE `active` = '1'";
				$query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$from_date = date("d.m.Y",strtotime($row['fromdate']));
					$to_date = date("d.m.Y",strtotime($row['todate']));
				}
			?>
			
				<div class="box-body" style="min-height:400px;"><br/><br/><br/>
					<div class="row">
						<div class="col-md-18">
							<div class="col-md-18" align="center">
								<form action="acc_updatevouchers.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<table style="width:95%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th><label>Trnum<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>DC No.</label></th>
												<th><label>From CoA<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>To CoA<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Sector<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Remarks</label></th>
											</tr>
										</thead>
										<?php
											$cedit = 0;
											$sqledit = "SELECT * FROM `acc_vouchers` WHERE `trnum` LIKE '$invoiceid' AND `flag` = '0'"; $queryedit = mysqli_query($conn,$sqledit);
											while($rowedit = mysqli_fetch_assoc($queryedit)){
										?>
											<tr style="line-height:30px;">
												<td><input type="text" name="trnum" id="trnum" value="<?php echo $rowedit['trnum']; ?>" class="form-control" readonly ></td>
												<td><input type="text"  name="pdate" id="pdate"class="form-control vou_datepickers" value="<?php echo date("d.m.Y",strtotime($rowedit['date'])); ?>" readonly></td>
												<td><input type="text" name="dcno" id="dcno" value="<?php echo $rowedit['dcno']; ?>" class="form-control" style="width:80px;"></td>
												<td><select name="fcoa" id="fcoa" class="form-control select2" > <option value="select">select</option><?php foreach($acode as $fcode){ ?> <option <?php if($rowedit['fcoa'] == $fcode){ echo 'selected'; } ?> value="<?php echo $acode[$fcode]; ?>"><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="tcoa" id="tcoa" class="form-control select2" ><option value="select">select</option><?php foreach($acode as $fcode){ ?> <option <?php if($rowedit['tcoa'] == $fcode){ echo 'selected'; } ?> value="<?php echo $acode[$fcode]; ?>"><?php echo $adesc[$fcode]; ?></option> <?php } ?></select></td>
												<td><input type="text" name="amount" id="amount" class="form-control" value="<?php echo $rowedit['amount']; ?>" onkeyup="granttotalamount()" onchange="getamountinwords()"></td>
												<td><select name="sector" id="sector" class="form-control select2" > <option value="select">select</option> <?php foreach($wcode as $fcode){ ?> <option <?php if($rowedit['warehouse'] == $fcode){ echo 'selected'; } ?> value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><textarea name="remark" id="remark" class="form-control" style="height: 23px;"><?php echo $rowedit['remarks']; ?></textarea></td>
												<td style="visibility:hidden;"><input type="text" name="gtamtinwords" id="gtamtinwords" class="form-control" value="<?php echo $row['amtinwords']; ?>" readonly /></td>
											</tr>
										<?php 
											}
										?>
									</table><br/><br/><br/>
									<div class="box-body" align="center">
										<button type="submit" name="submittrans" id="submittrans" value="updatepage" class="btn btn-flat btn-social btn-linkedin">
											<i class="fa fa-save"></i> Update
										</button>&ensp;&ensp;&ensp;&ensp;
										<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
											<i class="fa fa-trash"></i> Cancel
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php include "header_foot.php"; ?>
		<script src="dist/js/adminlte.min.js"></script>
		<script src="dist/js/demo.js"></script>
		<script>
			function getamountinwords() {
				var a = document.getElementById("amount").value;
				var b = convertNumberToWords(a);
				document.getElementById("gtamtinwords").value = b;
			}
			function redirection_page(){ window.location.href = "acc_displayvouchers.php"; }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function checkval(){
				document.getElementById("submittrans").style.visibility = "hidden";
				var b = document.getElementById("fcoa").value;
				var c = document.getElementById("tcoa").value;
				var d = document.getElementById("amount").value;
				var e = document.getElementById("sector").value;
				var k = true;
				if(b.match("select")){
					alert("Please select From CoA name");
					k = false;
				}
				else if(c.match("select")){
					alert("Please select To CoA");
					k = false;
				}
				else if(d == 0 || d == "" || d.lenght == 0){
					alert("Please enter amount");
					k = false;
				}
				else if(e.match("select")){
					alert("Please select sector");
					k = false;
				}
				else {
					k = true;
				}
				if(k === true){
					//alert(k);
					return true;
				}
				else if(k == false){
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
				else {
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
			}
			document.getElementById("form_id").onkeypress = function(e) {
				var key = e.charCode || e.keyCode || 0;     
				if (key == 13) {
				//alert("No Enter!");
				e.preventDefault();
				}
			} 
		</script>
		<script src="main_numbertoamount.js"></script>
        <script src="handle_ebtn_as_tbtn.js"></script>
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>