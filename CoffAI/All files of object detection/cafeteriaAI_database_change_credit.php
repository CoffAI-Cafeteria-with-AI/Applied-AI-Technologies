<!-- Maxim Bickel, Dennis Herzog, Philipp Dobler -->

<!DOCTYPE html>
<html lang="en">

<head>
    <title>CoffAI</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./cafeteriaAI.css">
    <script>
        // assign random number to rerun loadFile() each time (ignore cache)
        document.write('<script src="./cafeteriaAI.js?dev=' + Math.floor(Math.random() * 100) + '"\><\/script>');
    </script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="./cafeteriaFavicon.png" type="image/ico">
    <style>
        fieldset {
            margin: 3em 0 0 0;
        }
    </style>
</head>

<body onload="loadFile()">
    <div id="headerFieldset">
        <header>
            <a href="cafeteriaAI.html" title="Back to main page"><img src="./cafeteriaFavicon_breiter.png" id="logo"></a>
            <h1>CoffAI - Database</h1>
            <a href="cafeteriaAI_normalMode.php" title="Run Artificial Intelligence"><button class="homeButtons" id="runAI">Run AI</button></a>
            <a href="cafeteriaAI_Prices.html" title="Change Settings"><button class="homeButtons" id="settings">Settings</button></a>
            <a href="cafeteriaAI_database.html" title="Go to Database features"><button class="homeButtons" id="databaseButton">Database</button></a>
        </header>
        <main>
            <div id="flex_sideBySide">
                <form action="cafeteriaAI_database.php" method="POST" class="simple-form">
                    <fieldset>
                        <legend>To get the credit:</legend>
                        <div hidden>
                            <input type="text" name="callbackFile" class="category" value="cafeteriaAI_database_change_credit.php" />
                            <input type="text" name="fileToUpload" id="fileToUpload" class="category" />
                            <input type="text" name="tsql" class="category" value="EXEC dbo.Dobler_sp_get_credit " />
                        </div>
                        <div>
                            <label for="matriculation_number" class="matriculation_number">Matriculation No.:</label>
                            <input type="text" name="matriculation_number" class="category" pattern="[0-9]*" required placeholder="required field" style="width:6em" />
                        </div>
                        <div class="spacing_1_5"></div>

                        <input type="submit" class="submit" value="Get credit" />
                    </fieldset>
                </form>
                <form action="cafeteriaAI_database.php" method="POST" class="simple-form">
                    <fieldset>
                        <legend>To recharge the corresponding credit:</legend>
                        <div hidden>
                            <input type="text" name="callbackFile" class="category" value="cafeteriaAI_database_change_credit.php" />
                            <input type="text" name="fileToUpload" id="fileToUpload" class="category" />
                            <input type="text" name="tsql" class="category" value="EXEC dbo.Dobler_sp_recharge_credit " />
                        </div>
                        <div>
                            <label for="matriculation_number" class="matriculation_number">Matriculation No.:</label>
                            <input type="text" name="matriculation_number" class="category" pattern="[0-9]*" required placeholder="required field" style="width:6em" />
                        </div>
                        <div>
                            <label for="credit" class="credit">Money:</label>
                            <input type="text" name="credit" class="category" id="money1" pattern="[0-9]*[.|,]{0,}[0-9]{1,2}" required placeholder="required field" style="width:6em" />
                            <p class="currency"></p>
                        </div>
                        <div class="spacing_1_5"></div>

                        <input type="submit" class="submit" value="Recharge credit" onclick="replaceComma();" />
                    </fieldset>
                </form>
                <form action="cafeteriaAI_database.php" method="POST" class="simple-form">
                    <fieldset>
                        <legend>To withdraw money from credit:</legend>
                        <div hidden>
                            <input type="text" name="callbackFile" class="category" value="cafeteriaAI_database_change_credit.php" />
                            <input type="text" name="fileToUpload" id="fileToUpload" class="category" />
                            <input type="text" name="tsql" class="category" value="EXEC dbo.Dobler_sp_withdraw_money_from_credit " />
                        </div>
                        <div>
                            <label for="matriculation_number" class="matriculation_number">Matriculation No.:</label>
                            <input type="text" name="matriculation_number" class="category" pattern="[0-9]*" required placeholder="required field" style="width:6em" />
                        </div>
                        <div>
                            <label for="credit" class="credit">Money:</label>
                            <input type="text" name="credit" class="category" id="money2" pattern="[0-9]*[.|,]{0,}[0-9]{1,2}" required placeholder="required field" style="width:6em" />
                            <p class="currency"></p>
                        </div>
                        <div class="spacing_1_5"></div>

                        <input type="submit" class="submit" value="Withdraw money from credit" style="width: 16em" onclick="replaceComma();" />
                    </fieldset>
                </form>
            </div>
            <div hidden id="databaseResultForChangedCredits"></div>
            <?php
                if (isset($_POST['databaseResultDiv'])) {
                    // databaseResult was received from database_connect_execute.php
                    // write databaseResult on this page
                    echo "<script>document.getElementById('databaseResultForChangedCredits').innerHTML = " . json_encode($_POST['databaseResultDiv']) . "; </script>";
                    echo "<script>document.getElementById('databaseResultForChangedCredits').hidden = false; </script>";
                }
                // get currency from visualization.py
                if (isset($_POST['fileToUpload']) && $_POST['fileToUpload'] != false) {
                    $fileContent = file_get_contents($_POST['fileToUpload']);
                    echo "<script>var fileContentPHP = " . json_encode($fileContent) . "; </script>";
                }
            ?>
            <script>
                document.getElementById('fileToUpload').value = fileLocation;
                if (typeof fileContentPHP !== 'undefined') {
                    // received currency from visualization.py
                    if (document.getElementById('databaseResultForChangedCredits').innerHTML.search(/^No /) == -1) {
                        // no error message
                        // set currency on currency which is in visualization.py
                        var currency;
                        if (fileContentPHP.search(/[€]/) != -1) {
                            currency = " €";
                        } else if (fileContentPHP.search(/[$]/) != -1) {
                            currency = " $";
                        } else if (fileContentPHP.search(/[£]/) != -1) {
                            currency = " £";
                        }
                        document.getElementById("databaseResultForChangedCredits").innerHTML = document.getElementById("databaseResultForChangedCredits").innerHTML + currency;
                    }
                }
                // if message starts with "No " --> error message
                if (document.getElementById('databaseResultForChangedCredits').innerHTML.search(/^No /) != -1) {
                    document.getElementById("databaseResultForChangedCredits").style.borderColor = "#c80000";
                }
                // if message contains " not sufficient " --> error message
                if (document.getElementById('databaseResultForChangedCredits').innerHTML.search(/ not sufficient /) != -1) {
                    document.getElementById("databaseResultForChangedCredits").style.borderColor = "#c80000";
                }
            </script>
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