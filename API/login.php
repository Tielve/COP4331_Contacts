<?php
    $inData = json_decode(file_get_contents('php://input'), true);
    $connection = new mysqli("localhost", "test", "test", "contactmanagerdb");

    if ($connection->connect_error)
    {
        returnWithError($connection->connect_error);
    }
    else
    {
        $statement = $connection->prepare("
            SELECT id
            FROM   users
            WHERE  username = ?
            AND    pw = ?
        ")

        $statement->bind_param("ss", $inData["username"], $inData["pw"]);
        $statement->execute();
        $result = $statement->get_result();

        if ($row = $result->fetch_assoc())
        {
            returnWithInfo($row['id']);
        }
        else
        {
            returnWithError("No Records Found");
        }

        $statement->close();
        $connection->close();
    }
    
    function returnWithError($error)
    {
        $returnValue = 
        '{
            "id" : 0,
            "error" : "' . $error . '"
        }';
        sendResultInfoAsJson($returnValue);
    }

    function returnWithInfo($id)
    {
        $returnValue = 
        '{
            "id" : ' . $id . '
        }';
        sendResultInfoAsJson($returnValue);
    }

    function sendResultInfoAsJson($object)
    {
        header('Content-type: application/json');
        echo $object;
    }
?>