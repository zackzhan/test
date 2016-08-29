<?php

function getCurrentTime() {
    if (!$_SERVER['REQUEST_TIME']) {
        $time = time();
    } else {
        $time = $_SERVER['REQUEST_TIME'];
    }

    return $time;
}

/**
 * HTTP Protocol defined status codes
 *
 * @param int $num
 */
function httpStatus($num) {
    static $http = [
        100 => "HTTP/1.1 100 Continue",
        101 => "HTTP/1.1 101 Switching Protocols",
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        203 => "HTTP/1.1 203 Non-Authoritative Information",
        204 => "HTTP/1.1 204 No Content",
        205 => "HTTP/1.1 205 Reset Content",
        206 => "HTTP/1.1 206 Partial Content",
        300 => "HTTP/1.1 300 Multiple Choices",
        301 => "HTTP/1.1 301 Moved Permanently",
        302 => "HTTP/1.1 302 Found",
        303 => "HTTP/1.1 303 See Other",
        304 => "HTTP/1.1 304 Not Modified",
        305 => "HTTP/1.1 305 Use Proxy",
        307 => "HTTP/1.1 307 Temporary Redirect",
        400 => "HTTP/1.1 400 Bad Request",
        401 => "HTTP/1.1 401 Unauthorized",
        402 => "HTTP/1.1 402 Payment Required",
        403 => "HTTP/1.1 403 Forbidden",
        404 => "HTTP/1.1 404 Not Found",
        405 => "HTTP/1.1 405 Method Not Allowed",
        406 => "HTTP/1.1 406 Not Acceptable",
        407 => "HTTP/1.1 407 Proxy Authentication Required",
        408 => "HTTP/1.1 408 Request Time-out",
        409 => "HTTP/1.1 409 Conflict",
        410 => "HTTP/1.1 410 Gone",
        411 => "HTTP/1.1 411 Length Required",
        412 => "HTTP/1.1 412 Precondition Failed",
        413 => "HTTP/1.1 413 Request Entity Too Large",
        414 => "HTTP/1.1 414 Request-URI Too Large",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        416 => "HTTP/1.1 416 Requested range not satisfiable",
        417 => "HTTP/1.1 417 Expectation Failed",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
        502 => "HTTP/1.1 502 Bad Gateway",
        503 => "HTTP/1.1 503 Service Unavailable",
        504 => "HTTP/1.1 504 Gateway Time-out",
    ];
    header($http[$num]);
}

function _jsonEncode($data) {
    header("Content-type: application/json; charset=utf-8");

    return json_encode($data, JSON_UNESCAPED_UNICODE);
}

function returnErrorData($error_code, $error) {
    $data['error_code'] = $error_code;
    $data['error']      = urlencode($error);
    $url_decode_flag    = true;

    return apiReturnOutput($data, $url_decode_flag);
}

function writeIniFile($path, $assoc_array) {
    $content = '';
    foreach ($assoc_array as $key => $item) {
        if (is_array($item)) {
            $content .= "\n[{$key}]\n";
            foreach ($item as $key2 => $item2) {
                if (is_numeric($item2) || is_bool($item2)) {
                    $content .= "{$key2} = {$item2}\n";
                } else {
                    $content .= "{$key2} = \"{$item2}\"\n";
                }
            }
        } else {
            if (is_numeric($item) || is_bool($item)) {
                $content .= "{$key} = {$item}\n";
            } else {
                $content .= "{$key} = \"{$item}\"\n";
            }
        }
    }
    if (!$handle = fopen($path, 'w')) {
        return false;
    }

    if (!fwrite($handle, $content)) {
        return false;
    }

    fclose($handle);

    return true;
}

function apiReturnSuccess($outputData = []) {
    $data['code']    = 200;
    $data['message'] = 'success';
    $data['data']    = $outputData;
    $url_decode_flag = false;

    return apiReturnOutput($data, $url_decode_flag);
}

function apiReturnError($error_code, $params = [], $error = null) {
    $error           = $error ? $error : L($error_code, $params);
    $data['code']    = intval($error_code);
    $data['message'] = urlencode($error);
    $data['request'] = '';
    $url_decode_flag = true;

    return apiReturnOutput($data, $url_decode_flag);
}

function apiReturnOutput($data, $url_decode_flag = false) {
    $return_data = _jsonEncode($data);
    if ($url_decode_flag) {
        $return_data = urldecode($return_data);
    }

    return $return_data;
}

function L($language = '', $params = [], $path) {
    static $LANG = [];
    if (!$LANG) {
        if (!file_exists($path)) {
            return $language;
        }
        require_once $path;
    }
    if (!array_key_exists($language, $LANG)) {
        return $language;
    } else {
        $language = $LANG[$language];
        if ($params) {
            $language = vsprintf($language, $params);
        }

        return $language;
    }
}

