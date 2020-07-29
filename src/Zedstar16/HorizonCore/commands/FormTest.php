<?php


namespace Zedstar16\HorizonCore\commands;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class FormTest extends Command
{

    public $i = 10;


    public function __construct()
    {
        parent::__construct("ftest");
        $this->description = "cmd desc";
        $this->usageMessage = "usage";
        $this->setPermission("horizon.cmd");
    }


    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $this->send($sender, $args[0] ?? 2);
    }

    public function send(Player $p, int $increment)
    {
        $form = new SimpleForm(function (Player $player, $data = null) use ($increment) {
            if ($data === null) {
                return;
            }
            if ($data === 0) {
                $this->send($player, $increment);
                $this->i += $increment;
            }
        });
        $form->setTitle("");
        $form->setContent("• Iteration: $this->i\n" . str_repeat("a", $this->i));
        $form->addButton("");
        $p->sendForm($form);
    }

}