<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class CronHelper
{
    /**
     * Attempt to register the Laravel Scheduler automatically based on OS.
     */
    public static function register(): bool
    {
        $phpPath = PHP_BINARY;
        $artisanPath = base_path('artisan');
        $command = "\"{$phpPath}\" \"{$artisanPath}\" schedule:run";

        if (PHP_OS_FAMILY === 'Windows') {
            return self::registerWindows($command);
        }

        return self::registerLinux($command);
    }

    /**
     * Register to Windows Task Scheduler (using schtasks)
     */
    protected static function registerWindows(string $command): bool
    {
        try {
            $taskName = '3FLO_Scheduler_' . substr(md5(base_path()), 0, 8);
            
            // Check if task exists first
            exec("schtasks /query /tn \"{$taskName}\" 2>nul", $output, $result);
            if ($result === 0) return true; // Already exists

            // Create task to run every minute and HIDDEN (/ru SYSTEM)
            $cmd = "schtasks /create /sc minute /mo 1 /tn \"{$taskName}\" /tr \"{$command}\" /ru SYSTEM /f";
            exec($cmd, $output, $result);

            return $result === 0;
        } catch (\Exception $e) {
            Log::error('CronHelper (Windows) Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Register to Linux Crontab
     */
    protected static function registerLinux(string $command): bool
    {
        try {
            $fullCommand = "* * * * * cd " . base_path() . " && " . $command . " >> /dev/null 2>&1";
            
            // Get current crontab
            exec('crontab -l 2>/dev/null', $existingCrontab, $result);
            $crontabContent = implode("\n", $existingCrontab);

            // Check if already in crontab
            if (str_contains($crontabContent, base_path())) {
                return true;
            }

            // Append new task
            $newCrontabContent = $crontabContent . ($crontabContent ? "\n" : "") . $fullCommand . "\n";
            $tmpFile = tempnam(sys_get_temp_dir(), 'cron');
            file_put_contents($tmpFile, $newCrontabContent);

            exec("crontab \"{$tmpFile}\"", $output, $result);
            unlink($tmpFile);

            return $result === 0;
        } catch (\Exception $e) {
            Log::error('CronHelper (Linux) Failed: ' . $e->getMessage());
            return false;
        }
    }
}
