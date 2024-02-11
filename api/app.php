<?php
include 'db.php';
include 'endpoint.php';
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

// couuld reuse will not
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
    //changed left join to right join to get all the items in the list including null ones in selection (special case)
    $sql = "SELECT 
        ToDoItems.ItemID, 
        ToDoItems.ItemName, 
        Subcategories.SubcategoryID, 
        Subcategories.SubcategoryName, 
        Subcategories.Order
    FROM 
        ToDoItems 
    RIGHT JOIN 
        Subcategories 
    ON 
        ToDoItems.ItemID = Subcategories.ItemID
    INNER JOIN
        ToDoLists
    ON
        ToDoItems.ListID = ToDoLists.ListID
    WHERE 
        ToDoItems.ListID = :listId AND ToDoLists.UserID = :userId
    ORDER BY Subcategories.Order ASC";
    $params = array(':listId' => $listId, ':userId' => $userId);
    $stmt = $db->query($sql, $params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $todoItems = array();
    $subcategories = array();

    //format it nicely

    foreach ($results as $row) {
        $itemId = $row['ItemID'];
        $itemName = $row['ItemName'];
        $subcategoryId = $row['SubcategoryID'];
        $subcategoryName = $row['SubcategoryName'];
        $subcategoryOrder = $row['Order'];

        if (!isset($todoItems[$itemId])) {
            $todoItems[$itemId] = array(
                'itemId' => $itemId,
                'itemName' => $itemName,
                'subcategories' => array()
            );
        }

        $todoItems[$itemId]['subcategories'][] = array(
            'subcategoryId' => $subcategoryId,
            'subcategoryName' => $subcategoryName,
            'subcategoryOrder' => $subcategoryOrder
        );
    }

    $items = array_values($todoItems);
    
    
    $sql2 = "SELECT ListSubcategories.ListSubcategoryID, ListSubcategories.SubcategoryName
    FROM ListSubcategories
    INNER JOIN ToDoLists ON ListSubcategories.ListID = ToDoLists.ListID
    WHERE ToDoLists.ListID = :listId AND ToDoLists.UserID = :userId";
    $stmt2 = $db->query($sql2, $params);
    $subcategories = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $db->close();


// Additional code to handle the response
if (count($items) > 0) {
    $response = array(
        'selection' => $subcategories,
        'display' => $items
    );
    send_response($response);
} else {
    send_response(array('message' => 'No items found in the todo list.'), 404);
}
    
    // Additional code to handle the response
    if (count($items) > 0) {
        send_response($items);
    } else {
        send_response(array('message' => 'No items found in the todo list.'), 404);
    }
}

