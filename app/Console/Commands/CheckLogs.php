<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckLogs extends Command
{
    protected $signature = 'check:logs';
    protected $description = 'Check recent Laravel logs';

    public function handle()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            $lines = file($logFile);
            $recentLines = array_slice($lines, -50); // Last 50 lines
            
            $this->info("=== RECENT LARAVEL LOGS ===");
            foreach ($recentLines as $line) {
                if (strpos($line, 'Voided sales') !== false || strpos($line, 'Sale ID') !== false) {
                    echo trim($line) . "\n";
                }
            }
        } else {
            $this->info("No log file found");
        }
        
        return 0;
    }
}
