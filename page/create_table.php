<?php

include __DIR__.'/../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableName = $_POST['table_name'];
    $columns = $_POST['columns'];

    try {
        $table = $client->table($tableName);

        $columnDefinitions = [];
        foreach ($columns as $column) {
            if (isset($column['type'])) {
                $columnDefinitions[$column['name']] = ['type' => $column['type']];
                if (count(array_filter($column['options'])) > 0 && isset($column['options'])) {

                    if ($column['type'] == 'string'){
                        $columnDefinitions[$column['name']]['options'] = array_keys(array_filter($column['options']));
                    }else{
                        $columnDefinitions[$column['name']]['options'] = array_filter($column['options']);
                    }
                }
            }
        }

        $table->create($columnDefinitions);
        echo "Table '$tableName' created!";
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
    <title>Tablo Create</title>
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

<h2 style="text-align: center;">New Table Create</h2>

<form action="create_table.php" method="POST">
    <label for="table_name">Table Name:</label>
    <input type="text" id="table_name" name="table_name" required>

    <h3>Columns:</h3>
    <div id="columns">
        <div class="column">
            <label>Column Name:</label>
            <input type="text" name="columns[0][name]" placeholder="Column Name" >

            <label>Column Type:</label>
            <select name="columns[0][type]" class="column-type" data-index="0" >
                <option value="int">integer</option>
                <option value="text">text</option>
                <option value="bigint">bigint</option>
                <option value="float">float</option>
                <option value="float_vector">float_vector</option>
                <option value="bool">bool</option>
                <option value="json">json</option>
                <option value="string">string</option>
                <option value="timestamp">timestamp</option>
            </select>

            <div class="vector-options" style="display:none;">
                <label>Vector (dims):</label>
                <input type="number" name="columns[0][options][knn_dims]" placeholder="Vector dims" >
                <label>Vector (knn_type):</label>
                <select name="columns[0][options][knn_type]" >
                    <option value=""></option>
                    <option value="hnsw">HNSW</option>
                </select>
                <label>Vector (hnsw_similarity):</label>
                <select name="columns[0][options][hnsw_similarity]" >
                    <option value=""></option>
                    <option value="L2">L2</option>
                    <option value="IP">IP</option>
                    <option value="Cosine">Cosine</option>
                </select>
            </div>

            <div class="options" style="display:none;">
                <label>Indexed:</label>
                <input type="checkbox" name="columns[0][options][indexed]">
                <label>Stored:</label>
                <input type="checkbox" name="columns[0][options][stored]">
            </div>
        </div>
    </div>

    <button type="button" id="add-column-btn">New Column</button><br><br>
    <button type="submit">Create</button>
</form>

<script>
    $(document).ready(function() {
        let columnIndex = 1;

        $('#add-column-btn').click(function() {
            const newColumnDiv = $(`
                <div class="column">
                    <label>Column Name:</label>
                    <input type="text" name="columns[${columnIndex}][name]" placeholder="Column Name" >

                    <label>Column Type:</label>
                    <select name="columns[${columnIndex}][type]" class="column-type" data-index="${columnIndex}" >
                <option value="int">integer</option>
                <option value="text">text</option>
                <option value="bigint">bigint</option>
                <option value="float">float</option>
                <option value="float_vector">float_vector</option>
                <option value="bool">bool</option>
                <option value="json">json</option>
                <option value="string">string</option>
                <option value="timestamp">timestamp</option>
                    </select>

                    <div class="vector-options" style="display:none;">
                        <label>Vector (dims):</label>
                        <input type="number" name="columns[${columnIndex}][options][knn_dims]" placeholder="Vector dims" >
                        <label>Vector (knn_type):</label>
                        <select name="columns[${columnIndex}][options][knn_type]" >
                            <option value=""></option>
                            <option value="hnsw">HNSW</option>
                        </select>
                        <label>Vector (hnsw_similarity):</label>
                        <select name="columns[${columnIndex}][options][hnsw_similarity]" >
                            <option value=""></option>
                            <option value="L2">L2</option>
                            <option value="IP">IP</option>
                            <option value="Cosine">Cosine</option>
                        </select>
                    </div>
                    <div class="options" style="display:none;">
                        <label>Indexed:</label>
                        <input type="checkbox" name="columns[${columnIndex}][options][indexed]">
                        <label>Stored:</label>
                        <input type="checkbox" name="columns[${columnIndex}][options][stored]">
                    </div>
                </div>
            `);

            $('#columns').append(newColumnDiv);
            columnIndex++;
        });

        $(document).on('change', '.column-type', function() {
            const index = $(this).data('index');
            const selectedType = $(this).val();
            const columnDiv = $(this).closest('.column');

            if (selectedType === "float_vector") {
                columnDiv.find('.vector-options').show();
            } else {
                columnDiv.find('.vector-options').hide();
            }

            if (selectedType === "string") {
                columnDiv.find('.options').show();
            } else {
                columnDiv.find('.options').hide();
            }
        });
    });
</script>

</body>
</html>