<?php 
require_once('../../includes/initialize.php');

if ($session->is_logged_in()) {
    redirect_to("index.php");
}

// Remember to give your form's submit tag a name="submit" attribute (button element)
// or a value="submit" when using input element.
if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

      // check database to see if username exists, and if so
      // retrieve his/her password
      // $found_user_pwd = LoginUser::find_gym_name_by_username($username);
      $found_user_pwd = LoginUser::find_user_by_username(strtolower($username));

    if ($found_user_pwd) {
        // the hashed pwd stored in db
        $existing_hash = $found_user_pwd->password;
        // hash the pwd that the user input
        $hash = crypt($password, $existing_hash);

        // check if the combination of username and password
        // is correct, and if so authenticate user
        $authenticate_user = LoginUser::authenticate($username, $hash);
        if ($authenticate_user) {
            $session->login($found_user_pwd);
            $msg  = $session->real_name;
            $msg .= ", you are logged in to gym: ";
            $msg .= $session->gym_name;
            $msg .= " as ";
            $msg .= $session->role_name;
            $session->message($msg);
            redirect_to("index.php");
        } else {
            // username/password combo was not found in the database
            $session->message("Username/password combination incorrect.");
            redirect_to("login.php");
        }
    } else {
        $session->message("Username does not exist.");
        redirect_to("login.php");
    }
} else {
    // Form has not been submitted
    $username = "";
    $password = "";
}
include('../../includes/layouts/header.php');
include('../../includes/layouts/login_menu.php');
?>
<div class="container-fluid main_center">
<div class="jumbotron page-header">
    <?php
    if (!empty($message)) {
        echo '<div class="alert alert-danger message" style="margin-bottom:0px">';
        echo '<span class="glyphicon glyphicon-exclamation-sign"></span> ';
        echo output_message($message);
    } else {
        echo '<div class="alert alert-info message" style="margin-bottom:0px">';
        echo '<span class="glyphicon glyphicon-info-sign"></span> ';
        echo output_message("Log in with your credentials (username / password)");
    }
    ?> 
 </div><!-- /.alert alert-info message -->
</div>
</div><!-- /.container-fluid main_center -->
<?php
include('../../includes/layouts/footer.php');
if (isset($db)) {
    $db->close_connection();
}
?>
