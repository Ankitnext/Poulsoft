<?php
//broiler_add_contraaccount2.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['contraaccount2'];
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
		$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'  ".$sector_access_filter1." ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
        
        $sql = "SELECT * FROM `inv_sectors` WHERE `description` LIKE '%head office%' AND `active` = '1' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sec_hcode = "";
		while($row = mysqli_fetch_assoc($query)){ $sec_hcode = $row['code']; }

		$sql = "SELECT * FROM `acc_coa` WHERE (`ctype` LIKE '%CASH%' OR `ctype` LIKE '%BANK%') AND `active` = '1' AND `dflag` = '0'AND `visible_flag` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

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
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Contra Note</h3></div>
                        </div>
                        <div class="m-0 p-2 card-body">
                            <div class="col-md-18">
                                <form action="broiler_save_contraaccount2.php" method="post" role="form" onSubmit="return checkval()">
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
                                                <th style="width:60px;"></th>
												<th><label>Dr Batch</label></th>
												<th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="row_body">
                                            <tr>
                                                <td><input type="text" name="date[]" id="date[0]" class="form-control datepicker" style="width:110px;"  value="<?php echo date('d.m.Y'); ?>" /></td>
                                                <td><input type="text" name="dcno[]" id="dcno[0]" class="form-control" style="width:70px;"></td>
                                                <td><select name="fcoa[]" id="fcoa[0]" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>"><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><select name="tcoa[]" id="tcoa[0]" class="form-control select2" style="width:160px;" onchange="fetch_batch_list(this.id);"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>"><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><input type="text" name="amount[]" id="amount[0]" class="form-control text-right" onkeyup="validatenum(this.id);calculate_final_total_amount();" onchange="validateamount(this.id);"></td>
												<td><select name="sector[]" id="sector[0]" class="form-control select2" style="width:160px;">  <?php foreach($sector_code as $fcode){ ?> <option value="<?php echo $sector_code[$fcode]; ?>" <?php if($fcode == $sec_hcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option> <?php } ?> </select></td>
												<td><textarea name="remark[]" id="remark[0]" class="form-control" style="height: 23px;"></textarea></td>
                                                <td id="action[0]"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                <td><select name="to_batch[]" id="to_batch[0]" class="form-control select2" style="width:160px;"><option value="select">select</option></select></td>
                                                <td style="visibility:hidden;"><input type="text" name="farm_flag[]" id="farm_flag[0]" class="form-control" style="width:10px;"></td>
                                            </tr>
                                        </tbody>
                                    </table><br/>
                                    <div class="col-md-12" style="margin-bottom:3px;">
                                        <div class="row" style="margin-bottom:3px;">
                                            <div class="col-md-3 form-group">
                                                <label>Total Amount</label>
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
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-purple">Submit</button>&ensp;
                                            <button type="button" name="cancel" id="cancel" class="btn btn-sm bg-danger" onClick="return_back()">Cancel</button>
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
                window.location.href = 'broiler_display_contraaccount2.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incrs = document.getElementById("incr").value;
                var a = b = c = d = e = 0; var icode = "";
                var k = true;
                    for (var j=0;j<=incrs;j++){
                        b = document.getElementById("fcoa["+j+"]").value;
                        c = document.getElementById("tcoa["+j+"]").value;
                        to_batch = document.getElementById("to_batch["+j+"]").value;
                        farm_flag = document.getElementById("farm_flag["+j+"]").value;
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
                        else if (b == c) {
                            alert("From CoA and To CoA cannot be the same in row : " + l);
                            document.getElementById("tcoa[" + j + "]").value = "select"; // Reset To CoA dropdown
                            document.getElementById("tcoa[" + j + "]").focus();
                            k = false;
                        } 
                        else if(d == 0 || d == "" || d.lenght == 0){
                            alert("Please Enter Amount in row : "+l);
                            document.getElementById("amount["+j+"]").focus();
                            k = false;
                        }
                        else if(e.match("select") || e == "" || e.lenght == 0){
                            alert("Please select Sector in row : "+l);
                            document.getElementById("sector["+j+"]").focus();
                            k = false;
                        }
                        else if(parseFloat(farm_flag) == 1 && to_batch == "select"){
                            alert("Please select Dr Batch in row : "+l);
                            document.getElementById("to_batch["+j+"]").focus();
                            k = false;
                        }
                        else {
                            k = true;
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
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><input type="text" name="date[]" id="date['+d+']" class="form-control datepicker" style="width:110px;"  value="<?php echo date('d.m.Y'); ?>" /></td>';
                html += '<td><input type="text" name="dcno[]" id="dcno['+d+']" class="form-control" style="width:70px;"></td>';
                html += '<td><select name="fcoa[]" id="fcoa['+d+']" class="form-control select2" style="width:160px;"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>"><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>';
                html += '<td><select name="tcoa[]" id="tcoa['+d+']" class="form-control select2" style="width:160px;" onchange="fetch_batch_list(this.id);"> <option value="select">select</option> <?php foreach($coa_code as $fcode){ ?> <option value="<?php echo $coa_code[$fcode]; ?>"><?php echo $coa_name[$fcode]; ?></option> <?php } ?> </select></td>';
                html += '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" onkeyup="validatenum(this.id);calculate_final_total_amount();" onchange="validateamount(this.id);"></td>';
                html += '<td><select name="sector[]" id="sector['+d+']" class="form-control select2" style="width:160px;">  <?php foreach($sector_code as $fcode){ ?> <option value="<?php echo $sector_code[$fcode]; ?>" <?php if($fcode == $sec_hcode){ echo "selected"; } ?>><?php echo $sector_name[$fcode]; ?></option> <?php } ?> </select></td>';
                html += '<td><textarea name="remark[]" id="remark['+d+']" class="form-control" style="height: 23px;"></textarea></td>';
                html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td><select name="to_batch[]" id="to_batch['+d+']" class="form-control select2" style="width:160px;"><option value="select">select</option></select></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="farm_flag[]" id="farm_flag['+d+']" class="form-control" style="width:10px;"></td>';
                html += '</tr>';
                $('#row_body').append(html);
                $('.select2').select2();
                $( ".datepicker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
                calculate_final_total_amount();
            }
            function calculate_final_total_amount(){
                var incr = document.getElementById("incr").value; var i = amount = final_amount = 0;
                for(i = 0;i <= incr;i++){
                    amount = document.getElementById("amount["+i+"]").value;
                    final_amount = parseFloat(final_amount) + parseFloat(amount);
                }
                document.getElementById("final_total").value = final_amount.toFixed(2);
            }
            function fetch_batch_list(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var farms = document.getElementById("tcoa["+d+"]").value;
                removeAllOptions(document.getElementById("to_batch["+d+"]"));

                document.getElementById("farm_flag["+d+"]").value = 0;

                myselect1 = document.getElementById("to_batch["+d+"]");
                theOption1=document.createElement("OPTION");
                theText1=document.createTextNode("-select-");
                theOption1.value = "select";
                theOption1.appendChild(theText1);
                myselect1.appendChild(theOption1);

                if(farms != "select"){
                    var batch_list = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_batchlist.php?farm_code="+farms+"&row="+d;
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
                                    myselect1 = document.getElementById("to_batch["+row+"]");
                                    theOption1=document.createElement("OPTION");
                                    theText1=document.createTextNode(batch_name);
                                    theOption1.value = batch_code;
                                    theOption1.appendChild(theText1);
                                    myselect1.appendChild(theOption1);
                                    document.getElementById("farm_flag["+d+"]").value = 1;
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