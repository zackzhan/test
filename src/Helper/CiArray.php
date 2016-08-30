<?php
/**
 * 简述
 *
 * 详细说明(可略)
 *
 * @copyright Copyright&copy; 2016, 
 * @author   zhanjuzhang <zhanjuzhang@gmail.com>
 * @version $Id: Array.php, v ${VERSION} 8/29/16 3:23 PM Exp $
 */

namespace Juzhang\Test\Helper;

class CiArray {

    /**
     * Element
     *
     * Lets you determine whether an array index is set and whether it has a value.
     * If the element is empty it returns NULL (or whatever you specify as the default value.)
     *
     * @param    string
     * @param    array
     * @param    mixed
     * @return    mixed    depends on what the array contains
     */
    public static function element($item, $array, $default = null)
    {
        return array_key_exists($item, $array) ? $array[$item] : $default;
    }

    /**
     * Random Element - Takes an array as input and returns a random element
     *
     * @param    array
     * @return    mixed    depends on what the array contains
     */
    public static function random_element($array)
    {
        return is_array($array) ? $array[array_rand($array)] : $array;
    }


    /**
     * Elements
     *
     * Returns only the array items specified. Will return a default value if
     * it is not set.
     *
     * @param    array
     * @param    array
     * @param    mixed
     * @return    mixed    depends on what the array contains
     */
    public static function elements($items, $array, $default = null)
    {
        $return = [];

        is_array($items) OR $items = [$items];

        foreach ($items as $item) {
            $return[$item] = array_key_exists($item, $array) ? $array[$item] : $default;
        }

        return $return;
    }
}