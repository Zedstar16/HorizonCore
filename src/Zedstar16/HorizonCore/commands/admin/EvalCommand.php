<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class EvalCommand extends Command
{


    public function __construct()
    {
        parent::__construct("eval");
        $this->description = "Execute PHP Code";
        $this->usageMessage = "/eval <code>";
        $this->setPermission("horizon.eval");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(isset($args[0])){
            try {
                $code = implode(" ", $args);
                $code = str_replace(["“", "”", '"'], "'", $code);
                $result = eval($code);
                if ($result !== null) {
                    if (!is_string($result)) {
                        $result = json_encode($result, JSON_PRETTY_PRINT);
                    }
                } else {
                    $result = "§aExecuted, null return";
                }
                $sender->sendMessage($result);
            } catch (\Throwable $error) {
                $errormessage = $error->getMessage();
                $sender->sendMessage("§cError: §f$errormessage");
            }
        }else $sender->sendMessage("§bProvide some code to run");
    }

}