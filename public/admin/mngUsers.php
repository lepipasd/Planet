<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in() or !$session->is_session_valid()) {
    $session->logout();
    redirect_to("login.php");
}
if (request_is_same_domain() and request_is_post()) {
    $form_params = ['userName', 'password', 'name', 'surname', 'emailInput',
    'role_id', 'gym_id', 'csrf_token', 'csrf_token_time'];
    $msg = "";
    $csrf_msg = "";
    // manage form submission for adding a user
    if (isset($_POST['add_user'])) {
        if ($session->csrf_token_is_valid()) {
            $csrf_msg = "Valid form submission ";
            if ($session->csrf_token_is_recent()) {
                $csrf_msg .= "(recent). ";
            } else {
                $csrf_msg .= "(not recent). ";
            }
        } else {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: CRSF token missing or mismatched.";
            $msg .= "</li>";
            $msg .=  "</ul>";
            $session->message($msg);
            redirect_to("mngUsers.php");
        }
        $valid_form = new Form($form_params);
        $valid_post_params = $valid_form->allowed_post_params();

        $username = mb_strtolower($valid_post_params['userName'], "UTF-8");
        $password = $valid_post_params['password'];
        // first check username for uniquness
        $check_username = LoginUser::find_user_by_username($username);
        // check if password has min length 6 chars and contains at least one special char
        $check_pwd_length = has_length($password, ['min' => 6]);
        $check_pwd_format = has_format_matching($password, '/[^A-Za-z0-9]/');

        if ($check_username and ($check_pwd_length and $check_pwd_format)) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: username: ";
            $msg .= h($username);
            $msg .= " already exists in the database. Try again.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } elseif (!$check_username and (!$check_pwd_length or !$check_pwd_format)) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Password must be at least 6 characters and contain a special char.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } elseif ($check_username and (!$check_pwd_length or !$check_pwd_format)) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: username: ";
            $msg .= h($username);
            $msg .= " already exists in the database. Try again.";
            $msg .= "</li>";
            $msg .= "<li>";
            $msg .= "Password must be at least 6 characters and contain a special char.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $user = new LoginUser();
            $user->username = $username;
            $user->password = password_encrypt($password);
            $user->name = mb_strtoupper($valid_post_params['name'], "UTF-8");
            $user->surname = mb_strtoupper($valid_post_params['surname'], "UTF-8");
            $user->email = $valid_post_params['emailInput'];
            $user->role_id = $valid_post_params['role_id'];
            $user->gym_id = $valid_post_params['gym_id'];
            $msg = $user->validate_user_input_fields();
        }

        if ($msg == "") {
            $result = $user->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "User created with id: ";
            $msg .= h($result);

            $session->message($msg);
            redirect_to("mngUsers.php");
        } else {
            $session->message($msg);
            redirect_to("mngUsers.php");
        }
    }
    // valid fields submitted from edit form via post
    $form_edit_params = ['edit_login_user_id', 'edituserName', 'editname', 'editsurname',
    'editEmailInput', 'editroleid', 'edit_gym_id', 'csrf_token', 'csrf_token_time'];
    // manage modal-form submission for editing user details
    if (isset($_POST['edit_user'])) {
        if ($session->csrf_token_is_valid()) {
            $csrf_msg = "Valid form submission ";
            if ($session->csrf_token_is_recent()) {
                $csrf_msg .= "(recent). ";
            } else {
                $csrf_msg .= "(not recent). ";
            }
        } else {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: CRSF token missing or mismatched.";
            $msg .= "</li>";
            $msg .=  "</ul>";
            $session->message($msg);
            redirect_to("mngUsers.php");
        }
        $valid_edit_form = new Form($form_edit_params);
        $valid_post_edit_params = $valid_edit_form->allowed_post_params();
        // check submitted login_user_id if it is a valid choice and find
        // current username
        $login_user_id_edit = $valid_post_edit_params['edit_login_user_id'];
        $username_edit = mb_strtolower($valid_post_edit_params['edituserName'], "UTF-8");

        $check_login_user_id = has_presence($login_user_id_edit);
        $allowed_login_user_ids = LoginUser::find_array_of_login_user_ids();
        $check_login_user_id_inclusion =
        has_inclusion_in($login_user_id_edit, $allowed_login_user_ids);
        $check_username = false;
        if (!$check_login_user_id or !$check_login_user_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Login user ID: ";
            $msg .= h($login_user_id_edit);
            $msg .= " cannot be blank and must be a valid choice.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            // check if username is different from current username and if so
            // check for uniquness
            $current_user = LoginUser::find_by_primary_key($login_user_id_edit);

            if ($username_edit != $current_user->username) {
                $check_username = LoginUser::find_user_by_username($username_edit);
            }
        }
        
        if ($check_username) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: username: ";
            $msg .= h($username_edit);
            $msg .= " already exists in the database. Try again.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $user_pwd = LoginUser::find_by_primary_key($login_user_id_edit);
            $password = $user_pwd->password;
            $user_edit = new LoginUser();
            $user_edit->login_user_id = $login_user_id_edit;
            $user_edit->username = $username_edit;
            $user_edit->password = $password;
            $user_edit->name = mb_strtoupper($valid_post_edit_params['editname'], "UTF-8");
            $user_edit->surname = mb_strtoupper($valid_post_edit_params['editsurname'], "UTF-8");
            $user_edit->email = $valid_post_edit_params['editEmailInput'];
            $user_edit->role_id = $valid_post_edit_params['editroleid'];
            $user_edit->gym_id = $valid_post_edit_params['edit_gym_id'];
            $msg = $user_edit->validate_user_input_fields();
        }
        if ($msg == "") {
            $result = $user_edit->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "User with id: ";
            $msg .= h($result);
            $msg .= " has been edited.";

            $session->message($msg);
            redirect_to("mngUsers.php");
        } else {
            $session->message($msg);
            redirect_to("mngUsers.php");
        }
    }
    // valid fields submitted from delete form via post
    $form_delete_params = ['login_user_id', 'csrf_token', 'csrf_token_time'];
    // manage form submission for deleting a user
    if (isset($_POST['delete_user'])) {
        if ($session->csrf_token_is_valid()) {
            $csrf_msg = "Valid form submission ";
            if ($session->csrf_token_is_recent()) {
                $csrf_msg .= "(recent). ";
            } else {
                $csrf_msg .= "(not recent). ";
            }
        } else {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: CRSF token missing or mismatched.";
            $msg .= "</li>";
            $msg .=  "</ul>";
            $session->message($msg);
            redirect_to("mngUsers.php");
        }
        $valid_form_delete = new Form($form_delete_params);
        $valid_post_delete_params = $valid_form_delete->allowed_post_params();
        $login_user_id_delete = $valid_post_delete_params['login_user_id'];

        $check_login_user_id_delete = has_presence($login_user_id_delete);
        $allowed_login_user_ids = LoginUser::find_array_of_login_user_ids();
        $check_login_user_id_delete_inclusion =
        has_inclusion_in($login_user_id_delete, $allowed_login_user_ids);
        if (!$check_login_user_id_delete or !$check_login_user_id_delete_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Login user ID: ";
            $msg .= h($login_user_id_delete);
            $msg .= " cannot be blank and must be a valid choice.";
            $msg .= "</li>";
            $msg .=  "</ul>";

            $session->message($msg);
            redirect_to("mngUsers.php");
        } else {
            $user_delete = new LoginUser();
            $user_delete->login_user_id = $login_user_id_delete;
            $result_delete = $user_delete->delete();
            if (!$result_delete) {
                $msg .= "Unable to delete user with id: ";
                $msg .= h($login_user_id_delete);
                $session->message($msg);
                redirect_to("mngUsers.php");
            } else {
                $log_msg  = "User ";
                $log_msg .= h($session->real_name);
                $log_msg .= " with ID: ";
                $log_msg .= h($session->login_user_id);
                $log_msg .= " as ";
                $log_msg .= h($session->role_name);
                $log_msg .= " deleted user with ID: ";
                $log_msg .= h($login_user_id_delete);
                logger("WARNING:", $log_msg);
                $msg .= $csrf_msg;
                $msg .= "User with id: ";
                $msg .= h($login_user_id_delete);
                $msg .= " succesfully deleted.";
                $session->message($msg);
                redirect_to("mngUsers.php");
            }
        }
    }
}
include('../../includes/layouts/header.php');
include('../../includes/layouts/menu.php');
?>
<div id="hld">
  <div class="container-fluid main_center">
    <ol class="breadcrumb" style="margin-top: 20px;">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="management.php">Management</a></li>
        <li class="breadcrumb-item active">Manage Users</li>
    </ol> 
    <?php
    if (!empty($message) and strpos($message, 'created')) {
        echo '<div class="alert alert-success message_manage">';
        echo '<span class="glyphicon glyphicon glyphicon-ok"></span> ';
        echo output_message($message);
    } elseif (!empty($message) and (strpos($message, 'edited') or strpos($message, 'deleted'))) {
        echo '<div class="alert alert-warning message_manage">';
        echo '<span class="glyphicon glyphicon-alert"></span> ';
        echo output_message($message);
    } elseif (!empty($message) and (strpos($message, 'Unable') or strpos($message, 'Fix'))) {
        echo '<div class="alert alert-danger message_manage">';
        echo '<span class="glyphicon glyphicon-remove"></span> ';
        echo output_message($message);
    } elseif (empty($message)) {
        echo '<div class="alert alert-info message_manage">';
        echo '<span class="glyphicon glyphicon-info-sign"></span> ';
        echo output_message("Add a new user, edit user details or delete a user.");
    } else {
        echo '<div class="alert alert-danger message_manage">';
        echo '<span class="glyphicon glyphicon-info-remove"></span> ';
        echo output_message($message);
    }
    ?>
    </div><!-- /.alert alert-info message -->

    <div id='ajax_loader'>
        <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i></br>
    </div>

    <div class="panel panel-warning">
      <div class="panel-heading">
        <h3 class="panel-title" style="color: #476692">Management user</h3>
      </div><!-- /.panel-heading -->
      <form data-toggle="validator" class="form-horizontal" action="mngUsers.php" method="post">
        <div class="panel-body">

          <div class="col-md-12">

            <div class="col-md-12 form_space">
              <h3 style="text-align:left;padding-bottom:5px">Add user</h3>
            </div><!-- /.col-md-12 form_space -->
            <div class="col-md-12" style="background-color: #ffa834; height: 3px;margin-bottom: 20px"></div>
                <?php echo $session->csrf_token_tag(); ?>
                <?php echo $session->csrf_token_tag_time(); ?>
                <div class="form-group row form_space has-feedback">
                    <label for="userName" class="col-xs-2 col-form-label">User Name:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <i class="fa fa-user"></i>
                            </span>
                            <input class="form-control" type="text" name="userName" 
                            id="userName" data-error="Username cannot be blank." required>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter username.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space --> 

                <div class="form-group row form_space has-feedback">
                    <label for="password" class="col-xs-2 col-form-label">Password:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <i class="fa fa-lock"></i>
                            </span>
                            <input class="form-control" type="text" name="password" id="password" 
                            minlength=6 data-error="Password must have minimum length of 
                            6 characters and must contain at least one special char." required>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter password.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space --> 

                <div class="form-group row form_space has-feedback">
                    <label for="name" class="col-xs-2 col-form-label">Name:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <i class="fa fa-user-secret"></i>
                            </span>
                            <input class="form-control" type="text" name="name" id="name" 
                            pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$" data-error="Name 
                            must consists of latin or greek characters only." required>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter name.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="surname" class="col-xs-2 col-form-label">Surname:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <i class="fa fa-user-secret"></i>
                            </span>
                            <input class="form-control" type="text" name="surname" 
                            id="surname" pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$" data-error="Surname 
                            must consists of latin or greek characters only."required>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter surname.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="emailInput" class="col-xs-2 col-form-label">Email:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                              <i class="fa fa-envelope"></i>
                            </span>
                            <input class="form-control" type="email" name="emailInput" id="emailInput"
                            data-error="Email address must have a valid form.">
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter email address.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="role_id" class="col-xs-2 col-form-label">Role:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-cogs"></i>
                        </span>
                        <select class="selectpicker form-control show-tick" name="role_id" 
                        id="role_id">
                            <?php
                            if ($session->role_id == 4) {
                                echo '<option value=4>Admin</option>';
                            }
                            ?>
                            <option value=2>Manager</option>
                            <option value=1>User</option>
                        </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select role.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->
                
                <?php if ($session->role_id == 4) : ?>
                    <div class="form-group row form_space">
                        <label for="gym_id" class="col-xs-2 col-form-label">Gym Name:</label>
                        <div class="col-xs-10">
                            <div class="input-group">
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-registration-mark"></span>
                                </span>
                                <select class="selectpicker form-control show-tick" 
                                data-live-search="true" title="Select a Gym" name="gym_id" id="gym_id">
                                <?php
                                $gyms = Gym::find_gyms_for_select();
                                foreach ($gyms as $gym) :
                                ?>
                                <option value="<?php echo $gym->gym_id ?>"> 
                                <?php echo h($gym->gym_name); ?>
                                </option>
                                <?php
                                endforeach;
                                ?>
                                </select>
                            </div><!-- /.input-group -->
                            <div class="help-block with-errors">Select gym name.</div>
                        </div><!-- /.col-xs-10 -->
                    </div><!-- /.form-group -->
                <?php else : ?>
                    <div class="form-group row form_space has-feedback">
                        <label for="gym_name" class="col-xs-2 col-form-label">ΓΥΜΝΑΣΤΗΡΙΟ:</label>
                        <div class="col-xs-10">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-registration-mark"></span>
                                </span>
                                <input type="hidden" class="form-control" name="gym_id" 
                                id="gym_id" value="<?php echo $session->gym_id ?>">
                                <input type="text" class="form-control" 
                                value="<?php echo $session->gym_name ?>"
                                name="gym_name" id="gym_name" data-error="Gym name 
                                is automatic filled in, readonly field." readonly>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div><!-- /.input-group -->
                        <div class="help-block with-errors">Gym name.</div>
                        </div><!-- /.col-xs-10 -->
                    </div><!-- /.form-group row form_space -->
                <?php endif; ?>
          </div><!-- /.col-md-12 --> 
        </div><!-- /.panel-body -->

        <div class="panel-footer" id="add_user_panel">
            <div class="form-group" style="padding-right: 15px;margin-bottom: 0px;">
              <button type="submit" name="add_user" class="btn btn-success pull-right">Add user
                <span class="glyphicon glyphicon-plus-sign"></span></button>
            </div>
        </div><!-- /.panel-footer -->

      </form>
    </div><!-- /.panel panel-warning -->

    <div class="panel panel-info" style="margin-top: 10px;margin-bottom:60px;">

      <div class="panel-heading">
        <h3 class="panel-title" style="color: #476692">Choose export format <small>
          <span class="glyphicon glyphicon-export"></span>
        </small></h3>
      </div><!-- /.panel-heading -->

      <div class="panel-body">

      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="json_gym">
          <thead>
            <tr class="tablehead">
              <th>User Name</th>
              <th>Name</th>
              <th>Surname</th>
              <th>Email</th>
              <th>Role ID</th>
              <th>Gym Name</th>
              <th></th>
              <th></th>
              </tr>
          </thead>
          <tfoot>
            <tr>
              <th colspan="5"></th>
              <th colspan="1" id="searchgym"">Gym Name</th>
              <th colspan="2"></th>
            </tr>
          </tfoot>
        </table>
      </div><!-- /.table-responsive -->

      </div><!-- /.panel-body -->

      <div class="panel-footer">
        Digest the displayed information at your own risk.
      </div><!-- /.panel-footer -->

    </div><!-- /.panel panel-default -->

  </div><!-- /.container-fluid -->
