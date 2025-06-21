<?php
//broiler_add_contcurrency.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['contcurrency'];
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

        $sql = "SELECT * FROM `country_currency` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `cont_name` ASC";
        $query = mysqli_query($conn,$sql); $cont_code = $cont_name = array();
        while($row = mysqli_fetch_assoc($query)){ $cont_code[$row['code']] = $row['code']; $cont_name[$row['code']] = $row['cont_name']; $curr_name[$row['code']] = $row['curr_name']; }
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
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Current Currency Value</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_contcurrency.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row" style="display:flex; justify-content: center;">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                 <label>Prefix</label>
									            <input type="text" name="prfx" id="prfx" class="form-control" placeholder="Enter Prefix Value" onkeyup="">
                                            </div>
                                        </div>
                                       
                                         <div class="col-md-2">
                                            <div class="form-group">
                                                 <label>Country Name</label>
									            <input type="text" name="ct_name" id="ct_name" class="form-control" placeholder="Enter Country Name" onkeyup="">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                 <label>Currency Name</label>
									            <input type="text" name="cr_name" id="cr_name" class="form-control" placeholder="Enter Currency Name" onkeyup="">
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
                window.location.href = 'broiler_display_contcurrency.php?ccid='+ccid;
            }
			function checkval(){
				var prfx = document.getElementById("prfx").value;
				var ct_name = document.getElementById("ct_name").value;
				var cr_name = document.getElementById("cr_name").value;
                
                var l = true;
				if(prfx == ""){
					alert("Enter Prefix ..!");
                    document.getElementById("prfx").focus();
					l = false;
				}
				else if(ct_name == ""){
					alert("Enter Country Name ..!");
                    document.getElementById("ct_name").focus();
					l = false;
				}
				else if(cr_name == ""){
					alert("Enter Currency Name ..!");
                    document.getElementById("cr_name").focus();
					l = false;
				}
                
                if(l == true){
                    return true;
                }
				else {
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
             function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
			function validateamount(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
            function validatenumdecim(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50) { a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g,'');}  var parts = a.split('.'); if (parts.length > 2) { a = parts[0] + '.' + parts.slice(1).join('').replace(/\./g, ''); } document.getElementById(x).value = a; }
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