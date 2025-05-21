<?php
//broiler_closedfarms.php
$requested_data = json_decode(file_get_contents('php://input'),true);
if(!isset($_SESSION)){ session_start(); }
$db = $_SESSION['db'] = $_GET['db'];
$client = $_SESSION['client'];
if($db == ''){
    $user_code = $_SESSION['userid'];
    include "../newConfig.php";
    include "header_head.php";
    $form_path = "broiler_closedfarms.php";
}
else{
    $user_code = $_GET['userid'];
    include "APIconfig.php";
    include "header_head.php";
    $form_path = "broiler_closedfarms.php?db=$db&userid=".$user_code;
}

$file_name = "Previous Farms Performance Report";
$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'All' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $num_format_file = $row['num_format_file']; $img_logo = "../".$row['logopath']; $cdetails = $row['cdetails']; $company_name = $row['cname']; }
if($num_format_file == ""){ $num_format_file = "number_format_ind.php"; }
include $num_format_file;

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $vendor_code[$row['code']] = $row['code']; $vendor_ccode[$row['code']] = $row['cus_ccode'];$vendor_name[$row['code']] = $row['name']; }

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

$fdate = $tdate = date("Y-m-d"); $vendors = $sectors = $item_cat = $items =  "all"; $excel_type = "display";
if(isset($_POST['submit_report']) == true){
    $fdate = date("Y-m-d",strtotime($_POST['fdate']));
    $tdate = date("Y-m-d",strtotime($_POST['tdate']));
    // $vendors = $_POST['vendors'];
    // $item_cat = $_POST['item_cat'];
    // $items = $_POST['items'];
    // $sectors = $_POST['sectors'];

    
    // if($vendors == "all"){ $vendor_filter = ""; } else{ $vendor_filter = " AND `vcode` IN ('$vendors')"; }
    // if($sectors == "all"){ $sector_filter = ""; } else{ $sector_filter = " AND `warehouse` IN ('$sectors')"; }
    // if($items != "all"){ $item_filter = " AND `itemcode` IN ('$items')"; }
    // else if($item_cat == "all"){ $item_filter = ""; }
    // else{
    //     $icat_list = $item_filter = "";
    //     foreach($item_code as $icode){
    //         $item_category[$icode];
    //         if($item_category[$icode] == $item_cat){
    //             if($icat_list == ""){
    //                 $icat_list = $icode;
    //             }
    //             else{
    //                 $icat_list = $icat_list."','".$icode;
    //             }
    //         }
    //     }
    //     $item_filter = " AND `itemcode` IN ('$icat_list')";
    // }
	$excel_type = $_POST['export'];
	//$url = "../PHPExcel/Examples/InventoryAdjustment-Excel.php?fromdate=".$fdate."&todate=".$tdate."&items=".$items;
}
?>
<html>
    <head>
        <title>Poulsoft Solutions</title>
        <link href="../datepicker/jquery-ui.css" rel="stylesheet">
        <?php if($excel_type == "print"){ include "headerstyle_wprint.php"; } else{ include "headerstyle_woprint.php"; } ?>
    </head>
    <body align="center">
        <table class="tbl" align="center">
            <thead class="thead3" align="center" width="auto">
                <tr align="center">
                    <th colspan="2" align="center"><img src="<?php echo $img_logo; ?>" height="110px"/></th>
                    <th colspan="20" align="center"><?php echo $cdetails; ?><h5><?php echo $file_name; ?></h5></th>
                </tr>
            </thead>
            <form action="<?php echo $form_path; ?>" method="post">
                <thead class="thead2 text-primary layout-navbar-fixed">
                    <tr>
                        <th colspan="22">
                            <div class="row">
                                <div class="m-2 form-group">
                                    <label>From Date</label>
                                    <input type="text" name="fdate" id="fdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($fdate)); ?>" />
                                </div>
                                <div class="m-2 form-group">
                                    <label>To Date</label>
                                    <input type="text" name="tdate" id="tdate" class="form-control datepicker" style="width:110px;" value="<?php echo date("d.m.Y",strtotime($tdate)); ?>" />
                                </div>
                                <!-- <div class="m-2 form-group">
                                    <label>Customer</label>
                                    <select name="vendors" id="vendors" class="form-control select2">
                                        <option value="all" <?php if($vendors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($vendor_code as $cust){ if($vendor_name[$cust] != ""){ ?>
                                        <option value="<?php echo $cust; ?>" <?php if($vendors == $cust){ echo "selected"; } ?>><?php echo $vendor_name[$cust]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                               
                                <div class="m-2 form-group">
                                    <label>Farm/Location</label>
                                    <select name="sectors" id="sectors" class="form-control select2">
                                        <option value="all" <?php if($sectors == "all"){ echo "selected"; } ?>>-All-</option>
                                        <?php foreach($sector_code as $whcode){ if($sector_name[$whcode] != ""){ ?>
                                        <option value="<?php echo $whcode; ?>" <?php if($sectors == $whcode){ echo "selected"; } ?>><?php echo $sector_name[$whcode]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div> -->
                                <div class="m-2 form-group">
                                    <label>Export</label>
                                    <select name="export" id="export" class="form-control select2" onchange="tableToExcel('main_table', '<?php echo $file_name; ?>','<?php echo $file_name; ?>', this.options[this.selectedIndex].value)">
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
        </table>
        <table id="main_table" class="tbl" align="center">
            <thead class="thead3" align="center" id="head_names">
                <tr align="center">
                    <th>Date</th>
                    <th>Lot No.</th>
                    <th>COMPANY NAME OF CHICKS</th>
                    <th>CHICKS (IN PCS)</th>
                    <th>MORTILITY (IN PCS)</th>
                    <th>AVG.PERCENTAGE OF MORTALITY</th>
                    <th>LAME BIRDS   (Pcs.)</th>
                    <th>AVG.PERCENTAGE OF LAME BIRDS</th>
                    <th>LAME BIRDS (kGS.)</th>
                    <th>READY BIRDS (IN PCS)</th>
                    <th>FRASH READY BIRDS (IN PCS)</th>
                    <th>READY BIRDS (IN KGS)</th>
                    <th>FRASH READY BIRDS (IN KGS)</th>
                    <th>AVG. BIRDS WT.</th>
                    <th>FCR</th>
                    <th>CHICK RATE</th>
                    <th>CHICKS COST (RS)</th>
                    <th>TOTAL FEED CONSUMED (KGS)</th>
                    <th>TOTAL FEED CONSUMED (KGS) / PER BIRDS</th>
                    <th> TOTAL FEED CONSUMED (KGS) / FRASH BIRDS </th>
                    <th>AVG. WT. FRACH BIRDS</th>
                    <th> FCR WITHOUT LAME BIRDS </th>
                    <th>RATE</th>
                    <th>TOTAL FEED COST (RS)</th>
                    <th>MEDICINE COST (RS)</th>
                    <th>ADMINISTRATION COST (RS)</th>
                    <th>FARMER PAYMENT (RS)</th>
                    <th>TOTAL COSTING (RS)</th>
                    <th>AVERAGE COST PER BIRDS</th>
                    <th>LIFTING DATE</th>
                    <th>AVG. SALE PRICE (KG)</th>
                    <th>TOTAL SALES (RS)</th>
                    <th>PROFIT / LOSS</th>
                </tr>
            </thead>
            <?php
            if(isset($_POST['submit_report']) == true){
            ?>
            <tbody class="tbody1">
                <?php
                $total_qty = $total_amt = 0;
                $sql = "SELECT * FROM `closedfarms_realization` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `dflag` = '0' ORDER BY `date` ASC"; $query = mysqli_query($conn,$sql);
                while($row = mysqli_fetch_assoc($query)){
                ?>
                <tr>
                    <td><?php echo date("d.m.Y",strtotime($row['date'])); ?></td>
                    <td><?php echo $row['lot_no']; ?></td>
                    <td><?php echo $row['company']; ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['chick_qty'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['mortality'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['avg_mort'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['lame_birds'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['avg_lamebirds'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['lame_kgs'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['ready_birds'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['fresh_ready_birds'],2)); ?></td>
                    
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['ready_kgs'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['fresh_birds_kgs'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['avg_bwt'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['fcr'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['c_rate'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['c_cost'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['feed_cons'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['total_feed_cons'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['total_feed_fs_birds'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['avg_wt_fresh'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['fcr_w_lame'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['lame_rate'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['feed_cost'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['med_cost'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['admin_cost'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['farmer_payment'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['total_cost'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['avg_cost'],2)); ?></td>
                    <td><?php echo date("d.m.Y",strtotime($row['lifting_date'])); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['avg_sale_rate'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['total_sales'],2)); ?></td>
                    <td style="text-align:right;"><?php echo number_format_ind(round($row['profit_loss'],2)); ?></td>
                    
                </tr>
                <?php
                    $total_chick_qty = $total_chick_qty + $row['chick_qty'];
                    $total_mortality = $total_mortality + $row['mortality'];
                    $total_avg_mort = $total_avg_mort + $row['avg_mort'];
                    $total_lame_birds = $total_lame_birds + $row['lame_birds'];
                    $total_avg_lamebirds = $total_avg_lamebirds + $row['avg_lamebirds'];
                    $total_lame_kgs = $total_lame_kgs + $row['lame_kgs'];
                    $total_ready_birds = $total_ready_birds + $row['ready_birds'];
                    $total_fresh_ready_birds = $total_fresh_ready_birds + $row['fresh_ready_birds'];
                    $total_ready_kgs = $total_ready_kgs + $row['ready_kgs'];
                    $total_fresh_birds_kgs = $total_fresh_birds_kgs + $row['fresh_birds_kgs'];
                    $total_avg_bwt = $total_avg_bwt + $row['avg_bwt'];
                    $total_fcr = $total_fcr + $row['fcr'];
                    $total_c_rate = $total_c_rate + $row['c_rate'];
                    $total_c_cost = $total_c_cost + $row['c_cost'];
                    $total_feed_cons = $total_feed_cons + $row['feed_cons'];
                    $total_total_feed_cons = $total_total_feed_cons + $row['total_feed_cons'];
                    $total_total_feed_fs_birds = $total_total_feed_fs_birds + $row['total_feed_fs_birds'];
                    $total_avg_wt_fresh = $total_avg_wt_fresh + $row['avg_wt_fresh'];
                    $total_fcr_w_lame = $total_fcr_w_lame + $row['fcr_w_lame'];
                    $total_lame_rate = $total_lame_rate + $row['lame_rate'];
                    $total_feed_cost = $total_feed_cost + $row['feed_cost'];
                    $total_med_cost = $total_med_cost + $row['med_cost'];
                    $total_admin_cost = $total_admin_cost + $row['admin_cost'];
                    $total_farmer_payment = $total_farmer_payment + $row['farmer_payment'];
                    $total_total_cost = $total_total_cost + $row['total_cost'];
                    $total_avg_cost = $total_avg_cost + $row['avg_cost'];
                    $total_avg_sale_rate = $total_avg_sale_rate + $row['avg_sale_rate'];
                    $total_total_sales = $total_total_sales + $row['total_sales'];
                    $total_profit_loss = $total_profit_loss + $row['profit_loss'];
                }
                // $avg_prc = 0;
                // if((float)$total_qty != 0){
                //     $avg_prc = ((float)$total_amt / (float)$total_qty);
                // }
                ?>
                <tr class="thead4">
                    <th colspan="3" style="text-align:center;">Total</th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_chick_qty,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_mortality,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_avg_mort,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_lame_birds,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_avg_lamebirds,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_lame_kgs,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_ready_birds,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_fresh_ready_birds,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_ready_kgs,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_fresh_birds_kgs,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_avg_bwt,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_fcr,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_c_rate,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_c_cost,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_feed_cons,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_total_feed_cons,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_total_feed_fs_birds,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_avg_wt_fresh,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_fcr_w_lame,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_lame_rate,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_feed_cost,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_med_cost,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_admin_cost,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_farmer_payment,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_total_cost,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_avg_cost,2)); ?></th>
                    <th style="text-align:right;"></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_avg_sale_rate,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_total_sales,2)); ?></th>
                    <th style="text-align:right;"><?php echo number_format_ind(round($total_profit_loss,2)); ?></th>
                   
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
        <script>
            function table_sort() {
		        console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `.order-inactive span { visibility:hidden; } .order-inactive:hover span { visibility:visible; } .order-active span { visibility: visible; }`;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;
                        const b_val = b.children[index].innerText;
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }
            function convertDate(d){ var p = d.split("."); return (p[2]+p[1]+p[0]); }
            function table_sort3() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_date').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_date').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = convertDate(a.children[index].innerText);
                        const b_val = convertDate(b.children[index].innerText);
                        return (asc) ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val)
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
            }

            function convertNumber(d) { var p = intval(d); return (p); }

            function table_sort2() {
                console.log("test");
                const styleSheet = document.createElement('style');
                styleSheet.innerHTML = `
                        .order-inactive span {
                            visibility:hidden;
                        }
                        .order-inactive:hover span {
                            visibility:visible;
                        }
                        .order-active span {
                            visibility: visible;
                        }
                    `;
                document.head.appendChild(styleSheet);

                document.querySelectorAll('#order_num').forEach(th_elem => {
                    console.log("test1");

                    let asc = true;
                    const span_elem = document.createElement('span');
                    span_elem.style = "font-size:0.8rem; margin-left:0.5rem";
                    span_elem.innerHTML = "▼";
                    th_elem.appendChild(span_elem);
                    th_elem.classList.add('order-inactive');

                    const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
                    th_elem.addEventListener('click', (e) => {
                    document.querySelectorAll('#order_num').forEach(elem => {
                        elem.classList.remove('order-active')
                        elem.classList.add('order-inactive')
                    });
                    th_elem.classList.remove('order-inactive');
                    th_elem.classList.add('order-active');

                    if (!asc) {
                        th_elem.querySelector('span').innerHTML = '▲';
                    } else {
                        th_elem.querySelector('span').innerHTML = '▼';
                    }
                    
                    var arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr'));
                    arr.sort((a, b) => {
                        const a_val = a.children[index].innerText;    
                        if(isNaN(a_val)){
                        a_val1 = a_val.split(',').join(''); }
                        else {
                            a_val1 = a_val; }
                        const b_val = b.children[index].innerText;
                        if(isNaN(b_val)){
                        b_val1 = b_val.split(',').join('');}
                        else {
                            b_val1 = b_val; }
                        return (asc) ? b_val1 - a_val1:  a_val1 - b_val1 
                    });
                    arr.forEach(elem => {
                        th_elem.closest("table").querySelector("tbody").appendChild(elem)
                    });
                    slnos();
                    asc = !asc;
                    })
                });
                
            }
            function slnos(){
                var rcount = document.getElementById("tbody1").rows.length;
                var myTable = document.getElementById('tbody1');
                var j = 0;
                for(var i = 1;i <= rcount;i++){ j = i - 1; myTable.rows[j].cells[0].innerHTML = i; }
            }

            table_sort();
            table_sort2();
            table_sort3();
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
        <script type="text/javascript">
            function tableToExcel(table, name, filename, chosen){
                if(chosen === 'excel'){
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html +='<tr style="text-align:center;" align="center">';
                    html +='<th style="text-align:center;">Date</th>';
                    html +='<th style="text-align:center;">Transaction No.</th>';
                    html +='<th style="text-align:center;">Customer</th>';
                    html +='<th style="text-align:center;">Item</th>';
                    html +='<th style="text-align:center;">Quantity</th>';
                    html +='<th style="text-align:center;">Price</th>';
                    html +='<th style="text-align:center;">Amount</th>';
                    html +='<th style="text-align:center;">Warehouse/Location</th>';
                    html +='<th style="text-align:center;">Status</th>';
                    html +='<th style="text-align:center;">Remarks</th>';
                    html +='</tr>';
                    $('#head_names').append(html);

                    var uri = 'data:application/vnd.ms-excel;base64,'
                    , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
                    , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
                    , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
                    //  return function(table, name, filename, chosen) {
                
                    if (!table.nodeType) table = document.getElementById(table)
                    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
                    //window.location.href = uri + base64(format(template, ctx))
                    var link = document.createElement("a");
                    link.download = filename+".xls";
                    link.href = uri + base64(format(template, ctx));
                    link.click();
                    //}
                    
                    document.getElementById("head_names").innerHTML = "";
                    var html = '';
                    html +='<tr style="text-align:center;" align="center">';
                    html +='<th style="text-align:center;" id="order_date">Date</th>';
                    html +='<th style="text-align:center;" id="order">Transaction No.</th>';
                    html +='<th style="text-align:center;" id="order">Customer</th>';
                    html +='<th style="text-align:center;" id="order">Item</th>';
                    html +='<th style="text-align:center;" id="order_num">Quantity</th>';
                    html +='<th style="text-align:center;" id="order_num">Price</th>';
                    html +='<th style="text-align:center;" id="order_num">Amount</th>';
                    html +='<th style="text-align:center;" id="order">Warehouse/Location</th>';
                    html +='<th style="text-align:center;" id="order">Status</th>';
                    html +='<th style="text-align:center;" id="order">Remarks</th>';
                    html +='</tr>';
                    //$('#head_names').append(html);
                    document.getElementById("head_names").innerHTML = html;
                    table_sort();
                    table_sort2();
                    table_sort3();
                }
                else{ }
            }
        </script>
        <script src="../datepicker/jquery/jquery.js"></script>
        <script src="../datepicker/jquery-ui.js"></script>
    </body>
</html>
<?php
include "header_foot.php";
?>