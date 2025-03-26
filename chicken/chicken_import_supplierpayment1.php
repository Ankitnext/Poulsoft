<?php
//chicken_import_supplierpayment1.php
include "newConfig.php";
//$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = "chicken_add_customerreceipt1.php"; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $date = date("Y-m-d"); 
    $today = date("d.m.Y");
    global $trns_dtype; $trns_dtype = "Receipt"; include "chicken_fetch_daterangemaster.php"; if($rng_mdate == ""){ $rng_mdate = $today; }

    $sql = "SELECT * FROM `main_tcds` WHERE `fdate` <= '$date' AND `tdate` >= '$date' AND `type` = 'TDS' AND `active` = '1' AND `dflag` = '0'";
    $query = mysqli_query($conn,$sql); $tcds_per = 0;
    while($row = mysqli_fetch_assoc($query)){ $tcds_per = $row['tcds']; }
    
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%S%' AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $sup_code = $sup_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sup_code[$row['code']] = $row['code']; $sup_name[$row['code']] = $row['name']; }

    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $mode_code = $mode_name = array();
    while($row = mysqli_fetch_assoc($query)){ $mode_code[$row['code']] = $row['code']; $mode_name[$row['code']] = $row['description']; }

    $sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $method_code = $method_name = array();
    while($row = mysqli_fetch_assoc($query)){ $method_code[$row['code']] = $row['code']; $method_name[$row['code']] = $row['description']; }

