<?php
//broiler_add_masterreportfields.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['masterreportfields'];
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
        $sql = "SELECT DISTINCT(dblist) as dbname FROM `log_useraccess` WHERE `account_access` IN ('BTS','ATS') ORDER BY `dbname` ASC"; $query = mysqli_query($conns,$sql);
        while($row = mysqli_fetch_assoc($query)){ $database_array_list[$row['dbname']] = $row['dbname']; }
        $sql = "SELECT * FROM `master_reportfields` WHERE `active` = '1' ORDER BY `field_name` ASC"; $query = mysqli_query($sconn,$sql);
        while($row = mysqli_fetch_assoc($query)){
            $field_name_array_list[$row['id']] = $row['field_name'];
            $field_href_array_list[$row['id']] = $row['field_href'];
            $field_id_array_list[$row['id']] = $row['id'];
        }

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
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Purchase Return</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_masterreportfields.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <!--<div class="form-group">
                                            <label>Type<b style="color:red;">&nbsp;*</b></label>
                                            <select name="transaction_type" id="transaction_type" class="form-control select2" style="width:180px;">
                                                <option value="select">select</option>
                                                <option value="insert">-Insert-</option>
                                                <option value="modify">-Modify-</option>
                                            </select>
                                        </div>&ensp;-->
                                        <div class="form-group">
                                            <label>Databases<b style="color:red;">&nbsp;*</b></label>
                                            <select name="database[]" id="database" class="form-control select2" style="width:380px;" multiple >
                                                <option value="all">-All-</option>
                                                <?php foreach($database_array_list as $dcode){ ?><option value="<?php echo $dcode; ?>"><?php echo $database_array_list[$dcode]; ?></option><?php } ?>
                                            </select>
                                        </div>&ensp;
                                        <div class="form-group">
                                            <label>Report Name<b style="color:red;">&nbsp;*</b></label>
                                            <select name="report_name" id="report_name" class="form-control select2" style="width:280px;" onchange="fatch_field_details(this.id);">
                                                <option value="select">select</option>
                                                <?php foreach($field_id_array_list as $fcode){ ?><option value="<?php echo $fcode; ?>"><?php echo $field_name_array_list[$fcode]; ?></option><?php } ?>
                                            </select>
                                        </div>&ensp;
                                        <div class="form-group">
                                            <label>File URL<b style="color:red;">&nbsp;*</b></label>
                                            <select name="file_url" id="file_url" class="form-control select2" style="width:350px;" onchange="fatch_field_details(this.id);">
                                                <option value="select">select</option>
                                                <?php foreach($field_id_array_list as $fcode){ ?><option value="<?php echo $fcode; ?>"><?php echo $field_href_array_list[$fcode]; ?></option><?php } ?>
                                            </select>
                                        </div>&ensp;
                                        <!--<div class="form-group">
                                            <label>Username<b style="color:red;">&nbsp;*</b></label>
                                            <select name="user_name" id="user_name" class="form-control select2" style="width:180px;">
                                                <option value="all">-All-</option>
                                            </select>
                                        </div>&ensp;-->
                                        <div class="form-group"><br/>
                                            <button type="button" name="fetch_details" id="fetch_details" class="btn btn-sm bg-danger" onclick="fetch_field_details()">Fetch</button>
                                        </div>&ensp;
                                    </div><br/>
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-6" id="row_body"></div>
                                        <div class="col-md-3"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="" >
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" align="center">
                                            <button type="submit" name="submit" id="submit" class="btn btn-sm bg-green">Submit</button>&ensp;
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
                window.location.href = 'broiler_display_masterreportfields.php?ccid'+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var Toast = Swal.mixin({
                toast: true,
                position: 'top-right',
                showConfirmButton: false,
                timer: 3000
                });
                
                var database = ""; var l = true;
                for(var option of document.getElementById('database').options){
                    if(option.selected){ if(database == ""){ database = option.value; } else{ database = database+",\n"+option.value; } }
                }
                var report_name = document.getElementById("report_name").value;
                var file_url = document.getElementById("file_url").value;
                var incr = document.getElementById("incr").value;
                
                if(database.match("select") || database.length == 0){
                    Toast.fire({ icon: 'error', title: 'Select Database.' });
                    document.getElementById("database").focus();
                    l = false;
                }
                else if(report_name.match("select")){
                    Toast.fire({ icon: 'error', title: 'Select Report Name.' });
                    document.getElementById("report_name").focus();
                    l = false;
                }
                else if(file_url.match("select")){
                    Toast.fire({ icon: 'error', title: 'Select File URL.' });
                    document.getElementById("file_url").focus();
                    l = false;
                }
                else if(incr == "" || incr < 0){
                    Toast.fire({ icon: 'error', title: 'Select Fetch details.' });
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
            function fatch_field_details(x) {
                var code = document.getElementById(x);
                if(x.match("file_url")){
                    $('#report_name').select2();
                    document.getElementById("report_name").value = code.value;
                    $('#report_name').select2();
                }
                else if(x.match("report_name")){
                    $('#file_url').select2();
                    document.getElementById("file_url").value = code.value;
                    $('#file_url').select2();
                }
                else{

                }
            }
            function fetch_field_details(){
                var Toast = Swal.mixin({
                toast: true,
                position: 'top-right',
                showConfirmButton: false,
                timer: 3000
                });
                //var transaction_type = document.getElementById("transaction_type").value;
                var database = "";
                for(var option of document.getElementById('database').options){
                    if(option.selected){ if(database == ""){ database = option.value; } else{ database = database+",\n"+option.value; } }
                }
                var report_name = document.getElementById("report_name").value;
                var file_url = document.getElementById("file_url").value;
                /*if(transaction_type.match("select")){
                    Toast.fire({ icon: 'error', title: 'Select Transaction Type.' });
                    document.getElementById("transaction_type").focus();
                }
                else*/
                if(database.match("select") || database.length == 0){
                    Toast.fire({ icon: 'error', title: 'Select Database.' });
                    document.getElementById("database").focus();
                }
                else if(report_name.match("select")){
                    Toast.fire({ icon: 'error', title: 'Select Report Name.' });
                    document.getElementById("report_name").focus();
                }
                else if(file_url.match("select")){
                    Toast.fire({ icon: 'error', title: 'Select File URL.' });
                    document.getElementById("file_url").focus();
                }
                //else if(transaction_type.match("modify") && database.match("all")){
                    //Toast.fire({ icon: 'warning', title: 'Select Modify option to all Databse \n If you save this transaction, this will update in all database.' });
                //}
                else{
                    document.getElementById("row_body").innerHTML = "";
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_adminmasterreport.php?database="+database+"&report_name="+report_name+"&file_url="+file_url;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var item_list = this.responseText;
                            if(item_list.length > 0){
                                var elist = item_list.split("@");
                                $('#row_body').append(elist[0]);
                                document.getElementById("incr").value = elist[1];
                            }
                            else{
                                Toast.fire({ icon: 'error', title: 'Invalid request \n Kindly check and try again ...!' });
                            }
                        }
                    }
                }
            }
            document.addEventListener("keydown", (e) => { if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function (){ $('#submit').click(); }); } } else{ } });
            function validatename(x) { expr = /^[a-zA-Z0-9 (.&)_-]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^a-zA-Z0-9 (.&)_-]/g, ''); } document.getElementById(x).value = a; }
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