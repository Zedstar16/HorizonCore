<?php

declare(strict_types=1);

namespace Zedstar16\HorizonCore;

use pocketmine\event\Event;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;
use Zedstar16\HorizonCore\commands\admin\EvalCommand;
use Zedstar16\HorizonCore\commands\admin\FSenderCommand;
use Zedstar16\HorizonCore\commands\admin\SetKitCommand;
use Zedstar16\HorizonCore\commands\BaseCommand;
use Zedstar16\HorizonCore\commands\GamemodeCommand;
use Zedstar16\HorizonCore\commands\ReportCommand;
use Zedstar16\HorizonCore\components\misc\FakeCommandSender;
use Zedstar16\HorizonCore\events\HorizonEvent;
use Zedstar16\HorizonCore\libs\muqsit\invmenu\InvMenuHandler;
use Zedstar16\HorizonCore\libs\xenialdan\apibossbar\API;
use Zedstar16\HorizonCore\libs\xenialdan\apibossbar\BossBar;
use Zedstar16\HorizonCore\listeners\InventoryEventListener;
use Zedstar16\HorizonCore\listeners\NetworkListener;
use Zedstar16\HorizonCore\listeners\EntityEventListener;
use Zedstar16\HorizonCore\listeners\PlayerEventListener;
use Zedstar16\HorizonCore\managers\FFAManager;
use Zedstar16\HorizonCore\managers\FileManager;
use Zedstar16\HorizonCore\managers\PlayerDataManager;
use Zedstar16\HorizonCore\tasks\BossBarUpdateTask;
use Zedstar16\HorizonCore\tasks\Ticker;

class Horizon extends PluginBase implements Listener
{

    /** @var Horizon|null */
    private static $instance = null;

    public static $players = [];

    public static $currentbossbartick;

    public static $vanished = [];

    public const prefix = "§7[§6Horizon§cPE§7]";

    public function onEnable(): void
    {
        self::$instance = $this;
        self::$currentbossbartick = 0;
        API::load($this);
        $this->initializeFiles();
        $this->getLogger()->info("Horizon Core Enabled");
        $this->initializeListeners();
        $this->loadCommands();
        $this->initializePermissions();
        $this->registerTasks();
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        FFAManager::loadArenas();
    }

    public static function Config(): array
    {
        return [
            "scoreboardtick" => 5,
            "praczone" => [
                    "max" => [
                        "x" => 4,
                        "y" => 72,
                        "z" => 4
                    ],
                    "min" => [
                        "x" => -4,
                        "y" => 65,
                        "z" => -4
                    ]
                ],
            "ffalevels" => [""]
        ];
    }

    /**
     * @return Horizon
     */
    public static function getInstance(): Horizon
    {
        return self::$instance;
    }

    public function loadCommands(): void
    {
        $commandMap = $this->getServer()->getCommandMap();
        $commandMap->registerAll("horizon", [
            new BaseCommand(),
            new GamemodeCommand(),
            new ReportCommand(),
            new SetKitCommand(),
            new FSenderCommand(),
            new EvalCommand()
        ]);
    }


    private function initializePermissions()
    {
        $this->addPermissions([
            new Permission("horizon.base", "o", Permission::DEFAULT_OP),
            new Permission("horizon.gamemode", "", "op"),
            new Permission("horizon.gamemode.other", "", "op"),
            new Permission("horizon.report", "", "true"),
            new Permission("horizon.admin", "", "op"),
        ]);
    }

    /**
     * @param array $permissions
     */
    protected function addPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            PermissionManager::getInstance()->addPermission($permission);
        }
    }


    private function initializeListeners()
    {
        $this->registerListeners($this, [
            new PlayerEventListener(),
            new NetworkListener(),
            new EntityEventListener(),
            new InventoryEventListener()
        ]);
    }

    /**
     * @param Plugin $plugin
     * @param array $listeners
     */
    public function registerListeners(Plugin $plugin, array $listeners): void
    {
        foreach ($listeners as $listener) {
            $this->getServer()->getPluginManager()->registerEvents($listener, $plugin);
        }
    }

    public function registerTasks()
    {
        $this->getScheduler()->scheduleRepeatingTask(new Ticker(), 20);
    }

    private function initializeFiles()
    {
        $files = ["reportconfig.yml", "infoui.yml"];
        $path = $this->getDataFolder();
        foreach ($files as $file) {
            $filename = $path . $file;
            if (!(file_exists($filename))) {
                fopen($filename, "w+");
            }
        }
    }

    public static function getPlayer($username) : ?HorizonPlayer{
        $player = Server::getInstance()->getPlayer($username);
        if($player instanceof HorizonPlayer){
            return $player;
        }
        return null;
    }

    public function onDisable(): void
    {
        $this->getLogger()->info("Bye");
    }
}
