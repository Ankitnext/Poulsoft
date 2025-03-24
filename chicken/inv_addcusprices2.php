<?php
//inv_addcusprices2.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['dcusprices2'];
	$dbname = $_SESSION['dbase'];
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
	
	$sql = "SELECT * FROM `main_officetypes` WHERE `description` = 'Shop'";  $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $office_code = $row['code']; }
	$sql = "SELECT * FROM `inv_sectors` WHERE `type` = '$office_code' AND `active` = '1'";  $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
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
			<h1>Fill all required fields</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
				<li><a href="#">Item</a></li>
				<li class="active">Customer Prices</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
				<div class="box-body">
					<div class="col-md-12">
						<form action="inv_updatecusprices2.php" method="post" onsubmit="return checkval()">
							<div class="row">
								<div class="form-group col-md-2" style="visibility:hidden;">
									<label>incr<b style="color:red;">&nbsp;*</b></label>
									<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
								</div>
							</div>
							<div class="row">
								<div class="col-md-12" align="center">
									<table class="table1" style="width:50%;">
										<thead>
											<tr>
												<th style="width: 160px;text-align:center;"><label>customer<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Item Description<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Price Type<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Add/Deduct Price<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>Type 2<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>value 2<b style="color:red;">&nbsp;*</b></label></th>
												<th style="text-align:center;"><label>+/-</label></th>
											</tr>
										</thead>
										<tbody id="tbody">
											<tr>
												<td><div class="form-group col-md-12"><select name="ccode0" id="ccode0" class="form-control select" style="width:150px;"><option value="select">select</option><option value="all">-All-</option><?php foreach($cus_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $cus_name[$ucode]; ?></option><?php } ?></select></div></td>
												<td><div class="form-group col-md-12"><select name="idesc0" id="idesc_0" class="form-control select" style="width:150px;"><option value="select">select</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $item_name[$ucode]; ?></option><?php } ?></select></div></td>
												<td><div class="form-group col-md-6"><select name="ptype0" id="ptype0" class="form-control select" style="width:150px;"><option value="select">select</option><option value="A">Add</option><option value="D">Deduct</option><option value="F">Fixed</option></select></div></td>
												<td><div class="form-group col-md-6"><input type="text" name="iprice0" id="iprice0" class="form-control" style="width:150px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" /></div></td>
												<td><div class="form-group col-md-6"><select name="ptype20" id="ptype20" class="form-control select" style="width:150px;"><option value="none">None</option><option value="M">Multiply</option></select></div></td>
												<td><div class="form-group col-md-6"><input type="text" name="iprice20" id="iprice20" class="form-control" style="width:150px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" /></div></td>
												<td><div class="form-group col-md-12"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a><a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></div></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4"></div>
								<div class="col-md-4">
									<div class="box-body" align="center">
										<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin">
											<i class="fa fa-save"></i> Save
										</button>&ensp;&ensp;&ensp;&ensp;
										<button type="button" name="cancelled" id="cancelled" class="btn btn-flat btn-social btn-google" onclick="redirection_page()">
											<i class="fa fa-trash"></i> Cancel
										</button>
									</div>
								</div>
								<div class="col-md-3"></div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>
		<script>
			function rowgen(){
				var a = document.getElementById("incr").value;
				document.getElementById("addval["+a+"]").style.visibility = "hidden";
				document.getElementById("rmval["+a+"]").style.visibility = "hidden";
				a++;
				document.getElementById("incr").value = a;
				html = '';
				html+= '<tr id="tr_'+a+'">';
				html+= '<td><div class="form-group col-md-12"><select name="ccode'+a+'" id="ccode'+a+'" class="form-control select" style="width:150px;"><option value="select">select</option><option value="all">-All-</option><?php foreach($cus_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $cus_name[$ucode]; ?></option><?php } ?></select></div></td>';
				html+= '<td><div class="form-group col-md-12"><select name="idesc'+a+'" id="idesc_'+a+'" class="form-control select" style="width:150px;"><option value="select">select</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $item_name[$ucode]; ?></option><?php } ?></select></div></td>';
				html+= '<td><div class="form-group col-md-6"><select name="ptype'+a+'" id="ptype'+a+'" class="form-control select" style="width:150px;"><option value="select">select</option><option value="A">Add</option><option value="D">Deduct</option><option value="F">Fixed</option></select></div></td>';
				html+= '<td><div class="form-group col-md-6"><input type="text" name="iprice'+a+'" id="iprice'+a+'" class="form-control" style="width:150px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" /></div></td>';
				html+= '<td><div class="form-group col-md-6"><select name="ptype2'+a+'" id="ptype2'+a+'" class="form-control select" style="width:150px;"><option value="none">None</option><option value="M">Multiply</option></select></div></td>';
				html+= '<td><div class="form-group col-md-6"><input type="text" name="iprice2'+a+'" id="iprice2'+a+'" class="form-control" style="width:150px;" onkeyup="validatenum(this.id)" onchange="validateamount(this.id)" /></div></td>';
				html+= '<td style="width: 60px;"><div class="form-group col-md-12"><a href="JavaScript:Void(0); "name="addval[]" id="addval['+a+']" onclick="rowgen()"><i class="fa fa-plus"></i></a><a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+a+']" class="delete" onclick="rowdes()" title="'+a+'"><i class="fa fa-minus" style="color:red;"></i></a></div></td>';
				html+= '</tr>';
				$('#tbody').append(html);
				$('.select').select2();
			}
			function rowdes(){
				var a = document.getElementById("incr").value;
				document.getElementById('tr_'+a).remove();
				a--;
				if(a > 0){
					document.getElementById("addval["+a+"]").style.visibility = "visible";
					document.getElementById("rmval["+a+"]").style.visibility = "visible";
				}
				else{
					document.getElementById("addval["+a+"]").style.visibility = "visible";
				}
				document.getElementById("incr").value = a;
			}
			function checkval(){
				var a = document.getElementById("incr").value;
				var b = c = d = e = f = g = "";
				var l = true;
				for(var x = 0;x <= a;x++){
					if(l == true){
						g = x + 1;
						b = document.getElementById("ccode"+x).value;
						c = document.getElementById("idesc_"+x).value;
						e = document.getElementById("ptype"+x).value;
						f = document.getElementById("iprice"+x).value;
						
						if(b.match("select")){
							alert("Please select Customer in row: "+g);
							document.getElementById("ccode"+x).focus();
							l = false;
						}
						else if(c.match("select")){
							alert("Please select Item Description in row: "+g);
							document.getElementById("idesc_"+x).focus();
							l = false;
						}
						else if(e.match("select")){
							alert("Please select Price value Add/Deduct in row: "+g);
							document.getElementById("ptype"+x).focus();
							l = false;
						}
						else if(f.length == 0){
							alert("Please Enter Price in row: "+g);
							document.getElementById("iprice"+x).focus();
							l = false;
						}
						else{
							l = true;
						}
					}
				}
				if(l == true){
					return true;
				}
				else if(l == false){
					return false;
				}
				else{
					return false;
				}
			}
			function redirection_page(){
				var a = '<?php echo $cid; ?>';
				window.location.href = "main_displaycusprices2.php?cid="+a;
			}
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
		</script>
	</body>
</html>
<?php include "header_foot.php"; ?>