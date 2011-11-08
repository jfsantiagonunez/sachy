function mainmenuEvent(event)
{
	alert(event);
}

function leitmotifUpdate(idLeitmotif)
{
	var theItem = document.getElementById(idLeitmotif);
	
	var theTextEdit = document.getElementById(idLeitmotif+"-Edit");
	
	if (theTextEdit.className == 'leitmotifTextEdit')
	{	
	    //localStorage.setItem('todoData', this.innerHTML);
		
		var loc = window.location;
		var theUrl = loc.href.substring(0, loc.href.lastIndexOf('/') + 1);
		//alert(theUrl);
		//alert ("idLeitmotif="+idLeitmotif+
		//	  		"idUp=0"+
		//	  		"&textLeitmotif="+escape(theText.innerHTML));
		
		// Call the Ajax...
		var request = $.ajax({
			  url: theUrl + "addeditleitmotif",
			  type: "POST",
			  data: "idLeitmotif="+idLeitmotif+
			  		"&idUp=0"+
			  		"&textLeitmotif="+escape(theTextEdit.innerHTML),
			  
			  success : function(msg) {
				  
				  if (idLeitmotif.substring(0,13) == 'leitmotifNew-' )
				  {   // new element. So del and append
					  var theList = theItem.parentNode;
					  theList.removeChild(theItem);
					  $(theList).append(msg);
				  }
				  else
				  {	  // edit element. extract child and replace the existing element.
					  $(theItem).replaceWith(msg);
				  }
		          //theList.appendChild(msg);
				  //$(edit).html( msg ); 
				  
			  },

			  fail : function() {
				  	alert( "Request failed" ); }
			  });
	}
	
}


function leitmotifDelete(idLeitmotif)
{
	var edit = document.getElementById(idLeitmotif);

	if ( (edit) &&  ( confirm('Confirm ?') ) )
	{

		var loc = window.location;
		var theUrl = loc.href.substring(0, loc.href.lastIndexOf('/') + 1);
		// Call the Ajax...
		var request = $.ajax({
			  url: theUrl + "deleteajax",
			  type: "POST",
			  data: "idLeitmotif="+idLeitmotif,
			  
			  success : function(msg) {
				  var theList = edit.parentNode;
		          theList.removeChild(edit);
				  },

			  fail : function() {
				  	alert( "Request failed" ); }
			  });
	}
	return false; // Return false to avoid the anchor href to be called
	
}



function leitmotifSelect(idLeitmotif)
{
	var divTitle = document.getElementById(idLeitmotif+'-Title');
	var divEdit = document.getElementById(idLeitmotif+'-Edit');
	if  (divEdit && divTitle) 
	{
		var divFrame = divTitle.parentNode.parentNode;
		divFrame.style.height = "100px";
		divTitle.style.display = "none";
		divEdit.style.display = "block";
		divEdit.focus();
	}
	return true; // Return false to avoid the anchor href to be called
}



function leitmotifGotoLevel(idLeitmotif,step)
{
	//level = -1 .Go up
	// level = 1 . Go down
	var loc = window.location;
	var theUrl = loc.href.substring(0, loc.href.lastIndexOf('/') + 1);
	//alert(theUrl);
	//	alert ("idLeitmotif="+idLeitmotif+
	//		  		"  level="+level);
		//	  		"&textLeitmotif="+escape(theText.innerHTML));
		
	// Call the Ajax...
	var request = $.ajax({
		  url: theUrl + "retrieve",
		  type: "POST",
		  data: "idLeitmotif="+idLeitmotif+
		  		"&step="+step,
		  
		  success : function(msg) {
			  // Replace the whole list
			  var theList = document.getElementById(idLeitmotif).parentNode;

			  $(theList	).replaceWith(msg);
			  
		  },

		  fail : function() {
			  	alert( "Request failed" ); }
	  });
	
	return false; //Return false to avoid the anchor href to be called
}