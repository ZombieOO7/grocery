<?php

use Illuminate\Database\Seeder;

class EmailTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('email_templates')->delete();

        \DB::table('email_templates')->insert(array(
            0 => array(
                'uuid' => '913c5ceede406d31a970c79a07fea8ca',
                'title' => 'Profile Under Review',
                'slug' => 'profile-under-review',
                'subject' => 'Profile Under Review',
                'body' => '<p>Profile Under Review</p>',
                'status' => 1,
                'created_at' => '2020-02-25 13:17:05',
                'updated_at' => '2020-02-26 11:49:49',
                'deleted_at' => null,
            ),
            1 => array(
                'uuid' => 'b7ee0552-cf1e-46fc-9fc2-a39ae7a0c260',
                'title' => 'Update Profile',
                'slug' => 'update-profile',
                'subject' => 'Update Profile',
                'body' => '<p>Hello [USER FULL NAME],<br />
your profile has been updated by admin.</p>',
                'status' => 1,
                'created_at' => '2020-02-24 11:02:18',
                'updated_at' => '2020-02-24 11:25:38',
                'deleted_at' => null,
            ),
            2 => array(
                'uuid' => 'd2e9bcdf-3612-4283-a64f-490546d5a5f1',
                'title' => 'Delete Profile',
                'slug' => 'delete-profile',
                'subject' => 'Delete Profile',
                'body' => '<p>Hello [USER FULL NAME],<br />
Sorry to inform you but your profile has been deleted by admin.</p>',
                'status' => 1,
                'created_at' => '2020-02-24 11:10:17',
                'updated_at' => '2020-02-24 11:36:50',
                'deleted_at' => null,
            ),
            3 => array(
                'uuid' => 'ac1a5155757696de2218e244eafa2cbf',
                'title' => 'Verify your email',
                'slug' => 'verify-your-email',
                'subject' => 'Verify your email',
                'body' => null,
                'status' => 1,
                'created_at' => '2020-02-25 13:17:05',
                'updated_at' => '2020-02-25 13:17:05',
                'deleted_at' => null,
            ),
            4 => array(
                'uuid' => 'd41d8cd98f00b204e9800998ecf8427e',
                'title' => 'Forgot Password',
                'slug' => 'forgot-password',
                'subject' => 'Forgor Password',
                'body' => '<br />
                Use following 
                Password : [content]<br />
                <br />
                Regards,<br />
                Team IPPS Engineering</p>',
                'status' => 1,
                'created_at' => '2020-02-25 13:17:05',
                'updated_at' => '2020-02-25 13:17:05',
                'deleted_at' => null,
            ),
        ));

    }
}
