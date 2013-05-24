<?php
/**
 * Implement current interface to create custom configuration providers.
 */
interface iDEALConnector_Configuration_IConnectorConfiguration
{
    /**
     * @return string
     */
    public function getAcquirerCertificatePath();

    /**
     * @return string
     */
    public function getAcquirerDirectoryURL();

    /**
     * @return string
     */
    public function getAcquirerStatusURL();

    /**
     * @return string
     */
    public function getAcquirerTransactionURL();

    /**
     * @return string
     */
    public function getCertificatePath();

    /**
     * @return integer
     */
    public function getExpirationPeriod();

    /**
     * @return string
     */
    public function getMerchantID();

    /**
     * @return string
     */
    public function getPassphrase();

    /**
     * @return string
     */
    public function getPrivateKeyPath();

    /**
     * @return string
     */
    public function getMerchantReturnURL();

    /**
     * @return integer
     */
    public function getSubID();

    /**
     * @return integer
     */
    public function getAcquirerTimeout();

    /**
     * @return string
     */
    public function getProxy();

    /**
     * @return string
     */
    public function getProxyUrl();

    /**
     * @return string
     */
    public function getLogFile();

    /**
     * @return string
     */
    public function getLogLevel();
}