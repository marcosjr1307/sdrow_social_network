<?php
  namespace JetPHP\Model;
  class DB {
    public static $conexao = null;
    public static $qr      = null;
    public static $sql     = null;

    public static function getInstance() {
      return new self;
    }

    public static function connect() {
      if (self::$conexao != null) {
        return new self;
      } else {
        try {

          $host = Config::show('DB_HOST');
          $dbname = Config::show('DB_NOME');
          $username = Config::show('DB_USUARIO');
          $password = Config::show('DB_SENHA');

          self::$conexao = new \PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
          self::$conexao->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
          return new self;
        } catch (\PDOException $e) {
          echo "<p>Erro ao conectar no banco de dados: <b>".$e->getMessage()."</b></p>";
          return false;
        }
      }
    }

    public static function count() {
      return self::$qr->rowCount();
    }

    public static function list($tipo=null) {
      if ($tipo == null) $tipo = \PDO::FETCH_OBJ;
      return self::$qr->fetch($tipo);
    }

    public static function generico() {
      return self::$qr;
    }
    public static function execute($sql, $bp='') {
      if (self::connect()) {
        $qr = self::$conexao->prepare($sql);
        if (is_array($bp)) {
          foreach ($bp as $name=>$param) {
            $name+=1;
            $qr->bindValue($name,$param);
          }
        }
        $qr->execute();
        self::$qr = $qr;
        return new self;
      }
    }

    public static function all($table) {
      $sql = "select * from $table";
      if (self::connect()) {
        $qr = self::$conexao->prepare($sql);

        self::$qr = $qr;
        return new self;
      }
    }

    public static function findById($table, $id) {
      $sql = "select * from $table where id = $id";
      if (self::connect()) {
        $qr = self::$conexao->prepare($sql);

        self::$qr = $qr;
        return new self;
      }
    }

    public static function find($table, $column, $value) {
      $sql = "select * from $table where $column = $value";
      if (self::connect()) {
        $qr = self::$conexao->prepare($sql);

        self::$qr = $qr;
        return new self;
      }
    }
    
    public static function last($table, $where = "1 = 1") {
      $sql = "select * from $table where $where order by id desc limit 1";
      if (self::connect()) {
        $qr = self::$conexao->prepare($sql);

        self::$qr = $qr;
        return new self;
      }
    }
    
    public static function first($table, $where = "1 = 1") {
      $sql = "select * from $table where $where order by id asc limit 1";
      if (self::connect()) {
        $qr = self::$conexao->prepare($sql);

        self::$qr = $qr;
        return new self;
      }
    }

    public static function delete($table, $id) {
      $sql = "delete from $table where id = $id";
      if (self::connect()) {
        $qr = self::$conexao->prepare($sql);

        self::$qr = $qr;
        return new self;
      }
    }
  }
?>
