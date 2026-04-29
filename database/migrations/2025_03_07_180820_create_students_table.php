<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
            $table->integer('status');
            $table->char('gender', length: 1);
            $table->date('birthday');
            $table->string('curp', length: 18);
            $table->string('uuid', length: 36);
            $table->unsignedBigInteger('person_id');

            $table->foreign('person_id')
                ->references('id')
                ->on('people');
        });
        DB::statement('ALTER TABLE students ADD COLUMN search_text TSVECTOR');
        DB::statement('CREATE INDEX idx_search_text ON students (search_text)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
