<?php

declare(strict_types = 1);

namespace lovetwice1012/gbans;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\VersionString;

class isbanned extends AsyncTask {

  public function onRun() {
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://api.github.com/repos/fuyutsuki/Texter/releases",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_USERAGENT => "php_".PHP_VERSION,
      CURLOPT_SSL_VERIFYPEER => false
    ]);
    $json = curl_exec($curl);
    $errorNo = curl_errno($curl);
    if ($errorNo) {
      $error = curl_error($curl);
      throw new \Exception($error);
    }
    curl_close($curl);
    $data = json_decode($json, true);
    $this->setResult($data);
  }

  public function onCompletion(Server $server){
    $core = Core::get();
    if ($core->isEnabled()) {
      $data = $this->getResult();
      if (isset($data[0])) {
        $ver = new VersionString($data[0]["name"]);
        $core->compareVersion(true, $ver, $data[0]["html_url"]);
      }else {
        $core->compareVersion(false);
      }
    }
  }
}
