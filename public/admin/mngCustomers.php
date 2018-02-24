<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in() or !$session->is_session_valid()) {
    $session->logout();
    redirect_to("login.php");
}
if (request_is_same_domain() and request_is_post()) {
    $form_params = ['customer_name', 'gym_id', 'telephoneInput', 'barcodeInput', 'csrf_token', 'csrf_token_time'];
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
            redirect_to("mngCustomers.php");
        }
        $valid_form = new Form($form_params);
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
            $customer->barcode = $valid_post_params['barcodeInput'];
            $customer->gym_id = $check_gym_id->gym_id;
            $msg = $customer->validate_customer_input_fields();
        }
        
        if ($msg == "") {
            $result = $customer->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Customer created with id: ";
            $msg .= $result;

            $session->message($msg);
            redirect_to("mngCustomers.php");
        } else {
            $session->message($msg);
            redirect_to("mngCustomers.php");
        }
    }
    $form_edit_params = ['edit_customer_id', 'edit_customer_name', 'edit_telephone', 'edit_gym_id', 'edit_barcode'];
    // manage modal-form submission for editing user details
    if (isset($_POST['edit_customer'])) {
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
            redirect_to("mngCustomers.php");
        }
        $valid_edit_form = new Form($form_edit_params);
        $valid_post_edit_params = $valid_edit_form->allowed_post_params();
        $check_customer_id = has_presence($valid_post_edit_params['edit_customer_id']);
        $allowed_customer_ids = Customer::find_array_of_customers_ids();
        $check_customer_id_inclusion =
        has_inclusion_in($valid_post_edit_params['edit_customer_id'], $allowed_customer_ids);
        if (!$check_customer_id or !$check_customer_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Customer: ";
            $msg .= h($valid_post_edit_params['edit_customer_id']);
            $msg .= " cannot be blank and must be a valid choice: ";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $customer_edit = new Customer();
            $customer_edit->id = $valid_post_edit_params['edit_customer_id'];
            $customer_edit->name = mb_strtoupper($valid_post_edit_params['edit_customer_name'], "UTF-8");
            $customer_edit->telephone = $valid_post_edit_params['edit_telephone'];
            $customer_edit->barcode = $valid_post_edit_params['edit_barcode'];
            $customer_edit->gym_id = $valid_post_edit_params['edit_gym_id'];
            $msg = $customer_edit->validate_customer_input_fields();
        }
        
        if ($msg == "") {
            $result = $customer_edit->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Customer with id: ";
            $msg .= $result;
            $msg .= " has been edited.";

            $session->message($msg);
            redirect_to("mngCustomers.php");
        } else {
            $session->message($msg);
            redirect_to("mngCustomers.php");
        }
    }
    $form_delete_params = ['delete_customer_id'];
    // manage form submission for deleting a user
    if (isset($_POST['delete_customer'])) {
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
            redirect_to("mngCustomers.php");
        }
        $valid_form_delete = new Form($form_delete_params);
        $valid_post_delete_params = $valid_form_delete->allowed_post_params();
        $customer_id_delete = $valid_post_delete_params['delete_customer_id'];
        $allowed_customer_ids = Customer::find_array_of_customers_ids();
        $check_customer_id_inclusion = has_inclusion_in($customer_id_delete, $allowed_customer_ids);
        $numbers_of_products = IncomeReport::find_income_report_by_customer_id($customer_id_delete);
        if ($numbers_of_products > 0 and $check_customer_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Customer with ID: ";
            $msg .= h($customer_id_delete);
            $msg .= " has buyed ";
            $msg .= h($numbers_of_products);
            $msg .= " products. Cannot delete him/her.";
            $msg .= "</li>";
            $msg .= "</ul>";
            $session->message($msg);
            redirect_to("mngCustomers.php");
        } elseif (!$check_customer_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Customer: ";
            $msg .= h($customer_id_delete);
            $msg .= " cannot be blank and must be a valid choice: ";
            $msg .= "</li>";
            $msg .= "</ul>";
            $session->message($msg);
            redirect_to("mngCustomers.php");
        } else {
            $customer_delete = new Customer();
            $customer_delete->id = $customer_id_delete;
            $result_delete = $customer_delete->delete();
            if (!$result_delete) {
                $msg .= "Unable to delete customer with id: ";
                $msg .= h($customer_id_delete);
                $session->message($msg);
                redirect_to("mngCustomers.php");
            } else {
                $log_msg  = "User ";
                $log_msg .= h($session->real_name);
                $log_msg .= " with ID: ";
                $log_msg .= h($session->login_user_id);
                $log_msg .= " as ";
                $log_msg .= h($session->role_name);
                $log_msg .= " deleted customer with ID: ";
                $log_msg .= h($customer_id_delete);
                logger("WARNING:", $log_msg);
                $msg .= $csrf_msg;
                $msg .= "Customer with id: ";
                $msg .= h($customer_id_delete);
                $msg .= " succesfully deleted.";
                $session->message($msg);
                redirect_to("mngCustomers.php");
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
        <li class="breadcrumb-item"><a href="administration.php">Administration</a></li>
        <li class="breadcrumb-item active">Manage Customers</li>
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
        echo output_message("Add a new customer, edit customer details or delete a customer.");
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
        <h3 class="panel-title" style="color: #476692">Management Customer</h3>
      </div><!-- /.panel-heading -->
      <form data-toggle="validator" class="form-horizontal" action="mngCustomers.php" method="post">
        <div class="panel-body">

          <div class="col-md-12">

            <div class="col-md-12 form_space">
              <h3 style="text-align:left;padding-bottom:5px">Add Customer</h3>
            </div><!-- /.col-md-12 form_space -->
            <div class="col-md-12" style="background-color: #ffa834; height: 3px;margin-bottom: 20px"></div>
                
                <input type="hidden" class="form-control" value=<?php echo $session->gym_id ?> 
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
                      <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
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
                            data-error="Phone number cannot be blank and must consists of 
                            exactly 10 digits." required>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter customer phone number.</div>
                    </div><!-- /.col-xs-10 --> 
                </div><!-- /.form-group row form_space has-feedback -->

                <div class="form-group row form_space has-feedback">
                    <label for="barcodeInput" class="col-xs-2 col-form-label">BARCODE:
                    </label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-barcode"></i>
                            </span>
                            <input class="form-control" type="text" name="barcodeInput"
                            id="barcodeInput" pattern="[0-9]{6}$" maxlength=6 
                            data-error="Barcode cannot be blank and must consists of 
                            exactly 6 digits." required>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter barcode number.</div>
                    </div><!-- /.col-xs-10 --> 
                </div><!-- /.form-group row form_space has-feedback -->

                <div class="form-group row form_space has-feedback">
                  <label for="gym_name" class="col-xs-2 col-form-label">ΓΥΜΝΑΣΤΗΡΙΟ:</label>
                  <div class="col-xs-10">
                    <div class="input-group">
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-registration-mark"></span>
                      </span>
                      <input type="text" class="form-control" value="<?php echo $session->gym_name ?>"
                      name="gym_name" id="gym_name" data-error="Gym name is automatic 
                      filled in, readonly field." readonly>
                      <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div><!-- /.input-group -->
                    <div class="help-block with-errors">Gym name.</div>
                  </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->
          </div><!-- /.col-md-12 --> 
        </div><!-- /.panel-body -->

        <div class="panel-footer" id="add_customer_panel">
            <div class="form-group" style="padding-right: 15px;margin-bottom: 0px;">
              <button type="submit" name="add_customer" class="btn btn-success pull-right">Add Customer
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
              <th>Name</th>
              <th>Telephone</th>
              <th>Barcode</th>
              <th>Gym Name</th>
              <th></th>
              <th></th>
              </tr>
          </thead>
          <tfoot>
            <tr>
              <th colspan="3"></th>
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

<div class="modal fade" id="deletecustomer" tabindex="-1" role="dialog"
 aria-labelledby="deletecustomerLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="deletecustomerLabel"></h4>
      </div><!-- /.modal-header -->
      <div class="modal-body">
       
      </div><!-- /.modal-body -->
      <div class="modal-footer">
        <form action="mngCustomers.php" method="post">    
            <input type="hidden" class="form-control" name="delete_customer_id" id="delete_customer_id">
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_customer" class="btn btn-warning">Delete Customer</button>
      </div><!-- /.modal-footer -->
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade bs-editcustomer-modal-lg" id="editcustomer" tabindex="-1"
 role="dialog" aria-labelledby="editcustomerLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="editcustomerLabel"></h4>
      </div>
      <div class="modal-body">
        <form action="mngCustomers.php" data-toggle="validator" method="post">
          <input type="hidden" class="form-control" name="edit_customer_id" id="edit_customer_id"> 
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>
          <div class="form-group row form_space has-feedback">
            <label for="edit_customer_name" class="col-xs-2 col-form-label">ΠΕΛΑΤΗΣ:</label>
            <div class="col-xs-10">
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-user"></span>
                </span>
                <input class="form-control" type="text" name="edit_customer_name"
                id="edit_customer_name" pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$"
                data-error="Customer name cannot be blank and must consists 
                of latin or greek characters only." required>
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
              </div><!-- /.input-group -->
              <div class="help-block with-errors">Enter customer full name.
              </div>
            </div><!-- /.col-xs-10 --> 
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="edit_telephone" class="col-xs-2 col-form-label">ΤΗΛΕΦΩΝΟ:</label>
            <div class="col-xs-10">
              <div class="input-group">
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-phone-alt"></span>
                </span>
                <input class="form-control" type="text" name="edit_telephone" id="edit_telephone"
                pattern="[0-9]{10}$" maxlength=10 data-error="Phone number cannot 
                be blank and must consists of exactly 10 digits." required>
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
              </div><!-- /.input-group -->
              <div class="help-block with-errors">Enter customer phone number.</div>
            </div><!-- /.col-xs-10 --> 
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="edit_barcode" class="col-xs-2 col-form-label">BARCODE:</label>
            <div class="col-xs-10">
              <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-barcode"></i>
                </span>
                <input class="form-control" type="text" name="edit_barcode" id="edit_barcode"
                pattern="[0-9]{6}$" maxlength=6 data-error="Barcode cannot 
                be blank and must consists of exactly 6 digits." required>
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
              </div><!-- /.input-group -->
              <div class="help-block with-errors">Enter barcode number.</div>
            </div><!-- /.col-xs-10 --> 
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="edit_gym_id" class="col-xs-2 col-form-label">ΓΥΜΝΑΣΤΗΡΙΟ:</label>
            <div class="col-xs-10">
              <div class="input-group">
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-registration-mark"></span>
                </span>
                <select class="form-control" name="edit_gym_id" data-live-search="true" 
                id="edit_gym_id" data-error="Gym name must be a valid choice and cannot be blank.">
                </select>
              </div><!-- /.input-group -->
              <div class="help-block with-errors">Select gym name.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->

      </div><!-- /.modal-body -->
      <div class="modal-footer">
          <div class="form-group row form_space pull-right"  style="padding-right: 15px">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_customer" class="btn btn-warning">Save Changes</button>
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

  var title = $('#json_gym thead th').eq($(this).index()+2).text();
          
  $(this).html('<input type="text" placeholder="Search ' + title + '"/>');
});

