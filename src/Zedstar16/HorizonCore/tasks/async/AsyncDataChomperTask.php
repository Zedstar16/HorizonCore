<?php


namespace Zedstar16\HorizonCore\tasks\async;


use pocketmine\scheduler\AsyncTask;

class AsyncDataChomperTask extends AsyncTask
{
    protected $datavalue;

    public function __construct(String $datavalue)
    {
        $this->datavalue = $datavalue;
    }

    public function onRun()
    {
       $files = array_diff(scandir("plugins/HorizonCore/resources/players"), ['..', '.']);
       $data = [];
       $list = [];
       foreach ($files as $filename){
           $file = json_decode(file_get_contents($filename), true);
           $data[$file["username-cased"]] = $file[$this->datavalue];
       }
       foreach($data as $player => $stats){
           foreach($stats as $key => $value){
               $list[$key][$player] = $value;
           }
       }
       foreach($list as $key => $player){
           ksort($list[$key]);
       }

    }

}