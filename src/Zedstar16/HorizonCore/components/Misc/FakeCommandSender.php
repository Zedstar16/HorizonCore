<?php


namespace Zedstar16\HorizonCore\components\Misc;


use pocketmine\command\CommandSender;
use pocketmine\command\RemoteConsoleCommandSender;
use pocketmine\lang\TextContainer;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionAttachmentInfo;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class FakeCommandSender implements CommandSender
{

    /** @var PermissibleBase */
    private $perm;
    /** @var String  */
    private $username;
    /** @var RemoteConsoleCommandSender $rsender */
    private $rsender;

    /** @var int|null */
    protected $lineHeight = null;

    public function __construct(String $username, RemoteConsoleCommandSender $rsender){
        $this->perm = new PermissibleBase($this);
        $this->username = $username;
        $this->rsender = $rsender;
    }

    /**
     * @param Permission|string $name
     */
    public function isPermissionSet($name) : bool{
        return $this->perm->isPermissionSet($name);
    }

    /**
     * @param Permission|string $name
     */
    public function hasPermission($name) : bool{
        return $this->perm->hasPermission($name);
    }

    public function addAttachment(Plugin $plugin, string $name = null, bool $value = null) : PermissionAttachment{
        return $this->perm->addAttachment($plugin, $name, $value);
    }

    /**
     * @return void
     */
    public function removeAttachment(PermissionAttachment $attachment){
        $this->perm->removeAttachment($attachment);
    }

    public function recalculatePermissions(){
        $this->perm->recalculatePermissions();
    }

    /**
     * @return PermissionAttachmentInfo[]
     */
    public function getEffectivePermissions() : array{
        return $this->perm->getEffectivePermissions();
    }

    /**
     * @return Server
     */
    public function getServer(){
        return Server::getInstance();
    }

    /**
     * @param TextContainer|string $message
     *
     * @return void
     */
    public function sendMessage($message){
        if($message instanceof TextContainer){
            $message = $this->getServer()->getLanguage()->translate($message);
        }else{
            $message = $this->getServer()->getLanguage()->translateString($message);
        }

        $this->rsender->sendMessage(trim($message, "\r\n"));
    }

    public function getName() : string{
        return $this->username;
    }

    public function isOp() : bool{
        return $this->getServer()->isOp($this->username);
    }

    /**
     * @return void
     */
    public function setOp(bool $value){

    }

    public function getScreenLineHeight() : int{
        return $this->lineHeight ?? PHP_INT_MAX;
    }

    public function setScreenLineHeight(int $height = null){
        if($height !== null and $height < 1){
            throw new \InvalidArgumentException("Line height must be at least 1");
        }
        $this->lineHeight = $height;
    }

}