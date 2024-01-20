<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $page }} | Madness Mart</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('public/images/madness_fevicon.png') }}">
    <!-- Custom Stylesheet -->
    <link href="{{url('public/plugins/toastr/css/toastr.min.css')}}" rel="stylesheet">
    <link href="{{ url('public/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ url('public/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ url('public/plugins/clockpicker/dist/jquery-clockpicker.min.css') }}" rel="stylesheet">
    <link href="{{ url('public/plugins/jquery-asColorPicker-master/css/asColorPicker.css') }}" rel="stylesheet">
    <link href="{{ url('public/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ url('public/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <link href="{{ url('public/plugins/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <link href="{{ url('public/css/jquery.filer.css') }}" rel="stylesheet">
    <link href="{{ url('public/css/jquery.filer-dragdropbox-theme.css') }}" rel="stylesheet">
    <link href="{{ url('public/plugins/summernote/dist/summernote.css') }}" rel="stylesheet">
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css" integrity="sha512-GPEZI1E6wle+Inl8CkTU3Ncgc9WefoWH4Jp8urbxZNbaISy0AhzIMXVzdK2GEf1+OVhA+MlcwPuNve3rL1F9yg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{url('public/css/style.css')}}" rel="stylesheet">
    <link href="{{url('public/css/custom-style.css')}}" rel="stylesheet">
{{--    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">--}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    {{--    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>--}}
</head>

<body>

<!--*******************
    Preloader start
********************-->
<div id="preloader">
    <div class="loader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
        </svg>
    </div>
</div>
<!--*******************
    Preloader end
********************-->


<!--**********************************
    Main wrapper start
***********************************-->
<div id="main-wrapper">

    <!--**********************************
        Nav header start
    ***********************************-->
    <div class="nav-header">
        <div class="brand-logo">
            <a href="#">
                <b class="logo-abbr"><img src="{{ url('public/images/madness_fevicon.png') }}" alt=""> </b>
                <span class="logo-compact"><img src="{{ url('public/images/logo-compact.png') }}" alt=""></span>
                <span class="brand-title text-white">
                    Madness Mart
                    <!-- <img src="{{ url('public/images/logo-text.png') }}" alt=""> -->
                </span>
            </a>
        </div>
    </div>
    <!--**********************************
        Nav header end
    ***********************************-->

    <!--**********************************
        Header start
    ***********************************-->
    <input type="hidden" name="web_url" value="{{ url("/") }}" id="web_url">
    <div class="header">
        <div class="header-content clearfix">

            <div class="nav-control">
                <div class="hamburger">
                    <span class="toggle-icon"><i class="icon-menu"></i></span>
                </div>
            </div>

            <div class="header-right">
                <ul class="clearfix">
                    <li class="icons dropdown">
                        <div class="user-img c-pointer position-relative"   data-toggle="dropdown">
                            <span class="activity active"></span>
                            @if(isset(\Illuminate\Support\Facades\Auth::user()->profile_pic))
                            <img src="{{ url('public/images/profile_pic/'.\Illuminate\Support\Facades\Auth::user()->profile_pic) }}" height="40" width="40" alt="Profile">
                            @else
                            <img src="{{ url('public/images/default_avatar.jpg') }}" height="40" width="40" alt="Profile">
                            @endif
                        </div>
                        <div class="drop-down dropdown-profile   dropdown-menu">
                            <div class="dropdown-content-body">
                                <ul>
                                    <li>
                                        <a href="{{ route('profile') }}"><i class="icon-lock"></i> <span>Profile</span></a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.logout') }}"><i class="icon-key"></i> <span>Logout</span></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--**********************************
        Header end ti-comment-alt
    ***********************************-->

    <!--**********************************
        Sidebar start
    ***********************************-->
    <div class="nk-sidebar">
        <div class="nk-nav-scroll">
            <ul class="metismenu" id="menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" aria-expanded="false">
                        <i class="icon-badge menu-icon"></i><span class="nav-text">Dashboard</span>
                    </a>
                </li>
                @php $leftMenuPages = getLeftMenuPages(); @endphp

                @foreach($leftMenuPages as $page)
                    @if($page['is_display_in_menu'] == 0)
                        @if(getUSerRole()==1)
                            <li>
                                <a href="{{ isset($page['route_url']) ? route($page['route_url']) : '#' }}" aria-expanded="false">
                                    <i class="icon-badge menu-icon"></i><span class="nav-text">{{ $page['label'] }}</span>
                                </a>
                            </li>
                        @else
                            <?php $check_user_permission = \App\Models\UserPermission::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)
                                ->where('project_page_id',$page['id'])
                                ->where(function($query) {
                                    $query->where('can_read',1)
                                        ->orWhere('can_write',1)
                                        ->orWhere('can_delete',1);
                                })
                                ->first(); ?>
                            @if(isset($check_user_permission) && !empty($check_user_permission))
                            <li class="leftmenu_page" data-id="{{ $page['id'] }}">
                                <a href="{{ isset($page['route_url']) ? route($page['route_url']) : '#' }}" aria-expanded="false">
                                    <i class="icon-badge menu-icon"></i><span class="nav-text">{{ $page['label'] }}</span>
                                </a>
                            </li>
                            @endif
                        @endif
                    @endif

                    @if($page['is_display_in_menu'] == 1)
                        <?php
                        $submenupages = \App\Models\ProjectPage::where('parent_menu',$page['id'])->where('is_display_in_menu',1)->get()->toArray();
                        ?>
                        @if(getUSerRole()==1)
                            <li>
                                <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                                    <i class="icon-speedometer menu-icon"></i><span class="nav-text">{{ $page['label'] }}</span>
                                </a>
                                <ul aria-expanded="false">
                                    @foreach($submenupages as $subpage)
                                    <li><a href="{{ isset($subpage['route_url']) ? route($subpage['route_url']) : '#' }}">{{ $subpage['label'] }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <?php
                                $submenupage_ids = \App\Models\ProjectPage::where('parent_menu',$page['id'])->where('is_display_in_menu',1)->pluck('id')->toArray();
                                $check_user_permissions = \App\Models\UserPermission::with('project_page')
                                    ->where('user_id',\Illuminate\Support\Facades\Auth::user()->id)
                                    ->whereIn('project_page_id',$submenupage_ids)
                                    ->where(function($query) {
                                        $query->where('can_read',1)
                                            ->orWhere('can_write',1)
                                            ->orWhere('can_delete',1);
                                    })
                                    ->get()->toArray();
                            ?>
                            @if(isset($check_user_permissions) && !empty($check_user_permissions))
                                <li>
                                    <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                                        <i class="icon-speedometer menu-icon"></i><span class="nav-text">{{ $page['label'] }}</span>
                                    </a>
                                    <ul aria-expanded="false">
                                        @foreach($check_user_permissions as $subpage)
                                            <li class="leftmenu_page" data-id="{{ $subpage['project_page']['id'] }}"><a href="{{ isset($subpage['project_page']['route_url']) ? route($subpage['project_page']['route_url']) : '#' }}">{{ $subpage['project_page']['label'] }}</a></li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endif
                    @endif
                @endforeach

            </ul>
        </div>
    </div>
    <!--**********************************
        Sidebar end
    ***********************************-->

    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">
        @yield('content')
    </div>
    <!--**********************************
        Content body end
    ***********************************-->


    <!--**********************************
        Footer start
    ***********************************-->
    <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; Designed & Developed by <a href="#">Web Vedant Technology</a> 2022</p>
        </div>
    </div>
    <!--**********************************
        Footer end
    ***********************************-->
</div>
<!--**********************************
    Main wrapper end
***********************************-->

<!--**********************************
    Scripts
***********************************-->

<script src="{{ url('public/js/common.min.js') }}"></script>
<script src="{{ url('public/js/custom.min.js') }}"></script>
<script src="{{ url('public/js/settings.js') }}"></script>
<script src="{{ url('public/js/gleek.js') }}"></script>
<script src="{{ url('public/js/styleSwitcher.js') }}"></script>
{{--<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>--}}
<!-- Toaster -->
<script src="{{ url('public/plugins/toastr/js/toastr.min.js') }}"></script>
<script src="{{ url('public/plugins/toastr/js/toastr.init.js') }}"></script>
<!-- dataTable -->
<script src="{{ url('public/plugins/tables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('public/plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('public/plugins/tables/js/datatable-init/datatable-basic.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js" type="text/javascript"></script>

<script src="{{ url('public/plugins/moment/moment.js') }}"></script>
<script src="{{ url('public/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>

<script src="{{ url('public/plugins/clockpicker/dist/jquery-clockpicker.min.js') }}"></script>
<script src="{{ url('public/plugins/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
<script src="{{ url('public/plugins/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
<script src="{{ url('public/plugins/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>
<script src="{{ url('public/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ url('public/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ url('public/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ url('public/js/plugins-init/form-pickers-init.js') }}"></script>
<script src="{{ url('public/plugins/summernote/dist/summernote.min.js') }}"></script>
<script src="{{ url('public/plugins/summernote/dist/summernote-init.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

<script src="{{ url('public/js/jquery.filer.min.js') }}" type="text/javascript"></script>
<script src="{{ url('public/js/CatImgJs.js') }}" type="text/javascript"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js" integrity="sha512-iSVaq9Huv1kxBDAOH7Il1rwIJD+uspMQC1r4Y73QquhbI2ia+PIXUoS5rBjWjYyD03S8t7gZmON+Dk6yPlWHXw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.js" integrity="sha512-jGARnXmmqjqH0OuF7bFoLjZiVwFwoFvkgGqJpUf2TNjMnKjK1rrVCwmFoUGq564c2Auaf6ULAknrnUGKK1SnqQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@yield('js')

</body>
</html>

