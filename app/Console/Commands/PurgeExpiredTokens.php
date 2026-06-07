<?php

namespace App\Console\Commands;

use App\Models\SessionToken;
use Illuminate\Console\Command;

class PurgeExpiredTokens extends Command
{
    protected $signature = 'tokens:purge';
    protected $description = 'Elimina session_tokens expirados';

    public function handle(): void
    {
        $deleted = SessionToken::where('expiration', '<', time())->delete();
        $this->info("Tokens eliminados: {$deleted}");
    }
}
