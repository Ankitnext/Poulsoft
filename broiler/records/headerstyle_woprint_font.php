<?php
echo '  <style>
.col-md-6 { position: relative;  left: 200px; max-width: 0%; }
.col-md-5{ position: relative;  left: 200px; }
div.dataTables_wrapper div.dataTables_filter { text-align: left; }
table thead,
table tfoot {
    position: sticky;
}
table thead {
inset-block-start: 0; /* "top" */
}
table tfoot {
inset-block-end: 0; /* "bottom" */
}
</style>';
echo '<style>body { left:0;width:auto;overflow:auto; } table { white-space: nowrap; }
table.tbl { left:0;margin-right: auto;visibility:visible; }
table.tbl2 { left:0;margin-right: auto; }
.tbl table, .tbl tr, .tbl th, .tbl td { padding:3px 5px;font-size:'.$font_size.';color:#000000;border:0.1vh solid #000000;border-collapse:collapse; }
.tbl2 table, .tbl2 tr, .tbl2 th, .tbl2 td { padding:3px 5px;font-size:'.$font_size.';color:#000000;border:0.1vh solid #000000;border-collapse:collapse; }
.thead1 { background-image: linear-gradient(#9CC2D5,#9CC2D5); box-shadow: 0px 0px 10px #EAECEE; }
.thead2 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
.thead3 { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
.thead4 { background-image: linear-gradient(#9CC2D5,#9CC2D5); }
.tbody1 { background-image: linear-gradient(#FFFFFF,#FFFFFF); color:#000000; }
.report_head { background-image: linear-gradient(#9cc2d5,#9cc2d5); }
.tbody1 tr:hover { background-image: linear-gradient(#FADBD8,#FADBD8); }</style>';

echo "<style>body{ font-family: ".$font_stype."; } </style>";