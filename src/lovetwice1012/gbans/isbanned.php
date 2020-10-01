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

public function __construct($name,$ip,$uid,$event) {
    $this->name = $name;
    $this->ip = $ip;
    $this->uid = $uid;
    $this->event = $event;
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
             $this->setResult(true);
             $this->event->setkickMessage("§4You are banned.");
             $this->event->setCancelled();
        }else{
             $this->setResult(false);
        }
  }

  public function onCompletion(Server $server){
     $result = $this->getResult();          	
  }
}
