// JavaScript Document

var xmlHttp;
var vLastProduct;
var vLastMeal;
var vLastCuisine;

// ---------------------------------------------------------------------------
// Initiates the appropriate recipe search
// ---------------------------------------------------------------------------
function searchRecipe(pType)
{
   var vLangId = 1; //English
   var vCountryId = 1;
   var vOrderBy = 0;
   var vSort = 1;
   var vMsg = '';
   var page="recipes_results.php";
   
   switch (pType)
   {
      case 'product':
         var product_id = document.getElementById('selProducts').value;
         vLastProduct = product_id;
         if (product_id <= 0)
         {
            vMsg = '<br/><br/><table align="center" width="100%" cellpadding="10" cellspacing="0" border="0"><tr><td align="center" class="cRecipeText">';
            vMsg += 'Please select a Product to view associated recipes.</td></tr></table>';
            document.getElementById("dLog").innerHTML= vMsg;
            return;
         }
         var params = encodeURI("?product_id=" + product_id);
         break;

      case 'meal':
         var meal_id = document.getElementById('selMeals').value;
         vLastMeal = meal_id;
         if (meal_id <= 0)
         {
            vMsg = '<br/><br/><table align="center" width="100%" cellpadding="10" cellspacing="0" border="0"><tr><td align="center" class="cRecipeText">';
            vMsg += 'Please select a Meal to view associated recipes.</td></tr></table>';
            document.getElementById("dLog").innerHTML= vMsg;
            return;
         }
         var params = encodeURI("?meal_id=" + meal_id);
         break;

      case 'cuisine':
         var cuisine_id = document.getElementById('selCuisine').value;
         vLastCuisine = cuisine_id;
         if (cuisine_id <= 0)
         {
            vMsg = '<br/><br/><table align="center" width="100%" cellpadding="10" cellspacing="0" border="0"><tr><td align="center" class="cRecipeText">';
            vMsg += 'Please select a Cuisine to view associated recipes.</td></tr></table>';
            document.getElementById("dLog").innerHTML= vMsg;
            return;
         }
         var params = encodeURI("?cuisine_id=" + cuisine_id);
         break;

      default:
         break;      
   }
   params += encodeURI("&sid=" + Math.random());
   xmlHttp = GetXmlHttpObject()
   if (xmlHttp == null)
   {
      alert ("Browser does not support HTTP Request");
      return;
   } 

   var url = page + params;
   xmlHttp.onreadystatechange=stateChanged ;
   xmlHttp.open("GET", url, true);
   xmlHttp.send(null);
} 

function goBack(pProjectId,pMealId,pCuisineId){
   
   var page="recipes_results.php";
   
   if(pProjectId !=0){
      var params = encodeURI("?product_id=" + pProjectId);
   }else if(pMealId != 0){
      
      var params = encodeURI("?meal_id=" + pMealId);
   }else if(pCuisineId != 0){
      var params = encodeURI("?cuisine_id=" + pCuisineId);
   }
   params += encodeURI("&sid="+Math.random());
   xmlHttp=GetXmlHttpObject()
   if (xmlHttp==null){
      alert ("Browser does not support HTTP Request");
      return;
   } 

   var url = page + params;
   xmlHttp.onreadystatechange=stateChanged ;
   xmlHttp.open("GET",url,true);
   xmlHttp.send(null);
} 

function viewProduct(pProductId){
   var page="products_details.php";
   var params = encodeURI("?product_id=" + product_id);
      params += encodeURI("&sid="+Math.random());
   
   xmlHttp=GetXmlHttpObject()
   if (xmlHttp==null){
      alert ("Browser does not support HTTP Request");
      return;
   } 

   var url = page + params;
   xmlHttp.onreadystatechange=stateChanged ;
   xmlHttp.open("GET",url,true);
   xmlHttp.send(null);
} 

