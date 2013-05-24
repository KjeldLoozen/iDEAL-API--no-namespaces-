<?php
class iDEALConnector_Xml_XmlSecurity
{
    public function sign(DOMDocument $doc, $privateCertificatePath, $privateKeyPath, $passphrase)
    {
        $signature = new XMLSecurityDSig();
        $signature->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $signature->addReference($doc, XMLSecurityDSig::SHA256, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'), array('force_uri' => true));

        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type' => 'private'));

        $key->passphrase = $passphrase;
        $key->loadKey($privateKeyPath, TRUE);

        $signature->sign($key);

        $fingerprint = $this->getFingerprint($privateCertificatePath);

        $signature->addKeyInfoAndName($fingerprint);

        $signature->appendSignature($doc->documentElement);

        return $doc->saveXML();
    }

    public function verify(DOMDocument $doc, $certificatePath)
    {
        $signature = new XMLSecurityDSig();
        $sig       = $signature->locateSignature($doc);

        if (empty($sig))
        {
            throw new SecurityException('Cannot locate Signature Node');
        }

        //$signature->setCanonicalMethod(XMLSecurityDSig::EXC_C14N); //whitespaces are significant
        $signature->canonicalizeSignedInfo();

        try
        {
            $signature->validateReference();
        }
        catch(Exception $ex)
        {
            throw new iDEALConnector_Exceptions_SecurityException('Reference Validation Failed');
        }

        $key = $signature->locateKey();

        if (empty($key))
        {
            throw new iDEALConnector_Exceptions_SecurityException('Cannot locate the key.');
        }

        $key->loadKey($certificatePath, true);

        return $signature->verify($key) == 1;
    }

    private function getFingerprint($path)
    {
        $contents = file_get_contents(__DIR__ .'/'. $path);

        if ($contents === null)
        {
            throw new iDEALConnector_Exceptions_SecurityException('Empty certificate.');
        }

        $r = array(
            '-----BEGIN CERTIFICATE-----',
            '-----END CERTIFICATE-----',
        );

        $contents = str_replace($r, '', $contents);
        $contents = base64_decode($contents);

        return sha1($contents);
    }
}
