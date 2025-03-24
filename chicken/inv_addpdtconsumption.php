<?php
//inv_addpdtconsumption.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['pdtcons'];
	$sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $cat_code[$row['code']] = $row['code']; $cat_name[$row['code']] = $row['description']; $cat_rflag[$row['code']] = $row['rflag']; }
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Fill all required fields</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Item</a></li>
				<li class="active">Conversion</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
				<div class="box-body">
					<div class="col-md-12">
						<form action="inv_updatepdtconsumption.php" method="post" role="form" onsubmit="return checkval(this.id)" name="form_name" id = "form_id" >
							<div class="row">
								<div class="form-group col-md-2" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
									<label>incr<b style="color:red;">&nbsp;*</b></label>
									<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<table class="table1" style="width:100%;">
										<thead>
											<tr>
												<th><label>From Category<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>From Items<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>To Category<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>To Items<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>+/-</label></th>
											</tr>
										</thead>
										<tbody id="tbody">
											<tr>
												<td><div class="form-group col-md-12"><select name="from_cat0" id="from_cat0" class="form-control select" style="width:250px;" onchange="fetchitemdetails(this.id)"><option value="select">select</option> <?php foreach($cat_code as $ccode){ ?> <option value="<?php echo $cat_code[$ccode]; ?>"><?php echo $cat_name[$ccode]; ?></option> <?php } ?> </select></div></td>
												<td><div class="form-group col-md-12"><select name="from_item0" id="from_item0" class="form-control select" style="width:250px;"><option value="select">select</option></select></div></td>
												<td><div class="form-group col-md-12"><select name="to_cat0" id="to_cat0" class="form-control select" style="width:250px;" onchange="fetchitemdetails(this.id)"><option value="select">select</option> <?php foreach($cat_code as $ccode){ ?> <option value="<?php echo $cat_code[$ccode]; ?>"><?php echo $cat_name[$ccode]; ?></option> <?php } ?> </select></div></td>
												<td><div class="form-group col-md-12"><select name="to_item0[]" id="to_item0" class="form-control select" multiple style="width:250px;"><option value="select" disabled >select</option></select></div></td>
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
				html+= '<td><div class="form-group col-md-12"><select name="from_cat'+a+'[]" id="from_cat'+a+'" class="form-control select" style="width:250px;" onchange="fetchitemdetails(this.id)"><option value="select">select</option><?php foreach($cat_code as $ccode){ ?> <option value="<?php echo $cat_code[$ccode]; ?>"><?php echo $cat_name[$ccode]; ?></option> <?php } ?> </select></div></td>';
				html+= '<td><div class="form-group col-md-12"><select name="from_item'+a+'[]" id="from_item'+a+'" class="form-control select" style="width:250px;"><option value="select" >select</option></select></div></td>';
				html+= '<td><div class="form-group col-md-12"><select name="to_cat'+a+'[]" id="to_cat'+a+'" class="form-control select" style="width:250px;" onchange="fetchitemdetails(this.id)"><option value="select">select</option><?php foreach($cat_code as $ccode){ ?> <option value="<?php echo $cat_code[$ccode]; ?>"><?php echo $cat_name[$ccode]; ?></option> <?php } ?> </select></div></td>';
				html+= '<td><div class="form-group col-md-12"><select name="to_item'+a+'[]" id="to_item'+a+'" class="form-control select" multiple style="width:250px;"><option value="select">select</option></select></div></td>';
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
			function checkval(){
				var a = document.getElementById("incr").value;
				var b = c = d = e = ""; var l = true;
				for(var i = 0;i <= a;i++){
					if(l == true){
						b = c = d = e = "";
						for (var option of document.getElementById('from_cat'+i).options){
							if (option.selected) {
								b+= option.value;
							}
						}
						for (var option of document.getElementById('from_item'+i).options){
							if (option.selected) {
								e+= option.value;
							}
						}
						for (var option of document.getElementById('to_cat'+i).options){
							if (option.selected) {
								d+= option.value;
							}
						}
						for (var option of document.getElementById('to_item'+i).options){
							if (option.selected) {
								c+= option.value;
							}
						}
						if(b.match("select") || b.length == 0){
							alert("Select From Category in row:- "+i);
							document.getElementById('from_cat'+i).focus();
							l = false;
						}
						else if(e.match("select") || e.length == 0){
							alert("Select From Item in row:- "+i);
							document.getElementById('from_item'+i).focus();
							l = false;
						}
						else if(d.match("select") || d.length == 0){
							alert("Select To Category in row:- "+i);
							document.getElementById('to_cat'+i).focus();
							l = false;
						}
						else if(c.length == 0){
							alert("Select To Items in row:- "+i);
							document.getElementById('to_item'+i).focus();
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
				window.location.href = "inv_pdtconsumption.php?cid="+a;
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