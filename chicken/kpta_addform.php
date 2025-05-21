<?php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['kptaform'];
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
			table th{
				padding: 5px;
				text-align:right;
			}
			.th_headers{
				text-align:center;
				background-color: #D1CFB2;
			}
			table td{
				padding: 5px;
				text-align:left;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Create KPTA-FORM</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">KPTA</a></li>
				<li class="active">Display</li>
				<li class="active">Add</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18" align="center" >
								<form  action="kpta_updateform.php" method="post" name="form_name" id = "form_id" class="form_class" role="form" enctype="multipart/form-data"  onsubmit="return checkval()">
									<table class="table" style="width:95%;border: 0.1vh dotted #022222;">
										<thead>
											<tr>
												<th class="th_headers" colspan="6">KPTA-Form Details</th>
											</tr>
											<tr>
												<th style="width:90px;">Date</th>
												<td style="width:290px;"><input type="text" name="doj" id="datepickers1" class="form-control" placeholder="select DOJ" /></td>
										
										 		<th style="width:110px;">File No.:</th>
												<td style="width:290px;"><input type="text" name="file_no" id="file_no" class="form-control" placeholder="select FileNo." /></td>
												<th style="width:90px;">Zone:</th>
												<td>
													<select name="zone" id="zone" class="form-control select2"/>
														<option value="select">-select-</option>
														<option value="z1">Zone-1</option>
														<option value="z2">Zone-2</option>
														<option value="z3">Zone-3</option>
														<option value="z4">Zone-4</option>
														<option value="z5">Zone-5</option>
													</select>
												</td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th class="th_headers" colspan="6">Personal Details</th>
											</tr>
											<tr>
												<th>Name:</th>
												<td><input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" /></td>
												<th>Father Name:</th>
												<td><input type="text" name="father_name" id="father_name" class="form-control" placeholder="Enter Father Name" /></td>
												<th>Date Of Birth(DOB):</th>
												<td><input type="text" name="dob" id="datepickers" class="form-control" placeholder="select DOB" /></td>
											</tr>
											<tr>
												<th>Age:</th>
												<td><input type="text" name="age" id="age" class="form-control" placeholder="Enter Age" /></td>
												<th>Mobile No.:</th>
												<td><input type="text" name="mobile_no" id="mobile_no" class="form-control" placeholder="Enter Mob" /></td>
												<th>Address:</th>
												<td><textarea name="address" id="address" class="form-control" placeholder="Enter Address"></textarea></td>
											</tr>
											<tr>
												<th class="th_headers" colspan="6">Business Details</th>
											</tr>
											<tr>
												<th>Company Name:</th>
												<td><input type="text" name="business_farm_name" id="business_farm_name" class="form-control" placeholder="Company Name" /></td>
												<th>License No.:</th>
												<td><input type="text" name="co_license_no" id="co_license_no" class="form-control" placeholder="License No." /></td>
												<th>Business Address:</th>
												<td><textarea name="business_address" id="business_address" class="form-control" placeholder="Business Address"></textarea></td>
											</tr>
											<tr>
												<th>Daily Sales:</th>
												<td><input type="text" name="daily_sales" id="daily_sales" class="form-control" placeholder="Sales" /></td>
												<th>KPTA Member's Opinion:</th>
												<td colspan="3"><textarea name="kpta_opinion" id="kpta_opinion" class="form-control" placeholder="Members Opinion"></textarea></td>
											</tr>
											<tr>
												<th class="th_headers" colspan="6">Document Details</th>
											</tr>
											<tr>
												<!--<td>Photo:&ensp;<input type="file" name="photo_img" id="photo_img" class="form-control" placeholder="select Photo" /></td>
												<td>Document-1:&ensp;<input type="file" name="doc1" id="doc1" class="form-control" placeholder="select Document" /></td>
												<td>Document-2:&ensp;<input type="file" name="doc2" id="doc2" class="form-control" placeholder="select Document" /></td>
												<td>Document-3:&ensp;<input type="file" name="doc3" id="doc3" class="form-control" placeholder="select Document" /></td>
												<td>Document-4:&ensp;<input type="file" name="doc4" id="doc4" class="form-control" placeholder="select Document" /></td>
												<td>Document-5:&ensp;<input type="file" name="doc5" id="doc5" class="form-control" placeholder="select Document" /></td>-->
												
												<td colspan="6">
													<div class="row col-md-12">
														<div class="form-group col-md-2">Photo:&ensp;<input type="file" name="photo_img" id="photo_img" class="form-control" placeholder="select Photo" /></div>
														<div class="form-group col-md-2">Document-1:&ensp;<input type="file" name="doc1" id="doc1" class="form-control" placeholder="select Document" /></div>
														<div class="form-group col-md-2">Document-2:&ensp;<input type="file" name="doc2" id="doc2" class="form-control" placeholder="select Document" /></div>
														<div class="form-group col-md-2">Document-3:&ensp;<input type="file" name="doc3" id="doc3" class="form-control" placeholder="select Document" /></div>
														<div class="form-group col-md-2">Document-4:&ensp;<input type="file" name="doc4" id="doc4" class="form-control" placeholder="select Document" /></div>
														<div class="form-group col-md-2">Document-5:&ensp;<input type="file" name="doc5" id="doc5" class="form-control" placeholder="select Document" /></div>
													</div>
												</td>
											</tr>
											<tr>
												<th class="th_headers" colspan="6">Witness Details</th>
											</tr>
											<tr>
												<th>Name:</th>
												<td colspan="2"><input type="text" name="witness_name" id="witness_name" class="form-control" placeholder="Witness Name" /></td>
												<th>Mobile No.:</th>
												<td colspan="2"><input type="text" name="witness_no" id="witness_no" class="form-control" placeholder="Witness Mobile" /></td>
											</tr>
										</tbody>
									</table><br/>
										<div class="box-body" align="center">
											<button type="submit" name="submittrans" id="submittrans" value="addpage" class="btn btn-flat btn-social btn-linkedin" >
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
		<script>
			function redirection_page(){
				var a = '<?php echo $cid; ?>';
				window.location.href="kpta_displayform.php?cid="+a;
			}
						
			
			document.getElementById("form_id").onkeypress = function(e) {
    var key = e.charCode || e.keyCode || 0;     
    if (key == 13) {
      //alert("No Enter!");
      e.preventDefault();
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
		<?php include "header_foot.php"; ?>
	</body>
</html>