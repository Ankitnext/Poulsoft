<?php
//broiler_edit_vouchers3.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['vouchers3'];
date_default_timezone_set("Asia/Kolkata");
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
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
        $acc_code =  $acc_name = $sector_code = $sector_name =  $cash_code = $cash_name = $bank_code = $bank_name = array(); $cash_mode = $bank_mode = "";
        $sql = "SELECT * FROM `acc_coa` WHERE `vouexp_flag` = '1' AND `active` = '1' AND `dflag` = '0' AND `visible_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $acc_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['description']; }
        
        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1'".$branch_access_filter2."".$line_access_filter2."".$farm_access_filter1." AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `acc_modes` WHERE `description` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Cash"){ $cash_mode = $row['code']; } else if($row['description'] == "Bank"){ $bank_mode = $row['code']; } }

        $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $cash_code[$row['code']] = $row['code']; $cash_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Bank') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $bank_code[$row['code']] = $row['code']; $bank_name[$row['code']] = $row['description']; }

?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: auto;
            /*zoom: 0.8;*/
        }
        .form-control{
            font-size: 13px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $ids = $_GET['trnum'];
        $sql = "SELECT * FROM `broiler_voucher_notes` WHERE `trnum` = '$ids' AND `dflag` = '0' ORDER BY `id` ASC";
        $query = mysqli_query($conn,$sql); $c = 0; $pflag = array();
        while($row = mysqli_fetch_assoc($query)){
            $date = $row['date'];
            $billno = $row['dcno'];
            
            $warehouse = $row['warehouse'];
            $vehicle_code = $row['vehicle_code'];
            $pflag = $row['pflag'];
            if((int)$pflag == 1){
                $group_code = $row['group_code'];
                $coa_code = $row['vcode'];
            }
            else{
                $vcode[$c] = $row['vcode'];
                $amount[$c] = round($row['amount'],5);
                $remarks[$c] = $row['remarks'];
                $c++;
            }
        }
        $c = $c - 1;
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Vouchers</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_vouchers3.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group" style="width:110px;">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date('d.m.Y',strtotime($date)); ?>" style="width:100px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:90px;">
                                            <label>Bill No</label>
                                            <input type="text" name="billno" id="billno" class="form-control" value="<?php echo $billno; ?>" style="width:80px;" onkeyup="validatename(this.id);" />
                                        </div>
                                        <div class="form-group" style="width:190px;">
                                            <label>Farm/Sector<b style="color:red;">&nbsp;*</b></label>
                                            <select name="warehouse" id="warehouse" class="form-control select2" style="width:180px;">
                                                <option value="select">select</option>
                                                <?php foreach($sector_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($warehouse == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option><?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <?php if($cash_mode != ""){ ?>
                                        <div class="form-group" style="width:40px;">
                                            <label for="group_code1">Cash</label>
                                            <input type="radio" name="group_code" id="group_code1" class="form-control" value="<?php echo $cash_mode; ?>" style="transform:scale(0.5);" onchange="update_accounts(this.id);" <?php if($group_code == $cash_mode){ echo "checked"; } ?> />
                                        </div>
                                        <?php } ?>
                                        <?php if($bank_mode != ""){ ?>
                                        <div class="form-group" style="width:40px;">
                                            <label for="group_code2">Bank</label>
                                            <input type="radio" name="group_code" id="group_code2" class="form-control" value="<?php echo $bank_mode; ?>" style="transform:scale(0.5);" onchange="update_accounts(this.id);" <?php if($group_code == $bank_mode){ echo "checked"; } ?> />
                                        </div>
                                        <?php } ?>
                                        <div class="form-group"  style="width:190px;">
                                            <label>CoA Account<b style="color:red;">&nbsp;*</b></label>
                                            <select name="coa_code" id="coa_code" class="form-control select2" style="width:180px;" onchange="update_firstrow();">
                                                <option value="select">select</option>
                                                <?php if($group_code == $cash_mode){ foreach($cash_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($coa_code == $scode){ echo "selected"; } ?>><?php echo $cash_name[$scode]; ?></option><?php } }
                                                else if($group_code == $bank_mode){ foreach($bank_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($coa_code == $scode){ echo "selected"; } ?>><?php echo $bank_name[$scode]; ?></option><?php } } ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width:90px;">
                                            <label>Vehicle</label>
                                            <input type="text" name="vehicle_code" id="vehicle_code" class="form-control" value="<?php echo $vehicle_code; ?>" style="width:90px;" onkeyup="validatename(this.id);" />
                                        </div>
                                    </div><br/>
                                    <table>
                                        <thead>
                                            <tr style="text-align:center;">
                                                <th>Expense Type</th>
                                                <th>Amount</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <?php $c = 0; foreach($acc_code as $acode){ ?>
                                            <tr>
                                                <td><select name="vcode[]" id="vcode[<?php echo $c; ?>]" class="form-control select2" style="width:180px;"><option value="<?php echo $vcode[$c]; ?>"><?php echo $acc_name[$vcode[$c]]; ?></option></select></td>
                                                <td><input type="text" name="amount[]" id="amount[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $amount[$c]; ?>" style="width:90px;" onkeyup="validatenum(this.id);calculate_total_amt();" onchange="validateamount(this.id);" /></td>
                                                <th><textarea name="remarks[]" id="remarks[<?php echo $c; ?>]" class="form-control" style="width:150px;height:28px;"><?php echo $remarks[$c]; ?></textarea></th>
                                            </tr>
                                            <?php $c++; } $c = $c - 1; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td style="text-align:right;">Total</td>
                                                <td style="visibility:visible;"><input type="text" name="tamount" id="tamount" class="form-control text-right" style="width:90px;" readonly /></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table><br/>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:30px;">
                                            <label>ID</label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>IN</label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $c; ?>" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>EC</label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
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
                window.location.href = 'broiler_display_vouchers3.php?ccid'+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden"; 
                var l = true;
                var date = document.getElementById("date").value;
                var warehouse = document.getElementById("warehouse").value;
                var coa_code = document.getElementById("coa_code").value;
                var tamount = document.getElementById("tamount").value; if(tamount == ""){ tamount = 0; }

                if(date == ""){
                    alert("Please select Date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else if(warehouse.match("select")){
                    alert("Please select Farm/Sector");
                    document.getElementById("warehouse").focus();
                    l = false;
                }
                else if(coa_code == "select"){
                    alert("Please select CoA Account");
                    document.getElementById("coa_code").focus();
                    l = false;
                }
                else if(parseFloat(tamount) == 0){
                    alert("Please enter amount in any one expense type");
                    l = false;
                }
                else{
                    var incr = document.getElementById("incr").value;
                    var vcode = ""; var amount = 0;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            vcode = document.getElementById("vcode["+d+"]").value;

                            if(vcode == "select" || vcode == ""){
                                alert("Please select Description");
                                document.getElementById("vcode["+d+"]").focus();
                                l = false;
                            }
                            else{ }
                        }
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function update_accounts(a){
                var coa_mode = document.getElementById(a).value;
                removeAllOptions(document.getElementById("coa_code"));

                if(coa_mode != ""){
					var prices = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_cashrbankdetails.php?coa_mode="+coa_mode+"&type=add";
                    //window.open(url);
					var asynchronous = true;
					prices.open(method, url, asynchronous);
					prices.send();
					prices.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var coa_code = this.responseText;
							$('#coa_code').append(coa_code);
						}
					}
				}
            }
            function calculate_total_amt(){
                var incr = document.getElementById("incr").value;
                var tamount = 0;
                for(var d= 0;d <= incr;d++){
                    var amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                    tamount =parseFloat(tamount) + parseFloat(amount);
                }
                
                document.getElementById("tamount").value = parseFloat(tamount);
            } calculate_total_amt();
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validate_count(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
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