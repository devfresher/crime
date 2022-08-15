<?php 
require_once 'includes/config.php';
require_once INCLUDES_DIR.'session_check.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    
	<!-- Favicon icon -->
	<link rel="canonical" href="<?php echo BASE_URL?>" />
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo BASE_URL?>assets/images/favicon.png">
    
	<title><?php echo $pageInfo->title ?> - Crime Management System</title>

	<link href="<?php echo BASE_URL ?>assets/vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo BASE_URL ?>assets/vendor/toastr/css/toastr.min.css" rel="stylesheet" type="text/css">

	<link href="<?php echo BASE_URL ?>assets/css/style.css" rel="stylesheet" type="text/css">