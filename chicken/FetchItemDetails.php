<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
?>
<html>
	<head>
		<style>
.select2-container .select2-selection--single{
	box-sizing:border-box;
	cursor:pointer;
	display:block;
	height:23px;
	user-select:none;
	-webkit-user-select:none;
}.select2-container--default .select2-selection--single{background-color:#fff;border:1px solid #aaa;border-radius:4px}
.select2-container--default .select2-selection--single .select2-selection__rendered{color:#444;line-height:18px}
.select2-container--default .select2-selection--single .select2-selection__clear{cursor:pointer;float:right;font-weight:bold}
.select2-container--default .select2-selection--single .select2-selection__placeholder{color:#999}
.select2-container--default .select2-selection--single .select2-selection__arrow{height:23px;position:absolute;top:1px;right:1px;width:20px}
.select2-container--default .select2-selection--single .select2-selection__arrow b{border-color:#888 transparent transparent transparent;border-style:solid;border-width:5px 4px 0 4px;height:0;left:50%;margin-left:-4px;margin-top:-2px;position:absolute;top:50%;width:0}
		#tab3 .form-control {
			width: 90px;
			height: 23px;
		}
		.form-control {
			width: 145px;
			height: 23px;
		}
		.disabledbutton{
			pointer-events: none;
			opacity: 0.4;
		}
		body {
			over-flow: auto;
		}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Fill all required fields</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Purchases</a></li>
				<li class="active">Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$c = ""; foreach ($_GET['icats'] as $itemcat){ if($c == ""){ $c = $itemcat; } else { $c = $c."','".$itemcat; } }
			?>
				<div class="box-body">
					<div class="col-md-18">
						<label>
						<input type="checkbox" name="stdprice" id="stdprice" value="SP" onclick="showDetails(this.id,this.value)">Standard Price <!--Fill Credit / Debit Details-->
						</label>
					</div>
					<div class="col-md-18" id="standardprice" style="display:none;">
						<form action="inv_updateprice.php" method="get" role="form" onsubmit="return checkval()">
						<div class="box-body col-md-18" align="left">
								<div class="form-group col-md-2">
									<label>From Date<b style="color:red;">&nbsp;*</b></label>
									<input type="text" name="fdate" id="datepickers" class="form-control" value="<?php echo $fdate; ?>">
								</div><br/><br/>
								<table style="width:30%;line-height:30px;" id="tab3">
									<tr style="line-height:30px;">
										<th><label>Item Description<b style="color:red;">&nbsp;*</b></label></th>
										<th><label>Item Price<b style="color:red;">&nbsp;*</b></label></th>
									</tr>
									<?php
										$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$c')"; $query = mysqli_query($conn,$sql);
										$a = 0;
										while($row = mysqli_fetch_assoc($query)){
											$a = $a + 1;
									?>
										<tr>
											<td>
												<select name="stdidesc[]" id="idesc[<?php echo $a; ?>]" style="width:150px;" class="select2"><option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option></select>
											</td>
											<td>
												<input name="stdiprice[]" id="iprice[<?php echo $a; ?>]" class="form-control" onchange="validateamount(this.id)">
											</td>
										</tr>
									<?php
										}
									?>
								</table>
							</div><br/><br/><br/>
							<div class="box-body" align="center">
								<button type="submit" name="substdprice" id="substdprice" value="addpage" class="btn btn-flat btn-social btn-linkedin">
									<i class="fa fa-save"></i> Save
								</button>&ensp;&ensp;&ensp;&ensp;
								<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
									<i class="fa fa-trash"></i> Cancel
								</button>
							</div>
						</form>
					</div>
					<div class="col-md-18">
						<label>
						<input type="checkbox" name="cusprice" id="cusprice" value="CP" onclick="showDetails(this.id,this.value)">Customer based Item Price <!--Fill Credit / Debit Details-->
						</label>
					</div>
					<div class="col-md-18" id="customerprice" style="display:none;">
						<form action="inv_updateprice.php" method="get" role="form" onsubmit="return checkval()">
							<div class="col-md-18">
								<div class="form-group col-md-2">
									<label>From Date<b style="color:red;">&nbsp;*</b></label>
									<input type="text" name="fdate" id="datepickers1" class="form-control" value="<?php echo $fdate; ?>">
								</div>
								<div class="form-group col-md-2">
									<label>Customer<b style="color:red;">&nbsp;*</b></label>
									<select name="ccode" id="ccode" class="form-control select2" style="width: 100%;">
										<option value="select">select</option>
										<?php
											$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
											while($row = mysqli_fetch_assoc($query)){
										?>
												<option value="<?php echo $row['code']; ?>"><?php echo $row['name']; ?></option>
										<?php
											}
										?>
									</select>
								</div>
							</div><br/><br/><br/>
							<div class="box-body col-md-18" align="left">
								<table style="width:30%;line-height:30px;" id="tab3">
									<tr style="line-height:30px;">
										<th><label>Item Description<b style="color:red;">&nbsp;*</b></label></th>
										<th><label>Item Price<b style="color:red;">&nbsp;*</b></label></th>
									</tr>
									<?php
										$sql = "SELECT * FROM `item_details` WHERE `category` IN ('$c')"; $query = mysqli_query($conn,$sql);
										$a = 0;
										while($row = mysqli_fetch_assoc($query)){
											$a = $a + 1;
									?>
										<tr>
											<td>
												<select name="cusidesc[]" id="cusidesc[<?php echo $a; ?>]" style="width:150px;" class="select2"><option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option></select>
											</td>
											<td>
												<input name="cusiprice[]" id="cusiprice[<?php echo $a; ?>]" class="form-control" onchange="validateamount(this.id)">
											</td>
										</tr>
									<?php
										}
									?>
								</table>
							</div><br/><br/><br/>
							<div class="box-body" align="center">
								<button type="submit" name="subcusbprice" id="subcusbprice" value="addpage" class="btn btn-flat btn-social btn-linkedin">
									<i class="fa fa-save"></i> Save
								</button>&ensp;&ensp;&ensp;&ensp;
								<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
									<i class="fa fa-trash"></i> Cancel
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>
		<script>
			function checkval(){
				var a = confirm("Please confirm to Add this transaction ..!");
				if(a == true){
					return true;
				}
				else if(a == false){
					return false;
				}
				else {
					return false;
				}
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
			function validatenum(x) {
				expr = /^[0-9]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 50){
					a = a.substr(0,a.length - 1);
				}
				if(!a.match(expr)){
					a = a.replace(/[^0-9]/g, '');
				}
				document.getElementById(x).value = a;
			}
			function validateamount(x) {
				expr = /^[0-9.]*$/;
				var a = document.getElementById(x).value;
				if(a.length > 50){
					a = a.substr(0,a.length - 1);
				}
				while(!a.match(expr)){
					a = a.replace(/[^0-9.]/g, '');
				}
				if(a == ""){ a = 0; } else { }
				var b = parseFloat(a).toFixed(2);
				document.getElementById(x).value = b;
			}
		</script>
		<?php include "header_foot.php"; ?>
		<script>
			function redirection_page(){ window.location.href = "main_displayprices.php"; } </script>
		<script language="javascript">
		
		function showDetails(a,b) {
			var checkBox = document.getElementById(a);
			if(b.match("CP")){
				var text = document.getElementById("customerprice");
				var a = document.getElementById("stdprice");
				var b = document.getElementById("standardprice");
				if (checkBox.checked == true){
					a.checked = false;
					b.style.display = "none";
					text.style.display = "block";
				}
				else {
					text.style.display = "none";
				}
			}
			else if(b.match("SP")){
				var text = document.getElementById("standardprice");
				var a = document.getElementById("cusprice");
				var b = document.getElementById("customerprice");
				if (checkBox.checked == true){
					a.checked = false;
					b.style.display = "none";
					text.style.display = "block";
				}
				else {
					text.style.display = "none";
				}
			}
			else {}
			
		}
	</script>
	</body>
</html>
