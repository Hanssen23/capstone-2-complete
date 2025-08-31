<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RfidReaderService;

class RfidReaderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rfid:reader {action : The action to perform (start|stop|restart|status)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Control the RFID reader process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $rfidService = new RfidReaderService();

        switch ($action) {
            case 'start':
                $this->startReader($rfidService);
                break;
            case 'stop':
                $this->stopReader($rfidService);
                break;
            case 'restart':
                $this->restartReader($rfidService);
                break;
            case 'status':
                $this->showStatus($rfidService);
                break;
            default:
                $this->error("Invalid action. Use: start, stop, restart, or status");
                return 1;
        }

        return 0;
    }

    private function startReader(RfidReaderService $rfidService)
    {
        $this->info('Starting RFID reader...');
        
        if ($rfidService->isRunning()) {
            $this->warn('RFID reader is already running.');
            return;
        }

        $success = $rfidService->start();
        
        if ($success) {
            $this->info('RFID reader started successfully!');
        } else {
            $this->error('Failed to start RFID reader.');
        }
    }

    private function stopReader(RfidReaderService $rfidService)
    {
        $this->info('Stopping RFID reader...');
        
        if (!$rfidService->isRunning()) {
            $this->warn('RFID reader is not running.');
            return;
        }

        $success = $rfidService->stop();
        
        if ($success) {
            $this->info('RFID reader stopped successfully!');
        } else {
            $this->error('Failed to stop RFID reader.');
        }
    }

    private function restartReader(RfidReaderService $rfidService)
    {
        $this->info('Restarting RFID reader...');
        
        $success = $rfidService->restart();
        
        if ($success) {
            $this->info('RFID reader restarted successfully!');
        } else {
            $this->error('Failed to restart RFID reader.');
        }
    }

    private function showStatus(RfidReaderService $rfidService)
    {
        $status = $rfidService->getStatus();
        
        $this->info('RFID Reader Status:');
        $this->line('Running: ' . ($status['running'] ? 'Yes' : 'No'));
        $this->line('Log File: ' . $status['log_file']);
        
        if (!empty($status['last_logs'])) {
            $this->info('Recent Logs:');
            foreach ($status['last_logs'] as $log) {
                $this->line($log);
            }
        } else {
            $this->warn('No recent logs available.');
        }
    }
}
