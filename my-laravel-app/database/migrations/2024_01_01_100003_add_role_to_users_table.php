<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->default(2)->after('id')->constrained('roles');
            $table->string('lietotajvards', 50)->unique()->nullable()->after('name');
            $table->text('bio')->nullable()->after('email');
            $table->string('avatar_url')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'lietotajvards', 'bio', 'avatar_url']);
        });
    }
};
