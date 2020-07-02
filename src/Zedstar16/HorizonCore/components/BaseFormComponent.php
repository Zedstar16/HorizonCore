<?php


namespace Zedstar16\HorizonCore\components;


use pocketmine\Player;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

abstract class BaseFormComponent
{

    /** @var HorizonPlayer */
    public $p;

    public function __construct(Player $player)
    {
        $this->p = $player;
        $this->primary();
    }

    abstract public function primary();

    private function default()
    {
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
        });
        $form->setTitle("");
        $form->setContent("");
        $form->addButton("");
        $this->p->sendForm($form);
    }

}