<?php
/**
 *  This exception occurs during the serialization of entities.
 */
class iDEALConnector_Exceptions_SerializationException extends iDEALConnector_Exceptions_ConnectorException
{
    private $xml;

    public function getXml()
    {
        return $this->xml;
    }
}