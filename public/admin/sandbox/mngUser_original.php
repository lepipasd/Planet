<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in()) {
    redirect_to("login.php");
}

// manage form submission for adding a user
if (isset($_POST['add_user'])) {
    $username = trim($_POST['userName']);
    $password = password_encrypt(trim($_POST['password']));
    $name = !empty(trim($_POST['name'])) ? trim($_POST['name']) : null;
    $surname = trim($_POST['surname']);
    $email = trim($_POST['emailInput']);
    $role_id = !empty($_POST['role_id']) ? (int)trim($_POST['role_id']) : 1;
    $gym_name = trim($_POST['gymName']);
    $check_name = LoginUser::find_by_username(strtolower($username));
  
    $msg = "";

    if (!empty($check_name)) {
        $counter = count($check_name);
        $msg .= $counter;
        $msg .= $counter == 1 ? " user named: " . strtolower($username) : " users named: " . strtolower($username);
        $msg .= array_shift($check_name)->username;
        $msg .= $counter == 1 ? " exists in the database." : " exist in the database";
        $session->message($msg);
        redirect_to("mngUsers.php");
    } else {
        $find_gym_id = Gym::find_by_foreign_key(mb_strtoupper($gym_name, "UTF-8"));
        if (!empty($find_gym_id)) {
            $user = new LoginUser();
            $user->username = $username;
            $user->password = $password;
            $user->name = $name;
            $user->surname = $surname;
            $user->email = $email;
            $user->role_id = $role_id;
            $user->gym_id = array_shift($find_gym_id)->gym_id;
            $result = $user->save();

            $session->message("User created with login_user_id: " . $result);
            redirect_to("mngUsers.php");
        } else {
            $session->message("Gym with name: " . strtoupper($gym_name) . " does not exist in the database.");
            redirect_to("mngUsers.php");
        }
    }
}

// manage modal-form submission for editing user details
if (isset($_POST['edit_user'])) {
    $login_user_id_edit = trim($_POST['edit_login_user_id']);
    $username_edit = trim($_POST['edituserName']);
    $name_edit = !empty(trim($_POST['editname'])) ? (int)trim($_POST['editname']) : null;
    $surname_edit = trim($_POST['editsurname']);
    $email_edit = trim($_POST['editEmailInput']);
    $role_id_edit = !empty($_POST['editroleid']) ? (int)trim($_POST['editroleid']) : null;
    $gym_name_edit = trim($_POST['editgymName']);
    $check_name = LoginUser::find_by_username(strtolower($username));
    $msg = "";

    if (count($check_name_edit) > 1) {
        $counter_edit = count($check_name_edit);
        $msg .= $counter_edit;
        $msg .= $counter_edit == 2 ? " user named: " : " users named: ";
        $msg .= array_shift($check_name_edit)->username;
        $msg .= $counter_edit == 2 ? " exists in the database." : " exist in the database";
        $session->message($msg);
        redirect_to("mngUsers.php");
    } else {
        $find_gym_id_edit = Gym::find_by_foreign_key(strtolower($gym_name_edit));
        if (!empty($find_gym_id_edit)) {
            $user_edit = new LoginUser();
            $user_edit->login_user_id = $login_user_id_edit;
            $user_edit->username = $username_edit;
            $user_edit->name = $name_edit;
            $user_edit->surname = $surname_edit;
            $user_edit->email = $email_edit;
            $user_edit->role_id = $role_id_edit;
            $user_edit->gym_id = array_shift($find_gym_id_edit)->gym_id;
            $result = $user_edit->save();

            $session->message("User with id: " . $login_user_id_edit . " has been edited.");
            redirect_to("mngUsers.php");
        } else {
            $session->message("Gym with name: " . $gym_name_edit . " does not exist in the database.");
            redirect_to("mngUsers.php");
        }
    }
}

