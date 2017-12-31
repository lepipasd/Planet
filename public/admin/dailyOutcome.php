<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in() or !$session->is_session_valid()) {
    $session->logout();
    redirect_to("login.php");
}

if (request_is_same_domain() and request_is_post()) {
    // the allowed params from form post submission
    $form_params = ['ap', 'gym_id', 'login_user_id', 'provider_id', 'shift_id',
        'price_paied', 'reason_outcome_id', 'outcome_id', 'comments', 'csrf_token', 'csrf_token_time'];
    $msg = "";
    $csrf_msg = "";
    // manage form submission for adding a customer
    if (isset($_POST['create_outcome_record'])) {
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
            redirect_to("dailyOutcome.php");
        }
        $valid_form = new Form($form_params);
        $valid_post_params = $valid_form->allowed_post_params();
        if ($valid_post_params['gym_id'] != $session->gym_id or
            $valid_post_params['login_user_id'] != $session->login_user_id) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: gym_id or login_user_id was hacked.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $outcome_report = new OutcomeReport();
            $outcome_report->ap = $valid_post_params['ap'];
            $outcome_report->gym_id = $valid_post_params['gym_id'];
            $outcome_report->login_user_id = $valid_post_params['login_user_id'];
            $outcome_report->provider_id = $valid_post_params['provider_id'];
            $outcome_report->shift_id = $valid_post_params['shift_id'];
            $outcome_report->price_paied = $valid_post_params['price_paied'];
            $outcome_report->reason_outcome_id = $valid_post_params['reason_outcome_id'];
            $outcome_report->outcome_id = $valid_post_params['outcome_id'];
            $outcome_report->comments = $valid_post_params['comments'];
            $msg = $outcome_report->validate_user_input_outcome();
        }

        if ($msg == "") {
            $result = $outcome_report->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Outcome Report created with id: ";
            $msg .= h($result);

            $session->message($msg);
            redirect_to("dailyOutcome.php");
        } else {
            $session->message($msg);
            redirect_to("dailyOutcome.php");
        }
    }
    $form_edit_params = ['edit_record_id', 'edit_ap', 'edit_gym_id', 'edit_login_user_id',
    'edit_provider_id', 'edit_shift_id', 'edit_outcome_id', 'edit_price_paied',
    'edit_reason_outcome_id', 'edit_comments'];
    // manage modal-form submission for editing user details
    if (isset($_POST['edit_outcome_record'])) {
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
            redirect_to("dailyOutcome.php");
        }
        $valid_form_edit = new Form($form_edit_params);
        $valid_post_edit_params = $valid_form_edit->allowed_post_params();
        if ($valid_post_edit_params['edit_gym_id'] != $session->gym_id or
            $valid_post_edit_params['edit_login_user_id'] != $session->login_user_id) {
            $passed_validation_edit_tests = false;
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: gym_id or login_user_id was hacked.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $outcome_report = new OutcomeReport();
            $outcome_report->outcome_report_id = $valid_post_edit_params['edit_record_id'];
            $outcome_report->ap = $valid_post_edit_params['edit_ap'];
            $outcome_report->gym_id = $valid_post_edit_params['edit_gym_id'];
            $outcome_report->login_user_id = $valid_post_edit_params['edit_login_user_id'];
            $outcome_report->provider_id = $valid_post_edit_params['edit_provider_id'];
            $outcome_report->shift_id = $valid_post_edit_params['edit_shift_id'];
            $outcome_report->price_paied = $valid_post_edit_params['edit_price_paied'];
            $outcome_report->reason_outcome_id = $valid_post_edit_params['edit_reason_outcome_id'];
            $outcome_report->outcome_id = $valid_post_edit_params['edit_outcome_id'];
            $outcome_report->comments = $valid_post_edit_params['edit_comments'];
            $msg = $outcome_report->validate_user_input_outcome();
        }

        if ($msg == "") {
            $result = $outcome_report->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Outcome Report with id: ";
            $msg .= h($result);
            $msg .= " has been edited.";

            $session->message($msg);
            redirect_to("dailyOutcome.php");
        } else {
            $session->message($msg);
            redirect_to("dailyOutcome.php");
        }
    }
    $form_delete_params = ['delete_outcome_record_id'];
    // manage form submission for deleting a user
    if (isset($_POST['delete_outcome_record'])) {
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
            redirect_to("dailyOutcome.php");
        }
        $valid_form_delete = new Form($form_delete_params);
        $valid_post_delete_params = $valid_form_delete->allowed_post_params();
        $record_id_delete = $valid_post_delete_params['delete_outcome_record_id'];
        $array_of_outcome_report_ids = OutcomeReport::find_array_of_outcome_report_ids();
        $check_outcome_id_inclusion = has_inclusion_in($record_id_delete, $array_of_outcome_report_ids);
        if (!$check_outcome_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Outcome: ";
            $msg .= h($record_id_delete);
            $msg .= " must be a valid choice.";
            $msg .= "</li>";
            $session->message($msg);
            redirect_to("dailyOutcome.php");
        } else {
            $record_delete = new OutcomeReport();
            $record_delete->outcome_report_id = $record_id_delete;
            $result_delete = $record_delete->delete();
            if (!$result_delete) {
                $msg .= "Unable to delete record with id: ";
                $msg .= h($record_id_delete);
                $session->message($msg);
                redirect_to("dailyOutcome.php");
            } else {
                $log_msg  = "User ";
                $log_msg .= h($session->real_name);
                $log_msg .= " with ID: ";
                $log_msg .= h($session->login_user_id);
                $log_msg .= " as ";
                $log_msg .= h($session->role_name);
                $log_msg .= " deleted outcome record with ID: ";
                $log_msg .= h($record_id_delete);
                logger("WARNING:", $log_msg);
                $msg .= $csrf_msg;
                $msg .= "Record with id: ";
                $msg .= h($record_id_delete);
                $msg .= " succesfully deleted.";
                $session->message($msg);
                redirect_to("dailyOutcome.php");
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
        <li class="breadcrumb-item"><a href="daily_acc_mng.php">Account</a></li>
        <li class="breadcrumb-item active">Daily Outcome</li>
    </ol>
    <?php
    if (!empty($message) and strpos($message, 'created')) {
        echo '<div class="alert alert-success message_manage" style="margin-top: 10px;">';
        echo '<span class="glyphicon glyphicon glyphicon-ok"></span> ';
        echo output_message($message);
    } elseif (!empty($message) and (strpos($message, 'edited') or strpos($message, 'deleted'))) {
        echo '<div class="alert alert-warning message_manage" style="margin-top: 10px;">';
        echo '<span class="glyphicon glyphicon-alert"></span> ';
        echo output_message($message);
    } elseif (!empty($message) and (strpos($message, 'Unable') or strpos($message, 'Fix'))) {
        echo '<div class="alert alert-danger message_manage" style="margin-top: 10px;">';
        echo '<span class="glyphicon glyphicon-remove"></span> ';
        echo output_message($message);
    } elseif (empty($message)) {
        echo '<div class="alert alert-info message_manage" style="margin-top: 10px;">';
        echo '<span class="glyphicon glyphicon-info-sign"></span> ';
        echo output_message("Assign a product to a customer, if customer exists.");
    } else {
        echo '<div class="alert alert-danger message_manage" style="margin-top: 10px;">';
        echo '<span class="glyphicon glyphicon glyphicon-remove"></span> ';
        echo output_message($message);
    }
    ?>
    </div><!-- /.alert alert-info message -->

    <div class="row">
      <div class="col-md-12">
      
        <div class="col-md-8 content">

          <h3>Report Outcome to Provider
            <small><span class="glyphicon glyphicon-save"></span></small> 
          </h3>
          <div class="col-md-12" style="background-color: #ffa834; height: 3px;margin-bottom: 20px"></div>
          
          <div class="col-md-12 form_space">

            <div class="form-group has-feedback">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-user"></span>
                    </span>
                    <select class="selectpicker form-control show-tick"  
                    data-live-search="true" title="ΔΙΑΛΕΞΕ ΠΡΟΜΗΘΕΥΤΗ" 
                    name="select_provider" id="select_provider"
                    data-error="Provider name must be a valid choice and cannot be blank.">
                    <?php
                    $providers = Provider::find_providers_full_details();
                    foreach ($providers as $provider) {
                        $output  = "<option value='";
                        $output .= $provider->provider_id;
                        $output .="'>";
                        $output .= h($provider->provider_name);
                        $output .= "</option>";
                        echo $output;
                    }
                    ?>
                    </select>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Select provider.</div>
            </div><!-- /.form-group -->

           <div class="form-group collapse">
            <button type="button" style="width: 100%;"  id="modalAssignProvider" 
            class="btn btn-success" data-toggle="modal" data-target="#assignProvider" data-providerid="id">
            Create a new outcome record <span class="glyphicon glyphicon-log-in"></span></button>
           </div><!-- /.form-group collapse -->

          </div><!-- /.col-md-12 form_space -->

        </div><!-- /.col-md-8 content -->
        
        <div class="col-md-4 content pull-right" style="width:32%;margin-left: 5px;">
          <h3>Overview  
            <small><span class="glyphicon glyphicon-stats"></span></small>
          </h3>
        <?php if ($session->role_id == 4) : ?>
                <div class="col-md-12 form_space">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-registration-mark"></span>
                            </span>
                            <select class="selectpicker form-control show-tick"  
                            data-live-search="true" title="Select a Gym" 
                            name="select_provider" id="select_gym">
                                <?php $gyms = Gym::find_all(); foreach ($gyms as $gym) : ?>
                                    <option value="<?php echo $gym->gym_id ?>">
                                        <?php echo h($gym->gym_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select gym.</div>
                    </div><!-- /.form-group has-feedback -->
                </div><!-- /.col-md-12 form_space -->
                <div class="col-md-12 form_space">
                    <div class="form-group has-feedback collapse" id="collapsed_date">
                        <div class="input-group input-daterange">
                          <div class="input-group-addon">
                              <span class="glyphicon glyphicon-th"></span>
                          </div><!-- /.input-group-addon -->
                          <input type="text" id="daterange" name="daterange" class="form-control" 
                          data-error="Interval cannot be blank." required>
                        </div><!-- /.input-group input-daterange -->
                        <div class="help-block with-errors">Select a range or pick a date.
                        </div>
                    </div><!-- /.form-group has-feedback -->
                </div><!-- /.col-md-12 form_space collapse -->
            <?php elseif ($session->role_id == 2) : ?>
              <div class="col-md-12 form_space">
                <div class="form-group has-feedback">
                  <div class="input-group input-daterange">
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-th"></span>
                    </div>
                    <input type="text" id="daterange" name="daterange" class="form-control" 
                    data-error="Interval cannot be blank." required>
                  </div><!-- /.input-group input-daterange -->
                  <div class="help-block with-errors">Select a range or pick a date.</div>
                </div><!-- /.form-group has-feedback -->
              </div><!-- /.col-md-12 form_space -->
            <?php endif; ?>
          <div class="col-md-12" style="background-color: #ffa834; height: 3px;">
          </div>
          <div class="col-md-12" style="padding-top: 25px;">
            <ul class="list-group" id = "list_outcome">
            </ul>
          </div>
        </div><!-- /.col-md-4 content -->
        
    </div><!-- /.col-md-12 -->
    </div><!-- /.row -->
    
    <div id='ajax_loader'>
        <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i></br>
    </div>

    <div class="panel panel-info" style="margin-top: 10px;margin-bottom:60px;">

      <div class="panel-heading">
        <h3 class="panel-title" style="color: #476692">Choose export format <small>
          <span class="glyphicon glyphicon-export"></span>
        </small></h3>
      </div><!-- /.panel-heading -->

      <div class="panel-body">

        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="json_record">
            <thead>
              <tr class="tablehead">
                <th>Date</th>
                <th>A.P.Y.</th>
                <th>Gym name</th>
                <th>Reception</th>
                <th>Provider</th>
                <th>Shift</th>
                <th>Outcome</th>
                <th>Paied</th>
                <th>Reason</th> 
                <th>Comments</th>
                <th></th>
                <th></th>
                </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="2"></th>
                <th colspan="1" id="searchgym"">Gym name</th>
                <th colspan="4" style="text-align:right">Total: </th>
                <th colspan="5"></th>
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

<div class="modal fade bs-assignProvider-modal-lg" id="assignProvider" 
 tabindex="-1" role="dialog" aria-labelledby="assignProviderLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="assignProviderLabel"></h4>
      </div>
      <div class="modal-body">

        <form action="dailyOutcome.php" data-toggle="validator" method="post">
          <input type="hidden" class="form-control" name="provider_id" id="provider_id">
          <input type="hidden" class="form-control" value=<?php echo $session->login_user_id ?> 
          name="login_user_id" id="login_user_id">
          <input type="hidden" class="form-control" value=<?php echo $session->gym_id ?> 
          name="gym_id" id="gym_id">

            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>

          <div class="form-group row form_space has-feedback">
            <label for="assignCustomerName" class="col-xs-2 col-form-label">ΠΡΟΜΗΘΕΥΤΗΣ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-user"></span>
                    </span>
                    <input class="form-control" type="text" name="assignProviderName"
                    id="assignProviderName" data-error="Provider name is readonly field." readonly>
                    <span class="glyphicon form-control-feedback" aria-hidden="true">
                    </span>
                </div><!-- /.input-group --> 
                <div class="help-block with-errors">Enter provider full name.</div>
            </div><!-- /.col-xs-10 --> 
          </div><!-- /.form-group row form_space -->

           <div class="form-group row form_space has-feedback">
            <label for="gymName" class="col-xs-2 col-form-label">ΓΥΜΝΑΣΤΗΡΙΟ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-registration-mark"></span>
                    </span>
                    <input type="text" class="form-control" value="<?php echo $session->gym_name ?>"
                    name="gymName" id="gymName" data-error="Gym name is the name of the gym logged in." readonly>
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Gym name.</div>
              </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="reception_name" class="col-xs-2 col-form-label">Reception:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-user"></span>
                    </span>
                    <input type="text" class="form-control" 
                    value="<?php echo $session->real_name ?>"
                    name="reception_name" id="reception_name" 
                    data-error="Reception name is automatic filled in, readonly field." readonly>
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Reception name.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->
          
          <div class="form-group row form_space has-feedback">
            <label for="shift_id" class="col-xs-2 col-form-label">ΒΑΡΔΙΑ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                   <select class="selectpicker form-control show-tick" name="shift_id" 
                   id="shift_id" data-error="Shift cannot be blank, and must be a valid choice.">
                     <option value=1>ΠΡΩΙ</option>
                     <option value=2>ΑΠΟΓΕΥΜΑ</option>
                   </select>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Shift.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="ap" class="col-xs-2 col-form-label">Α.Π:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-qrcode"></span>
                    </span>
                    <input class="form-control" type="text" name="ap" id="ap"
                    pattern="[0-9]{1,}$" data-error="A.P. must consists only of digits 0-9.">
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Α.Π.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="price_paied" class="col-xs-2 col-form-label">ΠΟΣΟ ΠΛΗΡΩΜΗΣ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-euro"></span>
                    </span>
                    <input class="form-control" type="text" name="price_paied" id="price_paied"
                    pattern="[0-9.]{1,}$" data-error="Price paied must consists only 
                    of digits 0-9. Use a dot [.] for decimal number.">
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter price paied.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="outcome_id" class="col-xs-2 col-form-label">ΕΙΔΟΣ ΕΞΟΔΟΥ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-search"></span>
                    </span>
                    <select class="form-control" name="outcome_id" title="Select Outcome" 
                    data-live-search="true" id="outcome_id" data-error="Outcome cannot be blank, 
                    and must be a valid choice." required>
                    </select>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Select Outcome ID.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="reason_outcome_id" class="col-xs-2 col-form-label">ΠΗΓΗ ΕΞΟΔΩΝ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-check"></span>
                    </span>
                    <select class="selectpicker form-control show-tick" name="reason_outcome_id"
                    id="reason_outcome_id">
                        <option value="1">ΓΕΝΙΚΟ ΤΑΜΕΙΟ</option>
                        <option value="2">ΗΜΕΡΗΣΙΟ ΤΑΜΕΙΟ</option>
                    </select>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">ΓΕΝΙΚΟ - ΗΜΕΡΗΣΙΟ ΤΑΜΕΙΟ.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="comments" class="col-xs-2 col-form-label">ΠΑΡΑΤΗΡΗΣΕΙΣ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-text-height"></span>
                    </span>
                    <textarea class="form-control vresize" name="comments" id="comments"></textarea>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Comments must not exceed 200 characters.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          </div><!-- /.modal-body -->
          <div class="modal-footer">
              <div class="form-group row form_space pull-right"  style="padding-right: 15px">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                <button type="submit" name="create_outcome_record" class="btn btn-warning">Αdd Record</button>
              </div><!-- /.form-group row form_space -->
          </div>
        </form>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 

<div class="modal fade" id="deleterecord" tabindex="-1" role="dialog" aria-labelledby="deleterecordLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="deleterecordLabel"></h4>
      </div><!-- /.modal-header -->
      <div class="modal-body">
       
      </div><!-- /.modal-body -->
      <div class="modal-footer">
        <form action="dailyOutcome.php" method="post">    
            <input type="hidden" class="form-control" name="delete_outcome_record_id"
            id="delete_outcome_record_id">
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_outcome_record" class="btn btn-warning">Delete Record</button>
      </div><!-- /.modal-footer -->
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade bs-editrecord-modal-lg" id="editrecord" tabindex="-1" 
 role="dialog" aria-labelledby="editrecordLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="editrecordLabel"></h4>
      </div>
      <div class="modal-body">

        <form action="dailyOutcome.php" data-toggle="validator" method="post">
          <input type="hidden" class="form-control" name="edit_record_id" id="edit_record_id">
          <input type="hidden" class="form-control" value=<?php echo $session->login_user_id ?> 
          name="edit_login_user_id" id="edit_login_user_id">
          <input type="hidden" class="form-control" value=<?php echo $session->gym_id ?> 
          name="edit_gym_id" id="edit_gym_id">
          
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>

          <div class="form-group row form_space">
            <label for="edit_provider_id" class="col-xs-2 col-form-label">ΠΡΟΜΗΘΕΥΤΗΣ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-user"></span>
                    </span>
                    <select class="form-control" name="edit_provider_id" title="Select Provider" 
                    data-live-search="true" id="edit_provider_id">
                    <?php
                    $providers_edit = Provider::find_providers_full_details();
                    foreach ($providers_edit as $provider_edit) {
                        $output  = "<option value='";
                        $output .= $provider_edit->provider_id;
                        $output .="'>";
                        $output .= h($provider_edit->provider_name);
                        $output .= "</option>";
                        echo $output;
                    }
                    ?>
                    </select>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Select provider name.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

           <div class="form-group row form_space">
            <label for="editgymName" class="col-xs-2 col-form-label">Gym Name:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-registration-mark"></span>
                    </span>
                    <input type="text" class="form-control" value="<?php echo $session->gym_name ?>"
                    name="editgymName" id="editgymName" data-error="Gym name is the name 
                    of the gym logged in." readonly>
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Gym name is automatic filled in, readonly field.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space">
            <label for="edit_reception_name" class="col-xs-2 col-form-label">Reception:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-user"></span>
                    </span>
                    <input type="text" class="form-control" 
                    value=<?php echo $session->real_name ?>
                    name="edit_reception_name" id="edit_reception_name" 
                    data-error="Reception name is automatic filled in, readonly field." readonly>
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Reception name.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->
          
          <div class="form-group row form_space">
            <label for="edit_shift_id" class="col-xs-2 col-form-label">ΒΑΡΔΙΑ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                   <select class="selectpicker form-control show-tick" name="edit_shift_id" 
                   id="edit_shift_id" data-error="Shift cannot be blank, and must be a valid choice.">
                     <option value=1>ΠΡΩΙ</option>
                     <option value=2>ΑΠΟΓΕΥΜΑ</option>
                   </select>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Shift.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space">
            <label for="edit_ap" class="col-xs-2 col-form-label">Α.Π:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-qrcode"></span>
                    </span>
                    <input class="form-control" type="text" name="edit_ap" id="edit_ap"
                    pattern="[0-9]{1,}$" data-error="A.P. must consists only of digits 0-9.">
                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Α.Π.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space">
            <label for="edit_price_paied" class="col-xs-2 col-form-label">ΠΟΣΟ ΠΛΗΡΩΜΗΣ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-euro"></span>
                    </span>
                    <input class="form-control" type="text" name="edit_price_paied" 
                    id="edit_price_paied" pattern="[0-9.]{1,}$" 
                    data-error="Price paied must consists only 
                    of digits 0-9. Use a dot [.] for decimal number.">
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Enter price paied.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space">
            <label for="edit_outcome_id" class="col-xs-2 col-form-label">ΕΙΔΟΣ ΕΞΟΔΟΥ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-search"></span>
                    </span>
                    <select class="form-control" name="edit_outcome_id" title="Select Outcome" 
                    data-live-search="true" id="edit_outcome_id" 
                    data-error="Outcome cannot be blank, and must be a valid choice." required>
                    </select>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Select Outcome ID.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space">
            <label for="edit_reason_outcome_id" class="col-xs-2 col-form-label">ΠΗΓΗ ΕΞΟΔΩΝ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-check"></span>
                    </span>
                    <select class="selectpicker form-control show-tick" 
                    name="edit_reason_outcome_id" id="edit_reason_outcome_id">
                        <option value="1">ΓΕΝΙΚΟ ΤΑΜΕΙΟ</option>
                        <option value="2">ΗΜΕΡΗΣΙΟ ΤΑΜΕΙΟ</option>
                    </select>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">ΓΕΝΙΚΟ - ΗΜΕΡΗΣΙΟ ΤΑΜΕΙΟ.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space">
            <label for="edit_comments" class="col-xs-2 col-form-label">ΠΑΡΑΤΗΡΗΣΕΙΣ:</label>
            <div class="col-xs-10">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-text-height"></span>
                    </span>
                    <textarea class="form-control vresize" name="edit_comments" id="edit_comments"></textarea>
                </div><!-- /.input-group -->
                <div class="help-block with-errors">Comments must not exceed 200 characters.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

      </div><!-- /.modal-body -->
      <div class="modal-footer">
          <div class="form-group row form_space pull-right"  style="padding-right: 15px">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_outcome_record" class="btn btn-warning">Save Changes</button>
          </div><!-- /.form-group row form_space -->
      </div>
        </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 

<script type="text/javascript">
$(function () { 
    $('input[name="daterange"]').daterangepicker({
        locale: { format: 'YYYY-MM-DD' }
    });
    var el = $('#select_gym');
    var el_daterange = $('#daterange');
    if (el.length + el_daterange.length == 2) {
        $('#select_gym').on('changed.bs.select', function (e, clickedIndex, newValue, oldValue) {
            var selectedGym = $(this).find("option:selected").val();
            $('.collapse#collapsed_date').collapse();
            var list_outcome = $('#list_outcome')[0];
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $('#list_outcome').html("");
                $.ajax({
                    url:'../../charts_init/json_view_outcome_overview.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        id: selectedGym,
                        start_date: picker.startDate.format('YYYY-MM-DD'),
                        end_date: picker.endDate.format('YYYY-MM-DD')
                    },
                    beforeSend: function(){
                        $("#ajax_loader").show();
                    },
                    complete: function(){
                        $("#ajax_loader").hide();
                    },
                    success: function(data) {
                        var list_outcome = document.getElementById("list_outcome");
                        if (data.table.daily_outcome_price) {
                            var element_li = document.createElement("li");
                            var element = document.createElement("span");
                            element.append(data.table.number_of_daily_outcome);
                            element_li.append(('ΗΜΕΡΗΣΙΟ ΤΑΜΕΙΟ: ' + 
                            data.table.daily_outcome_price + '\u20AC '));
                            element_li.append(element);
                            list_outcome.append(element_li);
                            $('#list_outcome li span').addClass("badge badge-default badge-pill");
                            $('#list_outcome li').addClass("list-group-item");
                        }
                        if (data.table.general_outcome_price) {
                            var element_li = document.createElement("li");
                            var element = document.createElement("span");
                            element.append(data.table.number_of_general_outcome);
                            element_li.append(('ΓΕΝΙΚΟ ΤΑΜΕΙΟ: ' + 
                            data.table.general_outcome_price + '\u20AC '));
                            element_li.append(element);
                            list_outcome.append(element_li);
                            $('#list_outcome li span').addClass("badge badge-default badge-pill");
                            $('#list_outcome li').addClass("list-group-item");
                        }
                        var element_li = document.createElement("li");
                        var element = document.createElement("h4");
                        element.append('TOP 3 ΕΙΔΗ ΕΞΟΔΩΝ');
                        element_li.append(element);
                        list_outcome.append(element_li);
                        $('#list_outcome li h4').addClass("list-group-item-heading");
                        $('#list_outcome li').addClass("list-group-item");
                        $.each(data.chart, function(key, value) {
                            var element = document.createElement("li");
                            element.appendChild(document.createTextNode(key + ': ' + value.outcome + '\u20AC'));
                            list_outcome.append(element);
                            $('#list_outcome li').addClass("list-group-item");
                        });
                    } // success function ends here
                }); // ajax request ends here
            }); // daterangepicker function ends here
        }); // changed.bs.select function ends here
    } else if (el.length + el_daterange.length == 1) {
        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
            $('#list_outcome').html("");
            $.ajax({
                url:'../../charts_init/json_view_outcome_overview.php',
                method: 'post',
                dataType: 'json',
                data: {
                    start_date: picker.startDate.format('YYYY-MM-DD'),
                    end_date: picker.endDate.format('YYYY-MM-DD')
                },
                beforeSend: function(){
                    $("#ajax_loader").show();
                },
                complete: function(){
                    $("#ajax_loader").hide();
                },
                success: function(data) {
                    var list_outcome = document.getElementById("list_outcome");
                    if (data.table.daily_outcome_price) {
                        var element_li = document.createElement("li");
                        var element = document.createElement("span");
                        element.append(data.table.number_of_daily_outcome);
                        element_li.append(('ΗΜΕΡΗΣΙΟ ΤΑΜΕΙΟ: ' + 
                            data.table.daily_outcome_price + '\u20AC '));
                        element_li.append(element);
                        list_outcome.append(element_li);
                        $('#list_outcome li span').addClass("badge badge-default badge-pill");
                        $('#list_outcome li').addClass("list-group-item");
                    }
                    if (data.table.general_outcome_price) {
                        var element_li = document.createElement("li");
                        var element = document.createElement("span");
                        element.append(data.table.number_of_general_outcome);
                        element_li.append(('ΓΕΝΙΚΟ ΤΑΜΕΙΟ: ' + 
                            data.table.general_outcome_price + '\u20AC '));
                        element_li.append(element);
                        list_outcome.append(element_li);
                        $('#list_outcome li span').addClass("badge badge-default badge-pill");
                        $('#list_outcome li').addClass("list-group-item");
                    }
                    var element_li = document.createElement("li");
                    var element = document.createElement("h4");
                    element.append('TOP 3 ΕΙΔΗ ΕΞΟΔΩΝ');
                    element_li.append(element);
                    list_outcome.append(element_li);
                    $('#list_outcome li h4').addClass("list-group-item-heading");
                    $('#list_outcome li').addClass("list-group-item");

                    $.each(data.chart, function(key, value) {
                    var element = document.createElement("li");
                    element.appendChild(document.createTextNode(key + ': ' + value.outcome + '\u20AC'));
                    list_outcome.appendChild(element);
                    $('#list_outcome li').addClass("list-group-item");
                    });
                } // success function ends here
            }); // ajax request ends here
        }); // daterangepicker function ends here
    }  else {
        $('#list_outcome').html("");
        $.ajax({
            url:'../../charts_init/json_view_outcome_overview.php',
            method: 'post',
            dataType: 'json',
            success: function(data) {
                var list_outcome = document.getElementById("list_outcome");
                if (data.table.daily_outcome_price) {
                    var element_li = document.createElement("li");
                    var element = document.createElement("span");
                    element.append(data.table.number_of_daily_outcome);
                    element_li.append(('ΗΜΕΡΗΣΙΟ ΤΑΜΕΙΟ: ' + 
                        data.table.daily_outcome_price + '\u20AC '));
                    element_li.append(element);
                    list_outcome.append(element_li);
                    $('#list_outcome li span').addClass("badge badge-default badge-pill");
                    $('#list_outcome li').addClass("list-group-item");
                }
                if (data.table.general_outcome_price) {
                    var element_li = document.createElement("li");
                    var element = document.createElement("span");
                    element.append(data.table.number_of_general_outcome);
                    element_li.append(('ΓΕΝΙΚΟ ΤΑΜΕΙΟ: ' + 
                        data.table.general_outcome_price + '\u20AC '));
                    element_li.append(element);
                    list_outcome.append(element_li);
                    $('#list_outcome li span').addClass("badge badge-default badge-pill");
                    $('#list_outcome li').addClass("list-group-item");
                }
                var element_li = document.createElement("li");
                var element = document.createElement("h4");
                element.append('TOP 3 ΕΙΔΗ ΕΞΟΔΩΝ');
                element_li.append(element);
                list_outcome.append(element_li);
                $('#list_outcome li h4').addClass("list-group-item-heading");
                $('#list_outcome li').addClass("list-group-item");

                $.each(data.chart, function(key, value) {
                var element = document.createElement("li");
                element.appendChild(document.createTextNode(key + ': ' + value.outcome + '\u20AC'));
                list_outcome.appendChild(element);
                $('#list_outcome li').addClass("list-group-item");
                });
            } // success function ends here
        }); // ajax request ends here
    }  // if ends here
}); // function ends here
// var options = {
//   url: "../../datatables_init/json_gym.php",
//   getValue: "gym_name",
//   list: {
//     match: {
//       enabled: true
//     }
//   }
// };
// $("#gym_name").easyAutocomplete(options);
// $("#editgymName").easyAutocomplete(options);
// Setup - add a text input to each footer cell
$('#searchgym').each(function(){
    var title = $('#json_record thead th').eq($(this).index()+1).text();        
    $(this).html('<input type="text" placeholder="Search ' + title + '"/>');
});
$.ajax({
    url:'../../datatables_init/json_outcome_records_by_role.php',
    method: 'post',
    dataType: 'json',
    beforeSend: function(){
        $("#ajax_loader").show();
    },
    complete: function(){
        $("#ajax_loader").hide();
    },
    success: function(data) {
        var table = $('#json_record').DataTable({
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 7 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 7, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 7 ).footer() ).html(
                '$'+pageTotal.toFixed(2) +' ( $'+ total.toFixed(2) +' total)'
            );
        },
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
            {'data': 'time'},
            {'data': 'ap'},
            {'data': 'gym_name'},
            {'data': 'username'},
            {'data': 'provider_name'},
            {'data': 'shift_name'},
            {'data': 'outcome_name'},
            {'data': 'price_paied'},
            {'data': 'reason_outcome_name'},
            {'data': 'comments'},
            {'data': 'outcome_report_id'},
            {'data': 'outcome_report_id'},
        ],
        columnDefs: [
        {
            targets: 7,
            orderable: true,
            searchable: true,
            className: 'dt-body-center',
            render: function(data, type, full, meta) {
                return data + '\u20AC';
            }
        },
        {
            targets: 10,
            orderable: false,
            searchable: false,
            className: 'dt-body-center',
            render: function(data, type, full, meta) {
                edit_customer = '';
                edit_customer += '<button type="button" class="btn btn-warning" data-toggle="modal"'
                edit_customer += 'data-target=".bs-editrecord-modal-lg" data-recordid="';
                edit_customer += data;
                edit_customer += '">';
                edit_customer += '<span class="glyphicon glyphicon-edit" style="color: #000"></span>';
                edit_customer += '</button>';
                return edit_customer;
            }
        },
        {
            targets: 11,
            orderable: false,
            searchable: false,
            className: 'dt-body-center',
            render: function(data, type, full, meta) {
                delete_customer = '';
                delete_customer += '<button type="button" class="btn btn-danger"';
                delete_customer += ' data-toggle="modal" data-target="#deleterecord" data-recordid="';
                delete_customer += data;
                delete_customer += '">';
                delete_customer += '<span class="glyphicon glyphicon-trash" style="color: #000"></span>';
                delete_customer += '</button>';
                return delete_customer;
            }
        }
        ],
    }); // DataTable ends here
    table.column(2).every(function(){
        var tableColumn = this;
        $(this.footer()).find('input').on('keyup change', function(){
            var term = $(this).val();
            tableColumn.search(term).draw();                   
        });          
    });
  } // success function ends here
});  // ajax request ends here
$('#select_provider').on('changed.bs.select', function (e, clickedIndex, newValue, oldValue) {
    var selectedProvider = $(this).find("option:selected").val();
    $('.collapse').collapse()
});
$('#assignProvider').on('show.bs.modal', function (event) {
    var provider_id_for_modal = $('#select_provider').find("option:selected").val();
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('providerid') // Extract info from data-* attributes
    $.ajax({
        url:'../../modal_ajax/modal_provider_outcome_json.php',
        method: 'post',
        dataType: 'json',
        data: {id: provider_id_for_modal},
        beforeSend: function(){
            $.spin('modal');
        },
        complete: function(){
            $.spin('false');
        },
        success: function(data) {
            var modal = $('#assignProvider');
            modal.find('.modal-title').text('Create outcome record for provider: ' + data.provider.provider_name);
            modal.find('.modal-body input#provider_id[type="hidden"]').val(provider_id_for_modal);
            modal.find('.modal-body input[name="assignProviderName"]').val(data.provider.provider_name);
            var select = document.getElementById("outcome_id");
            $.each(data.outcome, function(key, value) {
                var option = document.createElement("option");
                option.text = value.outcome_name;
                option.value = value.outcome_id;
                select.appendChild(option);
            });
            $('#outcome_id').addClass('selectpicker');
            $('#outcome_id').addClass(' show-tick');
            $('#outcome_id').selectpicker({
                style: 'btn-default',
                size: 4
            });
            $('#reason_outcome_id').selectpicker('val', 2);  
        } // success function ends here
    }); // ajax request ends here
});
$('#deleterecord').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('recordid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library 
    // or other methods instead.
    $.ajax({
        url:'../../modal_ajax/modal_outcome_record_json.php',
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
            var modal = $('#deleterecord');
            modal.find('.modal-title').text('Delete record from Customer: ' + data.record.provider_name);
            modal.find('.modal-body').text('Are you sure that you want to delete record with ID: ' + recipient + ' ?');
            modal.find('.modal-footer input#delete_outcome_record_id[type="hidden"]').val(recipient);
        } // success function ends here
    }); // ajax request ends here
  }); // modal ends here

