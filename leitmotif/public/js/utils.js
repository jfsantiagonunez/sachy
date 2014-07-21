


function deleteOldEditors() {
    var graphs_to_remove = new Array();
    var oldgraphtoberemoved=$('textarea');
    if (oldgraphtoberemoved.length > 0 )
    {
        $(oldgraphtoberemoved).each(function (key, item) {
            var thedives = $(item).closest('.editor');
        
            if (thedives.length>0 )
            {
                $(thedives).slideUp(); // Hide graphs
                graphs_to_remove.push($(thedives));
            }
        });
        if (graphs_to_remove.length > 0) { // Delete the graphs later
        	$(graphs_to_remove).each(function() {
        		$(this).data("remove", true); // Flag graph as "to be removed" so no updates are applied to it
        	});
            setTimeout(function() {
                $(graphs_to_remove).each(function() {
                    $(this).remove();
                });
            }, 500);
        }
    }
}




function showTooltip(x, y, contents) {
    $('<div id="flot_chart_tooltip">' + contents + '</div>').css( {
        top: y - 30,
        left: x + 5
    }).appendTo("body").fadeIn(200);
}

function clean_old_tables() {
    $(".container").children("table").filter(".used_table").remove();
    var tables_in_use = $(".container").children("table").filter(".new_table");
    tables_in_use.removeClass("new_table");
    tables_in_use.addClass("used_table");
}

function attach_changelist_tooltip(parent_object) {
    $(parent_object).each(function(test_result_key, test_result_value) {
        if (($(test_result_value).attr('changelist') === "undefined") || ($(test_result_value).attr('changelist') == "")) {
            return true; // This element does not contain any valid changelist so do not add tooltip to it
        }
        if ($(test_result_value).data('qtip')) { // Skip if it already has a tooltip
            return true;
        }
        $(test_result_value).qtip({
            content: {
                title: '<p align="center"><font size="3">Changes list for Job ' + $(test_result_value).attr('jobname') + '</font></p>',
                text: '<style> \
                            #loadImg{position:absolute;z-index:999;} \
                            #loadImg div{display:table-cell;width:600px;height:400px;background:#fff;text-align:center;vertical-align:middle;} \
                        </style> \
                        <div id="loadImg"><div><img src="images/loading_bear.gif" /></div></div> \
                        <iframe style="width: 600px; height: 400px" src="' + $(test_result_value).attr('changelist') + '" \
                        onload="$(this).prev().css({display: \'none\'});"></iframe>' // Use the changelist element for the content and "div" with gif displayed on top while the content is loaded
            },
            position: {
                my: 'top left',  // Position my tool tip top left corner...
                at: 'center center', // at the center right of...
                target: $(this) // my target
            },
              style: {
                    classes: 'qtip-rounded qtip-blue qtip-shadow',
                    tip: {
                    width: 30,
                    height: 25
                }
                },
              show: {
                  delay: 200,
                    solo: $('.qtips'), // Hide tooltips within the .qtips element when shown
                    effect: function(offset) {
                        $(this).show().css({ opacity: 0 }).animate({ opacity: 1 }, { duration: 500 });
                  }
              },
              hide: {
                  fixed: true,
                delay: 300,
                effect: function() { 
                    $(this).animate({ opacity: 0 }, { duration: 300 });
                  },
                  distance: 200 // Hide it after we move some pixels away from the origin
              },
              events: {
                  show: function(event, api) { // Recalculate tooltip position depending on cell position on the window to avoid off-window tooltips 
                    var ele = $(api.elements.target[0]);
                    var right_space = window.innerWidth - (Math.round($(ele).offset().left) + ($(ele).width()/2) + 600); // get the rounded offset of the element
                    var bottom_space = window.innerHeight - ((Math.round($(ele).offset().top) + ($(ele).height()/2) + 400 - $(window).scrollTop())); // get the rounded offset of the element
                    var side = 'left';
                    var vertical = 'top';
                    if (right_space < 0) {
                        side = 'right';
                    }
                    if (bottom_space < 0) {
                        vertical = 'bottom';
                    }
                    ele.qtip('option', 'position.my', vertical + ' ' + side); // Reposition tooltip inside window
                  }
              }
        });
    });
}

function attach_test_name_tooltip(object_id) {
    $(object_id).each(function(test_name_key, test_name_value) {
        if (($(test_name_value).attr('test-name') == null) || ($(test_name_value).attr('test-name') == "")) { // Skip empty tooltips
            return true;
        }
        if ($(test_name_value).data('qtip')) { // Skip if it already has a tooltip
            return true;
        }
        $(test_name_value).qtip({
            content: {
                text: '<font size="3">' + $(test_name_value).attr('test-name') + '</font>'
            },
            position: {
                my: 'bottom left',  // Position my tool tip bottom left part...
                at: 'top center', // at the top center of...
                target: $(test_name_value) // my target
            },
              style: {
                    classes: 'qtip-rounded qtip-green qtip-shadow',
                    tip: {
                    width: 25,
                    height: 7
                }
                },
              show: {
                  delay: 100,
                    solo: $('.qtips'), // Hide tooltips within the .qtips element when shown
                    effect: function(offset) {
                        $(this).show().css({ opacity: 0 }).animate({ opacity: 1 }, { duration: 500 });
                  }
              },
              hide: {
                  fixed: true,
                delay: 150,
                effect: function() { 
                    $(this).animate({ opacity: 0 }, { duration: 300 });
                  },
                  distance: 100 // Hide it after we move some pixels away from the origin
              }
        });
    });
}

