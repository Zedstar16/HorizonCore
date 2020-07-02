<?php


namespace Zedstar16\HorizonCore\components\shop;


use pocketmine\Player;
use Zedstar16\HorizonCore\components\BaseFormComponent;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\utils\Utils;

class Shop
{

    public $data = [];
    /** @var Player $p */
    public $p;


    public function __construct(Player $player)
    {
        $this->data = FileManager::getYamlData("shop");
        $this->p = $player;
        $this->primary();
    }

    public function primary()
    {
        $buttons = [];
        foreach($this->data as $key => $content){
            $buttons[] = $content;
        }
        $form = new SimpleForm(function (Player $player, $data = null) use($buttons){
            if ($data === null) {
                return;
            }else {
                new ShopSubForm($this->p, $buttons[$data]["contents"]);
            }
        });
        $form->setTitle("Item Shop");
        $form->setContent("Select an option");
        foreach($buttons as $button){
            $form->addButton($button["button-name"]);
        }
        $this->p->sendForm($form);
    }

}