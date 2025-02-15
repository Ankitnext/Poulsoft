<?php
//breeder_edit_standards1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['standards1'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
$sql = "SELECT * FROM `main_linkdetails` WHERE `href` LIKE '$href' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
$link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $link_childid = $row['childid']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $alink = array(); $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $alink = explode(",",$row['editaccess']);
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; }
        else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; }
        else{ $user_type = "N"; }
    }
    if($user_type == "S"){ $acount = 1; }
    else{
        foreach($alink as $edit_access_flag){
            if($edit_access_flag == $link_childid){
                $acount = 1;
            }
        }
    }
    if($acount == 1){

        $sql = "SELECT * FROM `breeder_breed_details` WHERE `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $br_code = $br_name = array();
        while($row = mysqli_fetch_assoc($query)){ $br_code[$row['code']] = $row['code']; $br_name[$row['code']] = $row['description']; }
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
        ::-webkit-scrollbar { width: 8px; height:8px; } /*display: none;*/
        .row_body2{
            overflow-y: auto;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $ids = $_GET['id'];
        $sql = "SELECT * FROM `breeder_breed_standards` WHERE `id` = '$ids' AND `dflag` = '0' AND `trlink` = 'breeder_display_standards1.php'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $breed_code = $row['breed_code'];
            $breed_age = round($row['breed_age'],5);
            $livability = round($row['livability'],5);
            $ffeed_pbird = round($row['ffeed_pbird'],5);
            $mfeed_pbird = round($row['mfeed_pbird'],5);
            $hd_per = round($row['hd_per'],5);
            $he_per = round($row['he_per'],5);
            $hhp_pweek = round($row['hhp_pweek'],5);
            $chhp_pweek = round($row['chhp_pweek'],5);
            $hhe_pweek = round($row['hhe_pweek'],5);
            $chhe_pweek = round($row['chhe_pweek'],5);
            $hatch_per = round($row['hatch_per'],5);
            $chicks_pweek = round($row['chicks_pweek'],5);
            $cchicks_pweek = round($row['cchicks_pweek'],5);
            $egg_weight = round($row['egg_weight'],5);
            $fbird_bweight = round($row['fbird_bweight'],5);
            $egg_weight = round($row['egg_weight'],5);
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Breed Standards</h3></div>
                        </div>
                        <div class="p-1 card-body">
                            <form action="breeder_modify_standards1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="p-1 row row_body2" style="margin-bottom:3px;">
                                    <table class="p-1">
                                        <thead>
                                            <tr>
                                                <th><label>Breed<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Age (In Weeks)<b style="color:red;">*</b></label></th>
                                                <th style="text-align:center;"><label>Livability</label></th>
                                                <th style="text-align:center;"><label>F.Feed/Bird (gms)</label></th>
                                                <th style="text-align:center;"><label>M.Feed/Bird (gms)</label></th>
                                                <th style="text-align:center;"><label>HD%</label></th>
                                                <th style="text-align:center;"><label>HE%</label></th>
                                                <th style="text-align:center;"><label>HHP/Week</label></th>
                                                <th style="text-align:center;"><label>C.HHP/Week</label></th>
                                                <th style="text-align:center;"><label>HHE/Week</label></th>
                                                <th style="text-align:center;"><label>C.HHE/Week</label></th>
                                                <th style="text-align:center;"><label>Hatch %</label></th>
                                                <th style="text-align:center;"><label>Chicks/week</label></th>
                                                <th style="text-align:center;"><label>C.Chicks/ week</label></th>
                                                <th style="text-align:center;"><label>Egg Weight</label></th>
                                                <th style="text-align:center;"><label>F.B.Wt(gms)</label></th>
                                                <th style="text-align:center;"><label>M.B.Wt(gms)</label></th>
                                                <th style="visibility:hidden;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <tr>
                                                <td><select name="breed_code" id="breed_code" class="form-control select2" style="width:200px;">
                                                    <option value="select">-select-</option>
                                                    <?php foreach($br_code as $b_code){ ?>
                                                        <option value="<?php echo $b_code ?>" <?php if($breed_code == $b_code) { echo 'selected'; } ?>><?php echo $br_name[$b_code] ?></option>          
                                                  <?php  } ?>
                                                </select></td>
                                                <td><input type="text" name="breed_age" id="breed_age" class="form-control" value="<?php echo $breed_age; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="livability" id="livability" class="form-control text-right" value="<?php echo $livability; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="ffeed_pbird" id="ffeed_pbird" class="form-control text-right" value="<?php echo $ffeed_pbird; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="mfeed_pbird" id="mfeed_pbird" class="form-control text-right" value="<?php echo $mfeed_pbird; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="hd_per" id="hd_per" class="form-control text-right" value="<?php echo $hd_per; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="he_per" id="he_per" class="form-control text-right" value="<?php echo $he_per; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="hhp_pweek" id="hhp_pweek" class="form-control text-right" value="<?php echo $hhp_pweek; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="chhp_pweek" id="chhp_pweek" class="form-control text-right" value="<?php echo $chhp_pweek; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="hhe_pweek" id="hhe_pweek" class="form-control text-right" value="<?php echo $hhe_pweek; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="chhe_pweek" id="chhe_pweek" class="form-control text-right" value="<?php echo $chhe_pweek; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="hatch_per" id="hatch_per" class="form-control text-right" value="<?php echo $hatch_per; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="chicks_pweek" id="chicks_pweek" class="form-control text-right" value="<?php echo $chicks_pweek; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="cchicks_pweeks" id="cchicks_pweeks" class="form-control text-right" value="<?php echo $cchicks_pweek; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="egg_weight" id="egg_weight" class="form-control text-right" value="<?php echo $egg_weight; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="fbird_bweight" id="fbird_bweight" class="form-control text-right" value="<?php echo $fbird_bweight; ?>" style="width:90px;" /></td>
                                                <td><input type="text" name="mbird_bweight" id="mbird_bweight" class="form-control text-right" value="<?php echo $mbird_bweight; ?>" style="width:90px;" /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><br/>
                                <div class="row" style="visibility:hidden;">
                                    <div class="form-group" style="width:30px;">
                                        <label>ID<b style="color:red;">&ensp;*</b></label>
                                        <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                                    </div>
                                    <div class="form-group" style="width:30px;">
                                        <label>EB<b style="color:red;">&ensp;*</b></label>
                                        <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
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
                var breed_age = document.getElementById("breed_age").value;
               
                if(breed_code == "select"){
                    alert("Please enter Breed");
                    document.getElementById("breed_code").focus();
                    l = false;
                } else if(breed_age == ""){
                    alert("Please enter Age");
                    document.getElementById("breed_age").focus();
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
                window.location.href = 'breeder_display_standards1.php?ccid='+ccid;
            }
			function check_duplicate(){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
				var breed_age = document.getElementById("breed_age").value;
                var id = '<?php echo $ids; ?>';
				var type = "edit";
				if(breed_age != ""){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "breeder_fetch_standards1_duplicates.php?breed_age="+breed_age+"&id="+id+"&type="+type;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_dt = this.responseText;
                            var dup_info = dup_dt.split("@");
                            
							if(parseInt(dup_info[0]) == 0){
								document.getElementById("dupflag"). value = 0;
							}
							else {
								alert("Standards already exist for same Age.\n Kindly change and try again.");
								document.getElementById("dupflag"). value = 1;
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