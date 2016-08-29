<?php
/**
 * 简述
 *
 * 详细说明(可略)
 *
 * @copyright Copyright&copy; 2016, 
 * @author   zhanjuzhang <zhanjuzhang@gmail.com>
 * @version $Id: Des.php, v ${VERSION} 8/29/16 11:00 AM Exp $
 */

namespace Juzhang\Util;

class Des {

    public static function encrypt($key, $text) {
        $size = mcrypt_get_block_size('des', 'ecb');
        $text = self::pkcs5Pad($text, $size);
        $td   = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv   = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $text);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);

        return $data;
    }

    public static function decrypt($key, $encrypted) {
        $encrypted = base64_decode($encrypted);
        $td        = mcrypt_module_open('des', '', 'ecb', '');
        $iv        = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks        = mcrypt_enc_get_key_size($td);
        mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $encrypted);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $plain_text = self::pkcs5Unpad($decrypted);

        return $plain_text;
    }

    public function encryptUngeneric($key, $text) {
        $iv               = mcrypt_create_iv(mcrypt_get_iv_size('tripledes', MCRYPT_MODE_ECB), MCRYPT_RAND);
        $encrypted_string = mcrypt_encrypt('tripledes', $key, $text, MCRYPT_MODE_ECB, $iv);
        $des3             = bin2hex($encrypted_string);

        return $des3;
    }

    public function decryptUngeneric($key, $encrypted) {
        $encrypted_string = @pack("H*", $encrypted);
        $iv               = mcrypt_create_iv(mcrypt_get_iv_size('tripledes', MCRYPT_MODE_ECB), MCRYPT_RAND);
        $plain_txt        = mcrypt_decrypt('tripledes', $key, $encrypted_string, MCRYPT_MODE_ECB, $iv);

        return $plain_txt;
    }

    public static function pkcs5Pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text . str_repeat(chr($pad), $pad);
    }

    public static function pkcs5Unpad($text) {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }

        return substr($text, 0, -1 * $pad);
    }

}