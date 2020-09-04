<?php

/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 3/19/14
 * Time: 2:52 PM
 * To change this template use File | Settings | File Templates.
 */
class Mava_String
{

    public static function printPostParam()
    {
        if (sizeof($_POST) > 0) {
            foreach ($_POST as $k => $v) {
                echo '$' . $k . ' = Mava_Url::getParam("' . $k . '");<br />';
            }
        }
    }

    public static function printObject($obj)
    {
        echo '<xmp>';
        print_r($obj);
        echo '</xmp>';
    }

    public static function price_format($price, $unit = 'VND')
    {
        switch ($unit) {
            case 'VND':
                return number_format($price, 0, ',', '.') . ' đ';
                break;
            case 'USD':
                return '$' . number_format($price, 2, '.', ',');
                break;
        }
    }

    public static function isJson($str)
    {
        try {
            $obj = json_decode($str);
        } catch (Exception $e) {
            return false;
        }
        return (is_object($obj) || is_array($obj)) ? true : false;
    }

    public static function makeStringToLower($str)
    {
        $str = str_replace(
            array("À", "Á", "Ạ", "Ả", "Ã", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ"),
            array("à", "á", "ạ", "ả", "ã", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ"),
            $str
        );
        $str = str_replace(
            array("È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ"),
            array("è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ"),
            $str
        );
        $str = str_replace("Đ", "đ", $str);
        $str = str_replace(array("Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ"), array("ỳ", "ý", "ỵ", "ỷ", "ỹ"), $str);
        $str = str_replace(
            array("Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ"),
            array("ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ"),
            $str
        );
        $str = str_replace(array("Ì", "Í", "Ị", "Ỉ", "Ĩ"), array("ì", "í", "ị", "ỉ", "ĩ"), $str);
        $str = str_replace(
            array("Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ"),
            array("ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ"),
            $str
        );
        return strtolower($str);
    }

    public static function getRandomHash($length = 32)
    {
        $chars = "ABCDEFGHIJKMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime() * 1000000);
        $i = 0;
        $hash = '';
        while ($i < $length) {
            $num = rand() % (strlen($chars) - 1);
            $tmp = substr($chars, $num, 1);
            $hash = $hash . $tmp;
            $i++;
        }
        return $hash;
    }

    public static function getYoutubeId($url)
    {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
        return $matches[0];
    }

    public static function generalHash($chars = 0, $length = 32, $prefix = '')
    {
        if ($chars == 1) {
            $chars = "ABCDEFGHIJKMNOPQRSTUVWXYZ";
        } else {
            if ($chars == 2) {
                $chars = "0123456789";
            } else {
                $chars = "ABCDEFGHIJKMNOPQRSTUVWXYZ023456789";
            }
        }
        //srand((double)microtime()*1000000);
        $i = 0;
        $hash = '';
        while ($i < $length) {
            $num = rand() % (strlen($chars) - 1);
            $tmp = substr($chars, $num, 1);
            $hash = $hash . $tmp;
            $i++;
        }
        return $prefix . $hash;
    }

    public static function doImplode($array)
    {
        if (!empty($array)) {
            return "'" . implode("','", is_array($array) ? $array : array($array)) . "'";
        } else {
            return 0;
        }
    }

    public static function isEmail($email)
    {
        return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
    }

    public static function isUrl($url)
    {
        return strlen($url) > 0 && preg_match(
            "/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/",
            $url
        );
    }

    public static function isPhoneNumber($phone)
    {
        return strlen($phone) >= 9 && strlen($phone) <= 15;
    }

    public static function unsignString($str, $separator = ' ')
    {
        $str = str_replace(
            array("à", "á", "ạ", "ả", "ã", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ"),
            "a",
            $str
        );
        $str = str_replace(
            array("À", "Á", "Ạ", "Ả", "Ã", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ"),
            "A",
            $str
        );
        $str = str_replace(array("è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ"), "e", $str);
        $str = str_replace(array("È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ"), "E", $str);
        $str = str_replace("đ", "d", $str);
        $str = str_replace("Đ", "D", $str);
        $str = str_replace(array("ỳ", "ý", "ỵ", "ỷ", "ỹ", "ỹ"), "y", $str);
        $str = str_replace(array("Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ"), "Y", $str);
        $str = str_replace(array("ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ"), "u", $str);
        $str = str_replace(array("Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ"), "U", $str);
        $str = str_replace(array("ì", "í", "ị", "ỉ", "ĩ"), "i", $str);
        $str = str_replace(array("Ì", "Í", "Ị", "Ỉ", "Ĩ"), "I", $str);
        $str = str_replace(
            array("ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ"),
            "o",
            $str
        );
        $str = str_replace(
            array("Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ"),
            "O",
            $str
        );
        $str = str_replace(
            array(
                '–',
                '…',
                '“',
                '”',
                "~",
                "!",
                "@",
                "#",
                "$",
                "%",
                "^",
                "&",
                "*",
                "/",
                "\\",
                "?",
                "<",
                ">",
                "'",
                "\"",
                ":",
                ";",
                "{",
                "}",
                "[",
                "]",
                "|",
                "(",
                ")",
                ",",
                ".",
                "`",
                "+",
                "=",
                "-"
            ),
            $separator,
            $str
        );
        $str = preg_replace("/[^_A-Za-z0-9- ]/i", '', $str);
        $str = preg_replace('/(\s+)/', $separator, $str);
        return strtolower($str);
    }

    public static function createToken($str)
    {
        return md5($str . "_hodela");
    }

    public static function validateToken($id, $token)
    {
        return $token == self::createToken($id);
    }
}