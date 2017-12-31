<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in() or !$session->is_session_valid()) {
    $session->logout();
    redirect_to("login.php");
}
if (request_is_same_domain() and request_is_post()) {
    $form_params = ['provider_name', 'gym_id', 'telephoneInput', 'csrf_token', 'csrf_token_time'];
    $msg = "";
    $csrf_msg = "";
    // manage form submission for adding a provider
    if (isset($_POST['add_provider'])) {
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
            redirect_to("mngProviders.php");
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
            $provider = new Provider();
            $provider->provider_name = mb_strtoupper($valid_post_params['provider_name'], "UTF-8");
            $provider->telephone = $valid_post_params['telephoneInput'];
            $provider->gym_id = $check_gym_id->gym_id;
            $msg = $provider->validate_provider_input_fields();
        }
        if ($msg == "") {
            $result = $provider->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Provider created with id: ";
            $msg .= h($result);

            $session->message($msg);
            redirect_to("mngProviders.php");
        } else {
            $session->message($msg);
            redirect_to("mngProviders.php");
        }
    }
    $form_edit_params = ['edit_provider_id', 'edit_provider_name', 'edit_telephone', 'edit_gym_id',
    'csrf_token', 'csrf_token_time'];
    // manage modal-form submission for editing user details
    if (isset($_POST['edit_provider'])) {
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
        }
        $valid_edit_form = new Form($form_edit_params);
        $valid_post_edit_params = $valid_edit_form->allowed_post_params();
        $check_provider_id = has_presence($valid_post_edit_params['edit_provider_id']);
        $allowed_provider_ids = Provider::find_array_of_providers_ids();
        $check_provider_id_inclusion =
        has_inclusion_in($valid_post_edit_params['edit_provider_id'], $allowed_provider_ids);
        if (!$check_provider_id or !$check_provider_id_inclusion) {
            $msg .= "<li>";
            $msg .= "Provider: ";
            $msg .= h($valid_post_edit_params['edit_provider_id']);
            $msg .= " cannot be blank and must be a valid choice: ";
            $msg .= "</li>";
        } else {
            $provider_edit = new Provider();
            $provider_edit->provider_id = $valid_post_edit_params['edit_provider_id'];
            $provider_edit->provider_name = mb_strtoupper($valid_post_edit_params['edit_provider_name'], "UTF-8");
            $provider_edit->telephone = $valid_post_edit_params['edit_telephone'];
            $provider_edit->gym_id = $valid_post_edit_params['edit_gym_id'];
            $msg = $provider_edit->validate_provider_input_fields();
        }
        if ($msg == "") {
            $result = $provider_edit->save();
            $msg .= $csrf_msg;
            $msg .= "Passed Validation Tests. ";
            $msg .= "Provider with id: ";
            $msg .= h($result);
            $msg .= " has been edited.";

            $session->message($msg);
            redirect_to("mngProviders.php");
        } else {
            $session->message($msg);
            redirect_to("mngProviders.php");
        }
    }
    $form_delete_params = ['delete_provider_id', 'csrf_token', 'csrf_token_time'];
    // manage form submission for deleting a user
    if (isset($_POST['delete_provider'])) {
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
        }
        $valid_form_delete = new Form($form_delete_params);
        $valid_post_delete_params = $valid_form_delete->allowed_post_params();
        $provider_id_delete = $valid_post_delete_params['delete_provider_id'];
        $allowed_provider_ids = Provider::find_array_of_providers_ids();
        $check_provider_id_inclusion = has_inclusion_in($provider_id_delete, $allowed_provider_ids);
        $numbers_of_products = OutcomeReport::find_outcome_report_by_provider_id($provider_id_delete);

        if ($numbers_of_products > 0 and $check_provider_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Provider with ID: ";
            $msg .= h($provider_id_delete);
            $msg .= " is related with ";
            $msg .= h($numbers_of_products);
            $msg .= " products. Cannot delete him/her.";
            $msg .= "</li>";
            $msg .= "</ul>";
            $session->message($msg);
            redirect_to("mngProviders.php");
        } elseif (!$check_provider_id_inclusion) {
            $msg  = "Fix the following error(s): ";
            $msg .="<ul style='text-align:left;margin-left:33%'>";
            $msg .= "<li>";
            $msg .= "Provider: ";
            $msg .= h($this->provider_id);
            $msg .= " cannot be blank and must be a valid choice: ";
            $msg .= "</li>";
            $msg .= "</ul>";
            $session->message($msg);
            redirect_to("mngProviders.php");
        } else {
            $provider_delete = new Provider();
            $provider_delete->provider_id = $provider_id_delete;
            $result_delete = $provider_delete->delete();
            if (!$result_delete) {
                $msg .= "Unable to delete provider with id: ";
                $msg .= h($provider_id_delete);
                $session->message($msg);
                redirect_to("mngProviders.php");
            } else {
                $log_msg  = "User ";
                $log_msg .= h($session->real_name);
                $log_msg .= " with ID: ";
                $log_msg .= h($session->login_user_id);
                $log_msg .= " as ";
                $log_msg .= h($session->role_name);
                $log_msg .= " deleted provider with ID: ";
                $log_msg .= h($provider_id_delete);
                logger("WARNING:", $log_msg);
                $msg .= $csrf_msg;
                $msg .= "Provider with id: ";
                $msg .= h($provider_id_delete);
                $msg .= " succesfully deleted.";
                $session->message($msg);
                redirect_to("mngProviders.php");
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
        <li class="breadcrumb-item active">Manage Providers</li>
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
        echo output_message("Add a new provider, edit provider details or delete a provider.");
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
        <h3 class="panel-title" style="color: #476692">Management Provider</h3>
      </div><!-- /.panel-heading -->
      <form data-toggle="validator" class="form-horizontal" action="mngProviders.php" method="post">
        <div class="panel-body">

          <div class="col-md-12">

            <div class="col-md-12 form_space">
              <h3 style="text-align:left;padding-bottom:5px">Add Provider</h3>
            </div><!-- /.col-md-12 form_space -->
            <div class="col-md-12" style="background-color: #ffa834; height: 3px;margin-bottom: 20px"></div>

                <input type="hidden" class="form-control" value=<?php echo $session->gym_id ?> 
          name="gym_id" id="gym_id">
                <?php echo $session->csrf_token_tag(); ?>
                <?php echo $session->csrf_token_tag_time(); ?>
                <div class="form-group row form_space has-feedback">
                  <label for="provider_name" class="col-xs-2 col-form-label">ΠΡΟΜΗΘΕΥΤΗΣ:</label>
                  <div class="col-xs-10">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-user"></span>
                      </span>
                      <input class="form-control" type="text" name="provider_name" 
                      id="provider_name" pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$" 
                      data-error="Provider name cannot be blank and must consists 
                      of latin or greek characters only." required>
                      <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div><!-- /.input-group --> 
                    <div class="help-block with-errors">Enter provider full name.</div>
                  </div><!-- /.col-xs-10 --> 
                </div><!-- /.form-group row form_space --> 

                <div class="form-group row form_space has-feedback">
                <label for="telephoneInput" class="col-xs-2 col-form-label">ΤΗΛΕΦΩΝΟ:</label>
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
                  <div class="help-block with-errors">Provide provider phone number.</div>
                </div><!-- /.col-xs-10 --> 
              </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space has-feedback">
                  <label for="gym_name" class="col-xs-2 col-form-label">ΓΥΜΝΑΣΤΗΡΙΟ:</label>
                  <div class="col-xs-10">
                    <div class="input-group">
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-registration-mark"></span>
                      </span>
                      <input type="text" class="form-control" value="<?php echo $session->gym_name ?>"
                      name="gym_name" id="gym_name" data-error="Customer name cannot be blank." readonly>
                      <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                    </div><!-- /.input-group -->
                    <div class="help-block with-errors">Gym name.</div>
                  </div><!-- /.col-xs-10 -->
                </div><!-- /.form-group row form_space -->

          </div><!-- /.col-md-12 --> 
        </div><!-- /.panel-body -->

        <div class="panel-footer" id="add_provider_panel">
            <div class="form-group" style="padding-right: 15px;margin-bottom: 0px;">
              <button type="submit" name="add_provider" class="btn btn-success pull-right">Add Provider
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
              <th>ID</th>
              <th>Name</th>
              <th>Telephone</th>
              <th>Gym Name</th>
              <th></th>
              <th></th>
              </tr>
          </thead>
          <tfoot>
            <tr>
              <th colspan="1"></th>
              <th colspan="1" id="searchgym"">Gym Name</th>
              <th colspan="4"></th>
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

<div class="modal fade" id="deleteprovider" tabindex="-1" role="dialog" aria-labelledby="deleteproviderLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="deleteproviderLabel"></h4>
      </div><!-- /.modal-header -->
      <div class="modal-body">
       
      </div><!-- /.modal-body -->
      <div class="modal-footer">
        <form action="mngProviders.php" method="post">    
            <input type="hidden" class="form-control" name="delete_provider_id" id="delete_provider_id">
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>
          <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
          <button type="submit" name="delete_provider" class="btn btn-warning">Delete Provider</button>
      </div><!-- /.modal-footer -->
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade bs-editprovider-modal-lg" id="editprovider" tabindex="-1" 
 role="dialog" aria-labelledby="editproviderLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="editproviderLabel"></h4>
      </div>
      <div class="modal-body">
        <form action="mngProviders.php" data-toggle="validator" method="post">
          <input type="hidden" class="form-control" name="edit_provider_id" id="edit_provider_id"> 
            <?php echo $session->csrf_token_tag(); ?>
            <?php echo $session->csrf_token_tag_time(); ?>
          <div class="form-group row form_space has-feedback">
            <label for="edit_provider_name" class="col-xs-2 col-form-label">ΠΡΟΜΗΘΕΥΤΗΣ:</label>
            <div class="col-xs-10">
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-user"></span>
                </span>
                <input class="form-control" type="text" name="edit_provider_name" 
                id="edit_provider_name" pattern="[a-zA-Zα-ωΑ-Ω ]{1,}$" 
                data-error="Provider name cannot be blank and must consists 
                of latin or greek characters only." required>
                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                </div><!-- /.input-group --> 
                <div class="help-block with-errors">Enter provider full name.</div>
              </div><!-- /.col-xs-10 --> 
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="edit_telephone" class="col-xs-2 col-form-label">ΤΗΛΕΦΩΝΟ:</label>
            <div class="col-xs-10">
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-phone-alt"></span>
                </span>
              <input class="form-control" type="text" name="edit_telephone" 
              id="edit_telephone" pattern="[0-9]{10}$" maxlength=10 
              data-error="Phone number cannot be blank and must consists of 
              exactly 10 digits." required>
              <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
              </div><!-- /.input-group -->
              <div class="help-block with-errors">Enter provider phone number.</div>
            </div><!-- /.col-xs-10 --> 
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space has-feedback">
            <label for="edit_gym_id" class="col-xs-2 col-form-label">ΓΥΜΝΑΣΤΗΡΙΟ:</label>
            <div class="col-xs-10">
              <div class="input-group">
                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-registration-mark"></span>
                </span>
                <select class="form-control" name="edit_gym_id" data-live-search="true" id="edit_gym_id">
                </select>
              </div><!-- /.input-group -->
              <div class="help-block with-errors">Select gym name.</div>
            </div><!-- /.col-xs-10 -->
          </div><!-- /.form-group row form_space -->
      </div><!-- /.modal-body -->
      <div class="modal-footer">
          <div class="form-group row form_space pull-right"  style="padding-right: 15px">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_provider" class="btn btn-warning">Save Changes</button>
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

  var title = $('#json_gym thead th').eq($(this).index()).text();
          
  $(this).html('<input type="text" placeholder="Search ' + title + '"/>');
});

