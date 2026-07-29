<?php

namespace App\Repositories;

use App\Interfaces\QrRepositoryInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Mpdf\Mpdf;

class QrRepository implements QrRepositoryInterface
{
    public function generate_qr_code(string $content): string
    {
        $writer = new PngWriter();
        $svgWriter = new SvgWriter();
        $qrCode = new QrCode(
            data: $content,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 500,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
        return $svgWriter->write($qrCode)->getString();
    }

    public function generate_qr_pdf(string $content): string
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [50, 25],
            'margin_left' => 13,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'tempDir' => env('MPDF_TEMP_DIR', storage_path('app/mpdf')),
        ]);

        $mpdf->WriteHTML($content);
        return $mpdf->Output('', 'S');
    }
}
