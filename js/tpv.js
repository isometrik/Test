{
	Array.prototype.forEach.call(document.getElementsByClassName("page_select"), function (select) {
		select.onchange = function () {
			if (!this.selectedIndex) {
				return; // 1. Option gewählt => nix machen
			}
			var input = this.previousElementSibling; // Caveat: <input> must come immediately before <select>
			if (input.value) {
				input.value += ","; // falls schon was im <input> steht, dann Komma anhängen
			}
			input.value += this.value; // gewählten Wert anhängen
		}
	});
	function myFunction(info) {
		var info = info;
		return confirm(info);
	}
	function tpvToggleLine(key, line0, line1) {
			var line0 = line0 + "[" + key + "]";
			var line1 = line1 + "[" + key + "]";
            if(document.getElementById(line0).style.display == 'none') {
                document.getElementById(line0).style.display = 'table-row';
            } else {
                document.getElementById(line0).style.display = 'none';
            }
            if(document.getElementById(line1).style.display == 'none') {
                document.getElementById(line1).style.display = 'table-row';
            } else {
                document.getElementById(line1).style.display = 'none';
            }
	}
}