$.ajax({
    url:'../../datatables_init/json_customer.php',
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
      "bDeferRender": true,
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
        {'data': 'name'},
        {'data': 'telephone'},
        {'data': 'barcode'},
        {'data': 'gym_name'},
        {'data': 'id'},
        {'data': 'id'},
      ],
      columnDefs: [
        {
          targets: 4,
          orderable: false,
          searchable: false,
          className: 'dt-body-center',
          render: function(data, type, full, meta) {
            edit_customer = '';
            edit_customer += '<button type="button" class="btn btn-warning" data-toggle="modal"'
            edit_customer += 'data-target=".bs-editcustomer-modal-lg" data-customerid="';
            edit_customer += data;
            edit_customer += '">';
            edit_customer += '<span class="glyphicon glyphicon-edit" style="color: #000"></span>';
            edit_customer += '</button>';
            return edit_customer;
          }
        },
        {
          targets: 5,
          orderable: false,
          searchable: false,
          className: 'dt-body-center',
          render: function(data, type, full, meta) {
            delete_customer = '';
            delete_customer += '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deletecustomer" data-customerid="';
            delete_customer += data;
            delete_customer += '">';
            delete_customer += '<span class="glyphicon glyphicon-trash" style="color: #000"></span>';
            delete_customer += '</button>';
            return delete_customer;
          }
        }
      ],
    }); // DataTable ends here
    table.column(3).every(function(){
      var tableColumn = this;

      $(this.footer()).find('input').on('keyup change', function(){

        var term = $(this).val();
        tableColumn.search(term).draw();                   
      });          
    });
  } // success function ends here
});  // ajax request ends here

