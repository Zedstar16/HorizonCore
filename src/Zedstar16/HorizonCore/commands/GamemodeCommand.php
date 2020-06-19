<?php


namespace Zedstar16\HorizonCore\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GamemodeCommand extends Command
{

    private static $mode = [
        "Survival",
        "Creative",
        "Adventure",
        "Spectator"
    ];

    public function __construct()
    {
        parent::__construct("gm");
        $this->description = "Switch Gamemode";
        $this->setPermission("horizon.gamemode");
        $this->setAliases(["gmc", "gma", "gmspc", "gms"]);
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        switch ($commandLabel) {
            case "gms":
                $this->process($args, 0, $sender);
                break;
            case "gmc":
                $this->process($args, 1, $sender);
                break;
            case "gma":
                $this->process($args, 2, $sender);
                break;
            case "gmspc":
                $this->process($args, 3, $sender);
                break;
            case "gm":
                $gm = [
                    "s" => 0,
                    "c" => 1,
                    "a" => 2,
                    "spc" => 3
                ];
                if (isset($args[0]) && isset($gm[$args[0]])) {
                    $player = isset($args[1]) ? [$args[1]] : [];
                    $this->process($player, $gm[$args[0]], $sender);
                } else $sender->sendMessage("§c Use /gm s/c/a/spc [player]");
                break;
        }
    }

    public function process(array $args, Int $gamemode, CommandSender $sender)
    {
        if (isset($args[0])) {
            if ($sender->hasPermission("horizon.gamemode.other")) {
                $target = Server::getInstance()->getPlayer($args[0]);
                if ($target !== null) {
                    $sender->sendMessage("§bGamemode for §3{$target->getName()}§b changed to §3" . self::$mode[$gamemode] . "§b mode");
                    $this->gamemode($gamemode, $target);
                } else $sender->sendMessage("§cTarget player not online");
            } else $sender->sendMessage("§cYou do not have permission to change the gamemode of other players");
        } elseif ($sender instanceof Player) {
            $this->gamemode($gamemode, $sender);
        } else $sender->sendMessage("§cYou must be a player to change your gamemode");
    }

    public function gamemode(Int $option, Player $player)
    {
        $gm = self::$mode;
        $player->setGamemode($option);
        $player->sendMessage("§bYour gamemode has been changed to §3$gm[$option] §bmode");
    }

}