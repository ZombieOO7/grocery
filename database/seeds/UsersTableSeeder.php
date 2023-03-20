<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /** Super admin */
        $superadmin = [
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@yopmail.com',
            'password' => 'testadmin',
            'status' => 1,
        ];
        $superadmin = App\Models\Admin::create($superadmin);
        $role = $superadmin->assignRole('superadmin');
        $superadmin->givePermissionTo(Permission::all());

        /*
         * Frontend
         */
        $admin = [
            'first_name' => 'Carol',
            'last_name' => 'Crowley',
            'email' => 'webclues.user@gmail.com',
            'password' => Hash::make('testadmin'),
            'status' => 1,
        ];
        $admin = App\Models\User::create($admin);
        // Assign role to  Frontend
        $role = $admin->assignRole('frontend');
    }
}