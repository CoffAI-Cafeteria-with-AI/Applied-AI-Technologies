/* Maxim Bickel, Dennis Herzog, Philipp Dobler */

'use strict';

window.fileLocation = "./utils/visualization_utils.py";

var reader = new XMLHttpRequest() || new ActiveXObject('MSXML2.XMLHTTP');

var allCategories;
var fileContent;

var newFileContentPHP;
var arrayPHP;
var firstCallPHP;

// transfers variables from php-file to this javascript file as global variables
function transferData(tmp1, tmp2, tmp3) {
    newFileContentPHP = tmp1;
    arrayPHP = tmp2;
    firstCallPHP = tmp3;
}

function changeCurrency(selectedCurrency = document.getElementById("selectedCurrency").value) {
    let tempArray = document.getElementsByClassName('currency');
    for (let i = 0; i < tempArray.length; i++) {
        let element = tempArray[i];
        element.innerHTML = selectedCurrency;
    }
}

function loadFile() {
    reader.open('get', fileLocation, true);
    reader.onreadystatechange = displayContents;
    reader.send(null);
}

function displayContents() {
    if (reader.readyState == 4) {
        fileContent = reader.responseText;

        // set currency to the currency, which can be found in file
        var currency;
        if (fileContent.search(/[€]/) != -1) {
            currency = "€";
        } else if (fileContent.search(/[$]/) != -1) {
            currency = "$";
        } else if (fileContent.search(/[£]/) != -1) {
            currency = "£";
        }
        if (document.getElementById('selectedCurrency')) {
            let element = document.getElementById('selectedCurrency');
            element.value = currency;
            adjustNamesAndPrices();
        } else {
            changeCurrency(currency);
        }
    }
}

function adjustNamesAndPrices(tmp = fileContent, priceArray = 0) {
    // create Elements and adjust Names dynamically, set the new prices and correct the currency
    // show all in the formular

    var foundLine = tmp.match(/prices = \{("[\wäöüß ]+": [0-9]*[.|,]{0,}[0-9]{1,2}, *)*\}/);
    allCategories = String(foundLine[0]).match(/,/g);
    if (!document.getElementById("0")) {
        createElementsDynamically();
    }
    changeCurrency();

    var categoryStringArray = String(foundLine).match(/"[\wäöüß ]+": [0-9]*[.|,]{0,}[0-9]{1,2},/g);
    var categoryNameArray = String(foundLine).match(/"[\wäöüß ]+"/g);
    for (let i = 0; i < allCategories.length; i++) {
        let loopElement = document.querySelectorAll('.name')[i];
        loopElement.innerHTML = categoryNameArray[i].replace(/"/g, '').concat(":");
        if (priceArray == 0) {
            var singleCategoryPrice = String(categoryStringArray[i]).match(/[0-9]*[.|,]{0,}[0-9]{1,2}/);
            document.querySelectorAll('.category')[i].placeholder = singleCategoryPrice;
            var callFunction = true;
        } else {
            document.querySelectorAll('.category')[i].placeholder = priceArray[i + 1];
            console.log(priceArray[i + 1]);
        }
    }
    if (callFunction) {
        // no priceArray parameter from PHP
        adjustDisplayedValues();
    }
}

function savePlaceholder() {
    // convert all placeholders to written values
    for (let i = 0; i < allCategories.length; i++) {
        let loopElement = document.querySelectorAll('.category')[i];
        if (loopElement.checkValidity() && loopElement.value == "") {
            // if invalid --> no submit
            // if valid AND empty then replace placeholder
            loopElement.value = loopElement.placeholder;
        }
    }
    adjustCurrency();
}

function adjustCurrency() {
    // hand the selected currency over
    var selectedCurrency = document.getElementById("selectedCurrency").value;
    fileContent = fileContent.replace(/[€$£]/g, selectedCurrency);
    document.getElementById("fileContent").innerHTML = fileContent;
    document.getElementById("htmlForm").submit();
}

function adjustDisplayedValues() {
    // to keep the values everytime actual
    if (typeof newFileContentPHP !== 'undefined') {
        if (typeof arrayPHP !== 'undefined') {
            document.getElementById("selectedCurrency").value = arrayPHP[0];
            adjustNamesAndPrices(newFileContentPHP, arrayPHP);
        }
        document.getElementById("fileContent").innerHTML = newFileContentPHP;
    }
    if (typeof firstCallPHP !== 'undefined') {
        document.getElementById('changedPricesMessage').style.visibility = firstCallPHP;
    }
}

function createElementsDynamically() {
    console.log(allCategories.length);
    // create divs with label, input and paragraph (for currency) depended on the amount of categories
    for (let i = 0; i < allCategories.length; i++) {

        let newDiv = document.createElement('div');
        let endDivs = document.getElementById("endDivs");
        endDivs.insertAdjacentElement("beforebegin", newDiv);

        let newLabel = document.createElement('label');
        newLabel.setAttribute("for", "name");
        newLabel.setAttribute("class", "name");
        newDiv.insertAdjacentElement("beforeend", newLabel);

        let newInput = document.createElement('input');
        newInput.setAttribute("type", "text");
        newInput.setAttribute("name", i);
        newInput.setAttribute("class", "category");
        newInput.setAttribute("id", i);
        newInput.setAttribute("pattern", "[0-9]*[.|,]{0,}[0-9]{1,2}");
        // https://www.mediaevent.de/html/input-pattern.html invalid – Hilfestellung per CSS
        newDiv.insertAdjacentElement("beforeend", newInput);

        let newP = document.createElement('p');
        newP.setAttribute("class", "currency");
        newP.innerHTML = "€";
        newDiv.insertAdjacentElement("beforeend", newP);
    }
}

function replaceComma() {
    // replace all commas to periods, if any
    document.getElementById("money1").value = document.getElementById("money1").value.replace(/,/g, '.');
    document.getElementById("money2").value = document.getElementById("money2").value.replace(/,/g, '.');
}