<?php
//broiler_customerledger_masterreport.php
$requested_data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION)) {
    session_start();
}
if (!empty($_GET['db'])) {
    $db = $_SESSION['db'] = $_SESSION['dbase'] = $_GET['db'];
} else {
    $db = '';
}
if ($db == '') {
    include "../newConfig.php";

    $sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $num_format_file = $row['num_format_file'];
    }
    if ($num_format_file == "") {
        $num_format_file = "number_format_ind.php";
    }
    include $num_format_file;

    include "header_head.php";
    $user_code = $_SESSION['userid'];
} else {
    //include "../newConfig.php";
    include "APIconfig.php";
    include "number_format_ind.php";
    include "header_head.php";
    $user_code = $_GET['userid'];
}

include "../broiler_check_tableavailability.php";

$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Arial";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Arial, sans-serif";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "Helvetica, Arial, sans-serif";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Verdana, sans-serif";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Tahoma, sans-serif";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Trebuchet MS";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Trebuchet MS', sans-serif";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "'Times New Roman'";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Times New Roman', serif";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Georgia, serif";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Garamond, serif";
//$i++; $font_family_code[$i] = $i; $font_family_name[$i] = "'Courier New', monospace";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Courier, monospace";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Optima";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Segoe";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Calibri";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Candara";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Lucida Grande";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Lucida Sans Unicode";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "Gill Sans";
$i++;
$font_family_code[$i] = $i;
$font_family_name[$i] = "'Source Sans Pro', 'Arial', sans-serif";

for ($i = 0; $i <= 30; $i++) {
    $fsizes[$i . "px"] = $i . "px";
}

$i = 0;

/*Master Report Format*/
$href = explode("/", $_SERVER['REQUEST_URI']);
$field_href = explode("?", $href[2]);
$col_names_all = $act_col_numbs = $nac_col_numbs = array();
$i = $col_count = 0;
$key_id = "";
if ($count64 > 0) {
    $sql1 = "SHOW COLUMNS FROM `broiler_reportfields`";
    $query1 = mysqli_query($conn, $sql1);
    while ($row1 = mysqli_fetch_assoc($query1)) {
        if ($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag") {
        } else {
            $col_names_all[$row1['Field']] = $row1['Field'];
            $i++;
        }
    }
    $sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
    $query2 = mysqli_query($conn, $sql2);
    $c1 = mysqli_num_rows($query2);
    $fbc = 999;
    if ($c1 > 0) {
        while ($row2 = mysqli_fetch_assoc($query2)) {
            foreach ($col_names_all as $cna) {
                $fas_details = explode(":", $row2[$cna]);
                if ($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] > 0) {
                    $key_id = $row2[$cna];
                    $act_col_numbs[$key_id] = $cna;
                    //echo "<br/>".$act_col_numbs[$key_id];
                    if ($cna == "customer_credit") {
                        if ($fbc > $fas_details[2]) {
                            $fbc = $fas_details[2];
                        }
                    }
                    if ($cna == "customer_debit") {
                        if ($fbc > $fas_details[2]) {
                            $fbc = $fas_details[2];
                        }
                    }
                    if ($cna == "customer_runningbalance") {
                        if ($fbc > $fas_details[2]) {
                            $fbc = $fas_details[2];
                        }
                    }
                } else if ($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] > 0) {
                    $key_id = $row2[$cna];
                    $nac_col_numbs[$key_id] = $cna;
                } else {
                }
            }
            $col_count = $row2['column_count'];
        }
    }
}
$farm_region = $farm_branch = $farm_line = $region_name = $branch_name = $line_name = $batch_name = $sector_name = $vehicle_code = $vehicle_name = $emp_code = $emp_name =
    $coa_code = $coa_name = $mode_code = $mode_name = $vendor_code = $vendor_ccode = $vendor_name = $obdate = $obtype = $obamt = $item_code = $item_name = $item_category = array();
if ($count26 > 0) {
    $sql = "SELECT * FROM `broiler_farm`";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $sector_name[$row['code']] = $row['description'];
        $farm_region[$row['code']] = $row['region_code'];
        $farm_branch[$row['code']] = $row['branch_code'];
        $farm_line[$row['code']] = $row['line_code'];
    }
}
if ($count12 > 0) {
    $sql = "SELECT * FROM `broiler_batch`";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $batch_name[$row['code']] = $row['description'];
    }
}
if ($count95 > 0) {
    $sql = "SELECT * FROM `location_region`";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $region_name[$row['code']] = $row['description'];
    }
}
if ($count93 > 0) {
    $sql = "SELECT * FROM `location_branch`";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $branch_name[$row['code']] = $row['description'];
    }
}
if ($count94 > 0) {
    $sql = "SELECT * FROM `location_line`";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $line_name[$row['code']] = $row['description'];
    }
}
if ($count86 > 0) {
    $sql = "SELECT * FROM `inv_sectors`";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $sector_name[$row['code']] = $row['description'];
    }
}
if ($count68 > 0) {
    $sql = "SELECT * FROM `broiler_vehicle`";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $vehicle_code[$row['code']] = $row['code'];
        $vehicle_name[$row['code']] = $row['registration_number'];
    }
}
if ($count25 > 0) {
    $sql = "SELECT * FROM `broiler_employee`";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $emp_code[$row['code']] = $row['code'];
        $emp_name[$row['code']] = $row['name'];
    }
}
if ($count2 > 0) {
    $sql = "SELECT * FROM `acc_coa` ";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $coa_code[$row['code']] = $row['code'];
        $coa_name[$row['code']] = $row['description'];
    }
}
if ($count4 > 0) {
    $sql = "SELECT * FROM `acc_modes`";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $mode_code[$row['code']] = $row['code'];
        $mode_name[$row['code']] = $row['description'];
    }
}
if ($count98 > 0) {
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `dflag` = '0' ORDER BY `name` ASC";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $vendor_code[$row['code']] = $row['code'];
        $vendor_ccode[$row['code']] = $row['cus_ccode'];
        $vendor_name[$row['code']] = $row['name'];
        $coa_name[$row['code']] = $row['name'];
        $obdate[$row['code']] = $row['obdate'];
        $obtype[$row['code']] = $row['obtype'];
        $obamt[$row['code']] = $row['obamt'];
    }
}
if ($count89 > 0) {
    $sql = "SELECT * FROM `item_details`";
    $query = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $item_code[$row['code']] = $row['code'];
        $item_name[$row['code']] = $row['description'];
        $item_category[$row['code']] = $row['category'];
    }
}
$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Customer Ledger Master' AND `field_function` = 'On Click:Transaction No to edit screen' AND (`user_access` = 'all' OR `user_access` = '$user_code') AND `flag` = '1'";
$query = mysqli_query($conn, $sql); $tronclick_edit_flag = mysqli_num_rows($query);

$fdate = $tdate = date("Y-m-d");
$vendors = "select";
$excel_type = "display";
$font_stype = "";
$font_size = "11px";
if (isset($_POST['submit_report']) == true) {
    $fdate = date("Y-m-d", strtotime($_POST['fdate']));
    $tdate = date("Y-m-d", strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];

    $font_stype = $_POST['font_stype'];
    $font_size = $_POST['font_size'];

    $excel_type = $_POST['export'];
    $url = "../PHPExcel/Examples/broiler_customerledger_masterreport-Excel.php?fromdate=" . $fdate . "&todate=" . $tdate . "&vendors=" . $vendors;
}
else if(isset($_REQUEST['fdate']) == true){
    $fdate = date("Y-m-d", strtotime($_REQUEST['fdate']));
    $tdate = date("Y-m-d", strtotime($_REQUEST['tdate']));
    $vendors = $_REQUEST['vendors'];
}
else {
    $url = "";
}
?>
<html>

<head>
    <title>Poulsoft Solutions</title>
    <script src="../../col/jquery-3.5.1.js"></script>
    <script src="../../col/jquery.dataTables.min.js"></script>
    <script>
        var exptype = '<?php echo $excel_type; ?>';
        var url = '<?php echo $url; ?>';
        if (exptype.match("excel")) {
            window.open(url, "_BLANK");
        }
    </script>
    <link href="../datepicker/jquery-ui.css" rel="stylesheet">
    <!---  <style>
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
            inset-block-start: 0; /* "top" */
            }
            table tfoot {
            inset-block-end: 0; /* "bottom" */
            }
        </style> --->
    <?php

    if ($excel_type == "print") {
        include "headerstyle_wprint_font.php";
    } else {

        include "headerstyle_woprint_font.php";
    }
    /* if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
               .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
				
                .thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#9CC2D5,#9CC2D5);}
                .thead2_empty_row { display:none; }
                .tbl_toggle { display:none; }
                .dataTables_filter { display:none; }
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
                
            }*/
    ?>
</head>

