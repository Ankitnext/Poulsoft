<?php
	//chicken_datewise_useractivity.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
	$requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
	$db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){
		include "../config.php";
		include "header_head.php"; 
		include "number_format_ind.php"; 
	}
	else{
		//include "../newConfig.php";
		include "APIconfig.php";
		include "number_format_ind.php";
		include "header_head_new.php";
	}
    include "../broiler_check_tableavailability.php";
    
    $dbname = $_SESSION['dbase'];
    $user_code = $user_name = array();
    $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname' AND `dflag` = '0' ORDER BY `username` ASC"; $query = mysqli_query($conns,$sql);
    while($row = mysqli_fetch_assoc($query)){ $user_code[$row['empcode']] = $row['empcode']; $user_name[$row['empcode']] = $row['username']; }
    
    $tdate = date("Y-m-d"); $usr_code = $trans_type = "all"; $exoption = "displaypage";
    if(isset($_POST['submit_report']) == true){
        $tdate = date("Y-m-d",strtotime($_POST['tdate']));
        $usr_code = $_POST['usr_code'];
        $trans_type = $_POST['trans_type'];
    }
    
?>
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<script>
			var exptype = '<?php echo $exoption; ?>';
			var url = '<?php echo $url; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
		</script>
		<style>
			body{
				font-size: 15px;
				font-weight: bold;
				color: black;
			}
			.thead2,.tbody1 {
				font-size: 15px;
				font-weight: bold;
				padding: 1px;
				color: black;
                background-color: #98fb98;
			}
			.formcontrol {
				font-size: 15px;
				font-weight: bold;
				color: black;
				height: 23px;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				color: black;
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}
			.tbody1 td {
				font-size: 15px;
				font-weight: bold;
				color: black;
				padding-right: 5px;
				text-align: left;
			}
			.reportselectionlabel{
				font-size: 15px;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini" align="center">
	<?php if($exoption == "displaypage" || $exoption == "printerfriendly") { ?>
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ $company_name = $row['cname']; $qr_img_path = $row['qr_img_path']; ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;">User Activity Report</label>&ensp;&ensp;
						<?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">User:</b>&nbsp;<?php echo $user_name[$cname]; ?></label>&ensp;&ensp;
						<?php
							}
						?>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label class="reportheaderlabel"><b style="color: green;">Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($tdate)); ?></label>
					</td>
				</tr>
			</table>
		</header>
	<?php } ?>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
				<?php if($db == ''){?>
					<form action="chicken_datewise_useractivity.php?cid=<?php echo $cid; ?>" method="post" onsubmit="return checkval()">
					<?php } else { ?>
						<form action="chicken_datewise_useractivity.php?cid=<?php echo $cid; ?>&db=<?php echo $db; ?>" method="post" onsubmit="return checkval()">
					<?php } ?>
					
						<table class="table1" style="width:auto;line-height:23px;">
						<?php if($exoption == "displaypage" || $exoption == "exportpdf") { ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="25">
										<label class="reportselectionlabel">To Date</label>&nbsp;
										<input type="text" name="tdate" id="datepickers1" class="formcontrol" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>"/>
									    &ensp;&ensp;
										<label class="reportselectionlabel">User</label>&nbsp;
										<select name="usr_code" id="usr_code" class="form-control select2" style="width:290px;text-align:left;">
                                            <option value="all" <?php if($usr_code == "all"){ echo "selected"; } ?>>-All-</option>
                                            <?php foreach($user_code as $fcode){ if($user_name[$fcode] != ""){ ?>
                                            <option value="<?php echo $fcode; ?>" <?php if($usr_code == $fcode){ echo "selected"; } ?>><?php echo $user_name[$fcode]; ?></option>
                                            <?php } } ?>
                                        </select>&ensp;&ensp;
                                        <label class="reportselectionlabel">Transaction Type</label>&nbsp;
										<select name="trans_type" id="trans_type" class="form-control select2" style="width:290px;text-align:left;">
                                        <option value="all" <?php if($trans_type == "all"){ echo "selected"; } ?>>-All-</option>
                                            <option value="purchase" <?php if($trans_type == "purchase"){ echo "selected"; } ?>>Purchase</option>
                                            <option value="payment" <?php if($trans_type == "payment"){ echo "selected"; } ?>>Payments</option>
                                            <option value="sales" <?php if($trans_type == "sales"){ echo "selected"; } ?>>Sales</option>
                                            <option value="receipt" <?php if($trans_type == "receipt"){ echo "selected"; } ?>>Receipt</option>
                                            <option value="voucher" <?php if($trans_type == "voucher"){ echo "selected"; } ?>>Vouchers</option>
                                            <option value="dly_prt" <?php if($trans_type == "dly_prt"){ echo "selected"; } ?>>Daily Paper Rate</option>
                                            <option value="authorize" <?php if($trans_type == "authorize"){ echo "selected"; } ?>>Authorize</option>
                                            <option value="d_details" <?php if($trans_type == "d_details"){ echo "selected"; } ?>>Deletion Details</option>
                                        </select>&ensp;&ensp;
										<button type="submit" class="btn btn-warning btn-sm" name="submit_report" id="submit_report">Open Report</button>
									</td>
								</tr>
							</thead>
							<?php }
							if(isset($_POST['submit_report']) == true){
                                $usr_arr_list = array(); if($usr_code == "all"){ foreach($user_code as $ucode){ $usr_arr_list[$ucode] = $ucode; } } else{ $usr_arr_list[$usr_code] = $usr_code; }
                                //$ecode = implode("','",$usr_arr_list);
                                $html = ""; $time1 = date("Y-m-d",strtotime($tdate)); $time2 = date("Y-m-d H:i:s",strtotime($tdate." 23:59:59"));
                            ?>
                            <thead class="thead2">
                                <tr>
                                    <th>Sl.No.</th>
                                    <th>User</th>
                                    <th>Transaction Date</th>
                                    <th>Transaction Type</th>
                                    <th>Transaction No.</th>
                                    <th>Amount</th>
                                    <th>Activity Type</th>
                                    <th>Date &amp; Time</th>
                                </tr>
                            </thead>
                            <tbody class="tbody1" style="background-color: #f4f0ec;">
                            <?php
                            foreach($usr_arr_list as $ecode){
                                $i = 0;
                                if($count57 > 0){
                                    $sql = "SELECT * FROM `pur_purchase` WHERE `addedemp` IN ('$ecode') AND `addedtime` >= '$time1' AND `addedtime` <= '$time2' OR `updatedemp` IN ('$ecode') AND `updated` >= '$time1' AND `updated` <= '$time2'";
                                    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                                    if($count > 0){
                                        while($row = mysqli_fetch_assoc($query)){
                                            $aemp = $row['addedemp']; $eemp = $row['updatedemp']; $edate = date("d.m.Y",strtotime($row['date']));
                                            $adtime1 = date("Y-m-d",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['addedtime']));
                                            if($row['addedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$aemp]."</td><td>".$edate."</td><td>Purchase</td><td>".$row['invoice']."</td><td style='text-align:right;'>".number_format_ind($row['finaltotal'])."</td><td style='color:green;'>Entry Created</td><td>".$adtime2."</td></tr>";
                                            }
                                            $adtime1 = date("Y-m-d",strtotime($row['updated'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['updated']));
                                            if($row['updatedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                if($row['tdflag'] == 1 || $row['pdflag'] == '1'){
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Purchase</td><td>".$row['invoice']."</td><td style='text-align:right;'>".number_format_ind($row['finaltotal'])."</td><td style='color:red;'>Entry Deleted</td><td>".$adtime2."</td></tr>";
                                                }
                                                else{
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Purchase</td><td>".$row['invoice']."</td><td style='text-align:right;'>".number_format_ind($row['finaltotal'])."</td><td style='color:orange;'>Entry Modified</td><td>".$adtime2."</td></tr>";
                                                }
                                            }
                                        }
                                    }
                                }
                                if($count56 > 0){
                                    $sql = "SELECT * FROM `pur_payments` WHERE `addedemp` IN ('$ecode') AND `addedtime` >= '$time1' AND `addedtime` <= '$time2' OR `updatedemp` IN ('$ecode') AND `updatedtime` >= '$time1' AND `updatedtime` <= '$time2'";
                                    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                                    if($count > 0){
                                        while($row = mysqli_fetch_assoc($query)){
                                            $aemp = $row['addedemp']; $eemp = $row['updatedemp']; $edate = date("d.m.Y",strtotime($row['date']));
                                            $adtime1 = date("Y-m-d",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['addedtime']));
                                            if($row['addedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$aemp]."</td><td>".$edate."</td><td>Payment</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['amount'])."</td><td style='color:green;'>Entry Created</td><td>".$adtime2."</td></tr>";
                                            }
                                            $adtime1 = date("Y-m-d",strtotime($row['updatedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['updatedtime']));
                                            if($row['updatedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                if($row['tdflag'] == 1 || $row['pdflag'] == '1'){
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Payment</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['amount'])."</td><td style='color:red;'>Entry Deleted</td><td>".$adtime2."</td></tr>";
                                                }
                                                else{
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Payment</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['amount'])."</td><td style='color:orange;'>Entry Modified</td><td>".$adtime2."</td></tr>";
                                                }
                                            }
                                        }
                                    }
                                }
                                if($count14 > 0){
                                    $sql = "SELECT * FROM `customer_sales` WHERE `addedemp` IN ('$ecode') AND `addedtime` >= '$time1' AND `addedtime` <= '$time2' OR `updatedemp` IN ('$ecode') AND `updated` >= '$time1' AND `updated` <= '$time2'";
                                    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                                    if($count > 0){
                                        while($row = mysqli_fetch_assoc($query)){
                                            $aemp = $row['addedemp']; $eemp = $row['updatedemp']; $edate = date("d.m.Y",strtotime($row['date']));
                                            $adtime1 = date("Y-m-d",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['addedtime']));
                                            
                                            if($row['addedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$aemp]."</td><td>".$edate."</td><td>Sale</td><td>".$row['invoice']."</td><td style='text-align:right;'>".number_format_ind($row['finaltotal'])."</td><td style='color:green;'>Entry Created</td><td>".$adtime2."</td></tr>";
                                            }
                                            $adtime1 = date("Y-m-d",strtotime($row['updated'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['updated']));
                                            if($row['updatedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                if($row['tdflag'] == 1 || $row['pdflag'] == '1'){
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Sale</td><td>".$row['invoice']."</td><td style='text-align:right;'>".number_format_ind($row['finaltotal'])."</td><td style='color:red;'>Entry Deleted</td><td>".$adtime2."</td></tr>";
                                                }
                                                else{
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Sale</td><td>".$row['invoice']."</td><td style='text-align:right;'>".number_format_ind($row['finaltotal'])."</td><td style='color:orange;'>Entry Modified</td><td>".$adtime2."</td></tr>";
                                                }
                                            }
                                        }
                                    }
                                }
                                if($count13 > 0){
                                    $sql = "SELECT * FROM `customer_receipts` WHERE `addedemp` IN ('$ecode') AND `addedtime` >= '$time1' AND `addedtime` <= '$time2' OR `updatedemp` IN ('$ecode') AND `updatedtime` >= '$time1' AND `updatedtime` <= '$time2'";
                                    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                                    if($count > 0){
                                        while($row = mysqli_fetch_assoc($query)){
                                            $aemp = $row['addedemp']; $eemp = $row['updatedemp']; $edate = date("d.m.Y",strtotime($row['date']));
                                            $adtime1 = date("Y-m-d",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['addedtime']));
                                            if($row['addedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$aemp]."</td><td>".$edate."</td><td>Receipt</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['amount'])."</td><td style='color:green;'>Entry Created</td><td>".$adtime2."</td></tr>";
                                            }
                                            $adtime1 = date("Y-m-d",strtotime($row['updatedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['updatedtime']));
                                            if($row['updatedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                if($row['tdflag'] == 1 || $row['pdflag'] == '1'){
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Receipt</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['amount'])."</td><td style='color:red;'>Entry Deleted</td><td>".$adtime2."</td></tr>";
                                                }
                                                else{
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Receipt</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['amount'])."</td><td style='color:orange;'>Entry Modified</td><td>".$adtime2."</td></tr>";
                                                }
                                            }
                                        }
                                    }
                                }
                                if($count7 > 0){
                                    $sql = "SELECT * FROM `acc_vouchers` WHERE `addedemp` IN ('$ecode') AND `addedtime` >= '$time1' AND `addedtime` <= '$time2' OR `updatedemp` IN ('$ecode') AND `updatedtime` >= '$time1' AND `updatedtime` <= '$time2'";
                                    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                                    if($count > 0){
                                        while($row = mysqli_fetch_assoc($query)){
                                            $aemp = $row['addedemp']; $eemp = $row['updatedemp']; $edate = date("d.m.Y",strtotime($row['date']));
                                            $adtime1 = date("Y-m-d",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['addedtime']));
                                            if($row['addedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$aemp]."</td><td>".$edate."</td><td>Voucher</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['amount'])."</td><td style='color:green;'>Entry Created</td><td>".$adtime2."</td></tr>";
                                            }
                                            $adtime1 = date("Y-m-d",strtotime($row['updatedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['updatedtime']));
                                            if($row['updatedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                if($row['tdflag'] == 1 || $row['pdflag'] == '1'){
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Voucher</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['amount'])."</td><td style='color:red;'>Entry Deleted</td><td>".$adtime2."</td></tr>";
                                                }
                                                else{
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Voucher</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['amount'])."</td><td style='color:orange;'>Entry Modified</td><td>".$adtime2."</td></tr>";
                                                }
                                            }
                                        }
                                    }
                                }
                                if($count33 > 0){
                                    $sql = "SELECT * FROM `main_dailypaperrate` WHERE `addedemp` IN ('$ecode') AND `addedtime` >= '$time1' AND `addedtime` <= '$time2' OR `updatedemp` IN ('$ecode') AND `updatedtime` >= '$time1' AND `updatedtime` <= '$time2'";
                                    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                                    if($count > 0){
                                        while($row = mysqli_fetch_assoc($query)){
                                            $aemp = $row['addedemp']; $eemp = $row['updatedemp']; $edate = date("d.m.Y",strtotime($row['date']));
                                            $adtime1 = date("Y-m-d",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['addedtime']));
                                            if($row['addedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$aemp]."</td><td>".$edate."</td><td>Paper Rate</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['new_price'])."</td><td style='color:green;'>Entry Created</td><td>".$adtime2."</td></tr>";
                                            }
                                            $adtime1 = date("Y-m-d",strtotime($row['updatedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['updatedtime']));
                                            if($row['updatedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                if($row['tdflag'] == 1 || $row['pdflag'] == '1'){
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Paper Rate</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['new_price'])."</td><td style='color:red;'>Entry Deleted</td><td>".$adtime2."</td></tr>";
                                                }
                                                else{
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Paper Rate</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['new_price'])."</td><td style='color:orange;'>Entry Modified</td><td>".$adtime2."</td></tr>";
                                                }
                                            }
                                        }
                                    }
                                }
                                if($count10 > 0){
                                    $sql = "SELECT * FROM `authorize` WHERE `addedemp` IN ('$ecode') AND `addedtime` >= '$time1' AND `addedtime` <= '$time2' OR `updatedemp` IN ('$ecode') AND `updatedtime` >= '$time1' AND `updatedtime` <= '$time2'";
                                    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                                    if($count > 0){
                                        while($row = mysqli_fetch_assoc($query)){
                                            $aemp = $row['addedemp']; $eemp = $row['updatedemp']; $edate = date("d.m.Y",strtotime($row['date']));
                                            $adtime1 = date("Y-m-d",strtotime($row['addedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['addedtime']));
                                            if($row['addedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$aemp]."</td><td>".$edate."</td><td>Authorization</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['finalamt'])."</td><td style='color:green;'>Entry Authorized</td><td>".$adtime2."</td></tr>";
                                            }
                                            $adtime1 = date("Y-m-d",strtotime($row['updatedtime'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['updatedtime']));
                                            if($row['updatedemp'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                if($row['tdflag'] == 1 || $row['pdflag'] == '1'){
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Authorization</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['finalamt'])."</td><td style='color:red;'>Entry Deleted</td><td>".$adtime2."</td></tr>";
                                                }
                                                else{
                                                    $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$eemp]."</td><td>".$edate."</td><td>Authorization</td><td>".$row['trnum']."</td><td style='text-align:right;'>".number_format_ind($row['finalamt'])."</td><td style='color:orange;'>Entry Modified</td><td>".$adtime2."</td></tr>";
                                                }
                                            }
                                        }
                                    }
                                }
                                if($count35 > 0){
                                    $sql = "SELECT * FROM `main_deletiondetails` WHERE `empcode` IN ('$ecode') AND `updated` >= '$time1' AND `updated` <= '$time2'";
                                    $query = mysqli_query($conn,$sql); $count = mysqli_num_rows($query);
                                    if($count > 0){
                                        while($row = mysqli_fetch_assoc($query)){
                                            $aemp = $row['empcode'];
                                            $adtime1 = date("Y-m-d",strtotime($row['updated'])); $adtime2 = date("d.m.Y h:i:sA",strtotime($row['updated']));
                                            if($row['empcode'] != "" && strtotime($adtime1) == strtotime($tdate)){
                                                $i++;
                                                $html .= "<tr><td style='text-align:center;'>".$i."</td><td>".$user_name[$aemp]."</td><td>".$edate."</td><td>".$row['type']."</td><td>".$row['transactionno']."</td><td style='text-align:right;'>".number_format_ind($row['amount'])."</td><td style='color:red;'>Entry Deleted</td><td>".$adtime2."</td></tr>";
                                            }
                                        }
                                    }
                                }
                            }
                            echo $html;
                            ?>
                            </tbody>
                            <?php
							}
							?>
						</table>
					</form>
				</div>
		</section>
		<script type="text/javascript" lahguage="javascript">
			function checkval(){
				var a = document.getElementById("cname").value;
				if(a.match("select") || a.match("-select-")){
					alert("Please select customer ..!");
					return false;
				}
				else {
					return true;
				}
			}
		</script>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
