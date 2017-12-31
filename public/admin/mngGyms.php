<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in() or !$session->is_session_valid()) {
    $session->logout();
    redirect_to("login.php");
}
if (request_is_same_domain() and request_is_post()) {
    $form_params = ['gymName', 'gymAddress', 'contactPerson', 'telephoneInput', 'emailInput'];
    $msg = "";
    // manage form submission for adding a gym
    if (isset($_POST['add_gym'])) {
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
            redirect_to("mngGyms.php");
        }
        $valid_form = new Form($form_params);
        $valid_post_params = $valid_form->allowed_post_params();
        $gym_name = mb_strtoupper($valid_post_params['gymName'], "UTF-8");
        $check_gym_name = Gym::find_gyms_by_name($gym_name);
        if ($check_gym_name > 0) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: gym name: ";
            $msg .= h($gym_name);
            $msg .= " already exists in the database. Try again.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $gym = new Gym();
            $gym->gym_name = $gym_name;
            $gym->address = $valid_post_params['gymAddress'];
            $gym->contact_person = $valid_post_params['contactPerson'];
            $gym->email = $valid_post_params['emailInput'];
            $gym->telephone = $valid_post_params['telephoneInput'];
            $msg = $gym->validate_gym_input_fields();
        }

        if ($msg == "") {
            $result = $gym->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Gym created with id: ";
            $msg .= h($result);

            $session->message($msg);
            redirect_to("mngGyms.php");
        } else {
            $session->message($msg);
            redirect_to("mngGyms.php");
        }
    }
    $form_edit_params = ['edit_gym_id', 'editgymName', 'editgymAddress', 'editContactPerson',
    'editTelephoneInput', 'editEmailInput'];
    // manage modal-form submission for editing gym details
    if (isset($_POST['edit_gym'])) {
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
            redirect_to("mngGyms.php");
        }
        $valid_edit_form = new Form($form_edit_params);
        $valid_post_edit_params = $valid_edit_form->allowed_post_params();

        $check_gym_id = has_presence($valid_post_edit_params['edit_gym_id']);
        $allowed_gym_ids = Gym::find_array_of_gym_ids();
        $check_gym_id_inclusion =
        has_inclusion_in($valid_post_edit_params['edit_gym_id'], $allowed_gym_ids);

        $gym_details = Gym::find_by_primary_key($valid_post_edit_params['edit_gym_id']);

        $gym_name_edit = mb_strtoupper($valid_post_edit_params['editgymName'], "UTF-8");
        $check_gym_name_edit_uniquness = Gym::find_gyms_by_name($gym_name_edit);

        if (!$check_gym_id or !$check_gym_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Gym: ";
            $msg .= h($valid_post_edit_params['edit_gym_id']);
            $msg .= " cannot be blank and must be a valid choice: ";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } elseif ($gym_details->gym_name != $gym_name_edit and $check_gym_name_edit_uniquness > 0) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: gym name: ";
            $msg .= h($gym_name_edit);
            $msg .= " already exists in the database. Try again.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $gym_edit = new Gym();
            $gym_edit->gym_id = $valid_post_edit_params['edit_gym_id'];
            $gym_edit->gym_name = $gym_name_edit;
            $gym_edit->address = $valid_post_edit_params['editgymAddress'];
            $gym_edit->contact_person = $valid_post_edit_params['editContactPerson'];
            $gym_edit->email = $valid_post_edit_params['editEmailInput'];
            $gym_edit->telephone = $valid_post_edit_params['editTelephoneInput'];
            $msg = $gym_edit->validate_gym_input_fields();
        }

        if ($msg == "") {
            $result = $gym_edit->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Gym with id: ";
            $msg .= $result;
            $msg .= " has been edited.";

            $session->message($msg);
            redirect_to("mngGyms.php");
        } else {
            $session->message($msg);
            redirect_to("mngGyms.php");
        }
    }
    $form_delete_params = ['gym_id'];
    // manage form submission for deleting a gym
    if (isset($_POST['delete_gym'])) {
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
            redirect_to("mngGyms.php");
        }
        $valid_form_delete = new Form($form_delete_params);
        $valid_post_delete_params = $valid_form_delete->allowed_post_params();
        $gym_id_delete = $valid_post_delete_params['gym_id'];
        $allowed_gym_ids = Gym::find_array_of_gym_ids();
        $check_gym_id_inclusion =
        has_inclusion_in($valid_post_delete_params['gym_id'], $allowed_gym_ids);

        $numbers_of_products_customers = IncomeReport::find_income_report_by_gym_id($gym_id_delete);

        $numbers_of_products_providers = OutcomeReport::find_outcome_report_by_gym_id($gym_id_delete);
        if (($numbers_of_products_customers > 0 or $numbers_of_products_providers > 0) and $check_gym_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Gym with ID: ";
            $msg .= h($gym_id_delete);
            $msg .= " has selled ";
            $msg .= h($numbers_of_products_customers);
            $msg .= " , ";
            $msg .= "and buyed: ";
            $msg .= h($numbers_of_products_providers);
            $msg .= " of products respectively. Cannot delete gym.";
            $msg .= "</li>";
            $msg .= "</ul>";
            $session->message($msg);
            redirect_to("mngGyms.php");
        } elseif (!$check_gym_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Gym: ";
            $msg .= h($gym_id_delete);
            $msg .= " cannot be blank and must be a valid choice: ";
            $msg .= "</li>";
            $msg .= "</ul>";
            $session->message($msg);
            redirect_to("mngGyms.php");
        } else {
            $gym_delete = new Gym();
            $gym_delete->gym_id = $gym_id_delete;
            $result_delete = $gym_delete->delete();
            if (!$result_delete) {
                $msg .= "Unable to delete gym with id: ";
                $msg .= h($gym_id_delete);
                $session->message($msg);
                redirect_to("mngGyms.php");
            } else {
                $log_msg  = "User ";
                $log_msg .= h($session->real_name);
                $log_msg .= " with ID: ";
                $log_msg .= h($session->login_user_id);
                $log_msg .= " as ";
                $log_msg .= h($session->role_name);
                $log_msg .= " deleted gym with ID: ";
                $log_msg .= h($gym_id_delete);
                logger("WARNING:", $log_msg);
                $msg .= $csrf_msg;
                $msg .= "Gym with id: ";
                $msg .= h($gym_id_delete);
                $msg .= " succesfully deleted.";
                $session->message($msg);
                redirect_to("mngGyms.php");
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
        <li class="breadcrumb-item active">Manage Gyms</li>
    </ol>
        <?php
        if (!empty($message) and strpos($message, 'created')) {
            echo '<div class="alert alert-success message_manage">';
            echo '<span class="glyphicon glyphicon-ok"></span> ';
            echo output_message($message);
        } elseif (!empty($message) and (strpos($message, 'edited') or strpos($message, 'deleted'))) {
            echo '<div class="alert alert-warning message_manage">';
            echo '<span class="glyphicon glyphicon-alert"></span> ';
            echo output_message($message);
        } elseif (!empty($message) and (strpos($message, 'Unable') or strpos($message, 'Fix'))) {
            echo '<div class="alert alert-danger message_manage">';
            echo '<span class="glyphicon glyphicon-info-remove"></span> ';
            echo output_message($message);
        } elseif (empty($message)) {
            echo '<div class="alert alert-info message_manage">';
            echo '<span class="glyphicon glyphicon-info-sign"></span> ';
            echo output_message("Add a new gym, edit or delete an existing one.");
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
        <h3 class="panel-title" style="color: #476692">Manage Gym</h3>
      </div><!-- /.panel-heading -->
        
      <form class="form-horizontal" data-toggle="validator"  action="mngGyms.php" method="post">
        <div class="panel-body">
          <div class="col-md-12">

            <div class="col-md-12 form_space">
              <h3 style="text-align:left;padding-bottom:5px">Add Gym</h3>
            </div><!-- /.col-md-12 form_space -->
            <div class="col-md-12" style="background-color: #ffa834; height: 3px;margin-bottom: 20px"></div>
                <?php echo $session->csrf_token_tag(); ?>
                <?php echo $session->csrf_token_tag_time(); ?>
              <div class="form-group row form_space has-feedback">
                <label for="gymName" class="col-xs-2 col-form-label">Gym Name:</label>
                <div class="col-xs-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-registration-mark"></span>
                        </span>
                        <input class="form-control" type="text" name="gymName" 
                        id="gymName" pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$" data-error="Gym name 
                        cannot be blank and is required." required>
                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div><!-- /.input-group -->
                    <div class="help-block with-errors">Enter gym name.</div>
                </div><!-- /.col-xs-10 -->
              </div><!-- /.form-group row form_space --> 

              <div class="form-group row form_space has-feedback">
                <label for="gymAddress" class="col-xs-2 col-form-label">Address:</label>
                <div class="col-xs-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-key"></i>
                        </span>
                        <input class="form-control" type="text" name="gymAddress" id="gymAddress"
                        pattern="[a-zA-Zα-ωΑ-Ω0-9 ]{1,}$" data-error="Address must consists
                        of latin, greek characters or numbers only.">
                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div><!-- /.input-group -->
                    <div class="help-block with-errors">Enter address.</div>
                </div><!-- /.col-xs-10 -->
              </div><!-- /.form-group row form_space -->

              <div class="form-group row form_space has-feedback">
                <label for="contactPerson" class="col-xs-2 col-form-label">Contact Person:</label>
                <div class="col-xs-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-user"></span>
                        </span>
                        <input class="form-control" type="text" name="contactPerson" 
                        id="contactPerson" pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$" data-error="Name 
                        must consists of latin, greek characters or numbers only.">
                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div><!-- /.input-group -->
                    <div class="help-block with-errors">Enter full name.</div>
                </div><!-- /.col-xs-10 -->
              </div><!-- /.form-group row form_space -->

              <div class="form-group row form_space has-feedback">
                <label for="telephoneInput" class="col-xs-2 col-form-label">Telephone:</label>
                <div class="col-xs-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-phone-alt"></span>
                        </span>
                        <input class="form-control" type="text" name="telephoneInput" 
                        id="telephoneInput" pattern="[0-9]{10}$" maxlength=10 
                        data-error="Phone number cannot be blank and must consists of 
                        exactly 10 digits." required>
                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div><!-- /.input-group -->
                    <div class="help-block with-errors">Enter telephone.</div>
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

        </div><!-- /.col-md-12 --> 
        </div><!-- /.panel-body -->
        <div class="panel-footer" id="add_user_panel">
            <div class="form-group" style="padding-right: 15px;margin-bottom: 0px;">
              <button type="submit" name="add_gym" class="btn btn-success pull-right">Add Gym
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
              <th>Gym Name</th>
              <th>Address</th>
              <th>Contact Person</th>
              <th>Telephone</th>
              <th>Email</th>
              <th>Edit</th>
              <th>Delete</th>
            </tr>
          </thead>
        </table>
      </div><!-- /.table-responsive -->

      </div><!-- /.panel-body -->

      <div class="panel-footer">
        Digest the displayed information at your own risk.
      </div><!-- /.panel-footer -->

    </div><!-- /.panel panel-default -->

  </div><!-- /.container-fluid -->
</div><!-- /#hld -->

<div class="modal fade" id="deletegym" tabindex="-1" role="dialog" aria-labelledby="deletegymLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="deletegymLabel"></h4>
      </div><!-- /.modal-header -->
      <div class="modal-body">
       
      </div><!-- /.modal-body -->
      <div class="modal-footer">
        <form action="mngGyms.php" id="form_delete" method="post">    
            <input type="hidden" class="form-control" name="gym_id" id="gym_id">
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_gym" class="btn btn-warning">Delete gym</button>
      </div><!-- /.modal-footer -->
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade bs-editgym-modal-lg" id="editgym" tabindex="-1" role="dialog" aria-labelledby="editgymLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="editgymLabel"></h4>
      </div>
      <div class="modal-body">
        <form action="mngGyms.php" data-toggle="validator" method="post">

          <input type="hidden" class="form-control" name="edit_gym_id" id="edit_gym_id">
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>
          <div class="form-group row form_space has-feedback">
            <label for="editgymName" class="col-xs-2 col-form-label">Gym Name:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-registration-mark"></span>
                    </span>
                    <input class="form-control" type="text" name="editgymName" 
                    id="editgymName_mng" pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$" data-error="Gym 
                    name cannot be blank and is required." required>
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter gym name.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space --> 

          <div class="form-group row form_space has-feedback">
            <label for="editgymAddress" class="col-xs-2 col-form-label">Address:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-key"></i>
                    </span>
                    <input class="form-control" type="text" name="editgymAddress" 
                    id="editgymAddress" pattern="[a-zA-Zα-ωΑ-Ω0-9 ]{1,}$" 
                    data-error="Address must consists of latin, greek characters or numbers only.">
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter address.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="editContactPerson" class="col-xs-2 col-form-label">Contact Person:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-user"></span>
                    </span>
                    <input class="form-control" type="text" name="editContactPerson" 
                    id="editContactPerson" pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$" data-error="Name 
                    must consists of latin or greek characters only.">
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter full name.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="editTelephoneInput" class="col-xs-2 col-form-label">Telephone:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                      <span class="glyphicon glyphicon-phone-alt"></span>
                    </span>
                    <input class="form-control" type="text" name="editTelephoneInput" 
                    id="editTelephoneInput" pattern="[0-9]{10}$" maxlength=10 
                    data-error="Phone number cannot be blank and must consists of 
                    exactly 10 digits." required>
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter telephone.</div>
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
                    id="editEmailInput" data-error="Email address must have a valid form.">
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter email address.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->                   
      </div><!-- /.modal-body -->
      <div class="modal-footer">
          <div class="form-group row form_space pull-right" style="padding-right: 15px">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_gym" class="btn btn-warning">Save Changes</button>
          </div><!-- /.form-group row form_space -->
      </div>
        </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$(function () { 
    $.ajax({
    url:'../../datatables_init/json_gym.php',
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
          {'data': 'gym_name'},
          {'data': 'address'},
          {'data': 'contact_person'},
          {'data': 'telephone'},
          {'data': 'email'},
          {'data': 'gym_id'},
          {'data': 'gym_id'},
        ],
        columnDefs: [
          {
            targets: 5,
            orderable: false,
            searchable: false,
            className: 'dt-body-center',
            render: function(data, type, full, meta) {
              edit_gym = '';
              edit_gym += '<button type="button" class="btn btn-warning" data-toggle="modal" data-target=".bs-editgym-modal-lg" data-gymid="';
              edit_gym += data;
              edit_gym += '">';
              edit_gym += '<span class="glyphicon glyphicon-edit" style="color: #000"></span>';
              edit_gym += '</button>';
              return edit_gym;
            }
          },
          {
            targets: 6,
            orderable: false,
            searchable: false,
            className: 'dt-body-center',
            render: function(data, type, full, meta) {
              delete_gym = '';
              delete_gym += '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deletegym" data-gymid="';
              delete_gym += data;
              delete_gym += '">';
              delete_gym += '<span class="glyphicon glyphicon-trash" style="color: #000"></span>';
              delete_gym += '</button>';
              return delete_gym;
            }
          }
        ],
      }); // DataTable ends here
    } // success function ends here
  });  // ajax request ends here

  $('#deletegym').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('gymid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    // console.log(recipient)
    $.ajax({
        url:'../../modal_ajax/modal_gym_json.php',
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
        var modal = $('#deletegym');
        modal.find('.modal-title').text('Delete Gym: ' + data.gym.gym_name);
        msg = '<p>';
        msg += 'Are you sure that you want to delete gym: ';
        msg += data.gym.gym_name;
        msg += '?';
        msg += '</p>';
        modal.find('.modal-body').html(msg);
        modal.find('.modal-footer input#gym_id[type="hidden"]').val(recipient);
      } // success function ends here
    }); // ajax request ends here
  }); // modal ends here

  $('#editgym').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('gymid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    $.ajax({
        url:'../../modal_ajax/modal_gym_json.php',
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
        var modal = $('#editgym');
        modal.find('.modal-title').text('Edit gym: ' + data.gym.gym_name);
        modal.find('.modal-body input#edit_gym_id[type="hidden"]').val(recipient);
        modal.find('.modal-body input[name="editgymName"]').val(data.gym.gym_name);
        modal.find('.modal-body input[name="editgymAddress"]').val(data.gym.address);
        modal.find('.modal-body input[name="editContactPerson"]').val(data.gym.contact_person);
        modal.find('.modal-body input[name="editTelephoneInput"]').val(data.gym.telephone);
        modal.find('.modal-body input[name="editEmailInput"]').val(data.gym.email);
      } // success function ends here
    }); // ajax request ends here
  }); // modal ends here
}); // function ends here
</script>
<?php
include('../../includes/layouts/footer.php');
if (isset($db)) {
    $db->close_connection();
}
?>

