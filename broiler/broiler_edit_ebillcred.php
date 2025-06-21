<?php
//broiler_edit_ebillcred.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['ebillcred'];
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
        $sql = "SELECT * FROM `broiler_ebill_credentials` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
          
            $einvusername = $row['einvusername'];
            $einvpassword = $row['einvpassword'];
            $gstin = $row['gstin'];
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
                            <div class="float-left"><h3 class="card-title">Edit Ebill Credential</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_modify_ebillcred.php" method="post" role="form" onsubmit="return checkval()">
                                     <div class="row" style="display:flex; justify-content: center;">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                 <label>E_User</label>
									            <input type="text" name="euser" id="euser" class="form-control" value="<?php echo $einvusername; ?>" placeholder="Enter E_User" onkeyup="">
                                            </div>
                                        </div>
                                       
                                         <div class="col-md-2">
                                            <div class="form-group">
                                                 <label>E_Password</label>
									            <input type="password" name="epass" id="epass" class="form-control" value="<?php echo $einvpassword; ?>" placeholder="Enter e_Password" onkeyup="">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                 <label>GST_No</label>
									            <input type="text" name="gst_no" id="gst_no" class="form-control" value="<?php echo $gstin; ?>" placeholder="Enter GST Inv" onkeyup="">
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
                window.location.href = 'broiler_display_ebillcred.php?ccid='+ccid;
            }
			function checkval(){
				var euser = document.getElementById("euser").value;
				var epass = document.getElementById("epass").value;
				var gst_no = document.getElementById("gst_no").value;
                
                var l = true;
				if(euser == ""){
					alert("Enter E_User ..!");
                    document.getElementById("euser").focus();
					l = false;
				}
				else if(epass == ""){
					alert("Enter Password ..!");
                    document.getElementById("epass").focus();
					l = false;
				}
				else if(gst_no == ""){
					alert("Enter gst_no ..!");
                    document.getElementById("gst_no").focus();
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