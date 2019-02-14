

/*
    function addNewLinkedTask(parent, team, parent_team){
    	//console.log('anlt in c.js');
        if(!parent || !team){ return false; }
        
        var leadTeam = $('#qaLeadTeamSelect');
        var lpTeam = $('#qaLinkedParentDiv').find('.linkableParentSelect');
        var qaTC = $('#qaTimeCtrl');
        leadTeam.val(team);
        updateSignature(team, leadTeam, parent_team);
        updateLinkableParents(team, $('#qaLinkedParentDiv'), parent);
        lpTeam.trigger('change');
        qaTC.trigger('change');
        
        if(!$('#colAdd').hasClass('in')){
            $('#colAddHeading').trigger('click');
        }

        $('html, body').animate({
            scrollTop: $('#ajax-content-load').offset().top
        }, 800);
    }


    function addNewTask(start){
        //console.log('called add new task from c.js');
        if(!start){return false;}
        var startTime = $(document).find('#qaStartTime');
        var endTime = $(document).find('#qaEndTime');
        var col_add = $(document).find('#colAdd');
        var col_add_head = $(document).find('#colAddHeading');

        startTime.data('DateTimePicker').date(moment(start));
        endTime.data('DateTimePicker').date(moment(start));
        $(document).find('#qaLeadTeamSelect').trigger('change');
        
        if(!col_add.hasClass('in')){
            col_add_head.trigger('click');
            //console.log('got to col_add had class collapse');
        }

        $('html, body').animate({
            scrollTop: $(document).find('#ajax-content-load').offset().top
        }, 800);        
    }
*/






/*
		$('#newTaskFromMenu').on('click', function(e){
    	
        e.preventDefault();
        e.stopPropagation();
    	
    	console.log('hi');
        $.get('/tasks/quickAdd/', function(data){
			bootbox.dialog({
				message:data,
				size:'large',
				title: 'Add New Task<span class="pull-right"><span class="btn btn-default">Add Task</span></span>'
			});
		});
		//return false;
	});
*/


    // Task Details -- NEW  
/*
    $('#ajax-content-load').on('click', '.task-panel-heading', function(e){
        var tid = $(this).attr('data-tid');
        var details = $('#viewTaskModal');
		details.find('.modal-body').load('/tasks/quickAdd/'+tid, function(data){
			details.modal('show');	
		});
		

	});
	
    $('#newTaskFromMenu').on('click', function(e){
    	
        //e.preventDefault();
        //e.stopPropagation();
    	
    	console.log('hi');
        var newtask = $('body').find('#newTaskModal');
        console.log(newtask.find('.modal-body'));
		newtask.find('.modal-body').load('/tasks/quickAdd/'), function(data){
			newtask.modal('show');
		}
		//return false;
	});

    
	$('#coViewDetailsBut').on('click', function(){
        var in_vl = $('#coViewDetails');
        var this_val = $(this).data('checked');
        var nval;
        var shtml = '<i class=\"fa fa-eye\"></i> Show Details';
        var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Details';                                   

        if(this_val == 0){
            nval = 1;
            $(this).data('checked', 1);
        }
        else{
            nval=0;
            $(this).data('checked', 0);
        }
        in_vl.val(nval);
        in_vl.trigger('change');
            
        if($(this).hasClass('details_hidden')){
            $('div.divTaskDetails').show();
            $(this).removeClass('details_hidden').addClass('details_shown');
            $(this).html(hhtml);    
        }
        else{
	        $('div.divTaskDetails').hide();
            $(this).removeClass('details_shown').addClass('details_hidden');
            $(this).html(shtml); 
        }
    });
                    

    $('#coViewThreadedBut').on('click', function(){
        var in_vl = $('#coViewThreaded');
        var this_val = $(this).data('checked');
        var nval;
        var hhtml = '<i class=\"fa fa-list-ol\"></i> Show Unthreaded';
        var shtml = '<i class=\"fa fa-indent\"></i> Show Threaded';                                   
        
        if(this_val == 0){
            nval = 1;
            $(this).data('checked', 1);
        }
        else{
            nval=0;
            $(this).data('checked', 0);
        }
        in_vl.val(nval);
        in_vl.trigger('change');

        if($(this).hasClass('showing_rundown')){
            $('div.isChild').hide();
            $(this).removeClass('showing_rundown').addClass('showing_threaded');
            $(this).html(hhtml);    
        }
        else{
            $('div.isChild').show();
            $(this).removeClass('showing_threaded').addClass('showing_rundown');
            $(this).html(shtml); 
        }
    });

    $('#coViewLinksBut').on('click', function(){
        var in_vl = $('#coViewLinks');
        var this_val = $(this).data('checked');
        var shtml = '<i class=\"fa fa-eye\"></i> Show Linkages';
        var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Linkages';   
        var nval;

        if(this_val == 0){
            nval = 1;
            $(this).data('checked', 1);
        }
        else{
            nval=0;
            $(this).data('checked', 0);
        }

        in_vl.val(nval);
        in_vl.trigger('change');
        
        if($(this).hasClass('links_hidden')){
            $('div.taskLinkages').show();
            $(this).removeClass('links_hidden').addClass('links_shown');
            $(this).html(hhtml);    
        }
        else{
            $('div.taskLinkages').hide();
            $(this).removeClass('links_shown').addClass('links_hidden');
            $(this).html(shtml); 
        }
    });
    */

    /*
	$('#taskListWrap').on('click', 'div.task-panel-heading button', function(e){
        //e.stopPropagation();
    });*/
    

    /*        
    $('#qaReqAllBut').on('click', function(){
        if($(this).prop('disabled') == false){
            if($(this).text() == 'Request ALL'){
                $(this).text('Request NONE');
                $('#qaPushAllBut').text('Push ALL');
                $(this).parents('div.teams-panel').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 3).removeClass('btn-default btn-success btn-ttrid0 btn-ttrid2').addClass('btn-danger');
                });
            }
            else {
                $(this).text('Request ALL');
                $(this).parents('div.teams-panel').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 0).removeClass('btn-danger btn-success btn-default btn-ttrid2').addClass('btn-ttrid0');
                });
            }
        }
    });
 
    // Disables PushALL and RequestAll buttons when no lead team is selected
    $('#qaReqAllBut, #qaPushAllBut').on('change', function(){
        lteam = $('#qaLeadTeamSelect').val();
        if(!lteam){
            $(this).attr('disabled', 'disabled');
        }else{
            $(this).removeAttr('disabled');
        }
    });        
   */
