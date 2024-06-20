<?php 
ob_start(); // Bắt đầu bộ đệm đầu ra
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
<!-- Thư viện Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

	
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<meta name="google-signin-client_id" content="903289929360-7umpc2inp7iov7sbsmnrmpiai16onig9.apps.googleusercontent.com">

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link rel="shortcut icon" type="image/png" href="../assets/image/logo.ico">

<!-- Thư viện Summernote -->


    <title>Phiêu Vũ </title>
</head>
<body >
    
<?php 

include('./modules/header.php'); ?>
<div class="main-container">
    
        <?php include('./modules/menu.php');
         ?>
        <?php include('./modules/main.php'); ?>
        <?php include('./modules/footer.php'); ?>
    </div>
</body>
</html>
