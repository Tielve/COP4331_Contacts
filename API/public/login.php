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
        $statement = $connection->prepare("
            SELECT id
            FROM   users
            WHERE  username = ?
            AND    pw = ?
        ");

        $statement->bind_param("ss", $inData["username"], $inData["pw"]);
        $statement->execute();
        $result = $statement->get_result();

        if ($row = $result->fetch_assoc())
        {
            returnSuccess($row['id']);
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
        http_response_code(401); // unauthorized; invalid uID
        $returnValue = 
        '{
            "id" : 0,
            "error" : "' . $error . '"
        }';
        sendResultInfoAsJson($returnValue);
    }

    function returnSuccess($id)
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