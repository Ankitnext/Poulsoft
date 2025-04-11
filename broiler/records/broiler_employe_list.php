<?php
//broiler_employe_list.php
include "../newConfig.php";
$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
include "header_head.php";
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    $sector_code[$row['code']] = $row['code'];
    $sector_name[$row['code']] = $row['description'];
}
$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $mode_name[$row['code']] = $row['description']; $mode_code[$row['code']] = $row['code']; }
$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $method_name[$row['code']] = $row['description']; $method_code[$row['code']] = $row['code']; }
$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; }
$sql = "SELECT * FROM `breeder_cus_lines` WHERE `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cline_code[$row['code']] = $row['code']; $cline_name[$row['code']] = $row['description']; }
$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $group_code[$row['code']] = $row['code']; $group_name[$row['code']] = $row['description']; }
$sql = "SELECT * FROM `country_states` WHERE `dflag` = '0' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $state_code[$row['code']] = $row['code']; $state_name[$row['code']] = $row['name']; }
$sql = "SELECT * FROM `broiler_designation` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $desg_code[$row['code']] = $row['code']; $desg_name[$row['code']] = $row['description']; }
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }
//$sql = "SELECT * FROM `extra_access` WHERE `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE '%$user_code%' OR `field_name` IN ('Decimal','Purchase Qty') AND `user_access` LIKE 'all'"; $query = mysqli_query($conn,$sql);
//while($row = mysqli_fetch_assoc($query)){ if($row['field_name'] == "Decimal"){ $decimal_no = $row['flag']; } if($row['field_name'] == "Purchase Qty"){ $qty_on_sqty_flag = $row['flag']; } }
$design = $groups = $clines = "all"; $excel_type = "display"; $url = "";
if(isset($_POST['submit_report']) == true){
    $design = $_POST['design'];
    // $groups = $_POST['groups'];
    // $clines = $_POST['cline'];
    $desg_fltr = "";
    if($design != "all"){ $desg_fltr = " AND `desig_code` IN ('$design')"; }
    // $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%'".$vcodes."".$cline_fltr." ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
    // while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_ccode[$row['code']] = $row['cus_ccode'];$vendor_name[$row['code']] = $row['name'];$cus_alist[$row['code']] = $row['code']; }
    // $cus_list = implode("','",$cus_alist);
    // $customer_filter = " AND `ccode` IN ('$cus_list')";
    // if($vendors == "all"){ $vcodes = ""; } else{ $vcodes = " AND `code` = '$vendors'"; }
    // if($groups != "all"){ $gcodes = " AND `groupcode` = '$groups'"; } else{ $gcodes = ""; }
    // $export_vendors =$vendor_name[$_POST['vendors']]; if ($export_vendors == "") { $export_vendors = "All"; }
    // $export_groups = $group_name[$_POST['groups']]; if ($export_groups == "") { $export_groups = "All"; } 
    $export_desgns =$desg_name[$_POST['design']]; if ($export_desgns == "") { $export_desgns = "All"; }
    $filename = "Employee List";
	$excel_type = $_POST['export'];
	//$url = "../PHPExcel/Examples/CustomerListReport-Excel.php?fromdate=".$fdate."&todate=".$tdate."&vendors=".$vendors."&groups=".$groups;
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <?php
            if($excel_type == "print"){
                echo '<style>body { padding:10px;text-align:center; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { display:none;background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .thead2_empty_row { display:none; }
                .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); font-weight:bold; }</style>';
            }
            else{
                echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
                table.tbl { left:0;margin-right: auto;visibility:visible; }
                table.tbl2 { left:0;margin-right: auto; }
                .tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:11px;color:black;border:0.1vh solid #585858;border-collapse:collapse; }
                .thead1 { background-image: linear-gradient(#D5D8DC,#D5D8DC); box-shadow: 0px 0px 10px #EAECEE; }
                .thead2 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .thead3 { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .thead4 { background-image: linear-gradient(#D5D8DC,#D5D8DC); }
                .tbody1 { background-image: linear-gradient(#F5EEF8,#F5EEF8); }
                .report_head { background-image: linear-gradient(#ABB2B9,#ABB2B9); }
                .tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';
            }
        ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="19" align="center"><?php echo $row['cdetails']; ?><h5>Customer List Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_employe_list.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="21">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Designation</label>
                                    <select name="design" id="design" class="form-control select2">
                                        <option value="all" <?php if($design == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($desg_code as $cust){ if($desg_name[$cust] != ""){ ?>
                                        <option value="<?php echo $cust; ?>" <?php if($design == $cust){ echo "selected"; } ?>><?php echo $desg_name[$cust]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2"  onchange="tableToExcel('main_body', 'Customer List','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
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
            </table>
            </form>
            <div class="row" style="padding-left:100px;">
            <div class="m-2 form-group">
                                    <input style="width: 300px;padding-left:100px;" type="text" class="cd-search table-filter" data-table="tbl" placeholder="Search here..." />
                                    <br/>
                                </div>
            </div>
           <table id="main_body" class="tbl" align="center"  style="width:1300px;">
            <thead class="thead1" align="center" style="width:1212px;  display:none; ">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
                <tr align="center">
                <th colspan="20" align="center"><?php echo $row['cdetails']; ?><h5>Employee List Report</h5></th>
                </tr>
            <?php } ?>
                <tr>
                <th colspan="20">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>Employee: <?php echo $export_desgns; ?></label>
                                </div>
                                <!-- <div class="m-2 form-group">
                                    <label>Group: <?php //echo $export_groups; ?></label>
                                </div> -->
                                <div class="m-2 form-group">
                                    <label><br/></label>
                                </div>
                        </th>
                </tr>
            </thead>
            <thead class="thead3" align="center">
                <tr align="center">
                    <th>Sl No.</th>
                    <th>Emp. Id</th>
                    <th>Employee Name</th>
                    <th>Designation</th>
                    <th>Sector</th>
                    <th>Birth Date</th>
                    <th>Join Date</th>
                    <th>Rejoin Date</th>
                    <th>Salary</th>
                    <th>Qualification</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Relationship</th>
                    <th>Contact No</th>
                    <th>Aadhar No</th>
                    <th>PAN No</th>
                    <th>Vehicle No</th>
                    <th>Drv. Lic No</th>
                    <th>Exp. Date Drv. Lic</th>
                    <th>Street</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Pincode</th>
                    <th>UAN No.</th>
                    <th>ESI No</th>
                    <th>Bank Name</th>
                    <th>Account No</th>
                    <th>IFSC Code</th>
                    <th>Bank Branch</th>
                    <th>Reference Person</th>
                    <th>Blood Group</th>
                    <th>Father Name</th>
                    <th>PP Contact</th>
                    <th>Note</th>
                    <th>Status</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $sl = 1;
                $sql_record = "SELECT * FROM `broiler_employee` WHERE `dflag` = '0'".$desg_fltr." AND `active` = '1' ORDER BY `name` ASC";
                $query = mysqli_query($conn,$sql_record); $tot_bds = $tot_qty = $tot_amt = 0;
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td title="Sl No."><?php echo $sl++; ?></td>
                    <td title="Employee Id"><?php echo $row['emp_id']; ?></td>
                    <td title="Employee Name"><?php echo $row['name']; ?></td>
                    <td title="Designation"><?php echo $desg_name[$row['desig_code']]; ?></td>
                    <td title="Sector"><?php echo $sector_name[$row['warehouse']]; ?></td>
                    <td title="Birth Date"><?php echo $row['birth_date']; ?></td>
                    <td title="Join Date"><?php echo $row['join_date']; ?></td>
                    <td title="Rejoin Date"><?php echo $row['join_date']; ?></td>
                    <td title="Salary"><?php echo $row['gross_salary']; ?></td>
                    <td title="Qualification"></td>
                    <td title="Email"><?php echo $row['email']; ?></td>
                    <!-- <td title="D.O.B"><?php //echo date("d.m.Y",strtotime($row['birth_date'])); ?></td> -->
                    <td title="Gender"><?php echo $row['gender']; ?></td>
                    <td title="Relationship"></td>
                    <td title="Mobile"><?php echo $row['mobile']; ?></td>
                    <td title="Adhar No."><?php echo $row['aadhar_no']; ?></td>
                    <td title="Pan No."><?php echo $row['pan_no']; ?></td>
                    <td title="Vehicle"><?php echo $row['vehicle']; ?></td>
                    <td title="Driving Licence No."></td>
                    <td title="Exp Driving Licence No."></td>
                    <td title="Street Name"><?php echo $row['street_name']; ?></td>
                    <td title="City Name"><?php echo $row['city_name']; ?></td>
                    <td title="State"><?php echo $state_name[$row['state_code']]; ?></td>
                    <td title="Pincode"><?php echo $row['pincode']; ?></td>
                    <td title="Uan No."><?php echo $row['uan_no']; ?></td>
                    <td title="Esi No."><?php echo $row['esi_no']; ?></td>
                    <td title="Bank Name"><?php echo $row['bank_name']; ?></td>
                    <td title="Bank Acc. No."><?php echo $row['bank_acc_no']; ?></td>
                    <td title="IFSC Code"><?php echo $row['bank_ifsc_code']; ?></td>
                    <td title="Branch Name"><?php echo $row['bank_branch_name']; ?></td>
                    <td title="Reference Person"></td>
                    <td title="Blood Group"></td>
                    <td title="Father Name"></td>
                    <td title="PP Contact"></td>
                    <td title="Note"><?php echo $row['remarks']; ?></td>
                    <?php
                        $status = $row['active']; 
                        if ($status == "1" || $status == 1) { 
                        ?>
                            <td title="Status A">Active</td>
                        <?php 
                        } else { 
                        ?>
                            <td title="Status In">Inactive</td>
                        <?php 
                        } 
                        ?>
                </tr>
                <?php
                }
                ?>
            </tbody>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    <script src="../table_search_filter/Search_Script.js"></script>
    <script type="text/javascript">
var tableToExcel = (function() {
  var uri = 'data:application/vnd.ms-excel;base64,'
    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
   // if (selectedValue === 'excel') {  
  return function(table, name, filename, chosen) {
    if (chosen === 'excel') { 
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    //window.location.href = uri + base64(format(template, ctx))
    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
    }
  }
//}
})()
</script>
    </body>
</html>
<?php
include "header_foot.php";
?>