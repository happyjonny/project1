<?php

  class indexController extends Controller
  {
    private $index;

    public function __construct()
    {
      parent::__construct();
      $this->index = new indexModel;
    }

    public function index()
    {


      include_once 'View/index/index.html';

    }


  }

