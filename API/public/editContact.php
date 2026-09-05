<?php
    /*
     * The following lines starting with "require_once" through "$connection"
     * should appear at the top of every PHP file. This ensures the .env is 
     * being used and that a connection to the database is established.
     */

    require_once __DIR__ . '/../vendor/autoload.php';
    $dotEnv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotEnv->load();

    $inData = json_decode(file_get_contents('php://input'), true);
    $connection = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'],
                             $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
    
    if ($connection->connect_error)
    {
        returnWithError($connection->connect_error);
    }
    else
    {
        $cID = $inData["cID"]; // contact ID

        $statement = $connection->prepare("
            UPDATE contacts
            SET    fname = ?, lname = ?, phone = ?, email = ?, company = ?
            WHERE  uID = ?
            AND    cID = ?
        ");
        
        $statement->bind_param("sssssii", 
            $inData["fname"], $inData["lname"], $inData["phone"],
            $inData["email"], $inData["company"], $inData["uID"], $cID);
        
        try
        {
            $statement->execute();
        }
        catch(mysqli_sql_exception $exception)
        {
            returnWithError($cID, $exception->getMessage());
            $statement->close();
            $connection->close();
            exit;
        }
        
        if ($statement->affected_rows == 0)
        {
            returnWithError($cID, "No Records Found");
        }
        else
        {
            returnSuccess($cID);
        }

        $statement->close();
        $connection->close();
    }

    function returnWithError($cID, $error)
    {
        http_response_code(400);
        $returnValue = 
        '{
            "cID" : "' . $cID . '", 
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