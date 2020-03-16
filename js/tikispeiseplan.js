// Global Variables
var inputText = document.getElementById("menuInput");
var submitButton = document.getElementById("submitButton");
var weekDays = ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"];
var restaurants = [
	{ name: "kunzmann", regex: /^kunzmann$/i, image: "img/res_kunzmann_logo.svg", icon:"img/res_kunzmann_icon.png"},
	{ name: "teamfood", regex: /^team\s?food$/i, image: "img/res_teamfood_logo.png", icon:"img/res_teamfood_icon.png" },
];
// Datum muss im ISO Format sein https://www.w3schools.com/js/js_date_formats.asp
var dateFormat = /\d{4}-\d{2}.\d{2}/;
var priceFormat = /^\d{1,2}[,.]{1}\d{2}€?$/;

// Variablen, welche die eingegebenen Speiseplandaten enthalten
var restaurant;
var menuSegments;
var menuItems;

window.onload = testCollision;
window.onresize = testCollision;

// Wenn das Element inputText nicht gefunden wurde, sind wir in der falschen Datei
if(inputText)
{
	// Fokussiert und Selektiert den Eingabebereich
	inputText.onfocus = function () {
		inputText.select();
	}

	inputText.focus();

	// Event löst bei jedem eingegebenen Zeichen aus
	inputText.oninput = tryParse;

	// Upload Button
	submitButton.onclick = uploadMenu;
}


// ---------------------------------------------------------
// Funktion zum Parsen der Eingabe
// ---------------------------------------------------------
function tryParse() {
	var text = inputText.value;
	var lines = text.split(/\r?\n/);
	menuItems = new Array();

	// Vorschaubereich zurücksetzen
	flushPreview();

	// Prüfe ob Testfeld leer
	if (lines[0] == null) return;

	restaurant = checkRestaurant(lines[0]);
	if (!restaurant) return;

	menuSegments = extractMenuSegments(lines);
	if (!menuSegments) return;

	for(var seg of menuSegments) {
		var newItems = parseMenuSegment(seg, restaurant);
		menuItems.push(newItems);
	}

	renderPreview(menuItems);

	return menuItems;
}

// ---------------------------------------------------------
// Prüfe ob gültiges Restaurant angegeben
// ---------------------------------------------------------
function checkRestaurant(line) {
	var rest;

	for (var r of restaurants) {
		if (r.regex.test(line)) {
			rest = r;
		}
	}

	if (!rest) 
	{
		inputText.style.backgroundImage = "";
		return null;
	}

	inputText.style.backgroundImage = "url('" + rest.image + "')";
	return rest;
}


// ---------------------------------------------------------
// Extrahiert aus einem Zeilen Array die Teilarrays,
// die mit einem Wochentag beginnen
// ---------------------------------------------------------
function extractMenuSegments(lineArray) {
	var weekDayLines = new Array();
	var i;

	// -- Finde Zeilen, die gültiges Datum enthalten
	for (i = 0; i < lineArray.length; i++) {
		if (dateFormat.test(lineArray[i])) weekDayLines.push(i);
	}
	if (weekDayLines.length == 0) return null;
	//console.log(weekDayLines);

	// -- Erzeuge Array aus Speiseplan-Segmenten (Tage)
	var resultArray = [];
	for (i = 0; i < (weekDayLines.length - 1); i++) {
		resultArray.push(lineArray.slice(weekDayLines[i], weekDayLines[i + 1]));
	}
	resultArray.push(lineArray.slice(weekDayLines[i]));
	//console.log(resultArray);

	return resultArray;
}

