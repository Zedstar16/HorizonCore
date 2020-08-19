<?php


namespace Zedstar16\HorizonCore\cache;


class Cache
{

    public static $data = [];
    public static $altered = [];

    public static $top_data = [];
    public static $top_killers = [];


    public static $discord_dispatch = [];

    public static function write()
    {
        foreach (self::$altered as $username) {
            file_put_contents("plugins/HorizonCore/resources/players/$username.json", json_encode(self::$data[$username], JSON_THROW_ON_ERROR));
            unset(self::$altered[$username]);
        }
    }

}