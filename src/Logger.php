<?php

namespace Apaar\Gearman;

/**
 * Class Logger Asynchronous Logger using Gearman
 *
 * @package Apaar\Gearman
 */
class Logger
{
    /**
     * Gearman logger queue
     */
    const QUEUE = 'log';

    /**
     * Log levels
     */
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';

    /**
     * @var Logger
     */
    private static $instance;

    /**
     * @var \GearmanClient
     */
    private $client;

    /**
     * Returns instance of Logger (Singleton)
     *
     * @param string $host Hostname of Gearman server
     * @param int $port Port on which Gearman server is listening
     * @return Logger
     */
    public static function getInstance($host = 'localhost', $port = 4730)
    {
        if (null === self::$instance) {
            self::$instance = new Logger($host, $port);
        }
        return self::$instance;
    }

    /**
     * Logger constructor.
     * @param string $host Hostname
     * @param int $port Port number
     */
    private function __construct($host, $port)
    {
        $this->client = new \GearmanClient();
        $this->client->addServer($host, $port);
    }

    /**
     * Logs the given message with specified log level
     *
     * @param string $message
     * @param string $level
     */
    public function log($message, $level = self::LEVEL_DEBUG)
    {
        $this->client->doBackground(self::QUEUE, json_encode(array(
            'level' => $level,
            'message' => $message,
            'timestamp' => time(),
            'host' => gethostname(),
        )));
    }

    /**
     * Logs warnings
     *
     * @param string $message
     */
    public function warn($message)
    {
        $this->log($message, self::LEVEL_WARNING);
    }

    /**
     * Logs error message
     *
     * @param string $message
     */
    public function err($message)
    {
        $this->log($message, self::LEVEL_ERROR);
    }
}