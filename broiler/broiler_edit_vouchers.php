<?php
//broiler_edit_vouchers.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['vouchers'];
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
        foreach($elink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){

        //check and fetch date range
        $file_aurl = str_replace("_edit_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))); $e_code = $_SESSION['userid'];
        $sql = "SELECT * FROM `dataentry_daterange_master` WHERE `file_name` LIKE '$file_aurl' AND `user_code` LIKE '$e_code' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $r_cnt = mysqli_num_rows($query); $s_days = $e_days = 0; $rdate = date("d.m.Y");
        if($r_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $s_days = $row['min_days']; $e_days = $row['max_days']; } }

		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

		$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

        //$sql = "SELECT *  FROM `extra_access` WHERE `field_name` LIKE 'Feed Sale' AND `field_function` LIKE 'Stock Check'"; $query = mysqli_query($conn,$sql); $stockcheck_flag = 0; $sccount = mysqli_num_rows($query);
        //if($sccount > 0){ while($row = mysqli_fetch_assoc($query)){ $stockcheck_flag = $row['flag']; } } else{ $stockcheck_flag = 0; } if($stockcheck_flag == "" || $stockcheck_flag == 0){ $stockcheck_flag = 0; }
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
        $sql = "SELECT * FROM `account_vouchers` WHERE `trnum` = '$id'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $voucher_type = $row['type'];
            $date = $row['date'];
            $dcno = $row['dcno'];
            $fcoa = $row['fcoa'];
            $tcoa = $row['tcoa'];
            $amount = $row['amount'];
            $cheque_no = $row['cheque_no'];
            $amtinwords = $row['amtinwords'];
            $warehouse = $row['warehouse'];
            $remarks = $row['remarks'];
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Vouchers</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_modify_vouchers.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group col-md-2" >
                                            <label>Transaction No.<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $id; ?>" readonly >
                                        </div><br/>
                                    <table>
                                        <thead>
                                            <tr style="text-align:center;">
                                            <th><label>Date<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Dc No.</label></th>
												<th><label>From CoA<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>To CoA<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Cheque No</label></th>
												<th><label>Sector<b style="color:red;">&nbsp;*</b></label></th>
												<th><label>Remarks</label></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <tr>
                                                <td><input type="text" name="date" id="date" class="form-control range_picker" style="width:110px;"  value="<?php echo date('d.m.Y',strtotime($date)); ?>" readonly /></td>
                                                <td><input type="text" name="dcno" id="dcno" class="form-control" value="<?php echo $dcno; ?>" style="width:70px;"></td>
                                                <td><select name="fcoa" id="fcoa" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>" <?php if($fcoa == $fcode){ echo "selected"; } ?>><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="tcoa" id="tcoa" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>" <?php if($tcoa == $fcode){ echo "selected"; } ?>><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><input type="text" name="amount" id="amount" class="form-control" value="<?php echo $amount; ?>" onkeyup="validatenum(this.id);" onchange="getamountinwords();validateamount(this.id);"></td>
												<td><input type="text" name="cheque_no" id="cheque_no" class="form-control" value="<?php echo $cheque_no; ?>" style="width:90px;" onkeyup="validatename(this.id);"></td>
												<td><select name="sector" id="sector" class="form-control select2" style="width:160px;">  <?php foreach($sector_code as $fcode){ ?> <option value="<?php echo $sector_code[$fcode]; ?>" <?php if($warehouse == $fcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><textarea name="remark" id="remark" class="form-control" style="margin:0;padding:0;height: 23px;"><?php echo $remarks; ?></textarea></td>
                                                <td style="visibility:hidden;"><input type="text" name="gtamtinwords" id="gtamtinwords" class="form-control" readonly /></td>
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
                window.location.href = 'broiler_display_vouchers.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var a = b = c = d = e = 0; var icode = "";
                var k = true;
                b = document.getElementById("fcoa").value;
                c = document.getElementById("tcoa").value;
                d = document.getElementById("amount").value; if(d == ""){ d = 0; }
                e = document.getElementById("sector").value;
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
                else if(parseFloat(d) == 0){
                    alert("Please Enter Amount");
                    document.getElementById("amount").focus();
                    k = false;
                }
                else if(e.match("select") || e == "" || e.lenght == 0){
                    alert("Please select Sector");
                    document.getElementById("sector").focus();
                    k = false;
                }
                else { }

				if(k == true){
					return true;
				}
				else {
					document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
					return false;
				}
            }
			function getamountinwords() {
				var a = document.getElementById("amount").value;
				var b = convertNumberToWords(a);
				document.getElementById("gtamtinwords").value = b;
			}
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 ,]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 ,]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			setInterval(function(){ if(window.screen.availWidth <= 400){ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; } } else{ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; } } }, 1000);
        </script>
        <script>
            //Date Range selection
            var s_date = '<?php echo date('d.m.Y', strtotime('-'.$s_days.' days', strtotime($rdate))); ?>';
            var e_date = '<?php echo date('d.m.Y', strtotime('+'.$e_days.' days', strtotime($rdate))); ?>';
            $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
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