function checkifchangedtodolist($userId, $data) {
    // get current from db
    $db = new Database();
    $sql = "SELECT ListID, ListName FROM `ToDoLists` WHERE `UserID` = :userId";
    $params = array(':userId' => $userId);
    $stmt = $db->query($sql, $params);
    $todoLists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db->close();

    // compare with new data incoming form the parameter


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


class Difference {
    public $itemId;
    public $subcategoryId;
    public $attribute;
    public $message;

    public function __construct($itemId, $subcategoryId, $attribute, $message) {
        $this->itemId = $itemId;
        $this->subcategoryId = $subcategoryId;
        $this->attribute = $attribute;
        $this->message = $message;
    }
}

function fetchmelist($userId,$listId) {
    $db = new Database();
    $sql = "SELECT 
    ToDoItems.ItemID, 
    ToDoItems.ItemName, 
    Subcategories.SubcategoryID, 
    Subcategories.SubcategoryName, 
    Subcategories.Order
FROM 
    ToDoItems 
RIGHT JOIN 
    Subcategories 
ON 
    ToDoItems.ItemID = Subcategories.ItemID
INNER JOIN
    ToDoLists
ON
    ToDoItems.ListID = ToDoLists.ListID
WHERE 
    ToDoItems.ListID = :listId AND ToDoLists.UserID = :userId
ORDER BY Subcategories.Order ASC";



$params = array(':listId' => $listId, ':userId' => $userId);
$stmt = $db->query($sql, $params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$todoItems = array();
$subcategories = array();

//format it nicely

foreach ($results as $row) {
    $itemId = $row['ItemID'];
    $itemName = $row['ItemName'];
    $subcategoryId = $row['SubcategoryID'];
    $subcategoryName = $row['SubcategoryName'];
    $subcategoryOrder = $row['Order'];

    if (!isset($todoItems[$itemId])) {
        $todoItems[$itemId] = array(
            'itemId' => $itemId,
            'itemName' => $itemName,
            'subcategories' => array()
        );
    }

    $todoItems[$itemId]['subcategories'][] = array(
        'subcategoryId' => $subcategoryId,
        'subcategoryName' => $subcategoryName,
        'subcategoryOrder' => $subcategoryOrder
    );
}

// call from db
$obj1 = array_values($todoItems);
$db->close();
return $obj1;
}

function find_differences($userId, $listId, $obj2) {
    $deletions = [];
    $changes = [];
    $additions = [];
   
    // Call to the database to fetch the existing items
    // $obj1 = fetchmelist($userId, $listId);

    $obj1 = fetchmelist($userId, $listId);
 
        $assignedIds = []; // skip deletion of subcategories for these
        foreach ($obj2 as &$item) {
        $groupedAdditions = array(); 
        // need to get new ids here, I have no temporary ids to create a map for the subcategories
        if($item['itemId'] == null){

            $db = new Database();
            $sql = "INSERT INTO ToDoItems (ItemName, ListID) VALUES (:itemName, :listId)";
            $params = array(
                ':itemName' => $item['itemName'],
                ':listId' => $listId,
            );
            $db->query($sql, $params);
    
            $lastInsertedId = intval($db->getLastInsertedId());
            $item['itemId'] =  $lastInsertedId;
            $assignedIds[] = $lastInsertedId; // skip
            $db->close();
        }
    
        // 
     
        $subcategories = array(); // Array to store subcategories for this item
        foreach ($item['subcategories'] as $sub) {
            $subcategoryId = $sub['subcategoryId'];
            $subcategoryName = $sub['subcategoryName'];
            $subcategoryOrder = $sub['subcategoryOrder'];
            // Check if subcategory ID is null
            if ($subcategoryId === null) {
                $subcategories[] = array(
                    'subcategoryId' => null,
                    'subcategoryName' => $subcategoryName,
                    'subcategoryOrder' => $subcategoryOrder
                );
            }
        }

            // Add the item with its subcategories to the grouped additions array
            $groupedAdditions = array(
                'itemId' => $item['itemId'],
                'itemName' => $item['itemName'],
                'subcategories' => $subcategories
            );
            if (!empty($groupedAdditions['subcategories'])) {
                $additions[] = $groupedAdditions;
            }
      
    }
 

    // here works as expected $obj2

 
 
    for ($i = 0; $i < count($obj1); $i++) {
        $id1 = $obj1[$i]['itemId'];
        $found = false;
        $obj2Item = null;
    
        // Check if the item exists in obj2 has to be a reference otherwise old copy comes in
        foreach ($obj2 as &$item) {
            if ($item['itemId'] === $id1) {
                $found = true;
                $obj2Item = $item; // Save the corresponding item from obj2
                break;
            }
        }
        // If the item does not exist in obj2, consider it deleted
        if (!$found) {
            $deletions[] = new Difference($id1, null, null, "Item with ID $id1 is deleted");
            continue;
        }
    
        // Compare subcategories of obj1 and obj2
        foreach ($obj1[$i]['subcategories'] as $sub1) {
            $found = false;
            foreach ($obj2Item['subcategories'] as $sub2) { // Use the corresponding item from obj2
                if ($sub1['subcategoryId'] === $sub2['subcategoryId']) {
                    $found = true;
                    break;
                }
            }
            // If the subcategory does not exist in obj2, consider it deleted
            if (!$found) {
                $deletions[] = new Difference($id1, $sub1['subcategoryId'], null, "Subcategory with ID {$sub1['subcategoryId']} is deleted");
            }
        }
    }

        // Check for changes in subcategories
    foreach ($obj2 as &$item) {
        $id2 = $item['itemId'];
        foreach ($item['subcategories'] as $sub) {
            $subcategoryId = $sub['subcategoryId'];
            $found = false;

            $changed_attributes = [];
            foreach ($obj1 as $prevItem) {
                if ($prevItem['itemId'] === $id2) {
                    $changed_attributes_item = [];
                    // Check for changes in item attributes
                    if ($prevItem['itemName'] != $item['itemName']) {
                        $changed_attributes_item['ItemName'] = $item['itemName'];
                    }
                    if (!empty($changed_attributes_item)) {
                        $changes[] = new Difference($id2, null, (object) $changed_attributes_item, "Item with ID $id2 has changed" . implode(", ", $changed_attributes_item));
                    }
                 }

                foreach ($prevItem['subcategories'] as $prevSub) {
                    if ($prevSub['subcategoryId'] === $sub['subcategoryId']) {
                        

                        // Check for changes in subcategory attributes
                        if ($prevSub['subcategoryName'] != $sub['subcategoryName']) {
                            $changed_attributes['subcategoryName'] = $sub['subcategoryName'];
                        }
                        if ($prevSub['subcategoryOrder'] != $sub['subcategoryOrder']) {
                            $changed_attributes['subcategoryOrder'] = $sub['subcategoryOrder'];
                        }
                        if (!empty($changed_attributes)) {
                            $changes[] = new Difference($id2, $subcategoryId, $changed_attributes, "Subcategory with ID $subcategoryId has changed" . implode(", ", $changed_attributes));
                        }
                        $found = true;
                        break 2;
                    }
                }
            } 
         
            if (($found || in_array($id2, $assignedIds)) && $subcategoryId !== null && $prevItem['itemId'] !== $id2) {
                // Remove the deletion entry for the moved subcategory
                $foundIndex = array_search($subcategoryId, array_column($deletions, 'subcategoryId'));
                if ($foundIndex !== false) {
                    unset($deletions[$foundIndex]);
                }
                // change id of todo-item
                $changes[] = new Difference($id2, $subcategoryId, (object) ['ItemID' => $id2],null, "Subcategory with ID $subcategoryId moved from item with ID {$prevItem['itemId']} to item with ID $id2");
    
            }
        }
    }


    // Return differences as an object
    return (object) [
        'deletions' => $deletions,
        'changes' => $changes,
        'additions' => $additions,
    ];
}


function updatemylist($userId, $listId, $obj2)
{

    $differences = find_differences($userId, $listId, $obj2);
    if (empty($differences->deletions) && empty($differences->changes) && empty($differences->additions)) {
        send_response([
            'status' => 1,
            'message' => 'No changes detected',
        ]);
    }

    $db = new Database();
    $db->beginTransaction();

    // pass reference
    foreach ($differences->additions as &$addition) {
            // Loop through subcategories
            foreach ($addition['subcategories'] as $subcategory) {
                // Access subcategory properties
                $subcategoryName = $subcategory['subcategoryName'];
                $subcategoryOrder = $subcategory['subcategoryOrder'];

                // Perform operations on each subcategory
                // ...
                $sql = "INSERT INTO Subcategories (ItemID,SubcategoryName, `Order`) VALUES (:itemId,:subcategoryName, :subcategoryOrder)";
                $params = array(
                    ':itemId' => $addition['itemId'],
                    ':subcategoryName' =>  $subcategoryName,
                    ':subcategoryOrder' => $subcategoryOrder,
                );
                $db->query($sql, $params);
            }
          
      
    }

 
    
    // look into
    // new todo-items dont have an id this causes problems when changing the subcatogories
    // need to get it the id from additions and change differences acoordingly
    foreach ($differences->changes as $change) {
        // update item attributes
        if($change->subcategoryId === null){
            $sql = "UPDATE ToDoItems SET ItemName = :itemName WHERE ItemID = :itemId";
            $params = array(
                ':itemName' => $change->attribute->ItemName,
                ':itemId' => $change->itemId,
            );
            $db->query($sql, $params);
            continue;
        }


        $sql = "UPDATE Subcategories SET ";
        $params = [];
        foreach ($change->attribute as $key => $value) {
            // my fault
            if ($key === 'subcategoryOrder') {
                $key = 'Order';
                $sql .= "`$key` = :$key, ";
                $params[":$key"] = $value;
            } else {
                $sql .= "$key = :$key, ";
                $params[":$key"] = $value;
            }
        }
        $sql = rtrim($sql, ', ');
        $sql .= " WHERE SubcategoryID = :subcategoryId";
        $params[':subcategoryId'] = $change->subcategoryId;
        $db->query($sql, $params);
    }

    

    foreach ($differences->deletions as $deletion) {
        if ($deletion->subcategoryId === null) {
            // delete all sssociated subcategories
            $sql = "DELETE FROM Subcategories WHERE ItemID = :itemId";
            $params = array(':itemId' => $deletion->itemId);
            $db->query($sql, $params);
            
            $sql = "DELETE FROM ToDoItems WHERE ItemID = :itemId";
            $params = array(':itemId' => $deletion->itemId);
            $db->query($sql, $params);
        } else {
            $sql = "DELETE FROM Subcategories WHERE SubcategoryID = :subcategoryId";
            $params = array(':subcategoryId' => $deletion->subcategoryId);
            $db->query($sql, $params);
        }
    }


    $db->commit();
}


function saveall($listId, $userId, $data) {

   //save all in main container
    updatemylist($userId,$listId, $data['display']);
    // also save selection here
    send_response([
        'status' => 1,
        'message' => 'Data saved successfully',
    ]);
}




session_start();



$data = get_request_data();

$userId = $_SESSION['user'] ?? $data['UserID'];

switch ($data['action']) {
    case 'gettodolists':
        getToDoLists($userId);
        break;
    case 'addtodolist':
        addTodoList($data['ListNameArray'], $userId);
        break;
    case 'saveall':
        // $listId = $data['ListID'];
        saveall($data['ListID'], $userId, $data['data']);
        break;
    case 'checkifchangedtodolist':
        checkifchangedtodolist($userId, $data);
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
