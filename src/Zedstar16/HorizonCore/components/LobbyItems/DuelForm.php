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
        $form->addButton("Duel a Bot");
        $this->p->sendForm($form);
    }

    public function duelPlayer($failed = false, $username = "")
    {
        $form = new CustomForm(function (Player $player, $data = null) {
            if (is_string($data["label"])) {

            }
        });
        $form->setTitle("Duels");
        $form->addInput("Enter Username of the player you wish to duel", "Username", "", "label");
        $this->p->sendForm($form);
    }

    public function openBotDuelMenu()
    {

    }

}