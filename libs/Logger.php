<?php

class Logger {
    private $logDirectory;

    public function __construct($logDirectory = 'logs') {
        $this->logDirectory = $logDirectory;
        if (!file_exists($this->logDirectory)) {
            mkdir($this->logDirectory, 0777, true);
        }
    }

    public function log($message, $level = 'INFO', $file = __FILE__) {
        $filename = $this->logDirectory . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
        $logEntry = date('Y-m-d H:i:s') . " [{$level}] " . basename($file) . " - " . $message . PHP_EOL;
        file_put_contents($filename, $logEntry, FILE_APPEND);
    }
}

?>
