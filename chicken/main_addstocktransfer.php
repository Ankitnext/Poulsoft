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
				width: 12%;
				text-align: center;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Add Stock Transfer</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Transaction</a></li>
				<li class="active">Stock Transfer Display</li>
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
				
				$sql = "SELECT * FROM `main_transactionfields` WHERE `field` LIKE 'Stock Transfer' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['jals_flag']; $birds_flag = $row['birds_flag']; }
				if($jals_flag == "" || $jals_flag == NULL){ $jals_flag = 0; }
				if($birds_flag == "" || $birds_flag == NULL){ $birds_flag = 0; }
							
				$sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%MACHINE%' OR `description` LIKE '%SHOP INVESTMENT%' OR `description` LIKE '%SCALE%' OR `description` LIKE '%BOARD%' OR `description` LIKE '%CASH%' OR `description` LIKE '%OTHERS%') AND `active` = '1' ORDER BY `id`";
				$query = mysqli_query($conn,$sql); $cat_alist = array();
				while($row = mysqli_fetch_assoc($query)) { $cat_alist[$row['code']] = $row['code']; }
				$cat_list = implode("','",$cat_alist);

				$sql = "SELECT * FROM `item_details` WHERE `category` NOT IN ('$cat_list') AND `active` = '1' ORDER BY `description` ASC";
				$query = mysqli_query($conn,$sql); $icode = $idesc = array();
				while($row = mysqli_fetch_assoc($query)){ $icode[$row['code']] = $row['code']; $idesc[$row['code']] = $row['description']; }

				$idisplay = ''; $ndisplay = 'style="display:none;';
			?>
			
				<div class="box-body" style="min-height:400px;">
					<div class="row">
						<div class="col-md-18" align="center">
							<div class="col-md-18" align="center">
								<form action="main_updatestocktransfer.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<div class="form-group col-md-18" style="visibility:hidden;">
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
										<input type="text" style="width:auto;" class="form-control" name="incrs" id="incrs" value="0">
									</div>
									<table style="width:95%;line-height:30px;" id="tab3" align="center">
										<thead>
											<tr style="line-height:30px;">
												<th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>DC No.</label></th>
												<th><label>From Warehouse<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Item<b style="color:red;">&nbsp;*</b></label></th>
												<?php if($jals_flag == 1) { echo '<th><label>Jals<b style="color:red;">&nbsp;*</b></label></th>'; } ?>
												<?php if($birds_flag == 1) { echo '<th><label>Birds<b style="color:red;">&nbsp;*</b></label></th>'; } ?>
												<th><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Price<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>To Warehouse<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Remarks</label></th>
												<th>Action</th>
											</tr>
										</thead>
											<tr style="line-height:30px;">
												<td><input type="text"  name="pdate[]" id="pdate[0]"class="form-control str_datepickers" value="<?php echo $fdate; ?>" readonly></td>
												<td><input type="text" name="dcno[]" id="dcno[0]" class="form-control"></td>
												<td><select name="fsector[]" id="fsector[0]" class="form-control select2" > <option value="select">select</option> <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="code[]" id="code[0]" class="form-control select2"> <option value="select">select</option> <?php foreach($icode as $fcode){ ?> <option value="<?php echo $icode[$fcode]; ?>"><?php echo $idesc[$fcode]; ?></option> <?php } ?> </select></td>
												<?php if($jals_flag == 1) { echo '<td><input name="jalqty[]" id="jalqty[0]" class="form-control" value="0" /></td>'; } ?>
												<?php if($birds_flag == 1) { echo '<td><input name="birdqty[]" id="birdqty[0]" class="form-control" value="0" /></td>'; } ?>
												<td><input name="qty[]" id="qty[0]" class="form-control" value="0" /></td>
												<td><input type="text" name="price[]" id="price[0]" class="form-control" value="0" onkeyup="granttotalamount()"></td>
												<td><select name="tsector[]" id="tsector[0]" class="form-control select2" style="width:120px;"> <option value="select">select</option> <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>
												<td><textarea name="remark[]" id="remark[0]" class="form-control" style="width:120px;height: 23px;"></textarea></td>
												<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
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
			function rowgen(){
				var c = document.getElementById("incr").value;
				document.getElementById("addval["+c+"]").style.visibility = "hidden";
				document.getElementById("rmval["+c+"]").style.visibility = "hidden";
				var date = document.getElementById("pdate["+c+"]").value;
				c++;
				var html = '';
				html+= '<tr style="line-height:30px;">';
					html+= '<td><input type="text" name="pdate[]" id="pdate['+c+']" class="form-control str_datepickers" value="'+date+'" onmouseover="str_displaycalendor()" readonly></td>';
					html+= '<td><input type="text" name="dcno[]" id="dcno['+c+']" class="form-control"></td>';
					html+= '<td><select name="fsector[]" id="fsector['+c+']" class="form-control select2"> <option value="select">select</option> <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><select name="code[]" id="code['+c+']" class="form-control select2"> <option value="select">select</option> <?php foreach($icode as $fcode){ ?> <option value="<?php echo $icode[$fcode]; ?>"><?php echo $idesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<?php if($jals_flag == 1) { ?><td><input name="jalqty[]" id="jalqty['+c+']" class="form-control" value="0" /></td> <?php } ?>';
					html+= '<?php if($birds_flag == 1) { ?><td><input name="birdqty[]" id="birdqty['+c+']" class="form-control" value="0" /></td> <?php } ?>';
					html+= '<td><input name="qty[]" id="qty['+c+']" class="form-control" value="0" /></td>';
					html+= '<td><input type="text" name="price[]" id="price['+c+']" class="form-control" value="0" onkeyup="granttotalamount()"></td>';
					html+= '<td><select name="tsector[]" id="tsector['+c+']" class="form-control select2"> <option value="select">select</option> <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></td>';
					html+= '<td><textarea name="remark[]" id="remark['+c+']" class="form-control" style="height: 23px;"></textarea></td>';
					html+= '<td style="width: 100%;"><a href="JavaScript:Void(0);" name="addval[]" id="addval['+c+']" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+c+']" class="delete" onclick="rowdes()" style="visibility:visible;"><i class="fa fa-minus" style="color:red;"></i></a></td>';
				html+= '</tr>';
				$('#tab3 tbody').append(html); var row = $('#row_cnt').val(); $('#row_cnt').val(parseInt(row) + parseInt(1)); var newtrlen = c; if(newtrlen > 0){ $('#submit').show(); } else{ $('#submit').hide(); } document.getElementById("incr").value = c;
				$('.select2').select2(); granttotalamount();
			}
			$(document).on('click','tr',function(){	var index = $('tr').index(this); var newIndex = parseInt(index) - parseInt(1); document.getElementById("incrs").value = newIndex; });
			$(document).on('click','.delete',function(){ var index = $('.delete').index(this); var newIndex = parseInt(index) + parseInt(1); $('#tab3 > tbody > tr:eq('+newIndex+')').remove(); var row = $('#row_cnt').val(); var trlen = $('#tab3 > tbody > tr').length; var minusIndex = parseInt(trlen) - parseInt(1); if(trlen > 1){ $('.add:eq('+minusIndex+')').removeClass('disabledbutton'); $('#row_cnt').val(trlen); }else{ $('.add:eq(0)').removeClass('disabledbutton'); $('#row_cnt').val(1); } var a = document.getElementById("incr").value; a--; document.getElementById("incr").value = a; if(a > 0){ document.getElementById("rmval["+a+"]").style.visibility = "visible"; } else { document.getElementById("rmval["+a+"]").style.visibility = "hidden"; } document.getElementById("addval["+a+"]").style.visibility = "visible"; granttotalamount(); });
			function granttotalamount(){
				var s = document.getElementById("incr").value;
				var k = l = 0;
				for(var j=0;j<=s;j++){
					k = document.getElementById("amount["+j+"]").value;
					l = parseFloat(l) + parseFloat(k);
				}
				s++;
				document.getElementById("tno").value = s;
				document.getElementById("gtamt").value = l;
			}
			function redirection_page(){ window.location.href = "main_displaystocktransfer.php"; }
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function checkval(){
				document.getElementById("submittrans").style.visibility = "hidden";
				var a = document.getElementById("incr").value;
				for (var j=0;j<=a;j++){
					var b = document.getElementById("fsector["+j+"]").value;
					var c = document.getElementById("code["+j+"]").value;
					var d = document.getElementById("qty["+j+"]").value;
					var e = document.getElementById("price["+j+"]").value;
					var f = document.getElementById("tsector["+j+"]").value;
					var k = false; var l = j; l++;
					if(b.match("select")){
						alert("Please select From Warehouse in row : "+l);
						k = false;
					}
					else if(c.match("select")){
						alert("Please select Code in row : "+l);
						k = false;
					}
					else if(d == 0 || d == "" || d.lenght == 0){
						alert("Please Enter quantity in row : "+l);
						k = false;
					}
					else if(e == 0 || e == "" || e.lenght == 0){
						alert("Please enter price in row : "+l);
						k = false;
					}
					else if(f.match("select")){
						alert("Please select To Warehouse in row : "+l);
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
			function updatecode(){
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("mode["+a+"]").value;
				removeAllOptions(document.getElementById("code["+a+"]"));
				
				myselect = document.getElementById("code["+a+"]"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(b.match("MOD-001")){
					document.getElementById("cno["+a+"]").style.visibility = "hidden";
					document.getElementById("cdate["+a+"]").style.visibility = "hidden";
					<?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Cash' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				else if(b.match("MOD-002") || b.match("MOD-004") || b.match("MOD-005")){
					if(b.match("MOD-002")){
						document.getElementById("cno["+a+"]").style.visibility = "visible";
						document.getElementById("cdate["+a+"]").style.visibility = "visible";
					}
					else {
						document.getElementById("cno["+a+"]").style.visibility = "hidden";
						document.getElementById("cdate["+a+"]").style.visibility = "hidden";
					}
					
					<?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Bank' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				else {
					theOption1=document.createElement("OPTION"); theText1=document.createTextNode("Other"); theOption1.value = "Other"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				}
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
		 <script src="handle_ebtn_as_tbtn.js"></script>
		<footer align="center"> <?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>