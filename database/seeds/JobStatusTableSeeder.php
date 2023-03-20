<?php

use Illuminate\Database\Seeder;

class JobStatusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('job_status')->delete();

        \DB::table('job_status')->insert(array(
            0 => array(
                'id' => 1,
                'uuid' => '52435d08-6ee6-498b-875b-d5e64bf32de1',
                'title' => 'Job Request',
                'slug' => 'job_request',
                'created_at' => null,
                'updated_at' => null,
            ),
            1 => array(
                'id' => 2,
                'uuid' => '52435d08-6ee6-498b-875b-d5e64bf32de2',
                'title' => 'Assigned',
                'slug' => 'assigned',
                'created_at' => null,
                'updated_at' => null,
            ),
            2 => array(
                'id' => 3,
                'uuid' => '52435d08-6ee6-498b-875b-d5e64bf32de3',
                'title' => 'Work Order',
                'slug' => 'work_order',
                'created_at' => null,
                'updated_at' => null,
            ),
            3 => array(
                'id' => 4,
                'uuid' => '52435d08-6ee6-498b-875b-d5e64bf32de4',
                'title' => 'Completed',
                'slug' => 'completed',
                'created_at' => null,
                'updated_at' => null,
            ),
            4 => array(
                'id' => 5,
                'uuid' => '52435d08-6ee6-498b-875b-d5e64bf32de5',
                'title' => 'Declined',
                'slug' => 'declined',
                'created_at' => null,
                'updated_at' => null,
            ),
            5 => array(
                'id' => 6,
                'uuid' => '52435d08-6ee6-498b-875b-d5e64bf32de6',
                'title' => 'KIV',
                'slug' => 'kiv',
                'created_at' => null,
                'updated_at' => null,
            ),
            6 => array(
                'id' => 7,
                'uuid' => '52435d08-6ee6-498b-875b-d5e64bf32de7',
                'title' => 'Unable To Complete',
                'slug' => 'unable_to_complete',
                'created_at' => null,
                'updated_at' => null,
            ),
        ));

    }
}
