<?php
//broiler_feed_production.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

include "header_head.php";
$user_code = $_SESSION['userid'];

$sql = "SELECT * FROM `main_access` WHERE `active` = '1' AND `empcode` = '$user_code'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $branch_access_code = $row['branch_code']; $line_access_code = $row['line_code']; $farm_access_code = $row['farm_code']; $sector_access_code = $row['loc_access']; }
if($branch_access_code == "all"){ $branch_access_filter1 = ""; }
else{ $branch_access_list = implode("','", explode(",",$branch_access_code)); $branch_access_filter1 = " AND `code` IN ('$branch_access_list')"; $branch_access_filter2 = " AND `branch_code` IN ('$branch_access_list')"; }
if($line_access_code == "all"){ $line_access_filter1 = ""; }
else{ $line_access_list = implode("','", explode(",",$line_access_code)); $line_access_filter1 = " AND `code` IN ('$line_access_list')"; $line_access_filter2 = " AND `line_code` IN ('$line_access_list')"; }
if($farm_access_code == "all"){ $farm_access_filter1 = ""; }
else{ $farm_access_list = implode("','", explode(",",$farm_access_code)); $farm_access_filter1 = " AND `code` IN ('$farm_access_list')"; }
if($sector_access_code == "all"){ $sector_access_filter1 = ""; }
else{ $sector_access_list = implode("','", explode(",",$sector_access_code)); $sector_access_filter1 = " AND `code` IN ('$sector_access_list')"; }


$feedmill_type_code = "";
$sql = "SELECT * FROM `main_officetypes` WHERE `description` LIKE '%Feedmill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%mill%' AND `active` = '1' AND `dflag` = '0' OR `description` LIKE '%feed mill%' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($feedmill_type_code == ""){ $feedmill_type_code = $row['code']; } else{ $feedmill_type_code = $feedmill_type_code."','".$row['code']; }
}
$sql = "SELECT * FROM `inv_sectors` WHERE `type` IN ('$feedmill_type_code') ".$sector_access_filter1." AND `active` = '1' AND `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_employee`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $emp_code[$row['code']] = $row['code']; $emp_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `broiler_feed_formula` GROUP BY `code` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $formula_code[$row['code']] = $row['code']; $formula_name[$row['code']] = $row['description']; }

$item_cats = array();
$sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' and (`description` like '%feed%' OR `description` like '%Premix%') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_cats[$row['code']] = $row['code']; }

$icat_list = implode("','",$item_cats);
$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' AND `category` IN ('$icat_list') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'broiler_display_feedproduction2.php' AND `field_function` = 'Converting Feed To Bags' AND `flag` = '1'";
            $query = mysqli_query($conn,$sql);
            $countbagflag = mysqli_num_rows($query);
            if($countbagflag > 0){
                while($row = mysqli_fetch_assoc($query)){
                    $bagval = $row['field_value'];
                }
            }

