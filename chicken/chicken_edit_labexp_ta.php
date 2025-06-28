<?php
//chicken_edit_labourexp.php
include "newConfig.php";
include "chicken_generate_trnum_details.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
  
    //Sector Details
    $sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
    $query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
    while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

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
                    $trnum = $row['code'];
                    $sold_kgs = $row['sold_kgs'];
                    $warehouse = $row['warehouse'];
                    $no_labours = $row['no_labours'];
                    $labour_code[$c] = $row['labour_code'];
                    $supervisor_value[$c] = $row['supervisor_value'];
                    $sold_weight[$c] = round($row['sold_weight'],2);
                    $rate[$c] = round($row['rate'],2);
                    $amount[$c] = round($row['amount'],2);
                    $remarks = $row['remarks'];
                    $c++;
                } $c = $c - 1;
            }
            ?>
            <div class="card border-secondary mb-3">
                <div class="card-header">Edit Shop Investment</div>
                <form action="chicken_modify_labourexp.php" method="post" onsubmit="return checkval();">
                    <div class="ml-5 card-body">
                        <div class="row">
                            <div class="form-group" style="width:110px;">
                                <label for="date">Date</label>
                                <input type="text" name="date" id="date" class="form-control range_picker" value="<?php echo $fdate; ?>" style="width:100px;" onchange="fetch_veh();" readonly />
                            </div>
                             <div class="form-group" style="width:210px;">
                                <label for="warehouse">Vehicle<b style="color:red;">&nbsp;*</b></label>
                                <select name="warehouse" id="warehouse" class="form-control select2" style="width:200px;" onchange="fetch_sold_kgs()"><option value="select">-select-</option><?php foreach($sector_code as $cc){ ?><option value="<?php echo $sector_code[$cc]; ?>" <?php if($cc == $warehouse){ echo "selected";} ?>><?php echo $sector_name[$cc]; ?></option><?php } ?></select>
                            </div>
                            <div class="form-group" style="width:100px;">
                                <label for="sold_kgs">Sold Kgs</label>
                                <input type="text" name="sold_kgs" id="sold_kgs" class="form-control" value="<?php echo $sold_kgs; ?>" style="width:90px;" readonly/>
                            </div>
                            <div class="form-group" style="width:150px;">
                                <label for="no_labours">No. Of Labours</label>
                                <input type="text" name="no_labours" id="no_labours" class="form-control" value="<?php echo $no_labours; ?>" style="width:140px;" />
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
                                        <th style="text-align:center;"></th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                <?php  ?>
                                <?php $incr = $c; for($c = 0;$c <= $incr;$c++){  ?>
                                    <tr id="">
                                        <td style="width: 250px;"><select name="labour_code[]" id="labour_code[<?php echo $c; ?>]" class="form-control select2" data-row="<?php echo $c; ?>" data-col="0" style="width: 250px;"><option value="select">-select-</option><?php foreach($acc_code as $cc){ ?><option value="<?php echo $acc_code[$cc]; ?>" <?php if($cc == $labour_code[$c]){ echo "selected";} ?>><?php echo $acc_name[$cc]; ?></option><?php } ?></select></td>
                                        <?php $colIndex = 1; ?>
                                        <td style="width:21px;display:flex;justify-content:center;"><input type="checkbox" name="check[]" id="check[<?php echo $c; ?>]" class="form-control" value="100" style="width:10px;" <?php if (!empty($supervisor_value[$c])) echo 'checked'; ?>></td>
                                        <td style="width:100px;"><input type="text" name="supervisor_value[]" id="supervisor_value[<?php echo $c; ?>]" class="form-control" value="<?php echo $supervisor_value[$c]; ?>" style="width:90px;" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" readonly/></td>
                                        <td style="width:100px;"><input type="text" name="sold_weight[]" id="sold_weight[<?php echo $c; ?>]" class="form-control" value="<?php echo $sold_weight[$c]; ?>" style="width:90px;" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" onkeyup="calculate_cpt(this.id);calculate_amt(this.id);" readonly/></td>
                                        <td style="width:150px;"><input type="text" name="rate[]" id="rate[<?php echo $c; ?>]" class="form-control" value="<?php echo $rate[$c]; ?>" style="width:140px;" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" onkeyup="calculate_amt(this.id)"></td>
                                        <td style="width:100px;"><input type="text" name="amount[]" id="amount[<?php echo $c; ?>]" class="form-control" value="<?php echo $amount[$c]; ?>" style="width:90px;" data-row="<?php echo $c; ?>" data-col="<?php echo $colIndex++; ?>" onkeyup="calculate_cpt(this.id)"></td>
                                        <td style="width:20px;visibility:hidden;"><input type="text" name="trnum" id="trnum" class="form-control" value="<?php echo $trnum; ?>" style="width:20px;" readonly /></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div><br/>
                       <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="0" style="width:20px;" readonly />
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
                function calculate_amt(a){
                    var b= a.split("["); var c = b[1].split("]"); var d = c[0];
                    var qty = document.getElementById("sold_weight["+d+"]").value; if(qty == ""){ qty = 0; }
                    var price = document.getElementById("rate["+d+"]").value; if(price == ""){ price = 0; }
                    
                     var amot = parseFloat(price) * parseFloat(qty);
                    document.getElementById("amount["+d+"]").value = amot.toFixed(0);
                }
                function calculate_cpt(a){
                    var b= a.split("["); var c = b[1].split("]"); var d = c[0];
                    var qty = document.getElementById("sold_weight["+d+"]").value; if(qty == ""){ qty = 0; }
                    var amt = document.getElementById("amount["+d+"]").value; if(amt == ""){ amt = 0; }

                    if(qty != "" || qty != 0 ){ var cpt = parseFloat(amt) / parseFloat(qty);} else { cpt = 0;}
                    document.getElementById("rate["+d+"]").value = cpt.toFixed(0);
                }
           
                function fetch_veh() {
                    var selectedDate = document.getElementById('date').value;

                    if (selectedDate === '') return;

                    $.ajax({
                        url: 'fetch_vehicles.php',
                        type: 'POST',
                        data: { date: selectedDate },
                        success: function(response) {
                            $('#warehouse').html(response);
                            if(response != ""){ 
                                $('#sold_kgs').val(0); 
                                $('input[name="sold_weight[]"]').val(0); }
                            
                            // console.log(response);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching vehicle data:", error);
                        }
                    });
                }
                function fetch_sold_kgs() {
                    var selectedDate = $('#date').val();
                    var selectedWarehouse = $('#warehouse').val();

                    if (selectedDate === '' || selectedWarehouse === 'select') return;

                    $.ajax({
                        url: 'fetch_sold_kgs.php',
                        type: 'POST',
                        data: { date: selectedDate, warehouse: selectedWarehouse },
                        success: function(response) {
                            $('#sold_kgs').val(response);

                            let sold = parseFloat(response) || 0;
                            let count = parseInt($('#no_labours').val()) || 1;
                            if (count < 1) count = 1;

                            let perLabour = (sold / count).toFixed(1);

                            $("input[name='sold_weight[]']").each(function () {
                                $(this).val(perLabour);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching sold kgs:", error);
                        }
                    });
                }

                $(document).ready(function () {
                    $('#no_labours').on('keyup', function () {
                        let count = parseInt($(this).val());
                        let sold = parseFloat($('#sold_kgs').val()) || 0;

                        if (isNaN(count) || count < 1) {
                            count = 1;
                        }

                        // Keep row 0 and remove any additional rows
                        $('#row_body tr').each(function (index) {
                            if (index > 0) {
                                $(this).remove();
                            }
                        });

                        $('#incr').val(1);

                        // Recreate rows
                        for (let i = 1; i < count; i++) {
                            create_row(i);
                        }

                        // Once rows are rendered, distribute sold kgs
                        setTimeout(function () {
                            let perLabour = count > 0 ? (sold / count).toFixed(1) : 0;

                            $("input[name='sold_weight[]']").each(function () {
                                $(this).val(perLabour);
                            });
                        }, 100); // Delay to ensure rows are added
                    });

                });

                document.addEventListener("DOMContentLoaded", function () {
                    // Event delegation: listen on the table body
                    document.getElementById("row_body").addEventListener("change", function (e) {
                        if (e.target && e.target.type === "checkbox" && e.target.name === "check[]") {
                            let checkbox = e.target;
                            let idMatch = checkbox.id.match(/\[(\d+)\]/); // extract index from "check[0]"

                            if (idMatch) {
                                let index = idMatch[1];
                                let supervisorInput = document.getElementById("supervisor_value[" + index + "]");
                                if (supervisorInput) {
                                    supervisorInput.value = checkbox.checked ? "100" : "0";
                                }
                            }
                        }
                    });
                });
               function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true;
                   
                    var vcode = itemcode = ""; var amount = 0;
                    var incr = parseInt(document.getElementById("incr").value);
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            c = d + 1;
                            date = document.getElementById("date["+d+"]").value;
                            vcode = document.getElementById("vcode["+d+"]").value;
                            amount = document.getElementById("amount["+d+"]").value; if(amount == ""){ amount = 0; }
                            itemcode = document.getElementById("itemcode["+d+"]").value;
                           
                            if(date == ""){
                                alert("Please select Date in row: "+c);
                                document.getElementById("date["+d+"]").focus();
                                l = false;
                                break;
                            }
                            else if(vcode == "select"){
                                alert("Please select Customer names in row: "+c);
                                document.getElementById("vcode["+d+"]").focus();
                                l = false;
                                break;
                            }
                            else if(parseFloat(amount) == 0){
                                alert("Please enter Amount in row: "+c);
                                document.getElementById("amount["+d+"]").focus();
                                l = false;
                                break;
                            }
                             else if(itemcode == "select"){
                                alert("Please select Item names in row: "+c);
                                document.getElementById("itemcode["+d+"]").focus();
                                l = false;
                                break;
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
                function create_row(a){
                    // var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    // document.getElementById("action["+d+"]").style.visibility = "hidden";
                    var colIndex = 0;
                    var d = a;
                    d++; 
                    var html = '';
                    document.getElementById("incr").value = d;
                    html += '<tr id="row_no['+d+']">';
                    html+= '<td style="width: 250px;padding-right:5px;"><select name="labour_code[]" id="labour_code['+d+']" class="form-control select2" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width: 250px;"><option value="select">-select-</option><?php foreach($acc_code as $cc){ ?><option value="<?php echo $acc_code[$cc]; ?>"><?php echo $acc_name[$cc]; ?></option><?php } ?></select></td>';
                    html+= '<td style="width:21px;display:flex;justify-content:center;"><input type="checkbox" name="check[]" id="check['+d+']" class="form-control" value="" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:10px;"/></td>';
                    html+= '<td style="width:100px;"><input type="text" name="supervisor_value[]" id="supervisor_value['+d+']" class="form-control" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:90px;" onkeyup="validatenum(this.id);" onchange="validateamount(this.id)" readonly/></td>';
                    html+= '<td style="width:100px;"><input type="text" name="sold_weight[]" id="sold_weight['+d+']" class="form-control" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:90px;" value="" onkeyup="validatenum(this.id);calculate_cpt(this.id);calculate_amt(this.id);" onchange="validateamount(this.id)" readonly/></td>';
                    html+= '<td style="width:150px;"><input type="text" name="rate[]" id="rate['+d+']" class="form-control" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:140px;" onkeyup="validatenum(this.id);calculate_amt(this.id);" onchange="validateamount(this.id)" /></td>';
                    html+= '<td style="width:100px;"><input type="text" name="amount[]" id="amount['+d+']" class="form-control" data-row="'+d+'" data-col="'+(colIndex++)+'" style="width:90px;" onkeyup="validatenum(this.id);calculate_cpt(this.id);" onchange="validateamount(this.id)" /></td>';
                    html += '<td id="action['+d+']"><a href="javascript:void(0);" id="addrow['+d+']" onclick="create_row(this.id)"><i class="fa fa-plus"></i></a>&ensp;<a href="javascript:void(0);" id="deductrow['+d+']" onclick="destroy_row(this.id)"><i class="fa fa-minus" style="color:red;"></i></a></td>';
                    html += '</tr>';
                    $('#row_body').append(html);
                    $('.select2').select2();
                    // document.getElementById("vcode["+d+"]").focus();
                    // $( ".range_picker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", minDate: s_date, maxDate: e_date, beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
                }
                function destroy_row(a){
                    var b = a.split("["); var c = b[1].split("]"); var d = c[0];
                    document.getElementById("row_no["+d+"]").remove();
                    d--;
                    document.getElementById("incr").value = d;
                    document.getElementById("action["+d+"]").style.visibility = "visible";
                   
                }
                document.addEventListener("keydown", (e) => { var key_search = document.activeElement.id.includes("["); if(key_search == true){ var b = document.activeElement.id.split("["); var c = b[1].split("]"); var d = c[0]; document.getElementById("incrs").value = d; } if (e.key === "Enter"){ var ebtncount = document.getElementById("ebtncount").value; if(ebtncount > 0){ event.preventDefault(); } else{ $(":submit").click(function () { $('#submittrans').click(); }); } } else{ } });
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
