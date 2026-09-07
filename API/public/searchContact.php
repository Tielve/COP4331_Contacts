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
        $searchTerm = "%" . $inData["search"] . "%"; // what user enters in search bar
        $searchCount = 0; // number of search results returned
        $searchResults = ""; // returned search results in json format

        $statement = $connection->prepare("
            SELECT cID, fname, lname, phone, email, company
            FROM   contacts
            WHERE  uID = ?
            AND    (   fname   LIKE ?
                    OR lname   LIKE ?
                    OR phone   LIKE ?
                    OR email   LIKE ?
                    OR company LIKE ?);
        ");

        $statement->bind_param("isssss", $inData["uID"],
            $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        
        try
        {
            $statement->execute();
        }
        catch(mysqli_sql_exception $exception)
        {
            returnWithError($exception->getMessage());
        }

        $result = $statement->get_result();

        // get next row in the search results, stop when there are no more rows
        while ($row = $result->fetch_assoc())
        {
            if ($searchCount > 0)
            {
                $searchResults .= ",";
            }

            $searchCount++;

            $searchResults .= 
            '{ 
                "cID" : "' . $row["cID"] . '", 
                "fname" : "' . $row["fname"] . '", 
                "lname" : "' . $row["lname"] . '",
                "phone" : "' . $row["phone"] . '",
                "email" : "' . $row["email"] . '",
                "company" : "' . $row["company"] . '"
            }';
        }

        if ($searchCount == 0)
        {
            returnWithError("No Records Found");
        }
        else
        {
            returnSuccess($searchResults);
        }

        $statement->close();
        $connection->close();
    }

    function returnWithError($error)
    {
        http_response_code(400);
        $returnValue = 
        '{
            "error" : "' . $error . '"
        }';
        sendResultInfoAsJson($returnValue);
    }

    function returnSuccess($searchResults)
    {
        $returnValue =
        '{
            "results" : 
            [' . $searchResults . ']
        }';
        sendResultInfoAsJson($returnValue);
    }

    function sendResultInfoAsJson($object)
    {
        header('Content-type: application/json');
        echo $object;
    }
?>