<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="plugins/toastr/toastr.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker-->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- Page specific script -->
<?php
date_default_timezone_set("Asia/Kolkata");
$today = date("d.m.Y");

//Fetch Column From Date Range Table
$sql='SHOW COLUMNS FROM `dataentry_daterange`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $c = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }

//Add Columns to Sales Table
if(in_array("emp_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `dataentry_daterange` ADD `emp_code` VARCHAR(250) NULL DEFAULT NULL AFTER `type`"; mysqli_query($conn,$sql); }
$days = 0;
$days1 = 1;
$empcode = $_SESSION['userid'];
$sql = "SELECT * FROM `dataentry_daterange` WHERE `active` = '1' AND dflag  = 0 AND emp_code = '$empcode' ";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
  if($row['type'] == "medicine_transfer"){
      $days = $row['days'];
  }else{
    $days = 0;
  }
}

  $from_date = date('d.m.Y', strtotime('-'.$days.' days', strtotime($today)));
  $upto_date = date('d.m.Y', strtotime('+'.$days1.' days', strtotime($today)));

?>
<script>
    $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });  
  var today = '<?php echo $today; ?>';
  var from_date = '<?php echo $from_date; ?>';
  var upto_date = '<?php echo $upto_date; ?>';
  $( ".datepicker_limit" ).datepicker({
    inline: true,
    showButtonPanel: false,
    changeMonth: true,
    changeYear: true,
    dateFormat: "dd.mm.yy",
    minDate: from_date,
    maxDate: today,
    beforeShow: function(){    
        $(".ui-datepicker").css('font-size', 12) 
    }
  });

  $( ".datepicker_plus_oneday" ).datepicker({
    inline: true,
    showButtonPanel: false,
    changeMonth: true,
    changeYear: true,
    dateFormat: "dd.mm.yy",
    maxDate: upto_date,
    beforeShow: function(){    
        $(".ui-datepicker").css('font-size', 12) 
    }
  });
  
  $( ".datepicker" ).datepicker({
    inline: true,
    showButtonPanel: false,
    changeMonth: true,
    changeYear: true,
    dateFormat: "dd.mm.yy",
    maxDate: today,
    beforeShow: function(){    
        $(".ui-datepicker").css('font-size', 12) 
    }
  });
  $( ".fin_datepicker" ).datepicker({
    inline: true,
    showButtonPanel: false,
    changeMonth: true,
    changeYear: true,
    dateFormat: "dd.mm.yy",
    beforeShow: function(){    
        $(".ui-datepicker").css('font-size', 12) 
    }
  });
  $( ".rc_datepicker" ).datepicker({
    inline: true,
    showButtonPanel: false,
    changeMonth: true,
    changeYear: true,
    dateFormat: "dd.mm.yy",
    beforeShow: function(){    
        $(".ui-datepicker").css('font-size', 12) 
    }
  });
  $( ".placementplan_datepicker" ).datepicker({ inline: true, showButtonPanel: false, changeMonth: true, changeYear: true, dateFormat: "dd.mm.yy", beforeShow: function(){ $(".ui-datepicker").css('font-size', 12) } });
  $('.select2').select2();
  //Date range picker
  $('#reservation').daterangepicker();
  /*
  // Disable inspect element
  $(document).bind("contextmenu",function(e) {
  e.preventDefault();
  });
  $(document).keydown(function(e){
      if(e.which === 123){
      return false;
      }
  });*/
  //$(document).on('select2:open', (e) => { var id = e.target.id; document.querySelector('div[name='+id+'_search]').focus(); });
  //$(document).on('select2:open', function(e) { document.querySelector(`[aria-controls="select2-${e.target.id}-results"]`).focus(); });
</script>