<?php

namespace BeholderWebClient\Eyes\Db;

use Exception;

use BeholderWebClient\Eyes\Db\DbStatus as Status;
use BeholderWebClient\Eyes\AbstractEye;

abstract class AbstractDb extends AbstractEye implements iDb {

  abstract protected function testConn();
  abstract protected function testQuery();
  abstract protected function getDefaultPort();

  protected $code;
  protected $message;
  protected $port;

  public function __construct($conf){

    if(!isset($conf['port']))
      $conf['port'] = $this->getDefaultPort();

    parent::__construct($conf);

  }

  public function look(){

    try {

      $this->checkRequirement();

      $this->testConn();

      if(isset($this->conf['query']) and is_null($this->code))
        $this->testQuery();

      if( is_null($this->code) ) {
        $this->code = Status::ok_number;
        $this->message = 'everything worked';
      }

    }catch( Exception $ex ) {
      $this->code = Status::internalServerError_number;
      $this->message = $ex->getMessage();
    }

    $this->closeConnection();

  }

  protected function closeConnection(){

    if($this->adapter){
      $this->adapter->closeConnection();
    }

  }

  public function getMessage(){
    return $this->message;
  }

  public function getStatusCode(){
    return $this->code;
  }

}
