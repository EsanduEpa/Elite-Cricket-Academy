<?php
    class Database {
        private $host = DB_HOST;
        private $user = DB_USER;
        private $password = DB_PASS;
        private $dbname = DB_NAME;

        private $dbh;
        private $statement;
        private $error;

        public function __construct() {
            $dsn = "mysql:host=". $this->host .";dbname=". $this->dbname;
            $options = array(
                // Use non-persistent connections to avoid hanging on bad pooled connections
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Shorter timeouts so a bad DB connection doesn't hang forever
                PDO::ATTR_TIMEOUT => 5
            );

            //initiate pdo
            try {
                $this->dbh = new PDO($dsn, $this->user, $this->password, $options);
                
                // Set PDO to preserve natural column name casing from database
                $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
                
                // Ensure error mode and default fetch mode are set
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            } catch (PDOException $e) {
                $this->error = $e->getMessage();
                echo "Connection failed: " . $this->error;
            }
        }

        //perpare statement
        public function query($sql) {
            $this->statement = $this->dbh->prepare($sql);

    }

    //bind values
    public function bind($param, $value, $type = null) {
            if (is_null($type)) {
                switch(true){
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }
            $this->statement->bindValue($param, $value, $type);
        }
        //excute the prepared statement
        public function execute() {
            try {
                $result = $this->statement->execute();
                if (!$result) {
                    $errorInfo = $this->statement->errorInfo();
                    error_log("Database execution failed with error: " . print_r($errorInfo, true));
                }
                return $result;
            } catch (PDOException $e) {
                error_log("Database execution error: " . $e->getMessage());
                error_log("SQL Error Info: " . print_r($this->statement->errorInfo(), true));
                return false;
            }
        }

        //get multiple records as result set
        public function resultSet() {
            $this->execute();
            return $this->statement->fetchAll(PDO::FETCH_OBJ);
        }

        //get single record as object
        public function single(){
            $this->execute();
            return $this->statement->fetch(PDO::FETCH_OBJ);
        }

        //get row count
        public function rowCount(){
            return $this->statement->rowCount();
        }

        //get last insert id
        public function lastInsertId(){
            return $this->dbh->lastInsertId();
        }

        public function beginTransaction(){
            return $this->dbh->beginTransaction();
        }

        public function commit(){
            return $this->dbh->commit();
        }

        public function rollBack(){
            if ($this->dbh->inTransaction()) {
                return $this->dbh->rollBack();
            }
            return false;
        }

        public function inTransaction(){
            return $this->dbh->inTransaction();
        }

        //get error info from last statement
        public function getError(){
            return $this->statement ? $this->statement->errorInfo() : ['00000', null, 'No statement executed'];
        }
    }
?>