<?php
require_once('../../includes/initialize.php');

if (!$session->is_logged_in() or !$session->is_session_valid()) {
    $session->logout();
    redirect_to("login.php");
}
include('../../includes/layouts/header.php');
include('../../includes/layouts/menu.php');
?>
<div class="container-fluid main_center">
  <!-- breadcrumb for pagination -->
  <ol class="breadcrumb" style="margin-top: 20px;">
    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="stats.php">Statistics</a></li>
    <li class="breadcrumb-item active">View Daily Report</li>
  </ol><!-- ol .breadcrumb -->
  <!-- display info message -->
    <?php if ($session->role_id == 1) : ?>
    <div class="alert alert-info message_manage">
    <span class="glyphicon glyphicon-info-sign"></span>
    <?php echo output_message("View daily report for " . $session->gym_name . "."); ?>
    <?php elseif ($session->role_id == 2) : ?>
    <div class="alert alert-info message_manage">
    <span class="glyphicon glyphicon-info-sign"></span> 
    <?php echo output_message("Pick a date or select a range and view overall report."); ?>
    <?php elseif ($session->role_id == 4) : ?>
    <div class="alert alert-info message_manage">
    <span class="glyphicon glyphicon-info-sign"></span> 
    <?php echo output_message("Select a gym, pick a date or select a range and view overall report."); ?>
    <?php endif; ?>
    </div><!-- /.alert alert-info message -->

  <!-- display main content in tabs -->
  <div class="col-md-12 content" style="margin-bottom: 60px;">

    <?php if ($session->role_id == 4) : ?>
      <div class="col-md-12 form_space">
        <div class="form-group has-feedback">
          <div class="input-group">
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-registration-mark"></span>
            </span>
            <select class="selectpicker form-control show-tick" 
            data-live-search="true" title="ΔΙΑΛΕΞΕ ΓΥΜΝΑΣΤΗΡΙΟ" 
            name="select_provider" id="select_gym" 
            data-error="Gym name must be a valid choice and cannot be blank."
            required>
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
        <div class="form-group has-feedback collapse">
          <div class="input-group input-daterange">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
            <input type="text" id="daterange" name="daterange" class="form-control" 
            data-error="Interval cannot be blank." required>
          </div><!-- /.input-group input-daterange -->
          <div class="help-block with-errors">Select a range or pick a date.</div>
        </div><!-- /.form-group has-feedback -->
      </div><!-- /.col-md-12 form_space collapse -->
      <div class="col-md-12" style="background-color: #ffa834; height: 3px;margin-bottom: 20px">
      </div>
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
      <div class="col-md-12" style="background-color: #ffa834; height: 3px;margin-bottom: 20px">
      </div>
    <?php endif; ?>
    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#bar_chart" role="tab" 
        id="nav-bar-chart">
          <i class="fa fa-bar-chart"></i> Bar Chart
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#table_view" role="tab" id="nav-table-view"> 
          <i class="fa fa-table"></i> Table View
        </a>
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="bar_chart" role="tabpanel">
        <div id="chart" style="width:100%; height:100%;"></div>
      </div><!-- /.tab-pane active #barchart -->
      <div class="tab-pane" id="table_view" role="tabpanel">
        <div class="table-responsive">
        <table class="table table-bordered table-hover" id="json_record" style="width: 100%;">
          <thead>
            <tr class="tablehead">
              <th>ΓΥΜΝΑΣΤΗΡΙΟ</th>
              <th>ΕΣΟΔΑ</th>
              <th>ΕΣΟΔΑ - ΜΕΤΡΗΤΟΙΣ</th>
              <th>ΕΞΟΔΑ - ΗΜΕΡΗΣΙΟ ΤΑΜΕΙΟ</th>
              <th>ΕΞΟΔΑ - ΓΕΝΙΚΟ ΤΑΜΕΙΟ</th>
              <th>ΤΑΜΕΙΟ</th>
              </tr>
          </thead>
          <tfoot>
            <tr>
              <th colspan="1" id="searchgym"">ΓΥΜΝΑΣΤΗΡΙΟ</th>
              <th colspan="5"></th>
            </tr>
          </tfoot>
        </table>
      </div><!-- /.table-responsive -->
      </div><!-- /.tab-pane active #table_view -->
    </div><!-- /.tab-content -->
  </div><!-- /.col-md-12 content -->  
  
  <div id='ajax_loader'>
      <i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i></br>
  </div>

