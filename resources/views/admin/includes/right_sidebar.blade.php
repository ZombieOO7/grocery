@php
$setting = getWebSetting();
@endphp
<div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general m-stack--fluid">
    <div class="m-stack__item m-topbar__nav-wrapper">
        <ul class="m-topbar__nav m-nav m-nav--inline">
            @if(isset($setting) && $setting->notify == 1)
            <li class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-center 	m-dropdown--mobile-full-width" m-dropdown-toggle="click" m-dropdown-persistent="1">
                {{-- <a href="#" class="m-nav__link m-dropdown__toggle" id="m_topbar_notification_icon">
                    <span class="m-nav__link-badge m-badge m-badge--dot m-badge--dot-small m-badge--danger"></span>
                    <span class="m-nav__link-icon">
                        <i class="flaticon-music-2"></i>
                    </span>
                </a> --}}
                <a href="#" class="m-nav__link m-dropdown__toggle" id="m_topbar_notification_icon">
                    <span class="m-nav__link-badge m-badge m-badge--brand" id='count-notification' style="display:none;"></span>
                    <span class="m-nav__link-icon"><i class="flaticon-alert-2"></i></span>
                </a>
                <div class="m-dropdown__wrapper" style="margin-left: -275px !important;">
                    <span class="m-dropdown__arrow m-dropdown__arrow--right" style="left: auto;right: 85px;"></span>
                    <div class="m-dropdown__inner">
                        {{-- <div class="m-dropdown__header m--align-center" style="background:  url({{asset('images/notification_bg.jpg')}}); background-size: cover;">
                            <span class="m-dropdown__header-title" id='data-count'>{{($totalNotification==0)? 'No Notifications':$totalNotification.' New'}}</span>
                            <span class="m-dropdown__header-subtitle" id='data-count-label'>{{($totalNotification==0)? '':'User Notifications' }}</span>
                        </div> --}}
                        <div class="m-dropdown__body">
                            <div class="m-dropdown__content">
                                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--brand" role="tablist">
                                    <li class="nav-item m-tabs__item">
                                        <a id='alertDiv' style="display:none;" class="nav-link m-tabs__link active" data-toggle="tab" href="#topbar_notifications_notifications" role="tab">
                                            Alerts
                                        </a>
                                    </li>
                                    {{-- <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_events" role="tab">Events</a>
                                    </li>
                                    <li class="nav-item m-tabs__item">
                                        <a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_logs" role="tab">Logs</a>
                                    </li> --}}
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="topbar_notifications_notifications" role="tabpanel">
                                        <div class="m-scrollable" data-scrollable="true" data-height="250" data-mobile-height="200">
                                            <div class="m-list-timeline m-list-timeline--skin-light">
                                                <div class="m-list-timeline__items">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="topbar_notifications_events" role="tabpanel">
                                        <div class="m-scrollable" data-scrollable="true" data-height="250" data-mobile-height="200">
                                            <div class="m-list-timeline m-list-timeline--skin-light">
                                                <div class="m-list-timeline__items">
                                                    <div class="m-list-timeline__item">
                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-success"></span>
                                                        <a href="" class="m-list-timeline__text">New order received</a>
                                                        <span class="m-list-timeline__time">Just now</span>
                                                    </div>
                                                    <div class="m-list-timeline__item">
                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-danger"></span>
                                                        <a href="" class="m-list-timeline__text">New invoice received</a>
                                                        <span class="m-list-timeline__time">20 mins</span>
                                                    </div>
                                                    <div class="m-list-timeline__item">
                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-success"></span>
                                                        <a href="" class="m-list-timeline__text">Production server up</a>
                                                        <span class="m-list-timeline__time">5 hrs</span>
                                                    </div>
                                                    <div class="m-list-timeline__item">
                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-info"></span>
                                                        <a href="" class="m-list-timeline__text">New order received</a>
                                                        <span class="m-list-timeline__time">7 hrs</span>
                                                    </div>
                                                    <div class="m-list-timeline__item">
                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-info"></span>
                                                        <a href="" class="m-list-timeline__text">System shutdown</a>
                                                        <span class="m-list-timeline__time">11 mins</span>
                                                    </div>
                                                    <div class="m-list-timeline__item">
                                                        <span class="m-list-timeline__badge m-list-timeline__badge--state1-info"></span>
                                                        <a href="" class="m-list-timeline__text">Production server down</a>
                                                        <span class="m-list-timeline__time">3 hrs</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="topbar_notifications_logs" role="tabpanel">
                                        <div class="m-stack m-stack--ver m-stack--general" style="min-height: 180px;">
                                            <div class="m-stack__item m-stack__item--center m-stack__item--middle">
                                                <span class="">All caught up!
                                                    <br>No new logs.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            @endif
            <li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"
                m-dropdown-toggle="click">
                <a href="javascript:;" class="m-nav__link m-dropdown__toggle">
                    <span class="m-topbar__userpic">
                        <img src="{{asset('images/user_profile_bg.jpg')}}" class="m--img-rounded m--marginless" alt=""
                            style="text-align: center;
                                        max-width: 40px!important;
                                        height: 40px;
                                        margin: 0 auto!important;
                                        border-radius: 50%;
                                        width: 100%;">
                    </span>
                    <span class="m-topbar__username m--hide">Nick</span>
                </a>
                <div class="m-dropdown__wrapper">
                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                    <div class="m-dropdown__inner">
                        <div class="m-dropdown__header m--align-center"
                            style="background: url({{ asset('/images/user_profile_bg.jpg') }}); background-size: cover;">
                            <div class="m-card-user m-card-user--skin-dark">
                                <div class="m-card-user__pic">
                                    <img src="{{  config('app.defaultAdminProfilePic') }}"
                                        class="m--img-rounded m--marginless" alt="" style="text-align: center;height: 70px;
                                                        " />
                                </div>
                                <div class="m-card-user__details">
                                    <span class="m-card-user__name m--font-weight-500">
                                        @if(\Auth::guard('admin')->user())
                                        {{ \Auth::guard('admin')->user()->first_name . \Auth::guard('admin')->user()->last_name }}
                                    </span>
                                    <a href="javascript:;" class="m-card-user__email m--font-weight-300 m-link">
                                        {{  \Auth::guard('admin')->user()->email }}
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="m-dropdown__body">
                            <div class="m-dropdown__content">
                                <ul class="m-nav m-nav--skin-light">
                                    <li class="m-nav__item">
                                        <a href="{{ route('admin.logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"
                                            class="m-nav__link">
                                            <i class="m-nav__link-icon flaticon-logout"></i>
                                            <span class="m-nav__link-text">Logout</span>
                                        </a>
                                        <form id="frm-logout" action="{{ route('admin.logout') }}" method="post"
                                            style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>