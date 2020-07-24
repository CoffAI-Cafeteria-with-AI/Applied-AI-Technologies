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
    <style>
        input[type="submit"] {
            margin-left: 2em;
            padding: 3em;
            text-align: left;
        }
    </style>
</head>

<body>
    <div id="headerFieldset">
        <header>
            <a href="cafeteriaAI.html" title="Back to main page"><img src="./cafeteriaFavicon_breiter.png" id="logo"></a>
            <h1>CoffAI - Database</h1>
            <a href="cafeteriaAI_normalMode.php" title="Run Artificial Intelligence"><button class="homeButtons" id="runAI">Run AI</button></a>
            <a href="cafeteriaAI_Prices.html" title="Change Settings"><button class="homeButtons" id="settings">Settings</button></a>
            <a href="cafeteriaAI_database.html" title="Go to Database features"><button class="homeButtons" id="databaseButton">Database</button></a>
        </header>
        <main>
            <form action="cafeteriaAI_database.php" method="POST" class="simple-form">
                    <div hidden>
                        <input type="text" name="placeholder1" class="category" value="" />
                        <input type="text" name="placeholder2" class="category" value="" />
                        <input type="text" name="tsql" class="category" value="EXEC dbo.Dobler_sp_get_customers" />
                    </div>
                    <input type="submit" class="submit" value="Show table of customers" id="showCustomerTable"/>
            </form>
            <form action="cafeteriaAI_database.php" method="POST" class="simple-form...">
                    <div hidden>
                        <input type="text" name="placeholder1" class="category" value="" />
                        <input type="text" name="placeholder2" class="category" value="" />
                        <input type="text" name="tsql" class="category" value="EXEC dbo.Dobler_sp_get_purchases" />
                    </div>
                    <input type="submit" class="submit" value="Show table of purchases" id="showPurchasesTable"/>
            </form>
            <form action="cafeteriaAI_database.php" method="POST" class="simple-form">
                    <div hidden>
                        <input type="text" name="placeholder1" class="category" value="" />
                        <input type="text" name="placeholder2" class="category" value="" />
                        <input type="text" name="tsql" class="category" value="EXEC dbo.Dobler_sp_get_joined_table" />
                    </div>
                    <input type="submit" class="submit" value="Show both tables in a joined table" id="showJoinedTable"/>
            </form>
            <form hidden action="cafeteriaAI_database.php" method="POST" class="simple-form" id="dbCursor">
                    <div hidden>
                        <input type="text" name="callbackFile" class="category" value="cafeteriaAI_database_get_tables.php" />
                        <input type="text" name="placeholder1" class="category" value="" />
                        <input type="text" name="tsql" class="category" value="EXEC dbo.Dobler_sp_cursor" />
                    </div>
            </form>
            <div hidden id="databaseResult" style="margin: 3em;"></div>
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

    <?php
        if (isset($_POST['databaseResultDiv'])) {
            // databaseResult was received from database_connect_execute.php
            // write databaseResult on this page
            echo "<script>document.getElementById('databaseResult').innerHTML = " . json_encode($_POST['databaseResultDiv']) . "; </script>";
            echo "<script>document.getElementById('databaseResult').hidden = false; </script>";
        } else {
            // databaseResult was not received from database_connect_execute.php
            // get databaseResult
            echo "<script>
                    if(document.getElementById('databaseResult').hidden){
                        document.getElementById('dbCursor').submit();
                    };
                </script>";
        }
    ?>

</body>

</html>