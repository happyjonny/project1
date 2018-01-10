<?php

  class indexController extends Controller
  {
    private $index;

    public function __construct()
    {
//      echo '111';
      parent::__construct();
//      echo '222';
      $this->index = new indexModel;
    }

    public function index()
    {
      include_once 'View/index/index.html';
    }


  }

