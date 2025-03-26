<?php
//broiler_edit_payment.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['payment'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['editaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
        $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code'];
        $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access'];
    }
    if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
    else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
    if($line_access_code == "all"){ $line_access_filter1 = ""; }
    else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
    if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
    else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
    if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
    else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }

    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$fpcode[$row['code']] = $row['code'];
			$fpname[$row['code']] = $row['name'];
		}
        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$wcode[$row['code']] = $row['code'];
			$wdesc[$row['code']] = $row['description'];
		}
		$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$acode[$row['code']] = $row['code'];
			$adesc[$row['code']] = $row['description'];
		}
        $sql = "SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Cash' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $cash_code[$row['code']] = $row['code'];
            $cash_name[$row['code']] = $row['description'];
        }
        $sql = "SELECT * FROM `acc_coa` WHERE `ctype` LIKE '%Bank%' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $bank_code[$row['code']] = $row['code'];
            $bank_name[$row['code']] = $row['description'];
        }
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: auto;
        }
        .form-control{
            padding-left: 1px;
            padding-right: 1px;
            margin-right: 10px;
            height: 25px;
        }
    </style>
    </head>
    <body class="m-0 p-0 hold-transition">
        <?php
            $id = $_GET['trnum'];
            $sql = "SELECT * FROM `broiler_payments` WHERE `trnum` = '$id'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $date = date("d.m.Y",strtotime($row['date']));
                $ccode = $row['ccode'];
                $docno = $row['docno'];
                $mode = $row['mode'];
                $method = $row['method'];
                $amount = $row['amount'];
                $bank_crg1 = round($row['bank_crg1'],5);
                $amtinwords = $row['amtinwords'];
                $vtype = $row['vtype'];
                $warehouse = $row['warehouse'];
                $remarks = $row['remarks'];
            }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Payment</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_payment.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row">
                                        <div class="form-group" style="width:110px;">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date" id="date" class="form-control rc_datepicker" style="width:100px;" value="<?php echo date('d.m.Y',strtotime($date)); ?>" />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Location<b style="color:red;">&nbsp;*</b></label>
							                <select name="sector" id="sector" class="form-control select2" style="width:160px;" ><option value="select">select</option><?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>" <?php if($warehouse == $fcode){ echo "selected"; } ?>><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group" style="width:170px;">
                                            <label>Supplier</label>
							                <select name="pname" id="pname" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($fpcode as $fcode){ ?> <option value="<?php echo $fpcode[$fcode]; ?>" <?php if($ccode == $fcode){ echo "selected"; } ?>><?php echo $fpname[$fcode]; ?></option> <?php } ?> </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Mode<b style="color:red;">&nbsp;*</b></label>
							                <select name="mode" id="mode" class="form-control select2" style="width:160px;" onchange="updatecode()"> <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>" <?php if($mode == $fcode){ echo "selected"; } ?>><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Code<b style="color:red;">&nbsp;*</b></label>
							                <select name="code" id="code" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <?php
                                                if($mode == "MOD-001"){ 
                                                    foreach($cash_code as $ccode){
                                                        ?>
														<option value="<?php echo $ccode; ?>" <?php if($method == $ccode){ echo 'selected'; } ?>><?php echo $cash_name[$ccode]; ?></option>
												        <?php
                                                    }
                                                }
                                                else{
                                                    foreach($bank_code as $bcode){
                                                        ?>
														<option value="<?php echo $bcode; ?>" <?php if($method == $bcode){ echo 'selected'; } ?>><?php echo $bank_name[$bcode]; ?></option>
												        <?php
                                                    }
                                                }
												?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>Amount<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="amount" id="amount" class="form-control" value="<?php echo $amount; ?>" style="width:100px;" onkeyup="validatenum(this.id);getamountinwords();" onchange="validateamount(this.id);">
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>Bank Charges</label>
							                <input type="text" name="bank_crg1" id="bank_crg1" class="form-control" value="<?php echo $bank_crg1; ?>" style="width:100px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);">
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>Reference No</label>
							                <input type="text" name="dcno" id="dcno" class="form-control" value="<?php echo $docno; ?>" style="width:100px;" />
                                        </div>
                                        <div class="form-group" style="width:130px;">
                                            <label>Remarks</label>
							                <textarea name="remark" id="remark" class="form-control" style="padding:1px;width:120px;height:23px;"><?php echo $remarks; ?></textarea>
                                        </div>
                                        <div class="form-group" style="width:60px;visibility:hidden">
                                            <label>Reference No</label>
							                <input type="text" name="gtamtinwords" id="gtamtinwords" class="form-control" value="<?php echo $amtinwords; ?>" style="width:50px;" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Update</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onclick="return_back()">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_payment.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
				var k = true;
                var g = document.getElementById("sector").value;
                var b = document.getElementById("pname").value;
                var c = document.getElementById("mode").value;
                var d = document.getElementById("code").value;
                var e = document.getElementById("amount").value;
                
                if(g.match("select") || g == "" || g.lenght == 0){
					alert("Please select sector");
                    document.getElementById("sector").focus();
					k = false;
				}
                else if(b.match("select")){
                    alert("Please select supplier name");
                    document.getElementById("pname").focus();
                    k = false;
                }
                else if(c.match("select")){
                    alert("Please select mode of payment");
                    document.getElementById("mode").focus();
                    k = false;
                }
                else if(d.match("select")){
                    alert("Please select Paying method");
                    document.getElementById("code").focus();
                    k = false;
                }
                else if(e == 0 || e == "" || e.lenght == 0){
                    alert("Please enter amount");
                    document.getElementById("amount").focus();
                    k = false;
                }
                else {
                    k = true;
                }
				if(k === true){
					return true;
				}
				else {
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
					return false;
				}
			}
			function getamountinwords() {
				var b = document.getElementById("amount").value;
				var c = convertNumberToWords(b);
				document.getElementById("gtamtinwords").value = c;
			}
			function updatecode(){
				var b = document.getElementById("mode").value;
				removeAllOptions(document.getElementById("code"));
				
				myselect = document.getElementById("code"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
				if(b.match("MOD-001")){
					<?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Cash' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
				else {
					<?php
					$sql="SELECT * FROM `acc_coa` WHERE `ctype` LIKE '%Bank%' AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
					while($row = mysqli_fetch_assoc($query)){ ?> 
						theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $row['description']; ?>"); theOption1.value = "<?php echo $row['code']; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
					<?php } ?>
				}
			}
            setInterval(function(){
                // window.screen.availHeight window.screen.availWidth
                if(window.screen.availWidth <= 400){
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; }
                }
                else{
                    const collection = document.getElementsByClassName("labelrow");
                    for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; }
                }
            }, 1000);
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
		<script src="main_numbertoamount.js"></script>
        <?php include "header_foot.php"; ?>
    </body>
</html>

<?php
    }
    else{
        echo "You don't have access to this page \n Kindly contact your admin for more information"; 
    }
}
else{
    echo "You don't have access to this page \n Kindly contact your admin for more information";
}
?>