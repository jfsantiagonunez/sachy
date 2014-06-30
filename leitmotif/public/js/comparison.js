
function clean_jobs_list() { // Get ID of Jobs present in jobs bucket and delete them from the selection list in the tree
    $("#comparison-bucket").find('.diff-job').each(function () {
        $('#jobs_selection_list').find('.diff-job').filter('#' + $(this).attr('id')).closest('tr').remove();
    });
}

function add_job_to_comparison_bucket_event_handler() { // Get ID of Job with JQuery and add it to the jobs bucket
    var job = $(this).find('.diff-job');
    var new_job_comparison = $('<td></td>');
    var new_job_comparison_icon = $('<tr></tr>').append(job.clone());
    new_job_comparison.append(new_job_comparison_icon);
    var new_job_comparison_iname = $('<tr align="center"></tr>').append($(job).attr('jobname'));
    new_job_comparison.append(new_job_comparison_iname);
    $("#comparison-bucket-jobs").find('tr').first().append(new_job_comparison); // Add
    $(this).closest('ul').slideUp();
    $(this).closest('tr').remove(); // Automatically remove row from possible selection list
    $("#comparison-bucket-jobs").slideDown();
    update_jobs_comparison_bucket_buttons();
}

function update_jobs_comparison_bucket_buttons() {
    var button = $("#comparison-bucket-buttons").children().first();
    if ($("#comparison-bucket-jobs").find('#selected_jobs_row').children().length >= 2) { // Enable comparison button
        if (button.attr('id') == 'comparison-disabled'){
            $("#comparison-bucket-buttons").children().remove();
            button = null;
        }
        if (button == null || button.length == 0) {
            $("#comparison-bucket-buttons").append('<button id="comparison-enabled">Compare results from these Jobs</button>');
            button = $("#comparison-bucket-buttons").children().first();
            button.on('click', compare_selected_jobs);
        }
    } else { // Disable comparison button
        if (button.attr('id') == 'comparison-enabled'){
            $("#comparison-bucket-buttons").children().remove();
            button = null;
        }
        if (button == null || button.length == 0) {
            $("#comparison-bucket-buttons").append('<p id="comparison-disabled">Add more Jobs to compare</p>');
        }
    }
}

function compare_selected_jobs() {
    var jobs = new Array();
    var jobs_list = "";
    var job;
    $("#comparison-bucket-jobs").find('.diff-job').each(function (key, value){ // Get list of comparison jobs
        job = $(value).attr('id');
        jobs.push(job);
        jobs_list += "&jobid" + key + "=" + job;
    });
    
    // Show loading animation
    $('div').filter('#comparison-bucket').after('<div id="waiting-bear"><img style="display: block; margin: auto" src="images/loading_bear.gif"></div>');
    
    // Request comparison to PHP backend
    var url_path = window.location.pathname;
    if (url_path.indexOf('index.php') == -1){
        url_path += 'index.php';
    }
    $("#comparison-bucket").after('<div id="comparison-table-container"></div>'); // Create comparison talbe section and fillit up with the results from PHP call
    $("#comparison-table-container").load(url_path +'/Comparison/comparejobs?total=' + jobs.length + jobs_list, function(responseTxt,statusTxt,xhr){
        if (statusTxt=="success") { // Remove accordeons and loading content when comparison is ready
            $('div').filter('.ui-accordion').remove();
            $("#comparison-bucket").children().remove(); // Remove comparison bucket items
        }
        if (statusTxt=="error") {
            alert("Error comparing test results: "+xhr.status+": "+xhr.statusText);
        }
        $('div').filter('#waiting-bear').remove();
    });
}


/* ----- Functions related to comparison table ----- */

function setup_clickable_cells() {
    $("tr.test-result-on-target").on('click', function() { // Make result cells clickable as link to test results
        var url = $(this).attr("href");
        if (!(typeof url === "undefined")) {
            var win=window.open(url, '_blank');
            win.focus();
        }
    });
    $("td.test_result_name").on('click', function() { // Make result cells clickable as selection highlight
        $(this).toggleClass("highlighted-cell");
    });
}
function resize_table() {
    var table = $.fn.dataTable.fnTables(true);
    if ( table.length > 0 ) {
        var tableHeight = $(window).height() - 260;
        $(table).dataTable().fnSettings().oScroll.sY = tableHeight;
        $(table).dataTable().fnAdjustColumnSizing();
    }
}

