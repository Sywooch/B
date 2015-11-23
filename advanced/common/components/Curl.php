<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/20
 * Time: 17:28
 */

namespace common\components;

use \linslin\yii2\curl\Curl as BaseCurl;
use Yii;

class Curl extends BaseCurl
{
    public $charset;
    public $cookie_file;
    public $symbol;

    public function __construct($symbol = null)
    {
        $this->symbol = $symbol ? $symbol : md5(microtime(true) . rand(0, 99999));
    }

    private $_defaultOptions = [
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_1_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Mobile/12B466',
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_FOLLOWLOCATION => false, //不自动跳转
        CURLOPT_MAXREDIRS      => 5,
        CURLINFO_HEADER_OUT    => 1,
    ];

    public function nossl()
    {
        $this->setOption(CURLOPT_SSL_VERIFYHOST, 0);
        $this->setOption(CURLOPT_SSL_VERIFYPEER, 0);

        return $this;
    }

    public function setHeader($header)
    {
        $this->setOption(CURLOPT_HTTPHEADER, $header);

        return $this;
    }

    public function setCookies($cookies)
    {
        $this->setOption(CURLOPT_COOKIE, $cookies);
        $this->cookie_file = Yii::getAlias(
                '@frontend'
            ) . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $this->symbol;

        return $this;
    }

    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
        
        return $this;
    }

    public function setCharset($charset)
    {
        $this->charset = ($charset == 'utf-8') ? null : $charset;

        return $this;
    }

    public function getAndSaveCookie($url)
    {
        if ($this->cookie_file) {
            $this->setOption(CURLOPT_COOKIEFILE, $this->cookie_file);
        }


        return $this->get($url);
    }

    public function postAndSaveCookie($url, $data)
    {
        if ($this->cookie_file) {
            $this->setOption(CURLOPT_COOKIEFILE, $this->cookie_file);
        }

        $data = is_array($data) ? http_build_query($data) : $data;
        $this->setOptions(
            [
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_POST       => true,
            ]
        );

        return $this->post($url, $data);
    }

    private function _httpRequest($method, $url, $raw = false)
    {
        //set request type and writer function
        $this->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));

        //check if method is head and set no body
        if ($method === 'HEAD') {
            $this->setOption(CURLOPT_NOBODY, true);
            $this->unsetOption(CURLOPT_WRITEFUNCTION);
        }

        //setup error reporting and profiling
        Yii::trace('Start sending cURL-Request: ' . $url . '\n', __METHOD__);
        Yii::beginProfile(
            $method . ' ' . $url . '#' . md5(serialize($this->getOption(CURLOPT_POSTFIELDS))),
            __METHOD__
        );

        /**
         * proceed curl
         */
        $curl = curl_init($url);
        curl_setopt_array($curl, $this->getOptions());
        $body = curl_exec($curl);

        //check if curl was successful
        if ($body === false) {
            switch (curl_errno($curl)) {

                case 7:
                    $this->responseCode = 'timeout';

                    return false;
                    break;

                default:
                    throw new Exception('curl request failed: ' . curl_error($curl), curl_errno($curl));
                    break;
            }
        }

        //retrieve response code
        $this->responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (!empty($this->charset)) {
            $body = mb_convert_encoding($body, 'utf-8', $this->charset);
        }
        $this->response = $body;

        //stop curl
        curl_close($curl);

        //end yii debug profile
        Yii::endProfile($method . ' ' . $url . '#' . md5(serialize($this->getOption(CURLOPT_POSTFIELDS))), __METHOD__);

        //check responseCode and return data/status
        if ($this->getOption(CURLOPT_CUSTOMREQUEST) === 'HEAD') {
            return true;
        } else {
            $this->response = $raw ? $this->response : Json::decode($this->response);

            return $this->response;
        }
    }

}