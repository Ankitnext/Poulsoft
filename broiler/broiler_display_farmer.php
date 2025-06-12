<?php
//broiler_display_farmer.php
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['farmer'] = $cid; } else{ $cid = $_SESSION['farmer']; }
$sql = "SELECT * FROM `main_linkdetails` WHERE `childid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC";
$query = mysqli_query($conn,$sql); $link_active_flag = mysqli_num_rows($query);
if($link_active_flag > 0){
    while($row = mysqli_fetch_assoc($query)){ $cname = $row['name']; }
    $sql = "SELECT * FROM `main_access` WHERE `empcode` LIKE '$user_code' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
    $dlink = $alink = $elink = $rlink = $plink = $ulink = $flink = array(); $sector_access = $cgroup_access = $user_type = "";
    while($row = mysqli_fetch_assoc($query)){
        $dlink = str_replace(",","','",$row['displayaccess']);
        $alink = str_replace(",","','",$row['addaccess']);
        $elink = str_replace(",","','",$row['editaccess']);
        $rlink = str_replace(",","','",$row['deleteaccess']);
        $plink = str_replace(",","','",$row['printaccess']);
        $ulink = str_replace(",","','",$row['otheraccess']);
        $sector_access = $row['loc_access'];
        $cgroup_access = $row['cgroup_access'];
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; } else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; } else{ $user_type = "N"; }
    }
    $aid = 0;
    $flink = explode("','",$dlink); $acount = 0; foreach($flink as $flinks){ if($flinks == $cid){ $aid = 1; } }
    if($user_type == "S"){ $acount = 1; }
    else if($aid == 1){ $acount = 1; }
    else{ $acount = 0; }




$colarr=array('usc','serviceno');

$q='show columns from broiler_farmer';
$qr=mysqli_query($conn,$q) or die(mysqli_error());
$i=0;
$columns=array();
while($rw=mysqli_fetch_assoc($qr))
{
    $columns[$i]=$rw['Field'];       
    $i++;
}

$diff_array=array_diff($colarr,$columns);


if(in_array('usc',$diff_array))
{
    $q="ALTER TABLE `broiler_farmer` ADD `usc` VARCHAR(100) NULL DEFAULT NULL AFTER `nationalidno`";
    $qr=mysqli_query($conn,$q) or die(mysqli_error());
}

if(in_array('serviceno',$diff_array))
{
    $q="ALTER TABLE `broiler_farmer` ADD `serviceno` VARCHAR(100) NULL DEFAULT NULL AFTER `nationalidno`";
    $qr=mysqli_query($conn,$q) or die(mysqli_error());
}

