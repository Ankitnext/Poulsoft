<?php
//broiler_supplieraging_report2.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Supplier Age Analysis Report";
include "header_head.php";

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `dflag` = '0' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $vendor_code = $vendor_name = $vendor_group = array();
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_name[$row['code']] = $row['name']; $vendor_group[$row['code']] = $row['groupcode']; }

$grp_list = implode("','", $vendor_group);
$sql = "SELECT * FROM `main_groups` WHERE `dflag` = '0' AND `code` IN ('$grp_list') ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $grp_code = $grp_name = $control_acc_group = array();
while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; $control_acc_group[$row['code']] = $row['sup_controller_code']; }

$fdate = $tdate = date("Y-m-d"); $vendors = $groups = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $vendors = $_POST['vendors'];
    $groups = $_POST['groups'];

    if($vendors != "all"){
        $gcode = ""; $gcode = $vendor_group[$vendors]; $ccode = ""; $ccode = $control_acc_group[$gcode];
        $vendor_filter = " AND `vendor` IN ('$vendors')";
        $coa_filter = " AND `coa_code` IN ('$ccode')";
    }
    else if($groups != "all"){
        $vendor_list = $group_list = "";
        foreach($vendor_code as $vcode){
            if($vendor_group[$vcode] == $groups){
                $gcode = ""; $gcode = $vendor_group[$vcode]; $ccode = ""; $ccode = $control_acc_group[$gcode];
                if($vendor_list == ""){ $vendor_list = $vcode; } else{ $vendor_list = $vendor_list."','".$vcode; }
                if($group_list == ""){ $group_list = $ccode; } else{ $group_list = $group_list."','".$ccode; }
            }
        }
        $vendor_filter = " AND `vendor` IN ('$vendor_list')";
        $coa_filter = " AND `coa_code` IN ('$group_list')";
    }
    else{
        $vendor_list = $group_list = "";
        foreach($vendor_code as $vcode){
            $gcode = ""; $gcode = $vendor_group[$vcode]; $ccode = ""; $ccode = $control_acc_group[$gcode];
            if($vendor_list == ""){ $vendor_list = $vcode; } else{ $vendor_list = $vendor_list."','".$vcode; }
            if($group_list == ""){ $group_list = $ccode; } else{ $group_list = $group_list."','".$ccode; }
        }
        $vendor_filter = " AND `vendor` IN ('$vendor_list')";
        $coa_filter = " AND `coa_code` IN ('$group_list')";
    }
    
    $export_tdate = $_POST['tdate'];
    $export_supplier =$vendor_name[$_POST['vendors']]; if ($export_supplier == "") { $export_supplier = "All"; } 
    $filename = "Supplier Age Analysis_".$export_tdate;
    $excel_type = $_POST['export'];
