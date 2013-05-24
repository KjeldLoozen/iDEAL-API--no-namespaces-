<?php
class iDEALConnector_Validation_EntityValidator
{
    public function validate($request)
    {
        $className = get_class($request);

        switch ($className)
        {
            case 'iDEALConnector_Entities_DirectoryRequest':
                    /* @var $request iDEALConnector_Entities_DirectoryRequest */
                    $this->validateMerchant($request->getMerchant());
                break;
            case 'iDEALConnector_Entities_AcquirerTransactionRequest':
                    /* @var $request iDEALConnector_Entities_AcquirerTransactionRequest */
                    $this->validateAcquirerTransactionRequest($request);
                break;
            case 'iDEALConnector_Entities_AcquirerStatusRequest':
                    /* @var $request iDEALConnector_Entities_AcquirerStatusRequest */
                    $this->validateAcquirerStatusRequest($request);
                break;
            case 'iDEALConnector_Entities_DirectoryResponse':
                    /* @var $request iDEALConnector_Entities_DirectoryResponse */
                    $this->validateDirectoryResponse($request);
                break;
            case 'iDEALConnector_Entities_AcquirerTransactionResponse':
                    /* @var $request iDEALConnector_Entities_AcquirerTransactionResponse */
                    $this->validateAcquirerTransactionResponse($request);
                break;
            case 'iDEALConnector_Entities_AcquirerStatusResponse':
                    /* @var $request iDEALConnector_Entities_AcquirerStatusResponse */
                    $this->validateAcquirerStatusResponse($request);
                break;
            default:
                    throw new iDEALConnector_Exceptions_ValidationException('Given object type could not be validated.');
                break;
        }
    }

    private function validateAcquirerStatusRequest(iDEALConnector_Entities_AcquirerStatusRequest $input)
    {
        if(strlen($input->getTransactionID()) !== 16)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.transactionID length not 16.');
        }

        $length = preg_match('/[0-9]+/', $input->getTransactionID(), $matches);

