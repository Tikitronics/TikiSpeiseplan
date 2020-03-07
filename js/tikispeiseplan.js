// Global Variables
var inputText = document.getElementById("menuInput");
var weekDay = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag"];
var restaurants = [
	{ name: "Kunzmann", regex: /^kunzmann$/i, image: "../img/mercedes-logo-semi.svg", icon: "../img/mercedes-logo.svg" },
	{ name: "Team Food", regex: /^team\s?food$/i, image: "../img/teamfood-semi.png", icon: "../img/teamfood_icon.png" },
];

// Fokussiert und Selektiert den Eingabebereich
inputText.onfocus = function () {
	inputText.select();
}

inputText.focus();

// Event löst bei jedem eingegebenen Zeichen aus
inputText.oninput = tryParse;


// ---------------------------------------------------------
// Funktion zum Parsen der Eingabe
// ---------------------------------------------------------
function tryParse() {
	var text = inputText.value;
	var lines = text.split(/\r?\n/);
	var menuItems = new Array();

	// Vorschaubereich zurücksetzen
	flushPreview();

	// Prüfe ob Testfeld leer
	if (lines[0] == null) return;

	var restaurant = checkRestaurant(lines[0]);
	if (restaurant == null) return;

	var menuSegments = extractMenuSegments(lines);
	if (menuSegments == null) return;

	console.log("Yo!");

	for(var seg of menuSegments) {
		var newItems = parseMenuSegment(seg, restaurant);
		menuItems.push(newItems);
	}
	console.log(menuItems);

	renderPreview(menuItems);
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

	// -- Finde Zeilen, die Wochentage enthalten
	for (i = 0; i < lineArray.length; i++) {
		for (var d of weekDay) {
			var reg = new RegExp(d, "i");
			if (reg.test(lineArray[i])) weekDayLines.push(i);
		}
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
	var dateRegEx = /\d{1,2}.\d{1,2}.\d{2,4}/;
	var day;
	var date;
	var i;

	// Guard Clauses
	if (!menuSegment) {
		console.log("Leeres menuSegment übergeben.");
		return;
	}
	if (menuSegment.length < 2) {
		console.log("menuSegment mit nur einer Zeile übergeben.");
		return;
	}

	segmentContent = menuSegment.slice(1);

	// Datum extrahieren
	for (i = 0; i < weekDay.length; i++) {
		date = dateRegEx.exec(menuSegment[0]);
		var reg = new RegExp(weekDay[i], "i");
		if (reg.test(menuSegment[0])) {
			day = weekDay[i];
		}
	}

	// Inhalt extrahieren
	// Format: Beschreibung; Zusätzliche Beschreibung; Beilage; Typ; Preis
	for (var line of segmentContent) {
		var lineContent = line.split(';');
		var item = new MenuItem(restaurant, day, date, lineContent[0], lineContent[1], lineContent[2], lineContent[3], lineContent[4],)
		menuItems.push(item);
	}

	return menuItems;
}

// ---------------------------------------------------------
// Konstruktor MenuItem
// ---------------------------------------------------------
function MenuItem(restaurant, weekDay, date, descr, addDescr, side, type, price) {
	this.restaurant = restaurant;
	this.weekDay = weekDay;
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
		var dateText = menuDay[0].weekDay;
		if(menuDay[0].date) dateText += ", " + menuDay[0].date;
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
	// Zelle für Gericht
	var mainCell = document.createElement("td");
	var mainText = document.createElement("span");
	mainText.className = "dish";
	var descr = dish.descr;
	if(dish.side) descr += " + " + dish.side;
	mainText.innerText = descr;
	mainCell.appendChild(mainText);

	// Zelle für Preis
	var priceCell = document.createElement("td");
	priceCell.innerText = dish.price || "";
	priceCell.className = "priceCell";

	// Zu Zeile zusammenfügen
	var row = document.createElement("tr");
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
