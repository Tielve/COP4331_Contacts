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
        
        try
        {
            $statement->execute();
            returnSuccess($statement->insert_id);
        }
        catch(mysqli_sql_exception $exception)
        {
            returnWithError($exception->getMessage());
        }
        finally
        {
            $statement->close();
            $connection->close();
        }
    }

    function returnWithError($error)
    {
        http_response_code(401); // unauthorized; invalid uID
        $returnValue = 
        '{
            "error" : "' . $error . '"
        }';
        sendResultInfoAsJson($returnValue);
    }

    function returnSuccess($cID)
    {
        $returnValue =
        '{
            "cID" : "' . $cID . '"
        }';
        sendResultInfoAsJson($returnValue);
    }

    function sendResultInfoAsJson($object)
    {
        header('Content-type: application/json');
        echo $object;
    }
?>