$.ajax({
    url:'../../datatables_init/json_provider.php',
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
        {'data': 'provider_id'},
        {'data': 'provider_name'},
        {'data': 'telephone'},
        {'data': 'gym_name'},
        {'data': 'provider_id'},
        {'data': 'provider_id'},
      ],
      columnDefs: [
        {
          targets: 4,
          orderable: false,
          searchable: false,
          className: 'dt-body-center',
          render: function(data, type, full, meta) {
            edit_provider = '';
            edit_provider += '<button type="button" class="btn btn-warning" data-toggle="modal"'
            edit_provider += 'data-target=".bs-editprovider-modal-lg" data-providerid="';
            edit_provider += data;
            edit_provider += '">';
            edit_provider += '<span class="glyphicon glyphicon-edit" style="color: #000"></span>';
            edit_provider += '</button>';
            return edit_provider;
          }
        },
        {
          targets: 5,
          orderable: false,
          searchable: false,
          className: 'dt-body-center',
          render: function(data, type, full, meta) {
            delete_provider = '';
            delete_provider += '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteprovider" data-providerid="';
            delete_provider += data;
            delete_provider += '">';
            delete_provider += '<span class="glyphicon glyphicon-trash" style="color: #000"></span>';
            delete_provider += '</button>';
            return delete_provider;
          }
        }
      ],
    }); // DataTable ends here
    table.column(1).every(function(){
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

$('#deleteprovider').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('providerid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    // $('#deleteuser').find('.modal-title').text('Delete user: ');
    $.ajax({
        url:'../../modal_ajax/modal_provider_json.php',
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
        var modal = $('#deleteprovider');
        modal.find('.modal-title').text('Delete Provider with ID: ' + data.provider.provider_id);
        modal.find('.modal-body').text('Are you sure that you want to delete user: ' + data.provider.provider_name + ' ?');
        modal.find('.modal-footer input#delete_provider_id[type="hidden"]').val(recipient);
      } // success function ends here
    }); // ajax request ends here
  }); // modal ends here

$('#editprovider').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('providerid') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    $.ajax({
        url:'../../modal_ajax/modal_provider_json.php',
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
        // console.log(data)
        var modal = $('#editprovider');
        modal.find('.modal-title').text('Edit Provider: ' + data.provider.provider_name);
        modal.find('.modal-body input#edit_provider_id[type="hidden"]').val(recipient);
        modal.find('.modal-body input[name="edit_provider_name"]').val(data.provider.provider_name);
        modal.find('.modal-body input[name="edit_telephone"]').val(data.provider.telephone);

        var select = document.getElementById("edit_gym_id");

        $.each(data.gyms, function(key, value) {
          var option = document.createElement("option");
          option.text = value.gym_name;
          option.value = value.gym_id;
          select.appendChild(option);
        });

        $('#edit_gym_id').addClass('selectpicker');
        $('#edit_gym_id').addClass(' show-tick');
        $('#edit_gym_id').selectpicker('val', data.provider.gym_id);

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

