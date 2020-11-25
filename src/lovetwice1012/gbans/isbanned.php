<?php

declare(strict_types = 1);

namespace lovetwice1012\gbans;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\VersionString;
use lovetwice1012\gbans\Main;
class isbanned extends AsyncTask {

public $name;
public $ip;
public $uid;
public $event;

public function __construct($name,$ip,$uid) {
    $this->name = $name;
    $this->ip = $ip;
    $this->uid = $uid;
}

  public function onRun() {
    $url = 'http://passionalldb.s1008.xrea.com/gban/check3.php';

        $data = array(
            'check' => 'check',
            'username' => $this->name,
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
        if($result=="Banned"){
		$isbanned = true;	
	}else{
		$isbanned = false;
	}
        $results = [$isbanned,$this->name,$result];
        $this->setResult($results);
      }

  public function onCompletion(Server $server){
     $result = $this->getResult();    
     $core = Main::get();
    if ($core->isEnabled()) {
        $core->resbanned($result[0], $result[1], $result[2]);
    }      	
  }
}