function viewRecipe(pProductId, pMealId, pCuisineId, pRecipeId){
   var page="recipes_details.php";
   var params = encodeURI("?product_id=" + pProductId + "&meal_id=" + pMealId + "&cuisine_id=" + pCuisineId + "&recipe_id=" + pRecipeId);
      params += encodeURI("&sid="+Math.random());
   
   xmlHttp=GetXmlHttpObject()
   if (xmlHttp==null){
      alert ("Browser does not support HTTP Request");
      return;
   } 

   var url = page + params;
   xmlHttp.onreadystatechange=stateChanged ;
   xmlHttp.open("GET",url,true);
   xmlHttp.send(null);

} 

function stateChanged(){ 
   if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
      document.getElementById("dLog").innerHTML=xmlHttp.responseText ;
   } 
} 

function GetXmlHttpObject(){ 
   var objXMLHttp=null;
   if(window.XMLHttpRequest){
      objXMLHttp=new XMLHttpRequest();
   }else if(window.ActiveXObject){
      objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
   return objXMLHttp
} 

function showDetails(pItem){
   switch(pItem){
   case 1:
      document.getElementById('dIngredients').style.visibility= 'visible';
      document.getElementById('dDirections').style.visibility= 'hidden';
      document.getElementById('dNutrients').style.visibility= 'hidden';
      
      document.getElementById('btnIngredients').style.backgroundColor= '#990000';
      document.getElementById('btnDirections').style.backgroundColor= '#000000';
      document.getElementById('btnNutrients').style.backgroundColor= '#000000';
      
   break;
   
   case 2:
      document.getElementById('dIngredients').style.visibility= 'hidden';
      document.getElementById('dDirections').style.visibility= 'visible';
      document.getElementById('dNutrients').style.visibility= 'hidden';
      
      document.getElementById('btnIngredients').style.backgroundColor= '#000000';
      document.getElementById('btnDirections').style.backgroundColor= '#990000';
      document.getElementById('btnNutrients').style.backgroundColor= '#000000';
   break;
   
   case 3:
      document.getElementById('dIngredients').style.visibility= 'hidden';
      document.getElementById('dDirections').style.visibility= 'hidden';
      document.getElementById('dNutrients').style.visibility= 'visible';
      
      document.getElementById('btnIngredients').style.backgroundColor= '#000000';
      document.getElementById('btnDirections').style.backgroundColor= '#000000';
      document.getElementById('btnNutrients').style.backgroundColor= '#990000';
   break;
   
   default:
   break;
   }
   document.getElementById('iView').setAttribute('src','images/universal/recipetab' + pItem + '.jpg');
}

function doPrint(){
   var vTitle = document.getElementById('dTitles').innerHTML;
   var vIngredients = document.getElementById('dIngredients').innerHTML;
   var vDirections = document.getElementById('dDirections').innerHTML;
   var vNurtients = document.getElementById('dNutrients').innerHTML;
   
   
   var vHTML = '<html><head><link href="css/styles.css" rel="stylesheet" type="text/css"></link></head><body>';
   vHTML +=' <table align="center" width="565"  border="0" cellspacing="0" cellpadding="0">\n';
   vHTML +='<tr><td width="800"><div align="right" class="style1"><img src="images/yves_logo_white.jpg" ></div></td></tr>';
   vHTML += '<tr><td>' + vTitle + '</td></tr>';
   vHTML += '<tr><td><hr/></td></tr>';
   vHTML += '<tr><td>' + vIngredients + '</td></tr>';
   vHTML += '<tr><td><hr/></td></tr>';
   vHTML += '<tr><td>' + vDirections + '</td></tr>';
   vHTML += '<tr><td><hr/></td></tr>';
   vHTML += '<tr><td>' + vNurtients + '</td></tr>';
   vHTML += '</table></body></html>';
   
   var printWin = window.open('','','');
   printWin.document.open();
   printWin.document.write(vHTML);
   printWin.document.close();
   printWin.print();
   
   
}

function targetOpener(mylink, closeme, closeonly)
{
   if (! (window.focus && window.opener)) return true;
   window.opener.focus();
   if (! closeonly) window.opener.location.href = mylink.href;
   if (closeme) window.close();
   return false;
}

   /*xmlHttp.open("POST",url ,true);
   xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.setRequestHeader("Content-length", params.length);
    xmlHttp.setRequestHeader("Connection", "close");
    xmlHttp.send(params);*/