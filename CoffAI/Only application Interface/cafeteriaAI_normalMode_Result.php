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
            <h1>CoffAI - Run AI</h1>
            <a href="cafeteriaAI_Prices.html" title="Change Settings"><button class="homeButtons" id="settings">Settings</button></a>
            <a href="cafeteriaAI_database.html" title="Go to Database features"><button class="homeButtons" id="databaseButton">Database</button></a>
        </header>
        <main>
            <div id="flex_div">
                <div id="image_saved_div">
                    <img src="" id="image_saved" class="thumb">
                </div>
                <div hidden>
                    <p id="counter"></p>
                </div>
                <div style="margin: auto">
                    <pre id="bill"></pre>
                    <form action="cafeteriaAI_database.php" method="POST" class="simple-form" id="formForPayment">
                        <fieldset style="margin: auto; margin-top: 4em">
                            <div hidden>
                                <input type="text" name="callbackFile" class="category" value="cafeteriaAI_normalMode_Result.php" />
                                <textarea name="fileToUpload" id="fileToUpload" readonly hidden></textarea>
                                <input type="text" name="tsql" class="category" value="EXEC dbo.Dobler_sp_create_new_purchase " />
                            </div>
                            <div>
                                <label for="matriculation_number" class="matriculation_number">Matriculation No.:</label>
                                <input type="text" name="matriculation_number" class="category" pattern="[0-9]*" required placeholder="required field" style="width:6em" />
                            </div>
                            <div hidden>
                                <input type="number" step="0.01" name="finalprice" id="formFinalprice" class="category" />
                                <input type="text" name="bill" id="form_bill" class="category" />
                            </div>
                            <input type="submit" class="submit" value="Pay" style="margin-left: 25%"/>
                        </fieldset>
                    </form>
                    <div id="databaseResult" class="center">
                    </div>
                    <div id="rechargeCreditButtonDiv" class="center" style="visibility: hidden;">
                        <button id="rechargeCreditButton">Recharge credit...</button>
                    </div>
                </div>
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
    <script>
        document.getElementById('rechargeCreditButton').onclick = function() {
            document.getElementById('databaseResult').style.visibility = "hidden";
            document.getElementById('rechargeCreditButtonDiv').style.visibility = "hidden";
            document.getElementById('formForPayment').hidden = false;
            window.open('./cafeteriaAI_database_change_credit.php');
        }
    </script>
    <?php
        $fileName = $_POST["fileToUpload"];
        if ($fileName != null) {
            // $_POST["fileToUpload"] exists

            if (!isset($_POST['databaseResultDiv'])) {
                // $_POST['databaseResultDiv'] does not exist
                // adjust paths in Object_detection_image.py and then run the batch file

                $target_dir = "./uploads";
                $target_file = $target_dir . "/" . $fileName;
                $target_dir_saved = "./finishedPictures";
                $target_file_saved = $target_dir_saved . "/" . $fileName;

                if (!file_exists($target_dir)) {
                    mkdir($target_dir);
                }
                if (!file_exists($target_dir_saved)) {
                    mkdir($target_dir_saved);
                }

                $fileLocation = "./Object_detection_image.py";
                $content = file_get_contents($fileLocation);
                $searchPattern = "/PATH_UPLOAD = '.*uploads'/";
                $replacement = "PATH_UPLOAD = '" . $target_dir . "'";
                $content = preg_replace($searchPattern, $replacement, $content);
                $searchPattern = "/IMAGE_NAME = PATH_UPLOAD \+ '.*'/";
                $replacement = "IMAGE_NAME = PATH_UPLOAD + '/" . $fileName . "'";
                $content = preg_replace($searchPattern, $replacement, $content);
                $searchPattern = "/IMAGE_SAVE = PATH_FINISHED \+ '.*'/";
                $replacement = "IMAGE_SAVE = PATH_FINISHED + '/" . $fileName . "'";
                $content = preg_replace($searchPattern, $replacement, $content);
                file_put_contents($fileLocation, $content);
                // save Object_detection_image.py

                system('cmd /c "Object_detection_image.bat"');
            } else {
                // $_POST['databaseResultDiv'] exists
                $databaseResultDiv = $_POST['databaseResultDiv'];
                if (strpos($databaseResultDiv, 'table') == false) {
                    // databaseResult is stored in table
                    echo "<script>document.getElementById('rechargeCreditButtonDiv').style.visibility = 'visible'; </script>";
                } else {
                    // databaseResult is only a message
                    echo "<script>document.getElementById('databaseResult').innerHTML = " . json_encode($databaseResultDiv) . "; </script>";
                }
                echo "<script>var formHiddenPHP = " . json_encode(true) . "; </script>";
                echo "<script>document.getElementById('databaseResult').innerHTML = " . json_encode($databaseResultDiv) . "; </script>";
            }
            $bill_counter = file_get_contents("./bills/bill_counter.txt");
            echo "<script>var billCounterPHP = " . json_encode($bill_counter) . "; </script>";
            echo "<script>var fileNamePHP = " . json_encode($fileName) . "; </script>";
        }
    ?>

    <script>
        if (typeof fileNamePHP !== 'undefined' && fileNamePHP !== null) {
            // fileNamePHP exists

            if (typeof formHiddenPHP !== 'undefined' && formHiddenPHP !== null) {
                // formHiddenPHP exists
                document.getElementById('formForPayment').hidden = formHiddenPHP;
            }
            // show finished picture
            document.getElementsByTagName('body')[0].hidden = false;
            document.getElementById('image_saved').src = "./finishedPictures/" + fileNamePHP;
            document.getElementById('fileToUpload').value = fileNamePHP;
            var savedImageFileName = fileNamePHP;
            
            var reader = new XMLHttpRequest() || new ActiveXObject('MSXML2.XMLHTTP');
            
            console.log("counter.txt =", billCounterPHP);
            getBill(billCounterPHP);

            function getBill(billCounter) {
                // create name of bill
                var fileLocation = "./bills/";
                const d = new Date();
                const year = d.getFullYear();
                const month = ("00" + (d.getMonth() + 1)).slice(-2);
                const day = ("00" + d.getDate()).slice(-2);
                billCounter = ("00000" + (billCounter - 1)).slice(-5);
                fileLocation = fileLocation.concat(year).concat("-").concat(month).concat("-").concat(day).concat("/Bill_").concat(billCounter).concat(".txt");
                console.log(fileLocation);
                loadFile(fileLocation);
            }

            function loadFile(fileLocation) {
                // load bill file
                reader.open('get', fileLocation, true);
                reader.onreadystatechange = displayContents;
                reader.send(null);
            }

            function displayContents() {
                if (reader.readyState == 4) {
                    var fileContent = reader.responseText;
                    if (fileContent.search(savedImageFileName) == 0) {
                        // loaded bill file matches uploaded picture to ensure that it's the right bill

                        var remainder = fileContent.substring(savedImageFileName.length + 1);
                        document.getElementById("bill").innerHTML = remainder;

                        // adjust string with the content of the bill file
                        let form_bill = remainder.substring(15);
                        form_bill = form_bill.replace(/ \nFinaler Preis: \d.*/,"");
                        form_bill = form_bill.replace(/ \nDazu gekommen: /g," | ");
                        form_bill = form_bill.replace(/Dazu gekommen: /,"");
                        form_bill = form_bill.replace(/ für: /g," = ");
                        console.log(form_bill);
                        document.getElementById("form_bill").value = form_bill;
                        let finalPriceString = String(remainder).match(/Finaler Preis: \d.*/);
                        let finalPrice = String(finalPriceString).match(/[0-9]+\.?[0-9]*/);
                        console.log("Finalprice =", finalPrice[0]);
                        document.getElementById("formFinalprice").value = finalPrice[0];
                    } else {
                        const billError = "Bill file does not match the uploaded picture !";
                        console.error(billError);
                        document.getElementById("bill").innerHTML = billError;
                    }
                    console.log("File name", savedImageFileName, "was read by php.");
                    console.log("File name", fileContent.substring(0, savedImageFileName.length), "was read by bill.");
                }
            }
        } else {
            // fileNamePHP does not exist
            window.document.location.href = "cafeteriaAI_normalMode.php";
        }
    </script>

</body>

</html>