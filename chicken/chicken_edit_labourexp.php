<?php
//chicken_edit_labourexp.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
  
    //Labour Details
    $sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `driver_flag` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $acc_code = $acc_name = array();
    while($row = mysqli_fetch_assoc($query)){ $acc_code[$row['code']] = $row['code']; $acc_name[$row['code']] = $row['description']; }

    //Fetch Column From CoA Table
    $sql='SHOW COLUMNS FROM `acc_coa`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
    while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
    if(in_array("mobile_no", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `mobile_no` VARCHAR(300) NULL DEFAULT NULL AFTER `flag`"; mysqli_query($conn,$sql); }
    if(in_array("transport_flag", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `acc_coa` ADD `transport_flag` INT(100) NOT NULL DEFAULT '0' AFTER `mobile_no`"; mysqli_query($conn,$sql); }

    //check and fetch date range
    global $drng_cday; $drng_cday = 0; global $drng_furl; $drng_furl = str_replace("_add_","_display_",basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    include "poulsoft_fetch_daterange_master.php";

    $colspan = 8;
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
            <style>
                body{
                    overflow: auto;
                }
                /*table,tr,th,td {
                    border: 1px solid black;
                    border-collapse: collapse;
                }*/
                label{
                    font-weight:bold;
                }
            </style>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        </head>
        <body>
            <?php
            $ids = $_GET['trnum'];
            if($ids != ""){
                $sql = "SELECT * FROM `chicken_labveh_expenses` WHERE `code` = '$ids' AND `dflag` = '0' ORDER BY `date`,`code` ASC";
                $query = mysqli_query($conn,$sql); $tcdsamt = array(); $c = 0;
                while($row = mysqli_fetch_assoc($query)){
                   $fdate = date("d.m.Y",strtotime($row['date']));
                    $date = $row['date'];
                    $trnum = $row['code'];
                    $sold_kgs = $row['sold_kgs'];
                    $pur_kgs = $row['pur_kgs'];
                    // $bonus = $row['bonus'];
                    // $warehouse = $row['warehouse'];
                    $no_labours = $row['no_labours'];
                    $labour_code[$c] = $row['labour_code'];
                    $supervisor_value[$c] = $row['supervisor_value'];
                    $sold_weight[$c] = round($row['sold_weight'],2);
                    $rate[$c] = round($row['rate'],2);
                    $amount[$c] = round($row['amount'],2);
                    $bonus[$c] = round($row['bonus'],2);
                    $remarks = $row['remarks'];
                    $c++;
                } $c = $c - 1;
            }
            $sql = "SELECT DISTINCT `warehouse` FROM `chicken_labveh_expenses` WHERE `code` = '$ids' AND `dflag` = '0' LIMIT 1";
                $result = mysqli_query($conn, $sql);
                $warehouse = '';
                if ($row = mysqli_fetch_assoc($result)) {
                    $warehouse = $row['warehouse'];
            }

            $sql = "SELECT * FROM `customer_sales` WHERE `date` = '$date' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `warehouse` ASC;";
            $query = mysqli_query($conn,$sql); $sector_alist = array();
            while ($row = mysqli_fetch_assoc($query)) { $sector_alist[$row['warehouse']] = $row['warehouse']; }
            $sector_list = implode("','",$sector_alist);

             //Sector Details
            $sql = "SELECT * FROM `inv_sectors` WHERE `code` IN ('$sector_list') AND `active` = '1' ORDER BY `description` ASC";
            $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
            while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Labour Expense</div>
                <form action="chicken_modify_labourexp.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                        <div class="row">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date</label>
                                <input type="text" name="date" id="date" class="form-control range_picker" value="<?php echo $fdate; ?>" style="width:100px;" onchange="fetch_act_veh();" readonly />
                            </div>
                             <div class="form-group" style="width:210px;">
                                <label for="warehouse">Vehicle<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:200px;" onchange="fetch_dvwise_saleqtys()"><option value="select">-select-</option><?php foreach($sector_code as $acode){ ?><option value="<?php echo $acode; ?>" <?php if($acode == $warehouse){ echo "selected";} ?>><?php echo $sector_name[$acode]; ?></option><?php } ?></select>
                            </div>
                            <div class="form-group" style="width:100px;">
                                <label for="sold_kgs">Sold Kgs</label>
                                <input type="text" name="sold_kgs" id="sold_kgs" class="form-control" value="<?php echo $sold_kgs; ?>" style="width:90px;" readonly/>
                            </div>
                            <div class="form-group" style="width:150px;">
                                <label for="no_labours">No. Of Labours</label>
                                <input type="text" name="no_labours" id="no_labours" class="form-control" value="<?php echo $no_labours; ?>" style="width:140px;" onkeyup="validate_count(this.id);generate_labrows();" />
                            </div>
                             <div class="form-group" style="width:100px;">
                                <label for="pur_kgs">Purchase Kg</label>
                                <input type="text" name="pur_kgs" id="pur_kgs" class="form-control" value="<?php echo $pur_kgs; ?>" style="width:90px;" readonly/>
                            </div>
                        </div>
                        <div class="row">
                            <table>
                                <thead>
                                    <tr>
                                        <th colspan="<?php echo $colspan; ?>" style="background-color:#d1ffe4;color:#00722f;text-align:center;">Labour Expense Details</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align:center;"><label>Labour Name<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label></label></th>
                                        <th style="text-align:center;"><label>Supervisor<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>SOLD KGS<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>COST PER KG<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Amount<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"><label>Bonus<b style="color:red;">&nbsp;*</b></label></th>
                                        <th style="text-align:center;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php  ?>
                                <?php $incr = $c; for($c = 0;$c <= $incr;$c++){  ?>
                                    <tr id="">
                                        <td ><select name="labour_code[]" id="labour_code[<?php echo $c; ?>]" class="form-control select2" data-row="<?php echo $c; ?>" data-col="0" style="width: 250px;"><option value="select">-select-</option><?php foreach($acc_code as $cc){ ?><option value="<?php echo $acc_code[$cc]; ?>" <?php if($cc == $labour_code[$c]){ echo "selected";} ?>><?php echo $acc_name[$cc]; ?></option><?php } ?></select></td>
                                        <?php $colIndex = 1; ?>
                                        <td ><input type="checkbox" name="supr_chk[]" id="supr_chk[<?php echo $c; ?>]" class="form-control" value="" style="width:10px;" onchange="update_suprprc(this.id);" <?php if (!empty($supervisor_value[$c])) echo 'checked'; ?>></td>
                                        <td ><input type="text" name="supr_amt[]" id="supr_amt[<?php echo $c; ?>]" class="form-control" value="<?php echo $supervisor_value[$c]; ?>" style="width:90px;" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" readonly/></td>
                                        <td ><input type="text" name="sold_weight[]" id="sold_weight[<?php echo $c; ?>]" class="form-control" value="<?php echo $sold_weight[$c]; ?>" style="width:90px;" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" readonly/></td>
                                        <td ><input type="text" name="rate[]" id="rate[<?php echo $c; ?>]" class="form-control" value="<?php echo $rate[$c]; ?>" style="width:140px;" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" onkeyup="validatenum(this.id);cal_tot_amt(this.id)"></td>
                                        <td ><input type="text" name="amount[]" id="amount[<?php echo $c; ?>]" class="form-control" value="<?php echo $amount[$c]; ?>" style="width:60px;" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" onkeyup="validatenum(this.id);cal_avg_prc(this.id)"></td>
                                        <td ><input type="text" name="bonus[]" id="bonus[<?php echo $c; ?>]" class="form-control" value="<?php echo $bonus[$c]; ?>" style="width:60px;" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" readonly></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $trnum; ?>" style="width:20px;" readonly /></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div><br/>
                       <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $incr; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>ID</label>
                                <input type="text" name="idvalue" id="idvalue" class="form-control" value="<?php echo $ids; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Update</button>&ensp;
                                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                 //Date Range selection
                var s_date = '<?php echo $rng_sdate; ?>'; var e_date = '<?php echo $rng_edate; ?>';
                function return_back(){
                    window.location.href = "chicken_display_labourexp.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                    var date = document.getElementById("date").value;
                    var warehouse = document.getElementById("warehouse").value;
                    var sold_kgs = document.getElementById("sold_kgs").value; if(sold_kgs == ""){ sold_kgs = 0; }
                    var no_labours = document.getElementById("no_labours").value; if(no_labours == ""){ no_labours = 0; }

                    if(date == ""){
                        alert("Please select Date");
                        document.getElementById("date").focus();
                        l = false;
                    }
                    else if(warehouse == "" || warehouse == "select"){
                        alert("Please select vehicle");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(parseFloat(sold_kgs) == 0){
                        alert("Please select appropriate vehicle to fetch Sold Kgs");
                        document.getElementById("warehouse").focus();
                        l = false;
                    }
                    else if(parseFloat(no_labours) == 0){
                        alert("Please enter No. of Labours");
                        document.getElementById("no_labours").focus();
                        l = false;
                    }
                    else{
                        var labour_code = ""; var c = sold_weight = rate = amount = 0;
                        var incr = document.getElementById("incr").value;
                        for(var d = 0;d <= incr;d++){
                            if(l == true){
                                c = d + 1;
                                labour_code = document.getElementById("labour_code["+d+"]").value;
                                sold_weight = document.getElementById("sold_weight["+d+"]").value; if(sold_weight == ""){ sold_weight = 0; }
                                rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                                amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }

                                if(labour_code == "" || labour_code == "select"){
                                    alert("Please select Labour Name in row: "+c);
                                    document.getElementById("labour_code["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(sold_weight) == 0){
                                    alert("Please enter SOLD KGS in row: "+c);
                                    document.getElementById("sold_weight["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(rate) == 0){
                                    alert("Please enter COST PER KG in row: "+c);
                                    document.getElementById("rate["+d+"]").focus();
                                    l = false;
                                }
                                else if(parseFloat(amount) == 0){
                                    alert("Please enter SOLD KGS (or) COST PER KG in row: "+c);
                                    document.getElementById("amount["+d+"]").focus();
                                    l = false;
                                }
                                else{ }
                            }
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
                function fetch_act_veh(){
                    var date = document.getElementById('date').value;
                    document.getElementById("warehouse").innerHTML = "";
                    if(date != ""){
                        var fch_veh = new XMLHttpRequest();
                        var method = "GET";
                        var url = "chicken_fetch_sale_asectors.php?date="+date;
                        var asynchronous = true;
                        fch_veh.open(method, url, asynchronous);
                        fch_veh.send();
                        fch_veh.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var sec_list = this.responseText;
                                $('#warehouse').append(sec_list);
                                //  generate_labrows();
                                $('#sold_kgs').val(0);
                                $('#pur_kgs').val(0);
                            }
                        }
                    }
                }
                function fetch_dvwise_saleqtys(){
                    var date = document.getElementById('date').value;
                    var warehouse = document.getElementById('warehouse').value;
                    document.getElementById("sold_kgs").value = "";
                    if(date != "" && warehouse != "select"){
                        var fch_sqty = new XMLHttpRequest();
                        var method = "GET";
                        var url = "chicken_fetch_dvwise_soldqtys.php?date="+date+"&warehouse="+warehouse;
                        var asynchronous = true;
                        fch_sqty.open(method, url, asynchronous);
                        fch_sqty.send();
                        fch_sqty.onreadystatechange = function(){
                            if(this.readyState == 4 && this.status == 200){
                                var sp_qty = this.responseText.split('@');
                                 if(sp_qty[0] == ""){ sp_qty[0] = 0; }
                                 if(sp_qty[1] == ""){ sp_qty[1] = 0; }
                                document.getElementById("sold_kgs").value = parseFloat(sp_qty[0]).toFixed(2);
                                document.getElementById("pur_kgs").value = parseFloat(sp_qty[1]).toFixed(2);
                                // generate_labrows();
                                 var no_labours_input = document.getElementById("no_labours");
                                var no_labours = parseInt(no_labours_input.value) || 1;
                                var each_share = (parseFloat(sp_qty[0]) / no_labours).toFixed(2);
                                var each_share1 = (parseFloat(sp_qty[1]) / no_labours).toFixed(2);
                                var selectedCount = 0;
                                console.log(each_share1);

                                for (var i = 0; i < no_labours; i++) {
                                    var labourSelect = document.getElementById("labour_code[" + i + "]");
                                    var weightInput = document.getElementById("sold_weight[" + i + "]");
                                    var rateInput = document.getElementById("rate[" + i + "]");
                                    var amtInput = document.getElementById("amount[" + i + "]");
                                    var bonusInput = document.getElementById("bonus[" + i + "]");

                                    if (labourSelect && labourSelect.value !== "select") {
                                        selectedCount++;

                                        if (weightInput) {
                                            weightInput.value = each_share;
                                        } 
                                        if (bonusInput) {
                                            bonusInput.value = each_share1;
                                        }

                                        var rate = (rateInput && rateInput.value !== "") ? parseFloat(rateInput.value) : 0;
                                        if (amtInput) {
                                            amtInput.value = (each_share * rate).toFixed(2);
                                        }
                                    } else {
                                        // If no labour selected, clear inputs
                                        if (weightInput) weightInput.value = '';
                                        if (bonusInput) bonusInput.value = '';
                                        if (amtInput) amtInput.value = '';
                                    }
                                }

                                // Update no_labours field with count of actual selected entries
                                no_labours_input.value = selectedCount;
                            }
                        }
                    }
                }
                function generate_labrows(){
                    clear_data();
                    var no_labours = document.getElementById("no_labours").value; if(no_labours == ""){ no_labours = 0; }
                    if(parseInt(no_labours) > 0){
                        var html = '';
                        var sold_kgs = document.getElementById("sold_kgs").value; if(sold_kgs == ""){ sold_kgs = 0; }
                        var pur_kgs = document.getElementById("pur_kgs").value; if(pur_kgs == ""){ pur_kgs = 0; }
                        // var unit_qty = parseFloat(sold_kgs) / parseFloat(no_labours);
                        var unit_qty = parseFloat((parseFloat(sold_kgs) / parseFloat(no_labours)).toFixed(2));
                        var unit_qty2 = parseFloat((parseFloat(pur_kgs) / parseFloat(no_labours)).toFixed(2));
                        unit_qty = parseFloat(unit_qty).toFixed(2);
                        for(var d = 0; d < no_labours;d++){
                            html += '<tr id="row_no['+d+']">';
                            html+= '<td><select name="labour_code[]" id="labour_code['+d+']" class="form-control select2" style="width:250px;"><option value="select">-select-</option><?php foreach($acc_code as $acode){ ?><option value="<?php echo $acode; ?>"><?php echo $acc_name[$acode]; ?></option><?php } ?></select></td>';
                            html+= '<td><input type="checkbox" name="supr_chk[]" id="supr_chk['+d+']" class="form-control text-right" onchange="update_suprprc(this.id);" /></td>';
                            html+= '<td><input type="text" name="supr_amt[]" id="supr_amt['+d+']" class="form-control text-right" style="width:90px;" readonly /></td>';
                            html+= '<td><input type="text" name="sold_weight[]" id="sold_weight['+d+']" class="form-control text-right" value="'+unit_qty+'" style="width:90px;" readonly /></td>';
                            html+= '<td><input type="text" name="rate[]" id="rate['+d+']" class="form-control text-right" style="width:140px;" onkeyup="validatenum(this.id);cal_tot_amt(this.id);" /></td>';
                            html+= '<td><input type="text" name="amount[]" id="amount['+d+']" class="form-control text-right" style="width:90px;" onkeyup="validatenum(this.id);cal_avg_prc(this.id);" /></td>';
                            html+= '<td><input type="text" name="bonus[]" id="bonus['+d+']" value="'+unit_qty2+'" class="form-control text-right" style="width:90px;" readonly/></td>';
                            html += '</tr>';
                        }
                        $('#row_body').append(html);
                        $('.select2').select2();
                        document.getElementById("incr").value = parseInt(no_labours);
                    }
                }
                function clear_data(){
                    document.getElementById("incr").value = 0;
                    document.getElementById("row_body").innerHTML = "";
                }
                function update_suprprc(a){
                    var b= a.split("["); var c = b[1].split("]"); var d = c[0];
                    var supr_chk = document.getElementById("supr_chk["+d+"]");
                    if(supr_chk.checked == true){ document.getElementById("supr_amt["+d+"]").value = 100; }
                    else{ document.getElementById("supr_amt["+d+"]").value = ""; }
                }
                function cal_tot_amt(a){
                    var b= a.split("["); var c = b[1].split("]"); var d = c[0];
                    var sold_weight = document.getElementById("sold_weight["+d+"]").value; if(sold_weight == ""){ sold_weight = 0; }
                    var rate = document.getElementById("rate["+d+"]").value; if(rate == ""){ rate = 0; }
                    var amount = parseFloat(sold_weight) * parseFloat(rate);
                    document.getElementById("amount["+d+"]").value = parseFloat(amount).toFixed(2);
                }
                function cal_avg_prc(a){
                    var b= a.split("["); var c = b[1].split("]"); var d = c[0];
                    var sold_weight = document.getElementById("sold_weight["+d+"]").value; if(sold_weight == ""){ sold_weight = 0; }
                    var amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                    var rate = 0; if(parseFloat(sold_weight) > 0){ rate = parseFloat(amount) / parseFloat(sold_weight); }
                    document.getElementById("rate["+d+"]").value = parseFloat(rate).toFixed(2);
                }
                function validate_count(x){ expr = /^[0-9]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9]/g, ''); } document.getElementById(x).value = a; }
                function validatenum(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } if(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } document.getElementById(x).value = a; }
                function validateamount(x){ expr = /^[0-9.]*$/; var a = document.getElementById(x).value; if(a.length > 50){ a = a.substr(0,a.length - 1); } while(!a.match(expr)){ a = a.replace(/[^0-9.]/g, ''); } if(a == ""){ a = 0; } else { } var b = parseFloat(a).toFixed(2); document.getElementById(x).value = b; }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
            <script>
                //Date Range selection
                $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
            </script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
