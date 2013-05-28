<?php
class iDEALConnector_Http_WebRequest
{
    /**
     * Send a POST request to actual API
     *
     * @param string $url   URL to send request to
     * @param string $data  Correct XML
     * @param mixed  $proxy Proxy data
     * @return string
     */
    static public function Post($url, $data, $proxy = null)
    {
        $request = curl_init();
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($request, CURLOPT_POST, 1);
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($request, CURLOPT_POSTFIELDS, (string)$data);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);

        if (isset($proxy))
        {
            $idx  = strrpos($proxy, ':');
            $host = substr( $proxy, 0, $idx);
            $idx  = strpos( $proxy, ':');
            $port = substr( $proxy, $idx + 1);

            curl_setopt($request, CURLOPT_PROXY, $host);
            curl_setopt($request, CURLOPT_PROXYPORT, $port);
        }

        $output = curl_exec($request);
        $error  = curl_error($request);

        if (!empty($error))
        {
            throw new iDEALConnector_Exceptions_ConnectorException($error);
        }

        curl_close($request);

        return $output;
    }
}