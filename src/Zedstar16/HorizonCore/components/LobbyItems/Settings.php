<?php


namespace Zedstar16\HorizonCore\components\LobbyItems;


use pocketmine\Player;
use Zedstar16\HorizonCore\components\KitEditor;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class Settings
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
        $buttons = ["KitEditor"];
        $form = new SimpleForm(function(Player $player, $data = null){
            if($data === null){
                return;
            }
            switch($data){
                case 0:
                    new KitEditor($this->p);
                    break;
            }
        });
        $form->setTitle("Settings");
        $form->setContent("Select an option");
        foreach($buttons as $button){
            $form->addButton($button);
        }
        $this->p->sendForm($form);
    }


}