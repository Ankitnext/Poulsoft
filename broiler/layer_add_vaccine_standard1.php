<?php
//layer_add_vaccine_standard1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['vaccine_standard1'];
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
        $sql = "SELECT * FROM `layer_breed_details` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $bbreed_code = $bbreed_name = array();
        while($row = mysqli_fetch_assoc($query)){ $bbreed_code[$row['code']] = $row['code']; $bbreed_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `item_category` WHERE (`description` LIKE '%medicine%' OR `description` LIKE '%vaccine%') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $icat_alist = array();
        while($row = mysqli_fetch_assoc($query)){ $icat_alist[$row['code']] = $row['code']; }

        $icat_list = implode("','", $icat_alist);
        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
        while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

   
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    <style>
        body{
            overflow: hidden;
        }
        .form-control{
            font-size: 13px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Vaccine Schedule</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="layer_save_vaccine_standard1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <table class="table1" style="width:auto;">
                                            <thead>
                                                <tr>
                                                    <th><label for="breed_code">Breed<b style="color:red;">&nbsp;*</b></label></th>
                                                    <td><select name="breed_code" id="breed_code" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($bbreed_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $bbreed_name[$ucode]; ?></option><?php } ?></select></td>
                                                </tr>
                                                <tr>
                                                    <th style="text-align:center;"><label>Age<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Vaccine Name<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>+/-</label></th>
                                                    <th style="visibility:hidden;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">
                                                <tr>
                                                    <td><input type="text" name="age[]" id="age[0]" class="form-control text-right" style="width:110px;" onkeyup="validate_count(this.id);" onchange="validate_count(this.id);check_duplicate(this.id);" /></td>
                                                    <td><select name="medvac_code[]" id="medvac_code[0]" class="form-control select2" style="width:190px;" onchange="check_duplicate(this.id);"><option value="select">-select-</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $item_name[$ucode]; ?></option><?php } ?></select></td>
                                                    <td id="action[0]" style="width:80px;"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                    <td style="visibility:hidden;"><input type="text" name="dupflag[0]" id="dupflag[0]" class="form-control text-right" value="0" style="width:20px;" readonly /></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div><br/>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:30px;">
                                            <label>IN<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>EB<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
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
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var l = true;
                var breed_code = document.getElementById("breed_code").value;
                if(breed_code == "" || breed_code == "select"){
                    alert("Please select Breed");
                    document.getElementById("breed_code").focus();
                    l = false;
                }
                else{
                    var incr = document.getElementById("incr").value;
                    var medvac_code = dupflag = rage = icode = ""; var e = age = 0;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            e = d + 1;
                            age = document.getElementById("age["+d+"]").value;
                            medvac_code = document.getElementById("medvac_code["+d+"]").value;
                            dupflag = document.getElementById("dupflag["+d+"]").value;
                                
                            if(age == ""){
                                alert("Please enter Age in row: "+e);
                                document.getElementById("age["+d+"]").focus();
                                l = false;
                            }
                            else if(medvac_code == "" || medvac_code == "select"){
                                alert("Please enter Vaccine Name in row: "+e);
                                document.getElementById("medvac_code["+d+"]").focus();
                                l = false;
                            }
                            else if(parseInt(dupflag) == 1){
                                alert("Vaccine Name already exist.\n Kindly check in row: "+e);
                                document.getElementById("medvac_code["+d+"]").focus();
                                l = false;
                            }
                            else{
                                for(var c = 0;c <= incr;c++){
                                    if(l == true){
                                        if(c == d){ }
                                        else{
                                            rage = document.getElementById("age["+c+"]").value;
                                            icode = document.getElementById("medvac_code["+c+"]").value;
                                            if(rage == age && icode == medvac_code){
                                                e = c + 1;
                                                alert("Same vaccine and age details already available in row: "+e);
                                                document.getElementById("age["+d+"]").focus();
                                                l = false;
                                            }
                                        }
                                    }
                                } 
                            }
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
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'layer_display_vaccine_standard1.php?ccid='+ccid;
            }
			function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                var slno = d + 1;
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><input type="text" name="age[]" id="age['+d+']" class="form-control text-right" style="width:110px;" onkeyup="validate_count(this.id);" onchange="validate_count(this.id);check_duplicate(this.id);" /></td>';
                html += '<td><select name="medvac_code[]" id="medvac_code['+d+']" class="form-control select2" style="width:190px;" onchange="check_duplicate(this.id);"><option value="select">-select-</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $item_name[$ucode]; ?></option><?php } ?></select></td>';
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
			function check_duplicate(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var age = document.getElementById("age["+d+"]").value;
				var medvac_code = document.getElementById("medvac_code["+d+"]").value;
				var type = "add";
				if(age != "" && medvac_code != "select"){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "layer_fetch_vaccine_std_duplicates.php?age="+age+"&medvac_code="+medvac_code+"&type="+type+"&row_count="+d;
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
								alert("Vaccine Name already exist.\n Kindly change the Vaccine Name");
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