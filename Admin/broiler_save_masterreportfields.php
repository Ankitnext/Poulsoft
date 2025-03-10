<?php
//broiler_save_masterreportfields.php
include "newConfig.php";
if(!isset($_SESSION)){ session_start(); }
date_default_timezone_set("Asia/Kolkata");
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['masterreportfields'];

/*Master Field List*/
$row_incr = 0;
$mfl_col_name[$row_incr] = "field_pattern"; $row_incr++;
$mfl_col_name[$row_incr] = "user_access_code"; $row_incr++;
$mfl_col_name[$row_incr] = "sl_no"; $row_incr++;
$mfl_col_name[$row_incr] = "date"; $row_incr++;
$mfl_col_name[$row_incr] = "trnum"; $row_incr++;
$mfl_col_name[$row_incr] = "vendor_name"; $row_incr++;
$mfl_col_name[$row_incr] = "item_name"; $row_incr++;
$mfl_col_name[$row_incr] = "transaction_type"; $row_incr++;
$mfl_col_name[$row_incr] = "gst_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "discount_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "freight_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "tcds_price"; $row_incr++;
$mfl_col_name[$row_incr] = "tcds_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "vendor_cr_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "vendor_dr_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "vendor_bal_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "cash_receipt_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "bank_receipt_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "receipt_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "vehicle_no"; $row_incr++;
$mfl_col_name[$row_incr] = "driver_name"; $row_incr++;
$mfl_col_name[$row_incr] = "sector_name"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_client"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_trtype"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_assignee"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_priority"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_devtype"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_dbtype"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_work"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_link"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_remarks"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_references"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_status"; $row_incr++;
$mfl_col_name[$row_incr] = "ticket_createdby"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_trnum"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_link_trnum"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_date"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_ccode"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_name"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_billno"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_itemname"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_nof_bags"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_birds"; $row_incr++;
$mfl_col_name[$row_incr] = "cus_total_weight"; $row_incr++;
$mfl_col_name[$row_incr] = "cus_empty_weight"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_snt_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_mort_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_cull_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_rcd_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_fre_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_itemprice"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_dis_per"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_dis_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_address"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_gst_no"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_gst_per"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_gst_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_cgst_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_sgst_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_igst_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_tcds_per"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_tcds_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_itemamount"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_freight_type"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_freight_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_freight_pay_type"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_freight_pay_acc"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_freight_acc"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_round_off"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_finl_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_avg_price"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_avg_wt"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_profit"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_remarks"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_warehouse"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_farm_batch"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_farm_mnuname"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_batch_mnuname"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_supervisor_code"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_bag_code"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_bag_count"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_batch_no"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_exp_date"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_vehicle_code"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_driver_code"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_sale_type"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_gc_flag"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_active"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_addedemp"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_addedtime"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_updatedemp"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_updatedtime"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_latitude"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_longitude"; $row_incr++;
$mfl_col_name[$row_incr] = "Customer_sale_location"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_imei"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_mob_flag"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_sale_image"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_credit"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_debit"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_marketing_executive"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_runningbalance"; $row_incr++;

$mfl_col_name[$row_incr] = "customer_transit_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_transit_price"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_transit_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_transit_totalamount"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_salereturn_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_salereturn_price"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_salereturn_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_salereturn_totalamount"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_salereturn_remarks"; $row_incr++;

$mfl_col_name[$row_incr] = "customer_shipping_address"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_eway_no"; $row_incr++;

$mfl_col_name[$row_incr] = "customer_net_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_net_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_net_invamt"; $row_incr++;
$mfl_col_name[$row_incr] = "customer_diff_amt"; $row_incr++;


$mfl_col_name[$row_incr] = "vendor_group"; $row_incr++;
$mfl_col_name[$row_incr] = "sup_company_name"; $row_incr++;
$mfl_col_name[$row_incr] = "sup_brand_name"; $row_incr++;

$mfl_col_name[$row_incr] = "supplier_trnum"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_date"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_name"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_billno"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_itemname"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_snt_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_dcrcd_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_nof_bags"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_bag_weight"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_mortality"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_shortage"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_weaks"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_excess_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_chicks_pur"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_rcd_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_short_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_fre_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_rate"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_dis_per"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_dis_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_address"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_gst_no"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_gst_per"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_gst_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_cgst_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_sgst_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_igst_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_tgst_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_tcds_per"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_tcds_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_item_tamt"; $row_incr++;

