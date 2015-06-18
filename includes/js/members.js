$(document).ready(function() {
    if (document.getElementById('0')){ //If the admin checkboxes exist, sort from column 1
    	var table = $("#members").tablesorter({
	        cssHeader: "unsorted",
	        cssAsc: "ascending",
	        cssDesc: "descending",
	        sortList: [[1,0]]
   		});
    } else {
    	var table = $("#members").tablesorter({
	        cssHeader: "unsorted",
	        cssAsc: "ascending",
	        cssDesc: "descending",
	        sortList: [[0,0]]
   		});
    }
});