/*
    $('.inputAssignments').select2({
        theme:'bootstrap',
        placeholder: 'Assign To',
        multiple: true,
        minimumResultsForSearch: Infinity,
    });
 */   
/*
    $('#ajax-content-load').find('.boot-popover, .helpTTs').popover({
        html: true,
        container: 'body',
    });
*/

    /*
    $('#qaAssignSelect').select2({
        theme:'bootstrap',
        multiple:true,    
        allowClear: true,
        placeholder: 'Select role',
        minimumResultsForSearch: Infinity,
    }).on('change', function(e){
        tsel = $(this);
        var clrs = tsel.parent('div').find('li.select2-selection__choice span.select2-selection__choice__remove');
        var sels = tsel.parent('div').find('span.select2 li.select2-selection__choice');
        $.each(clrs, function(){
            $(this).css({'color':'#fff'});
        });
        $.each(sels, function(i,val){
            $(this).css({'background-color':'#ff751a', 'color':'#fff'});
        });
    });

	*/			

/*
    function getOffset(min,sec,sign){
        var offset = 60*parseInt(min) + parseInt(sec);
        if (sign == '-'){
            offset = (-1)*parseInt(offset);
        }
        return offset;
    }
*/


	// Generic Refresh of Tasks after Compile Options change
/*    function updateCo(){
        var form = $('#cForm');
        var form_data = form.serialize();
        var spinner = $('#global-busy-indicator');

		if(form){
	        $.ajax( {
	            url: '/tasks/compile/',
	            data: form_data,                
	            type: 'post',
	            dataType:'html',
	            beforeSend: function(){
	            	spinner.fadeIn('fast');
	            },
	            success:function(data, textStatus){
	            	var single_task = $.urlParam('task');
	            	if(single_task){
	            		window.location = '/tasks/compile/';
	            	}
	            	else{
	            		$('#taskListWrap').html(data).fadeIn('fast');	
	            	}
	            },
	            complete: function(){
	            	spinner.fadeOut('fast');
	            }
	        });
		}
    }
*/

   
    
/*
    $('body').on('dp.change', '#qaStartTime, #qaEndTime', function (e) {
    	console.log('hit dp.chg of #qas/e time in at.js');
        var inStart = $(this).parents('form').find('.inputStartTime');
        var inEnd = $(this).parents('form').find('.inputEndTime');
    	
        if(inStart.data('DateTimePicker') != undefined && inEnd.data('DateTimePicker') != undefined){
	        var startTime = inStart.data('DateTimePicker').date();
	        var endTime = inEnd.data('DateTimePicker').date();
	        var diff = endTime.diff(startTime);
	        
	        if(e.oldDate != undefined){
		        var old_start = e.oldDate;
		        var new_start = e.date;
		        //var delta = new_start.diff(old_start);
		        //var delta = e.date.diff(e.oldDate);
		        var delta = new_start.diff(old_start);
		        var old_dur = endTime.diff(old_start);
		
		        // Enforce end after start
		        inEnd.data('DateTimePicker').minDate(startTime);
		
		        if(old_dur != 0){
		            //console.log('olddur ' + old_dur);
		            inEnd.data('DateTimePicker').date(endTime.add(delta));
		            inEnd.data('DateTimePicker').minDate(startTime);
		        }
		
		        if(delta != 0){
		            //inEnd.data('DateTimePicker').date(endTime.add(delta));
		        }
	        }
	        
        }
    });

	$('#qaPushAllBut').on('click', function(){
        if($(this).prop('disabled') == false){
            if($(this).text() == 'Push ALL'){
                $('#qaReqAllBut').text('Request ALL');
                $(this).text('Push NONE');
                $(this).parents('div.teams-panel').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 2).removeClass('btn-danger btn-success btn-ttrid0').addClass('btn-ttrid2');
                });
            }
            else {
                $(this).text('Push ALL');
                $('#qaReqAllBut').text('Request ALL');
                $(this).parents('div.teams-panel').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 0).removeClass('btn-danger btn-success btn-default btn-ttrid2').addClass('btn-ttrid0');
                });
            }
        }
    });*/
   
/*
	// Add new task
	$('body').on('submit', '#qaForm', function(e){
		console.log('got submit of add form');
        e.preventDefault();
        var subBut = $(this).find('.qaSubmitButton');
        var valCont = $(this).find('.qaValidationContent');
        var spinner = $('#global-busy-indicator');
        //var pageNum = $('body').find('.pageNum').html();
        //var to_min = $('#inputOffMin').val();
        //var to_sec = $('#inputOffSec').val();
        //var to_sign = $('#inputOffSign').val();
    
        $('#qaNewTeamsList').find('.tt-btn').each(function(){
            var nteam = $(this).data('team_id');
            var ntr = $(this).data('tr_id');
            $('<input>').attr({
                type: 'hidden',
                name: 'data[TeamRoles]['+nteam+']',
                value: ntr,
            }).appendTo('#qaForm');
        });
        
        $.ajax( {
            url: $(this).attr('action'),
            type: 'post',
            data: $(this).serialize(),
            dataType:'html',
            beforeSend:function () {
                subBut.html('<i class="fa fa-cog fa-spin"></i> Saving...').attr('disabled', true);
                valCont.fadeOut('fast');                
                spinner.fadeIn('fast');
            },
            success:function(data, textStatus) {
                //$('#qaForm').trigger('reset');
        		bootbox.hideAll();

                $('#qaInputDetails').code('');
                var dstr = moment().format('YYYY-MM-DD HH:00:00');
                var dmom = moment(dstr, 'YYYY-MM-DD HH:mm:ss');
                $('#qaStartTime').data('DateTimePicker').date(moment(dmom));
                $('#qaEndTime').data('DateTimePicker').date(moment(dmom));
                $('#qaLeadTeamSelect').trigger('change');
              	$('#qaForm').find('.linkableParentSelect').val(0).trigger('change');
                $('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');
                
                var is_single = $.urlParam('task'); 
                if(is_single){
            		//window.location = '/tasks/compile/';
            		var get_url = '/tasks/compile?task='+is_single;	
            	}
            	else{
            		var get_url = '/tasks/compile?scr=action';
            	}
                
                // Refresh tasks list
                $('#taskListWrap').load(get_url, function(response, status, xhr){
                    if(status == 'success'){
                        $('#taskListWrap').html(response);
                    }
                });
            },
            error: function(xhr, statusText, err){
                valCont.html(xhr.responseText).fadeIn('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                subBut.html('<i class="fa fa-plus"></i> Add New Task').prop('disabled', false);
                spinner.fadeOut('fast');
            },
        });
    });
*/

