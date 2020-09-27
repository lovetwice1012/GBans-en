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

class Main extends PluginBase implements Listener
{
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
        	if($this->isbanned($name,$cip,(string)$uid)){
        		$event->setkickMessage("§4You are banned.");
        		$event->setCancelled();
        	}
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
    public function isbanned($name,$ip,$uid){

        $url = 'http://passionalldb.s1008.xrea.com/gban/check3.php';

        $data = array(
            'check' => 'check',
            'username' => $name,
	    'cip' => $ip,
	    'uid' => $uid
        );

        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded',)),
                'content' => http_build_query($data)
            )
        );

        $result = @file_get_contents($url, false, stream_context_create($context));
        if($result=="Banned"){
            return true;
        }else{
            return false;
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
            	if($this->ban($args[0],$args[1],$sender->getName(),$this->config2->get($args[0]),(string)$this->config3->get($args[0]))){
            		$player = Server::getInstance()->getPlayer($args[0]);
                        if ($player instanceof Player){
			    $player->setBanned(true);
	                }	    
		  	$sender->sendMessage("Global ban. response: \"".$this->message."\"");  
               	 	return true;
           	}else{
			$sender->sendMessage("Global ban could not be done.  Please try again after a while. response: \"".$this->message."\"");
                	return true;
            	}
	     
	    } 
            if ($command->getName() === "gunban"){
            
           	if (empty($args[0])){
               	 	$sender->sendMessage(" §bHou to use : /gunban gamertag");
                	return true;
            	}
            	if($this->unban($args[0],$sender->getName())){
           		$sender->sendMessage("Canceled the damage report from this server. response: \"".$this->message."\"");
                	return true;
            	}else{
		     	$sender->sendMessage("The damage report from this server could not be undone.  The request has been rejected. response: \"".$this->message."\"");
                    	$sender->sendMessage("§4[Caution] The UNBAN command is executed by the OP who banned the person, and will be rejected unless it is executed on the server that was when the person was banned.");
		    	return true;
            	}
		
       	    }
	    return true;
    }
    
    public function ban($name,$reason,$user,$ip,$uid){

        $url = 'http://passionalldb.s1008.xrea.com/gban/ban3.php';

        $data = array(
            'ban' => 'ban',
            'username' => $name,
            'reason' => $reason,
	    'user' => $user,
	    'cip' => $ip,
	    'uid' => $uid
        );

        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded',)),
                'content' => http_build_query($data)
            )
        );

        $result = @file_get_contents($url, false, stream_context_create($context));
	$this->message = $result;
        if($result=="success"){
            return true;
        }else{
	    
            return false;
        }
        
    }      
    public function unban($name,$user){

        $url = 'http://passionalldb.s1008.xrea.com/gban/unban.php';

        $data = array(
            'unban' => 'unban',
            'username' => $name,
            'user' => $user
        );

        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded',)),
                'content' => http_build_query($data)
            )
        );

        $result = @file_get_contents($url, false, stream_context_create($context));
	$this->message = $result;
        if($result=="success"){
            return true;
        }else{
	    
            return false;
        }
        
    }    
    
}
