<?php
//broiler_edit_contraaccount.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['contraaccount'];
date_default_timezone_set("Asia/Kolkata");
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $elink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $elink = explode(",",$row['editaccess']);
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
        foreach($elink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
		
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

		$sql = "SELECT * FROM `acc_coa` WHERE `visible_flag` = '1' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $item_name = array();
        while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['name']; }

        $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `dflag` = '0'".$farm_access_filter1."".$branch_access_filter2."".$line_access_filter2." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
        
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
            padding-left: 2px;
            padding-right: 0px;
        }
        .form-group{
            margin: 0 3px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $id = $_GET['trnum'];
        $sql = "SELECT * FROM `account_contranotes` WHERE `trnum` = '$id'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $date = $row['date'];
            $dcno = $row['dcno'];
            $fcoa = $row['fcoa'];
            $tcoa = $row['tcoa'];
            $to_batch = $row['to_batch'];
            $amount = $row['amount'];
            $warehouse = $row['warehouse'];
            $remarks = $row['remarks'];
        }
        $batch_slist = ""; $farm_flag = 0;
        if(!empty($farm_code[$tcoa]) && $farm_code[$tcoa] == $tcoa){
            $sql = "SELECT * FROM `broiler_batch` WHERE `farm_code` = '$tcoa' AND `active` = '1' AND `dflag` = '0' ORDER BY `batch_no` ASC";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $bcode = $row['code']; $bname = $row['description'];
                if($row['code'] == $to_batch){ $batch_slist .= '<option value="'.$bcode.'" selected>'.$bname.'</option>'; }
                else{ $batch_slist .= '<option value="'.$bcode.'">'.$bname.'</option>'; }
                $farm_flag = 1;
            }
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Contra Note</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_modify_contraaccount.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                    <table>
                                        <thead>
                                            <tr style="text-align:center;">
                                            <th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Dc No.</label></th>
												<th><label>Cr (-)<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Dr (+)<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Sector<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Remarks</label></th>
												<th><label>Dr Batch</label></th>
												<th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <tr>
                                                <td><input type="text" name="date" id="date" class="form-control datepicker" style="width:110px;"  value="<?php echo date('d.m.Y',strtotime($date)); ?>" /></td>
                                                <td><input type="text" name="dcno" id="dcno" class="form-control" value="<?php echo $dcno; ?>" style="width:70px;"></td>
                                                <td><select name="fcoa" id="fcoa" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>" <?php if($fcoa == $fcode){ echo "selected"; } ?>><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="tcoa" id="tcoa" class="form-control select2" style="width:160px;" onchange="fetch_batch_list();"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>" <?php if($tcoa == $fcode){ echo "selected"; } ?>><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><input type="text" name="amount" id="amount" class="form-control text-right" value="<?php echo $amount; ?>" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);"></td>
												<td><select name="sector" id="sector" class="form-control select2" style="width:160px;">  <?php foreach($sector_code as $fcode){ ?> <option value="<?php echo $sector_code[$fcode]; ?>" <?php if($warehouse == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><textarea name="remark" id="remark" class="form-control" style="margin:0;padding:0;height: 23px;"><?php echo $remarks; ?></textarea></td>
                                                <td><select name="to_batch" id="to_batch" class="form-control select2" style="width:160px;"><option value="select">select</option><?php echo $batch_slist; ?></select></td>
                                                <td style="visibility:hidden;"><input type="text" name="farm_flag" id="farm_flag" class="form-control" value="<?php echo $farm_flag; ?>" style="width:10px;"></td>
                                            </tr>
                                        </tbody>
                                    </table><br/>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>id Value<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="id_value" id="id_value" class="form-control" value="<?php echo $id; ?>">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
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
                window.location.href = 'broiler_display_contraaccount.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var b = c = d = e = 0; var icode = "";
                var k = true;
                b = document.getElementById("fcoa").value;
                c = document.getElementById("tcoa").value;
                d = document.getElementById("amount").value;
                e = document.getElementById("sector").value;
                to_batch = document.getElementById("to_batch").value;
                farm_flag = document.getElementById("farm_flag").value;
				if(b.match("select")){
                    alert("Please select From CoA");
                    document.getElementById("fcoa").focus();
                    k = false;
                }
                else if(c.match("select")){
                    alert("Please select To CoA");
                    document.getElementById("tcoa").focus();
                    k = false;
                }
                else if(d == 0 || d == "" || d.lenght == 0){
                    alert("Please Enter Amount");
                    document.getElementById("amount").focus();
                    k = false;
                }
                else if(e.match("select") || e == "" || e.lenght == 0){
                    alert("Please select Sector");
                    document.getElementById("sector").focus();
                    k = false;
                }
                else if(parseFloat(farm_flag) == 1 && to_batch == "select"){
                    alert("Please select Dr Batch");
                    document.getElementById("to_batch").focus();
                    k = false;
                }
                else {
                    k = true;
                }
				if(k === true){
					return true;
				}
				else if(k == false){
					document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
					return false;
				}
				else {
					document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
					return false;
				}
            }
            function fetch_batch_list(){
                var farms = document.getElementById("tcoa").value;
                removeAllOptions(document.getElementById("to_batch"));

                document.getElementById("farm_flag").value = 0;

                myselect1 = document.getElementById("to_batch");
                theOption1=document.createElement("OPTION");
                theText1=document.createTextNode("-select-");
                theOption1.value = "select";
                theOption1.appendChild(theText1);
                myselect1.appendChild(theOption1);

                if(farms != "select"){
                    var batch_list = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_batchlist.php?farm_code="+farms+"&row=0";
                    var asynchronous = true;
                    //window.open(url);
                    batch_list.open(method, url, asynchronous);
                    batch_list.send();
                    batch_list.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var batch_dt1 = this.responseText;
                            var batch_dt2 = batch_dt1.split("[@$%&]");
                            var batch_dt3 = batch_dt2[0];
                            var row = batch_dt2[1];
                            var count = batch_dt2[2];

                            if(parseFloat(count) > 0 && batch_dt3.length > 0){
                                var batch_dt4 = batch_dt3.split("@$&");
                                var batch_dt5 = []; var batch_name = batch_code = "";
                                for(var e = 0; e < batch_dt4.length;e++){
                                    batch_dt5 = []; batch_name = batch_code = "";
                                    batch_dt5 = batch_dt4[e].split("@");
                                    batch_code = batch_dt5[0]; batch_name = batch_dt5[1];
                                    myselect1 = document.getElementById("to_batch");
                                    theOption1=document.createElement("OPTION");
                                    theText1=document.createTextNode(batch_name);
                                    theOption1.value = batch_code;
                                    theOption1.appendChild(theText1);
                                    myselect1.appendChild(theOption1);
                                    document.getElementById("farm_flag").value = 1;
                                }
                            }
                            else{ }
                        }
                    }
                }				
            }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			setInterval(function(){ if(window.screen.availWidth <= 400){ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; } } else{ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; } } }, 1000);
        </script>
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