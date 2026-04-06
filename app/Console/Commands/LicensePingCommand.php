<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LicenseService;

class LicensePingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping LicenseHub API to keep license active and check status';

    /**
     * Execute the console command.
     */
    public function handle(LicenseService $licenseService)
    {
        $this->info('Pinging LicenseHub API...');

        $result = $licenseService->ping();

        if (!$result['success']) {
            $this->error("License ping failed: " . $result['status']);
            return self::FAILURE;
        }

        $this->info("License ping successful!");
        $this->line("Status: <fg=green>{$result['status']}</>");
        
        if (isset($result['license']['last_ping_at'])) {
            $this->line("Last Ping: " . $result['license']['last_ping_at']);
        }

        return self::SUCCESS;
    }
}
