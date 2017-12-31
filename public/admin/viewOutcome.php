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
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="stats.php">Statistics</a></li>
    <li class="breadcrumb-item active">View Outcome</li>
  </ol><!-- ol .breadcrumb -->
    <!-- display info message -->
    <?php if ($session->role_id == 1) : ?>
    <div class="alert alert-info message_manage">
    <span class="glyphicon glyphicon-info-sign"></span>
    <?php echo output_message("View daily outcome for " . $session->gym_name . "."); ?>
    <?php else : ?>
    <div class="alert alert-info message_manage">
    <span class="glyphicon glyphicon-info-sign"></span> 
    <?php echo output_message("Select a gym and view outcome."); ?>
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
            name="select_gym" id="select_gym" 
            data-error="Gym name must be a valid choice and cannot be blank." required>
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
        </div><!-- /.form-group has-feedback collapse -->
      </div><!-- /.col-md-12 form_space -->
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
          <i class="fa fa-bar-chart"></i> Bar Chart - Daily
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#pie_chart" role="tab" 
        id="nav-pie-chart">
          <i class="fa fa-pie-chart"></i> Pie Chart - Daily
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#bar_chart_general" role="tab"
        id="nav-bar-chart-general">
          <i class="fa fa-bar-chart"></i> Bar Chart - General
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#pie_chart_general" role="tab" 
        id="nav-pie-chart-general">
          <i class="fa fa-pie-chart"></i> Pie Chart - General
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#table_view" role="tab"
        id="nav-table-view"> 
          <i class="fa fa-table"></i> Table View
        </a>
      </li>
    </ul>
        
    <!-- Tab panes -->
    <div class="tab-content">
      <div class="tab-pane active" id="bar_chart" role="tabpanel">
        <div id="chart" style="width:100%; height:100%;"></div>
      </div><!-- /.tab-pane active #bar_chart -->
      <div class="tab-pane" id="pie_chart" role="tabpanel">
        <div id="pie-chart" style="width:100%; height:100%;"></div>
      </div><!-- /.tab-pane active #pie_chart -->
      <div class="tab-pane" id="bar_chart_general" role="tabpanel">
        <div id="chart_general" style="width:100%; height:100%;"></div>
      </div><!-- /.tab-pane active #bar_chart_general -->
      <div class="tab-pane" id="pie_chart_general" role="tabpanel">
        <div id="pie-chart-general" style="width:100%; height:100%;"></div>
      </div><!-- /.tab-pane #pie_chart_general -->
      <div class="tab-pane" id="table_view" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="json_record"  style="width: 100%;">
            <thead>
              <tr class="tablehead">
                <th>Reason Outcome Name</th>
                <th>Outcome Name</th>
                <th>No of Items</th>
                <th>Paied</th> 
                </tr>
            </thead>
            <tfoot>
              <tr>
                <th colspan="1"></th>
                <th colspan="1" id="searchgym"">Outcome Name</th>
                <th colspan="2"></th>
              </tr>
            </tfoot>
          </table>
        </div><!-- /.table-responsive -->
      </div><!-- /.tab-pane #table_view -->
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
      $('.collapse').collapse();
      $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $.ajax({
          url:'../../charts_init/json_view_outcome.php',
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
            var outcome_daily_keys = [];
            var outcome_daily_pie = [];
            var outcome_general = [];
            var outcome_general_keys = [];
            var outcome_general_pie = [];
            $.each(data.chart.daily, function(key, value) {
              outcome_daily.push(value.outcome);
              outcome_daily_keys.push(key);
              outcome_daily_pie.push([key, value.outcome]);
            });
            $.each(data.chart.general, function(key, value) {
              outcome_general.push(value.outcome);
              outcome_general_keys.push(key);
              outcome_general_pie.push([key, value.outcome])
            });
            var myChart = Highcharts.chart('chart', {
              chart: {
                  type: 'bar'
              },
              title: {
                  text: 'Outcome Report - Daily'
              },
              xAxis: {
                  categories: outcome_daily_keys //Object.keys(data.chart)
              },
              yAxis: {
                type: 'linear',
                tickInterval: 25,
                  title: {
                      text: 'Balance'
                  }
              },
              series: [{
                  name: 'Outcome',
                  data: outcome_daily // outcome
              }] // series data ends here
            }); // chart object ends here
            var myChart = Highcharts.chart('pie-chart', {
              chart: {
                  plotBackgroundColor: null,
                  plotBorderWidth: null,
                  plotShadow: false,
                  type: 'pie'
              },
              title: {
                text: 'Summary Report'
              },
              tooltip: {
                  pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
              },
              plotOptions: {
                  pie: {
                      allowPointSelect: true,
                      cursor: 'pointer',
                      showInLegend: true,
                      dataLabels: {
                          enabled: true,
                          format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                          style: {
                              color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                          }
                      }
                  }
              },
              series: [{
                name: 'ΠΟΣΟΣΤΟ',
                colorByPoint: true,
                data: outcome_daily_pie
              }] // series data ends here
            }); // bar-chart object ends here
            // General
            var myChart = Highcharts.chart('chart_general', {
              chart: {
                  type: 'bar'
              },
              title: {
                  text: 'Outcome Report - General'
              },
              xAxis: {
                  categories: outcome_general_keys //Object.keys(data.chart)
              },
              yAxis: {
                type: 'linear',
                tickInterval: 25,
                  title: {
                      text: 'Balance'
                  }
              },
              series: [{
                  name: 'Outcome',
                  data: outcome_general // outcome
              }] // series data ends here
            }); // chart object ends here
            var myChart = Highcharts.chart('pie-chart-general', {
              chart: {
                  plotBackgroundColor: null,
                  plotBorderWidth: null,
                  plotShadow: false,
                  type: 'pie'
              },
              title: {
                text: 'Summary Report'
              },
              tooltip: {
                  pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
              },
              plotOptions: {
                  pie: {
                      allowPointSelect: true,
                      cursor: 'pointer',
                      showInLegend: true,
                      dataLabels: {
                          enabled: true,
                          format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                          style: {
                              color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                          }
                      }
                  }
              },
              series: [{
                name: 'ΠΟΣΟΣΤΟ',
                colorByPoint: true,
                data: outcome_general_pie
              }] // series data ends here
            }); // bar-chart object ends here
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
              data: data.table,
              columns: [
                {'data': 'reason_outcome_name'},
                {'data': 'outcome_name'},
                {'data': 'number_of_outcome'},
                {'data': 'price_paied'},
              ],
              columnDefs: [
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
    }); // changed.bs.select function ends here
  } else if (el.length + el_daterange.length == 1) {
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
      $.ajax({
        url:'../../charts_init/json_view_outcome.php',
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
          var outcome_daily_keys = [];
          var outcome_daily_pie = [];
          var outcome_general = [];
          var outcome_general_keys = [];
          var outcome_general_pie = [];
          $.each(data.chart.daily, function(key, value) {
            outcome_daily.push(value.outcome);
            outcome_daily_keys.push(key);
            outcome_daily_pie.push([key, value.outcome]);
          });
          $.each(data.chart.general, function(key, value) {
            outcome_general.push(value.outcome);
            outcome_general_keys.push(key);
            outcome_general_pie.push([key, value.outcome])
          });
          // Daily
          var myChart = Highcharts.chart('chart', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Outcome Report - Daily'
            },
            xAxis: {
                categories: outcome_daily_keys
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
              data: outcome_daily
            }] // series data ends here
          }); // chart object ends here
          var myChart = Highcharts.chart('pie-chart', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
              text: 'Summary Report'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    showInLegend: true,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
              name: 'ΠΟΣΟΣΤΟ',
              colorByPoint: true,
              data: outcome_daily_pie
            }] // series data ends here
          }); // bar-chart object ends here
          // General
          var myChart = Highcharts.chart('chart_general', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Outcome Report - General'
            },
            xAxis: {
                categories: outcome_general_keys //Object.keys(data.chart)
            },
            yAxis: {
              type: 'linear',
              tickInterval: 25,
                title: {
                    text: 'Balance'
                }
            },
            series: [{
                name: 'Outcome',
                data: outcome_general // outcome
            }] // series data ends here
          }); // chart object ends here
          var myChart = Highcharts.chart('pie-chart-general', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
              text: 'Summary Report'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    showInLegend: true,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
              name: 'ΠΟΣΟΣΤΟ',
              colorByPoint: true,
              data: outcome_general_pie
            }] // series data ends here
          }); // bar-chart object ends here
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
            data: data.table,
            columns: [
              {'data': 'reason_outcome_name'},
              {'data': 'outcome_name'},
              {'data': 'number_of_outcome'},
              {'data': 'price_paied'},
            ],
            columnDefs: [
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
  } else {
    $.ajax({
      url:'../../charts_init/json_view_outcome.php',
      method: 'post',
      dataType: 'json',
      // data: {
      //   start_date: picker.startDate.format('YYYY-MM-DD'),
      //   end_date: picker.endDate.format('YYYY-MM-DD')
      // },
      beforeSend: function(){
        $("#ajax_loader").show();
      },
      complete: function(){
        $("#ajax_loader").hide();
      },
      success: function(data) {
        var outcome_daily = [];
        var outcome_daily_keys = [];
        var outcome_daily_pie = [];
        var outcome_general = [];
        var outcome_general_keys = [];
        var outcome_general_pie = [];
        $.each(data.chart.daily, function(key, value) {
          outcome_daily.push(value.outcome);
          outcome_daily_keys.push(key);
          outcome_daily_pie.push([key, value.outcome]);
        });
        $.each(data.chart.general, function(key, value) {
          outcome_general.push(value.outcome);
          outcome_general_keys.push(key);
          outcome_general_pie.push([key, value.outcome])
        });
        var myChart = Highcharts.chart('chart', {
          chart: {
              type: 'bar'
          },
          title: {
              text: 'Summary Report'
          },
          xAxis: {
              categories: outcome_daily_keys
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
            data: outcome_daily
          }] // series data ends here
        }); // chart object ends here
        var myChart = Highcharts.chart('pie-chart', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
              text: 'Summary Report'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    showInLegend: true,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
              name: 'ΠΟΣΟΣΤΟ',
              colorByPoint: true,
              data: outcome_daily_pie
            }] // series data ends here
          }); // bar-chart object ends here
        // General
        var myChart = Highcharts.chart('chart_general', {
          chart: {
              type: 'bar'
          },
          title: {
              text: 'Outcome Report - General'
          },
          xAxis: {
              categories: outcome_general_keys //Object.keys(data.chart)
          },
          yAxis: {
            type: 'linear',
            tickInterval: 25,
              title: {
                  text: 'Balance'
              }
          },
          series: [{
              name: 'Outcome',
              data: outcome_general // outcome
          }] // series data ends here
        }); // chart object ends here
        var myChart = Highcharts.chart('pie-chart-general', {
          chart: {
              plotBackgroundColor: null,
              plotBorderWidth: null,
              plotShadow: false,
              type: 'pie'
          },
          title: {
            text: 'Summary Report'
          },
          tooltip: {
              pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
          },
          plotOptions: {
              pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  showInLegend: true,
                  dataLabels: {
                      enabled: true,
                      format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                      style: {
                          color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                      }
                  }
              }
          },
          series: [{
            name: 'ΠΟΣΟΣΤΟ',
            colorByPoint: true,
            data: outcome_general_pie
          }] // series data ends here
        }); // bar-chart object ends here
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
          data: data.table,
          columns: [
            {'data': 'reason_outcome_name'},
            {'data': 'outcome_name'},
            {'data': 'number_of_outcome'},
            {'data': 'price_paied'},
          ],
          columnDefs: [
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
  } // if ends here
}); // function ends here
// autocomplete deprecated
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

  var title = $('#json_record thead th').eq($(this).index()).text();
          
  $(this).html('<input type="text" placeholder="Search ' + title + '"/>');
});
</script>
<?php
include('../../includes/layouts/footer.php');
if (isset($db)) {
    $db->close_connection();
}
?>

