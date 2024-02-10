<?php
include 'db.php';
include 'endpoint.php';
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);


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
 

    // Check if the lengths of the objects are different
    if (count($obj1) != count($obj2)) {
        $deletions[] = new Difference(null, null, null, "Objects have different lengths");
    }
    
    // Iterate through each item in obj1
    for ($i = 0; $i < count($obj1); $i++) {
        $id1 = $obj1[$i]['itemId'];
        $found = false;

        // Check if the item exists in obj2
        foreach ($obj2 as $item) {
            if ($item['itemId'] === $id1) {
                $found = true;
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
            foreach ($obj2[$i]['subcategories'] as $sub2) {
                if ($sub1['subcategoryId'] === $sub2['subcategoryId']) {
                    $changed_attributes = [];

                    // Check for changes in subcategory attributes
                    if ($sub1['subcategoryName'] !== $sub2['subcategoryName']) {
                        $changed_attributes['subcategoryName'] = $sub2['subcategoryName'];
                    }
                    if ($sub1['subcategoryOrder'] !== $sub2['subcategoryOrder']) {
                        $changed_attributes['subcategoryOrder'] = $sub2['subcategoryOrder'];
                    }

                    // If there are changes, add them to the changes array
                    if (!empty($changed_attributes)) {
                        $changes[] = new Difference($id1, $sub1['subcategoryId'], (object) $changed_attributes, "Subcategory with ID {$sub1['subcategoryId']} in item with ID $id1 has changed");
                    }
                    $found = true;
                    break;
                }
            }

            // If subcategory is not found in obj2, consider it deleted
            if (!$found && $sub1['subcategoryId'] !== null) {
                $deletions[] = new Difference($id1, $sub1['subcategoryId'], null, "Subcategory with ID {$sub1['subcategoryId']} in item with ID $id1 is deleted");
            }
        }
    }

    
    // // Check for additions of null subcategories in obj2
    // foreach ($obj2 as $item) {
    //     foreach ($item['subcategories'] as $sub) {
    //         if ($sub['subcategoryId'] === null) {
    //             $additions[] = new Difference($item['itemId'], null, null, "Null subcategory in item with ID {$item['itemId']} is added");
    //         }
    //     }
    // }
    foreach ($obj2 as $item) {
        $groupedAdditions = array(); 
        $itemId = $item['itemId'];
        $itemName = $item['itemName'];
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
            'itemId' => $itemId,
            'itemName' => $itemName,
            'subcategories' => $subcategories
        );
        if(!empty($groupedAdditions['subcategories'])){
            $additions[] = $groupedAdditions;
        }
    }
    
    
    // Check for subcategories moved to different items checkpoint
    foreach ($obj2 as $item) {
        $id2 = $item['itemId'];
        foreach ($item['subcategories'] as $sub) {
            $subcategoryId = $sub['subcategoryId'];
            $found = false;
            foreach ($obj1 as $prevItem) {
                foreach ($prevItem['subcategories'] as $prevSub) {
                    if ($prevSub['subcategoryId'] === $subcategoryId) {
                        $found = true;
                        break 2;
                    }
                }
            }
            if ($found && $subcategoryId !== null && $prevItem['itemId'] !== $id2) {
                // Remove the deletion entry for the moved subcategory
                $foundIndex = array_search($subcategoryId, array_column($deletions, 'subcategoryId'));
                if ($foundIndex !== false) {
                    unset($deletions[$foundIndex]);
                }
                $changes[] = new Difference($id2, $subcategoryId, (object) ['ItemID' => $id2], "Subcategory with ID $subcategoryId moved from item with ID {$prevItem['itemId']} to item with ID $id2");
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
        // get id
        if($addition['itemId'] == null){
            $sql = "INSERT INTO ToDoItems (ItemName, ListID) VALUES (:itemName, :listId)";
            $params = array(
                ':itemName' => $addition['itemName'],
                ':listId' => $listId,
            );
            $db->query($sql, $params);

            $lastInsertedId = $db->getLastInsertedId();
            $addition['itemId'] = $lastInsertedId;
        }
      
      


     
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
    case 'getitemsintodolist':
        getItemsInToDoList($data['ListID'], $userId);
        break;
    default:
        send_response([
            'status' => 0,
            'message' => 'Neplatná akce',
        ]);
}
