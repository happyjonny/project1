<?php

  class sendSMS
  {
    private $rest;

    public function __construct()
    {
      include_once '../public/CCPRestSDK.php';
      $this->rest = new REST(SERVERIP, SERVERPORT, SOFTVERSION);
      $this->rest->setAccount(ACCOUNTSID, ACCOUNTTOKEN);
      $this->rest->setAppId(APPID);
    }

    public function sendsms($to, $datas = array(), $time)
    {
      $res = $this->rest->sendTemplateSMS($to, $datas, $time);
    }
  }