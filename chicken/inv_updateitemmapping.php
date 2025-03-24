<?php
//inv_updateitemmapping.php
session_start(); include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$cid = $_SESSION['retcatmap'];

if($_POST['submittrans'] == "addpage"){
	$cat_incr = $_POST['incr'];
	
	for($i = 0;$i <= $cat_incr;$i++){
		if($retail_cat[$i] == ""){
			$retail_cat[$i] = $_POST['ret_cat'.$i];
		}
		else{
			$retail_cat[$i] = $retail_cat[$i]."','".$_POST['ret_cat'.$i];
		}
		foreach($_POST['ret_item'.$i] as $ritem){
			if($retail_item[$i] == ""){
				$retail_item[$i] = $ritem;
			}
			else{
				$retail_item[$i] = $retail_item[$i]."','".$ritem;
			}
		}
		foreach($_POST['ret_shop'.$i] as $rshop){
			if($retail_shop[$i] == ""){
				$retail_shop[$i] = $rshop;
			}
			else{
				$retail_shop[$i] = $retail_shop[$i].",".$rshop;
			}
		}
	}
	for($i = 0;$i <= $cat_incr;$i++){
		$sql = "UPDATE `item_category` SET `rflag` = '1',`flag` = '1',`rshops` = '$retail_shop[$i]' WHERE `code` IN ('$retail_cat[$i]')";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		
		$sql = "UPDATE `item_details` SET `rflag` = '1',`flag` = '1',`rshops` = '$retail_shop[$i]' WHERE `code` IN ('$retail_item[$i]')";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
	}
	?>
	<script>
		var a = '<?php echo $cid; ?>';
		var x = confirm("Would you like add one more Category ?");
		if(x == true){
			window.location.href = "inv_additemmapping.php?cid="+a;
		}
		else if(x == false) {
			window.location.href = "inv_itemmapping.php?cid="+a;
		}
		else {
			window.location.href = "inv_itemmapping.php?cid="+a;
		}
	</script>
	<?php
}
else if($_POST['submittrans'] == "updatepage"){
	$retail_cat = $_POST['ret_cat']; $retail_item = $_POST['ret_item']; $retail_shop = "";
	foreach($_POST['ret_shop'] as $rshop){
		if($retail_shop == ""){
			$retail_shop = $rshop;
		}
		else{
			$retail_shop = $retail_shop.",".$rshop;
		}
	}
	$sql = "UPDATE `item_category` SET `rflag` = '1',`flag` = '1',`rshops` = '$retail_shop' WHERE `code` IN ('$retail_cat')";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		
	$sql = "UPDATE `item_details` SET `rflag` = '1',`flag` = '1',`rshops` = '$retail_shop' WHERE `code` IN ('$retail_item')";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_itemmapping.php?cid='.$cid); }
}
else{
	$page = $_GET['page'];
	$id = $_GET['id'];
	if($page == "delete"){
		$sql = "UPDATE `item_details` SET `rflag` = '0' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
		
		$sql = "SELECT * FROM `item_details` WHERE `code` = '$id'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$item_cat = $row['category'];
		}
		$sql = "SELECT * FROM `item_details` WHERE `category` = '$item_cat' AND `rflag` = '1'";
		$query = mysqli_query($conn,$sql); $icount = mysqli_num_rows($query);
		
		if($icount > 0){ header('location:inv_itemmapping.php?cid='.$cid); }
		else{
			$sql = "UPDATE `item_category` SET `rflag` = '0' WHERE `code` = '$item_cat'";
			if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_itemmapping.php?cid='.$cid); }
		}
	}
	else if($page == "activate"){
		$sql = "UPDATE `item_details` SET `active` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_itemmapping.php?cid='.$cid); }
	}
	else if($page == "pause"){
		$sql = "UPDATE `item_details` SET `active` = '0' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_itemmapping.php?cid='.$cid); }
	}
	else if($page == "authorize"){
		$sql = "UPDATE `item_details` SET `flag` = '1' WHERE `code` = '$id'";
		if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:inv_itemmapping.php?cid='.$cid); }
	}
	else{
	
	}
}
?>