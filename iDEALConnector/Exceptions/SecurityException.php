<?php
/**
 *  This exception occurs during security checks of transport messages.
 */
class iDEALConnector_Exceptions_SecurityException extends iDEALConnector_Exceptions_ConnectorException
{
    private $xml = null;

    /**
     * Current class constructor
     *
     * @param string    $message           Message, which explains raised error
     * @param integer   $code              Exception code
     * @param Exception $previousException Previously raised, but catched exception
     * @param mixed     $xml               XML response (if any)
     */
    public function __construct($message, $code = 0, Exception $previousException = null, $xml = null)
    {
        parent::__construct($message, $code, $previousException);

        if ($xml !== null)
        {
            $this->xml = $xml;
        }
    }

    /**
     * Return possible XML response. If no XML response passed to constructor, NULL will be returned
     *
     * @return mixed
     */
    public function getXml()
    {
        return $this->xml;
    }
}