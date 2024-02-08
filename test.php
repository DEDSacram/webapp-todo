<?php

$obj1 = [
    [
        "itemId" => 1,
        "itemName" => "Item 1",
        "subcategories" => [
            ["subcategoryId" => 11, "subcategoryName" => "Subcategory 1 for Item 1", "subcategoryOrder" => 1],
            ["subcategoryId" => 12, "subcategoryName" => "Subcategory 2 for Item 1", "subcategoryOrder" => 2]
        ]
    ],
    [
        "itemId" => 2,
        "itemName" => "Item 2",
        "subcategories" => [
            ["subcategoryId" => 21, "subcategoryName" => "Subcategory 1 for Item 2", "subcategoryOrder" => 1],
            ["subcategoryId" => 22, "subcategoryName" => "Subcategory 2 for Item 2", "subcategoryOrder" => 2]
        ]
    ],
    [
        "itemId" => 3,
        "itemName" => "Item 3",
        "subcategories" => [
            ["subcategoryId" => 31, "subcategoryName" => "Subcategory 1 for Item 3", "subcategoryOrder" => 1],
            ["subcategoryId" => 32, "subcategoryName" => "Subcategory 2 for Item 3", "subcategoryOrder" => 2]
        ]
    ]
];

$obj2 = [
    [
        "itemId" => 1,
        "itemName" => "Item 1",
        "subcategories" => [
            ["subcategoryId" => 11, "subcategoryName" => "Subcategory 1 for Item 1", "subcategoryOrder" => 1],
            ["subcategoryId" => 12, "subcategoryName" => "Subcategory 2 for Item 1", "subcategoryOrder" => 2]
        ]
    ],
    [
        "itemId" => 2,
        "itemName" => "Item 2",
        "subcategories" => [
            ["subcategoryId" => 21, "subcategoryName" => "Subcategory 1 for Item 2", "subcategoryOrder" => 1],
            ["subcategoryId" => 22, "subcategoryName" => "Subcategory 2 for Item 2", "subcategoryOrder" => 2]
        ]
    ],
    [
        "itemId" => 3,
        "itemName" => "Item 3",
        "subcategories" => [
            ["subcategoryId" => 31, "subcategoryName" => "Subcategory 1 for Item 3", "subcategoryOrder" => 1],
            ["subcategoryId" => 32, "subcategoryName" => "Subcategory 2 for Item 3", "subcategoryOrder" => 2],
            ["subcategoryId" => 33, "subcategoryName" => "New Subcategory for Item 3", "subcategoryOrder" => 3]
        ]
    ]
];

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

function find_differences($obj1, $obj2) {
    $deletions = [];
    $changes = [];
    $additions = [];

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






$differences = find_differences($obj1, $obj2);

echo '<pre>';
print_r($differences);
echo '</pre>';
?>
