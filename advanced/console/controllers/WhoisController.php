<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/20
 * Time: 16:56
 */

namespace console\controllers;


use common\components\Curl;
use common\components\Notifier;
use Yii;
use yii\console\Controller;

class WhoisController extends Controller
{
    const COOKIES = 'cur_location=https%3A%2F%2Fagent.dns.com.cn%2FAgent%2Findex.php%3Fdoaction%3D00040001; cur_menu_id=a000; PHPSESSID=hgijl2kh13qih4jb9p9ftau257; Hm_lvt_ec045aaa3b85d40ae96ff66af791ab31=1448010017; Hm_lpvt_ec045aaa3b85d40ae96ff66af791ab31=1448010017; PHPSESSID=oaarg1nsto1qiipdebl9ktpd45';
    
    public function actionIndex()
    {
        $domain = $this->buildDomainDict();
        $tail = ['cn', 'com', 'com.cn'];
        $response = $this->query($domain, $tail);
        
        $result = $this->checkResult($response);
        
        foreach ($result as $item) {
            Yii::trace(PHP_EOL . implode(PHP_EOL, $item) . PHP_EOL, 'domain');
        }
    }
    
    private function buildDomainDict()
    {
        $prefix = '';
        $suffix = 'cheng';


    }
    
    private function checkResult($response)
    {
        $result = [];
        foreach ($response as $item) {
            if (preg_match(
                '/<dl class=\'(.+?)\'><dt>(.+?)<\/dt><dd class=\'.+?\'>(.+?)<\/dd><\/dl>/',
                $item,
                $data
            )) {
                $status = $data[1] == 'disabled' ? 0 : 1;
                $data[2] = preg_replace('/&nbsp;<input.+?>/', '', $data[2]);
                $result[$status][] = sprintf("%s\t%s", $data[2], $data[3]);
            }
        }
        
        return $result;
    }
    
    
    private function query(array $domains, array $tails)
    {
        $curl = new Curl();
        $cookie = self::COOKIES;
        $response = [];
        foreach ($domains as $domain) {
            foreach ($tails as $tail) {
                $url = sprintf(
                    'https://agent.dns.com.cn/Agent/index.php?doaction=00030028&_=%d&sDomain=%s&sTail=.%s&sType=en&sTab=1',
                    time() . rand(100, 999),
                    $domain,
                    $tail
                );
                
                $curl->setCharset('gb2312')->nossl()->setCookies($cookie)->setHeader(
                    [
                        'Accept: */*',
                        'Accept-Language: zh-cn',
                        'Content-Type: application/x-www-form-urlencoded',
                        'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:10.0) Gecko/20100101 Firefox/10.0',
                        'Host: agent.dns.com.cn',
                        'Connection: Keep-Alive',
                        'Referer: https://agent.dns.com.cn/Agent/index.php?doaction=00030006',
                    ]
                );
                
                //echo $curl->responseCode, PHP_EOL;
                $response[] = $curl->get($url);
            }
        }
        
        return $response;
    }
}