<body align="center">
    <table class="tbl" align="center" <?php if($excel_type == "print"){ echo ' id="mine"'; } else{ echo 'width="1300px"'; } ?>>
        <?php
        $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC";
        $query = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($query)) {
        ?>
            <thead class="thead1" align="center" width="1212px">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px" /></th>
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Customer Ledger Report</h5></br>
                        <h6><span style="color:red;"> From Date: </span><span style="color:green;"><b><?php echo date("d.m.Y", strtotime($fdate)); ?></b></span> &nbsp;&nbsp;&nbsp;<span style="color:red;">To Date: </span> <span style="color:green;"><b><?php echo date("d.m.Y", strtotime($tdate)); ?></b></span> &nbsp;&nbsp;</br><span style="color:red;">Customer: </span><span style="color:green;"><b><?php echo $vendor_name[$vendors]; ?></b></span> </h6>
                    </th>
                </tr>
            </thead>
        <?php } ?>
        <?php if ($db == '') { ?>
            <form action="broiler_customerledger_masterreport.php" method="post" onsubmit="return checkval();">
            <?php } else { ?>
                <form action="broiler_customerledger_masterreport.php?db=<?php echo $db; ?>&userid=<?php echo $user_code; ?>" method="post">
                <?php } ?>
                <thead class="thead2 text-primary layout-navbar-fixed" width="1212px">
                    <tr>
                        <th colspan="16">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y", strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y", strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Customer</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="select" <?php if ($vendors == "select") {
                                                                    echo "selected";
                                                                } ?>>-select-</option>
                                        <?php foreach ($vendor_code as $vcode) {
                                            if ($vendor_name[$vcode] != "") { ?>
                                                <option value="<?php echo $vcode; ?>" <?php if ($vendors == $vcode) {
                                                                                            echo "selected";
                                                                                        } ?>><?php echo $vendor_name[$vcode]; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Font Style</label>
                                    <select name="font_stype" id="font_stype" class="form-control select2"> <!-- onchange="update_font_family()"-->
                                        <option value="" <?php if($font_stype == ""){ echo "selected"; } ?>>-Defalut-</option>
                                        <?php
                                        foreach($font_family_code as $i){
                                        ?>
                                        <option value="<?php echo $font_family_name[$i]; ?>" <?php if($font_stype == $font_family_name[$i]){ echo "selected"; } ?>><?php echo $font_family_name[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Font Size</label>
                                    <select name="font_size" id="font_size" class="form-control select2">
                                        <?php
                                        foreach($fsizes as $i){
                                        ?>
                                        <option value="<?php echo $fsizes[$i]; ?>" <?php if($font_size == $fsizes[$i]){ echo "selected"; } ?>><?php echo $fsizes[$i]; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2">
                                        <option value="display" <?php if ($excel_type == "display") {
                                                                    echo "selected";
                                                                } ?>>-Display-</option>
                                        <option value="excel" <?php if ($excel_type == "excel") {
                                                                    echo "selected";
                                                                } ?>>-Excel-</option>
                                        <option value="print" <?php if ($excel_type == "print") {
                                                                    echo "selected";
                                                                } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <br />
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                </form>
                <?php if($excel_type == "print"){ } else{ ?>
    </table>
    <table class="tbl_toggle" style="position: relative;  left: 35px;">
        <tr>
            <td><br></td>
        </tr>
        <tr>
            <td>
                <div id='control_sh'>
                    <?php
                    for ($i = 1; $i <= $col_count; $i++) {
                        $key_id = "A:1:" . $i;
                        $key_id1 = "A:0:" . $i;
                        if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_trnum") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_trnum" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Transaction No.</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_link_trnum") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_link_trnum" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Linked Transaction</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_date") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_date" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Date</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_name") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_name" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Customer Name</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_billno") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_billno" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Doc No.</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_itemname") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_itemname" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Item Name</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_birds") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_birds" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Birds</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_snt_qty") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_snt_qty" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Sent Qty</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_mort_qty") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_mort_qty" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Mort Qty</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_cull_qty") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_cull_qty" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Cull Qty</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_rcd_qty") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_rcd_qty" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Quantity</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_fre_qty") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_fre_qty" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Free Qty</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_itemprice") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_itemprice" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Item Price</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_dis_per") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_dis_per" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Discount %</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_dis_amt") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_dis_amt" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Discount Amt</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_gst_per") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_gst_per" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>GST %</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_gst_amt") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_gst_amt" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>GST Amt</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_tcds_per") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_tcds_per" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>TCS %</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_tcds_amt") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_tcds_amt" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>TCS Amt</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_itemamount") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_itemamount" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Item Amount</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_freight_type") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_freight_type" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Freight Type</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_freight_amt") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_freight_amt" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Freight Amt</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_freight_pay_type") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_freight_pay_type" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Freight Pay Type</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_freight_pay_acc") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_freight_pay_acc" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Freight Pay Account</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_freight_acc") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_freight_acc" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Freight Account</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_round_off") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_round_off" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Round Off</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_finl_amt") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_finl_amt" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Invoice Amount</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_avg_price") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_avg_price" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Avg Price</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_avg_wt") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_avg_wt" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Avg Weight</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_profit") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_profit" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Profit</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_remarks") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_remarks" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Remarks</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_warehouse") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_warehouse" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Sector/Warehouse</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_farm_batch") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_farm_batch" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Batch Name</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_supervisor_code") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_supervisor_code" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Supervisor Name</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_bag_code") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_bag_code" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Bag Name</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_bag_count") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_bag_count" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Bag Count</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_batch_no") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_batch_no" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Batch No</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_exp_date") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_exp_date" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Expiry Date</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_vehicle_code") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_vehicle_code" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Vehicle No.</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_driver_code") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_driver_code" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Driver Name</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_sale_type") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_sale_type" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Sale Type</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_gc_flag") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_gc_flag" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>GC Flag</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_addedemp") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_addedemp" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Added By</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_addedtime") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_addedtime" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Added Time</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_updatedemp") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_updatedemp" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Edited By</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_updatedtime") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_updatedtime" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Edited Time</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_latitude") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_latitude" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Sale Latitude</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_longitude") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_longitude" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Sale Longitude</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "Customer_sale_location") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="Customer_sale_location" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Sale Location</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_imei") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_imei" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>IMEI</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_mob_flag") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_mob_flag" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Mobile Flag</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_sale_image") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_sale_image" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Sale Image</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "region_name") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="region_name" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Region Name</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "branch_name") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="branch_name" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Branch Name</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "line_name") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="line_name" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Line Name</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_credit") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_credit" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Debit</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_debit") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_debit" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Credit</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_runningbalance") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_runningbalance" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Balance</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "transaction_type") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="transaction_type" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Type</span>';
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_shipping_address") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_shipping_address" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Shipping Address    </span>';
                           
                        }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode" || !empty($nac_col_numbs[$key_id1]) && $nac_col_numbs[$key_id1] == "customer_ccode") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="customer_ccode" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Customer Code </span>';
                           
                        } else {
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><br></td>
        </tr>
    </table>
    <table id="mine" class="tbl" align="center" style="width:1300px;">
    <?php } ?>
        <thead class="thead3" align="center" style="width:1212px;">
            <tr align="center">
                <?php
                $pbc = $fbh = 0;
                for ($i = 1; $i <= $col_count; $i++) {
                    $key_id = "A:1:" . $i;
                    if (!empty($act_col_numbs[$key_id])) {
                        if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") {
                            echo "<th id='order'>Transaction No.</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                            echo "<th id='order'>Linked Transaction</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                            echo "<th id='order_date'>Date</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                            echo "<th id='order'>Customer Name</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                            echo "<th id='order'>Doc No.</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                            echo "<th id='order'>Item Name</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                            echo "<th id='order_num'>Birds</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                            echo "<th id='order_num'>Sent Qty</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                            echo "<th id='order_num'>Mort Qty</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                            echo "<th id='order_num'>Cull Qty</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                            echo "<th id='order_num'>Quantity</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                            echo "<th id='order_num'>Free Qty</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                            echo "<th id='order_num'>Item Price</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                            echo "<th id='order_num'>Discount %</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                            echo "<th id='order_num'>Discount Amt</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                            echo "<th id='order_num'>GST %</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                            echo "<th id='order_num'>GST Amt</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                            echo "<th id='order_num'>TCS %</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                            echo "<th id='order_num'>TCS Amt</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                            echo "<th id='order_num'>Item Amount</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                            echo "<th id='order'>Freight Type</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                            echo "<th id='order_num'>Freight Amt</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                            echo "<th id='order'>Freight Pay Type</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                            echo "<th id='order'>Freight Pay Account</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                            echo "<th id='order'>Freight Account</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                            echo "<th id='order_num'>Round Off</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                            echo "<th id='order_num'>Invoice Amount</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                            echo "<th id='order_num'>Avg Price</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                            echo "<th id='order_num'>Avg Weight</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                            echo "<th id='order_num'>Profit</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                            echo "<th id='order'>Remarks</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                            echo "<th id='order'>Sector/Warehouse</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                            echo "<th id='order'>Batch Name</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                            echo "<th id='order'>Supervisor Name</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                            echo "<th id='order'>Bag Name</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                            echo "<th id='order'>Bag Count</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                            echo "<th id='order'>Batch No</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                            echo "<th id='order'>Expiry Date</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                            echo "<th id='order'>Vehicle No.</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                            echo "<th id='order'>Driver Name</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                            echo "<th id='order'>Sale Type</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                            echo "<th id='order'>GC Flag</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                            echo "<th id='order'>Added By</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                            echo "<th id='order'>Added Time</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                            echo "<th id='order'>Edited By</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                            echo "<th id='order'>Edited Time</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                            echo "<th id='order'>Sale Latitude</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                            echo "<th id='order'>Sale Longitude</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                            echo "<th id='order'>Sale Location</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                            echo "<th id='order'>IMEI</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                            echo "<th id='order'>Mobile Flag</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                            echo "<th id='order'>Sale Image</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                            echo "<th id='order'>Region Name</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                            echo "<th id='order'>Branch Name</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                            echo "<th id='order'>Line Name</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                            echo "<th id='order_num'>Debit</th>";
                            $pbc++;
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                            echo "<th id='order_num'>Credit</th>";
                            $pbc++;
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                            echo "<th id='order_num'>Balance</th>";
                            $pbc++;
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                            echo "<th id='order'>Type</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                            echo "<th id='order'>Shipping Address</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                            echo "<th id='order'>Customer Code</th>";
                            if ($fbc > $i) {
                                $fbh++;
                            }
                        } else {
                        }
                    }
                }
                ?>
            </tr>
        </thead>

        <?php
        if (isset($_POST['submit_report']) == true || isset($_REQUEST['fdate']) == true) {
        ?>
            
                <?php
                $old_inv = "";
                $opening_sales = $opening_receipts = $opening_ccn = $opening_cdn = $opening_cntcr = $opening_cntdr = $opening_returns = $opening_tloss = $rb_amt = 0;
                if ($count65 > 0) {
                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            if ($old_inv != $row['trnum']) {
                                $opening_sales += (float)$row['finl_amt'];
                                $old_inv = $row['trnum'];
                            }
                        }
                    }
                }
                if ($count63 > 0) {
                    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` < '$fdate' AND `ccode` = '$vendors' AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $opening_receipts += (float)$row['amount'];
                        }
                    }
                }
                if ($count54 > 0) {
                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $opening_returns += (float)$row['amount'];
                        }
                    }
                }
                if ($count17 > 0) {
                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            if ($row['crdr'] == "Credit") {
                                $opening_ccn += (float)$row['amount'];
                            } else {
                                $opening_cdn += (float)$row['amount'];
                            }
                        }
                    }
                }
                if ($count7 > 0) {
                    $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` < '$fdate' AND `fcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $opening_cntcr += (float)$row['amount'];
                        }
                    }

                    $sql_record = "SELECT SUM(amount) as amount FROM `account_contranotes` WHERE `date` < '$fdate' AND `tcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $opening_cntdr += (float)$row['amount'];
                        }
                    }
                }
                if ($count130 > 0) {
                    $sql_record = "SELECT SUM(amount) as amount FROM `broiler_transitloss` WHERE `date` < '$fdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `rtype` IN ('TransitLoss') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record); $i = 0; $transaction_count = 0; if (!empty($query)) { $transaction_count = mysqli_num_rows($query); }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $opening_tloss += (float)$row['amount'];
                        }
                    }
                }

                $ob_cramt = $ob_dramt = 0;
                if ($obtype[$vendors] == "Cr") {
                    $ob_cramt = (float)$obamt[$vendors];
                } else {
                    $ob_dramt = (float)$obamt[$vendors];
                }

                $ob_rcv = (float)$opening_sales + (float)$opening_cdn + (float)$opening_cntdr + (float)$ob_dramt;
                $ob_pid = (float)$opening_receipts + (float)$opening_returns + (float)$opening_ccn + (float)$opening_cntcr + (float)$opening_tloss + (float)$ob_cramt;

                $cr_amt = $dr_amt = $rb_amt = 0;
                if ((float)$ob_rcv >= (float)$ob_pid) {
                    $dr_amt = (float)$ob_rcv - (float)$ob_pid;
                } else {
                    $cr_amt = (float)$ob_pid - (float)$ob_rcv;
                }
                $rb_amt += ((float)$ob_rcv - (float)$ob_pid);

                if ($pbc > 0) {
                    $pbl = 1;
                    echo "<tr>";
                    if ($col_count > $pbc) {
                        echo "<td colspan='" . $fbh . "' style='font-weight:bold;text-align:center;'>Previous Balance</td>";
                        $pbl = $fbc;
                    } else {
                    }
                    for ($i = $pbl; $i <= $col_count; $i++) {
                        $key_id = "A:1:" . $i;
                        if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                            echo "<td style='font-weight:bold;text-align:right;'>" . number_format_ind($cr_amt) . "</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                            echo "<td style='font-weight:bold;text-align:right;'>" . number_format_ind($dr_amt) . "</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                            echo "<td style='font-weight:bold;text-align:right;'>" . number_format_ind($rb_amt) . "</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                            echo "<td></td>";
                        }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                            echo "<td></td>";
                        }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                            echo "<td></td>";
                        } else {
                        }
                    }
                    echo "</tr>";
                }
                
                echo "<tbody class='tbody1' id = 'tbody1'>";

                $key_code = "";
                $sale_info = $receipt_info = $return_info = $ccn_info = $cdn_info = $inv_count = $contra_cr = $contra_dr = $tloss_info = $tloss_amt = $tloss_count = array();
                if ($count65 > 0) {
                    $sql_record = "SELECT * FROM `broiler_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $i = 0;
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $i++;
                            $key_code = $row['date'] . "@" . $i;
                            $sale_info[$key_code] = $row['trnum'] . "@" . $row['link_trnum'] . "@" . $row['date'] . "@" . $row['vcode'] . "@" . $row['billno'] . "@" . $row['icode'] . "@" . $row['birds'] . "@" . $row['snt_qty'] . "@" . $row['mort_qty'] . "@" . $row['cull_qty'] . "@" . $row['rcd_qty'] . "@" . $row['fre_qty'] . "@" . $row['rate'] . "@" . $row['dis_per'] . "@" . $row['dis_amt'] . "@" . $row['gst_per'] . "@" . $row['gst_amt'] . "@" . $row['tcds_per'] . "@" . $row['tcds_amt'] . "@" . $row['item_tamt'] . "@" . $row['freight_type'] . "@" . $row['freight_amt'] . "@" . $row['freight_pay_type'] . "@" . $row['freight_pay_acc'] . "@" . $row['freight_acc'] . "@" . $row['round_off'] . "@" . $row['finl_amt'] . "@" . $row['avg_price'] . "@" . $row['avg_wt'] . "@" . $row['profit'] . "@" . $row['remarks'] . "@" . $row['warehouse'] . "@" . $row['farm_batch'] . "@" . $row['supervisor_code'] . "@" . $row['bag_code'] . "@" . $row['bag_count'] . "@" . $row['batch_no'] . "@" . $row['exp_date'] . "@" . $row['vehicle_code'] . "@" . $row['driver_code'] . "@" . $row['sale_type'] . "@" . $row['gc_flag'] . "@" . $row['addedemp'] . "@" . $row['addedtime'] . "@" . $row['updatedemp'] . "@" . $row['updatedtime'] . "@" . $row['latitude'] . "@" . $row['longitude'] . "@" . $row['imei'] . "@" . $row['mob_flag'] . "@" . $row['sale_image']."@" . $row['shipping_address'];
                            if (!empty($inv_count[$row['trnum']])) {
                                $inv_count[$row['trnum']] += 1;
                            } else {
                                $inv_count[$row['trnum']] = 1;
                            }
                        }
                    }
                }
                if ($count63 > 0) {
                    $sql_record = "SELECT * FROM `broiler_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$vendors' AND `vtype` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $i = 0;
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $i++;
                            $key_code = $row['date'] . "@" . $i;
                            $receipt_info[$key_code] = $row['trnum'] . "@" . $row['date'] . "@" . $row['ccode'] . "@" . $row['docno'] . "@" . $row['mode'] . "@" . $row['method'] . "@" . $row['amount'] . "@" . $row['remarks'] . "@" . $row['warehouse'] . "@" . $row['addedemp'] . "@" . $row['addedtime'] . "@" . $row['updatedemp'] . "@" . $row['updatedtime'];
                        }
                    }
                }
                if ($count54 > 0) {
                    $sql_record = "SELECT * FROM `broiler_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $i = 0;
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $i++;
                            $key_code = $row['date'] . "@" . $i;
                            $return_info[$key_code] = $row['trnum'] . "@" . $row['link_trnum'] . "@" . $row['date'] . "@" . $row['vcode'] . "@" . $row['itemcode'] . "@" . $row['birds'] . "@" . $row['quantity'] . "@" . $row['price'] . "@" . $row['gst_per'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['addedemp'] . "@" . $row['addedtime'] . "@" . $row['updatedemp'] . "@" . $row['updatedtime'];
                        }
                    }
                }
                if ($count17 > 0) {
                    $sql_record = "SELECT * FROM `broiler_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $i = $j = 0;
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            if ($row['crdr'] == "Credit") {
                                $i++;
                                $key_code = $row['date'] . "@" . $i;
                                $ccn_info[$key_code] = $row['trnum'] . "@" . $row['link_trnum'] . "@" . $row['date'] . "@" . $row['vcode'] . "@" . $row['docno'] . "@" . $row['coa'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['addedemp'] . "@" . $row['addedtime'] . "@" . $row['updatedemp'] . "@" . $row['updatedtime'];
                            } else {
                                $j++;
                                $key_code = $row['date'] . "@" . $j;
                                $cdn_info[$key_code] = $row['trnum'] . "@" . $row['link_trnum'] . "@" . $row['date'] . "@" . $row['vcode'] . "@" . $row['docno'] . "@" . $row['coa'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['addedemp'] . "@" . $row['addedtime'] . "@" . $row['updatedemp'] . "@" . $row['updatedtime'];
                            }
                        }
                    }
                }
                if ($count7 > 0) {
                    $sql_record = "SELECT * FROM `account_contranotes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `fcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $i = 0;
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $i++;
                            $key_code = $row['date'] . "@" . $i;
                            $contra_cr[$key_code] = $row['trnum'] . "@" . $row['date'] . "@" . $row['dcno'] . "@" . $row['fcoa'] . "@" . $row['tcoa'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['addedemp'] . "@" . $row['addedtime'] . "@" . $row['updatedemp'] . "@" . $row['updatedtime'];
                        }
                    }

                    $sql_record = "SELECT * FROM `account_contranotes` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `tcoa` = '$vendors' AND `type` IN ('ContraNote') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record);
                    $i = 0;
                    $transaction_count = 0;
                    if (!empty($query)) {
                        $transaction_count = mysqli_num_rows($query);
                    }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $i++;
                            $key_code = $row['date'] . "@" . $i;
                            $contra_dr[$key_code] = $row['trnum'] . "@" . $row['date'] . "@" . $row['dcno'] . "@" . $row['fcoa'] . "@" . $row['tcoa'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['addedemp'] . "@" . $row['addedtime'] . "@" . $row['updatedemp'] . "@" . $row['updatedtime'];
                        }
                    }
                }
                if ($count130 > 0) {
                    $sql_record = "SELECT * FROM `broiler_transitloss` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$vendors' AND `type` IN ('Customer') AND `rtype` IN ('TransitLoss') AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
                    $query = mysqli_query($conn, $sql_record); $i = 0; $transaction_count = 0; if (!empty($query)) { $transaction_count = mysqli_num_rows($query); }
                    if ($transaction_count > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $i++; $key_code = $row['date']."@".$i;
                            $tloss_info[$key_code] = $row['trnum']."@".$row['date']."@".$row['link_trnum']."@".$row['vcode']."@".$row['itemcode']."@".$row['birds']."@".$row['quantity']."@".$row['price']."@".$row['gst_per']."@".$row['amount']."@".$row['warehouse']."@".$row['stk_status']."@".$row['remarks'];
                            $tloss_amt[$row['trnum']] += (float)$row['amount'];
                            $tloss_count[$row['trnum']] += 1;
                        }
                    }
                }

                $sale_ccount = sizeof($sale_info);
                $receipt_ccount = sizeof($receipt_info);
                $return_ccount = sizeof($return_info);
                $ccn_ccount = sizeof($ccn_info);
                $cdn_ccount = sizeof($cdn_info);
                $cdr_ccount = sizeof($contra_dr);
                $ccr_ccount = sizeof($contra_cr);
                $tlb_ccount = sizeof($tloss_info);

                $exist_inv = "";
                $bt_sale_amt = $bt_rct_amt = $tot_birds = $tot_rqty = $tot_iamt = $tot_tcds_amt = 0;
                for ($cdate = strtotime($fdate); $cdate <= strtotime($tdate); $cdate += (86400)) {
                    $adate = date('Y-m-d', $cdate);

                    //Sale Entries
                    for ($a = 0; $a <= $sale_ccount; $a++) {
                        if (!empty($sale_info[$adate . "@" . $a])) {
                            $sales_details = explode("@", $sale_info[$adate . "@" . $a]);

                            $rcode = $rname = $bcode = $bname = $lcode = $lname = $fbname = $fcode = "";
                            $fcode = $sales_details[31];
                            if (!empty($farm_region[$fcode])) {
                                $rcode = $farm_region[$fcode];
                                if (!empty($region_name[$rcode])) {
                                    $rname = $region_name[$rcode];
                                }
                            }
                            if (!empty($farm_branch[$fcode])) {
                                $bcode = $farm_branch[$fcode];
                                if (!empty($branch_name[$bcode])) {
                                    $bname = $branch_name[$bcode];
                                }
                            }
                            if (!empty($farm_line[$fcode])) {
                                $lcode = $farm_line[$fcode];
                                if (!empty($line_name[$lcode])) {
                                    $lname = $line_name[$lcode];
                                }
                            }
                            if (!empty($batch_name[$sales_details[32]])) {
                                if (!empty($batch_name[$fcode])) {
                                    $fbname = $batch_name[$fcode];
                                }
                            }

                            $tot_birds += (float)$sales_details[6];
                            $tot_rqty += (float)$sales_details[10];
                            $tot_iamt += (float)$sales_details[19];

                            if ($exist_inv != $sales_details[0]) {
                                $exist_inv = $sales_details[0];
                                $bt_sale_amt += (float)$sales_details[26];
                                $dr_amt += (float)$sales_details[26];
                                $rb_amt += (float)$sales_details[26];
                                $tot_tcds_amt += (float)$sales_details[18];
                                echo "<tr>";
                                for ($i = 1; $i <= $col_count; $i++) {
                                    $key_id = "A:1:" . $i;
                                    $link_dt = "";
                                    if(!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum"){
                                        if((int)$tronclick_edit_flag == 1 && $_SESSION['dbase'] == "vpspoulsoft_broiler_ap_saishivoham"){
                                            $link_dt = "https://broiler.poulsoft.co.in/broiler_edit_sc_sales.php?utype=edit&trnum=".$sales_details[0];
                                            echo "<td  style='text-align:left;' title='Transaction No.' rowspan=" . $inv_count[$sales_details[0]] . "><a href='$link_dt' target='_BLANK'>" . $sales_details[0] . "</a></td>";
                                        }
                                        else{
                                            echo "<td  style='text-align:left;' title='Transaction No.' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[0] . "</td>";
                                        }
                                        
                                    }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                                        echo "<td  style='text-align:left;' title='Linked Transaction' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[1] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                                        echo "<td  style='text-align:left;' title='Date' rowspan=" . $inv_count[$sales_details[0]] . ">" . date('d.m.Y', strtotime($sales_details[2])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                                        echo "<td  style='text-align:left;' title='Customer Name' rowspan=" . $inv_count[$sales_details[0]] . ">" . $vendor_name[$sales_details[3]] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                                        echo "<td  style='text-align:left;' title='Doc No.' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[4] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                                        echo "<td  style='text-align:left;' title='Item Name'>" . $item_name[$sales_details[5]] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                                        echo "<td  style='text-align:right;' title='Birds'>" . str_replace('.00', '', number_format_ind($sales_details[6])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                                        echo "<td  style='text-align:right;' title='Sent Qty'>" . number_format_ind($sales_details[7]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                                        echo "<td  style='text-align:right;' title='Mort Qty'>" . str_replace('.00', '', number_format_ind($sales_details[8])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                                        echo "<td  style='text-align:right;' title='Cull Qty'>" . str_replace('.00', '', number_format_ind($sales_details[9])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                                        echo "<td  style='text-align:right;' title='Quantity'>" . number_format_ind($sales_details[10]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                                        echo "<td  style='text-align:right;' title='Free Qty'>" . number_format_ind($sales_details[11]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                                        echo "<td  style='text-align:right;' title='Item Price'>" . number_format_ind($sales_details[12]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                                        echo "<td  style='text-align:right;' title='Discount %'>" . number_format_ind($sales_details[13]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                                        echo "<td  style='text-align:right;' title='Discount Amt'>" . number_format_ind($sales_details[14]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                                        echo "<td  style='text-align:right;' title='GST %'>" . number_format_ind($sales_details[15]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                                        echo "<td  style='text-align:right;' title='GST Amt'>" . number_format_ind($sales_details[16]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                                        echo "<td  style='text-align:right;' title='TCS %' rowspan=" . $inv_count[$sales_details[0]] . ">" . number_format_ind($sales_details[17]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                                        echo "<td  style='text-align:right;' title='TCS Amt' rowspan=" . $inv_count[$sales_details[0]] . ">" . number_format_ind($sales_details[18]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                                        echo "<td  style='text-align:right;' title='Item Amount'>" . number_format_ind($sales_details[19]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                                        echo "<td  style='text-align:left;' title='Freight Type' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[20] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                                        echo "<td  style='text-align:right;' title='Freight Amt' rowspan=" . $inv_count[$sales_details[0]] . ">" . number_format_ind($sales_details[21]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                                        echo "<td  style='text-align:left;' title='Freight Pay Type' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[22] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                                        if (!empty($coa_name[$sales_details[23]])) {
                                            echo "<td  style='text-align:left;' title='Freight Pay Account' rowspan=" . $inv_count[$sales_details[0]] . ">" . $coa_name[$sales_details[23]] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Freight Pay Account' rowspan=" . $inv_count[$sales_details[0]] . "></td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                                        if (!empty($coa_name[$sales_details[24]])) {
                                            echo "<td  style='text-align:left;' title='Freight Account' rowspan=" . $inv_count[$sales_details[0]] . ">" . $coa_name[$sales_details[24]] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Freight Account' rowspan=" . $inv_count[$sales_details[0]] . "></td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                                        echo "<td  style='text-align:right;' title='Round Off' rowspan=" . $inv_count[$sales_details[0]] . ">" . number_format_ind($sales_details[25]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                                        echo "<td  style='text-align:right;' title='Invoice Amount' rowspan=" . $inv_count[$sales_details[0]] . ">" . number_format_ind($sales_details[26]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                                        echo "<td  style='text-align:right;' title='Avg Price'>" . number_format_ind($sales_details[27]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                                        if (!empty($sales_details[6]) && $sales_details[6] != 0) {
                                            $t1 = 0;
                                            $t1 = $sales_details[10] / $sales_details[6];
                                        } else {
                                            $t1 = 0;
                                        }
                                        echo "<td  style='text-align:right;' title='Avg Weight'>" . number_format_ind(round(($t1), 2)) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                                        echo "<td  style='text-align:right;' title='Profit' rowspan=" . $inv_count[$sales_details[0]] . ">" . number_format_ind($sales_details[29]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                                        echo "<td  style='text-align:left;' title='Remarks' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[30] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                                        if (!empty($sector_name[$fcode])) {
                                            echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $sector_name[$fcode] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $fcode . "</td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                                        echo "<td  style='text-align:left;' title='Batch Name'>" . $fbname . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                                        if (!empty($emp_name[$sales_details[33]])) {
                                            echo "<td  style='text-align:left;' title='Supervisor Name'>" . $emp_name[$sales_details[33]] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Supervisor Name'></td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                                        echo "<td  style='text-align:left;' title='Bag Name' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[34] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                                        echo "<td  style='text-align:right;' title='Bag Count' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[35] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                                        echo "<td  style='text-align:left;' title='Batch No' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[36] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                                        if (date('d.m.Y', strtotime($sales_details[37])) == "01.01.1970") {
                                            echo "<td  style='text-align:left;' title='Expiry Date' rowspan=" . $inv_count[$sales_details[0]] . "></td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Expiry Date' rowspan=" . $inv_count[$sales_details[0]] . ">" . date('d.m.Y', strtotime($sales_details[37])) . "</td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                                        if (!empty($vehicle_name[$sales_details[38]])) {
                                            echo "<td  style='text-align:left;' title='Vehicle No.'>" . $vehicle_name[$sales_details[38]] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Vehicle No.'>" . $sales_details[38] . "</td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                                        if (!empty($emp_name[$sales_details[39]])) {
                                            echo "<td  style='text-align:left;' title='Driver Name'>" . $emp_name[$sales_details[39]] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Driver Name'>" . $sales_details[39] . "</td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                                        echo "<td  style='text-align:left;' title='Sale Type' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[40] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                                        echo "<td  style='text-align:right;' title='GC Flag'>" . str_replace('.00', '', number_format_ind($sales_details[41])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                                        if (!empty($emp_name[$sales_details[42]])) {
                                            echo "<td  style='text-align:left;' title='Added By' rowspan=" . $inv_count[$sales_details[0]] . ">" . $emp_name[$sales_details[42]] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Added By' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[42] . "</td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                                        echo "<td  style='text-align:left;' title='Added Time' rowspan=" . $inv_count[$sales_details[0]] . ">" . date('d.m.Y h:i:sA', strtotime($sales_details[43])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                                        if (!empty($emp_name[$sales_details[44]])) {
                                            echo "<td  style='text-align:left;' title='Edited By' rowspan=" . $inv_count[$sales_details[0]] . ">" . $emp_name[$sales_details[44]] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Edited By' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[44] . "</td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                                        echo "<td  style='text-align:left;' title='Edited Time' rowspan=" . $inv_count[$sales_details[0]] . ">" . date('d.m.Y h:i:sA', strtotime($sales_details[45])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                                        echo "<td  style='text-align:right;' title='Sale Latitude' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[46] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                                        echo "<td  style='text-align:right;' title='Sale Longitude' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[47] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                                        echo "<td  style='text-align:left;' title='Sale Location' rowspan=" . $inv_count[$sales_details[0]] . "></td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                                        echo "<td  style='text-align:left;' title='IMEI' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[48] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                                        echo "<td  style='text-align:right;' title='Mobile Flag' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[49] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                                        if (!empty($sales_details[50])) {
                                            echo "<td  style='text-align:left;' title='Sale Image' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[50] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Sale Image' rowspan=" . $inv_count[$sales_details[0]] . "></td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                                        echo "<td  style='text-align:left;' title='Region Name'>" . $rname . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                                        echo "<td  style='text-align:left;' title='Branch Name'>" . $bname . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                                        echo "<td  style='text-align:left;' title='Line Name'>" . $lname . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                                        echo "<td  style='text-align:right;' title='Cr' rowspan=" . $inv_count[$sales_details[0]] . "></td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                                        echo "<td  style='text-align:right;' title='Dr' rowspan=" . $inv_count[$sales_details[0]] . ">" . number_format_ind($sales_details[26]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                                        echo "<td  style='text-align:right;' title='Balance' rowspan=" . $inv_count[$sales_details[0]] . ">" . number_format_ind($rb_amt) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                                        echo "<td  style='text-align:left;' title='Type' rowspan=" . $inv_count[$sales_details[0]] . ">Sales Invoice</td>";
                                    }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                                        echo "<td  style='text-align:left;' title='Sale Image' rowspan=" . $inv_count[$sales_details[0]] . ">" . $sales_details[51] . "</td>";
                                    }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                                        echo "<td  style='text-align:left;' title='Sale Image' rowspan=" . $vendor_ccode[$sales_details[3]] . "</td>";
                                    } else {
                                    }
                                }
                                echo "</tr>";
                            } else {
                                echo "<tr>";
                                for ($i = 1; $i <= $col_count; $i++) {
                                    $key_id = "A:1:" . $i;

                                    if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                                        echo "<td  style='text-align:left;' title='Item Name'>" . $item_name[$sales_details[5]] . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                                        echo "<td  style='text-align:right;' title='Birds'>" . str_replace('.00', '', number_format_ind($sales_details[6])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                                        echo "<td  style='text-align:right;' title='Sent Qty'>" . number_format_ind($sales_details[7]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                                        echo "<td  style='text-align:right;' title='Mort Qty'>" . str_replace('.00', '', number_format_ind($sales_details[8])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                                        echo "<td  style='text-align:right;' title='Cull Qty'>" . str_replace('.00', '', number_format_ind($sales_details[9])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                                        echo "<td  style='text-align:right;' title='Quantity'>" . number_format_ind($sales_details[10]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                                        echo "<td  style='text-align:right;' title='Free Qty'>" . number_format_ind($sales_details[11]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                                        echo "<td  style='text-align:right;' title='Item Price'>" . number_format_ind($sales_details[12]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                                        echo "<td  style='text-align:right;' title='Discount %'>" . number_format_ind($sales_details[13]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                                        echo "<td  style='text-align:right;' title='Discount Amt'>" . number_format_ind($sales_details[14]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                                        echo "<td  style='text-align:right;' title='GST %'>" . number_format_ind($sales_details[15]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                                        echo "<td  style='text-align:right;' title='GST Amt'>" . number_format_ind($sales_details[16]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                                        echo "<td  style='text-align:right;' title='Item Amount'>" . number_format_ind($sales_details[19]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                                        echo "<td  style='text-align:right;' title='Avg Price'>" . number_format_ind($sales_details[27]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                                        echo "<td  style='text-align:right;' title='Avg Weight'>" . number_format_ind($sales_details[28]) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                                        if (!empty($sector_name[$fcode])) {
                                            echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $sector_name[$fcode] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $fcode . "</td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                                        echo "<td  style='text-align:left;' title='Batch Name'>" . $fbname . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                                        if (!empty($emp_name[$sales_details[33]])) {
                                            echo "<td  style='text-align:left;' title='Supervisor Name'>" . $emp_name[$sales_details[33]] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Supervisor Name'></td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                                        if (!empty($vehicle_name[$sales_details[38]])) {
                                            echo "<td  style='text-align:left;' title='Vehicle No.'>" . $vehicle_name[$sales_details[38]] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Vehicle No.'>" . $sales_details[38] . "</td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                                        if (!empty($emp_name[$sales_details[39]])) {
                                            echo "<td  style='text-align:left;' title='Driver Name'>" . $emp_name[$sales_details[39]] . "</td>";
                                        } else {
                                            echo "<td  style='text-align:left;' title='Driver Name'>" . $sales_details[39] . "</td>";
                                        }
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                                        echo "<td  style='text-align:right;' title='GC Flag'>" . str_replace('.00', '', number_format_ind($sales_details[41])) . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                                        echo "<td  style='text-align:left;' title='Region Name'>" . $rname . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                                        echo "<td  style='text-align:left;' title='Branch Name'>" . $bname . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                                        echo "<td  style='text-align:left;' title='Line Name'>" . $lname . "</td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                                        echo "<td></td>";
                                    } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                                        echo "<td>".$vendor_ccode[$sales_details[3]]."</td>";
                                    }else {
                                    }
                                }
                                echo "</tr>";
                            }
                        }
                    }

                    // Receipt Entries
                    for ($a = 0; $a <= $receipt_ccount; $a++) {
                        if (!empty($receipt_info[$adate . "@" . $a])) {
                            $receipt_details = explode("@", $receipt_info[$adate . "@" . $a]);

                            $rcode = $rname = $bcode = $bname = $lcode = $lname = $fbname = $fcode = $mname = $mmname = "";
                            $fcode = $receipt_details[8];
                            if (!empty($farm_region[$fcode])) {
                                $rcode = $farm_region[$fcode];
                                if (!empty($region_name[$rcode])) {
                                    $rname = $region_name[$rcode];
                                }
                            }
                            if (!empty($farm_branch[$fcode])) {
                                $bcode = $farm_branch[$fcode];
                                if (!empty($branch_name[$bcode])) {
                                    $bname = $branch_name[$bcode];
                                }
                            }
                            if (!empty($farm_line[$fcode])) {
                                $lcode = $farm_line[$fcode];
                                if (!empty($line_name[$lcode])) {
                                    $lname = $line_name[$lcode];
                                }
                            }

                            if (!empty($mode_code[$receipt_details[4]])) {
                                $mname = $mode_name[$receipt_details[4]];
                            }
                            if (!empty($coa_name[$receipt_details[5]])) {
                                $mmname = $coa_name[$receipt_details[5]];
                            }

                            $bt_rct_amt += (float)$receipt_details[6];
                            $cr_amt += (float)$receipt_details[6];
                            $rb_amt = (float)round($rb_amt,5) - (float)round($receipt_details[6],5);

                            echo "<tr>";
                            for ($i = 1; $i <= $col_count; $i++) {
                                $key_id = "A:1:" . $i;

                                if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") {
                                    echo "<td  style='text-align:left;' title='Transaction No.'>" . $receipt_details[0] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                                    echo "<td  style='text-align:left;' title='Linked Transaction'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                                    echo "<td  style='text-align:left;' title='Date'>" . date('d.m.Y', strtotime($receipt_details[1])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                                    echo "<td  style='text-align:left;' title='Customer Name'>" . $vendor_name[$receipt_details[2]] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                                    echo "<td  style='text-align:left;' title='Doc No.'>" . $receipt_details[3] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                                    echo "<td  style='text-align:left;' title='Item Name'>" . $mname . " - " . $mmname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                                    echo "<td  style='text-align:left;' title='Birds'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                                    echo "<td  style='text-align:left;' title='Sent Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                                    echo "<td  style='text-align:left;' title='Mort Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                                    echo "<td  style='text-align:left;' title='Cull Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                                    echo "<td  style='text-align:left;' title='Quantity'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                                    echo "<td  style='text-align:left;' title='Free Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                                    echo "<td  style='text-align:left;' title='Item Price'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                                    echo "<td  style='text-align:left;' title='Discount %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                                    echo "<td  style='text-align:left;' title='Discount Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                                    echo "<td  style='text-align:left;' title='GST %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                                    echo "<td  style='text-align:left;' title='GST Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                                    echo "<td  style='text-align:left;' title='TCS %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                                    echo "<td  style='text-align:left;' title='TCS Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                                    echo "<td  style='text-align:left;' title='Item Amount'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                                    echo "<td  style='text-align:left;' title='Freight Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                                    echo "<td  style='text-align:left;' title='Freight Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                                    echo "<td  style='text-align:left;' title='Freight Pay Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                                    echo "<td  style='text-align:left;' title='Freight Pay Account'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                                    echo "<td  style='text-align:left;' title='Freight Account'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                                    echo "<td  style='text-align:left;' title='Round Off'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                                    echo "<td  style='text-align:right;' title='Invoice Amount'>" . number_format_ind($receipt_details[6]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                                    echo "<td  style='text-align:left;' title='Avg Price'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                                    echo "<td  style='text-align:left;' title='Avg Weight'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                                    echo "<td  style='text-align:left;' title='Profit'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                                    echo "<td  style='text-align:left;' title='Remarks'>" . $receipt_details[7] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                                    if (!empty($sector_name[$fcode])) {
                                        echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $sector_name[$fcode] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Sector/Warehouse'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                                    echo "<td  style='text-align:left;' title='Batch Name'>" . $fbname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                                    echo "<td  style='text-align:left;' title='Supervisor Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                                    echo "<td  style='text-align:left;' title='Bag Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                                    echo "<td  style='text-align:left;' title='Bag Count'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                                    echo "<td  style='text-align:left;' title='Batch No'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                                    echo "<td  style='text-align:left;' title='Expiry Date'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                                    echo "<td  style='text-align:left;' title='Vehicle No.'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                                    echo "<td  style='text-align:left;' title='Driver Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                                    echo "<td  style='text-align:left;' title='Sale Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                                    echo "<td  style='text-align:left;' title='GC Flag'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                                    if (!empty($emp_name[$receipt_details[9]])) {
                                        echo "<td  style='text-align:left;' title='Added By'>" . $emp_name[$receipt_details[9]] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Added By'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                                    echo "<td  style='text-align:left;' title='Added Time'>" . date('d.m.Y h:i:sA', strtotime($receipt_details[10])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                                    if (!empty($emp_name[$receipt_details[11]])) {
                                        echo "<td  style='text-align:left;' title='Edited By'>" . $emp_name[$receipt_details[11]] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Edited By'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                                    echo "<td  style='text-align:left;' title='Edited Time'>" . date('d.m.Y h:i:sA', strtotime($receipt_details[12])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                                    echo "<td  style='text-align:left;' title='Sale Latitude'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                                    echo "<td  style='text-align:left;' title='Sale Longitude'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                                    echo "<td  style='text-align:left;' title='Sale Location'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                                    echo "<td  style='text-align:left;' title='IMEI'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                                    echo "<td  style='text-align:left;' title='Mobile Flag'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                                    echo "<td  style='text-align:left;' title='Sale Image'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                                    echo "<td  style='text-align:left;' title='Region Name'>" . $rname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                                    echo "<td  style='text-align:left;' title='Branch Name'>" . $bname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                                    echo "<td  style='text-align:left;' title='Line Name'>" . $lname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                                    echo "<td  style='text-align:right;' title='Cr'>" . number_format_ind($receipt_details[6]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                                    echo "<td  style='text-align:left;' title='Dr'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                                    echo "<td  style='text-align:right;' title='Balance'>" . number_format_ind($rb_amt) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                                    echo "<td  style='text-align:left;' title='Type'>Receipts</td>";
                                }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                                    echo "<td></td>";
                                }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                                    echo "<td>".$vendor_ccode[$receipt_details[2]]."</td>";
                                } else {
                                }
                            }
                        }
                    }

                    // Return Entries
                    for ($a = 0; $a <= $return_ccount; $a++) {
                        if (!empty($return_info[$adate . "@" . $a])) {
                            $return_details = explode("@", $return_info[$adate . "@" . $a]);

                            $rcode = $rname = $bcode = $bname = $lcode = $lname = $fbname = $fcode = $mname = $mmname = "";
                            $fcode = $return_details[10];
                            if (!empty($farm_region[$fcode])) {
                                $rcode = $farm_region[$fcode];
                                if (!empty($region_name[$rcode])) {
                                    $rname = $region_name[$rcode];
                                }
                            }
                            if (!empty($farm_branch[$fcode])) {
                                $bcode = $farm_branch[$fcode];
                                if (!empty($branch_name[$bcode])) {
                                    $bname = $branch_name[$bcode];
                                }
                            }
                            if (!empty($farm_line[$fcode])) {
                                $lcode = $farm_line[$fcode];
                                if (!empty($line_name[$lcode])) {
                                    $lname = $line_name[$lcode];
                                }
                            }

                            $bt_rct_amt += (float)$return_details[9];
                            $cr_amt += (float)$return_details[9];
                            $rb_amt = (float)round($rb_amt,5) - (float)round($return_details[9],5);

                            echo "<tr>";
                            for ($i = 1; $i <= $col_count; $i++) {
                                $key_id = "A:1:" . $i;

                                if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") {
                                    echo "<td  style='text-align:left;' title='Transaction No.'>" . $return_details[0] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                                    echo "<td  style='text-align:left;' title='Linked Transaction'>" . $return_details[1] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                                    echo "<td  style='text-align:left;' title='Date'>" . date('d.m.Y', strtotime($return_details[2])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                                    echo "<td  style='text-align:left;' title='Customer Name'>" . $vendor_name[$return_details[3]] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                                    echo "<td  style='text-align:left;' title='Doc No.'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                                    echo "<td  style='text-align:left;' title='Item Name'>" . $item_name[$return_details[4]] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                                    echo "<td  style='text-align:right;' title='Birds'>" . str_replace('.00', '', number_format_ind($return_details[5])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                                    echo "<td  style='text-align:left;' title='Sent Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                                    echo "<td  style='text-align:left;' title='Mort Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                                    echo "<td  style='text-align:left;' title='Cull Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                                    echo "<td  style='text-align:right;' title='Quantity'>" . number_format_ind($return_details[6]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                                    echo "<td  style='text-align:left;' title='Free Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                                    echo "<td  style='text-align:right;' title='Item Price'>" . number_format_ind($return_details[7]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                                    echo "<td  style='text-align:left;' title='Discount %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                                    echo "<td  style='text-align:left;' title='Discount Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                                    echo "<td  style='text-align:right;' title='GST %'>" . number_format_ind($return_details[8]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                                    echo "<td  style='text-align:left;' title='GST Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                                    echo "<td  style='text-align:left;' title='TCS %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                                    echo "<td  style='text-align:left;' title='TCS Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                                    echo "<td  style='text-align:left;' title='Item Amount'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                                    echo "<td  style='text-align:left;' title='Freight Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                                    echo "<td  style='text-align:left;' title='Freight Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                                    echo "<td  style='text-align:left;' title='Freight Pay Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                                    echo "<td  style='text-align:left;' title='Freight Pay Account'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                                    echo "<td  style='text-align:left;' title='Freight Account'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                                    echo "<td  style='text-align:left;' title='Round Off'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                                    echo "<td  style='text-align:right;' title='Invoice Amount'>" . number_format_ind($return_details[9]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                                    echo "<td  style='text-align:left;' title='Avg Price'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                                    echo "<td  style='text-align:left;' title='Avg Weight'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                                    echo "<td  style='text-align:left;' title='Profit'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                                    echo "<td  style='text-align:left;' title='Remarks'>" . $return_details[11] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                                    if (!empty($sector_name[$fcode])) {
                                        echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $sector_name[$fcode] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Sector/Warehouse'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                                    echo "<td  style='text-align:left;' title='Batch Name'>" . $fbname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                                    echo "<td  style='text-align:left;' title='Supervisor Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                                    echo "<td  style='text-align:left;' title='Bag Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                                    echo "<td  style='text-align:left;' title='Bag Count'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                                    echo "<td  style='text-align:left;' title='Batch No'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                                    echo "<td  style='text-align:left;' title='Expiry Date'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                                    echo "<td  style='text-align:left;' title='Vehicle No.'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                                    echo "<td  style='text-align:left;' title='Driver Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                                    echo "<td  style='text-align:left;' title='Sale Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                                    echo "<td  style='text-align:left;' title='GC Flag'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                                    if (!empty($emp_name[$return_details[12]])) {
                                        echo "<td  style='text-align:left;' title='Added By'>" . $emp_name[$return_details[12]] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Added By'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                                    echo "<td  style='text-align:left;' title='Added Time'>" . date('d.m.Y h:i:sA', strtotime($return_details[13])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                                    if (!empty($emp_name[$return_details[14]])) {
                                        echo "<td  style='text-align:left;' title='Edited By'>" . $emp_name[$return_details[14]] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Edited By'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                                    echo "<td  style='text-align:left;' title='Edited Time'>" . date('d.m.Y h:i:sA', strtotime($return_details[15])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                                    echo "<td  style='text-align:left;' title='Sale Latitude'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                                    echo "<td  style='text-align:left;' title='Sale Longitude'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                                    echo "<td  style='text-align:left;' title='Sale Location'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                                    echo "<td  style='text-align:left;' title='IMEI'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                                    echo "<td  style='text-align:left;' title='Mobile Flag'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                                    echo "<td  style='text-align:left;' title='Sale Image'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                                    echo "<td  style='text-align:left;' title='Region Name'>" . $rname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                                    echo "<td  style='text-align:left;' title='Branch Name'>" . $bname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                                    echo "<td  style='text-align:left;' title='Line Name'>" . $lname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                                    echo "<td  style='text-align:right;' title='Cr'>" . number_format_ind($return_details[9]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                                    echo "<td  style='text-align:left;' title='Dr'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                                    echo "<td  style='text-align:right;' title='Balance'>" . number_format_ind($rb_amt) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                                    echo "<td  style='text-align:left;' title='Type'>Sales Return</td>";
                                }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                                    echo "<td></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                                    echo "<td>". $vendor_ccode[$return_details[3]]."</td>";
                                } else {
                                }
                            }
                        }
                    }

                    // CCN Entries
                    for ($a = 0; $a <= $ccn_ccount; $a++) {
                        if (!empty($ccn_info[$adate . "@" . $a])) {
                            $ccn_details = explode("@", $ccn_info[$adate . "@" . $a]);

                            $rcode = $rname = $bcode = $bname = $lcode = $lname = $fbname = $fcode = $mname = $mmname = "";
                            $fcode = $ccn_details[7];
                            if (!empty($farm_region[$fcode])) {
                                $rcode = $farm_region[$fcode];
                                if (!empty($region_name[$rcode])) {
                                    $rname = $region_name[$rcode];
                                }
                            }
                            if (!empty($farm_branch[$fcode])) {
                                $bcode = $farm_branch[$fcode];
                                if (!empty($branch_name[$bcode])) {
                                    $bname = $branch_name[$bcode];
                                }
                            }
                            if (!empty($farm_line[$fcode])) {
                                $lcode = $farm_line[$fcode];
                                if (!empty($line_name[$lcode])) {
                                    $lname = $line_name[$lcode];
                                }
                            }

                            $bt_rct_amt += (float)$ccn_details[6];
                            $cr_amt += (float)$ccn_details[6];
                            $rb_amt = (float)round($rb_amt,5) - (float)round($ccn_details[6],5);

                            echo "<tr>";
                            for ($i = 1; $i <= $col_count; $i++) {
                                $key_id = "A:1:" . $i;

                                if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") {
                                    echo "<td  style='text-align:left;' title='Transaction No.'>" . $ccn_details[0] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                                    echo "<td  style='text-align:left;' title='Linked Transaction'>" . $ccn_details[1] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                                    echo "<td  style='text-align:left;' title='Date'>" . date('d.m.Y', strtotime($ccn_details[2])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                                    echo "<td  style='text-align:left;' title='Customer Name'>" . $vendor_name[$ccn_details[3]] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                                    echo "<td  style='text-align:left;' title='Doc No.'>" . $ccn_details[4] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                                    echo "<td  style='text-align:left;' title='Item Name'>" . $coa_name[$ccn_details[5]] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                                    echo "<td  style='text-align:left;' title='Birds'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                                    echo "<td  style='text-align:left;' title='Sent Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                                    echo "<td  style='text-align:left;' title='Mort Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                                    echo "<td  style='text-align:left;' title='Cull Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                                    echo "<td  style='text-align:left;' title='Quantity'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                                    echo "<td  style='text-align:left;' title='Free Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                                    echo "<td  style='text-align:left;' title='Item Price'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                                    echo "<td  style='text-align:left;' title='Discount %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                                    echo "<td  style='text-align:left;' title='Discount Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                                    echo "<td  style='text-align:left;' title='GST %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                                    echo "<td  style='text-align:left;' title='GST Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                                    echo "<td  style='text-align:left;' title='TCS %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                                    echo "<td  style='text-align:left;' title='TCS Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                                    echo "<td  style='text-align:left;' title='Item Amount'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                                    echo "<td  style='text-align:left;' title='Freight Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                                    echo "<td  style='text-align:left;' title='Freight Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                                    echo "<td  style='text-align:left;' title='Freight Pay Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                                    echo "<td  style='text-align:left;' title='Freight Pay Account'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                                    echo "<td  style='text-align:left;' title='Freight Account'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                                    echo "<td  style='text-align:left;' title='Round Off'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                                    echo "<td  style='text-align:right;' title='Invoice Amount'>" . number_format_ind($ccn_details[6]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                                    echo "<td  style='text-align:left;' title='Avg Price'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                                    echo "<td  style='text-align:left;' title='Avg Weight'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                                    echo "<td  style='text-align:left;' title='Profit'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                                    echo "<td  style='text-align:left;' title='Remarks'>" . $ccn_details[8] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                                    if (!empty($sector_name[$fcode])) {
                                        echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $sector_name[$fcode] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Sector/Warehouse'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                                    echo "<td  style='text-align:left;' title='Batch Name'>" . $fbname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                                    echo "<td  style='text-align:left;' title='Supervisor Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                                    echo "<td  style='text-align:left;' title='Bag Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                                    echo "<td  style='text-align:left;' title='Bag Count'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                                    echo "<td  style='text-align:left;' title='Batch No'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                                    echo "<td  style='text-align:left;' title='Expiry Date'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                                    echo "<td  style='text-align:left;' title='Vehicle No.'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                                    echo "<td  style='text-align:left;' title='Driver Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                                    echo "<td  style='text-align:left;' title='Sale Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                                    echo "<td  style='text-align:left;' title='GC Flag'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                                    if (!empty($emp_name[$ccn_details[10]])) {
                                        echo "<td  style='text-align:left;' title='Added By'>" . $emp_name[$ccn_details[10]] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Added By'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                                    echo "<td  style='text-align:left;' title='Added Time'>" . date('d.m.Y h:i:sA', strtotime($ccn_details[10])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                                    if (!empty($emp_name[$ccn_details[12]])) {
                                        echo "<td  style='text-align:left;' title='Edited By'>" . $emp_name[$ccn_details[12]] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Edited By'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                                    echo "<td  style='text-align:left;' title='Edited Time'>" . date('d.m.Y h:i:sA', strtotime($ccn_details[13])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                                    echo "<td  style='text-align:left;' title='Sale Latitude'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                                    echo "<td  style='text-align:left;' title='Sale Longitude'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                                    echo "<td  style='text-align:left;' title='Sale Location'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                                    echo "<td  style='text-align:left;' title='IMEI'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                                    echo "<td  style='text-align:left;' title='Mobile Flag'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                                    echo "<td  style='text-align:left;' title='Sale Image'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                                    echo "<td  style='text-align:left;' title='Region Name'>" . $rname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                                    echo "<td  style='text-align:left;' title='Branch Name'>" . $bname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                                    echo "<td  style='text-align:left;' title='Line Name'>" . $lname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                                    echo "<td  style='text-align:right;' title='Cr'>" . number_format_ind($ccn_details[6]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                                    echo "<td  style='text-align:left;' title='Dr'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                                    echo "<td  style='text-align:right;' title='Balance'>" . number_format_ind($rb_amt) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                                    echo "<td  style='text-align:left;' title='Type'>Customer Credit Note</td>";
                                }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                                    echo "<td></td>";
                                }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                                    echo "<td>".$vendor_ccode[$ccn_details[3]]."</td>";
                                } else {
                                }
                            }
                        }
                    }

                    // CDN Entries
                    for ($a = 0; $a <= $cdn_ccount; $a++) {
                        if (!empty($cdn_info[$adate . "@" . $a])) {
                            $cdn_details = explode("@", $cdn_info[$adate . "@" . $a]);

                            $rcode = $rname = $bcode = $bname = $lcode = $lname = $fbname = $fcode = $mname = $mmname = "";
                            $fcode = $cdn_details[7];
                            if (!empty($farm_region[$fcode])) {
                                $rcode = $farm_region[$fcode];
                                if (!empty($region_name[$rcode])) {
                                    $rname = $region_name[$rcode];
                                }
                            }
                            if (!empty($farm_branch[$fcode])) {
                                $bcode = $farm_branch[$fcode];
                                if (!empty($branch_name[$bcode])) {
                                    $bname = $branch_name[$bcode];
                                }
                            }
                            if (!empty($farm_line[$fcode])) {
                                $lcode = $farm_line[$fcode];
                                if (!empty($line_name[$lcode])) {
                                    $lname = $line_name[$lcode];
                                }
                            }

                            $bt_sale_amt += (float)$cdn_details[6];
                            $dr_amt += (float)$cdn_details[6];
                            $rb_amt = (float)round($rb_amt,5) + (float)round($cdn_details[6],5);

                            echo "<tr>";
                            for ($i = 1; $i <= $col_count; $i++) {
                                $key_id = "A:1:" . $i;

                                if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") {
                                    echo "<td  style='text-align:left;' title='Transaction No.'>" . $cdn_details[0] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                                    echo "<td  style='text-align:left;' title='Linked Transaction'>" . $cdn_details[1] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                                    echo "<td  style='text-align:left;' title='Date'>" . date('d.m.Y', strtotime($cdn_details[2])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                                    echo "<td  style='text-align:left;' title='Customer Name'>" . $vendor_name[$cdn_details[3]] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                                    echo "<td  style='text-align:left;' title='Doc No.'>" . $cdn_details[4] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                                    echo "<td  style='text-align:left;' title='Item Name'>" . $coa_name[$cdn_details[5]] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                                    echo "<td  style='text-align:left;' title='Birds'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                                    echo "<td  style='text-align:left;' title='Sent Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                                    echo "<td  style='text-align:left;' title='Mort Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                                    echo "<td  style='text-align:left;' title='Cull Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                                    echo "<td  style='text-align:left;' title='Quantity'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                                    echo "<td  style='text-align:left;' title='Free Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                                    echo "<td  style='text-align:left;' title='Item Price'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                                    echo "<td  style='text-align:left;' title='Discount %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                                    echo "<td  style='text-align:left;' title='Discount Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                                    echo "<td  style='text-align:left;' title='GST %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                                    echo "<td  style='text-align:left;' title='GST Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                                    echo "<td  style='text-align:left;' title='TCS %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                                    echo "<td  style='text-align:left;' title='TCS Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                                    echo "<td  style='text-align:left;' title='Item Amount'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                                    echo "<td  style='text-align:left;' title='Freight Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                                    echo "<td  style='text-align:left;' title='Freight Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                                    echo "<td  style='text-align:left;' title='Freight Pay Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                                    echo "<td  style='text-align:left;' title='Freight Pay Account'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                                    echo "<td  style='text-align:left;' title='Freight Account'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                                    echo "<td  style='text-align:left;' title='Round Off'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                                    echo "<td  style='text-align:right;' title='Invoice Amount'>" . number_format_ind($cdn_details[6]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                                    echo "<td  style='text-align:left;' title='Avg Price'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                                    echo "<td  style='text-align:left;' title='Avg Weight'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                                    echo "<td  style='text-align:left;' title='Profit'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                                    echo "<td  style='text-align:left;' title='Remarks'>" . $cdn_details[8] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                                    if (!empty($sector_name[$fcode])) {
                                        echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $sector_name[$fcode] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Sector/Warehouse'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                                    echo "<td  style='text-align:left;' title='Batch Name'>" . $fbname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                                    echo "<td  style='text-align:left;' title='Supervisor Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                                    echo "<td  style='text-align:left;' title='Bag Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                                    echo "<td  style='text-align:left;' title='Bag Count'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                                    echo "<td  style='text-align:left;' title='Batch No'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                                    echo "<td  style='text-align:left;' title='Expiry Date'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                                    echo "<td  style='text-align:left;' title='Vehicle No.'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                                    echo "<td  style='text-align:left;' title='Driver Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                                    echo "<td  style='text-align:left;' title='Sale Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                                    echo "<td  style='text-align:left;' title='GC Flag'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                                    if (!empty($emp_name[$cdn_details[10]])) {
                                        echo "<td  style='text-align:left;' title='Added By'>" . $emp_name[$cdn_details[10]] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Added By'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                                    echo "<td  style='text-align:left;' title='Added Time'>" . date('d.m.Y h:i:sA', strtotime($cdn_details[10])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                                    if (!empty($emp_name[$cdn_details[12]])) {
                                        echo "<td  style='text-align:left;' title='Edited By'>" . $emp_name[$cdn_details[12]] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Edited By'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                                    echo "<td  style='text-align:left;' title='Edited Time'>" . date('d.m.Y h:i:sA', strtotime($cdn_details[13])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                                    echo "<td  style='text-align:left;' title='Sale Latitude'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                                    echo "<td  style='text-align:left;' title='Sale Longitude'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                                    echo "<td  style='text-align:left;' title='Sale Location'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                                    echo "<td  style='text-align:left;' title='IMEI'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                                    echo "<td  style='text-align:left;' title='Mobile Flag'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                                    echo "<td  style='text-align:left;' title='Sale Image'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                                    echo "<td  style='text-align:left;' title='Region Name'>" . $rname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                                    echo "<td  style='text-align:left;' title='Branch Name'>" . $bname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                                    echo "<td  style='text-align:left;' title='Line Name'>" . $lname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                                    echo "<td  style='text-align:right;' title='Cr'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                                    echo "<td  style='text-align:right;' title='Dr'>" . number_format_ind($cdn_details[6]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                                    echo "<td  style='text-align:right;' title='Balance'>" . number_format_ind($rb_amt) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                                    echo "<td  style='text-align:left;' title='Type'>Customer Debit Note</td>";
                                }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                                    echo "<td></td>";
                                }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                                    echo "<td>".$vendor_ccode[$cdn_details[3]]."</td>";
                                } else {
                                }
                            }
                        }
                    }

                    // Contra CR Note Entries
                    for ($a = 0; $a <= $ccr_ccount; $a++) {
                        if (!empty($contra_cr[$adate . "@" . $a])) {
                            $ccr_details = explode("@", $contra_cr[$adate . "@" . $a]);

                            $rcode = $rname = $bcode = $bname = $lcode = $lname = $fbname = $fcode = $mname = $mmname = "";
                            $fcode = $ccr_details[6];
                            if (!empty($farm_region[$fcode])) {
                                $rcode = $farm_region[$fcode];
                                if (!empty($region_name[$rcode])) {
                                    $rname = $region_name[$rcode];
                                }
                            }
                            if (!empty($farm_branch[$fcode])) {
                                $bcode = $farm_branch[$fcode];
                                if (!empty($branch_name[$bcode])) {
                                    $bname = $branch_name[$bcode];
                                }
                            }
                            if (!empty($farm_line[$fcode])) {
                                $lcode = $farm_line[$fcode];
                                if (!empty($line_name[$lcode])) {
                                    $lname = $line_name[$lcode];
                                }
                            }

                            $bt_rct_amt += (float)$ccr_details[5];
                            $cr_amt += (float)$ccr_details[5];
                            $rb_amt = (float)round($rb_amt,5) - (float)round($ccr_details[5],5);

                            echo "<tr>";
                            for ($i = 1; $i <= $col_count; $i++) {
                                $key_id = "A:1:" . $i;

                                if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") {
                                    echo "<td  style='text-align:left;' title='Transaction No.'>" . $ccr_details[0] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                                    echo "<td  style='text-align:left;' title='Linked Transaction'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                                    echo "<td  style='text-align:left;' title='Date'>" . date('d.m.Y', strtotime($ccr_details[1])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                                    echo "<td  style='text-align:left;' title='Customer Name'>" . $coa_name[$ccr_details[3]] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                                    echo "<td  style='text-align:left;' title='Doc No.'>" . $ccr_details[2] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                                    echo "<td  style='text-align:left;' title='Item Name'>" . $coa_name[$ccr_details[4]] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                                    echo "<td  style='text-align:left;' title='Birds'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                                    echo "<td  style='text-align:left;' title='Sent Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                                    echo "<td  style='text-align:left;' title='Mort Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                                    echo "<td  style='text-align:left;' title='Cull Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                                    echo "<td  style='text-align:left;' title='Quantity'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                                    echo "<td  style='text-align:left;' title='Free Qty'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                                    echo "<td  style='text-align:left;' title='Item Price'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                                    echo "<td  style='text-align:left;' title='Discount %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                                    echo "<td  style='text-align:left;' title='Discount Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                                    echo "<td  style='text-align:left;' title='GST %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                                    echo "<td  style='text-align:left;' title='GST Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                                    echo "<td  style='text-align:left;' title='TCS %'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                                    echo "<td  style='text-align:left;' title='TCS Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                                    echo "<td  style='text-align:left;' title='Item Amount'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                                    echo "<td  style='text-align:left;' title='Freight Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                                    echo "<td  style='text-align:left;' title='Freight Amt'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                                    echo "<td  style='text-align:left;' title='Freight Pay Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                                    echo "<td  style='text-align:left;' title='Freight Pay Account'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                                    echo "<td  style='text-align:left;' title='Freight Account'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                                    echo "<td  style='text-align:left;' title='Round Off'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                                    echo "<td  style='text-align:right;' title='Invoice Amount'>" . number_format_ind($ccr_details[5]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                                    echo "<td  style='text-align:left;' title='Avg Price'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                                    echo "<td  style='text-align:left;' title='Avg Weight'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                                    echo "<td  style='text-align:left;' title='Profit'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                                    echo "<td  style='text-align:left;' title='Remarks'>" . $ccr_details[7] . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                                    if (!empty($sector_name[$fcode])) {
                                        echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $sector_name[$fcode] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Sector/Warehouse'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                                    echo "<td  style='text-align:left;' title='Batch Name'>" . $fbname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                                    echo "<td  style='text-align:left;' title='Supervisor Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                                    echo "<td  style='text-align:left;' title='Bag Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                                    echo "<td  style='text-align:left;' title='Bag Count'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                                    echo "<td  style='text-align:left;' title='Batch No'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                                    echo "<td  style='text-align:left;' title='Expiry Date'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                                    echo "<td  style='text-align:left;' title='Vehicle No.'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                                    echo "<td  style='text-align:left;' title='Driver Name'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                                    echo "<td  style='text-align:left;' title='Sale Type'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                                    echo "<td  style='text-align:left;' title='GC Flag'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                                    if (!empty($emp_name[$ccr_details[8]])) {
                                        echo "<td  style='text-align:left;' title='Added By'>" . $emp_name[$ccr_details[8]] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Added By'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                                    echo "<td  style='text-align:left;' title='Added Time'>" . date('d.m.Y h:i:sA', strtotime($ccr_details[9])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                                    if (!empty($emp_name[$ccr_details[10]])) {
                                        echo "<td  style='text-align:left;' title='Edited By'>" . $emp_name[$ccr_details[10]] . "</td>";
                                    } else {
                                        echo "<td  style='text-align:left;' title='Edited By'></td>";
                                    }
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                                    echo "<td  style='text-align:left;' title='Edited Time'>" . date('d.m.Y h:i:sA', strtotime($ccr_details[11])) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                                    echo "<td  style='text-align:left;' title='Sale Latitude'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                                    echo "<td  style='text-align:left;' title='Sale Longitude'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                                    echo "<td  style='text-align:left;' title='Sale Location'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                                    echo "<td  style='text-align:left;' title='IMEI'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                                    echo "<td  style='text-align:left;' title='Mobile Flag'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                                    echo "<td  style='text-align:left;' title='Sale Image'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                                    echo "<td  style='text-align:left;' title='Region Name'>" . $rname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                                    echo "<td  style='text-align:left;' title='Branch Name'>" . $bname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                                    echo "<td  style='text-align:left;' title='Line Name'>" . $lname . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                                    echo "<td  style='text-align:right;' title='Cr'>" . number_format_ind($ccr_details[5]) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                                    echo "<td  style='text-align:left;' title='Dr'></td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                                    echo "<td  style='text-align:right;' title='Balance'>" . number_format_ind($rb_amt) . "</td>";
                                } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                                    echo "<td  style='text-align:left;' title='Type'>Contra Cr Note</td>";
                                }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                                    echo "<td></td>";
                                }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                                    echo "<td></td>";
                                } else {
                                }
                            }
                        }
                    }

                    // Contra DR Note Entries
                    for ($a = 0; $a <= $cdr_ccount; $a++) {
                        if (!empty($contra_dr[$adate . "@" . $a])) {
                            $cdr_details = explode("@", $contra_dr[$adate . "@" . $a]);

                            $rcode = $rname = $bcode = $bname = $lcode = $lname = $fbname = $fcode = $mname = $mmname = "";
                            $fcode = $cdr_details[6];
                            if (!empty($farm_region[$fcode])) { $rcode = $farm_region[$fcode]; if (!empty($region_name[$rcode])) { $rname = $region_name[$rcode]; } }
                            if (!empty($farm_branch[$fcode])) { $bcode = $farm_branch[$fcode]; if (!empty($branch_name[$bcode])) { $bname = $branch_name[$bcode]; } }
                            if (!empty($farm_line[$fcode])) { $lcode = $farm_line[$fcode]; if (!empty($line_name[$lcode])) { $lname = $line_name[$lcode]; } }

                            $bt_sale_amt += (float)$cdr_details[5];
                            $dr_amt += (float)$cdr_details[5];
                            $rb_amt = (float)round($rb_amt,5) + (float)round($cdr_details[5],5);

                            echo "<tr>";
                            for ($i = 1; $i <= $col_count; $i++) {
                                $key_id = "A:1:" . $i;

                                if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") { echo "<td  style='text-align:left;' title='Transaction No.'>" . $cdr_details[0] . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") { echo "<td  style='text-align:left;' title='Linked Transaction'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") { echo "<td  style='text-align:left;' title='Date'>" . date('d.m.Y', strtotime($cdr_details[1])) . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") { echo "<td  style='text-align:left;' title='Customer Name'>" . $coa_name[$cdr_details[4]] . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") { echo "<td  style='text-align:left;' title='Doc No.'>" . $cdr_details[2] . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") { echo "<td  style='text-align:left;' title='Item Name'>" . $coa_name[$cdr_details[3]] . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") { echo "<td  style='text-align:left;' title='Birds'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") { echo "<td  style='text-align:left;' title='Sent Qty'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") { echo "<td  style='text-align:left;' title='Mort Qty'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") { echo "<td  style='text-align:left;' title='Cull Qty'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") { echo "<td  style='text-align:left;' title='Quantity'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") { echo "<td  style='text-align:left;' title='Free Qty'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") { echo "<td  style='text-align:left;' title='Item Price'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") { echo "<td  style='text-align:left;' title='Discount %'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") { echo "<td  style='text-align:left;' title='Discount Amt'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") { echo "<td  style='text-align:left;' title='GST %'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") { echo "<td  style='text-align:left;' title='GST Amt'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") { echo "<td  style='text-align:left;' title='TCS %'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") { echo "<td  style='text-align:left;' title='TCS Amt'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") { echo "<td  style='text-align:left;' title='Item Amount'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") { echo "<td  style='text-align:left;' title='Freight Type'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") { echo "<td  style='text-align:left;' title='Freight Amt'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") { echo "<td  style='text-align:left;' title='Freight Pay Type'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") { echo "<td  style='text-align:left;' title='Freight Pay Account'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") { echo "<td  style='text-align:left;' title='Freight Account'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") { echo "<td  style='text-align:left;' title='Round Off'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") { echo "<td  style='text-align:right;' title='Invoice Amount'>" . number_format_ind($cdr_details[5]) . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") { echo "<td  style='text-align:left;' title='Avg Price'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") { echo "<td  style='text-align:left;' title='Avg Weight'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") { echo "<td  style='text-align:left;' title='Profit'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") { echo "<td  style='text-align:left;' title='Remarks'>" . $cdr_details[7] . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                                    if (!empty($sector_name[$fcode])) { echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $sector_name[$fcode] . "</td>"; }
                                    else { echo "<td  style='text-align:left;' title='Sector/Warehouse'></td>"; }
                                }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") { echo "<td  style='text-align:left;' title='Batch Name'>" . $fbname . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") { echo "<td  style='text-align:left;' title='Supervisor Name'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") { echo "<td  style='text-align:left;' title='Bag Name'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") { echo "<td  style='text-align:left;' title='Bag Count'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") { echo "<td  style='text-align:left;' title='Batch No'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") { echo "<td  style='text-align:left;' title='Expiry Date'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") { echo "<td  style='text-align:left;' title='Vehicle No.'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") { echo "<td  style='text-align:left;' title='Driver Name'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") { echo "<td  style='text-align:left;' title='Sale Type'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") { echo "<td  style='text-align:left;' title='GC Flag'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                                    if (!empty($emp_name[$cdr_details[8]])) { echo "<td  style='text-align:left;' title='Added By'>" . $emp_name[$cdr_details[8]] . "</td>"; }
                                    else { echo "<td  style='text-align:left;' title='Added By'></td>"; }
                                }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") { echo "<td  style='text-align:left;' title='Added Time'>" . date('d.m.Y h:i:sA', strtotime($cdr_details[9])) . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                                    if (!empty($emp_name[$cdr_details[10]])) { echo "<td  style='text-align:left;' title='Edited By'>" . $emp_name[$cdr_details[10]] . "</td>"; }
                                    else { echo "<td  style='text-align:left;' title='Edited By'></td>"; }
                                }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") { echo "<td  style='text-align:left;' title='Edited Time'>" . date('d.m.Y h:i:sA', strtotime($cdr_details[11])) . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") { echo "<td  style='text-align:left;' title='Sale Latitude'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") { echo "<td  style='text-align:left;' title='Sale Longitude'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") { echo "<td  style='text-align:left;' title='Sale Location'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") { echo "<td  style='text-align:left;' title='IMEI'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") { echo "<td  style='text-align:left;' title='Mobile Flag'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") { echo "<td  style='text-align:left;' title='Sale Image'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") { echo "<td  style='text-align:left;' title='Region Name'>" . $rname . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") { echo "<td  style='text-align:left;' title='Branch Name'>" . $bname . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") { echo "<td  style='text-align:left;' title='Line Name'>" . $lname . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") { echo "<td  style='text-align:right;' title='Cr'></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") { echo "<td  style='text-align:left;' title='Dr'>" . number_format_ind($cdr_details[5]) . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") { echo "<td  style='text-align:right;' title='Balance'>" . number_format_ind($rb_amt) . "</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") { echo "<td  style='text-align:left;' title='Type'>Contra Dr Note</td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") { echo "<td></td>"; }
                                else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") { echo "<td></td>"; }
                                else { }
                            }
                        }
                    }

                    // Transit Loss Entries
                    for ($a = 0; $a <= $tlb_ccount; $a++) {
                        if (!empty($tloss_info[$adate."@".$a])) {
                            $tls_details = explode("@", $tloss_info[$adate."@".$a]);

                            $rcode = $rname = $bcode = $bname = $lcode = $lname = $fbname = $fcode = $mname = $mmname = "";
                            $fcode = $tls_details[10];
                            if (!empty($farm_region[$fcode])) { $rcode = $farm_region[$fcode]; if (!empty($region_name[$rcode])) { $rname = $region_name[$rcode]; } }
                            if (!empty($farm_branch[$fcode])) { $bcode = $farm_branch[$fcode]; if (!empty($branch_name[$bcode])) { $bname = $branch_name[$bcode]; } }
                            if (!empty($farm_line[$fcode])) { $lcode = $farm_line[$fcode]; if (!empty($line_name[$lcode])) { $lname = $line_name[$lcode]; } }

                            echo "<tr>";
                            if($tloss_oinv != $tls_details[0]){
                                $tloss_oinv = $tls_details[0];
                                if(empty($tloss_amt[$tls_details[0]]) || $tloss_amt[$tls_details[0]] == ""){ $tloss_amt[$tls_details[0]] = 0; }
                                $bt_rct_amt += (float)$tloss_amt[$tls_details[0]];
                                $cr_amt += (float)$tloss_amt[$tls_details[0]];
                                $rb_amt = (float)round($rb_amt,5) - (float)round($tloss_amt[$tls_details[0]],5);
    
                                for ($i = 1; $i <= $col_count; $i++) {
                                    $key_id = "A:1:" . $i;
    
                                    if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") { echo "<td  style='text-align:left;' title='Transaction No.'>" . $tls_details[0] . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") { echo "<td  style='text-align:left;' title='Linked Transaction'>" . $tls_details[2] . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") { echo "<td  style='text-align:left;' title='Date'>" . date('d.m.Y', strtotime($tls_details[1])) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") { echo "<td  style='text-align:left;' title='Customer Name'>" . $vendor_name[$tls_details[3]] . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") { echo "<td  style='text-align:left;' title='Doc No.'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") { echo "<td  style='text-align:left;' title='Item Name'>" . $item_name[$tls_details[4]] . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") { echo "<td  style='text-align:right;' title='Birds'>" . number_format_ind($tls_details[5]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") { echo "<td  style='text-align:right;' title='Sent Qty'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") { echo "<td  style='text-align:right;' title='Mort Qty'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") { echo "<td  style='text-align:right;' title='Cull Qty'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") { echo "<td  style='text-align:right;' title='Quantity'>" . number_format_ind($tls_details[6]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") { echo "<td  style='text-align:right;' title='Free Qty'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") { echo "<td  style='text-align:right;' title='Item Price'>" . number_format_ind($tls_details[7]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") { echo "<td  style='text-align:right;' title='Discount %'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") { echo "<td  style='text-align:right;' title='Discount Amt'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") { echo "<td  style='text-align:right;' title='GST %'>" . number_format_ind($tls_details[8]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") { echo "<td  style='text-align:right;' title='GST Amt'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") { echo "<td  style='text-align:right;' title='TCS %'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") { echo "<td  style='text-align:right;' title='TCS Amt'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") { echo "<td  style='text-align:right;' title='Item Amount'>" . number_format_ind($tls_details[9]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") { echo "<td  style='text-align:right;' title='Freight Type'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") { echo "<td  style='text-align:right;' title='Freight Amt'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") { echo "<td  style='text-align:right;' title='Freight Pay Type'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") { echo "<td  style='text-align:right;' title='Freight Pay Account'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") { echo "<td  style='text-align:right;' title='Freight Account'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") { echo "<td  style='text-align:right;' title='Round Off'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") { echo "<td  style='text-align:right;' title='Invoice Amount'>" . number_format_ind($tls_details[9]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") { echo "<td  style='text-align:right;' title='Avg Price'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") { echo "<td  style='text-align:right;' title='Avg Weight'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") { echo "<td  style='text-align:right;' title='Profit'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") { echo "<td  style='text-align:right;' title='Remarks'>" . $tls_details[12] . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                                        if (!empty($sector_name[$fcode])) { echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $sector_name[$fcode] . "</td>"; }
                                        else { echo "<td  style='text-align:left;' title='Sector/Warehouse'></td>"; }
                                    }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") { echo "<td  style='text-align:left;' title='Batch Name'>" . $fbname . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") { echo "<td  style='text-align:left;' title='Supervisor Name'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") { echo "<td  style='text-align:left;' title='Bag Name'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") { echo "<td  style='text-align:left;' title='Bag Count'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") { echo "<td  style='text-align:left;' title='Batch No'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") { echo "<td  style='text-align:left;' title='Expiry Date'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") { echo "<td  style='text-align:left;' title='Vehicle No.'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") { echo "<td  style='text-align:left;' title='Driver Name'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") { echo "<td  style='text-align:left;' title='Sale Type'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") { echo "<td  style='text-align:left;' title='GC Flag'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") { echo "<td  style='text-align:left;' title='Added By'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") { echo "<td  style='text-align:left;' title='Added Time'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") { echo "<td  style='text-align:left;' title='Edited By'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") { echo "<td  style='text-align:left;' title='Edited Time'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") { echo "<td  style='text-align:left;' title='Sale Latitude'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") { echo "<td  style='text-align:left;' title='Sale Longitude'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") { echo "<td  style='text-align:left;' title='Sale Location'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") { echo "<td  style='text-align:left;' title='IMEI'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") { echo "<td  style='text-align:left;' title='Mobile Flag'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") { echo "<td  style='text-align:left;' title='Sale Image'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") { echo "<td  style='text-align:left;' title='Region Name'>" . $rname . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") { echo "<td  style='text-align:left;' title='Branch Name'>" . $bname . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") { echo "<td  style='text-align:left;' title='Line Name'>" . $lname . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") { echo "<td  style='text-align:right;' title='Cr' rowspan='".$tloss_count[$tls_details[0]]."'>" . number_format_ind($tloss_amt[$tls_details[0]]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") { echo "<td  style='text-align:left;' title='Dr' rowspan='".$tloss_count[$tls_details[0]]."'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") { echo "<td  style='text-align:right;' title='Balance' rowspan='".$tloss_count[$tls_details[0]]."'>" . number_format_ind($rb_amt) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") { echo "<td  style='text-align:left;' title='Type'>Transit Loss</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") { echo "<td></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") { echo "<td>".$vendor_ccode[$tls_details[3]]."</td>"; }
                                    else { }
                                }
                            }
                            else{
                                for ($i = 1; $i <= $col_count; $i++) {
                                    $key_id = "A:1:" . $i;
    
                                    if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") { echo "<td  style='text-align:left;' title='Transaction No.'>" . $tls_details[0] . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") { echo "<td  style='text-align:left;' title='Linked Transaction'>" . $tls_details[2] . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") { echo "<td  style='text-align:left;' title='Date'>" . date('d.m.Y', strtotime($tls_details[1])) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") { echo "<td  style='text-align:left;' title='Customer Name'>" . $vendor_name[$tls_details[3]] . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") { echo "<td  style='text-align:left;' title='Doc No.'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") { echo "<td  style='text-align:left;' title='Item Name'>" . $item_name[$tls_details[4]] . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") { echo "<td  style='text-align:right;' title='Birds'>" . number_format_ind($tls_details[5]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") { echo "<td  style='text-align:right;' title='Sent Qty'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") { echo "<td  style='text-align:right;' title='Mort Qty'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") { echo "<td  style='text-align:right;' title='Cull Qty'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") { echo "<td  style='text-align:right;' title='Quantity'>" . number_format_ind($tls_details[6]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") { echo "<td  style='text-align:right;' title='Free Qty'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") { echo "<td  style='text-align:right;' title='Item Price'>" . number_format_ind($tls_details[7]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") { echo "<td  style='text-align:right;' title='Discount %'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") { echo "<td  style='text-align:right;' title='Discount Amt'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") { echo "<td  style='text-align:right;' title='GST %'>" . number_format_ind($tls_details[8]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") { echo "<td  style='text-align:right;' title='GST Amt'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") { echo "<td  style='text-align:right;' title='TCS %'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") { echo "<td  style='text-align:right;' title='TCS Amt'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") { echo "<td  style='text-align:right;' title='Item Amount'>" . number_format_ind($tls_details[9]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") { echo "<td  style='text-align:right;' title='Freight Type'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") { echo "<td  style='text-align:right;' title='Freight Amt'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") { echo "<td  style='text-align:right;' title='Freight Pay Type'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") { echo "<td  style='text-align:right;' title='Freight Pay Account'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") { echo "<td  style='text-align:right;' title='Freight Account'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") { echo "<td  style='text-align:right;' title='Round Off'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") { echo "<td  style='text-align:right;' title='Invoice Amount'>" . number_format_ind($tls_details[9]) . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") { echo "<td  style='text-align:right;' title='Avg Price'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") { echo "<td  style='text-align:right;' title='Avg Weight'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") { echo "<td  style='text-align:right;' title='Profit'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") { echo "<td  style='text-align:right;' title='Remarks'>" . $tls_details[12] . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                                        if (!empty($sector_name[$fcode])) { echo "<td  style='text-align:left;' title='Sector/Warehouse'>" . $sector_name[$fcode] . "</td>"; }
                                        else { echo "<td  style='text-align:left;' title='Sector/Warehouse'></td>"; }
                                    }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") { echo "<td  style='text-align:left;' title='Batch Name'>" . $fbname . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") { echo "<td  style='text-align:left;' title='Supervisor Name'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") { echo "<td  style='text-align:left;' title='Bag Name'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") { echo "<td  style='text-align:left;' title='Bag Count'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") { echo "<td  style='text-align:left;' title='Batch No'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") { echo "<td  style='text-align:left;' title='Expiry Date'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") { echo "<td  style='text-align:left;' title='Vehicle No.'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") { echo "<td  style='text-align:left;' title='Driver Name'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") { echo "<td  style='text-align:left;' title='Sale Type'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") { echo "<td  style='text-align:left;' title='GC Flag'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") { echo "<td  style='text-align:left;' title='Added By'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") { echo "<td  style='text-align:left;' title='Added Time'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") { echo "<td  style='text-align:left;' title='Edited By'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") { echo "<td  style='text-align:left;' title='Edited Time'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") { echo "<td  style='text-align:left;' title='Sale Latitude'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") { echo "<td  style='text-align:left;' title='Sale Longitude'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") { echo "<td  style='text-align:left;' title='Sale Location'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") { echo "<td  style='text-align:left;' title='IMEI'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") { echo "<td  style='text-align:left;' title='Mobile Flag'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") { echo "<td  style='text-align:left;' title='Sale Image'></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") { echo "<td  style='text-align:left;' title='Region Name'>" . $rname . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") { echo "<td  style='text-align:left;' title='Branch Name'>" . $bname . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") { echo "<td  style='text-align:left;' title='Line Name'>" . $lname . "</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") { echo "<td  style='text-align:left;' title='Type'>Transit Loss</td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") { echo "<td></td>"; }
                                    else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") { echo "<td>".$vendor_ccode[$tls_details[3]]."</td>"; }
                                    else { }
                                }
                            }
                            echo "</tr>";
                        }
                    }
                }

                if ($pbc > 0) {
                    $pbl = 1;
                    echo "<tfoot  class='thead4'>";
                    echo "<tr>";
                    //if ($col_count > $pbc) { echo "<td colspan='" . $fbh . "' style='font-weight:bold;text-align:center;'>Between Dates Total</td>"; $pbl = $fbc; } else { }
                    for ($i = 1; $i <= $col_count; $i++) {
                        $key_id = "A:1:" . $i;
                        if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                            echo "<td style='text-align:right;'>".str_replace(".00","",number_format_ind($tot_birds))."</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                            echo "<td style='text-align:right;'>".number_format_ind($tot_rqty)."</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                            echo "<td style='text-align:right;'>".number_format_ind($tot_tcds_amt)."</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                            echo "<td style='text-align:right;'>".number_format_ind($tot_iamt)."</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                            echo "<td style='font-weight:bold;text-align:right;'>" . number_format_ind($bt_rct_amt) . "</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                            echo "<td style='font-weight:bold;text-align:right;'>" . number_format_ind($bt_sale_amt) . "</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                            echo "<td style='font-weight:bold;text-align:right;'></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                            echo "<td></td>";
                        }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                            echo "<td></td>";
                        }else {
                        }
                    }
                    echo "</tr>";
                }

                if ($pbc > 0) {
                    $pbl = 1;
                    echo "<tr>";
                    if ($col_count > $pbc) {
                        echo "<td colspan='" . $fbh . "' style='font-weight:bold;text-align:center;'>Closing Total</td>";
                        $pbl = $fbc;
                    } else {
                    }
                    for ($i = $pbl; $i <= $col_count; $i++) {
                        $key_id = "A:1:" . $i;
                        if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                            echo "<td style='font-weight:bold;text-align:right;'>" . number_format_ind($cr_amt) . "</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                            echo "<td style='font-weight:bold;text-align:right;'>" . number_format_ind($dr_amt) . "</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                            echo "<td style='font-weight:bold;text-align:right;'></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                            echo "<td></td>";
                        }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                            echo "<td></td>";
                        }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                            echo "<td></td>";
                        } else {
                        }
                    }
                    echo "</tr>";
                }

                if ($pbc > 0) {
                    $pbl = 1;
                    $final_cr_amt = $final_dr_amt = 0;
                    if(number_format_ind($cr_amt) == number_format_ind($dr_amt)){
                        $final_cr_amt = $final_dr_amt = 0;
                    }
                    else if($cr_amt > $dr_amt){
                        $final_cr_amt = (float)$cr_amt - (float)$dr_amt;
                    }
                    else{
                        $final_dr_amt = (float)$dr_amt - (float)$cr_amt;
                    }
                    echo "<tr>";
                    if ($col_count > $pbc) {
                        echo "<td colspan='" . $fbh . "' style='font-weight:bold;text-align:center;'>Outstanding</td>";
                        $pbl = $fbc;
                    } else {
                    }
                    for ($i = $pbl; $i <= $col_count; $i++) {
                        $key_id = "A:1:" . $i;
                        if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_trnum") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_link_trnum") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_date") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_billno") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemname") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_birds") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_snt_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mort_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_cull_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_rcd_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_fre_qty") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemprice") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_dis_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gst_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_per") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_tcds_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_itemamount") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_pay_acc") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_freight_acc") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_round_off") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_finl_amt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_price") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_avg_wt") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_profit") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_remarks") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_warehouse") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_farm_batch") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_supervisor_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_bag_count") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_batch_no") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_exp_date") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_vehicle_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_driver_code") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_type") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_gc_flag") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedemp") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_addedtime") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedemp") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_updatedtime") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_latitude") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_longitude") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "Customer_sale_location") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_imei") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_mob_flag") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_sale_image") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "region_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "branch_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "line_name") {
                            echo "<td></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_debit") {
                            echo "<td style='font-weight:bold;text-align:right;'>" . number_format_ind($final_cr_amt) . "</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_credit") {
                            echo "<td style='font-weight:bold;text-align:right;'>" . number_format_ind($final_dr_amt) . "</td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_runningbalance") {
                            echo "<td style='font-weight:bold;text-align:right;'></td>";
                        } else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "transaction_type") {
                            echo "<td></td>";
                        }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_shipping_address") {
                            echo "<td></td>";
                        }else if (!empty($act_col_numbs[$key_id]) && $act_col_numbs[$key_id] == "customer_ccode") {
                            echo "<td></td>";
                        } else {
                        }
                    }
                    echo "</tr>";
                    echo "</tfoot>";
                }
                ?>
            </tbody>
        <?php
        }
        ?>
    </table><br /><br /><br />
    <script src="../datepicker/jquery/jquery.js"></script>
    <script src="../datepicker/jquery-ui.js"></script>
    <script>
        function checkval() {
            var vendors = document.getElementById("vendors").value;
            var l = true;
            if (vendors == "select") {
                alert("Kindly select customer to fetch Ledger");
                l = false;
            } else {}
            if (l == true) {
                return true;
            } else {
                return false;
            }
        }

        function update_masterreport_status(a) {
            var file_url = '<?php echo $field_href[0]; ?>';
            var user_code = '<?php echo $user_code; ?>';
            var field_name = a;
            var modify_col = new XMLHttpRequest();
            var method = "GET";
            var url = "broiler_modify_clientfieldstatus.php?file_url=" + file_url + "&user_code=" + user_code + "&field_name=" + field_name;
            //window.open(url);
            var asynchronous = true;
            modify_col.open(method, url, asynchronous);
            modify_col.send();
            modify_col.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var item_list = this.responseText;
                    if (item_list == 0) {
                        //alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.")
                    } else {
                        alert("Invalid request \n Kindly check and try again ...!");
                    }
                }
            }
        }
    </script>
           <script>
 function table_sort() {
		        console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('.tbody1 tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;
                        const b_val = b.children[index].innerText;
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector(".tbody1").appendChild(elem)
                    });
                   // slnos();
                    asc = !asc;
                    })
                });
            }
            function convertDate(d){ var p = d.split("."); return (p[2]+p[1]+p[0]); }
            function table_sort3() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_date').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_date').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('.tbody1 tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector(".tbody1").appendChild(elem)
                    });
                  //  slnos();
                    asc = !asc;
                    })
                });
            }

            function convertNumber(d) { var p = intval(d); return (p); }

            function table_sort2() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_num').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_num').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    
                    var arr = Array.from(th_elem.closest("table").querySelectorAll('.tbody1 tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;    
                        if(isNaN(a_val)){
                        a_val1 = a_val.split(',').join(''); }
                        else {
                            a_val1 = a_val; }
                        const b_val = b.children[index].innerText;
                        if(isNaN(b_val)){
                        b_val1 = b_val.split(',').join('');}
                        else {
                            b_val1 = b_val; }
                        return (asc) ? b_val1 - a_val1:  a_val1 - b_val1 
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector(".tbody1").appendChild(elem)
                    });
                   // slnos();
                    asc = !asc;
                    })
                });
                
            }
           /* function slnos(){

                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }*/
            table_sort();
            table_sort2();
            table_sort3();
        </script>
</body>

</html>
<?php
include "header_foot.php";
?>