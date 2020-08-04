<?php


namespace Zedstar16\HorizonCore\commands\economy;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Zedstar16\HorizonCore\managers\TopDataManager;

class TopCoinsCommand extends Command
{

    public function __construct()
    {
        parent::__construct("topcoins");
        $this->description = "View top richest members on the server";
        $this->setAliases(["baltop", "topbal", "topmoney"]);
        $this->setPermission("horizon.cmd");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $page = $args[0] ?? 1;
        TopDataManager::$top_money;
        $string = "-=-= Top Richest Users on the server =-=-";
        for($i=0; $i<=10; $i++){

        }
    }

}