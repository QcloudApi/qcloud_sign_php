<?php
/*=============================================================================
#
#     FileName: Qcloudapirequest.php
#         Desc:
#
#       Author: gavinyao
#        Email: gavinyao@tencent.com
#
#      Created: 2014-07-24 20:54:48
#      Version: 0.0.1
#      History:
#               0.0.1 | gavinyao | 2014-07-24 20:54:48 | initialization
#
=============================================================================*/

include realpath( dirname( __FILE__ ) ) . '/Sign.class.php';
class QcloudApiRequest
{
    protected static $_requestUrl = '';

    protected static $_rawResponse = '';

    public function __construct()
    {
    }

    public static function getRequestUrl()
    {
        return self::$_requestUrl;
    }

    public static function getRawResponse()
    {
        return self::$_rawResponse;
    }

    public static function SendRequest($paramArray, $secretId, $secretKey,
        $requestMethod = 'POST', $requestHost = YUNAPI_URL,
        $requestPath = '/v2/index.php', $https = true)
    {
        if(!isset($paramArray['SecretId']))
        {
            $paramArray['SecretId'] = $secretId;
        }

        $plainText = Sign::makeSignPlainText($paramArray,
            $requestMethod, $requestHost, $requestPath);

        $sign = Sign::sign($plainText, $secretKey);

        $paramArray['Signature'] = $sign;

        if ($https) {
            $url = 'https://' . $requestHost . $requestPath;
        } else {
            $url = 'http://' . $requestHost . $requestPath;
        }
        $ret = self::_sendRequest($url, $paramArray, $requestMethod);

        return $ret;
    }

    protected static function _sendRequest($url, $paramArray, $method = 'POST')
    {

        $ch = curl_init();

        if ($method == 'POST')
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramArray);
        }
        else
        {
            $url .= '?' . http_build_query($paramArray);
        }

        self::$_requestUrl = $url;

        curl_setopt($ch, CURLOPT_URL, $url);
        echo('url = ' . $url . PHP_EOL);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (false !== strpos($url, "https")) {
            // 证书
            // curl_setopt($ch,CURLOPT_CAINFO,"ca.crt");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,  false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);
        }
        $resultStr = curl_exec($ch);

        var_dump($resultStr);
        self::$_rawResponse = $resultStr;

        $result = json_decode($resultStr, true);
        if (!$result)
        {
            return $resultStr;
        }
        return $result;
    }

}

