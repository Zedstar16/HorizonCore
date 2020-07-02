<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\Player;
use pocketmine\Server;
use Zedstar16\HorizonCore\components\BaseFormComponent;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\CustomForm;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class DuelForm extends BaseFormComponent
{

    public function primary()
    {
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
            if ($data == 0) {
                Server::getInstance()->dispatchCommand($player, "duelqueue");
            } elseif ($data == 1) {
                $this->duelPlayer();
            } elseif ($data == 2) {
                $this->openBotDuelMenu();
            }
        });
        $form->setTitle("Duels");
        $form->setContent("Select an option");
        $form->addButton("Duels Queue");
        $form->addButton("Duel a Player");
        $this->p->sendForm($form);
    }

    public function duelPlayer($failed = false, $username = "")
    {
        $form = new CustomForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
            if (isset($data[0]) && is_string($data[0])) {
                $p = Server::getInstance()->getPlayer($data[0]);
                if ($p == null) {
                    $this->duelPlayer(true, $data[0]);
                } else {
                    Server::getInstance()->dispatchCommand($player, "duel $data[0]");
                }
            }
        });
        $form->setTitle("Duels");
        $text = $failed ? "§c$username is not online§f\n\nEnter Username of the player you wish to duel" : "Enter Username of the player you wish to duel";
        $form->addInput($text, "Username");
        $this->p->sendForm($form);
    }

    public function openBotDuelMenu()
    {

    }

}