$mfl_col_name[$row_incr] = "gross_pur_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "per_bag_rate"; $row_incr++;
$mfl_col_name[$row_incr] = "sup_icat_name"; $row_incr++;
$mfl_col_name[$row_incr] = "frt_crg_per"; $row_incr++;
$mfl_col_name[$row_incr] = "sup_net_rate"; $row_incr++;
$mfl_col_name[$row_incr] = "sup_iwise_amt"; $row_incr++;

$mfl_col_name[$row_incr] = "supplier_freight_type"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_freight_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_freight_pay_type"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_freight_pay_acc"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_freight_acc"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_round_off"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_finl_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_avg_price"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_remarks"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_warehouse"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_farm_batch"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_bag_code"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_bag_count"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_batch_no"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_exp_date"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_vehicle_code"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_driver_code"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_amt_basedon"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_ttype"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_gc_flag"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_direct_sale_flag"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_lqt_flag"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_grade_flag"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_addedemp"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_addedtime"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_updatedemp"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_updatedtime"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_receipt_date"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_receive_date"; $row_incr++;
$mfl_col_name[$row_incr] = "supplier_trlink"; $row_incr++;

$mfl_col_name[$row_incr] = "acc_fromwarehouse"; $row_incr++;
$mfl_col_name[$row_incr] = "acc_quantity"; $row_incr++;
$mfl_col_name[$row_incr] = "acc_paid_received"; $row_incr++;
$mfl_col_name[$row_incr] = "acc_cheque_no"; $row_incr++;
$mfl_col_name[$row_incr] = "acc_cheque_date"; $row_incr++;
$mfl_col_name[$row_incr] = "acc_remarks"; $row_incr++;
$mfl_col_name[$row_incr] = "acc_cr"; $row_incr++;
$mfl_col_name[$row_incr] = "acc_dr"; $row_incr++;
$mfl_col_name[$row_incr] = "acc_running_balance"; $row_incr++;

$mfl_col_name[$row_incr] = "hatchery_name"; $row_incr++;
$mfl_col_name[$row_incr] = "hatchery_hatcher_no"; $row_incr++;
$mfl_col_name[$row_incr] = "hatchery_setting_no"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_date"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_no"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_broken"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_damaged"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_misshapped"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_dirty"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_total_rejection"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_itemname"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_stock_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_stock_price"; $row_incr++;
$mfl_col_name[$row_incr] = "egg_grading_stock_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_date"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_no"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_hatchdate"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_itemname"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_broken"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_damaged"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_price"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_eggweight"; $row_incr++;
$mfl_col_name[$row_incr] = "tray_setting_expectedchicks"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_entrydate"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_entryno"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_itemname"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_eggset"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_infertile"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_count_no"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_count_per"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_cull_count"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_cull_per"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_hatcheryloss"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_mortality"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_saleablechick_count"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_saleablechick_per"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_expenses"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_chickprice"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_chickamt"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_avgchickwt"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_medvac_code"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_medvac_qty"; $row_incr++;
$mfl_col_name[$row_incr] = "hatch_medvac_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "gc_date"; $row_incr++;
$mfl_col_name[$row_incr] = "region_name"; $row_incr++;
$mfl_col_name[$row_incr] = "branch_name"; $row_incr++;
$mfl_col_name[$row_incr] = "line_name"; $row_incr++;
$mfl_col_name[$row_incr] = "supervisor_name"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_name"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_ccode"; $row_incr++;

$mfl_col_name[$row_incr] = "farm_type"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_capacity"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_status"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_image"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_state"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_district"; $row_incr++;

$mfl_col_name[$row_incr] = "farm_address"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_agreement_months"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_agreement_copy"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_Security_Cheque_1"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_Security_Cheque_2"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_other_documents"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_remarks"; $row_incr++;

