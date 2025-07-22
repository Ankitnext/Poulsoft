<?php
//broiler_inventoryadjustment.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Inventory Adjustment Report";
include "header_head.php";

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_farm` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `broiler_vehicle`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vehicle_code[$row['code']] = $row['code']; $vehicle_name[$row['code']] = $row['registration_number']; }

$sql = "SELECT * FROM `item_category` ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $bcodes = "";
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

$fdate = $tdate = date("Y-m-d"); $item_cat = $items =  "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    $item_cat = $_POST['item_cat'];
    $items = $_POST['items'];

    
    if($items != "all"){ $item_filter = " AND `item_code` IN ('$items')"; }
    else if($item_cat == "all"){ $item_filter = ""; }
    else{
        $icat_list = $item_filter = "";
        foreach($item_code as $icode){
            $item_category[$icode];
            if($item_category[$icode] == $item_cat){
                if($icat_list == ""){
                    $icat_list = $icode;
                }
                else{
                    $icat_list = $icat_list."','".$icode;
                }
            }
        }
        $item_filter = " AND `item_code` IN ('$icat_list')";
    }
    $export_fdate = $_POST['fdate'];
    $export_tdate = $_POST['tdate'];
    $export_item_cat =$icat_name[$_POST['item_cat']]; if ($export_item_cat == "") { $export_item_cat = "All"; }
    $export_items = $item_name[$_POST['items']]; if ($export_items == "") { $export_items = "All"; } 
     
     if ($export_fdate == $export_tdate)
     {$filename = "Inventory Adjustment Report_".$export_tdate; }
      else {
     $filename = "Inventory Adjustment Report_".$export_fdate."_to_".$export_tdate; }
    $excel_type = $_POST['export'];
	//$url = "../PHPExcel/Examples/InventoryAdjustment-Excel.php?fromdate=".$fdate."&todate=".$tdate."&items=".$items;
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
       
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <style>
        .thead3 th {
                top: 0;
                position: sticky;
                background-color: #9cc2d5;
			}
        </style>
       
        <?php
          if($excel_type == "print"){
            echo '<style>body { padding:10px;text-align:center; }
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
        <table class="tbl" align="center">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Sales Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="7" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Inventory Adjustment Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_inventoryadjustment.php" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="9">
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
                                    <label>Category</label>
                                    <select name="item_cat" id="item_cat" class="form-control select2" onchange="fetch_item_list();">
                                        <option value="all" <?php if($item_cat == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($icat_code as $icats){ if($icat_name[$icats] != ""){ ?>
                                        <option value="<?php echo $icats; ?>" <?php if($item_cat == $icats){ echo "selected"; } ?>><?php echo $icat_name[$icats]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Items</label>
                                    <select name="items" id="items" class="form-control select2">
                                        <option value="all" <?php if($items == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php if($item_cat == "all"){ ?>
                                        <?php foreach($item_code as $icodes){ if($item_name[$icodes] != ""){ ?>
                                        <option value="<?php echo $icodes; ?>" <?php if($items == $icodes){ echo "selected"; } ?>><?php echo $item_name[$icodes]; ?></option>
                                        <?php } } }
                                        else{
                                            foreach($item_code as $icodes){
                                                if($item_cat == $item_category[$icodes]){
                                                ?>
                                                <option value="<?php echo $icodes; ?>" <?php if($items == $icodes){ echo "selected"; } ?>><?php echo $item_name[$icodes]; ?></option>
                                                <?php
                                                }
                                            }
                                        }
                                            ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2"  onchange="tableToExcel('main_body', 'Inventory Adjustment','<?php echo $filename;?>', this.options[this.selectedIndex].value)">
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
                   
                    <th colspan="8" align="center" style="border-right:none;"><?php echo $row['cdetails']; ?><h5>Inventory Adjustment Report</h5></th>
                    
                </tr>
            <?php } ?>
            
  
                <tr>
                       
                <th colspan="8">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date: <?php echo date("d.m.Y",strtotime($fdate)); ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date: <?php echo date("d.m.Y",strtotime($tdate)); ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Category: <?php echo $export_item_cat; ?></label>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Items: <?php echo $export_items; ?></label>
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
                    <th>Transaction No.</th>
                    <th>Type</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
                    <th>Warehouse/Location</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $total_qty = $total_amt = 0;
                $sql = "SELECT * FROM `broiler_inv_adjustment` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$item_filter." AND `active` = '1' AND `dflag` = '0' ORDER BY `date`,`trnum` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td><?php echo $row['trnum']; ?></td>
                    <td><?php echo $row['a_type']; ?></td>
                    <td><?php echo $item_name[$row['item_code']]; ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['quantity'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['rate'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['amount'],2)); ?></td>
                    <td><?php echo $sector_name[$row['sector']]; ?></td>
                    <td><?php echo $row['remarks']; ?></td>
                </tr>
                <?php
                    $total_qty = $total_qty + $row['quantity'];
                    $total_amt = $total_amt + $row['amount'];
                }
                ?>
                <tr class="thead4">
                    <th colspan="4" style="text-align:center;">Total</th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_qty,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round(($total_amt / $total_qty),2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_amt,2)); ?></th>
                    <th style="text-align:right;"></th>
                    <th style="text-align:right;"></th>
                </tr>
            </tbody>
        <?php
            }
        ?>
        </table><br/><br/><br/>
        <script>
            function fetch_item_list(){
                var fcode = document.getElementById("item_cat").value;
                removeAllOptions(document.getElementById("items"));
                myselect = document.getElementById("items"); theOption1=document.createElement("OPTION"); theText1=document.createTextNode("-All-"); theOption1.value = "all"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);
                if(fcode != "all"){
                <?php
                    foreach($item_code as $icodes){
                        $icats = $item_category[$icodes];
                        echo "if(fcode == '$icats'){";
                ?> 
                    theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icodes]; ?>"); theOption1.value = "<?php echo $icodes; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                <?php
                        echo "}";
                    }
                ?>
                }
                else{
                    <?php
                        foreach($item_code as $icodes){
                            $icats = $item_category[$icodes];
                    ?> 
                        theOption1=document.createElement("OPTION"); theText1=document.createTextNode("<?php echo $item_name[$icodes]; ?>"); theOption1.value = "<?php echo $icodes; ?>"; theOption1.appendChild(theText1); myselect.appendChild(theOption1);	
                    <?php
                        }
                    ?>
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
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
    </body>
</html>
<?php
include "header_foot.php";
?>