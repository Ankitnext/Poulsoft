<?php
//broiler_edit_daterange.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['daterange'];
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
        $database_name = $_SESSION['dbase'];
        $sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$database_name' AND `flag` = '1' AND `dflag` = '0' ORDER BY `username` ASC";
        $query = mysqli_query($conns, $sql); $emp_code = $emp_name = array();
        while ($row = mysqli_fetch_assoc($query)) { $emp_code[$row['empcode']] = $row['empcode']; $emp_name[$row['empcode']] = $row['username']; }

        $sql = "SELECT * FROM `date_range_master` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($sconn,$sql); $file_url = array();
        while($row = mysqli_fetch_assoc($query)){ $file_url[$row['file_name']] = $row['file_name']; }
        $file_list = implode("','",$file_url);

        $sql = "SELECT * FROM `main_linkdetails` WHERE `href` IN ('$file_list') AND `active` = '1' ORDER BY `name` ASC";
        $query = mysqli_query($conn,$sql); $file_code = $file_name = array();
        while($row = mysqli_fetch_assoc($query)){ $file_code[$row['href']] = $row['href']; $file_name[$row['href']] = $row['name']; }
        
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
        }
        .table1{
            transform: scale(0.8);
            transform-origin: top left;
        }*/
    </style>
    </head>
    <body class="m-0 hold-transition">
        <?php
        $id = $_GET['id']; $ucode = "";
        $sql = "SELECT * FROM `dataentry_daterange_master` WHERE `id` = '$id' AND `flag` = '0' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($query)){ $ucode = $row['user_code']; }
        $sql = "SELECT * FROM `dataentry_daterange_master` WHERE `user_code` = '$ucode' AND `flag` = '0' AND `dflag` = '0' ORDER BY `id` ASC";
        $query = mysqli_query($conn,$sql); $c = 0; $file_url = array();
        while($row = mysqli_fetch_assoc($query)){
            $id_alist[$c] = $row['id'];
            $file_url[$c] = $row['file_name'];
            $min_days[$c] = $row['min_days'];
            $max_days[$c] = $row['max_days'];
            $c++;
        } $incr = $c - 1;
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <div class="float-left"><h3 class="card-title">Edit Date Range</h3></div>
                        </div>
                        <div class="pl-2 card-body">
                            <form action="broiler_modify_daterange.php" method="post" role="form" onsubmit="return checkval()">
                                <div class="row row_body2">
                                    <table class="p-1 table1" style="width:auto;" align="center">
                                        <thead>
                                            <tr>
                                                <th colspan="4">
                                                    <div class="row">
                                                        <div class="form-group" style="width:200px;">
                                                            <label for="user_code">User<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="user_code" id="user_code" class="form-control select2" style="width:190px;">
                                                                <option value="<?php echo $ucode; ?>"><?php echo $emp_name[$ucode]; ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="text-align:center;"><label>File Name<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>Start days<b style="color:red;">&nbsp;*</b></label></th>
                                                <th style="text-align:center;"><label>End days<b style="color:red;">&nbsp;*</b></label></th>
                                                <!--<th style="visibility:hidden;"><label>Action</label></th>-->
                                                <th style="visibility:hidden;"><label>ID</label></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            <?php
                                            $i = $incr;
                                            for($c = 0;$c <= $i;$c++){
                                            ?>
                                            <tr id="row_no[<?php echo $c; ?>]">
                                                <td><select name="file_name[]" id="file_name[<?php echo $c; ?>]" class="form-control select2" style="width:190px;"><option value="<?php echo $file_url[$c]; ?>" selected><?php echo $file_name[$file_url[$c]]; ?></option></select></td>
                                                <td><input type="text" name="min_days[]" id="min_days[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $min_days[$c]; ?>" style="width:90px;" onkeyup="validate_count(this.id);" /></td>
                                                <td><input type="text" name="max_days[]" id="max_days[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $max_days[$c]; ?>" style="width:90px;" onkeyup="validate_count(this.id);" /></td>
                                                <?php
                                                /*if($c == $i){ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:visible;">'; }
                                                else{ echo '<td id="action['.$c.']" style="padding-top: 5px;visibility:hidden;">'; }
                                                echo '<a href="javascript:void(0);" id="addrow['.$c.']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;';
                                                if($c > 0){ echo '<a href="javascript:void(0);" id="deductrow['.$c.']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a>'; }
                                                echo '</td>';*/
                                                ?>
                                                <td style="visibility:hidden;"><input type="text" name="id_alist[]" id="id_alist[<?php echo $c; ?>]" class="form-control text-right" value="<?php echo $id_alist[$c]; ?>" value="0" style="padding:0;width:30px;" readonly /></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div><br/>
                                <div class="row" style="visibility:hidden;">
                                    <div class="form-group" style="width:30px;">
                                        <label>IN</label>
                                        <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $incr; ?>" style="padding:0;width:20px;" readonly />
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
                var incr = document.getElementById("incr").value;
                var file_name = ""; var e = min_days = max_days = 0;

                for(var d = 0;d <= incr;d++){
                    if(l == true){
                        e = d + 1;
                        file_name = document.getElementById("file_name["+d+"]").value;
                        min_days = document.getElementById("min_days["+d+"]").value;
                        max_days = document.getElementById("max_days["+d+"]").value;
                        
                        if(file_name == "" || file_name == "select"){
                            alert("Please select File Name in row: "+e);
                            document.getElementById("file_name["+d+"]").focus();
                            l = false;
                        }
                        else if(min_days == ""){
                            alert("Please enter Start days in row: "+e);
                            document.getElementById("min_days["+d+"]").focus();
                            l = false;
                        }
                        else if(min_days == ""){
                            alert("Please enter End days in row: "+e);
                            document.getElementById("max_days["+d+"]").focus();
                            l = false;
                        }
                        else{ }
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
            function return_back(){
                var ccid = '<?php echo $ccid; ?>';
                window.location.href = 'broiler_display_daterange.php?ccid='+ccid;
            }
			function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                
                html += '<tr id="row_no['+d+']">';
                html += '<td><select name="file_name[]" id="file_name['+d+']" class="form-control select2" style="width:190px;"><option value="select">-select-</option><?php foreach($file_code as $ucode){ ?><option value="<?php echo $ucode; ?>"><?php echo $file_name[$ucode]; ?></option><?php } ?></select></td>';
                html += '<td><input type="text" name="min_days[]" id="min_days['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td><input type="text" name="max_days[]" id="max_days['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validate_count(this.id);" /></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="dup_flag[]" id="dup_flag['+d+']" class="form-control text-right" value="0" style="padding:0;width:30px;" readonly /></td>';
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