$mfl_col_name[$row_incr] = "batch_name"; $row_incr++;
$mfl_col_name[$row_incr] = "book_no"; $row_incr++;
$mfl_col_name[$row_incr] = "placement_date"; $row_incr++;
$mfl_col_name[$row_incr] = "lifting_start_date"; $row_incr++;
$mfl_col_name[$row_incr] = "liquidation_date"; $row_incr++;
$mfl_col_name[$row_incr] = "gap_days"; $row_incr++;
$mfl_col_name[$row_incr] = "brood_age"; $row_incr++;
$mfl_col_name[$row_incr] = "brood_act_age"; $row_incr++;
$mfl_col_name[$row_incr] = "mean_age"; $row_incr++;
$mfl_col_name[$row_incr] = "chick_placed"; $row_incr++;
$mfl_col_name[$row_incr] = "opening_birdsno"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_chick_price"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_chick_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_1week_count"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_1week_per"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_30days_count"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_30days_per"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_30more_count"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_30more_per"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_count"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_cum"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_cum_per"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_per"; $row_incr++;
$mfl_col_name[$row_incr] = "mortality_img"; $row_incr++;
$mfl_col_name[$row_incr] = "yesturday_mort"; $row_incr++;
$mfl_col_name[$row_incr] = "previous_day_mort"; $row_incr++;
$mfl_col_name[$row_incr] = "culls_count"; $row_incr++;
$mfl_col_name[$row_incr] = "culls_per"; $row_incr++;
$mfl_col_name[$row_incr] = "culls_img"; $row_incr++;
$mfl_col_name[$row_incr] = "today_mort_count"; $row_incr++;
$mfl_col_name[$row_incr] = "lifting_efficiency"; $row_incr++;
$mfl_col_name[$row_incr] = "gc_week_no"; $row_incr++;
$mfl_col_name[$row_incr] = "gc_sale_sdate"; $row_incr++;
$mfl_col_name[$row_incr] = "gc_sale_edate"; $row_incr++;
$mfl_col_name[$row_incr] = "act_gc_prc"; $row_incr++;
$mfl_col_name[$row_incr] = "act_gc_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "gc_sale_inc_prc"; $row_incr++;
$mfl_col_name[$row_incr] = "gc_sale_inc_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "approved_gc_prc"; $row_incr++;
$mfl_col_name[$row_incr] = "approved_gc_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "gc_inc_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "gc_dec_amt"; $row_incr++;
$mfl_col_name[$row_incr] = "sold_birdsno"; $row_incr++;
$mfl_col_name[$row_incr] = "sold_birdswt"; $row_incr++;
$mfl_col_name[$row_incr] = "sold_birds_per"; $row_incr++;
$mfl_col_name[$row_incr] = "sold_batch_count"; $row_incr++;
$mfl_col_name[$row_incr] = "avg_bodywt"; $row_incr++;
$mfl_col_name[$row_incr] = "std_bodywt"; $row_incr++;
$mfl_col_name[$row_incr] = "available_birds"; $row_incr++;
$mfl_col_name[$row_incr] = "sold_perkg_price"; $row_incr++;
$mfl_col_name[$row_incr] = "sold_perno_price"; $row_incr++;
$mfl_col_name[$row_incr] = "sold_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "last_lifting_date"; $row_incr++;
$mfl_col_name[$row_incr] = "feed_brand_name"; $row_incr++;
$mfl_col_name[$row_incr] = "chick_received_from"; $row_incr++;
$mfl_col_name[$row_incr] = "chickin_hatchery_name"; $row_incr++;
$mfl_col_name[$row_incr] = "chickin_supplier_name"; $row_incr++;
$mfl_col_name[$row_incr] = "latest_feedin_brand"; $row_incr++;
$mfl_col_name[$row_incr] = "total_sale_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "sale_amt_wtcds"; $row_incr++;
$mfl_col_name[$row_incr] = "std_chick_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "std_chick_perkg_price"; $row_incr++;
$mfl_col_name[$row_incr] = "std_chick_perno_price"; $row_incr++;
$mfl_col_name[$row_incr] = "bird_shortage_count"; $row_incr++;
$mfl_col_name[$row_incr] = "bird_shortage_per"; $row_incr++;
$mfl_col_name[$row_incr] = "bird_excess_count"; $row_incr++;
$mfl_col_name[$row_incr] = "bird_excess_per"; $row_incr++;
$mfl_col_name[$row_incr] = "bird_shortexcess_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "feedopening_count"; $row_incr++;
$mfl_col_name[$row_incr] = "feedin_sector_count"; $row_incr++;
$mfl_col_name[$row_incr] = "feedin_sector_bags"; $row_incr++;
$mfl_col_name[$row_incr] = "feedin_sector_per"; $row_incr++;
$mfl_col_name[$row_incr] = "feedin_farm_count"; $row_incr++;
$mfl_col_name[$row_incr] = "feedin_farm_bags"; $row_incr++;
$mfl_col_name[$row_incr] = "feedin_farm_per"; $row_incr++;
$mfl_col_name[$row_incr] = "feedin_count"; $row_incr++;
$mfl_col_name[$row_incr] = "feedin_per"; $row_incr++;
$mfl_col_name[$row_incr] = "feedconsumed_count"; $row_incr++;
$mfl_col_name[$row_incr] = "feedcumconsumed_count"; $row_incr++;
$mfl_col_name[$row_incr] = "feedconsumed_per"; $row_incr++;
$mfl_col_name[$row_incr] = "feedconsumed_bags"; $row_incr++;
$mfl_col_name[$row_incr] = "feedout_count"; $row_incr++;
$mfl_col_name[$row_incr] = "feedout_per"; $row_incr++;
$mfl_col_name[$row_incr] = "feedout_farms_count"; $row_incr++;
$mfl_col_name[$row_incr] = "feedout_farms_bags"; $row_incr++;
$mfl_col_name[$row_incr] = "feedout_sector_count"; $row_incr++;
$mfl_col_name[$row_incr] = "feedout_sector_bags"; $row_incr++;
$mfl_col_name[$row_incr] = "feed_balance_count"; $row_incr++;
$mfl_col_name[$row_incr] = "feed_balance_bags"; $row_incr++;
$mfl_col_name[$row_incr] = "high_feedin_gc_sup"; $row_incr++;
$mfl_col_name[$row_incr] = "yesturday_feed"; $row_incr++;
$mfl_col_name[$row_incr] = "previous_day_feed"; $row_incr++;
$mfl_col_name[$row_incr] = "next_3days_feed"; $row_incr++;
$mfl_col_name[$row_incr] = "feed_balance_days"; $row_incr++;
$mfl_col_name[$row_incr] = "feed_img"; $row_incr++;
$mfl_col_name[$row_incr] = "std_feed_birdsno"; $row_incr++;
$mfl_col_name[$row_incr] = "std_feed_birdswt"; $row_incr++;
$mfl_col_name[$row_incr] = "std_feed_perbirdno"; $row_incr++;
$mfl_col_name[$row_incr] = "std_feed_price"; $row_incr++;
$mfl_col_name[$row_incr] = "std_feed_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_feed_birdsno"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_feed_birdswt"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_feed_perbirdno"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_feed_perbirdno2"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_cumfeed_perbirdno"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_feed_price"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_feed_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "std_fcr"; $row_incr++;
$mfl_col_name[$row_incr] = "fcr"; $row_incr++;
$mfl_col_name[$row_incr] = "std_cfcr"; $row_incr++;
$mfl_col_name[$row_incr] = "cfcr"; $row_incr++;
$mfl_col_name[$row_incr] = "day_gain"; $row_incr++;
$mfl_col_name[$row_incr] = "daily_prate"; $row_incr++;
$mfl_col_name[$row_incr] = "eef"; $row_incr++;
$mfl_col_name[$row_incr] = "old_fcr"; $row_incr++;
$mfl_col_name[$row_incr] = "old_cfcr"; $row_incr++;
$mfl_col_name[$row_incr] = "week1_bodywt"; $row_incr++;
$mfl_col_name[$row_incr] = "gc_grading"; $row_incr++;
$mfl_col_name[$row_incr] = "std_medicine_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "std_medicine_price"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_medicine_price"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_medicine_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "std_admin_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "std_admin_price"; $row_incr++;
$mfl_col_name[$row_incr] = "std_admin_swtprc"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_admin_price"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_admin_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_production_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_prodperkg_price"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_prodperno_price"; $row_incr++;
$mfl_col_name[$row_incr] = "grade"; $row_incr++;
$mfl_col_name[$row_incr] = "std_gc_perkg"; $row_incr++;
$mfl_col_name[$row_incr] = "act_gc_perkg"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_incentive"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_decentives"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_gc_perkg_price"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_gc_perkg_price2"; $row_incr++;
$mfl_col_name[$row_incr] = "rearing_charges"; $row_incr++;
$mfl_col_name[$row_incr] = "total_rearing_charges"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_tds_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "other_deduction"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_payable"; $row_incr++;
$mfl_col_name[$row_incr] = "final_farmer_payable"; $row_incr++;
$mfl_col_name[$row_incr] = "paid_to_farmer"; $row_incr++;
$mfl_col_name[$row_incr] = "pl_on_fmrpay"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_bank_name"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_bank_branch"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_bank_aname"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_bank_accno"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_bank_ifsc"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_aadharno"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_panno"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_pay_status"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_actpay_paid"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_actpay_diff"; $row_incr++;
$mfl_col_name[$row_incr] = "actual_prod_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "mgmt_perkg_price"; $row_incr++;
$mfl_col_name[$row_incr] = "profit_and_loss"; $row_incr++;
$mfl_col_name[$row_incr] = "remakrs"; $row_incr++;
$mfl_col_name[$row_incr] = "dieases_name"; $row_incr++;
$mfl_col_name[$row_incr] = "farm_location"; $row_incr++;
$mfl_col_name[$row_incr] = "entry_location"; $row_incr++;
$mfl_col_name[$row_incr] = "difference_kms"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_name"; $row_incr++;
$mfl_col_name[$row_incr] = "mobile_no1"; $row_incr++;
$mfl_col_name[$row_incr] = "mobile_no2"; $row_incr++;
$mfl_col_name[$row_incr] = "address"; $row_incr++;
$mfl_col_name[$row_incr] = "pan_no"; $row_incr++;
$mfl_col_name[$row_incr] = "aadhar_no"; $row_incr++;
$mfl_col_name[$row_incr] = "national_id"; $row_incr++;
$mfl_col_name[$row_incr] = "usc"; $row_incr++;
$mfl_col_name[$row_incr] = "service_no"; $row_incr++;
$mfl_col_name[$row_incr] = "farmer_group"; $row_incr++;
$mfl_col_name[$row_incr] = "tcds_type"; $row_incr++;
$mfl_col_name[$row_incr] = "tcds_per"; $row_incr++;
$mfl_col_name[$row_incr] = "bank_name"; $row_incr++;
$mfl_col_name[$row_incr] = "bank_branch"; $row_incr++;
$mfl_col_name[$row_incr] = "bank_ifsc_code"; $row_incr++;
$mfl_col_name[$row_incr] = "bank_account_no"; $row_incr++;