/*
    // Offset Sec to Min Rollover    
    $('body').on('keyup input', '.inputOffSec', function(e){
        var form = $(this).parents('form');
        var off_min = form.find('.inputOffMin');
        var off_sec = form.find('.inputOffSec');
        //var off_sign = form.find('.inputOffSign');

        if(off_sec.val()==60){
            var old_val = parseInt(off_min.val());
            if(!old_val){ old_val = 0;}
            off_sec.val(0);    
            if(old_val >=0){
                off_min.val(old_val+1);
            }
            else{
                off_min.val(1);
            }
        }
        else if(off_sec.val()==-1){
            var old_val = parseInt(off_min.val());
            if(!old_val){ old_val = 0; }
            if(old_val >= 1){
                off_min.val(old_val-1);
                off_sec.val(59); }
            else{
                off_sec.val(0); 
            }
        }
    });*/

	
	/*
	function taskStartEnd(in_start, start_m, in_end, end_m, type, dur){
		var start = start_m;
		var end = end_m;
		
		if(type == 'start'){
			if(start.diff(end) > 0){
				in_end.data('DateTimePicker').minDate(start);
			}
		}
		if(type == 'end'){
			// Enforce end after start
			if(start_m.diff(end_m) > 0){
			}
		}
	}*/
        //Update role list
        /*
        $.ajax( {
            url: '/assignments/getByUser?team='+leadt,
            type: 'post',
            dataType:'json',
            beforeSend:function () {
                spinner.fadeIn('fast');
            },
            success:function(data, textStatus) {
        		asgns.empty();
            		
        	    asgns.select2({
    				theme:'bootstrap',
			       	tags:true,    
			       	allowClear: true,
    				placeholder: {
      					id: 0,
      					text:"Select a role",
      					selected:'selected'
    				},
            		//placeholder: 'Select an option',
    				//multiple: false,
				    minimumResultsForSearch: Infinity,
    				data: data
				});
            },
            error: function(xhr, statusText, err){
                var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                $('#eaErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                spinner.fadeOut('fast');
            },
        });
*/
/*
    $('#qaStartTime, #qaEndTime').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        //format: 'YYYY-MM-DD HH:mm', 
        format: 'YYYY-MM-DD HH:mm',
    });    
    
	$('#qaDueDate').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD', 
    });   
    
    $('.inputTaskLeadTeam').select2({
		theme:'bootstrap',
		multiple:false,   
		allowClear: true,
		placeholder: 'Select a team',
	    minimumResultsForSearch: Infinity,
	});
	
	$('#qaInputDetails').summernote({
		height: 150,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']],
            ['misc', ['undo','redo','help']],
        ]
    });
*/


/**********************************************************************
 * CRUD Tasks
 * ******************************************************************/
 
