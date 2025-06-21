<?php
	//chicken_edit_saleorder.php
	session_start(); include "newConfig.php";
	include "header_head.php";
	$cid = $_SESSION['disppursale'];
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
			input[type=text] {
				padding: 0;
			}
            .amount-format{
                text-align: right;
             }
			.bg-danger{
				background-color: #dc3545 !important;
			}
			.text-white{
				color: white !important;
			}
			.bg-success {
				background-color: #28a745 !important;
			}
			/* input:focus {
				box-shadow: 0 0 25px rgba(104, 179, 219, 0.5) !important;
				border-color: rgb(104, 179, 219) !important;
			}  */


		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<section class="content-header">
			<h1>Edit Sales-Order</h1>
			<h5>Please fill mandatory (<b style="color:red;"> * </b>) fields</h5>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Purchase-Sales</a></li>
				<li class="active">Display</li>
				<li class="active">Edit</li>
			</ol>
		</section>
		<section class="content">
			<div class="box box-default">
			<?php
				$fdate = date("Y-m-d");
				$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
				while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

				$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Warehouse%' AND `active` = '1' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $office_alist = array();
				while($row = mysqli_fetch_assoc($query)){ $office_alist[$row['code']] = $row['code']; }

				$sql = "SELECT * FROM `main_groups` WHERE `active` = '1' ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $group_code = $group_name = array();
				while($row = mysqli_fetch_assoc($query)){ $group_code[$row['code']] = $row['code']; $group_name[$row['code']] = $row['description']; }

				// $sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
                // $query = mysqli_query($conn,$sql); $cont_code = $cont_name = array();
				// while($row = mysqli_fetch_assoc($query)){ $cont_code[$row['code']] = $row['code']; $cont_name[$row['code']] = $row['description']; $cont_gname[$row['code']] = $row['groupcode']; }

                $office_list = implode("','",$office_alist);
				$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `type` IN ('$office_list') ORDER BY `description` ASC";
                $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
				while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

				 $sql = "SELECT * FROM `chicken_supplier_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
				$query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query); $sp_code = $sp_name = array();
				while($row = mysqli_fetch_assoc($query)){$sp_code[$row['code']] = $row['code']; $sp_name[$row['code']] = $row['description'];}
				
				$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'"; $query = mysqli_query($conn,$sql);
				while($row = mysqli_fetch_assoc($query)){ $spzflag = $row['spzflag']; $ifwt = $row['wt']; $ifbw = $row['bw']; $ifjbw = $row['jbw']; $ifjbwen = $row['jbwen']; $ifctype = $row['ctype']; $pst_prate_flag = $row['pst_prate_flag']; }
				if($spzflag == "" || $spzflag == 0 || $spzflag == NULL){ $spzflag = 0; } else{ }

				$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
                $query = mysqli_query($conn,$sql); $cus_code = $cus_name = $sup_code = $sup_name = array();
				while($row = mysqli_fetch_assoc($query)){
					if($row['contacttype'] == "C" || $row['contacttype'] == "S&C"){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cont_gname[$row['code']] = $row['groupcode']; } else{ }
					if($row['contacttype'] == "S" || $row['contacttype'] == "S&C"){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; } else{ }
				}
			?>
				<div class="container mt-5">
                    <?php
                        $id = $_GET['id'];
                        $sql ="SELECT * FROM `salesorder` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
                        if($ccount > 0){
                            while($row = mysqli_fetch_assoc($query)){
                                $fdate = date("d.m.Y",strtotime($row['date']));
                                $trnum = $row['trnum'];
                                $cnames = $row['ccode'];
                                $itemcode = $row['itemcode'];
                                $qty = $row['twt'];
                           
                                $vehicleno = $row['vehicleno'];
                                $snames = $row['supplier'];
                                $place = $row['place'];
                                $supervisor = $row['supervisor'];
                                $warehouse = $row['warehouse'];
                                $narr = $row['remarks'];
                            }
                        }
                       
                    ?>
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-18">
								<form action="chicken_modify_saleorder.php" method="post" role="form" onsubmit="return checkval()">
									<div class="row">
									<div class="form-group col-md-1">
										<label>Date<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:100px;" class="form-control datepickers" name="pdate" value="<?php echo $fdate; ?>" id="pdate" readonly>
									</div>
									<div class="form-group col-md-2">
										<label>Warehouse<b style="color:red;">&nbsp;*</b></label>
										<select name="wcodes" id="wcodes" class="form-control select2" style="width: 100%;"><?php foreach($sector_code as $it){ ?><option value="<?php echo $sector_code[$it]; ?>" <?php if($warehouse == $it){ echo "selected"; } ?>><?php echo $sector_name[$it]; ?></option><?php } ?></select>
									</div>
									
									<div class="form-group col-md-2">
										<label>Item Description<b style="color:red;">&nbsp;*</b></label>
										<select name="scat" id="scat" class="form-control select2" style="width:150px;" onchange=""><?php foreach($item_code as $ic){ ?><option value="<?php echo $item_code[$ic] ?>" <?php if($itemcode == $ic){ echo "selected"; } ?>><?php echo $item_name[$ic]; ?></option><?php } ?></select>
									</div>
									
									<div class="form-group col-md-2" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
										<label>incr<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
									</div>
									<div class="form-group col-md-1" style="visibility:hidden;">
										<label>ECount<b style="color:red;">&nbsp;*</b></label>
										<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
									</div>
									</div>
									<div class="col-md-18">
										<table style="width:103%;line-height:30px;" id="tab3" class="table table-bordered table-striped">
											<tr style="line-height:30px;">
													<th style="text-align:center;"><label>Customer Name<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Quantity<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Supplier Name<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Place</label></th>
                                                    <th style="text-align:center;"><label>Supervisor No.</label></th>
                                                    <th style="text-align:center;"><label>Vehicle No.</label></th>
                                                    <th style="text-align:center;"><label>Remarks</label></th>
                                                    <th style="text-align:center;"></th>
												<!--<th><label>Outstanding<b style="color:red;">&nbsp;*</b></label</th>>-->
											</tr>
											<tbody id="bodytab">
											<tr id="tblrow" style="margin:5px 0px 5px 0px;">
                                               	
                                                <td style="width: 150px;padding-right:5px;"><select name="cnames" id="cnames" class="form-control select2" style="width: 150px;" onchange=""><option value="select">-select-</option><?php foreach($cus_code as $cc){ ?><option value="<?php echo $cus_code[$cc]; ?>" <?php if($cnames == $cc){ echo "selected"; } ?>><?php echo $cus_name[$cc]; ?></option><?php } ?></select></td>
												<td><input type="text" name="cus_qty" id="cus_qty" value="<?php echo $qty; ?>" onkeyup="validatenum(this.id);" onchange="validateamount(this.id)" class="form-control amount-format"></td>
												<td style="width: 150px;padding-right:5px;"><select name="snames" id="snames" class="form-control select2" style="width: 150px;" onchange=""><option value="select">-select-</option><?php foreach($sup_code as $cc){ ?><option value="<?php echo $sup_code[$cc]; ?>" <?php if($snames == $cc){ echo "selected"; } ?>><?php echo $sup_name[$cc]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="place" id="place" value="<?php echo $place; ?>" onkeyup="" class="form-control amount-format"></td>
												<td><input type="text" name="sv_no" id="sv_no" value="<?php echo $supervisor; ?>" class="form-control amount-format" ></td>
												<td><input type="text" name="v_no" id="v_no" value="<?php echo $vehicleno; ?>" class="form-control amount-format" ></td>
												<td style="width: auto;"><textarea name="narr" id="narr" class="form-control" ><?php echo $narr; ?></textarea></td>
												<td style="width: 60px;"></td>
											</tr>
											</tbody>
										</table><br/>
										
										<div class="col-md-12" align="left">
											<div class="col-md-4" style="width:auto;visibility:hidden;">
												<label>Id Value</label>
												<input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>" >
											</div>
										</div>
										<div class="box-body" align="center">
											<button type="submit" name="submittrans" id="submittrans" value="updatepage" class="btn btn-sm text-white bg-success">
												 Update
											</button>&ensp;&ensp;&ensp;&ensp;
											<button type="button" name="cancelled" id="cancelled" class="btn btn-sm text-white bg-danger" onclick="redirection_page()">
												 Cancel
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
                   
                    var l = true;
                    var date = document.getElementById("pdate").value;
                    var wcodes = document.getElementById("wcodes").value;
                    var scat = document.getElementById("scat").value;

					var cnames = document.getElementById("cnames").value;
					var cus_qty = document.getElementById("cus_qty").value; if(cus_qty == ""){ cus_qty = 0; }
					var snames = document.getElementById("snames").value;
					var place = document.getElementById("place").value;
					var sv_no = document.getElementById("sv_no").value;
					var v_no = document.getElementById("v_no").value;

                    if(cnames == "select"){
						alert("Please select Customer names in row: ");
						document.getElementById("cnames").focus();
						l = false;
						
					}
					else if(parseFloat(cus_qty) == 0){
						alert("Please enter Quantity in row: ");
						document.getElementById("cus_qty").focus();
						l = false;
						
					}
					else if(snames == "select"){
						alert("Please select Supplier names in row: ");
						document.getElementById("snames").focus();
						l = false;
						
					}
					return l;
                    
                }

			function redirection_page(){
				var a = '<?php echo $cid; ?>';
				window.location.href = "chicken_display_saleorder.php?cid="+a;
			}
			

			document.addEventListener("keydown", (e) => {
                if (e.key === "Enter"){
                    //alert(e.key+"==="+document.activeElement.id+"==="+key_search);
					var ebtncount = document.getElementById("ebtncount").value;
					if(ebtncount > 0){
						event.preventDefault();
					}
					else{
						$(":submit").click(function () {
							$('#submittrans').click();
						});
					}
                }
                else{ }
				
            });
			function validate_count(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			
 
			calculatetotal(); calfinaltotal();
		</script>
	</body>
</html>