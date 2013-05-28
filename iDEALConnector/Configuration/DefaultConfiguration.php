<?php
class iDEALConnector_Configuration_DefaultConfiguration implements iDEALConnector_Configuration_IConnectorConfiguration
{
    private $certificate = '';
    private $privateKey  = '';
    private $passphrase  = '';

    private $acquirerCertificate = '';

    private $merchantID = '';
    private $subID      = 0;
    private $returnURL  = '';

    private $expirationPeriod       = 60;
    private $acquirerDirectoryURL   = '';
    private $acquirerTransactionURL = '';
    private $acquirerStatusURL      = '';
    private $timeout                = 10;

    private $proxy    = null;
    private $proxyUrl = '';

    private $logFile = 'logs/connector.log';
    private $logLevel = iDEALConnector_Log_LogLevel::Error;

    function __construct($path)
    {
        $data = $this->loadFromFile($path);

        if (!empty($data))
        {
            $this->setConfig($data);
        }
    }

    private function loadFromFile($path)
    {
        $configData = array();

        $file = fopen($path, 'r');

        if ($file)
        {
            while (!feof($file))
            {
                $buffer = fgets($file);

                /* @var $buffer array() */
                $buffer = trim($buffer);

                if (!empty($buffer))
                {
                    if ($buffer[0] !== '#')
                    {
                        $pos = strpos($buffer, '=');

                        if ($pos > 0 && $pos != (strlen($buffer) - 1))
                        {
                            $dumb = trim(substr($buffer, 0, $pos));

                            if (!empty($dumb))
                            {
                                // Populate the configuration array
                                $configData[strtoupper(substr($buffer, 0, $pos))] = substr($buffer, $pos + 1);
                            }
                        }
                    }
                }
            }
        }

        fclose($file);

        return $configData;
    }

    public function setConfig($configData)
    {
        if (!empty($configData['MERCHANTID']))
        {
            $this->merchantID = $configData['MERCHANTID'];
        }

        if (!empty($configData['SUBID']))
        {
            $this->subID = intval($configData['SUBID']);
        }

        if (!empty($configData['MERCHANTRETURNURL']))
        {
            $this->returnURL = $configData['MERCHANTRETURNURL'];
        }


        if (!empty($configData['ACQUIRERURL']))
        {
            $this->acquirerDirectoryURL   = $configData['ACQUIRERURL'];
            $this->acquirerStatusURL      = $configData['ACQUIRERURL'];
            $this->acquirerTransactionURL = $configData['ACQUIRERURL'];
        }

        if (!empty($configData['ACQUIRERTIMEOUT']))
        {
            $this->timeout = intval($configData['ACQUIRERTIMEOUT']);
        }

        if (!empty($configData['EXPIRATIONPERIOD']))
        {
            $this->expirationPeriod = 60;

            if ($configData['EXPIRATIONPERIOD'] !== 'PT1H')
            {
                $value = substr($configData['EXPIRATIONPERIOD'], 2, strlen($configData['EXPIRATIONPERIOD']) - 3);

                if (is_numeric($value))
                {
                    $this->expirationPeriod = (int)$value;
                }
            }
        }

        if (!empty($configData['CERTIFICATE0']))
        {
            $this->acquirerCertificate = $configData['CERTIFICATE0'];
        }

        if (!empty($configData['PRIVATECERT']))
        {
            $this->certificate = $configData['PRIVATECERT'];
        }

        if (!empty($configData['PRIVATEKEY']))
        {
            $this->privateKey = $configData['PRIVATEKEY'];
        }

        if (!empty($configData['PRIVATEKEYPASS']))
        {
            $this->passphrase = $configData['PRIVATEKEYPASS'];
        }

        if (!empty($configData['PROXY']))
        {
            $this->proxy = $configData['PROXY'];
        }

        if (!empty($configData['PROXYACQURL']))
        {
            $this->proxyUrl = $configData['PROXYACQURL'];
        }

        if (!empty($configData['LOGFILE']))
        {
            $this->logFile = $configData['LOGFILE'];
        }

        if (!empty($configData['TRACELEVEL']))
        {
            $level = strtolower($configData['TRACELEVEL']);

            switch ($level)
            {
                case 'debug':
                        $this->logLevel = iDEALConnector_Log_LogLevel::Debug;
                    break;
                case 'error':
                        $this->logLevel = iDEALConnector_Log_LogLevel::Error;
                    break;
                default:
                    break;
            }
        }
    }

    public function getAcquirerCertificatePath()
    {
        return $this->acquirerCertificate;
    }

    public function getCertificatePath()
    {
        return $this->certificate;
    }

    public function getExpirationPeriod()
    {
        return $this->expirationPeriod;
    }

    public function getMerchantID()
    {
        return $this->merchantID;
    }

    public function getPassphrase()
    {
        return $this->passphrase;
    }

    public function getPrivateKeyPath()
    {
        return $this->privateKey;
    }

    public function getMerchantReturnURL()
    {
        return $this->returnURL;
    }

    public function getSubID()
    {
        return $this->subID;
    }

    public function getAcquirerTimeout()
    {
        return $this->timeout;
    }

    public function getAcquirerDirectoryURL()
    {
        return $this->acquirerDirectoryURL;
    }

    public function getAcquirerStatusURL()
    {
        return $this->acquirerStatusURL;
    }

    public function getAcquirerTransactionURL()
    {
        return $this->acquirerTransactionURL;
    }

    /**
     * @return string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @return string
     */
    public function getProxyUrl()
    {
        return $this->proxyUrl;
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * @return integer
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }
}