<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{@$title}}</title>
    <style>
    .hid_spn {
        display: none !important;
    }
    </style>
</head>

<body>
    <table class="table table-striped table-responsive table-bordered table-hover table-checkable for_wdth">
        <thead>
            @if($type == 2 || $type == 3)
            <tr>
                @if($type ==2)
                <th colspan="9">
                    {{-- Location: {{$name}} --}}
                </th>
                @elseif($type ==3)
                <th colspan="9">
                    Summary of job done (options for numbers or graph)
                </th>
                @endif
            </tr>
            {{-- <tr> --}}
                {{-- <th colspan="10"></th> --}}
                {{-- <th colspan="3">Total Work Order Completed</th> --}}
            {{-- </tr> --}}
            @endif
            @if($type == 5)
            <tr>
                <th colspan="6">Fitter : {{@$name}} </th>
            </tr>
            <tr>
                <th>{{__('formname.report.no')}}</th>
                <th>{{__('formname.report.date')}}</th>
                <th>{{__('formname.job.machine')}}</th>
                <th>{{__('formname.job.problem')}}</th>
                <th>{{__('formname.job.job_status')}}</th>
                <th>{{__('formname.job.job_hours')}}</th>
                <th>{{__('formname.job.priority')}}</th>
            </tr>
            @elseif($type == 6)
            <tr>
                <th colspan="6"> {{@$name}} </th>
            </tr>
            <tr>
                <th>{{__('formname.report.no')}}</th>
                <th>{{__('formname.report.date')}}</th>
                <th>{{__('formname.job.problem')}}</th>
                <th>{{__('formname.job.assigned_to')}}</th>
                <th>{{__('formname.job.job_hours')}}</th>
                <th>{{__('formname.job.priority')}}</th>
            </tr>
            @elseif($type == 7 || $type == 8 || $type == 9)
            <tr>
                <th>{{__('formname.report.no')}}</th>
                <th>{{__('formname.report.date')}}</th>
                <th>{{__('formname.job.machine')}}</th>
                <th>{{__('formname.job.assigned_to')}}</th>
                <th>{{__('formname.job.job_hours')}}</th>
                <th>{{__('formname.job.problem')}}</th>
                <th>{{__('formname.job.comment')}}</th>
                <th>{{__('formname.job.priority')}}</th>
            </tr>
            @else
            <tr>
                <th>{{__('formname.report.no')}}</th>
                @if($type == 4)
                    <th>{{__('formname.report.engineer')}}</th>
                @else
                    <th>{{@$title}} {{ isset($name)?'(In '.$name.')':''}}</th>
                @endif
                <th>{{__('formname.report.total_job_request')}}</th>
                <th>{{__('formname.report.job_request')}}</th>
                <th>{{__('formname.report.assigned')}}</th>
                <th>{{__('formname.report.work_order')}}</th>
                <th>{{__('formname.report.complete')}}</th>
                <th>{{__('formname.report.decline')}}</th>
                <th>{{__('formname.report.kiv')}}</th>
                <th>{{__('formname.report.unable_to_complete')}}</th>
                @if($type == 4)
                    {{-- <th>{{__('formname.report.incomplete')}}</th> --}}
                @else
                    {{-- <th>{{__('formname.report.decline')}}</th> --}}
                    {{-- <th>{{__('formname.report.kiv')}}</th> --}}
                @endif
                <th>{{__('formname.report.pending')}}</th>
                <th>{{__('formname.report.preventive')}}</th>
                <th>{{__('formname.report.normal')}}</th>
                <th>{{__('formname.report.argent')}}</th>
            </tr>
            @endif
        </thead>
        <tbody>
            @php
                $i = 0;    
            @endphp
            @if($type == 5)
                @forelse($reportData as $key => $data)
                @php
                    $i++;
                @endphp
                <tr>
                    <td>{{@$i}}</td>
                    <td>{{date('d-m-Y', strtotime($data->created_at))}}</td>
                    <td>{{@$data->machine->title}}</td>
                    <td>{{@$data->problem->title}}</td>
                    <td>{{@config('constant.job_status_text')[$data->job_status_id]}}</td>
                    <td>{{@$data->total_job_duration}}</td>
                    <td>{{@config('constant.priorites_report_text')[$data->priority]}}</td>
                </tr>
                @empty
                    <tr>
                        <td colspan="4" align="center">No records founds</td>
                    </tr>
                @endforelse
            @elseif($type == 6)
                @forelse($reportData as $key => $data)
                @php
                    $i++;
                @endphp
                <tr>
                    <td>{{@$i}}</td>
                    <td>{{date('d-m-Y', strtotime($data->created_at))}}</td>
                    <td>{{@$data->problem->title}}</td>
                    <td>{{@$data->assignedTo->full_name }}</td>
                    <td>{{@$data->total_job_duration}}</td>
                    <td>{{@config('constant.priorites_report_text')[$data->priority]}}</td>
                </tr>
                @empty
                    <tr>
                        <td colspan="4" align="center">No records founds</td>
                    </tr>
                @endforelse
            @elseif($type == 7 || $type == 8 || $type == 9)
                @forelse($reportData as $key => $data)
                @php
                    $i++;
                @endphp
                <tr>
                    <td>{{@$i}}</td>
                    <td>{{date('d-m-Y', strtotime($data->created_at))}}</td>
                    <td>{{@$data->machine->title}}</td>
                    <td>{{@$data->assignedTo->full_name }}</td>
                    <td>{{@$data->total_job_duration}}</td>
                    <td>{{@$data->problem->title}}</td>
                    <td>{{@$data->comment}}</td>
                    <td>{{@config('constant.priorites_report_text')[$data->priority]}}</td>
                </tr>
                @empty
                    <tr>
                        <td colspan="4" align="center">No records founds</td>
                    </tr>
                @endforelse
            @else
                @forelse($reportData as $key => $data)
                <tr>
                    @php
                        $i++;
                    @endphp
                    <td>{{$i}}</td>
                    @if($type ==4)
                        <td>{{@$data->full_name}}</td>
                    @else
                        <td>{{@$data->title}}</td>
                    @endif
                        <td>{{@$data->total_job_request}}</td>
                        <td>{{@$data->job_request}}</td>
                        <td>{{@$data->assigned}}</td>
                        <td>{{@$data->work_order}}</td>
                        <td>{{@$data->complete}}</td>
                        <td>{{@$data->declined}}</td>
                        <td>{{@$data->kiv}}</td>
                        <td>{{@$data->unable_to_complete}}</td>
                    @if($type ==4)
                        {{-- <td>{{@$data->incomplete}}</td> --}}
                    @else
                        {{-- <td>{{@$data->declined}}</td> --}}
                        {{-- <td>{{@$data->kiv}}</td> --}}
                    @endif
                    <td>{{@$data->pending}}</td>
                    <td>{{@$data->low}}</td>
                    <td>{{@$data->medium}}</td>
                    <td>{{@$data->high}}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" align="center">No records founds</td>
                </tr>
                @endforelse                
            @endif
        </tbody>
    </table>
</body>
</html>
@php
// exit;    
@endphp