$fdate = $tdate = date("Y-m-d"); $sectors = "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $sectors = $_POST['sectors'];
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));

    if($sectors == "all"){ $mill_filter = ""; } else{ $mill_filter = " AND `feed_mill` IN ('$sectors')"; }
    
    $feed_type_list = "";
    foreach($_POST['feedtype'] as $ft2){
        //echo "<br/>".$ft2;
        if($ft2 == "all"){ $feed_type_list = ""; }
        else{
            if($feed_type_list == ""){
                $feed_type_list = $ft2;
            }
            else{
                $feed_type_list = $feed_type_list."','".$ft2;
            }
        }
    }
    if($feed_type_list == ""){
        $mill_filter .= "";
    }
    else{
        $mill_filter .= " AND `feed_code` IN ('$feed_type_list')";
    }

    $export_fdate = $_POST['fdate'];
    $export_tdate = $_POST['tdate'];
    $export_sectors = $sector_name[$_POST['sectors']]; if ( $export_sectors == "") {  $export_sectors = "All"; }
   
    $efeed_type_list = "";
    foreach($_POST['feedtype'] as $eft2){
        //echo "<br/>".$ft2;
        if($eft2 == "all"){ $efeed_type_list = ""; }
        else{
            if($efeed_type_list == ""){
                $efeed_type_list = $item_name[$eft2];
            }
            else{
                $efeed_type_list = $efeed_type_list.", ".$item_name[$eft2];
            }
        }
    }
    if ($efeed_type_list == "") { $efeed_type_list = "All";}
         
    if ($export_fdate == $export_tdate)
    {$filename = "Feed Production Report_".$export_tdate; }
     else {
    $filename = "Feed Production Report_".$export_fdate."_to_".$export_tdate; }
   $excel_type = $_POST['export'];
 
	//$url = "../PHPExcel/Examples/FeedFormulaReport-Excel.php?sectors=".$sectors."&fdate=".$fdate."&tdate=".$tdate;
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
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Feed Production Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_feed_production.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="14">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>Feed Mill</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $prod_code){ ?><option value="<?php echo $prod_code; ?>" <?php if($sectors == $prod_code){ echo "selected"; } ?>><?php echo $sector_name[$prod_code]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Feed Type</label>
                                    <select name="feedtype[]" id="feedtype[]" class="form-control select2" multiple>
                                        <option value="all" <?php foreach($_POST['feedtype'] as $ft1){ if($ft1 == "all"){ echo "selected"; } } ?>>-All-</option>
                                        <?php foreach($item_code as $icode){ ?><option value="<?php echo $icode; ?>" <?php foreach($_POST['feedtype'] as $ft1){ if($ft1 == $icode){ echo "selected"; } } ?>><?php echo $item_name[$icode]; ?></option><?php } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_body', 'Feed Production Summary','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
                   
                    <th colspan="14" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Feed Production Report</h5></th>
                    
                </tr>
            <?php } ?>
            
  
                <tr>
                       
                <th colspan="14">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date: <?php echo date("d.m.Y",strtotime($fdate)); ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Feed Mill: <?php echo $export_sectors; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Feed Type: <?php echo $efeed_type_list; ?></label>
                                </div>
                               
                                <div class="m-2 form-group">
                                    <label><br/></label>
            
                                </div>
                                
                        </th>
                    
                </tr>
               
            </thead>
            
            <thead class="thead3" align="center">
                <tr align="center">
                    <th>Date</th>
                    <th>Feed Mill</th>
                    <th>Feed Type</th>
                    <th>Formula</th>
                    <th>No.of Batches</th>
                    <th>Item Cosnumed</th>
                    <th>Feed Produced</th>
                    <th>Shrinkage</th>
                    <th>Shrinkage %</th>
                    <th>Bag Type</th>
                    <th>No.of Bags</th>
                    <th>Total Feed Cost</th>
                    <th>Feed Cost/ Bag</th>
                    <th>Feed Cost/ Kg</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $sql_record = "SELECT *  FROM `broiler_feed_production` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0'".$mill_filter." ORDER BY `date` ASC";
                $query = mysqli_query($conn,$sql_record); $tot_bds = $tot_qty = $tot_amt = 0;
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td title='Date'><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td title='Feed Mill'><?php echo $sector_name[$row['feed_mill']]; ?></td>
                    <td title='Feed Type'><?php echo $item_name[$row['feed_code']]; ?></td>
                    <td title='Formula'><?php echo $formula_name[$row['formula_code']]; ?></td>
                    <td title='No.of Batches' style="text-align:right;"><?php echo number_format_ind(round($row['total_tons'],2)); ?></td>
                    <td title='Item Cosnumed' style="text-align:right;"><?php 
                     if($countbagflag > 0){
                        echo number_format_ind(round($row['consumed_quantity']/$bagval,2)); 
                     }else{
                        echo number_format_ind(round($row['consumed_quantity'],2));
                     }
                    
                    
                    ?></td>
                    <td title='Feed Produced' style="text-align:right;"><?php 
                     if($countbagflag > 0){
                        echo number_format_ind(round($row['produced_quantity']/$bagval,2)); 
                     }else{
                        echo number_format_ind(round($row['produced_quantity'],2)); 
                     }
                    
                    
                    ?></td>
                    <td title='Shrinkage' style="text-align:right;"><?php echo number_format_ind(round($row['wastage_quantity'],2)); ?></td>
                    <td title='Shrinkage %' style="text-align:right;"><?php echo number_format_ind(round($row['wastage_per'],2)); ?></td>
                    <td title='Bag Type'><?php echo $item_name[$row['bag_code_feed']]; ?></td>
                    <td title='No.of Bags' style="text-align:right;"><?php echo number_format_ind(round($row['no_of_bags_feed'],2)); ?></td>
                    <td title='Total Feed Cost' style="text-align:right;"><?php echo number_format_ind(round($row['produced_amount'],2)); ?></td>
                    <td title='Feed Cost/ Bag' style="text-align:right;">
                    <?php
                    if($row['produced_amount'] > 0 && $row['no_of_bags_feed'] > 0){
                        echo number_format_ind(round(($row['produced_amount'] / $row['no_of_bags_feed']),2));
                    }
                    else{
                        echo number_format_ind(0);
                    }
                        
                    ?>
                    </td>
                    <td title='Feed Cost/ Kg' style="text-align:right;"><?php echo number_format_ind(round(($row['produced_price']),2)); ?></td>
                </tr>
                <?php
                    $tot_batches = $tot_batches + $row['total_tons'];
                    $tot_feed_consumed = $tot_feed_consumed + $row['consumed_quantity'];
                    $tot_feed_produced = $tot_feed_produced + $row['produced_quantity'];
                    $tot_feed_wastaged = $tot_feed_wastaged + $row['wastage_quantity'];
                    $tot_nof_bags = $tot_nof_bags + $row['no_of_bags_feed'];
                    $tot_feed_amount = $tot_feed_amount + $row['produced_amount'];
                }
                ?>
            </tbody>
            <tr class="thead4">
                <th colspan="4" style="text-align:center;">Total</th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_batches,2)); ?></th>
                <th style="text-align:right;"><?php
                 if($countbagflag > 0){
                    echo number_format_ind(round($tot_feed_consumed/$bagval,2)); 
                 }else{
                    echo number_format_ind(round($tot_feed_consumed,2)); 
                 }
                    
                
                ?></th>
                <th style="text-align:right;"><?php
                if($countbagflag > 0){
                    echo number_format_ind(round($tot_feed_produced/$bagval,2));
                }else{
                    echo number_format_ind(round($tot_feed_produced,2));
                }
                  
                 ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_feed_wastaged,2)); ?></th>
                <th style="text-align:right;">
                <?php
                    if($tot_feed_wastaged > 0 && $tot_feed_consumed > 0){
                        echo number_format_ind(round((($tot_feed_wastaged / $tot_feed_consumed) * 100),2));
                    }
                    else{
                        echo number_format_ind(0);
                    }
                    
                ?></th>
                <th style="text-align:right;"></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_nof_bags,2)); ?></th>
                <th style="text-align:right;"><?php echo number_format_ind(round($tot_feed_amount,2)); ?></th>
                <th style="text-align:right;">
                <?php
                    if($tot_feed_amount > 0 && $tot_nof_bags > 0){
                        echo number_format_ind(round(($tot_feed_amount / $tot_nof_bags),2));
                    }
                    else{
                        echo number_format_ind(0);
                    }
                
                ?></th>
                <th style="text-align:right;">
                <?php
                    if($tot_feed_amount > 0 && $tot_feed_produced > 0){
                        echo number_format_ind(round(($tot_feed_amount / $tot_feed_produced),2));
                    }
                    else{
                        echo number_format_ind(0);
                    }
                
                ?></th>
            </tr>
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