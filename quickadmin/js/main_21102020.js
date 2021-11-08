$(document).ready(function () {

    var handleCheckboxes = function (html, rowIndex, colIndex, cellNode) {
        var $cellNode = $(cellNode);
        var $check = $cellNode.find(':checked');
        return ($check.length) ? ($check.val() == 1 ? 'Yes' : 'No') : $cellNode.text();
    };

    var activeSub = $(document).find('.active-sub');
    if (activeSub.length > 0) {
        activeSub.parent().show();
        activeSub.parent().parent().find('.arrow').addClass('open');
        activeSub.parent().parent().addClass('open');
    }
    window.dtDefaultOptions = {
        retrieve: true,
        dom: 'lBfrtip<"actions">',
        columnDefs: [],
        "iDisplayLength": 100,
        "aaSorting": [],
        buttons: [
          
            {
                extend: 'excel',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: handleCheckboxes
                    }
                }
            },
            {
                extend: 'pdf',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: handleCheckboxes
                    }
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: handleCheckboxes
                    }
                }
            },
           // 'colvis'
        ]
    };

    processAjaxTables()
    
    $('.datatable').each(function () {
        if ($(this).hasClass('dt-select')) {
            window.dtDefaultOptions.select = {
                style: 'multi',
                selector: 'td:first-child'
            };

            window.dtDefaultOptions.columnDefs.push({
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            });
        }
        $(this).dataTable(window.dtDefaultOptions);
    });

    if (typeof window.route_mass_send_email != 'undefined' &&  typeof window.email_templates !='undefined') {
        //var selectHtml = email_templates
         //console.log($.parseJSON(email_templates).length); 
            var optionHtml='';
        $.each(window.email_templates, function(idx, obj) {
    console.log(obj.id);
    optionHtml +='<option value="'+obj.id+'">'+obj.email_subject+'</option>';
});
        $('.datatable, .ajaxTable,.datatable1').siblings('.actions').html('<div class="col-md-12"><div class="col-md-2"><a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">Delete selected</a></div><div class="col-md-4"><select rel='+window.route_mass_send_email+' name="selected_email" id="select_email" class="form-control selectedEmailTemplate"><option value=0>--Select Email Template--</option>'+optionHtml+'</div</div>');
       // $('.datatable, .ajaxTable').siblings('.actions').html('<div><a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">Delete selected</a></div>');
    }
    else if(typeof window.route_mass_crud_entries_destroy != 'undefined') {
        $('.datatable, .ajaxTable,.datatable1').siblings('.actions').html('<a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">Delete selected</a>');
       // $('.datatable, .ajaxTable').siblings('.actions').html('<div><a href="' + window.route_mass_crud_entries_destroy + '" class="btn btn-xs btn-danger js-delete-selected" style="margin-top:0.755em;margin-left: 20px;">Delete selected</a></div>');
    }


$(document).on('change', '.selectedEmailTemplate', function () {
        if (confirm('Are you sure')) {
            var ids = [];

            $(this).closest('.actions').siblings('.datatable, .ajaxTable,.datatable1').find('tbody tr.selected').each(function () {
                //console.log("selected", $(this).find('input:checkbox').val());
                ids.push($(this).find('input:checkbox').val());
            });
          //alert($(this).attr('rel'))
            $.ajax({
                method: 'POST',
                url: $(this).attr('rel'),
                data: {
                    _token: _token,
                    ids: ids,
                    email_template_id:$(this).val()
                }
            }).done(function () {
              //  location.reload();
            });
        }

        return false;
    });

    $(document).on('click', '.js-delete-selected', function () {
        if (confirm('Are you sure')) {
            var ids = [];

            $(this).closest('.actions').siblings('.datatable, .datatable1 ,.ajaxTable').find('tbody tr.selected').each(function () {
               ids.push($(this).find('input:checkbox:first').val());
            });

            $.ajax({
                method: 'POST',
                url: $(this).attr('href'),
                data: {
                    _token: _token,
                    ids: ids
                }
            }).done(function () {
               location.reload();
            });
        }

        return false;
    });

    $(document).on('click', '#select-all', function () {
        var selected = $(this).is(':checked');

        $(this).closest('table.datatable, table.ajaxTable,table.datatable1').find('td:first-child').each(function () {
            
            if (selected != $(this).closest('tr').hasClass('selected')) {
                $(this).click();
            }
        });
    });

    $('.mass').click(function () {
        if ($(this).is(":checked")) {
            $('.single').each(function () {
                if ($(this).is(":checked") == false) {
                    $(this).click();
                }
            });
        } else {
            $('.single').each(function () {
                if ($(this).is(":checked") == true) {
                    $(this).click();
                }
            });
        }
    });

    $('.page-sidebar').on('click', 'li > a', function (e) {

        if ($('body').hasClass('page-sidebar-closed') && $(this).parent('li').parent('.page-sidebar-menu').size() === 1) {
            return;
        }

        var hasSubMenu = $(this).next().hasClass('sub-menu');

        if ($(this).next().hasClass('sub-menu always-open')) {
            return;
        }

        var parent = $(this).parent().parent();
        var the = $(this);
        var menu = $('.page-sidebar-menu');
        var sub = $(this).next();

        var autoScroll = menu.data("auto-scroll");
        var slideSpeed = parseInt(menu.data("slide-speed"));
        var keepExpand = menu.data("keep-expanded");

        if (keepExpand !== true) {
            parent.children('li.open').children('a').children('.arrow').removeClass('open');
            parent.children('li.open').children('.sub-menu:not(.always-open)').slideUp(slideSpeed);
            parent.children('li.open').removeClass('open');
        }

        var slideOffeset = -200;

        if (sub.is(":visible")) {
            $('.arrow', $(this)).removeClass("open");
            $(this).parent().removeClass("open");
            sub.slideUp(slideSpeed, function () {
                if (autoScroll === true && $('body').hasClass('page-sidebar-closed') === false) {
                    if ($('body').hasClass('page-sidebar-fixed')) {
                        menu.slimScroll({
                            'scrollTo': (the.position()).top
                        });
                    }
                }
            });
        } else if (hasSubMenu) {
            $('.arrow', $(this)).addClass("open");
            $(this).parent().addClass("open");
            sub.slideDown(slideSpeed, function () {
                if (autoScroll === true && $('body').hasClass('page-sidebar-closed') === false) {
                    if ($('body').hasClass('page-sidebar-fixed')) {
                        menu.slimScroll({
                            'scrollTo': (the.position()).top
                        });
                    }
                }
            });
        }
        if (hasSubMenu == true || $(this).attr('href') == '#') {
            e.preventDefault();
        }
    });

   $('.select2').select2();


});

