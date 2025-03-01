<?php
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
				width: 14%;
				text-align: center;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Add Closing Stock</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Closing Stock Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today));
				$y = date("y"); $y2 = $y + 1; $m = date("m"); $d = date("d");
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$wcode[$row['code']] = $row['code'];
					$wdesc[$row['code']] = $row['description'];
				}
				
				$sql = "SELECT * FROM `main_transactionfields` WHERE `field` LIKE 'Closing Stock' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; }
				if($jals_flag == "" || $jals_flag == NULL){ $jals_flag = 0; }
				if($birds_flag == "" || $birds_flag == NULL){ $birds_flag = 0; }
				
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){
					$icode[$row['code']] = $row['code'];
					$idesc[$row['code']] = $row['description'];
				}
				$idisplay = ''; $ndisplay = 'style="display:none;';
			?>
			
				<div class="box-body" style="min-height:400px;">
					<div class="row">
						<div class="col-md-18" align="center">
							<div class="col-md-18" align="center">
								<form action="main_updateclosingstocks.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<div class="form-group col-md-18" style="visibility:hidden;">
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
										<input type="text" style="width:auto;" class="form-control" name="incrs" id="incrs" value="0">
									</div>
									<table style="width:100%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Warehouse<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Item Code<b style="color:red;">&nbsp;*</b></label></th>
												<?php if($jals_flag == 1) { echo '<th><label>Jals<b style="color:red;">&nbsp;*</b></label></th>'; } ?>
												<?php if($birds_flag == 1) { echo '<th><label>Birds<b style="color:red;">&nbsp;*</b></label></th>'; } ?>
												<th style="width:110px;"><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
												<th style="width:110px;"><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th style="width:110px;"><label>Amount</label></th>
												<th><label>Remarks</label></th>
												<th>Action</th>
												<th style="visibility:hidden;"><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
											</tr>
										</thead>
											<tr style="line-height:30px;">
												<td><input type="text" name="pdate[]" id="pdate[0]" class="form-control cst_datepickers" value="<?php echo $fdate; ?>" readonly></td>
												<td><select name="sector[]" id="sector[0]" class="form-control select2" style="width:180px;" > <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="code[]" id="code[0]" class="form-control select2" style="width:180px;" onchange="currentstockcheck();fetchavailablestock();fetchavailableprice();"> <?php foreach($icode as $fcode){ ?> <option value="<?php echo $icode[$fcode]; ?>"><?php echo $idesc[$fcode]; ?></option> <?php } ?> </select></td>
												<?php if($jals_flag == 1) { echo '<td><input name="jalqty[]" id="jalqty[0]" class="form-control" value="0" /></td>'; } ?>
												<?php if($birds_flag == 1) { echo '<td><input name="birdqty[]" id="birdqty[0]" class="form-control" value="0" /></td>'; } ?>
												<td><input name="cqty[]" id="cqty[0]" class="form-control" value="0" style="width:110px;" onkeyup="calculatetotal(this.id)"/></td>
												<td><input name="cpri[]" id="cpri[0]" class="form-control" value="0" style="width:110px;" onkeyup="calculatetotal(this.id)" /></td>
												<td><input name="camt[]" id="camt[0]" class="form-control" style="width:110px;" readonly /></td>
												<td><textarea name="remark[]" id="remark[0]" class="form-control" style="height: 23px;"></textarea></td>
												<td style="width: 100%;text-align:center;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
												<td style="visibility:hidden;"><input name="oqty[]" id="oqty[0]" class="form-control" value="0" readonly /></td>
											</tr>
									</table><br/><br/><br/>
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
			</div>
		</section>
		<?php include "header_foot.php"; ?>
		<script src="dist/js/adminlte.min.js"></script>
		<script src="dist/js/demo.js"></script>
		<script>
			function calculatetotal(a){
				var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var qty = document.getElementById("cqty["+d+"]").value;
				var price = document.getElementById("cpri["+d+"]").value;
				if(qty == "" || qty.length == "" || qty == 0 || qty == "0" || qty == "0.00" || qty == 0.00){ qty = 0; }
				if(price == "" || price.length == "" || price == 0 || price == "0" || price == "0.00" || price == 0.00){ price = 0; }
				var amount = 0;
				amount = parseFloat(qty) * parseFloat(price);
				document.getElementById("camt["+d+"]").value = amount.toFixed(2);
			}
			function rowgen(){
				var c = document.getElementById("incr").value;
				document.getElementById("addval["+c+"]").style.visibility = "hidden";
				document.getElementById("rmval["+c+"]").style.visibility = "hidden";
				c++;
				var html = '';
				html+= '<tr style="line-height:30px;">';
					html+= '<td><input type="text" name="pdate[]" id="pdate['+c+']" class="form-control cst_datepickers" value="<?php echo $fdate; ?>" onmouseover="cst_displaycalendor()" readonly></td>';
					html+= '<td><select name="sector[]" id="sector['+c+']" class="form-control select2"> <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><select name="code[]" id="code['+c+']" class="form-control select2" onchange="currentstockcheck();fetchavailablestock();fetchavailableprice();"> <?php foreach($icode as $fcode){ ?> <option value="<?php echo $icode[$fcode]; ?>"><?php echo $idesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<?php if($jals_flag == 1) { ?><td><input name="jalqty[]" id="jalqty['+c+']" class="form-control" value="0" /></td> <?php } ?>';
					html+= '<?php if($birds_flag == 1) { ?><td><input name="birdqty[]" id="birdqty['+c+']" class="form-control" value="0" /></td> <?php } ?>';
					html+= '<td><input name="cqty[]" id="cqty['+c+']" class="form-control" style="width:110px;" value="0" onkeyup="calculatetotal(this.id)" /></td>';
					html+= '<td><input name="cpri[]" id="cpri['+c+']" class="form-control" style="width:110px;" value="0" onkeyup="calculatetotal(this.id)" /></td>';
					html+= '<td><input name="camt[]" id="camt['+c+']" class="form-control" style="width:110px;" readonly /></td>';
					html+= '<td><textarea name="remark[]" id="remark['+c+']" class="form-control" style="height: 23px;"></textarea></td>';
					html+= '<td style="width: 100%;text-align:center;"><a href="JavaScript:Void(0);" name="addval[]" id="addval['+c+']" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+c+']" class="delete" onclick="rowdes()" style="visibility:visible;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
					html+= '<td style="visibility:hidden;"><input name="oqty[]" id="oqty['+c+']" class="form-control" value="0" readonly /></td>';
				html+= '</tr>';
				$('#tab3 tbody').append(html); var row = $('#row_cnt').val(); $('#row_cnt').val(parseInt(row) + parseInt(1)); var newtrlen = c; if(newtrlen > 0){ $('#submit').show(); } else{ $('#submit').hide(); } document.getElementById("incr").value = c;
				$('.select2').select2(); granttotalamount();
			}
			$(document).on('click','tr',function(){	var index = $('tr').index(this); var newIndex = parseInt(index) - parseInt(1); document.getElementById("incrs").value = newIndex; });
			$(document).on('click','.delete',function(){ var index = $('.delete').index(this); var newIndex = parseInt(index) + parseInt(1); $('#tab3 > tbody > tr:eq('+newIndex+')').remove(); var row = $('#row_cnt').val(); var trlen = $('#tab3 > tbody > tr').length; var minusIndex = parseInt(trlen) - parseInt(1); if(trlen > 1){ $('.add:eq('+minusIndex+')').removeClass('disabledbutton'); $('#row_cnt').val(trlen); }else{ $('.add:eq(0)').removeClass('disabledbutton'); $('#row_cnt').val(1); } var a = document.getElementById("incr").value; a--; document.getElementById("incr").value = a; if(a > 0){ document.getElementById("rmval["+a+"]").style.visibility = "visible"; } else { document.getElementById("rmval["+a+"]").style.visibility = "hidden"; } document.getElementById("addval["+a+"]").style.visibility = "visible"; granttotalamount(); });
			function redirection_page(){ window.location.href = "main_displayclosingstock.php"; }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function checkval(){
				document.getElementById("submittrans").style.visibility = "hidden";
				var a = document.getElementById("incr").value;
				for (var j=0;j<=a;j++){
					var b = document.getElementById("sector["+j+"]").value;
					var c = document.getElementById("code["+j+"]").value;
					var d = document.getElementById("cqty["+j+"]").value;
					var k = false; var l = j; l++;
					if(b.match("select") || b == "" || b.lenght == 0){
						alert("Please select Warehouse in row : "+l);
						k = false;
					}
					else if(c.match("select") || c == "" || c.lenght == 0){
						alert("Please select Code in row : "+l);
						k = false;
					}
					else if(d == "" || d.lenght == 0){
						alert("Please Enter quantity in row : "+l);
						k = false;
					}
					else {
						k = true;
					}
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
			function currentstockcheck(){
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("code["+a+"]").value;
				//document.getElementById("cqty["+a+"]").value = b;
			}
			function fetchavailablestock(){
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("sector["+a+"]").value;
				var c = document.getElementById("code["+a+"]").value;
				var d = document.getElementById("pdate["+a+"]").value;
				if(!b.match("select")){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "main_getavailablestock.php?sector="+b+"&iname="+c+"&pdate="+d;
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var f = this.responseText;
							var g = f.split("@");
							var h = g[0];
							if(f == ""){
								document.getElementById("oqty["+a+"]").value = 0;
							}
							else if(h.match("error")){
								alert("Already closing entry has been added on the date: "+g[1]);
							}
							else {
								document.getElementById("oqty["+a+"]").value = f;
							}
							//alert(f);
						}
					}
				}
				else {
					alert("Please select warehouse first in row: "+a);
				}
			}
			function fetchavailableprice(){
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("sector["+a+"]").value;
				var c = document.getElementById("code["+a+"]").value;
				var d = document.getElementById("pdate["+a+"]").value;
				if(!b.match("select")){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "main_getavailableprice.php?sector="+b+"&iname="+c+"&pdate="+d;
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var f = this.responseText;
							document.getElementById("cpri["+a+"]").value = f;
						}
					}
				}
				else {
					alert("Please select warehouse first in row: "+a);
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
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>