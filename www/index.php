<?php

namespace App;

use App\src\Controllers\Error;
use Symfony\Component\Yaml\Yaml;

spl_autoload_register("App\myAutoloader");
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Core/helpers.php';

session_start();

function myAutoloader($class): void
{
	$file = __DIR__ . '/' . str_replace("App\\", "", $class);
	$file = str_replace("\\", "/", $file);
	$file .= ".php";
	if (file_exists($file)) {
		include $file;
	}
}

// Charger les routes YAML
$fileRoute = __DIR__ . '/routes.yaml';
if (!file_exists($fileRoute)) {
	die("Le fichier de routing n'existe pas");
}

$listOfRoutes = Yaml::parseFile($fileRoute);
if (!$listOfRoutes) {
	die("Erreur lors du chargement des routes. Veuillez vérifier le format YAML.");
}

// Récupérer l'URI
$uri = strtolower($_SERVER["REQUEST_URI"]);
$uri = strtok($uri, "?");  // Enlever les paramètres GET
if (strlen($uri) > 1) {
	$uri = rtrim($uri, "/");
}

// Vérifier si la route existe
if (!empty($listOfRoutes[$uri])) {
	$route = $listOfRoutes[$uri];
	$controller = $route['controller'] ?? null;
	$action = $route['action'] ?? null;

	if ($controller && $action) {
		$controllerPath = __DIR__ . "/src/Controllers/{$controller}.php";
		if (file_exists($controllerPath)) {
			include $controllerPath;
			$controllerClass = "App\\src\\Controllers\\{$controller}";

			if (class_exists($controllerClass)) {
				$object = new $controllerClass();
				if (method_exists($object, $action)) {
					$object->$action();
				} else {
					die("L'action '{$action}' n'existe pas dans le contrôleur '{$controllerClass}'.");
				}
			} else {
				die("Le contrôleur '{$controllerClass}' n'existe pas.");
			}
		} else {
			die("Le fichier du contrôleur '{$controller}' n'existe pas.");
		}
	} else {
		die("La route '{$uri}' ne possède pas de contrôleur ou d'action.");
	}
} else {
	include __DIR__ . '/src/Controllers/Error.php';
	$errorController = new Error();
	$errorController->error404();
}
