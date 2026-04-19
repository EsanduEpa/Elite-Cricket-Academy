<?php
    /**
     * Database wrapper around PDO.
     *
     * Viva explanation:
     * Instead of every model creating its own raw PDO code, models call this class.
     * The common pattern is:
     * 1. query('SQL with :placeholders')
     * 2. bind(':placeholder', $value)
     * 3. resultSet(), single(), or execute()
     */
    class Database {
        // Connection values come from app/config/config.php.
        private $host = DB_HOST;
        private $user = DB_USER;
        private $password = DB_PASS;
        private $dbname = DB_NAME;

        // $dbh is the PDO connection, and $statement holds the prepared query.
        private $dbh;
        private $statement;
        private $error;

        public function __construct() {
            // DSN tells PDO which database driver, host, and database name to use.
            $dsn = "mysql:host=". $this->host .";dbname=". $this->dbname;
            $options = array(
                // Use non-persistent connections to avoid hanging on bad pooled connections
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Shorter timeouts so a bad DB connection doesn't hang forever
                PDO::ATTR_TIMEOUT => 5
            );

            // Create the PDO connection once for this Database object.
            // Models use this wrapper instead of writing raw mysqli code everywhere.
            try {
                $this->dbh = new PDO($dsn, $this->user, $this->password, $options);
                
                // Set PDO to preserve natural column name casing from database
                $this->dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
                
                // Ensure error mode and default fetch mode are set
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            } catch (PDOException $e) {
                // If connection fails, store and display the error for local demo debugging.
                // In production, this should be logged without showing details to users.
                $this->error = $e->getMessage();
                echo "Connection failed: " . $this->error;
            }
        }

        // Prepare SQL with placeholders before binding values.
        // Example: SELECT * FROM user WHERE UserID = :id
        public function query($sql) {
            // prepare() compiles the SQL but does not run it yet.
            $this->statement = $this->dbh->prepare($sql);

    }

    // Bind PHP values safely into SQL placeholders.
    // This helps prevent SQL injection and keeps types correct.
    public function bind($param, $value, $type = null) {
            // If no PDO type is supplied, infer the safest matching type from PHP value.
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
        // Execute the prepared SQL statement and log database errors.
        public function execute() {
            try {
                // execute() runs the prepared statement using the bound values.
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

        // Return many rows as objects.
        public function resultSet() {
            // Common for SELECT queries that return lists, such as all notifications.
            $this->execute();
            return $this->statement->fetchAll(PDO::FETCH_OBJ);
        }

        // Return one row as an object.
        public function single(){
            // Common for SELECT queries that should return one item, such as one user.
            $this->execute();
            return $this->statement->fetch(PDO::FETCH_OBJ);
        }

        // Return number of affected/fetched rows from the last statement.
        public function rowCount(){
            // Useful after UPDATE/DELETE to know how many rows changed.
            return $this->statement->rowCount();
        }

        // Return the auto-increment ID created by the latest INSERT.
        public function lastInsertId(){
            // Example: after inserting a notification, this returns NotificationID.
            return $this->dbh->lastInsertId();
        }

        // Transaction helpers are used where multiple database changes must succeed together.
        // Example: create a rental row and reduce stock; if one fails, roll back both.
        public function beginTransaction(){
            // Start a group of database changes that should succeed or fail together.
            return $this->dbh->beginTransaction();
        }

        public function commit(){
            // Permanently save all changes made after beginTransaction().
            return $this->dbh->commit();
        }

        public function rollBack(){
            // Undo changes if something fails halfway through a transaction.
            if ($this->dbh->inTransaction()) {
                return $this->dbh->rollBack();
            }
            return false;
        }

        public function inTransaction(){
            return $this->dbh->inTransaction();
        }

        // Get error info from the last statement for debugging.
        public function getError(){
            return $this->statement ? $this->statement->errorInfo() : ['00000', null, 'No statement executed'];
        }
    }
