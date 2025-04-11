<?php
//broiler_add_payment.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['payment'];
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
        $file_aurl = str_replace("_edit_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))); $e_code = $_SESSION['userid'];
        $sql = "SELECT * FROM `dataentry_daterange_master` WHERE `file_name` LIKE '$file_aurl' AND `user_code` LIKE '$e_code' AND `active` = '1' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql); $r_cnt = mysqli_num_rows($query); $s_days = $e_days = 0; $rdate = date("d.m.Y");
        if($r_cnt > 0){ while($row = mysqli_fetch_assoc($query)){ $s_days = $row['min_days']; $e_days = $row['max_days']; } }

        $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$fpcode[$row['code']] = $row['code'];
			$fpname[$row['code']] = $row['name'];
		}
        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'  ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$wcode[$row['code']] = $row['code'];
			$wdesc[$row['code']] = $row['description'];
		}
        
        $sql = "SELECT * FROM `inv_sectors` WHERE `description` LIKE '%head office%' AND `active` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sec_hcode = "";
		while($row = mysqli_fetch_assoc($query)){ $sec_hcode = $row['code']; }

		$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$acode[$row['code']] = $row['code'];
			$adesc[$row['code']] = $row['description'];
		}
		$sql = "SELECT * FROM `acc_coa` WHERE `ctype` LIKE 'Cash' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$cash_code[$row['code']] = $row['code'];
			$cash_name[$row['code']] = $row['description'];
		}
        $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $wcode[$row['code']] = $row['code']; $wdesc[$row['code']] = $row['description']; }
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
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Payment</h3></div>
                        </div>
                        <div class="p-0 pt-5 card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_payment.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="form-group" style="width:110px;">
                                            <label>Date<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="date" id="date" class="form-control range_picker" style="width:100px;" value="<?php echo date('d.m.Y'); ?>" />
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Location<b style="color:red;">&nbsp;*</b></label>
							                <select name="sector" id="sector" class="form-control select2" style="width:160px;" ><option value="select">select</option><?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group" style="width:170px;">
                                            <label>Supplier</label>
							                <select name="pname[]" id="pname[0]" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($fpcode as $fcode){ ?> <option value="<?php echo $fpcode[$fcode]; ?>"><?php echo $fpname[$fcode]; ?></option> <?php } ?> </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Mode<b style="color:red;">&nbsp;*</b></label>
							                <select name="mode[]" id="mode[0]" class="form-control select2" style="width:160px;" onchange="updatecode(this.id)"> <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>" ><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select>
                                        </div>
                                        <div class="form-group" style="width:170px;">
                                            <label>Code<b style="color:red;">&nbsp;*</b></label>
							                <select name="code[]" id="code[0]" class="form-control select2" style="width:160px;"> <option value="select">select</option></select>
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>Amount<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="amount[]" id="amount[0]" class="form-control" style="width:100px;" onkeyup="validatenum(this.id);getamountinwords();" onchange="validateamount(this.id);">
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>Bank Charges</label>
							                <input type="text" name="bank_crg1[]" id="bank_crg1[0]" class="form-control" style="width:100px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);">
                                        </div>
                                        <div class="form-group" style="width:110px;">
                                            <label>Reference No</label>
							                <input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:100px;" />
                                        </div>
                                        <div class="form-group" style="width:130px;">
                                            <label>Remarks</label>
							                <textarea name="remark[]" id="remark[0]" class="form-control" style="width:120px;height:23px;"></textarea>
                                        </div>
                                        <div class="form-group" id="action[0]" style="padding-top: 12px;"><br/>
                                            <a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>
                                        </div>
                                        <div class="form-group" style="width:60px;visibility:hidden">
                                            <label>Reference No</label>
							                <input type="text" name="gtamtinwords[]" id="gtamtinwords[0]" class="form-control" style="width:50px;" />
                                        </div>
                                    </div>
                                    <div class="p-0 col-md-12" id="row_body">

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0">
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
                window.location.href = 'broiler_display_payment.php?ccid='+ccid;
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<div class="row" id="row_no['+d+']">';
                //html += '<div class="form-group" style="width:110px;"><label class="labelrow" style="display:none;">Date<b style="color:red;">&nbsp;*</b></label><input type="text" name="date[]" id="date['+c+']" class="form-control datepicker" style="width:100px;" value="<?php //echo date("d.m.Y"); ?>" onmouseover="displaycalendor()" ></div>';
                html += '<div class="form-group" style="width:170px;"><label class="labelrow" style="display:none;">Supplier</label><select name="pname[]" id="pname['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option> <?php foreach($fpcode as $fcode){ ?> <option value="<?php echo $fpcode[$fcode]; ?>"><?php echo $fpname[$fcode]; ?></option> <?php } ?> </select></div>';
                html += '<div class="form-group" style="width:170px;"><label class="labelrow" style="display:none;">Mode<b style="color:red;">&nbsp;*</b></label><select name="mode[]" id="mode['+d+']" class="form-control select2" style="width:160px;" onchange="updatecode(this.id)"> <option value="select">select</option> <?php foreach($acode as $fcode){ ?> <option value="<?php echo $acode[$fcode]; ?>"><?php echo $adesc[$fcode]; ?></option> <?php } ?> </select></div>';
                html += '<div class="form-group" style="width:170px;"><label class="labelrow" style="display:none;">Code<b style="color:red;">&nbsp;*</b></label><select name="code[]" id="code['+d+']" class="form-control select2" style="width:160px;"> <option value="select">select</option></select></div>';
                html += '<div class="form-group" style="width:110px;"><label class="labelrow" style="display:none;">Amount</label><input type="text" name="amount[]" id="amount['+d+']" class="form-control" style="width:100px;" onkeyup="validatenum(this.id);getamountinwords();" onchange="validateamount(this.id);"></div>';
                html += '<div class="form-group" style="width:110px;"><label class="labelrow" style="display:none;">Bank Charges</label><input type="text" name="bank_crg1[]" id="bank_crg1['+d+']" class="form-control" style="width:100px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);"></div>';
                html += '<div class="form-group" style="width:110px;"><label class="labelrow" style="display:none;">Reference No<b style="color:red;">&nbsp;*</b></label><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:100px;"></div>';
                //html += '<div class="form-group" style="width:170px;"><label class="labelrow" style="display:none;">Sector<b style="color:red;">&nbsp;*</b></label><select name="sector[]" id="sector['+c+']" class="form-control select2" style="width:160px;"> <?php foreach($wcode as $fcode){ ?> <option value="<?php echo $wcode[$fcode]; ?>"><?php echo $wdesc[$fcode]; ?></option> <?php } ?> </select></div>';
                html += '<div class="form-group" style="width:130px;"><label class="labelrow" style="display:none;">Remarks</label><textarea name="remark[]" id="remark['+d+']" class="form-control" style="width:120px;height:23px;"></textarea></div>';
                html += '<div class="form-group" id="action['+d+']" style="padding-top: 5px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></div>';
                html += '<div class="form-group" style="width:60px;visibility:hidden;"><label class="labelrow" style="display:none;">G-total<b style="color:red;">&nbsp;*</b></label><input type="text" name="gtamtinwords[]" id="gtamtinwords['+d+']" class="form-control" style="width:50px;"></div>';
                html += '</div>';
                html += '<hr class="labelrow" style="display:none;" />';
                $('#row_body').append(html); $('.select2').select2();
                //$( ".datepicker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
				var a = document.getElementById("incr").value;
				var k = true;
                var g = document.getElementById("sector").value;
                if(g.match("select") || g == "" || g.lenght == 0){
					alert("Please select sector in row : "+l);
                    document.getElementById("sector").focus();
					k = false;
				}
                else{
                    for (var j=0;j<=a;j++){
                        if(k == true){
                            var b = document.getElementById("pname["+j+"]").value;
                            var c = document.getElementById("mode["+j+"]").value;
                            var d = document.getElementById("code["+j+"]").value;
                            var e = document.getElementById("amount["+j+"]").value;
                            var l = j; l++;
                            if(b.match("select")){
                                alert("Please select supplier name in row : "+l);
                                document.getElementById("pname["+j+"]").focus();
                                k = false;
                            }
                            else if(c.match("select")){
                                alert("Please select mode of payment in row : "+l);
                                document.getElementById("mode["+j+"]").focus();
                                k = false;
                            }
                            else if(d.match("select")){
                                alert("Please select Paying method in row : "+l);
                                document.getElementById("code["+j+"]").focus();
                                k = false;
                            }
                            else if(e == 0 || e == "" || e.lenght == 0){
                                alert("Please enter amount in row : "+l);
                                document.getElementById("amount["+j+"]").focus();
                                k = false;
                            }
                            else {
                                k = true;
                            }
                        }
                    }
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
				var a = document.getElementById("incrs").value;
				var b = document.getElementById("amount["+a+"]").value;
				var c = convertNumberToWords(b);
				document.getElementById("gtamtinwords["+a+"]").value = c;
			}
			function updatecode(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var b = document.getElementById("mode["+d+"]").value;
				removeAllOptions(document.getElementById("code["+d+"]"));
				
				myselect = document.getElementById("code["+d+"]"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("select"); theOption1.value = "select"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
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
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
            setInterval(function(){
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
		</script>
        <script>
            //Date Range selection
            var s_date = '<?php echo date('d.m.Y', strtotime('-'.$s_days.' days', strtotime($rdate))); ?>';
            var e_date = '<?php echo date('d.m.Y', strtotime('+'.$e_days.' days', strtotime($rdate))); ?>';
            $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
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