<?php
namespace MyApp\Config;

use RedBeanPHP\R;

class DatabaseConfig
{
    public static function init(): void
    {
        // Setup DB connection
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $_ENV['DB_HOST'], $_ENV['DB_NAME']);

        R::setup($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);

        if (!R::testConnection()) {
            throw new \Exception("Failed to connect to the database");
        }

        // Freeze schema in production
        if ($_ENV['DB_FREEZE'] === 'true') {
            R::freeze(true);  // Production: schema locked
        } else {
            R::freeze(false); // Development: schema auto-updates
        }
    }
}
