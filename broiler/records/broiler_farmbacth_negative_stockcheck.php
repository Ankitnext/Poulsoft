<?php
//broiler_farmbacth_negative_stockcheck.php
include "../newConfig.php";

$sql = "SELECT * FROM `main_companyprofile` WHERE `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;
global $page_title; $page_title = "Farm-Batch Negative Stock Check Report";
include "header_head.php";

$database_name = $_SESSION['dbase']; $dbs_flag = 0;
if($database_name == "poulso6_admin_broiler_broilermaster"){
    $hostname = "213.165.245.128"; $db_users = "poulso6_userlist123"; $db_pass = "XBiypkFG2TF!9UB";
    $aconn = new mysqli($hostname, $db_users, $db_pass);
    $sql = "SHOW DATABASES"; $query = mysqli_query($aconn,$sql); $active_databases = array();
    while($row = mysqli_fetch_assoc($query)){ $active_databases[$row["Database"]] = $row["Database"]; }
    $db_list = implode("','",$active_databases);
    
    $sql = "SELECT DISTINCT(dblist) as dblist FROM `log_useraccess` WHERE `flag` = '1' AND `account_access` IN ('BTS') AND `dblist` IN ('$db_list')"; $query = mysqli_query($conns,$sql);
    while($row = mysqli_fetch_assoc($query)){ $db_name[$row['dblist']] = $row['dblist']; }
}
else{
    $db_name[$database_name] = $database_name; $dbs_flag = 1;
}

