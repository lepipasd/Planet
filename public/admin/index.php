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
      <li class="breadcrumb-item active">Home</li>
    </ol>
    <div class="col-md-12 content">
        <h2 style="padding-top: 10px;">Choose an action <i class="fa fa-dashboard"></i></h2>
        <?php if (!empty($message)) : ?>
            <div class="container alert alert-success message">
            <span class="glyphicon glyphicon-ok"></span> 
            <?php echo output_message($message); ?>
        <?php else : ?>
            <?php
            $msg  = $session->real_name;
            $msg .= ", you are logged in to gym: ";
            $msg .= $session->gym_name;
            $msg .= " as ";
            $msg .= $session->role_name;
            $msg .= ".";
            ?>
            <div class="container alert alert-info message">
            <span class="glyphicon glyphicon-info-sign"></span> 
            <?php echo output_message($msg); ?>
        <?php endif; ?>
            </div><!-- /.alert alert-info message -->

        <div class="container pills">
            <?php if ($session->role_id != 1) : ?>
            <?php echo Navigation::display_administration_pills(0, 4, $session->role_id); ?>
            <?php endif; ?>
            <?php
            echo Navigation::display_administration_pills(0, 1, $session->role_id);
            echo Navigation::display_administration_pills(0, 2, $session->role_id);
            echo Navigation::display_administration_pills(0, 3, $session->role_id);
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
