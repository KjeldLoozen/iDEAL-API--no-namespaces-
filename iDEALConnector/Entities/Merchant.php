<?php
/**
 *  The Merchant description.
 */
class iDEALConnector_Entities_Merchant
{
    private $merchantID;
    private $subID;
    private $merchantReturnURL;

    /**
     * @param string  $merchantID
     * @param integer $subID
     * @param string  $merchantReturnURL
     */
    public function __construct($merchantID, $subID, $merchantReturnURL)
    {
        if(!is_string($merchantID))
        {
            throw new InvalidArgumentException('Parameter \'merchantID\' must be of type string.');
        }

        if(!is_int($subID))
        {
            throw new InvalidArgumentException('Parameter \'subID\' must be of type integer.');
        }

        $this->merchantID        = $merchantID;
        $this->merchantReturnURL = $merchantReturnURL;
        $this->subID             = $subID;
    }

    /**
     * @return string
     */
    public function getMerchantID()
    {
        return $this->merchantID;
    }

    /**
     * @return integer
     */
    public function getSubID()
    {
        return $this->subID;
    }

    /**
     * @return string
     */
    public function getMerchantReturnURL()
    {
        return $this->merchantReturnURL;
    }
}