<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $roles = Config::get('role');
        foreach ($roles as $key => $value) {
            if ($value == 'superadmin') {
                $gaurd_name = 'admin';
            } else {
                $gaurd_name = 'web';
            }
            $role = Role::create(['name' => strtolower($value), 'guard_name' => $gaurd_name]);
        }
    }
}