?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header">Import Payments</div>
                <form action="chicken_import_supplierpayment1.php" method="post" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <table align="center">
                                <thead>
                                    <tr>
                                        <td>
                                            <a href="ChickenModule-supplier_payment_import.xlsx" id="download_file" download title="download">
                                                <img src="../images/Excel-Icon_1.png" height="40px"/>Download Format&ensp;
                                            </a>
                                        </td>
                                        <th>Upload Supplier Payment-Excel</th>
                                        <td>
                                            <input type="file" name="file_uploads" id="file_uploads" class="form-control-file" />
                                        </td>
                                        <th>
                                            <button type="button" class="btn btn-success btn-sm" name="submit_import" id="submit_import" onclick="this.form.submit();">Import</button>
                                            <button type="button" name="cancel1" id="cancel1" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </form>
                <?php
                if (!empty($_FILES['file_uploads']['name'])) {
                    $file_name = $_FILES['file_uploads']['name'];
                    require_once('Classes/PHPExcel.php');
                    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $allowed_ext = ['xls', 'csv', 'xlsx'];
                
                    if (in_array($file_ext, $allowed_ext)) {
                        $file_path = $_FILES['file_uploads']['tmp_name'];
                        $read_excel = PHPExcel_IOFactory::createReaderForFile($file_path);
                        $excel_obj = $read_excel->load($file_path);

                        $excel_info = $excel_obj->getSheet('0');
                        
                        $act_rows = $excel_info->getHighestRow();
                        $act_cols = $excel_info->getHighestDataColumn();
                        $col_cno = PHPExcel_Cell::columnIndexFromString($act_cols);

                        $heading_name = array();
                        $html = ''; $row = 1; $incr = 0;
                        $html .= '<tr class="thead2">';
                
                        for($col = 0;$col < $col_cno;$col++){
                            $hname = $excel_info->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getValue();
                            if ($hname == "Date") { $heading_name[$col] = "date"; $html .= '<th>Date<b style="color:red;">&nbsp;*</b></th>'; }
                            else if ($hname == "Supplier") { $heading_name[$col] = "supplier"; $html .= '<th>Supplier<b style="color:red;">&nbsp;*</b></th>'; }
                            else if ($hname == "Mode") { $heading_name[$col] = "mode"; $html .= '<th>Mode<b style="color:red;">&nbsp;*</b></th>'; }
                            else if ($hname == "Cash/Bank") { $heading_name[$col] = "method"; $html .= '<th>Cash/Bank<b style="color:red;">&nbsp;*</b></th>'; }
                            else if ($hname == "Amount") { $heading_name[$col] = "amount"; $html .= '<th>Amount<b style="color:red;">&nbsp;*</b></th>'; }
                            else if ($hname == "Doc No.") { $heading_name[$col] = "docno"; $html .= '<th>Doc No.</th>'; }
                            else if ($hname == "Warehouse") { $heading_name[$col] = "warehouse"; $html .= '<th>Warehouse</th>'; }
                            else if ($hname == "Remarks") { $heading_name[$col] = "remarks"; $html .= '<th>Remarks</th>'; }
                        }
                        $html .= '</tr>';
                
                        $cincr = 0;
                        for ($row = 2; $row <= $act_rows; $row++) {
                            $html .= '<tr>';
                            for($col = 0;$col < $col_cno;$col++){
                                $hvalue = $excel_info->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getValue();

                                if ($heading_name[$col] == "date") {
                                    $html .= '<td><input type="text" name="date[]" id="date[' . $cincr . ']" class="form-control rct_datepickers" value="' . date("d.m.Y", strtotime(str_replace(".", "-", $hvalue))) . '" /></td>';
                                }
                                else if ($heading_name[$col] == "supplier") {
                                    $html .= '<td><select name="ccode[]" id="ccode[' . $cincr . ']" class="form-control select2">';
                                    $html .= '<option value="select">-select-</option>';
                                    foreach ($sup_code as $code => $name) {
                                        if(strtolower($hvalue) == strtolower($sup_name[$code])){ $html .= '<option value="'.$code.'" selected>'.$sup_name[$code].'</option>'; }
                                        else{ $html .= '<option value="'.$code.'">'.$sup_name[$code].'</option>'; }
                                    }
                                    $html .= '</select></td>';
                                } 
                                 else if ($heading_name[$col] == "mode") {
                                    $html .= '<td><select name="mode[]" id="mode['.$cincr.']" class="form-control select2">';
                                    $html .= '<option value="select">-select-</option>';
                                    foreach ($mode_code as $code => $desc) {
                                        if(strtolower($hvalue) == strtolower($mode_name[$code])){ $html .= '<option value="'.$code.'" selected>'.$mode_name[$code].'</option>'; }
                                        else{ $html .= '<option value="'.$code.'">'.$mode_name[$code].'</option>'; }
                                    }
                                    $html .= '</select></td>';
                                }
                                else if ($heading_name[$col] == "method") {
                                    $html .= '<td><select name="method[]" id="method['.$cincr.']" class="form-control select2">';
                                    $html .= '<option value="select">-select-</option>';
                                    foreach ($method_code as $code => $desc) {
                                        if(strtolower($hvalue) == strtolower($method_name[$code])){ $html .= '<option value="'.$code.'" selected>'.$method_name[$code].'</option>'; }
                                        else{ $html .= '<option value="'.$code.'">'.$method_name[$code].'</option>'; }
                                    }
                                    $html .= '</select></td>';
                                }
                                else if ($heading_name[$col] == "amount") {
                                    $html .= '<td><input type="text" name="amount[]" id="amount[' . $cincr . ']" class="form-control text-right" value="' . floatval($hvalue) . '" /></td>';
                                }
                                else if ($heading_name[$col] == "docno") {
                                    $html .= '<td><input type="text" name="docno[]" id="docno[' . $cincr . ']" class="form-control" value="' . $hvalue . '" /></td>';
                                }
                                else if ($heading_name[$col] == "sector") {
                                    $html .= '<td><select name="sector[]" id="sector['.$cincr.']" class="form-control select2">';
                                    $html .= '<option value="select">-select-</option>';
                                    foreach ($sector_code as $code => $desc) {
                                        if(strtolower($hvalue) == strtolower($sector_name[$code])){ $html .= '<option value="'.$code.'" selected>'.$sector_name[$code].'</option>'; }
                                        else{ $html .= '<option value="'.$code.'">'.$sector_name[$code].'</option>'; }
                                    }
                                    $html .= '</select></td>';
                                }
                                else if ($heading_name[$col] == "remarks") {
                                    $html .= '<td><input type="text" name="remarks[]" id="remarks[' . $cincr . ']" class="form-control" value="' . $hvalue . '" /></td>';
                                }
                            }
                            $html .= '</tr>';
                            $cincr++;
                        }
                        if($cincr > 0){ $cincr--; }
                ?>
                <form action="chicken_import_save_supplierpayment1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table>
                                <?php echo $html; ?>
                            </table>
                        </div><br/>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $cincr; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" name="ebtncount" id="ebtncount" class="form-control" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
                                <button type="button" name="cancel2" id="cancel2" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
                <?php } } ?>
            </div>
            <script>
                function return_back(){
                    window.location.href = "chicken_display_supplierpayment1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;

                    var date = ccode = mode = code = sector = ""; var c = amount1 = amount = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            ccode = document.getElementById("ccode["+d+"]").value;
                            mode = document.getElementById("mode["+d+"]").value;
                            method = document.getElementById("method["+d+"]").value;
                            sector = document.getElementById("sector["+d+"]").value;
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }

                            if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                            }
                            else if(ccode == "select"){
                                alert("Please select Customer in row: "+c);
                                document.getElementById("ccode["+d+"]").focus();
                                l = false;
                            }
                            else if(mode == "select"){
                                alert("Please select Mode in row: "+c);
                                document.getElementById("mode["+d+"]").focus();
                                l = false;
                            }
                            else if(method == "select"){
                                alert("Please select Cash/Bank in row: "+c);
                                document.getElementById("method["+d+"]").focus();
                                l = false;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Amount in row: "+c);
                                document.getElementById("amount["+d+"]").focus();
                                l = false;
                            }
                            else{ }
                        }
                    }
                    if(l == true){
                        return true;
                    }
                    else{
                        document.getElementById("submit").style.visibility = "visible";
                        document.getElementById("ebtncount").value = "0";
                        return false;
                    }
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
