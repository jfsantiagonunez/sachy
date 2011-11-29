
function Leitmotif()
{
    var self = this;
    
    // Create
}


Leitmotif.prototype {
	
    get id()
    {
        if (!("_id" in this))
            this._id = 0;
        return this._id;
    },

    set id(x)
    {
        this._id = x;
    },
	
	
	Update: function (idLeitmotif)
	{
		var theItem = document.getElementById(idLeitmotif);
		
		var theTextEdit = document.getElementById(idLeitmotif+"-Edit");
		
		if (theTextEdit.className == 'leitmotifTextEdit')
		{	
			if (theTextEdit.innerHTML.length == 0 )
			{
				var divFrame = theTextEdit.parentNode.parentNode;
				var divTitle = document.getElementById(idLeitmotif+'-Title');				
				divFrame.style.height = "25px";
				divTitle.style.display = "block";
				theTextEdit.style.display = "none";
				return false;
			}
		    //localStorage.setItem('todoData', this.innerHTML);
			
			var loc = window.location;
			var theUrl = loc.href.substring(0, loc.href.lastIndexOf('/') + 1);
			//alert(theUrl);
			//alert ("idLeitmotif="+idLeitmotif+
			//	  		"idUp=0"+
			//	  		"&textLeitmotif="+escape(theText.innerHTML));
			
			// Call the Ajax...
			var theList = theItem.parentNode;
			var theParent = theList.id;
			
			var request = $.ajax({
				  url: theUrl + "addeditleitmotif",
				  type: "POST",
				  data: "idLeitmotif="+idLeitmotif+
				  		"&idUp="+theParent+
				  		"&textLeitmotif="+escape(theTextEdit.innerHTML),
				  
				  success : function(msg) {
					  
					  if (idLeitmotif.substring(0,13) == 'leitmotifNew-' )
					  {   // new element. So del and append					  
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
	
	
    Delete : function (idLeitmotif)
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
	
	
	
	Select : function (idLeitmotif)
	{
		var divTitle = document.getElementById(idLeitmotif+'-Title');
		var divEdit = document.getElementById(idLeitmotif+'-Edit');
		if  (divEdit && divTitle) 
		{
			var divFrame = divTitle.parentNode.parentNode;
			divFrame.style.height = "100px";
			divEdit.style.height = "100px";
			divTitle.style.display = "none";
			divEdit.style.display = "block";
			divEdit.focus();
		}
		return true; // Return false to avoid the anchor href to be called
	}
	
	
	
	GotoLevel : function (idLeitmotif,step)
	{
		//level = -1 .Go up
		// level = 1 . Go down
		var loc = window.location;
		var theUrl = loc.href.substring(0, loc.href.lastIndexOf('/') + 1);
		var theList = document.getElementById(idLeitmotif).parentNode;
		//alert(theUrl);
		//	alert ("idLeitmotif="+idLeitmotif+
		//		  		"  level="+level);
			//	  		"&textLeitmotif="+escape(theText.innerHTML));
			
		// Call the Ajax...
		var request = $.ajax({
			  url: theUrl + "retrieve",
			  type: "POST",
			  data: "idLeitmotif="+idLeitmotif+
			  		"&idUp="+theList.id+
			  		"&step="+step,
			  
			  success : function(msg) {
				  // Replace the whole list
				  
				  
				  $(theList	).replaceWith(msg);
				  
			  },
	
			  fail : function() {
				  	alert( "Request failed" ); }
		  });
		
		return false; //Return false to avoid the anchor href to be called
	}

}

var GLevel = 0;
var GUserId = 0;

function FirstLoadLeitmotifs()
{
	// In the first load (after login) we expect:
	//   <div class='leitmotifList' id='userid'></div>
	var divList = document.getElementsByClass('leitmotifList')[0];
	
	if (divList == null)
	{
		//
		alert('You need to login');
		return;
	}
	
	GUserId = divUser.id;
	
	LoadLeidmotifs(GLevel,GUserId);
}


addEventListener('load', FirstLoadLeitmotifs, false);
