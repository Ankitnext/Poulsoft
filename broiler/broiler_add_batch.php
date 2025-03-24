<?php
//broiler_add_batch.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $ccid = $_SESSION['batch'];
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
        $sql='SHOW COLUMNS FROM `broiler_batch`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
        while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
        if(in_array("book_num", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_batch` ADD `book_num` VARCHAR(300) DEFAULT NULL COMMENT '' AFTER `batch_no`"; mysqli_query($conn,$sql); }
        
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
                            <div class="float-left"><h3 class="card-title">Add Batch</h3></div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form action="broiler_save_batch.php" method="post" role="form" onsubmit="return checkval()">
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="stype">Farm Name<b style="color:red;">&nbsp;*</b></label>
                                                <select name="farm_name" id="farm_name" class="form-control select2" style="width: 100%;" onchange="fatch_batch_details(this.id)">
                                                    <option value="select">select</option>
                                                    <?php
                                                        $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                            <option value="<?php echo $row['code']; ?>"><?php echo $row['description']; ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="stype">Farm Code<b style="color:red;">&nbsp;*</b></label>
                                                <select name="farm_code" id="farm_code" class="form-control select2" style="width: 100%;" onchange="fatch_batch_details(this.id)">
                                                    <option value="select">select</option>
                                                    <?php
                                                        $sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
                                                        while($row = mysqli_fetch_assoc($query)){
                                                    ?>
                                                            <option value="<?php echo $row['code']; ?>"><?php echo $row['farm_code']; ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Batch<b style="color:red;">&nbsp;*</b></label>
							                <input type="text" name="batch" id="batch" class="form-control" style="font-size: 15px;" onchange="check_duplicate();" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                            <label>Book Number</label>
							                <input type="text" name="book_num" id="book_num" class="form-control" style="font-size: 15px;">
                                            </div>
                                        </div> 
                                        <div class="col-md-2"></div>
                                    </div>
                                    <div class="row">
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
                window.location.href = 'broiler_display_batch.php?ccid='+ccid;
            }
            function checkval(){
				document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                var a = document.getElementById("batch").value;
                if(a.length == 0){
                    document.getElementById("submit").style.visibility = "visible";
					document.getElementById("ebtncount").value = "0";
                    alert("Select Appropriate Farm (or) Farm Code to generate Batch Code ..!");
                    return false;
                }
                else {
                    return true;
                }
            }
            function fatch_batch_details(x) {
                var code = document.getElementById(x).value;
                if(x.match("farm_name")){
                    $('#farm_code').select2();
                    document.getElementById("farm_code").value = code;
                    $('#farm_code').select2();
                }
                else if(x.match("farm_code")){
                    $('#farm_name').select2();
                    document.getElementById("farm_name").value = code;
                    $('#farm_name').select2();
                }
                else{

                }
                if(!x.match("select")){
                    var batchinfo = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_fetch_farmbatch.php?farm_code="+code;
                    var asynchronous = true;
                    batchinfo.open(method, url, asynchronous);
                    batchinfo.send();
                    batchinfo.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var b = this.responseText;
                            if(b == 0 || b == "" || b == "0@0"){
                                alert("Invalid");
                            }
                            else {
                                var batchno = b;
                                document.getElementById("batch").value = batchno;
                            }
                        }
                    }
                }
                else{

                }
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