<?php


namespace Zedstar16\HorizonCore\cache;


class Cache
{

    public static $data = [];
    public static $altered = [];

    public static $discord_dispatch = [];

    public static function write()
    {
        print_r(self::$data);
        foreach (self::$altered as $username) {
            file_put_contents("plugins/HorizonCore/resources/players/$username.json", json_encode(self::$data[$username], JSON_THROW_ON_ERROR));
            unset(self::$altered[$username]);
        }
    }

}