<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('includes/initialize.php');
if (!$session->is_logged_in()) {
    redirect_to("public/admin/login.php");
} else {
    redirect_to("public/admin/index.php");
}

include('../../includes/layouts/header.php');
include('../../includes/layouts/menu.php');
?>

  <div class="container-fluid main_center">
    <div class="jumbotron page-header">

    <div class="alert alert-info message">
      <span class="glyphicon glyphicon-info-sign"></span>
        <?php 
        echo !empty($message) ? output_message($message) : output_message($session->real_name . ", you are logged in to gym: " . $session->gym_name);
        ?>
    </div><!-- /.alert alert-info message -->

      <div class="container pills">
            <?php
            echo Navigation::display_role_btn($session->role_id);
            ?>
      </div><!-- /.container.pills -->
          </div><!-- /.innercontent --> 
        </div><!-- /.col-md-6 center --> 
      </div><!-- /.col-md-12 content box -->

    </div><!-- /.jumbotron page-header -->
  </div><!-- /.container-fluid .main_center-->
    
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="../js/jquery-2.2.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../js/bootstrap.min.js"></script>
<script src="../js/jquery.progresstimer.js"></script>
<script src="../js/leaflet.js"></script>
<script src="../js/leaflet.draw.js"></script>
<script type="text/javascript" src="../media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../js/buttons.flash.min.js"></script>
<script type="text/javascript" src="../js/jszip.min.js"></script>
<script type="text/javascript" src="../js/pdfmake.min.js"></script>
<script type="text/javascript" src="../js/vfs_fonts.js"></script>
<script type="text/javascript" src="../js/buttons.html5.min.js"></script> 
<script type="text/javascript" src="../js/buttons.print.min.js"></script>
<?php
include('../../includes/layouts/footer.php');
if (isset($db)) {
    $db->close_connection();
}
?>