<html>
<head>
    <title>CPSC 304 PHP/Oracle Demonstration</title>
</head>

<body>
<h2>Campany</h2>
<hr />


<hr />

<h2>Shipping a new item</h2>
<form method="POST" action="company.php"> <!--refresh page when submitted-->
    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
    Item ID: <input type="text" name="insNo"> <br /><br />


    <input type="submit" value="Ship" name="insertSubmit"></p>
</form>

<hr />

<h2>Update Shipping status</h2>
<p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

<form method="POST" action="company.php"> <!--refresh page when submitted-->
    <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
    Item ID: <input type="text" name="itemID"> <br /><br />
    Current Location: <input type="text" name="currentLocation"> <br /><br />

    <input type="submit" value="Update" name="updateSubmit"></p>
</form>

<hr />

<h2>Delete Shipping status</h2>
<p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

<form method="POST" action="company.php"> <!--refresh page when submitted-->
    <input type="hidden" id="deleteQueryRequest" name="deleteQueryRequest">
    Item ID: <input type="text" name="itemID"> <br /><br />


    <input type="submit" value="Delete" name="deleteSubmit"></p>
</form>

<hr />

<h2>Count the Tuples in ShippingStatus</h2>
<form method="GET" action="company.php"> <!--refresh page when submitted-->
    <input type="hidden" id="countTupleRequest" name="countTupleRequest">
    <input type="submit" name="countTuples"></p>
</form>

<hr />

<h2>Display the Tuples in ShippingStatus</h2>
<form method="GET" action="company.php"> <!--refresh page when submitted-->
    <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
    <input type="submit" name="displayTuples"></p>
</form>

<?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
}
}
/*<p>Total Package: </p>
<p>Package on Road: </p>

<h2>Reset</h2>
<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

<form method="POST" action="company_alt.php">
    <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
    <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
    <p><input type="submit" value="Reset" name="reset"></p>
</form>*/
function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;

    $statement = OCIParse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }

    return $statement;
}

function executeBoundSQL($cmdstr, $list) {
    /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
In this case you don't need to create the statement several times. Bound variables cause a statement to only be
parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
See the sample code below for how this function is used */

    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            OCIBindByName($statement, $bind, $val);
            unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
}

function printResult($result) { //prints results from a select statement
    echo "<br>Retrieved data from table ShippingStatus:<br>";
    echo "<table>";
    echo "<tr><th>itemID</th><th>currentLocation</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["ITEMID"] . "</td><td>" .  $row["CURRENTLOCATION"] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}
/*
echo "<br>Retrieved data from table ShippingStatus:<br>";
    echo "<table>";
    echo "<tr><th>itemID</th><th>currentLocation</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["itemID"] . "</td><td>" .  $row["currentLocation"] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
*/
/*
 echo "<br>Retrieved data from database:<br>";
    echo "<table>";

    $printAttributes = true;

    while ($row = OCI_Fetch_Array($result, OCI_BOTH))
    {
        if ($printAttributes == true) {
            echo "<br><tr>";
            foreach (array_keys($row) as $key) {
                echo "<th>$key</th>";
            }
            echo "</tr></br>";
            $printAttributes = false;
        }

        echo "<br>";
        foreach($row as $value) {
            echo "$value     ";
        }
        echo "</br>";
    }

    echo "</table>"

*/
function connectToDB() {
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = OCILogon("ora_liyaqi", "a53908398", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB() {
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}

function handleUpdateRequest() {
    global $db_conn;

    $old_name = $_POST['itemID'];
    $new_name = $_POST['currentLocation'];

    // you need the wrap the old name and new name values with single quotations
    executePlainSQL("UPDATE ShippingStatus SET currentLocation='" . $new_name . "' WHERE itemID='" . $old_name . "'");
    OCICommit($db_conn);
}
function handleDeleteRequest() {
    global $db_conn;

    $old_name = $_POST['itemID'];


    // you need the wrap the old name and new name values with single quotations
    executePlainSQL("DELETE FROM ShippingStatus  WHERE itemID='" . $old_name . "'");
    OCICommit($db_conn);
}

function handleResetRequest() {
    global $db_conn;
    // Drop old table
    executePlainSQL("DROP TABLE ShippingStatus");

    // Create new table
    echo "<br> creating new table <br>";
    executePlainSQL("CREATE TABLE ShippingStatus (itemID int PRIMARY KEY, currentLocation char(30))");
    OCICommit($db_conn);
}

function handleInsertRequest() {
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['insNo'],

);

    $alltuples = array (
        $tuple
    );

    executeBoundSQL("insert into ShippingStatus values (:bind1,NULL)", $alltuples);
    OCICommit($db_conn);
}

function handleCountRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM ShippingStatus");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of packages in total: " . $row[0] . "<br>";
    }
}

function handleDisplayRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT * FROM ShippingStatus S");
    printResult($result);
}


// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        } else if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        } else if (array_key_exists('deleteQueryRequest', $_POST)) {
            handleDeleteRequest();
        }

        disconnectFromDB();

    }
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('countTuples', $_GET)) {
            handleCountRequest();
        }
        if (array_key_exists('displayTuples', $_GET)) {
            handleDisplayRequest();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])|| isset($_POST['deleteSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])) {
    handleGETRequest();
} else if (isset($_GET['displayTupleRequest'])) {
    handleGETRequest();
}
    ?>
</body>
</html>

