<?php
//broiler_edit_salaryparam1.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['salaryparam1'];
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
       
        $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
 	
        $farms = array();
		$sql = "SELECT * FROM `broiler_batch` WHERE `dflag` = '0' AND `gc_flag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){ $farms[$row['code']] = $row['farm_code'];$farms_batch[$row['farm_code']] = $row['description']; }
        $farm_list = implode("','", $farms);
	
        $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `code` IN ('$farm_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
		while($row = mysqli_fetch_assoc($query)){  $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; $farm_code[$row['code']] = $row['farm_code']; }
      
        
        // Sector Code
        // $bsql = "SELECT * FROM `inv_sectors` WHERE  `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        // while($brow = mysqli_fetch_assoc($bquery)){ $sector_code[$brow['code']] = $brow['code']; $sector_name[$brow['code']] = $brow['description']; }
 
        // Designation Code
        $bsql = "SELECT * FROM `broiler_designation` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $bquery = mysqli_query($conn,$bsql);
        while($brow = mysqli_fetch_assoc($bquery)){ $desg_code[$brow['code']] = $brow['code']; $desg_name[$brow['code']] = $brow['description']; }
 
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
        .form-control{
            font-size: 13px;
        }
        /*::-webkit-scrollbar { width: 8px; height:8px; }
        .row_body2{
            width:100%;
            overflow-y: auto;
        }*/
        .table1{
            transform: scale(0.8);
            transform-origin: top left;
        }
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $ids = $_GET['trnum'];
        $sql = "SELECT * FROM `salary_structures` WHERE `id` = '$ids' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            
            $sect = $row['sector_code'];
            $desig_code = $row['desig_code'];
            $basic = round($row['basic'],5);
            $hra = round($row['hra'],5);
            $medical = round($row['medical'],5);
            $con_allow = round($row['con_allow'],5);
            $transport = round($row['transport'],5);
    
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Stock Transfer</h3></div>
                        </div>
                        <div class="pl-2 card-body">
                            <form action="broiler_modify_salaryparam1.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row row_body2">
                                    <table class="p-1 table1" style="width:auto;">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center;"><label>Sectors<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Designation<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Basic<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Hra</label></th>
                                                <th style="text-align:center;"><label>Medical<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Co. Allowances</label></th>
                                                <th style="text-align:center;"><label>Transport</label></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <tr>
                                                <td><select name="sector" id="sector" class="form-control select2" style="width:190px;" onchange=""><option value="select">-select-</option><?php foreach($sector_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $sect){ echo "selected"; } ?>><?php echo $sector_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><select name="desg" id="desg" class="form-control select2" style="width:190px;" onchange=""><option value="select">-select-</option><?php foreach($desg_code as $ucode){ ?><option value="<?php echo $ucode; ?>" <?php if($ucode == $desig_code){ echo "selected"; } ?>><?php echo $desg_name[$ucode]; ?></option><?php } ?></select></td>
                                                <td><input type="text" name="basic" id="basic" class="form-control text-right" value="<?php echo $basic; ?>" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="hra" id="hra" class="form-control text-right" value="<?php echo $hra; ?>" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="med" id="med" class="form-control text-right" value="<?php echo $medical; ?>" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="conv" id="conv" class="form-control text-right" value="<?php echo $con_allow; ?>" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                                <td><input type="text" name="trans" id="trans" class="form-control text-right" value="<?php echo $transport; ?>" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id);" /></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><br/>
                                <div class="row" style="visibility:hidden;">
                                    <div class="form-group" style="width:30px;">
                                        <label>ID</label>
                                        <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="padding:0;width:20px;" readonly />
                                    </div>
                                    <div class="form-group" style="width:30px;">
                                        <label>EB</label>
                                        <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="padding:0;width:20px;" readonly />
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
				update_ebtn_status(1);
                var l = true;
              
                var sector = document.getElementById("sector").value;
                var desg = document.getElementById("desg").value;
                var basic = document.getElementById("basic").value; if(basic == ""){ basic = 0; }
               
                        
                if(sector == "" || sector == "select"){
                    alert("Please select From Sector");
                    document.getElementById("sector").focus();
                    l = false;
                }
                else if(desg == "" || desg == "select"){
                    alert("Please select Designation");
                    document.getElementById("desg").focus();
                    l = false;
                }
                else if(parseFloat(basic) == 0){
                    alert("Please enter Basic");
                    document.getElementById("basic").focus();
                    l = false;
                }
                else{ }
                
                if(l == true){
                    return true;
                }
                else{
                    update_ebtn_status(0);
                    return false;
                }
			}
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_salaryparam1.php?ccid='+ccid;
            }
           
            function update_ebtn_status(a){
                if(parseInt(a) == 1){
                    document.getElementById("ebtncount").value = "1";
                    document.getElementById("submit").style.visibility = "hidden";
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                }
            }
           
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
			function validatenum(x) { expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
			function validate_count(x) { expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
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