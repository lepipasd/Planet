<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in() or !$session->is_session_valid()) {
    $session->logout();
    redirect_to("login.php");
}

include('../../includes/layouts/header.php');
include('../../includes/layouts/menu.php');
?>
<div class="container-fluid main_center">
  <ol class="breadcrumb" style="margin-top: 20px;">
    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
    <li class="breadcrumb-item active">Statistics</li>
  </ol>
  <div class="col-md-12 content">
    <h2 style="padding-top: 10px;">View <span class="glyphicon glyphicon-eye-open"></span></h2>
    <div class="container alert alert-info message">
      <span class="glyphicon glyphicon-info-sign"></span>
        <?php
          $msg  = "View stats and graphs.";
          echo !empty($message) ? output_message($message) : output_message($msg);
        ?>
    </div><!-- /.alert alert-info message -->
    <div class="container pills">
        <?php
          echo Navigation::display_administration_pills(1, 3, $session->role_id);
        ?>
    </div><!-- /.container.pills -->
  </div><!-- /.jumbotron page-header -->
</div><!-- /.container-fluid .main_center--> 
<?php
include('../../includes/layouts/footer.php');
if (isset($db)) {
    $db->close_connection();
}
?>