$database = $correct_type = "farm_negative_stock_check"; $farm_type = "closed_farm";
if(isset($_POST['submit_report']) == true){
$database = $_POST['database'];
$correct_type = $_POST['correct_type'];
$farm_type = $_POST['farm_type'];
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <script>
            var exptype = '<?php echo $excel_type; ?>';
            var url = '<?php echo $url; ?>';
            if(exptype.match("excel")){ window.open(url,"_BLANK"); }
        </script>
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
        <table class="tbl" align="center" id="main_table">
            <?php
            $sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'purchases Invoice' OR `type` = 'All' ORDER BY `id` DESC"; $query = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($query)){
            ?>
            <thead class="thead1" align="center" style="width:1212px;">
                <tr align="center">
                    <td colspan="2" align="center"><img src="<?php echo "../".$row['logopath']; ?>" height="110px"/></td>
                    <th colspan="12" align="center"><?php echo $row['cdetails']; ?><h5>Farm-Batch Negative Stock Check Report</h5></th>
                </tr>
            </thead>
            <?php } ?>
            <form action="broiler_farmbacth_negative_stockcheck.php" method="post" onsubmit="return checkval();">
                <thead class="thead2 text-primary layout-navbar-fixed" style="width:1212px;">
                    <tr>
                        <th colspan="14">
                            <div class="row">
                                <div class="m-2 form-group" <?php if($dbs_flag == 1){ echo "style='visibility:hidden;'"; } ?>>
                                    <label>Databases</label>
                                    <select name="database" id="database" class="form-control select2">
                                    <?php if($dbs_flag == 0){ ?><option value="select" <?php if($database == "select"){ echo "selected"; } ?>>-select-</option><?php } ?>
                                        <?php foreach($db_name as $fcode){ if($fcode != ""){ ?>
                                        <option value="<?php echo $fcode; ?>" <?php if($database == $fcode){ echo "selected"; } ?>><?php echo $fcode; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Correction/Fetch Type</label>
                                    <select name="correct_type" id="correct_type" class="form-control select2" style="width:280px;">
                                        <option value="select" <?php if($correct_type == "select"){ echo "selected"; } ?>>-select-</option>
                                        <option value="farm_negative_stock_check" <?php if($correct_type == "farm_negative_stock_check"){ echo "selected"; } ?>>-Farm Negative Stock Check-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group">
                                    <label>Farm Type</label>
                                    <select name="farm_type" id="farm_type" class="form-control select2" style="width:160px;">
                                        <option value="live_farm" <?php if($farm_type == "live_farm"){ echo "selected"; } ?>>-Live Farm-</option>
                                        <option value="closed_farm" <?php if($farm_type == "closed_farm"){ echo "selected"; } ?>>-Closed Farm-</option>
                                    </select>
                                </div>
                                <div class="m-2 form-group" style="width: 210px;">
                                    <label for="search_table">Search</label>
                                    <input type="text" name="search_table" id="search_table" class="form-control" style="padding:0;padding-left:2px;width:200px;" />
                                </div>
                                <div class="m-2 form-group">
                                    <br/>
                                    <button type="submit" name="submit_report" id="submit_report" class="btn btn-sm btn-success">Fetch</button>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
            </form>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <tr>
                    <th>Sl.No.</th>
                    <th>Database</th>
                    <th>Farm</th>
                    <th>Batch</th>
                    <th>Category</th>
                    <th>Item</th>
                    <th>Summary Stock</th>
                    <th>Base Stock</th>
                </tr>
                <?php
                if($database != "select"){
                    $tconn = mysqli_connect("213.165.245.128","poulso6_userlist123","XBiypkFG2TF!9UB",$database);

                    if($correct_type == "farm_negative_stock_check"){
                        if($farm_type == "live_farm"){
                            $gcflag_filter = " AND `gc_flag` = '0'";
                        }
                        else if($farm_type == "closed_farm"){
                            $gcflag_filter = " AND `gc_flag` = '1'";
                        }
                        else{ $gcflag_filter = ""; }

                        $sql = "SELECT * FROM `broiler_batch` WHERE `active` = '1'".$gcflag_filter." AND `dflag` = '0' ORDER BY `batch_no`,`code` ASC";
                        $query = mysqli_query($tconn,$sql); $batch_code = $batch_name = $batch_farm = $batch_alist = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $batch_code[$row['code']] = $row['code'];
                            $batch_name[$row['code']] = $row['description'];
                            $batch_farm[$row['code']] = $row['farm_code'];
                            if(empty($batch_alist[$row['farm_code']]) || $batch_alist[$row['farm_code']] == ""){ $batch_alist[$row['farm_code']] = $row['code']; }
                            else{ $batch_alist[$row['farm_code']] = $batch_alist[$row['farm_code']]."@".$row['code']; }
                        }
                        
                        $sql = "SELECT * FROM `item_category` WHERE `dflag` = '0' AND (`description` LIKE '%Feed%' || `description` LIKE '%medicine%' || `description` LIKE '%vaccine%') ORDER BY `description` ASC";
                        $query = mysqli_query($tconn,$sql); $icat_arr_code = $icat_arr_iac = $icat_name = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $icat_arr_code[$row['code']] = $row['code'];
                            $icat_name[$row['code']] = $row['description'];
                            $icat_arr_iac[$row['code']] = $row['iac'];
                        }
                        
                        $icat_list = implode("','",$icat_arr_code);
                        $sql = "SELECT * FROM `item_details` WHERE `category` IN ('$icat_list') AND `dflag` = '0' ORDER BY `description` ASC";
                        $query = mysqli_query($tconn,$sql); $item_code =  $item_name = $item_category = array();
                        while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

                        $farm_list = implode("','",$batch_farm);
                        $sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' AND `code` IN ('$farm_list') ORDER BY `description` ASC";
                        $query = mysqli_query($tconn,$sql); $farm_code =  $farm_name = array();
                        while($row = mysqli_fetch_assoc($query)){ $farm_code[$row['code']] = $row['code']; $farm_name[$row['code']] = $row['description']; }

                        //Summary Calculations
                        $coa_list = implode("','",$icat_arr_iac); $item_list = implode("','",$item_code); $batch_list = implode("','",$batch_code);
                        $sql = "SELECT location,batch,item_code,crdr,SUM(quantity) as quantity FROM `account_summary` WHERE `crdr` IN ('CR','DR') AND `coa_code`IN ('$coa_list') AND `item_code` IN ('$item_list') AND `batch` IN ('$batch_list') AND `active` = '1' AND `dflag` = '0' GROUP BY `location`,`batch`,`item_code`,`crdr` ORDER BY `location`,`batch`,`item_code`,`crdr` ASC";
                        $query = mysqli_query($tconn,$sql); $stk_cr_qty = $stk_dr_qty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['location']."@".$row['batch']."@".$row['item_code'];
                            if($row['crdr'] == "CR"){
                                $stk_cr_qty[$key] += (float)$row['quantity'];
                            }
                            else if($row['crdr'] == "DR"){
                                $stk_dr_qty[$key] += (float)$row['quantity'];
                            }
                            else{ }
                        }

                        //Purchases
                        $sql = "SELECT warehouse,farm_batch,icode,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty FROM `broiler_purchases` WHERE `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` =' 0' GROUP BY `farm_batch`,`icode` ORDER BY `farm_batch`,`icode` ASC";
                        $query = mysqli_query($tconn,$sql); $pur_stk_qty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['warehouse']."@".$row['farm_batch']."@".$row['icode'];
                            $pur_stk_qty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        }
                        //Stock-In
                        $sql = "SELECT towarehouse,to_batch,code,SUM(quantity) as quantity FROM `item_stocktransfers` WHERE `code` IN ('$item_list') AND `to_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` =' 0' GROUP BY `to_batch`,`code` ORDER BY `to_batch`,`code` ASC";
                        $query = mysqli_query($tconn,$sql); $tin_stk_qty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['towarehouse']."@".$row['to_batch']."@".$row['code'];
                            $tin_stk_qty[$key] += (float)$row['quantity'];
                        }
                        //Daily Entry
                        $sql = "SELECT * FROM `broiler_daily_record` WHERE `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` =' 0' ORDER BY `batch_code` ASC";
                        $query = mysqli_query($tconn,$sql); $dentry_stk_qty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['farm_code']."@".$row['batch_code']."@".$row['item_code1']; $dentry_stk_qty[$key] += (float)$row['kgs1'];
                            $key = $row['farm_code']."@".$row['batch_code']."@".$row['item_code2']; $dentry_stk_qty[$key] += (float)$row['kgs2'];
                        }
                        //MedVac Entry
                        $sql = "SELECT * FROM `broiler_medicine_record` WHERE `batch_code` IN ('$batch_list') AND `active` = '1' AND `dflag` =' 0' ORDER BY `batch_code` ASC";
                        $query = mysqli_query($tconn,$sql); $medvac_stk_qty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['farm_code']."@".$row['batch_code']."@".$row['item_code'];
                            $medvac_stk_qty[$key] += (float)$row['quantity'];
                        }
                        //Sale
                        $sql = "SELECT warehouse,farm_batch,icode,SUM(rcd_qty) as rcd_qty,SUM(fre_qty) as fre_qty FROM `broiler_sales` WHERE `icode` IN ('$item_list') AND `farm_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` =' 0' GROUP BY `farm_batch`,`icode` ORDER BY `farm_batch`,`icode` ASC";
                        $query = mysqli_query($tconn,$sql); $sale_stk_qty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['warehouse']."@".$row['farm_batch']."@".$row['icode'];
                            $sale_stk_qty[$key] += ((float)$row['rcd_qty'] + (float)$row['fre_qty']);
                        }
                        //Stock-Out
                        $sql = "SELECT fromwarehouse,from_batch,code,SUM(quantity) as quantity FROM `item_stocktransfers` WHERE `code` IN ('$item_list') AND `from_batch` IN ('$batch_list') AND `active` = '1' AND `dflag` =' 0' GROUP BY `from_batch`,`code` ORDER BY `from_batch`,`code` ASC";
                        $query = mysqli_query($tconn,$sql); $tout_stk_qty = array();
                        while($row = mysqli_fetch_assoc($query)){
                            $key = $row['fromwarehouse']."@".$row['from_batch']."@".$row['code'];
                            $tout_stk_qty[$key] += (float)$row['quantity'];
                        }

                        $batch_item_smry_stock = $batch_item_base_stock = array();
                        foreach($batch_code as $bcode){
                            $fcode = $batch_farm[$bcode];
                            foreach($item_code as $icode){
                                $key = $fcode."@".$bcode."@".$icode;
                                if(empty($stk_cr_qty[$key]) || $stk_cr_qty[$key] == ""){ $stk_cr_qty[$key] = 0; }
                                if(empty($stk_dr_qty[$key]) || $stk_dr_qty[$key] == ""){ $stk_dr_qty[$key] = 0; }
                                $batch_item_smry_stock[$key] = (float)$stk_dr_qty[$key] - (float)$stk_cr_qty[$key];

                                if(empty($pur_stk_qty[$key]) || $pur_stk_qty[$key] == ""){ $pur_stk_qty[$key] = 0; }
                                if(empty($tin_stk_qty[$key]) || $tin_stk_qty[$key] == ""){ $tin_stk_qty[$key] = 0; }
                                if(empty($dentry_stk_qty[$key]) || $dentry_stk_qty[$key] == ""){ $dentry_stk_qty[$key] = 0; }
                                if(empty($medvac_stk_qty[$key]) || $medvac_stk_qty[$key] == ""){ $medvac_stk_qty[$key] = 0; }
                                if(empty($sale_stk_qty[$key]) || $sale_stk_qty[$key] == ""){ $sale_stk_qty[$key] = 0; }
                                if(empty($tout_stk_qty[$key]) || $tout_stk_qty[$key] == ""){ $tout_stk_qty[$key] = 0; }
                                $batch_item_base_stock[$key] = (((float)$pur_stk_qty[$key] + (float)$tin_stk_qty[$key]) - ((float)$dentry_stk_qty[$key] + (float)$medvac_stk_qty[$key] + (float)$sale_stk_qty[$key] + (float)$tout_stk_qty[$key]));
                            }
                        }

                        $html = ''; $c = 0;
                        foreach($farm_code as $fcode){
                            $blist = explode("@",$batch_alist[$fcode]);
                            foreach($blist as $bcode){
                                foreach($item_code as $icode){
                                    $key = $fcode."@".$bcode."@".$icode;
                                    if(empty($batch_item_smry_stock[$key]) || $batch_item_smry_stock[$key] == ""){ $batch_item_smry_stock[$key] = 0; }
                                    if(empty($batch_item_base_stock[$key]) || $batch_item_base_stock[$key] == ""){ $batch_item_base_stock[$key] = 0; }
                                    if((float)$batch_item_smry_stock[$key] < 0 || (float)$batch_item_base_stock[$key] < 0){
                                        $c++;
                                        $html .= '<tr>';
                                        $html .= '<td>'.$c.'</td>';
                                        $html .= '<td>'.$database.'</td>';
                                        $html .= '<td>'.$farm_name[$fcode].' ('.$fcode.')</td>';
                                        $html .= '<td>'.$batch_name[$bcode].' ('.$bcode.')</td>';
                                        $html .= '<td>'.$icat_name[$item_category[$icode]].' ('.$item_category[$icode].')</td>';
                                        $html .= '<td>'.$item_name[$icode].' ('.$icode.')</td>';
                                        if(number_format_ind($batch_item_smry_stock[$key]) == number_format_ind($batch_item_base_stock[$key])){
                                            $html .= '<td style="text-align:right;color:green;">'.number_format_ind($batch_item_smry_stock[$key]).'</td>';
                                            $html .= '<td style="text-align:right;color:green;">'.number_format_ind($batch_item_base_stock[$key]).'</td>';
                                        }
                                        else{
                                            $html .= '<td style="text-align:right;color:red;">'.number_format_ind($batch_item_smry_stock[$key]).'</td>';
                                            $html .= '<td style="text-align:right;color:red;">'.number_format_ind($batch_item_base_stock[$key]).'</td>';
                                        }
                                        $html .= '</tr>';
                                    }
                                }
                            }
                        }
                        echo $html;
                    }
                }
                ?>
            </tbody>
            <?php
            }
            ?>
        </table><br/><br/><br/>
        
        <script>
            function checkval(){
                var database = document.getElementById("database").value;
                if(database.match("select")){
                    alert("Kindly select Database");
                    document.getElementById("database").focus();
                    return false;
                }
                else{
                    return true;
                }
            }
			function removeAllOptions(selectbox){ var i; for(i=selectbox.options.length-1;i>=0;i--){ selectbox.remove(i); } }
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('search_table');
            const table = document.getElementById('main_table');
            const tableBody = table.querySelector('tbody');

            searchInput.addEventListener('input', () => {
                const filter = searchInput.value.toLowerCase();
                const rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    let found = false;

                    cells.forEach(cell => {
                        if (cell.textContent.toLowerCase().includes(filter)) {
                            found = true;
                        }
                    });

                    row.style.display = found ? '' : 'none';
                });
            });
        });

        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>