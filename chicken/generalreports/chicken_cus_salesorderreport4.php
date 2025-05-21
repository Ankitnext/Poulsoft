<?php
	//chicken_cus_salesorderreport4.php
	$time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $start = $time;
    $requested_data = json_decode(file_get_contents('php://input'),true);
	session_start();
    $db = $_SESSION['db'] = $_GET['db'];
	if($db == ''){ include "../config.php"; include "header_head.php"; include "number_format_ind.php";$dbname = $_SESSION['dbase'];
		$users_code = $_SESSION['userid']; }
	else{ include "APIconfig.php"; include "number_format_ind.php"; include "header_head.php"; $dbname = $db;
		$users_code = $_GET['emp_code']; 
        $admin_flag = mysqli_fetch_assoc(mysqli_query($conn,"SELECT admin_access FROM `main_access` WHERE empcode = '$users_code' AND active = 1"))['admin_access'];
        if($admin_flag == 1){
            $cond = "";
        }else{
            $cond = " AND code = '$users_code'";
        }
       
    }

    
	$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' $cond ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
	while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
	$sql = "SELECT * FROM `item_details` ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
	while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }
    
    $date_type = "delivery_date"; $fdate = date("Y-m-d"); $vendors = $items = "all"; $trnums = "";
    if(isset($_POST['submit']) == true){
        $date_type = $_POST['date_type'];
        $fdate = date("Y-m-d",strtotime($_POST['fdate']));
        $vendors = $_POST['vendors'];
        $items = $_POST['items'];
    }
    else if(isset($_REQUEST['date_type']) == true){
        $date_type = $_REQUEST['date_type'];
        $fdate = date("Y-m-d",strtotime($_REQUEST['fdate']));
        $vendors = $_REQUEST['vendors'];
        $items = $_REQUEST['items'];
        $trnums = $_REQUEST['trnums'];
    }
	
	$exoption = "displaypage";
	$url = "../PHPExcel/Examples/tdsSummary-Excel.php?fromdate=".$fromdate."&todate=".$todate;
	
