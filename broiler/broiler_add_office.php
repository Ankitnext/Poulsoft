<?php
//broiler_add_office.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['office'];
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
        $sql = "SELECT * FROM `breeder_extra_access` WHERE `field_name` = 'Breeder Office' AND `field_function` = 'Create Breeder Sectors/Offices' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $bsec_sflag = mysqli_num_rows($query);

        $sql = "SELECT * FROM `layer_extra_access` WHERE `field_name` = 'layer Office' AND `field_function` = 'Create layer Sectors/Offices' AND `user_access` = 'all' AND `flag` = '1'";
        $query = mysqli_query($conn,$sql); $lsec_sflag = mysqli_num_rows($query);
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <style>
        body{
            overflow: auto;
        }
        .form-control{
            font-size: 13px;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
            $sql = "SELECT * FROM `main_officetypes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ if($row['description'] == "Shop" || $row['description'] == "shop"){ $shop_code = $row['code']; } }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Office</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_office.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                            <label>Sector Description<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="idesc" id="idesc" class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id)" onchange="check_duplicate();">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="stype">Sector Type<b style="color:red;">&nbsp;*</b></label>
                                                <select name="stype" id="stype" class="form-control select2" style="width: 100%;" onchange="addretailinfo()">
                                                    <option value="select">select</option>
                                                    <?php
                                                        $sql = "SELECT * FROM `main_officetypes` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                            <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-1" style="padding-top: 12px;"><br/>
                                            <a href="broiler_add_sectortype.php" target="_new"><i class="fa fa-plus"></i></a>
                                        </div>
                                        <div class="col-md-4"> </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <label>Address<b style="color:red;">&nbsp;*</b></label>
                                            <Textarea name="sector_address" id="sector_address"  rows="4" cols="50" maxlength="300" class="form-control" onkeyup="validatename(this.id)"></Textarea>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <label>Location<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" name="sloc" id="sloc" class="form-control" placeholder="Enter Location..." onkeyup="validatename(this.id)">
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <?php if((int)$bsec_sflag == 1){ ?>
                                        <div class="row justify-content-center align-items-center">
                                            <div class="form-group">
                                                <label for="brd_sflag"><input type="checkbox" name="brd_sflag" id="brd_sflag" />&nbsp;Breeder</label>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if((int)$lsec_sflag == 1){ ?>
                                    <div class="row justify-content-center align-items-center">
                                        <div class="form-group">
                                            <label for="lyr_sflag"><input type="checkbox" name="lyr_sflag" id="lyr_sflag" />&nbsp;Layer</label>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <div class="col-md-12" id="shop_details" style="visibility:hidden;">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label>Shop Manager</label>
                                                    <input type="text" name="shop_manager" id="shop_manager" class="form-control" placeholder="Manager Name...">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Phone/Mobile</label>
                                                    <input type="text" name="shop_mobile" id="shop_mobile" class="form-control" placeholder="Mobile No...">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>State</label>
                                                    <input type="text" name="shop_state" id="shop_state" class="form-control" placeholder="State...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label>Address</label>
                                                    <input type="text" name="shop_address" id="shop_address" class="form-control" placeholder="Enter Address...">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Email</label>
                                                    <input type="text" name="shop_email" id="shop_email" class="form-control" placeholder="Enter Email...">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="form-group col-md-1" style="visibility:hidden;">
                                                    <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                                    <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                                </div>
                                                <div class="form-group col-md-1" style="visibility:hidden;">
                                                    <label>D-Flag<b style="color:red;">&nbsp;*</b></label>
                                                    <input type="text" style="width:auto;" class="form-control" name="dupflag" id="dupflag" value="0">
                                                </div>
                                            </div>
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
                window.location.href = 'broiler_display_office.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var a = document.getElementById("idesc").value;
                var b = document.getElementById("stype").value;
                var c = document.getElementById("sloc").value;
                var dupflag = document.getElementById("dupflag").value;
                var l = true;
                if(parseFloat(dupflag) > 0){
                    alert("Sector/Office name already Exist \n Kindly check and try again ..!");
                    document.getElementById("idesc").focus();
                    l = false;
                }
                else if(a.length == 0){
                    alert("Enter Description ..!");
                    document.getElementById("idesc").focus();
                    l = false;
                }
                else if(b.match("select")){
                    alert("Select Category ..!");
                    document.getElementById("stype").focus();
                    l = false;
                }
                else if(c.length == 0){
                    alert("Enter Location ..!");
                    document.getElementById("sloc").focus();
                    l = false;
                }
                else { }
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
			function check_duplicate(){ 
				var b = document.getElementById("idesc").value;
				var c = "add";
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_sector_duplicates.php?cname="+b+"&type="+c;
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_count = this.responseText;
							if(parseFloat(dup_count) > 0){
								alert("Sector/Office Details are available with the same name.\n Kindly change the name");
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