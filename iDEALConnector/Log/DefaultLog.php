<?php
class iDEALConnector_Log_DefaultLog implements iDEALConnector_Log_IConnectorLog
{
    private $logPath;
    private $logLevel;

    public function __construct($logLevel, $logPath)
    {
        $this->logLevel = $logLevel;
        $this->logPath  = $logPath;
    }

    public function logAPICall($method, iDEALConnector_Entities_AbstractRequest $request)
    {
        if ($this->logLevel === 0)
        {
            $this->log('Entering['. $method .']', $request);
        }
    }

    public function logAPIReturn($method, iDEALConnector_Entities_AbstractResponse $response)
    {
        if ($this->logLevel === 0)
        {
            $this->log('Exiting['. $method .']', $response);
        }
    }

    public function logRequest($xml)
    {
        if ($this->logLevel === 0)
        {
            $this->log('Request', $xml);
        }
    }

    public function logResponse($xml)
    {
        if ($this->logLevel === 0)
        {
            $this->log('Response', $xml);
        }
    }

    public function logErrorResponse(iDEALConnector_Exceptions_iDEALException $exception)
    {
        $this->log('ErrorResponse', $exception);
    }

    public function logException(iDEALConnector_Exceptions_ConnectorException $exception)
    {
        $this->log('Exception', $exception);
    }

    /**
     * Log a message and value to a log file
     *
     * @param string $message
     * @param mixed $value
     * @return iDEALConnector_Log_DefaultLog
     */
    private function log($message, $value)
    {
        // Supress any direct logging, if no logPath defined
        if (empty($this->logPath))
        {
            return $this;
        }

        @file_put_contents($this->logPath, '['. date('Y-m-d H:i:s') .'] '. $message ."\n". serialize($value) ."\n\n", FILE_APPEND);

        return $this;
    }
}