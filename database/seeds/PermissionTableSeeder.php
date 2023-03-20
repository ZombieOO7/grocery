<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions = Config::get('rolePermission');
        foreach ($permissions as $key => $value) {
            Permission::updateOrCreate([
                'name' => $value,
                'guard_name' => 'admin',
            ], [
                'name' => $value,
            ]);
        }
        $role = Role::find(1);
        $role->syncPermissions(Permission::all());
    }
}