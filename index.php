<?php

include __DIR__.'/config.php';


if (isset($_GET['action']) && $_GET['action'] == 'delete_table'){
    $params = [
        'table' => $_GET['table'],
        'body' => ['silent'=>true ]
    ];
    $response = $client->tables()->drop($params);
}

$notes = new \Manticoresearch\Nodes($client);

$response = $notes->tables();


?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ManticoreSearch Panel</title>
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f4f4f4;
        }
        .btn {
            padding: 5px 10px;
            margin: 5px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-family: monospace;
        }
        .btn-danger {
            background-color: red;
        }
        .btn-green{
            background-color: lawngreen;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">ManticoreSearch Control Panel</h2>


<table>
    <thead>
    <tr>
        <th>Table Name</th>
        <th>Table Type</th>
        <th>Table Describe</th>
        <th style=" min-width: 400px; ">Options</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($response as $table){


        $params = [
            'table' => $table['Table'],
        ];
        $columnname = $client->tables()->describe($params);

        ?>
        <tr>
            <td><?php echo $table['Table']; ?></td>
            <td><?php echo $table['Type']; ?></td>
            <td><?php

                foreach ($columnname as $col_key=>$col){
                    echo $col_key.' => '.$col['Type'].' ';
                    if (strlen($col['Properties'])){
                        echo $col['Properties'];
                    }
                    echo ', ';
                }
                ?></td>
            <td>

                <a href="page/select.php?table=<?php echo $table['Table']; ?>" class="btn btn-green">Select List</a>


                <a href="page/insert.php?table=<?php echo $table['Table']; ?>" class="btn">Insert</a>

                <a href="?action=delete_table&table=<?php echo $table['Table']; ?>" class="btn btn-danger">Drop</a>

            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>


<div style=" text-align: center; ">
    <a href="page/create_table.php" class="btn">Table Create</a>
</div>
</body>
</html>
