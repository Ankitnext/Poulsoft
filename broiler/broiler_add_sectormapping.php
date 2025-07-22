<?php
//broiler_add_sectormapping.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['sectormapping'];
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
        $sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $branch_code = $branch_name = array();
        while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; }

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
                            <div class="float-left"><h3 class="card-title">Add Sector-Branch Mapping</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_sectormapping.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-6">
                                            <table style="width:auto;">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;"><label>Sector<b style="color:red;">&nbsp;*</b></label></th>
                                                        <th style="text-align:center;"><label>Branch<b style="color:red;">&nbsp;*</b></label></th>
                                                        <th style="text-align:center;"><label>+/-</label></th>
                                                        <th style="text-align:center;"><label></label></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody">
                                                    <tr>
                                                        <td><select name="sector_code[]" id="sector_code[0]" class="form-control select2" style="width:190px;" onchange="check_duplicate(this.id);"><option value="select">select</option><?php foreach($sector_code as $wcode){ ?><option value="<?php echo $wcode; ?>"><?php echo $sector_name[$wcode]; ?></option><?php } ?></select></td>
                                                        <td><select name="branch_code[]" id="branch_code[0]" class="form-control select2" style="width:190px;" onchange="check_duplicate(this.id);"><option value="select">select</option><?php foreach($branch_code as $bcode){ ?><option value="<?php echo $bcode; ?>"><?php echo $branch_name[$bcode]; ?></option><?php } ?></select></td>
                                                        <td id="action[0]" style="width:80px;text-align:center;"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                        <td style="visibility:hidden;"><input type="text" name="dupflag[]" id="dupflag[0]" class="form-control text-right" value="0" style="width:20px;" readonly /></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="row" style="visibility:hidden;">
                                                <div class="form-group" style="width:25px;">
                                                    <label>IN</label>
                                                    <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                                                </div>
                                                <div class="form-group" style="width:25px;">
                                                    <label>EC</label>
                                                    <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
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
				var incr = document.getElementById("incr").value;
				var sector_code = branch_code = scode = bcode = ""; var dupflag = a = e = 0;
				var l = true;
				for(var d = 0;d <= incr;d++){
					if(l == true){
						a = d + 1;
						sector_code = document.getElementById("sector_code["+d+"]").value;
						branch_code = document.getElementById("branch_code["+d+"]").value;
						dupflag = document.getElementById("dupflag["+d+"]").value; if(dupflag == ""){ dupflag = 0; }
						
						if(sector_code == "select"){
							alert("Please select sector in row: "+a);
							document.getElementById("sector_code["+d+"]").focus();
							l = false;
						}
						else if(branch_code == "select"){
							alert("Please select branch in row: "+a);
							document.getElementById("branch_code["+d+"]").focus();
							l = false;
						}
						else if(parseInt(dupflag) == 1){
							alert("Same entry already available in row: "+a);
							document.getElementById("branch_code["+d+"]").focus();
							l = false;
						}
						else{
                            for(var c = 0;c <= incr;c++){
                                if(l == true){
                                    if(c == d){ }
                                    else{
                                        scode = document.getElementById("sector_code["+c+"]").value;
                                        bcode = document.getElementById("branch_code["+c+"]").value;
                                        if(sector_code == scode || branch_code == bcode){
                                            e = c + 1;
                                            alert("Same Sector (or) Branch mappings are already available in row: "+e);
                                            document.getElementById("sector_code["+d+"]").focus();
                                            l = false;
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
                window.location.href = 'broiler_display_sectormapping.php?ccid='+ccid;
            }
			function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                var slno = d + 1;
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="sector_code[]" id="sector_code['+d+']" class="form-control select2" style="width:190px;" onchange="check_duplicate(this.id);"><option value="select">select</option><?php foreach($sector_code as $wcode){ ?><option value="<?php echo $wcode; ?>"><?php echo $sector_name[$wcode]; ?></option><?php } ?></select></td>';
                html += '<td><select name="branch_code[]" id="branch_code['+d+']" class="form-control select2" style="width:190px;" onchange="check_duplicate(this.id);"><option value="select">select</option><?php foreach($branch_code as $bcode){ ?><option value="<?php echo $bcode; ?>"><?php echo $branch_name[$bcode]; ?></option><?php } ?></select></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="dupflag[]" id="dupflag['+d+']" class="form-control text-right" value="0" style="width:20px;" /></td>';
				html += '</tr>';
				$('#tbody').append(html);
				$('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove(); d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function check_duplicate(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                sector_code = document.getElementById("sector_code["+d+"]").value;
                branch_code = document.getElementById("branch_code["+d+"]").value;

                if(sector_code != "select" && branch_code != "select"){
                    var ven_bals = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_check_sectormapping_duplicate.php?sector_code="+sector_code+"&branch_code="+branch_code+"&row_count="+d;
                    //window.open(url);
                    var asynchronous = true;
                    ven_bals.open(method, url, asynchronous);
                    ven_bals.send();
                    ven_bals.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var dup_dt1 = this.responseText;
                            var dup_dt2 = dup_dt1.split("@");
                            var rows = dup_dt2[0];
                            var dupflag = dup_dt2[1];
                            document.getElementById("dupflag["+rows+"]").value = parseInt(dupflag);
                        }
                    }
                }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
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