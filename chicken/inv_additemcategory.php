<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Fill all required fields</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">CoA</a></li>
				<li class="active">Item-Category</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<form action="inv_updateitemcategory.php" method="get" role="form" onsubmit="return checkval(this.id)" name="form_name" id = "form_id" >
								<div class="form-group col-md-9">
									<label>Description<b style="color:red;">&ensp;*</b></label>
									<input type="text" name="cdesc" id="cdesc" class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id);colorchange(this.id);">
								</div>
								<div class="form-group col-md-3">
									<label>Prefix<b style="color:red;">&ensp;*</b></label>
									<input type="text" name="prefix" id="prefix" class="form-control" placeholder="Enter description..." onkeyup="validateprefix(this.id);colorchange(this.id);">
								</div>
								<div class="col-md-18">
									<label>
										<input type="checkbox" name="newaccounts" id="newaccounts" value="new_acc" onclick="showDetails(this.id,this.value)">Create &amp; attach new accounts <!--Fill Credit / Debit Details-->
									</label>
								</div>
								<div class="col-md-18">
									<label>
										<input type="checkbox" name="newaccounts" id="preaccounts" value="pre_acc" onclick="showDetails(this.id,this.value)">Pre-defined Accounts <!--Fill Credit / Debit Details-->
									</label>
								</div>
								<div class="col-md-18" id="itemaccounts" style="display:none;">
									<div class="form-group col-md-3">
										<label>Item A/c<b style="color:red;">&nbsp;*</b></label>
										<select name="iac" id="iac" class="form-control select2" style="width: 100%;">
											<option value="select">select</option>
											<?php
												$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'STOCK -%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div class="form-group col-md-3" id="icogs1">
										<label>COGS A/c<b style="color:red;">&nbsp;*</b></label>
										<select name="icogs" id="icogs" class="form-control select2" style="width: 100%;">
											<option value="select">select</option>
											<?php
												$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'COGS -%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div class="form-group col-md-3" id="isalesac1">
										<label>Sales A/c<b style="color:red;">&nbsp;*</b></label>
										<select name="isalesac" id="isalesac" class="form-control select2" style="width: 100%;">
											<option value="select">select</option>
											<?php
												$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Sales -%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div class="form-group col-md-3" id="israc1">
										<label>Sales Return A/c<b style="color:red;">&nbsp;*</b></label>
										<select name="israc" id="israc" class="form-control select2" style="width: 100%;">
											<option value="select">select</option>
											<?php
												$sql = "SELECT * FROM `acc_coa` WHERE `description` LIKE 'Sales Return -%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
												while($row = mysqli_fetch_assoc($query)){
											?>
													<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
											<?php
												}
											?>
										</select>
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
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>
		<script>
		function checkval(){
			var a = document.getElementById("cdesc").value;
			var b = document.getElementById("prefix").value;
			var c = document.getElementById("iac").value;
			var d = document.getElementById("icogs").value;
			var e = document.getElementById("isalesac").value;
			var f = document.getElementById("israc").value;
			var g = document.getElementById("newaccounts");
			var h = document.getElementById("preaccounts");
			if(a.length == 0){
				alert("Enter Description ..!");
				document.getElementById("cdesc").focus();
				document.getElementById("cdesc").style.border = "1px solid red";
				return false;
			}
			else if(b.length == 0){
				alert("Enter Prefix ..!");
				document.getElementById("prefix").focus();
				document.getElementById("prefix").style.border = "1px solid red";
				return false;
			}
			else if(g.checked == false && h.checked == false){
				alert("Please select Accounts ..!");
				return false;
			}
			else if(g.checked == false && h.checked == true){
				if(c.match("select")){
					alert("Select Item account ..!");
					document.getElementById("iac").focus();
					document.getElementById("iac").style.border = "1px solid red";
					return false;
				}
				else if(d.match("select")){
					alert("Select COGS account ..!");
					document.getElementById("icogs").focus();
					document.getElementById("icogs").style.border = "1px solid red";
					return false;
				}
				else if(e.match("select")){
					alert("Select Sales account ..!");
					document.getElementById("isalesac").focus();
					document.getElementById("isalesac").style.border = "1px solid red";
					return false;
				}
				else if(f.match("select")){
					alert("Select Sales Return account ..!");
					document.getElementById("israc").focus();
					document.getElementById("cdesc").style.border = "1px solid red";
					return false;
				}
			}
			else {
				return true;
			}
		}
		function colorchange(x){
			document.getElementById(x).style.border = "1px solid green";
		}
		function redirection_page(){
			window.location.href = "inv_displayitemcategory.php";
		}
		function validatename(x) {
			expr = /^[a-zA-Z0-9 (.&)_*?-]*$/;
			var a = document.getElementById(x).value;
			if(a.length > 50){
				a = a.substr(0,a.length - 1);
			}
			if(!a.match(expr)){
				a = a.replace(/[^a-zA-Z0-9 (.&)_*?-]/g, '');
			}
			document.getElementById(x).value = a;
		}
		function validateprefix(x) {
			expr = /^[a-zA-Z]*$/;
			var a = document.getElementById(x).value;
			if(a.length > 5){
				a = a.substr(0,a.length - 1);
			}
			if(!a.match(expr)){
				a = a.replace(/[^a-zA-Z]/g, '');
			}
			a = a.toUpperCase();
			document.getElementById(x).value = a;
		}
		function showDetails(a,b) {
			var checkBox = document.getElementById(a);
			if(b.match("new_acc")){
				var a = document.getElementById("preaccounts");
				var b = document.getElementById("itemaccounts");
				if (checkBox.checked == true){
					a.checked = false;
					b.style.display = "none";
				}
				else { }
			}
			else if(b.match("pre_acc")){
				var a = document.getElementById("newaccounts");
				var b = document.getElementById("itemaccounts");
				if (checkBox.checked == true){
					a.checked = false;
					b.style.display = "block";
				}
				else {
				}
			}
			else {}
			
		}
		document.getElementById("form_id").onkeypress = function(e) {
				var key = e.charCode || e.keyCode || 0;     
				if (key == 13) {
				//alert("No Enter!");
				e.preventDefault();
				}
			} 
		</script>
	</body>
</html>
<?php include "header_foot.php"; ?>