        if ($length !== 1 || $matches[0] !== $input->getTransactionID())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.transactionID does not match format.');
        }

        $this->validateMerchant($input->getMerchant());
    }

    private function validateAcquirerTransactionRequest(iDEALConnector_Entities_AcquirerTransactionRequest $input)
    {
        $length = preg_match('/[A-Z]{6,6}[A-Z2-9][A-NP-Z0-9]([A-Z0-9]{3,3}){0,1}/', $input->getIssuerID(), $matches);

        if ($length !== 1 || $matches[0] !== $input->getIssuerID())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Issuer.issuerID does not match format.');
        }

        $this->validateMerchant($input->getMerchant());
        $this->validateTransaction($input->getTransaction());
    }

    private function validateMerchant(iDEALConnector_Entities_Merchant $merchant)
    {
        if (strlen($merchant->getMerchantID()) !== 9)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Merchant.merchantID length is not 9');
        }

        $length = preg_match('/[0-9]+/',$merchant->getMerchantID(), $matches);

        if ($length !== 1 || $matches[0] !== $merchant->getMerchantID())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Merchant.merchantID does not match format.');
        }

        if ($merchant->getSubID() > 999999 || $merchant->getSubID() < 0)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Merchant.subID value must be between 0 and 999999.');
        }

        if (strlen($merchant->getMerchantReturnURL()) > 512)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Merchant.merchantReturnURL length is to large.');
        }
    }

    private function validateTransaction(iDEALConnector_Entities_Transaction $transaction)
    {
        if ($transaction->getAmount() < 0 || $transaction->getAmount() >= 1000000000000)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.amount outside value range.');
        }

        if($transaction->getCurrency() !== 'EUR')
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.currency does not match format.');
        }

        if($transaction->getExpirationPeriod() < 1 || $transaction->getExpirationPeriod() > 60)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.expirationPeriod length outside range(\'PT1M\', \'PT1H\').');
        }

        if(strlen($transaction->getLanguage()) !== 2)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.language length not 2.');
        }

        if (strlen($transaction->getDescription()) < 1 || strlen($transaction->getDescription()) > 35)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.description length outside range(1, 35).');
        }

        if(strlen($transaction->getEntranceCode()) < 1 || strlen($transaction->getEntranceCode()) > 40)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.entranceCode length outside range(1, 35).');
        }

        $length = preg_match('/[a-z]+/', $transaction->getLanguage(), $matches);

        if ($length !== 1 || $matches[0] !== $transaction->getLanguage())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.language does not match format.');
        }

        $length = preg_match('/[a-zA-Z0-9]+/',$transaction->getEntranceCode(), $matches);

        if ($length !== 1 || $matches[0] !== $transaction->getEntranceCode())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.entranceCode does not match format.');
        }

        $length = preg_match('/[a-zA-Z0-9]+/',$transaction->getPurchaseId(), $matches);

        if ($length !== 1 || $matches[0] !== $transaction->getPurchaseId())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.purchaseId does not match format.');
        }
    }

    private function validateDirectoryResponse(iDEALConnector_Entities_DirectoryResponse $response)
    {
        foreach ($response->getCountries() as $k_c => $country)
        {
            $length = strlen($country->getCountryNames());

            if ($length < 1 || $length > 128)
            {
                throw new iDEALConnector_Exceptions_ValidationException('Country.issuerID does not match format.');
            }

            foreach ($country->getIssuers() as $k_i => $issuer)
            {

                $length = preg_match('/[A-Z]{6,6}[A-Z2-9][A-NP-Z0-9]([A-Z0-9]{3,3}){0,1}/', $issuer->getId(), $matches);

                if ($length !== 1 || $matches[0] !== $issuer->getId())
                {
                    throw new iDEALConnector_Exceptions_ValidationException('Country['. $k_c. '].Issuer['. $k_i .'].issuerID does not match format.');
                }

                $length = strlen($issuer->getName());

                if ($length < 1 || $length > 35)
                {
                    throw new iDEALConnector_Exceptions_ValidationException('Country['. $k_c .'].Issuer['. $k_i .'].issuerName does not match format.');
                }
            }
        }
    }

    private function validateAcquirerTransactionResponse(iDEALConnector_Entities_AcquirerTransactionResponse $response)
    {
        $length = strlen ($response->getAcquirerID());

        if ($length !== 4)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Acquirer.acquirerID does not match length.');
        }

        $length = preg_match('/[0-9]+/', $response->getAcquirerID(), $matches);

        if ($length !== 1 || $matches[0] !== $response->getAcquirerID())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Acquirer.acquirerID does not match format.');
        }

        $length = strlen($response->getIssuerAuthenticationURL());

        if ($length > 512)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Issuer.issuerAuthenticationURL exceeds length.');
        }

        $length = strlen ($response->getTransactionID());

        if ($length !== 16)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.transactionID exceeds length.');
        }

        $length = preg_match('/[0-9]+/', $response->getTransactionID(), $matches);

        if ($length !== 1 || $matches[0] !== $response->getTransactionID())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.transactionID does not match format.');
        }

        $length = strlen ($response->getPurchaseID());

        if ($length < 1 || $length > 35)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.purchaseID length not in range(1,35).');
        }

        $length = preg_match('/[a-zA-Z0-9]+/', $response->getPurchaseID(), $matches);

        if ($length !== 1 || $matches[0] !== $response->getPurchaseID())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.purchaseID does not match format.');
        }
    }

    private function validateAcquirerStatusResponse(iDEALConnector_Entities_AcquirerStatusResponse $response)
    {
        $length = strlen($response->getAcquirerID());

        if ($length !== 4)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Acquirer.acquirerID does not match length.');
        }

        $length = preg_match('/[0-9]+/', $response->getAcquirerID(), $matches);

        if ($length !== 1 || $matches[0] !== $response->getAcquirerID())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Acquirer.acquirerID does not match format.');
        }

        $length = strlen($response->getTransactionID());

        if ($length !== 16)
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.transactionID exceeds length.');
        }

        $length = preg_match('/[0-9]+/', $response->getTransactionID(), $matches);

        if ($length !== 1 || $matches[0] !== $response->getTransactionID())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.transactionID does not match format.');
        }

        $statuses = iDEALConnector_Entities_AcquirerStatusResponse::getStatuses();

        $length = preg_match('/'. implode('|', $statuses) .'/', $response->getStatus(), $matches);

        if ($length !== 1 || $matches[0] !== $response->getStatus())
        {
            throw new iDEALConnector_Exceptions_ValidationException('Transaction.status does not match format.');
        }
    }
}