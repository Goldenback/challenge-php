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
		$page = $this->db->getOneBy('pages', ['is_published' => true, 'is_home' => true]);

		if (!$page && requireRole('admin')) {
			header('Location: /admin/pages');
			exit;
		} else if (!$page) {
			echo 'Erreur 404';
			return;
		}

		header('Location: /page?slug=' . $page['slug']);
	}
}
