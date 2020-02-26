// Global Variables
var inputText = document.getElementById("menuInput");
var chosenRestaurant;
let weekDay = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag","Samstag", "Sonntag"];
let restaurants = [
  {name:"Kunzmann", regex: /^kunzmann$/i},
  {name:"Team Food", regex: /^team\s?food$/i}
];


// Fokussiert und Selektiert den Eingabebereich
inputText.onfocus = function() {
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
	
	
	// Prüfe ob Testfeld leer
	if(lines[0] == null) return;
	
	var restaurant = checkRestaurant(lines[0]);
	if(restaurant == null) return;
	
	var menuSegments = extractMenuSegments(lines);
	if(menuSegments == null) return;
	
	
}


// ---------------------------------------------------------
// Prüfe ob gültiges Restaurant angegeben
// ---------------------------------------------------------
function checkRestaurant(line) {
	var rest;
	
	for (var r of restaurants) {
		if(r.regex.test(line)) {
			rest = r;
		}
	}
	
	if(!rest) return null;
	
	console.log('Sie haben das Restaurant ' + rest.name + ' gewählt!');
	
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
	for(i = 0; i < lineArray.length; i++) {
		for(var d of weekDay) {
			var reg = new RegExp(d, "i");
			if(reg.test(lineArray[i])) weekDayLines.push(i);
		}
	}
	if (weekDayLines.length == 0) return null;
	//console.log(weekDayLines);
	
	// -- Erzeuge Array aus Speiseplan-Segmenten (Tage)
	var resultArray = [];
	for(i = 0; i < (weekDayLines.length-1); i++) {
		resultArray.push(lineArray.slice(weekDayLines[i], weekDayLines[i+1]));
	}
	resultArray.push(lineArray.slice(weekDayLines[i]));
	console.log(resultArray);
	
	return resultArray;
}

// ---------------------------------------------------------
// Extrahiert die Daten aus einem MenuSegment
// (ein Kalendertag / evtl. mehrere Gerichte)
// ---------------------------------------------------------
function parseMenuSegment(menuSegment) {
	var dateRegEx = /\d{1,2}.\d{1,2}.\d{2,4}/;
	var day;
	var i;
	
	// Guard Clauses
	if(!menuSegment) {
		console.log("Leeres menuSegment übergeben.");
		return;
	}
	if(menuSegment.length < 2) {
		console.log("menuSegment mit nur einer Zeile übergeben.");
		return;
	}
	
	// Datum extrahieren
	for(i = 0; i < weekDay.length ; i++) {
		var reg = new RegExp(weekDay[i], "i");
		if(reg.test(menuSegment[0])) {
			day = weekDay[i];
		}
		
	for(var i = 1; i < menuSegment.length; i++) {
		
	}
	
	
}

// ---------------------------------------------------------
// Konstruktor MenuItem
// ---------------------------------------------------------
function MenuItem(restaurant, date, descr, addDescr, price) {
  this.restaurant = restaurant;
  this.date = date;
  this.descr = descr;
  this.addDescr = addDescr;
  this.price = price;
}