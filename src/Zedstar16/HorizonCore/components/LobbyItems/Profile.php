<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\Player;
use pocketmine\Server;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\CustomForm;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class Profile
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
        $content = "";


        $form = new SimpleForm(function(Player $player, $data = null){
            if($data === null){
                return;
            }
        });
        $form->setTitle("Your Profile");
        $form->setContent("Select an option");
        $form->addButton("Close");
        $this->p->sendForm($form);
    }


}