function curlGetContents($url, $timeout = 3) {
    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0); // 让CURL支持HTTPS访问
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, $timeout);
    $result = curl_exec($curlHandle);
    curl_close($curlHandle);

    return $result;
}

function curlPostContents($url, $params, $use_http_build_query = true) {
    if ($use_http_build_query) {
        $params = http_build_query($params);
    }

    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_POST, 1);
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0); // 让CURL支持HTTPS访问
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $params);
    $result = curl_exec($curlHandle);
    curl_close($curlHandle);

    return $result;
}

function bigIntVal($value) {
    $value = trim($value);
    if (ctype_digit($value)) {
        return $value;
    }
    $value = preg_replace("/[^0-9](.*)$/", '', $value);
    if (ctype_digit($value)) {
        return $value;
    }

    return 0;
}

/**
 * 将当前字符串从 BeginString 向右截取
 *
 * @param string $String
 * @param string $BeginString
 * @param boolean $self
 * @return String
 */
function rightString($String, $BeginString, $self = false) {
    $Start = strpos($String, $BeginString);
    if ($Start === false) {
        return null;
    }
    if (!$self) {
        $Start += strlen($BeginString);
    }
    $newString = substr($String, $Start);

    return $newString;
}

/**
 * 将当前字符串从 BeginString 向左截取
 *
 * @param string $String
 * @param string $BeginString
 * @param boolean $self
 * @return String
 */
function leftString($BeginString, $String, $self = false) {
    $Start = strpos($String, $BeginString);
    if ($Start === false) {
        return null;
    }
    if ($self) {
        $Start += strlen($BeginString);
    }
    $newString = substr($String, 0, $Start);

    return $newString;
}

// 去除首尾全角及半角空格,多个空格合并为一个
function _trim($str) {
    $str = preg_replace('/( |　|\r\n|\r|\n)+/', ' ', $str);

    return trim(preg_replace("/^　+|　+$/ ", " ", $str));
}

// 帖子的空格特殊处理
function trimSpecialSpace($content) {
    $content = trim($content, " ");

    return trim($content);
}

function subString($String, $BeginString, $EndString = null) {
    $Start = strpos($String, $BeginString);
    if ($Start === false) {
        return null;
    }
    $Start += strlen($BeginString);
    $String = substr($String, $Start);
    if (!$EndString) {
        return $String;
    }
    $End = strpos($String, $EndString);
    if ($End == false) {
        return null;
    }

    return substr($String, 0, $End);
}

function _mkdir($dir) {
    if (file_exists($dir)) {
        return true;
    }
    $u = umask(0);
    $r = @mkdir($dir, 0755);
    umask($u);

    return $r;
}

function _mkdirs($dir, $rootpath = '') {
    if ($rootpath == '.') {
        $rootpath = realpath($rootpath);
    }
    $forlder = explode('/', $dir);
    $path    = '';
    for ($i = 0; $i < count($forlder); $i++) {
        if ($current_dir = trim($forlder[$i])) {
            if ($current_dir == '.') {
                continue;
            }
            $path .= '/' . $current_dir;
            if ($current_dir == '..') {
                continue;
            }
            if (file_exists($rootpath . $path)) {
                @chmod($rootpath . $path, 0755);
            } else {
                if (!_mkdir($rootpath . $path)) {
                    return false;
                }
            }
        }
    }

    return true;
}

function isEmail($email) {
    return preg_match('/^\w[_\-\.\w]+@\w+\.([_-\w]+\.)*\w{2,4}$/', $email);
}

function isMobile($phone) {
    return preg_match("/^1\d{10}$/", $phone);
}

function isDateValid($str) {
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $str)) {
        return false;
    }
    $stamp = strtotime($str);
    if (!is_numeric($stamp)) {
        return false;
    }
    if (checkdate(date('m', $stamp), date('d', $stamp), date('Y', $stamp))) {
        return true;
    }

    return false;
}

/**
 * 匹配正整数
 * @param $mixed
 * @return bool
 */
function isIntVal($mixed) {
    return (preg_match('/^\d+$/', $mixed) == 1);
}

function getIP() {
    $onlineip = null;
    if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    }

    preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
    $onlineip = $onlineipmatches[0] ? $onlineipmatches[0] : null;
    unset($onlineipmatches);

    return $onlineip;
}

