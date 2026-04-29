<?php

namespace App\Interfaces;

interface QrRepositoryInterface{
    public function generate_qr_code(string $content): string;
    public function generate_qr_pdf(string $content): string;
}