</div><!-- /#hld -->

<div class="modal fade" id="deleteuser" tabindex="-1" role="dialog" aria-labelledby="deleteuserLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="deleteuserLabel"></h4>
      </div><!-- /.modal-header -->
      <div class="modal-body">
       
      </div><!-- /.modal-body -->
      <div class="modal-footer">
        <form action="mngUsers.php" method="post">    
            <input type="hidden" class="form-control" name="login_user_id" id="login_user_id">
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_user" class="btn btn-warning">Delete user</button>
      </div><!-- /.modal-footer -->
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade bs-edituser-modal-lg" id="edituser" tabindex="-1" role="dialog" aria-labelledby="edituserLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="edituserLabel"></h4>
      </div>
      <div class="modal-body">
        <form action="mngUsers.php" data-toggle="validator" method="post">
          <input type="hidden" class="form-control" name="edit_login_user_id" id="edit_login_user_id">
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>
          <div class="form-group row form_space has-feedback">
            <label for="edituserName" class="col-xs-2 col-form-label">User Name:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-user"></i>
                    </span>
                    <input class="form-control" type="text" name="edituserName" 
                    id="edituserName" data-error="Username cannot be blank." required>
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter username.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space --> 

          <div class="form-group row form_space has-feedback">
            <label for="editname" class="col-xs-2 col-form-label">Name:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-user-secret"></i>
                    </span>
                    <input class="form-control" type="text" name="editname" id="editname" 
                    pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$" data-error="Name 
                    must consists of latin or greek characters only." required>
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter name.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="editsurname" class="col-xs-2 col-form-label">Surname:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-user-secret"></i>
                    </span>
                    <input class="form-control" type="text" name="editsurname" 
                    id="editsurname" pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$" data-error="Surname 
                    must consists of latin or greek characters only."required>
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter surname.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="editEmailInput" class="col-xs-2 col-form-label">Email:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-envelope"></i>
                    </span>
                    <input class="form-control" type="email" name="editEmailInput" 
                    id="editEmailInput"
                    data-error="Email address must have a valid form.">
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter email address.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

           <div class="form-group row form_space">
            <label for="editroleid" class="col-xs-2 col-form-label">Role ID:</label>
            <div class="col-xs-10">
                <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-cogs"></i>
                </span>
                <select class="selectpicker form-control show-tick" name="editroleid" 
                id="editroleid">
                    <?php
                    if ($session->role_id == 4) {
                        echo '<option value=4>Admin</option>';
                    }
                    ?>
                    <option value=2>Manager</option>
                    <option value=1>User</option>
                </select>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Select role.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

            <?php if ($session->role_id == 4) : ?>
            <div class="form-group row form_space">
                <label for="edit_gym_id" class="col-xs-2 col-form-label">Gym Name:</label>
                <div class="col-xs-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-registration-mark"></span>
                        </span>
                        <select class="form-control" name="edit_gym_id" data-live-search="true" 
                        id="edit_gym_id">
                        </select>
                    </div><!-- /.input-group -->
                    <div class="help-block with-errors">Select gym name.</div>
                </div><!-- /.col-xs-10 -->
            </div><!-- /.form-group row form_space -->
            <?php else : ?>
                <div class="form-group row form_space has-feedback">
                    <label for="gym_name" class="col-xs-2 col-form-label">ΓΥΜΝΑΣΤΗΡΙΟ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-registration-mark"></span>
                            </span>
                            <input type="hidden" class="form-control" name="edit_gym_id" 
                            id="edit_gym_id" value="<?php echo $session->gym_id ?>">
                            <input type="text" class="form-control" 
                            value="<?php echo $session->gym_name ?>"
                            name="edit_gym_name" id="edit_gym_name" data-error="Gym name 
                            is automatic filled in, readonly field." readonly>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                    <div class="help-block with-errors">Gym name.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->
            <?php endif; ?>
      </div><!-- /.modal-body -->
      <div class="modal-footer">
          <div class="form-group row form_space pull-right"  style="padding-right: 15px">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_user" class="btn btn-warning">Save Changes</button>
          </div><!-- /.form-group row form_space -->
      </div>
        </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 

