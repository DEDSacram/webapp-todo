<?php
//include 'db.php';
include 'endpoint.php';
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

// start to be able to reach user

class Database {
    private $conn;
    
    public function __construct() {
        $servername = "127.0.0.1"; // Use localhost or 127.0.0.1 since it's on the host machine
        $port = 3306; // Use the port number you need
        $username = "admin";
        $password = "admin12345";
        $database = "bmwa";
        try {
            $this->conn = new PDO("mysql:host=$servername;port=$port;dbname=$database", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function close() {
        $this->conn = null;
    }


    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getLastInsertedId() {
        return $this->conn->lastInsertId();
    }

}


function getToDoLists($userId) {
    $db = new Database();
    $sql = "SELECT `ListID`, `ListName` FROM `ToDoLists` WHERE `UserID` = :userId";
    $params = array(':userId' => $userId);
    $stmt = $db->query($sql, $params);
    $todoLists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();
    
    // Additional code to handle the response
    if (count($todoLists) > 0) {
        send_response($todoLists);
    } else {
        send_response(array('message' => 'No todo lists found.'), 404);
    }
}

function getItemsInToDoList($listId, $userId) {
    $db = new Database();
    $sql = "SELECT 
    ToDoItems.ItemID, 
    ToDoItems.ItemName, 
    Subcategories.SubcategoryID, 
    Subcategories.SubcategoryName, 
    Subcategories.Order
FROM 
    ToDoItems 
LEFT JOIN 
    Subcategories 
ON 
    ToDoItems.ItemID = Subcategories.ItemID
INNER JOIN
    ToDoLists
ON
    ToDoItems.ListID = ToDoLists.ListID
WHERE 
    ToDoItems.ListID = :listId AND ToDoLists.UserID = :userId";
    $params = array(':listId' => $listId, ':userId' => $userId);
    $stmt = $db->query($sql, $params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();
    
    // Additional code to handle the response
    if (count($items) > 0) {
        send_response($items);
    } else {
        send_response(array('message' => 'No items found in the todo list.'), 404);
    }
}


function addToDoList($listNames, $userId) {
    $db = new Database();
    $listIds = array();
    for ($i = 0; $i < count($listNames); $i++) {
        $listName = $listNames[$i];
        $sql = "INSERT INTO `ToDoLists` (`UserID`,`ListName`) VALUES (:userId,:listName)";
        $params = array(':userId' => $userId, ':listName' => $listName);
        $stmt = $db->query($sql, $params);
        $listIds[] = $db->getLastInsertedId();
    }

    $db->close();


    // Add last inserted IDs into an array
    $result = array(
        'lastInsertedIds' => $listIds
    );
    send_response($result);
}




session_start();


// get this after testing from postman
// if(!isset($_SESSION['user'])){
//     send_response([
//         'status' => 0,
//         'message' => 'Nepřihlášený uživatel',
//     ]);
//     return;
// }



$data = get_request_data();

$userId = $_SESSION['user'] ?? $data['UserID'];

switch ($data['action']) {
    case 'gettodolists':
        getToDoLists($userId);
        break;
    case 'addtodolist':
        addTodoList($data['ListNameArray'], $userId);
        break;
    case 'getitemsintodolist':
        getItemsInToDoList($data['ListID'], $userId);
        break;
    default:
        send_response([
            'status' => 0,
            'message' => 'Neplatná akce',
        ]);
}
