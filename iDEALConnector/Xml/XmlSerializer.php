<?php
class iDEALConnector_Xml_XmlSerializer
{
    public function serialize(iDEALConnector_Entities_AbstractRequest $input)
    {
        $doc = new DOMDocument('1.0', 'utf-8');

        $className = get_class($input);

        switch ($className)
        {
            case 'iDEALConnector_Entities_DirectoryRequest':
                    $element = $doc->createElement('DirectoryReq');
                    $this->serializeAbstractRequest($element, $input);

                    /* @var $input iDEALConnector_Entities_DirectoryRequest */
                    $this->serializeDirectoryRequest($element, $input);
                    $doc->appendChild($element);
                break;
            case 'iDEALConnector_Entities_AcquirerTransactionRequest':
                    $element = $doc->createElement('AcquirerTrxReq');
                    $this->serializeAbstractRequest($element, $input);

                    /* @var $input iDEALConnector_Entities_AcquirerTransactionRequest */
                    $this->serializeAcquirerTransactionRequest($element, $input);
                    $doc->appendChild($element);
                break;
            case 'iDEALConnector_Entities_AcquirerStatusRequest':
                    $element = $doc->createElement('AcquirerStatusReq');
                    $this->serializeAbstractRequest($element, $input);

                    /* @var $input iDEALConnector_Entities_AcquirerStatusRequest */
                    $this->serializeAcquirerStatusRequest($element, $input);
                    $doc->appendChild($element);
                break;
            default:
                    throw new iDEALConnector_Exceptions_SerializationException('Given object type could not be serialized.');
                break;
        }

        return $doc;
    }

    private function serializeAbstractRequest(DOMElement $element, iDEALConnector_Entities_AbstractRequest $request)
    {
        $element->appendChild(new DOMElement('createDateTimestamp', $request->getCreateDateTimestamp()->format('Y-m-d\TH:i:s\Z')));
        $element->setAttribute('version', $request->getVersion());

        $element->setAttribute('xmlns', 'http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1');
    }

    private function serializeDirectoryRequest(DOMElement $element, iDEALConnector_Entities_DirectoryRequest $request)
    {
        $merchant = $element->ownerDocument->createElement('Merchant');
        $this->serializeMerchant($merchant, $request->getMerchant());
        $element->appendChild($merchant);
    }

    private function serializeAcquirerTransactionRequest(DOMElement $element, iDEALConnector_Entities_AcquirerTransactionRequest $request)
    {
        $item = $element->ownerDocument->createElement('Issuer');
        $item->appendChild(new DOMElement('issuerID', $request->getIssuerID()));
        $element->appendChild($item);

        $item = $element->ownerDocument->createElement('Merchant');
        $this->serializeMerchant($item, $request->getMerchant(), true);
        $element->appendChild($item);

        $item = $element->ownerDocument->createElement('Transaction');
        $this->serializeTransaction($item, $request->getTransaction());
        $element->appendChild($item);
    }

    private function serializeAcquirerStatusRequest(DOMElement $element, iDEALConnector_Entities_AcquirerStatusRequest $request)
    {
        $item = $element->ownerDocument->createElement('Merchant');
        $this->serializeMerchant($item, $request->getMerchant());
        $element->appendChild($item);

        $item = $element->ownerDocument->createElement('Transaction');
        $item->appendChild(new DOMElement('transactionID', $request->getTransactionID()));
        $element->appendChild($item);
    }

    private function serializeMerchant(DOMElement $element, iDEALConnector_Entities_Merchant $merchant, $withUrl = false)
    {
        $element->appendChild(new DOMElement('merchantID', $merchant->getMerchantID()));
        $element->appendChild(new DOMElement('subID', $merchant->getSubID()));

        if (!empty($withUrl))
        {
            $element->appendChild(new DOMElement('merchantReturnURL', $merchant->getMerchantReturnURL()));
        }
    }

    private function serializeTransaction(DOMElement $element, iDEALConnector_Entities_Transaction $transaction)
    {
        $element->appendChild(new DOMElement('purchaseID', $transaction->getPurchaseId()));
        $element->appendChild(new DOMElement('amount',     number_format($transaction->getAmount(), 2)));
        $element->appendChild(new DOMElement('currency',   $transaction->getCurrency()));

        $expPeriod = 'PT1H';

        if ($transaction->getExpirationPeriod() !== 60)
        {
            $expPeriod = 'PT'. $transaction->getExpirationPeriod() .'M';
        }

        $element->appendChild(new DOMElement('expirationPeriod', $expPeriod));

        $element->appendChild(new DOMElement('language',     $transaction->getLanguage()));
        $element->appendChild(new DOMElement('description',  $transaction->getDescription()));
        $element->appendChild(new DOMElement('entranceCode', $transaction->getEntranceCode()));
    }

