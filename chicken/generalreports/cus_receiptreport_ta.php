<?php
    //cus_receiptreport_ta.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){
		include "../config.php";
		include "number_format_ind.php";
		$dbname = $_SESSION['dbase'];
		$users_code = $_SESSION['userid'];

        $form_reload_page = "cus_receiptreport_ta.php";
	}
	else{
		include "APIconfig.php";
		include "number_format_ind.php";
		$dbname = $db;
		$users_code = $_GET['emp_code'];
        $form_reload_page = "cus_receiptreport_ta.php?db=".$db;
	}

    $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Receipt Report' OR `type` = 'All' ORDER BY `id` DESC";
    $query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
    while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; }

	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC";
	$query = mysqli_query($conn,$sql); $cus_code = $cus_name = $cus_group = array();
	while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_group[$row['code']] = $row['groupcode']; }

	$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $grp_code = $grp_name = array();
	while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }

	$sql = "SELECT * FROM `main_reportfields` WHERE `field` = 'Receipt Report' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $dflag = $row['denomination']; } if($dflag == ""){ $dflag = 0; }

	$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $mode_code = $mode_name = array();
	while($row = mysqli_fetch_assoc($query)){ $mode_code[$row['code']] = $row['code']; $mode_name[$row['code']] = $row['description']; }

	$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $coa_code = $coa_name = array();
	while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

	$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
	$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
	while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    // Logo Flag
    $sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'Reports' AND `field_function` LIKE 'Fetch Logo Dynamically' AND `user_access` LIKE 'all' AND `flag` = '1'";
    $query = mysqli_query($conn,$sql); $dlogo_flag = mysqli_num_rows($query); //$avou_flag = 1;
    if($dlogo_flag > 0) { while($row = mysqli_fetch_assoc($query)){ $logo1 = $row['field_value']; } }

    // COA Flag
	$sql = "SELECT * FROM `extra_access` WHERE `field_name` LIKE 'cus_receiptreport.php' AND `field_function` LIKE 'multiple' AND `user_access` LIKE 'all' AND `flag` = '1'";
	$query = mysqli_query($conn,$sql); $mult_flag = mysqli_num_rows($query); //$avou_flag = 1;
    
	$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code'";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$saccess = $row['supadmin_access'];
		$aaccess = $row['admin_access'];
		$naccess = $row['normal_access'];
	}
	$utype = "NA";
	if($saccess == 1){
		$utype = "S";
	}
	else if($aaccess == 1){
		$utype = "A";
	}
	else if($naccess == 1){
		$utype = "N";
	}
	if($utype == "S" || $utype == "A"){
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = "";
	}
	else{
		$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		$addedemp = "";
		//$sql = "SELECT * FROM `log_useraccess` WHERE `empcode` = '$users_code' AND `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
		//while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
		//$addedemp = " AND `addedemp` LIKE '$users_code'";
	}
	
	$fdate = $tdate = date("Y-m-d"); $sectors = array(); 
    $groups = array(); $groups['all'] = "all"; $sectors["all"] = $modes["all"] = "all";
    $exports = "displaypage"; $cus_all_flag = $grp_all_flag = $sec_all_flag = $mod_all_flag = $coa_all_flag = $user_all_flag = 0;
	if(isset($_POST['submit']) == true){
		$fdate = date("Y-m-d",strtotime($_POST['fdate']));
		$tdate = date("Y-m-d",strtotime($_POST['tdate']));
		// $customers = $_POST['customers'];
		// $modes = $_POST['modes'];
		// $coas = $_POST['coas'];
		// $users = $_POST['users'];
		$exports = $_POST['exports'];

        $sects = $groups = array(); $grp_all_flag = 0;
        foreach($_POST['groups'] as $grps){ $groups[$grps] = $grps; if($grps == "all"){ $grp_all_flag = 1; } }
        $grp_list = implode("@",$groups);

        //Sector Filter
        $sectors = array(); $sec_list = "";
        foreach($_POST['sectors'] as $scts){ $sectors[$scts] = $scts; if($scts == "all"){ $sec_all_flag = 1; } }
        $sects_list = implode("','", array_map('addslashes', $sectors));
        $secct_fltr = "";
        if($sec_all_flag == 1 ){ $secct_fltr = ""; $sec_list = "all"; }
        else { $secct_fltr = "AND `warehouse` IN ('$sects_list')"; $sec_list = implode(",",$sectors); }

        if($mult_flag > 0){ 
            $customers["all"] = $coas["all"] = $modes["all"] = $users["all"] = "all";
            //customers Filter
            // $customers = array(); $cust_list = "";
            // foreach($_POST['customers'] as $cst){ $customers[$cst] = $cst; if($cst == "all"){ $cus_all_flag = 1; } }
            // $custs_list = implode("','", array_map('addslashes', $customers));
            // $cus_filter = "";
            // if($cus_all_flag == 1 ){ $cus_filter = ""; $cust_list = "all"; }
            // else { $cus_filter = "AND `ccode` IN ('$custs_list')"; $cust_list = implode(",",$customers); }
            $customers = array(); $cust_list = "";
            foreach($_POST['customers'] as $cst){
                // Only include customer if it's in the selected group(s) or if "all" is selected
                if(in_array("all", $_POST['groups']) || in_array($cus_group[$cst], $_POST['groups']) || $cst == "all") {
                    $customers[$cst] = $cst;
                    if($cst == "all"){ $cus_all_flag = 1; }
                }
            }
            $custs_list = implode("','", array_map('addslashes', $customers));
            $cus_filter = "";

            if($cus_all_flag == 1){
                $cus_filter = "";
                $cust_list = "all";
            } else {
                $cus_filter = "AND `ccode` IN ('$custs_list')";
                $cust_list = implode(",", $customers);
            }

            //Mode Filter
            $modes = array(); $mod_list = "";
            foreach($_POST['modes'] as $md){ $modes[$md] = $md; if($md == "all"){ $mod_all_flag = 1; } }
            $mode_list = implode("','", array_map('addslashes', $modes));
            $mode_filter = "";
            if($mod_all_flag == 1 ){ $mode_filter = ""; $mod_list = "all"; }
            else { $mode_filter = "AND `mode` IN ('$mode_list')"; $mod_list = implode(",",$modes); }
            //coas Filter
            $coas = array(); $coa_list = "";
            foreach($_POST['coas'] as $co){ $coas[$co] = $co; if($co == "all"){ $coa_all_flag = 1; } }
            $coas_list = implode("','", array_map('addslashes', $coas));
            $coa_filter = "";
            if($coa_all_flag == 1 ){ $coa_filter = ""; $coa_list = "all"; }
            else { $coa_filter = "AND `method` IN ('$coas_list')"; $coa_list = implode(",",$coas); }
            //users Filter
            $users = array(); $user_list = "";
            foreach($_POST['users'] as $us){ $users[$us] = $us; if($us == "all"){ $user_all_flag = 1; } }
            $users_list = implode("','", array_map('addslashes', $users));
            $user_filter = "";
            if($user_all_flag == 1 ){ $user_filter = ""; $user_list = "all"; }
            else { $user_filter = "AND `addedemp` IN ('$users_list')"; $user_list = implode(",",$users); }

        } else {
            $customers = $modes = $coas = $users = "all";

            $customers = $_POST['customers'];
            $modes = $_POST['modes'];
            $coas = $_POST['coas'];
            $users = $_POST['users'];

            $cus_filter = "";
            if($customers != "all"){ $cus_filter = " AND `ccode` IN ('$customers')"; }
            else if($grp_all_flag == 0){
                foreach($cus_code as $ccode){
                    $gcode = $cus_group[$ccode];
                    if(empty($groups[$gcode]) || $groups[$gcode] == ""){ }
                    else{ if($cus_list == ""){ $cus_list = $ccode; } else{ $cus_list = $cus_list."','".$ccode; } }
                }
                $cus_filter = " AND `ccode` IN ('$cus_list')";
            } else{ }

            if($modes == "all"){ $mode_filter = ""; } else{ $mode_filter = " AND `mode` IN ('$modes')"; }
            if($coas == "all"){ $coa_filter = ""; } else{ $coa_filter = " AND `method` IN ('$coas')"; }
            if($users == "all"){ $user_filter = ""; } else{ $user_filter = " AND `addedemp` IN ('$users')"; }

        }
	}
	$url = "../PHPExcel/Examples/ReceiptReport-Excel.php?fdate=".$fdate."&tdate=".$tdate."&customers=".$customers."&modes=".$modes."&coas=".$coas."&sectors=".$sec_list."&users=".$users."&groups=".$grp_list;
	
