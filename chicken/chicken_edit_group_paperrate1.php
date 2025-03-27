<?php
//chicken_edit_group_paperrate1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Paper Rate"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }
    
    $sql = "SELECT * FROM `item_details` WHERE `description` LIKE '%bird%' AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $item_code = $item_name = array();
    while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `main_groups` WHERE `active` = '1' AND `gvpr_flag` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $group_code = $group_name = array();
    while($row = mysqli_fetch_assoc($query)){ $group_code[$row['code']] = $row['code']; $group_name[$row['code']] = $row['description']; }

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            $sql = "SELECT * FROM `main_dailypaperrate` WHERE `trnum` = '$ids' AND `dflag` = '0' AND `trlink` = 'chicken_display_group_paperrate1.php'";
            $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $date = $row['date'];
                $cgroup = $row['cgroup'];
                $code = $row['code'];
                $new_price = round($row['new_price'],5);
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Paper Rate</div>
                <form action="chicken_modify_group_paperrate1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <th>Date<b style="color:red;">&nbsp;*</b></th>
                                        <th>Customer Group<b style="color:red;">&nbsp;*</b></th>
                                        <?php foreach($item_code as $scode){ echo '<th>'.$item_name[$scode].'</th>'; } ?>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <tr style="margin:5px 0px 5px 0px;">
                                        <td><input type="text" name="date" id="date" class="form-control prate_datepickers" value="<?php echo date("d.m.Y",strtotime($date)); ?>" style="width:100px;" onchange="fetch_tcds_per(this.id);" readonly /></td>
                                        <td><select name="cgroup" id="cgroup" class="form-control select2" style="width:180px;"><option value="all">-All-</option><?php foreach($group_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $cgroup){ echo "selected"; } ?>><?php echo $group_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><select name="code" id="code" class="form-control select2" style="width:180px;"><option value="all">-All-</option><?php foreach($item_code as $scode){ ?><option value="<?php echo $scode; ?>" <?php if($scode == $code){ echo "selected"; } ?>><?php echo $item_name[$scode]; ?></option><?php } ?></select></td>
                                        <td><input type="text" name="new_price" id="new_price" class="form-control text-right" value="<?php echo $new_price; ?>" style="width:90px;" onkeyup="validate_num(this.id);" onchange="validate_amount(this.id);" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><br/>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Update</button>&ensp;
                                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                function return_back(){
                    window.location.href = "chicken_display_group_paperrate1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var date = document.getElementById("date").value;
                    var cgroup = document.getElementById("cgroup").value;
                    var code = document.getElementById("code").value;
                    var new_price = document.getElementById("new_price").value; if(new_price == ""){ new_price = 0; }
                    if(date == ""){
                        alert("Please select/enter Date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(cgroup == "select"){
                        alert("Please select Customer Group");
                        document.getElementById("cgroup").focus();
                        l = false;
                    }
                    else if(code == "select"){
                        alert("Please select item");
                        document.getElementById("code").focus();
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
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
            <script src="handle_ebtn_as_tbtn.js"></script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
