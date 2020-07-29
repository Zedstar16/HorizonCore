<?php


namespace Zedstar16\HorizonCore\components\InfoUI;


use pocketmine\Player;
use Zedstar16\HorizonCore\components\BaseFormComponent;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class SubInfoForm extends BaseFormComponent
{

    public $data = [];

    public function __construct(Player $player, array $data)
    {
        if (!in_array($this, InfoReturn::$data[$player->getName()], true)) {
            InfoReturn::$data[$player->getName()][] = $this;
        }
        $this->data = $data;
        parent::__construct($player);
    }

    public function primary()
    {
        $buttons = [];
        $sub = false;
        foreach ($this->data as $button => $subdata) {
            if ($button !== "content") {
                if (is_array($subdata)) {
                    $sub = true;
                }
                $buttons[] = [
                    "button" => $button,
                    "data" => $subdata
                ];
            }
        }
        $form = new SimpleForm(function (Player $player, $data = null) use ($sub, $buttons) {
            if ($data === null) {
                InfoReturn::$data[$player->getName()] = [];
                return;
            }
            if (isset($buttons[$data]) && $sub) {
                new SubInfoForm($player, $buttons[$data]["data"]);
            }
            if (($data === count($buttons)) or !$sub) {
                $class = InfoReturn::$data[$player->getName()][count(InfoReturn::$data[$player->getName()]) - 2] ?? null;
                if ($class === null) {
                    new MainInfoForm($player);
                } else {
                    if ((count(InfoReturn::$data[$player->getName()]) - 1) !== 0 && (!$class instanceof MainInfoForm && $class->data !== $this->data)) {
                        $class->primary();
                        unset(InfoReturn::$data[$player->getName()][count(InfoReturn::$data[$player->getName()]) - 1]);
                    } else new MainInfoForm($player);
                }
            }
        });
        $form->setTitle("§b§1Info");
        $form->setContent($this->data["content"]);
        foreach ($buttons as $buttondata) {
            $form->addButton($buttondata["button"]);
        }
        $form->addButton("§4Back");
        $this->p->sendForm($form);
    }

}