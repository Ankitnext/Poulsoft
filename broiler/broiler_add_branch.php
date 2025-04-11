<?php
//broiler_add_branch.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['loc_branch'];
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
                            <div class="float-left"><h3 class="card-title">Add Branch</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_branch.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row justify-content-center align-items-center">
                                        <table align="center">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <div class="form-group">
                                                            <label>Region<b style="color:red;">&nbsp;*</b></label>
                                                            <select name="region" id="region" class="form-control select2" style="width: 100%;" onchange=""><option value="select">select</option><?php foreach($region_code as $rcode){ ?><option value="<?php echo $rcode; ?>"><?php echo $region_name[$rcode]; ?></option><?php } ?></select>
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Branch<b style="color:red;">&nbsp;*</b></th>
                                                    <th>Prefix<b style="color:red;">&nbsp;*</b></th>
                                                    <th style="visibility:hidden;">Action</th>
                                                    <th style="visibility:hidden;">DF</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">
                                                <tr>
                                                    <td><input type="text" name="branch[]" id="branch[0]" class="form-control " placeholder="Enter description..." onkeyup="validatename(this.id)" onchange="check_duplicate(this.id);"></td>
                                                    <td><input type="text" name="flk_prefix[]" id="flk_prefix[0]"  class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id)"></td>
                                                    <td id="action[0]" style="width:80px;"><a href="javascript:void(0);" id="addrow[0]" onClick="create_row(this.id)" class="form-control" style="width:15px; height:15px;border:none;"><i class="fa fa-plus" style="color:green;"></i></a></td>
                                                    <td style="visibility:hidden;"><input type="text" name="dupflag[0]" id="dupflag[0]" class="form-control text-right" value="0" style="width:20px;" readonly /></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12" id="row_body">

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-1" style="visibility:hidden;">
                                            <label>Incr<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="incr" id="incr" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-1" style="visibility:hidden;"><!-- style="visibility:hidden;"-->
                                            <label>ECount<b style="color:red;">&nbsp;*</b></label>
                                            <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0">
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
                window.location.href = 'broiler_display_branch.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
               var a = document.getElementById("region").value;
                if(a.match("select")){
                        alert("Please select Region  ");
                        document.getElementById("region").focus();
                        l = false;
                }
                var incr = document.getElementById("incr").value;
                var d = e = ""; var c = dupflag = 0; var l = true;
                for(var b = 0;b <= incr;b++){
                    if(l == true){
                       // a = document.getElementById("region["+b+"]").value;
                        d = document.getElementById("branch["+b+"]").value;
                        e = document.getElementById("flk_prefix["+b+"]").value;
                        dupflag = document.getElementById("dupflag["+b+"]").value;
                       if(d.length == 0){
                            c = b + 1;
                            alert("Please enter Branch in row: "+c);
                            document.getElementById("branch["+b+"]").focus();
                            l = false;
                        }
                        else if(e.length == 0){
                            c = b + 1;
                            alert("Please enter Prefix in row: "+c);
                            document.getElementById("flk_prefix["+b+"]").focus();
                            l = false;
                        }
                        else if(dupflag == 1 || dupflag == "1"){
                            c = b + 1;
                            alert("Branch Name already exist \n Please check and try again in row: "+c);
                            document.getElementById("branch["+b+"]").focus();
                            l = false;
                        }
                        else{
                            l = true;
                        }
                    }
                }
                if(l == true){
                    return true;
                }
                else{
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    return false;
                }
            }
            function create_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("action["+d+"]").style.visibility = "hidden";
                d++; var html = '';
                document.getElementById("incr").value = d;
                 html += '<tr id="row_no['+d+']">';
                 html += '<td><input type="text" name="branch[]" id="branch['+d+']" class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id)" onchange="check_duplicate(this.id);"></td>';
                 html += '<td><input type="text" name="flk_prefix[]" id="flk_prefix['+d+']"  class="form-control" placeholder="Enter description..." onkeyup="validatename(this.id)"></td>';
                 html += '<td id="action['+d+']" style="padding-top: 5px;width:80px;"><br class="labelrow" style="display:none;" /><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                 html += '<td style="visibility:hidden;"><input type="text" name="dupflag['+d+']" id="dupflag['+d+']" class="form-control text-right" value="0" style="width:20px;" /></td>';
                 html += '</tr>';
				 $('#tbody').append(html); $('.select2').select2();
            }
            function destroy_row(a){
                var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                document.getElementById("row_no["+d+"]").remove();
                d--;
                document.getElementById("incr").value = d;
                document.getElementById("action["+d+"]").style.visibility = "visible";
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
			function check_duplicate(aa){
                var bb = aa.split("["); var cc = bb[1].split("]"); var d = cc[0];
				var b = document.getElementById("branch["+d+"]").value;
				var c = "add";
				if(!b.length == 0){
					var oldqty = new XMLHttpRequest();
					var method = "GET";
					var url = "broiler_fetch_branch_duplicates.php?cname="+b+"&type="+c;
                    //window.open(url);
					var asynchronous = true;
					oldqty.open(method, url, asynchronous);
					oldqty.send();
					oldqty.onreadystatechange = function(){
						if(this.readyState == 4 && this.status == 200){
							var dup_count = this.responseText;
							if(parseFloat(dup_count) > 0){
								alert("Branch Details are available with the same name.\n Kindly change the name");
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
        
            
document.addEventListener('keydown', function (event) {
  if (event.keyCode === 13 && event.target.nodeName === 'INPUT') {
    var form = event.target.form;
    var index = Array.prototype.indexOf.call(form, event.target);
    form.elements[index + 1].focus();
    event.preventDefault();
  }
});
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