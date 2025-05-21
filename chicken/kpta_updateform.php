<?php
//kpta_updateform.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
$client = $_SESSION['client'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$cid = $_SESSION['kptaform'];
if(isset($_POST['submittrans']) == "addpage"){
	$doj = date("Y-m-d",strtotime($_POST['doj']));
	$file_no = $_POST['file_no'];
	$zone = $_POST['zone'];
	$name = $_POST['name'];
	$father_name = $_POST['father_name'];
	$dob = date("Y-m-d",strtotime($_POST['dob']));
	$age = $_POST['age'];
	$mobile_no = $_POST['mobile_no'];
	$address = $_POST['address'];
	$business_farm_name = $_POST['business_farm_name'];
	$co_license_no = $_POST['co_license_no'];
	$business_address = $_POST['business_address'];
	$daily_sales = $_POST['daily_sales'];
	$kpta_opinion = $_POST['kpta_opinion'];
	
	/*Images*/
	//echo $_POST["photo_img"];
	if(!empty($_FILES["photo_img"]["name"])){
	$fileName = basename($_FILES["photo_img"]["name"]); 
	$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
	$image = $_FILES['photo_img']['tmp_name']; 
	$photo_img = addslashes(file_get_contents($image)); 
	$filetmp = $_FILES['photo_img']['tmp_name'];
	$filename = $_FILES['photo_img']['name'];
	$filetype = $_FILES['photo_img']['type'];
	$photo_img_path = "kpta_images/".$filename;
	$ppath = "printformatlibrary/Examples/kpta_images/".$filename;
	move_uploaded_file($filetmp,$ppath);
	} else { $photo_img = ""; $photo_img_path = ""; }
	
	if(!empty($_FILES["doc1"]["name"])){
	$fileName = basename($_FILES["doc1"]["name"]); 
	$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
	$image = $_FILES['doc1']['tmp_name']; 
	$doc1 = addslashes(file_get_contents($image)); 
	$filetmp = $_FILES['doc1']['tmp_name'];
	$filename = $_FILES['doc1']['name'];
	$filetype = $_FILES['doc1']['type'];
	$doc1_path = "kpta_images/".$filename;
	$d1_path = "printformatlibrary/Examples/kpta_images/".$filename;
	move_uploaded_file($filetmp,$d1_path);
	} else { $doc1 = ""; $doc1_path = "";}
	
	if(!empty($_FILES["doc2"]["name"])){
	$fileName = basename($_FILES["doc2"]["name"]); 
	$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
	$image = $_FILES['doc2']['tmp_name']; 
	$doc2 = addslashes(file_get_contents($image)); 
	$filetmp = $_FILES['doc2']['tmp_name'];
	$filename = $_FILES['doc2']['name'];
	$filetype = $_FILES['doc2']['type'];
	$doc2_path = "kpta_images/".$filename;
	$d2_path = "printformatlibrary/Examples/kpta_images/".$filename;
	move_uploaded_file($filetmp,$d2_path);
	} else { $doc2 = ""; $doc2_path = ""; }
	
	if(!empty($_FILES["doc3"]["name"])){
	$fileName = basename($_FILES["doc3"]["name"]); 
	$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
	$image = $_FILES['doc3']['tmp_name']; 
	$doc3 = addslashes(file_get_contents($image)); 
	$filetmp = $_FILES['doc3']['tmp_name'];
	$filename = $_FILES['doc3']['name'];
	$filetype = $_FILES['doc3']['type'];
	$doc3_path = "kpta_images/".$filename;
	$d3_path = "printformatlibrary/Examples/kpta_images/".$filename;
	move_uploaded_file($filetmp,$d3_path);
	} else { $doc3 = ""; $doc3_path = ""; }
	
	if(!empty($_FILES["doc4"]["name"])){
	$fileName = basename($_FILES["doc4"]["name"]); 
	$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
	$image = $_FILES['doc4']['tmp_name']; 
	$doc4 = addslashes(file_get_contents($image)); 
	$filetmp = $_FILES['doc4']['tmp_name'];
	$filename = $_FILES['doc4']['name'];
	$filetype = $_FILES['doc4']['type'];
	$doc4_path = "kpta_images/".$filename;
	$d4_path = "printformatlibrary/Examples/kpta_images/".$filename;
	move_uploaded_file($filetmp,$d4_path);
	} else { $doc4 = ""; $doc4_path = ""; }
	
	if(!empty($_FILES["doc5"]["name"])){
	$fileName = basename($_FILES["doc5"]["name"]); 
	$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
	$image = $_FILES['doc5']['tmp_name']; 
	$doc5 = addslashes(file_get_contents($image)); 
	$filetmp = $_FILES['doc5']['tmp_name'];
	$filename = $_FILES['doc5']['name'];
	$filetype = $_FILES['doc5']['type'];
	$doc5_path = "kpta_images/".$filename;
	$d5_path = "printformatlibrary/Examples/kpta_images/".$filename;
	move_uploaded_file($filetmp,$d5_path);
	} else { $doc5 = ""; $doc5_path = ""; }
	
	$witness_name = $_POST['witness_name'];
	$witness_no = $_POST['witness_no'];
	
	$sql = "SELECT MAX(incr) as incr FROM `kpta_form`"; $query = mysqli_query($conn,$sql); $incr_count = mysqli_num_rows($query);
	if($incr_count > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } } else{ $incr = 0; } $incr = $incr + 1;
	if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
	
	$prefix = "KPTF";
	$code = $prefix."-".$incr;
	
	$sql = "INSERT INTO `kpta_form` (incr,prefix,code,doj,file_no,zone,name,father_name,address,business_address,dob,age,mobile_no,business_farm_name,daily_sales,co_license_no,kpta_opinion,witness_name,witness_no,photo_img,doc1,doc2,doc3,doc4,doc5,photo_img_path,doc1_path,doc2_path,doc3_path,doc4_path,doc5_path,active,dflag,addedemp,addedtime,client)
	 VALUES('$incr','$prefix','$code','$doj','$file_no','$zone','$name','$father_name','$address','$business_address','$dob','$age','$mobile_no','$business_farm_name','$daily_sales','$co_license_no','$kpta_opinion','$witness_name','$witness_no','$photo_img','$doc1','$doc2','$doc3','$doc4','$doc5','$photo_img_path','$doc1_path','$doc2_path','$doc3_path','$doc4_path','$doc5_path','1','0','$addedemp','$addedtime','$client')";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
	else {
	?>
		<script>
			var x = confirm("Would you like to add more entries? <?php echo $cid; ?>");
			if(x == true){
				window.location.href = "kpta_addform.php?cid=<?php echo $cid; ?>";
			}
			else if(x == false){
				window.location.href = "kpta_displayform.php?cid=<?php echo $cid; ?>";
			}
		</script>
	<?php
	}
	
}
else if(isset($_POST['submittran']) == "updatepage"){
	$code = $_POST['code'];
	$doj = date("Y-m-d",strtotime($_POST['doj']));
	$file_no = $_POST['file_no'];
	$zone = $_POST['zone'];
	$name = $_POST['name'];
	$father_name = $_POST['father_name'];
	$dob = date("Y-m-d",strtotime($_POST['dob']));
	$age = $_POST['age'];
	$mobile_no = $_POST['mobile_no'];
	$address = $_POST['address'];
	$business_farm_name = $_POST['business_farm_name'];
	$co_license_no = $_POST['co_license_no'];
	$business_address = $_POST['business_address'];
	$daily_sales = $_POST['daily_sales'];
	$kpta_opinion = $_POST['kpta_opinion'];
	
	/*Images*/
	//echo $_POST["photo_img"];
	if(!empty($_FILES["photo_img"]["name"])) { 
		$fileName = basename($_FILES["photo_img"]["name"]); 
		$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
		$image = $_FILES['photo_img']['tmp_name']; 
		$photo_img = addslashes(file_get_contents($image)); 
		$filetmp = $_FILES['photo_img']['tmp_name'];
		$filename = $_FILES['photo_img']['name'];
		$filetype = $_FILES['photo_img']['type'];
		$photo_img_path = "kpta_images/".$filename;
		$ppath = "printformatlibrary/Examples/kpta_images/".$filename;
		move_uploaded_file($filetmp,$ppath);
		$upd_profile = ",`photo_img` = '$photo_img',`photo_img_path` = '$photo_img_path'";
	} else { $upd_profile = ""; $photo_img_path = ""; }
	if(!empty($_FILES["doc1"]["name"])) { 
		$fileName = basename($_FILES["doc1"]["name"]); 
		$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
		$image = $_FILES['doc1']['tmp_name']; 
		$doc1 = addslashes(file_get_contents($image)); 
		$filetmp = $_FILES['doc1']['tmp_name'];
		$filename = $_FILES['doc1']['name'];
		$filetype = $_FILES['doc1']['type'];
		$doc1_path = "kpta_images/".$filename;
		$d1_path = "printformatlibrary/Examples/kpta_images/".$filename;
		move_uploaded_file($filetmp,$d1_path);
		$upd_doc1 = ",`doc1` = '$doc1',`doc1_path` = '$doc1_path'";
	} else { $upd_doc1 = ""; $doc1_path = ""; }
	if(!empty($_FILES["doc2"]["name"])) { 
		$fileName = basename($_FILES["doc2"]["name"]); 
		$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
		$image = $_FILES['doc2']['tmp_name']; 
		$doc2 = addslashes(file_get_contents($image)); 
		$filetmp = $_FILES['doc2']['tmp_name'];
		$filename = $_FILES['doc2']['name'];
		$filetype = $_FILES['doc2']['type'];
		$doc2_path = "kpta_images/".$filename;
		$d2_path = "printformatlibrary/Examples/kpta_images/".$filename;
		move_uploaded_file($filetmp,$d2_path);
		$upd_doc2 = ",`doc2` = '$doc2',`doc2_path` = '$doc2_path'";
	} else { $upd_doc2 = ""; $doc2_path = ""; }
	if(!empty($_FILES["doc3"]["name"])) { 
		$fileName = basename($_FILES["doc3"]["name"]); 
		$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
		$image = $_FILES['doc3']['tmp_name']; 
		$doc3 = addslashes(file_get_contents($image)); 
		$filetmp = $_FILES['doc3']['tmp_name'];
		$filename = $_FILES['doc3']['name'];
		$filetype = $_FILES['doc3']['type'];
		$doc3_path = "kpta_images/".$filename;
		$d3_path = "printformatlibrary/Examples/kpta_images/".$filename;
		move_uploaded_file($filetmp,$d3_path);
		$upd_doc3 = ",`doc3` = '$doc3',`doc3_path` = '$doc3_path'";
	} else { $upd_doc3 = ""; $doc3_path = ""; }
	if(!empty($_FILES["doc4"]["name"])) { 
		$fileName = basename($_FILES["doc4"]["name"]); 
		$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
		$image = $_FILES['doc4']['tmp_name']; 
		$doc4 = addslashes(file_get_contents($image)); 
		$filetmp = $_FILES['doc4']['tmp_name'];
		$filename = $_FILES['doc4']['name'];
		$filetype = $_FILES['doc4']['type'];
		$doc4_path = "kpta_images/".$filename;
		$d4_path = "printformatlibrary/Examples/kpta_images/".$filename;
		move_uploaded_file($filetmp,$d4_path);
		$upd_doc4 = ",`doc4` = '$doc4',`doc4_path` = '$doc4_path'";
	} else { $upd_doc4 = ""; $doc4_path = ""; }
	if(!empty($_FILES["doc5"]["name"])) { 
		$fileName = basename($_FILES["doc5"]["name"]); 
		$fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
		$image = $_FILES['doc5']['tmp_name']; 
		$doc5 = addslashes(file_get_contents($image)); 
		$filetmp = $_FILES['doc5']['tmp_name'];
		$filename = $_FILES['doc5']['name'];
		$filetype = $_FILES['doc5']['type'];
		$doc5_path = "kpta_images/".$filename;
		$d5_path = "printformatlibrary/Examples/kpta_images/".$filename;
		move_uploaded_file($filetmp,$d5_path);
		$upd_doc5 = ",`doc5` = '$doc5',`doc5_path` = '$doc5_path'";
	} else { $upd_doc5 = ""; $doc5_path = ""; }
	$witness_name = $_POST['witness_name'];
	$witness_no = $_POST['witness_no'];
		$upload_img = $upd_profile."".$upd_doc1."".$upd_doc2."".$upd_doc3."".$upd_doc4."".$upd_doc5;
	$sql = "UPDATE `kpta_form` SET `doj` = '$doj',`file_no` = '$file_no',`zone` = '$zone',`name` = '$name',`father_name` = '$father_name',`address` = '$address',`business_address` = '$business_address',`dob` = '$dob',`age` = '$age',`mobile_no` = '$mobile_no',`business_farm_name` = '$business_farm_name',`daily_sales` = '$daily_sales',`co_license_no` = '$co_license_no',`kpta_opinion` = '$kpta_opinion',`witness_name` = '$witness_name',`witness_no` = '$witness_no'".$upload_img.",`active` = '1',`dflag` = '0',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime',`client` = '$client' WHERE `code` = '$code'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
	else {
		header('location:kpta_displayform.php?cid='.$cid);
	}
}
else if($_GET['page'] == "delete"){
	$id = $_GET['id'];
	$sql = "UPDATE `kpta_form` SET `active` = '0',`dflag` = '1' WHERE `code` = '$id'";
	if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); }
	else {
		header('location:kpta_displayform.php?cid='.$cid);
	}
}
?>