$mfl_col_name[$row_incr] = "lgpl_slno"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_odate"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_trnum"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ddate"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_cus_code"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_cus_name"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_cus_mobile"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_farm_name"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_farm_address"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_farm_latitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_farm_longitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_farm_location"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_farm_img"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_obirds"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_oavg_wt"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_oremarks"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_olatitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_olongitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_oimei"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_oroute_flag"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_otripflag"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_oallroute_flag"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_date"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_trnum"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_name"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_customer"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_farm"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rpc_name"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_distance"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_vehicle"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_driver"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_remarks"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_latitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_longitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_location"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_rp_imei"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_cemp_name"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_cemp_status"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_date"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_trnum"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_vehicle"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_driver"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_start_meter"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_start_mimg"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_end_meter"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_end_mimg"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_tot_kms"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_remarks"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_slatitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_slongitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_slocation"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_simei"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_elatitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_elongitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_elocation"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_eimei"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_trip_type"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_mtr_reading"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_ts_mtr_img"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_date"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_trnum"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_dcno"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_customer"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_farm"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_boxes"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_birds"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_tweight"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_eweight"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_nweight"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_remarks"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_latitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_longitude"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_location"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_imei"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_lb_farm_img"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_trnum"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_date"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_billno"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_customer"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_itemcode"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_jals"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_birds"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_tweight"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_eweight"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_nweight"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_avgwt"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_rate"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_amount"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_from_sector"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_to_sector"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_remarks"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_vehicle_type"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_vehicle"; $row_incr++;
$mfl_col_name[$row_incr] = "lgpl_jw_driver"; $row_incr++;
$mfl_col_name[$row_incr] = "pp_sent_birds"; $row_incr++;
$mfl_col_name[$row_incr] = "pp_sent_weight"; $row_incr++;