<script type="text/javascript">
$(function () { 
    // Setup - add a text input to each footer cell
$('#searchgym').each(function(){

  var title = $('#json_gym thead th').eq($(this).index()+4).text();
          
  $(this).html('<input type="text" placeholder="Search ' + title + '"/>');
});

$.ajax({
    url:'../../datatables_init/json_user.php',
    method: 'post',
    dataType: 'json',
    beforeSend: function(){
        $("#ajax_loader").show();
    },
    complete: function(){
    $("#ajax_loader").hide();
    },
    success: function(data) {
    var table = $('#json_gym').DataTable({
      "destroy": true,
      dom: 'lBfrtip',
      buttons: [
        'copy',
        'excel',
        'csv',
        'pdf',
        'print'
      ],
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
      data: data,
      columns: [
        {'data': 'username'},
        {'data': 'name'},
        {'data': 'surname'},
        {'data': 'email'},
        {'data': 'role_name'},
        {'data': 'gym_name'},
        {'data': 'login_user_id'},
        {'data': 'login_user_id'},
      ],
      columnDefs: [
        {
          targets: 6,
          orderable: false,
          searchable: false,
          className: 'dt-body-center',
          render: function(data, type, full, meta) {
            edit_user = '';
            edit_user += '<button type="button" class="btn btn-warning" data-toggle="modal"'
            edit_user += 'data-target=".bs-edituser-modal-lg" data-userid="';
            edit_user += data;
            edit_user += '">';
            edit_user += '<span class="glyphicon glyphicon-edit" style="color: #000"></span>';
            edit_user += '</button>';
            return edit_user;
          }
        },
        {
          targets: 7,
          orderable: false,
          searchable: false,
          className: 'dt-body-center',
          render: function(data, type, full, meta) {
            delete_user = '';
            delete_user += '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteuser" data-userid="';
            delete_user += data;
            delete_user += '">';
            delete_user += '<span class="glyphicon glyphicon-trash" style="color: #000"></span>';
            delete_user += '</button>';
            return delete_user;
          }
        }
      ],
    }); // DataTable ends here
    table.column(5).every(function(){
      var tableColumn = this;

      $(this.footer()).find('input').on('keyup change', function(){

        var term = $(this).val();
          // regExSearch = '^' + term +'$';
          // regExSearch_all = '[\s\S]*';
        tableColumn.search(term).draw();
          // if (term) {
          //   tableColumn.search(regExSearch, true, false).draw();
          // } else {
          //   tableColumn.search(regExSearch_all, true, false).draw();
          // }                      
      });          
    });
  } // success function ends here
});  // ajax request ends here

$('#deleteuser').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('userid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    // console.log(recipient);
    // $('#deleteuser').find('.modal-title').text('Delete user: ');
    $.ajax({
        url:'../../modal_ajax/modal_user_json.php',
        method: 'post',
        dataType: 'json',
        data: {id: recipient},
        beforeSend: function(){
            $.spin('modal');
        },
        complete: function(){
            $.spin('false');
        },
        success: function(data) {
        // console.log(data);
        var modal = $('#deleteuser');
        modal.find('.modal-title').text('Delete user: ' + data.user.username);
        modal.find('.modal-body').text('Are you sure that you want to delete user: ' + data.user.name + ' ' +data.user.surname + ' ?');
        modal.find('.modal-footer input#login_user_id[type="hidden"]').val(recipient);
      } // success function ends here
    }); // ajax request ends here
  }); // modal ends here

$('#edituser').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('userid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    $.ajax({
        url:'../../modal_ajax/modal_user_json.php',
        method: 'post',
        dataType: 'json',
        data: {id: recipient},
        beforeSend: function(){
            $.spin('modal');
        },
        complete: function(){
            $.spin('false');
        },
        success: function(data) {
        console.log(data)
        var modal = $('#edituser');
        modal.find('.modal-title').text('Edit User: ' + data.user.username);
        modal.find('.modal-body input#edit_login_user_id[type="hidden"]').val(recipient);
        modal.find('.modal-body input[name="edituserName"]').val(data.user.username);
        modal.find('.modal-body input[name="editname"]').val(data.user.name);
        modal.find('.modal-body input[name="editsurname"]').val(data.user.surname);
        modal.find('.modal-body input[name="editEmailInput"]').val(data.user.email);
        $('.selectpicker#editroleid').selectpicker('val', data.user.role_id);

        var select = document.getElementById("edit_gym_id");

        $.each(data.gyms, function(key, value) {
          var option = document.createElement("option");
          option.text = value.gym_name;
          option.value = value.gym_id;
          select.appendChild(option);
        });

        $('#edit_gym_id').addClass('selectpicker');
        $('#edit_gym_id').addClass(' show-tick');
        $('#edit_gym_id').selectpicker('val', data.user.gym_id);

        $('#edit_gym_id').selectpicker({
          style: 'btn-default',
          size: 4
        });
      } // success function ends here
    }); // ajax request ends here
  }); // modal ends here
    
    $('.selectpicker#role_id').selectpicker({
      style: 'btn-default',
      size: 4
    });
  $('.selectpicker#role_id').selectpicker('val', 1);
}); // function ends here
</script>
<?php
include('../../includes/layouts/footer.php');
if (isset($db)) {
    $db->close_connection();
}
?>

