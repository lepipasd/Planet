<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in() or !$session->is_session_valid()) {
    $session->logout();
    redirect_to("login.php");
}
if (request_is_same_domain() and request_is_post()) {
    $form_params = ['incomeName', 'price', 'description'];
    $msg = "";
    // manage form submission for adding a gym
    if (isset($_POST['add_income'])) {
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
            redirect_to("mngIncome.php");
        }
        $valid_form = new Form($form_params);
        $valid_post_params = $valid_form->allowed_post_params();
        $income_name = mb_strtoupper($valid_post_params['incomeName'], "UTF-8");
        $check_income_name = Income::find_incomes_by_name($income_name);
        if ($check_income_name > 0) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: Income name: ";
            $msg .= h($income_name);
            $msg .= " already exists in the database. Try again.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $income = new Income();
            $income->income_name = $income_name;
            $income->income_price = $valid_post_params['price'];
            $income->description = $valid_post_params['description'];
            $msg = $income->validate_income_input_fields();
        }

        if ($msg == "") {
            $result = $income->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Income created with ID: ";
            $msg .= h($result);

            $session->message($msg);
            redirect_to("mngIncome.php");
        } else {
            $session->message($msg);
            redirect_to("mngIncome.php");
        }
    }
    $form_edit_params = ['edit_income_id', 'editincomeName', 'edit_price', 'edit_description'];
    // manage modal-form submission for editing gym details
    if (isset($_POST['edit_income'])) {
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
            redirect_to("mngIncome.php");
        }
        $valid_edit_form = new Form($form_edit_params);
        $valid_post_edit_params = $valid_edit_form->allowed_post_params();
        // check id is valid (presence - inclusion).
        $check_income_id = has_presence($valid_post_edit_params['edit_income_id']);
        $allowed_income_ids = Income::find_array_of_income_ids();
        $check_income_id_inclusion =
        has_inclusion_in($valid_post_edit_params['edit_income_id'], $allowed_income_ids);
        // find income by valid id.
        $income_details = Income::find_by_primary_key($valid_post_edit_params['edit_income_id']);
        // check name for uniquness.
        $income_name_edit = mb_strtoupper($valid_post_edit_params['editincomeName'], "UTF-8");
        $check_income_name_edit_uniquness = Income::find_incomes_by_name($income_name_edit);

        if (!$check_income_id or !$check_income_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Income ID: ";
            $msg .= h($valid_post_edit_params['edit_income_id']);
            $msg .= " cannot be blank and must be a valid choice: ";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } elseif ($income_details->income_name != $income_name_edit and
            $check_income_name_edit_uniquness > 0) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Warning: Income name: ";
            $msg .= h($income_name_edit);
            $msg .= " already exists in the database. Try again.";
            $msg .= "</li>";
            $msg .=  "</ul>";
        } else {
            $income_edit = new Income();
            $income_edit->income_id = $valid_post_edit_params['edit_income_id'];
            $income_edit->income_name = $income_name_edit;
            $income_edit->income_price = $valid_post_edit_params['edit_price'];
            $income_edit->description = $valid_post_edit_params['edit_description'];
            $msg = $income_edit->validate_income_input_fields();
        }

        if ($msg == "") {
            $result = $income_edit->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Income with ID: ";
            $msg .= $result;
            $msg .= " has been edited.";

            $session->message($msg);
            redirect_to("mngIncome.php");
        } else {
            $session->message($msg);
            redirect_to("mngIncome.php");
        }
    }
    $form_delete_params = ['income_id'];
    // manage form submission for deleting a gym
    if (isset($_POST['delete_income'])) {
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
            redirect_to("mngIncome.php");
        }
        $valid_form_delete = new Form($form_delete_params);
        $valid_post_delete_params = $valid_form_delete->allowed_post_params();
        $income_id_delete = $valid_post_delete_params['income_id'];
        $allowed_income_ids = Income::find_array_of_income_ids();
        $check_income_id_inclusion =
        has_inclusion_in($valid_post_delete_params['income_id'], $allowed_income_ids);

        $numbers_of_products_customers = IncomeReport::find_income_report_by_income_id($income_id_delete);

        if (($numbers_of_products_customers > 0) and $check_income_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Income with ID: ";
            $msg .= h($income_id_delete);
            $msg .= " has been selled to ";
            $msg .= h($numbers_of_products_customers);
            $msg .= " . Cannot delete Income.";
            $msg .= "</li>";
            $msg .= "</ul>";
            $session->message($msg);
            redirect_to("mngIncome.php");
        } elseif (!$check_income_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Income ID: ";
            $msg .= h($income_id_delete);
            $msg .= " cannot be blank and must be a valid choice: ";
            $msg .= "</li>";
            $msg .= "</ul>";
            $session->message($msg);
            redirect_to("mngIncome.php");
        } else {
            $income_delete = new Income();
            $income_delete->income_id = $income_id_delete;
            $result_delete = $income_delete->delete();
            if (!$result_delete) {
                $msg .= "Unable to delete Income with ID: ";
                $msg .= h($income_id_delete);
                $session->message($msg);
                redirect_to("mngIncome.php");
            } else {
                $log_msg  = "User ";
                $log_msg .= h($session->real_name);
                $log_msg .= " with ID: ";
                $log_msg .= h($session->login_user_id);
                $log_msg .= " as ";
                $log_msg .= h($session->role_name);
                $log_msg .= " deleted Income with ID: ";
                $log_msg .= h($income_id_delete);
                logger("WARNING:", $log_msg);
                $msg .= $csrf_msg;
                $msg .= "Income with ID: ";
                $msg .= h($income_id_delete);
                $msg .= " succesfully deleted.";
                $session->message($msg);
                redirect_to("mngIncome.php");
            }
        }
    }
}
include('../../includes/layouts/header.php');
include('../../includes/layouts/menu.php');
?>
<div class="container-fluid main_center">	
    <ol class="breadcrumb" style="margin-top: 20px;">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="management.php">Management</a></li>
        <li class="breadcrumb-item active">Manage Income</li>
    </ol>
    <?php if (!empty($message) and strpos($message, 'created')) : ?>
    <div class="alert alert-success message_manage">
    <span class="glyphicon glyphicon-ok"></span> 
    <?php echo output_message($message); ?>
    <?php elseif (!empty($message) and (strpos($message, 'edited') or strpos($message, 'deleted'))) : ?>
        <div class="alert alert-success message_manage">
        <span class="glyphicon glyphicon-ok"></span> 
        <?php echo output_message($message); ?>
    <?php elseif (!empty($message) and (strpos($message, 'Unable') or strpos($message, 'Fix'))) : ?>
        <div class="alert alert-warning message_manage">
        <span class="glyphicon glyphicon-alert"></span> 
        <?php echo output_message($message); ?>
    <?php elseif (empty($message)) : ?>
        <div class="alert alert-info message_manage">
        <span class="glyphicon glyphicon-info-sign"></span> 
        <?php echo output_message("Add a new Income, edit or delete an existing one."); ?>
    <?php else : ?>
        <div class="alert alert-danger message_manage">
        <span class="glyphicon glyphicon-info-remove"></span> 
        <?php echo output_message($message); ?>
    <?php endif; ?>
    </div><!-- /.alert alert-info message -->
    <div id='ajax_loader'>
        <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i></br>
    </div>
    <div class="panel panel-warning">
        <div class="panel-heading">
            <h3 class="panel-title" style="color: #476692">Create Product - Service
             <i class="fa fa-pencil-square"></i></h3>
        </div><!-- /.panel-heading -->
        
        <form class="form-horizontal" data-toggle="validator"  action="mngIncome.php" method="post">
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-12 form_space">
                        <h3 style="text-align:left;padding-bottom:5px">Add new Item 
                        <i class="fa fa-plus-square"></i></h3>
                    </div><!-- /.col-md-12 form_space -->
                    <div class="col-md-12" style="background-color: #ffa834; height: 3px;margin-bottom: 20px"></div>
                    <?php echo $session->csrf_token_tag(); ?>
                    <?php echo $session->csrf_token_tag_time(); ?>
                    <div class="form-group row form_space has-feedback">
                        <label for="incomeName" class="col-xs-2 col-form-label">ΠΡΟΪΟΝ - ΥΠΗΡΕΣΙΑ:</label>
                        <div class="col-xs-10">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-trademark"></i>
                                </span>
                                <input class="form-control" type="text" name="incomeName" 
                                id="incomeName" data-error="Product - Service name cannot be blank." required>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div><!-- /.input-group -->
                            <div class="help-block with-errors">Enter Product - Service name.</div>
                        </div><!-- /.col-xs-10 -->
                    </div><!-- /.form-group row form_space -->

                    <div class="form-group row form_space has-feedback">
                        <label for="price" class="col-xs-2 col-form-label">ΤΙΜΗ:</label>
                        <div class="col-xs-10">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-euro"></span>
                                </span>
                                <input class="form-control" type="text" name="price" 
                                id="price" pattern="[0-9.]{1,}$" 
                                data-error="Price must consists only of digits 0-9. 
                                Use a dot [.] for decimal number.">
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div><!-- /.input-group -->
                            <div class="help-block with-errors">Enter price paied.</div>
                        </div><!-- /.col-xs-10 -->
                    </div><!-- /.form-group row form_space -->

                    <div class="form-group row form_space">
                        <label for="description" class="col-xs-2 col-form-label">ΠΕΡΙΓΡΑΦΗ:</label>
                        <div class="col-xs-10">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-text-height"></span>
                                </span>
                                <textarea class="form-control vresize" name="description" 
                                id="description"></textarea>
                            </div><!-- /.input-group -->
                            <div class="help-block with-errors">Description must not exceed 200 characters.</div>
                        </div><!-- /.col-xs-10 -->
                    </div><!-- /.form-group row form_space -->

                </div><!-- /.col-md-12 --> 
            </div><!-- /.panel-body -->
            <div class="panel-footer" id="add_user_panel">
                <div class="form-group" style="padding-right: 15px;margin-bottom: 0px;">
                    <button type="submit" name="add_income" class="btn btn-success pull-right">Add Service
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
              <th>ΠΡΟΪΟΝ - ΥΠΗΡΕΣΙΑ</th>
              <th>ΤΙΜΗ</th>
              <th>ΠΕΡΙΓΡΑΦΗ</th>
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

