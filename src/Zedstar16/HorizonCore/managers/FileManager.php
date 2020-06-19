<?php


namespace Zedstar16\HorizonCore\managers;


use Zedstar16\HorizonCore\Horizon;

class FileManager
{

    public static function getData(string $filename) : ?string
    {
       return file_get_contents(Horizon::getInstance()->getDataFolder().$filename);
    }

    public static function getYamlData(string $filename) : ?array
    {
        $filename .= ".yml";
        $folder = Horizon::getInstance()->getDataFolder()."resources/";
        if (file_exists($folder . $filename)) {
            return yaml_parse_file($folder . $filename);
        } else yaml_emit_file($folder . $filename, []);
        return [];
    }

    public static function getJsonData(string $filename) : ?array
    {
        $filename .= ".json";
        $folder = Horizon::getInstance()->getDataFolder()."resources/";
        if (file_exists($folder . $filename)) {
            return json_decode(file_get_contents($folder . $filename), true);
        } else file_put_contents($folder . $filename, json_encode([]));
        return [];
    }


    public static function saveData(string $filename, $data) : void
    {
        file_put_contents(Horizon::getInstance()->getDataFolder()."resources/".$filename, $data);
    }

    public static function saveYamlData(string $filename, $data) : void
    {
        yaml_emit_file(Horizon::getInstance()->getDataFolder()."resources/".$filename.".yml", $data);
    }

    public static function saveJsonData(string $filename, $data) : void
    {
        file_put_contents(Horizon::getInstance()->getDataFolder()."resources/".".json", json_encode($data));
    }


}