function getPaginate($limit, $total, $page, $step = 10) {
    $Multi['Page']      = max(1, $page);
    $Multi['PageSize']  = $limit;
    $Multi['RecordNum'] = $total;
    $Multi['start_num'] = ($page - 1) * $limit + 1;
    $Multi['end_num']   = $page * $limit;
    if ($Multi['end_num'] > $total) {
        $Multi['end_num'] = $total;
    }
    $Multi['PageCount'] = ceil($Multi['RecordNum'] / $Multi['PageSize']);
    paginate($Multi, $step);

    return $Multi;
}

function paginate(&$multipages, $n = 10) {
    $page_step = ceil($n / 2);
    if ($multipages['Page'] - $page_step > 0) {
        $multipages['FirstPage'] = 1;
    }
    if ($multipages['Page'] - 1 > 0) {
        $multipages['BackPage'] = $multipages['Page'] - 1;
    }
    if ($multipages['Page'] < $multipages['PageCount']) {
        $multipages['NextPage'] = ($multipages['Page'] + 1);
    }
    if ($multipages['Page'] + $n < $multipages['PageCount']) {
        $multipages['LastPage'] = $multipages['PageCount'];
    }
    $n   = $n - 1;
    $min = ($multipages['Page'] - $page_step) > 0 ? $multipages['Page'] - $page_step : 1;
    $max = ($min + $n) < $multipages['PageCount'] ? ($min + $n) : $multipages['PageCount'];
    for ($i = $min; $i <= $max; $i++) {
        $multipages['PageNums'][$i] = $i;
    }
}

function checkCharLength($string, $min_length, $max_length = '', $encode = 'UTF-8') {
    if (!$encode) {
        return false;
    }
    $length = (strlen($string) + mb_strlen($string, $encode)) / 2;
    if (!isIntVal($max_length)) {
        if ($length >= $min_length) {
            return true;
        }

        return false;
    }
    if ($length >= $min_length && $length <= $max_length) {
        return true;
    } else {
        return false;
    }
}

/**
 * 不区分中英文，任何字符都算1个长度
 * @param $string
 * @param $min_length
 * @param string $max_length
 * @param string $encode
 * @return bool
 */
function checkNotMixedCharLength($string, $min_length, $max_length = '', $encode = 'UTF-8') {
    if (!$encode) {
        return false;
    }
    $length = mb_strlen($string, $encode);
    if (!isIntVal($max_length)) {
        if ($length >= $min_length) {
            return true;
        }

        return false;
    }
    if ($length >= $min_length && $length <= $max_length) {
        return true;
    } else {
        return false;
    }
}

/**
 * 数组是否为整型数组
 * @param $ids_array
 * @return bool
 */
function checkIntVal($ids_array) {
    if (!is_array($ids_array) || !$ids_array) {
        return false;
    }
    foreach ($ids_array as $id) {
        if (!isIntVal($id)) {
            return false;
        }
    }

    return true;
}

function checkFloat($ids_array) {
    if (!is_array($ids_array) || !$ids_array) {
        return false;
    }
    foreach ($ids_array as $id) {
        if (!is_numeric($id)) {
            return false;
        }
    }

    return true;
}

/**
 * 返回一定位数的时间戳，多少位由参数决定
 * @param bool $digits 多少位的时间戳
 * @return int|string 时间戳
 */
function getTimestamp($digits = false) {
    $digits = $digits > 10 ? $digits : 10;
    $digits = $digits - 10;
    if ((!$digits) || ($digits == 10)) {
        return time();
    } else {
        return number_format(microtime(true), $digits, '', '');
    }
}


function getDateByUnixTime($time, $format = 'Y-m-d H:i:s') {
    if ($time) {
        return date($format, $time);
    }

    return false;
}

/**
 * 多字符串截取
 *
 * @author zhanjuzhang
 * @param        $string
 * @param int $length
 * @param string $dot
 * @param bool $htmlencode
 * @param string $charset
 * @return string
 */
function mbcutstr($string, $length = 20, $dot = '...', $htmlencode = true, $charset = 'UTF-8') {
    if (mb_strlen($string, $charset) <= $length) {
        if ($htmlencode) {
            return htmlspecialchars($string);
        } else {
            return $string;
        }
    }

    $dot_len = mb_strlen($dot);
    $strcut  = mb_strcut($string, 0, $length - $dot_len, $charset);

    if ($htmlencode) {
        $strcut = htmlspecialchars($strcut);
    }

    return $strcut . $dot;
}

