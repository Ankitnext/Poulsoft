<?php
//admin_add_dayworks.php
include "newConfig.php";
date_default_timezone_set("Asia/Kolkata");
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['dayworks'];
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
        $date = date("d.m.Y");
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
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Add Daily Works</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="admin_save_dayworks.php" method="post" role="form" enctype="multipart/form-data" onsubmit="return checkval()" >
                                    <!-- <div class="row">
                                       <div class="form-group" style="width:120px;">
                                            <label for="date">Date</label>
                                            <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" onchange="" readonly />
                                        </div>
                                    </div> -->
                                    <div class="row row_body2">
                                        <table class="p-1 table1" style="width:auto;" align="center">
                                            <thead>
                                                <tr>
                                                    <th colspan="4" style="text-align:center;">
                                                        <div class="row justify-content-center align-items-center">
                                                            <div class="form-group" style="width:120px;">
                                                                <label for="date">Date</label>
                                                                <input type="text" name="date" id="date" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" onchange="clear_data();" readonly />
                                                            </div>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <thead>
                                                <tr>
                                                    <th style="text-align:center;"><label>Ticket No.<b style="color:red;">&nbsp;*</b></label></th>
                                                    <th style="text-align:center;"><label>Module</label></th>
                                                    <th style="text-align:center;"><label>Client Name</label></th>
                                                    <th style="text-align:center;"><label>Work Type</label></th>
                                                    <th style="text-align:center;"><label>Given Date</label></th>
                                                    <th style="text-align:center;"><label>File Type</label></th>
                                                    <th style="text-align:center;"><label>File Link</label></th>
                                                    <th style="text-align:center;"><label>Work Date</label></th>
                                                    <th style="text-align:center;"><label>Time Taken(in min)</label></th>
                                                    <th style="text-align:center;"><label>Status</label></th>
                                                    <th style="text-align:center;"><label>Remarks</label></th>
                                                    <th style="text-align:center;"><label>Attach-1</label></th>
                                                    <th style="text-align:center;"><label>Attach-2</label></th>
                                                    <th style="text-align:center;"><label>Attach-3</label></th>
                                                    <th style="visibility:hidden;"><label>Action</label></th>
                                                    <th style="visibility:hidden;"><label>SQ</label></th>
                                                    <th style="visibility:hidden;"><label>SP</label></th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">
                                                <tr>
                                                    <td ><input type="text" name="tic_no[]" id="tic_no[0]" class="form-control" value="" style="width:127px;"></td>
                                                    <td><select name="mod_type[]" id="mod_type[0]" class="form-control select2" style="width:180px;"><option value="select">select</option><option value="CTS">-CTS-</option><option value="BTS">-BTS-</option></select></td>
                                                    <td ><input type="text" name="cl_name[]" id="cl_name[0]" class="form-control" value="" style="width:127px;"></td>
                                                    <td><select name="wok_type[]" id="wok_type[0]" class="form-control select2" style="width:180px;"><option value="select">select</option><option value="new">-New-</option><option value="modify">-Modified-</option><option value="problem">-Problems-</option></select></td>
                                                    <td><input type="text" name="gdate[]" id="gdate[0]" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" onchange="" readonly /></td>
                                                    <td><select name="fl_type[]" id="fl_type[0]" class="form-control select2" style="width:180px;"><option value="select">select</option><option value="Master">-Master-</option><option value="Transaction">-Transaction-</option><option value="Reports">-Reports-</option></select></td>
                                                    <td ><input type="text" name="fl_link[]" id="fl_link[0]" class="form-control" value="" style="width:127px;"></td>
                                                    <td><input type="text" name="wdate[]" id="wdate[0]" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" onchange="" readonly /></td>
                                                    <td ><input type="text" name="t_taken[]" id="t_taken[0]" class="form-control" value="" style="width:127px;"></td>
                                                    <td><input type="text" name="statuses[]" id="statuses[0]" class="form-control" value=""  style="width:127px;"></td>
                                                    <td><textarea name="remarks[]" id="remarks[0]" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>
                                                    <td><input type="file" name="logo_image[0]" id="image[0]" class="form-control"/></td>
                                                    <td><input type="file" name="logo_image2[0]" id="image2[0]" class="form-control"/></td>
                                                    <td><input type="file" name="logo_image3[0]" id="image3[0]" class="form-control"/></td>

                                                    <td id="action[0]" style="width:80px;"><a href="javascript:void(0);" id="addrow[0]" onclick="create_row(this.id);" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                    <td style="visibility:hidden;"><input type="text" name="stk_qty[]" id="stk_qty[0]" class="form-control text-right" style="width:20px;" readonly /></td>
                                                    <td style="visibility:hidden;"><input type="text" name="stk_prc[]" id="stk_prc[0]" class="form-control text-right" style="width:20px;" readonly /></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr> </tr>
                                            </tfoot>
                                        </table>
                                    </div><br/>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:30px;">
                                            <label>IN</label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0" style="padding:0;width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>EB</label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="padding:0;width:20px;" readonly />
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
                var date = document.getElementById("date").value;
                
                if(date == ""){
                    alert("Please select Date");
                    document.getElementById("date").focus();
                    l = false;
                }
                else{
                    var incr = document.getElementById("incr").value;
                    var tic_no = to_item = ""; var to_qty = disposed_qty = 0;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            e = d + 1;
                            tic_no = document.getElementById("tic_no["+d+"]").value;
                            mod_type = document.getElementById("mod_type["+d+"]").value;
                            cl_name = document.getElementById("cl_name["+d+"]").value; 
                            wok_type = document.getElementById("wok_type["+d+"]").value; 
                            gdate = document.getElementById("gdate["+d+"]").value; 
                            fl_type = document.getElementById("fl_type["+d+"]").value; 
                            fl_link = document.getElementById("fl_link["+d+"]").value; 
                            wdate = document.getElementById("wdate["+d+"]").value; 
                            t_taken = document.getElementById("t_taken["+d+"]").value; 
                            statuses = document.getElementById("statuses["+d+"]").value; 
                            remarks = document.getElementById("remarks["+d+"]").value; 
                           // disposed_qty = document.getElementById("disposed_qty["+d+"]").value; if(disposed_qty == ""){ disposed_qty = 0; }
                            
                            if(tic_no == ""){
                                alert("Please Enter Ticket Number in row: "+e);
                                document.getElementById("tic_no["+d+"]").focus();
                                l = false;
                            }
                            else if(mod_type == "" || mod_type == "select"){
                                alert("Please select Module Type in row: "+e);
                                document.getElementById("mod_type["+d+"]").focus();
                                l = false;
                            }
                            else if(wok_type == "" || wok_type == "select"){
                                alert("Please select Work Type in row: "+e);
                                document.getElementById("wok_type["+d+"]").focus();
                                l = false;
                            }
                            else if(fl_type == "" || fl_type == "select"){
                                alert("Please select File Type in row: "+e);
                                document.getElementById("fl_type["+d+"]").focus();
                                l = false;
                            }
                            else if(cl_name == ""){
                                alert("Please Enter Client Name in row: "+e);
                                document.getElementById("cl_name["+d+"]").focus();
                                l = false;
                            }
                            else if(statuses == ""){
                                alert("Please Enter Status in row: "+e);
                                document.getElementById("statuses["+d+"]").focus();
                                l = false;
                            }
                            else if(t_taken == ""){
                                alert("Please Enter Time Taken in row: "+e);
                                document.getElementById("t_taken["+d+"]").focus();
                                l = false;
                            }
                            else if(fl_link == ""){
                                alert("Please Enter File Link in row: "+e);
                                document.getElementById("fl_link["+d+"]").focus();
                                l = false;
                            }
                            else if(wdate == ""){
                                alert("Please Enter Work Date in row: "+e);
                                document.getElementById("wdate["+d+"]").focus();
                                l = false;
                            }
                            else if(gdate == ""){
                                alert("Please Enter Given Date in row: "+e);
                                document.getElementById("gdate["+d+"]").focus();
                                l = false;
                            }
                            else{ }
                        }
                    }
                }
                
                if(l == true){
                    return true;
                }
                else{
                    update_ebtn_status(0);
                    return false;
                }
			}
          
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<tr id="row_no['+d+']">';
                html += '<td><input type="text" name="tic_no[]" id="tic_no['+d+']" class="form-control" value="" ></td>';
                html += '<td><select name="mod_type[]" id="mod_type['+d+']" class="form-control select2" style="width:180px;"><option value="select">select</option><option value="insert">-CTS-</option><option value="modify">-BTS-</option></select></td>';
                html += '<td><input type="text" name="cl_name[]" id="cl_name['+d+']" class="form-control" value="" ></td>';
                html += '<td><select name="wok_type[]" id="wok_type['+d+']" class="form-control select2" style="width:180px;"><option value="select">select</option><option value="new">-New-</option><option value="modify">-Modified-</option><option value="problem">-Problems-</option></select></td>';
                html += ' <td><input type="text" name="gdate[]" id="gdate['+d+']" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" onchange="" readonly /></td>';
                html += ' <td><select name="fl_type[]" id="fl_type['+d+']" class="form-control select2" style="width:180px;"><option value="select">select</option><option value="new">-Master-</option><option value="modify">-Transaction-</option><option value="problem">-Reports-</option></select></td>';
                html += '  <td><input type="text" name="fl_link[]" id="fl_link['+d+']" class="form-control" value="" ></td>';
                html += ' <td><input type="text" name="wdate[]" id="wdate['+d+']" class="form-control datepicker" value="<?php echo $date; ?>" style="width:110px;" onchange="" readonly /></td>';
                html += '  <td><input type="text" name="t_taken[]" id="t_taken['+d+']" class="form-control" value="" ></td>';
                html += '  <td><input type="text" name="statuses[]" id="statuses['+d+']" class="form-control" value="" ></td>';
                html += ' <td><textarea name="remarks[]" id="remarks['+d+']" class="form-control" style="padding:0;width:150px;height:28px;" onkeyup="validatename(this.id);"></textarea></td>';
                html += ' <td><input type="file" name="logo_image['+d+']" id="image['+d+']" class="form-control"/></td>';
                html += ' <td><input type="file" name="logo_image2['+d+']" id="image2['+d+']" class="form-control"/></td>';
                html += ' <td><input type="file" name="logo_image3['+d+']" id="image3['+d+']" class="form-control"/></td>';
                html += '<td id="action['+d+']" style="width:80px;"><a href="javascript:void(0);" id="addrow['+d+']" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>';
                html += '</tr>';
                $('#tbody').append(html);
                $('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
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