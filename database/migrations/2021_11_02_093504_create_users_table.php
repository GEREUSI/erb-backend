<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->nullable(false)->unique();
            $table->string('email')->nullable(false)->unique();
            $table->string('password')->nullable(false);
            $table->string('typeId')->nullable(false);
            $table->timestamp('emailVerifiedAt')->nullable(true);
            $table->timestamp('createdAt');
            $table->timestamp('updatedAt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
