<?php
function find_differences($obj1, $obj2) {
    $deletions = [];
    $changes = [];
    $additions = [];

    if (count($obj1) != count($obj2)) {
        $deletions[] = "Objects have different lengths";
    }

    for ($i = 0; $i < count($obj1); $i++) {
        // Check if the item ID exists in both objects
        $id1 = $obj1[$i]['itemId'];
        $found = false;
        foreach ($obj2 as $item) {
            if ($item['itemId'] === $id1) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            // If the item ID is missing in obj2, consider it deleted
            $deletions[] = "Item with ID $id1 is deleted";
            continue; // Skip further comparison for this row
        }

        // Compare subcategory IDs
        foreach ($obj1[$i]['subcategories'] as $sub1) {
            $found = false;
            foreach ($obj2[$i]['subcategories'] as $sub2) {
                if ($sub1['subcategoryId'] === $sub2['subcategoryId']) {
                    $found = true;
                    break;
                }
            }
            if (!$found && $sub1['subcategoryId'] !== null) {
                $deletions[] = "Subcategory with ID {$sub1['subcategoryId']} in item with ID $id1 is deleted";
            }
        }
    }

    // Identify null subcategory IDs in obj2 as additions
    foreach ($obj2 as $item) {
        foreach ($item['subcategories'] as $sub) {
            if ($sub['subcategoryId'] === null) {
                $additions[] = "Null subcategory in item with ID {$item['itemId']} is added";
            }
        }
    }

    // Detect moved subcategories
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
                $changes[] = "Subcategory with ID $subcategoryId moved from item with ID {$prevItem['itemId']} to item with ID $id2";
            }
        }
    }

    return [
        'deletions' => $deletions,
        'changes' => $changes,
        'additions' => $additions,
    ];
}


// Sample objects
$obj1 = [
    [
        "itemId" => 5,
        "itemName" => "Dummy Task 1",
        "subcategories" => [
            ["subcategoryId" => 13, "subcategoryName" => "Subcategory 3 (Not NULL)", "subcategoryOrder" => 1],
            ["subcategoryId" => 14, "subcategoryName" => "Subcategory 4 (Not NULL)", "subcategoryOrder" => 2],
            ["subcategoryId" => 17, "subcategoryName" => "Subcategory 5 (Not NULL)", "subcategoryOrder" => 3]
        ]
    ],
    [
        "itemId" => 6,
        "itemName" => "Dummy Task 2",
        "subcategories" => [
            ["subcategoryId" => 18, "subcategoryName" => "Subcategory 5 (Not NULL)", "subcategoryOrder" => 1],
            ["subcategoryId" => 20, "subcategoryName" => "Subcategory 6 (Not NULL)", "subcategoryOrder" => 2]
        ]
    ]
];

$obj2 = [
    [
        "itemId" => 5,
        "itemName" => "Dummy Task 1",
        "subcategories" => [
            ["subcategoryId" => 13, "subcategoryName" => "Subcategory 3 (Not NULL)", "subcategoryOrder" => 1],
            ["subcategoryId" => 14, "subcategoryName" => "Subcategory 4 (Not NULL)", "subcategoryOrder" => 2],
            ["subcategoryId" => 17, "subcategoryName" => "Subcategory 5 (Not NULL)", "subcategoryOrder" => 3],
            ["subcategoryId" => 19, "subcategoryName" => "Subcategory 6 (Not NULL)", "subcategoryOrder" => 4]
        ]
    ],
    [
        "itemId" => null,
        "itemName" => "Dummy Task 1",
        "subcategories" => [
            ["subcategoryId" => null, "subcategoryName" => "Subcategory 3 (Not NULL)", "subcategoryOrder" => 1],
            ["subcategoryId" => null, "subcategoryName" => "Subcategory 4 (Not NULL)", "subcategoryOrder" => 2],
            ["subcategoryId" => null, "subcategoryName" => "Subcategory 5 (Not NULL)", "subcategoryOrder" => 3],
            ["subcategoryId" => null, "subcategoryName" => "Subcategory 6 (Not NULL)", "subcategoryOrder" => 4]
        ]
    ],
    [
        "itemId" => 6,
        "itemName" => "Dummy Task 2",
        "subcategories" => [
            ["subcategoryId" => null, "subcategoryName" => "Subcategory 5 (Not NULL)", "subcategoryOrder" => 1],
            ["subcategoryId" => 17, "subcategoryName" => "Subcategory 5 (Not NULL)", "subcategoryOrder" => 3],
            ["subcategoryId" => 20, "subcategoryName" => "Subcategory 6 (Not NULL)", "subcategoryOrder" => 2]
        ]
    ]
];

$differences = find_differences($obj1, $obj2);

echo "Deletions:\n";
foreach ($differences['deletions'] as $difference) {
    echo $difference . "\n";
}

echo "\nChanges:\n";
foreach ($differences['changes'] as $difference) {
    echo $difference . "\n";
}

echo "\nAdditions:\n";
foreach ($differences['additions'] as $difference) {
    echo $difference . "\n";
}

?>
