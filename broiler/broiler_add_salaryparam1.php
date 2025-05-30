<?php
//broiler_add_salaryparam1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['salaryparam1'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
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
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $add_access_flag){
            if($add_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $date = date("d.m.Y");

        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
 	
        $farms = array();
		$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' AND `gc_flag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $farms[$row['code']] = $row['farm_code'];$farms_batch[$row['farm_code']] = $row['description']; }
        $farm_list = implode("','", $farms);
	
        $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){  $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; $farm_code[$row['code']] = $row['farm_code']; }
      
        
        // Sector Code
        // $bsql = "SELECT * FROM `inv_sectors` WHERE  `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        // while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
 
        // Designation Code
        $bsql = "SELECT * FROM `broiler_designation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        while($brow = mysqli_fetch_assoc($bquery)){ $desg_code[$brow['code']] = $brow['code']; $desg_name[$brow['code']] = $brow['description']; }
 
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
            font-size: 13px;
        }
        /*::-webkit-scrollbar { width: 8px; height:8px; }
        .row_body2{
            width:100%;
            overflow-y: auto;
        }*/
        .table1{
            transform: scale(0.8);
            transform-origin: top left;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Salary Structure</h3></div>
                        </div>
                        <div class="pl-2 card-body">
                            <form action="broiler_save_salaryparam1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row row_body2">
                                    <table class="p-1 table1" style="width:auto;">
                                        <thead> 
                                            <tr>
                                                <th style="text-align:center;"><label>Sectors<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Designation<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Basic<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Hra</label></th>
                                                <th style="text-align:center;"><label>Medical<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Co. Allowances</label></th>
                                                <th style="text-align:center;"><label>Transport</label></th>
                                                <th style="visibility:hidden;"><label>Action</label></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <tr>
                                                <td><select name="sector[]" id="sector[0]" class="form-control select2" style="width:190px;" onchange="check_duplicate(this.id);"><option value="select">-select-</option><?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $sector_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><select name="desg[]" id="desg[0]" class="form-control select2" style="width:190px;" onchange="check_duplicate(this.id);"><option value="select">-select-</option><?php foreach($desg_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $desg_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="basic[]" id="basic[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="hra[]" id="hra[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="med[]" id="med[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="conv[]" id="conv[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="trans[]" id="trans[0]" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td id="action[0]" style="width:80px;"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                <td style="visibility:hidden;"><input type="text" name="dupflag[0]" id="dupflag[0]" class="form-control text-right" value="0" style="width:20px;" readonly /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><br/>
                                <div class="row" style="visibility:hidden;">
                                    <div class="form-group" style="width:30px;">
                                        <label>IN</label>
                                        <input type="text" name="incr" id="incr" class="form-control" value="0" style="padding:0;width:20px;" readonly />
                                    </div>
                                    <div class="form-group" style="width:30px;">
                                        <label>EB</label>
                                        <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="padding:0;width:20px;" readonly />
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
            </section>
        </div>
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
			function checkval(){
				update_ebtn_status(1);
                var l = true;
                var incr = document.getElementById("incr").value;
                var sector = desg = dupflag = ""; var basic = hra = med = conv = trans = 0;
                var bstk_cflag = '<?php echo $bstk_cflag; ?>'; if(bstk_cflag == ""){ bstk_cflag = 0; }

                for(var d = 0;d <= incr;d++){
                    if(l == true){
                        e = d + 1;
                        sector = document.getElementById("sector["+d+"]").value;
                        desg = document.getElementById("desg["+d+"]").value;
                        basic = document.getElementById("basic["+d+"]").value; if(basic == ""){ basic = 0; }
                        dupflag = document.getElementById("dupflag["+d+"]").value;
                        
                        if(sector == "" || sector == "select"){
                            alert("Please select From Sector in row: "+e);
                            document.getElementById("sector["+d+"]").focus();
                            l = false;
                        }
                        else if(desg == "" || desg == "select"){
                            alert("Please select From Designation in row: "+e);
                            document.getElementById("desg["+d+"]").focus();
                            l = false;
                        }
                        else if(parseInt(dupflag) == 1){
                            alert("This Row already exist.\n Kindly check in row: "+e);
                            document.getElementById("sector["+d+"]").focus();
                            l = false;
                        }
                        else if(parseFloat(basic) == 0){
                            alert("Please enter Basic in row: "+e);
                            document.getElementById("basic["+d+"]").focus();
                            l = false;
                        }
                        else{ }
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    update_ebtn_status(0);
                    return false;
                }
			}
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_salaryparam1.php?ccid='+ccid;
            }
			function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="sector[]" id="sector['+d+']" class="form-control select2" style="width:190px;" onchange="check_duplicate(this.id);"><option value="select">-select-</option><?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $sector_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><select name="desg[]" id="desg['+d+']" class="form-control select2" style="width:190px;" onchange="check_duplicate(this.id);"><option value="select">-select-</option><?php foreach($desg_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $desg_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="basic[]" id="basic['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="hra[]" id="hra['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="med[]" id="med['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="conv[]" id="conv['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td><input type="text" name="trans[]" id="trans['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="dupflag['+d+']" id="dupflag['+d+']" class="form-control text-right" value="0" style="width:20px;" /></td>';
                html += '</tr>';
                $('#tbody').append(html);
                $('.select2').select2();
                }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
           
            function update_ebtn_status(a){
                if(parseInt(a) == 1){
                    document.getElementById("ebtncount").value = "1";
                    document.getElementById("submit").style.visibility = "hidden";
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                }
            }
            function check_duplicate(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var sector = document.getElementById("sector["+d+"]").value;
				var desg = document.getElementById("desg["+d+"]").value;
				var type = "add";
				if(sector != "" && desg != ""){ 
					var oldqty = new XMLHttpRequest();
					var method = "GET"; 
					var url = "broiler_fetch_salaryparam1_duplicates.php?sector="+sector+"&desg="+desg+"&type="+type+"&row_count="+d;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_dt = this.responseText;
                            var dup_info = dup_dt.split("@");
                            var row = dup_info[1];
							if(parseInt(dup_info[0]) == 0){
								document.getElementById("dupflag["+row+"]"). value = 0;
							}
							else {
								alert("This Row already exist.\n Kindly change the Sector or Designation");
								document.getElementById("dupflag["+row+"]"). value = 1;
							}
						}
					}
				}
				else { }
			}
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
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