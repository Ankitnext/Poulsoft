<?php
	session_start(); include "broiler_check_tableavailability.php";
	include "number_format_ind.php";

    $field_link_list = $field_name_list = array(); $i = 0;
    $field_link_list[$i] = "generalreports/profitandlossreport.php"; $field_name_list[$i] = "Profit & Loss"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/main_paperratereport.php"; $field_name_list[$i] = "Date Wise Paper Rate"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/cus_paperrateupdated.php"; $field_name_list[$i] = "Customer Paper Rate"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/CustomerUsageReport1.php"; $field_name_list[$i] = "Customer Usage"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/cus_salesplreport.php"; $field_name_list[$i] = "Customer PL"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/cus_outstandingBalanceWINACReport.php"; $field_name_list[$i] = "In-active Customer Balance"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/DailyRateSummaryReport.php"; $field_name_list[$i] = "Daily Rate Analysis"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/cusbalanceform.php"; $field_name_list[$i] = "Customer Balance Confirmation Form"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/main_stock_report.php"; $field_name_list[$i] = "Stock Report"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/cus_tcssummary.php"; $field_name_list[$i] = "TCS Summary"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/cus_tcsdetailed.php"; $field_name_list[$i] = "TCS Detailed"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/sup_tdssummary.php"; $field_name_list[$i] = "TDS Summary"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/sup_tdsdetailed.php"; $field_name_list[$i] = "TDS Detailed"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/main_datewiseloadreport.php"; $field_name_list[$i] = "Load wise Ledger"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/CustomerLedgerReportAllNew_print.php"; $field_name_list[$i] = "All Customer Balance Print"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/cus_outstandingBalanceReport.php"; $field_name_list[$i] = "Customer Balance Message"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/chicken_customerandsupplierledgerall.php"; $field_name_list[$i] = "C&S Balance All"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/chicken_datewise_useractivityj.php"; $field_name_list[$i] = "User Activity Report"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    $field_link_list[$i] = "generalreports/CustomerBalanceExceeded.php"; $field_name_list[$i] = "Customer Balance Exceeded List"; $field_icon_list[$i] = '<i class="fas fa-barcode"></i>'; $i++;
    
?>
	<html>
		<head>
        <?php include "header_head.php"; ?>
        <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
		</head>
		<body class="hold-transition skin-yellow-light sidebar-collapse sidebar-mini" style="min-height:700px;">
			<section class="content-header" style="padding-bottom: 75px;">
				<div class="row">
					<div class="col-md-6" align="left"><h3>MIS Report List</h3></div>
					<div class="col-md-6"></div>
				</div>
			</section>
			<!-- Main content -->
			<section class="content">
                <?php
                    $k = 0;
                    for($j = 0; $j < $i; $j++){
                        $k++;
                        $a = $j % 4;
                        if($j == 0){
                            echo "<div class='row'>";
                        }
                        else if($a == 0){
                            echo "</div><br/><div class='row'>";
                        }
                    ?>
                        <div class="col-md-3">
                            <a href="<?php echo $field_link_list[$j]; ?>" target="_BLANK" class="btn btn-lg bg-success" style="width:100%;">
                                <?php echo $field_icon_list[$j]." ".$field_name_list[$j]; ?>
                            </a>
                        </div>
                    <?php
                        //if($k == 4 && $a == 0){ echo "</div>"; }
                    }
                ?>
			</section>
            <?php include "header_foot.php"; ?>
		</body>
	</html>