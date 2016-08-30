<?php
/**
 * 简述
 *
 * 详细说明(可略)
 *
 * @copyright Copyright&copy; 2016, 
 * @author   zhanjuzhang <zhanjuzhang@gmail.com>
 * @version $Id: Redis.php, v ${VERSION} 8/29/16 10:27 AM Exp $
 */

namespace Juzhang\Test\Util;

class Redis {
    private $redis;
    private $host;
    private $port;

    public function __construct($data = null) {
        if (isset($data['redis_servers'])) {
            $redis_config = $data['redis_servers'];
        } else {
            global $config;
            $redis_config = $config['redis_servers'];
        }
        $pconnect = false;
        if (isset($data['pconnect'])) {
            $pconnect = true;
        }
        $redis = new Redis();
        if ($redis == false) {
            return false;
        }
        $this->redis  = $redis;
        $redis_server = $redis_config[array_rand($redis_config)];
        $redis_server = explode(':', $redis_server);
        $this->host   = $redis_server['0'];
        $this->port   = $redis_server['1'];

        if ($pconnect) {
            $this->pconnect();
        } else {
            $this->connect();
        }
    }

    public function pconnect() {
        return $this->redis->pconnect($this->host, $this->port);
    }

    public function connect() {
        try {
            return $this->redis->connect($this->host, $this->port);
        } catch (Exception $e) {
            writeLog($e->__toString(), 'redis_connect_error');
            echo apiReturnError(1003);
            die();
        }
    }

    public function close() {
        return $this->redis->close();
    }

    public function get($key) {
        $data = $this->redis->get($key);
        if (!$data) {
            return $data;
        }

        return json_decode($data, true);
    }

    public function mGet($key) {
        return $this->redis->mGet($key);
    }

    public function set($key, $value) {
        $value = intval($value) === $value ? $value : json_encode($value);

        return $this->redis->set($key, $value);
    }

    public function setex($key, $value, $expires = 86400) {
        $value = intval($value) === $value ? $value : json_encode($value);

        return $this->redis->setex($key, $expires, $value);
    }

    public function increment($key, $value = 1) {
        return $this->redis->incr($key, $value);
    }

    public function decrement($key, $value = 1) {
        return $this->redis->decr($key, $value);
    }

    public function rPush($key, $value) {
        return $this->redis->rPush($key, $value);
    }

    public function lPush($key, $value) {
        return $this->redis->lPush($key, $value);
    }

    public function lPop($key) {
        return $this->redis->lPop($key);
    }

    public function rPop($key) {
        return $this->redis->rPop($key);
    }

    public function lRange($key, $start = 0, $end = -1) {
        return $this->redis->lRange($key, $start, $end);
    }

    public function lLen($key) {
        return $this->redis->lLen($key);
    }

    public function lRem($key, $value) {
        return $this->redis->lRem($key, $value);
    }

    public function lTrim($key, $start = 0, $end = -1) {
        return $this->redis->lTrim($key, $start, $end);
    }

    public function zAdd($key, $score, $value) {
        return $this->redis->zAdd($key, $score, $value);
    }

    public function zCard($key) {
        return $this->redis->zCard($key);
    }

    public function zDelete($key, $member) {
        return $this->redis->zDelete($key, $member);
    }

    public function zRange($key, $start, $end, $with_score = false) {
        return $this->redis->zRange($key, $start, $end, $with_score);
    }

    public function zRangeByScore($key, $start, $end, $options = null) {
        if (is_array($options) && !empty($options)) {
            return $this->redis->zRangeByScore($key, $start, $end, $options);
        } else {
            return $this->redis->zRangeByScore($key, $start, $end);
        }
    }

    public function zRevRangeByScore($key, $start, $end, $options = []) {
        return $this->redis->zRevRangeByScore($key, $start, $end, $options);
    }

    public function zRemRangeByScore($key, $start, $end) {
        return $this->redis->zRemRangeByScore($key, $start, $end);
    }

    public function zRemRangeByRank($key, $start, $end) {
        return $this->redis->zRemRangeByRank($key, $start, $end);
    }

    public function zRem($key, $value) {
        return $this->redis->zRem($key, $value);
    }

    public function zSize($key) {
        return $this->redis->zSize($key);
    }

    public function zUnion($keyOutput, $arrayZSetKeys) {
        return $this->redis->zUnion($keyOutput, $arrayZSetKeys);
    }

    public function sAdd($key, $value) {
        return $this->redis->sAdd($key, $value);
    }

    public function sMembers($key) {
        return $this->redis->sMembers($key);
    }

    public function delete($key) {
        return $this->redis->delete($key);
    }

    public function lSize($key) {
        return $this->redis->lSize($key);
    }

    public function hGet($key, $hashKey) {
        return $this->redis->hGet($key, $hashKey);
    }

    public function hSet($key, $hashKey, $value) {
        return $this->redis->hSet($key, $hashKey, $value);
    }

    public function hLen($key) {
        return $this->redis->hLen($key);
    }

    public function hMset($key, $data) {
        if (!$data || !is_array($data)) {
            return false;
        }

        return $this->redis->hMset($key, $data);
    }

    public function hGetAll($key) {
        return $this->redis->hGetAll($key);
    }

    public function hExists($key, $memberKey) {
        return $this->redis->hExists($key, $memberKey);
    }

    public function exists($key) {
        return $this->redis->exists($key);
    }

    public function ttl($key) {
        $time = $this->redis->ttl($key);

        return ($time < 0) ? 0 : $time;
    }

}
