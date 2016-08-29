<?php
/**
 * 简述
 *
 * 详细说明(可略)
 *
 * @copyright Copyright&copy; 2016, 
 * @author   zhanjuzhang <zhanjuzhang@gmail.com>
 * @version $Id: Encryption.php, v ${VERSION} 8/29/16 11:00 AM Exp $
 */

namespace Juzhang\Util;

class Encryption {
    private $skey            = "ieiskillingme"; // you can change it
    private $mcrypt_rijndael = MCRYPT_RIJNDAEL_256; // 密钥生成算法128,192,256
    private $mcrypt_mode     = MCRYPT_MODE_ECB;

    public function __construct($data = null) {
        if (isset($data['skey'])) {
            $this->skey = $data['skey'];
        }
        if (isset($data['mcrypt_rijndael'])) {
            $this->mcrypt_rijndael = $data['mcrypt_rijndael'];
        }
        if (isset($data['mcrtpt_mode'])) {
            $this->mcrtpt_mode = $data['mcrtpt_mode'];
        }
    }

    public function safeB64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(
            [
                '+',
                '/',
                '=',
            ],
            [
                '-',
                '_',
                '',
            ],
            $data
        );

        return $data;
    }

    public function safeB64decode($string) {
        $data = str_replace(
            [
                '-',
                '_',
            ],
            [
                '+',
                '/',
            ],
            $string
        );
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }

        return base64_decode($data);
    }

    public function encode($value) {
        if (!$value) {
            return false;
        }
        $text      = $value;
        $iv_size   = mcrypt_get_iv_size($this->mcrypt_rijndael, $this->mcrypt_mode);
        $iv        = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt($this->mcrypt_rijndael, $this->skey, $text, $this->mcrypt_mode, $iv);

        return _trim($this->safeB64encode($crypttext));
    }

    public function decode($value) {
        if (!$value) {
            return false;
        }
        $crypttext   = $this->safeB64decode($value);
        $iv_size     = mcrypt_get_iv_size($this->mcrypt_rijndael, $this->mcrypt_mode);
        $iv          = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt($this->mcrypt_rijndael, $this->skey, $crypttext, $this->mcrypt_mode, $iv);

        return _trim($decrypttext);
    }

}
