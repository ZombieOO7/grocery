<?php

use Illuminate\Database\Seeder;

class UserRoleMastersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_role_masters')->delete();
        
        \DB::table('user_role_masters')->insert(array (
            0 => 
            array (
                'id' => 8,
                'uuid' => '36eaa5b3-6aa5-4c86-9211-c927c4d4197b',
                'user_type' => 3,
                'name' => 'Staff',
                'slug' => 'staff',
                'status' => 1,
                'created_at' => '2020-03-19 07:35:28',
                'updated_at' => '2020-03-19 07:43:09',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 1,
                'uuid' => '8ede8786-2018-4637-a939-390607b74b25',
                'user_type' => 1,
                'name' => 'Foreman Manager',
                'slug' => 'foreman-manager',
                'status' => 1,
                'created_at' => '2020-03-19 06:22:15',
                'updated_at' => '2020-03-19 07:55:22',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 2,
                'uuid' => 'd824a799-7941-4a4e-b3dc-6eda90f67c56',
                'user_type' => 2,
                'name' => 'Foreman',
                'slug' => 'foreman',
                'status' => 1,
                'created_at' => '2020-03-19 07:35:28',
                'updated_at' => '2020-03-19 07:55:26',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 3,
                'uuid' => '618fbf25-f21b-4fe1-b147-151ff079137f',
                'user_type' => 2,
                'name' => 'Contractors',
                'slug' => 'contractors',
                'status' => 1,
                'created_at' => '2020-03-19 07:35:28',
                'updated_at' => '2020-03-19 07:55:27',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 4,
                'uuid' => '50416a89-0fd5-4be3-b5f7-041d919fcb0d',
                'user_type' => 2,
                'name' => 'Job Fitters',
                'slug' => 'job-fitters',
                'status' => 1,
                'created_at' => '2020-03-19 07:35:28',
                'updated_at' => '2020-03-19 07:55:29',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 5,
                'uuid' => '1e77eb2b-f946-43da-9c44-1e9379e91125',
                'user_type' => 2,
                'name' => 'Apprentice',
                'slug' => 'apprentice',
                'status' => 1,
                'created_at' => '2020-03-19 07:35:28',
                'updated_at' => '2020-03-19 07:55:31',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 6,
                'uuid' => 'ba64f60f-e8df-40fc-be09-b0ef294cd3c3',
                'user_type' => 3,
                'name' => 'Supervisor',
                'slug' => 'supervisor',
                'status' => 1,
                'created_at' => '2020-03-19 07:35:28',
                'updated_at' => '2020-03-19 07:55:33',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 7,
                'uuid' => '5e7d4488-814f-4a55-831d-9051d1556517',
                'user_type' => 3,
                'name' => 'Workshop Fitter',
                'slug' => 'workshop-fitter',
                'status' => 1,
                'created_at' => '2020-03-19 07:35:28',
                'updated_at' => '2020-03-19 07:55:35',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}