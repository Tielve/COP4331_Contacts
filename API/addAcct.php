<?php
    $inputData = json_decode(file_get_contents('php://input'), true);//make json file front end gives us as useable
    //---------------------------------------------------------------------------------
    //Database guy, change below to have the correct credentials if it does not already
    //Thank you!
    //---------------------------------------------------------------------------------
    $connection = new mysqli("localhost", "admin", "admin", "contactmanagerdb"); //connect to our database

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
