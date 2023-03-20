<?php

// Home
Breadcrumbs::for('home', function ($trail) {
    $trail->push(trans('formname.home'), route('home'));
});

// Home > Dashboard
Breadcrumbs::for('dashboard', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('formname.dashboard'), route('admin_dashboard'));
});
// Home > Dashboard > User
Breadcrumbs::for('userlist', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('formname.user_list'), route('user_index'));
});
// Home > Dashboard > User >User Create
Breadcrumbs::for('usercreate', function ($trail) {
    $trail->parent('userlist');
    $trail->push(trans('formname.user_create'), route('user_index'));
});
// Home > Dashboard > User >User Update
Breadcrumbs::for('userupdate', function ($trail) {
    $trail->parent('userlist');
    $trail->push(trans('formname.user_update'), route('user_index'));
});

// Home > Dashboard > Role
Breadcrumbs::for('rolelist', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('formname.role_list'), route('role_index'));
});
// Home > Dashboard > Role >Role Create
Breadcrumbs::for('rolecreate', function ($trail) {
    $trail->parent('rolelist');
    $trail->push(trans('formname.role_create'), route('role_index'));
});
// Home > Dashboard > Role >Role Update
Breadcrumbs::for('roleupdate', function ($trail) {
    $trail->parent('rolelist');
    $trail->push(trans('formname.role_update'), route('role_index'));
});
// Home > Dashboard > Admin
Breadcrumbs::for('adminlist', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('formname.admin_list'), route('admin_index'));
});
// Home > Dashboard > Admin >Admin Create
Breadcrumbs::for('admincreate', function ($trail) {
    $trail->parent('adminlist');
    $trail->push(trans('formname.admin_create'), route('admin_index'));
});
// Home > Dashboard > Admin >User Update
Breadcrumbs::for('adminupdate', function ($trail) {
    $trail->parent('adminlist');
    $trail->push(trans('formname.admin_update'), route('admin_index'));
});
// Home > Dashboard > Permission
Breadcrumbs::for('permissionlist', function ($trail) {
    $trail->parent('dashboard');
    $trail->push(trans('formname.permissions_list'), route('permission_index'));
});
// Home > Dashboard > Permission >Permission Create
Breadcrumbs::for('permissioncreate', function ($trail) {
    $trail->parent('permissionlist');
    $trail->push(trans('formname.permission_create'), route('permission_index'));
});
// Home > Dashboard > Permission >Permission Update
Breadcrumbs::for('permissionupdate', function ($trail) {
    $trail->parent('permissionlist');
    $trail->push(trans('formname.permission_update'), route('permission_index'));
});