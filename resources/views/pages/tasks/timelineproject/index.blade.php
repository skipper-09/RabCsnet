@extends('layout.base')

@section('tittle', $tittle)

@push('css')
<!-- Plugin css -->
<style>
    .fc-license-message {
        display: none;
    }
    .task{
        font-weight: bold;
        font-size: 16px
    }
</style>

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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Timeline Proyek</li>
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
<script src="{{ asset('assets/libs/fullcalendar_new/dist/index.global.js') }}"></script>
<!-- Calendar init -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialDate: new Date(),
        editable: false,
        selectable: false,
        nowIndicator: false,
        aspectRatio: 2.0,
        headerToolbar: {
            left: 'today prev,next',
            center: 'title',
            right: 'resourceTimelineYear,resourceTimelineMonth,resourceTimelineWeek'
        },
        initialView: 'resourceTimelineMonth',
        views: {
            resourceTimelineYear: {
                type: 'resourceTimeline',
                duration: { years: 3 },
                buttonText: 'Year',
                slotDuration: { months: 1 },
                slotLabelFormat: [{ year: 'numeric' }, { month: 'short' }]
            },
            resourceTimelineMonth: {
                type: 'resourceTimeline',
                duration: { month: 4 },
                buttonText: 'Month',
                slotDuration: { days: 1 },
                slotLabelFormat: [{month:'long'},{ weekday: 'short', day: 'numeric', omitCommas: true }]
            },
            resourceTimelineWeek: {
                type: 'resourceTimeline',
                duration: { week: 4 },
                buttonText: 'Week',
                slotDuration: { days: 1 },
                slotLabelFormat: [{ month: 'short'},{ weekday: 'long', day: 'numeric', omitCommas: true }]
            }
        },
        resourceAreaWidth: '40%',
        resourceAreaColumns: [  
            {
                headerContent: 'Task',
                field: 'task',
                cellClassNames: 'task',
            },
            {
                headerContent: 'Progress',
                field: 'progress'
            }
        ],
        resources: function(fetchInfo, successCallback, failureCallback) {
            fetch('{{ route('tasks.data') }}')
                .then(response => response.json())
                .then(data => {
                    successCallback(data.resources); 
                })
                .catch(error => {
                    console.error('Error fetching resources:', error);
                    failureCallback(error); 
                });
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('{{ route('tasks.data') }}')
                .then(response => response.json())
                .then(data => {
                    successCallback(data.events); 
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error); 
                });
        }
    });

    calendar.render();
});

</script>





@endpush
@endsection