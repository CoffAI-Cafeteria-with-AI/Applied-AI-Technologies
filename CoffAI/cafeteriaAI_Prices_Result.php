<!-- Maxim Bickel, Dennis Herzog, Philipp Dobler -->

<!DOCTYPE html>
<html lang="en">

<head>
    <title>CoffAI</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./cafeteriaAI.css">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="./cafeteriaAI.js"></script>
    <link rel="icon" href="./cafeteriaFavicon.png" type="image/ico">
</head>

<body>
    <div id="headerFieldset">
        <header>
            <a href="cafeteriaAI.html" title="Back to main page"><img src="./cafeteriaFavicon_breiter.png" id="logo"></a>
            <h1>CoffAI - Settings</h1>
            <a href="cafeteriaAI_normalMode.php" title="Run Artificial Intelligence"><button class="homeButtons" id="runAI">Run AI</button></a>
            <a href="cafeteriaAI_database.html" title="Go to Database features"><button class="homeButtons" id="databaseButton">Database</button></a>
        </header>
        <main>
            <div id="flex_div">
                <form action="cafeteriaAI_Prices_Result.php" method="POST" class="simple-form">
                    <fieldset>
                        <legend>Please enter the new prices!</legend>
                        <div>
                            <label for="titel">Currency:</label>
                            <select onchange="changeCurrency()" name="selectedCurrency" id="selectedCurrency" class="form_selection">
                            <option value="€">€ Euro</option>
                            <option value="$">$ Dollar</option>
                            <option value="£">£ Pound</option>
                        </select>
                        </div>
                        <div class="spacing_0_35"></div>
                        <div id="endDivs" hidden>
                            <textarea name="fileContent" id="fileContent" readonly></textarea>
                        </div>
                        <div class="spacing_0_35"></div>

                        <input type="submit" class="submit" value="Save" onclick="savePlaceholder();" />
                        <input type="reset" class="reset" value="Reset" />
                    </fieldset>
                </form>
                <p id="changedPricesMessage" style="margin-left:5em; visibility:hidden;">Changed prices successfully!</p>
            </div>

            <?php
                if (isset($_POST["selectedCurrency"])) {
                    // $_POST["selectedCurrency"] exists

                    // make array and write all POST variables into it
                    $array = array();
                    $foreachCounter = 0;
                    foreach ($_POST as $key => $value) {
                        $array[$foreachCounter] = $value;
                        $foreachCounter++;
                    }

                    // replace the comma with a period, if any, in all strings in the array
                    for ($i = 0; $i < count($array) - 1; $i++) {
                        if (isset($array[$i])) {
                            $array[$i] = str_replace(',', '.', $array[$i]);
                            $ok = true;
                        }
                    }
                    if ($ok) {
                        // the last element of the array was successfully reached

                        // replace old prices in visualization_utils.py with the new ones
                        $fileLocation = "./utils/visualization_utils.py";
                        $content = $_POST["fileContent"];
                        $searchPattern = '/prices = \{("[\wäöüß ]+": [0-9]*[.|,]{0,}[0-9]{1,2}, *)*\}/';
                        preg_match('/prices = \{("[\wäöüß ]+": [0-9]*[.|,]{0,}[0-9]{1,2}, *)*\}/', $content, $foundLine);
                        preg_match_all('/,/', $foundLine[0], $allCategories);
                        $categoryNumber = count($allCategories[0]);
                        // $allCategories[0][0] = ','           $allCategories[0][1] = ','      etc.
                        preg_match_all('/"[\wäöüß ]+": [0-9]*[.|,]{0,}[0-9]{1,2},/', $foundLine[0], $categoryStringArray);
                        preg_match_all('/"[\wäöüß ]+"/', $foundLine[0], $categoryNameArray);
                        $replacement = '';
                        for ($i = 0; $i < $categoryNumber; $i++) {
                            $loopElementName = $categoryNameArray[0][$i].": ";
                            if ($i==0) {
                                $tmp = 'prices = {';
                            } else {
                                $tmp = '';
                            }
                            $replacement = $replacement.$tmp.$loopElementName.$array[$i+1].", ";
                        }
                        $replacement = $replacement."}";

                        // $replacement = 'prices = {"Getraenk": '.$array[0].', "Salat": '.$array[1].', "Hauptspeise": '.$array[2].', "Beilage": '.$array[3].', "Vorspeise": '.$array[4].', "Dessert": '.$array[5].',}';
                        $content = preg_replace($searchPattern, $replacement, $content);
                        $content = preg_replace('/€$£/', $array[0], $content);
                        file_put_contents($fileLocation, $content);
                        // save visualization_utils.py

                        echo "<script>var newFileContentPHP = " . json_encode($content) . "; </script>";
                        $firstCall = "visible";
                        echo "<script>var firstCallPHP = " . json_encode($firstCall) . "; </script>";
                        echo "<script>var arrayPHP = " . json_encode($array) . "; </script>";
                    }
                }
            ?>
            <script>
                // for change prices again, load newly written file
                loadFile();
                transferData(newFileContentPHP, arrayPHP, firstCallPHP);
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