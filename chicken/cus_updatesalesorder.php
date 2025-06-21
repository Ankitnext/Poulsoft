<?php

//cus_updatesalesorder.php

session_start(); include "newConfig.php";

include "number_format_ind.php";

date_default_timezone_set("Asia/Kolkata");

$addedemp = $_SESSION['userid'];

$addedtime = date('Y-m-d H:i:s');

$client = $_SESSION['client'];

if($_POST['submittrans'] == "addpage"){

	$date = date("Y-m-d",strtotime($_POST['pdate']));

	$i = 0; foreach($_POST['cnames'] as $cnames){ $i = $i + 1; $cdetails = explode("@",$cnames); $vendorcode[$i] = $cus_code[$i] = $cdetails[0]; $cus_names[$i] = $cdetails[1]; }

	$i = 0; foreach($_POST['scat'] as $icats){ $i = $i + 1; $itemdetails = explode("@",$icats); $itemcode[$i] = $itemdetails[0]; }

	$i = 0; foreach($_POST['jval'] as $jal){ $i = $i + 1; $jals[$i] = $jal; }

	$i = 0; foreach($_POST['bval'] as $bird){ $i = $i + 1; $birds[$i] = $bird; }

	$i = 0; foreach($_POST['ddate'] as $ddate){ $i = $i + 1; $ddates[$i] = date("Y-m-d",strtotime($ddate)); }

	$i = 0; foreach($_POST['narr'] as $narr){ $i = $i + 1; $narrs[$i] = $narr; }

	$i = 0; foreach($_POST['wval'] as $weights){ $i = $i + 1; $totalweight[$i] = $weights; }

	$i = 0; $i = sizeof($cus_code);

	

	$sql = "SELECT * FROM `main_financialyear` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `active` = '1'"; $query = mysqli_query($conn,$sql);

	while($row = mysqli_fetch_assoc($query)){ $fprefix = $row['prefix']; }

			

	$sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);

	while($row = mysqli_fetch_assoc($query)){ $so = $row['salesorder']; } $incr = $so + $i;

	

	$sql = "UPDATE `master_generator` SET `salesorder` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";

	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

	

	$incr = $so;

	for($j = 1;$j <= $i;$j++){

		if($cus_code[$i] == "select" || $itemcode[$j] == "select") {

			

		}

		else{

			$incr = $incr + 1;

			if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }

			$trnum = "SO-".$fprefix."".$incr;

			if($jals[$j] == "" || $jals[$j] == NULL){ $jals[$j] = "0"; }

			if($birds[$j] == "" || $birds[$j] == NULL){ $birds[$j] = "0"; }

			if($totalweight[$j] == "" || $totalweight[$j] == NULL){ $totalweight[$j] = "0"; }

			$sql = "INSERT INTO `salesorder` (incr,prefix,trnum,date,ccode,itemcode,jals,birds,twt,delivery_date,remarks,mflag,addedemp,addeddate,updatetime) 

			VALUES ('$incr','SO','$trnum','$date','$cus_code[$j]','$itemcode[$j]','$jals[$j]','$birds[$j]','$totalweight[$j]','$ddates[$j]','$narrs[$j]','0','$addedemp','$addedtime','$addedtime')";

			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

		}

	}

	?>

	<script>

		var x = confirm("Would you like to save more entries?");

		if(x == true){

			window.location.href = "cus_addsalesorder.php";

		}

		else if(x == false){

			window.location.href = "cus_salesorder.php?cid=P2-C12";

		}

	</script>

	<?php

}

else if($_POST['submittrans'] == "updatepage"){

	$pdate = date("Y-m-d",strtotime($_POST['pdate']));

	$trnums = $_POST['trnums'];

	$cnames = explode("@",$_POST['cnames']);

	$ccode = $cnames[0];

	$scat = explode("@",$_POST['scat']);

	$itemcode = $scat[0];

	$jval = $_POST['jval'];

	$bval = $_POST['bval'];

	$wval = $_POST['wval'];

	$narr = $_POST['narr'];

	$ddates = date("Y-m-d",strtotime($_POST['ddate']));

			if($jval == "" || $jval == NULL){ $jval = "0"; }

			if($bval == "" || $bval == NULL){ $bval = "0"; }

			if($wval == "" || $wval == NULL){ $wval = "0"; }

	$sql = "UPDATE `salesorder` SET `date` ='$pdate', `ccode` ='$ccode', `itemcode` ='$itemcode', `jals` ='$jval', `birds` ='$bval', `twt` ='$wval',`delivery_date` = '$ddates', `remarks` ='$narr', `addedemp` ='$addedemp', `updatetime` ='$addedtime' WHERE `trnum` = '$trnums'";

	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:cus_salesorder.php?cid=P2-C12'); }
               
}

else{

	$id = $_GET['id'];

	$updatetype = $_GET['page'];

		

	if($updatetype == "edit"){ header('location:cus_editsales.php?id='.$id); }

		

	else if($updatetype == "delete"){

		/*$sql ="SELECT * FROM `salesorder` WHERE `trnum` = '$id' AND `isDelete` = '0'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);

		if($ccount > 0){

			while($row = mysqli_fetch_assoc($query)){

				$type = "Sales Order";

				$date = $row['date'];

				$transactionno = $row['trnum'];

				$pcode = $row['ccode'];

				$icode = $row['itemcode'];

				$quantity = $row['jals']."@".$row['birds']."@".$row['twt'];

				$amount = $row['finaltotal'];

				$sql = "INSERT INTO `main_deletiondetails` (type,date,transactionno,pcode,icode,quantity,amount,empcode,client) 

				VALUES('$type','$date','$transactionno','$pcode','$icode','$quantity','$amount','$addedemp','$client')";

				if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

			}

			$sql = "DELETE FROM `salesorder` WHERE `trnum` = '$id'";

			if(!mysqli_query($conn,$sql)){

				die("Error:-".mysqli_error($conn));

			}

			else {

				header('location:cus_salesorder.php?cid=P2-C12');

			}

		}

		else {

		?>

			<script>

			var x = alert("This transaction is already approved or used, \n kindly check the transaction");

			if(x == true){

				window.location.href = "cus_salesorder.php?cid=P2-C12";

			}

			else if(x == false) {

				window.location.href = "cus_salesorder.php?cid=P2-C12";

			}

			else {

				window.location.href = "cus_salesorder.php?cid=P2-C12";

			}

			</script>

		<?php

		}

		*/

		$id = $_GET['id'];

		$sql = "UPDATE `salesorder` SET `isDelete` = '1' WHERE `trnum` = '$id'";

		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:cus_salesorder.php?cid=P2-C12'); }

	}

	else if($updatetype == "activate"){

		$id = $_GET['id'];

		$sql = "UPDATE `salesorder` SET `active` = '1' WHERE `trnum` = '$id'";

		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:cus_salesorder.php?cid=P2-C12'); }

	}

	else if($updatetype == "pause"){

		$id = $_GET['id'];

		$sql = "UPDATE `salesorder` SET `active` = '0' WHERE `trnum` = '$id'";

		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:cus_salesorder.php?cid=P2-C12'); }

	}

	else if($updatetype == "authorize"){

		$id = $_GET['id'];

		$sql = "UPDATE `salesorder` SET `flag` = '1',`approvedemp` = '$addedemp',`approvedtime` = '$addedtime' WHERE `trnum` = '$id'";

		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:cus_salesorder.php?cid=P2-C12'); }

	}

	else {}

	

}

?>