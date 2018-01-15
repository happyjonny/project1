<?php

  class indexModel
  {
    private $pdo;

    public function __construct()
    {
      $this->pdo = new DB;
    }

    public function getAdminInfo()
    {
      try {
        $res[] = $this->pdo
          ->field(' name, lasttime, ipaddress, lcount')
          ->table(' admin ')
          ->where(' id = ' . $_SESSION['admin']['id'])
          ->find();
        $tmp = $this->pdo
          ->field(' count(id) as count')
          ->table('admin')
          ->select();
        $res[0]['count'] = $tmp[0]['count'];
        $tmp1 = $this->pdo
          ->field(' VERSION() as version')
          ->table('admin')
          ->select();

        $res[0]['mysqlv'] = $tmp1[0]['version'];
        unset($tmp);
        return $res;
      } catch (Exception $e) {
        myNotice('请联系管理员','./index.php');
      }
    }

  }

