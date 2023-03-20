<?php
$routeName = Route::currentRouteName();
?>
<!-- BEGIN: Left Aside -->
<button class="m-aside-left-close  m-aside-left-close--skin-dark " id="m_aside_left_close_btn">
    <i class="la la-close"></i>
</button>
<div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
    <!-- BEGIN: Aside Menu -->
    <div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark " m-menu-vertical="1"
        m-menu-scrollable="1" m-menu-dropdown-timeout="500" style="position: relative;">
        <ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow side_bar">
            <li class="m-menu__item  @if ($routeName == 'admin_dashboard') m-menu__item--active @endif"
                aria-haspopup="true">
                <a href="{{ route('admin_dashboard') }}" class="m-menu__link ">
                    <i class="m-menu__link-icon fa 	fa-tv"></i>
                    <span class="m-menu__link-title">
                        <span class="m-menu__link-wrap">
                            <span class="m-menu__link-text">{{ __('formname.dashboard') }}</span>
                        </span>
                    </span>
                </a>

            </li>
            <li class="m-menu__item  m-menu__item--submenu 
                    @if ($routeName == 'cms_index' ||$routeName == 'cms_create' ||$routeName == 'cms_edit' ||
                        $routeName == 'category.index' || $routeName == 'category.create' || $routeName == 'category.edit' ||
                        $routeName == 'subcategory.index' || $routeName == 'subcategory.create' || $routeName == 'subcategory.edit' ||
                        $routeName == 'banner.index' || $routeName == 'banner.create' || $routeName == 'banner.edit'
                        ) m-menu__item--active m-menu__item--open @endif "
                    aria-haspopup="true" m-menu-submenu-toggle="hover">
                    <a href="javascript:;" class="m-menu__link m-menu__toggle">
                        <i class="m-menu__link-icon flaticon-layers"></i>
                        <span class="m-menu__link-text">{{ __('formname.masters') }}</span>
                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <!-- CMS Management -->
                    {{-- <div class="m-menu__submenu ">
                        <span class="m-menu__arrow"></span>
                        <ul class="m-menu__subnav">
                            @if (
                                \Auth::guard('admin')->user()->hasAnyPermission([
                                        'page view',
                                        'page create',
                                        'page edit',
                                        'page delete',
                                        'page multiple delete',
                                    ]))
                                <li class="m-menu__item @if ($routeName == 'cms_index' || $routeName == 'cms_create' || $routeName == 'cms_edit') m-menu__item--active @endif"
                                    aria-haspopup="true">
                                    <a href="{{ route('cms_index') }}" class="m-menu__link">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">{{ __('formname.cms_mgt') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div> --}}
                    <!-- Category Management -->
                    <div class="m-menu__submenu ">
                        <span class="m-menu__arrow"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item @if ($routeName == 'category.index' || $routeName == 'category.create' || $routeName == 'category.edit') m-menu__item--active @endif"
                                aria-haspopup="true">
                                <a href="{{ route('category.index') }}" class="m-menu__link">
                                    <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="m-menu__link-text">{{ __('formname.category.label') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Sub Category Management -->
                    <div class="m-menu__submenu ">
                        <span class="m-menu__arrow"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item @if ($routeName == 'subcategory.index' || $routeName == 'subcategory.create' || $routeName == 'subcategory.edit') m-menu__item--active @endif"
                                aria-haspopup="true">
                                <a href="{{ route('subcategory.index') }}" class="m-menu__link">
                                    <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="m-menu__link-text">{{ __('formname.subcategory.label') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Banner Management -->
                    <div class="m-menu__submenu ">
                        <span class="m-menu__arrow"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item @if ($routeName == 'banner.index' || $routeName == 'banner.create' || $routeName == 'banner.edit') m-menu__item--active @endif"
                                aria-haspopup="true">
                                <a href="{{ route('banner.index') }}" class="m-menu__link">
                                    <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="m-menu__link-text">{{ __('formname.banner.label') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Faq Management -->
                    {{-- <div class="m-menu__submenu ">
                        <span class="m-menu__arrow"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item @if ($routeName == 'faq.index' || $routeName == 'faq.create' || $routeName == 'faq.edit') m-menu__item--active @endif"
                                aria-haspopup="true">
                                <a href="{{ route('faq.index') }}" class="m-menu__link">
                                    <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="m-menu__link-text">{{ __('formname.faq.label') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div> --}}
                    <!-- Email Template Management -->
                    {{-- <div class="m-menu__submenu ">
                        <span class="m-menu__arrow"></span>
                        <ul class="m-menu__subnav">
                            <li class="m-menu__item @if ($routeName == 'emailTemplate.index' || $routeName == 'emailTemplate.create' || $routeName == 'emailTemplate.edit') m-menu__item--active @endif"
                                aria-haspopup="true">
                                <a href="{{ route('emailTemplate.index') }}" class="m-menu__link">
                                    <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="m-menu__link-text">{{ __('formname.emailTemplate.label') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div> --}}
                </li>
            {{-- @if (
                \Auth::guard('admin')->user()->hasAnyPermission([
                        'user view',
                        'user create',
                        'user edit',
                        'user delete',
                        'user multiple delete',
                        'user multiple active',
                        'user multiple inactive',
                        'user active inactive',
                        'admin view',
                        'admin create',
                        'admin edit',
                        'admin delete',
                        'admin multiple delete',
                        'admin active inactive',
                        'admin multiple inactive',
                        'admin multiple active',
                    ]))
                <li class="m-menu__item  m-menu__item--submenu @if (
                    $routeName == 'user_index' ||
                        $routeName == 'user_create' ||
                        $routeName == 'user_edit' ||
                        $routeName == 'admin_index' ||
                        $routeName == 'admin_create' ||
                        $routeName == 'admin_edit' ||
                        $routeName == 'user_detail' ||
                        $routeName == 'user-role.index' ||
                        $routeName == 'user-role.create' ||
                        $routeName == 'user-role.edit') m-menu__item--active m-menu__item--open @endif"
                    aria-haspopup="true" m-menu-submenu-toggle="hover">
                    <a href="javascript:;" class="m-menu__link m-menu__toggle">
                        <i class="m-menu__link-icon fa fa-users"></i>
                        <span class="m-menu__link-text">{{ __('formname.users') }}</span>
                        <i class="m-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="m-menu__submenu ">
                        <span class="m-menu__arrow"></span>
                        <ul class="m-menu__subnav">
                            <!-- User Management -->
                            @if (
                                \Auth::guard('admin')->user()->hasAnyPermission([
                                        'user view',
                                        'user create',
                                        'user edit',
                                        'user delete',
                                        'user multiple delete',
                                        'user multiple active',
                                        'user multiple inactive',
                                        'user active inactive',
                                    ]))
                                <li class="m-menu__item @if (
                                    $routeName == 'user_index' ||
                                        $routeName == 'user_create' ||
                                        $routeName == 'user_edit' ||
                                        $routeName == 'user_detail') m-menu__item--active @endif "
                                    aria-haspopup="true">
                                    <a href="{{ route('user_index') }}" class="m-menu__link">
                                        <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                            <span></span>
                                        </i>
                                        <span class="m-menu__link-text">{{ __('formname.user_mngt') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif --}}
            <li class="m-menu__item  @if ($routeName == 'product.index' || $routeName == 'product.create' ||$routeName == 'product.edit') m-menu__item--active @endif"
            aria-haspopup="true">
                    <a href="{{ route('product.index') }}" class="m-menu__link ">
                        <i class="m-menu__link-icon flaticon-cart "></i>
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">{{ __('formname.product.label') }}</span>
                            </span>
                        </span>
                    </a>
                </li>

            {{-- <li class="m-menu__item  @if (
                $routeName == 'contact_us_index' ||
                    $routeName == 'contact_us_create' ||
                    $routeName == 'contact_us_edit' ||
                    $routeName == 'contact_us_detail') m-menu__item--active @endif"
                aria-haspopup="true">
                <a href="{{ route('contact_us_index') }}" class="m-menu__link ">
                    <i class="m-menu__link-icon fa fa-envelope"></i>
                    <span class="m-menu__link-title">
                        <span class="m-menu__link-wrap">
                            <span class="m-menu__link-text">{{ __('formname.customer_inquiry') }}</span>
                        </span>
                    </span>
                </a>
            </li> --}}
            {{-- @if (
                \Auth::guard('admin')->user()->hasAnyPermission([
                        'web setting view',
                        'web setting create',
                        'web setting edit',
                        'web setting delete',
                        'web setting multiple delete',
                    ]))
                <li class="m-menu__item  @if ($routeName == 'web_setting_index' || $routeName == 'web_setting_create' || $routeName == 'web_setting_edit') m-menu__item--active @endif"
                    aria-haspopup="true">
                    <a href="{{ route('web_setting_index') }}" class="m-menu__link ">
                        <i class="m-menu__link-icon fas fa-cog"></i>
                        <span class="m-menu__link-title">
                            <span class="m-menu__link-wrap">
                                <span class="m-menu__link-text">{{ __('formname.web_setting.name') }}</span>
                            </span>
                        </span>
                    </a>
                </li>
            @endif --}}
            <li class="m-menu__item  @if ($routeName == 'profile') m-menu__item--active @endif"
                aria-haspopup="true">
                <a href="{{ route('profile') }}" class="m-menu__link ">
                    <i class="m-menu__link-icon 
            flaticon-profile "></i>
                    <span class="m-menu__link-title">
                        <span class="m-menu__link-wrap">
                            <span class="m-menu__link-text">{{ __('formname.profile') }}</span>
                        </span>
                    </span>
                </a>
            </li>
            <li class="m-menu__item " aria-haspopup="true">
                <a href="{{ route('admin.logout') }}"
                    onclick="event.preventDefault(); document.getElementById('frm-side-logout').submit();"
                    class="m-menu__link">
                    <i class="m-menu__link-icon fa 	fa-sign-out-alt"></i>
                    <span class="m-menu__link-text">Logout</span>
                </a>
                <form id="frm-side-logout" action="{{ route('admin.logout') }}" method="post"
                    style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
    <!-- END: Aside Menu -->
</div>
<!-- END: Left Aside -->
