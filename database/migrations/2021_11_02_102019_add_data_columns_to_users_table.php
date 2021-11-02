<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataColumnsToUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('birthdayDate')->nullable(true);
            $table->string('firstName')->nullable(true);
            $table->string('lastName')->nullable(true);

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('birthdayDate');
            $table->dropColumn('firstName');
            $table->dropColumn('lastName');
        });
    }
}
