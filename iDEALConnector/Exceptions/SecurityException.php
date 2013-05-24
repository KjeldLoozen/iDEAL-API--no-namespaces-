<?php
/**
 *  This exception occurs during security checks of transport messages.
 */
class iDEALConnector_Exceptions_SecurityException extends iDEALConnector_Exceptions_ConnectorException
{
    private $xml;

    public function getXml()
    {
        return $this->xml;
    }
}