// manage form submission for deleting a user
if (isset($_POST['delete_user'])) {
    $login_user_id_delete = $_POST['login_user_id'];
    $user_delete = new LoginUser();
    $user_delete->login_user_id = $login_user_id_delete;
    $result_delete = $user_delete->delete();
    if (!$result_delete) {
        $session->message("Unable to delete user with id: " . $login_user_id_delete);
        redirect_to("mngUsers.php");
    } else {
        $session->message("User with id: " . $login_user_id_delete . " succesfully deleted.");
        redirect_to("mngUsers.php");
    }
}

include('../../includes/layouts/header.php');
include('../../includes/layouts/menu.php');
?>
<div id="hld">
  <div class="container-fluid main_center"> 
    <?php
      if (!empty($message) and strpos($message, 'created')) {
          echo '<div class="alert alert-success message_manage">';
          echo '<span class="glyphicon glyphicon-exclamation-sign"></span> ';
          echo output_message($message);
      } elseif (!empty($message) and (strpos($message, 'edited') or strpos($message, 'deleted'))) {
          echo '<div class="alert alert-warning message_manage">';
          echo '<span class="glyphicon glyphicon-exclamation-sign"></span> ';
          echo output_message($message);
      } elseif (!empty($message) and (strpos($message, 'Unable') or strpos($message, 'database'))) {
          echo '<div class="alert alert-danger message_manage">';
          echo '<span class="glyphicon glyphicon-exclamation-sign"></span> ';
          echo output_message($message);
      } else {
          echo '<div class="alert alert-info message_manage">';
          echo '<span class="glyphicon glyphicon-info-sign"></span> ';
          echo output_message("Add a new user, edit user details or delete a user.");
      }
    ?>
    </div><!-- /.alert alert-info message -->
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

                <div class="form-group row form_space">
                  <label for="userName" class="col-xs-2 col-form-label">User Name:</label>
                  <div class="col-xs-10">
                    <input class="form-control" type="text" name="userName" id="userName" required>
                  </div>
                </div><!-- /.form-group row form_space --> 

                <div class="form-group row form_space">
                  <label for="password" class="col-xs-2 col-form-label">Password:</label>
                  <div class="col-xs-10">
                    <input class="form-control" type="text" name="password" id="password" required>
                  </div>
                </div><!-- /.form-group row form_space --> 

                <div class="form-group row form_space">
                  <label for="name" class="col-xs-2 col-form-label">Name:</label>
                  <div class="col-xs-10">
                    <input class="form-control" type="text" name="name" id="name">
                  </div>
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                  <label for="surname" class="col-xs-2 col-form-label">Surname:</label>
                  <div class="col-xs-10">
                    <input class="form-control" type="text" name="surname" id="surname">
                  </div>
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                  <label for="emailInput" class="col-xs-2 col-form-label">Email:</label>
                  <div class="col-xs-10">
                    <input class="form-control" type="email" name="emailInput" id="emailInput">
                  </div>
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space">
                  <label for="role_id" class="col-xs-2 col-form-label">Role ID:</label>
                  <div class="col-xs-10">
                    <input class="form-control" type="number" min="1" max="2" value="1" name="role_id" id="role_id" required>
                  </div>
                </div><!-- /.form-group row form_space -->

                <div class="form-group row form_space" id="searchArea">
                  <label for="gymName" class="col-xs-2 col-form-label">Gym Name:</label>
                  <div class="col-xs-10">
                    <input class="form-control" type="search" name="gymName" id="gymName" required>
                  </div>
                </div><!-- /.form-group row form_space -->

          </div><!-- /.col-md-12 --> 
        </div><!-- /.panel-body -->

        <div class="panel-footer" id="add_user_panel">
            <div class="form-group" style="padding-right: 60px;">
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
              <th colspan="1" id="searchgym"">gym Name</th>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="deleteuserLabel"></h4>
      </div><!-- /.modal-header -->
      <div class="modal-body">
       <form action="mngUsers.php" method="post">    
          <input type="hidden" class="form-control" name="login_user_id" id="login_user_id">
      </div><!-- /.modal-body -->
      <div class="modal-footer">
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="edituserLabel"></h4>
      </div>
      <div class="modal-body">
        <form action="mngUsers.php" data-toggle="validator" method="post">
          <input type="hidden" class="form-control" name="edit_login_user_id" id="edit_login_user_id">
          
          <div class="form-group row form_space">
            <label for="edituserName" class="col-xs-2 col-form-label">user Name:</label>
            <div class="col-xs-10">
              <input class="form-control" type="text" name="edituserName" id="edituserName" required>
            </div>
          </div><!-- /.form-group row form_space --> 

          <div class="form-group row form_space">
            <label for="editname" class="col-xs-2 col-form-label">name:</label>
            <div class="col-xs-10">
              <input class="form-control" type="text" name="editname" id="editname">
            </div>
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space">
            <label for="editsurname" class="col-xs-2 col-form-label">Surname:</label>
            <div class="col-xs-10">
              <input class="form-control" type="text" name="editsurname" id="editsurname">
            </div>
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space">
            <label for="editEmailInput" class="col-xs-2 col-form-label">Email:</label>
            <div class="col-xs-10">
              <input class="form-control" type="email" name="editEmailInput" id="editEmailInput">
            </div>
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space">
            <label for="editroleid" class="col-xs-2 col-form-label">Role ID:</label>
            <div class="col-xs-10">
              <input class="form-control" type="tel" name="editroleid" id="editroleid">
            </div>
          </div><!-- /.form-group row form_space -->

          <div class="form-group row form_space" id="searchArea" >
            <label for="editgymName" class="col-xs-2 col-form-label">gym Name:</label>
            <div class="col-xs-10" >
              <input class="form-control"  type="search"  name="editgymName" id="editgymName" required>
            </div>
          </div><!-- /.form-group row form_space -->

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
 
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="../js/jquery-2.2.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../js/bootstrap.min.js"></script>
<script src="../js/validator.min.js"></script>
<script src="../js/jquery.easy-autocomplete.min.js"></script> 
<script type="text/javascript" src="../media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="../js/buttons.flash.min.js"></script>
<script type="text/javascript" src="../js/jszip.min.js"></script>
<script type="text/javascript" src="../js/pdfmake.min.js"></script>
<script type="text/javascript" src="../js/vfs_fonts.js"></script>
<script type="text/javascript" src="../js/buttons.html5.min.js"></script> 
<script type="text/javascript" src="../js/buttons.print.min.js"></script>
<script type="text/javascript">
var options = {
  url: "../../datatables_init/json_gym.php",
  getValue: "gym_name",
  list: {
    match: {
      enabled: true
    }
  }
};
$("#gymName").easyAutocomplete(options);
$("#editgymName").easyAutocomplete(options);

