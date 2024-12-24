<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

class AlterPasswordColumnToHash extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            // Modify the column to store hashed passwords (length is 255)
            $table->string('password', 255)->change();
        });

        // Optionally hash existing passwords if they are stored as raw text
        $users = \DB::table('user')->get();
        foreach ($users as $user) {
            \DB::table('user')
                ->where('id', $user->id)
                ->update(['password' => Hash::make($user->password)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            // Revert the column to its original state (length is 45)
            $table->string('password', 45)->change();
        });
    }
}
