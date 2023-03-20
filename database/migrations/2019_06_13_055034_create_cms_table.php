<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('page_title', '255')->nullable();
            $table->string('page_slug', '255')->nullable();
            $table->text('api_page_slug')->nullable();
            $table->text('page_content', '255')->nullable();
            $table->string('meta_title', '255')->nullable();
            $table->string('meta_keyword', '255')->nullable();
            $table->text('meta_description', '255')->nullable();
            $table->string('meta_robots', '255')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->tinyInteger('status')->default('1')->comment = '0 Inactive/1 Active';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('CMS');
    }
}