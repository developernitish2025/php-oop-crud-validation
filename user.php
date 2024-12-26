<?php
require_once 'db.php';

class User {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAllUsers() {
        $stmt = $this->conn->prepare( 'SELECT * FROM users' );
        $stmt->execute();
        return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    public function getUserById( $id ) {
        $stmt = $this->conn->prepare( 'SELECT * FROM users WHERE id = :id' );
        $stmt->execute( [ 'id' => $id ] );
        return $stmt->fetch( PDO::FETCH_ASSOC );
    }

    public function createUser( $data ) {
        $stmt = $this->conn->prepare( 'INSERT INTO users (name, email, photo, contact) VALUES (:name, :email, :photo, :contact)' );
        return $stmt->execute( $data );
    }

    public function updateUser( $id, $data ) {
        $stmt = $this->conn->prepare( 'UPDATE users SET name = :name, email = :email, photo = :photo, contact = :contact WHERE id = :id' );
        $data[ 'id' ] = $id;
        return $stmt->execute( $data );
    }

    public function deleteUser( $id ) {
        $stmt = $this->conn->prepare( 'DELETE FROM users WHERE id = :id' );
        return $stmt->execute( [ 'id' => $id ] );
    }
}
?>
