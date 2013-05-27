<?php
class iDEALConnector_Entities_AcquirerStatusRequest extends iDEALConnector_Entities_AbstractRequest
{
    private $merchant;
    private $transactionID;

    /**
     * @param iDEALConnector_Entities_Merchant $merchant
     * @param string                           $transactionID
     */
    public function __construct(iDEALConnector_Entities_Merchant $merchant, $transactionID)
    {
        if (!is_string($transactionID))
        {
            throw new InvalidArgumentException('Parameter \'transactionID\' must be of type string.');
        }

        parent::__construct();

        $this->merchant      = $merchant;
        $this->transactionID = $transactionID;
    }

    /**
     * @return iDEALConnector_Entities_Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @return string
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }
}
