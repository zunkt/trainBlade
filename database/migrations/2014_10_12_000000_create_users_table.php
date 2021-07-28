<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
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
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('is_admin')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        User::create([
            'email' => 'tiendang212@gmail.com',
            'password' => Hash::make('12345678'),
            'name' => 'tiendang',
        ]);

        User::create([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'name' => 'admin',
            'is_admin' => 1
        ]);
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
