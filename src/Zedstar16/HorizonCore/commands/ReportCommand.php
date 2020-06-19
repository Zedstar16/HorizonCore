<?php

declare(strict_types=1);

namespace Zedstar16\HorizonCore\commands;


use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Internet;
use pocketmine\utils\TextFormat;
use Zedstar16\HorizonCore\Horizon;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\CustomForm;
use Zedstar16\HorizonCore\libs\jojoe77777\FormAPI\SimpleForm;
use Zedstar16\HorizonCore\managers\FileManager;

class ReportCommand extends Command
{

    public static $msg;
    public static $playerform;
    public static $bugform;
    public static $mainform;
    public $cooldown = [];
    
    public function __construct()
    {
        parent::__construct("report");
        $this->description = "Report a player or bug on the server to our staff team";
        $this->usageMessage = "/report";
        $this->setPermission("horizon.report");
        $config = FileManager::getYamlData("reportconfig");
        self::$msg = $config["messages"];
        self::$playerform = $config["player-form"];
        self::$mainform = $config["main-form"];
        self::$bugform = $config["bug-form"];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            $name = $sender->getName();
            $ct = floatval(FileManager::getYamlData("reportconfig")["report-cooldown"]);
            if (!isset($this->cooldown[$name]) || ($this->cooldown[$name] + $ct - time() <= 0)) {
                try {
                    $this->sendMainUI($sender);
                } catch (\Throwable$e) {
                    $sender->sendMessage(TextFormat::RED . "An error occured when attempting to send this report");
                    //var_dump($e);
                }
            } else {
                $sender->sendMessage(str_replace("{cooldown}", $this->cooldown[$name] + $ct - time(), self::$msg["report-in-cooldown"]));
            }
        } else $sender->sendMessage(TextFormat::RED . "Use this command in-game nerd");
    }
      

    public function sendMainUI(Player $player)
    {
        $form = new SimpleForm(function (Player $player, $data = null): void {
            if ($data !== null) {
                switch ($data) {
                    case 0:
                        if (count(Horizon::getInstance()->getServer()->getOnlinePlayers()) > 1) {
                            $this->sendPlayerReportUI($player);
                        } else $player->sendMessage("§cThere are no online players to report");
                        break;
                    case 1:
                        $this->sendBugReportUI($player);
                        break;
                }
            } else {
                $player->sendMessage(self::$msg["exit-ui"]);
            }
        });
        $form->setTitle(self::$mainform["title"]);
        $form->setContent(self::$mainform["content"]);
        $form->addButton(self::$mainform["playerreport"]);
        $form->addButton(self::$mainform["bugreport"]);
        $player->sendForm($form);
    }


    public function sendPlayerReportUI(Player $player)
    {
        $players = [];
        foreach (Horizon::getInstance()->getServer()->getOnlinePlayers() as $p) {
            if ($p->getName() !== $player->getName()) {
                $players[] = $p->getName();
            }
        }
        $form = new CustomForm(function (Player $player, $data = null) use ($players) : void {
            if ($data == null) {
                $player->sendMessage(self::$msg["exit-ui"]);
                return;
            } else {
                $target = $players[$data[1]];
                $reason = $data[2];
                $reporter = $player->getName();
                if ($reason !== null && strlen($reason) >= self::$playerform["min-reason-length"]) {
                    $this->dispatchReport($target, $reporter, $reason);
                    $this->cooldown[$player->getName()] = time();
                    $player->sendMessage(self::$msg["report-success"]);
                } else {
                    $player->sendMessage(self::$msg["reason-too-short"]);
                    return;
                }
            }

        });

        $form->setTitle(self::$playerform["title"]);
        $form->addLabel(self::$playerform["content"]);
        $form->addDropdown(self::$playerform["dropdownbox-label"], $players);
        $form->addInput(self::$playerform["inputbox-title"], self::$playerform["typehint-for-inputbox"]);
        $player->sendForm($form);
    }

    public function sendBugReportUI(Player $player)
    {
        $form = new CustomForm(function (Player $player, $data = null): void {
            if ($data == null) {
                $player->sendMessage(self::$msg["exit-ui"]);
                return;
            }
            $reason = $data[1];
            $reporter = $player->getName();
            if ($reason !== null && strlen($reason) >= self::$bugform["min-reason-length"]) {
                $this->bugReport($reporter, $reason);
                $this->cooldown[$player->getName()] = time();
                $player->sendMessage(self::$msg["report-success"]);
            } else {
                $player->sendMessage(self::$msg["reason-too-short"]);
                return;
            }

        });
        $form->setTitle(self::$bugform["title"]);
        $form->addLabel(self::$bugform["content"]);
        $form->addInput(self::$bugform["inputbox-title"], self::$bugform["typehint-for-inputbox"]);
        $player->sendForm($form);
    }


    public function bugReport($reporter, $reason)
    {
        $timestamp = new \DateTime();
        $timestamp->setTimezone(new \DateTimeZone("UTC"));        $webhookdata = [];
        $webhookdata['embeds'][] = [
            'color' => 0xff0000,
            'timestamp' => $timestamp->format("Y-m-d\TH:i:s.v\Z"),
            'fields' => [
                [
                    'name' => 'Bug Report',
                    'value' => "**Reporter:** $reporter\n**Reason:** $reason",
                    'inline' => true
                ]]
        ];
        Internet::postURL("https://discordapp.com/api/webhooks/615850142926503939/k_JaHvGVPNSTizsKF4JYkUxG5V2_7D9pcMz4o_1NBKGxYC-etlMNaMrejBaGuAwH80UD", json_encode($webhookdata), 10, ['Content-Type' => 'application/json']);


    }


    public function dispatchReport($target, $reporter, $reason)
    {
        $i = 0;
        foreach (Horizon::getInstance()->getServer()->getOnlinePlayers() as $p) {
            if ($p->hasPermission("staffchat")) {
                $p->sendMessage("§8§l[§bSTAFF§8]§r §a$reporter §freported §b$target §ffor §e$reason");
                $i++;
            }
        }

        $timestamp = new \DateTime();
        $timestamp->setTimezone(new \DateTimeZone("UTC"));
        $webhookdata = [];
        $str = ($i < 1) ? "@here" : "";
        $webhookdata['embeds'][] = [
            'color' => 0xff0000,
            'timestamp' => $timestamp->format("Y-m-d\TH:i:s.v\Z"),
            'fields' => [
                [
                    'name' => "$str New report",
                    'value' => "**Suspect:** {$target}\n**Reporter:** $reporter\n**Reason:** `$reason`\n**Staff Online**: $i",
                    'inline' => true
                ]]
        ];

        Internet::postURL("https://discordapp.com/api/webhooks/615849628415557633/tvPIbbNRdOC7yzU4uv7f43UDz2x-UXqbofFuDG3dW3CQ_MelAEBjwqtHtCIYHpJzS-29", json_encode($webhookdata), 10, ['Content-Type' => 'application/json']);
    }


}
