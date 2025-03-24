<?php
	session_start();
	include "newConfig.php";
	include "header_head.php";
?>
<html>
	<head>
	<body class="hold-transition skin-blue sidebar-mini">
    <section class="content-header">
      <h1>Fill all required fields</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Profile</a></li>
        <li class="active">Create User Access</li>
        <li class="active">Add</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <!-- SELECT2 EXAMPLE -->
      <div class="box box-default">
        <?php
			$sql = "SELECT * FROM `main_linkdetails` WHERE `activate` = '1' ORDER BY `sortorder`,`gparentid` ASC";
			//$query = mysqli_query($conn,$sql); while($row = mysqli_fetch_assoc($query)){ }
		?>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
				<form action="main_updateuseraccess.php" method="post" role="form" onsubmit="return checkval()" name="form_name" id = "form_id" >
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>Username<b style="color:red;">&nbsp;*</b></label>
							<input type="text" name="uname" id="uname" class="form-control" placeholder="Enter description..." value="" onkeyup="validatename(this.id);" onchange="check_duplicate_user(this.id);">
						</div>
						<div class="form-group col-md-1" style="visibility:hidden;">
							<label>Dup Flag<b style="color:red;">&nbsp;*</b></label>
							<input type="text" name="dup_flag" id="dup_flag" class="form-control" value="0" readonly />
						</div>
						<div class="form-group col-md-1" style="visibility:hidden;">
							<label>ECount<b style="color:red;">&nbsp;*</b></label>
							<input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
						</div>
						<div class="form-group col-md-2"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>Password<b style="color:red;">&nbsp;*</b></label>
							<input type="password" name="upass" id="upass" class="form-control" value="" placeholder="Enter password...">
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>Mobile No.<b style="color:red;">&nbsp;*</b></label>
							<input type="text" name="umobile" id="umobile" class="form-control" placeholder="Enter password...">
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>Location Access<b style="color:red;">&nbsp;*</b></label>
							<select name="lname[]" id="lname[]" multiple class="form-control">
								<option value="all" selected >All</option>
								<?php
									$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
								?>
									<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
								<?php
									}
								?>
							</select>
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>Customer Group Access<b style="color:red;">&nbsp;*</b></label>
							<select name="cgroup[]" id="cgroup[]" multiple class="form-control">
								<option value="all" selected >All</option>
								<?php
									$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
								?>
									<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
								<?php
									}
								?>
							</select>
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>Sale Price Edit Access<b style="color:red;">&nbsp;*</b></label>
							<input type="radio" name="spe_flag" id="uaccess2" value="1" onclick="adminaccess(this.id)" checked />Yes&ensp;&ensp;&ensp;&ensp;&ensp;
							<input type="radio" name="spe_flag" id="uaccess3" value="0" onclick="adminaccess(this.id)" />No
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>Login Type<b style="color:red;">&nbsp;*</b></label>
							<input type="radio" name="logintype" id="logintype" value="normal" checked />&nbsp;Password&ensp;&ensp;&ensp;&ensp;&ensp;
							<input type="radio" name="logintype" id="logintype1" value="otp" />&nbsp;OTP
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>Access Type<b style="color:red;">&nbsp;*</b></label>
							<input type="radio" name="uaccess" id="uaccess" value="A" onclick="adminaccess(this.id)" />Admin Access&ensp;&ensp;&ensp;&ensp;&ensp;
							<input type="radio" name="uaccess" id="uaccess1" value="N" onclick="adminaccess(this.id)" checked />Sub-Admin Access
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-2">
							<label>Cash Access</label>
							<select name="cash_coa" id="cash_coa" class="form-control select2">
								<option value="all" selected >All</option>
								<?php
									$sql = "SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Cash' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
								?>
									<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
								<?php
									}
								?>
							</select>
						</div>
						<div class="form-group col-md-2">
							<label>bank Access</label>
							<select name="bank_coa" id="bank_coa" class="form-control select2">
								<option value="all" selected >All</option>
								<?php
									$sql = "SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Bank' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
									while($row = mysqli_fetch_assoc($query)){
								?>
									<option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
								<?php
									}
								?>
							</select>
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-4">
							<label>MIS Reports<b style="color:red;">&nbsp;*</b></label>
							<input type="radio" name="misr_flag" id="misr_flag1" value="1" />Yes&ensp;&ensp;&ensp;&ensp;&ensp;
							<input type="radio" name="misr_flag" id="misr_flag2" value="0" checked />No
						</div>
						<div class="form-group col-md-4"></div>
					</div>
					<!--<div class="col-md-12">
						<div class="form-group col-md-4"></div>
						<div class="form-group col-md-6">
							<label>Mobile Access<b style="color:red;">&nbsp;*</b></label>
							<input type="checkbox" name="m_sal" id="m_sal" value="sales" onclick="adminaccess(this.id)" />Sales&ensp;&ensp;&ensp;&ensp;&ensp;
							<input type="checkbox" name="m_rct" id="m_rct" value="receipts" onclick="adminaccess(this.id)" />Receipts&ensp;&ensp;&ensp;&ensp;&ensp;
							<input type="checkbox" name="m_rep" id="m_rep" value="reports" onclick="adminaccess(this.id)" />Reports&ensp;&ensp;&ensp;&ensp;&ensp;
							<input type="checkbox" name="m_slo" id="m_slo" value="salesorder" onclick="adminaccess(this.id)" />Sales Order
						</div>
						<div class="form-group col-md-2"></div>
					</div>-->
					<div class="col-md-12">
						<div class="form-group col-md-3"></div>
						<div class="form-group col-md-6">
							<table class="table table-bordered" style="line-height: 23px;">
								<thead>
									<tr>
										<th colspan="4" style="text-align:center;"><label>Android Mobile Accesses</label><br/>
										</th>
									</tr>
									<tr style="text-align:center;">
										<th colspan="4"><label>Transaction Access Details</label><br/>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
									<?php
									$sql = "SELECT * FROM `app_permissions` WHERE `type` LIKE 'Transaction' AND `adminflag` = '1' ORDER BY `screens_position` ASC"; $query = mysqli_query($conn,$sql); $app_count = mysqli_num_rows($query);
									if($app_count > 0){
										$c = 0;
										while($row = mysqli_fetch_assoc($query)){
											$c = $c + 1;
											if($c <= 4){
											?>
												<td><input type="checkbox" name="transaction_access[]" id="transaction_access[<?php echo $c; ?>]" value="<?php echo $row['screens']; ?>" />&nbsp;&nbsp;<?php echo $row['display_name']; ?></td>
											<?php
											}
											else{
											?>
												</tr><tr><td><input type="checkbox" name="transaction_access[]" id="transaction_access[<?php echo $c; ?>]" value="<?php echo $row['screens']; ?>" />&nbsp;&nbsp;<?php echo $row['display_name']; ?></td>
											<?php
												$c = 1;
											}
										}
									}
									?>
								</tbody>
								<thead>
									<tr style="text-align:center;">
										<th colspan="4"><label>Report Access Details</label><br/>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
									<?php
									$sql = "SELECT * FROM `app_permissions` WHERE `type` LIKE 'Report' AND `adminflag` = '1' ORDER BY `screens_position` ASC"; $query = mysqli_query($conn,$sql); $app_count = mysqli_num_rows($query);
									if($app_count > 0){
										$c = 0;
										while($row = mysqli_fetch_assoc($query)){
											$c = $c + 1;
											if($c <= 4){
											?>
												<td><input type="checkbox" name="report_access[]" id="report_access[<?php echo $c; ?>]" value="<?php echo $row['screens']; ?>" />&nbsp;&nbsp;<?php echo $row['display_name']; ?></td>
											<?php
											}
											else{
											?>
												</tr><tr><td><input type="checkbox" name="report_access[]" id="report_access[<?php echo $c; ?>]" value="<?php echo $row['screens']; ?>" />&nbsp;&nbsp;<?php echo $row['display_name']; ?></td>
											<?php
												$c = 1;
											}
										}
									}
									?>
								</tbody>
								<thead>
									<tr>
										<th colspan="4" style="text-align:center;"><label>IOS Mobile Accesses</label><br/>
										</th>
									</tr>
									<tr style="text-align:center;">
										<th colspan="4"><label>IOS Transaction Access Details</label><br/>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
									<?php
									$sql = "SELECT * FROM `app_permissions` WHERE `type` LIKE 'Transaction' AND `ios_AdminFlag` = '1' ORDER BY `screens_position` ASC"; $query = mysqli_query($conn,$sql); $app_count = mysqli_num_rows($query);
									if($app_count > 0){
										$c = 0;
										while($row = mysqli_fetch_assoc($query)){
											$c = $c + 1;
											if($c <= 4){
											?>
												<td><input type="checkbox" name="ios_transaction_access[]" id="ios_transaction_access[<?php echo $c; ?>]" value="<?php echo $row['screens']; ?>" />&nbsp;&nbsp;<?php echo $row['display_name']; ?></td>
											<?php
											}
											else{
											?>
												</tr><tr><td><input type="checkbox" name="ios_transaction_access[]" id="ios_transaction_access[<?php echo $c; ?>]" value="<?php echo $row['screens']; ?>" />&nbsp;&nbsp;<?php echo $row['display_name']; ?></td>
											<?php
												$c = 1;
											}
										}
									}
									?>
								</tbody>
								<thead>
									<tr style="text-align:center;">
										<th colspan="4"><label>IOS Report Access Details</label><br/>
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
									<?php
									$sql = "SELECT * FROM `app_permissions` WHERE `type` LIKE 'Report' AND `ios_AdminFlag` = '1' ORDER BY `screens_position` ASC"; $query = mysqli_query($conn,$sql); $app_count = mysqli_num_rows($query);
									if($app_count > 0){
										$c = 0;
										while($row = mysqli_fetch_assoc($query)){
											$c = $c + 1;
											if($c <= 4){
											?>
												<td><input type="checkbox" name="ios_report_access[]" id="ios_report_access[<?php echo $c; ?>]" value="<?php echo $row['screens']; ?>" />&nbsp;&nbsp;<?php echo $row['display_name']; ?></td>
											<?php
											}
											else{
											?>
												</tr><tr><td><input type="checkbox" name="ios_report_access[]" id="ios_report_access[<?php echo $c; ?>]" value="<?php echo $row['screens']; ?>" />&nbsp;&nbsp;<?php echo $row['display_name']; ?></td>
											<?php
												$c = 1;
											}
										}
									}
									?>
								</tbody>
							</table>
						</div>
						<div class="form-group col-md-2"></div>
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-3"></div>
						<div class="form-group col-md-6">
							<label>User Access<b style="color:red;">&nbsp;*</b></label>
							<table class="table table-bordered">
							<?php
								echo "<tr align='center'>";
									echo "<th rowspan='2' colspan='2'>Header</th>";
									echo "<th rowspan='2'>Links</th>";
									echo "<th colspan='4' align='center'>Accesses</th>";
								echo "</tr>";
								echo "<tr>";
									echo "<th align='center'>Display</th>";
									echo "<th align='center'>Add</th>";
									echo "<th align='center'>Edit</th>";
									echo "<th align='center'>Other</th>";
								echo "</tr>";
								echo "<tr>";
									echo "<th colspan='2'></th>";
									echo "<th>Default</th>";
									echo "<th><input type='checkbox' name='display' id='display' value='1' title='' onclick='checkall(this.id)'/></th>";
									echo "<th><input type='checkbox' name='add' id='add' value='2' title='' onclick='checkall(this.id)' /></th>";
									echo "<th><input type='checkbox' name='edit' id='edit' value='3' title='' onclick='checkall(this.id)' /></th>";
									echo "<th><input type='checkbox' name='update' id='update' value='4' title='' onclick='checkall(this.id)' /></th>";
								echo "</tr>";
								$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = 'P1' AND `activate` = '1' AND `href` LIKE 'javascript:void(0)' ORDER BY `parentid`,`sortorder` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$href = $row['href']; $module = $row['module'];
									//echo "<tr>";
									//	echo "<th>".$row['module']."</th>";
									//	echo "<th colspan='6'></th>";
									//echo "</tr>";
									//echo "<li class='treeview'>";
									//	echo "<a href='".$href."'>";
									//		echo "<i class='fa fa-share'></i> <span>".$module."</span>";
									//		echo "<span class='pull-right-container'>";
									//		echo "<i class='fa fa-angle-left pull-right'></i>";
									//		echo "</span>";
									//	echo "</a>";
									//	echo "<ul class='treeview'>";
										$c1 = $row['childid'];
										$sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c1' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
										while($row1 = mysqli_fetch_assoc($query1)){
											$href1 = $row1['href']; $module1 = $row1['name'];
											echo "<tr>";
									//			echo "<th></th>";
												echo "<th colspan='2'>".$row1['name']."</th>";
												echo "<th colspan='6'></th>";
											echo "</tr>";
									//		echo "<li><a href='".$href1."'><i class='fa fa-arrow-circle-right'></i>".$module1."</a></li>";
									//		echo "<ul class='treeview'>";
											
											$c2 = $row1['childid'];
											$sql2 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c2' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query2 = mysqli_query($conn,$sql2);
											while($row2 = mysqli_fetch_assoc($query2)){
												$m2 = $row2['name'];
												echo "<tr>";
												echo "<th colspan='2'></th>";
												echo "<th>".$row2['name']."</th>";
									//			echo "<li align='right'>".$module2;
												$c3 = $row2['childid'];
												$sql3 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c3' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query3 = mysqli_query($conn,$sql3);
												while($row3 = mysqli_fetch_assoc($query3)){
													$childid = $row3['childid']; $module2 = $row3['name'];
													$c4 = $row3['childid'];
												echo "<th><input type='checkbox' name='displays[]' id='displays[]' value='$childid' title='$module2' /></th>";
												$ccount = 0;
													$sql4 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c4' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query4 = mysqli_query($conn,$sql4);
													while($row4 = mysqli_fetch_assoc($query4)){
														$ccount = $ccount + 1;
														$childid4 = $row4['childid']; $module4 = $row4['name'];
														if($m2 == "Financial Year" && $ccount == 2){
															echo "<th></th>";
														}
														if($module4 =="Add"){
															echo "<th><input type='checkbox' name='adds[]' id='adds[]' value='$childid4' title='$module4' /></th>";
														}
														else if($module4 =="Edit"){
															echo "<th><input type='checkbox' name='edits[]' id='edits[]' value='$childid4' title='$module4' /></th>";
														}
														else if($module4 =="Update"){
															echo "<th><input type='checkbox' name='updates[]' id='updates[]' value='$childid4' title='$module4' /></th>";
														}
														else {
															echo "<th><input type='checkbox' name='cbox[]' id='cbox[]' value='$childid4' title='$module4' /></th>";
														}
														
													}
									//				echo "</li>";
												}
												echo "</tr>";
											}
									//		echo "</ul>";
										}
									//	echo "</ul>";
									//echo "</li>";	
								}
							?>
							<?php
								$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = 'P2' AND `activate` = '1' AND `href` LIKE 'javascript:void(0)' ORDER BY `parentid`,`sortorder` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$href = $row['href']; $module = $row['module'];
									echo "<tr>";
										echo "<th>".$row['module']."</th>";
										echo "<th colspan='6'></th>";
									echo "</tr>";
									//echo "<li class='treeview'>";
									//	echo "<a href='".$href."'>";
									//		echo "<i class='fa fa-share'></i> <span>".$module."</span>";
									//		echo "<span class='pull-right-container'>";
									//		echo "<i class='fa fa-angle-left pull-right'></i>";
									//		echo "</span>";
									//	echo "</a>";
									//	echo "<ul class='treeview'>";
										$c1 = $row['childid'];
										$sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c1' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
										while($row1 = mysqli_fetch_assoc($query1)){
											$childid = $row1['childid']; $module1 = $row1['name'];
											echo "<tr>";
												echo "<th colspan='2'></th>";
												echo "<th>".$row1['name']."</th>";
												echo "<th><input type='checkbox' name='displays[]' id='displays[]' value='$childid' title='$module1' /></th>";
										//	echo "<li align='right'><a href='".$href1."'><i class='fa fa-arrow-circle-right'></i>".$module1."</a>";
											$c2 = $row1['childid'];
											$sql2 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c2' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query2 = mysqli_query($conn,$sql2);
											while($row2 = mysqli_fetch_assoc($query2)){
												$childid = $row2['childid']; $module2 = $row2['name'];
												//echo "<th><input type='checkbox' name='cbox[]' id='cbox[]' value='$childid' title='$module2' /></th>";
												if($module2 =="Add"){
													echo "<th><input type='checkbox' name='adds[]' id='adds[]' value='$childid' title='$module2' /></th>";
												}
												else if($module2 =="Edit"){
													echo "<th><input type='checkbox' name='edits[]' id='edits[]' value='$childid' title='$module2' /></th>";
												}
												else if($module2 =="Update"){
													echo "<th><input type='checkbox' name='updates[]' id='updates[]' value='$childid' title='$module2' /></th>";
												}
												else {
													echo "<th><input type='checkbox' name='cbox[]' id='cbox[]' value='$childid' title='$module2' /></th>";
												}
											}
											echo "</tr>";
										}
									//	echo "</ul>";
								//	echo "</li>";	
								}
							?>
							<?php
								$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = 'P6' AND `activate` = '1' AND `href` LIKE 'javascript:void(0)' ORDER BY `parentid`,`sortorder` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$href = $row['href']; $module = $row['module'];
									echo "<tr>";
										echo "<th>".$row['module']."</th>";
										echo "<th colspan='6'></th>";
									echo "</tr>";
									//echo "<li class='treeview'>";
									//	echo "<a href='".$href."'>";
									//		echo "<i class='fa fa-share'></i> <span>".$module."</span>";
									//		echo "<span class='pull-right-container'>";
									//		echo "<i class='fa fa-angle-left pull-right'></i>";
									//		echo "</span>";
									//	echo "</a>";
									//	echo "<ul class='treeview'>";
										$c1 = $row['childid'];
										$sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c1' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
										while($row1 = mysqli_fetch_assoc($query1)){
											$childid = $row1['childid']; $module1 = $row1['name'];
											echo "<tr>";
												echo "<th colspan='2'></th>";
												echo "<th>".$row1['name']."</th>";
												echo "<th><input type='checkbox' name='displays[]' id='displays[]' value='$childid' title='$module1' /></th>";
										//	echo "<li align='right'><a href='".$href1."'><i class='fa fa-arrow-circle-right'></i>".$module1."</a>";
											$c2 = $row1['childid'];
											$sql2 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c2' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query2 = mysqli_query($conn,$sql2);
											while($row2 = mysqli_fetch_assoc($query2)){
												$childid = $row2['childid']; $module2 = $row2['name'];
												//echo "<th><input type='checkbox' name='cbox[]' id='cbox[]' value='$childid' title='$module2' /></th>";
												if($module2 =="Add"){
													echo "<th><input type='checkbox' name='adds[]' id='adds[]' value='$childid' title='$module2' /></th>";
												}
												else if($module2 =="Edit"){
													echo "<th><input type='checkbox' name='edits[]' id='edits[]' value='$childid' title='$module2' /></th>";
												}
												else if($module2 =="Update"){
													echo "<th><input type='checkbox' name='updates[]' id='updates[]' value='$childid' title='$module2' /></th>";
												}
												else {
													echo "<th><input type='checkbox' name='cbox[]' id='cbox[]' value='$childid' title='$module2' /></th>";
												}
											}
											echo "</tr>";
										}
									//	echo "</ul>";
								//	echo "</li>";	
								}
							?>
							<?php
								$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = 'P9' AND `activate` = '1' AND `href` LIKE 'javascript:void(0)' ORDER BY `parentid`,`sortorder` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$href = $row['href']; $module = $row['module'];
									echo "<tr>";
										echo "<th>".$row['module']."</th>";
										echo "<th colspan='6'></th>";
									echo "</tr>";
									//echo "<li class='treeview'>";
									//	echo "<a href='".$href."'>";
									//		echo "<i class='fa fa-share'></i> <span>".$module."</span>";
									//		echo "<span class='pull-right-container'>";
									//		echo "<i class='fa fa-angle-left pull-right'></i>";
									//		echo "</span>";
									//	echo "</a>";
									//	echo "<ul class='treeview'>";
										$c1 = $row['childid'];
										$sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c1' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
										while($row1 = mysqli_fetch_assoc($query1)){
											$childid = $row1['childid']; $module1 = $row1['name'];
											echo "<tr>";
												echo "<th colspan='2'></th>";
												echo "<th>".$row1['name']."</th>";
												echo "<th><input type='checkbox' name='displays[]' id='displays[]' value='$childid' title='$module1' /></th>";
										//	echo "<li align='right'><a href='".$href1."'><i class='fa fa-arrow-circle-right'></i>".$module1."</a>";
											$c2 = $row1['childid'];
											$sql2 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c2' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query2 = mysqli_query($conn,$sql2);
											while($row2 = mysqli_fetch_assoc($query2)){
												$childid = $row2['childid']; $module2 = $row2['name'];
												//echo "<th><input type='checkbox' name='cbox[]' id='cbox[]' value='$childid' title='$module2' /></th>";
												if($module2 =="Add"){
													echo "<th><input type='checkbox' name='adds[]' id='adds[]' value='$childid' title='$module2' /></th>";
												}
												else if($module2 =="Edit"){
													echo "<th><input type='checkbox' name='edits[]' id='edits[]' value='$childid' title='$module2' /></th>";
												}
												else if($module2 =="Update"){
													echo "<th><input type='checkbox' name='updates[]' id='updates[]' value='$childid' title='$module2' /></th>";
												}
												else {
													echo "<th><input type='checkbox' name='cbox[]' id='cbox[]' value='$childid' title='$module2' /></th>";
												}
											}
											echo "</tr>";
										}
									//	echo "</ul>";
								//	echo "</li>";	
								}
							?>
							<?php
								$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = 'P3' AND `activate` = '1' AND `href` LIKE 'javascript:void(0)' ORDER BY `parentid`,`sortorder` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$href = $row['href']; $module = $row['module'];
									echo "<tr>";
										echo "<th>".$row['module']."</th>";
										echo "<th colspan='6'></th>";
									echo "</tr>";
										$c1 = $row['childid'];
										$sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c1' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
										while($row1 = mysqli_fetch_assoc($query1)){
											$childid = $row1['childid']; $module1 = $row1['name'];
											echo "<tr>";
												echo "<th colspan='2'></th>";
												echo "<th>".$row1['name']."</th>";
												echo "<th><input type='checkbox' name='displays[]' id='displays[]' value='$childid' title='$module1' /></th>";
												echo "<th></th>";
												echo "<th></th>";
												echo "<th></th>";
											echo "</tr>";
										}
									//	echo "</ul>";
								//	echo "</li>";	
								}
							?>
							<?php
								$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = 'P4' AND `activate` = '1' AND `href` LIKE 'javascript:void(0)' ORDER BY `parentid`,`sortorder` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$href = $row['href']; $module = $row['module'];
									echo "<tr>";
										echo "<th>".$row['module']."</th>";
										echo "<th colspan='6'></th>";
									echo "</tr>";
										$c1 = $row['childid'];
										$sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c1' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
										while($row1 = mysqli_fetch_assoc($query1)){
											$childid = $row1['childid']; $module1 = $row1['name'];
											echo "<tr>";
												echo "<th colspan='2'></th>";
												echo "<th>".$row1['name']."</th>";
												echo "<th><input type='checkbox' name='displays[]' id='displays[]' value='$childid' title='$module1' /></th>";
												echo "<th></th>";
												echo "<th></th>";
												echo "<th></th>";
											echo "</tr>";
										}
									//	echo "</ul>";
								//	echo "</li>";	
								}
							?>
							<?php
								$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = 'P5' AND `activate` = '1' AND `href` LIKE 'javascript:void(0)' ORDER BY `parentid`,`sortorder` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$href = $row['href']; $module = $row['module'];
									echo "<tr>";
										echo "<th>".$row['module']."</th>";
										echo "<th colspan='6'></th>";
									echo "</tr>";
										$c1 = $row['childid'];
										$sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c1' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
										while($row1 = mysqli_fetch_assoc($query1)){
											$childid = $row1['childid']; $module1 = $row1['name'];
											echo "<tr>";
												echo "<th colspan='2'></th>";
												echo "<th>".$row1['name']."</th>";
												echo "<th><input type='checkbox' name='displays[]' id='displays[]' value='$childid' title='$module1' /></th>";
												echo "<th></th>";
												echo "<th></th>";
												echo "<th></th>";
											echo "</tr>";
										}
									//	echo "</ul>";
								//	echo "</li>";	
								}
							?>
							<?php
								$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = 'P7' AND `activate` = '1' AND `href` LIKE 'javascript:void(0)' ORDER BY `parentid`,`sortorder` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$href = $row['href']; $module = $row['module'];
									echo "<tr>";
										echo "<th>".$row['module']."</th>";
										echo "<th colspan='6'></th>";
									echo "</tr>";
										$c1 = $row['childid'];
										$sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c1' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
										while($row1 = mysqli_fetch_assoc($query1)){
											$childid = $row1['childid']; $module1 = $row1['name'];
											echo "<tr>";
												echo "<th colspan='2'></th>";
												echo "<th>".$row1['name']."</th>";
												echo "<th><input type='checkbox' name='displays[]' id='displays[]' value='$childid' title='$module1' /></th>";
												echo "<th></th>";
												echo "<th></th>";
												echo "<th></th>";
											echo "</tr>";
										}
									//	echo "</ul>";
								//	echo "</li>";	
								}
							?>
							<?php
								$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = 'P8' AND `activate` = '1' AND `href` LIKE 'javascript:void(0)' ORDER BY `parentid`,`sortorder` ASC"; $query = mysqli_query($conn,$sql);
								while($row = mysqli_fetch_assoc($query)){
									$href = $row['href']; $module = $row['module'];
									echo "<tr>";
										echo "<th>".$row['module']."</th>";
										echo "<th colspan='6'></th>";
									echo "</tr>";
										$c1 = $row['childid'];
										$sql1 = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$c1' AND `activate` ='1' ORDER BY `parentid`,`sortorder` ASC"; $query1 = mysqli_query($conn,$sql1);
										while($row1 = mysqli_fetch_assoc($query1)){
											$childid = $row1['childid']; $module1 = $row1['name'];
											echo "<tr>";
												echo "<th colspan='2'></th>";
												echo "<th>".$row1['name']."</th>";
												echo "<th><input type='checkbox' name='displays[]' id='displays[]' value='$childid' title='$module1' /></th>";
												echo "<th></th>";
												echo "<th></th>";
												echo "<th></th>";
											echo "</tr>";
										}
									//	echo "</ul>";
								//	echo "</li>";	
								}
							?>
							</table>
						</div>
						<div class="form-group col-md-4"></div>
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
              <!-- /.form-group -->
            </div>
		</div>
		</div>
		</div>
    </section>
