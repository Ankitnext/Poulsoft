<?php
//breeder_edit_unitmap1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['unitmap1'];
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
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($elink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){
        $sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Cold Room%' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $office_code = array();
        while($row = mysqli_fetch_assoc($query)){ $office_code[$row['code']] = $row['code']; } $off_list = implode("','", $office_code);

        $sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$off_list') AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
        while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

        $sql = "SELECT * FROM `breeder_units` WHERE `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $unit_code = $unit_name = array();
        while($row = mysqli_fetch_assoc($query)){ $unit_code[$row['code']] = $row['code']; $unit_name[$row['code']] = $row['description']; }
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
        <?php
		$ids = $_GET['id'];
        $sql = "SELECT * FROM `broiler_secunit_mapping` WHERE `id` = '$ids' AND `dflag` = '0'";  $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $id = $row['code'];
            $sec_code = $row['sector_code'];
            $brh_code = $row['unit_code'];
        }
		?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Sector-Unit Mapping</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="breeder_modify_unitmap1.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-6">
                                            <table style="width:auto;">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;"><label>Sector<b style="color:red;">&nbsp;*</b></label></th>
                                                        <th style="text-align:center;"><label>Unit<b style="color:red;">&nbsp;*</b></label></th>
                                                        <th style="text-align:center;"><label></label></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody">
                                                    <tr>
                                                        <td><select name="sector_code" id="sector_code" class="form-control select2" style="width:190px;" onchange="check_duplicate();"><option value="select">select</option><?php foreach($sector_code as $wcode){ ?><option value="<?php echo $wcode; ?>" <?php if($sec_code == $wcode){ echo "selected"; } ?>><?php echo $sector_name[$wcode]; ?></option><?php } ?></select></td>
                                                        <td><select name="unit_code" id="unit_code" class="form-control select2" style="width:190px;" onchange="check_duplicate();"><option value="select">select</option><?php foreach($unit_code as $bcode){ ?><option value="<?php echo $bcode; ?>" <?php if($brh_code == $bcode){ echo "selected"; } ?>><?php echo $unit_name[$bcode]; ?></option><?php } ?></select></td>
                                                        <td style="visibility:hidden;"><input type="text" name="dupflag" id="dupflag" class="form-control text-right" value="0" style="width:20px;" readonly /></td>
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
                                                    <label>ID</label>
                                                    <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
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
			function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var sector_code = document.getElementById("sector_code").value;
                var unit_code = document.getElementById("unit_code").value;
                var dupflag = document.getElementById("dupflag").value; if(dupflag == ""){ dupflag = 0; }
				var l = true;
				if(sector_code == "select"){
					alert("Please select sector");
					document.getElementById("sector_code").focus();
					l = false;
				}
				else if(unit_code == "select"){
					alert("Please select branch");
					document.getElementById("unit_code").focus();
					l = false;
				}
				else if(parseInt(dupflag) == 1){
					alert("Same entry already available");
					document.getElementById("unit_code").focus();
					l = false;
				}
				else{ }

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
                window.location.href = 'breeder_display_unitmap1.php?ccid='+ccid;
            }
            function check_duplicate(){
                sector_code = document.getElementById("sector_code").value;
                unit_code = document.getElementById("unit_code").value;
                var id = '<?php echo $ids; ?>';
                if(sector_code != "select" && unit_code != "select"){
                    var ven_bals = new XMLHttpRequest();
                    var method = "GET";
                    var url = "breeder_check_unitmap1_duplicate.php?sector_code="+sector_code+"&unit_code="+unit_code+"&id="+id+"&row_count=0";
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
                            document.getElementById("dupflag").value = parseInt(dupflag);
                        }
                    }
                }
            }
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