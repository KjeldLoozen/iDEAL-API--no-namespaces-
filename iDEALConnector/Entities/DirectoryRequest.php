<?php
/**
 * The DirectoryRequest object used for the directory request call.
 */
class iDEALConnector_Entities_DirectoryRequest extends iDEALConnector_Entities_AbstractRequest
{
    private $merchant;

    /**
     * @param iDEALConnector_Entities_Merchant $merchant
     */
    public function __construct(iDEALConnector_Entities_Merchant $merchant)
    {
        parent::__construct();

        $this->merchant = $merchant;
    }

    /**
     * @return iDEALConnector_Entities_Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }
}