<?php
/*=============================================================================
#
#     FileName: testRequest.php
#         Desc:
#
#       Author: gavinyao
#        Email: gavinyao@tencent.com
#
#      Created: 2014-07-24 21:29:51
#      Version: 0.0.1
#      History:
#               0.0.1 | gavinyao | 2014-07-24 21:29:51 | initialization
#
=============================================================================*/

include realpath( dirname( __FILE__ ) ) . '/QcloudApiRequest.class.php';

define('SECRET_ID', 'YOUR_SECRET_ID');
define('SECRET_KEY', 'YOUR_SECRET_KEY');

define('OPENAPI_URL', 'cvm.api.qcloud.com');

$paramArray = array();
$paramArray['Nonce'] = mt_rand(1, time());
$paramArray['Region'] = 'gz';
$paramArray['Timestamp'] = time();

$paramArray['Action'] = 'DescribeInstances';

$ret = QcloudApiRequest::SendRequest($paramArray, SECRET_ID, SECRET_KEY, 'POST', OPENAPI_URL);

var_dump($ret);