/*    
    $('#qaStartTime, #qaEndTime').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD HH:mm:ss', 
    });    
    
	$('#qaDueDate').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD', 
    });   
    
    $('#qaAssignSelect').select2({
		theme:'bootstrap',
		multiple:true,    
		allowClear: true,
		placeholder: 'Assign task',
	    minimumResultsForSearch: Infinity,
	});
	
	$('#qaInputDetails').summernote({
		height: 150,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']],
            ['misc', ['undo','redo','help']],
        ]
    });    
    
	$('#qaPushAllBut').on('click', function(){
        if($(this).prop('disabled') == false){
            if($(this).text() == 'Push ALL'){
                $('#qaReqAllBut').text('Request ALL');
                $(this).text('Push NONE');
                $(this).parents('div.teams-panel').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 2).removeClass('btn-danger btn-success btn-ttrid0').addClass('btn-ttrid2');
                });
            }
            else {
                $(this).text('Push ALL');
                $('#qaReqAllBut').text('Request ALL');
                $(this).parents('div.teams-panel').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 0).removeClass('btn-danger btn-success btn-default btn-ttrid2').addClass('btn-ttrid0');
                });
            }
        }
    });
            
    $('#qaReqAllBut').on('click', function(){
        if($(this).prop('disabled') == false){
            if($(this).text() == 'Request ALL'){
                $(this).text('Request NONE');
                $('#qaPushAllBut').text('Push ALL');
                $(this).parents('div.teams-panel').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 3).removeClass('btn-default btn-success btn-ttrid0 btn-ttrid2').addClass('btn-danger');
                });
            }
            else {
                $(this).text('Request ALL');
                $(this).parents('div.teams-panel').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 0).removeClass('btn-danger btn-success btn-default btn-ttrid2').addClass('btn-ttrid0');
                });
            }
        }
    });
    
    // Disables PushALL and RequestAll buttons when no lead team is selected
    $('#qaReqAllBut, #qaPushAllBut').on('change', function(){
        lteam = $('#qaLeadTeamSelect').val();
        if(!lteam){
            $(this).attr('disabled', 'disabled');
        }else{
            $(this).removeAttr('disabled');
        }
    });        

	// Add new task
	$('#qaForm').on('submit', function(e){
        e.preventDefault();
        var subBut = $(this).find('.qaSubmitButton');
        var valCont = $(this).find('.qaValidationContent');
        var spinner = $('#global-busy-indicator');
        var pageNum = $('body').find('.pageNum').html();
        var to_min = $('#inputOffMin').val();
        var to_sec = $('#inputOffSec').val();
        var to_sign = $('#inputOffSign').val();
    
        $('#qaNewTeamsList').find('.tt-btn').each(function(){
            var nteam = $(this).data('team_id');
            var ntr = $(this).data('tr_id');
            $('<input>').attr({
                type: 'hidden',
                name: 'data[TeamRoles]['+nteam+']',
                value: ntr,
            }).appendTo('#qaForm');
        });
        
        /*
        $('#qaAssignSelect').find('option:selected').each(function(){
        	console.log($(this));
        	var sel = $(this);
            //var nteam = $(this).data('team_id');
            //var ntr = $(this).data('tr_id');
            $('<input>').attr({
                type: 'hidden',
                //name: 'data[Assignments]['+nteam+']',
                name: 'data[Assignments][]',
                //value: ntr,
                value: sel.val(),
            }).appendTo('#qaForm');
        });

        
        $.ajax( {
            url: $(this).attr('action'),
            type: 'post',
            data: $(this).serialize(),
            dataType:'html',
            beforeSend:function () {
                subBut.html('<i class="fa fa-cog fa-spin"></i> Saving...').attr('disabled', true);
                valCont.fadeOut('fast');                
                spinner.fadeIn('fast');
            },
            success:function(data, textStatus) {
                $('#qaForm').trigger('reset');
                $('#qaInputDetails').code('');
                var dstr = moment().format('YYYY-MM-DD HH:00:00');
                var dmom = moment(dstr, 'YYYY-MM-DD HH:mm:ss');
                $('#qaStartTime').data('DateTimePicker').date(moment(dmom));
                $('#qaEndTime').data('DateTimePicker').date(moment(dmom));
                $('#qaLeadTeamSelect').trigger('change');
              	$('#qaForm').find('.linkableParentSelect').val(0).trigger('change');
                $('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');
                
                var is_single = $.urlParam('task'); 
                if(is_single){
            		//window.location = '/tasks/compile/';
            		var get_url = '/tasks/compile?task='+is_single;	
            	}
            	else{
            		var get_url = '/tasks/compile?scr=action';
            	}
                
                // Refresh tasks list
                $('#taskListWrap').load(get_url, function(response, status, xhr){
                    if(status == 'success'){
                        $('#taskListWrap').html(response);
                    }
                });
            },
            error: function(xhr, statusText, err){
                valCont.html(xhr.responseText).fadeIn('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                subBut.html('<i class="fa fa-plus"></i> Add New Task').prop('disabled', false);
                spinner.fadeOut('fast');
            },
        });
    });

    // Edit Task
    $('body').on('submit','form.formEditTask', function(e){
        e.preventDefault();
        var thisform = $(this);
        var subBut = thisform.find('.eaSubmitButton');
        var valCont = $(this).find('.eaValidationContent');
        var spinner = $('#global-busy-indicator');
        //console.log('form edit task'); console.log(spinner);
        
        var pageNum = $('#pageNum').html();
        var viewSingle = $('#singleTask').html();

		var is_single = $.urlParam('task'); 
        if(is_single){
    		//window.location = '/tasks/compile/';
    		var get_url = '/tasks/compile?task='+is_single;	
    	}
    	else{
    		var get_url = '/tasks/compile?scr=action';
    	}

        // Grab state from teams' buttons; save as hidden inputs            
        $(this).find('.tt-btn').each(function(){
            var nteam = $(this).data('team_id');
            var ntr = $(this).data('tr_id');
            $('<input>').attr({
                type: 'hidden', name: 'data[TeamRoles]['+nteam+']', value: ntr}).appendTo(thisform);
        });
            
        $.ajax( {
            url: $(this).attr('action'),
            type: 'post',
            data: $(this).serialize(),
            dataType:'html',
            beforeSend:function () {
                subBut.html('<i class="fa fa-cog fa-spin"></i> Saving...').attr('disabled', true);
                spinner.fadeIn('fast');
                valCont.fadeOut('fast');              
            },
            success:function(data, textStatus) {
                spinner.fadeOut('fast');
                $('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');
                // Refresh tasks list
                $('#taskListWrap').load(get_url, function(response, status, xhr){
                    if(status == 'success'){
                        $('#taskListWrap').html(response);
                    }
                });
            },
            error: function(xhr, statusText, err){
                valCont.html(xhr.responseText).fadeIn('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                subBut.html('<i class="fa fa-save"></i> Save Task').attr('disabled', false);
            },
        });
        return false;
    });

    // Delete Task  
    $('#ajax-content-load').on('click', '.eaTaskDeleteButton', function(){
        var tid = $(this).data('tid');
        var desc = $(this).data('desc');
        var del_mod = $(this).parents('#ajax-content-load').find('#deleteTaskModal');
        var tdesc = del_mod.find('.deleteTaskDesc');
        var thidid = del_mod.find('#deleteTaskId');
        var pageNum = $('#pageNum').html();
        
        tdesc.html('<b>'+desc+'</b>');
        thidid.html(tid);
        del_mod.modal('show');
    });

*/


    // Offset Sec to Min Rollover
    /*    
    $('#ajax-content-load').on('keyup input', '.inputOffSec', function(e){
        var form = $(this).parents('form');
        var off_min = form.find('.inputOffMin');
        var off_sec = form.find('.inputOffSec');

        if(off_sec.val()==60){
            var old_val = parseInt(off_min.val());
            if(!old_val){ old_val = 0;}
            off_sec.val(0);    
            if(old_val >=0){
                off_min.val(old_val+1);
            }
            else{
                off_min.val(1);
            }
        }
        else if(off_sec.val()==-1){
            var old_val = parseInt(off_min.val());
            if(!old_val){ old_val = 0; }
            if(old_val >= 1){
                off_min.val(old_val-1);
                off_sec.val(59); }
            else{
                off_sec.val(0); 
            }
        }
    });
    */
    // Offset
    /*
    $('#ajax-content-load').on('change', '.inputOffSec, .inputOffMin, .inputOffType', function(){
    	//console.log("got chg of inoff");
        var form = $(this).parents('form');
        var in_off_min = form.find('.inputOffMin');
        var in_off_sec = form.find('.inputOffSec');
        var in_off_type = form.find('.inputOffType');
        var to_min = in_off_min.val();
        var to_sec = in_off_sec.val();
        var to_type = in_off_type.val();
        var timeCtrl = form.find('.inputTC');
        var timeOffset = $(this);
        var startTime = form.find('.inputStartTime');
        var endTime = form.find('.inputEndTime');
        var toVal =  60*parseInt(to_min) + parseInt(to_sec);
        var parentTask = form.find('.linkableParentSelect');
        var pt_sel = parentTask.find('option:selected');
	    var pt_start_val = pt_sel.data('stime');
        var pt_end_val = pt_sel.data('etime');
        var ost = moment(startTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var oet = moment(endTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var odur = oet.diff(ost);
        var opst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss');
        var isTc = timeCtrl.prop('checked');
        
        // 4 cases for offset type: 1) -1: BEFORE linked task STARTS, 2) -2: BEFORE linked task ENDS, 3) 1: AFTER linked task STARTS, 4) 2: AFTER linked task ENDS
        if(to_type ==-1){
	        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s');
	        var net = moment(nst).add(odur, 'ms');
        }
        else if (to_type == -2) {
	        var nst = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s');
	        var net = moment(nst).add(odur, 'ms');
        }
        else if (to_type == 1){
	        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
	        var net = moment(nst).add(odur, 'ms');
        }
        else if(to_type == 2){
	        var nst = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
	        var net = moment(nst).add(odur, 'ms');
        }
        
        if(isTc){
            startTime.data('DateTimePicker').date(moment(nst));
            endTime.data('DateTimePicker').minDate(moment(nst)).date(moment(net));
        }
    });    

    //Time Ctrl
    $('#ajax-content-load').on('change', '.inputTC', function(){
    	//console.log('got chg of inputTTC');
        var form = $(this).parents('form');
        var startTime = form.find('.inputStartTime');
        var endTime = form.find('.inputEndTime');
        var in_off_min = form.find('.inputOffMin');
        var in_off_sec = form.find('.inputOffSec');
        var in_off_type = form.find('.inputOffType');
        var to_min = in_off_min.val();
        var to_sec = in_off_sec.val();
        var to_type = in_off_type.val();
        var toVal = 60*parseInt(to_min) + parseInt(to_sec);
        var ost = moment(startTime.val());
        var parentTask = form.find('.linkableParentSelect');
        var pt_sel = parentTask.find('option:selected');
        var pt_start_val = pt_end_val =  null;
        var ost = moment(startTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var oet = moment(endTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var odur = oet.diff(ost)
        var stHelp = form.find('.stHelpWhenTC');

        if(pt_sel.data('stime')){
            pt_start_val = pt_sel.data('stime');    
        }
        if(pt_sel.data('etime')){
            pt_end_val = pt_sel.data('etime');    
        }

        if(to_type ==-1 || to_type ==0){
	        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s');
	        var net = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s').add(odur, 'ms');
        }
        else if (to_type == -2) {
	        var nst = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s');
	        var net = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s').add(odur, 'ms');
        }
        else if (to_type == 1){
	        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
	        var net = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s').add(odur, 'ms');
        }
        else if(to_type == 2){
	        var nst = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
	        var net = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s').add(odur, 'ms');
        }
        
        if($(this).prop('checked') && pt_start_val){
            //console.log('tc checked and start val');
            startTime.prop('readonly', true);
            startTime.data('DateTimePicker').date(moment(nst));
            endTime.data('DateTimePicker').minDate(moment(nst)).date(moment(net));
            in_off_min.prop('disabled', false);
            in_off_sec.prop('disabled', false);
            in_off_type.prop('disabled', false);
            stHelp.removeClass('collapse');
        }
        else{
            in_off_min.val(0).prop('disabled', true);
            in_off_sec.val(0).prop('disabled', true);
            in_off_type.prop('disabled', true);
            startTime.prop('readonly', false);
            stHelp.addClass('collapse');
        }
    });

    //Change Linked Parent
    $('body').on('change','.linkableParentSelect', function(e){
        console.log('chg lpt_id from compile');
        var this_sel = $(this);
        var sel_par = $(this).val();
        var form = this_sel.parents('form');
        var this_clear_parent_but = $(this).parents('.form-group').find('.pidClearBut');
        var tc = form.find('.inputTC');
        var start = form.find('.inputStartTime');
        var stHelp = form.find('.stHelpWhenTC');
        var to_min = form.find('.inputOffMin');
        var to_sec = form.find('.inputOffSec');
        var to_sign = form.find('.inputOffSign');
        var advpid = form.find('.advancedParent');
        var curTID =  form.find('.hiddenTaskId').text();
        var partask_label = $(this).parents('.form-group').find('label');

        to_min.val(0).prop('disabled', true);
        to_sec.val(0).prop('disabled', true);        
        to_sign.prop('disabled', true);
        tc.prop('checked', false);
        
        if(!sel_par){
            advpid.addClass('collapse');
            start.prop('readonly',false);
            this_clear_parent_but.addClass('disabled');
            stHelp.addClass('collapse');
            $(this).prop('readonly', false);
            to_min.val(0).prop('disabled', true);
            to_sec.val(0).prop('disabled', true);        
        }
        else{
            advpid.removeClass('collapse');
            this_clear_parent_but.removeClass('disabled');
            start.prop('readonly', false);
            stHelp.addClass('collapse');
            var no_tc_html = '<div class=\"alert alert-danger par_disallow\"><i class=\"fa fa-lg fa-exclamation-triangle\"></i> <b>Cannot Link to The Selected Task</b><br/>The task you\'re attempting to link to already links <u>back</u> to this task. Please select a different task to link to. <a class=\"helpTTs\" tabindex=\"0\" role=\"button\" data-toggle=\"popover\" data-trigger=\"focus\" title=\"Why Can\'t I Link to This?\" data-content=\"<p>The task you\'re trying to link to already links back to your task -- possibly through intermediate tasks.</p><p>This would potentially create an infinite loop. We prevent the loop by preventing you from linking to this task. <p>This can often be resolved by changing the linked task to something with higher priority, like a Production item or Chair task.</p>\"><i class=\"fa fa-question-circle text-info\"></i></a></div>';
            tc.prop('checked', false);
            tc.prop('disabled', false);
                            
            if(sel_par && curTID){
                //console.log('check PID from compile');
                $.ajax( {
                    url: '/tasks/checkPid/',
                    data: {task:curTID, parent:sel_par},
                    type: 'post',
                    dataType:'json',
                    beforeSend:function () {
                        partask_label.append('<span class="tr_spin" style="margin-left:5px;"><i class="fa fa-cog fa-spin"></i></span>');
                        advpid.parent().find('.par_disallow').remove();
                    },            
                    success:function(data, textStatus) {
                        if(data.allow_parent == false){
                            this_sel.val('').trigger('change');
                            tc.prop('checked', false);
                            tc.prop('disabled', true);
                            to_min.prop('disabled', true);
                            to_sec.prop('disabled', true);
                            to_sign.prop('disabled', true);
                            advpid.parent().append(no_tc_html);

                            $('.helpTTs').popover({
                                container: 'body',
                                html:true,
                            });
                        }
                        else{
                            tc.prop('disabled', false);
                        }
                    },
                    complete:function (XMLHttpRequest, textStatus) {
                        partask_label.find('.tr_spin').remove();
                    },
                });
            }// No curTID (i.e. adding new) or parent val selected  
            else{
                to_min.val(0).prop('disabled', true);
                to_sec.val(0).prop('disabled', true);        
                to_sign.prop('disabled', true);
            }
        }
    });
    
    //Change lead
    $('body').on('change','.inputLeadTeam', function(){
        console.log('got lead change in compile');
        var leadt = $(this).val();
        var thisT = $(this); 
        var form = $(this).parents('form');
        var lead_label = $(this).parents('div.form-group').find('label');
        var partask_label = form.find('.linkedParentDiv').parents('div.form-group').find('label');
        var partask_list = form.find('.linkedParentDiv');
        var ea_tlist = form.find('.teamsList');
        var curPID =  form.find('.hiddenParId').text();
        var curTID =  form.find('.hiddenTaskId').text();
        var asgns = form.find('.inputAssignments');
        var spinner = $('#global-busy-indicator');

        //Update role list
        /*
        $.ajax( {
            url: '/assignments/getByUser?team='+leadt,
            type: 'post',
            dataType:'json',
            beforeSend:function () {
                spinner.fadeIn('fast');
            },
            success:function(data, textStatus) {
        		asgns.empty();
            		
        	    asgns.select2({
    				theme:'bootstrap',
			       	tags:true,    
			       	allowClear: true,
    				placeholder: {
      					id: 0,
      					text:"Select a role",
      					selected:'selected'
    				},
            		//placeholder: 'Select an option',
    				//multiple: false,
				    minimumResultsForSearch: Infinity,
    				data: data
				});
            },
            error: function(xhr, statusText, err){
                var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                $('#eaErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                spinner.fadeOut('fast');
            },
        });

        $.ajax( {
            url: '/tasks_teams/updateSig/',
            data: {team:leadt},
            type: 'post',
            dataType:'html',
            beforeSend:function () {
                spinner.fadeIn('fast');
            },
            success:function(data, textStatus) {
                ea_tlist.html(data).fadeIn('fast');
                $('#qaReqAllBut, #qaPushAllBut').trigger('change');
                if(leadt){
                    updateLinkableParents(leadt, partask_list, curPID, curTID);
                    //console.log('updating sig due to lead change in compile.js'); 
                }
            },
            error: function(xhr, statusText, err){
                var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                $('#eaErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                spinner.fadeOut('fast');
            },
        });
    });   */ 
	
	




    // Task Details -- NEW  
