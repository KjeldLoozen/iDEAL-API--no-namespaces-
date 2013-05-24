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

    private function log($message, $value)
    {
        $now = new DateTime();

        file_put_contents($this->logPath, $now->format('Y-m-d H:i:s') .' '. $message ."\n". serialize($value) ."\n\n", FILE_APPEND);
    }
}