<?php

    $inData = json_decode(file_get_contents('php://input'), true);//decode input
    $connection = new mysqli("localhost", "admin", "admin", "ContactManager");//connect to sql database
    

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
            if($statement->affected_rows == 0){returnNothingDeleted($cID);}//if nothing was deleted, return that
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
    function returnNothingDeleted($cID)
    {
        $returnValue = '{ 
            "cID" : "' . $cID . '", 
            "Error" : "Nothing was removed for cID ' . $cID . '",  
            "Removed" : false 
        }';
        sendResultInfoAsJson($returnValue);
    }

    //returns error, with removed being false
    function returnWithError($error)
    {
        $returnValue = 
        '{
            "error" : "' . $error . '",
            "Removed": false
        }';
        sendResultInfoAsJson($returnValue);
    }

    //returns the removed cID, with removed being true
    function returnSuccess($cID)
    {   
        
        $returnValue =
        '{
            "cID" : "' . $cID . '",
            "Removed" : true
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






