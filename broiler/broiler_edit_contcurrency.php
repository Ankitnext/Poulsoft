<?php
//broiler_edit_contcurrency.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['contcurrency'];
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
        $sql = "SELECT * FROM `country_currency` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `cont_name` ASC";
        $query = mysqli_query($conn,$sql); $cont_code = $cont_name = array();
        while($row = mysqli_fetch_assoc($query)){ $cont_code[$row['code']] = $row['code']; $cont_name[$row['code']] = $row['cont_name']; $curr_name[$row['code']] = $row['curr_name']; }

        // print_r($cont_code);

		$id = $_GET['id'];
        $sql = "SELECT * FROM `country_currency` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            // $code = $row['code'];
            // $gtype = $row['gtype'];
            $prefix = $row['prefix'];
            $cont_name = $row['cont_name'];
            $curr_name = $row['curr_name'];
            // $id = $row['id'];
           
        }
      
        
        $sql = "SELECT * FROM `main_contactdetails` WHERE `groupcode` = '$code'";
        $query = mysqli_query($conn,$sql); $ucount = mysqli_num_rows($query);
    
		?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Current Currency</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_contcurrency.php" method="post" role="form" onsubmit="return checkval()">
                                     <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                 <label>Prefix</label>
									            <input type="text" name="prfx" id="prfx" class="form-control" value="<?php echo $prefix; ?>" placeholder="Enter Prefix Value" onkeyup="">
                                            </div>
                                        </div>
                                       
                                         <div class="col-md-4">
                                            <div class="form-group">
                                                 <label>Country Name</label>
									            <input type="text" name="ct_name" id="ct_name" class="form-control" value="<?php echo $cont_name; ?>" placeholder="Enter Country Name" onkeyup="">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                 <label>Currency Name</label>
									            <input type="text" name="cr_name" id="cr_name" class="form-control" value="<?php echo $curr_name; ?>" placeholder="Enter Currency Name" onkeyup="">
                                            </div>
                                        </div>
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
        <script>
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_contcurrency.php?ccid='+ccid;
            }
			function checkval(){
				var gdesc = document.getElementById("gdesc").value;
				var gtype = document.getElementById("gtype").value;
                var cus_controller_code = cus_prepayment_code = sup_controller_code = sup_prepayment_code = "";
                var l = true;
				if(gdesc.length == 0){
					alert("Enter Description ..!");
                    document.getElementById("gdesc").focus();
					l = false;
				}
				else if(gtype.match("select")){
					alert("Select Type ..!");
                    document.getElementById("gtype").focus();
					l = false;
				}
                else {
                    if(gtype == "C"){
                        cus_controller_code = document.getElementById("cus_controller_code").value;
                        cus_prepayment_code = document.getElementById("cus_prepayment_code").value;
                        if(cus_controller_code == "select"){
                            alert("Please select Customer Controller Account ..!");
                            document.getElementById("cus_controller_code").focus();
                            l = false;
                        }
                        else if(cus_prepayment_code == "select"){
                            alert("Please select Customer Pre-payment Account ..!");
                            document.getElementById("cus_prepayment_code").focus();
                            l = false;
                        }
                        else{ }
                    }
                    else if(gtype == "S"){
                        sup_controller_code = document.getElementById("sup_controller_code").value;
                        sup_prepayment_code = document.getElementById("sup_prepayment_code").value;
                        if(sup_controller_code == "select"){
                            alert("Please select Supplier Controller Account ..!");
                            document.getElementById("sup_controller_code").focus();
                            l = false;
                        }
                        else if(sup_prepayment_code == "select"){
                            alert("Please select Supplier Pre-payment Account ..!");
                            document.getElementById("sup_prepayment_code").focus();
                            l = false;
                        }
                        else{ }
                    }
                    else if(gtype == "S&C"){
                        cus_controller_code = document.getElementById("cus_controller_code").value;
                        cus_prepayment_code = document.getElementById("cus_prepayment_code").value;
                        sup_controller_code = document.getElementById("sup_controller_code").value;
                        sup_prepayment_code = document.getElementById("sup_prepayment_code").value;
                        if(cus_controller_code == "select"){
                            alert("Please select Customer Controller Account ..!");
                            document.getElementById("cus_controller_code").focus();
                            l = false;
                        }
                        else if(cus_prepayment_code == "select"){
                            alert("Please select Customer Pre-payment Account ..!");
                            document.getElementById("cus_prepayment_code").focus();
                            l = false;
                        }
                        else if(sup_controller_code == "select"){
                            alert("Please select Supplier Controller Account ..!");
                            document.getElementById("sup_controller_code").focus();
                            l = false;
                        }
                        else if(sup_prepayment_code == "select"){
                            alert("Please select Supplier Pre-payment Account ..!");
                            document.getElementById("sup_prepayment_code").focus();
                            l = false;
                        }
                        else{ }
                    }
                }
                if(l == true){
                    return true;
                }
				else {
					return false;
				}
			}
            function show_acc(){
                var gtype = document.getElementById("gtype").value;
                if(gtype == "C"){
                    document.getElementById("supplier_acc").style.visibility = "hidden";
                    document.getElementById("customer_acc").style.visibility = "visible";
                }
                else if(gtype == "S"){
                    document.getElementById("customer_acc").style.visibility = "hidden";
                    document.getElementById("supplier_acc").style.visibility = "visible";
                }
                else if(gtype == "S&C"){
                    document.getElementById("customer_acc").style.visibility = "visible";
                    document.getElementById("supplier_acc").style.visibility = "visible";
                }
                else{
                    document.getElementById("customer_acc").style.visibility = "hidden";
                    document.getElementById("supplier_acc").style.visibility = "hidden";
                }
            }
            show_acc();
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