// ---------------------------------------------------------
// Extrahiert die Daten aus einem MenuSegment
// (ein Kalendertag / evtl. mehrere Gerichte)
// ---------------------------------------------------------
function parseMenuSegment(menuSegment, restaurant) {
	var segmentContent;
	var menuItems = new Array();
	var date;

	// Guard Clauses
	if (!menuSegment) {
		console.log("Leeres menuSegment übergeben.");
		return;
	}
	if (menuSegment.length < 2) {
		console.log("menuSegment mit nur einer Zeile übergeben.");
		return;
	}

	// slice nimmt ein Teilarray (ab Element mit Index 1)
	segmentContent = menuSegment.slice(1);

	// Datum extrahieren
	var date = new Date(dateFormat.exec(menuSegment[0]));

	// Inhalt extrahieren
	// Format: Beschreibung; Zusätzliche Beschreibung; Beilage; Typ; Preis
	for (var line of segmentContent) {
		var lineContent = line.split(';');
		let price = lineContent[4];
		if(price && priceFormat.test(price)) {
			price = price.replace(",", ".");
			price = price.replace("€", "");
		}
		else price = "0";
		var item = new MenuItem(restaurant, date, lineContent[0], lineContent[1], lineContent[2], lineContent[3], price);
		menuItems.push(item);
	}

	return menuItems;
}

// ---------------------------------------------------------
// Konstruktor MenuItem
// ---------------------------------------------------------
function MenuItem(restaurant, date, descr, addDescr, side, type, price) {
	this.restaurant = restaurant;
	this.date = date;
	this.descr = descr;
	this.addDescr = addDescr;
	this.side = side;
	this.type = type;
	this.price = price;
}

// ---------------------------------------------------------
// Zeigt eine Vorschau der eingegebenen MenuItems. Eine MenuItem
// stellt einen Tag dar und kann mehrere Gerichte enthalten.
//
// Zielformat:
// -----------
// <div class="day">
// 	<h2>Mittwoch, 12.02.2020</h2>
// 	<table class="dishtable">
// 		<tr>
// 			<td>
// 				<span class="dish">Hähnchenbrust "Melba" (+ side)</span>
//				<span class="addition">addDesr</span>
// 			</td>
// 			<td class="priceCell">
// 				4,80€
// 			</td>
// 		</tr>
// 		<tr>
// 			<td>
// 				<span class="dish">Käsespätzle mit Speckwürfel</span>
// 			</td>
// 			<td class="priceCell">
// 				4,80€
// 			</td>
// 		</tr>
// 	</table>
// </div>
// ---------------------------------------------------------
function renderPreview(menuItems) {
	var previewSection = document.getElementById("previewDiv");
	if (!previewSection) {
		console.log("HTML <div> PreviewDiv nicht gefunden!");
		return;
	}
	
	if(!menuItems) return;
	if(menuItems.length == 0) return;
	
	// Jedes Element in menuItems steht für einen Tag
	for(var menuDay of menuItems)
	{
		if (!menuDay || menuDay.length == 0) {
			console.log("Leerer menuDay.");
			return;
		}

		// Section, überschrift und Tabelle
		var sec = document.createElement("div");
		sec.className = "day";

		var header = document.createElement("h2");
		var date = menuDay[0].date;			// Datum kann aus beliebigem Element genommen werden, hier 0
		var dateText = weekDays[date.getDay()] + ', ' + date.getDate().toString().padStart(2, '0') + '.' + (date.getMonth(date) + 1).toString().padStart(2, '0') + '.' + date.getFullYear();
		header.innerText = dateText;

		var table = document.createElement("table");
		table.className = "dishtable";

		sec.appendChild(header);
		sec.appendChild(table);

		// Einzelne Einträge zu Tabelle zusammenfügen
		for(var dish of menuDay) {
			var row = formPreviewRow(dish);
			table.appendChild(row);
		}

		previewSection.appendChild(sec);
	}
}

