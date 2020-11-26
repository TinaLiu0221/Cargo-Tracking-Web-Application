<!DOCTYPE html>

<html>
<body>
<h2>Customer</h2>
<p>Hello, Welcome^_^</p>
<hr />
<p>Click here to check all the customers' information</p>



<form method="GET" action="customer.php">
    <input type="hidden" id="printTuplesRequest" name="printTuplesRequest">
    <p><input type="submit" value="Check" name="print"></p>
</form>

<?php
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = NULL; // edit the login credentials in connectToDB()
$show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())
function debugAlertMessage($message)
{
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages)
    {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
}
}

function executePlainSQL($cmdstr)
{ //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;

    $statement = OCIParse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work
    if (!$statement)
    {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r)
    {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }

    return $statement;
}

function printShippingStatus($result) {

    echo "<table>";
    echo "<tr><th>customerID &nbsp</th><th>name &nbsp</th><th>itemID &nbsp</th><th>current location</th></tr>";


    while ($row = OCI_Fetch_Array($result, OCI_BOTH))
    {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function printCost($result) {

    echo "<table>";
    echo "<tr><th>customerID &nbsp</th><th>name &nbsp</th><th>cost</th></tr>";


    while ($row = OCI_Fetch_Array($result, OCI_BOTH))
    {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function printAgeMinPrice($result) {

    echo "<table>";
    echo "<tr><th>age &nbsp</th><th>AVGprice &nbsp</th><th>";


    while ($row = OCI_Fetch_Array($result, OCI_BOTH))
    {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" ; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function printStoreID($result) {

    echo "<table>";
    echo "<tr><th>storeID</th></tr>";


    while ($row = OCI_Fetch_Array($result, OCI_BOTH))
    {
        echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function connectToDB()
{
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = OCILogon("ora_liyaqi", "a53908398", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn)
    {
        debugAlertMessage("Database is Connected");
        return true;
    }
    else
    {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB()
{
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}

function handleGETRequest()
{
    if (connectToDB())
    {
        if (array_key_exists('printTuplesRequest', $_GET))
        {
            handlePrintRequest();
        }
        disconnectFromDB();
    }
}



function handlePrintRequest()
{
    $result1 = executePlainSQL("SELECT c.customerID, c.name, itc.itemID, s.currentLocation FROM Customer c, ItemToCustomer itc, ShippingStatus s WHERE c.customerID = itc.customerID AND itc.itemID = s.itemID");

    echo "Shipping status:";
    printShippingStatus($result1);

    $result2 = executePlainSQL("SELECT c.customerID, c.name, SUM(i.price) FROM Customer c, ItemToCustomer itc, Item i WHERE c.customerID = itc.customerID AND itc.itemID = i.itemID GROUP BY c.customerID, c.name");

    echo "Total cost of each customer";
    printCost($result2);

    $result3 = executePlainSQL("SELECT c.customerID, c.name, SUM(i.price) FROM Customer c, ItemToCustomer itc, Item i WHERE c.customerID = itc.customerID AND itc.itemID = i.itemID GROUP BY c.customerID, c.name HAVING SUM(i.price) > 125");

    echo "Total cost of customers who have purchased at least 125 dollars worth of items";
    printCost($result3);

    $result4 = executePlainSQL("SELECT
    c.customerID,
        c.name,
        i.price
    FROM
    Customer c,
    ItemToCustomer itc,
    Item i
    WHERE
    c.customerID = itc.customerID
    AND itc.itemID = i.itemID
    AND i.price >= ALL (
    SELECT
    i2.price
    FROM
    Item i2
)
    ");

    echo "Customer who have purchased the most expensive item";
    printCost($result4);

    $result5 = executePlainSQL("
    SELECT
    distinct its.storeID
    FROM
    ItemToStore its
    WHERE
    NOT EXISTS (
    (SELECT
    its2.itemID
    FROM
    ItemToStore its2
    WHERE
    its.storeID = its2.storeID)
    MINUS
    (SELECT
    itemID
    FROM
    ShippingStatus
    WHERE
    currentLocation = 'Vancouver'))
    ");

    echo "Stores that have every one of its item currently in Vancouver";
    printStoreID($result5);

    $result6 = executePlainSQL("
    SELECT
    c.age, AVG(i.price)
    FROM
    ItemToCustomer it, Item i, Customer c
    WHERE
    c.customerID = it.customerID
    AND i.itemID = it.ItemID
    GROUP BY c.age
    HAVING AVG(i.price) <= all (
    SELECT
    AVG(ii.price)
    FROM 
    Customer cc, Item ii, ItemToCustomer iitt
    WHERE
    cc.customerID = iitt.customerID
    AND ii.itemID = iitt.ItemID
    GROUP BY cc.age
    )
    
    ");
    echo "Ages for which their average price in purchase is the minimum over all ages";
    printAgeMinPrice($result6);


}
if (isset($_GET['printTuplesRequest']))
{
    handleGETRequest();
}
    ?>

</body>
</html>