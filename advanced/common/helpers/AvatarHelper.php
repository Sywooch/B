<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 15/5/17 下午4:14
 * description:
 */

namespace common\helpers;

class AvatarHelper
{
    public $user_id;
    public $size;

    public function __construct($user_id, $size = 50)
    {
        $this->user_id = $user_id;
        $this->size = $size;
    }

    public function getAvater()
    {
        #todo 做缓存
        //var_dump($this->email, $this->size);exit;
        $identicon = new \Identicon\Identicon();
        return $identicon->getImageDataUri($this->user_id, $this->size);
    }

    /**
     * 根据 email 获取 gravatar 头像的地址
     * @return string
     */
    private function getGravatar()
    {
        $hash = md5(strtolower(trim($this->user_id)));
        return sprintf('http://gravatar.com/avatar/%s?s=%d&d=%s', $hash, $this->size, 'identicon');
    }

    /**
     * 验证email是否有对应的 Gravatar 头像（效率太低）
     * @return bool
     */
    private function validateGravatar()
    {
        $hash = md5(strtolower(trim($this->user_id)));
        $uri = 'http://gravatar.com/avatar/' . $hash . '?d=404';
        $headers = @get_headers($uri);
        if (!preg_match("|200|", $headers[0])) {
            return false;
        } else {
            return true;
        }
    }
}