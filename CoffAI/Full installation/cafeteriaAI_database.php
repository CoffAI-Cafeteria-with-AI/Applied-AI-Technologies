<!-- Maxim Bickel, Dennis Herzog, Philipp Dobler -->

<!DOCTYPE html>
<html lang="en">

<head>
    <title>CoffAI</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./cafeteriaAI.css">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="./cafeteriaFavicon.png" type="image/ico">
</head>

<body hidden>
    <div id="headerFieldset">
        <header>
            <a href="cafeteriaAI.html" title="Back to main page"><img src="./cafeteriaFavicon_breiter.png" id="logo"></a>
            <h1>CoffAI - Database</h1>
            <a href="cafeteriaAI_normalMode.php" title="Run Artificial Intelligence"><button class="homeButtons" id="runAI">Run AI</button></a>
            <a href="cafeteriaAI_Prices.html" title="Change Settings"><button class="homeButtons" id="settings">Settings</button></a>
            <a href="cafeteriaAI_database.html" title="Go to Database features"><button class="homeButtons" id="databaseButton">Database</button></a>
        </header>
        <main>
            <!-- form to submit databaseResult to website which made an SQL-query -->
            <form hidden action="" method="POST" class="simple-form" id="back">
                <textarea name="fileToUpload" id="fileToUpload"></textarea>
                <textarea name="databaseResultDiv" id="databaseResultDiv"></textarea>
            </form>
            <div id="databasePage" style="margin:2em">
                <?php
                    function OpenConnection()
                    {
                        try {
                            // here we have used the database server from our local university
                            // due to privacy we had to replace the login information for our database
                            $serverName = "serverName";
                            $username = "username";
                            $password = "password";
                            $database = "database";
                            $connectionOptions = array("Database"=>$database, "UID"=>$username, "PWD"=>$password, "CharacterSet"=>"UTF-8", 'ReturnDatesAsStrings'=> true);
                            $conn = sqlsrv_connect($serverName, $connectionOptions);
                            if ($conn == false) {
                                echo "Connection could not be established.<br />";
                                die(print_r(sqlsrv_errors(), true));
                            }
                            return $conn;
                        } catch (Exception $e) {
                            echo("Error!");
                        }
                    }

                    function OpenConnectionAndReadData()
                    {
                        try {
                            $tsql = "";
                            // $_POST[0] = name="callbackFile"     class="category"        value="cafeteriaAI_normalMode_Result.php"
                            // $_POST[1] = name="fileToUpload"     id="fileToUpload"
                            // $_POST[2] = name="tsql"             class="category"        value="EXEC dbo.Dobler_sp_create_new_purchase"

                            $i = 0;
                            foreach ($_POST as $key => $value) {
                                if ($i > 1) {
                                    if ($value == null) {
                                        $value = 'NULL';
                                    } else {
                                        if ($i != 2) {
                                            $value = "'".$value."'";
                                        }
                                    }
                                    if ($i == 2 || $i == 3) {
                                        $tsql = $tsql.$value;
                                    } else {
                                        $tsql = $tsql.",".$value;
                                    }
                                }
                                $i++;
                            }
                            $wholeDivText = "";
                            if ($tsql != "") {
                                $conn = OpenConnection();
                                $stmt = sqlsrv_query($conn, $tsql);

                                if ($stmt == false) {
                                    // error at statement execution

                                    if (($errors = sqlsrv_errors()) != null) {
                                        foreach ($errors as $error) {
                                            $message = $error[ 'message'];
                                            $message = preg_replace('/\[.*\]\[.*\]/', '', $message);
                                            echo $message;
                                            $wholeDivText = $wholeDivText.$message;
                                        }
                                    }
                                } else {
                                    // no error at statement execution

                                    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

                                    // very important in SQL when Update / Insert / Delete: SET NOCOUNT ON

                                    $zeile = 0;

                                    if ($row!=false) {
                                        // Resultset is not empty

                                        if ($row != null) {
                                            $wholeDivText = "<table>";
                                            echo "<div id='databaseOutput'><table>";
                                            $columnNumber = sqlsrv_num_fields($stmt);
                                            $wholeDivText = $wholeDivText."<tr><th>Row</th>";
                                            echo("<tr>");
                                            echo("<th>Row</th>");

                                            // Read column names
                                            for ($i=0; $i < $columnNumber; $i++) {
                                                $wholeDivText = $wholeDivText."<th>";
                                                echo("<th>");
                                                $wholeDivText = $wholeDivText.array_keys($row)[$i];
                                                print_r(array_keys($row)[$i]);
                                                echo("</th>");
                                                $wholeDivText = $wholeDivText."</th>";
                                            }
                                            echo("</tr>");
                                            $wholeDivText = $wholeDivText."</tr>";

                                            // read all cells from all rows
                                            do {
                                                $wholeDivText = $wholeDivText."<tr>";
                                                echo("<tr>");
                                                $wholeDivText = $wholeDivText."<td style='text-align:right'>".++$zeile."</td>";
                                                echo("<td style='text-align:right'>".$zeile."</td>");

                                                // Read content of each cell from actual row
                                                foreach ($row as $field => $value) {
                                                    if (is_string($value)) {
                                                        $wholeDivText = $wholeDivText."<td>".$value."</td>";
                                                        echo("<td>".$value."</td>");
                                                    } else {
                                                        $wholeDivText = $wholeDivText."<td style='text-align:right'>".$value."</td>";
                                                        echo("<td style='text-align:right'>".$value."</td>");
                                                    }
                                                }
                                                $wholeDivText = $wholeDivText."</tr>";
                                                echo("</tr>");
                                            } while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC));
                                            // go to next row, if exists

                                            $wholeDivText = $wholeDivText."</table>";
                                            echo "</table></div>";
                                        }
                                    } else {
                                        $wholeDivText = $wholeDivText."No entries to display !";
                                        echo "No entries to display !";
                                    }
                                    sqlsrv_free_stmt($stmt);
                                }
                                /* Free statement and connection resources. */
                                sqlsrv_close($conn);

                                // save PHP contents to javascript variables
                                if (isset($_POST['callbackFile'])) {
                                    echo "<script>var callbackFilePHP = " . json_encode($_POST['callbackFile']) . "; </script>";
                                }
                                if (isset($_POST['fileToUpload'])) {
                                    echo "<script>var fileToUploadPHP = " . json_encode($_POST['fileToUpload']) . "; </script>";
                                }
                                echo "<script>var wholeDivTextPHP = " . json_encode($wholeDivText) . "; </script>";
                            } else {
                                echo "No SQL Statement available !";
                            }
                        } catch (Exception $e) {
                            echo("Error!");
                        }
                    }
                    OpenConnectionAndReadData();
                ?>
                <script>
                    if (typeof fileToUploadPHP !== 'undefined' && fileToUploadPHP !== null) {
                        document.getElementById("fileToUpload").value = fileToUploadPHP;
                    }
                    if (typeof wholeDivTextPHP !== 'undefined' && wholeDivTextPHP !== null) {
                        document.getElementById("databaseResultDiv").value = wholeDivTextPHP;
                    }
                    if(typeof callbackFilePHP !== 'undefined' && callbackFilePHP !== null){
                        // callbackFilePHP exists --> do a callback to this website
                        document.getElementById("back").action = callbackFilePHP;
                        document.getElementById("back").submit();
                    } else {
                        // callbackFilePHP doesn't exists --> show body on this page
                        document.getElementsByTagName('body')[0].hidden = false;
                    }
                </script>
            </div>
        </main>
    </div>

    <footer>
        &copy;
        <script>
            const d = new Date();
            document.write(d.getFullYear());
        </script>
        by Maxim Bickel, Dennis Herzog & Philipp Dobler
    </footer>

</body>

</html>