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

			include 'View/index/index.html';
		}

		public function top()
		{
			include 'View/index/top.html';
		}

		public function left()
		{
			include 'View/index/left.html';
		}

		public function bottom()
		{
			include 'View/index/bottom.html';
		}

		public function main()
		{
      $data = $this->index->getAdminInfo();


			include 'View/index/main.html';
		}

		public function swich()
		{
			include 'View/index/swich.html';
		}


  }

