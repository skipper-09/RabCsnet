@extends('layout.base')

@section('tittle', $tittle)

@push('css')
<!-- Plugin css -->
<link rel="stylesheet" href="{{ asset('assets/libs/@fullcalendar/core/main.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/libs/@fullcalendar/daygrid/main.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/libs/@fullcalendar/bootstrap/main.min.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('assets/libs/@fullcalendar/timegrid/main.min.css') }}" type="text/css">
@endpush

@section('content')


<!-- start page title -->
<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="page-title">
                    <h4>Calendar</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Morvin</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Calendar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->




<div class="page-content-wrapper">
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card mb-0">
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row-->
    {{-- <div style='clear:both'></div> --}}


</div>
<!-- End Page-content -->
<!-- end main content-->


@push('js')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
{{-- custom swetaert --}}
<script src="{{ asset('assets/js/custom.js') }}"></script>

<!-- plugin js -->
<script src="{{ asset('assets/libs/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('assets/libs/jquery-ui-dist/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/libs/@fullcalendar/core/main.min.js') }}"></script>
<script src="{{ asset('assets/libs/@fullcalendar/bootstrap/main.min.js') }}"></script>
<script src="{{ asset('assets/libs/@fullcalendar/daygrid/main.min.js') }}"></script>
<script src="{{ asset('assets/libs/@fullcalendar/timegrid/main.min.js') }}"></script>
<script src="{{ asset('assets/libs/@fullcalendar/interaction/main.min.js') }}"></script>
<!-- Calendar init -->
<script>
   document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ["bootstrap", "interaction", "dayGrid", "timeGrid"],
            editable: !0,
            droppable: !0,
            selectable: !0,
            defaultView: "dayGridMonth",
            themeSystem: "bootstrap",
            header: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
            },
        events: "{{ route('tasks.data') }}",
        eventClick: function(info) {
            // Handle event click
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.open(info.event.url);
            }
        },
        select: function(info) {
            // Handle date selection
        }
    });
    
    calendar.render();
});
</script>





@endpush
@endsection