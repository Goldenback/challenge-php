<?php

namespace App\src\Core;

use App\src\Models\Page;
use App\src\Models\User;
use PDO;
use PDOException;

class DB
{
	private static ?DB $instance = null;
	private PDO $connection;

	private static array $tableMapping = [
		'users' => User::class,
		'pages' => Page::class,
	];

	private function __construct()
	{
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		];

		// Vérifier si DATABASE_URL est définie (environnement Heroku)
		$databaseUrl = getenv('DATABASE_URL');

		if ($databaseUrl) {
			// Parse DATABASE_URL
			$dbOpts = parse_url($databaseUrl);

			$host = $dbOpts["host"] ?? null;
			$port = $dbOpts["port"] ?? '5432'; // Port par défaut PostgreSQL
			$database = ltrim($dbOpts["path"], '/') ?? null;
			$user = $dbOpts["user"] ?? null;
			$password = $dbOpts["pass"] ?? null;
		} else {
			// Informations de connexion locales ou par défaut
			$host = 'localhost';
			$port = '5432';
			$database = 'db_name';
			$user = 'root';
			$password = 'root';
		}

		$dsn = "pgsql:host={$host};port={$port};dbname={$database}";

		try {
			$this->connection = new PDO($dsn, $user, $password, $options);
		} catch (PDOException $e) {
			// Gérer les exceptions de connexion
			error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
			die('Une erreur est survenue lors de la connexion à la base de données.');
		}
	}

	public static function getInstance(): ?DB
	{
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function getConnection(): PDO
	{
		return $this->connection;
	}

	public function getOneBy(string $table, array $criteria, string $returnType = "array")
	{
		$sql = "SELECT * FROM " . $table . " WHERE ";
		foreach ($criteria as $column => $value) {
			$sql .= $column . "=:" . $column . " AND ";
		}
		$sql = rtrim($sql, ' AND ');

		$stmt = $this->connection->prepare($sql);
		$stmt->execute($criteria);

		if ($returnType == "object") {
			return $stmt->fetchObject($this->getClassNameFromTable($table));
		} else {
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
	}

	private function getClassNameFromTable(string $table): string
	{
		if (array_key_exists($table, self::$tableMapping)) {
			return self::$tableMapping[$table];
		} else {
			throw new \Exception("Aucune classe trouvée pour la table '{$table}'.");
		}
	}

	public function insert(string $table, array $data): bool
	{
		$columns = implode(', ', array_keys($data));
		$placeholders = ':' . implode(', :', array_keys($data));

		$sql = "INSERT INTO $table ($columns) VALUES ($placeholders);";
		$stmt = $this->connection->prepare($sql);

		foreach ($data as $key => &$value) {
			// converti les bool
			if (is_bool($value)) {
				$value = $value ? 't' : 'f'; // PostgreSQL accepte 't' et 'f' comme valeurs booléennes
			}
			$stmt->bindParam(":$key", $value);
		}

		return $stmt->execute();
	}

	public function update(string $table, array $criteria, array $data): bool
	{
		$sql = "UPDATE " . $table . " SET ";
		$updates = [];
		foreach ($data as $key => $value) {
			$updates[] = "$key = :$key";
		}
		$sql .= implode(', ', $updates);
		$sql .= " WHERE ";
		foreach ($criteria as $key => $value) {
			$sql .= "$key = :$key AND ";
		}
		$sql = rtrim($sql, ' AND ');

		$stmt = $this->connection->prepare($sql);
		return $stmt->execute(array_merge($data, $criteria));
	}

	public function delete(string $table, array $criteria): bool
	{
		$sql = "DELETE FROM $table WHERE ";
		foreach ($criteria as $key => $value) {
			$sql .= "$key = :$key AND ";
		}
		$sql = rtrim($sql, ' AND ');

		$stmt = $this->connection->prepare($sql);
		return $stmt->execute($criteria);
	}
}