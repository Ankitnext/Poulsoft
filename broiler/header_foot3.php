<?php
// header_foot3.php
?>
<!-- jQuery (only once - full version) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Popper.js for Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>

<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

<!-- Select2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- jQuery UI Datepicker -->
<script src="datepicker/jquery-ui.js"></script>

<!-- DataTables Core -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css">
<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>

<!-- DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.0/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/3.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.0/js/buttons.colVis.min.js"></script>

<!-- Optional: Excel and PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Initialize Select2, DatePicker, and DataTables -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Initialize Select2
    $('.select2').select2();

    // Initialize Datepicker
    $(".datepicker").datepicker({
      inline: true,
      showButtonPanel: false,
      changeMonth: true,
      changeYear: true,
      dateFormat: "dd.mm.yy",
      beforeShow: function () {
        $(".ui-datepicker").css('font-size', 12);
      }
    });

    // Initialize DataTable
    new DataTable('#example', {
      layout: {
        topStart: 'buttons'
      },
      buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis'],
      columnDefs: [{
        targets: 4,
        render: {
          display: function (data) {
            const d = new Date(data);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}.${month}.${year}`;
          },
          sort: function (data) {
            return data;
          }
        }
      }]
    });
  });
</script>
