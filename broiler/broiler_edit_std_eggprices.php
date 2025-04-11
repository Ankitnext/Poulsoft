<?php
//broiler_edit_std_eggprices.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['std_eggprices'];
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
        $item_cat = ""; $today = date("d.m.Y");
        $sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND (`description` LIKE '%Hatch%' OR `description` LIKE '%chick%' OR `description` LIKE '%Broiler Birds%')"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ if( $item_cat == ""){  $item_cat = $row['code']; } else{ $item_cat = $item_cat."','".$row['code']; } }

        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$item_cat') AND `active` = '1' ORDER BY `description` ASC";
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
        <?php
		$ids = $_GET['id'];
        $sql = "SELECT * FROM `broiler_std_item_prices` WHERE `id` = '$ids' AND `active` = '1'";  $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $id = $row['code'];
            $date = date("d.m.Y",strtotime($row['date']));
            $itemcode = $row['itemcode'];
            $rate = $row['rate'];
        }
		?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Standard Chick/Egg Rate</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_std_eggprices.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row" style="visibility:hidden;">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>incr<b style="color:red;">&nbsp;*</b></label>
									            <input type="text" style="width:auto;" class="form-control" name="incr" id="incr" value="0">
                                            </div>
                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-6">
                                            <table class="table1" style="width:50%;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 160px;text-align:center;"><label>Date<b style="color:red;">&nbsp;*</b></label></th>
                                                        <th style="text-align:center;"><label>Item Description<b style="color:red;">&nbsp;*</b></label></th>
                                                        <th style="text-align:center;"><label>Rate<b style="color:red;">&nbsp;*</b></label></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody">
                                                    <tr>
                                                        <td><div class="form-group col-md-12"><input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo $date; ?>" style="width:100px;" /></div></td>
                                                        <td><div class="form-group col-md-12"><select name="itemcode" id="itemcode" class="form-control select2" style="width:150px;"><option value="select">select</option><?php foreach($item_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($itemcode == $ucode){ echo "selected"; } ?>><?php echo $item_name[$ucode]; ?></option><?php } ?></select></div></td>
                                                        <td><div class="form-group col-md-6"><input type="text" name="rate" id="rate" class="form-control" value="<?php echo $rate; ?>" style="width:150px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></div></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="form-group col-md-12" style="visibility:hidden;">
                                        <label>id<b style="color:red;">&ensp;*</b></label>
                                        <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $id; ?>" placeholder="Enter description..." onkeyup="validatename(this.id);colorchange(this.id);">
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
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_std_eggprices.php?ccid='+ccid;
            }
			function checkval(){
				a = document.getElementById("date").value;
				b = document.getElementById("itemcode").value;
				c = document.getElementById("rate").value;
				var l = true;
				if(a.length == 0){
					alert("Please select Date");
					document.getElementById("date").focus();
					l = false;
				}
				else if(b.match("select")){
					alert("Please select Item Description");
					document.getElementById("itemcode").focus();
					l = false;
				}
				else if(c.length == 0 || c == 0 || c == 0.00){
					alert("Please Enter Rate");
					document.getElementById("rate").focus();
					l = false;
				}
				else{
					l = true;
				}
				if(l == true){
					return true;
				}
				else if(l == false){
					return false;
				}
				else{
					return false;
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