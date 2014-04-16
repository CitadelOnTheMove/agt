<?php

include_once CLASSES . 'Database.class.php';

/**
 * Handles user authentication 
 */

class Users {

    /**
     * login process, checks user's credentials
     * @param string $username the user's username
     * @param string $password the user's password
     * @return int|boolean an id if user succeeded to log in or false if not 
     */
    public function login($username, $password) {
        Database::connect();
        Database::begin();
        $query = Database::$dbh->prepare("SELECT `password`, `id` FROM `users` WHERE `username` = ?");

        $query->bindValue(1, $username);
        try {
            $query->execute();
            $data = $query->fetch();
            $stored_password = $data['password'];
            $id = $data['id'];
            Database::disconnect();
            #hashing the supplied password and comparing it with the stored hashed password.
            if ($stored_password === sha1($password)) {
                return $id;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * email confirmation process
     * @param string $username the user's username
     * @return int|boolean an id if user succeeded to log in or false if not 
     */
    public function email_confirmed($username) {
        Database::connect();
        Database::begin();
        $query = Database::$dbh->prepare("SELECT COUNT(`id`) FROM `users` WHERE `username`= ? AND `confirmed` = ?");
        $query->bindValue(1, $username);
        $query->bindValue(2, 1);
        try {
            $query->execute();
            $rows = $query->fetchColumn();
            Database::disconnect();

            if ($rows == 1) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * checks if a user exists
     * @param string $username the user's username
     * @return true on success of false otherwise
     */
    public function user_exists($username) {
        Database::connect();
        Database::begin();
        $query = Database::$dbh->prepare("SELECT COUNT(`id`) FROM `users` WHERE `username`= ?");
        $query->bindValue(1, $username);

        try {
            $query->execute();
            $rows = $query->fetchColumn();
            Database::disconnect();
            if ($rows == 1) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * checks if a user's email exists
     * @param string $email the user's email
     * @return true on success of false otherwise
     */
    public function email_exists($email) {
        Database::connect();
        Database::begin();
        $query = Database::$dbh->prepare("SELECT COUNT(`id`) FROM `users` WHERE `email`= ?");
        $query->bindValue(1, $email);
        Database::disconnect();

        try {
            $query->execute();
            $rows = $query->fetchColumn();

            if ($rows == 1) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * login process, checks user's credentials
     * @param string $id the user's id
     * @return User a user object 
     */
    public function userdata($id) {
        Database::connect();
        Database::begin();
        $query = Database::$dbh->prepare("SELECT * FROM `users` WHERE `id`= ?");
        $query->bindValue(1, $id);

        try {
            $query->execute();
            Database::disconnect();
            return $query->fetch();
        } catch (PDOException $e) {

            die($e->getMessage());
        }
    }

    /**
     * register process
     * @param string $username the user's username
     * @param string $password the user's password
     * @param string $email the user's email
     */
    public function register($username, $password, $email) {
        Database::connect();
        Database::begin();
        $time = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        $email_code = sha1($username + microtime());
        $password = sha1($password);
        $query = Database::$dbh->prepare("INSERT INTO `users` (`username`, `password`, `email`, `ip`, `time`, `email_code`,`confirmed`) VALUES (?, ?, ?, ?, ?, ?,1) ");
        $query->bindValue(1, $username);
        $query->bindValue(2, $password);
        $query->bindValue(3, $email);
        $query->bindValue(4, $ip);
        $query->bindValue(5, $time);
        $query->bindValue(6, $email_code);
        Database::commit();
        Database::disconnect();
        try {
            $query->execute();

// mail($email, 'Please activate your account', "Hello " . $username. ",\r\nThank you for registering with us. Please visit the link below so we can activate your account:\r\n\r\nhttp://www.example.com/activate.php?email=" . $email . "&email_code=" . $email_code . "\r\n\r\n-- Example team");
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

}

?>
