<?php
class iDEALConnector_Entities_AcquirerTransactionRequest extends iDEALConnector_Entities_AbstractRequest
{
    private $issuerID;
    private $merchant;
    private $transaction;

    /**
     * @param string                               $issuerID
     * @param iDEALConnector_Entities_Merchant     $merchant
     * @param iDEALConnector_Entities_Transaction  $transaction
     */
    public function __construct($issuerID, iDEALConnector_Entities_Merchant $merchant, iDEALConnector_Entities_Transaction $transaction)
    {
        parent::__construct();

        $this->issuerID    = $issuerID;
        $this->merchant    = $merchant;
        $this->transaction = $transaction;
    }

    /**
     * @return string
     */
    public function getIssuerID()
    {
        return $this->issuerID;
    }

    /**
     * @return iDEALConnector_Entities_Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @return iDEALConnector_Entities_Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}