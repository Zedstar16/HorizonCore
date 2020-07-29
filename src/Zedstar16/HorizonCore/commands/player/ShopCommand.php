<?php


namespace Zedstar16\HorizonCore\commands\player;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zedstar16\HorizonCore\components\shop\Shop;

class ShopCommand extends Command
{


    public function __construct()
    {
        parent::__construct("shop");
        $this->description = "Open ItemShop";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.cmd");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            new Shop($sender);
        }
    }

}