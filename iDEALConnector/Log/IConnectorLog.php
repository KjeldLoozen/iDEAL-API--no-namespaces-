<?php
/**
 * Implement this interface to get access to log messages at transport level.
 */
interface iDEALConnector_Log_IConnectorLog
{
    /**
     * @param iDEALConnector_Exceptions_iDEALException $exception
     */
    public function logErrorResponse(iDEALConnector_Exceptions_iDEALException $exception);

    /**
     * @param iDEALConnector_Exceptions_ConnectorException $exception
     */
    public function logException(iDEALConnector_Exceptions_ConnectorException $exception);

    /**
     * @param string $method
     * @param iDEALConnector_Entities_AbstractRequest $request
     */
    public function logAPICall($method, iDEALConnector_Entities_AbstractRequest $request);

    /**
     * @param string $method
     * @param iDEALConnector_Entities_AbstractResponse $response
     */
    public function logAPIReturn($method, iDEALConnector_Entities_AbstractResponse $response);

    /**
     * @param string $xml
     */
    public function logRequest($xml);

    /**
     * @param string $xml
     */
    public function logResponse($xml);
}
