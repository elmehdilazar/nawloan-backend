@extends('layouts.admin.app')
@section('title',' | ' . __('site.messages'))
@section('styles')
    <!-- Quill Editor -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/quill.snow.css')}}">
    <!-- DataTable CSS -->
    <link rel="stylesheet" href="{{asset('assets/tiny/css/dataTables.bootstrap4.css')}}">
    <!-- IntlTelInput -->
    <link rel="stylesheet" href="{{asset('assets/css/flag-icon-css/flag-icon.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/css/intlTelInput.css" rel="stylesheet">
@endsection
@section('content')
<h2 class="section-title mb-4">@lang('site.messages')</h2>
@if (session('success'))
<div class="alert alert-success" role="alert">
    <p class="mb-0">{{session('success')}}</p>
</div>
@endif
@if (session('errors'))
<div class="alert alert-danger" role="alert">
    <p class="mb-0">{{session('errors')}}</p>
</div>
@endif
<ul class="nav nav-pills gap-20 nav-fill mb-4" id="pills-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="supportMessages-tab" data-toggle="pill" href="#supportMessages"
           role="tab" aria-controls="supportMessages" aria-selected="true">@lang('site.customers_messages')</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab"
           aria-controls="pills-profile" aria-selected="false">@lang('site.emails_messages')</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab"
           aria-controls="pills-contact" aria-selected="false">@lang('site.sms_messages')</a>
    </li>
