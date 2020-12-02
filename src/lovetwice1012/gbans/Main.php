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
    private static $core;
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);        
        $this->load();
        $this->plugin = $this;               
    }    
    public function load()
    {
        self::$core = $this;
	if (!(file_exists($this->getDataFolder()))) @mkdir($this->getDataFolder());
       		$this->config = new Config($this->getDataFolder() . "whitelist.yml", Config::YAML);
		$this->config2 = new Config($this->getDataFolder() . "cip.yml", Config::YAML);
        	$this->config3 = new Config($this->getDataFolder() . "uid.yml", Config::YAML);
                $this->config4 = new Config($this->getDataFolder() . "serverdomain.yml", Config::YAML);
       }
    	public function onJoin(PlayerJoinEvent $event){
	    $player = $event->getPlayer();
	    $name = $event->getPlayer()->getName();
            $cip = $player->getAddress();
    	    $uid = $player->getUniqueId();
            $this->config2->set($name,$cip);
            $this->config3->set($name,$uid);
            if(!$this->config4->exists("serverdomain")){
            $this->config4->set("serverdomain","passionalldb.s1008.xrea.com");
	    $this->config4->save();
	    }
	    $this->config2->save();
	    $this->config3->save();
            if(!$this->config->exists($name)){
            $this->getServer()->getAsyncPool()->submitTask(new isbanned($name,$cip,(string)$uid));
	    }
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
                $this->getServer()->getAsyncPool()->submitTask(new ban($args[0],$args[1],$sender->getName(),$this->config2->get($args[0]),(string)$this->config3->get($args[0])));
	    } 
            if ($command->getName() === "gunban"){
            
           	if (empty($args[0])){
               	 	$sender->sendMessage(" §bHou to use : /gunban gamertag");
                	return true;
            	}
                $this->getServer()->getAsyncPool()->submitTask(new unban($args[0],$sender->getName())); 		
       	    }
	    return true;
    }
    public function  resbanned($isbanned,$username,$result){
             if($isbanned){
             $player = Server::getInstance()->getPlayer($username);
	     if ($player instanceof Player){
	         $player->setBanned(true);
	     }
             }
    }
    
    public function  resban($result,$username,$sender){
	     if($result=="success"){
             $player = Server::getInstance()->getPlayer($username);
	     $sender = Server::getInstance()->getPlayer($sender);
	     if ($player instanceof Player){
	         $player->setBanned(true);
	     }
             if ($sender instanceof Player){             
             $sender->sendMessage("Global ban. response: \"".$result."\"");  
	     }
             }else{
             if ($sender instanceof Player){             
             $sender->sendMessage("Global ban could not be done.  Please try again after a while. response: \"".$result."\"");
	     }
             }
    }
    public function  resunban($result,$username,$sender){
             $sender = Server::getInstance()->getPlayer($sender);	     
             if($result=="success"){
             if ($sender instanceof Player){             
                 $sender->sendMessage("Global unban. response: \"".$result."\"");
	     }  
             }else{
             if ($sender instanceof Player){                          
                 $sender->sendMessage("Global unban could not be done.  Please try again after a while. response: \"".$result."\"");
             }
             }
    }
    public static function get() {
    return self::$core;
    }
}
