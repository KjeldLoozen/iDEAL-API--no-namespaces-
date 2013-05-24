<?php
/**
 * The DirectoryResponse object received from the directory request call.
 */
class iDEALConnector_Entities_DirectoryResponse extends iDEALConnector_Entities_AbstractResponse
{
    private $directoryDate;
    private $acquirerID;
    private $countries;

    /**
     * Class constructor
     *
     * @param DateTime $date
     * @param DateTime $directoryDate
     * @param string   $acquirerID
     * @param array    $countries Array of iDEALConnector_Entities_Country
     * @return iDEALConnector_Entities_DirectoryResponse
     * @throws InvalidArgumentException
     */
    public function __construct(DateTime $date, DateTime $directoryDate, $acquirerID, $countries)
    {
        if(!is_string($acquirerID))
        {
            throw new InvalidArgumentException('Parameter \'acquirerID\' should be of type string.');
        }

        if(!is_array($countries))
        {
            throw new InvalidArgumentException('Parameter \'countries\' should be an array.');
        }

        parent::__construct($date);

        $this->directoryDate = $directoryDate;
        $this->acquirerID    = $acquirerID;
        $this->countries     = $countries;
    }

    /**
     * @return string
     */
    public function getAcquirerID()
    {
        return $this->acquirerID;
    }

    /**
     * @return array Array of iDEALConnector_Entities_Country
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * @return DateTime
     */
    public function getDirectoryDate()
    {
        return $this->directoryDate;
    }
}