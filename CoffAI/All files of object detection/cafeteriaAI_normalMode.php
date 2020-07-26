<!-- Maxim Bickel, Dennis Herzog, Philipp Dobler -->

<!DOCTYPE html>
<html lang="en">

<head>
    <title>CoffAI</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./cafeteriaAI.css">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="icon" href="./cafeteriaFavicon.png" type="image/ico">
</head>

<body>
    <div id="headerFieldset">
        <header>
            <a href="cafeteriaAI.html" title="Back to main page"><img src="./cafeteriaFavicon_breiter.png" id="logo"></a>
            <h1>CoffAI - Run AI</h1>
            <a href="cafeteriaAI_Prices.html" title="Change Settings"><button class="homeButtons" id="settings">Settings</button></a>
            <a href="cafeteriaAI_database.html" title="Go to Database features"><button class="homeButtons" id="databaseButton">Database</button></a>
        </header>
        <main>
            <form id="form" action="cafeteriaAI_normalMode_Result.php" method="POST">
                <textarea name="fileToUpload" id="fileToUpload" readonly hidden></textarea>
            </form>

            <div id="image_div">
                <img src="" id="image" class="thumb">
            </div>
            
            <div id="loadingDiv" hidden>
                <div id="loading"></div>
                <p id=loadingParagraph>Loading...</p>
            </div>
            <div id="message" hidden>
                <p></p>
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

    <?php
        if (isset($_GET["fileToUpload"])) {
            // $_GET["fileToUpload"] exists

            $fileName = $_GET["fileToUpload"];
            echo "<script>var fileToUploadPHP = " . json_encode($fileName) . "; </script>";
        }
    ?>

    <script>
        var dir = "./uploads/";
        if(typeof fileToUploadPHP !== 'undefined' && fileToUploadPHP !== null){
            // fileToUploadPHP exists

            console.log(fileToUploadPHP);
            document.getElementById("fileToUpload").innerHTML = fileToUploadPHP;

            // concat filename with upload-directory and show uploaded image on page
            var tmpPath = dir.concat(fileToUploadPHP);
            $("#image").attr("src", tmpPath);
            document.getElementById('loadingDiv').hidden = false;

            // go automatically to cafeteriaAI_normalMode_Result.php and run AI there
            document.getElementById("form").submit();
        } else {
            // fileToUploadPHP does not exist
            document.getElementById('message').innerHTML = ('Please run "Powershell-Watcher.bat" and insert the new taken image in: <br />"').concat(dir).concat('"');
            document.getElementById('message').hidden = false;
        }
    </script>

</body>

</html>