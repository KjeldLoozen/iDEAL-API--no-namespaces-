<?php
/**
 * iDEALConnector Library v2.0
 *
 * Port to PHP 5.2, by ttll
 */
class iDEALConnector
{
    private $serializer;
    private $signer;
    private $validator;
    private $configuration;
    private $log;
    private $merchant;

    /**
     * Constructs an instance of iDEALConnector.
     *
     * @param mixed $configuration  An instance of a implementation of IConnectorConfiguration or a string with a path
     * @param mixed $log            An instance of a implementation of IConnectorLog
     */
    public function __construct($configuration, iDEALConnector_Log_IConnectorLog $log = null)
    {
        if (is_object($configuration) === false || !($configuration instanceof iDEALConnector_Configuration_IConnectorConfiguration))
        {
            if (is_string($configuration) === false || $configuration === '' || file_exists($configuration) === false)
            {
                throw new iDEALConnector_Exceptions_iDEALException('Wrong value passed as configuration data');
            }

            $configuration = new iDEALConnector_Configuration_DefaultConfiguration($configuration);
        }

        $this->log           = $log;
        $this->configuration = $configuration;
        date_default_timezone_set('UTC');

        spl_autoload_register(array($this, '__autoloader'));

        $this->serializer = new iDEALConnector_Xml_XmlSerializer();
        $this->signer     = new iDEALConnector_Xml_XmlSecurity();
        $this->validator  = new iDEALConnector_Validation_EntityValidator();

        $this->merchant = new iDEALConnector_Entities_Merchant(
            $this->configuration->getMerchantID(),
            $this->configuration->getSubID(),
            $this->configuration->getMerchantReturnURL()
        );
    }

    public function __autoloader($className)
    {
        $className  = str_replace('_', '/', $className);
        $_className = $className;
        $className  = __DIR__ .'/'. $className;

        if (is_readable($className) === false)
        {
            $className = __DIR__ .'/Libraries/'. $_className;
        }

        require_once $className .'.php';
    }

    /**
     * This is a conveninence method to create an instance of iDEALConnector using the default implementations of IConnectorConfiguration and IConnector Log
     *
     * @param string $configurationPath The path of your config.conf file
     * @return iDEALConnector
     * @static
     */
    static public function getDefaultInstance($configurationPath)
    {
        $config = new iDEALConnector_Configuration_DefaultConfiguration($configurationPath);

        return new iDEALConnector($config, new iDEALConnector_Log_DefaultLog($config->getLogLevel(),$config->getLogFile()));
    }


    /**
     * Get directory listing
     *
     * @return iDEALConnector_Entities_DirectoryResponse
     * @throws iDEALConnector_Exceptions_SerializationException
     * @throws iDEALConnector_Exceptions_iDEALException
     * @throws iDEALConnector_Exceptions_ValidationException
     * @throws iDEALConnector_Exceptions_SecurityException
     */
    public function getIssuers()
    {
        try
        {
            $request = new iDEALConnector_Entities_DirectoryRequest($this->merchant);

            if ($this->log !== null)
            {
                $this->log->logAPICall('getIssuers()', $request);
            }

            $this->validator->validate($request);

            $response = $this->sendRequest($request, $this->configuration->getAcquirerDirectoryURL());

            $this->validator->validate($response);

            if ($this->log !== null)
            {
                $this->log->logAPIReturn('getIssuers()', $response);
            }

            return $response;
        }
        catch (iDEALConnector_Exceptions_iDEALException $ex)
        {
            if ($this->log !== null)
            {
                $this->log->logErrorResponse($ex);
            }

            throw $ex;
        }
        catch (iDEALConnector_Exceptions_ValidationException $ex)
        {
            if ($this->log !== null)
            {
                $this->log->logException($ex);
            }

            throw $ex;
        }
        catch (iDEALConnector_Exceptions_SerializationException $ex)
        {
            if ($this->log !== null)
            {
                $this->log->logException($ex);
            }

            throw $ex;
        }
        catch (iDEALConnector_Exceptions_SecurityException $ex)
        {
            if ($this->log !== null)
            {
                $this->log->logException($ex);
            }

            throw $ex;
        }
    }

