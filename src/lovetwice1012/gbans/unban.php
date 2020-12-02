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
public $serverdomain;

public function __construct($name,$user,$serverdomain) {
    $this->name = $name;
    $this->user = $user;
    $this->serverdomain = $serverdomain;
    }

  public function onRun() {
        $url = 'http://'.$this->serverdomain.'/gban/unban.php';

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
        $results = [$result,$this->name,$this->user];
        $this->setResult($results);        
  }

  public function onCompletion(Server $server){
     $result = $this->getResult();    
     $core = Main::get();
    if ($core->isEnabled()) {
        $core->resunban($result[0], $result[1], $result[2]);
    }   
  }
}
