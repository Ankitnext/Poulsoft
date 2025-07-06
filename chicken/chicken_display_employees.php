<?php
	//chicken_display_employees.php
	session_start(); include "newConfig.php";
	include "xendorheadlink.php";
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$user_name = $_SESSION['users']; $user_code = $_SESSION['userid'];
	if($_GET['ccid'] == "" && $_SESSION['employees'] != ""){ $cid = $_SESSION['employees']; } else if($_GET['ccid'] != "" && $_SESSION['employees'] == ""){ $cid = $_GET['ccid']; $_SESSION['employees'] = $cid; } else { $cid = $_GET['ccid']; $_SESSION['employees'] = $cid; }
	$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` LIKE '%$cid%' AND `activate` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
        if($row['name'] == "display" || $row['name'] == "Display"){ $gp_id = $row['childid']; }
		$gc_id[$row['childid']] = $row['childid'];
		$gp_name[$row['childid']] = $row['name'];
		$gp_link[$row['childid']] = $row['href'];
	}
	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$dlink = $row['displayaccess'];
		$alink = $row['addaccess'];
		$elink = $row['editaccess'];
		$ulink = $row['otheraccess'];
		$sa = $row['supadmin_access'];
		$aa = $row['admin_access'];
		$na = $row['normal_access'];
		$la = $row['loc_access'];
	}
	$dlink = explode(",",$dlink); foreach($dlink as $dlink1){ $dis_acc[$dlink1] = $dlink1; $dis_link_acc = $dis_link_acc.",".$dlink1; }
	$alink = explode(",",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; $add_link_acc = $add_link_acc.",".$alink1; }
	$elink = explode(",",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; $edt_link_acc = $edt_link_acc.",".$elink1; }
	$ulink = explode(",",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; $upd_link_acc = $upd_link_acc.",".$ulink1; }
	$la1 = explode(",",$la); foreach($la1 as $la2){ $loc_det[$la2] = $la2; }
	if($add_acc[$gp_id."-A"] != ""){$add_flag = 1; $add_link = $gp_link[$gp_id."-A"]; } else { $add_flag = 0; }
	if($edt_acc[$gp_id."-E"] != ""){ $edt_flag = 1; $edit_link = $gp_link[$gp_id."-E"]; } else { $edt_flag = 0; }
	if($upd_acc[$gp_id."-U"] != ""){ $upd_flag = 1; $upd_link = $gp_link[$gp_id."-U"]; } else { $upd_link = 0; }

    /*Check for Table Availability*/
    $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
    $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
    if(in_array("chicken_designation", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.chicken_designation LIKE poulso6_admin_chickenmaster.chicken_designation;"; mysqli_query($conn,$sql1); }
    if(in_array("chicken_employee", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.chicken_employee LIKE poulso6_admin_chickenmaster.chicken_employee;"; mysqli_query($conn,$sql1); }
    
?>
<html>
	<body class="hold-transition skin-blue sidebar-mini">
		<?php if($add_flag == 1){ ?>
		<div align="right" style="margin: 10px 10px 0 10px;">
			<button type="button" class="btn btn-warning" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
		</div>
		<?php } ?>
		<section class="content">
			<div class="row">
				<div class="col-lg-19">
					<div class="box">
						<div class="box-body">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Code</th>
										<th>Name</th>
										<th>Mobile</th>
										<th>E-mail</th>
										<th>Designation</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
								<?php
                                $sql = "SELECT * FROM `chicken_designation` WHERE `dflag`= '0' ORDER BY `description` ASC";
                                $query = mysqli_query($conn,$sql); $desig_code = $desig_name = array();
                                while($row = mysqli_fetch_assoc($query)){ $desig_code[$row['code']] = $row['code']; $desig_name[$row['code']] = $row['description']; }

                                $sql = "SELECT * FROM `chicken_employee` WHERE `dflag` = '0' ORDER BY `name` ASC";
                                $query = mysqli_query($conn,$sql);
                                while($row = mysqli_fetch_assoc($query)){
                                    $id = $row['code'];
                                    $id1 = $row['code']."@".$row['name'];
                                    $edit_url = $edit_link."?id=".$id;
                                    $authorize_url = $upd_link."?page=authorize&id=".$id;
                                    if($row['active'] == 1){ $update_url = $upd_link."?page=pause&id=".$id; }
                                    else{ $update_url = $upd_link."?page=activate&id=".$id; }

                                ?>
										<tr>
											<td><?php echo $row['code']; ?></td>
											<td><?php echo $row['name']; ?></td>
											<td><?php echo $row['mobile']; ?></td>
											<td><?php echo $row['email']; ?></td>
											<td><?php echo $desig_name[$row['desig_code']]; ?></td>
											<td style="width:15%;" align="left">
											<?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i>&ensp;";
                                            }
                                            else {
                                                if($edt_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pencil' style='color:brown;' title='edit'></i></a>&ensp;";
                                                }
                                                if($upd_flag == 1){
                                                    ?>
                                                    <a href='javascript:void(0)' id='<?php echo $id1; ?>' value='<?php echo $id1; ?>' onclick='checkdelete(this.id)'>
                                                    <i class='fa fa-trash' style='color:red;' title='delete'></i>
                                                    </a>&ensp;
                                                    <?php
                                                }
                                                if($upd_flag == 1){
                                                    if($row['active'] == 1){
                                                        echo "<a href='".$update_url."'><i class='fa fa-pause' style='color:blue;' title='Activate'></i></a>&ensp;";
                                                    }
                                                    else{
                                                        echo "<a href='".$update_url."'><i class='fa fa-play' style='color:blue;' title='Pause'></i></a>&ensp;";
                                                    }
                                                }
                                                if($upd_flag == 1){
                                                    echo "<a href='".$authorize_url."'><i class='fa fa-lock-open' style='color:orange;' title='Authorize'></i></a>&ensp;";
                                                }
                                            }
											?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php include "xendorfootlink.php"; ?>
		<script>
			function add_page(a){ var b = document.getElementById(a).value; window.location.href = b; }
			function checkdelete(a){
				var rval = a.split("@");
                var code = rval[0];
                var bname = rval[1];
                var sname = rval[2];
				var b = "<?php echo $upd_link.'?page=delete&id='; ?>"+code;
				var c = confirm("are you sure you want to delete the Entry : "+bname+" ?");
				if(c == true){
					window.location.href = b;
				}
				else{ }
				}
		</script>
		<footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer>
	</body>
</html>