// ---------------------------------------------------------
// Formuliert eine Tabellen-Zeile für das Preview
//
//	<tr>
// 		<td>
// 			<span class="dish">Hähnchenbrust "Melba" (+ side)</span>
//				<span class="addition">addDesr</span>
// 		</td>
// 		<td class="priceCell">4,80€</td>
// 	</tr>
// ---------------------------------------------------------
function formPreviewRow(dish)
{
	// Zelle für Logo
	var logoCell = document.createElement("td");
	logoCell.className = "logoCell";
	var logo = document.createElement("img");
	logo.setAttribute("src", restaurant.icon);
	logo.className = "logo";
	logoCell.appendChild(logo);

	// Zelle für Gericht
	var mainCell = document.createElement("td");

	// Haupttext
	var mainText = document.createElement("span");
	mainText.className = "dish";
	var descr = dish.descr;
	if(dish.side) descr += " + " + dish.side;
	mainText.innerText = descr;
	mainCell.appendChild(mainText);

	// Additional Description falls vorhanden
	if(dish.addDescr) {
		var br = document.createElement("br");
		mainCell.appendChild(br);
		var addSpan = document.createElement("span");
		addSpan.innerText = dish.addDescr;
		addSpan.className = "addition";
		mainCell.appendChild(addSpan);
	}

	// Zelle für Preis
	var priceCell = document.createElement("td");
	if(dish.price) {
		priceCell.innerText = dish.price.replace(".", ",") + "€";
	}
	else priceCell.innerText = "";
	
	priceCell.className = "priceCell";

	// Zu Zeile zusammenfügen
	var row = document.createElement("tr");
	row.appendChild(logoCell);
	row.appendChild(mainCell);
	row.appendChild(priceCell);

	return row;
}

// ---------------------------------------------------------
// Preview Inhalte löschen
// ---------------------------------------------------------
function flushPreview() {
	var previewSection = document.getElementById("previewDiv");
	if (!previewSection) {
		console.log("HTML <div> PreviewDiv nicht gefunden!");
		return;
	}

	previewSection.innerHTML = "";
}

// ---------------------------------------------------------
// Speiseplan Upload AJAX
// ---------------------------------------------------------
function uploadMenu() {
	var pass = document.getElementById("pw").value;

	if(!pass) {
		alert ("Bitte Passwort eingeben");
		return;
	}
	
	var postArray = new Array();
	var menuItems = tryParse();
	
	// Formatiere Array für JSON übergabe
	for (const day of menuItems) {
		for(const mi of day) {
			var item = {
				password: pass,
				operation: "add",
				restaurant: mi.restaurant.name,
				day: mi.date.toISOString().slice(0,10),
				description: mi.descr,
				additional_description: mi.addDescr,
				side: mi.side,
				price: mi.price
			}
		}

		postArray.push(item);
	}

	var xhttp = new XMLHttpRequest();

	xhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		alert("Alles klar!");
		//alert("Alles klar! Server hat geantwortet: " + this.responseText);
	  }
	};

	var postData = JSON.stringify(postArray);

	xhttp.open("POST", "php/api.php", true);
	xhttp.setRequestHeader("Content-type", "application/json");
  	xhttp.send(postData);
}

// ---------------------------------------------------------
// Ändert die Farbe des Hintergrundmusters
// ---------------------------------------------------------
function toggleBackgroundColor() {
	console.log("Ay! You clicked me!");

	let style = window.getComputedStyle(document.body);
	if(style)
	{
		let current = style.getPropertyValue('background-image');
		if(current.includes("red")) {
			document.body.style.backgroundImage = "url('img/bg_blue.png')";
		}
		else {
			document.body.style.backgroundImage = "url('img/bg_red.png')";
		}
	}
	console.log(style.getPropertyValue('background-image'));
}

// ---------------------------------------------------------
// Kollisionsdetektion zwischen Chef-Bild und Speiseplan
// ---------------------------------------------------------
function testCollision () {
	var chefImage = document.getElementById("imageChef");
	var chefRect = chefImage.getBoundingClientRect();
	var divs = document.getElementsByClassName("day");
	var lastDiv = divs.item(divs.length-1);
	var lastDivBB = lastDiv.getBoundingClientRect();

	if(!((chefRect.x > lastDivBB.right) | (chefRect.y > lastDivBB.bottom)))
	{
		chefImage.style.opacity = 0.15;
	}
	else {
		chefImage.style.opacity = 1;
	}
}


