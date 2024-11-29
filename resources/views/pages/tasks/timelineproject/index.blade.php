@extends('layout.base')

@section('tittle', $tittle)

@push('css')
<!-- Plugin css -->

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
<script src="{{ asset('assets/libs/fullcalendar_new/dist/index.global.js') }}"></script>
<!-- Calendar init -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
    
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialDate: new Date(), // Set tanggal awal
            editable: false,
            selectable: false,
            nowIndicator: false,
            aspectRatio: 1.8,
            headerToolbar: {
                left: 'today prev,next',
                center: 'title',
                right: 'resourceTimelineYear,resourceTimelineMonth,resourceTimelineWeek'
            },
            initialView: 'resourceTimelineYear', // Default ke Year
            views: {
                resourceTimelineYear: {
                    type: 'resourceTimeline',
                    duration: { years: 3 }, // Durasi 3 tahun
                    buttonText: 'Year',
                    slotDuration: { months: 1 },
                    slotLabelFormat: [{ year: 'numeric', month: 'short' }],
                    slotLabelInterval: { months: 1 }
                },
                resourceTimelineMonth: {
                    type: 'resourceTimeline',
                    duration: { weeks: 12 }, // Durasi 12 minggu
                    buttonText: 'Month',
                    slotDuration: { days: 7 },
                    slotLabelFormat: [{ weekday: 'short', month: 'numeric', day: 'numeric', omitCommas: true }]
                },
                resourceTimelineWeek: {
                    type: 'resourceTimeline',
                    duration: { days: 20 }, // Durasi 1 bulan
                    buttonText: 'Week',
                    slotDuration: { days: 7 },
                    slotLabelFormat: [{ weekday: 'long', month: 'numeric', day: 'numeric', omitCommas: true }],
                    slotLabelInterval: { days: 1 }
                }
            },
            resourceAreaWidth: '40%',
            resourceAreaColumns: [
                {
                    group: true,
                    headerContent: 'Project',
                    field: 'project'
                },
                {
                    headerContent: 'Task',
                    field: 'task',
                }
            ],
            resources: [],
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('{{ route('tasks.data') }}') 
                    .then(response => response.json())
                    .then(data => {
                      
                        successCallback(data.events);

                        const resources = data.resources.map(resource => ({
                        id: resource.id,
                        project: resource.project,
                        task: resource.task,
                    }));
                    calendar.setOption('resources', resources);
                       
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                        failureCallback(error);
                    });
            },
        });
    
        calendar.render();
    });
</script>




@endpush
@endsection