    /**
     * Deserializes XML
     *
     * @param DOMDocument $xml
     * @return iDEALConnector_Entities_AbstractResponse
     */
    public function deserialize(DOMDocument $xml)
    {
        $this->checkForErrorMessage($xml);

        return $this->deserializeResponse($xml->documentElement);
    }

    private function checkForErrorMessage(DOMDocument $doc)
    {
        if ($doc->documentElement->tagName !== 'AcquirerErrorRes')
        {
            return;
        }

        $code    = null;
        $message = null;
        $details = '';
        $action  = '';
        $consumerMessage = '';

        if($doc->documentElement->hasChildNodes() === false)
        {
            throw new iDEALConnector_Exceptions_SerializationException('Error response missing content.');
        }

        $elements = $doc->documentElement->getElementsByTagName('Error');

        if ($elements->length === 1)
        {
            try
            {
                /* @var $element DOMElement */
                $element = $elements->item(0);

                $code    = $element->getElementsByTagName('errorCode')->item(0)->nodeValue;
                $message = $element->getElementsByTagName('errorMessage')->item(0)->nodeValue;

                $nodes = $element->getElementsByTagName('errorDetail');

                if ($nodes->length === 1)
                {
                    $details = $nodes->item(0)->nodeValue;
                }

                $nodes = $element->getElementsByTagName('suggestedAction');

                if ($nodes->length === 1)
                {
                    $action = $nodes->item(0)->nodeValue;
                }

                $nodes = $element->getElementsByTagName('consumerMessage');

                if ($nodes->length === 1)
                {
                    $consumerMessage = $nodes->item(0)->nodeValue;
                }
            }
            catch(Exception $e)
            {
                //Pass-through to exception throwing if minimum requirements are not met.
            }
        }

        if($code === null || $message === null)
        {
            throw new iDEALConnector_Exceptions_SerializationException('Invalid format of error response.');
        }

        throw new iDEALConnector_Exceptions_iDEALException($code, $message, $details, $action, $consumerMessage);
    }

    private function deserializeResponse(DOMElement $xml)
    {
        $timestamp = $xml->getElementsByTagName('createDateTimestamp');

        if ($timestamp->length != 1)
        {
            throw new iDEALConnector_Exceptions_SerializationException('Element \'createDateTimestamp\' should be present once.');
        }

        $timestamp = new DateTime($timestamp->item(0)->nodeValue);

        switch ($xml->tagName)
        {
            case 'DirectoryRes':
                    return $this->deserializeDirectoryResponse($xml, $timestamp);
                break;
            case 'AcquirerTrxRes':
                    return $this->deserializeAcquirerTransactionResponse($xml, $timestamp);
                break;
            case 'AcquirerStatusRes':
                    return $this->deserializeAcquirerStatusResponse($xml, $timestamp);
                break;
            default:
                break;
        }

        throw new iDEALConnector_Exceptions_SerializationException('Could not deserialize response.');
    }

    private function deserializeDirectoryResponse(DOMElement $xml, DateTime $createdTimestamp)
    {
        /* @var $nodes DOMNodeList */
        $nodes = $this->getChildren($xml, 'Acquirer');
        /* @var $node DOMElement */
        $node  = $nodes->item(0);
        $acquirerID = $this->getFirstValue($node, 'acquirerID', 'Acquirer.acquirerID');

        $nodes = $this->getChildren($xml, 'Directory');
        $node  = $nodes->item(0);

        $timestamp = new DateTime($this->getFirstValue($node, 'directoryDateTimestamp', 'Directory.directoryDateTimestamp'));

        $countryElements = $this->getChildren($node, 'Country', 'Directory.Country', -1);

        $countries = array();

        /* @var $country DOMElement */
        foreach ($countryElements as $country)
        {
            $names = $this->getFirstValue($country, 'countryNames', 'Directory.Country.countryNames');

            $subNodes = $this->getChildren($country, 'Issuer', 'Directory.Country.Issuer', -1);
            $issuers  = array();

            /* @var $issuer DOMElement */
            foreach ($subNodes as $issuer)
            {
                $id   = $this->getFirstValue($issuer, 'issuerID', 'Directory.Country.Issuer.issuerID');
                $name = $this->getFirstValue($issuer, 'issuerName', 'Directory.Country.Issuer.issuerName');

                array_push($issuers, new iDEALConnector_Entities_Issuer($id, $name));
            }

            array_push($countries, new iDEALConnector_Entities_Country($names, $issuers));
        }

        return new iDEALConnector_Entities_DirectoryResponse($createdTimestamp, $timestamp, $acquirerID, $countries);
    }

