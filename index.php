<?php
/**
 * @link http://github.com/hbhe
 * @copyright Copyright (c) 2016 hbhe
 * @wxtest is an MIT-licensed open source project
 * @author: 57620133@qq.com
 *
 * Usage:
 *
 */

$config = array(
    //'url' => 'http://wechat.mysite.com/',
    'url' => 'http://127.0.0.1/mkt/wechat/web/index.php?r=site',
    'gh_id' => 'gh_8510652496c4',
    'openid' => 'oD8xWwg-GJiFi9RLEllEzR1bwJ9A',
);

if (!empty($_POST["MsgType"])) {
    $c = new WxClient();
    $reqStr = null;
    $respStr = $c->send($_POST['url'], $_POST['gh_id'], $_POST['openid'], $_POST['MsgType'], $_POST['Event'], $_POST['EventKey'], $_POST['Content']);
    $respObj = simplexml_load_string($respStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    $respArr = json_decode(json_encode($respObj), true); 

    $reqObj = simplexml_load_string($reqStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    $reqArr = json_decode(json_encode($reqObj), true);
}

class WxClient
{
    public static function log($obj="", $log_file='')
    {
        if (is_array($obj))
            $str = print_r($obj, true);
        else if(is_object($obj))         
            $str = print_r($obj, true);                        
        else 
            $str = "{$obj}";

        if (empty($log_file))
            $log_file = __DIR__ .'/errors.log';
            
        $date = date("Y-m-d H:i:s");
        $log_str = sprintf("%s,%s\n",$date,$str);
        error_log($log_str, 3, $log_file);
    }

    public function send($url, $gh_id='gh_8510652496c4', $openid='oD8xWwg-GJiFi9RLEllEzR1bwJ9A', $MsgType='text', $Event='subscribe', $EventKey = 'MyEventKey', $Content='content')
    {
        global $reqStr;
        $msg = $this->getDemoRequestXml($gh_id, $openid, $MsgType, $Event, $EventKey);
        $reqStr = $msg;
        self::log($msg);

        $resp = self::sendDemoMessage($url, $msg);
        self::log($resp);
        return $resp;
    }

    public static function sendDemoMessage($url, $posts = []) {
        $curlOptions = [
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => is_string($posts) ? $posts : json_encode($posts),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1,            
        ];

        $curlResource = curl_init();
        foreach ($curlOptions as $option => $value) {
            curl_setopt($curlResource, $option, $value);
        }
        $response = curl_exec($curlResource);
        
        $responseHeaders = curl_getinfo($curlResource);

        $errorNumber = curl_errno($curlResource);
        $errorMessage = curl_error($curlResource);

        curl_close($curlResource);

        if ($errorNumber > 0) {
            throw new \Exception('Curl error requesting "' .  $url . '": #' . $errorNumber . ' - ' . $errorMessage);
        }
        if (strncmp($responseHeaders['http_code'], '20', 2) !== 0) {
            throw new \Exception('Request failed with code: ' . $responseHeaders['http_code'] . ', message: ' . $response);
        }

        return $response;
    }

    /*
    $MsgType: 'text','image','location','link','event','music','news','voice','video','shortvideo'
    $Event:'subscribe','unsubscribe','SCAN','LOCATION','CLICK','VIEW'    
    */
    public function getDemoRequestXml($gh_id='gh_id', $openid='openid', $MsgType='text', $Event='subscribe', $EventKey = 'MyEventKey') 
    {
        $time = time();
        switch ($MsgType) 
        {
            case 'text':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>1402545118</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[.debug]]></Content>
<MsgId>6023885413174756692</MsgId>
</xml>
EOD;
                break;

            case 'image':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>1402716823</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<PicUrl><![CDATA[http://mmbiz.qpic.cn/mmbiz/sfPia9sGialANxsfkib9L3pLolJcbrtXkkPFxRUNFeTry12vibeDHOhIibvDmVquhPIiboSbv0tm6GRO7UU7tkAQEXTA/0]]></PicUrl>
<MsgId>6024622880534320141</MsgId>
<MediaId><![CDATA[1HxU1hdTALQ2KwCxEUz7RvVWYKiCZxYCWLovam3GT19KHd1G_gDCUlc6tuOIKq1f]]></MediaId>
</xml>
EOD;
                break;

            case 'location':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>1402716680</CreateTime>
<MsgType><![CDATA[location]]></MsgType>
<Location_X>30.512074</Location_X>
<Location_Y>114.315926</Location_Y>
<Scale>16</Scale>
<Label><![CDATA[wuchang district wuhan,hubei province, china]]></Label>
<MsgId>6024622266353996807</MsgId>
</xml>
EOD;
                break;

            case 'link':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>1351776360</CreateTime>
<MsgType><![CDATA[link]]></MsgType>
<Title><![CDATA[title]]></Title>
<Description><![CDATA[desc]]></Description>
<Url><![CDATA[http://baidu.com]]></Url>
<MsgId>1234567890123456</MsgId>
</xml>
EOD;
                break;

            case 'voice':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>1402716535</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
<MediaId><![CDATA[bFvd4vTiEb89CpfKVg8AsKJOBNSU0m3kZtL2pxnx4mSgQMvqo9EDHNKAyU6ZsUre]]></MediaId>
<Format><![CDATA[amr]]></Format>
<MsgId>6024621643583738876</MsgId>
<Recognition><![CDATA[voice text]]></Recognition>
</xml>                
EOD;
                break;

            case 'video':    
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>1402717029</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
<MediaId><![CDATA[MP_AE2Ofqe-YPzwHjgsI8zm5ScOz5nh34JfnfTsY52UimfvFaOssz_exTtIxnzCQ]]></MediaId>
<ThumbMediaId><![CDATA[X6bSlsb_sVIZnsDFJHsA36KI5XIdZIhAt0i6r1aPKCYdlFQzjpJXp-eHmBRGfBMx]]></ThumbMediaId>
<MsgId>6024623765297583125</MsgId>
</xml>
EOD;
                break;

            case 'event':
                switch ($Event) 
                {
                    case 'subscribe':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
</xml>
EOD;
                        break;

                    case 'unsubscribe':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[unsubscribe]]></Event>
</xml>                
EOD;
                        break;

                    case 'SCAN':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[SCAN]]></Event>
<EventKey><![CDATA[$EventKey]]></EventKey>
<Ticket><![CDATA[TICKET]]></Ticket>
</xml>
EOD;
                        break;

                    case 'LOCATION':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[LOCATION]]></Event>
<Latitude>23.137466</Latitude>
<Longitude>113.352425</Longitude>
<Precision>119.385040</Precision>
</xml>
EOD;
                        break;

                    case 'CLICK':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[CLICK]]></Event>
<EventKey><![CDATA[$EventKey]]></EventKey>
</xml>
EOD;
                        break;

                    case 'VIEW':
                $xml = <<<EOD
<xml>
<ToUserName><![CDATA[$gh_id]]></ToUserName>
<FromUserName><![CDATA[$openid]]></FromUserName>
<CreateTime>123456789</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[VIEW]]></Event>
<EventKey><![CDATA[$EventKey]]></EventKey>
</xml>
EOD;
                        break;
                }
                break;
                
            default:
                die('Invalid Demo MsgType');
                break;                
        }
        return $xml;
    }
    
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>模拟微信服务器向开发者服务器发消息</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body style="background-color: #7e7e7e">
<div class="row">
<div class="col-sm-6 col-sm-offset-1">
    <div class="well" style="margin-top: 10%;">
    <h3>向开发者服务器发微信消息包</h3>

    <form role="form" method="post" action="index.php" id="contactForm" data-toggle="validator" class="shake">

        <div class="hide1">
        <div class="form-group">
            <label for="url" class="h4">收包地址(URL)</label>
            <input type="text" class="form-control" name="url" id="url" placeholder="Enter URL" required data-error="ERROR URL" value="<?php echo $config['url'] ?> ">
            <div class="help-block with-errors"></div>
        </div>

        <div class="row">
            <div class="form-group col-sm-6">
                <label for="gh_id" class="h4">GHID</label>
                <input type="text" class="form-control" name="gh_id" id="gh_id" placeholder="Enter GHID" required data-error="ERROR GHID" value="<?php echo $config['gh_id'] ?> ">
                <div class="help-block with-errors"></div>
            </div>

            <div class="form-group col-sm-6">
                <label for="openid" class="h4">OpenID</label>
                <input type="text" class="form-control" name="openid" id="openid" placeholder="Enter OpenID" required data-error="ERROR OpenID" value="<?php echo $config['openid'] ?> ">
                <div class="help-block with-errors"></div>
            </div>
        </div>
        </div>

        <div class="row">
            <div class="form-group col-sm-6">
                <label for="MsgType" class="h4">MsgType</label>
                <select class="form-control" name="MsgType" id="MsgType">
                    <option value="text">text</option>
                    <option value="event">event</option>
                    <option value="image">image</option>
                    <option value="location">location</option>
                    <option value="link">link</option>
                    <option value="music">music</option>
                    <option value="news">news</option>
                    <option value="voice">voice</option>
                    <option value="video">video</option>
                    <option value="shortvideo">shortvideo</option>
                </select>
                <div class="help-block with-errors"></div>
            </div>

            <div class="form-group col-sm-6">
                <label for="Event" class="h4">Event</label>
                <select class="form-control" name="Event" id="Event">
                    <option value="subscribe">subscribe</option>
                    <option value="unsubscribe">unsubscribe</option>
                    <option value="SCAN">SCAN</option>
                    <option value="LOCATION">LOCATION</option>
                    <option value="CLICK">CLICK</option>
                    <option value="VIEW">VIEW</option>
                </select>
                <div class="help-block with-errors"></div>
            </div>
        </div>

        <div class="form-group">
            <label for="EventKey" class="h4">EventKey</label>
            <input type="text" class="form-control" name="EventKey" id="EventKey" placeholder="Enter EventKey">
            <div class="help-block with-errors"></div>
        </div>

        <div class="form-group">
            <label for="Content" class="h4">Content</label>
            <textarea name="Content" id="Content" class="form-control" rows="2" placeholder="Enter your Content" required></textarea>
            <div class="help-block with-errors"></div>
        </div>

        <button type="submit" id="form-submit" class="btn btn-success btn-lg pull-right ">发射...</button>
        <div id="msgSubmit" class="text-left"></div>
        <div class="clearfix"></div>
    </form>
    </div>
</div>

<?php if (!empty($_POST["MsgType"])): ?>
<div class="col-sm-4">
    <div class="well" style="margin-top: 10%;">
        <h3>请求</h3>
        <div style="height:300px;">
            <pre><?php print_r($reqArr); ?></pre>
        </div>
        
        <h3>响应</h3>
        <div style="height:300px;">
            <pre><?php print_r($respArr); ?></pre>
        </div>
    </div>
</div>
<?php endif; ?>

</div>
</body>
<script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
<!--
<script type="text/javascript" src="js/validator.min.js"></script>
-->
</html>