    /**
     * Start a transaction
     *
     * @param integer                             $issuerID
     * @param iDEALConnector_Entities_Transaction $transaction
     * @param mixed                               $merchantReturnUrl
     * @throws iDEALConnector_Exceptions_SerializationException
     * @throws iDEALConnector_Exceptions_iDEALException
     * @throws iDEALConnector_Exceptions_ValidationException
     * @throws iDEALConnector_Exceptions_SecurityException
     * @return iDEALConnector_Entities_AcquirerTransactionResponse
     */
    public function startTransaction($issuerID, iDEALConnector_Entities_Transaction $transaction,  $merchantReturnUrl = null)
    {
        try
        {
            $merchant = $this->merchant;

            if ($merchantReturnUrl !== null)
            {
                $merchant = new iDEALConnector_Entities_Merchant($this->configuration->getMerchantID(), $this->configuration->getSubID(), $merchantReturnUrl);
            }

            $request = new iDEALConnector_Entities_AcquirerTransactionRequest($issuerID, $merchant, $transaction);

            if ($this->log !== null)
            {
                $this->log->logAPICall('startTransaction()', $request);
            }

            $this->validator->validate($request);

            $response = $this->sendRequest($request, $this->configuration->getAcquirerTransactionURL());

            $this->validator->validate($response);

            if ($this->log !== null)
            {
                $this->log->logAPIReturn('startTransaction()', $response);
            }

            return $response;
        }
        catch (iDEALConnector_Exceptions_iDEALException $iex)
        {
            if ($this->log !== null)
            {
                $this->log->logErrorResponse($iex);
            }

            throw $iex;
        }
        catch (iDEALConnector_Exceptions_ValidationException $ex)
        {
            if ($this->log !== null)
            {
                $this->log->logException($ex);
            }

            throw $ex;
        }
        catch (iDEALConnector_Exceptions_SerializationException $ex)
        {
            if ($this->log !== null)
            {
                $this->log->logException($ex);
            }

            throw $ex;
        }
        catch (iDEALConnector_Exceptions_SecurityException $ex)
        {
            if ($this->log !== null)
            {
                $this->log->logException($ex);
            }

            throw $ex;
        }
    }

    /**
     * Get a transaction status
     *
     * @param integer $transactionID
     * @throws iDEALConnector_Exceptions_SerializationException
     * @throws iDEALConnector_Exceptions_iDEALException
     * @throws iDEALConnector_Exceptions_ValidationException
     * @throws iDEALConnector_Exceptions_SecurityException
     * @return iDEALConnector_Entities_AcquirerStatusResponse
     */
    public function getTransactionStatus($transactionID)
    {
        try
        {
            $request = new iDEALConnector_Entities_AcquirerStatusRequest($this->merchant, $transactionID);

            if ($this->log !== null)
            {
                $this->log->logAPICall('startTransaction()', $request);
            }

            $this->validator->validate($request);

            $response = $this->sendRequest($request, $this->configuration->getAcquirerStatusURL());

            $this->validator->validate($response);

            if ($this->log !== null)
            {
                $this->log->logAPIReturn('startTransaction()', $response);
            }

            return $response;
        }
        catch (iDEALConnector_Exceptions_iDEALException $iex)
        {
            if ($this->log !== null)
            {
                $this->log->logErrorResponse($iex);
            }

            throw $iex;
        }
        catch (iDEALConnector_Exceptions_ValidationException $ex)
        {
            if ($this->log !== null)
            {
                $this->log->logException($ex);
            }

            throw $ex;
        }
        catch (iDEALConnector_Exceptions_SerializationException $ex)
        {
            if ($this->log !== null)
            {
                $this->log->logException($ex);
            }

            throw $ex;
        }
        catch (iDEALConnector_Exceptions_SecurityException $ex)
        {
            if ($this->log !== null)
            {
                $this->log->logException($ex);
            }

            throw $ex;
        }
    }

    /*
     * Returns the assigned configuration array
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    private function sendRequest($request, $url)
    {
        $xml = $this->serializer->serialize($request);

        $this->signer->sign(
            $xml,
            $this->configuration->getCertificatePath(),
            $this->configuration->getPrivateKeyPath(),
            $this->configuration->getPassphrase()
        );

        $request = $xml->saveXML();

        if ($this->log !== null)
        {
            $this->log->logRequest($request);
        }

        if ($this->configuration->getProxy() !== null)
        {
            $response = iDEALConnector_Http_WebRequest::post($url, $request, $this->configuration->getProxy());
        }
        else
        {
            $response = iDEALConnector_Http_WebRequest::post($url, $request);
        }

        if ($this->log !== null)
        {
            $this->log->logResponse($response);
        }

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->loadXML($response);

        $verified = $this->signer->verify($doc, $this->configuration->getAcquirerCertificatePath());

        if ($verified === false)
        {
            throw new iDEALConnector_Exceptions_SecurityException('Response message signature check fails.');
        }

        return $this->serializer->deserialize($doc);
    }
}