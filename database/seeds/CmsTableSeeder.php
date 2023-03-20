<?php

use Illuminate\Database\Seeder;

class CmsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('cms')->delete();

        \DB::table('cms')->insert(array(
            0 => array(
                'page_title' => 'Terms & Conditions',
                'page_slug' => 'terms-conditions',
                'api_page_slug' => null,
                'page_content' => '<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>',
                'meta_title' => null,
                'meta_keyword' => 'Lorem Ipsum',
                'meta_description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
                'meta_robots' => null,
                'created_by' => null,
                'updated_by' => 1,
                'status' => 1,
                'created_at' => '2020-02-24 08:59:12',
                'updated_at' => '2020-02-24 08:59:12',
                'uuid' => '0d904ecd-6ac2-4b06-b33d-9610744dbcb1',
                'deleted_at' => null,
            ),
            1 => array(
                'page_title' => 'About us',
                'page_slug' => 'about-us',
                'api_page_slug' => null,
                'page_content' => '<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s</p>',
                'meta_title' => null,
                'meta_keyword' => null,
                'meta_description' => null,
                'meta_robots' => null,
                'created_by' => null,
                'updated_by' => 1,
                'status' => 1,
                'created_at' => '2020-02-24 08:59:46',
                'updated_at' => '2020-02-24 08:59:46',
                'uuid' => 'c3ce9053-7ad1-4095-8225-3f20de6d9877',
                'deleted_at' => null,
            ),
            2 => array(
                'page_title' => 'Privacy Policy',
                'page_slug' => 'privacy-policy',
                'api_page_slug' => null,
                'page_content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas fermentum nunc mi, in accumsan nulla maximus a. Morbi eleifend sem dolor, eu mattis orci molestie non. Pellentesque tristique ligula tortor. Suspendisse potenti. Pellentesque luctus enim at convallis mattis. Nunc euismod sagittis odio sed fringilla. Morbi non diam ut dui posuere vehicula. Vivamus lorem nunc, lobortis quis ultrices vel, aliquam scelerisque justo. Vivamus sapien nunc, dapibus non porta ac, accumsan non enim. Nulla eget dui maximus diam semper gravida sed nec lectus. Praesent consequat massa at massa auctor, ut fermentum libero tempor.&nbsp;</p>',
                'meta_title' => null,
                'meta_keyword' => null,
                'meta_description' => null,
                'meta_robots' => null,
                'created_by' => null,
                'updated_by' => 1,
                'status' => 1,
                'created_at' => '2020-02-25 13:15:20',
                'updated_at' => '2020-02-26 07:04:15',
                'uuid' => '74f746b6-42d7-4a5f-a550-d04443b72f61',
                'deleted_at' => null,
            ),
        ));

    }
}
