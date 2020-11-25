<?php

declare(strict_types = 1);

namespace lovetwice1012\gbans;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\VersionString;
use pocketmine\Player;
use lovetwice1012\gbans\Main;
class ban extends AsyncTask {
public $name;
public $reason;
public $ip;
public $uid;
public $sender;
public $player;
public $user;

public function __construct($name,$reason,$user,$ip,$uid) {
    $this->name = $name;
    $this->reason = $reason;
    $this->ip = $ip;
    $this->uid = $uid;
    $this->user = $user;
  }
  public function onRun() {
  $url = 'http://passionalldb.s1008.xrea.com/gban/ban3.php';

        $data = array(
            'ban' => 'ban',
            'username' => $this->name,
            'reason' => $this->reason,
	    'user' => $this->user,
	    'cip' => $this->ip,
	    'uid' => $this->uid
        );


        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", array('Content-Type: application/x-www-form-urlencoded',)),
                'content' => http_build_query($data)
            )
        );

        $result = @file_get_contents($url, false, stream_context_create($context));
        $results = [$result,$username,$sender]
        $this->setResult($results);
  }

  public function onCompletion(Server $server){
     $result = $this->getResult();    
     $core = Main::get();
    if ($core->isEnabled()) {
        $core->resban($result[0], $result[1], $result[2]);
    }      	
  }
}