$('#deletecustomer').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('customerid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    // $('#deleteuser').find('.modal-title').text('Delete user: ');
    $.ajax({
        url:'../../modal_ajax/modal_customer_json.php',
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
        var modal = $('#deletecustomer');
        modal.find('.modal-title').text('Delete Customer with ID: ' + data.customer.id);
        modal.find('.modal-body').text('Are you sure that you want to delete user: ' + data.customer.name + ' ?');
        modal.find('.modal-footer input#delete_customer_id[type="hidden"]').val(recipient);
      } // success function ends here
    }); // ajax request ends here
  }); // modal ends here

$('#editcustomer').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('customerid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    $.ajax({
        url:'../../modal_ajax/modal_customer_json.php',
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
        var modal = $('#editcustomer');
        modal.find('.modal-title').text('Edit Customer: ' + data.customer.name);
        modal.find('.modal-body input#edit_customer_id[type="hidden"]').val(recipient);
        modal.find('.modal-body input[name="edit_customer_name"]').val(data.customer.name);
        modal.find('.modal-body input[name="edit_barcode"]').val(data.customer.barcode);
        modal.find('.modal-body input[name="edit_telephone"]').val(data.customer.telephone);

        var select = document.getElementById("edit_gym_id");

        $.each(data.gyms, function(key, value) {
          var option = document.createElement("option");
          option.text = value.gym_name;
          option.value = value.gym_id;
          select.appendChild(option);
        });

        $('#edit_gym_id').addClass('selectpicker');
        $('#edit_gym_id').addClass(' show-tick');
        $('#edit_gym_id').selectpicker('val', data.customer.gym_id);

        $('#edit_gym_id').selectpicker({
          style: 'btn-default',
          size: 4
        });
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
