<?php
/**
 * The abstract used for all response objects.
 */
class iDEALConnector_Entities_AbstractResponse
{
    private $createDateTimestamp;

    /**
     * @param DateTime $createDateTimestamp
     */
    public function __construct(DateTime $createDateTimestamp)
    {
        $this->createDateTimestamp = $createDateTimestamp;
    }

    /**
     * @return DateTime
     */
    public function getCreateDateTimestamp()
    {
        return $this->createDateTimestamp;
    }
}