?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    </head>
    <body class="m-0 hold-transition sidebar-mini">
        <?php
        if($acount == 1){
            $gp_id = $gc_id = $gp_name = $gp_link = $gp_link = $p_id = $c_id = $p_name = $p_link = array();
            $sql = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$cid' AND `active` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $gp_id = $row['parentid'];
                $gc_id[$row['childid']] = $row['childid'];
                $gp_name[$row['childid']] = $row['name'];
                $gp_link[$row['childid']] = $row['href'];
            }
            $add_link_acc = $edt_link_acc = $del_link_acc = $pnt_link_acc = $upd_link_acc = "";
            $alink = explode("','",$alink); foreach($alink as $alink1){ $add_acc[$alink1] = $alink1; }
            $elink = explode("','",$elink); foreach($elink as $elink1){ $edt_acc[$elink1] = $elink1; }
            $rlink = explode("','",$rlink); foreach($rlink as $rlink1){ $del_acc[$rlink1] = $rlink1; }
            $plink = explode("','",$plink); foreach($plink as $plink1){ $pnt_acc[$plink1] = $plink1; $pnt_link_acc = $pnt_link_acc.",".$plink1; }
            $ulink = explode("','",$ulink); foreach($ulink as $ulink1){ $upd_acc[$ulink1] = $ulink1; }
            if(!empty($add_acc[$gp_id."A"])){ $add_flag = 1; $add_link = $gp_link[$gp_id."A"]; } else { $add_link = ""; $add_flag = 0; }
            if(!empty($edt_acc[$gp_id."E"])){ $edit_flag = 1; $edit_link = $gp_link[$gp_id."E"]; } else { $edit_link = ""; $edit_flag = 0; }
            if(!empty($del_acc[$gp_id."R"])){ $delete_flag = 1; $delete_link = $gp_link[$gp_id."R"]; } else { $delete_link = ""; $delete_flag = 0; }
            if(!empty($pnt_acc[$gp_id."P"])){ $print_flag = 1; $print_link = $gp_link[$gp_id."P"]; } else { $print_link = ""; $print_flag = 0; }
            if(!empty($upd_acc[$gp_id."U"])){ $update_flag = 1; $update_link = $gp_link[$gp_id."U"]; } else { $update_link = ""; $update_flag = 0; }
            
            /*Check User access Locations*/
            $sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
            if($branch_access_code == "all"){ $branch_access_filter1 = ""; } else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
            if($line_access_code == "all"){ $line_access_filter1 = ""; } else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
            if($farm_access_code == "all"){ $farm_access_filter1 = ""; } else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }

            $sql = "SELECT * FROM `location_branch` WHERE `active` = '1' AND `dflag` = '0' ".$branch_access_filter1." ORDER BY `description` ASC";
            //$sql = "SELECT * FROM `location_branch` WHERE `dflag` = '0' ORDER BY `description` ASC"; 
            $query = mysqli_query($conn,$sql); $c = 0;
            while($row = mysqli_fetch_assoc($query)){ $branch_code[$row['code']] = $row['code']; $branch_name[$row['code']] = $row['description']; $c++; if($c == 1){ $branches = $row['code']; } }
            
            if(isset($_POST['submit']) == true){
                $_SESSION['bfmrc_branch'] = $branches = $_POST['branches'];
                $_SESSION['bfmrc_status'] = $status = $_POST['status'];
            }
            else if(!empty($_SESSION['bfmrc_branch'])){
                $branches = $_SESSION['bfmrc_branch'];
                $status = $_SESSION['bfmrc_status'];
            }
            else{
                $status = "1";
            }
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <form action="<?php echo $url; ?>" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="form-group" style="width:150px;"><h3 class="card-title">Farmer Group</h3></div>
                                            <div class="form-group" style="width:250px;"><label for="tdate">Branch: </label>
                                                <select name="branches" id="branches" class="form-control select2" style="width:240px;">
                                                    <option value="all">-All-</option>
                                                    <?php
                                                    foreach($branch_code as $bcode){
                                                    ?>
                                                    <option value="<?php echo $bcode; ?>" <?php if($branches == $bcode){ echo "selected"; } ?>><?php echo $branch_name[$bcode]; ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group" style="width:150px;"><label for="tdate">Status: </label>
                                                <select name="status" id="status" class="form-control select2" style="width:140px;">
                                                    <option value="all" <?php if($status == "all"){ echo "selected"; } ?>>-All-</option>
                                                    <option value="1" <?php if($status == "1"){ echo "selected"; } ?>>-Active-</option>
                                                    <option value="0" <?php if($status == "0"){ echo "selected"; } ?>>-In-active-</option>
                                                </select>
                                            </div>
                                            <div class="form-group" style="width:100px;"><br/>
                                                <button type="submit" name="submit" id="submit" class="btn btn-success btn-sm">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" align="right">
                                    <?php if($add_flag == 1){ ?>
                                        <button type="button" class="btn bg-purple" id="addpage" value="<?php echo $add_link; ?>" onclick="add_page(this.id)" ><i class="fa fa-align-left"></i> ADD</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Code</th>
										<th>Description</th>
										<th>Group</th>
										<th>Mobile</th>
										<th>Pan No</th>
										<th>Aadhar No</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                        if($status == "all"){ $status_filter = ""; } else{ $status_filter = " AND `active` = '$status'"; }
                                        if($branches == "all"){ $branch_filter = ""; } else{ $branch_filter = " AND `branch_code` = '$branches'"; }
                                        $fmr_arr_code = array();
                                        $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0'".$branch_filter."".$status_filter." ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){ $fmr_arr_code[$row['farmer_code']] = $row['farmer_code']; }
                                        if($branches == "all"){ $fmr_code_filter = ""; }
                                        else{ $fmr_list = ""; $fmr_list = implode("','", $fmr_arr_code); $fmr_code_filter = " AND `code` IN ('$fmr_list')"; }

                                        $sql = "SELECT * FROM `broiler_farmergroup`"; $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){
                                        $fgroup_name[$row['code']] = $row['description'];
                                        }
                                        $delete_url = $delete_link."?utype=delete&id=";
                                        $sql = "SELECT * FROM `broiler_farmer` WHERE `dflag` = '0'".$fmr_code_filter."".$status_filter." ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['id'];
                                            $edit_url = $edit_link."?utype=edit&id=".$id;
                                            //$delete_url = $delete_link."?utype=delete&id=".$id;
                                            $print_url = $print_link."?utype=print&id=".$id;
                                            $authorize_url = $update_link."?utype=authorize&id=".$id;
                                            if($row['active'] == 1){
                                                $update_url = $update_link."?utype=pause&id=".$id;
                                            }
                                            else{
                                                $update_url = $update_link."?utype=activate&id=".$id;
                                            }
                                            $val = ""; $val = $row['id']."@".$row['name'];
                                    ?>
                                    <tr>
                                        <td><?php echo $row['code']; ?></td>
										<td><?php echo $row['name']; ?></td>
										<td><?php echo $fgroup_name[$row['farmer_group']]; ?></td>
										<td><?php echo $row['mobile1'].",".$row['mobile2']; ?></td>
										<td><?php echo $row['panno']; ?></td>
										<td><?php echo $row['aadharno']; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>";
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1){
                                                    ?>
                                                    <a href='javascript:void(0)' id='<?php echo $val; ?>' value='<?php echo $val; ?>' onclick='checkdelete(this.id)'>
                                                    <i class='fa fa-trash' style='color:red;' title='delete'></i>
                                                    </a>&ensp;&ensp;
                                                <?php
                                                }
                                                if($print_flag == 1){
                                                    echo "<a href='".$print_url."'><i class='fa fa-print' style='color:black;' title='Print'></i></a>&ensp;&ensp;";
                                                }
                                                if($update_flag == 1){
                                                    if($row['active'] == 1){
                                                        echo "<a href='".$update_url."'><i class='fa fa-pause' style='color:blue;' title='Activate'></i></a>&ensp;&ensp;";
                                                    }
                                                    else{
                                                        echo "<a href='".$update_url."'><i class='fa fa-play' style='color:blue;' title='Pause'></i></a>&ensp;&ensp;";
                                                    }
                                                    echo "<a href='".$authorize_url."'><i class='fa fa-lock-open' style='color:orange;' title='Authorize'></i></a>&ensp;&ensp;";
                                                }
                                                
                                            }
                                        ?>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <script>
			function checkdelete(x){
                var val1 = x.split("@"); var a = val1[0]; var val = val1[1];
                if(a != ""){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "broiler_check_farmer.php?id="+a;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var count = this.responseText;
                            if(parseFloat(count) > 0){
                                alert("You can't delete the Farmer: "+val+", as Farmer is already in use!");
                            }
                            else{
                                var b = "<?php echo $delete_url; ?>"+a;
                                var c = confirm("are you sure you want to delete the Farmer: "+val+"?");
                                if(c == true){
                                    window.location.href = b;
                                }
                                else{ }
                            }
                        }
                    }
                }
			}
        </script>
        <?php
            }
            else{
        ?>
        <script>
            var x = confirm("You don't have access to this file\folder \n Kindly contact your admin for more details\support");
            if(x == true){
                window.location.href="logout.php";
            }
            else{
                window.location.href="logout.php";
            }
        </script>
        <?php
            }
        ?>
        <script>
			function add_page(a){ var b = document.getElementById(a).value; window.location.href = b; }
		</script>
    <?php include "header_foot.php"; ?>
    </body>
</html>
<?php
}
else{
     header('location:index.php');
}
?>