//$url = "../PHPExcel/Examples/broiler_supplieraging-Excel.php?fromdate=".$fdate."&todate=".$tdate."&vendors=".$vendors;
}
else{
    $url = "";
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
        <style>
            .thead4{
                font-weight: bold;
            }
        </style>
    </head>
    <body align="center">
        <table class="tbl" style="width:auto;" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="14" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Supplier Age Analysis Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_supplieraging_report2.php" method="post" onSubmit="return checkval()">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="16">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Group</label>
                                    <select name="groups" id="groups" class="form-control select2" style="width:250px;" onchange="update_vendor_details();">
                                        <option value="all" <?php if($groups == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($grp_code as $fcode){ if($grp_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($groups == $fcode){ echo "selected"; } ?>><?php echo $grp_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Supplier</label>
                                    <select name="vendors" id="vendors" class="form-control select2" style="width:250px;">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vendor_code as $fcode){ if($vendor_name[$fcode] != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($vendors == $fcode){ echo "selected"; } ?>><?php echo $vendor_name[$fcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2"  onchange="tableToExcel('main_body', 'Supplier Age Analysis','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
                                        <option value="display" <?php if($excel_type == "display"){ echo "selected"; } ?>>-Display-</option>
                                        <option value="excel" <?php if($excel_type == "excel"){ echo "selected"; } ?>>-Excel-</option>
                                        <option value="print" <?php if($excel_type == "print"){ echo "selected"; } ?>>-Print-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group"><br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Submit</button>
                                </div>
                                <div class="m-2 form-group" style="visibility:hidden;">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
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
                     <th colspan="16" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Supplier Age Analysis Report</h5></th>
                 </tr>
             
             <?php } ?>
             <tr>
                       
                       <th colspan="16">
                                   <div class="row">
                                      
                                       <div class="m-2 form-group">
                                           <label>To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                       </div>
                                         
                                                                   
                                       <div class="m-2 form-group">
                                           <label>Supplier: <?php echo $export_supplier; ?></label>
                   
                                       </div>
                                        <div class="m-2 form-group">
                                           <label><br/></label>

                                       </div>
                               </th>
                           
                       </tr>
                      
            </thead>
            
            <thead class="thead3" align="center">
                <tr align="center">
                    <th>Sl.No.</th>
                    <th>Group</th>
                    <th>Supplier Name</th>
                    <th>0 To 30 Days</th>
                    <th>31 To 60 Days</th>
                    <th>61 To 90 Days</th>
                    <th>91 To 120 Days</th>
                    <th>121 To 150 Days</th>
                    <th>151 To 180 Days</th>
                    <th>181 And More Days</th>
                    <th>Total Pending</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(isset($_POST['submit_report']) == true){
                $sql = "SELECT SUM(amount) as amount,vendor FROM `account_summary` WHERE `date` <= '$tdate' AND `active` = '1'".$coa_filter."".$vendor_filter." AND `dflag` = '0' AND `crdr` = 'DR' GROUP BY `vendor` ORDER BY `vendor` ASC";
                $query = mysqli_query($conn,$sql); $vendor_payments = array(); $key = "";
                while($row = mysqli_fetch_assoc($query)){ $key = $row['vendor']; $vendor_payments[$key] = $row['amount']; }

                $supplier_name = $supplier_date = $supplier_tnum = $supplier_damt = $supplier_rbal = $supplier_days = $supplier_fbal = $vendor_balamt = $supplier_balance = 
                $days30 = $days60 = $days90 = $days120 = $days150 = $days180 = $days181 = array();
                $key = ""; $days_30 = $days_60 = $days_90 = $days_120 = $days_150 = $days_180 = $days_181 = $ftot_amt = $i = $inv_tamt = $inv_damt = $supplier_fbal = 0;

                $sql = "SELECT `date`,`trnum`,`vendor`,SUM(amount) as amount FROM `account_summary` WHERE `date` <= '$tdate' AND `active` = '1'".$coa_filter."".$vendor_filter." AND `dflag` = '0' AND `crdr` = 'CR' GROUP BY `date`,`trnum`,`vendor` ORDER BY `date`,`trnum`,`vendor` ASC";
                $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                    $key = $row['vendor'];
                    if(empty($supplier_balance[$key]) || $supplier_balance[$key] == ""){ $supplier_balance[$key] = 0; }
                    
                    if((float)$vendor_payments[$key] >= (float)$row['amount']){
                        $vendor_payments[$key] = (float)$vendor_payments[$key] - (float)$row['amount'];
                    }
                    else{
                        $days = 0; $days = ((strtotime(date("d.m.Y")) - strtotime($row['date'])) / 60 / 60 / 24); $i++;
                        if($vendor_payments[$key] > 0){
                            $supplier_balance[$key] = (float)$row['amount'] - (float)$vendor_payments[$key];
                            $inv_amt = 0; $inv_amt = (float)$row['amount'] - (float)$vendor_payments[$key];
                            $vendor_payments[$key] = 0; 
                        }
                        else{
                            $supplier_balance[$key] += (float)$row['amount'];
                            $inv_amt = 0; $inv_amt = (float)$row['amount'];
                        }
                        $vendor_array_list[$key] = $key;
                        
                        if($days >= 0 && $days <= 30){ $days30[$key] = (float)$days30[$key] + (float)$inv_amt; }
                        if($days >= 31 && $days <= 60){ $days60[$key] = (float)$days60[$key] + (float)$inv_amt; }
                        if($days >= 61 && $days <= 90){ $days90[$key] = (float)$days90[$key] + (float)$inv_amt; }
                        if($days >= 91 && $days <= 120){ $days120[$key] = (float)$days120[$key] + (float)$inv_amt; }
                        if($days >= 121 && $days <= 150){ $days150[$key] = (float)$days150[$key] + (float)$inv_amt; }
                        if($days >= 151 && $days <= 180){ $days180[$key] = (float)$days180[$key] + (float)$inv_amt; }
                        if($days >= 181){ $days181[$key] = (float)$days181[$key] + (float)$inv_amt; }
                        $supplier_fbal += (float)$inv_amt;
                        
                        /*Final Total Calculations*/
                        if($days >= 0 && $days <= 30){ $days_30 = $days_30 + $inv_amt; }
                        if($days >= 31 && $days <= 60){ $days_60 = $days_60 + $inv_amt; }
                        if($days >= 61 && $days <= 90){ $days_90 = $days_90 + $inv_amt; }
                        if($days >= 91 && $days <= 120){ $days_120 = $days_120 + $inv_amt; }
                        if($days >= 121 && $days <= 150){ $days_150 = $days_150 + $inv_amt; }
                        if($days >= 151 && $days <= 180){ $days_180 = $days_180 + $inv_amt; }
                        if($days >= 181){ $days_181 = $days_181 + $inv_amt; }
                    }
                }
                /*Display Supplier Analysis*/
                $i = 0;
                foreach($vendor_code as $key){
                    //echo "<br/>".$key."@".$supplier_balance[$key];
                    if(number_format_ind($supplier_balance[$key]) != "0.00"){
                        $url_link = "broiler_supplieraging_report.php?fdate=".$fdate."&tdate=".$tdate."&vendors=".$key;
                        $i++;
                        echo "<tr>";
                        echo "<td style='text-align:center;'>".$i."</td>";
                        echo "<td style='text-align:left;'>".$grp_name[$vendor_group[$key]]."</td>";
                        echo "<td style='text-align:left;'><a href='$url_link' target='_BLANK'>".$vendor_name[$key]."</a></td>";
                        echo "<td style='text-align:right;'>".number_format_ind($days30[$key])."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($days60[$key])."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($days90[$key])."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($days120[$key])."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($days150[$key])."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($days180[$key])."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($days181[$key])."</td>";
                        echo "<td style='text-align:right;'>".number_format_ind($supplier_balance[$key])."</td>";
                        echo "</tr>";
                    }
                }
                
                echo "<tr class='thead4'>";
                echo "<td colspan='3' style='text-align:right;'>Total</td>";
                echo "<td style='text-align:right;'>".number_format_ind($days_30)."</td>";
                echo "<td style='text-align:right;'>".number_format_ind($days_60)."</td>";
                echo "<td style='text-align:right;'>".number_format_ind($days_90)."</td>";
                echo "<td style='text-align:right;'>".number_format_ind($days_120)."</td>";
                echo "<td style='text-align:right;'>".number_format_ind($days_150)."</td>";
                echo "<td style='text-align:right;'>".number_format_ind($days_180)."</td>";
                echo "<td style='text-align:right;'>".number_format_ind($days_181)."</td>";
                echo "<td style='text-align:right;'>".number_format_ind($supplier_fbal)."</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
        <script>
            function checkval(){
                var vendors = document.getElementById("vendors").value;
                if(vendors.match("select")){
                    alert("Please select Supplier");
                    document.getElementById("vendors").focus();
                    return false;
                }
                else{
                    return true;
                }
            }
        </script>
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

        <script>
            function update_vendor_details(){
                var groups = document.getElementById("groups").value;
                removeAllOptions(document.getElementById("vendors"));

                sel_tag = document.getElementById("vendors");
                opt_tag = document.createElement("OPTION");
                opt_txt = document.createTextNode("-All-");
                opt_tag.value = "all";
                opt_tag.appendChild(opt_txt);
                sel_tag.appendChild(opt_tag);

                if(groups == "all"){
                    <?php foreach($vendor_code as $scode){ ?> 
                        opt_tag = document.createElement("OPTION");
                        opt_txt = document.createTextNode("<?php echo $vendor_name[$scode]; ?>");
                        opt_tag.value = "<?php echo $scode; ?>";
                        opt_tag.appendChild(opt_txt);
                        sel_tag.appendChild(opt_tag);	
                    <?php } ?>
                }
                else{
                    <?php foreach($vendor_code as $scode){ $groups = $vendor_group[$scode]; echo "if(groups == '$groups'){"; ?> 
                        opt_tag = document.createElement("OPTION");
                        opt_txt = document.createTextNode("<?php echo $vendor_name[$scode]; ?>");
                        opt_tag.value = "<?php echo $scode; ?>";
                        opt_tag.appendChild(opt_txt);
                        sel_tag.appendChild(opt_tag);	
                    <?php echo "}"; } ?>
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
    </body>
</html>
<?php
include "header_foot.php";
?>