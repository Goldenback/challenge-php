<?php

namespace App\src\Controllers;

use JetBrains\PhpStorm\NoReturn;
use ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FeatureController
{
	#[NoReturn] public function exportSolution(): void
	{
		if (!requireRole('admin')) {
			http_response_code(403);
			echo "Accès refusé.";
			exit();
		}

		$archiveFile = __DIR__ . '/../../backup/solution_backup.zip';
		$databaseDump = __DIR__ . '/../../backup/database_dump.sql';

		// Créer le répertoire de sauvegarde s'il n'existe pas
		if (!file_exists(dirname($archiveFile))) {
			mkdir(dirname($archiveFile), 0777, true);
		}

		// Paramètres de la base de données
		$dbHost = 'postgres';
		$dbUser = 'root';
		$dbPass = 'root';
		$dbName = 'db_name';

		// Chemin complet vers pg_dump
		$dumpCommand = "PGPASSWORD='{$dbPass}' /usr/bin/pg_dump --host={$dbHost} --username={$dbUser} {$dbName} > {$databaseDump} 2> /var/www/html/backup/pg_dump_error.log";

		// Exécuter la commande pg_dump et capturer la sortie
		exec($dumpCommand, $output, $returnVar);

		if ($returnVar !== 0) {
			$errorLog = file_get_contents('/var/www/html/backup/pg_dump_error.log');
			echo "Erreur lors de l'export de la base de données.<br>";
			echo "Commande exécutée : " . htmlspecialchars($dumpCommand) . "<br>";
			echo "Sortie : " . htmlspecialchars(implode("\n", $output)) . "<br>";
			echo "Erreur : " . htmlspecialchars($errorLog);
			exit();
		}

		// Créer l'archive ZIP
		$zip = new ZipArchive();
		if ($zip->open($archiveFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
			// Ajouter les fichiers du projet à l'archive
			$sourceDir = realpath(__DIR__ . '/../../');
			$files = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($sourceDir),
				RecursiveIteratorIterator::LEAVES_ONLY
			);

			foreach ($files as $file) {
				if (!$file->isDir()) {
					$filePath = $file->getRealPath();
					// Exclure le dossier backup pour éviter d'ajouter l'archive elle-même
					if (!str_contains($filePath, '/backup/') && !str_contains($filePath, '\backup\\')) {
						$relativePath = substr($filePath, strlen($sourceDir) + 1);
						$zip->addFile($filePath, $relativePath);
					}
				}
			}

			// Ajouter le dump de la base de données à l'archive
			$zip->addFile($databaseDump, 'database_dump.sql');
			$zip->close();
		} else {
			echo "Erreur lors de la création de l'archive ZIP.";
			exit();
		}

		// Supprimer le fichier de dump de la base de données
		unlink($databaseDump);

		// Envoyer l'archive ZIP au navigateur pour téléchargement
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="solution_backup.zip"');
		header('Content-Length: ' . filesize($archiveFile));

		readfile($archiveFile);

		// Supprimer l'archive après téléchargement
		unlink($archiveFile);

		exit();
	}
}
