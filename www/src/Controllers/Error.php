<?php

namespace App\src\Controllers;

class Error
{
	public function error404(): void
	{
		http_response_code(404);
		echo "Page non trouvée (404)";
	}
}