/*
    $('#ajax-content-load').on('click', '.task-panel-heading', function(e){
        var tid = $(this).attr('data-tid');
        var details = $('#viewTaskModal');
		details.find('.modal-body').load('/tasks/quickAdd/'+tid, function(data){
			details.modal('show');	
		});
		

	});
	
    $('#newTaskFromMenu').on('click', function(e){
    	
        //e.preventDefault();
        //e.stopPropagation();
    	
    	console.log('hi');
        var newtask = $('body').find('#newTaskModal');
        console.log(newtask.find('.modal-body'));
		newtask.find('.modal-body').load('/tasks/quickAdd/'), function(data){
			newtask.modal('show');
		}
		//return false;
	});

    
	$('#coViewDetailsBut').on('click', function(){
        var in_vl = $('#coViewDetails');
        var this_val = $(this).data('checked');
        var nval;
        var shtml = '<i class=\"fa fa-eye\"></i> Show Details';
        var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Details';                                   

        if(this_val == 0){
            nval = 1;
            $(this).data('checked', 1);
        }
        else{
            nval=0;
            $(this).data('checked', 0);
        }
        in_vl.val(nval);
        in_vl.trigger('change');
            
        if($(this).hasClass('details_hidden')){
            $('div.divTaskDetails').show();
            $(this).removeClass('details_hidden').addClass('details_shown');
            $(this).html(hhtml);    
        }
        else{
	        $('div.divTaskDetails').hide();
            $(this).removeClass('details_shown').addClass('details_hidden');
            $(this).html(shtml); 
        }
    });
                    

    $('#coViewThreadedBut').on('click', function(){
        var in_vl = $('#coViewThreaded');
        var this_val = $(this).data('checked');
        var nval;
        var hhtml = '<i class=\"fa fa-list-ol\"></i> Show Unthreaded';
        var shtml = '<i class=\"fa fa-indent\"></i> Show Threaded';                                   
        
        if(this_val == 0){
            nval = 1;
            $(this).data('checked', 1);
        }
        else{
            nval=0;
            $(this).data('checked', 0);
        }
        in_vl.val(nval);
        in_vl.trigger('change');

        if($(this).hasClass('showing_rundown')){
            $('div.isChild').hide();
            $(this).removeClass('showing_rundown').addClass('showing_threaded');
            $(this).html(hhtml);    
        }
        else{
            $('div.isChild').show();
            $(this).removeClass('showing_threaded').addClass('showing_rundown');
            $(this).html(shtml); 
        }
    });

    $('#coViewLinksBut').on('click', function(){
        var in_vl = $('#coViewLinks');
        var this_val = $(this).data('checked');
        var shtml = '<i class=\"fa fa-eye\"></i> Show Linkages';
        var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Linkages';   
        var nval;

        if(this_val == 0){
            nval = 1;
            $(this).data('checked', 1);
        }
        else{
            nval=0;
            $(this).data('checked', 0);
        }

        in_vl.val(nval);
        in_vl.trigger('change');
        
        if($(this).hasClass('links_hidden')){
            $('div.taskLinkages').show();
            $(this).removeClass('links_hidden').addClass('links_shown');
            $(this).html(hhtml);    
        }
        else{
            $('div.taskLinkages').hide();
            $(this).removeClass('links_shown').addClass('links_hidden');
            $(this).html(shtml); 
        }
    });
    */

    /*
	$('#taskListWrap').on('click', 'div.task-panel-heading button', function(e){
        //e.stopPropagation();
    });*/
    
