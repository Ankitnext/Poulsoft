<?php
//broiler_edit_branch.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['loc_branch'];
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
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
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
		$id = $_GET['id'];
		$sql = "SELECT * FROM `location_branch` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$description = $row['description'];
			$region_code = $row['region_code'];
			$flk_prefix = $row['flk_prefix'];
		}
		?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Branch</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_branch.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Region<b style="color:red;">&nbsp;*</b></label>
                                                <select name="region" id="region" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <?php
                                                    $sql = "SELECT * FROM `location_region` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                    <option value="<?php echo $row['code']; ?>" <?php if($region_code == $row['code']){ echo "selected"; } ?>><?php echo $row['description']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                     </div>
                                    <div class="row" id="row_no">
                                        <div class="col-md-4"></div>
                                        
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Branch<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="branch" id="branch" class="form-control" value="<?php echo $description; ?>" placeholder="Enter description..." onkeyup="validatename(this.id)" onchange="check_duplicate();">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Prefix<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="flk_prefix" id="flk_prefix" class="form-control" value="<?php echo $flk_prefix; ?>" placeholder="Enter description..." onkeyup="validatename(this.id)">
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>" placeholder="Enter description..." onkeyup="validatename(this.id);colorchange(this.id);">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>D-Flag<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="dupflag" id="dupflag" value="0">
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
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_branch.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var a = document.getElementById("region").value;
                var d = document.getElementById("branch").value;
                var e = document.getElementById("flk_prefix").value;
                var dupflag = document.getElementById("dupflag").value;
                l = true;
                if(a.match("select")){
                    alert("Please select Region");
                    document.getElementById("region").focus();
                    l = false;
                }
                else if(d.length == 0){
                    alert("Please enter Branch");
                    document.getElementById("branch").focus();
                    l = false;
                }
                else if(e.length == 0){
                    alert("Please enter Prefix");
                    document.getElementById("flk_prefix").focus();
                    l = false;
                }
                if(dupflag == 1 || dupflag == "1"){
                    alert("Branch Name already exist \n Please check and try again");
                    document.getElementById("branch").focus();
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
            function validatename(x) {
                expr = /^[a-zA-Z0-9 (.&)_-]*$/;
                var a = document.getElementById(x).value;
                if(a.length > 50){
                    a = a.substr(0,a.length - 1);
                }
                if(!a.match(expr)){
                    a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, '');
                }
                document.getElementById(x).value = a;
            }
            function addretailinfo(){
                var a = document.getElementById("stype").value;
                <?php
                    echo "if(a == '$shop_code'){";
                ?>
                document.getElementById("shop_details").style.visibility = "visible";
                <?php	
                    echo "} else{";
                ?>
                document.getElementById("shop_details").style.visibility = "hidden";
                <?php
                    echo "}";
                ?>
            }
			function check_duplicate(){
				var b = document.getElementById("branch").value;
				var c = "edit";
                var d = '<?php echo $id; ?>';
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_branch_duplicates.php?cname="+b+"&type="+c+"&id="+d;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_count = this.responseText;
							if(parseFloat(dup_count) > 0){
								alert("Branch Details are available with the same name.\n Kindly change the name");
								document.getElementById("dupflag"). value = 1;
							}
							else {
								document.getElementById("dupflag"). value = 0;
							}
						}
					}
				}
				else { }
			}
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
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