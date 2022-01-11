<?php
    session_start();
    $_SESSION['cred'] = $_GET['m'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
          integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous" />
    <link rel="stylesheet" types="text/css" href="fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.jqueryui.min.css">

    <!-- CSS Independent -->
    <link rel="stylesheet" href="../public/css/invoice.css" />
    <link rel="stylesheet" href="../public/css/loader.css" />

    <title>Tagihan Pembayaran | Lumintu Event</title>
</head>

<body>
<nav class="navbar navbar-light bg-secondary">
  <div class="container">
    <a class="navbar-brand" href="#"><b>Invoice</b></a>
  </div>
</nav>
<br>
  <div class="container">
  <div class="row">
    <div class="col-sm-7">
      <div class="card data-cust">
        
      </div>
    </div>
    
    <div class="col-sm-5 order">
      <div class="card">
          <div class="card-body">   
            <h5 class="card-title"><b>Dream World Wide in Jogja</b></h5>
            <div class="row id-invoice">
              
            </div>
          <hr>
    
            <div class="row orderannya">
              
            </div>
            <hr>
            <div class="row totalnya">
              
            </div> 
          <br>
          <div class="bayar">
            <a href='../controller/invoiceProcess.php?m=<?php echo($_GET['m']); ?>' class="btn btn-primary bayar">Bayar</a> 
          </div>
      </div>
      </div>
    </div>

    </div>

  </div>
  
  </div>


<!-- Jquery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<!-- Bootstrap CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns"
        crossorigin="anonymous"></script>

<!-- Font Awesome CDN -->
<script src="https://use.fontawesome.com/7a7a4d3981.js"></script>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<!-- Independent JS -->
<script src="../public/js/invoice.js"></script>
</body>

</html>