    private function deserializeAcquirerTransactionResponse(DOMElement $xml, DateTime $createdTimestamp)
    {
        /* @var $nodes DOMNodeList */
        $nodes = $this->getChildren($xml, 'Acquirer');
        /* @var $node DOMElement */
        $node  = $nodes->item(0);

        $acquirerID = $this->getFirstValue($node, 'acquirerID', 'Acquirer.acquirerID');

        $nodes = $this->getChildren($xml, 'Issuer');
        $node  = $nodes->item(0);

        $issuerAuthenticationUrl = $this->getFirstValue($node, 'issuerAuthenticationURL', 'Issuer.issuerAuthenticationURL');

        $nodes         = $this->getChildren($xml, 'Transaction');
        $node          = $nodes->item(0);
        $transactionId = $this->getFirstValue($node, 'transactionID', 'Transaction.transactionID');

        $transactionCreateDateTimestamp =
            new DateTime(
                $this->getFirstValue(
                    $node,
                    'transactionCreateDateTimestamp',
                    'Transaction.transactionCreateDateTimestamp'
                )
            );

        $purchaseID = $this->getFirstValue($node, 'purchaseID', 'Transaction.purchaseID');

        return new iDEALConnector_Entities_AcquirerTransactionResponse(
            $acquirerID,
            $issuerAuthenticationUrl,
            $purchaseID,
            $transactionId,
            $transactionCreateDateTimestamp,
            $createdTimestamp
        );
    }

    private function deserializeAcquirerStatusResponse(DOMElement $xml, DateTime $createdTimestamp)
    {
        /* @var $nodes DOMNodeList */
        $nodes = $this->getChildren($xml, 'Acquirer');
        /* @var $node DOMElement */
        $node  = $nodes->item(0);

        $acquirerID = $this->getFirstValue($node, 'acquirerID','Acquirer.acquirerID');

        $nodes         = $this->getChildren($xml, 'Transaction');
        $node          = $nodes->item(0);

        $transactionId =              $this->getFirstValue($node,  'transactionID',        'Transaction.transactionID');
        $status        =              $this->getFirstValue($node,  'status',               'Transaction.status');
        $timestamp     = new DateTime($this->getFirstValue($node,  'statusDateTimestamp',  'Transaction.statusDateTimestamp'));

        $consumerName =        $this->getFirstValue($node, 'consumerName',  'Transaction.consumerName', 0);
        $consumerIBAN =        $this->getFirstValue($node, 'consumerIBAN',  'Transaction.consumerIBAN', 0);
        $consumerBIC  =        $this->getFirstValue($node, 'consumerBIC',   'Transaction.consumerBIC',  0);
        $amount       = (float)$this->getFirstValue($node, 'amount',        'Transaction.amount',       0);
        $currency     =        $this->getFirstValue($node, 'currency',      'Transaction.currency',     0);

        return new iDEALConnector_Entities_AcquirerStatusResponse(
            $acquirerID,
            $amount,
            $consumerBIC,
            $consumerIBAN,
            $consumerName,
            $createdTimestamp,
            $currency,
            $status,
            $timestamp,
            $transactionId
        );
    }

    private function getChildren(DOMElement $element, $tag, $key = null, $occurs = 1)
    {
        if($key === null)
        {
            $key = $tag;
        }

        $nodes = $element->getElementsByTagName($tag);

        if ($occurs === 0 && $nodes->length != 1)
        {
            throw new iDEALConnector_Exceptions_SerializationException('Element \'$key\' should be present once.');
        }

        if ($occurs === 1 && $nodes->length < 1)
        {
            throw new iDEALConnector_Exceptions_SerializationException('Element \'$key\' should be present at least once.');
        }

        return $nodes;
    }

    private function getFirstValue(DOMElement $node, $tag, $key, $occurs = 1)
    {
        /* @var $nodes DOMNodeList */
        $nodes = $node->getElementsByTagName($tag);

        if ($nodes->length === 0)
        {
            return null;
        }

        if ($occurs === 1 && $nodes->length != 1)
        {
            throw new iDEALConnector_Exceptions_SerializationException('Element \'$key\' should be present once.');
        }

        if ($occurs === -1 && $nodes->length < 1)
        {
            throw new iDEALConnector_Exceptions_SerializationException('Element \'$key\' should be present at least once.');
        }

        return $nodes->item(0)->nodeValue;
    }
}