function cutstr($string, $length = 20, $dot = '...', $htmlencode = true, $charset = 'utf-8') {
    if (strlen($string) <= $length) {
        if ($htmlencode) {
            return htmlspecialchars($string);
        } else {
            return $string;
        }
    }
    $strcut = '';
    if (strtolower($charset) == 'utf-8') {
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t < 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }
    $original_strlen = strlen($string);
    $new_strlen      = strlen($strcut);
    if ($htmlencode) {
        $strcut = htmlspecialchars($strcut);
    }

    return $strcut . ($original_strlen > $new_strlen ? $dot : '');
}

function getImageUrl($relative_path, $prefix_path) {
    if (!empty($relative_path)) {
        if (is_array($relative_path)) {
            $result = '';
            foreach ($relative_path as $item) {
                $result[] = $prefix_path . '/' . $item;
            }

            return $result;
        } else {
            if (strpos($relative_path, 'http') === 0) {
                return $relative_path;
            }

            return $prefix_path . '/' . $relative_path;
        }
    }

    return '';
}

function highLight($text, $words, $prepend) {
    $text = str_replace('\"', '"', $text);
    $text = str_replace(
        [
            ' ',
            ' ',
        ],
        [
            '',
            '',
        ],
        $text
    );
    $text = preg_replace("/\s(?=\s)/", "\\1", $text);

    if (!is_array($words)) {
        $words = [
            $words,
        ];
    }

    foreach ($words as $key => $replaceword) {
        // $text = str_ireplace($replaceword,
        // '<highlight>'.$replaceword.'</highlight>', $text);
        $text = preg_replace("/(" . $replaceword . ")/isU", '<highlight>\\1</highlight>', $text);
    }

    return "$prepend$text";
}

/**
 * 过滤xss攻击
 * @param $val
 * @return mixed
 */
function remove_xss($val) {
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08\x0b-\x0c\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    //$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra1 = [
        'javascript',
        'vbscript',
        'expression',
        'applet',
        'xml',
        'blink',
        'link',
        'style',
        'script',
        'embed',
        'object',
        'iframe',
        'frame',
        'frameset',
        'ilayer',
        'layer',
        'bgsound',
        'title',
        'base',
    ];//去掉了meta标签防止跟公司的metal系列相同而误处理
    $ra2 = [
        'onabort',
        'onactivate',
        'onafterprint',
        'onafterupdate',
        'onbeforeactivate',
        'onbeforecopy',
        'onbeforecut',
        'onbeforedeactivate',
        'onbeforeeditfocus',
        'onbeforepaste',
        'onbeforeprint',
        'onbeforeunload',
        'onbeforeupdate',
        'onblur',
        'onbounce',
        'oncellchange',
        'onchange',
        'onclick',
        'oncontextmenu',
        'oncontrolselect',
        'oncopy',
        'oncut',
        'ondataavailable',
        'ondatasetchanged',
        'ondatasetcomplete',
        'ondblclick',
        'ondeactivate',
        'ondrag',
        'ondragend',
        'ondragenter',
        'ondragleave',
        'ondragover',
        'ondragstart',
        'ondrop',
        'onerror',
        'onerrorupdate',
        'onfilterchange',
        'onfinish',
        'onfocus',
        'onfocusin',
        'onfocusout',
        'onhelp',
        'onkeydown',
        'onkeypress',
        'onkeyup',
        'onlayoutcomplete',
        'onload',
        'onlosecapture',
        'onmousedown',
        'onmouseenter',
        'onmouseleave',
        'onmousemove',
        'onmouseout',
        'onmouseover',
        'onmouseup',
        'onmousewheel',
        'onmove',
        'onmoveend',
        'onmovestart',
        'onpaste',
        'onpropertychange',
        'onreadystatechange',
        'onreset',
        'onresize',
        'onresizeend',
        'onresizestart',
        'onrowenter',
        'onrowexit',
        'onrowsdelete',
        'onrowsinserted',
        'onscroll',
        'onselect',
        'onselectionchange',
        'onselectstart',
        'onstart',
        'onstop',
        'onsubmit',
        'onunload',
    ];
    $ra  = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val         = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }

    return $val;
}

/**
 * 建立一维数组
 * @param $data
 * @param $key
 * @return array|bool
 */
function getKeyArray($data, $key) {
    if (!is_array($data)) {
        return false;
    }

    if (!isset($data[0][$key])) {
        return false;
    }

    $new_data = [];
    foreach ($data as $k => $v) {
        $new_data[$v[$key]] = $v;
    }

    return $new_data;
}

function emu_getallheaders() {
    $headers = null;
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $name           = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
            $headers[$name] = $value;
        } else {
            if ($name == "CONTENT_TYPE") {
                $headers["Content-Type"] = $value;
            } else {
                if ($name == "CONTENT_LENGTH") {
                    $headers["Content-Length"] = $value;
                }
            }
        }
    }

    return $headers;
}