function processAjaxTables() {

  
   if(window.hasAppointment===undefined)
    {
      var rows_selected = [];
         var table = $('.datatable1').DataTable({
                  retrieve: true,
                dom: 'lBfrtip<"actions">',
               "iDisplayLength": 100,
               processing: true,
               serverSide: true,
              
                buttons: [
                    {
                        extend: 'excel',
                        
                    },
                    {
                        extend: 'pdf',
                       
                    },
                    {
                        extend: 'print',
                        
                    },
           // 'colvis'
           ],
          
                ajax: window.route_all_data,
               columns: [
                       
                        { "data":null},
                        { data: 'first_name', name: 'first_name' },
                        { data: 'last_name', name: 'last_name' },
                        { data: 'phone', name: 'phone' },
                        { data: 'email', name: 'email' },
                        { data: 'created_date', name: 'created_date' },
                        { data: 'comment', name: 'comment' },
                        { data: 'parent_name', name: 'parent_name' },
                        { data: 'moneybird_contact_id', name: 'moneybird_contact_id' },
                        { data: 'action', searchable: false, orderable: false }

                     ], 
                     'columnDefs': [{
                     'targets': 0,
                     'searchable': false,
                     'orderable': false,
                     'width': '1%',
                     'className': 'dt-body-center',
                     'render': function (data, type, full, meta){
                      //console.log(data);
                         return '<input type="checkbox" value="'+data.id+'">';
                     }
                  }],
                    select: {
                         style:'multi',
                        selector: 'td:first-child'
                    },

                    'rowCallback': function(row, data, dataIndex){
                     // Get row ID
                     var rowId = data[0];

                     // If row ID is in the list of selected row IDs
                     if($.inArray(rowId, rows_selected) !== -1){
                        $(row).find('input[type="checkbox"]').prop('checked', true);
                        $(row).addClass('selected');
                     }
                  }
       
                            });
                    table.on("click", "th.select-checkbox", function() {
                    if ($("th.select-checkbox").hasClass("selected")) {
                        table.rows().deselect();
                        $("th.select-checkbox").removeClass("selected");
                    } else {
                        table.rows().select();
                        $("th.select-checkbox").addClass("selected");
                    }
                }).on("select deselect", function() {
                    ("Some selection or deselection going on")
                    if (table.rows({
                            selected: true
                        }).count() !== table.rows().count()) {
                        $("th.select-checkbox").removeClass("selected");
                    } else {
                        $("th.select-checkbox").addClass("selected");
                    }
                });
      $('.datatable1 tbody').on('click', 'input[type="checkbox"]', function(e){
              
              var $row = $(this).closest('tr');

              // Get row data
              var data = table.row($row).data();
                
              // Get row ID
              var rowId = data[0];

              // Determine whether row ID is in the list of selected row IDs
              var index = $.inArray(rowId, rows_selected);

              // If checkbox is checked and row ID is not in list of selected row IDs
              if(this.checked && index === -1){
                 rows_selected.push(rowId);

              // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
              } else if (!this.checked && index !== -1){
                 rows_selected.splice(index, 1);
              }

              if(this.checked){
                 $row.addClass('selected');
              } else {
                 $row.removeClass('selected');
              }

              // Update state of "Select all" control
              updateDataTableSelectAllCtrl(table);

              // Prevent click event from propagating to parent
              e.stopPropagation();
           }); 
         

    }
    else
    {
        var rows_selected = [];
         var table = $('.datatable1').DataTable({
               retrieve: true,
               dom: 'lBfrtip<"actions">',
               "iDisplayLength": 100,
               processing: true,
               serverSide: true,
              "searching": false,
                buttons: [
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: handleCheckboxes
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: handleCheckboxes
                            }
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: handleCheckboxes
                            }
                        }
                    },
           
           ],
             'ajax': {
             'url':window.route_all_data,
             'data': function(data){
                
                var gender = $('#searchByGender').val();
                var name = $('#searchByName').val();
                var month = $('#searchByMonth').val();

                
                data.searchByGender = gender;
                data.searchByName = name;
                data.searchByMonth = month;
             }
          }, // ajax: window.route_all_data,
                 columns: [
                       
                        { "data":null},
                        { data: 'status', name: 'status' },
                        { data: 'start_time', name: 'start_time' },
                        { data: 'finish_time', name: 'finish_time' },
                        { data: 'price', name: 'price' },
                        { data: 'customer_name', name: 'clients.first_name' },
                        { data: 'phone', name: 'clients.phone' },
                        { data: 'location', name: 'location_name' },
                        { data: 'therapy_name', name: 'services.name' },
                        { data: 'therapist_name', name: 'employees.first_name' },
                        { data: 'room_no', name: 'rooms.room_name' },
                        { data: 'created_by', name: 'created_at' },
                        { data: 'client_email_verified', name: 'clients.email_verified' },
                        { data: 'moneybird_status', name: 'booking_status' },
                        { data: 'booking_status', name: 'booking_status' },
                        { data: 'action', searchable: false, orderable: false }

                     ],
                   
                     'columnDefs': [{
                     'targets': 0,
                     'searchable': false,
                     'orderable': false,
                     'width': '1%',
                     'className': 'dt-body-center',
                     'render': function (data, type, full, meta){
                      //console.log(data);
                         return '<input type="checkbox" name="checkbox_id[]" class="checkbox" value="'+data.id+'">';
                     }
                  }],
                    select: {
                         style:'multi',
                        selector: 'td:first-child'
                    },

                    'rowCallback': function(row, data, dataIndex){
                     // Get row ID
                     var rowId = data[0];

                     // If row ID is in the list of selected row IDs
                     if($.inArray(rowId, rows_selected) !== -1){
                        $(row).find('input[type="checkbox"]').prop('checked', true);
                        $(row).addClass('selected');
                     }
                  }
       
                            });
                    table.on("click", "th.select-checkbox", function() {
                    if ($("th.select-checkbox").hasClass("selected")) {
                        table.rows().deselect();
                        $("th.select-checkbox").removeClass("selected");
                    } else {
                        table.rows().select();
                        $("th.select-checkbox").addClass("selected");
                    }
                }).on("select deselect", function() {
                    ("Some selection or deselection going on")
                    if (table.rows({
                            selected: true
                        }).count() !== table.rows().count()) {
                        $("th.select-checkbox").removeClass("selected");
                    } else {
                        $("th.select-checkbox").addClass("selected");
                    }
                });
         $('#searchByName').keyup(function(){
        table.draw();
      });

      $('#searchByGender').change(function(){
        if($(this).val()=='by_therapist_name_month')
        { $("#Month").show();}
        else
          {$("#Month").hide();}
        table.draw();
      });
     $('#searchByMonth').change(function(){
        table.draw();
      });     
      $('.datatable1 tbody').on('click', 'input[type="checkbox"]', function(e){
              
              var $row = $(this).closest('tr');

              // Get row data
              var data = table.row($row).data();
                
              // Get row ID
              var rowId = data[0];

              // Determine whether row ID is in the list of selected row IDs
              var index = $.inArray(rowId, rows_selected);

              // If checkbox is checked and row ID is not in list of selected row IDs
              if(this.checked && index === -1){
                 rows_selected.push(rowId);

              // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
              } else if (!this.checked && index !== -1){
                 rows_selected.splice(index, 1);
              }

              if(this.checked){
                 $row.addClass('selected');
              } else {
                 $row.removeClass('selected');
              }

              // Update state of "Select all" control
              updateDataTableSelectAllCtrl(table);

              // Prevent click event from propagating to parent
              e.stopPropagation();
           }); 
    }  
   
 
}

function updateDataTableSelectAllCtrl(table){
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

   // If none of the checkboxes are checked
   if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If all of the checkboxes are checked
   } else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If some of the checkboxes are checked
   } else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = true;
      }
   }
}


 
