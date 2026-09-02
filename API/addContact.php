<?php
    $inData = json_decode(file_get_contents('php://input'), true);
    $connection = new mysqli("localhost", "admin", "admin", "ContactManager");
    $uID = $inData["uID"];
    $fname = $inData["fname"];
    $lname = $inData["lname"];
    $phone = $inData["phone"];
    $email = $inData["email"];
    $company = $inData["company"];

    if ($connection->connect_error)
    {
        returnWithError($connection->connect_error);
    }
    else
    {
        $statement = $connection->prepare("
            INSERT INTO contacts(uID, fname, lname, phone, email, company)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $statement->bind_param("ssssss", $uID, $fname, $lname, $phone, $email, $company);
        $statement->execute();
        $statement->close();
        $connection->close();
        returnWithoutError();
    }

    function returnWithError($error)
    {
        $returnValue = 
        '{
            "error" : "' . $error . '"
        }';
        sendResultInfoAsJson($returnValue);
    }

    function returnWithoutError()
    {
        sendResultInfoAsJson(null);
    }

    function sendResultInfoAsJson($object)
    {
        header('Content-type: application/json');
        echo $object;
    }
?>