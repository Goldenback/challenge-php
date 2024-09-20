<?php

namespace App\src\Controllers;

use App\src\Core\DB;

class HomeController
{
	private DB $db;

	public function __construct()
	{
		$this->db = DB::getInstance();
	}

	public function index(): void
	{
		include __DIR__ . '/../Views/home/index.php';
	}
}
