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
            var list_income = $('#list_income')[0];
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $('#list_income').html("");
                $.ajax({
                    url:'../../charts_init/json_view_income_overview.php',
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
                        var list_income = document.getElementById("list_income");
                        var element = document.createElement("li");
                        element.appendChild(document.createTextNode('ΕΣΟΔΑ: ' + 
                        data.table.price_paied + '\u20AC'));
                        list_income.appendChild(element);
                        var element = document.createElement("li");
                        element.appendChild(document.createTextNode('ΕΣΟΔΑ ΑΠΟ ΜΕΤΡΗΤΑ: ' + 
                        data.table.price_paied_cash + '\u20AC'));
                        list_income.appendChild(element);
                        var element = document.createElement("li");
                        element.appendChild(document.createTextNode('ΑΡΙΘΜΟΣ ΠΡΟΙΟΝΤΩΝ: ' + 
                        data.table.number_of_income));
                        list_income.appendChild(element);
                        var element_li = document.createElement("li");
                        var element = document.createElement("h4");
                        element.append('TOP 5 ΠΡΟΙΟΝΤΑ');
                        element_li.append(element);
                        list_income.append(element_li);
                        $('#list_income li h4').addClass("list-group-item-heading");
                        $('#list_income li').addClass("list-group-item");
                        $.each(data.chart, function(key, value) {
                            var element = document.createElement("li");
                            element.appendChild(document.createTextNode(key + ': ' + value.income + '\u20AC'));
                            list_income.append(element);
                            $('#list_income li').addClass("list-group-item");
                        }); // each loop ends here
                        // init table
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
                                    .column( 8 )
                                    .data()
                                    .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                    }, 0 );
                     
                                // Total over this page
                                pageTotal = api
                                    .column( 8, { page: 'current'} )
                                    .data()
                                    .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                    }, 0 );
                     
                                // Update footer
                                $( api.column( 8 ).footer() ).html(
                                    '$'+pageTotal.toFixed(2) +' ( $'+ total.toFixed(2) +' total)'
                                );
                            },
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
                            data: data.init_table,
                            columns: [
                                {'data': 'time'},
                                {'data': 'alp'},
                                {'data': 'apy'},
                                {'data': 'gym_name'},
                                {'data': 'username'},
                                {'data': 'name'},
                                {'data': 'shift_name'},
                                {'data': 'income_name'},
                                {'data': 'price_agreed'},
                                {'data': 'price_paied'},
                                {'data': 'payment_method_name'},
                                {'data': 'registration_name'},
                                {'data': 'taxes'},
                                {'data': 'attraction_name'},
                                {'data': 'comments'},
                                {'data': 'income_report_id'},
                                {'data': 'income_report_id'},
                            ],
                            columnDefs: [
                                {
                                    targets: 8,
                                    orderable: true,
                                    searchable: true,
                                    className: 'dt-body-center',
                                    render: function(data, type, full, meta) {
                                        return data + '\u20AC';
                                    }
                                },
                                {
                                    targets: 9,
                                    orderable: true,
                                    searchable: true,
                                    className: 'dt-body-center',
                                    render: function(data, type, full, meta) {
                                        return data + '\u20AC';
                                    }
                                },
                                {
                                    targets: 15,
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
                                    targets: 16,
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
                        table.column(3).every(function(){
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
            $('#list_income').html("");
            $.ajax({
                url:'../../charts_init/json_view_income_overview.php',
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
                    var list_income = document.getElementById("list_income");
                    var element = document.createElement("li");
                    element.appendChild(document.createTextNode('ΕΣΟΔΑ: ' + 
                    data.table.price_paied + '\u20AC'));
                    list_income.appendChild(element);
                    var element = document.createElement("li");
                    element.appendChild(document.createTextNode('ΕΣΟΔΑ ΑΠΟ ΜΕΤΡΗΤΑ: ' + 
                    data.table.price_paied_cash + '\u20AC'));
                    list_income.appendChild(element);
                    var element = document.createElement("li");
                    element.appendChild(document.createTextNode('ΑΡΙΘΜΟΣ ΠΡΟΙΟΝΤΩΝ: ' + 
                    data.table.number_of_income));
                    list_income.appendChild(element);
                    var element_li = document.createElement("li");
                    var element = document.createElement("h4");
                    element.append('TOP 5 ΠΡΟΙΟΝΤΑ');
                    element_li.append(element);
                    list_income.append(element_li);
                    $('#list_income li h4').addClass("list-group-item-heading");
                    $('#list_income li').addClass("list-group-item");
                    $.each(data.chart, function(key, value) {
                        var element = document.createElement("li");
                        element.appendChild(document.createTextNode(key + ': ' + value.income + '\u20AC'));
                        list_income.appendChild(element);
                        $('#list_income li').addClass("list-group-item");
                    }); // each loop ends here

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
                                    .column( 8 )
                                    .data()
                                    .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                    }, 0 );
                     
                                // Total over this page
                                pageTotal = api
                                    .column( 8, { page: 'current'} )
                                    .data()
                                    .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                    }, 0 );
                     
                                // Update footer
                                $( api.column( 8 ).footer() ).html(
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
                            data: data.init_table,
                            columns: [
                                {'data': 'time'},
                                {'data': 'alp'},
                                {'data': 'apy'},
                                {'data': 'gym_name'},
                                {'data': 'username'},
                                {'data': 'name'},
                                {'data': 'shift_name'},
                                {'data': 'income_name'},
                                {'data': 'price_agreed'},
                                {'data': 'price_paied'},
                                {'data': 'payment_method_name'},
                                {'data': 'registration_name'},
                                {'data': 'taxes'},
                                {'data': 'attraction_name'},
                                {'data': 'comments'},
                                {'data': 'income_report_id'},
                                {'data': 'income_report_id'},
                            ],
                            columnDefs: [
                                {
                                    targets: 8,
                                    orderable: true,
                                    searchable: true,
                                    className: 'dt-body-center',
                                    render: function(data, type, full, meta) {
                                        return data + '\u20AC';
                                    }
                                },
                                {
                                    targets: 9,
                                    orderable: true,
                                    searchable: true,
                                    className: 'dt-body-center',
                                    render: function(data, type, full, meta) {
                                        return data + '\u20AC';
                                    }
                                },
                                {
                                    targets: 15,
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
                                    targets: 16,
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
                        table.column(3).every(function(){
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
            url:'../../charts_init/json_view_income_overview.php',
            method: 'post',
            dataType: 'json',
            success: function(data) {                
                var list_income = document.getElementById("list_income");
                var element = document.createElement("li");
                element.appendChild(document.createTextNode('ΕΣΟΔΑ: ' + 
                    data.table.price_paied + '\u20AC'));
                list_income.appendChild(element);
                var element = document.createElement("li");
                    element.appendChild(document.createTextNode('ΕΣΟΔΑ ΑΠΟ ΜΕΤΡΗΤΑ: ' + 
                        data.table.price_paied_cash + '\u20AC'));
                    list_income.appendChild(element);
                var element = document.createElement("li");
                element.appendChild(document.createTextNode('ΑΡΙΘΜΟΣ ΠΡΟΙΟΝΤΩΝ: ' + 
                        data.table.number_of_income));
                list_income.appendChild(element);
                var element_li = document.createElement("li");
                var element = document.createElement("h4");
                element.append('TOP 3 ΠΡΟΙΟΝΤΑ');
                element_li.append(element);
                list_income.append(element_li);
                $('#list_income li h4').addClass("list-group-item-heading");
                $('#list_income li').addClass("list-group-item");
                $.each(data.chart, function(key, value) {
                    var element = document.createElement("li");
                    element.appendChild(document.createTextNode(key + ': ' + value.income + '\u20AC'));
                    list_income.appendChild(element);
                    $('#list_income li').addClass("list-group-item");
                });

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
                                    .column( 8 )
                                    .data()
                                    .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                    }, 0 );
                     
                                // Total over this page
                                pageTotal = api
                                    .column( 8, { page: 'current'} )
                                    .data()
                                    .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                    }, 0 );
                     
                                // Update footer
                                $( api.column( 8 ).footer() ).html(
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
                            data: data.init_table,
                            columns: [
                                {'data': 'time'},
                                {'data': 'alp'},
                                {'data': 'apy'},
                                {'data': 'gym_name'},
                                {'data': 'username'},
                                {'data': 'name'},
                                {'data': 'shift_name'},
                                {'data': 'income_name'},
                                {'data': 'price_agreed'},
                                {'data': 'price_paied'},
                                {'data': 'payment_method_name'},
                                {'data': 'registration_name'},
                                {'data': 'taxes'},
                                {'data': 'attraction_name'},
                                {'data': 'comments'},
                                {'data': 'income_report_id'},
                                {'data': 'income_report_id'},
                            ],
                            columnDefs: [
                                {
                                    targets: 8,
                                    orderable: true,
                                    searchable: true,
                                    className: 'dt-body-center',
                                    render: function(data, type, full, meta) {
                                        return data + '\u20AC';
                                    }
                                },
                                {
                                    targets: 9,
                                    orderable: true,
                                    searchable: true,
                                    className: 'dt-body-center',
                                    render: function(data, type, full, meta) {
                                        return data + '\u20AC';
                                    }
                                },
                                {
                                    targets: 15,
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
                                    targets: 16,
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
                        table.column(3).every(function(){
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
        var title = $('#json_record thead th').eq($(this).index()+2).text();       
        $(this).html('<input type="text" placeholder="Search ' + title + '"/>');
    });

    $('.js-data-example-ajax').select2({
      ajax: {
        url: '../../ajax_requests/json_customer_enumeration_search.php',
        dataType: 'json',
        cache: true
        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
      },
      placeholder: "ΔΙΑΛΕΞΕ ΠΕΛΑΤΗ",
      minimumInputLength: 3,
      allowClear: true
    });
    $('.js-data-example-ajax').val(null).trigger("change");;
    $('.js-data-example-ajax#select_customer').on('select2:select', function (e, clickedIndex, newValue, oldValue) {
        var selectedCustomer = $(this).find("option:selected").val();
        $('.collapse').collapse();
    });
    $('#assignCustomer').on('show.bs.modal', function (event) {
        var customer_id_for_modal = $('#select_customer').find("option:selected").val();
        $.ajax({
            url:'../../modal_ajax/modal_customer_income_json.php',
            method: 'post',
            dataType: 'json',
            data: {id: customer_id_for_modal},
            beforeSend: function(){
                $.spin('modal');
            },
            complete: function(){
                $.spin('false');
            },
            success: function(data) {
                var modal = $('#assignCustomer');
                modal.find('.modal-title').text('Assign Product to Customer: ' + data.customer.name);
                modal.find('.modal-body input#assign_customer_id[type="hidden"]').val(customer_id_for_modal);
                modal.find('.modal-body input[name="assignCustomerName"]').val(data.customer.name);
                var select = document.getElementById("income_id");
                $.each(data.income, function(key, value) {
                    var option = document.createElement("option");
                    option.text = value.income_name;
                    option.value = value.income_id;
                    select.appendChild(option);
                });
                $('#income_id').addClass('selectpicker');
                $('#income_id').addClass(' show-tick');
                $('#income_id').selectpicker({
                    style: 'btn-default',
                    size: 4
                });
                var select_payment_method = document.getElementById("payment_method_id");
                $.each(data.payment_method, function(key, value) {
                    var option = document.createElement("option");
                    option.text = value.payment_method_name;
                    option.value = value.payment_method_id;
                    select_payment_method.appendChild(option);
                });
                $('#payment_method_id').addClass('selectpicker');
                $('#payment_method_id').addClass(' show-tick');
                $('#payment_method_id').selectpicker('val', 1);
                $('#payment_method_id').selectpicker({
                    style: 'btn-default',
                    size: 4
                });
                var select_attraction_income = document.getElementById("attraction_income_id");
                $.each(data.attraction_income, function(key, value) {
                    var option = document.createElement("option");
                    option.text = value.attraction_income_name;
                    option.value = value.attraction_income_id;
                    select_attraction_income.appendChild(option);
                });
                $('#attraction_income_id').addClass('selectpicker');
                $('#attraction_income_id').addClass(' show-tick');
                $('#attraction_income_id').selectpicker('val', 8);
                $('#taxes').selectpicker('val', 0.24);
                $('#attraction_income_id').selectpicker({
                    style: 'btn-default',
                    size: 4
                });
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
            url:'../../modal_ajax/modal_record_json.php',
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
                modal.find('.modal-title').text('Delete record from Customer: ' + data.record.name);
                modal.find('.modal-body').text('Are you sure that you want to delete record with ID: ' + recipient + ' ?');
                modal.find('.modal-footer input#delete_record_id[type="hidden"]').val(recipient);
            } // success function ends here
        }); // ajax request ends here
    }); // modal ends here
    // var loading_content = '<div><i class="fa fa-spinner fa-pulse fa-4x fa-fw"></i></div>';
    $('#editrecord').on('show.bs.modal', function (event) {
        // $(this).find(".modal-content").html(loadingContent);
        var button = $(event.relatedTarget) // Button that triggered the modal
        var recipient = button.data('recordid') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library 
        // or other methods instead.
        console.log(recipient);
        $.ajax({
            url:'../../modal_ajax/modal_record_json.php',
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
                console.log(data);
                var modal = $('#editrecord');
                modal.find('.modal-title').text('Edit record from Customer: ' + data.record.name);
                
                modal.find('.modal-body input#edit_customer_id[type="hidden"]').val(data.record.customer_id);
                modal.find('.modal-body input[name="edit_customer_name"]').val(data.record.name);

                modal.find('.modal-body input#edit_record_id[type="hidden"]').val(recipient);
                modal.find('.modal-body input[name="edit_alp"]').val(data.record.alp);
                modal.find('.modal-body input[name="edit_apy"]').val(data.record.apy);
                modal.find('.modal-body input[name="edit_price_agreed"]').val(data.record.price_agreed);
                modal.find('.modal-body input[name="edit_price_paied"]').val(data.record.price_paied);
                if (data.record.registration_type == 1) {
                    // document.getElementById("edit_renewal").checked = true;
                    $("#edit_renewal").prop("checked", true);
                } else if (data.record.registration_type == 2) {
                    // document.getElementById("edit_renewal").checked = true;
                    $("#edit_registration").prop("checked", true);
                } else {
                    // document.getElementById("edit_renewal").checked = true;
                    $("#edit_free_registration").prop("checked", true);
                }
                modal.find('.modal-body textarea[name="edit_comments"]').val(data.record.comments);

                var select_edit = document.getElementById("edit_income_id");
                $.each(data.income, function(key, value) {
                    var option = document.createElement("option");
                    option.text = value.income_name;
                    option.value = value.income_id;
                    select_edit.appendChild(option);
                });

                $('#edit_income_id').addClass('selectpicker');
                $('#edit_income_id').addClass(' show-tick');
                $('#edit_income_id').selectpicker('val', data.record.income_id);
                $('#edit_income_id').selectpicker({
                    style: 'btn-default',
                    size: 4
                });
                var select_payment_method_edit = document.getElementById("edit_payment_method_id");
                $.each(data.payment_method, function(key, value) {
                    var option = document.createElement("option");
                    option.text = value.payment_method_name;
                    option.value = value.payment_method_id;
                    select_payment_method_edit.appendChild(option);
            });
                $('#edit_payment_method_id').addClass('selectpicker');
                $('#edit_payment_method_id').addClass(' show-tick');
                $('#edit_payment_method_id').selectpicker('val', data.record.payment_method_id);
                $('#edit_payment_method_id').selectpicker({
                    style: 'btn-default',
                    size: 4
                });
                $('#edit_taxes').selectpicker('val', data.record.taxes);
                var select_attraction_income_edit = document.getElementById("edit_attraction_income_id");
                $.each(data.attraction_income, function(key, value) {
                    var option = document.createElement("option");
                    option.text = value.attraction_income_name;
                    option.value = value.attraction_income_id;
                    select_attraction_income_edit.appendChild(option);
                });
                $('#edit_attraction_income_id').addClass('selectpicker');
                $('#edit_attraction_income_id').addClass(' show-tick');
                $('#edit_attraction_income_id').selectpicker('val', data.record.attraction_id);
                $('#edit_attraction_income_id').selectpicker({
                    style: 'btn-default',
                    size: 4
                });
            } // success function ends here
        }); // ajax request ends here
    }); // modal ends here
}); // function ends here
