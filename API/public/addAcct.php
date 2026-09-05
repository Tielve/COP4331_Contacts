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
    
    if ($connection->connect_error)//if connect error
    {
        returnError($connection->connect_error);//give that error
    }
    else
    {
        $toRun = $connection->prepare("INSERT INTO users (username,pw) VALUES (?,?) ");//create the insert statement with placeholder

        $toRun->bind_param("ss", $inputData["username"], $inputData["pw"]);//put the actual values from inputdata into the sql statment
        
        try
        {
            $toRun->execute();//try running

            $newid = $toRun->insert_id;//get insert id from the ran script
            
            returnSuccess($newid);//return new id to caller
        }
        catch(mysqli_sql_exception $exception)//iff error
        {
            
            returnError($exception->getMessage());//return error
        }
        
        //close our connection and sql executable
        $toRun->close();
        $connection->close();
    }


    //Returns to api caller the json with ID create
    function returnSuccess($newID)
    {
        $returnval = 
        '{
            "id" : "' . $newID . '"
        }';
        sendResultInfoAsJson($returnval);
    }

    //returns to api caller the json with the error
    function returnError($errortype)
    {
        $returnval = 
        '{
            "id" : 0,
            "error" : "' . $errortype . '"
        }';
        sendResultInfoAsJson($returnval);
    }

    //send api caller the json
    function sendResultInfoAsJson($object)
    {
        header('Content-type: application/json');
        echo $object;
    }
?>