$('#editrecord').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('recordid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library 
    // or other methods instead.
    $.ajax({
        url:'../../modal_ajax/modal_outcome_record_json.php',
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
            var modal = $('#editrecord');
            modal.find('.modal-title').text('Edit record for Provider: ' + data.record.provider_name);
            modal.find('.modal-body input#edit_record_id[type="hidden"]').val(recipient);
            modal.find('.modal-body input[name="edit_ap"]').val(data.record.ap);
            modal.find('.modal-body input[name="edit_price_paied"]').val(data.record.price_paied);
            modal.find('.modal-body textarea[name="edit_comments"]').val(data.record.comments);
            $('#edit_provider_id').addClass('selectpicker');
            $('#edit_provider_id').addClass(' show-tick');
            $('#edit_provider_id').selectpicker('val', data.record.provider_id);
            $('#edit_provider_id').selectpicker({
                style: 'btn-default',
                size: 4
            });
            var select_edit = document.getElementById("edit_outcome_id");
            $.each(data.outcome, function(key, value) {
                var option = document.createElement("option");
                option.text = value.outcome_name;
                option.value = value.outcome_id;
                select_edit.appendChild(option);
            });
            $('#edit_outcome_id').addClass('selectpicker');
            $('#edit_outcome_id').addClass(' show-tick');
            $('#edit_outcome_id').selectpicker('val', data.record.outcome_id);
            $('#edit_outcome_id').selectpicker({
                style: 'btn-default',
                size: 4
            });
            $('#edit_reason_outcome_id').selectpicker('val', data.record.reason_outcome_id);
        } // success function ends here
    }); // ajax request ends here
}); // modal ends here
</script>
<?php
include('../../includes/layouts/footer.php');
if (isset($db)) {
    $db->close_connection();
}
?>

