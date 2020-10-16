<?php


namespace Zedstar16\HorizonCore\components;


use SQLite3;
use Zedstar16\HorizonCore\Horizon;

class DB
{

    /** @var SQLite3 */
    public static $db;

    public static function init()
    {
        self::$db = new SQLite3("database.db");
        $stmt = "
        CREATE TABLE IF NOT EXISTS players(
            username TEXT PRIMARY KEY, 
            xuid INT,
            hits INT, 
            experience INT,
            coins INT,
            damage_taken INT, 
            damage_dealt INT,
            clicks INT,
            distance_travelled INT,
            kills INT, 
            deaths INT,
            joins INT,
            chat_messages INT,
            items_dropped INT,
            items_picked_up INT,
            items_consumed INT,
            blocks_placed INT,
            blocks_broken INT                   
        );
        CREATE TABLE IF NOT EXISTS last_seen(
            username TEXT PRIMARY KEY, 
            ip TEXT,
            cid INT,
            deviceid TEXT,
            os TEXT,
            ui TEXT,
            controls TEXT,
            timestamp INT                
        );
        CREATE TABLE IF NOT EXISTS xuids(
            username TEXT,
            xuid INT         
        );
        CREATE TABLE IF NOT EXISTS ips(
            username TEXT  
        );
        CREATE TABLE IF NOT EXISTS cids(
            username TEXT  
        );
        CREATE TABLE IF NOT EXISTS deviceids(
            username TEXT  
        );
    ";
        self::$db->exec($stmt);


        $data = [
            "username" => "player",
            "xuid" => 35325,
            "hits" => 0,
            "experience" => 0,
            "coins" => 0,
            "damage_taken" => 0,
            "damage_dealt" => 0,
            "clicks" => 0,
            "distance_travelled" => 0,
            "kills" => 0,
            "deaths" => 0,
            "joins" => 0,
            "chat_messages" => 0,
            "items_dropped" => 0,
            "items_picked_up" => 0,
            "items_consumed" => 0,
            "blocks_placed" => 0,
            "blocks_broken" => 0,
        ];

        $stmt1 = "INSERT INTO players(";
        $values = "VALUES (";
        $i = 0;
        foreach ($data as $key => $value) {
            $i++;
            $suffix = $i !== count($data) ? ", " : ")";
            $stmt1 .= $key . $suffix;
            if (is_string($value)) $value = "\"$value\"";
            $values .= $value . $suffix;
        }
        $query = $stmt1 . " " . $values . ";";
        var_dump($query);
        self::$db->exec($query);
    }

}