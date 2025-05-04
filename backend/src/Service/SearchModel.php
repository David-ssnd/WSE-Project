<?php


namespace App\Service;

use PDO;


class SearchModel
{
    private PDO $pdo;

    public function __construct()
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            getenv('POSTGRES_HOST') ? getenv('POSTGRES_HOST') : 'localhost',
            getenv('POSTGRES_PORT') ? getenv('POSTGRES_PORT') : 5432,
            getenv('POSTGRES_DB') ? getenv('POSTGRES_DB') : 'your_database'
        );
        
        // setup DB connection options
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        // get username and password from the environment
        $dbUser = getenv('POSTGRES_USER') ?? 'default_user';
        $dbPass = getenv('POSTGRES_PASSWORD') ?? 'default_password';
        
        // create a new PDO - PHP Data Objects instance
        $this->pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    }

}