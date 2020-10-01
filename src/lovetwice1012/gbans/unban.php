<?php

declare(strict_types = 1);

namespace lovetwice1012\gbans;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\VersionString;
use lovetwice1012\gbans\Main;
class unban extends AsyncTask {
public $name;
public $sender;
public $user;
public function __construct($name,$user,$sender) {
    $this->name = $name;
    $this->user = $user;
    $this->sender = $sender;
  }

  public function onRun() {
        $url = 'http://passionalldb.s1008.xrea.com/gban/unban.php';

        $data = array(
            'unban' => 'unban',
            'username' => $this->name,
            'user' => $this->user
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
                	           		$this->sender->sendMessage("Canceled the damage report from this server. response: \"".$this->message."\"");
        }else{
		     	$this->sender->sendMessage("The damage report from this server could not be undone.  The request has been rejected. response: \"".$this->message."\"");
                    	$this->sender->sendMessage("§4[Caution] The UNBAN command is executed by the OP who banned the person, and will be rejected unless it is executed on the server that was when the person was banned.");
		     $this->setResult(false);
                	
        }
  }

  public function onCompletion(Server $server){
  $result = $this->getResult();          	
  }
}
