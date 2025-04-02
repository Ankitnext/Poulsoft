<?php
//broiler_edit_clientdashboardfields.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['clientdashboardfields'];
$uri = explode("?",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = basename($uri[0]);
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
        $db_name = $_SESSION['dbase'];
        $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$db_name' AND `account_access` = 'BTS' AND `flag` = '1' ORDER BY `username` ASC"; $query = mysqli_query($conns,$sql);
        while($row = mysqli_fetch_assoc($query)){ $emp_uname[$row['empcode']] = $row['username']; $emp_ucode[$row['empcode']] = $row['empcode']; }

        $sql = "SELECT * FROM `master_dashboard_links` WHERE `user_code` = '$user_code' AND `active` = '1' AND `dflag` = '0' ORDER BY `sort_order` ASC"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $panel_name[$row['panel_name']] = $row['panel_name']; $panel_code[$row['panel_name']] = $row['field_name']."@".$row['panel_name']."@".$row['sort_order']; }

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
		$sql = "SELECT * FROM `master_dashboard_links` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){
			$ucode = $row['user_code'];
			$pname = $row['panel_name'];
			$sordr = $row['sort_order'];
		}
		?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Dashboard Access</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_clientdashboardfields.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">&nbsp;
                                        <div class="col-md-4"></div>
                                        <div style="display:flex; justify-content: center;" class="col-md-4">
                                            <div class="form-group" style="width:290px;">
                                                <label>User<b style="color:red;">&nbsp;*</b></label>
                                                <select name="user_code" id="user_code" class="form-control select2" style="width:180px;" onchange="check_duplication(this.id);">
                                                    <option value="select">select</option>
                                                    <?php
                                                     foreach($emp_ucode as $fn){ ?><option value="<?php echo $fn; ?>" <?php if($ucode == $fn){ echo "selected"; } ?>><?php echo $emp_uname[$fn]; ?></option> <?php } ?>
                                                </select>
                                            </div>&ensp;
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-7">
                                            <div class="row">&nbsp;
                                                <!-- <div class="form-group" style="width:190px;">
                                                    <label>User<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="user_code" id="user_code" class="form-control select2" style="width:180px;" onchange="check_duplication(this.id);">
                                                        <option value="select">select</option>
                                                        <?php
                                                        foreach($emp_ucode as $fn){ ?><option value="<?php echo $fn; ?>" <?php if($ucode == $fn){ echo "selected"; } ?>><?php echo $emp_uname[$fn]; ?></option> <?php } ?>
                                                    </select>
                                                </div>&ensp; -->
                                                <div class="form-group" style="width:290px;">
                                                    <label>Panel<b style="color:red;">&nbsp;*</b></label>
                                                    <select name="panel_name" id="panel_name" class="form-control select2" style="width:280px;" onchange="check_duplication(this.id);">
                                                        <option value="select">select</option>
                                                        <?php
                                                        foreach($panel_name as $fn){ ?><option value="<?php echo $panel_code[$fn]; ?>" <?php if($pname == $fn){ echo "selected"; } ?>><?php echo $fn; ?></option> <?php } ?>
                                                    </select>
                                                </div>&ensp;
                                                <div class="form-group" style="width:90px;">
                                                    <label>Sort Order<b style="color:red;">&nbsp;*</b></label>
                                                    <input type="text" style="width:80px;" class="form-control" name="sort_order" id="sort_order" value="<?php echo $sordr; ?>" />
                                                </div>&ensp;
                                                <div class="form-group" style="width:10px;visibility:hidden;">
                                                    <label>dflag<b style="color:red;">&nbsp;*</b></label>
                                                    <input type="text" style="width:9px;" class="form-control" name="dup_flag" id="dup_flag" />
                                                </div>&ensp;
                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="col-md-12" id="row_body"></div><br/><br/>
                                    <div class="row">
                                        <div class="form-group col-md-6" style="visibility:hidden;">
                                            <label>Id<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>">
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
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_clientdashboardfields.php?ccid'+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var user_code = panel_name = dup_flag = "";
                var l = true; var c = 0;
                
                dup_flag = document.getElementById("dup_flag").value;
                user_code = document.getElementById("user_code").value;
                panel_name = document.getElementById("panel_name").value;

                if(dup_flag == 1 || dup_flag == "1"){
                    alert("Already panel allocated for the selected user");
                    document.getElementById("user_code").focus();
                    l = false;
                }
                else if(user_code.match("select")){
                    alert("Select Username");
                    document.getElementById("user_code").focus();
                    l = false;
                }
                else if(panel_name.match("select")){
                    alert("Select Panel Name");
                    document.getElementById("panel_name").focus();
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
            function check_duplication(){
                var user_code = document.getElementById("user_code").value;
                var panel_name = document.getElementById("panel_name").value;
                var id = '<?php echo $id; ?>';
                if(user_code != "select" && panel_name != "select"){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_check_dashboardaccess_duplication.php?user_code="+user_code+"&panel_name="+panel_name+"&id="+id;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var dval = this.responseText;
                            var dval2 = dval.split("@");
                            var dup_flag  = dval2[0];
                            var row  = dval2[1];
                            if(dup_flag == 0 || dup_flag == "0"){
                                document.getElementById("dup_flag").value = dup_flag;
                                var pval1 = panel_name.split("@");
                                document.getElementById("sort_order").value = pval1[2];
                            }
                            else{
                                document.getElementById("dup_flag").value = dup_flag;
                                row = row + 1;
                                alert("Already panel allocated for the selected user");
                            }
                        }
                    }
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