<script>
function redirection_page(){
	window.location.href = "main_useraccess.php";
}
function validatename(x) {
	expr = /^[a-zA-Z0-9 (.&)_-]*$/;
	var a = document.getElementById(x).value;
	if(a.length > 50){
		a = a.substr(0,a.length - 1);
	}
	if(!a.match(expr)){
		a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, '');
	}
	document.getElementById(x).value = a;
}
function checkall(a){
	var c = document.getElementById(a).value;
	var selectallbox = document.getElementById(a);
	if(a.match("display")){
		var checkboxes = document.querySelectorAll('input[name="displays[]"]');
	}
	else if(a.match("add")){
		var checkboxes = document.querySelectorAll('input[name="adds[]"]');
	}
	else if(a.match("edit")){
		var checkboxes = document.querySelectorAll('input[name="edits[]"]');
	}
	else if(a.match("update")){
		var checkboxes = document.querySelectorAll('input[name="updates[]"]');
	}
	else {
		var checkboxes = document.querySelectorAll('input[name="cbox[]"]');
	}
	for (var i = 0; i < checkboxes.length; i++) {
		if(selectallbox.checked == true){
			checkboxes[i].checked = true;
			//alert(i);
		}
		else{
			checkboxes[i].checked = false;
		}
	}
}
function adminaccess(a){
	var c = document.getElementById(a).value;
	var checkboxes = document.querySelectorAll('input[type="checkbox"]');
	if(c.match("A")){
		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].checked = true;
		}
	}
	else if(c.match("N")){
		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].checked = false;
		}
	}
	else {
		
	}
}
function checkval(){
	document.getElementById("ebtncount").value = "1"; document.getElementById("submittrans").style.visibility = "hidden";
	var a = document.getElementById("uname").value;
	var b = document.getElementById("upass").value;
	var dup_flag = document.getElementById("dup_flag").value;
	var c = 0;
	var checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
	if(a.length == 0){
		alert("Enter Username ..!");
		document.getElementById("uname").focus();
		c = 0;
	}
	else if(b.length == 0){
		alert("Enter Password ..!");
		document.getElementById("upass").focus();
		c = 0;
	}
	else if(parseFloat(dup_flag) > 0){
		alert("Username Already exist with same name \n Kindly create new username ..!");
		document.getElementById("uname").focus();
		c = 0;
	}
	else if(checkboxes.length == 0){
		alert("Please select user access details ..!");
		c = 0;
	}
	else {
		c = checkboxes.length;
	}
	if(c > 0){
		var uname = document.getElementById("uname").value; var ttype = "add";
		var fetch_dupflag = new XMLHttpRequest();
		var method = "GET";
		var url = "broiler_fetch_userduplicate_flag.php?uname="+uname+"&ttype="+ttype;
		//window.open(url);
		var asynchronous = true;
		fetch_dupflag.open(method, url, asynchronous);
		fetch_dupflag.send();
		fetch_dupflag.onreadystatechange = function(){
			if(this.readyState == 4 && this.status == 200){
				var dup_flag = this.responseText;
				if(dup_flag == 1){
					alert("Username already exist \n Kindly create new Username ...!");
					document.getElementById("uname").focus();
					return false;
				}
				else{ return true; }
			}
		}
	}
	else {
		document.getElementById("ebtncount").value = "0";
		document.getElementById("submittrans").style.visibility = "visible";
		return false;
	}
}
function check_duplicate_user(a){
	var uname = document.getElementById(a).value; var ttype = "add";
    var fetch_dupflag = new XMLHttpRequest();
	var method = "GET";
	var url = "broiler_fetch_userduplicate_flag.php?uname="+uname+"&ttype="+ttype;
    //window.open(url);
	var asynchronous = true;
	fetch_dupflag.open(method, url, asynchronous);
	fetch_dupflag.send();
	fetch_dupflag.onreadystatechange = function(){
		if(this.readyState == 4 && this.status == 200){
			var dup_flag = this.responseText;
			if(dup_flag == 1){
				alert("Username already exist \n Kindly create new Username ...!");
				document.getElementById("uname").focus();
			}
			else{ }
            document.getElementById("dup_flag").value = dup_flag;
        }
    }
}
document.addEventListener("keydown", (e) => {
    /*var key_search = document.activeElement.id.includes("[");
    if(key_search == true){
        var b = document.activeElement.id.split("["); var c = b[1].split("]"); var d = c[0];
        //alert(e.key+"==="+document.activeElement.id+"==="+key_search+"==="+d);
        document.getElementById("incrs").value = d;
    }
    if (e.key === "Tab"){ } else{ }*/
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
/*document.getElementById("form_id").onkeypress = function(e) {
    var key = e.charCode || e.keyCode || 0;     
    if (key == 13) {
      	//alert("No Enter!");
      	e.preventDefault();
    }
} 
*/
</script>
</body>
</html>
	<?php include "header_foot.php"; ?>