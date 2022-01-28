<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
			$table->bigInteger('contact_number')->unsigned()->nullable();
			$table->integer('gender')->unsigned()->default(0);
			$table->string('address')->nullable();
			$table->string('city')->nullable();
			$table->integer('country')->unsigned()->default(0);
			$table->integer('nationality')->unsigned()->default(0);
			$table->string('profile_photo')->nullable();
			$table->bigInteger('department_id')->unsigned()->nullable();
			$table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
			$table->integer('is_budget_holder')->default(0)->unsigned();
			$table->integer('status')->unsigned()->default(0);
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
        Schema::dropIfExists('users');
    }
}
