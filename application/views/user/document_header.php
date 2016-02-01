
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
  <meta name="author" content="GeeksLabs">
  <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
  <link rel="shortcut icon" href="img/favicon.png">

  <title>Admin panel</title>

  <?php echo link_tag('bootstrap/css/bootstrap.min.css'); ?>
  <?php echo link_tag('bootstrap/css/bootstrap-theme.css'); ?>
  <?php echo link_tag('bootstrap/css/elegant-icons-style.css'); ?>
  <?php echo link_tag('bootstrap/css/font-awesome.min.css'); ?>
  <?php echo link_tag('bootstrap/css/style-responsive.css'); ?>
  <?php echo link_tag('bootstrap/css/style.css'); ?>
  <?php echo link_tag('bootstrap/css/content_style.css'); ?>
  <?php echo link_tag('bootstrap/datepicker/css/datepicker.css'); ?>


</head>

<body>
<!-- container section start -->
<section id="container" class="">


  <header class="header dark-bg">
    <h2><?=$_SESSION['user_email']?></h2>
  </header>
  <!--header end-->

  <!--sidebar start-->
  <aside>

  </aside>
  <!--sidebar end-->