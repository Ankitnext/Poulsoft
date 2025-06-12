<?php
//broiler_add_vouchers.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['vouchers'];
date_default_timezone_set("Asia/Kolkata");
$uri = explode("/",$_SERVER['REQUEST_URI']); $href = $uri[1];
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['addaccess']);
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
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        //check and fetch date range
        global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        include "poulsoft_fetch_daterange_master.php";

		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
        
        $sql = "SELECT * FROM `inv_sectors` WHERE `description` LIKE '%head office%' AND `active` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sec_hcode = "";
		while($row = mysqli_fetch_assoc($query)){ $sec_hcode = $row['code']; }

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
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Vouchers</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_save_vouchers.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group">
                                            <label>Voucher Type<b style="color:red;">&nbsp;*</b></label>
                                            <select name="voucher_type" id="voucher_type" class="form-control select2" style="width:160px;">
                                                <option value="select">select</option>
                                                <option value="PayVoucher">Payment Voucher</option>
                                                <option value="RctVoucher">Receipt Voucher</option>
                                                <option value="JorVoucher">Journal Voucher</option>
                                            </select>
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
                                                <th style="width:30px;"></th>
                                               <!-- <th id="addrow"><a href="javascript:void(0);" id="addrow" onclick="create_row(document.getElementById('rcnt').value)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></th>-->
                                                <th style="width:30px;"></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <tr>
                                                <td><input type="text" name="date[]" id="date[0]" class="form-control range_picker" style="width:110px;"  value="<?php echo date('d.m.Y'); ?>" readonly /></td>
                                                <td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:70px;"></td>
                                                <td><select name="fcoa[]" id="fcoa[0]" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>"><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="tcoa[]" id="tcoa[0]" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>"><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><input type="text" name="amount[]" id="amount[0]" class="form-control" value="" onkeyup="validatenum(this.id);calculate_final_total_amount();" onchange="getamountinwords();validateamount(this.id);"></td>
												<td><input type="text" name="cheque_no[]" id="cheque_no[0]" class="form-control" style="width:90px;" onkeyup="validatename(this.id);"></td>
												<td><select name="sector[]" id="sector[0]" class="form-control select2" style="width:160px;">  <?php foreach($sector_code as $fcode){ ?> <option value="<?php echo $sector_code[$fcode]; ?>" <?php if($fcode == $sec_hcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><textarea name="remark[]" id="remark[0]" class="form-control" style="height: 23px;"></textarea></td>
                                                <!--<td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>-->
                                                <td id="addrow[0]"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(document.getElementById('rcnt').value)" class="form-control" style="width:15px; height:15px;" style="color:green;"><i class="fa fa-plus"></i></a></td>
                                                <td style="visibility:hidden;"><input type="text" name="gtamtinwords[]" id="gtamtinwords[0]" class="form-control" readonly /></td>
                                            </tr>
                                        </tbody>
                                    </table><br/>
                                    <div class="col-md-12" style="margin-bottom:3px;">
                                        <div class="row" style="margin-bottom:3px;">
                                            <div class="col-md-3 form-group">
                                                <label>Total Vouchers</label>
                                                <input type="text" name="tno" id="tno" class="form-control" value="1"style="width:auto;" readonly>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>Voucher Amount</label>
                                                <input type="text" name="final_total" id="final_total" class="form-control" style="width:auto;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="rcnt" id="rcnt" class="form-control" value="0">
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
            //Date Range selection
            var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
            const del_array_list = [];
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_vouchers.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incrs = document.getElementById("incr").value;
                var a = b = c = d = e = 0; var icode = "";
                var k = true;
				a = document.getElementById("voucher_type").value;
				if(a.match("select")){
					alert("Please select Voucher Type");
                    document.getElementById("voucher_type").focus();
					k = false;
				}
                else{
                    for (var j=0;j<=incrs;j++){
                        b = document.getElementById("fcoa["+j+"]").value;
                        c = document.getElementById("tcoa["+j+"]").value;
                        d = document.getElementById("amount["+j+"]").value;
                        e = document.getElementById("sector["+j+"]").value;
                        var l = j; l++;
                        if(b.match("select")){
                            alert("Please select From CoA in row : "+l);
                            document.getElementById("fcoa["+j+"]").focus();
                            k = false;
                        }
                        else if(c.match("select")){
                            alert("Please select To CoA in row : "+l);
                            document.getElementById("tcoa["+j+"]").focus();
                            k = false;
                        }
                        else if(parseFloat(d) == 0){
                            alert("Please Enter Amount in row : "+l);
                            document.getElementById("amount["+j+"]").focus();
                            k = false;
                        }
                        else if(e.match("select") || e == "" || e.lenght == 0){
                            alert("Please select Sector in row : "+l);
                            document.getElementById("sector["+j+"]").focus();
                            k = false;
                        }
                        else {
                            k = true;
                        }
                    }
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
            function create_row(a){
                /*var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";*/
                var d = a;
                d++; var html = '';
               // document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control range_picker" style="width:110px;"  value="<?php echo date('d.m.Y'); ?>" readonly /></td>';
                html += '<td><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:70px;"></td>';
                html += '<td><select name="fcoa[]" id="fcoa['+d+']" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>"><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>';
                html += '<td><select name="tcoa[]" id="tcoa['+d+']" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>"><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>';
                html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control" value="" onkeyup="validatenum(this.id);calculate_final_total_amount();" onchange="getamountinwords();validateamount(this.id);"></td>';
                html += '<td><input type="text" name="cheque_no[]" id="cheque_no['+d+']" class="form-control" style="width:90px;" onkeyup="validatename(this.id);"></td>';
                html += '<td><select name="sector[]" id="sector['+d+']" class="form-control select2" style="width:160px;">  <?php foreach($sector_code as $fcode){ ?> <option value="<?php echo $sector_code[$fcode]; ?>" <?php if($fcode == $sec_hcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option> <?php } ?> </select></td>';
                html += '<td><textarea name="remark[]" id="remark['+d+']" class="form-control" style="height: 23px;"></textarea></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(rcnt.value)"><i class="fa fa-plus"></i></a></td>';                
                html += '<td style="visibility:hidden;"><input type="text" name="gtamtinwords[]" id="gtamtinwords['+d+']" class="form-control" readonly /></td>';
                html += '</tr>';
                $('#row_body').append(html);
                document.getElementById("rcnt").value = d;
                const incr =  parseFloat(document.getElementById("incr").value); 
                document.getElementById("incr").value =  parseFloat(incr)  + 1;
                $('.select2').select2();
                $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
                calculate_final_total_amount();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                del_array_list.push(d);
                d--;
                const incr =  parseFloat(document.getElementById("incr").value); 
                document.getElementById("incr").value =  parseFloat(incr) - 1;
                /*document.getElementById("action["+d+"]").style.visibility = "visible";*/
                calculate_final_total_amount();
            }
            function calculate_final_total_amount(){
                var incr = document.getElementById("incr").value; 
                var rcnt = document.getElementById("rcnt").value; 
                var i = amount = final_amount = 0;               
                for(i = 0;i <= rcnt;i++){
                    
                   const f = del_array_list.find(ele => ele == i);
                   const fi = del_array_list.findIndex(ele => ele == i);
                   
                    if( i != f )//if (fi > -1)
                    {
                    amount = document.getElementById("amount["+i+"]").value; if(amount == ""){ amount = 0; }
                    } else {
                        amount = 0;
                    }
                    final_amount = parseFloat(final_amount) + parseFloat(amount); 
                }
                document.getElementById("tno").value = parseFloat(incr) + 1;
                document.getElementById("final_total").value = final_amount.toFixed(2);
            }
			function getamountinwords() {
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("amount["+a+"]").value;
				var c = convertNumberToWords(b);
				document.getElementById("gtamtinwords["+a+"]").value = c;
			}
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 ,]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 ,]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
			setInterval(function(){ if(window.screen.availWidth <= 400){ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "inline"; } } else{ const collection = document.getElementsByClassName("labelrow"); for (let i = 0; i < collection.length; i++) { collection[i].style.display = "none"; } } }, 1000);
        </script>
        <?php include "header_foot.php"; ?>
        <script>
            //Date Range selection
            $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
        </script>
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