<?php

use Illuminate\Database\Seeder;

class NotificationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('notifications')->delete();
        
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 1,
                'notification_name' => 'New Job Request',
                'status' => 1,
                'created_at' => '2020-02-25 09:32:29',
                'updated_at' => '2020-02-25 09:32:29',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'notification_name' => 'Job Completed',
                'status' => 1,
                'created_at' => '2020-02-25 09:32:52',
                'updated_at' => '2020-02-25 09:32:52',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'notification_name' => 'Accept Job Request',
                'status' => 1,
                'created_at' => '2020-02-26 11:47:17',
                'updated_at' => '2020-02-26 11:47:17',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 5,
                'notification_name' => 'Job Started',
                'status' => 1,
                'created_at' => '2020-02-26 12:28:54',
                'updated_at' => '2020-02-26 12:28:54',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 4,
                'notification_name' => 'Engineer Assigned',
                'status' => 1,
                'created_at' => '2020-02-26 12:14:19',
                'updated_at' => '2020-02-26 12:14:19',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'notification_name' => 'Reassign Engineer',
                'status' => 1,
                'created_at' => '2020-02-26 12:43:11',
                'updated_at' => '2020-02-26 12:43:11',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'notification_name' => 'Job request declined',
                'status' => 1,
                'created_at' => '2020-02-26 12:56:22',
                'updated_at' => '2020-02-26 12:56:22',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 9,
                'notification_name' => 'On Going',
                'status' => 1,
                'created_at' => '2020-02-26 13:33:35',
                'updated_at' => '2020-02-26 13:33:35',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 8,
                'notification_name' => 'KIV',
                'status' => 1,
                'created_at' => '2020-02-26 13:03:58',
                'updated_at' => '2020-02-26 13:03:58',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'notification_name' => 'Profile Updated',
                'status' => 1,
                'created_at' => '2020-02-26 13:03:58',
                'updated_at' => '2020-02-26 13:03:58',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'notification_name' => 'Job InCompleted',
                'status' => 1,
                'created_at' => '2020-06-03 06:33:01',
                'updated_at' => '2020-06-03 06:33:01',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}