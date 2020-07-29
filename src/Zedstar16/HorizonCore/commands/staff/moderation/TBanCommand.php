<?php

namespace Zedstar16\HorizonCore\commands\staff\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\Moderation\Logger;
use Zedstar16\HorizonCore\components\Moderation\ModerationAPI;
use Zedstar16\HorizonCore\components\Moderation\ModerationException;
use Zedstar16\HorizonCore\components\Moderation\TimeFormat;
use Zedstar16\HorizonCore\utils\Utils;

class TBanCommand extends Command
{

    public function __construct()
    {
        parent::__construct("tban");
        $this->description = "Temporarily ban a player";
        $this->setPermission("horizon.staff");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $server = Server::getInstance();
        $staff = $sender->getName();
        if (isset($args[0])) {
            if (isset($args[1])) {
                $player = $server->getPlayer($args[0]) !== null ? $server->getPlayer($args[0])->getName() : $args[0];
                if (isset($args[2])) {
                    try {
                        $reason = implode(" ", array_slice($args, 2));
                        $silent = false;
                        if (strpos($reason, "-s") !== false) {
                            $silent = true;
                            $reason = str_replace("-s", "", $reason);
                        }
                        $expires = TimeFormat::timeToExpiry(TimeFormat::get($args[1]));
                        if ($server->getPlayer($args[0]) !== null) {
                            $server->getPlayer($args[0])->kick(Utils::centralise("You have temporarily banned by §b$staff\n§5Reason: §b$reason\n§6Ban Expires In: $expires\n\n§2Appeal at: §aappeal.horizonpe.net"));
                        }
                        ModerationAPI::ban($player, $staff, $reason, $args[1]);
                        Logger::log("Temporary Ban", $player, $staff, $reason, $args[1]);
                        if (!$silent) {
                            $server->broadcastMessage("§a$player §chas been temporarily banned by §b$staff\n§5Reason: §b$reason\n§6Duration: $expires");
                        } else {
                            $sender->sendMessage("§cYou have temporarily banned §a$player\n§5Reason: §b$reason\n§6Duration: $expires");
                        }
                    } catch (ModerationException $exception) {
                        $sender->sendMessage("§c" . $exception->getMessage());
                    }
                } else $sender->sendMessage("§cYou need to specify a reason for the punishment");
            } else $sender->sendMessage("§cYou need to specify a duration for the punishment");
        } else $sender->sendMessage("§cYou need to specify a target player");
    }

}