function color_test_results() {
    /*
     * Color test rows according to following criteria:
     * - Green  : All test passed in all jobs
     * - Yellow : Some tests failed but the results are the same on all executions (even if some results are missing)
     * - Orange : The number of total tests is different (test added/removed)
     * - Red    : Results are different among jobs (some fail on different jobs) or there is an error in any of the tests
     */
    $($($.fn.dataTable.fnTables(true)).dataTable().fnGetNodes()).filter("tr.test_result").each(function (test_row_key, test_row_value){ // Get all the rows with test results
        // In JQuery variables are stored in the object
        $(test_row_value).data('passed','');
        $(test_row_value).data('total','');
        $(test_row_value).data('test_status_diff','green'); // Test is green by default
        $(test_row_value).find(".test-results-on-job").each(function (test_results_key, test_results_value){ // Get all the test results for each job for current test
            $(test_results_value).find(".test-result-on-target").each(function (test_target_result_key, test_target_result_value){ // Get all the test results on each target hardware for current test on current job
                var local_passed = $(test_target_result_value).find(".passed-key").attr("passed-value");
                var local_total = $(test_target_result_value).find(".total-key").attr("total-value");
                var local_errors = $(test_target_result_value).find(".errors-key").attr("errors-value");
                // Retrieve out of context variables
                var passed = $(test_row_value).data('passed');
                var total = $(test_row_value).data('total');
                var test_status_diff = $(test_row_value).data('test_status_diff');
                // Calculate cell and row colors
                if (local_passed != local_total) { // Update status of passed VS total on the cell and row status
                    $(test_target_result_value).find(".passed-key").children().filter(".value-title").addClass("strong-red");
                    if ((test_status_diff != "red") && (test_status_diff != "orange")) {
                        test_status_diff = "yellow";
                    }
                }
                if (local_errors != 0) { // Update error status on the cell and row
                    test_status_diff = "red";
                    $(test_target_result_value).find(".errors-key").children().filter(".value-title").addClass("strong-red");
                }
                if (passed == "") { // Update row status based on passed tests differences among executions 
                    passed = local_passed;
                } else if ((test_status_diff != "red") && (passed != local_passed)) { // It wasn't red already and the results vary between executions
                    if ((local_passed == local_total) && (total != local_total)) { // Tests passed but there were some added/removed between executions
                        test_status_diff = "orange";
                    } else {
                        test_status_diff = "red"; // Different number of tests failed
                    }
                }
                if (total == "") { // Update row status based on total tests differences among executions 
                    total = local_total;
                    $(test_target_result_value).find(".total-key").children().filter(".value-title").removeClass("strong-red");
                } else if (total != local_total) {
                    $(test_target_result_value).find(".total-key").children().filter(".value-title").addClass("strong-red");
                    if (test_status_diff != "red") {
                        test_status_diff = "orange";
                    }
                } else { // Remove status from previous comparisons (e.g. after filtering)
                    $(test_target_result_value).find(".total-key").children().filter(".value-title").removeClass("strong-red");
                }
                // Save variables for external block context
                $(test_row_value).data('passed', passed);
                $(test_row_value).data('total', total);
                $(test_row_value).data('test_status_diff', test_status_diff);
            });
        });
        
        // Remove status from previous comparisons (e.g. after filtering)
        $(test_row_value).removeClass('green');
        $(test_row_value).removeClass('yellow');
        $(test_row_value).removeClass('orange');
        $(test_row_value).removeClass('red');
        $(test_row_value).addClass($(test_row_value).data('test_status_diff'));
    });
    // Clear boring results after coloring if button is activated
    var visible_boring_rows = $("#comparison-table_wrapper").find(".green").length + $("#comparison-table_wrapper").find(".yellow").length;
    if ($("button.clean-boring").data('enabled') && (visible_boring_rows > 0)) {
        updateComparisonTableFiltering();
    }
}