/*
    $('.inputAssignments').select2({
        theme:'bootstrap',
        placeholder: 'Assign To',
        multiple: true,
        minimumResultsForSearch: Infinity,
    });
 */   
/*
    $('#ajax-content-load').find('.boot-popover, .helpTTs').popover({
        html: true,
        container: 'body',
    });
*/


    // Time Shifting
    /*
    $('#taskListWrap').on('click', '.taskTs', function(e){
        e.stopPropagation();
    });*/
/*
    function getOffset(min,sec,sign){
        var offset = 60*parseInt(min) + parseInt(sec);
        if (sign == '-'){
            offset = (-1)*parseInt(offset);
        }
        return offset;
    }
*/
    /*
	$('#ajax-content-load').on('mouseenter', 'label.taskTimeshift', function(){
        var val = $(this).parent().find('input').prop('disabled');
        if(val)
            $(this).css('cursor', 'not-allowed');
    });    
    */

	/*
	function taskStartEnd(in_start, start_m, in_end, end_m, type, dur){
		var start = start_m;
		var end = end_m;
		
		if(type == 'start'){
			if(start.diff(end) > 0){
				in_end.data('DateTimePicker').minDate(start);
			}
		}
		if(type == 'end'){
			// Enforce end after start
			if(start_m.diff(end_m) > 0){
			}
		}
	}*/

