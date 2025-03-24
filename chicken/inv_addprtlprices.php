<?php
//inv_addprtlprices.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['rtlprices']; $office_type = "";
	$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE 'Shop' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ if($office_type == ""){ $office_type = $row['code']; } else{ $office_type = $office_type."','".$row['code']; } }
	$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$office_type') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $shop_code[$row['code']] = $row['code']; $shop_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `item_details` WHERE `active` = '1' AND `rflag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
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
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Item</a></li>
				<li class="active">Prices</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
				<div class="box-body">
					<div class="col-md-12">
						<form action="inv_updateprtlprices.php" method="post" role="form" onsubmit="return checkval(this.id)" name="form_name" id = "form_id" >
							<div class="row">
								<div class="form-group col-md-2"><!-- style="visibility:hidden;"-->
									<label>Date<b style="color:red;">&nbsp;*</b></label>
									<input type="text" name="date" id="date" class="form-control datepickers" value="<?php echo date("d.m.Y"); ?>"/>
								</div>
								<div class="form-group col-md-2"><!-- style="visibility:hidden;"-->
									<label>Shops<b style="color:red;">&nbsp;*</b></label>
									<select name="loc" id="loc" class="form-control select">
										<!--<option value="select">-Select-</option>-->
										<?php
										foreach($shop_code as $scode){
										?>
										<option value="<?php echo $scode; ?>"><?php echo $shop_name[$scode]; ?></option>
										<?php
										}
										?>
									</select>
								</div>
								<div class="form-group col-md-2" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
									<label>incr<b style="color:red;">&nbsp;*</b></label>
									<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<table class="table1" style="width:90%;">
										<thead>
											<tr>
												<th><label>Items<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>From Qty<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>To Qty<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Edit<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>+/-</label></th>
											</tr>
										</thead>
										<tbody id="tbody">
											<tr>
												<td><div class="form-group col-md-12"><select name="items[]" id="items[0]" class="form-control select" style="width:250px;"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></div></td>
												<td><div class="form-group col-md-12"><input type="number" step=".01" name="fromqty[]" id="fromqty[0]" class="form-control" onchange="validateamount(this.id)"/></div></td>
												<td><div class="form-group col-md-12"><input type="number" step=".01" name="toqty[]" id="toqty[0]" class="form-control" onchange="validateamount(this.id)" /></div></td>
												<td><div class="form-group col-md-12"><input type="number" step=".01" name="prices[]" id="prices[0]" class="form-control" onchange="validateamount(this.id)" /></div></td>
												<td><div class="form-group col-md-12"><input type="checkbox" name="eflags[]" id="eflags[0]" class="form-control1" /></div></td>
												<td><div class="form-group col-md-12"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></div></td>
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
				html+= '<td><div class="form-group col-md-12"><select name="items[]" id="items['+a+']" class="form-control select" style="width:250px;"><option value="select">select</option><?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>"><?php echo $item_name[$icode]; ?></option><?php } ?></select></div></td>';
				html+= '<td><div class="form-group col-md-12"><input type="number" step=".01" name="fromqty[]" id="fromqty['+a+']" class="form-control" onchange="validateamount(this.id)" /></div></td>';
				html+= '<td><div class="form-group col-md-12"><input type="number" step=".01" name="toqty[]" id="toqty['+a+']" class="form-control" onchange="validateamount(this.id)" /></div></td>';
				html+= '<td><div class="form-group col-md-12"><input type="number" step=".01" name="prices[]" id="prices['+a+']" class="form-control" onchange="validateamount(this.id)" /></div></td>';
				html+= '<td><div class="form-group col-md-12"><input type="checkbox" name="eflags[]" id="eflags['+a+']" /></div></td>';
				html+= '<td style="width: 60px;"><div class="form-group col-md-12"><a href="JavaScript:Void('+a+'); "name="addval[]" id="addval['+a+']" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+a+']" class="delete" onclick="rowdes()" title="'+a+'"><i class="fa fa-minus" style="color:red;"></i></a></div></td>';
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
				var b = c = d = e = f = ""; var l = true;
				for(var x = 0;x <=a;x++){
					if(l == true){
						b = document.getElementById("items["+x+"]").value;
						c = document.getElementById("fromqty["+x+"]").value;
						d = document.getElementById("toqty["+x+"]").value;
						e = document.getElementById("prices["+x+"]").value;
						f = x + 1;
						if(b.match("select")){
							alert("Please select Item in row: "+f);
							document.getElementById("items["+x+"]").focus();
							l = false;
						}
						else if(c.length == 0 || c == ""){
							alert("Please From Quantity in row: "+f);
							document.getElementById("fromqty["+x+"]").focus();
							l = false;
						}
						else if(d.length == 0 || d == ""){
							alert("Please To Quantity in row: "+f);
							document.getElementById("toqty["+x+"]").focus();
							l = false;
						}
						else if(e.length == 0 || e == ""){
							alert("Please Price in row: "+f);
							document.getElementById("prices["+x+"]").focus();
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
				window.location.href = "inv_prtlprices.php?cid="+a;
			}
			function fetchitemdetails(a){
				var b = a.split("_");
				var ft = b[0];
				var c = b[1].split("cat");
				var d = c[1];
				var e = document.getElementById(a).value;
				var f = ft+"_item"+d;
				//alert(f);
				removeAllOptions(document.getElementById(f));
				if(ft.match("from")){
					myselect = document.getElementById(f); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				}
				else{
					myselect = document.getElementById(f); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.setAttribute("disabled", "disabled"); theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				}
				<?php
					$sql = "SELECT * FROM `item_details` WHERE `rflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){
						$category = $row['category'];
						echo "if(e == '$category'){";
				?>
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				<?php
						echo "}";
					}
				?>
			}
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
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