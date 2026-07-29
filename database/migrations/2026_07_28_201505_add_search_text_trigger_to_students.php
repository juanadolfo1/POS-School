<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Trigger function: concatena name + lastnames de people
        DB::statement("
            CREATE OR REPLACE FUNCTION update_student_search_text()
            RETURNS TRIGGER AS \$\$
            BEGIN
                SELECT to_tsvector('simple',
                    lower(coalesce(p.name, '')) || ' ' ||
                    lower(coalesce(p.first_lastname, '')) || ' ' ||
                    lower(coalesce(p.second_lastname, ''))
                )
                INTO NEW.search_text
                FROM people p
                WHERE p.id = NEW.person_id;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE OR REPLACE TRIGGER trg_student_search_text
            BEFORE INSERT OR UPDATE ON students
            FOR EACH ROW EXECUTE FUNCTION update_student_search_text();
        ");

        // Poblar registros existentes
        DB::statement("
            UPDATE students s
            SET search_text = to_tsvector('simple',
                lower(coalesce(p.name, '')) || ' ' ||
                lower(coalesce(p.first_lastname, '')) || ' ' ||
                lower(coalesce(p.second_lastname, ''))
            )
            FROM people p
            WHERE p.id = s.person_id;
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS trg_student_search_text ON students");
        DB::statement("DROP FUNCTION IF EXISTS update_student_search_text");
    }
};