$mfl_col_name[$row_incr] = "addedemp"; $row_incr++;
$mfl_col_name[$row_incr] = "addedtime"; $row_incr++;

$sql_col_name1 = 'SHOW COLUMNS FROM `master_reportfields`'; $query_col_name1 = mysqli_query($sconn,$sql_col_name1); $existing_col_names = array(); $i = 0;
while($row_col_name1 = mysqli_fetch_assoc($query_col_name1)){ $existing_col_names[$i] = $row_col_name1['Field']; $i++; }
for($incr = 0;$incr < $row_incr;$incr++){
    $oincr = $incr - 1;
    if(in_array($mfl_col_name[$incr], $existing_col_names, TRUE) == ""){ $sql_col_name2 = "ALTER TABLE `master_reportfields` ADD `$mfl_col_name[$incr]` VARCHAR(15) NULL DEFAULT 'N:0:0' AFTER `$mfl_col_name[$oincr]`"; mysqli_query($sconn,$sql_col_name2); }
}

/*Client Support Program*/
$hostname = "213.165.245.128"; $db_users = "poulso6_userlist123"; $db_pass = "XBiypkFG2TF!9UB";

$aconn = new mysqli($hostname, $db_users, $db_pass);
$sql = "SHOW DATABASES"; $query = mysqli_query($aconn,$sql); $active_databases = array();
while($row = mysqli_fetch_assoc($query)){ $active_databases[$row["Database"]] = $row["Database"]; }
$db_list = implode("','",$active_databases);

