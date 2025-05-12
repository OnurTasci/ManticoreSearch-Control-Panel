<?php


include __DIR__ . '/../config.php';

$table = $_GET['table'];

$tableClient = $client->table($table);

$columnname = $tableClient->describe();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    echo '<pre>';
    $type= $_POST['search_type'];
    $value= $_POST['value'];
    $column= $_POST['column'];

    if ($type == 'full'){

        $results = $tableClient->search($value)->get();
        foreach($results as $doc) {
            echo 'ID:'.$doc->getId()."\n";
            foreach($doc->getData() as $field=>$val)
            {
                echo $field.": ".(is_array($val) ? implode(',',$val): $val)."\n";
            }
            echo "\n\n";
        }

    }

    if ($type == 'column'){

        if ($column == 'id'){

            $results = $tableClient->getDocumentById($value);
            print_r($results);

        }else{


            $results = $tableClient->search('')
                ->filter($column, $value)->get();
            
            foreach($results as $doc) {
                echo 'ID:'.$doc->getId()."\n";
                foreach($doc->getData() as $field=>$val)
                {
                    echo $field.": ".(is_array($val) ? implode(',',$val): $val)."\n";
                }
                echo "\n\n";
            }
        }


    }

    if ($type == 'knn'){

        $search = new \Manticoresearch\Search($client);
        $search->setTable($table);
        $results = $search->knn($column, array_map('floatval', str_replace(' ','',explode(',',$value))), 3)->get();

        foreach($results as $doc) {
            echo 'ID:'.$doc->getId()."\n";
            foreach($doc->getData() as $field=>$val)
            {
                echo $field.": ".(is_array($val) ? implode(',',$val): $val)."\n";
            }
            echo "\n\n";
        }


    }

    exit();

}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$table?> Select</title>
    <!-- jQuery ekliyoruz -->
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

<h2 style="text-align: center;"><?=$table?> Select</h2>


<form action="select.php?table=<?=$table?>" method="POST">

    <label>Search Type</label>
    <select name="search_type">
        <option value="full">Full text</option>
        <option value="column">Where Column</option>
        <option value="knn">Vectore search</option>
    </select>


    <label>Column</label>
    <select name="column">
        <?php
        foreach ($columnname as $key=>$item){
            echo '<option value="'.$key.'">'.$key.'</option>';
        } ?>
    </select>

    <label>Value</label>
    <input type="text" name="value" required>

    <button type="submit">Search</button>
</form>


</body>
</html>
