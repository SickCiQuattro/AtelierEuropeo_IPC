<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tiene la candidatura più vecchia per coppia utente/progetto
        DB::table('applications')
            ->whereNotIn('id', DB::table('applications')->selectRaw('MIN(id)')->groupBy('user_id', 'project_id'))
            ->delete();

        Schema::table('applications', function (Blueprint $table) {
            $table->unique(['user_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'project_id']);
        });
    }
};
