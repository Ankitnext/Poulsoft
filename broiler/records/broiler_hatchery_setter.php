<?php
//broiler_hatchery_setter.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    $dbname = $_SESSION['dbase'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_hatchery_setter.php";
}
else{
    $user_code = $_GET['userid'];
    $dbname = $db;
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_hatchery_setter.php?db=$db&userid=".$user_code;
}
include "decimal_adjustments.php";

$file_name = "Hatchery Setter Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

/*Check for Table Availability*/
$database_name = $dbname; $table_head = "Tables_in_".$database_name; $exist_tbl_names = array(); $i = 0;
$sql1 = "SHOW TABLES;"; $query1 = mysqli_query($conn,$sql1); while($row1 = mysqli_fetch_assoc($query1)){ $exist_tbl_names[$i] = $row1[$table_head]; $i++; }
if(in_array("main_officetypes", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.main_officetypes LIKE poulso6_admin_broiler_broilermaster.main_officetypes;"; mysqli_query($conn,$sql1); }
if(in_array("inv_sectors", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.inv_sectors LIKE poulso6_admin_broiler_broilermaster.inv_sectors;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_received_main_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_received_main_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_received_main_details;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_received_link_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_received_link_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_received_link_details;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_grading_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_grading_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_grading_details;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_grading_item_stocks", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_grading_item_stocks LIKE poulso6_admin_broiler_broilermaster.plant_bird_grading_item_stocks;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_portioning_consumed_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_portioning_consumed_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_portioning_consumed_details;"; mysqli_query($conn,$sql1); }
if(in_array("plant_bird_portioning_produced_details", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.plant_bird_portioning_produced_details LIKE poulso6_admin_broiler_broilermaster.plant_bird_portioning_produced_details;"; mysqli_query($conn,$sql1); }
if(in_array("broiler_sales", $exist_tbl_names, TRUE) == ""){ $sql1 = "CREATE TABLE $database_name.broiler_sales LIKE poulso6_admin_broiler_broilermaster.broiler_sales;"; mysqli_query($conn,$sql1); }

$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%hatch%' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql); $hatchery_alist = array();
while($row = mysqli_fetch_assoc($query)){ $hatchery_alist[$row['code']] = $row['code']; }

$hatchery_list = implode("','",$hatchery_alist);
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$hatchery_list') AND `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$hatcheries = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $hatcheries = $_POST['hatcheries'];
    $excel_type = $_POST['export'];
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table id="main_table" class="tbl" align="center">
            <thead class="thead3" align="center" width="auto">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="2" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed" width="auto" <?php if($excel_type == "print"){ echo 'style="display:none;"'; } ?>>
                    <tr>
                        <th colspan="4">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Hatchery</label>
                                    <select name="hatcheries" id="hatcheries" class="form-control select2">
                                        <option value="all" <?php if($hatcheries == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $scode){ if($sector_name[$scode] != ""){ ?>
                                        <option value="<?php echo $scode; ?>" <?php if($hatcheries == $scode){ echo "selected"; } ?>><?php echo $sector_name[$scode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="download_to_excel('main_table','<?php echo $file_name; ?>');">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="search_table">Search</label>
                                    <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <?php
            
            $html = $nhtml = $fhtml = '';
            $html .= '<thead class="thead3" id="head_names">';

            $nhtml .= '<tr style="text-align:center;" align="center">';
            $fhtml .= '<tr style="text-align:center;" align="center">';

            $nhtml .= '<th>Sl.No.</th>'; $fhtml .= '<th id="order_num">Sl.No.</th>';
            $nhtml .= '<th>Hatchery</th>'; $fhtml .= '<th id="order">Hatchery</th>';
            $nhtml .= '<th>Setter No.</th>'; $fhtml .= '<th id="order">Setter No.</th>';
            $nhtml .= '<th>Capacity</th>'; $fhtml .= '<th id="order_num">Capacity</th>';

            $nhtml .= '</tr>';
            $fhtml .= '</tr>';
            $html .= $fhtml;
            $html .= '</thead>';
            $html .= '<tbody class="tbody1" id="tbody1">';

            if(isset($_POST['submit_report']) == true){
                $hcry_fltr = ""; if($hatcheries != "all"){ $hcry_fltr = " AND `hatchery_code` IN ('$hatcheries')"; }

                //Hatcher
                $sql = "SELECT * FROM `broiler_hatchery_setter` WHERE `dflag` = '0'".$hcry_fltr." ORDER BY `hatchery_code`,`setter_no` ASC";
                $query = mysqli_query($conn,$sql); $slno = $t_capacity = 0;
                while($row = mysqli_fetch_array($query)){
                    $slno++;
                    $hcry_name = $sector_name[$row['hatchery_code']];
                    $h_name = $row['setter_no'];
                    $capacity = $row['setter_capacity'];

                    $html .= '<tr>';
                    $html .= '<td style="text-align:center;">'.$slno.'</td>';
                    $html .= '<td style="text-align:left;">'.$hcry_name.'</td>';
                    $html .= '<td style="text-align:left;">'.$h_name.'</td>';
                    $html .= '<td style="text-align:right;">'.decimal_adjustments($capacity,2).'</td>';
                    $html .= '</tr>';

                    $t_capacity += (float)$capacity;
                }
            }
            $html .= '<tr class="thead2">';
            $html .= '<th colspan="3">Total</th>';
            $html .= '<th style="text-align:right;">'.decimal_adjustments($t_capacity,2).'</th>';
            $html .= '</tr>';
            $html .= '</tbody>';

            echo $html;
        ?>
        </table><br/><br/><br/>
        <script type="text/javascript" src="table_sorting_wauto_slno.js"></script>
        <script type="text/javascript" src="table_search_fields.js"></script>
        <script type="text/javascript" src="table_download_excel.js"></script>
        <script type="text/javascript" src="table_column_date_format_change.js"></script>
        <script src="table_column_date_format_change.js"></script>
        <script type="text/javascript">
            function table_file_details1(){
                var dbname = '<?php echo $dbname; ?>';
                var fname = '<?php echo $wsfile_path; ?>';
                var wapp_msg = '<?php echo $file_name; ?>';
                var sms_type = '<?php echo $sms_type; ?>';
                return dbname+"[@$&]"+fname+"[@$&]"+wapp_msg+"[@$&]"+sms_type;
            }
            function table_heading_to_normal1(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_normal2(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $hhtml; ?>';
                html += '<?php echo $nhtml; ?>';
                $('#head_names').append(html);
            }
            function table_heading_to_standard_filters(){
                document.getElementById("head_names").innerHTML = "";
                var html = '';
                html += '<?php echo $fhtml; ?>';
                document.getElementById("head_names").innerHTML = html;
                    
                $('#export').select2();
                document.getElementById("export").value = "display";
                $('#export').select2();
                table_sort();
                table_sort2();
                table_sort3();
            }
        </script>
        <script>
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>