</div><!-- /.container-fluid -->

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
      var selectedGymName = $(this).find("option:selected").text();
      $('.collapse').collapse();
      $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $.ajax({
          url:'../../charts_init/json_view_dailyreport.php',
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
          var outcome_daily = [];
          var outcome_general = [];
          var income_agreed = [];
          var income_paied = [];
          $.each(data.chart, function(key, value) {
            outcome_daily.push(value.outcome_daily);
            outcome_general.push(value.outcome_general);
            income_agreed.push(value.income_agreed);
            income_paied.push(value.income_paied);
          });
      
          var myChart = Highcharts.chart('chart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Summary Report'
            },
            xAxis: {
                categories: Object.keys(data.chart)
            },
            yAxis: {
              type: 'linear',
              tickInterval: 25,
                title: {
                    text: 'Balance'
                }
            },
            series: [{
            name: 'Income',
            data: income_paied
            }, 
            {
            name: 'Outcome Daily',
            data: outcome_daily
            },
            {
            name: 'Outcome General',
            data: outcome_general
            }] // series data ends here
          }); // chart object ends here
          var table_obj = [];
          var values_obj = Object.values(data.chart)[0];
          var object = $.extend({}, values_obj, {"gym_name": Object.keys(data.chart)[0]});
          object = $.extend({}, values_obj, {"tameio": (object.cash - object.outcome_daily).toFixed(2)});
          table_obj.push(object);
          var table = $('#json_record').DataTable({
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
            data: table_obj,
            columns: [
              {'data': 'gym_name'},
              {'data': 'income_paied'},
              {'data': 'cash'},
              {'data': 'outcome_daily'},
              {'data': 'outcome_general'},
              {'data': 'tameio'},
            ],
            columnDefs: [
              {
              targets: 0,
              orderable: true,
              searchable: true,
              className: 'dt-body-center',
              render: function(data, type, full, meta) {
                return data + '\u20AC';
              }
            },
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
              targets: 2,
              orderable: true,
              searchable: true,
              className: 'dt-body-center',
              render: function(data, type, full, meta) {
                return data + '\u20AC';
              }
            },
            {
              targets: 3,
              orderable: true,
              searchable: true,
              className: 'dt-body-center',
              render: function(data, type, full, meta) {
                return data + '\u20AC';
              }
            },
            {
              targets: 4,
              orderable: true,
              searchable: true,
              className: 'dt-body-center',
              render: function(data, type, full, meta) {
                return data + '\u20AC';
              }
            },
            {
              targets: 5,
              orderable: true,
              searchable: true,
              className: 'dt-body-center',
              render: function(data, type, full, meta) {
                return data + '\u20AC';
              }
            }
            ],
          }); // DataTable ends here
          table.column(0).every(function(){
            var tableColumn = this;
            $(this.footer()).find('input').on('keyup change', function(){
              var term = $(this).val();  
              tableColumn.search(term).draw();                 
            });          
          });
        } // success function ends here
      }); // ajax request ends here
      }); // daterangepicker function ends here
    }); // changed.bs.select function ends here
  } else if (el.length + el_daterange.length == 1) {
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
      $.ajax({
        url:'../../charts_init/json_view_dailyreport.php',
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
          var outcome_daily = [];
          var outcome_general = [];
          var income_agreed = [];
          var income_paied = [];
          $.each(data.chart, function(key, value) {
            outcome_daily.push(value.outcome_daily);
            outcome_general.push(value.outcome_general);
            income_agreed.push(value.income_agreed);
            income_paied.push(value.income_paied);
          });
    
          var myChart = Highcharts.chart('chart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Summary Report'
            },
            xAxis: {
                categories: Object.keys(data.chart)
            },
            yAxis: {
              type: 'linear',
              tickInterval: 25,
                title: {
                    text: 'Balance'
                }
            },
            series: [{
                name: 'Income',
                data: income_paied
            }, 
            {
                name: 'Outcome Daily',
                data: outcome_daily
            },
            {
                name: 'Outcome General',
                data: outcome_general
            }] // series data ends here
          }); // chart object ends here
          var table_obj = [];
          var values_obj = Object.values(data.chart)[0];
          var object = $.extend({}, values_obj, {"gym_name": Object.keys(data.chart)[0]});
          object = $.extend({}, values_obj, {"tameio": (object.cash - object.outcome_daily).toFixed(2)});
          table_obj.push(object);
          var table = $('#json_record').DataTable({
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
            data: table_obj,
            columns: [
              {'data': 'gym_name'},
              {'data': 'income_paied'},
              {'data': 'cash'},
              {'data': 'outcome_daily'},
              {'data': 'outcome_general'},
              {'data': 'tameio'},
            ],
            columnDefs: [
              {
                targets: 0,
                orderable: true,
                searchable: true,
                className: 'dt-body-center',
                render: function(data, type, full, meta) {
                  if (!$.trim(data)) {
                    return <?php echo j($session->gym_name) ?>;
                  } else {
                    return data;
                  }
                }
              },
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
                targets: 2,
                orderable: true,
                searchable: true,
                className: 'dt-body-center',
                render: function(data, type, full, meta) {
                  return data + '\u20AC';
                }
              },
              {
                targets: 3,
                orderable: true,
                searchable: true,
                className: 'dt-body-center',
                render: function(data, type, full, meta) {
                  return data + '\u20AC';
                }
              },
              {
                targets: 4,
                orderable: true,
                searchable: true,
                className: 'dt-body-center',
                render: function(data, type, full, meta) {
                  return data + '\u20AC';
                }
              },
              {
                targets: 5,
                orderable: true,
                searchable: true,
                className: 'dt-body-center',
                render: function(data, type, full, meta) {
                  return data + '\u20AC';
                }
              }
            ],
          }); // DataTable ends here
          table.column(0).every(function(){
            var tableColumn = this;
            $(this.footer()).find('input').on('keyup change', function(){
              var term = $(this).val();  
              tableColumn.search(term).draw();                 
            });          
          });
        } // success function ends here
      }); // ajax request ends here
    }); // daterangepicker function ends here
  } else {
    $.ajax({
      url:'../../charts_init/json_view_dailyreport.php',
      method: 'post',
      dataType: 'json',
      beforeSend: function(){
          $("#ajax_loader").show();
      },
      complete: function(){
          $("#ajax_loader").hide();
      },
      success: function(data) {
        var outcome_daily = [];
        var outcome_general = [];
        var income_agreed = [];
        var income_paied = [];
        $.each(data.chart, function(key, value) {
          outcome_daily.push(value.outcome_daily);
          outcome_general.push(value.outcome_general);
          income_agreed.push(value.income_agreed);
          income_paied.push(value.income_paied);
        });
  
        var myChart = Highcharts.chart('chart', {
          chart: {
              type: 'bar'
          },
          title: {
              text: 'Summary Report'
          },
          xAxis: {
              categories: Object.keys(data.chart)
          },
          yAxis: {
            type: 'linear',
            tickInterval: 25,
              title: {
                  text: 'Balance'
              }
          },
          series: [{
              name: 'Income',
              data: income_paied
          }, 
          {
              name: 'Outcome Daily',
              data: outcome_daily
          },
          {
              name: 'Outcome General',
              data: outcome_general
          }] // series data ends here
        }); // chart object ends here
        var table_obj = [];
        var values_obj = Object.values(data.chart)[0];
        var object = $.extend({}, values_obj, {"gym_name": Object.keys(data.chart)[0]});
        console.log(object);
        object = $.extend({}, values_obj, {"tameio": (object.cash - object.outcome_daily).toFixed(2)});
        table_obj.push(object);
        var table = $('#json_record').DataTable({
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
          data: table_obj,
          columns: [
            {'data': 'gym_name'},
            {'data': 'income_paied'},
            {'data': 'cash'},
            {'data': 'outcome_daily'},
            {'data': 'outcome_general'},
            {'data': 'tameio'},
          ],
          columnDefs: [
            {
              targets: 0,
              orderable: true,
              searchable: true,
              className: 'dt-body-center',
              render: function(data, type, full, meta) {
                if (!$.trim(data)) {
                  return <?php echo j($session->gym_name) ?>;
                } else {
                  return data;
                }
              }
            },
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
              targets: 2,
              orderable: true,
              searchable: true,
              className: 'dt-body-center',
              render: function(data, type, full, meta) {
                return data + '\u20AC';
              }
            },
            {
              targets: 3,
              orderable: true,
              searchable: true,
              className: 'dt-body-center',
              render: function(data, type, full, meta) {
                return data + '\u20AC';
              }
            },
            {
              targets: 4,
              orderable: true,
              searchable: true,
              className: 'dt-body-center',
              render: function(data, type, full, meta) {
                return data + '\u20AC';
              }
            },
            {
              targets: 5,
              orderable: true,
              searchable: true,
              className: 'dt-body-center',
              render: function(data, type, full, meta) {
                return data + '\u20AC';
              }
            }
          ],
        }); // DataTable ends here
        table.column(0).every(function(){
          var tableColumn = this;
          $(this.footer()).find('input').on('keyup change', function(){
            var term = $(this).val();  
            tableColumn.search(term).draw();                 
          });          
        });
      } // success function ends here
    }); // ajax request ends here
  }
  // Setup - add a text input to each footer cell
$('#searchgym').each(function(){

  var title = $('#json_record thead th').eq($(this).index()).text();
          
  $(this).html('<input type="text" placeholder="Search ' + title + '"/>');
});
}); // function ends here
</script>
<?php
include('../../includes/layouts/footer.php');
if (isset($db)) {
    $db->close_connection();
}
?>

