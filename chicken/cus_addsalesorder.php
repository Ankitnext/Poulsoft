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
			.form-control { width: 85%; height: 23px; }
			label { line-height: 20px; }
			.disabledbutton{ pointer-events: none; opacity: 0.4; }
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Create Sales-Orders</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Sales-Orders</a></li>
				<li class="active">Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$today = date("Y-m-d");
				$fdate = date("d.m.Y",strtotime($today."+1 days"));
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $wh_code[$row['code']] = $row['code']; $wh_name[$row['code']] = $row['description']; }
				$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $jals_flag = $row['so_jals']; $birds_flag = $row['so_birds']; $wht_flag = $row['so_twt']; }
				$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
				$idisplay = ''; $ndisplay = 'style="display:none;';
			?>
			
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18">
								<form action="cus_updatesalesorder.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
									<div class="form-group col-md-1">
										<label>Date<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:100px;" class="form-control" name="pdate" value="<?php echo $fdate; ?>" id="slc_datepickers" readonly>
									</div>
									<div class="form-group col-md-2" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>incr<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>incrs<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="incrs" id="incrs" value="0">
									</div>
									<div class="col-md-12">
										<table style="width:auto;line-height:30px;" id="tab3">
											<tr style="line-height:30px;">
												<th><label>Customer<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Item Description<b style="color:red;">&nbsp;*</b></label></th>
												<th <?php if($jals_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><label>Jals<b style="color:red;">&nbsp;*</b></label></th>
												<th <?php if($birds_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><label>Birds<b style="color:red;">&nbsp;*</b></label></th>
												<th <?php if($wht_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><label>Weight<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Delivery Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Remarks</label></th>
												<th></th>
											</tr>
											<tr style="margin:5px 0px 5px 0px;">
												<td style="width: 180px;padding-right:30px;"><select name="cnames[]" id="cnames[0]" class="form-control select2" style="width: 100%;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
												<td style="width: 180px;padding-right:30px;"><select name="scat[]" id="scat[0]" class="form-control select2" style="width: 100%;"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select></td>
												<td <?php if($jals_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval[]" id="jval[0]" value="0" class="form-control" onchange="validatenum(this.id);" /></td>
												<td <?php if($birds_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval[]" id="bval[0]" value="0" class="form-control" onchange="validatenum(this.id);" /></td>
												<td <?php if($wht_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval[]" id="wval[0]" value="0" class="form-control" onchange="validateamount(this.id);" /></td>
												<td><input type="text" name="ddate[]" id="ddate[0]" value="<?php echo $fdate; ?>" class="form-control datepickers" /></td>
												<td><textarea name="narr[]" id="narr[0]" class="form-control" style="height:23px;"></textarea></td>
												<td style="width: 60px;"><a href="JavaScript:Void(0);" name="addval[]" id="addval[0]" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval[0]" onclick="rowdes()" style="visibility:hidden;"><i class="fa fa-minus" style="color:red;"></i></a></td>
											</tr>
										</table><br/>
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
				var a = document.getElementById("incr").value;
				document.getElementById("submittrans").style.visibility = "hidden";
				//var m = '<?php echo $jals_flag; ?>';
				//var n = '<?php echo $birds_flag; ?>';
				//var p = '<?php echo $wht_flag; ?>';
				var l = true; var k = 0;
				for(var j=0;j<=a;j++){
					var c = document.getElementById("cnames["+j+"]").value;
					var d = document.getElementById("scat["+j+"]").value;
					k = j + 1;
					if(l == true){
						if(c.match("select")){
							alert("Please select Customer in row: "+k);
							document.getElementById("cnames["+j+"]").focus();
							l = false;
						}
						else if(d.match("select")){
							alert("Please select Item Description in row: "+k);
							document.getElementById("scat["+j+"]").focus();
							l = false;
						}
						else{ }
					}
					else{ }
				}
				if(l == true){
					document.getElementById("submittrans").style.visibility = "visible";
					return true;
				}
				else{
					document.getElementById("submittrans").style.visibility = "visible";
					return false;
				}
			}
			function redirection_page(){
				window.location.href = "cus_salesorder.php";
			}
			function rowgen(){
				var trlen = $('#tab3 tbody tr').length;
				var c = document.getElementById("incr").value;
				document.getElementById("addval["+c+"]").style.visibility = "hidden";
				document.getElementById("rmval["+c+"]").style.visibility = "hidden";
				c++;
				var html = '';
				html+= '<tr style="margin:5px 0px 5px 0px;">';
				html+= '<td style="width: 180px;padding-right:30px;"><select name="cnames[]" id="cnames['+c+']" class="form-control select" style="width: 100%;"><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]."@".$cus_name[$cc]; ?>"><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>';
				html+= '<td style="width: 180px;padding-right:30px;"><select name="scat[]" id="scat['+c+']" class="form-control select" style="width: 100%;"><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic]."@".$item_name[$ic]; ?>"><?php echo $item_name[$ic]; ?></option><?php } ?></select></td>';
				html+= '<td <?php if($jals_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="jval[]" id="jval['+c+']" value="0" class="form-control" onchange="validatenum(this.id);" /></td>';
				html+= '<td <?php if($birds_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="bval[]" id="bval['+c+']" value="0" class="form-control" onchange="validatenum();" /></td>';
				html+= '<td <?php if($wht_flag == 1){ echo $idisplay; } else { echo $ndisplay; } ?>><input type="text" name="wval[]" id="wval['+c+']" value="0" class="form-control" onchange="validateamount(this.id);" /></td>';
				html+= '<td><input type="text" name="ddate[]" id="ddate['+c+']" value="<?php echo $fdate; ?>" class="form-control datepickers2" /></td>';
				html+= '<td><textarea name="narr[]" id="narr['+c+']" class="form-control" style="height:23px;"></textarea></td>';
				html+= '<td style="width: 60px;"><a href="JavaScript:Void(0); "name="addval[]" id="addval['+c+']" onclick="rowgen()"><i class="fa fa-plus"></i></a>&ensp;&ensp;<a href="JavaScript:Void(0);" name="rmval[]" id="rmval['+c+']" class="delete" onclick="rowdes()" title="'+c+'"><i class="fa fa-minus" style="color:red;"></i></a></td>';
				html += '</tr>';
				$('#tab3 tbody').append(html);
				var row = $('#row_cnt').val();
				$('#row_cnt').val(parseInt(row) + parseInt(1));
				var newtrlen = $('#tab3 tbody tr').length;
				if(newtrlen > 0){ $('#submit').show(); } else{ $('#submit').hide(); }
				document.getElementById("incr").value = c; $('.select').select2(); $('.datepickers2').datepicker({ dateFormat:'dd.mm.yy',changeMonth:true,changeYear:true});
			}
			$(document).on('click','tr',function(){	var index = $('tr').index(this); var newIndex = parseInt(index) - parseInt(1); document.getElementById("incrs").value = newIndex; });
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
			});
			function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
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