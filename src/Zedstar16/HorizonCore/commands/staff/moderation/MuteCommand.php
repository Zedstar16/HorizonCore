<?php

namespace Zedstar16\HorizonCore\commands\staff\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\Moderation\Logger;
use Zedstar16\HorizonCore\components\Moderation\ModerationAPI;
use Zedstar16\HorizonCore\components\Moderation\ModerationException;

class MuteCommand extends Command
{

    public function __construct()
    {
        parent::__construct("mute");
        $this->description = "Mute a player";
        $this->setPermission("horizon.staff");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $server = Server::getInstance();
        $staff = $sender->getName();
        if (isset($args[0])) {
            if (isset($args[1])) {
                try {
                    $player = $server->getPlayer($args[0]) !== null ? $server->getPlayer($args[0])->getName() : $args[0];
                    $online = $server->getPlayer($args[0]) !== null;
                    array_shift($args);
                    $reason = implode(" ", $args);
                    $silent = false;
                    if (strpos($reason, "-s") !== false) {
                        $silent = true;
                        $reason = str_replace("-s", "", $reason);
                    }
                    ModerationAPI::mute($player, $staff, $reason);
                    Logger::log("Permanent Mute", $player, $staff, $reason);
                    if (!$silent) {
                        $server->broadcastMessage("§a$player §chas been permanently muted by §b$staff\n§5Reason: §b$reason");
                    } else {
                        $sender->sendMessage("§cYou have permanently muted §a$player\n§5Reason: §b$reason");
                        if ($online) {
                            $server->getPlayer($args[0])->sendMessage("§cYou have been permanently muted by §b$staff\n§5Reason: §b$reason\n§2Appeal at: §aappeal.horizonpe.net");
                        }
                    }
                } catch (ModerationException $exception) {
                    $sender->sendMessage("§c" . $exception->getMessage());
                }
            } else $sender->sendMessage("§cYou need to specify a reason for the punishment");
        } else $sender->sendMessage("§cYou need to specify a target player");
    }

}