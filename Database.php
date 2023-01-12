<?php
require_once $_SERVER['DOCUMENT_ROOT'].'\Table.php';
//require_once 'Table.php';
// Partir d'un code pour en générer un projet
// Dans le cadre de la BDD, on appelle ça des "migrations"

// Fonctionnement des migrations :
// A l'état actuel, je veux une table personnes avec 3 colonnes
// Par rapport à l'état précédent, on ajoute une table produits avec 5 colonnes
// Par rapport à l'état précédent, ...


// Exemple : Backup incrémentale ou full
// Jour 1 : A (12Go)                    Jour 1 : A (12Go)
// Jour 2 : +B (5Mo)                    Jour 2 : B (12Go)
// Jour 3 : +C (500Ko)                  Jour 3 : C (12Go)
// Jour 4 : +D (8Mo)                    Jour 4 : D (13Go)
// En cas de restauration : A+B+C+D     // En cas de restauration : D

abstract class Database
{
    // Ce n'est pas un ORM, mais ça interagit avec la structure de la base de données

    /**
     * Singleton de connexion à la base de données
     * Un singleton ne sera instancié qu'une fois dans toute l'application
     */
    private static ?PDO $pdo = null;

    /**
     * Méthode permettant de récupérer (et si nécessaire d'instancier) une connexion à la BDD
     */
    private static function getDatabase()
    {
        if (!self::$pdo) {
            // TODO Récupérer ces informations d'un fichier .env (ou plus généralement de configuration)
            $config = [
                "host" => "localhost",
                "port" => 3306,
                "username" => "root",
                "password" => "",
                "engine" => "mysql"
            ];
            // Création d'une instance PDO
            // Utilisation de sprintf : https://php.net/sprintf
            self::$pdo = new PDO(sprintf(
                "%s:host=%s:%s",
                $config["engine"],
                $config["host"],
                $config["port"]
            ), $config["username"], $config["password"], [
                // https://www.php.net/manual/fr/pdo.constants.php
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ]);
        }
        return self::$pdo;
    }

    public static function createDatabaseIfNotExists(string $database)
    {
        $pdo = self::getDatabase();
        $stmt = $pdo->prepare(sprintf("CREATE DATABASE IF NOT EXISTS %s", $database));
        $stmt->execute();

        self::use($database);
    }

    public static function dropDatabase(string $database)
    {
        $pdo = self::getDatabase();
        $stmt = $pdo->prepare(sprintf("DROP DATABASE %s", $database));
        $stmt->execute();
    }

    public static function doesDatabaseExist(string $database) : bool
    {
        $pdo = self::getDatabase();
        $stmt = $pdo->prepare("SHOW DATABASES");
        $stmt->execute();
        $databases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($databases as $row)
        {
            if($row["Database"] == $database)
            {
                return true;
            }
        }
        return false;
    }

    public static function use(string $database)
    {
        $pdo = self::getDatabase();
        $stmt = $pdo->prepare(sprintf("USE %s", $database));
        $stmt->execute();
    }

    public static function createTable(Table $table)
    {
        $pdo = self::getDatabase();
        // CREATE TABLE table (colonne type extras, colonne type extras, ... PRIMARY KEY(colonne));
        $sql = sprintf("CREATE TABLE %s (", $table->name);
        foreach ($table->columns as $column) {
            $sql .= sprintf("%s %s %s, ", $column->name, $column->type, $column->extras);
        }
        $sql .= sprintf(" PRIMARY KEY(%s)", $table->primaryKey);
        $sql .= ")";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    public static function createTables(array $tables)
    {
        foreach ($tables as $table) {
            self::createTable($table);
        }
    }

    public static function migrate(string $database, array $tables)
    {
        self::createDatabaseIfNotExists($database);
        self::createTables($tables);
    }

    public static function bulkInsert(string $table, array $columns, array $data)
    {
        // $table = "clients"
        // $columns = ["name"]
        // $data = [ ["Bob"], ["John"], ["Alice"] ]

        // INSERT INTO clients
        $sql = sprintf("INSERT INTO %s ", $table);
        // (name)
        $sql .= "(" . implode(', ', $columns) . ") VALUES ";
        foreach($data as $row)
        {
            // $row = ["John"]
            // (?, ?, ?, ?)
            $sql .= "(" . implode(', ', array_fill(0, count($row), "?")) . "), ";
        }
        // INSERT INTO client (name, truc, machin) VALUES(?, ?, ?), (?, ?, ?), (?, ?, ?), 
        $sql = rtrim($sql, ", ");
        // INSERT INTO client (name, truc, machin) VALUES(?, ?, ?), (?, ?, ?), (?, ?, ?)
        $pdo = self::getDatabase();
        $stmt = $pdo->prepare($sql);

        $i = 1;
        foreach($data as $row)
        {
            // $row = ["John"]
            foreach($row as $value) {
                $stmt->bindValue($i++, $value);
            }
        }
        $stmt->execute();
    }

    public static function truncate(string $table)
    {
        $pdo = self::getDatabase();
        $stmt = $pdo->prepare(sprintf("TRUNCATE TABLE %s", $table));
        $stmt->execute();
    }

    public static function disableForeignKeyChecks()
    {
        $pdo = self::getDatabase();
        $stmt = $pdo->prepare("SET FOREIGN_KEY_CHECKS = 0");
        $stmt->execute();
    }

    public static function enableForeignKeyChecks()
    {
        $pdo = self::getDatabase();
        $stmt = $pdo->prepare("SET FOREIGN_KEY_CHECKS = 1");
        $stmt->execute();
    }

    public static function rawQuery(string $query)
    {
        $pdo = self::getDatabase();
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    }

    // ALTER TABLE comments ADD FOREIGN KEY (client_id) REFERENCES clients(id)
    //  ON DELETE SET NULL
}