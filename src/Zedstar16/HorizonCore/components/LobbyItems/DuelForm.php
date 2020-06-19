<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\Player;
use pocketmine\Server;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\CustomForm;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class DuelForm
{
    /** @var Player */
    public $p;

    public function __construct(Player $player)
    {
        $this->p = $player;
        $this->primary();
    }

    public function primary()
    {
        $form = new SimpleForm(function(Player $player, $data = null){
            if($data === null){
                return;
            }
            if($data == 0){
                Server::getInstance()->dispatchCommand($player, "duelqueue");
            }elseif($data == 1){
                $this->duelPlayer();
            }elseif($data == 2){
                $this->openBotDuelMenu();
            }
        });
        $form->setTitle("Duels");
        $form->setContent("Select an option");
        $form->addButton("Duels Queue");
        $form->addButton("Duel a player");
        $form->addButton("Duel a bot");
        $this->p->sendForm($form);
    }

    public function duelPlayer(){
        $form = new CustomForm(function(Player $player, $data = null){
            var_dump($data);
        });
        $form->setTitle("Duels");
        $form->addInput("Enter Username of the player you wish to duel", "Username", "default", "label");
        $this->p->sendForm($form);
    }

    public function openBotDuelMenu(){

    }

}