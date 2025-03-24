<?php
//inv_additemmapping.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['retitemmap'];
	$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE 'Shop' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $shop_type_code = $row['code']; }
	$sql = "SELECT * FROM `inv_sectors` WHERE `type` LIKE '$shop_type_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
	$sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cat_code[$row['code']] = $row['code']; $cat_name[$row['code']] = $row['description']; }
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Fill all required fields</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Item</a></li>
				<li class="active">Retail-Items</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
				<div class="box-body">
					<div class="col-md-12">
						<form action="inv_updateitemmapping.php" method="post" role="form" onsubmit="return checkval(this.id)" name="form_name" id = "form_id" >
							<div class="row">
								<div class="form-group col-md-2" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
									<label>incr<b style="color:red;">&nbsp;*</b></label>
									<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
								</div>
							</div>
							<div class="row">
								<div class="col-md-2"></div>
								<div class="col-md-8">
									<table class="table1" style="width:100%;">
										<thead>
											<tr>
												<th><label>Retails Category<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Retails Description<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Shops<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>+/-</label></th>
											</tr>
										</thead>
										<tbody id="tbody">
											<tr>
												<td><div class="form-group col-md-12"><select name="ret_cat0" id="ret_cat0" class="form-control select" style="width:250px;" onchange="fetchitemdetails(this.id)"><option value="select" >select</option> <?php foreach($cat_code as $ccode){ ?> <option value="<?php echo $cat_code[$ccode]; ?>"><?php echo $cat_name[$ccode]; ?></option> <?php } ?> </select></div></td>
												<td><div class="form-group col-md-12"><select name="ret_item0[]" id="ret_item0" class="form-control select" multiple style="width:250px;"><option value="select" disabled >select</option></select></div></td>
												<td><div class="form-group col-md-12"><select name="ret_shop0[]" id="ret_shop0" class="form-control select" multiple style="width:250px;"><option value="select" disabled >select</option> <?php foreach($sector_code as $scode){ ?> <option value="<?php echo $sector_code[$scode]; ?>"><?php echo $sector_name[$scode]; ?></option> <?php } ?> </select></div></td>
												<td><div class="form-group col-md-12"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></div></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-md-2"></div>
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
				html+= '<td><div class="form-group col-md-12"><select name="ret_cat'+a+'" id="ret_cat'+a+'" class="form-control select" style="width:250px;" onchange="fetchitemdetails(this.id)"><option value="select" >select</option> <?php foreach($cat_code as $ccode){ ?> <option value="<?php echo $cat_code[$ccode]; ?>"><?php echo $cat_name[$ccode]; ?></option> <?php } ?> </select></div></td>';
				html+= '<td><div class="form-group col-md-12"><select name="ret_item'+a+'[]" id="ret_item'+a+'" class="form-control select" multiple style="width:250px;"><option value="select" disabled >select</option></select></div></td>';
				html+= '<td><div class="form-group col-md-12"><select name="ret_shop'+a+'[]" id="ret_shop'+a+'" class="form-control select" multiple style="width:250px;"><option value="select" disabled >select</option> <?php foreach($sector_code as $scode){ ?> <option value="<?php echo $sector_code[$scode]; ?>"><?php echo $sector_name[$scode]; ?></option> <?php } ?> </select></div></td>';
				html+= '<td style="width: 60px;"><div class="form-group col-md-12"><a href="JavaScript:Void(0); "name="addval[]" id="addval['+a+']" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+a+']" class="delete" onclick="rowdes()" title="'+a+'"><i class="fa fa-minus" style="color:red;"></i></a></div></td>';
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
			function fetchitemdetails(a){
				var b = a.split("ret_cat");
				var c = document.getElementById(a).value;
				removeAllOptions(document.getElementById("ret_item"+b[1]));
				myselect = document.getElementById("ret_item"+b[1]); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("Select"); theOption1.value = "select"; theOption1.setAttribute("disabled", "disabled"); theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				<?php
					$sql = "SELECT * FROM `item_details` WHERE `rflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){
						$category = $row['category'];
						echo "if(c == '$category'){";
				?>
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				<?php
						echo "}";
					}
				?>
			}
			function checkval(){
				var a = document.getElementById("incr").value;
				var b = c = d = ""; var l = true;
				for(var i = 0;i <= a;i++){
					if(l == true){
						c = d = "";
						b = document.getElementById('ret_cat'+i).value;
						for (var option of document.getElementById('ret_item'+i).options){
							if (option.selected) {
								d+= option.value;
							}
						}
						for (var option of document.getElementById('ret_shop'+i).options){
							if (option.selected) {
								c+= option.value;
							}
						}
						if(b.match("select")){
							alert("Select Ratail Category in row:- "+i);
							document.getElementById('ret_cat'+i).focus();
							l = false;
						}
						else if(d.length == 0){
							alert("Select Ratail Items in row:- "+i);
							document.getElementById('ret_item'+i).focus();
							l = false;
						}
						else if(c.length == 0){
							alert("Select Ratail Shops in row:- "+i);
							document.getElementById('ret_shop'+i).focus();
							l = false;
						}
					}
					else{
						l = false;
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
				window.location.href = "inv_itemmapping.php?cid="+a;
			}
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