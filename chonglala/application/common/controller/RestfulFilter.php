<?php

namespace app\common\controller;


class RestfulFilter extends Base
{
    /**
     * 防SQL注入函数
     * @param String or Array
     * @return String or Array
     */
    public static function check_input($value)
    {
        // 去除斜杠
        if (get_magic_quotes_gpc()) {
            $value = self::stripslashes_array($value);
        }
        $value = self::filter_sql($value);
        return $value;
    }

    /**
     * 删除字符串或数组转义字符
     * @param String or Array
     * @return String or Array
     */
    public static function stripslashes_array($array)
    {
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $array[$k] = self::stripslashes_array($v);
            }
        } else if (is_string($array)) {
            $array = stripslashes($array);
        }
        return $array;
    }

    /**
     * 转义sql字符串或数组
     * @param String or Array
     * @return String or Array
     */
    public static function filter_sql($array)
    {
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $array[$k] = self::filter_sql($v);
            }
        } else if (!is_numeric($array)) {
            // 如果不是数字则加引号
            $array = mysql_real_escape_string($array);
        }
        return $array;
    }

    /**
     * 转义sql字符串或数组
     * @param String or Array
     * @return String or Array
     */
    public static function filter_sql2($array)
    {
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $array[$k] = self::filter_sql($v);
            }
        } else if (!is_numeric($array)) {
            // 如果不是数字则加引号
            $array = "'" . mysql_real_escape_string($array) . "'";
        }
        return $array;
    }
}