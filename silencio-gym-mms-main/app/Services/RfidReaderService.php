<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class RfidReaderService
{
    private $process = null;
    private $isRunning = false;
    private $logFile;

    public function __construct()
    {
        $this->logFile = storage_path('logs/rfid_reader.log');
    }

    /**
     * Start the RFID reader process
     */
    public function start()
    {
        if ($this->isRunning) {
            Log::info('RFID reader is already running');
            return false;
        }

        try {
            $pythonPath = $this->getPythonPath();
            $scriptPath = base_path('rfid_reader.py');
            $apiUrl = config('app.url');

            if (!file_exists($scriptPath)) {
                Log::error('RFID reader script not found at: ' . $scriptPath);
                return false;
            }

            // Create log directory if it doesn't exist
            if (!file_exists(dirname($this->logFile))) {
                mkdir(dirname($this->logFile), 0755, true);
            }

            // Build the command
            $command = sprintf(
                '%s "%s" --api %s >> "%s" 2>&1',
                $pythonPath,
                $scriptPath,
                $apiUrl,
                $this->logFile
            );

            // Start the process
            $this->process = Process::run($command, function ($type, $output) {
                if ($type === Process::ERR) {
                    Log::error('RFID Reader Error: ' . $output);
                } else {
                    Log::info('RFID Reader Output: ' . $output);
                }
            });

            $this->isRunning = true;
            Log::info('RFID reader started successfully');
            
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to start RFID reader: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Stop the RFID reader process
     */
    public function stop()
    {
        if (!$this->isRunning) {
            Log::info('RFID reader is not running');
            return false;
        }

        try {
            if ($this->process) {
                $this->process->terminate();
                $this->process = null;
            }

            // Kill any remaining Python processes running the RFID script
            $this->killRfidProcesses();

            $this->isRunning = false;
            Log::info('RFID reader stopped successfully');
            
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to stop RFID reader: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if the RFID reader is running
     */
    public function isRunning()
    {
        return $this->isRunning && $this->process && $this->process->running();
    }

    /**
     * Get the status of the RFID reader
     */
    public function getStatus()
    {
        return [
            'running' => $this->isRunning(),
            'log_file' => $this->logFile,
            'last_logs' => $this->getLastLogs(),
        ];
    }

    /**
     * Get the last few lines from the log file
     */
    public function getLastLogs($lines = 10)
    {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $logs = file($this->logFile);
        return array_slice($logs, -$lines);
    }

    /**
     * Get Python executable path
     */
    private function getPythonPath()
    {
        // Try common Python paths
        $pythonPaths = [
            'C:\Users\hanss\AppData\Local\Programs\Python\Python313\python.exe',
            'python',
            'python3',
            'py',
        ];

        foreach ($pythonPaths as $path) {
            if ($this->isPythonExecutable($path)) {
                return $path;
            }
        }

        throw new \Exception('Python executable not found');
    }

    /**
     * Check if a Python executable exists and works
     */
    private function isPythonExecutable($path)
    {
        try {
            $result = Process::run($path . ' --version');
            return $result->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Kill any remaining RFID reader processes
     */
    private function killRfidProcesses()
    {
        try {
            // On Windows, find and kill Python processes running rfid_reader.py
            $command = 'tasklist /FI "IMAGENAME eq python.exe" /FO CSV';
            $result = Process::run($command);
            
            if ($result->successful()) {
                $output = $result->output();
                $lines = explode("\n", $output);
                
                foreach ($lines as $line) {
                    if (strpos($line, 'rfid_reader.py') !== false) {
                        // Extract PID and kill the process
                        $parts = str_getcsv($line);
                        if (isset($parts[1])) {
                            $pid = trim($parts[1]);
                            Process::run("taskkill /PID $pid /F");
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not kill RFID processes: ' . $e->getMessage());
        }
    }

    /**
     * Restart the RFID reader
     */
    public function restart()
    {
        $this->stop();
        sleep(2); // Wait a bit before restarting
        return $this->start();
    }
}