/*
    function addNewTask(start){
        //console.log('called add new task from c.js');
        if(!start){return false;}
        var startTime = $(document).find('#qaStartTime');
        var endTime = $(document).find('#qaEndTime');
        var col_add = $(document).find('#colAdd');
        var col_add_head = $(document).find('#colAddHeading');

        startTime.data('DateTimePicker').date(moment(start));
        endTime.data('DateTimePicker').date(moment(start));
        $(document).find('#qaLeadTeamSelect').trigger('change');
        
        if(!col_add.hasClass('in')){
            col_add_head.trigger('click');
            //console.log('got to col_add had class collapse');
        }

        $('html, body').animate({
            scrollTop: $(document).find('#ajax-content-load').offset().top
        }, 800);        
    }

    function addNewLinkedTask(parent, team, parent_team){
    	//console.log('anlt in c.js');
        if(!parent || !team){ return false; }
        
        var leadTeam = $('#qaLeadTeamSelect');
        var lpTeam = $('#qaLinkedParentDiv').find('.linkableParentSelect');
        var qaTC = $('#qaTimeCtrl');
        leadTeam.val(team);
        
        updateSignature(team, leadTeam, parent_team);
        updateLinkableParents(team, $('#qaLinkedParentDiv'), parent);
        lpTeam.trigger('change');
        qaTC.trigger('change');
        
        if(!$('#colAdd').hasClass('in')){
            $('#colAddHeading').trigger('click');
        }

        $('html, body').animate({
            scrollTop: $('#ajax-content-load').offset().top
        }, 800);
    }

	function resetAfterChangeParents(lp_select){
        var advpid = lp_select.parents('form').find('.advancedParent');
        var sel_par = lp_select.val('');
        var start = lp_select.parents('form').find('.inputStartTime');
        var tc = lp_select.parents('form').find('.inputTC');
        var stHelp = lp_select.parents('form').find('.stHelpWhenTC');
        var to_min = lp_select.parents('form').find('.inputOffMin');
        var to_sec = lp_select.parents('form').find('.inputOffSec');
        var to_sign = lp_select.parents('form').find('.inputOffSign');
        
        to_min.val(0).prop('disabled', true);
        to_sec.val(0).prop('disabled', true);        
        to_sign.prop('disabled', true);
        tc.prop('checked', false);
        stHelp.addClass('collapse');
        start.prop('readonly', false);
		lp_select.val(null);        
    	tc.prop('disabled', true);
    	advpid.addClass('collapse');
	}
*/

    /*
	$('#taskListWrap_OLD').on('click', 'button.addTask', function(e){
        e.preventDefault();
        var stime = $(this).parents('.task-panel').find('.task-panel-heading').data('stime');
        var s_mom = moment(stime);
        addNewTask(stime);
    });*/

    /*
    $('#taskListWrap').on('click', 'button.addLink, a.addLink', function(e){
        var tid = $(this).parents('.task-panel').find('.task-panel-heading').data('tid');
        var tm_id = $(this).parents('.task-panel').find('.task-panel-heading').data('team_id');
        var jsonCIN = $(this).parents('.task-panel').find('.jsonCIN').html();
        var jqCIN = $.parseJSON(jsonCIN);
        var opts = '';

        $('#teamAddLinkedModal').find('span.hiddenParentId').html(tm_id);
        $('#teamAddLinkedModal').find('span.hiddenTid').html(tid);
        
        //: This apparently fails in IE<9
        var numCIN = Object.keys(jqCIN).length;

        if(numCIN == 1){
            var from_team = Object.keys(jqCIN)[0];
            addNewLinkedTask(tid, from_team, tm_id); 
        }      
        else{
            $.each(jqCIN, function(i,e){
                opts += '<option value=\"'+i+'\">'+e+'</option>';
            });
        
            $('#selectLinkedTeam').html(opts);
            $('#teamAddLinkedModal').modal('show');
        }
    });*/

/*
	function updateLinkableParents(team, update_div, current, child){
		//console.log('got up link par in c.js');
        $.ajax( {
            url: '/tasks/linkable/',
            data: {team:team, current:current, child:child},
            type: 'post',
            dataType:'html',
            success: function(data, textStatus){
                update_div.html(data).fadeIn('fast');
                var new_lps = update_div.find('.linkableParentSelect');
                bindToSelect2(new_lps);
                new_lps.val(current);
                new_lps.trigger('change');
            },
        });
    }
*/

/*   
	$('#teamAddLinkedModal').on('show.bs.modal', function(e) {
	    var parent_id = $(this).find('span.hiddenParentId').html();
        var tid = $(this).find('span.hiddenTid').html();
        var l_modal = $(this);
        var doLink = $(this).find('.btn-doLink');
        
        doLink.on('click', function(e){
            var team_sel = l_modal.find('#selectLinkedTeam option:selected');
            addNewLinkedTask(tid, team_sel.val(), parent_id);
            l_modal.modal('hide');
            $( this ).off(e);
        });
    });      
*/

