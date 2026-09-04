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

    //get our ids
    $uID = $inData["uID"];
    $cID = $inData["cID"];
    

    /***************************************
     * FRONT END JS GUYS PLEASE READ!      *
     * Use whatever getContact feature     *
     * beforehand to get the uID and cID,  *
     * then feed that into this script!    *
    ****************************************/


    if ($connection->connect_error)//if connect error, return with the error
    {
        returnWithError($connection->connect_error);
    }
    else
    {
        //prep statement
        $statement = $connection->prepare("DELETE FROM contacts WHERE cID = ? AND uID = ?");
        
        //bind our parameter
        $statement->bind_param("ii", $cID, $uID);
        
        try
        {
            $statement->execute();//excute
            if($statement->affected_rows == 0){returnWithError("Nothing was removed for cID ' . $cID . '");}//if nothing was deleted, return that
            else{returnSuccess($cID);}//return success
        }
        catch(mysqli_sql_exception $exception)
        {
            returnWithError($exception->getMessage());//if error return with error
        }
        finally
        {
            //close our connections
            $statement->close();
            $connection->close();
        }
    }

    //returns the ID that was attempted to be delected, notify the error, and say removed was false

    //returns error, with removed being false
    function returnWithError($error)
    {
        
        $returnValue = '{
            "cID" : "' . 0 . '",
            "error" : "' . $error . '"
        }';
        sendResultInfoAsJson($returnValue);
    }

    //returns the removed cID, with removed being true
    function returnSuccess($cID)
    {       
        $returnValue =
        '{
            "cID" : "' . $cID . '"
        }';
        sendResultInfoAsJson($returnValue);
    }

    //return as json
    function sendResultInfoAsJson($object)
    {
        header('Content-type: application/json');
        echo $object;
    }
?>
