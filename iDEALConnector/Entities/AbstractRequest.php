<?php
/**
 * The abstract used for all request objects.
 */
abstract class iDEALConnector_Entities_AbstractRequest
{
    private $createDateTimestamp;

    public function __construct()
    {
        $this->createDateTimestamp = new DateTime();
    }

    /**
     * @return DateTime
     */
    public function getCreateDateTimestamp()
    {
        return $this->createDateTimestamp;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '3.3.1';
    }
}