?>
<html>
	<head>
          <title>Receipt Report</title>
		<script>
			var exptype = '<?php echo $exports; ?>';
			var url = '<?php echo $url; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
		</script>
        <?php include "header_head2.php"; ?>
	</head>
	<body>
	    <?php if($exports == "displaypage" || $exports == "printerfriendly") { ?>
			<table align="center">
				<tr>
                <?php
                    if($dlogo_flag > 0) { ?>
                        <td><img src="../<?php echo $logo1; ?>" height="150px"/></td>
                    <?php }
                    else{ 
                     ?>
					<td><img src="<?php echo "../".$logopath; ?>" height="150px"/></td>
					<td><?php echo $cdetails; } ?></td>
					<td align="center">
						<h3>Receipt Report</h3>
                        <?php if($mult_flag > 0){ ?>
                            <?php if(in_array("all", $customers) || empty($customers)) { ?><label><b style="color: green;">Customer:</b>&nbsp;<?php echo "All"; ?></label><br/><?php }
                            else { ?><label><b style="color: green;">Customer:</b>&nbsp;<?php $names = []; foreach ($customers as $cust_id) { $names[] = $cus_name[$cust_id]; } echo implode(", ", $names); ?></label><br/><?php } ?>
                        <?php } else { ?>
                            <?php if($customers == "all" || $customers == "select" || $customers == ""){ ?><label><b style="color: green;">Customer:</b>&nbsp;<?php echo "All"; ?></label><br/><?php }
                            else{ ?><label><b style="color: green;">Customer:</b>&nbsp;<?php echo $cus_name[$customers]; ?></label><br/><?php } ?>
                        <?php } ?>
						<label><b style="color: green;">From Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>&ensp;&ensp;&ensp;&ensp;
						<label><b style="color: green;">To Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
					</td>
				</tr>
			</table>
	    <?php } ?>
		<section class="content" align="center">
			<div class="col-md-12" align="center">
				<form action="<?php echo $form_reload_page; ?>" method="post" onsubmit="return checkval()">
				    <table class="main-table table-sm table-hover" id="main_table">
						<?php if($exports == "displaypage" || $exports == "exportpdf") { ?>
						<thead class="thead1">
							<tr>
								<td colspan="19" class="p-1">
                                    <div class="m-1 p-1 row">
                                        <div class="form-group" style="width:110px;">
                                            <label for="fdate">From Date</label>
                                            <input type="text" name="fdate" id="fdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label for="tdate">To Date</label>
                                            <input type="text" name="tdate" id="tdate" class="form-control datepickers" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="padding:0;padding-left:2px;width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:290px;">
                                            <label for="groups[0]">Group</label>
                                            <select name="groups[]" id="groups[0]" class="form-control select2" style="width:280px;" multiple onchange="filter_group_customers()">
                                                <option value="all" <?php foreach($groups as $grps){ if($grps == "all"){ echo "selected"; } } ?>>All</option>
											    <?php foreach($grp_code as $gcode){ ?><option value="<?php echo $gcode; ?>" <?php foreach($groups as $grps){ if($grps == $gcode){ echo "selected"; } } ?>><?php echo $grp_name[$gcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php if($mult_flag > 0){ ?>
                                        <div class="form-group" style="width:170px;">
                                            <label for="customers[0]">Customer</label>
                                            <select name="customers[]" id="customers[0]" class="form-control select2" style="width:160px;" multiple>
                                                <option value="all" <?php if (in_array("all", $customers)) echo "selected"; ?>>All</option>
											    <?php
                                                if($grp_all_flag == 1){ foreach($cus_code as $vcode){ ?><option value="<?php echo $vcode; ?>" <?php if(in_array($vcode,$customers)){ echo "selected"; } ?>><?php echo $cus_name[$vcode]; ?></option><?php } }
                                                else{ foreach($cus_code as $vcode){ if(!empty($groups[$cus_group[$vcode]])){ ?><option value="<?php echo $vcode; ?>" <?php if(in_array($vcode,$customers)){ echo "selected"; } ?>><?php echo $cus_name[$vcode]; ?></option><?php } } }
                                                ?>
                                            </select>
                                        </div>
                                        <?php } else { ?>
                                        <div class="form-group" style="width:170px;">
                                            <label for="customers">Customer</label>
                                            <select name="customers" id="customers" class="form-control select2" style="width:160px;">
                                                <option value="all" <?php if($customers == "all"){ echo "selected"; } ?>>All</option>
											    <?php
                                                if($grp_all_flag == 1){ foreach($cus_code as $vcode){ ?><option value="<?php echo $vcode; ?>" <?php if($customers == $vcode){ echo "selected"; } ?>><?php echo $cus_name[$vcode]; ?></option><?php } }
                                                else{ foreach($cus_code as $vcode){ if(!empty($groups[$cus_group[$vcode]])){ ?><option value="<?php echo $vcode; ?>" <?php if($customers == $vcode){ echo "selected"; } ?>><?php echo $cus_name[$vcode]; ?></option><?php } } }
                                                ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                        <?php if($mult_flag > 0){ ?>
                                        <div class="form-group" style="width:150px;">
                                            <label for="modes[0]">Payment Mode</label>
                                            <select name="modes[]" id="modes[0]" class="form-control select2" style="width:140px;" multiple>
                                                <option value="all" <?php foreach($modes as $mds){ if($mds == "all"){ echo "selected"; } } ?>>All</option>
											    <?php foreach($mode_code as $mcode){ ?><option value="<?php echo $mcode; ?>" <?php if($modes == $mcode){ echo "selected"; } ?>><?php echo $mode_name[$mcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } else { ?>
                                        <div class="form-group" style="width:150px;">
                                            <label for="modes">Payment Mode</label>
                                            <select name="modes" id="modes" class="form-control select2" style="width:140px;">
                                                <option value="all" <?php if($modes == "all"){ echo "selected"; } ?>>All</option>
											    <?php foreach($mode_code as $mcode){ ?><option value="<?php echo $mcode; ?>" <?php if($modes == $mcode){ echo "selected"; } ?>><?php echo $mode_name[$mcode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                        <?php if($mult_flag > 0){ ?>
                                        <div class="form-group" style="width:150px;">
                                            <label for="coas[0]">Cash/Bank</label>
                                            <select name="coas[]" id="coas[0]" class="form-control select2" style="width:140px;" multiple>
                                                <option value="all" <?php foreach($coas as $grps){ if($grps == "all"){ echo "selected"; } } ?>>All</option>
											    <?php foreach($coa_code as $acode){ ?><option value="<?php echo $acode; ?>" <?php if($coas == $acode){ echo "selected"; } ?> ><?php echo $coa_name[$acode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } else { ?>
                                        <div class="form-group" style="width:150px;">
                                            <label for="coas">Cash/Bank</label>
                                            <select name="coas" id="coas" class="form-control select2" style="width:140px;">
                                                <option value="all" <?php if($coas == "all"){ echo "selected"; } ?>>All</option>
											    <?php foreach($coa_code as $acode){ ?><option value="<?php echo $acode; ?>" <?php if($coas == $acode){ echo "selected"; } ?>><?php echo $coa_name[$acode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } ?>
                                       <div class="form-group" style="width:190px;">
                                            <label for="sectors[0]">Warehouse</label>
                                            <select name="sectors[]" id="sectors[0]" class="form-control select2" style="width:180px;" multiple>
                                                <option value="all" <?php if (in_array("all", $sectors)) echo "selected"; ?>>All</option>
                                                <?php foreach($sector_code as $scode) { ?>
                                                    <option value="<?php echo $scode; ?>" <?php if (in_array($scode, $sectors)) echo "selected"; ?>>
                                                        <?php echo $sector_name[$scode]; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="m-1 p-1 row">
                                        <?php if($mult_flag > 0){ ?>
                                        <div class="form-group" style="width:150px;">
                                            <label for="users[0]">User</label>
                                            <select name="users[]" id="users[0]" class="form-control select2" style="width:140px;">
                                                <option value="all" <?php if($users == "all"){ echo "selected"; } ?>>All</option>
											    <?php foreach($user_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($users == $ucode){ echo "selected"; } ?>><?php echo $user_name[$ucode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                        <?php } else { ?>
                                        <div class="form-group" style="width:150px;">
                                            <label for="users">User</label>
                                            <select name="users" id="users" class="form-control select2" style="width:140px;">
                                                <option value="all" <?php if($users == "all"){ echo "selected"; } ?>>All</option>
											    <?php foreach($user_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($users == $ucode){ echo "selected"; } ?>><?php echo $user_name[$ucode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                         <?php } ?>
                                        <div class="form-group" style="width:150px;">
                                            <label for="exports">Export To</label>
                                            <select name="exports" id="exports" class="form-control select2" style="width:140px;">
                                                <option <?php if($exports == "displaypage") { echo 'selected'; } ?> value="displaypage">Display</option>
                                                <option <?php if($exports == "exportexcel") { echo 'selected'; } ?> value="exportexcel">Excel</option>
                                                <option <?php if($exports == "printerfriendly") { echo 'selected'; } ?> value="printerfriendly">Printer friendly</option>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 210px;">
                                            <label for="search_table">Search</label>
                                            <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                        </div>
                                        <div class="form-group">
                                            <br/><button type="submit" class="btn btn-warning btn-sm" name="submit" id="submit">Open Report</button>
                                        </div>
                                    </div>
								</td>
							</tr>
						</thead>
						<?php } ?>
						<thead class="thead2">
                            <tr>
								<th id='order'>Sl No.</th>
								<th id='order_date'>Date</th>
								<th id='order'>Customer</th>
								<th id='order'>Transaction No.</th>
								<th id='order'>Doc No.</th>
								<th id='order'>Payment Mode</th>
								<th id='order'>Payment Method</th>
								<th id='order_num'>Amount</th>
								<?php
								if($dflag == 1){
								?>
								<th id='order_num'>Coins</th>
								<th id='order_num'>C-10</th>
								<th id='order_num'>C-20</th>
								<th id='order_num'>C-50</th>
								<th id='order_num'>C-100</th>
								<th id='order_num'>C-200</th>
								<th id='order_num'>C-500</th>
								<th id='order_num'>C-2000</th>
								<?php
								}
								?>
								<th id='order'>Remarks</th>
								<th id='order'>Warehouse</th>
								<th id='order'>User</th>
                            </tr>
						</thead>
						<?php
                        if(isset($_POST['submit']) == true){
                            // $cus_filter = "";
                            // if($customers != "all"){ $cus_filter = " AND `ccode` IN ('$customers')"; }
                            // else if($grp_all_flag == 0){
                            //     foreach($cus_code as $ccode){
                            //         $gcode = $cus_group[$ccode];
                            //         if(empty($groups[$gcode]) || $groups[$gcode] == ""){ }
                            //         else{ if($cus_list == ""){ $cus_list = $ccode; } else{ $cus_list = $cus_list."','".$ccode; } }
                            //     }
                            //     $cus_filter = " AND `ccode` IN ('$cus_list')";
                            // } else{ }

                            // if($modes == "all"){ $mode_filter = ""; } else{ $mode_filter = " AND `mode` IN ('$modes')"; }
                            // if($coas == "all"){ $coa_filter = ""; } else{ $coa_filter = " AND `method` IN ('$coas')"; }
                            // if($users == "all"){ $user_filter = ""; } else{ $user_filter = " AND `addedemp` IN ('$users')"; }
                            
                            $html = '';
                            $html .= '<tbody class="tbody1">';
                            
                            $sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$cus_filter."".$mode_filter."".$coa_filter."".$user_filter."".$secct_fltr." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $sl = 1; $tccoins = $tc10 = $tc20 = $tc50 = $tc100 = $tc200 = $tc500 = $tc2000 = $tot_amount = 0;
                            while($row = mysqli_fetch_assoc($query)){
                                $date = date("d.m.Y",strtotime($row['date']));
                                $vname = $cus_name[$row['ccode']];
                                $trnum = $row['trnum'];
                                $docno = $row['docno'];
                                $mname = $mode_name[$row['mode']];
                                $aname = $coa_name[$row['method']];
                                $amount = number_format_ind($row['amount']);
                                $remarks = $row['remarks'];
                                $wname = $sector_name[$row['warehouse']];
                                $uname = $user_name[$row['addedemp']];

                                $html .= '<tr>';
                                $html .= '<td>'.$sl++.'</td>';
                                $html .= '<td>'.$date.'</td>';
                                $html .= '<td>'.$vname.'</td>';
                                $html .= '<td>'.$trnum.'</td>';
                                $html .= '<td>'.$docno.'</td>';
                                $html .= '<td>'.$mname.'</td>';
                                $html .= '<td>'.$aname.'</td>';
                                $html .= '<td class="text-right">'.$amount.'</td>';

                                if($dflag == 1){
                                    $html .= '<td class="text-right">'.number_format_ind($row['ccoins']).'</td>';
                                    $html .= '<td class="text-right">'.number_format_ind($row['c10']).'</td>';
                                    $html .= '<td class="text-right">'.number_format_ind($row['c20']).'</td>';
                                    $html .= '<td class="text-right">'.number_format_ind($row['c50']).'</td>';
                                    $html .= '<td class="text-right">'.number_format_ind($row['c100']).'</td>';
                                    $html .= '<td class="text-right">'.number_format_ind($row['c200']).'</td>';
                                    $html .= '<td class="text-right">'.number_format_ind($row['c500']).'</td>';
                                    $html .= '<td class="text-right">'.number_format_ind($row['c2000']).'</td>';
                                    $tccoins += (float)$row['ccoins'];
                                    $tc10 += (float)$row['c10'];
                                    $tc20 += (float)$row['c20'];
                                    $tc50 += (float)$row['c50'];
                                    $tc100 += (float)$row['c100'];
                                    $tc200 += (float)$row['c200'];
                                    $tc500 += (float)$row['c500'];
                                    $tc2000 += (float)$row['c2000'];
                                }
                                $html .= '<td>'.$remarks.'</td>';
                                $html .= '<td>'.$wname.'</td>';
                                $html .= '<td>'.$uname.'</td>';
                                $html .= '</tr>';
                                $tot_amount += (float)$row['amount'];
							}
                            $html .= '</tbody>';
                            $html .= '<tfoot class="tfoot1">';
                            $html .= '<tr>';
                            $html .= '<th colspan="7">Grand Total</th>';
                            $html .= '<th class="text-right">'.number_format_ind($tot_amount).'</th>';
                            if($dflag == 1){
                                $html .= '<th class="text-right">'.number_format_ind($tccoins).'</th>';
                                $html .= '<th class="text-right">'.number_format_ind($tc10).'</th>';
                                $html .= '<th class="text-right">'.number_format_ind($tc20).'</th>';
                                $html .= '<th class="text-right">'.number_format_ind($tc50).'</th>';
                                $html .= '<th class="text-right">'.number_format_ind($tc100).'</th>';
                                $html .= '<th class="text-right">'.number_format_ind($tc200).'</th>';
                                $html .= '<th class="text-right">'.number_format_ind($tc500).'</th>';
                                $html .= '<th class="text-right">'.number_format_ind($tc2000).'</th>';
                            }
                            $html .= '<th colspan="3"></th>';
                            $html .= '</tr>';
                            $html .= '</tfoot>';

                            echo $html;
                        }
                            ?>
						</table>
					</form>
				</div>
		</section>
        <script>
            function checkval(){
                var groups = document.getElementById('groups[0]').value;
                var l = true;
                if(groups == ""){
                    alert("Please select Group");
                    document.getElementById('groups[0]').focus();
                    l = false;
                }
                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
        <script>
            function filter_group_customers(){
                var selected = []; var fcode = ""; var all_flag = 0;
                
                removeAllOptions(document.getElementById("customers"));
                myselect = document.getElementById("customers");
                theOption1=document.createElement("OPTION");
                theText1=document.createTextNode("-All-");
                theOption1.value = "all";
                theOption1.appendChild(theText1);
                myselect.appendChild(theOption1);

                for(var option of document.getElementById('groups[0]').options){
                    if(option.selected){
                        fcode = option.value;
                        if(fcode == "all"){
                            all_flag = 1;
                        }
                    }
                }
                if(parseInt(all_flag) == 1){
                    <?php
                    foreach($cus_code as $vcode){
                    ?> 
                    theOption1=document.createElement("OPTION");
                    theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
                    theOption1.value = "<?php echo $vcode; ?>";
                    theOption1.appendChild(theText1);
                    myselect.appendChild(theOption1);	
                    <?php
                    }
                    ?>
                }
                else{
                    <?php
                    foreach($cus_code as $vcode){
                        $gcode = $cus_group[$vcode];
                    ?>
                    for(var option of document.getElementById('groups[0]').options){
                        if(option.selected){
                            fcode = option.value;
                            <?php
                            echo "if(fcode == '$gcode'){";
                            ?>
                            theOption1=document.createElement("OPTION");
                            theText1=document.createTextNode("<?php echo $cus_name[$vcode]; ?>");
                            theOption1.value = "<?php echo $vcode; ?>";
                            theOption1.appendChild(theText1);
                            myselect.appendChild(theOption1);
                            <?php
                            echo "}";
                            ?>
                        }
                    }
                    <?php
                    }
                    ?>
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="sort_table_columns.js"></script>
        <script src="searchbox.js"></script>
		<?php if($exports == "displaypage" || $exports == "exportpdf") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer> <?php } ?>
		<?php include "header_foot2.php"; ?>
	</body>
	
</html>
