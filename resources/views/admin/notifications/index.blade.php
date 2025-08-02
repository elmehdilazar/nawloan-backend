@extends('layouts.admin.app')
@section('title',' | ' . __('site.notifications'))
@section('styles')
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
@endsection
@section('content')
    <div class="flex-space mb-4 dash-head">
        <h2 class="section-title mb-0">@lang('site.all') @lang('site.notifications')</h2>
        <div class="head-btns mb-0">
            <span id="checks-count" class="onchange-visible"></span>
            <a href="#" class="btn btn-transparent navy">@lang('site.export')</a>
            <a href="#" class="btn btn-danger onchange-visible">Delete</a>
            <a href="{{route('admin.MarkAsRead_all')}}" class="btn btn-navy" title="@lang('site.read_all')">
                @lang('site.read_all')
            </a>
        </div>
    </div>
    <table class="table datatables datatables-active" id="">
        <thead>
            <tr>
                <th>
                    <div class="dt-checkbox">
                        <input type="checkbox" name="select_all" value="1" id="selectAll">
                        <label for="selectAll" class="visual-checkbox"></label>
                    </div>
                </th>
                <th>@lang('site.num')</th>
                <th>@lang('site.notification')</th>
                <th>@lang('site.name')</th>
                <th>@lang('site.at')</th>
                <th>@lang('site.edit')</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($notifications as $index=>$notification)
            <tr>
                <td></td>
                <td>{{$index + 1}}</td>
                <td>
                    @lang('site.' .$notification->data['title'] )
                    @lang('site.'. $notification->data['target'] )
                    {{$notification->data['target_id']}}
                </td>
                <td>{{ $notification->data['user'] }}</td>
                <td>{{$notification->created_at->diffForHumans()}}</td>
                <td>
                    @if ($notification->read_at!=null)
                        {{ $notification->read_at->diffForHumans() }}
                    @else
                        <ul class="actions">
                            <li>
                                <a href="{{route('admin.showAndRead',['id'=>$notification->id])}}" title="@lang('site.view')">
                                    <i class="fad fa-comment-check"></i>
                                </a>
                            </li>
                        </ul>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="flex-end mt-4">
        {{$notifications->appends(request()->query())->links()}}
    </div>
@endsection

@section('scripts')
    <!-- Data Tables -->
    <script src='{{asset('assets/tiny/js/jquery.dataTables.min.js')}}'></script>
    <script src='{{asset('assets/tiny/js/dataTables.bootstrap4.min.js')}}'></script>
    <!-- DataTables Playground (Setups, Options, Actions) -->
    <script src='{{asset('assets/js/dataTables-init.js')}}'></script>
@endsection
