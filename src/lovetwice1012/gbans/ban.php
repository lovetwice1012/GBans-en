<?php

declare(strict_types = 1);

namespace lovetwice1012\gbans;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\VersionString;
use lovetwice1012\gbans\Main;
class isbanned extends AsyncTask {

  public function onRun($name,$reason,$ip,$uid,$sender,$player) {
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
        if($result=="success"){
            $this->setResult(true);
            
            if ($player instanceof Player){
			          $player->setBanned(true);
	          }	    
		        	$sender->sendMessage("Global ban. response: \"".$result."\"");  
               	 	
			        }else{
             $this->setResult(false);
             $sender->sendMessage("Global ban could not be done.  Please try again after a while. response: \"".$result."\"");
                	
        }
  }

  public function onCompletion(Server $server){
    public static $result = $this->getResult();          	
  }
}