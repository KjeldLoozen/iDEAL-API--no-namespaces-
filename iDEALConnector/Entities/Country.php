<?php
/**
 *  The Country class specific to the directoryResponse.
 */
class iDEALConnector_Entities_Country
{
    private $countryNames;
    private $issuers;

    /**
     * @param string $countryNames
     * @param array  $issuers      Array of iDEALConnector_Entities_Issuer
     * @throws InvalidArgumentException
     */
    public function __construct($countryNames, $issuers)
    {
        if(!is_array($issuers))
        {
            throw new InvalidArgumentException('Parameter \'issuers\' must be array.');
        }

        if(!is_string($countryNames))
        {
            throw new InvalidArgumentException('Parameter \'countryNames\' must be of type string.');
        }

        $this->countryNames = $countryNames;
        $this->issuers      = $issuers;
    }

    /**
     * @return string
     */
    public function getCountryNames()
    {
        return $this->countryNames;
    }

    /**
     * @return array
     */
    public function getIssuers()
    {
        return $this->issuers;
    }
}