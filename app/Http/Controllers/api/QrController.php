<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Interfaces\QrRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class QrController extends Controller
{
    protected QrRepositoryInterface $qrRepository;

    public function __construct(QrRepositoryInterface $qrRepository){
        $this->qrRepository = $qrRepository;
    }

    public function generate_qr_code(Request $request)
    {
        $content = $request->query('content');
        $qrCodeString = str_replace('<?xml version="1.0"?>', '', $this->qrRepository->generate_qr_code($content));
        $qrPdf = $this->qrRepository->generate_qr_pdf($qrCodeString);
        return new Response(
            $qrPdf,
            Response::HTTP_OK, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="qrcode.pdf"'
            ]
        );

    }
}
