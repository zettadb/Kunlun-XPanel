<?php
/*
 * @Author: kinra
 * @Date: 2021-02-22 11:03:04
 * @LastEditTime: 2021-05-24 14:48:02
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * 
 * @Link: 官方接口文档:https://www.alibabacloud.com/help/zh/doc-detail/29439.htm?spm=a2c63.p38356.b99.49.a0f04809QRH48S
 */
class SendMail
{
    // DirectMail API 的服务接入地址：
    // 华东1：dm.aliyuncs.com
    // 亚太东南1（新加坡)：dm.ap-southeast-1.aliyuncs.com
    // 亚太东南2（悉尼）：dm.ap-southeast-2.aliyuncs.com
    private static $apiUrl = "http://dm.aliyuncs.com/?";

    // 管理控制台中配置的发信地址(必选, String)
    private static $AccountName = "发信地址";

    // 地址类型(必选, Integer) -- 0：为随机账号,1：为发信地址
    private static $AddressType = 1;

    // 使用管理控制台中配置的回信地址(必选, boolean)
    private static $ReplyToAddress = false;

    // 邮件主题(必选, String)
    public $Subject;

    // 目标地址(必选, String) -- 多个 email 地址可以用逗号分隔，最多100个地址。
    public $ToAddress;

    // 系统规定参数(非必选, String) -- 取值：SingleSendMail
    private static $Action = "SingleSendMail";

    // 是否打开数据跟踪功能(非必选, String) -- 1：开启,0：关闭(默认)
    private static $ClickTrace = 1;

    // 发信人昵称(非必选, String) -- 长度小于15个字符。
    public $FromAlias;

    // 邮件 html 正文(非必选, String) -- 限制28K。 
    public $HtmlBody;

    // 标签(非必选, String)
    public $TagName;

    // 邮件 text 正文(非必选, String) -- 限制28K。
    public $TextBody;

    // 阿里云颁发给用户的访问服务所用的密钥 ID。
    private static $AccessKeyId = "";

    //  阿里云颁发给用户的访问服务所用的密钥 Secret
    private static $AccessKeySecret = "";

    public function __construct($AccessKeyId, $AccessKeySecret, $AccountName)
    {
        self::$apiUrl = "https://dm.aliyuncs.com/?";
        self::$AccountName = $AccountName;
        self::$AddressType = 0;
        self::$ReplyToAddress = "false";
        self::$Action = "SingleSendMail";
        self::$ClickTrace = 1;
        self::$AccessKeyId = $AccessKeyId;
        self::$AccessKeySecret = $AccessKeySecret;

        $this->FromAlias = "";
        $this->Subject = "验证码";
        $this->ToAddress = "";
        $this->HtmlBody = "你好啊";
        $this->TagName = "";
        $this->TextBody = "你好啊";
    }

    /**
     * 发送邮件
     */
    public function send()
    {
        $data = [
            "Format" => "JSON",
            //返回值的类型，支持 JSON 与 XML。默认为 XML。
            "Version" => "2015-11-23",
            //API 版本号，为日期形式：YYYY-MM-DD。如果参数 RegionID 是cn-hangzhou，则版本对应为2015-11-23；如参数 RegionID 是cn-hangzhou 以外其他 Region，比如 ap-southeast-1，则版本对应为2017-06-22。
            "AccessKeyId" => self::$AccessKeyId,
            // "Signature" => base64_encode(hash_hmac("sha1", $str, $key, true)),
            "SignatureMethod" => "HMAC-SHA1",
            //签名方式，目前支持 HMAC-SHA1。
            "Timestamp" => gmdate(DATE_ATOM),
            //请求的时间戳。日期格式按照 ISO8601 标准表示，并需要使用 UTC 时间
            "SignatureVersion" => "1.0",
            //签名算法版本，目前版本是 1.0。
            "SignatureNonce" => time() . session_create_id(),
            //唯一随机数，用于防止网络重放攻击。不同的请求要使用不同的随机数值。您可以使用 UUID（随机串），或者自定义一个随机数。
            "RegionId" => "cn-hangzhou",
            //机房信息 ，目前支持 cn-hangzhou、ap-southeast-1、ap-southeast-2。

            "AccountName" => self::$AccountName,
            "AddressType" => self::$AddressType,
            "ReplyToAddress" => self::$ReplyToAddress,
            "Subject" => $this->Subject,
            "ToAddress" => $this->ToAddress,
            "Action" => self::$Action,
            "ClickTrace" => self::$ClickTrace,
            "FromAlias" => $this->FromAlias,
            "HtmlBody" => $this->HtmlBody,
            "TagName" => $this->TagName,
            "TextBody" => $this->TextBody,
        ];
        $stringToSign = $this->stringToSign("GET", $data);
        $data['Signature'] = $this->getSignature($stringToSign, self::$AccessKeySecret);

        $param = http_build_query($data, '', '&', PHP_QUERY_RFC3986); // 注意, http_build_query会将半角空格符号编码为加号 +, 而不是编码为 %20 ,此时会要将http_build_query 默认编码格式  PHP_QUERY_RFC1738  换为 PHP_QUERY_RFC3986
        return $this->doGet(self::$apiUrl . $param);
    }

    /**
     * 对请求参数进行排序和URL编码
     */
    public function stringToSign($http_method, array $data)
    {
        ksort($data);
        $CanonicalizedQueryString = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
        return $http_method . "&" . urlencode("/") . "&" . urlencode($CanonicalizedQueryString);
    }

    /**
     * 获取签名值（Signature）
     */
    public function getSignature($toSign, $encryptKey)
    {
        return base64_encode(hash_hmac("sha1", $toSign, $encryptKey . "&", true));
    }

    /**
     * 发送GET请求
     */
    public function doGet($url, $defaultHeader = true)
    {
        if ($defaultHeader) {
            $headerArray = array("Content-type:application/x-www-form-urlencoded;", "Accept:application/json");
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, true);
        return $output;
    }

    /**
     * 发送POST请求
     */
    public function doPost($url, $data)
    {
        $data = json_encode($data);
        $headerArray = array("Content-type:x-www-form-urlencoded;", "Accept:application/json");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output, true);
    }
}
// $object = new SendMail();
// $res = $object->send();
// var_dump($res);