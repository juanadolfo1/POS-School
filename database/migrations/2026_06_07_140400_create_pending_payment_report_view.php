<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW pending_payment_report AS
            SELECT
                cal.label AS academic_level,
                g.label AS \"group\",
                CONCAT_WS(' ', p.name, p.first_lastname, p.second_lastname) AS student_name,
                p.email,
                tutor_p.email AS tutor_email,
                last_pay.label AS last_payed_label,
                last_pay.paid_at
            FROM students s
            JOIN people p ON p.id = s.person_id
            JOIN student_groups sg ON sg.student_id = s.id AND sg.status = 1
            JOIN groups g ON g.id = sg.group_id
            JOIN cat_academic_levels cal ON cal.id = g.academic_level_id
            LEFT JOIN LATERAL (
                SELECT st2.person_id
                FROM student_tutors st2
                WHERE st2.student_id = s.id AND st2.status = 1
                LIMIT 1
            ) tutor_rel ON true
            LEFT JOIN people tutor_p ON tutor_p.id = tutor_rel.person_id
            LEFT JOIN LATERAL (
                SELECT cpc.label, pay.paid_at
                FROM payments pay
                JOIN ticket_products tp ON tp.id = pay.ticket_product_id
                JOIN cat_pay_concepts cpc ON cpc.id = tp.pay_concept_id
                JOIN tickets t ON t.id = tp.ticket_id
                JOIN student_groups sg2 ON sg2.id = t.student_group_id
                WHERE sg2.student_id = s.id
                  AND pay.deleted_at IS NULL
                  AND (t.is_cancelled IS NULL OR t.is_cancelled = false)
                ORDER BY pay.paid_at DESC
                LIMIT 1
            ) last_pay ON true
            WHERE s.status = 1
            ORDER BY cal.label, g.label, student_name
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS pending_payment_report");
    }
};