//*****************************  INPUT FUNCTIONS  *********************************//
/*
	function bindToSelect2(element, placeholder){
		$(element).select2({
			theme: 'bootstrap',
			'width':'100%',
			//'allowClear': true,
			placeholder: placeholder,
	        minimumResultsForSearch: Infinity,
       });
	}
	
	function bindStartEndToDTP(element){
		$(element).datetimepicker({
	        sideBySide: true,
	        showTodayButton: true,
	        allowInputToggle: true,
	        format: 'YYYY-MM-DD HH:mm', 
    	}); 
	}

	function bindDateOnlyToDTP(element){
		$(element).datetimepicker({
	        sideBySide: true,
	        showTodayButton: true,
	        allowInputToggle: true,
	        format: 'YYYY-MM-DD', 
    	}); 
	}

	function bindSummernoteStd(element){
		//console.log('hit bind summernote in compile.js');
		if(element){
			var ele = $(element);
			ele.summernote({
				//disableDragAndDrop: true,
	            height: 100,
	            toolbar: [
	                ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
	                ['para', ['ul', 'ol']],
	                ['insert', ['link']],
	                ['misc', ['undo','redo','help']],
	            ]
			});
		}
	}
*/
/*
    function deleteTask(tid){
    	var spinner = $('#global-busy-indicator');
        if(tid>0){
	        $.ajax( {
	            url: '/tasks/delete/'+tid,
	            type: 'post',
	            dataType:'json',
	            beforeSend:function () {
	                spinner.fadeIn('fast');
	            },
	            success:function(data, textStatus) {
	                var msg_html = '<div class=\"alert alert-success\" role=\"alert\">'+data.message+'</div>';
	                $('#taskListWrap').load('/tasks/compile?src=ajax', function(response, status, xhr){
	                    if(status == 'success'){
	                    	console.log('doing refresh after delete');
	                        $('#taskListWrap').html(response);
	                    }
	                });
		            $('#cErrorStatus').html(msg_html).fadeIn().delay(3000).fadeOut('fast');
	            },
	            complete:function (XMLHttpRequest, textStatus) {
	                spinner.fadeOut('fast');
	            }, 
	        });        
        }
        
        return false;
    }

    function updateSignature(team, in_lead, pushed){
    	//console.log('hit update sig in c.js');
    	if(!team || !in_lead){return false;}

        var lead_label = $(in_lead).parents('div.form-group').find('label');
        var partask_label = $(in_lead).parents('form').find('.linkedParentDiv').parents('div.form-group').find('label');
        var partask_list = $(in_lead).parents('form').find('.linkedParentDiv');
        var ea_tlist = $(in_lead).parents('form').find('.teamsList');
        var spinner = $('#global-busy-indicator');

    	$.ajax( {
            url: '/tasks_teams/updateSig/',
            data: {team:team},
            type: 'post',
            dataType:'html',
            beforeSend:function () {
            	spinner.fadeIn('fast');
            },
            success:function(data, textStatus) {
                ea_tlist.html(data).fadeIn('fast');
                $('#qaReqAllBut, #qaPushAllBut').trigger('change');
                
                if(pushed){
                	setParentTeamAsPushed(pushed, $('#qaNewTeamsList'));	
                }
            },
            error: function(xhr, statusText, err){
                var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                $('#eaErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                spinner.fadeOut('fast');
            },
        });
    }

    function setParentTeamAsPushed(par_team_id, tlist){
        var lpt_but = $(tlist).find('span[data-team_id='+par_team_id+']');
        if(lpt_but.hasClass('btn-ttrid0')){
            lpt_but.removeClass('btn-ttrid0').addClass('btn-ttrid2').data('tr_id', 2); 
        }
    }
*/


/*
    $('#taskListWrap').on('click', 'span.ban-edit', function(e){
    	console.log('got ban-edit click with taskListWrap bind');
        //e.preventDefault();
        //e.stopPropagation();
    });
*/    


/***************
 * Modals
 ***************/
/*
    $('#deleteTaskModal').on('show.bs.modal', function(e) {
    	//console.log('hit show modal');
	        //e.preventDefault();
	        e.stopPropagation();

        var doDel = $(this).find('.btn-doDelete');
        var task_id_span = $(this).find('#deleteTaskId');
        var task_id = task_id_span.html();
        //var pageNum = $('body').find('#pageNum').html();
        
        //console.log('doDel, task id');
        //console.log(doDel);
        //console.log(task_id);
        
        doDel.on('click', function(e){
        	//alert('tried to delete');
        	//console.log('got delete click');
            if(task_id>0){
                deleteTask(task_id);
                $('#deleteTaskModal').modal('hide');
            }
            return false;
        });
    });

	$('#deleteTaskModal').on('hidden.bs.modal', function () {
    	//$(this).data('bs.modal', null);
	      $(this).data('bs.modal', null)
	      $('#deleteTaskModal').removeData();

    	//alert('hi');
    	$('#deleteTaskId').html('');
	});
 */
	//Reset inputs after a change in linkable list
	/*
	function resetAfterChangeParents(lp_select){
		console.log('hit reset after parents change in at.js');
        var advpid = lp_select.parents('form').find('.advancedParent');
        var sel_par = lp_select.val('');
        var start = lp_select.parents('form').find('.inputStartTime');
        var tc = lp_select.parents('form').find('.inputTC');
        var stHelp = lp_select.parents('form').find('.stHelpWhenTC');
        var to_min = lp_select.parents('form').find('.inputOffMin');
        var to_type = lp_select.parents('form').find('.inputOffType');
        to_min.val(0).prop('disabled', true);
        to_type.prop('disabled', true);
        tc.prop('checked', false);
        stHelp.addClass('collapse');
        start.prop('readonly', false);
		lp_select.val(null);        
    	tc.prop('disabled', true);
    	advpid.addClass('collapse');
	}*/

/*
	function getTaskDetails(tid, tbody_div){
		console.log('hit get details in at.js');
		if(tid && tbody_div){
            $.ajax({ 
            	url: '/tasks/details/'+tid, type: 'post', dataType:'html',
                success:function(data, textStatus) {
                    tbody_div.html(data).fadeIn('fast');
                    var new_lpts = tbody_div.find('.linkableParentSelect');
                    bindToSelect2(new_lpts);
                },
            });
		}
	}*/