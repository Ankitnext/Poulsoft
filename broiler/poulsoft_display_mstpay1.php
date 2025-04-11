<?php
//poulsoft_display_mstpay1.php
include "newConfig.php";
include "number_format_ind.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['ccid'];
if($cid != ""){ $_SESSION['mstpay1'] = $cid; } else{ $cid = $_SESSION['mstpay1']; }
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
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
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    <!-- Datepicker -->
    <link href="datepicker/jquery-ui.css" rel="stylesheet">
    </head>
    <body class="m-0 hold-transition sidebar-mini">
        <?php
        if($acount == 1){
            /*Check for Table Availability*/
            $database_name = $_SESSION['dbase']; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
            $sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
            if(in_array("master_payments", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_payments LIKE poulso6_admin_broiler_broilermaster.master_payments;"; mysqli_query($conn,$sql1); }
            if(in_array("master_generator", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.master_generator LIKE poulso6_admin_broiler_broilermaster.master_generator;"; mysqli_query($conn,$sql1); }
            if(in_array("prefix_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.prefix_master LIKE poulso6_admin_broiler_broilermaster.prefix_master;"; mysqli_query($conn,$sql1); }
            if(in_array("broiler_printview_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_printview_master LIKE poulso6_admin_broiler_broilermaster.broiler_printview_master;"; mysqli_query($conn,$sql1); }
            if(in_array("dataentry_daterange_master", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.dataentry_daterange_master LIKE poulso6_admin_broiler_broilermaster.dataentry_daterange_master;"; mysqli_query($conn,$sql1); }
            
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

            $fsdate = $cid."-fdate"; $tsdate = $cid."-tdate"; 
            if(isset($_POST['submit']) == true){
                $fdate = date("Y-m-d",strtotime($_POST['fdate']));
                $tdate = date("Y-m-d",strtotime($_POST['tdate']));
                $_SESSION[$fsdate] = $fdate;
                $_SESSION[$tsdate] = $tdate;
            }
            else {
                $fdate = $tdate = date("Y-m-d");
                if(!empty($_SESSION[$fsdate])){ $fdate = date("Y-m-d",strtotime($_SESSION[$fsdate])); }
                if(!empty($_SESSION[$tsdate])){ $tdate = date("Y-m-d",strtotime($_SESSION[$tsdate])); }
            }

            //Fetch Print-View from Print Master
            $i = $pc = 0; $field_name = $field_name = $field_name = $field_name = $field_name = $field_name = array();
            $psql = "SELECT * FROM `broiler_printview_master` WHERE `file_name` LIKE '$href' AND `active` = '1' AND `dflag` = '0' ORDER BY `sort_order`,`id` ASC";
            $pquery = mysqli_query($conn,$psql);
            while($prow = mysqli_fetch_array($pquery)){
                $field_name[$i] = $prow['field_name'];
                $print_path[$i] = $prow['print_path'];
                $icon_type[$i] = $prow['icon_type'];
                $icon_path[$i] = $prow['icon_path'];
                $icon_color[$i] = $prow['icon_color'];
                $target[$i] = $prow['target'];
                $i++;
            }
            $pc = $i - 1;
        ?>
        <div class="m-0 p-0 wrapper">
            <section class="m-0 p-0 content">
                <div class="m-0 p-0 container-fluid">
                    <div class="m-0 p-0 card">
                        <div class="card-header">
                            <form action="<?php echo $href; ?>" method="post">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row" align="left">
                                            <div class="form-group" style="width:100px;">
                                                <label for="fdate">From Date: </label>
                                                <input type="text" name="fdate" id="fdate" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" style="width:90px;">
                                            </div>
                                            <div class="form-group" style="width:100px;">
                                                <label for="tdate">To Date: </label>
                                                <input type="text" name="tdate" id="tdate" class="form-control datepicker" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" style="width:90px;">
                                            </div>
                                            <div class="form-group" style="width:100px;">
                                                <br/><button type="submit" name="submit" id="submit" class="btn btn-success btn-sm">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4" align="right">
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
										<th>Date</th>
										<th>Trnum</th>
										<th>Account</th>
										<th>description</th>
										<th>Amount</th>
										<th>Cost Center</th>
										<th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT * FROM `acc_coa` WHERE `dflag` = '0' ORDER BY `description` ASC";
                                        $query = mysqli_query($conn,$sql); $vfcc_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $vfcc_name[$row['code']] = $row['description']; }

                                        $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC";
                                        $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $vfcc_name[$row['code']] = $row['description']; }

                                        $sql = "SELECT * FROM `main_contactdetails` WHERE `dflag` = '0' ORDER BY `name` ASC";
                                        $query = mysqli_query($conn,$sql);
                                        while($row = mysqli_fetch_assoc($query)){ $vfcc_name[$row['code']] = $row['name']; }

                                        $sql = "SELECT * FROM `inv_sectors` WHERE `dflag` = '0' ORDER BY `description` ASC";
                                        $query = mysqli_query($conn,$sql); $sector_name = array();
                                        while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

                                        $delete_url = $delete_link."?utype=delete&trnum=";
                                        $sql = "SELECT * FROM `master_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `dflag` = '0' AND `trlink` = '$href' ORDER BY `date`,`trnum` ASC";
                                        $query = mysqli_query($conn,$sql); $c = 0;
                                        while($row = mysqli_fetch_assoc($query)){
                                            $id = $row['trnum'];
                                            $edit_url = $edit_link."?utype=edit&trnum=".$id;
                                            $print_url = $print_link."?utype=print&trnum=".$id;
                                            $authorize_url = $update_link."?utype=authorize&trnum=".$id;
                                            if($row['active'] == 1){
                                                $update_url = $update_link."?utype=pause&trnum=".$id;
                                            }
                                            else{
                                                $update_url = $update_link."?utype=activate&trnum=".$id;
                                            }
                                            $val = ""; $val = $row['trnum'];
                                            $print_dt = ""; $print_dt = "?trnum=".$row['trnum']."&date=".$row['date']."&sector=".$row['sector']."&fcoa=".$row['from_account']."&tcoa=".$row['to_account'];
                                    ?>
                                    <tr>
                                        <td data-sort="<?= strtotime($row['date']) ?>"><?= date('d.m.Y',strtotime($row['date'])); ?></td>
										<td><?php echo $row['trnum']; ?></td>
										<td><?php echo $vfcc_name[$row['from_account']]; ?></td>
										<td><?php echo $vfcc_name[$row['to_account']]; ?></td>
										<td style="text-align:right;"><?php echo round($row['amount'],5); ?></td>
										<td><?php echo $sector_name[$row['sector']]; ?></td>
                                        <td style="width:15%;" align="left">
                                        <?php
                                            if($row['flag'] == 1){
                                                echo "<i class='fa fa-check' style='color:green;' title='Authorized'></i></a>";
                                            }
                                            else {
                                                if($edit_flag == 1){
                                                    echo "<a href='".$edit_url."'><i class='fa fa-pen' style='color:brown;' title='Edit'></i></a>&ensp;&ensp;";
                                                }
                                                if($delete_flag == 1 && $flk_ages[$row['flock_code']] == $row['breed_age']){
                                                    ?>
                                                    <a href='javascript:void(0)' id='<?php echo $val; ?>' value='<?php echo $val; ?>' onclick='checkdelete(this.id)'>
                                                    <i class='fa fa-trash' style='color:red;' title='delete'></i>
                                                    </a>&ensp;&ensp;
                                                <?php
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
                                            if($print_flag == 1){
                                                $printv_list = "";
                                                for($p = 0;$p <= $pc;$p++){
                                                    if($icon_path[$p] == "fa-brands fa-whatsapp"){ $ppath = ""; $ppath = $print_path[$p]."".$print_dt."&view_type=send_pdf"; } else{ $ppath = ""; $ppath = $print_path[$p]."".$print_dt; }
                                                    if($icon_type[$p] == "image"){ $printv_list .= '<a href="'.$ppath.'" target="'.$target[$p].'"><img src="'.$icon_path[$p].'" style="width:15px;height:15px;" title="'.$field_name[$p].'" /></a>&ensp;&ensp;'; }
                                                    else if($icon_type[$p] == "icon"){ $printv_list .= '<a href="'.$ppath.'" target="'.$target[$p].'"><i class="'.$icon_path[$p].'" style="color:'.$icon_color[$p].';" title="'.$field_name[$p].'"></i></a>&ensp;&ensp;'; }
                                                }
                                                echo $printv_list;
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
        <!-- Datepicker -->
        <script src="datepicker/jquery/jquery.js"></script>
        <script src="datepicker/jquery-ui.js"></script>
        <script>
			function checkdelete(x){
                var trnum = x;
                if(trnum != ""){
                    var inv_items = new XMLHttpRequest();
                    var method = "GET";
                    var url = "poulsoft_check_mstpay1.php?trnum="+trnum;
                    //window.open(url);
                    var asynchronous = true;
                    inv_items.open(method, url, asynchronous);
                    inv_items.send();
                    inv_items.onreadystatechange = function(){
                        if(this.readyState == 4 && this.status == 200){
                            var count = this.responseText;
                            if(parseFloat(count) > 0){
                                alert("You can't delete the Transaction: "+trnum+". As Transaction is already in use!");
                            }
                            else{
                                var b = "<?php echo $delete_url; ?>"+trnum;
                                var c = confirm("are you sure you want to delete the Transaction: "+trnum+" ?");
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
            var x = confirm("You don't have access to this file\folder \n Kindly contact your admin for more details.");
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