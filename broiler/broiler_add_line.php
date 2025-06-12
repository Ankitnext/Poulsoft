<?php
//broiler_add_line.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['loc_line'];
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
        $sql = "SELECT * FROM `location_region` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
        $query = mysqli_query($conn,$sql); $region_code = $region_name = array();
        while($row = mysqli_fetch_assoc($query)){ $region_code[$row['code']] = $row['code']; $region_name[$row['code']] = $row['description']; }
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
                            <div class="float-left"><h3 class="card-title">Add Line</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_line.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <table align="center">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <div class="form-group">
                                                            <label>Region<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="region" id="region" class="form-control select2" style="width: 100%;" onchange="clear_data();"><option value="select">select</option><?php foreach($region_code as $rcode){ ?><option value="<?php echo $rcode; ?>"><?php echo $region_name[$rcode]; ?></option><?php } ?></select>
                                                        </div>
                                                    </th> 
                                                    <th>
                                                        <div class="form-group">
                                                            <label>Branch<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="branch" id="branch" class="form-control select2" style="width:200px;"></select>
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Line<b style="color:red;">&nbsp;*</b></th>
                                                    <th style="visibility:hidden;">Action</th>
                                                    <th style="visibility:hidden;">DF</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">
                                                <tr>
                                                    <!-- <td><select name="branch[]" id="branch[0]" class="form-control select2" style="width:200px;"></select></td> -->
                                                    <td><input type="text" name="line[]" id="line[0]" class="form-control" style="width:250px;" onkeyup="validatename(this.id)" onchange="check_duplicate(this.id);" /></td>
                                                    <td id="action[0]" style="width:80px;"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                    <td style="visibility:hidden;"><input type="text" name="dupflag[0]" id="dupflag[0]" class="form-control text-right" value="0" style="width:20px;" readonly /></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row" style="visibility:hidden;">
                                        <div class="form-group" style="width:30px;">
                                            <label>IN<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
                                        </div>
                                        <div class="form-group" style="width:30px;">
                                            <label>EB<b style="color:red;">&ensp;*</b></label>
                                            <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
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
                window.location.href = 'broiler_display_line.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var incr = document.getElementById("incr").value;
                var a = document.getElementById("region").value;
                if(a.match("select")){
                    alert("Please select Region");
                    document.getElementById("region").focus();
                    l = false;
                }
                var d = e = ""; var c = dupflag = 0; var l = x = true;
                for(var b = 0;b <= incr;b++){
                    if(l == true){
                        // a = document.getElementById("region["+b+"]").value;
                        d = document.getElementById("branch["+b+"]").value;
                        e = document.getElementById("line["+b+"]").value;
                        dupflag = document.getElementById("dupflag["+b+"]").value;
                       if(d.match("select")){
                            c = b + 1;
                            alert("Please select Branch in row: "+c);
                            document.getElementById("branch["+b+"]").focus();
                            l = false;
                        }
                        else if(e.length == 0){
                            c = b + 1;
                            alert("Please enter Line in row: "+c);
                            document.getElementById("line["+b+"]").focus();
                            l = false;
                        }
                        else if(dupflag == 1 || dupflag == "1"){
                            c = b + 1;
                            alert("Line Name already exist \n Please check and try again in row: "+c);
                            document.getElementById("line["+b+"]").focus();
                            l = false;
                        }
                        else{
                            var oldqty = new XMLHttpRequest();
                            var method = "GET";
                            var url = "broiler_fetch_line_duplicates.php?cname="+e+"&type=add";
                            //window.open(url);
                            var asynchronous = true;
                            oldqty.open(method, url, asynchronous);
                            oldqty.send();
                            oldqty.onreadystatechange = function(){
                                if(this.readyState == 4 && this.status == 200){
                                    var dup_count = this.responseText;
                                    if(parseFloat(dup_count) > 0){
                                        c = b + 1;
                                        alert("Line Name already available with same name \n Please check and try again in row: "+c);
                                        document.getElementById("line["+b+"]").focus();
                                        l = false;
                                        x = false;
                                    }
                                    else { }
                                }
                            }
                        }
                    }
                }
                if(l == true){
                    if(x == true){
                        return true;
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                    
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
			function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                var selectedBranch = document.getElementById("branch").value;
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                var slno = d + 1;
                document.getElementById("incr").value = d;
                html += '<tr id="row_no['+d+']">';
               // html += '<td><select name="branch[]" id="branch['+d+']" class="form-control select2" style="width:200px;"></select></td>';
                html += '<td><input type="text" name="line[]" id="line['+d+']" class="form-control" style="width:250px;" onkeyup="validatename(this.id)" onchange="check_duplicate(this.id);" /></td>';
                html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="dupflag['+d+']" id="dupflag['+d+']" class="form-control text-right" value="0" style="width:20px;" /></td>';
				html += '</tr>';
				$('#tbody').append(html);
				$('.select2').select2();
                var prx = "row_no["+d+"]"; fetch_branch_details(prx);
                document.getElementById("branch").value = selectedBranch;
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
            }
            function clear_data(){
                var d = 0; var html = '';
                document.getElementById("tbody").innerHTML = "";
                document.getElementById("incr").value = d;

                html += '<tr id="row_no['+d+']">';
               // html += '<td><select name="branch" id="branch" class="form-control select2" style="width:200px;"></select></td>';
                html += '<td><input type="text" name="line[]" id="line['+d+']" class="form-control" style="width:250px;" onkeyup="validatename(this.id)" onchange="check_duplicate(this.id);" /></td>';
                html += '<td id="action['+d+']" style="width:80px;"><a href="javascript:void(0);" id="addrow['+d+']" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>';
                html += '<td style="visibility:hidden;"><input type="text" name="dupflag['+d+']" id="dupflag['+d+']" class="form-control text-right" value="0" style="width:20px;" /></td>';
				html += '</tr>';
				$('#tbody').append(html);
				$('.select2').select2();
                var prx = "row_no["+d+"]"; fetch_branch_details(prx);
            }
            function fetch_branch_details() {
                var reg_code = document.getElementById("region").value;
                var myselect1 = document.getElementById("branch");
                removeAllOptions(myselect1); // Clear any existing options first

                if (reg_code !== "select") {
                    var theOption1 = document.createElement("OPTION");
                    var theText1 = document.createTextNode("select");
                    theOption1.value = "select";
                    theOption1.appendChild(theText1);
                    myselect1.appendChild(theOption1);

                    <?php
                        /*Check User access Locations*/
                        $sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
                        if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
                        if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
                        if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

                        $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ".$branch_access_filter1." ORDER BY `description` ASC";
                       // $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC";
                        $query = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($query)){
                            $r_code = $row['region_code'];
                            echo "if (reg_code == '$r_code') {";
                    ?>
                            var theOption1 = document.createElement("OPTION");
                            var theText1 = document.createTextNode("<?php echo $row['description']; ?>");
                            theOption1.value = "<?php echo $row['code']; ?>";
                            theOption1.appendChild(theText1);
                            myselect1.appendChild(theOption1);
                    <?php
                            echo "}";
                        }
                    ?>
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
            function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
			function check_duplicate(aa){
                var bb = aa.split("["); var cc = bb[1].split("]"); var d = cc[0];
				var b = document.getElementById("line["+d+"]").value;
				var c = "add";
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_line_duplicates.php?cname="+b+"&type="+c;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_count = this.responseText;
							if(parseFloat(dup_count) > 0){
								alert("Line Details are available with the same name.\n Kindly change the name");
								document.getElementById("dupflag["+d+"]"). value = 1;
							}
							else {
								document.getElementById("dupflag["+d+"]"). value = 0;
							}
						}
					}
				}
				else { }
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