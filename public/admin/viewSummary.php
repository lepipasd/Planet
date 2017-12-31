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
      <li class="breadcrumb-item active">View Summary</li>
    </ol>
    <!-- display info message -->
    <div class="alert alert-info message_manage">
    <span class="glyphicon glyphicon-info-sign"></span> 
    <?php echo output_message("Select a gym, pick a date or select a range and view overall report."); ?>
    </div><!-- /.alert alert-info message -->

    <!-- display main content in tabs -->
    <div class="col-md-12 content" style="margin-bottom: 60px;">

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
                <th>Datetime</th>
                <th>Gym Name</th>
                <th>Income</th>
                <th>Outcome</th>
                </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="1"></th>
                <th colspan="1" id="searchgym"">Gym Name</th>
                <th colspan="2"></th>
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
  $('#daterange').on('apply.daterangepicker', function(ev, picker) {
    console.log(picker.startDate.format('YYYY-MM-DD'));
    console.log(picker.endDate.format('YYYY-MM-DD'));
    $.ajax({
      url:'../../charts_init/json_view_summary.php',
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
        console.log(data);
        var remained = data.outcome;
        $.each(data.income, function(key_income, value_income) {
          $.each(data.outcome, function(key_outcome, value_outcome) {
            if ((value_income.datetime == value_outcome.datetime) &&
              (value_income.gym_name == value_outcome.gym_name)) {
                value_income["outcome_price"] = value_outcome.outcome_price;
                value_outcome["matched"] = true;
            } 
          });
        });
        $.each(data.outcome, function(key_outcome, value_outcome) {
          if (!value_outcome.matched) {
            data.income.push(value_outcome);
          }
        });
        var income = [];
        var outcome = [];
        $.each(data.chart, function(key, value) {
          if (value.income_paied) {
            income.push(value.income_paied);
          } else {
            income.push(0);
          }
          if (value.outcome) {
            outcome.push(value.outcome);
          } else {
            
            outcome.push(0);
          }
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
              data: income
          }, {
              name: 'Outcome',
              data: outcome
          }] // series data ends here
        }); // chart object ends here
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
          data: data.income,
          columns: [
            {'data': 'datetime'},
            {'data': 'gym_name'},
            {'data': 'income_price'},
            {'data': 'outcome_price'},
          ],
          columnDefs: [
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
            }
          ],
        }); // DataTable ends here
        table.column(1).every(function(){
          var tableColumn = this;
          $(this.footer()).find('input').on('keyup change', function(){
            var term = $(this).val();  
            tableColumn.search(term).draw();                 
          });          
        });
      } // success function ends here
    }); // ajax request ends here
  }); // daterangepicker function ends here
    
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

