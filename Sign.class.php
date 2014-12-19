<?php
/*=============================================================================
#
#     FileName: Sign.class.php
#         Desc: v2 版签名
#
#       Author: gavinyao
#        Email: gavinyao@tencent.com
#
#      Created: 2014-07-18 14:24:57
#      Version: 0.0.1
#      History:
#               0.0.1 | gavinyao | 2014-07-18 14:24:57 | initialization
#
=============================================================================*/

class Sign
{
    public function __construct()
    {
    }

    public static function getEncryptMethodList()
    {
        return hash_algos();
    }

    /**
     * @brief 加密字符串, 目前只支持, HmacSHA1 一种加密算法
     * @author gavinyao@tencent.com
     * @date 2014-07-18 15:01:14
     *
     * @param $srcStr 原文
     * @param $secretKey 密钥
     * @param $method 加密算法
     *
     * @return 密文
     */
    public static function sign($srcStr, $secretKey, $method = 'HmacSHA1')
    {
        switch ($method) {
        case 'HmacSHA1':
            $retStr = base64_encode(hash_hmac('sha1', $srcStr, $secretKey, true));
            break;
            // case 'HmacSHA256':
            // $retStr = base64_encode(hash_hmac('sha256', $srcStr, $secretKey, true));
            // break;
        default:
            throw new Exception($method . ' is not a supported encrypt method');
            return false;
            break;
        }

        return $retStr;
    }

    /**
     * @brief 根据 请求参数, 请求方法等信息拼接签名字符串
     * @author gavinyao@tencent.com
     * @date 2014-07-24 20:43:35
     *
     * @param $requestParams
     * @param $requestMethod
     * @param $requestHost
     * @param $requestPath
     *
     * @return
     */
    public static function makeSignPlainText($requestParams,
        $requestMethod = 'GET', $requestHost = YUNAPI_URL,
        $requestPath = '/v2/index.php')
    {

        $url = $requestHost . $requestPath;

        // 取出所有的参数
        $paramStr = self::_buildParamStr($requestParams, $requestMethod);

        $plainText = $requestMethod;
        $plainText .= $url;
        $plainText .= $paramStr;

        return $plainText;
    }

    /**
     * @brief 根据请求参数拼接签名串
     * @author gavinyao@tencent.com
     * @date 2014-07-24 20:44:49
     *
     * @param $requestParams
     * @param $requestMethod
     *
     * @return
     */
    protected static function _buildParamStr($requestParams, $requestMethod = 'GET')
    {
        $paramStr = '';
        ksort($requestParams);
        $i = 0;
        foreach($requestParams as $key=>$value)
        {

            if(is_null($value))
            {
                continue;
            }

            if($key == 'Signature')
            {
                continue;
            }

            // 排除上传文件的参数
            if ($requestMethod == 'POST' && substr($value, 0, 1) == '@') {
                continue;
            }

            // 把 参数中的 _ 替换成 .
            if (strpos($key, '_'))
            {
                $key = str_replace('_', '.', $key);
            }

            if ($i == 0)
            {
                $paramStr .= '?';
            }
            else
            {
                $paramStr .= '&';
            }
            $paramStr .= $key . '=' . $value;
            ++$i;
        }

        return $paramStr;
    }

}

