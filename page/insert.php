<?php

include __DIR__.'/../config.php';

$table = $_GET['table'];

$tableClient = $client->table($table);
$columnname = $tableClient->describe();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $columns = $_POST['columns'];

    $postData = [];

    try {

        foreach ($columnname as $key=>$item){
            if ($key == 'id') { continue; }
            if($item['Type'] == 'float_vector'){
                $postData[$key] =  array_map('floatval',explode(',',str_replace(' ','',$columns[$key])));
            }  else {
                $postData[$key] = $columns[$key];
            }
        }


        $tableClient->addDocument($postData);


        echo "New record succesfull!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    echo '<a href="../">Home Page</a>';
    exit;
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$table?> Insert</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        input[type="text"], input[type="number"] {
            width: 96.6%;
            padding: 8px;
            margin: 5px 0;
        }
        select {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }

        .column {
            border: 1px solid gray;
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .options, .vector-options {
            padding: 1rem;
            border: 1px solid #007bff;
            border-radius: 5px;
        }

    </style>
</head>
<body>

<h2 style="text-align: center;"><?=$table?> Insert</h2>


<form action="insert.php?table=<?=$table?>" method="POST">

    <?php

    foreach ($columnname as $key=>$item){

        if ($key == 'id') { continue; }

        ?>
        <label for="table_name"><?=$key?>:</label>
        <?php if($item['Type'] == 'int' || $item['Type'] == 'bigint' || $item['Type'] == 'float'){ ?>
            <input type="number" id="table_name" name="columns[<?=$key?>]" required>
       <?php  } elseif($item['Type'] == 'timestamp') { ?>
            <input type="datetime-local" id="table_name" name="columns[<?=$key?>]" required>
       <?php  } elseif($item['Type'] == 'json') { ?>
            <textarea name="columns[<?=$key?>]"></textarea>
       <?php  } else { ?>
        <input type="text" id="table_name" name="columns[<?=$key?>]" required>
        <?php } ?>

    <?php } ?>


    <button type="submit">Insert</button>
</form>


</body>
</html>