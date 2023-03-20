<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        // $this->call(JobStatusTableSeeder::class);
        // $this->call(CmsTableSeeder::class);
        // $this->call(EmailTemplatesTableSeeder::class);
        // $this->call(NotificationsTableSeeder::class);
        // $this->call(UserRoleMastersTableSeeder::class);
    }
}