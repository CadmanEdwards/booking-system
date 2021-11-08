<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')
    <style>
        .cke_dialog_container {
            z-index: 20010 !important;
        }
    </style>
</head>

<body class="page-header-fixed">
    <div class="page-header navbar navbar-fixed-top">
        @include('partials.header')
    </div>

    <div class="clearfix"></div>

    <div class="page-container">
        <div class="page-sidebar-wrapper">
            @include('partials.sidebar')
        </div>

        <div class="page-content-wrapper">
            <div class="page-content">

                @if(isset($siteTitle))
                    <h3 class="page-title">
                        {{ $siteTitle }}
                    </h3>
                @endif

                <div class="row">
                    <div class="col-md-12">

                        @if (Session::has('message'))
                            <div class="note note-info">
                                <p>{{ Session::get('message') }}</p>
                            </div>
                        @endif
                        @if ($errors->count() > 0)
                            <div class="note note-danger">
                                <ul class="list-unstyled">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @yield('content')

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="scroll-to-top"
         style="display: none;">
        <i class="fa fa-arrow-up"></i>
    </div>

    {!! Form::open(['route' => 'auth.logout', 'style' => 'display:none;', 'id' => 'logout']) !!}
        <button type="submit">Logout</button>
    {!! Form::close() !!}
   
    @include('partials.javascripts')
    <script>
        function uploadExcelCustomer(){
            event.preventDefault();

            var token = $(event.target).find('input[name =_token]').val();

            if ($('#excelFileInput').get(0).files.length !== 0) {
                var file = $("#excelFileInput")[0].files[0];

                var formData = new FormData();
                formData.append("file", file);
                formData.append("_token", token);
                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();

                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                percentComplete = parseInt(percentComplete * 100);


                                $('#progress').attr("aria-valuenow",percentComplete).css("width",percentComplete+'%');



                            }
                        }, false);

                        return xhr;
                    },
                    url: "{{ route("admin.clients.post.excel") }}",
                    type: "POST",
                    processData: false,
                    contentType: false,
                    cache: false,
                    enctype: 'multipart/form-data',
                    data: formData,
                    success: function(result) {
                        console.log(result);

                        if(result.status){
                            alert("File has been uploaded successfully.")
                            window.location.href = "{{ \Illuminate\Support\Facades\URL::to('/') }}"+"/admin/clients"
                        }else{
                            alert("Error while uploading file")
                        }

                    }
                });
            }


        }

    </script>
    @if(isset($rooms) && isset($url))


        <script>
            var time;
            var isWorkinghour = false;


            $('.date').datepicker({
                autoclose: true,
                dateFormat: "{{ config('app.date_format_js') }}"
            }).datepicker("setDate", "0");



            //alert(newdate)
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var location_id =  document.getElementById('location_id_latest').value;

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: [ 'resourceTimeGrid', 'dayGrid','timeGrid','interaction' ],
                    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                    height: 'parent',
                    timeZone: 'UTC',
                    /* defaultView: 'resourceTimeGridDay',
                    defaultView: 'resourceTimeGridThirtyDay',*/
                    defaultView: 'resourceTimeGridDay',
                    header: {
                        left: 'prev,next',
                        center: 'title',
                        right: 'resourceTimeGridDay,resourceTimeGridFourDay,resourceTimeGridSevanDay'
                    },
                    titleFormat: {   month: 'long',
                        year: 'numeric',
                        day: 'numeric',
                        weekday: 'long'},
                    views: {
                        resourceTimeGridSevanDay: {
                            type: 'resourceTimeGrid',
                            duration: { days: 7 },
                            buttonText: 'Week'
                        },
                        resourceTimeGridFourDay: {
                            type: 'resourceTimeGrid',
                            duration: { days: 4 },
                            buttonText: '4 Days'
                        }
                    },
                    resources: {!! $rooms !!},

                    events: { url:'{{ $url  }}',
                        startParam  : 'Dates',
                        extraParams: function() { // a function that returns an object
                            return {
                                location_id: document.getElementById('location_id_latest').value,
                                therapist_id: document.getElementById('thera').value
                            };
                        }

                    },
                    dateClick: function(info) {
                        time =  info.dateStr
                        setTimeout(function () {

                            if(document.getElementById('thera').value != 0 && !isWorkinghour){

                                window.location.href  = '/admin/employees_customtiming/create/'+document.getElementById('thera').value+'?date='+time+'&start='+time+'&location_id='+$("#loca").val()
                            }
                        },1000)


                    },

                    eventClick: function(info) {
                        info.jsEvent.preventDefault();
                        if(info.event.extendedProps.description == 'Working hours'){
                            isWorkinghour = true
                        }

                        setTimeout(function (){
                            if (info.event.url) {
                                if(info.event.title != 'Available'){
                                    if(info.event.loc){
                                        window.location.href = info.event.url+"&selectedTime="+time+"&location_id="+info.event.loc
                                    }else{
                                        window.location.href = info.event.url+"&selectedTime="+time+"&location_id="+$("#loca").val()
                                    }
                                }else{
                                    if(info.event.loc){
                                    window.location.href = info.event.url+"&selectedTime="+time+"&location_id="+info.event.loc
                                    }else{
                                        window.location.href = info.event.url+"&selectedTime="+time+"&location_id="+$("#loca").val()
                                    }
                                }
                                return false;
                            }
                        },1000)
                    },
                    eventRender: function(info) {
                        //console.log(info.event.extendedProps.description);
                        console.log(info)

                        var tooltip = new Tooltip(info.el, {
                            title: info.event.extendedProps.description,
                            placement: 'top',
                            trigger: 'hover',
                            container: 'body',
                            html: true,
                        });
                    }

                });

                calendar.render();
                var calendarEve = calendar.getEventSourceById('calendar');

                $('#thera').on('change',function(){
                    if($(this).val() == 0){
                        $('#therapist_work_hour').hide()
                        $('#admin_working_hour').show()

                    }else{
                        $('#therapist_work_hour').show()
                        $('#admin_working_hour').hide()
                        $('#therapist_work_hour').attr('href',"{{ \Illuminate\Support\Facades\URL::to('/').'/admin/employees_working_hour/' }}"+$(this).val()+"")
                    }
                    calendar.refetchEvents();
                })
                $('.locationId').on('change',function(){
                    $('#location_id_latest').val($(this).val());
                    //var eventSource = calendar.getEventSourceById('calendar');
                    calendar.refetchEvents();
                    //calendar.refetch();
                })

                $('.date').on('change',function(){
                    //$('#location_id_latest').val($(this).val());
                    //var eventSource = calendar.getEventSourceById('calendar');
                    //calendar.refetchEvents();
                    calendar.gotoDate( $(this).val())
                    calendar.changeView('resourceTimeGridDay');

                    //calendar.fullCalendar('gotoDate', $(this).val());
                    //calendar.refetch();
                })


            });

        </script>
    @endif
</body>
</html>