<?php
namespace LiebigZs\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * This class implements the PSR3 logger interface.
 *
 * @package LiebigZs\Logger
 */
class SimpleLogger extends AbstractLogger
{
    /**
     * @var string Absolute path to log file.
     */
    private $logFilePath;

    /**
     * @var int Lowest log level which should be logged.
     */
    private $logLevelThreshold;

    /**
     * @var bool Dump the log line to the output too.
     */
    private $echo;

    /**
     * @var resource
     */
    private $fileHandle;

    /**
     * @var array Log level definitions.
     */
    private $logLevels = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT     => 1,
        LogLevel::CRITICAL  => 2,
        LogLevel::ERROR     => 3,
        LogLevel::WARNING   => 4,
        LogLevel::NOTICE    => 5,
        LogLevel::INFO      => 6,
        LogLevel::DEBUG     => 7
    ];

    public function __construct(string $logFilePath, string $logLevelThreshold = LogLevel::DEBUG, bool $echo = false)
    {
        $this->logFilePath = $logFilePath;
        $this->logLevelThreshold = $logLevelThreshold;
        $this->echo = $echo;

        if (\file_exists($this->logFilePath) && !\is_writable($this->logFilePath)) {
            throw new \RuntimeException('Unable to write to log file.');
        }

        $this->fileHandle = \fopen($this->logFilePath, 'a');
    }

    public function log($level, $message, array $context = [])
    {
        /**
         * Level is under the threshold, nothing to do.
         */
        if ($this->logLevels[$level] > $this->logLevels[$this->logLevelThreshold]) {
            return;
        }

        $message = \date(\DateTime::ISO8601) . ' [' . \strtoupper($level) . '] ' . $message;

        if ($context) {
            $message .= ' ' . \json_encode($context,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }

        $this->write($message);
    }

    private function write($message)
    {
        if (\fwrite($this->fileHandle, $message . PHP_EOL) === false) {
            throw new \RuntimeException('Unable to write to log file.');
        }

        if ($this->echo !== false) {
            if (\php_sapi_name() === 'cli') {
                \fwrite(STDOUT, $message . PHP_EOL);
            } else {
                echo '<pre style="margin:0">' . $message . '</pre>';
            }
        }
    }

    public function __destruct()
    {
        if ($this->fileHandle) {
            \fclose($this->fileHandle);
        }
    }
}