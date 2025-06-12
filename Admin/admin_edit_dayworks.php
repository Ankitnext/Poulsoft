<?php
//broiler_add_masterreportfields.php
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
        // $sql = "SELECT DISTINCT(dblist) as dbname FROM `log_useraccess` WHERE `account_access` IN ('BTS','ATS') ORDER BY `dbname` ASC"; $query = mysqli_query($conns,$sql);
        // while($row = mysqli_fetch_assoc($query)){ $database_array_list[$row['dbname']] = $row['dbname']; }
        // $sql = "SELECT * FROM `master_reportfields` WHERE `active` = '1' ORDER BY `field_name` ASC"; $query = mysqli_query($sconn,$sql);
        // while($row = mysqli_fetch_assoc($query)){
        //     $field_name_array_list[$row['id']] = $row['field_name'];
        //     $field_href_array_list[$row['id']] = $row['field_href'];
        //     $field_id_array_list[$row['id']] = $row['id'];
        // }

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
     <link href="datepicker/jquery-ui.css" rel="stylesheet">
    </head>
    <body class="m-0 hold-transition">
    <?php
        $ids = $_GET['id'];
        $sql = "SELECT * FROM `emp_daily_works` WHERE `id` = '$ids' AND `dflag` = '0'";
        $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){          
            $idk = $row['id'];
            $date = $row['date'];
            $tic_no = $row['tic_no'];
            $mod_type = $row['mod_type'];
            $cl_name = $row['cl_name'];
            $wok_type = $row['wok_type'];
            $gdate = $row['gdate'];
            $fl_type = $row['fl_type'];
            $fl_link = $row['fl_link'];
            $wdate = $row['wdate'];
            $t_taken = $row['t_taken'];
            $statuses = $row['statuses'];
            $remarks = $row['remarks'];
           
        }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Purchase Return</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="admin_modify_dayworks.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row">
                                       <div class="form-group" style="width:120px;">
                                            <label for="date">Date</label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" onchange="" readonly />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="">
                                            <label>Ticket No.<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="tic_no" id="tic_no" class="form-control" value="<?php echo $tic_no; ?>" >
                                        </div>&ensp;
                                        <div class="form-group"> 
                                            <label>Module<b style="color:red;">&nbsp;*</b></label>
                                            <select name="mod_type" id="mod_type" class="form-control select2" style="width:180px;">
                                                <option value="select" <?php if($mod_type == "select") { echo "selected";} ?>>select</option>
                                                <option value="CTS" <?php if($mod_type == "CTS") { echo "selected";} ?>>-CTS-</option>
                                                <option value="BTS" <?php if($mod_type == "BTS") { echo "selected";} ?>>-BTS-</option>
                                            </select>
                                        </div>&ensp;
                                        <div class="form-group col-md-1" style="">
                                            <label>Client Name<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="cl_name" id="cl_name" class="form-control" value="<?php echo $cl_name; ?>" >
                                        </div>&ensp;
                                        <div class="form-group"> 
                                            <label>Work Type<b style="color:red;">&nbsp;*</b></label>
                                            <select name="wok_type" id="wok_type" class="form-control select2" style="width:180px;">
                                                <option value="select" <?php if($wok_type == "BTS") { echo "selected";} ?>>select</option>
                                                <option value="new" <?php if($wok_type == "new") { echo "selected";} ?>>-New-</option>
                                                <option value="modify" <?php if($wok_type == "modify") { echo "selected";} ?>>-Modified-</option>
                                                <option value="problem" <?php if($wok_type == "problem") { echo "selected";} ?>>-Problem-</option>
                                            </select>
                                        </div>&ensp;
                                        <div class="form-group" style="width:120px;">
                                            <label for="date">Given Date</label>
                                            <input type="text" name="gdate" id="gdate" class="form-control datepicker" value="<?php echo $gdate; ?>" style="width:110px;" onchange="" readonly />
                                        </div>&ensp;
                                        <div class="form-group"> 
                                            <label>File Type<b style="color:red;">&nbsp;*</b></label>
                                            <select name="fl_type" id="fl_type" class="form-control select2" style="width:180px;">
                                                <option value="select" <?php if($fl_type == "select") { echo "selected";} ?>>select</option>
                                                <option value="Master" <?php if($fl_type == "Master") { echo "selected";} ?>>-Master-</option>
                                                <option value="Transaction" <?php if($fl_type == "Transaction") { echo "selected";} ?>>-Transaction-</option>
                                                <option value="Reports" <?php if($fl_type == "Reports") { echo "selected";} ?>>-Reports-</option>
                                            </select>
                                        </div>&ensp;
                                        <div class="form-group col-md-1" style="">
                                            <label>File Link<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="fl_link" id="fl_link" class="form-control" value="<?php echo $fl_link; ?>" >
                                        </div>&ensp;
                                        <div class="form-group" style="width:120px;">
                                            <label for="date">Work Date</label>
                                            <input type="text" name="wdate" id="wdate" class="form-control datepicker" value="<?php echo $wdate; ?>" style="width:110px;" onchange="" readonly />
                                        </div>&ensp;
                                        <div class="form-group col-md-1" style="">
                                            <label>Time Taken<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="t_taken" id="t_taken" class="form-control" value="<?php echo $t_taken; ?>" >
                                        </div>&ensp;
                                        <div class="form-group col-md-1" style="">
                                            <label>Status<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="status" id="status" class="form-control" value="<?php echo $statuses; ?>" >
                                        </div>&ensp;
                                        <div class="form-group col-md-1" style="">
                                            <label>Remarks<b style="color:red;">&ensp;*</b></label>
                                            <textarea name="remarks" id="remarks" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"><?php echo $remarks; ?></textarea>

                                        </div>&ensp;
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="" >
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>Id<b style="color:red;">&ensp;*</b></label>
                                            <input type="idvalue" name="idvalue" id="idvalue" class="form-control" value="<?php echo $idk; ?>" >
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
                window.location.href = 'admin_display_dayworks.php?ccid'+ccid;
            }
            function checkval(){
				update_ebtn_status(1);
                var l = true;
               
                    var incr = document.getElementById("incr").value;
                    var tic_no = to_item = ""; var to_qty = disposed_qty = 0;
                    
                            var tic_no = document.getElementById("tic_no").value;
                            var mod_type = document.getElementById("mod_type").value;
                            var cl_name = document.getElementById("cl_name").value; 
                            var wok_type = document.getElementById("wok_type").value; 
                            var gdate = document.getElementById("gdate").value; 
                            var fl_type = document.getElementById("fl_type").value; 
                            var fl_link = document.getElementById("fl_link").value; 
                            var wdate = document.getElementById("wdate").value; 
                            var t_taken = document.getElementById("t_taken").value; 
                            var statuses = document.getElementById("statuses").value; 
                            var remarks = document.getElementById("remarks").value; 
                           // disposed_qty = document.getElementById("disposed_qty").value; if(disposed_qty == ""){ disposed_qty = 0; }
                            
                            if(tic_no == ""){
                                alert("Please Enter Ticket Number  ");
                                document.getElementById("tic_no").focus();
                                l = false;
                            }
                            else if(mod_type == "" || mod_type == "select"){
                                alert("Please select Module Type  ");
                                document.getElementById("mod_type").focus();
                                l = false;
                            }
                            else if(wok_type == "" || wok_type == "select"){
                                alert("Please select Work Type  ");
                                document.getElementById("wok_type").focus();
                                l = false;
                            }
                            else if(fl_type == "" || fl_type == "select"){
                                alert("Please select File Type  ");
                                document.getElementById("fl_type").focus();
                                l = false;
                            }
                            else if(cl_name == ""){
                                alert("Please Enter Client Name  ");
                                document.getElementById("cl_name").focus();
                                l = false;
                            }
                            else if(statuses == ""){
                                alert("Please Enter Status  ");
                                document.getElementById("statuses").focus();
                                l = false;
                            }
                            else if(t_taken == ""){
                                alert("Please Enter Time Taken  ");
                                document.getElementById("t_taken").focus();
                                l = false;
                            }
                            else if(fl_link == ""){
                                alert("Please Enter File Link  ");
                                document.getElementById("fl_link").focus();
                                l = false;
                            }
                            else if(wdate == ""){
                                alert("Please Enter Work Date  ");
                                document.getElementById("wdate").focus();
                                l = false;
                            }
                            else if(gdate == ""){
                                alert("Please Enter Given Date  ");
                                document.getElementById("gdate").focus();
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