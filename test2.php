
<?php
include './api/db.php';


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

function find_differences($userId,$listId,$obj2) {
    $deletions = [];
    $changes = [];
    $additions = [];

    // call to db create $obj1 // this is only todo items I will handle them separately
    $obj1 = fetchmelist($userId,$listId);

    if (count($obj1) != count($obj2)) {
        $deletions[] = new Difference(null, null, null, "Objects have different lengths");
    }

    for ($i = 0; $i < count($obj1); $i++) {
        $id1 = $obj1[$i]['itemId'];
        $found = false;
        foreach ($obj2 as $item) {
            if ($item['itemId'] === $id1) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $deletions[] = new Difference($id1, null, null, "Item with ID $id1 is deleted");
            continue;
        }

        foreach ($obj1[$i]['subcategories'] as $sub1) {
            $found = false;
            foreach ($obj2[$i]['subcategories'] as $sub2) {
                if ($sub1['subcategoryId'] === $sub2['subcategoryId']) {
                    $changed_attributes = [];
                    if ($sub1['subcategoryName'] !== $sub2['subcategoryName']) {
                        $changed_attributes[] = "subcategoryName";
                    }
                    if ($sub1['subcategoryOrder'] !== $sub2['subcategoryOrder']) {
                        $changed_attributes[] = "subcategoryOrder";
                    }
                    if (!empty($changed_attributes)) {
                        $changes[] = new Difference($id1, $sub1['subcategoryId'], implode(", ", $changed_attributes), "Subcategory with ID {$sub1['subcategoryId']} in item with ID $id1 has changed in: " . implode(", ", $changed_attributes));
                    }
                    $found = true;
                    break;
                }
            }
            if (!$found && $sub1['subcategoryId'] !== null) {
                $deletions[] = new Difference($id1, $sub1['subcategoryId'], null, "Subcategory with ID {$sub1['subcategoryId']} in item with ID $id1 is deleted");
            }
        }
    }

    foreach ($obj2 as $item) {
        foreach ($item['subcategories'] as $sub) {
            if ($sub['subcategoryId'] === null) {
                $additions[] = new Difference($item['itemId'], null, null, "Null subcategory in item with ID {$item['itemId']} is added");
            }
        }
    }

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
                $changes[] = new Difference($id2, $subcategoryId, 'moved', "Subcategory with ID $subcategoryId moved from item with ID {$prevItem['itemId']} to item with ID $id2");
            }
        }
    }

    return (object)[
        'deletions' => $deletions,
        'changes' => $changes,
        'additions' => $additions,
    ];
}


function updatemylist($userId,$listId,$obj2){
    $differences = find_differences($userId,$listId,$obj2);
    $db = new Database();
    $db->beginTransaction();
    foreach ($differences->deletions as $deletion) {
        if ($deletion->subcategoryId === null) {
            $sql = "DELETE FROM ToDoItems WHERE ItemID = :itemId";
            $params = array(':itemId' => $deletion->itemId);
            $db->query($sql, $params);
        } else {
            $sql = "DELETE FROM Subcategories WHERE SubcategoryID = :subcategoryId";
            $params = array(':subcategoryId' => $deletion->subcategoryId);
            $db->query($sql, $params);
        }
    }

    foreach ($differences->changes as $change) {
        $sql = "UPDATE Subcategories SET ";
        $params = array();
        if (strpos($change->attribute, "subcategoryName") !== false) {
            $sql .= "SubcategoryName = :subcategoryName";
            $params[':subcategoryName'] = $obj2[$change->itemId]['subcategories'][$change->subcategoryId]['subcategoryName'];
        }
        if (strpos($change->attribute, "subcategoryOrder") !== false) {
            if (strpos($sql, "SubcategoryName") !== false) {
                $sql .= ", ";
            }
            $sql .= "Order = :subcategoryOrder";
            $params[':subcategoryOrder'] = $obj2[$change->itemId]['subcategories'][$change->subcategoryId]['subcategoryOrder'];
        }
        $sql .= " WHERE SubcategoryID = :subcategoryId";
        $params[':subcategoryId'] = $change->subcategoryId;
        $db->query($sql, $params);
    }

    foreach ($differences->additions as $addition) {
        $sql = "INSERT INTO Subcategories (ItemID, SubcategoryName, Order) VALUES (:itemId, :subcategoryName, :subcategoryOrder)";
        $params = array(
            ':itemId' => $addition->itemId,
            ':subcategoryName' => $obj2[$addition->itemId]['subcategories'][0]['subcategoryName'],
            ':subcategoryOrder' => $obj2[$addition->itemId]['subcategories'][0]['subcategoryOrder']
        );
        $db->query($sql, $params);
    }
    $db->commit();
    $db->close();
}


$obj2 = [
    [
        "itemId" => 5,
        "itemName" => "Dummy Task 1",
        "subcategories" => [
            ["subcategoryId" => 13, "subcategoryName" => "Subcateory 3 (Not NULL)", "subcategoryOrder" => 1],
            ["subcategoryId" => 14, "subcategoryName" => "Subcategory 4 (Not NULL)", "subcategoryOrder" => 2],
            ["subcategoryId" => 17, "subcategoryName" => "Subcategory 5 (Not NULL)", "subcategoryOrder" => 3],
            ["subcategoryId" => 18, "subcategoryName" => "Subcategory 6 (Not NULL)", "subcategoryOrder" => 4]
        ]
    ],
    [
        "itemId" => 6,
        "itemName" => "Dummy Task 2",
        "subcategories" => [
            ["subcategoryId" => 19, "subcategoryName" => "Subcategory 5 (Not NULL)", "subcategoryOrder" => 1],
            ["subcategoryId" => 20, "subcategoryName" => "Subcategory 6 (Not NULL)", "subcategoryOrder" => 2]
        ]
    ]
];



$differences = find_differences(6,50,$obj2);

echo '<pre>';
print_r($differences);
echo '</pre>';


updatemylist(6,50,$obj2);
echo "Updated";

?>