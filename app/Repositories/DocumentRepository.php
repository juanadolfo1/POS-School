<?php

namespace App\Repositories;

use App\Models\Scholarship;
use App\Models\StudentGroup;
use App\Models\Ticket;
use App\Models\TicketProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as DBCollection;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class DocumentRepository
{
    public function get_ticket(string $ticketFolio)
    {
        $ticket = Ticket::where('folio_ticket', $ticketFolio)->first();
        $ticket->pay_concetps = TicketProduct::join('cat_pay_concepts AS cpc', 'cpc.id', '=', 'ticket_products.pay_concept_id')
            ->select(
                'cpc.id AS pay_concept_id',
                'cpc.label AS pay_concept_name',
                'cpc.pay_concept_type AS pay_concept_type',
                'cpc.amount AS amount',
                'ticket_products.quantity'
            )->where('ticket_products.ticket_id', $ticket->id)
            ->get();
        $ticket->student = StudentGroup::join('groups AS gr', 'gr.id', '=', 'student_groups.group_id')
            ->join('cat_academic_levels AS cal', 'cal.id', '=', 'gr.academic_level_id')
            ->join('students AS st', 'st.id', '=', 'student_groups.student_id')
            ->join('people AS p', 'p.id', '=', 'st.person_id')
            ->where('student_groups.id', $ticket->student_group_id)
            ->select(
                'p.name',
                'p.first_lastname',
                'p.second_lastname',
                'cal.label AS academic_level',
                'gr.label AS group',
                'st.id AS student_id',
                'gr.scholar_year_id',
            )
            ->first();

        $ticket->scholarship = Scholarship::where([
            ['student_id', '=', $ticket->student->student_id],
            ['scholar_year_id', '=', $ticket->student->scholar_year_id],
        ])->first();

        $ancho = 80;
        $altoLinea = 12;     // altura aprox. por cada ítem (en mm)
        $altoCabecera = 60;  // cabecera fija en mm
        $altoTotales = 60;   // totales y footer
        $alto = $altoCabecera + (($ticket->pay_concetps->count() * (2 * $altoLinea - ($ticket->pay_concetps->count() - 1))) / 2) + $altoTotales;

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [$ancho, $alto],
            'margin_left' => 2,
            'margin_right' => 2,
            'margin_top' => 2,
            'margin_bottom' => 2,
        ]);

        $ticketCliente = true;

        $htmlCliente = view('ticket', compact('ticket', 'alto', 'ticketCliente'))->render();

        $mpdf->WriteHTML($htmlCliente);

        $ticketCliente = false;

        $htmlAuditoria = view('ticket', compact('ticket', 'alto', 'ticketCliente'))->render();

        $mpdf->AddPage(orientation: 'P');

        $mpdf->WriteHTML($htmlAuditoria);

        return $mpdf->Output($ticketFolio . '.pdf', 'S');

    }

    public function close_ticket(Collection $ticket_products, string $selectedDay): string
    {
        $today = date('d/m/Y ');
        $selectedDay = date('d/m/Y', strtotime($selectedDay));
        $html = view('closing', compact('ticket_products', 'today', 'selectedDay'))->render();
        return $html;
    }

    public function get_pdf($html, $format)
    {

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => $format,
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('closing.pdf', 'S');
    }

    public function get_pending_payment_report()
    {
        return DB::query()
            ->from('pending_payment_report')
            ->select('academic_level', 'group', 'student_name', 'email', 'tutor_email', 'last_payed_label', 'paid_at')
            ->get();
    }

    public function generate_csv_report(DBCollection $pending_payment_report)
    {
        $maxSize = 1024 * 1024 * 10;
        $file = fopen('php://temp/maxmemory:' . $maxSize, 'w');

        $headers = [
            'Nivel academico',
            'Grupo',
            'Nombre de estudiante',
            'Correo electronico',
            'Correo electronico de tutor',
            'Ultimo pago realizado',
            'Se pago'
        ];
        fputcsv($file, $headers);
        foreach ($pending_payment_report as $pending_payment) {
            fputcsv($file, [
                $pending_payment->academic_level,
                $pending_payment->group,
                $pending_payment->student_name,
                $pending_payment->email,
                $pending_payment->tutor_email,
                $pending_payment->last_payed_label,
                $pending_payment->paid_at,
            ]);
        }

        rewind($file);
        $csv = stream_get_contents($file);
        fclose($file);

        return $csv;
    }
}
