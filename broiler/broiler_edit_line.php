<?php
//broiler_edit_line.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['loc_line'];
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
		$sql = "SELECT * FROM `location_line` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$description = $row['description'];
			$region_code = $row['region_code'];
			$branch_code = $row['branch_code'];
		}
		?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Line</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_line.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Region<b style="color:red;">&nbsp;*</b></label>
                                                <select name="region" id="region" class="form-control select2" style="width: 100%;" onchange="fetch_branch_details(this.id)">
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
                                    <div class="row" id="row_no[0]">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Branch<b style="color:red;">&nbsp;*</b></label>
							                    <select name="branch" id="branch" class="form-control select2" style="width: 100%;">
                                                    <option value="select">select</option>
                                                    <?php
                                                    $sql = "SELECT * FROM `location_branch` WHERE `region_code` = '$region_code' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
                                                    while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                    <option value="<?php echo $row['code']; ?>" <?php if($branch_code == $row['code']){ echo "selected"; } ?>><?php echo $row['description']; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Line<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="line" id="line" class="form-control" value="<?php echo $description; ?>" placeholder="Enter description..." onkeyup="validatename(this.id)" onchange="check_duplicate();">
                                            </div>
                                        </div>
                                        <div class="col-md-3"></div>
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
                window.location.href = 'broiler_display_line.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var a = d = e = ""; var l = true;
                a = document.getElementById("region").value;
                d = document.getElementById("branch").value;
                e = document.getElementById("line").value;
                var dupflag = document.getElementById("dupflag").value;
                if(a.match("select")){
                    alert("Please select Region");
                    document.getElementById("region").focus();
                    l = false;
                }
                else if(d.match("select")){
                    alert("Please select Branch");
                    document.getElementById("branch").focus();
                    l = false;
                }
                else if(e.length == 0){
                    alert("Please enter Line");
                    document.getElementById("line").focus();
                    l = false;
                }
                if(dupflag == 1 || dupflag == "1"){
                    alert("Line Name already exist \n Please check and try again");
                    document.getElementById("line").focus();
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
            function fetch_branch_details(a){
                var reg_code = document.getElementById(a).value;
                if(!reg_code.match("select")){
                    removeAllOptions(document.getElementById("branch"));
                    myselect1 = document.getElementById("branch");
                    theOption1=document.createElement("OPTION");
                    theText1=document.createTextNode("select");
                    theOption1.value = "select"; 
                    theOption1.appendChild(theText1); 
                    myselect1.appendChild(theOption1);
                    <?php
                        /*Check User access Locations*/
                        $sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
                        if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
                        if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
                        if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

                        $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ".$branch_access_filter1." ORDER BY `description` ASC";
                        //$sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; 
                        $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $r_code = $row['region_code'];
                            echo "if(reg_code == '$r_code'){";
                    ?>
                        theOption1=document.createElement("OPTION");
						theText1=document.createTextNode("<?php echo $row['description']; ?>");
						theOption1.value = "<?php echo $row['code']; ?>";
						theOption1.appendChild(theText1); myselect1.appendChild(theOption1);
                    <?php
                        echo "}";
                        }
                    ?>
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
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
			function check_duplicate(){
				var b = document.getElementById("line").value;
				var c = "edit";
                var d = '<?php echo $id; ?>';
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_line_duplicates.php?cname="+b+"&type="+c+"&id="+d;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_count = this.responseText;
							if(parseFloat(dup_count) > 0){
								alert("Line Details are available with the same name.\n Kindly change the name");
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