</ul>
<div class="tab-content mb-1" id="pills-tabContent">
    <div class="tab-pane fade show active" id="supportMessages" role="tabpanel" aria-labelledby="supportMessages-tab">
        <table class="table datatables datatables-active" id="">
            <thead>
                <tr>
                    <th>@lang('site.num')</th>
                    <th>@lang('site.phone')</th>
                    <th class="min-width-170">@lang('site.title')</th>
                    <th class="min-width-230">@lang('site.message')</th>
                    <th>@lang('site.name')</th>
                    <th>@lang('site.at')</th>
                    <th>@lang('site.edit')</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($messages as $index=>$message)
                <tr>
                    <td>{{$index + 1}}</td>
                    <td>{{$message->user?->phone ?? '--'}}</td>
                    <td>{{$message->title}}</td>
                    <td>
                        <p class="lines-cap-2">{{$message->message}}</p>
                    </td>
                    <td>{{$message->user?->name ?? '--'}}</td>
                    <td>{{$message->created_at}}</td>
                    <td>
                        <ul class="actions">
                            <li>
                                <a href="#" data-toggle="modal" data-target="#showModal_{{$index}}"
                                   title="@lang('site.show')">
                                    <i class="fad fa-eye"></i>
                                </a>
                            </li>
                            <li>
                                <a class="warning" href="#" data-toggle="modal"
                                   data-target="#replayModal_{{$index}}" title="@lang('site.edit')">
                                    <i class="fad fa-reply"></i>
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="flex-end mt-4">
            {{$messages->appends(request()->query())->links()}}
        </div>
        @foreach ($messages as $index=>$message)
        <!-- Modal -->
        <div class="modal fade mini-modal" id="replayModal_{{$index}}" tabindex="-1" role="dialog"
             aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verticalModalTitle_{{$index}}">@lang('site.message_replay')</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                      d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                      transform="translate(-0.384 0.422)" fill="#d27979" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('admin.messages.customer_messages.replay',$message->id)}}" method="post">
                            @csrf
                            @method('post')
                            <div class="row">
                                <div class="textarea-group">
                                    <label class="" for="desc">@lang('site.desc')</label>
                                    <textarea id="desc" name="desc" placeholder="@lang('site.desc')"
                                              rows="3" class="form-control round-btn">
                                        @if(!empty($message->desc)) {{$message->desc}} @else {{old('desc')}} @endif
                                    </textarea>
                                    @error('desc')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="textarea-group">
                                    <label class="" for="notes">@lang('site.notes')</label>
                                    <textarea id="notes" name="notes" placeholder="@lang('site.notes')" rows="3"
                                              class="form-control round-btn">
                                        @if(!empty($message->notes)) {{$message->notes}} @else {{old('notes')}} @endif
                                    </textarea>
                                    @error('notes')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex-center mt-3">
                                <button type="submit" class="btn btn-navy" title="@lang('site.replay')">@lang('site.replay')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade mini-modal" id="showModal_{{$index}}" tabindex="-1" role="dialog"
             aria-labelledby="verticalModalTitle_{{$index}}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verticalModalTitle_{{$index}}">
                            @lang('site.show') @lang('site.customers_message')
                        </h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
                                <path id="Exclusion_23" data-name="Exclusion 23"
                                      d="M26.384,37.578h-14a12,12,0,0,1-12-12v-14a12,12,0,0,1,12-12h14a12,12,0,0,1,12,12v14a12,12,0,0,1-12,12Zm-7-16.4h0L26,27.793l2.6-2.6-6.617-6.617L28.6,11.961,26,9.363,19.384,15.98,12.767,9.363l-2.6,2.6,6.617,6.617-6.617,6.617,2.6,2.6,6.616-6.616Z"
                                      transform="translate(-0.384 0.422)" fill="#d27979" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row ">
                            <div class="col-md-5">
                                @lang('site.title')
                            </div>
                            <div class="col-md-7 text-capitalize">
                                {{$message->title}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                @lang('site.user')
                            </div>
                            <div class="col-md-7 text-capitalize">
                                {{$message->user?->name ?? '--'}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                @lang('site.phone')
                            </div>
                            <div class="col-md-12">
                                <p>{{$message->phone_code." ".$message->phone_number}}</p>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                @lang('site.message')
                            </div>
                            <div class="col-md-12">
                                <p>{{$message->message}}</p>
                            </div>
                        </div>
                        @if(!empty($message->desc))
                        <div class="row ">
                            <div class="col-md-12">
                                @lang('site.desc')
                            </div>
                            <div class="col-md-12">
                                <p>{{$message->desc}}</p>
                            </div>
                        </div>
                        @endif
                        @if(!empty($message->notes))
                        <div class="row ">
                            <div class="col-md-12">
                                @lang('site.notes')
                            </div>
                            <div class="col-md-12">
                                <p>{{$message->notes}}</p>
                            </div>
                        </div>
                        @endif
                        @if(!empty($message->replay_by))
                        <div class="row ">
                            <div class="col-md-12">
                                @lang('site.replay_by')
                            </div>
                            <div class="col-md-12">
                                {{$message->replayBy->name}}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
        <form action="{{route('admin.messages.sendMail')}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('post')
            <div class="row">
                <div class="col-xl-6 col-lg-12 co-12">
                    <div class="input-group">
                        <label for="">@lang('site.email')</label>
                        <input type="email" name="email" id="email" class="form-control round-btn"
                               placeholder="@lang('site.to')">
                        @error('email')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <label for="">@lang('site.cc')</label>
                        <input type="text" name="cc" id="cc" class="form-control round-btn"
                               placeholder="@lang('site.cc')" required>
                        @error('cc')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <label for="">@lang('site.bcc')</label>
                        <input type="text" name="bcc" id="bcc" class="form-control round-btn"
                               placeholder="@lang('site.bcc')" required>
                        @error('bcc')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <label for="">@lang('site.subject')</label>
                        <input type="text" name="subject" id="subject" class="form-control round-btn"
                               placeholder="@lang('site.subject')">
                        @error('subject')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-6 col-lg-12 co-12">
                    <div class="form-group">
                        <label for="users_list_mail">@lang('site.list')</label>
                        <select class="form-control select2-multi" id="users_list_mail" name="users_list_mail[]">
                            <option value=""  disabled >@lang('site.choose_list')</option>
                            <option value="user" {{old('users_list_mail')=='user'? 'selected' : ''}}>@lang('site.customers')</option>
                            <option value="vip_user" {{old('users_list_mail')=='vip_user'? 'selected' : ''}}>@lang('site.vip') @lang('site.customers')</option>
                            <option value="factory" {{old('users_list_mail')=='factory'? 'selected' : ''}}>@lang('site.factories')</option>
                            <option value="vip_factory" {{old('users_list_mail')=='vip_factory'? 'selected' : ''}}>@lang('site.vip') @lang('site.factories')</option>
                            <option value="driver" {{old('users_list_mail')=='driver'? 'selected' : ''}}>@lang('site.driver')</option>
                            <option value="vip_driver" {{old('users_list_mail')=='vip_driver'? 'selected' : ''}}>@lang('site.vip') @lang('site.driver')</option>
                            <option value="driverCompany" {{old('users_list_mail')=='driverCompany'? 'selected' : ''}}>@lang('site.driverCompany')</option>
                            <option value="vip_driverCompany" {{old('users_list_mail')=='vip_driverCompany' ? 'selected' : '' }}>@lang('site.vip') @lang('site.driverCompany')</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-lg-12 co-12">
                    <div class="input-group">
                        <label for="">@lang('site.message')</label>
                        <div id="" class="editor" style="height: 100px;">
                            <p>Write Something.</p>
                        </div>
                        <input type="hidden" name="message" id="editor" value="{{old('message')}}">
                        @error('message')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-navy min-width-170 mt-4 mb-5"
                    title="@lang('site.send')">
                @lang('site.send')
            </button>
        </form>
        <table class="table datatables datatables-active" id="">
            <thead>
            <tr>
                <th>@lang('site.num')</th>
                <th>@lang('site.email')</th>
                <th class="min-width-230">@lang('site.message')</th>
                <th>@lang('site.type')</th>
                <th>@lang('site.sender')</th>
                <th>@lang('site.receiver')</th>
                <th>@lang('site.at')</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($emails as $index=>$message)
                <tr>
                    <td>{{$index + 1}}</td>
                    <td>{{$message->notes}}</td>
                    <td>
                        <p class="lines-cap-2">{{strip_tags($message->message)}}</p>
                    </td>
                    <td>{{$message->type}}</td>
                    <td>{{$message->sender?->name ?? '--'}}</td>
                    <td>{{$message->to}}</td>
                    <td>{{$message->created_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="flex-end mt-4">
            {{$emails->appends(request()->query())->links()}}
        </div>
    </div>
    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
        <form action="{{route('admin.messages.sendSms')}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('post')
            <div class="row">
                <div class="col-xl-6 col-lg-12 co-12">
                    <div class="input-group">
                        <label for="intl_phone">@lang('site.phone')</label>
                        <div class="international-phone gray">
                            <div class="position-relative">
                                <input type="tel" id="intl_phone" class="form-control phone"
                                       placeholder="@lang('site.phone')" value="{{old('phone')}}">
                                <img src="{{asset('assets/images/svgs/perm-phone.svg')}}" alt="" class="icon">
                            </div>
                        </div>
                        <input type="hidden" name="phone" id="phone" value="">
                        @error('phone')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                    <div class="input-group">
                        <label for="">@lang('site.message')</label>
                        <div id="" class="editor" style="height: 100px;">
                            <p>Write Something.</p>
                        </div>
                        <input type="hidden" name="message" id="editor" value="{{old('message')}}">
                        @error('message')
                        <span class="text-danger"> {{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-xl-6 col-lg-12 co-12">
                    <div class="form-group">
                        <label for="users_list_mail">@lang('site.list')</label>
                        <select class="form-control select2-multi" iid="users_list" name="users_list[]">
                            <option value=""  disabled >@lang('site.choose_list')</option>
                            <option value="user" {{old('users_list')=='user'? 'selected' : ''}}>@lang('site.customers')</option>
                            <option value="vip_user" {{old('users_list')=='vip_user'? 'selected' : ''}}>@lang('site.vip') @lang('site.customers')</option>
                            <option value="factory" {{old('users_list')=='factory'? 'selected' : ''}}>@lang('site.factories')</option>
                            <option value="vip_factory" {{old('users_list')=='vip_factory'? 'selected' : ''}}>@lang('site.vip') @lang('site.factories')</option>
                            <option value="driver" {{old('users_list')=='driver'? 'selected' : ''}}>@lang('site.driver')</option>
                            <option value="vip_driver" {{old('users_list')=='vip_driver'? 'selected' : ''}}>@lang('site.vip') @lang('site.driver')</option>
                            <option value="driverCompany" {{old('users_list')=='driverCompany'? 'selected' : ''}}>@lang('site.driverCompany')</option>
                            <option value="vip_driverCompany" {{old('users_list')=='vip_driverCompany' ? 'selected' : '' }}>@lang('site.vip') @lang('site.driverCompany')</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-navy min-width-170 mt-4 mb-5"
                    title="@lang('site.send')">
                @lang('site.send')
            </button>
        </form>
        <table class="table datatables datatables-active" id="">
            <thead>
            <tr>
                <th>@lang('site.num')</th>
                <th>@lang('site.phone')</th>
                <th class="min-width-230">@lang('site.message')</th>
                <th>@lang('site.type')</th>
                <td>@lang('site.sender')</td>
                <td>@lang('site.receiver')</td>
                <td>@lang('site.at')</td>
                <td>@lang('site.status')</td>
            </tr>
            </thead>
            <tbody>
            @foreach ($sms as $index=>$message)
                <tr>
                    <td>{{$index + 1}}</td>
                    <td>{{$message->receiver?->phone ?? $message->notes}}</td>
                    <td>
                        <p class="lines-cap-2">{{$message->message}}</p>
                    </td>
                    <td>{{$message->type}}</td>
                    <td>{{$message->sender?->name ?? '--'}}</td>
                    <td>{{$message->receiver?->name ?? '--'}}</td>
                    <td>{{$message->created_at}}</td>
                    <td>
                        <span class="badge badge-pill @if($message->status =='wait') badge-primary @elseif ($message->status=='complete') badge-success
                        @elseif ($message->status=='REJECTED') badge-danger @endif">
                            @if($message->status=='wait')
                                @lang('site.wait')
                            @elseif($message->status=='complete')
                                @lang('site.completed')
                            @elseif($message->status=='REJECTED')
                                @lang('site.REJECTED')
                            @endif
                        </span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="flex-end mt-4">
            {{$sms->appends(request()->query())->links()}}
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <!-- Data Tables -->
    <script src='{{asset('assets/tiny/js/jquery.dataTables.min.js')}}'></script>
    <script src='{{asset('assets/tiny/js/dataTables.bootstrap4.min.js')}}'></script>
    <script>
        $('.datatables-active').DataTable({
            info: false,
            paging: false,
            searching: false,
            autoWidth: true,
            "bLengthChange": false,
        });
        const containers = document.querySelectorAll('.dataTables_wrapper .row:nth-child(2) [class*="col-"]');
        containers.forEach(container => {
            container.addEventListener('wheel', (event) => {
                if (container.scrollLeft === 0 && event.deltaY < 0) {
                    return;
                } else if (container.scrollLeft === container.scrollWidth - container.clientWidth && event.deltaY > 0) {
                    return;
                }
                event.preventDefault();
                container.scrollLeft += event.deltaY;
            });
        });
    </script>
    <!-- Quill Editor -->
    <script src='{{asset('assets/tiny/js/quill.min.js')}}'></script>
    <script>
        var editors = document.querySelectorAll('.editor');
        if (editors) {
            var toolbarOptions = [
                [
                    {
                        'font': []
                    }
                ],
                [
                    {
                        'header': [1, 2, 3, 4, 5, 6, false]
                    }
                ],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote'],
                [
                    {
                        'list': 'ordered'
                    },
                    {
                        'list': 'bullet'
                    }
                ],
                [
                    {
                        'indent': '-1'
                    },
                    {
                        'indent': '+1'
                    }
                ], // outdent/indent
                ['clean'] // remove formatting button
            ];
            editors.forEach(editor => {
                var quill = new Quill(editor, {
                    modules:
                        {
                            toolbar: toolbarOptions
                        },
                    theme: 'snow'
                });
                quill.on('text-change', function() {
                    var content = quill.getText();
                    var input = editor.closest('.input-group').querySelector('input[type="hidden"]');
                    input.value = content;
                });
            });
        }
    </script>
    <!-- IntlTelInput -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.7/js/intlTelInput.js"></script>
    <script>
        $(document).ready(function(){
            let country_codes= <?php echo json_encode( \App\Models\Country::select('country_code')->get()); ?>;
            let countries=[];
            for(var i=0;i<country_codes.length;i++){
                countries.push(country_codes[i].country_code);
            }
            $(".phone").intlTelInput({
                rtl: true,
                initialCountry: "eg",
                autoHideDialCode:false,
                allowDropdown:false,
                nationalMode: true,
                numberType: "MOBILE",
                onlyCountries:countries,// ['us', 'gb', 'ch', 'ca', 'do'],
                preferredCountries:['eg','sa','ue'],// ['sa', 'ae', 'qa','om','bh','kw','ma'],
                preventInvalidNumbers: true,
                separateDialCode: true ,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/js/utils.js",
            });

            $(".phone").on('change',function(e){
                e.preventDefault();
                $('#phone').val($(".phone").intlTelInput("getNumber"));
            });
            $(".intl-tel-input .country-list").on('click',function(e){
                e.preventDefault();
                $('#phone').val($(".phone").intlTelInput("getNumber"));
            });

            $(".intl-tel-input.allow-dropdown .flag-container").on('click',function(e){
                // e.stopPropagation();
                $(this).toggleClass("dropdown-opened");
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.intl-tel-input.allow-dropdown .flag-container').length) {
                    $('.intl-tel-input.allow-dropdown .flag-container').removeClass('dropdown-opened');
                }
            });
        });
    </script>
@endsection
