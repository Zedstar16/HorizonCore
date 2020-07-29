<?php


namespace Zedstar16\HorizonCore\components\InfoUI;


use pocketmine\Player;
use Zedstar16\HorizonCore\components\BaseFormComponent;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\HorizonCore\managers\FileManager;

class MainInfoForm extends BaseFormComponent
{

    public function primary()
    {
        $info = FileManager::getYamlData("infoui");
        InfoReturn::$data[$this->p->getName()] = [];
        InfoReturn::$data[$this->p->getName()][] = $this;
        $buttons = [];
        foreach ($info as $button => $subdata) {
            $buttons[] = [
                "button" => $button,
                "data" => $subdata
            ];
        }
        $form = new SimpleForm(function (Player $player, $data = null) use ($buttons) {
            if ($data === null) {
                return;
            }
            if (isset($buttons[$data])) {
                new SubInfoForm($player, $buttons[$data]["data"]);
            }
        });
        $form->setTitle("§l§1Info");
        $form->setContent("Select a catergory");
        foreach ($buttons as $buttondata) {
            $form->addButton($buttondata["button"]);
        }
        $this->p->sendForm($form);
    }
}