function hide_empty_rows() {
    var table = $($.fn.dataTable.fnTables(true)).dataTable();
    var previously_visible_columns = table.data('visible_columns');
    var columns = table.fnSettings().aoColumns;
    var visible_columns = 0;
    for (var i = 0; i < columns.length; i++) {
        if (columns[i].bVisible) {
            visible_columns++;
        }
    }
    if (previously_visible_columns != visible_columns) { // Remove rows with no results after a hidding a column by requesting a filter update
        table.data('visible_columns', visible_columns); // Update counter of visible columns
        updateComparisonTableFiltering();
    }
}

function updateComparisonTableFiltering() {
    var table = $('#comparison-table_wrapper');
    var last_visible_result = '';
    // Keep redrawing/filtering until the last visible row is the same after filtering  
    while ((typeof table.data('last_filtered') === "undefined") || (table.data('last_filtered') != last_visible_result )) {
        table.data('last_filtered', table.find('tr.test_result').last().attr('id'));
        $($.fn.dataTable.fnTables(true)).dataTable().fnDraw();
        last_visible_result = table.find('tr.test_result').last().attr('id');
        if (table.find('tr.test_result').length == 0) {
            return; // Stop updating when all the results are wiped out
        }
    }
}

function hide_boring_rows( oSettings, aData, iDataIndex ) { // Mark rows with green or yellow as hidden
    var row = oSettings.aoData[iDataIndex].nTr;
    if ($(row).find(".test-results-on-job").length == 0) { // Hide rows that don't contain any result
        return false;
    }
    if ($("button.clean-boring").data('enabled')) {
        var greens = $(row).filter('.green').length;
        var yellows = $(row).filter('.yellow').length;
        if (greens+yellows > 0) {
            return false;
        } else {
            return true;
        }
    } else { // No filtering
        return true; 
    }
}


/* ----- Functions related to tooltips ----- */

function attach_column_headers_tooltip(object_id) {
    $(object_id).each(function(job_header_key, job_header_value) {
        if ($(job_header_value).data('qtip')) { // Skip if it already has a tooltip
            return true;
        }
        $(job_header_value).qtip({
            content: {
                text: '<font size="3">' + $(job_header_value).attr('branch-name') + '</font>'
            },
            position: {
                my: 'bottom center',  // Position my tool tip bottom center part...
                at: 'top center', // at the top center of...
                target: $(job_header_value) // my target
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

function attach_legend_tooltip(object_id) {
    /*
     * Color test rows according to following criteria:
     * - Green  : All test passed in all jobs
     * - Yellow : Some tests failed but the results are the same on all executions (even if some results are missing)
     * - Orange : The number of total tests is different (test added/removed)
     * - Red    : Results are different among jobs (some fail on different jobs) or there is an error in any of the tests
     */
    if ($(object_id).data('qtip')) { // Skip if it already has a tooltip
        return true;
    }
    $(object_id).qtip({
        content: {
            text: '<font size="4"><table><tbody>' +
                        '<tr align="center" height="50" style="background-color:#FFBCBC;"><td class="legend"> The number of failed tests is different or there is an error in at least one of the tests </td></tr>' +
                        '<tr align="center" height="50" style="background-color:#FFCDA1;"><td class="legend"> The number of total tests is different (tests added/removed) </td></tr>' + 
                        '<tr align="center" height="50" style="background-color:#F1FFBB;"><td class="legend"> Some tests failed but the results are the same on all executions (some results may be missing) </td></tr>' + 
                        '<tr align="center" height="50" style="background-color:#BFFFBC;"><td class="legend"> All results have all tests passed </td></tr>' +
                    '</tbody></table></font>'
        },
        position: {
            my: 'bottom center',  // Position my tool tip bottom center part...
            at: 'top center', // at the center center of...
            target: $("div.legend-div") // my target
        },
          style: {
                classes: 'qtip-rounded qtip-blue qtip-shadow',
                tip: {
                width: 50,
                height: 15
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
          }
    });
}