function attach_test_results_tip(object_id) {
    $(object_id).each(function(key, value) {
        if (($(value).attr('result') == null) || ($(value).attr('total') == "")) { // Skip empty tooltips
            return true;
        }
        if ($(value).data('qtip')) { // Skip if it already has a tooltip
            return true;
        }
        $(value).qtip({
            content: {
                text: '<div align="center" style="vertical-align:top; font-size:large"><p style="display:inline; color:green">'+ $(value).attr('result') + '</p>/<p style="vertical-align:top; display:inline; color:red">' + $(value).attr('total') + '</p></div>'
            },
            position: {
                my: 'bottom center',  // Position my tool tip bottom left part...
                at: 'top center', // at the top center of...
                target: $(value) // my target
            },
              style: {
                    classes: 'qtip-rounded qtip-blue qtip-shadow',
                    tip: {
                    width: 9,
                    height: 11
                }
                },
              show: {
                  delay: 100,
                    solo: $('.qtips'), // Hide tooltips within the .qtips element when shown
                    effect: function(offset) {
                        $(this).show().css({ opacity: 0 }).animate({ opacity: 1 }, { duration: 500 });
                  }
              },
              hide: {
                  fixed: true,
                delay: 150,
                effect: function() { 
                    $(this).animate({ opacity: 0 }, { duration: 300 });
                  },
                  distance: 100 // Hide it after we move some pixels away from the origin
              }
        });
    });
}



function favoriteStarClick()
{
  $(".favoriteStar").click(function(e) {  
             
        var url = $(this).attr('href');
        var posting = $.post( url);
        var anchor = $(this);
          // Put the results in a div
        posting.done(function( data ) {
          //debugger
          var newurl="";
          if ( url.search("unmark") > 0 )
          {
            //It was marked as favorite, now it becomes non favorite
            newurl=url.replace("unmark","mark");
            anchor.html( "&#9734" );  
            
          }
          else
          {
            newurl=url.replace("mark","unmark");
            anchor.html( "&#9733" );
          }
          anchor.attr('href',newurl);
         
        });
          
        return false;
        
      });
}

  //$(".editajaxaction").focus(function(){
    
  //  $(this).val("");
  //});

function editajaxactionBlur()
{
  $(".editajaxaction").blur(function(){
    
    var name= $(this).attr("name");
    var form = $(this).parent(),
      newitemname = form.find( "input[name='"+name+"']" ).val(),
      url = form.attr( "action" );
      
    var $contentDiv = $(this).closest("div");

    // Send the data using post
    var data = jQuery(form).serialize();
   
    var posting = $.post( url, data );
   
    // Put the results in a div
    posting.done(function( data ) {
  
      $contentDiv.empty().append( data );
    });
   });
}

function editajaxactionEnter()
{

   $('.editajaxaction').keypress(function(e) {
          if(e.which == 13) {
              jQuery(this).blur();
              return false;
              //jQuery('#submit').focus().click();
          }
      });
  
}

function deleteajaxactionOldClick()
{
  $(".deleteajaxaction").click(function(e){
    var contentDiv = $(this).closest("div");
  
    if ( confirm("Confirm Deletion?" ) )
    {      
        contentDiv.load($(this).attr("href"));    
    }
    return false;
  });
}

function onlydeleteajaxactionClick()
{
    //This function does not update the parent with the content. 
	// It just execute the call and then remove the existing content.   
  $(".deleteajaxaction").click(function(e){
    var contentDiv = $(this).closest("div");
    
	  //alert(contentDiv.parent().parent().html());
    if ( confirm("Confirm Deletion?" ) )
    {      
    	var jqxhr = $.ajax( $(this).attr("href") )
    	  .done(function() {
    		  contentDiv.parent().parent().empty();
    	  })
    	  .fail(function() {
    	    alert( "Error Deleting" );
    	  });
    }
    return false;
  });
}

function addajaxactionClick()
{
$(".addajaxaction").click(function(){
    alert("Adding");
    return false;    
});
}

function newajaxactionFocus()
{
$(".newajaxaction").focus(function(){
  //debugger
  $(this).val("");
});
}

function optionselectChange()
{
$(".optionselect").change(function(){
  var id= $(this).attr("id");
  var value =$(this).find('option:selected').val();
  //alert(id+"-"+value);
  var form = $(this).parent(),
    url = form.attr( "action" );


  var $contentDiv = $(this).closest("div");
    // Send the data using post
    var data = jQuery(form).serialize()
   
    var posting = $.post( url, data );
   
    // Put the results in a div
    posting.done(function( data ) {
  
      $contentDiv.empty().append( data );
    });  
});
}

function newajaxactionBlur()
{
$(".newajaxaction").blur(function(){
  
  var name= $(this).attr("name");
  var form = $(this).parent(),
    newitemname = form.find( "input[name='"+name+"']" ).val(),
    url = form.attr( "action" );
    
  var $contentDiv = $(this).closest("div");

  // Send the data using post
  var data = jQuery(form).serialize();
 
  var posting = $.post( url, data );
 
  // Put the results in a div
  posting.done(function( data ) {

    $contentDiv.empty().append( data );
  });
  
     
  //alert(form.attr("action"));
  //return false; 
});
}

function newajaxactionEnter()
{
$('.newajaxaction').keypress(function(e) {
    if(e.which == 13) {
        jQuery(this).blur();
        return false;
        //jQuery('#submit').focus().click();
    }
});
}


