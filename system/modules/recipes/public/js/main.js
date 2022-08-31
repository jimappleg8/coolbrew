function init()
{
	//Load the Data Collection
	//loadXML();
	//Create Dropdown HTML
	if (window.ActiveXObject){
		//loadIESelect();
	}else if (document.implementation && document.implementation.createDocument){// code for Mozilla, etc.
		//loadIESelect();
	}
}

function addLog(){
	
	var vValid = validateActivity();
	
	if(vValid == true){
		//alert(document.getElementById('Exercise').selectedIndex)
		var vActivitySel = document.getElementById('Exercise').selectedIndex;
		var vWeight = document.getElementById('weight').value;
		var vMins = document.getElementById('mins').value;
		var vCalories = document.getElementById('exercisepoints').value;
		var vActivityObj = colActivities.Item(vActivitySel);
		vActivityObj.minutes = vMins;
		vActivityObj.burn = vCalories;
		var objIdx = colMyActivity.Add(vActivityObj.value,vActivityObj);
		drawLog();
	}else{
		return;
	}
}

function deleteLog(pId){
	colMyActivity.Remove(pId);
	drawLog()
}

function drawLog(){
	var vCalories = 0;
	var vTblStr = '<table border="0" width="100%" cellspacing="0" cellpadding="3">\n';
		vTblStr += '<tr><td class="cLog">#</td><td class="cLog">Activity</td><td class="cLog">Duration</td><td td class="cLog">Calorie Burn</td><td class="cLog">&nbsp;</td></tr>';
		//alert(colMyActivity.Count());
		for(var i=0; i < colMyActivity.Count(); i++){
			var activity = colMyActivity.Item(i).option;
			var minutes = colMyActivity.Item(i).minutes;
			var burn = colMyActivity.Item(i).burn;
			//alert(activity + ' - ' + minutes + ' - ' + burn);
			vTblStr += '<tr><td class="cLog">' + (i+1) + '</td><td class="cLog">' + activity + '</td><td class="cLog">' + minutes + '</td><td class="cLog">' + burn + '</td><td class="cLog"><input style="color:ffffff;background-color:000000;" type="button" value="Delete" onclick="deleteLog(' + i + ')"></INPUT></td></tr>';
			vCalories += Number(burn);
		}
	
		vTblStr += '<tr><td></td><td></td><td></td><td class="cText">Total Calorie Burn:</td><td class="cText">' + vCalories + '</td></tr>';
		vTblStr += '</table>';
	
	document.getElementById('dLog').innerHTML = vTblStr;
	
	if(colMyActivity.Count() != 0){
		var vPrint = '<INPUT style="color:ffffff;background-color:000000;height:20px;font-family:arial;font-size:12px;" name="print" type="button" onclick="printLog();" value="Print Log">';
		document.getElementById('dPrint').innerHTML = vPrint;
	}
}

function printLog(){
	var vCalories = 0;
	var vTblStr = '<table border="0" width="100%" cellspacing="0" cellpadding="3">\n';
		vTblStr += '<tr><td class="cLog">#</td><td class="cLog">Activity</td><td class="cLog">Duration</td><td td class="cLog">Calorie Burn</td><td class="cLog">&nbsp;</td></tr>';
		//alert(colMyActivity.Count());
		for(var i=0; i < colMyActivity.Count(); i++){
			var activity = colMyActivity.Item(i).option;
			var minutes = colMyActivity.Item(i).minutes;
			var burn = colMyActivity.Item(i).burn;
			//alert(activity + ' - ' + minutes + ' - ' + burn);
			vTblStr += '<tr><td class="cLog">' + (i+1) + '</td><td class="cLog">' + activity + '</td><td class="cLog">' + minutes + '</td><td class="cLog">' + burn + '</td><td class="cLog"></td></tr>';
			vCalories += Number(burn);
		}
	
		vTblStr += '<tr><td></td><td></td><td></td><td class="cText">Total Calorie Burn:</td><td class="cText">' + vCalories + '</td></tr>';
		vTblStr += '</table>';
	
	var printWin = window.open('','','');
	printWin.document.open();
	printWin.document.write(vTblStr);
	printWin.document.close();
	printWin.print();
	
}
