<?php //main_updatefinancialyear.php
$fdate = date("Y-m-d",strtotime($_POST['fdate']));
$tdate = date("Y-m-d",strtotime($_POST['tdate']));

session_start(); include "newConfig.php";
$fyear = date("y",strtotime($fdate))."".date("y",strtotime($tdate));
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');

if(isset($_POST['submittrans']) == "addpage"){
	$sql = "SELECT * FROM `main_financialyear` WHERE `prefix` = '$fyear'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
	if($ccount > 0){
	?>
	<script> var x = alert('Financial Year already exist \n Kindly check ..!'); if(x == true){ window.location.href='main_displaydefinefinancialyear.php'; } else { window.location.href='main_displaydefinefinancialyear.php'; } </script>

	<?php
	}
	else {
		$sql = "INSERT INTO `main_financialyear` (prefix,fdate,tdate,flag,active,addedemp,addedtime,client) VALUES ('$fyear','$fdate','$tdate','0','1','$addedemp','$addedtime','$client')";
		if(!mysqli_query($conn,$sql)){ echo die("Error:- ".mysqli_error($conn)); } else {
			$sql = "SELECT * FROM `master_generator` WHERE `fdate` = '$fdate' AND `tdate` = '$tdate' AND `type` LIKE 'transactions'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
			if($ccount > 0){
			?>
			<script> var x = alert('Transactional details already exist \n Kindly check ..!'); if(x == true){ window.location.href='main_displaydefinefinancialyear.php'; } else { window.location.href='main_displaydefinefinancialyear.php'; } </script>

			<?php
			}
			else {
				$sql = "INSERT INTO `master_generator` (type,fdate,tdate,client) VALUES ('transactions','$fdate','$tdate','$client')";
				if(!mysqli_query($conn,$sql)){ echo die("Error:- ".mysqli_error($conn)); } else {
					$sql = "SELECT * FROM `master_generator` WHERE `fdate` = '$fdate' AND `tdate` = '$tdate' AND `type` LIKE 'displaypage'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
					if($ccount > 0){
					?>
					<script> var x = alert('Display page details already exist \n Kindly check ..!'); if(x == true){ window.location.href='main_displaydefinefinancialyear.php'; } else { window.location.href='main_displaydefinefinancialyear.php'; } </script>

					<?php
					}
					else {
						$sql = "INSERT INTO `master_generator` (type,fdate,tdate,client) VALUES ('displaypage','$fdate','$tdate','$client')";
						if(!mysqli_query($conn,$sql)){ echo die("Error in Generator:- ".mysqli_error($conn)); } else {
							header('location:main_displaydefinefinancialyear.php');
						}
					}
				}
			}
		}
	}
}
else{
	$page = $_GET['page'];
	if($page == "delete"){
		$id = $_GET['id']; $id = explode("@",$id); $fdate = date("Y-m-d",strtotime($id[0])); $tdate = date("Y-m-d",strtotime($id[1]));
		$sql = "SELECT * FROM `master_generator` WHERE `fdate` = '$fdate' AND `tdate` = '$tdate' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $df_purchases = $row['purchases']; $df_payments = $row['payments']; $df_sales = $row['sales']; $df_receipts = $row['receipts']; $df_vencredit = $row['vencredit']; $df_vendebit = $row['vendebit']; $df_cuscredit = $row['cuscredit']; $df_cusdebit = $row['cusdebit']; $df_stktransfer = $row['stktransfer']; $df_ireceipt = $row['ireceipt']; $df_iissue = $row['iissue']; $df_pvouchers = $row['pvouchers']; $df_rvouchers = $row['rvouchers']; $df_jvouchers = $row['jvouchers']; $df_stkadj = $row['stkadj']; }
		if(number_format($df_purchases,2) == "0.00" && number_format($df_payments,2) == "0.00" && number_format($df_sales,2) == "0.00" && number_format($df_receipts,2) == "0.00" && number_format($df_vencredit,2) == "0.00" && number_format($df_vendebit,2) == "0.00" && number_format($df_cuscredit,2) == "0.00" && number_format($df_cusdebit,2) == "0.00" && number_format($df_stktransfer,2) == "0.00" && number_format($df_ireceipt,2) == "0.00" && number_format($df_iissue,2) == "0.00" && number_format($df_pvouchers,2) == "0.00" && number_format($df_rvouchers,2) == "0.00" && number_format($df_jvouchers,2) == "0.00" && number_format($df_stkadj,2) == "0.00"){
			$sql = "DELETE FROM `master_generator` WHERE `fdate` = '$fdate' AND `tdate` = '$tdate'";
			if(!mysqli_query($conn,$sql)){
				echo die("Error:- ".mysqli_error($conn));
			}
			else {
				$sql = "DELETE FROM `main_financialyear` WHERE `fdate` = '$fdate' AND `tdate` = '$tdate' AND `flag` = '0'";
				if(!mysqli_query($conn,$sql)){
					echo die("Error:- ".mysqli_error($conn));
				}
				else {
					header('location:main_displaydefinefinancialyear.php');
				}
			}
		}
		else {
			?>
			<script> var x = alert('Already transactions are processed in this financial Year \n Kindly check ..!'); if(x == true){ window.location.href='main_displaydefinefinancialyear.php'; } else { window.location.href='main_displaydefinefinancialyear.php'; } </script>
		<?php
		}
	}
	else if($page == "audit"){
		$id = $_GET['id']; $id = explode("@",$id); $fdate = date("Y-m-d",strtotime($id[0])); $tdate = date("Y-m-d",strtotime($id[1]));
		$sql = "UPDATE `main_financialyear` SET `flag` = '1',`updatedemp` = '$addedemp' WHERE `fdate` = '$fdate' AND `tdate` = '$tdate'";
		if(!mysqli_query($conn,$sql)){ echo die("Error:- ".mysqli_error($conn)); } else { header('location:main_displaydefinefinancialyear.php'); }
	}
}
?>