<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in() or !$session->is_session_valid()) {
    $session->logout();
    redirect_to("login.php");
}

if (request_is_same_domain() and request_is_post()) {
    $form_params_customer = ['customer_name', 'gym_id', 'telephoneInput', 'csrf_token',
    'csrf_token_time'];
    $msg = "";
    $csrf_msg = "";
    // manage form submission for adding a customer
    if (isset($_POST['add_customer'])) {
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
            redirect_to("dailyIncome.php");
        }
        $valid_form = new Form($form_params_customer);
        $valid_post_params = $valid_form->allowed_post_params();
        $gym_id = $valid_post_params['gym_id'];
        $check_gym_id = Gym::find_by_primary_key($gym_id);
        if ($check_gym_id->gym_id != $session->gym_id) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: gym_id was hacked.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $customer = new Customer();
            $customer->name = mb_strtoupper($valid_post_params['customer_name'], "UTF-8");
            $customer->telephone = $valid_post_params['telephoneInput'];
            $customer->gym_id = $check_gym_id->gym_id;
            $msg = $customer->validate_customer_input_fields();
        }
    
        if ($msg == "") {
            $result = $customer->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Customer created with id: ";
            $msg .= h($result);

            $session->message($msg);
            redirect_to("dailyIncome.php");
        } else {
            $session->message($msg);
            redirect_to("dailyIncome.php");
        }
    }
    // the allowed params from form post submission
    $form_params = ['alp', 'apy', 'assign_gym_id', 'login_user_id', 'assign_customer_id',
        'shift_id', 'income_id', 'registration_type', 'price_agreed', 'price_paied', 'payment_method_id', 'taxes',
        'attraction_income_id', 'comments'];
    $msg = "";
    // manage form submission for adding a customer
    if (isset($_POST['assign_product'])) {
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
            redirect_to("dailyIncome.php");
        }
        $valid_form = new Form($form_params);
        $valid_post_params = $valid_form->allowed_post_params();
        if ($valid_post_params['assign_gym_id'] != $session->gym_id or
            $valid_post_params['login_user_id'] != $session->login_user_id) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: gym_id or login_user_id was hacked.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $income_report = new IncomeReport();
            $income_report->alp = $valid_post_params['alp'];
            $income_report->apy = $valid_post_params['apy'];
            $income_report->gym_id = $valid_post_params['assign_gym_id'];
            $income_report->login_user_id = $valid_post_params['login_user_id'];
            $income_report->customer_id = $valid_post_params['assign_customer_id'];
            $income_report->shift_id = $valid_post_params['shift_id'];
            $income_report->income_id = $valid_post_params['income_id'];
            $income_report->registration_type = $valid_post_params['registration_type'];
            $income_report->price_agreed = $valid_post_params['price_agreed'];
            $income_report->price_paied = $valid_post_params['price_paied'];
            $income_report->payment_method_id = $valid_post_params['payment_method_id'];
            $income_report->taxes = $valid_post_params['taxes'];
            $income_report->attraction_id = $valid_post_params['attraction_income_id'];
            $income_report->comments = $valid_post_params['comments'];
            $msg = $income_report->validate_user_input();
        }
        
        if ($msg == "") {
            // echo "<pre>";
            // print_r($income_report);
            // echo "</pre>";
            $result = $income_report->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Income Report created with id: ";
            $msg .= h($result);

            $session->message($msg);
            redirect_to("dailyIncome.php");
        } else {
            $session->message($msg);
            redirect_to("dailyIncome.php");
        }
    }

    $form_edit_params = ['edit_record_id', 'edit_alp', 'edit_apy', 'edit_gym_id',
    'edit_login_user_id', 'edit_customer_id', 'edit_shift_id', 'edit_income_id',
    'edit_registration_type', 'edit_price_agreed', 'edit_price_paied',
    'edit_payment_method_id', 'edit_taxes', 'edit_attraction_income_id', 'edit_comments'];
    // manage modal-form submission for editing user details
    if (isset($_POST['edit_record'])) {
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
            redirect_to("dailyIncome.php");
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
            $income_report = new IncomeReport();
            $income_report->income_report_id = $valid_post_edit_params['edit_record_id'];
            $income_report->alp = $valid_post_edit_params['edit_alp'];
            $income_report->apy = $valid_post_edit_params['edit_apy'];
            $income_report->gym_id = $valid_post_edit_params['edit_gym_id'];
            $income_report->login_user_id = $valid_post_edit_params['edit_login_user_id'];
            $income_report->customer_id = $valid_post_edit_params['edit_customer_id'];
            $income_report->shift_id = $valid_post_edit_params['edit_shift_id'];
            $income_report->income_id = $valid_post_edit_params['edit_income_id'];
            $income_report->registration_type =
            $valid_post_edit_params['edit_registration_type'];
            $income_report->price_agreed = $valid_post_edit_params['edit_price_agreed'];
            $income_report->price_paied = $valid_post_edit_params['edit_price_paied'];
            $income_report->payment_method_id =
            $valid_post_edit_params['edit_payment_method_id'];
            $income_report->taxes = $valid_post_edit_params['edit_taxes'];
            $income_report->attraction_id =
            $valid_post_edit_params['edit_attraction_income_id'];
            $income_report->comments = $valid_post_edit_params['edit_comments'];
            $msg = $income_report->validate_user_input();
        }

        if ($msg == "") {
            $result = $income_report->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Income Report with ID: ";
            $msg .= h($result);
            $msg .= " has been edited.";

            $session->message($msg);
            redirect_to("dailyIncome.php");
        } else {
            $session->message($msg);
            redirect_to("dailyIncome.php");
        }
    }
    $form_delete_params = ['delete_record_id'];
    // manage form submission for deleting a user
    if (isset($_POST['delete_record'])) {
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
            redirect_to("dailyIncome.php");
        }
        $valid_form_delete = new Form($form_delete_params);
        $valid_post_delete_params = $valid_form_delete->allowed_post_params();
        $record_id_delete = $valid_post_delete_params['delete_record_id'];
        $array_of_income_report_ids = IncomeReport::find_array_of_income_report_ids();
        $check_income_id_inclusion = has_inclusion_in($record_id_delete, $array_of_income_report_ids);
        if (!$check_income_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Income: ";
            $msg .= h($record_id_delete);
            $msg .= " must be a valid choice.";
            $msg .= "</li>";
            $session->message($msg);
            redirect_to("dailyIncome.php");
        } else {
            $record_delete = new IncomeReport();
            $record_delete->income_report_id = $record_id_delete;
            $result_delete = $record_delete->delete();
            if (!$result_delete) {
                $msg .= "Unable to delete record with id: ";
                $msg .= h($record_id_delete);
                $session->message($msg);
                redirect_to("dailyIncome.php");
            } else {
                $log_msg  = "User ";
                $log_msg .= h($session->real_name);
                $log_msg .= " with ID: ";
                $log_msg .= h($session->login_user_id);
                $log_msg .= " as ";
                $log_msg .= h($session->role_name);
                $log_msg .= " deleted income record with ID: ";
                $log_msg .= h($record_id_delete);
                logger("WARNING:", $log_msg);
                $msg .= $csrf_msg;
                $msg .= "Record with id: ";
                $msg .= h($record_id_delete);
                $msg .= " succesfully deleted.";
                $session->message($msg);
                redirect_to("dailyIncome.php");
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
        <li class="breadcrumb-item active">Daily Income</li>
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
        echo '<div class="alert alert-danger message_manage">';
        echo '<span class="glyphicon glyphicon glyphicon-remove"></span> ';
        echo output_message($message);
    }
    ?>
    </div><!-- /.alert alert-info message -->

    <div class="row">
      <div class="col-md-12">
      
        <div class="col-md-8 content">

          <h3>ΠΩΛΗΣΗ ΠΡΟΪΟΝ ΣΕ ΠΕΛΑΤΗ 
            <small><span class="glyphicon glyphicon-save"></span></small> 
          </h3>
          <div class="col-md-12" style="background-color: #ffa834; height: 3px;margin-bottom: 20px"></div>
          
          <div class="col-md-12 form_space">

            <div class="form-group has-feedback">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-user"></span>
                    </span>
                    <select class="js-example-basic-single form-control show-tick"
                    name="select_customer" id="select_customer"
                    data-error="Customer name must be a valid choice and cannot be blank." required>
                    <?php
                    $customers = Customer::find_customers_enumeration();
                    foreach ($customers as $customer) :
                    ?>
                        <option data-subtext="<?php echo $customer->telephone ?>" value="<?php echo $customer->id ?>">
                            <?php echo h($customer->name); ?>
                        </option>
                    <?php
                    endforeach;
                    ?>    
                    </select>
              </div><!-- /.input-group -->
              <div class="help-block with-errors">Select customer.</div>
            </div><!-- /.form-group -->

           <div class="form-group collapse">
            <button type="button" style="width: 100%;"  id="modalAssignCustomer" 
            class="btn btn-success" data-toggle="modal" data-target="#assignCustomer" data-vesselid="id">
            Create a new income record <span class="glyphicon glyphicon-log-in"></span></button>
           </div><!-- /.form-group collapse -->

          </div><!-- /.col-md-12 form_space -->
          
          
            <div class="col-md-12 bs-callout bs-callout-default">
                <form data-toggle="validator" class="form-horizontal" action="dailyIncome.php"
                method="post">
                <div class="col-md-12 form_space">
                    <h3 style="text-align:left;padding-bottom:5px">or Add Customer if not exists
                </div><!-- /.col-md-12 form_space -->
                <div class="col-md-12" style="background-color: #ffa834;height: 3px;
                margin-bottom: 20px"></div>
                
                <input type="hidden" class="form-control" value=<?php echo $session->gym_id; ?> 
                name="gym_id" id="gym_id">

                <?php echo $session->csrf_token_tag(); ?>
                <?php echo $session->csrf_token_tag_time(); ?>

                <div class="form-group row form_space has-feedback">
                  <label for="customer_name" class="col-xs-2 col-form-label">ΠΕΛΑΤΗΣ:
                  </label>
                  <div class="col-xs-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-user"></span>
                        </span>
                        <input class="form-control" type="text" name="customer_name"
                        id="customer_name" pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$"
                        data-error="Customer name cannot be blank and must consists
                        of latin or greek characters only." required>
                        <span class="glyphicon form-control-feedback" aria-hidden="true">
                        </span>
                    </div><!-- /.input-group --> 
                    <div class="help-block with-errors">Enter customer full name.</div>
                  </div><!-- /.col-xs-10 --> 
                  
                </div><!-- /.form-group row form_space has-feedback --> 

                <div class="form-group row form_space has-feedback">
                  <label for="telephoneInput" class="col-xs-2 col-form-label">ΤΗΛΕΦΩΝΟ:
                  </label>
                  <div class="col-xs-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-phone-alt"></span>
                        </span>
                        <input class="form-control" type="text" name="telephoneInput"
                        id="telephoneInput" pattern="[0-9]{10}$" maxlength=10 
                        data-error="Phone number cannot be blank and must consists 
                        of exactly 10 digits." required>
                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div><!-- /.input-group --> 
                    <div class="help-block with-errors">Enter customer phone number.</div>
                  </div><!-- /.col-xs-10 --> 
                </div><!-- /.form-group row form_space has-feedback -->

                <div class="form-group row form_space has-feedback">
                  <label for="gym_name" class="col-xs-2 col-form-label">ΓΥΜΝΑΣΤΗΡΙΟ:
                  </label>
                  <div class="col-xs-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-registration-mark"></span>
                        </span>
                        <input type="text" class="form-control" value="<?php echo h($session->gym_name) ?>"
                        name="gym_name" id="gym_name" data-error="Gym name is the name of the gym logged in." readonly>
                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div><!-- /.input-group -->
                    <div class="help-block with-errors">Gym name.</div>
                  </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group" style="padding-right: 15px;margin-bottom: 0px;">
                  <button type="submit" name="add_customer" class="btn btn-success pull-right">Add Customer
                    <span class="glyphicon glyphicon-plus-sign"></span></button>
                </div>
                </form>
            </div><!-- /.col-md-12 -->
          
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
            <div class="col-md-12" style="background-color: #ffa834; height: 3px;"></div>
            <div class="col-md-12" style="padding-top: 25px;">
                <ul class="list-group" id = "list_income"></ul>
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
                <th>A.L.P.</th>
                <th>A.P.Y.</th>
                <th>Gym name</th>
                <th>Reception</th>
                <th>Customer</th>
                <th>Shift</th>
                <th>Service</th>
                <th>Agreed</th>
                <th>Paied</th>
                <th>Method</th>
                <th>Registration</th>
                <th>Taxes</th>
                <th>Attraction</th>
                <th>Comments</th>
                <th></th>
                <th></th>
                </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="3"></th>
                <th colspan="1" id="searchgym"">Gym name</th>
                <th colspan="4" style="text-align:right">Total: </th>
                <th colspan="9"></th>
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

<div class="modal fade bs-assignCustomer-modal-lg" id="assignCustomer" 
 tabindex="-1" role="dialog" aria-labelledby="assignCustomerLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="assignCustomerLabel"></h4>
            </div>
            <div class="modal-body">

            <form action="dailyIncome.php" data-toggle="validator" method="post">
                <input type="hidden" class="form-control" name="assign_customer_id" id="assign_customer_id">
                <input type="hidden" class="form-control"
                value=<?php echo $session->login_user_id ?> name="login_user_id" 
                id="login_user_id">
                <input type="hidden" class="form-control"
                value=<?php echo $session->gym_id ?> 
                name="assign_gym_id" id="assign_gym_id">
                <?php echo $session->csrf_token_tag(); ?>
                <?php echo $session->csrf_token_tag_time(); ?>

                <div class="form-group row form_space has-feedback">
                    <label for="assignCustomerName" class="col-xs-2 col-form-label">ΠΕΛΑΤΗΣ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-user"></span>
                            </span>
                            <input class="form-control" type="text"
                            name="assignCustomerName" id="assignCustomerName" readonly>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter customer full name.
                        </div>
                    </div><!-- /.col-xs-10 --> 
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="gymName" class="col-xs-2 col-form-label">ΓΥΜΝΑΣΤΗΡΙΟ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-registration-mark"></span>
                            </span>
                            <input type="text" class="form-control"
                            value="<?php echo $session->gym_name ?>"
                            name="gymName" id="gymName"
                            data-error="Gym name is the name of the gym logged in." readonly>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Gym name is automatic filled in, readonly field.</div>
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
                    <label for="alp" class="col-xs-2 col-form-label">Α.Λ.Π:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-qrcode"></span>
                            </span>
                            <input class="form-control" type="text" name="alp" id="alp"
                            pattern="[0-9]{1,}$" data-error="ALP must consists only of digits 0-9.">
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">ΑΡΙΘΜΟΣ ΛΙΑΝΙΚΗΣ ΠΩΛΗΣΗΣ.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="apy" class="col-xs-2 col-form-label">Α.Π.Υ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-qrcode"></span>
                            </span>
                            <input class="form-control" type="text" name="apy" id="apy"
                            pattern="[0-9]{1,}$" data-error="APY must consists only of digits 0-9.">
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Α.Π.Υ.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="income_id" class="col-xs-2 col-form-label">ΕΙΔΟΣ  ΥΠΗΡΕΣΙΑΣ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-search"></span>
                            </span>
                            <select class="form-control" name="income_id"
                            title="Select Income" data-live-search="true" id="income_id"
                            data-error="Shift cannot be blank, and must be a valid choice." required>
                            </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select Income ID.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->
            
                <div class="form-group row form_space">
                    <label class="col-xs-2">ΕΙΔΟΣ  ΕΓΓΡΑΦΗΣ:</label>
                    <div class="col-xs-10">
                        <label class="radio-inline">
                          <input type="radio" name="registration_type" id="renewal" value=1 checked> ΑΝΑΝΕΩΣΗ
                        </label>
                        <label class="radio-inline">
                          <input type="radio" name="registration_type" id="registration"
                          value=2> ΕΓΓΡΑΦΗ
                        </label>
                        <label class="radio-inline">
                          <input type="radio" name="registration_type"
                          id="free_registration" value=3> ΔΩΡΕΑΝ ΕΓΓΡΑΦΗ
                        </label>
                        <div class="help-block">Type of Registration.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="price_agreed" class="col-xs-2 col-form-label">ΤΙΜΗ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-euro"></span>
                            </span>
                            <input class="form-control" type="text" name="price_agreed" 
                            id="price_agreed" pattern="[0-9.]{1,}$"
                            data-error="Price must consists only of digits 0-9. Use a dot [.] for decimal number.">
                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter price agreed.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="price_paied" class="col-xs-2 col-form-label">ΠΟΣΟ ΠΛΗΡΩΜΗΣ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-euro"></span>
                            </span>
                            <input class="form-control" type="text" name="price_paied" 
                            id="price_paied" pattern="[0-9.]{1,}$" 
                            data-error="Price must consists only of digits 0-9. Use a dot [.] for decimal number.">
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter price paied.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="payment_method_id" class="col-xs-2 col-form-label">ΤΡΟΠΟΣ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-piggy-bank"></span>
                            </span>
                            <select class="form-control" title="Select payment method" 
                            name="payment_method_id" data-live-search="true" 
                            id="payment_method_id" data-error="Payment method cannot 
                            be blank, and must be a valid choice.">
                            </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select payment method.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="taxes" class="col-xs-2 col-form-label">ΦΟΡΟΣ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-percent"></i>
                            </span>
                            <select class="selectpicker form-control show-tick" 
                            name="taxes" id="taxes"
                            data-error="Taxes cannot be blank, and must be a valid choice.">
                                <option value="0.13">13%</option>
                                <option value="0.24">24%</option>
                            </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select taxes 13% or 24%.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="attraction_income_id" class="col-xs-2 col-form-label">ΠΡΟΣΕΛΚΥΣΗ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-link"></span>
                            </span>
                            <select class="form-control" title="Select attraction method" 
                            name="attraction_income_id" data-live-search="true" 
                            id="attraction_income_id" data-error="Attraction ID cannot 
                            be blank, and must be a valid choice.">
                            </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select attraction ID.</div>
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
                    <button type="submit" name="assign_product" class="btn btn-warning">Αdd Record</button>
                </div><!-- /.form-group row form_space -->
            </div><!-- /.modal-footer -->
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
        <form action="dailyIncome.php" method="post">    
            <input type="hidden" class="form-control" name="delete_record_id" id="delete_record_id">
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_record" class="btn btn-warning">Delete Record</button>
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

            <form action="dailyIncome.php" data-toggle="validator" method="post">
                <input type="hidden" class="form-control" name="edit_customer_id" id="edit_customer_id">
                <input type="hidden" class="form-control" name="edit_record_id" id="edit_record_id">
                <input type="hidden" class="form-control" 
                value=<?php echo $session->login_user_id ?> 
                name="edit_login_user_id" id="edit_login_user_id">
                <input type="hidden" class="form-control" 
                value=<?php echo $session->gym_id ?> 
                name="edit_gym_id" id="edit_gym_id">
    
                <?php echo $session->csrf_token_tag(); ?>
                <?php echo $session->csrf_token_tag_time(); ?>

                <div class="form-group row form_space has-feedback">
                    <label for="edit_customer_name" class="col-xs-2 col-form-label">ΠΕΛΑΤΗΣ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-user"></span>
                            </span>
                            <input class="form-control" type="text"
                            name="edit_customer_name" id="edit_customer_name" readonly>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter customer full name.
                        </div>
                    </div><!-- /.col-xs-10 --> 
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="editgymName" class="col-xs-2 col-form-label">ΓΥΜΝΑΣΤΗΡΙΟ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-registration-mark"></span>
                            </span>
                            <input type="text" class="form-control" 
                            value="<?php echo $session->gym_name ?>"
                            name="editgymName" id="editgymName" 
                            data-error="Gym name is the name of the gym logged in." readonly>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Gym name.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
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
                        <select class="selectpicker form-control show-tick" 
                        name="edit_shift_id" id="edit_shift_id" data-error="Shift 
                        cannot be blank, and must be a valid choice.">
                            <option value=1>ΠΡΩΙ</option>
                            <option value=2>ΑΠΟΓΕΥΜΑ</option>
                        </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select shift.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                    <label for="edit_alp" class="col-xs-2 col-form-label">Α.Λ.Π:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-qrcode"></span>
                            </span>
                            <input class="form-control" type="text" name="edit_alp" 
                            id="edit_alp" pattern="[0-9]{1,}$" data-error="ALP must consists only of digits 0-9.">
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">ΑΡΙΘΜΟΣ ΛΙΑΝΙΚΗΣ ΠΩΛΗΣΗΣ.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                    <label for="edit_apy" class="col-xs-2 col-form-label">Α.Π.Υ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-qrcode"></span>
                            </span>
                            <input class="form-control" type="text" name="edit_apy" 
                            id="edit_apy" pattern="[0-9]{1,}$" data-error="APY must 
                            consists only of digits 0-9.">
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Α.Π.Υ.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                    <label for="edit_income_id" class="col-xs-2 col-form-label">ΕΙΔΟΣ  ΥΠΗΡΕΣΙΑΣ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-search"></span>
                            </span>
                            <select class="form-control" name="edit_income_id" 
                            title="Select Income" data-live-search="true" 
                            id="edit_income_id" data-error="Shift cannot be blank, 
                            and must be a valid choice." required>
                            </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select Income ID.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                    <label class="col-xs-2">ΕΙΔΟΣ  ΕΓΓΡΑΦΗΣ:</label>
                    <div class="col-xs-10">
                        <label class="radio-inline">
                          <input type="radio" name="edit_registration_type" 
                          id="edit_renewal" value=1> ΑΝΑΝΕΩΣΗ
                        </label>
                        <label class="radio-inline">
                          <input type="radio" name="edit_registration_type" id="edit_registration"
                          value=2> ΕΓΓΡΑΦΗ
                        </label>
                        <label class="radio-inline">
                          <input type="radio" name="edit_registration_type"
                          id="edit_free_registration" value=3> ΔΩΡΕΑΝ ΕΓΓΡΑΦΗ
                        </label>
                        <div class="help-block">Type of Registration.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->



                <div class="form-group row form_space">
                    <label for="edit_price_agreed" class="col-xs-2 col-form-label">ΤΙΜΗ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-euro"></span>
                            </span>
                            <input class="form-control" type="text" name="edit_price_agreed" 
                            id="edit_price_agreed" pattern="[0-9.]{1,}$" 
                            data-error="Price must consists only of digits 0-9. 
                            Use a dot [.] for decimal number.">
                        <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter price agreed.</div>
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
                            data-error="Price must consists only of digits 0-9. 
                            Use a dot [.] for decimal number.">
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter price paied.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                    <label for="edit_payment_method_id" class="col-xs-2 col-form-label">ΤΡΟΠΟΣ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-piggy-bank"></span>
                            </span>
                            <select class="form-control" title="Select payment method" 
                            name="edit_payment_method_id" data-live-search="true" 
                            id="edit_payment_method_id"
                            data-error="Payment method cannot be blank, and must be a valid choice.">
                            </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select payment method.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                    <label for="edit_taxes" class="col-xs-2 col-form-label">ΦΟΡΟΣ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-book"></span>
                            </span>
                            <select class="selectpicker form-control show-tick" 
                            name="edit_taxes" id="edit_taxes" data-error="Taxes cannot 
                            be blank, and must be a valid choice.">
                                <option value="0.13">13%</option>
                                <option value="0.24">24%</option>
                            </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select taxes 13% or 24%.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                    <label for="edit_attraction_income_id" class="col-xs-2 col-form-label">ΠΡΟΣΕΛΚΥΣΗ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-link"></span>
                            </span>
                            <select class="form-control" title="Select attraction method" 
                            name="edit_attraction_income_id" data-live-search="true" 
                            id="edit_attraction_income_id"
                            data-error="Attraction ID cannot be blank, and must be a valid choice.">
                            </select>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Select attraction ID.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                    <label for="edit_comments" class="col-xs-2 col-form-label">ΠΑΡΑΤΗΡΗΣΕΙΣ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-text-height"></span>
                            </span>
                            <textarea class="form-control vresize" name="edit_comments" id="edit_comments"
                            data-error="Comments must not exceed 200 characters.">
                            </textarea>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Comments must not exceed 200 characters.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

            </div><!-- /.modal-body -->
            <div class="modal-footer">
                <div class="form-group row form_space pull-right"  style="padding-right: 15px">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_record" class="btn btn-warning">Save Changes</button>
                </div><!-- /.form-group row form_space -->
            </div><!-- /.modal-footer -->
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript" src="../js/dailyIncome_script.js"></script>
<?php
include('../../includes/layouts/footer.php');
if (isset($db)) {
    $db->close_connection();
}
?>
