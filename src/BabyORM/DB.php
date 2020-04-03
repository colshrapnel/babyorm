<?php
namespace BabyORM;

class DB
{
    public $pdo;

    protected $driver;

    public function __construct($driver, $host, $user, $pass, $db, $pdo_options = [], $port = 3306, $charset = 'utf8mb4')
    {
        $this->driver = $driver;
        $default_options = [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
        $options = array_replace($default_options, $pdo_options);
        $dsn = $this->getDsn($host, $db, $port, $charset);
        $this->pdo = new \PDO($dsn, $user, $pass, $options);
    }

    public function query($sql, $args = NULL)
    {
        if (!$args)
        {
            return $this->pdo->query($sql);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }

    public function escapeIdent($ident)
    {
        switch ($this->driver) {
            case 'mysql':
                return "`" . str_replace("`", "``", $ident) . "`";
                break;
            default:
                throw new \Exception("You must define escape rules for the driver ($this->driver)");
        }
    }

    protected function getDsn($host, $database, $port, $charset)
    {
        switch ($this->driver) {
            case 'mysql':
                return "$this->driver:host=$host;dbname=$database;port=$port;charset=$charset";
                break;
            default:
                throw new \Exception("Driver ($this->driver) is not supported");
        }
    }
}
