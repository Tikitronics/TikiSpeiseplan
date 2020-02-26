var inputText = document.getElementById("menuInput");
inputText.focus();
inputText.select();
inputText.onfocus = selectAll;

function selectAll() {
	inputText.select();
}