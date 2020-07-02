<?php


namespace Zedstar16\HorizonCore\commands\admin;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\RemoteConsoleCommandSender;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\misc\FakeCommandSender;

class FSenderCommand extends Command
{

    public function __construct()
    {
        parent::__construct("fsender");
        $this->description = "Internal Use only";
        $this->usageMessage = "";
        $this->setPermission("horizon.fsender");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof RemoteConsoleCommandSender) {
            if (count($args) >= 2) {
                $username = $args[0];
                array_shift($args);
                $command = implode(" ", $args);
                $sender = new FakeCommandSender($username, $sender);
                Server::getInstance()->dispatchCommand($sender, $command);
            }
        } else $sender->sendMessage("You're not meant to be using this command... so don;t even try");

    }

}