<?php
require_once(LIB_PATH.DS.'initialize.php');
// require_once('../../includes/initialize.php');
?>
<header>
  <nav id="custom-bootstrap-menu" class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a href="index.php" class="navbar-brand glyphicon glyphicon-baby-formula"
        style="color: #ffd700"></a>
      </div><!-- /.navbar-header -->        

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="navbar">
        <ul class="nav navbar-nav pull-right">
          <li><a href="index.php" id="home" class="menu_buttons_icon btn btn-default glyphicon glyphicon-home"></a></li>              
          <li><a href="planetSocket.php" id="socket" class="menu_buttons_icon btn btn-default"><i class="fa fa-road"></i></a></li>              
            <?php
            if ($session->role_id != 1) {
                echo Navigation::display_menu(0, 4, $session->role_id);
            }
            echo Navigation::display_menu(0, 1, $session->role_id);
            echo Navigation::display_menu(0, 2, $session->role_id);
            echo Navigation::display_menu(0, 3, $session->role_id);
            ?>
          <li><a href="logout.php" id="log_out" class="menu_buttons_icon btn btn-default glyphicon glyphicon-off"></a></li>
        </ul>
      </div><!-- /.collapse navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav> 
</header>
