<?php
//master_create_financialyear.php
include "../newConfig.php"; include "header_head.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');

$database_name = $_SESSION['dbase'];
if($database_name == "poulso6_admin_broiler_broilermaster"){
    $sql = "SELECT DISTINCT(dblist) as dblist,account_access FROM `log_useraccess` WHERE `flag` = '1' AND `account_access` IN ('BTS','CTS','ATS') ORDER BY `dblist` ASC"; $query = mysqli_query($conns,$sql);
    while($row = mysqli_fetch_assoc($query)){ $db_name[$row['dblist']] = $row['dblist']; $db_ptype[$row['dblist']] = $row['account_access']; }
}
$database = "all";

$fdate = $tdate = date("d.m.Y"); $status = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = $_POST['fdate'];
    $tdate = $_POST['tdate'];
    $project_type = $_POST['project_type'];
    $database = $_POST['database'];
    $status = $_POST['status'];
}
$db_filter = array();
if($database == "all"){
    if($project_type == "all"){
        foreach($db_name as $db){
            $db_filter[$db] = $db;
        }
    }
    else{
        foreach($db_name as $db){
            if($db_ptype[$db] == $project_type){
                $db_filter[$db] = $db;
            }
        }
    }
}
else{
    $db_filter[$database] = $database;
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
            .col-md-6 {
                position: relative;  left: 200px;
                max-width: 0%;
            }
            .col-md-5{
                position: relative;  left: 200px;
            }
            div.dataTables_wrapper div.dataTables_filter {
                text-align: left;
            }
            table thead,
            table tfoot {
                position: sticky;
            }
            table thead {
            inset-block-start: 0;
            }
            table tfoot {
            inset-block-end: 0;
            }
        </style>
        <?php
          if($excel_type == "print"){
            echo '<style>body { padding:10px;text-align:center; } table { white-space: nowrap; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead2_empty_row { display:none; }
            .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
        }
        else{
            echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
            table.tbl { left:0;margin-right: auto;visibility:visible; }
            table.tbl2 { left:0;margin-right: auto; }
            .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
            .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
            .thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
            .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
            .report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
            .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
            
        }

       ?>
    </head>
    <body align="center">
        <table class="tbl" align="center"   width="1300px">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" width="1212px">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Create Master Files</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="master_create_financialyear.php" method="post" onsubmit="return checkval();">
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                        <th colspan="14">
                            <div class="row">
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group" style="width:120px;">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Type</label>
                                    <select name="project_type" id="project_type" class="form-control select2">
                                        <option value="all" <?php if($project_type == "all"){ echo "selected"; } ?>>-All-</option>
                                        <option value="ATS" <?php if($project_type == "ATS"){ echo "selected"; } ?>>-ATS-</option>
                                        <option value="BTS" <?php if($project_type == "BTS"){ echo "selected"; } ?>>-BTS-</option>
                                        <option value="CTS" <?php if($project_type == "CTS"){ echo "selected"; } ?>>-CTS-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Databases</label>
                                    <select name="database" id="database" class="form-control select2">
                                        <option value="all" <?php if($database == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($db_name as $fcode){ if($fcode != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($database == $fcode){ echo "selected"; } ?>><?php echo $fcode; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Status</label>
                                    <select name="status" id="status" class="form-control select2" style="width:160px;">
                                        <option value="display" <?php if($status == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="create" <?php if($status == "create"){ echo "selected"; } ?>>-Create-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <thead class="thead3" align="center">
                    <tr>
                        <th>Sl.No.</th>
                        <th>Database</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Existing Year</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="tbody1">
                <?php
                if(isset($_POST['submit_report']) == true){
                    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
                    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
                    $fyear = date("y",strtotime($fdate))."".date("y",strtotime($tdate));
                    $slno = 0;
                    foreach($db_filter as $dname){
                        $tconn = mysqli_connect("213.165.245.128","poulso6_userlist123","pK5dI%u19mQJ",$dname);
                        //$table_head = "Tables_in_".$dname;
                        $year_exist = $year_error_code = $generator_exist = $generator_error_code = 0; $exist_finyear = "";
                        $sql = "SELECT * FROM `main_financialyear` WHERE `prefix` = '$fyear'"; $query = mysqli_query($tconn,$sql);
                        $ccount = mysqli_num_rows($query);
                        if($ccount > 0){
                            $year_exist = 1;
                        }
                        else{
                            $sql = "INSERT INTO `main_financialyear` (prefix,fdate,tdate,flag,active,addedemp,addedtime) VALUES ('$fyear','$fdate','$tdate','0','1','$addedemp','$addedtime')";
                            if($status == "create"){ if(!mysqli_query($tconn,$sql)){ $year_error_code = 1; } }

                            $sql = "SELECT * FROM `master_generator` WHERE `fdate` = '$fdate' AND `tdate` = '$tdate' AND `type` LIKE 'transactions'"; $query = mysqli_query($tconn,$sql);
                            $ccount = mysqli_num_rows($query);
                            if($ccount > 0){
                                $generator_exist = 1;
                            }
                            else{
                                $sql = "INSERT INTO `master_generator` (type,fdate,tdate,active) VALUES ('transactions','$fdate','$tdate','1')";
                                if($status == "create"){ if(!mysqli_query($tconn,$sql)){ $generator_error_code = 1; } else{ $success_status = 1; } }
                            }

                        }
                        $sql = "SELECT * FROM `main_financialyear` WHERE `id` IN (SELECT MAX(id) as id FROM `main_financialyear` WHERE `active` = '1')"; $query = mysqli_query($tconn,$sql);
                        while($row = mysqli_fetch_assoc($query)){ $exist_finyear = date("d.m.Y",strtotime($row['fdate']))."-".date("d.m.Y",strtotime($row['tdate'])); }
                        $slno++;
                        echo '<tr>';
                        echo '<td>'.$slno.'</td>';
                        echo '<td>'.$dname.'</td>';
                        echo '<td>'.date("d.m.Y",strtotime($fdate)).'</td>';
                        echo '<td>'.date("d.m.Y",strtotime($tdate)).'</td>';
                        echo '<td>'.$exist_finyear.'</td>';
                        echo '<td>';
                        if($status == "create"){
                            if($year_exist == 1){
                                echo "<b style='color:green;'>Financial year Exist</b>";
                            }
                            else if($year_error_code == 1){
                                echo "<b style='color:red;'>Error creating Financial year</b>";
                            }
                            if($generator_exist == 1){
                                echo "<b style='color:green;'>Generators Exist</b>";
                            }
                            else if($generator_error_code == 1){
                                echo "<b style='color:red;'>Error creating Generators</b>";
                            }
                        }
                        else{
                            if($year_exist == 1){
                                echo "<b style='color:green;'>Financial year Exist</b>";
                            }
                            else{
                                echo "<b style='color:blue;'>Financial year Not Exist</b>";
                            }
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                }
                ?>
                </tbody>
            </form>
        </table>
        <script>
            function checkval(){
                var from_file = document.getElementById("from_file").value;
                var to_file = document.getElementById("to_file").value;
                var l = true;
                if(from_file == ""){
                    alert("Please enter From file name");
                    document.getElementById("from_file").focus();
                    l = false;
                }
                else if(to_file == ""){
                    alert("Please enter To file name");
                    document.getElementById("to_file").focus();
                    l = false;
                }
                else{ }

                if(l == true){
                    return true;
                }
                else{
                    return false;
                }
            }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php include "header_foot.php"; ?>