$sql = "SELECT DISTINCT(dblist) as dbname FROM `log_useraccess` WHERE `account_access` IN ('BTS','ATS') AND `dblist` IN ('$db_list') ORDER BY `dbname` ASC"; $query = mysqli_query($conns,$sql);
while($row = mysqli_fetch_assoc($query)){
    $database_name = $row['dbname'];
    $tmp_conn = mysqli_connect($hostname, $db_users, $db_pass, $database_name);
    $table_head = "Tables_in_".$database_name;
    $sql1 = "SHOW TABLES WHERE ".$table_head." LIKE 'broiler_reportfields%';"; $query1 = mysqli_query($tmp_conn,$sql1); $count1 = mysqli_num_rows($query1);
    if($count1 > 0){
        $sql_col_name1 = 'SHOW COLUMNS FROM `broiler_reportfields`'; $query_col_name1 = mysqli_query($tmp_conn,$sql_col_name1); $existing_col_names = array(); $i = 0;
        while($row_col_name1 = mysqli_fetch_assoc($query_col_name1)){ $existing_col_names[$i] = $row_col_name1['Field']; $i++; }
        for($incr = 0;$incr < $row_incr;$incr++){
            $oincr = $incr - 1;
            if(in_array($mfl_col_name[$incr], $existing_col_names, TRUE) == ""){ $sql_col_name2 = "ALTER TABLE `broiler_reportfields` ADD `$mfl_col_name[$incr]` VARCHAR(15) NULL DEFAULT 'N:0:0' AFTER `$mfl_col_name[$oincr]`;"; mysqli_query($tmp_conn,$sql_col_name2); }
        }
    }
    else{
        /* Check and Create Table*/
        $sql3 = "
        CREATE TABLE `broiler_reportfields` (
        `id` int(100) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `field_name` varchar(500) DEFAULT NULL COMMENT 'File Reference Name',
        `field_href` varchar(500) DEFAULT NULL COMMENT 'File Link Code',
        `field_pattern` varchar(500) DEFAULT 'Active:Flag:Column',
        `user_access_code` varchar(1200) DEFAULT NULL COMMENT 'List of User Code',
        `column_count` int(100) NOT NULL DEFAULT '0',
        `active` int(100) NOT NULL DEFAULT '1',
        `dflag` int(100) NOT NULL DEFAULT '0'
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Master Report Access Fields';";
        if(!mysqli_query($tmp_conn,$sql3)){
            $error_details = mysqli_error($tmp_conn).".";
        }
        else{
            $sql_col_name1 = 'SHOW COLUMNS FROM `broiler_reportfields`'; $query_col_name1 = mysqli_query($tmp_conn,$sql_col_name1); $existing_col_names = array(); $i = 0;
            while($row_col_name1 = mysqli_fetch_assoc($query_col_name1)){ $existing_col_names[$i] = $row_col_name1['Field']; $i++; }
            for($incr = 0;$incr < $row_incr;$incr++){
                $oincr = $incr - 1;
                if(in_array($mfl_col_name[$incr], $existing_col_names, TRUE) == ""){ $sql_col_name2 = "ALTER TABLE `broiler_reportfields` ADD `$mfl_col_name[$incr]` VARCHAR(15) NULL DEFAULT 'N:0:0' AFTER `$mfl_col_name[$oincr]`"; mysqli_query($tmp_conn,$sql_col_name2); }
            }
        }
    }
}

$db_all_flag = 0; foreach($_POST['database'] as $dlist){ if($dlist == "all"){ $db_all_flag = 1; } }
//$report_name = $_POST['report_name'];
$file_id = $_POST['file_url'];

$sql1 = "SELECT * FROM `master_reportfields` WHERE `id` = '$file_id'"; $query1 = mysqli_query($sconn,$sql1);
while($row1 = mysqli_fetch_assoc($query1)){ $report_name = $row1['field_name']; $file_url = $row1['field_href']; }

$column_name_list = $column_number_list = "";
$i = 0; $col_number_list = array();
foreach($_POST['client_col_names'] as $cn){
    if($column_name_list == ""){
        $column_name_list = $cn;
    }
    else{
        $column_name_list = $column_name_list.",".$cn;
    }
    $col_name_list[$i] = $cn; $i++;
}
$i = 0;
foreach($_POST['client_ncol_nos'] as $cn){
    if($cn == ""){ $cn_value = "A:0:0"; } else{ $cn_value = "A:1:".$cn; }
    if($column_number_list == ""){
        $column_number_list = $cn_value;
    }
    else{
        $column_number_list = $column_number_list."','".$cn_value;
    }
    $col_number_list[$i] = $cn; $i++;
}
$set_act_val = "";
for($i = 0;$i <= sizeof($col_name_list);$i++){
    if($col_name_list[$i] != ""){
        if($col_number_list[$i] == ""){
            $cnl_value = "N:0:0";
        }
        else if($col_number_list[$i] == "0"){
            $cnl_value = "A:0:0";
        }
        else{
            $cnl_value = "A:1:".$col_number_list[$i];
        }
        if($set_act_val == ""){
            $set_act_val = " SET `".$col_name_list[$i]."` = '".$cnl_value."'";
        }
        else{
            $set_act_val = $set_act_val.", `".$col_name_list[$i]."` = '".$cnl_value."'";
        }
    }
}

$col_count = sizeof($col_number_list);
if($db_all_flag == 1){
    $sql1 = "SELECT DISTINCT(dblist) as dbname FROM `log_useraccess` WHERE `account_access` IN ('BTS','ATS') AND `dblist` IN ('$db_list') ORDER BY `dbname` ASC"; $query1 = mysqli_query($conns,$sql1);
    while($row1 = mysqli_fetch_assoc($query1)){
        $database_name = $row1['dbname'];
        $tmp_conn = mysqli_connect($hostname, $db_users, $db_pass, $database_name);
        
        $sql5 = "SELECT * FROM `main_access` WHERE `active` = '1' AND (`supadmin_access` = '1' OR `admin_access` = '1')";
        $query5 = mysqli_query($tmp_conn,$sql5); $uarr_list = array(); while($row5 = mysqli_fetch_assoc($query5)){ $uarr_list[$row5['empcode']] = $row5['empcode']; }
        $emp_list = implode("','",$uarr_list);

        $sql2 = "SELECT empcode FROM `log_useraccess` WHERE `dblist` LIKE '$database_name' AND `account_access` IN ('BTS','ATS') AND `flag` = '1' AND `id` IN (SELECT MIN(id) as id FROM `log_useraccess` WHERE `dblist` LIKE '$database_name' AND `account_access` IN ('BTS','ATS') AND `empcode` IN ('$emp_list') AND `flag` = '1')";
        $query2 = mysqli_query($conns,$sql2); $count2 = mysqli_num_rows($query2);
        if($count2 > 0){
            while($row2 = mysqli_fetch_assoc($query2)){ $admin_code = $row2['empcode']; }
            
            $sql3 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` = '$file_url' AND `user_access_code` = '$admin_code' AND `active` = '1'";
            $query3 = mysqli_query($tmp_conn,$sql3); $count3 = mysqli_num_rows($query3);
            if($count3 > 0){
                $set_value = "";
                for($incr = 2;$incr <= $row_incr;$incr++){
                    if($mfl_col_name[$incr] != ""){
                        if($set_value == ""){
                            $set_value = "SET `".$mfl_col_name[$incr]."` = 'N:0:0'";
                        }
                        else{
                            $set_value = $set_value.", `".$mfl_col_name[$incr]."` = 'N:0:0'";
                        }
                    }
                }
                $sql4 = "UPDATE `broiler_reportfields` ".$set_value.",`column_count` = '$col_count' WHERE `field_href` = '$file_url' AND `user_access_code` = '$admin_code' AND `active` = '1'";
                if(!mysqli_query($tmp_conn,$sql4)){ $error_details .= $database_name."@1@".mysqli_error($tmp_conn)."."; }
                else{
                    $sql5 = "UPDATE `broiler_reportfields` ".$set_act_val.",`field_name` = '$report_name',`field_href` = '$file_url',`column_count` = '$col_count' WHERE `field_href` = '$file_url' AND `user_access_code` = '$admin_code' AND `active` = '1'";
                    if(!mysqli_query($tmp_conn,$sql5)){ $error_details .= $database_name."@2@".mysqli_error($tmp_conn)."."; }
                }
            }
            else{
                $sql4 = "INSERT INTO `broiler_reportfields` (field_name,field_href,field_pattern,user_access_code,column_count,$column_name_list) VALUES ('$report_name','$file_url','Active:Flag:Column','$admin_code','$col_count','$column_number_list')";
                if(!mysqli_query($tmp_conn,$sql4)){ $error_details .= $database_name."@3@".mysqli_error($tmp_conn)."."; }
                else{ }
            }
        }
        else{ $error_details .= $database_name."@AdminError."; }
    }
}
else{
    $db_list = "";
    foreach($_POST['database'] as $dlist){
        if($db_list == ""){
            $db_list = $dlist;
        }
        else{
            $db_list = $db_list."','".$dlist;
        }
    }
    $sql1 = "SELECT DISTINCT(dblist) as dbname FROM `log_useraccess` WHERE `dblist` IN ('$db_list') AND `account_access` IN ('BTS','ATS') ORDER BY `dbname` ASC"; $query1 = mysqli_query($conns,$sql1);
    while($row1 = mysqli_fetch_assoc($query1)){
        $database_name = $row1['dbname'];
        $tmp_conn = mysqli_connect($hostname, $db_users, $db_pass, $database_name);
        
        $sql5 = "SELECT * FROM `main_access` WHERE `active` = '1' AND (`supadmin_access` = '1' OR `admin_access` = '1')";
        $query5 = mysqli_query($tmp_conn,$sql5); $uarr_list = array(); while($row5 = mysqli_fetch_assoc($query5)){ $uarr_list[$row5['empcode']] = $row5['empcode']; }
        $emp_list = implode("','",$uarr_list);
        
        $sql2 = "SELECT empcode FROM `log_useraccess` WHERE `dblist` LIKE '$database_name' AND `account_access` IN ('BTS','ATS') AND `flag` = '1' AND `id` IN (SELECT MIN(id) as id FROM `log_useraccess` WHERE `dblist` LIKE '$database_name' AND `account_access` IN ('BTS','ATS') AND `empcode` IN ('$emp_list') AND `flag` = '1')";
        $query2 = mysqli_query($conns,$sql2); $count2 = mysqli_num_rows($query2);
        if($count2 > 0){
            while($row2 = mysqli_fetch_assoc($query2)){ $admin_code = $row2['empcode']; }
            
            $sql3 = "SELECT * FROM `broiler_reportfields` WHERE `field_href` = '$file_url' AND `user_access_code` = '$admin_code' AND `active` = '1'";
            $query3 = mysqli_query($tmp_conn,$sql3); $count3 = mysqli_num_rows($query3);
            if($count3 > 0){
                $set_value = "";
                for($incr = 2;$incr <= $row_incr;$incr++){
                    if($mfl_col_name[$incr] != ""){
                        if($set_value == ""){
                            $set_value = "SET `".$mfl_col_name[$incr]."` = 'N:0:0'";
                        }
                        else{
                            $set_value = $set_value.", `".$mfl_col_name[$incr]."` = 'N:0:0'";
                        }
                    }
                }
                $sql4 = "UPDATE `broiler_reportfields` ".$set_value.",`column_count` = '$col_count' WHERE `field_href` = '$file_url' AND `user_access_code` = '$admin_code' AND `active` = '1'";
                if(!mysqli_query($tmp_conn,$sql4)){ $error_details .= $database_name."@1@".mysqli_error($tmp_conn)."."; }
                else{
                    $sql5 = "UPDATE `broiler_reportfields` ".$set_act_val.",`field_name` = '$report_name',`field_href` = '$file_url',`column_count` = '$col_count' WHERE `field_href` = '$file_url' AND `user_access_code` = '$admin_code' AND `active` = '1'";
                    if(!mysqli_query($tmp_conn,$sql5)){ $error_details .= $database_name."@2@".mysqli_error($tmp_conn)."."; }
                }
            }
            else{
                $sql4 = "INSERT INTO `broiler_reportfields` (field_name,field_href,field_pattern,user_access_code,column_count,$column_name_list) VALUES ('$report_name','$file_url','Active:Flag:Column','$admin_code','$col_count','$column_number_list')";
                if(!mysqli_query($tmp_conn,$sql4)){ $error_details .= $database_name."@3@".mysqli_error($tmp_conn)."."; }
                else{ }
            }
        }
        else{ $error_details .= $database_name."@AdminError."; }
    }
}

if($error_details != ""){
    echo $error_details;
}
else{
    header('location:broiler_display_masterreportfields.php?ccid='.$ccid);
}
?>