<div class="modal fade" id="deleteincome" tabindex="-1" role="dialog" aria-labelledby="deleteincomeLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" 
                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="deleteincomeLabel"></h4>
            </div><!-- /.modal-header -->
            <div class="modal-body">
            </div><!-- /.modal-body -->
            <div class="modal-footer">
                <form action="mngIncome.php" id="form_delete" method="post">    
                    <input type="hidden" class="form-control" name="income_id" id="income_id">
                    <?php echo $session->csrf_token_tag(); ?>
                    <?php echo $session->csrf_token_tag_time(); ?>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_income" class="btn btn-warning">Delete Income</button>
            </div><!-- /.modal-footer -->
                </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade bs-editincome-modal-lg" id="editincome" tabindex="-1" 
 role="dialog" aria-labelledby="editincomeLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="editincomeLabel"></h4>
            </div>
            <div class="modal-body">
            <form action="mngIncome.php" data-toggle="validator" method="post">
                <input type="hidden" class="form-control" name="edit_income_id" 
                id="edit_income_id">
                <?php echo $session->csrf_token_tag(); ?>
                <?php echo $session->csrf_token_tag_time(); ?>
                <div class="form-group row form_space has-feedback">
                    <label for="editincomeName" class="col-xs-2 col-form-label">Income Name:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-trademark"></i>
                            </span>
                            <input class="form-control" type="text" name="editincomeName" 
                            id="editincomeName_mng" data-error="Income name cannot be blank." required>
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter Income name.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                    <label for="edit_price" class="col-xs-2 col-form-label">ΤΙΜΗ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-euro"></span>
                            </span>
                            <input class="form-control" type="text" name="edit_price" 
                            id="edit_price" pattern="[0-9.]{1,}$" 
                            data-error="Price must consists only of digits 0-9. 
                            Use a dot [.] for decimal number.">
                            <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Enter price paied.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                    <label for="edit_description" class="col-xs-2 col-form-label">ΠΕΡΙΓΡΑΦΗ:</label>
                    <div class="col-xs-10">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-text-height"></span>
                            </span>
                            <textarea class="form-control vresize" name="edit_description" 
                            id="edit_description"></textarea>
                        </div><!-- /.input-group -->
                        <div class="help-block with-errors">Description must not exceed 200 characters.</div>
                    </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->                 
            </div><!-- /.modal-body -->
            <div class="modal-footer">
                <div class="form-group row form_space pull-right" style="padding-right: 15px">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_income" class="btn btn-warning">Save Changes</button>
                </div><!-- /.form-group row form_space -->
            </div><!-- /.modal-footer -->
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$(function () { 
    $.ajax({
    url:'../../datatables_init/json_income.php',
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
          {'data': 'income_name'},
          {'data': 'income_price'},
          {'data': 'description'},
          {'data': 'income_id'},
          {'data': 'income_id'},
        ],
        columnDefs: [
        {
            targets: 1,
            orderable: true,
            searchable: true,
            className: 'dt-body-center',
            render: function(data, type, full, meta) {
              return data + '\u20AC';
            }
          },
          {
            targets: 3,
            orderable: false,
            searchable: false,
            className: 'dt-body-center',
            render: function(data, type, full, meta) {
              edit_income = '';
              edit_income += '<button type="button" class="btn btn-warning" data-toggle="modal" data-target=".bs-editincome-modal-lg" data-incomeid="';
              edit_income += data;
              edit_income += '">';
              edit_income += '<span class="glyphicon glyphicon-edit" style="color: #000"></span>';
              edit_income += '</button>';
              return edit_income;
            }
          },
          {
            targets: 4,
            orderable: false,
            searchable: false,
            className: 'dt-body-center',
            render: function(data, type, full, meta) {
              delete_income = '';
              delete_income += '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteincome" data-incomeid="';
              delete_income += data;
              delete_income += '">';
              delete_income += '<span class="glyphicon glyphicon-trash" style="color: #000"></span>';
              delete_income += '</button>';
              return delete_income;
            }
          }
        ],
      }); // DataTable ends here
    } // success function ends here
  });  // ajax request ends here

  $('#deleteincome').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('incomeid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    // console.log(recipient)
    $.ajax({
        url:'../../modal_ajax/modal_income_json.php',
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
        var modal = $('#deleteincome');
        modal.find('.modal-title').text('Delete Income: ' + data.income.income_name);
        msg = '<p>';
        msg += 'Are you sure that you want to delete Income: ';
        msg += data.income.income_name;
        msg += '?';
        msg += '</p>';
        modal.find('.modal-body').html(msg);
        modal.find('.modal-footer input#income_id[type="hidden"]').val(recipient);
      } // success function ends here
    }); // ajax request ends here
  }); // modal ends here

  $('#editincome').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('incomeid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    $.ajax({
        url:'../../modal_ajax/modal_income_json.php',
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
        var modal = $('#editincome');
        modal.find('.modal-title').text('Edit Income: ' + data.income.income_name);
        modal.find('.modal-body input#edit_income_id[type="hidden"]').val(recipient);
        modal.find('.modal-body input[name="editincomeName"]').val(data.income.income_name);
        modal.find('.modal-body input[name="edit_price"]').val(data.income.income_price);
        modal.find('.modal-body textarea[name="edit_description"]').val(data.income.description);
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

