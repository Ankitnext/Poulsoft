<?php
//chicken_add_corderview1.php
include "newConfig.php";
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $href = basename($path);
global $ufile_name; $ufile_name = $href; include "chicken_check_accessmaster.php";

if($access_error_flag == 0){
    $sql = "SELECT * FROM `customerOrderViewPermissions` WHERE `dflag` = '0' ORDER BY `ccode` ASC";
    $query = mysqli_query($conn,$sql); $cus_alist = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_alist[$row['ccode']] = $row['ccode']; }

    $cus_list = implode("','", $cus_alist);
    $sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `code` NOT IN ('$cus_list') AND `active` = '1' ORDER BY `name` ASC";
    $query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
    while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }
    
?>
    <html>
        <head>
            <?php include "header_head1.php"; ?>
            <style>
                label{
                    font-weight:bold;
                }
                .tr_head{
                    border-bottom: 1px dashed black;
                }
            </style>
        </head>
        <body>
            <div class="card border-secondary mb-3">
                <div class="card-header">Add Screen Views</div>
                <form action="chicken_save_corderview1.php" method="post" onsubmit="return checkval();">
                    <div class="card-body">
                        <div class="row">
                            <table align="center" style="width:45%;">
                                <thead>
                                    <tr class="tr_head">
                                        <th style="text-align:center;">Sl.No.</th>
                                        <th style="text-align:center;">Select All<br/><input type="checkbox" name="checkall" id="checkall" onclick="check_achkbox(this.id);" /></th>
                                        <th style="text-align:center;">Customer Name</th>
                                        <th style="text-align:center;">Big Box<br/>Weight</th>
                                        <th style="text-align:center;">Big Boxes</th>
                                        <th style="text-align:center;">tandoori</th>
                                        <th style="text-align:center;">Dressed<br/>Chicken</th>
                                    </tr>
                                </thead>
                                <tbody id="row_body">
                                    <?php
                                    $c = 0;
                                    foreach($cus_code as $scode){
                                    ?>
                                    <tr>
                                        <td style="text-align:center;"><?php echo $c; ?></td>
                                        <td style="text-align:center;"><input type="checkbox" name="slno[<?php echo $c; ?>]" id="slno[<?php echo $c; ?>]" value="<?php echo $c; ?>" /></td>
                                        <td><select name="ccode[<?php echo $c; ?>]" id="ccode[<?php echo $c; ?>]" class="form-control select2" style="width:280px;"><option value="<?php echo $scode; ?>"><?php echo $cus_name[$scode]; ?></option></select></td>
                                        <td style="text-align:center;"><input type="checkbox" name="bigBoxWeight[<?php echo $c; ?>]" id="bigBoxWeight[<?php echo $c; ?>]" /></td>
                                        <td style="text-align:center;"><input type="checkbox" name="bigBoxes[<?php echo $c; ?>]" id="bigBoxes[<?php echo $c; ?>]" /></td>
                                        <td style="text-align:center;"><input type="checkbox" name="tandoori[<?php echo $c; ?>]" id="tandoori[<?php echo $c; ?>]" /></td>
                                        <td style="text-align:center;"><input type="checkbox" name="dressed_chicken[<?php echo $c; ?>]" id="dressed_chicken[<?php echo $c; ?>]" /></td>
                                    </tr>
                                    <?php $c++; } ?>
                                </tbody>
                            </table>
                        </div><br/>
                        <div class="row" style="visibility:hidden;">
                            <div class="form-group" style="width:30px;">
                                <label>IN</label>
                                <input type="text" name="incr" id="incr" class="form-control" value="<?php echo $c; ?>" style="width:20px;" readonly />
                            </div>
                            <div class="form-group" style="width:30px;">
                                <label>EB</label>
                                <input type="text" style="width:auto;" class="form-control" name="ebtncount" id="ebtncount" value="0" style="width:20px;" readonly />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group" align="center">
                                <button type="submit" name="submit" id="submit" class="btn btn-sm text-white bg-success">Submit</button>&ensp;
                                <button type="button" name="cancel" id="cancel" class="btn btn-sm text-white bg-danger" onclick="return_back()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script>
                function return_back(){
                    window.location.href = "chicken_display_corderview1.php";
                }
                function checkval(){
                    document.getElementById("ebtncount").value = "1"; document.getElementById("submit").style.visibility = "hidden";
                    var l = true; var c_cnt = r_cnt = 0;
                    var incr = document.getElementById("incr").value;
                    for(var d = 0;d <= incr;d++){
                        if(l == true){
                            var slno = document.getElementById("slno["+d+"]");
                            if(slno.checked == true){
                                var bbwht = document.getElementById("bigBoxWeight["+d+"]");
                                var bboxs = document.getElementById("bigBoxes["+d+"]");
                                var tdoor = document.getElementById("tandoori["+d+"]");
                                var dsdck = document.getElementById("dressed_chicken["+d+"]");
                                if(bbwht.checked == true || bboxs.checked == true || tdoor.checked == true || dsdck.checked == true){ }
                                else{
                                    alert("select/check atleast one field for selected Customer in row: "+d);
                                    l = false;
                                }
                                c_cnt++;
                            }
                        }
                    }
                    if(parseInt(c_cnt) == 0){
                        alert("select/check atleast one Customer");
                        l = false;
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
                function check_achkbox(a){
                    if(a == "checkall"){
                        var c_all = document.getElementById("checkall");
                        var slno = document.querySelectorAll('input[name="slno[]"]');
                        if(c_all.checked == true){ for(var i = 0; i < slno.length; i++){ slno[i].checked = true; } }
                        else{ for(var i = 0; i < slno.length; i++){ slno[i].checked = false; } }
                    }
                }
            </script>
		    <script src="chick_validate_basicfields.js"></script>
            <?php include "header_foot1.php"; ?>
		    <script src="handle_ebtn_as_tbtn.js"></script>
        </body>
    </html>
<?php
}
else{ include "chicken_error_popup.php"; }
