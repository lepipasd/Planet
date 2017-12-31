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
        <div class="navbar-brand glyphicon glyphicon-baby-formula" style="color: #ffd700">
        </div>
      </div><!-- /.navbar-header -->
         
        <form class="form-inline navbar-form navbar-right" action="login.php" method="post">
          <div class="form-group">                
            <input type="text" class="form-control" id="inputusername" name="username" maxlength="30"  placeholder="Enter username">
          </div><!-- /.form-group -->
          <div class="form-group">                
            <input type="password" class="form-control" id="inputpassword" name="password" placeholder="Enter password">
          </div><!-- /.form-group -->
          <div class="form-group">
            <button class="btn btn-default" type="submit" name="submit">Log in <span class="glyphicon glyphicon-log-in"></span></button>
          <!--<input type="image" src="../images/logBtn.png" border="0" alt="Submit" name="submit" value="submit"/>-->
          </div><!-- /.form-group -->
        </form>

      <!-- Collect the nav links, forms, and other content for toggling -->
    </div><!-- /.container-fluid -->
  </nav> 
</header>