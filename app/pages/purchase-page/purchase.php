<?php

if (php_sapi_name() === 'cli') {
    $isCli = true;
} else {
    $isCli = false;
}

$view_vars['loader'] = '../../../system/assets/loaders/Iphone-spinner-2.gif';
// CSS
$view_vars['add_css'] = '
<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
/* Container styling */
.container {
    max-width: 700px;
    margin: 40px auto;
    padding: 30px;
    background-color: #f9f9f9;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

/* Heading styling */
.container h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
    font-weight: 600;
}

/* Form group spacing */
.form-group {
    margin-bottom: 20px;
}

/* Input styling */
.form-control {
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.2);
    outline: none;
}

/* Dropdown styling */
#currencySelect {
    border-radius: 8px;
    padding: 10px;
    font-size: 16px;
}

/* Button styling */
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: 500;
    transition: background-color 0.2s, transform 0.1s;
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: translateY(-1px);
}

/* Surcharge & discount styling */
#surcharge, #discount {
    font-weight: 600;
    color: #555;
}

/* Confirmation alert */
#confirmation {
    margin-top: 30px;
}

#confirmation .alert {
    border-radius: 12px;
    padding: 20px;
    font-size: 16px;
}

#order-details p {
    margin: 5px 0;
}

#overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    z-index: 9998;
}

#loader {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
    width: 80px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    .form-control, #currencySelect {
        font-size: 14px;
    }

    .btn-primary {
        width: 100%;
        font-size: 14px;
    }
}
</style>
';

// JS
$view_vars['add_js'] = '
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.2/dist/purify.min.js"></script>
<script src="/client/js/purchase.js?v=' . JS_VERSION . '"></script>
<script src="https://cdn.jsdelivr.net/gh/your-repo/js/datatable-formatting-fix.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
';

extract($view_vars);

if (!$isCli) {
    include 'views/purchase.php';
}
