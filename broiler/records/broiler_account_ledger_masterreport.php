<?php
//broiler_account_ledger.php
include "../newConfig.php";
$user_code = $_SESSION['userid'];

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
$href = explode("/", $_SERVER['REQUEST_URI']); $field_href = explode("?", $href[2]); 
$sql1 = "SHOW COLUMNS FROM `broiler_reportfields`"; $query1 = mysqli_query($conn,$sql1); $col_names_all = array(); $i = 0;
while($row1 = mysqli_fetch_assoc($query1)){
    if($row1['Field'] == "id" || $row1['Field'] == "field_name" || $row1['Field'] == "field_href" || $row1['Field'] == "field_pattern" || $row1['Field'] == "user_access_code" || $row1['Field'] == "column_count" || $row1['Field'] == "active" || $row1['Field'] == "dflag"){ }
    else{ $col_names_all[$row1['Field']] = $row1['Field']; $i++; }
}
$sql2 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` LIKE '%$field_href[0]%' AND `user_access_code` = '$user_code' AND `active` = '1'";
$query2 = mysqli_query($conn,$sql2); $count2 = mysqli_num_rows($query2); $act_col_numbs = array(); $key_id = "";
if($count2 > 0){
    while($row2 = mysqli_fetch_assoc($query2)){
        foreach($col_names_all as $cna){
            $fas_details = explode(":",$row2[$cna]);
            if($fas_details[0] == "A" && $fas_details[1] == "1" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $act_col_numbs[$key_id] = $cna;
            }
            else if($fas_details[0] == "A" && $fas_details[1] == "0" && $fas_details[2] > 0){
                $key_id = $row2[$cna];
                $nac_col_numbs[$key_id] = $cna;
            }
            else{ }
        }
        $col_count = $row2['column_count'];
    }
}

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
    $num_format_file = $row['num_format_file'];
}
if ($num_format_file == "") {
    $num_format_file = "number_format_ind.php";
}

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
    $branch_access_code = $row['branch_code'];
    $line_access_code = $row['line_code'];
    $farm_access_code = $row['farm_code'];
    $sector_access_code = $row['loc_access'];
}
if ($branch_access_code == "all") {
    $branch_access_filter1 = "";
} else {
    $branch_access_list = implode("','", explode(",", $branch_access_code));
    $branch_access_filter1 = " AND `code` IN ('$branch_access_list')";
    $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')";
}
if ($line_access_code == "all") {
    $line_access_filter1 = "";
} else {
    $line_access_list = implode("','", explode(",", $line_access_code));
    $line_access_filter1 = " AND `code` IN ('$line_access_list')";
    $line_access_filter2 = " AND `line_code` IN ('$line_access_list')";
}
if ($farm_access_code == "all") {
    $farm_access_filter1 = "";
} else {
    $farm_access_list = implode("','", explode(",", $farm_access_code));
    $farm_access_filter1 = " AND `code` IN ('$farm_access_list')";
}
if ($sector_access_code == "all") {
    $sector_access_filter1 = "";
} else {
    $sector_access_list = implode("','", explode(",", $sector_access_code));
    $sector_access_filter1 = " AND `code` IN ('$sector_access_list')";
}

include $num_format_file;

include "header_head.php";
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' " . $sector_access_list . " ORDER BY `description` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
    $sector_code[$row['code']] = $row['code'];
    $sector_name[$row['code']] = $row['description'];
}
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' " . $farm_access_filter1 . "" . $branch_access_filter2 . "" . $line_access_filter2 . " ORDER BY `description` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
    $sector_code[$row['code']] = $row['code'];
    $sector_name[$row['code']] = $row['description'];
}
$sql = "SELECT * FROM `acc_coa`";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
    $coa_code[$row['code']] = $row['code'];
    $coa_name[$row['code']] = $row['description'];
}
$sql = "SELECT * FROM `item_details`";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
    $item_code[$row['code']] = $row['code'];
    $item_name[$row['code']] = $row['description'];
}

$sql = "SELECT * FROM `broiler_vehicle` ORDER BY `registration_number` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
    $vehicle_code[$row['code']] = $row['code'];
    $vehicle_name[$row['code']] = $row['registration_number'];
}

$sql = "SELECT * FROM `main_contactdetails` ORDER BY `name` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
    $ven_code[$row['code']] = $row['code'];
    $ven_name[$row['code']] = $row['name'];
}

$fdate = $tdate = date("Y-m-d");
$coas = "select";
$sectors = "all";
$excel_type = "display";
$font_stype = "";
$font_size = "11px";
if (isset($_POST['submit_report']) == true) {
    $fdate = date("Y-m-d", strtotime($_POST['fdate']));
    $tdate = date("Y-m-d", strtotime($_POST['tdate']));
    $coas = $_POST['coas'];
    $sectors = $_POST['sectors'];
    $font_stype = $_POST['font_stype'];
    $font_size = $_POST['font_size'];
    $sector_filter = "";
    if ($sectors != "all") {
        $sector_filter = " AND `location` LIKE '$sectors'";
    } else {
        $sector_filter = "";
    }
    $coa_filter = "";
    $coa_filter = " AND `coa_code` LIKE '$coas'";
    $excel_type = $_POST['export'];
    $url = "../PHPExcel/Examples/broiler_account_ledger_masterreport-Excel.php?fdate=" . $fdate . "&tdate=" . $tdate . "&coas=" . $coas . "&sectors=" . $sectors."&href=".$field_href[0];
} else if ($_GET['fdate'] != "") {
    $fdate = date("Y-m-d", strtotime($_GET['fdate']));
    $tdate = date("Y-m-d", strtotime($_GET['tdate']));
    $coas = $_GET['coas'];
    $sectors = $_GET['sectors'];
    $sector_filter = "";
    if ($sectors != "all") {
        $sector_filter = " AND `location` LIKE '$sectors'";
    } else {
        $sector_filter = "";
    }
    $coa_filter = "";
    $coa_filter = " AND `coa_code` LIKE '$coas'";
}
?>
<html>

<head>
    <title>Poulsoft Solutions</title>
    <script>
        var exptype = '<?php echo $excel_type; ?>';
        var url = '<?php echo $url; ?>';
        if (exptype.match("excel")) {
            window.open(url, "_BLANK");
        }
    </script>
    <link href="../datepicker/jquery-ui.css" rel="stylesheet">


    <?php
    if ($excel_type == "print") {
        include "headerstyle_wprint_font.php";
    } else {
        include "headerstyle_woprint_font.php";
    }
    ?>
</head>

<body align="center">
    <table class="tbl" align="center" <?php if ($excel_type == "print") {
                                            echo ' id="mine"';
                                        } else {
                                            echo 'width="1300px"';
                                        } ?>>
        <?php
        $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC";
        $query = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($query)) {
        ?>
            <thead class="thead1" align="center">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px" /></td>
                    <th colspan="15" align="center"><?php echo $row['cdetails']; ?><h5>Account Ledger Report</h5>
                    </th>
                </tr>
            </thead>
        <?php } ?>
        <form action="broiler_account_ledger_masterreport.php" method="post" onSubmit="return checkval()">
            <thead class="thead2 text-primary layout-navbar-fixed">
                <tr>
                    <th colspan="17">
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
                                <label>CoA</label>
                                <select name="coas" id="coas" class="form-control select2">
                                    <option value="select" <?php if ($coas == "select") {
                                                                echo "selected";
                                                            } ?>>-select-</option>
                                    <?php foreach ($coa_code as $ccode) {
                                        if ($coa_name[$ccode] != "") { ?>
                                            <option value="<?php echo $ccode; ?>" <?php if ($coas == $ccode) {
                                                                                        echo "selected";
                                                                                    } ?>><?php echo $coa_name[$ccode]; ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                            <div class="m-2 form-group">
                                <label>Farm/Location</label>
                                <select name="sectors" id="sectors" class="form-control select2">
                                    <option value="all" <?php if ($sectors == "all") {
                                                            echo "selected";
                                                        } ?>>-All-</option>
                                    <?php foreach ($sector_code as $whcode) {
                                        if ($sector_name[$whcode] != "") { ?>
                                            <option value="<?php echo $whcode; ?>" <?php if ($sectors == $whcode) {
                                                                                        echo "selected";
                                                                                    } ?>><?php echo $sector_name[$whcode]; ?></option>
                                    <?php }
                                    } ?>
                                </select>
                            </div>
                            <div class="m-2 form-group">
                                <label>Font Style</label>
                                <select name="font_stype" id="font_stype" class="form-control select2"> <!-- onchange="update_font_family()"-->
                                    <option value="" <?php if ($font_stype == "") {
                                                            echo "selected";
                                                        } ?>>-Defalut-</option>
                                    <?php
                                    foreach ($font_family_code as $i) {
                                    ?>
                                        <option value="<?php echo $font_family_name[$i]; ?>" <?php if ($font_stype == $font_family_name[$i]) {
                                                                                                    echo "selected";
                                                                                                } ?>><?php echo $font_family_name[$i]; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="m-2 form-group">
                                <label>Font Size</label>
                                <select name="font_size" id="font_size" class="form-control select2">
                                    <?php
                                    foreach ($fsizes as $i) {
                                    ?>
                                        <option value="<?php echo $fsizes[$i]; ?>" <?php if ($font_size == $fsizes[$i]) {
                                                                                        echo "selected";
                                                                                    } ?>><?php echo $fsizes[$i]; ?></option>
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
        <?php if ($excel_type == "print") {
        } else { ?>
    </table>
    <table class="tbl_toggle" style="position: relative;  left: 35px;">
        <tr>
            <td><br></td>
        </tr>
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
                        if ($act_col_numbs[$key_id] == "sl_no" || $nac_col_numbs[$key_id1] == "sl_no") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="sl_no" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Sl.No.</span>';
                        } else if ($act_col_numbs[$key_id] == "date" || $nac_col_numbs[$key_id1] == "date") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="date" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Date</span>';
                        } else if ($act_col_numbs[$key_id] == "trnum" || $nac_col_numbs[$key_id1] == "trnum") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="trnum" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Transaction No</span>';
                        } else if ($act_col_numbs[$key_id] == "transaction_type" || $nac_col_numbs[$key_id1] == "transaction_type") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="transaction_type" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Transaction Type</span>';
                        } else if ($act_col_numbs[$key_id] == "book_no" || $nac_col_numbs[$key_id1] == "book_no") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="book_no" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Doc. No.</span>';
                        } else if ($act_col_numbs[$key_id] == "acc_fromwarehouse" || $nac_col_numbs[$key_id1] == "acc_fromwarehouse") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="acc_fromwarehouse" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>From Warehouse</span>';
                        } else if ($act_col_numbs[$key_id] == "vehicle_no" || $nac_col_numbs[$key_id1] == "vehicle_no") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="vehicle_no" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Vehicle number</span>';
                        } else if ($act_col_numbs[$key_id] == "item_name" || $nac_col_numbs[$key_id1] == "item_name") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="item_name" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Item</span>';
                        } else if ($act_col_numbs[$key_id] == "acc_quantity" || $nac_col_numbs[$key_id1] == "acc_quantity") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="acc_quantity" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Quantity</span>';
                        } else if ($act_col_numbs[$key_id] == "acc_paid_received" || $nac_col_numbs[$key_id1] == "acc_paid_received") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="acc_paid_received" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Paid/Received</span>';
                        } else if ($act_col_numbs[$key_id] == "acc_cheque_no" || $nac_col_numbs[$key_id1] == "acc_cheque_no") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="acc_cheque_no" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Cheque No</span>';
                        } else if ($act_col_numbs[$key_id] == "acc_cheque_date" || $nac_col_numbs[$key_id1] == "acc_cheque_date") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="acc_cheque_date" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Cheque Date</span>';
                        } else if ($act_col_numbs[$key_id] == "acc_remarks" || $nac_col_numbs[$key_id1] == "acc_remarks") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="acc_remarks" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Remarks</span>';
                        } else if ($act_col_numbs[$key_id] == "acc_cr" || $nac_col_numbs[$key_id1] == "acc_cr") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="acc_cr" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Credit</span>';
                        } else if ($act_col_numbs[$key_id] == "acc_dr" || $nac_col_numbs[$key_id1] == "acc_dr") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="acc_dr" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Debit</span>';
                        } else if ($act_col_numbs[$key_id] == "acc_running_balance" || $nac_col_numbs[$key_id1] == "acc_running_balance") {
                            if (!empty($act_col_numbs[$key_id])) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }
                            echo '<input type="checkbox" class="hide_show" id="acc_running_balance" onclick="update_masterreport_status(this.id);" ' . $checked . '><span>Running Balance</span>';
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
    <thead class="thead3" align="center">
        <tr align="center">
            <?php
            $last_3column_count = 0;
            $column_visible_count = 0;
            for ($i = 1; $i <= $col_count; $i++) {
                $key_id = "A:1:" . $i;
                if ($act_col_numbs[$key_id] == "sl_no") {
                    echo "<th id='order_num'>Sl.No.</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "date") {
                    echo "<th id='order_date'>Date</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "trnum") {
                    echo "<th id='order'>Transaction No</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "transaction_type") {
                    echo "<th id='order'>Transaction Type</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "book_no") {
                    echo "<th id='order'>Doc. No.</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "acc_fromwarehouse") {
                    echo "<th id='order'>From Warehouse</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "vehicle_no") {
                    echo "<th id='order'>Vehicle number</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "item_name") {
                    echo "<th id='order'>Item</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "acc_quantity") {
                    echo "<th id='order_num'>Quantity</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "acc_paid_received") {
                    echo "<th id='order'>Paid/Received</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "acc_cheque_no") {
                    echo "<th id='order'>Cheque No</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "acc_cheque_date") {
                    echo "<th id='order'>Cheque Date</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "acc_remarks") {
                    echo "<th id='order'>Remarks</th>";
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "acc_cr") {
                    echo "<th id='order_num'>Credit</th>";
                    $last_3column_count = $last_3column_count + 1;
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "acc_dr") {
                    echo "<th id='order_num'>Debit</th>";
                    $last_3column_count = $last_3column_count + 1;
                    $column_visible_count =  $column_visible_count + 1;
                } else if ($act_col_numbs[$key_id] == "acc_running_balance") {
                    echo "<th id='order_num'>Running Balance</th>";
                    $last_3column_count = $last_3column_count + 1;
                    $column_visible_count =  $column_visible_count + 1;
                } else {
                }
            }
            ?>

        </tr>
    </thead>
    <?php
    if (isset($_POST['submit_report']) == true || $_GET['fdate'] != "") {
    ?>

        <?php
        $sql_record = "SELECT SUM(amount) as amount,crdr FROM `account_summary` WHERE `date` < '$fdate'" . $coa_filter . "" . $sector_filter . " AND `active` = '1' AND `dflag` = '0' GROUP BY `crdr` ORDER BY `crdr` ASC";
        $query = mysqli_query($conn, $sql_record);
        $tot_bds = $tot_qty = $tot_amt = 0;
        while ($row = mysqli_fetch_assoc($query)) {
            if ($row['crdr'] == "CR" || $row['crdr'] == "cr" || $row['crdr'] == "Cr") {
                $coa_cr_opening_amount = $row['amount'];
            } else if ($row['crdr'] == "DR" || $row['crdr'] == "dr" || $row['crdr'] == "Dr") {
                $coa_dr_opening_amount = $row['amount'];
            } else {
            }
        }
        if ($coa_dr_opening_amount > $coa_cr_opening_amount) {
            $coa_drbal_amount = $coa_dr_opening_amount - $coa_cr_opening_amount;
            $coa_runbal_amount = $coa_dr_opening_amount - $coa_cr_opening_amount;
            if ($last_3column_count > 0) {
                $colspan_count = $column_visible_count - $last_3column_count;
                $loopstart_count = $col_count - $last_3column_count;
                echo "<tr>";
                echo "<td colspan='" . $colspan_count . "' style='text-align:right;'>Previous Balance</td>";
                for ($a = $loopstart_count + 1; $a <= $col_count; $a++) {
                    $key_id = "A:1:" . $a;
                    if ($act_col_numbs[$key_id] == "acc_cr") {
                        echo "<td></td>";
                    } else if ($act_col_numbs[$key_id] == "acc_dr") {
                        echo "<td style='text-align:right;'>" . number_format_ind($coa_drbal_amount) . "</td>";
                    } else if ($act_col_numbs[$key_id] == "acc_running_balance") {
                        echo "<td style='text-align:right;'>" . number_format_ind($coa_runbal_amount) . "</td>";
                    } else {
                    }
                }
                echo "</tr>";
            }
        } else if ($coa_dr_opening_amount < $coa_cr_opening_amount) {
            $coa_crbal_amount = $coa_cr_opening_amount - $coa_dr_opening_amount;
            $coa_runbal_amount = $coa_dr_opening_amount - $coa_cr_opening_amount;
            if ($last_3column_count > 0) {
                $colspan_count = $column_visible_count - $last_3column_count;
                $loopstart_count = $col_count - $last_3column_count;
                echo "<tr>";
                echo "<td colspan='" . $colspan_count . "' style='text-align:right;'>Previous Balance</td>";
                for ($a = $loopstart_count + 1; $a <= $col_count; $a++) {
                    $key_id = "A:1:" . $a;
                    if ($act_col_numbs[$key_id] == "acc_cr") {
                        echo "<td style='text-align:right;'>" . number_format_ind($coa_crbal_amount) . "</td>";
                    } else if ($act_col_numbs[$key_id] == "acc_dr") {
                        echo "<td></td>";
                    } else if ($act_col_numbs[$key_id] == "acc_running_balance") {
                        echo "<td style='text-align:right;'>" . number_format_ind($coa_runbal_amount) . "</td>";
                    } else {
                    }
                }
                echo "</tr>";
            }
        } else {
            $coa_runbal_amount = $coa_balance_amount = 0;
            //echo "<tr><td colspan='3' style='text-align:right;'>Previous Balance</td><td colspan='7'></td><td></td><td style='text-align:right;'></td><td style='text-align:right;'>".$coa_runbal_amount."</td></tr>";
        }
        ?>
        <tbody class="tbody1" id="tbody1">
            <?php
            $sql_record = "SELECT * FROM `account_summary` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$sector_filter." AND `etype` IN ('PayVoucher','ContraNote','JorVoucher') AND `crdr` LIKE 'DR' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
            $query = mysqli_query($conn, $sql_record); $dr_acc = array();
            while($row = mysqli_fetch_assoc($query)){ $dr_acc[$row['trnum']] = $row['coa_code']; }

            $sql_record = "SELECT * FROM `account_summary` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$sector_filter." AND `etype` IN ('PayVoucher','ContraNote','JorVoucher') AND `crdr` LIKE 'CR' AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC";
            $query = mysqli_query($conn, $sql_record); $cr_acc = array();
            while($row = mysqli_fetch_assoc($query)){ $cr_acc[$row['trnum']] = $row['coa_code']; }

            $sql_record = "SELECT * FROM `account_summary` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$coa_filter."".$sector_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`addedtime`,`trnum` ASC";
            $query = mysqli_query($conn, $sql_record); $tot_bds = $tot_qty = $tot_amt = $c = 0;
            while($row = mysqli_fetch_assoc($query)){
                $c++;
                $vendors = $row['vendor'];
                $locations = $row['location'];
            ?>
                <tr>
                <?php
                    for ($i = 1; $i <= $col_count; $i++) {
                        $key_id = "A:1:" . $i;
                        if($act_col_numbs[$key_id] == "sl_no"){ echo "<td title='Sl.No.'>" . $c . "</td>"; }
                        else if($act_col_numbs[$key_id] == "date"){ echo "<td title='Date'>" . date("d.m.Y", strtotime($row['date'])) . "</td>"; }
                        else if($act_col_numbs[$key_id] == "trnum"){ echo "<td title='Transaction No'>" . $row['trnum'] . "</td>"; }
                        else if($act_col_numbs[$key_id] == "transaction_type"){ echo "<td title='Transaction Type'>" . $row['etype'] . "</td>"; }
                        else if($act_col_numbs[$key_id] == "book_no"){ echo "<td title='Doc. No.'>" . $row['dc_no'] . "</td>"; }
                        else if($act_col_numbs[$key_id] == "acc_fromwarehouse"){
                            if (!empty($sector_name[$locations])) { $from_waehouse = $sector_name[$locations]; } else if (!empty($ven_name[$vendors])) { $from_waehouse = $ven_name[$vendors]; } else { }
                            echo "<td title='From Warehouse'>" . $from_waehouse . "</td>";
                        }
                        else if($act_col_numbs[$key_id] == "vehicle_no"){
                            if(!empty($vehicle_code[$row['vehicle_code']])){ $vehicle_name = $vehicle_code[$row['vehicle_code']]; } else { $vehicle_name = $row['vehicle_code']; }
                            echo "<td title='Vehicle Name'>" . $vehicle_name  . "</td>";
                        }
                        else if($act_col_numbs[$key_id] == "item_name"){ echo " <td title='Item'>" . $item_name[$row['item_code']] . "</td>"; }
                        else if($act_col_numbs[$key_id] == "acc_quantity"){ echo "<td title='Quantity'>" . number_format_ind($row['quantity']) . "</td>"; }
                        else if($act_col_numbs[$key_id] == "acc_paid_received"){
                            $paid_Received =  "";
                            if($row['etype'] == "PayVoucher" || $row['etype'] == "JorVoucher" || $row['etype'] == "ContraNote"){
                                if($row['crdr'] == "CR" && $coas == $row['coa_code']){
                                    if(!empty($dr_acc[$row['trnum']])){ $paid_Received =  $coa_name[$dr_acc[$row['trnum']]]; } else{ }
                                }
                                else if($row['crdr'] == "DR" && $coas == $row['coa_code']){
                                    if(!empty($dr_acc[$row['trnum']])){ $paid_Received =  $coa_name[$cr_acc[$row['trnum']]]; } else{ }
                                }
                                else{ }
                            }
                            else if(!empty($ven_name[$vendors])){
                                $paid_Received =  $ven_name[$vendors];
                            }
                            else if(!empty($sector_name[$locations])){
                                $paid_Received =  $sector_name[$locations];
                            }
                            else{ }
                            echo "<td title='Paid Received'>".$paid_Received."</td>";
                        }
                        else if($act_col_numbs[$key_id] == "acc_cheque_no"){ echo "<td title='Cheque No' style='text-align:right;'></td>"; }
                        else if($act_col_numbs[$key_id] == "acc_cheque_date"){ echo "<td title='Cheque Date' style='text-align:right;'></td>"; }
                        else if($act_col_numbs[$key_id] == "acc_remarks"){ echo "<td title='Remarks' style='text-align:left;'>" . $row['remarks'] . "</td>"; }
                        else if($act_col_numbs[$key_id] == "acc_cr"){
                            if($row['crdr'] == "CR"){
                                $cr_amount =  number_format_ind($row['amount']);
                                $coa_crbal_amount = $coa_crbal_amount + $row['amount'];
                                $coa_runbal_amount = $coa_runbal_amount - $row['amount'];
                            }
                            else{ $cr_amount =  number_format_ind("0"); }
                            echo "<td title='Cr Amount'>".$cr_amount."</td>";
                        }
                        else if($act_col_numbs[$key_id] == "acc_dr"){
                            if($row['crdr'] == "DR"){
                                $dr_amount =  number_format_ind($row['amount']);
                                $coa_drbal_amount = $coa_drbal_amount + $row['amount'];
                                $coa_runbal_amount = $coa_runbal_amount + $row['amount'];
                            }
                            else{
                                $dr_amount =  number_format_ind("0");
                            }
                            echo "<td title='Dr Amount'>".$dr_amount."</td>";
                        }
                        else if($act_col_numbs[$key_id] == "acc_running_balance"){
                            echo "<td title='Running Balance' style='text-align:right;'>".number_format_ind($coa_runbal_amount)."</td>";
                        }
                        else { }
                    }
                    $tot_qty += $row['quantity'];
                ?>
                </tr>
            <?php
            }
            ?>
        </tbody>
        <tfoot>
            <tr class="thead4">
                <?php
                    $colspan_count = 0;
                    $colspan_count = $column_visible_count - $last_3column_count;
                    $loopstart_count = $col_count - $last_3column_count;
                    echo "<th colspan='" . $colspan_count . "' style='text-align:right;'>Between Days Total</th>";
                    for ($a = $loopstart_count + 1; $a <= $col_count; $a++) {
                        
                        $key_id = "A:1:" . $a;
                        if ($act_col_numbs[$key_id] == "acc_cr") {
                            echo "<th style='text-align:right;'>" .  number_format_ind(round($coa_crbal_amount, 2)) . "</th>";
                        } else if ($act_col_numbs[$key_id] == "acc_dr") {
                            echo "<th style='text-align:right;'>". number_format_ind(round($coa_drbal_amount, 2)) . "</th>";
                        } else if ($act_col_numbs[$key_id] == "acc_running_balance") {
                            echo "<th style='text-align:right;'>" . number_format_ind(round($coa_runbal_amount, 2)) . "</th>";
                        } else {
                        }
                    }
                ?>
            </tr>
            <tr class="thead4">
                <?php
                    $colspan_count = 0;
                    $colspan_count = $column_visible_count - $last_3column_count;
                    $loopstart_count = $col_count - $last_3column_count;
                    echo "<th colspan='" . $colspan_count . "' style='text-align:right;'>Closing Balance</th>";
                    for ($a = $loopstart_count + 1; $a <= $col_count; $a++) {
                        $key_id = "A:1:" . $a;
                        if ($act_col_numbs[$key_id] == "acc_cr") {
                            if ($coa_crbal_amount > $coa_drbal_amount) {
                                echo "<th style='text-align:right;'>" . number_format_ind(round(($coa_crbal_amount - $coa_drbal_amount), 2)) . "</th>";
                            }else{
                                echo "<th></th>";
                            }
                           
                        } else if ($act_col_numbs[$key_id] == "acc_dr") {
                            if ($coa_crbal_amount <= $coa_drbal_amount) {
                                echo "<th style='text-align:right;'>". number_format_ind(round(($coa_drbal_amount - $coa_crbal_amount), 2)). "</th>";
                            } else{
                                echo "<th></th>";
                            }
                        } else if ($act_col_numbs[$key_id] == "acc_running_balance") {
                            echo "<th></th>";
                        } else {
                        }
                    }
                ?>
            </tr>
        </tfoot>
    <?php
    }
    ?>
    </table><br /><br /><br />
    <script>
        function checkval() {
            var coas = document.getElementById("coas").value;
            var fdate = document.getElementById("fdate").value;
            var tdate = document.getElementById("tdate").value;

            if(fdate == ""){
                alert("Please select Start Date");
                document.getElementById("fdate").focus();
                l = false;
            }
            else if(tdate == ""){
                alert("Please select End Date");
                document.getElementById("tdate").focus();
                l = false;
            }
            else if (coas.match("select")) {
                alert("Kindly select CoA to fetch details");
                document.getElementById("coas").focus();
                return false;
            } else {
                return true;
            }
        }
    </script>
    <script>
        function update_masterreport_status(a){
                var file_url = '<?php echo $field_href[0]; ?>';
                var user_code = '<?php echo $user_code; ?>';
                var field_name = a;
                var modify_col = new XMLHttpRequest();
                var method = "GET";
                var url = "broiler_modify_clientfieldstatus.php?file_url="+file_url+"&user_code="+user_code+"&field_name="+field_name;
                //window.open(url);
                var asynchronous = true;
                modify_col.open(method, url, asynchronous);
                modify_col.send();
                modify_col.onreadystatechange = function(){
                    if(this.readyState == 4 && this.status == 200){
                        var item_list = this.responseText;
                        if(item_list == 0){
                            //alert("Column Modified Successfully ...! \n Kindly reload the page to see the changes.")
                        }
                        else{
                            alert("Invalid request \n Kindly check and try again ...!");
                        }
                    }
                }
            }
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
                    slnos();
                    asc = !asc;
                })
            });
        }

        function convertDate(d) {
            var p = d.split(".");
            return (p[2] + p[1] + p[0]);
        }

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
                    slnos();
                    asc = !asc;
                })
            });
        }

        function convertNumber(d) {
            var p = intval(d);
            return (p);
        }

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
                        if (isNaN(a_val)) {
                            a_val1 = a_val.split(',').join('');
                        } else {
                            a_val1 = a_val;
                        }
                        const b_val = b.children[index].innerText;
                        if (isNaN(b_val)) {
                            b_val1 = b_val.split(',').join('');
                        } else {
                            b_val1 = b_val;
                        }
                        return (asc) ? b_val1 - a_val1 : a_val1 - b_val1
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector(".tbody1").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                })
            });

        }

        function slnos() {
            var rcount = document.getElementById("tbody1").rows.length;
            var myTable = document.getElementById('tbody1');
            var j = 0;
            for (var i = 1; i <= rcount; i++) {
                j = i - 1;
                myTable.rows[j].cells[0].innerHTML = i;
            }
        }

        table_sort();
        table_sort2();
        table_sort3();
    </script>
    <script src="../datepicker/jquery/jquery.js"></script>
    <script src="../datepicker/jquery-ui.js"></script>
</body>

</html>
<?php
include "header_foot.php";
?>