// Setup - add a text input to each footer cell
$('#searchgym').each(function(){

  var title = $('#json_gym thead th').eq($(this).index()+4).text();
          
  $(this).html('<input type="text" placeholder="Search ' + title + '"/>');
});

$.ajax({
  url:'../../datatables_init/json_user.php',
  method: 'post',
  dataType: 'json',
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
    $.ajax({
      url:'../../modal_ajax/modal_user_json.php',
      method: 'post',
      dataType: 'json',
      data: {id: recipient},
      success: function(data) {
        var modal = $('#deleteuser');
        modal.find('.modal-title').text('Delete user: ' + data.username);
        modal.find('.modal-body').append('Are you sure that you want to delete user: ' + data.username + '?');
        modal.find('.modal-body input[type="hidden"]').val(recipient);
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
      success: function(data) {
        var modal = $('#edituser');
        modal.find('.modal-title').text('Edit gym: ' + data.username);
        modal.find('.modal-body input[type="hidden"]').val(recipient);
        modal.find('.modal-body input[name="edituserName"]').val(data.username);
        modal.find('.modal-body input[name="editname"]').val(data.name);
        modal.find('.modal-body input[name="editsurname"]').val(data.surname);
        modal.find('.modal-body input[name="editEmailInput"]').val(data.email);
        modal.find('.modal-body input[name="editroleid"]').val(data.role_id);
        modal.find('.modal-body input[name="editgymName"]').val(data.gym_name);
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
