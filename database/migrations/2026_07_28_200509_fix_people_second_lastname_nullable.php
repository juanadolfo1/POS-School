<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop view that depends on second_lastname
        DB::statement('DROP VIEW IF EXISTS pending_payment_report');

        // Alter column
        DB::statement('ALTER TABLE people ALTER COLUMN second_lastname DROP NOT NULL');

        // Recreate view (same definition as original migration)
        DB::statement("
            CREATE VIEW pending_payment_report AS
            SELECT
                cal.label AS academic_level,
                g.label AS \"group\",
                CONCAT_WS(' ', p.name, p.first_lastname, p.second_lastname) AS student_name,
                p.email,
                tp_person.email AS tutor_email,
                cpc.label AS last_payed_label,
                pay.paid_at
            FROM students s
            JOIN people p ON p.id = s.person_id
            JOIN student_groups sg ON sg.student_id = s.id AND sg.status = 1
            JOIN groups g ON g.id = sg.group_id
            JOIN cat_academic_levels cal ON cal.id = g.academic_level_id
            LEFT JOIN student_tutors st ON st.student_id = s.id AND st.status = 1
            LEFT JOIN people tp_person ON tp_person.id = st.person_id
            LEFT JOIN (
                SELECT DISTINCT ON (tp2.pay_concept_id, sg2.student_id)
                    tp2.pay_concept_id,
                    sg2.student_id,
                    pay2.paid_at
                FROM payments pay2
                JOIN ticket_products tp2 ON tp2.id = pay2.ticket_product_id
                JOIN tickets t2 ON t2.id = tp2.ticket_id AND t2.is_cancelled = false
                JOIN student_groups sg2 ON sg2.id = t2.student_group_id
                WHERE pay2.deleted_at IS NULL
                ORDER BY tp2.pay_concept_id, sg2.student_id, pay2.paid_at DESC
            ) last_pay ON last_pay.student_id = s.id
            LEFT JOIN cat_pay_concepts cpc ON cpc.id = last_pay.pay_concept_id
            LEFT JOIN payments pay ON pay.ticket_product_id IN (
                SELECT tp3.id FROM ticket_products tp3
                JOIN tickets t3 ON t3.id = tp3.ticket_id AND t3.is_cancelled = false
                JOIN student_groups sg3 ON sg3.id = t3.student_group_id
                WHERE sg3.student_id = s.id AND tp3.pay_concept_id = cpc.id
            ) AND pay.paid_at = last_pay.paid_at AND pay.deleted_at IS NULL
            WHERE s.status = 1
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS pending_payment_report');
        DB::statement('ALTER TABLE people ALTER COLUMN second_lastname SET NOT NULL');
        // Recreate view same as up
    }
};
