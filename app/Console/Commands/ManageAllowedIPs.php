<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class ManageAllowedIPs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ips:manage {action : Action to perform (list|add|remove|clear)} {ip? : IP address to add or remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage allowed IPs for public access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $ip = $this->argument('ip');

        switch ($action) {
            case 'list':
                $this->listIPs();
                break;
            case 'add':
                if (!$ip) {
                    $this->error('IP address is required for add action');
                    return 1;
                }
                $this->addIP($ip);
                break;
            case 'remove':
                if (!$ip) {
                    $this->error('IP address is required for remove action');
                    return 1;
                }
                $this->removeIP($ip);
                break;
            case 'clear':
                $this->clearIPs();
                break;
            default:
                $this->error('Invalid action. Use: list, add, remove, or clear');
                return 1;
        }

        return 0;
    }

    private function listIPs()
    {
        $allowedIPs = config('app.allowed_ips', []);
        
        if (empty($allowedIPs)) {
            $this->info('No allowed IPs configured.');
            return;
        }

        $this->info('Currently allowed IPs:');
        foreach ($allowedIPs as $index => $ip) {
            $this->line(($index + 1) . '. ' . $ip);
        }
    }

    private function addIP($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->error('Invalid IP address format');
            return;
        }

        $allowedIPs = config('app.allowed_ips', []);
        
        if (in_array($ip, $allowedIPs)) {
            $this->warn('IP ' . $ip . ' is already in the allowed list');
            return;
        }

        $allowedIPs[] = $ip;
        $this->updateConfig($allowedIPs);
        $this->info('IP ' . $ip . ' added successfully');
    }

    private function removeIP($ip)
    {
        $allowedIPs = config('app.allowed_ips', []);
        
        if (!in_array($ip, $allowedIPs)) {
            $this->warn('IP ' . $ip . ' is not in the allowed list');
            return;
        }

        $allowedIPs = array_values(array_filter($allowedIPs, function($allowedIP) use ($ip) {
            return $allowedIP !== $ip;
        }));

        $this->updateConfig($allowedIPs);
        $this->info('IP ' . $ip . ' removed successfully');
    }

    private function clearIPs()
    {
        $this->updateConfig([]);
        $this->info('All allowed IPs cleared');
    }

    private function updateConfig($allowedIPs)
    {
        // Note: This is a simplified approach. In production, you might want to
        // use environment variables or a database table for dynamic IP management
        $this->warn('Note: To persist changes, you need to manually update config/app.php or use environment variables');
        $this->info('Current allowed IPs: ' . implode(', ', $allowedIPs));
    }
} 