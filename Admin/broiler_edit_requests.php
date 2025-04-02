<?php
//broiler_edit_requests.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['requests'];
$uri = explode("/",$_SERVER['REQUEST_URI']); $url2 = explode("?",$uri[1]); $href = $url2[0];
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
        
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $id = $_GET['id'];
        $sql = "SELECT * FROM `ticket_management_system` WHERE `trnum` = '$id' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $edt_trnum = $row['trnum'];
            $edt_date = $row['date'];
            $edt_client_name = $row['client_name'];
            $edt_ticket_type = $row['ticket_type'];
            $edt_ticket_name = $row['ticket_name'];
            $edt_file_name = $row['file_name'];
            $edt_requirement_list1 = $row['requirement_list1'];
            $edt_link_details = $row['link_details'];
            $edt_requirement_list2 = $row['requirement_list2'];
            $edt_received_from = $row['received_from'];
            $edt_received_by = $row['received_by'];
            $edt_assignee = $row['assignee'];
            $edt_development_type = $row['development_type'];
            $edt_file_path1 = $row['file_path1'];
            $edt_file_path2 = $row['file_path2'];
            $edt_file_path3 = $row['file_path3'];
            $edt_file_path4 = $row['file_path4'];
            $edt_file_path5 = $row['file_path5'];
            $edt_remarks = $row['remarks'];
            $edt_ticket_priority = $row['ticket_priority'];
            $edt_ticket_status = $row['ticket_status'];
            $edt_current_status = $row['current_status'];
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Request</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="m-0 p-0 col-md-12">
                                <form action="broiler_modify_requests.php" method="post" role="form" onsubmit="return checkval()" enctype="multipart/form-data">
                                    <div class="m-0 p-0 col-md-12">
                                        <div class="m-0 p-0 row">
                                            <div class="form-group">
                                                <label>Date</label>
                                                <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($edt_date)); ?>" style="width:90px;" readonly >
                                            </div>&ensp;
                                            <div class="form-group">
                                                <label>Clients<b style="color:red;">&nbsp;*</b></label>
                                                <select name="client_name" id="client_name" class="form-control select2" style="width:auto;">
                                                    <option value="all" <?php if($edt_client_name == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <?php
                                                     $sql1 = "SELECT DISTINCT(dblist) as dbname FROM `log_useraccess` WHERE `account_access` IN ('BTS','CTS') ORDER BY `dbname` ASC";
                                                     $query1 = mysqli_query($conns,$sql1);
                                                     while($row1 = mysqli_fetch_assoc($query1)){
                                                    ?>
                                                    <option value="<?php echo $row1['dbname']; ?>" <?php if($edt_client_name == $row1['dbname']){ echo "selected"; } ?>><?php echo $row1['dbname']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>&ensp;
                                            <div class="form-group">
                                                <label>Transaction Type<b style="color:red;">&nbsp;*</b></label>
                                                <input type="text" name="transaction_type" id="transaction_type" class="form-control" value="<?php echo $edt_ticket_name; ?>" style="width:130px;">
                                            </div>&ensp;
                                            <div class="form-group">
                                                <label>Assign To<b style="color:red;">&nbsp;*</b></label>
                                                <select name="work_for[]" id="work_for[]" class="form-control select2 multiple" style="width:130px;">
                                                    <option value="Mallikarjuna" <?php if($edt_assignee == "Mallikarjuna"){ echo "selected"; } ?>>Mallikarjuna</option>
                                                    <option value="Mustafa" <?php if($edt_assignee == "Mustafa"){ echo "selected"; } ?>>Mustafa</option>
                                                    <option value="Parasuram" <?php if($edt_assignee == "Parasuram"){ echo "selected"; } ?>>Parasuram</option>
                                                    <option value="Pramodh" <?php if($edt_assignee == "Pramodh"){ echo "selected"; } ?>>Pramodh</option>
                                                    <option value="Suresh" <?php if($edt_assignee == "Suresh"){ echo "selected"; } ?>>Suresh</option>
                                                </select>
                                            </div>&ensp;
                                            <div class="form-group">
                                                <label>Priority<b style="color:red;">&nbsp;*</b></label>
                                                <select name="priority" id="priority" class="form-control select2" style="width:130px;">
                                                    <option value="low" <?php if($edt_ticket_priority == "low"){ echo "selected"; } ?>>-Low-</option>
                                                    <option value="medium" <?php if($edt_ticket_priority == "medium"){ echo "selected"; } ?>>-Medium-</option>
                                                    <option value="high" <?php if($edt_ticket_priority == "high"){ echo "selected"; } ?>>-High-</option>
                                                    <option value="urgent" <?php if($edt_ticket_priority == "urgent"){ echo "selected"; } ?>>-Urgent-</option>
                                                </select>
                                            </div>&ensp;
                                            <div class="form-group">
                                                <label>Development Type<b style="color:red;">&nbsp;*</b></label>
                                                <select name="development_type" id="development_type" class="form-control select2" style="width:180px;">
                                                    <?php
                                                     $sql1 = "SELECT * FROM `ticket_transaction_types` WHERE `active` = '1' ORDER BY `sort_order` ASC";
                                                     $query1 = mysqli_query($conn,$sql1);
                                                     while($row1 = mysqli_fetch_assoc($query1)){
                                                    ?>
                                                    <option value="<?php echo $row1['code']; ?>" <?php if($edt_development_type == $row1['code']){ echo "selected"; } ?>><?php echo $row1['description']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>&ensp;
                                            <div class="form-group">
                                                <label>DB Type<b style="color:red;">&nbsp;*</b></label>
                                                <select name="db_type" id="db_type" class="form-control select2" style="width:130px;">
                                                    <option value="admin" <?php if($edt_requirement_list2 == "admin"){ echo "selected"; } ?>>-Admin-</option>
                                                    <option value="broiler" <?php if($edt_requirement_list2 == "broiler"){ echo "selected"; } ?>>-Broiler-</option>
                                                    <option value="breeder" <?php if($edt_requirement_list2 == "breeder"){ echo "selected"; } ?>>-Breeder-</option>
                                                    <option value="chicken" <?php if($edt_requirement_list2 == "chicken"){ echo "selected"; } ?>>-Chicken-</option>
                                                    <option value="layer" <?php if($edt_requirement_list2 == "layer"){ echo "selected"; } ?>>-Layer-</option>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-md-12" align="center">
                                        <div class="row" align="center">
                                            <div class="col-md-6 form-group">
                                                <label>Work<b style="color:red;">&nbsp;*</b></label>
                                                <textarea name="work_details" id="work_details" class="form-control"><?php echo $edt_requirement_list1; ?></textarea>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Link</label>
                                                <input type="text" name="link_details" id="link_details" class="form-control" value="<?php echo $edt_link_details; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-3"></div>
                                            <div class="col-6">
                                                <label>Remarks</label>
                                                    <textarea name="remarks" id="remarks" class="form-control"><?php echo $edt_remarks; ?></textarea>
                                                </div>
                                            <div class="col-3"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" align="center">
                                        <div class="row" align="center">
                                            <div class="col-md-3">
                                                    <label>Reference-1</label>
                                                    <input type="file" name="file_path_1" id="file_path_1" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group1">
                                                    <label>Reference-2</label>
                                                    <input type="file" name="file_path_2" id="file_path_2" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group1">
                                                    <label>Reference-3</label>
                                                    <input type="file" name="file_path_3" id="file_path_4" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group1">
                                                    <label>Reference-4</label>
                                                    <input type="file" name="file_path_4" id="file_path_4" class="form-control">
                                                </div>
                                            </div>
                                            <!--<div class="col-md-3">
                                                <div class="form-group1">
                                                    <label>Reference-5</label>
                                                    <input type="file" name="file_path_5" id="file_path_5" class="form-control">
                                                </div>
                                            </div>-->
                                        </div>
                                    </div>
                                    <br/><br/><br/>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:visible;"-->
                                            <label>id<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="idvalue" id="idvalue" value="<?php echo $id; ?>" readonly >
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-green">Update</button>&ensp;
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
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
                });
                var transaction_type = document.getElementById("transaction_type").value;
                var work_details = document.getElementById("work_details").value;
                var l = true;
                if(transaction_type == ""){
                    Toast.fire({ icon: 'error', title: 'Enter Transaction Type.' });
                    document.getElementById("transaction_type").focus();
                    l = false;
                }
                else if(work_details == ""){
                    Toast.fire({ icon: 'error', title: 'Enter appropriate Work Details.' });
                    document.getElementById("work_details").focus();
                    l = false;
                }
                else{ }
                if(l == true){ return true; }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
					return false;
                }
            }
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_requests.php?ccid='+ccid;
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