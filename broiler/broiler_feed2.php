<?php
//broiler_profile
include "newConfig.php";
$user_name = $_SESSION['users']; $user_code = $_SESSION['userid']; $cid = $_GET['cid'];

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
        $ulink = str_replace(",","','",$row['otheraccess']);
        $sector_access = $row['loc_access'];
        $cgroup_access = $row['cgroup_access'];
        if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $user_type = "S"; } else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $user_type = "A"; } else{ $user_type = "N"; }
    }
    $aid = 0;
    $flink = explode("','",$dlink); $acount = 0; foreach($flink as $flinks){ $dis_acc[$flinks] = $flinks; if($flinks == $cid){ $aid = 1; } }
    if($user_type == "S"){ $acount = 1; }
    else if($aid == 1){ $acount = 1; }
    else{ $acount = 0; }
    
?>
<html lang="en">
    <head>
    <?php include "header_head.php"; ?>
    </head>
    <body class="hold-transition sidebar-mini">
        <?php
        if($acount == 1){
            $gp_id = $gc_id = $gp_name = $gp_link = $gp_link = $p_id = $c_id = $p_name = $p_link = array();
            $sql = "SELECT * FROM `main_linkdetails` WHERE `parentid` = '$cid' AND `childid` IN ('$dlink') AND `active` = '1' ORDER BY `sortorder` ASC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
                $gp_id[$row['childid']] = $row['parentid'];
                $gc_id[$row['childid']] = $row['childid'];
                $gp_name[$row['childid']] = $row['name'];
                $gp_link[$row['childid']] = $row['href'];
            }
        ?>
        <div class="m-0 mt-2 wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <div class="card card-purple card-tabs">
                                <div class="card-header p-0">
                                    <ul class="pt-1 nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                                    <li class=" pt-2 px-3"><h3 class="card-title"><?php echo $cname; ?></h3></li>
                                    <?php
                                    $c = 0;
                                    foreach($gc_id as $childid){
                                        if($childid != ""){
                                        $c++;
                                    ?>
                                    <li class="nav-item">
                                        <a <?php if($c == 1){ echo 'class="nav-link active"'; } else{ echo 'class="nav-link"'; } ?> id="<?php echo $childid."-tab"; ?>" data-toggle="pill" href="<?php echo "#".$childid; ?>" role="tab" aria-controls="<?php echo $childid; ?>" <?php if($c == 1){ echo 'aria-selected="true"'; } else{ echo 'aria-selected="false"'; } ?>><?php echo $gp_name[$childid]; ?></a>
                                    </li>
                                    <?php
                                        }
                                    }
                                    ?>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <?php
                                    $c = 0;
                                    foreach($gc_id as $childid){
                                        if($childid != ""){
                                        $c++;
                                    ?>
                                    <div <?php if($c == 1){ echo 'class="tab-pane fade show active"'; } else{ echo 'class="tab-pane fade"'; } ?> id="<?php echo $childid; ?>" role="tabpanel" aria-labelledby="<?php echo $childid."-tab"; ?>">
                                     <iframe src="<?php echo $gp_link[$childid]."?ccid=".$gc_id[$childid]; ?>" id="<?php echo "tab_".$c; ?>" style="width:100%;min-height:650px;border:none;"></iframe>
                                    </div>
                                    <?php
                                        }
                                    }
                                    ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

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
    <?php include "header_foot.php"; ?>
    </body>
</html>
<?php
}
else{
     header('location:index.php');
}
?>
