<?php

namespace Zedstar16\HorizonCore\commands\staff\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\Moderation\Logger;
use Zedstar16\HorizonCore\components\Moderation\ModerationAPI;
use Zedstar16\HorizonCore\components\Moderation\ModerationException;

class UnbanCommand extends Command
{

    public function __construct()
    {
        parent::__construct("unban");
        $this->description = "Unban a player";
        $this->setAliases(["pardon"]);
        $this->setPermission("horizon.staff");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $server = Server::getInstance();
        $staff = $sender->getName();
        if (isset($args[0])) {
            try {
                ModerationAPI::unban($args[0], $staff);
                if (isset($args[1]) && strpos($args[1], "-s") !== false) {
                    $sender->sendMessage("§aYou have successfully unbanned §b$args[0]");
                } else {
                    $server->broadcastMessage("§b$args[0]§a has been unbanned by §b$staff");
                }
                Logger::log("Unban", $args[0], $staff);
            } catch (ModerationException $exception) {
                $sender->sendMessage("§c" . $exception->getMessage());
            }
        } else $sender->sendMessage("§cYou need to specify a target player");
    }

}