?>	
<html>
	<head><link rel="stylesheet" type="text/css"href="reportstyle.css">
		<script>
			var exptype = '<?php echo $excel_type; ?>';
			var url = '<?php echo $url; ?>';
			if(exptype.match("exportexcel")){
				window.open(url,'_BLANK');
			}
		</script>
		<style>
			body{
				color: black;
			}
			.formcontrol {
				height: 23px;
				border: 0.1vh solid gray;
			}
			.formcontrol:focus {
				height: 23px;
				border: 0.1vh solid gray;
				outline: none;
			}
			.thead2 th {
				padding: 5px;
			}
			.tbody1 td {
				padding-right: 5px;
				text-align: right;
			}
		</style>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<header align="center">
			<table align="center" class="reportheadermenu">
				<tr>
				<?php
					$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?>
					<td><img src="../<?php echo $row['logopath']; ?>" height="150px"/></td>
					<td><?php echo $row['cdetails']; ?></td> <?php } ?></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<label style="font-weight:bold;" class="reportheaderlabel">Customer Sale Order Report</label>&ensp;
                        <?php
							if($cname == "all" || $cname == "select" || $cname == "") { } else {
						?>
							<label class="reportheaderlabel"><b style="color: green;">Supplier:</b>&nbsp;<?php echo $cus_name[$cname]; ?></label>&ensp;
						<?php
							}
						?>
						<label class="reportheaderlabel"><b style="color: green;">Date:</b>&nbsp;<?php echo date("d.m.Y",strtotime($fdate)); ?></label>
					</td>
				</tr>
			</table>
		</header>
		<section class="content" align="center">
				<div class="col-md-12" align="center">
					<table class="table1" style="width:auto;line-height:23px;">
                        <?php if($db == ''){?>
				        <form action="chicken_cus_salesorderreport4.php" method="post" >
					<?php } else { ?>
					<form action="chicken_cus_salesorderreport4.php?db=<?php echo $db; ?>&emp_code=<?php echo $_GET['emp_code']; ?>" method="post" >
					<?php } ?>
							<thead class="thead1" style="background-color: #98fb98;">
								<tr>
									<td colspan="20">
                                        <div class="row">
                                            <div class="form-group col-md-2" style="width:160px;">
                                                <label class="reportselectionlabel">Date Type</label>&nbsp;
                                                <select name="date_type" id="date_type" class="form-control select2" style="width:150px;">
                                                    <option value="order_date" <?php if($date_type == "order_date") { echo 'selected'; } ?>>Order Date</option>
                                                    <option value="delivery_date" <?php if($date_type == "delivery_date") { echo 'selected'; } ?>>Delivery Date</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style="width:120px;">
                                                <label class="reportselectionlabel">date</label>&nbsp;
                                                <input type="text" name="fdate" id="datepickers" class="form-control" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:110px;" readonly />
                                            </div>
                                            <div class="form-group col-md-2" style="width:190px;">
                                                <label class="reportselectionlabel">Customer</label>&nbsp;
                                                <select name="vendors" id="vendors" class="form-control select2" style="width:180px;">
                                                    <?php if($db == '' || $admin_flag == 1){ ?>
                                                        <option value="all" <?php if($vendors == "all") { echo 'selected'; } ?>>-All-</option>
                                                   <?php } ?>
                                                    
                                                    <?php foreach($cus_code as $vcode){ ?><option value="<?php echo $vcode; ?>" <?php if($vendors == $vcode) { echo 'selected'; } ?>><?php echo $cus_name[$vcode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2" style="width:190px;">
                                                <label class="reportselectionlabel">Item</label>&nbsp;
                                                <select name="items" id="items" class="form-control select2" style="width:180px;">
                                                    <option value="all" <?php if($items == "all") { echo 'selected'; } ?>>-All-</option>
                                                    <?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>" <?php if($items == $icode) { echo 'selected'; } ?>><?php echo $item_name[$icode]; ?></option><?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <br/><button type="submit" name="submit" id="submit" class="btn btn-warning btn-sm">Open Report</button>
                                            </div>
										</div>
									</td>
								</tr>
							</thead>
                        </form>
						<?php
                        if(isset($_POST['submit']) == true || isset($_REQUEST['date_type']) == true){
                            $date_filter = $col_dname = ""; if($date_type == "delivery_date"){ $date_filter = " AND `delivery_date` = '$fdate'"; $col_dname = "delivery_date"; } else{ $date_filter = " AND `date` = '$fdate'"; $col_dname = "date"; }
                            $vendor_filter = ""; if($vendors != "all"){ $vendor_filter = " AND `ccode` LIKE '$vendors'"; }
                            $item_filter = ""; if($items != "all"){ $item_filter = " AND `itemcode` LIKE '$items'"; }
                            $trnum_filter = ""; if($trnums != ""){ $trnum_filter = " AND `trnum` LIKE '$trnums'"; }

                            $html = '';
                            $html .= '<thead class="thead2" style="background-color: #98fb98;">';
                            $html .= '<tr>';
                            $html .= '<th>Sl No.</th>';
                            $html .= '<th>Order Date</th>';
                            $html .= '<th>Order No.</th>';
                            $html .= '<th>Customer</th>';
                            $html .= '<th>Delivery Date</th>';
                            $html .= '<th>Item</th>';
                            $html .= '<th>Order Quantity</th>';
                            $html .= '<th>Delivered Quantity</th>';
                            $html .= '<th>Status</th>';
                            $html .= '</tr>';
                            $html .= '</thead>';
                            $html .= '<tbody class="tbody1" style="background-color: #f4f0ec;">';
                            
                            $sql = "SELECT * FROM `salesorder` WHERE `isDelete` = '0'".$date_filter."".$vendor_filter."".$item_filter."".$trnum_filter." ORDER BY `$col_dname`,`ccode`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $inv_trnums = array();
                            while($row = mysqli_fetch_assoc($query)){ $inv_trnums[$row['trnum']] = $row['trnum']; }

                            $trnum_list = implode("','",$inv_trnums);
                            $sql = "SELECT * FROM `customer_sales` WHERE `so_trnum` IN ('$trnum_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice`,`itemcode` ASC";
                            $query = mysqli_query($conn,$sql); $sale_qty = array();
                            while($row = mysqli_fetch_assoc($query)){ $key = ""; $key = $row['so_trnum']."@".$row['itemcode']; $sale_qty[$key] += (float)$row['netweight']; }

                            $sql = "SELECT * FROM `salesorder` WHERE `isDelete` = '0'".$date_filter."".$vendor_filter."".$item_filter."".$trnum_filter." ORDER BY `$col_dname`,`ccode`,`trnum` ASC";
                            $query = mysqli_query($conn,$sql); $tot_oqty = $tot_dqty = 0; $old_inv = ""; $sl = 1;
                            while($row = mysqli_fetch_assoc($query)){
                                $key = $row['trnum']."@".$row['itemcode'];
                                if(empty($sale_qty[$key]) || $sale_qty[$key] == ""){ $sale_qty[$key] = 0; }

                                $html .= '<tr>';
                                if($old_inv != $row['trnum']){
                                    $html .= '<td style="text-align:left;">'.$sl++.'</td>';
                                    $html .= '<td style="text-align:left;">'.date("d.m.Y",strtotime($row['date'])).'</td>';
                                    $html .= '<td style="text-align:left;">'.$row['trnum'].'</td>';
                                    $html .= '<td style="text-align:left;">'.$cus_name[$row['ccode']].'</td>';
                                    if(date("d.m.Y",strtotime($row['delivery_date'])) == '01.01.1970'){
                                        $html .= '<td style="text-align:left;">'."".'</td>';
                                    }else{
                                        $html .= '<td style="text-align:left;">'.date("d.m.Y",strtotime($row['delivery_date'])).'</td>';
                                    }
                                    
                                    $old_inv = $row['trnum'];
                                }
                                else{
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                }
                                $html .= '<td style="text-align:left;">'.$item_name[$row['itemcode']].'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($row['twt']).'</td>';
                                $html .= '<td style="text-align:right;">'.number_format_ind($sale_qty[$key]).'</td>';
                                if((int)$row['sale_flag'] == 1){
                                    $html .= '<td style="color:green;text-align:left;">Delivered</td>';
                                }
                                else{
                                    $html .= '<td style="color:red;text-align:left;">Pending</td>';
                                }
                                $html .= '</tr>';
                                $tot_oqty += (float)$row['twt'];
                                $tot_dqty += (float)$sale_qty[$key];
                            }
                            $html .= '</tbody>';
                            $html .= '<tfoot>';
                            $html .= '<tr class="foottr" style="background-color: #98fb98;">';
                            $html .= '<td colspan="6" style="text-align:center;"><b>Total</b></td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind($tot_oqty).'</td>';
                            $html .= '<td style="text-align:right;">'.number_format_ind($tot_dqty).'</td>';
                            $html .= '<td></td>';
                            $html .= '</tr>';
                            $html .= '</tfoot>';
                            
                            echo $html;
						}
						?>
					</table>
				</div>
		</section>
		<?php if($exoption == "displaypage") { ?><footer align="center" style="margin-top:50px;"><?php $time = microtime(); $time = explode(' ', $time); $time = $time[1] + $time[0]; $finish = $time; $total_time = round(($finish - $start), 4); echo "Loaded in ".$total_time." seconds."; ?></footer><?php } ?>
		<script src="../loading_page_out.js"></script>
	</body>
	
</html>
<?php include "header_foot.php"; ?>
