<?php
//header_head2.php
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PoulSoft Solutions</title>
<link rel="icon" href="images/poulsoftlogo_2.png" type="image/ico" />

<!-- Bootstrap 4.0.0 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">

<!-- Font Awesome 6.7.1 -->
<link rel="stylesheet" href="FAW-6.7.1/css/all.min.css">

<!-- jQuery UI Datepicker -->
<link rel="stylesheet" href="datepicker/jquery-ui.css">

<!-- DataTables v2.3.2 -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.0/css/buttons.dataTables.min.css">

<style>
        #dboard { width: 100%; min-height: 100%; overflow: auto; } .treeview-menu { min-width:210px; }
        .select2-container .select2-selection--single{ padding-top: 0px; box-sizing:border-box; cursor:pointer; display:block; height:25px; user-select:none; -webkit-user-select:none; }
		.select2-container--default .select2-selection--single{background-color:#fff;border:1px solid #aaa;border-radius:4px}
		.select2-container--default .select2-selection--single .select2-selection__rendered{color:#444;line-height:18px}
		.select2-container--default .select2-selection--single .select2-selection__clear{cursor:pointer;float:right;font-weight:bold}
		.select2-container--default .select2-selection--single .select2-selection__placeholder{color:#999}
		.select2-container--default .select2-selection--single .select2-selection__arrow{height:25px;position:absolute;top:1px;right:1px;width:20px}
		.select2-container--default .select2-selection--single .select2-selection__arrow b{border-color:#888 transparent transparent transparent;border-style:solid;border-width:5px 4px 0 4px;height:0;left:50%;margin-left:-4px;margin-top:-2px;position:absolute;top:50%;width:0}
		.form-control { padding-left:0; padding-left:5px; height: 25px; font-size:15px; color:black; }
        /* width */
        ::-webkit-scrollbar {
            width: 5px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
        background: #f1f1f1; 
        }
        
        /* Handle */
        ::-webkit-scrollbar-thumb {
        background: #888; 
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
        background: #555; 
        }
        @media screen and (min-width: 80px and max-width: 480px){
            .label-addition {
                display: inline;
            }
        }
    </style>
    <style>
        /* Floating Pop-up Styling */
        #cus_ascrn,#itm_ascrn {
            display: none;
            position: fixed;
            top: 5%;
            left: 50%;
            transform: translate(-50%, 0);
            width: 25%;
            background: white;
            box-shadow: 0px -3px 10px rgba(0,0,0,0.3);
            padding: 20px;
            border-radius: 10px 10px 0 0;
            z-index: 1000;
            position: absolute; /* Needed for dragging */
            cursor: move; /* Show move cursor */
        }

        /* Close Icon Styling */
        .cus-clsicon, .itm-clsicon {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 15px;
            cursor: pointer;
            color: #333;
        }

        .cus-clsicon:hover {
            color: red;
        }
        .itm-clsicon:hover {
            color: red;
        }
    </style>

  <style>
    .dt-buttons {
      margin-bottom: 10px;
    }
  </style>

  <style>
/* Style the buttons container */
.dt-buttons {
  background-color:rgb(253, 254, 255); /* Dark gray */
  padding: 8px;
  border-radius: 4px;
  display: inline-flex; /* Ensure buttons are tightly packed */
  gap: 0; /* Remove spacing between buttons */
}

/* Style individual buttons */
.dt-button {
  background-color: #6c757d !important;
  color: white !important;
  border: none !important;
  padding: 6px 12px;
  border-radius: 3px;
  font-size: 0.875rem;
  margin: 0 !important; /* Remove any margin */
}

/* Button hover effect */
.dt-button:hover {
  background-color: #5a6268 !important;
}
</style>

