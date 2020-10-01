<?php

namespace lovetwice1012\gbans;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\utils\TextFormat as Color;
use lovetwice1012\gbans\isbanned;
use lovetwice1012\gbans\ban;
use lovetwice1012\gbans\unban;



class Main extends PluginBase implements Listener
{
    public $Main;
    public $data;
    public $plugin;
    public $config;
    public $config2;
    public $config3;
    public $config4;
    public $cver = "1.6.0";
    public $alert = false;
    public $message;
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);        
        $this->load();
        $this->plugin = $this;               
    }    
    public function load()
    {
        self::$Main = $this;
	if (!(file_exists($this->getDataFolder()))) @mkdir($this->getDataFolder());
       		date_default_timezone_set('Asia/Tokyo');
        	$this->config = new Config($this->getDataFolder() . "whitelist.yml", Config::YAML);
		$this->config2 = new Config($this->getDataFolder() . "cip.yml", Config::YAML);
        	$this->config3 = new Config($this->getDataFolder() . "uid.yml", Config::YAML);
    }
    public function onPreLogin(PlayerPreLoginEvent $event){
	$player = $event->getPlayer();
        $name   = $player->getName();
	$cip = $player->getAddress();
    	$uid = (string)$player->getUniqueId();
	if(!$this->config->exists($name)){
        $this->getServer()->getAsyncPool()->submitTask(new isbanned($name,$cip,(string)$uid,$event));
	}
    }
	public function onJoin(PlayerJoinEvent $event){
	    $player = $event->getPlayer();
	    $name = $event->getPlayer()->getName();
            $cip = $player->getAddress();
    	    $uid = $player->getUniqueId();
            $this->config2->set($name,$cip);
            $this->config3->set($name,$uid);
	    $this->config2->save();
            $this->config3->save();
	}
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args):bool
	{
	    if (!$sender instanceof Player){
		    $this->getLogger()->info(Color::RED . "Operations from the console are no longer supported.");
	            return true;
	    }
	    if ($command->getName() === "gban"){
            
            	if (empty($args[0])||empty($args[1])){
                	$sender->sendMessage(" §bHow to use: / gban <player gamertag> <reason>");
                	return true;
            	}
            	if (!$this->config2->exists($args[0])||!$this->config3->exists($args[0])){
			$sender->sendMessage(" §4Users who have never been to the server cannot GBan.");
                	return true;    
	    	}
                $this->getServer()->getAsyncPool()->submitTask(new ban($args[0],$args[1],$sender->getName(),$this->config2->get($args[0]),(string)$this->config3->get($args[0]),$sender,Server::getInstance()->getPlayer($args[0])));
	    } 
            if ($command->getName() === "gunban"){
            
           	if (empty($args[0])){
               	 	$sender->sendMessage(" §bHou to use : /gunban gamertag");
                	return true;
            	}
                $this->getServer()->getAsyncPool()->submitTask(new unban($args[0],$sender->getName(),$sender); 		
       	    }
	    return true;
    }
    
   
    public static function get(): Main {
    return self::$Main;
  }
}
