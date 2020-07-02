<?php


namespace Zedstar16\HorizonCore\components\shop;


use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;
use Zedstar16\HorizonCore\HorizonPlayer;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;

class ShopSubForm
{

    public $contents = [];
    /** @var HorizonPlayer */
    public $p;

    public function __construct(HorizonPlayer $player, array $contents)
    {
        $this->p = $player;
        $this->contents = $contents;
        $this->form();
    }

    private function getImagePath(Item $item): ?string
    {
        $name = strtolower(str_replace(" ", "_", $item->getVanillaName()));
        $path = "resources/items/$name.png";
        if (file_exists($path)) {
            return $path;
        }
        return null;
    }

    public function form()
    {
        $buttons = [];
        $data = $this->contents;
        foreach ($data as $string) {
            $itemdata = explode(":", $string);
            $item = ItemFactory::get($itemdata[0]);
            $buttons[] = [
                "id" => $itemdata[0],
                "damage" => $itemdata[1],
                "count" => $itemdata[2],
                "price" => (int)$itemdata[3],
                "name" => $item->getName(),
                "image" => $itemdata[4] ?? $this->getImagePath($item)
            ];
        }
        $form = new SimpleForm(function (Player $player, $data = null) use ($buttons) {
            if ($data === null) {
                return;
            }
            if ($data == 0) {
                new Shop($player);
                return;
            }
            $data--;
            $this->processItem($buttons[$data], function (string $message) {
                $this->p->sendMessage($message);
            });

        });
        $form->setTitle("Item Shop");
        $form->setContent("§7Your Coins: §a{$this->p->getEconomy()->getCoins()}\n§fSelect an item");
        $form->addButton("§4Back");
        foreach ($buttons as $button) {
          //  if($button["image"] !== null) {
            //    $form->addButton("§0$button[name] §8[" . "$" . "§6$button[price]§8]", SimpleForm::IMAGE_TYPE_PATH, $button["image"]);
          //  }else{
                $form->addButton("§0$button[name] §8[" . "$" . "§6$button[price]§8]");
          //  }
        }
        $this->p->sendForm($form);
    }

    public function processItem(array $info, ?callable $callable)
    {
        $e = $this->p->getEconomy();
        $balance = $e->getCoins();
        if ($balance >= $info["price"]) {
            $item = ItemFactory::get($info["id"], $info["damage"], $info["count"]);
            if ($this->p->getInventory()->canAddItem($item)) {
                $form = new SimpleForm(function (Player $player, $data = null) use ($info, $item, $callable) {
                    if ($data === null) {
                        $callable("Rejected purchase");
                    }
                    if ($data == 0) {
                        $this->p->getEconomy()->removeCoins($info["price"]);
                        $this->p->getInventory()->addItem($item);
                        $callable("You have purchased $info[count]x $info[name] for $info[price] coins");
                    } else $callable("Rejected purchase");

                });
                $form->setTitle("Item Shop");
                $form->setContent("Are you sure to wish to purchase\n\n$info[count]x $info[name] for $info[price] coins\n\n\n");
                $form->addButton("Yes");
                $form->addButton("No");
                $this->p->sendForm($form);
            } else $callable("You do not have enough space in your inventory to purchase this item");
        } else $callable("You need ".$info["price"]-$balance." more coins to purchase this item");
    }

}