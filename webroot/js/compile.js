$(document).ready(function () {

    //$('.helpTTs').popover({container: 'body',html:true, trigger:'click hover'});

	$('#taskListWrap').on('click', '.noProp, .csTaskDetails a, .ban-edit', function(e){
	    e.stopPropagation();
	});    

    $('body').find('.boot-popover, .helpTTs').popover({
        html: true,
        trigger: 'click',
        container: 'body',
    });

//**************** COMPILE OPTIONS  ******************//
     
    function sortState(state){
        var c_sort = $('.coSort');
        var s_disabled = false;
        if(state == 'disable'){ s_disabled = true; }
        c_sort.each(function(i,e){ $(e).prop('disabled', s_disabled); });    
    }

    function viewOptionsState(state){
        var vo = $('#compileViewOptions');
        if(state == 'disable'){
        	vo.hide();
        }
        else{
        	vo.show();
        }
    }


    $('#coTeams').multiselect({
        includeSelectAllOption: true,
        enableClickableOptGroups: true, 
        buttonClass: 'btn btn-info', 
        buttonWidth: '100%',
        numberDisplayed: 5,
        maxHeight: 200,
    });
    
    $('#coDateRange').on('apply.daterangepicker', function(ev, picker) {
        new_s = $(this).data('daterangepicker').startDate;
        new_e = $(this).data('daterangepicker').endDate;
        $('#coStartDate').val(moment(new_s).format('YYYY-MM-DD'));
        $('#coEndDate').val(moment(new_e).format('YYYY-MM-DD'));
        //$('#coDateRange').trigger('change');
        updateCo();
    });    
    
	$('#coViewList input').on('change', function(){
        var help = $('body').find('#coViewTypeHelp');
        var help_str = '';
        //$('#coViewType').val($(this).val()).trigger('change');
        if($(this).val() == 1){
            help_str+= '<b>Rundown View</b><ul><li>Default setting. Shows any tasks involving the selected Teams in any role.</li>';
            help_str+= '<li>Ordered by <u>ascending</u> or <u>descending</u> Start Time</li></ul>';
            sortState('enable');
            viewOptionsState('enable');        
        }else if($(this).val() == 2){
            help_str+= '<b>Event Day Timeline</b><ul><li>Shows a 24 hour timeline from <b>6AM event day</b> to <b>6AM the next day</b> of tasks involving the selected Teams.</li>';
            help_str+= "<li>Separated by Team's roles and ordered by <u>ascending</u> Start Time</li></ul>";
            sortState('disable');
            viewOptionsState('disable'); 
        }else if($(this).val() == 10){
            help_str+= '<b>Lead Only</b><ul><li>Shows only tasks where the selected Teams are the lead.</li>';
            help_str+= "<li>Useful for focusing on a single team's tasks.</li><li>Ordered by <u>ascending</u> or <u>descending</u> Start Time</li></ul>";
            sortState('enable');
            viewOptionsState('disable');        
        }else if($(this).val() == 30){
            help_str+= '<b>Open Requests <u>From</u> Other Teams (Owing)</b><ul><li>Listing of everything owed to other teams by the selected Teams.</li>';
            help_str+= '<li>Useful for tracking what your team owes other teams.</li><li>Fetches tasks from <u>all dates</u>.</li><li>Ordered by <u>ascending</u> or <u>descending</u> Start Time</li></ul>';
            sortState('enable');
            viewOptionsState('disable');        
        }else if($(this).val() == 31){
            help_str+= '<b>Open Requests <u>To</u> Other Teams (Waiting)</b><ul><li>Tasks where selected Teams requested help from other teams and the request is still Open.</li>';
            help_str+= '<li>Useful for tracking what other teams owe your team.</li><li>Fetches tasks from <u>all dates</u>.</li><li>Ordered by <u>ascending</u> or <u>descending</u> Start Time</li></ul>';
            sortState('enable');
            viewOptionsState('disable');        
        }else if($(this).val() == 100){
            help_str+= '<b>Recently Modified</b><ul><li>Shows most recently modified tasks.</li>';
            help_str+= '<li>Useful for seeing recent changes to tasks involving your Team.</li><li>Fetches tasks from <u>all dates</u>.</li><li>Ordered by <u>descending modified date</u> (most recently changed first).</li></ul>';
            sortState('disable');
            viewOptionsState('disable');
        }else if($(this).val() == 500){
            help_str+= '<b>Action Items</b><ul><li>Tasks that are important to the entire Ops Team.</li>';
            help_str+= '<li>Often take place over multiple weeks and progress needs to be tracked. Ex: Calling volunteers/submitting inventory requests</li><li>Fetches tasks from <u>all teams</u> on <u>all dates</u>.</li><li>Ordered by <u>ascending due date</u> to highlight upcoming due tasks.</li></ul>';
            sortState('disable');
            viewOptionsState('disable');        
        }
        help.html(help_str);
    });   
    
    $('#coViewList input:checked').trigger('change');
    //$('#cForm').on('change', 'input:not(.coDateRange)', updateCo);
	$('#cForm').on('change', 'input:not(.coDateRange, #coViewChildren)', updateCo);

	// Generic Refresh of Tasks after Compile Options change
    function updateCo(){
        var form = $('#cForm');
        var form_data = form.serialize();
        var spinner = $('#global-busy-indicator');

		if(form){
	        $.ajax( {
	            url: '/tasks/compile/', data: form_data, type: 'post', dataType:'html',
	            beforeSend: function(){ spinner.fadeIn('fast');},
	            success:function(data, textStatus){
	            	var single_task = $.urlParam('task');
	            	if(single_task){ 
	            		window.location = '/tasks/compile/';
            		}else{ 
            			$('#taskListWrap').html(data).fadeIn('fast');}
    			},
	            complete: function(){ spinner.fadeOut('fast');}
	        });
		}
    }

// *************************  FUNCTIONS   *********************************//

/*
	function getTaskDetails(tid, tbody_div){
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

// ******************* TASKS & DISPLAY *************************************//
	// Fetch task details
    $('#ajax-content-load').on('click', '.task-panel-heading', function(e){
        var tdheading_div = $(this);
        var tp = $(this).parent('.task-panel');
        var tid = tp.data('task_id');
        var tbody_div = tp.find('.taskPanelBody');
        var spinner = $('#global-busy-indicator');

		spinner.fadeOut('fast');

        if (!tbody_div.hasClass('is_vis') && (tid > 0)){
            $.ajax({
            	url: '/tasks/details/'+tid, type: 'post', dataType:'html', 
            	beforeSend:function () { spinner.fadeIn('fast'); },
                success:function(data, textStatus) {
                    tbody_div.html(data).addClass('is_vis').slideDown(250);
                    tdheading_div.addClass('fetched');
                    var new_lpts = tbody_div.find('.linkableParentSelect');
                    bindToSelect2(new_lpts);
                },
                complete:function (XMLHttpRequest, textStatus) { spinner.fadeOut('fast'); },
                error: function(xhr, statusText, err){
                    //var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                    //$('#cErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                },
            });
        }  
        else {  // Details are visible
            tbody_div.removeClass('is_vis').slideUp(250);
        }
        return false;
    });

	$('#taskListWrap').on('mouseenter', '.task-panel-heading' , function(e){
        var ucon_inv = $(this).parents('.task-panel').data('uconinv');
        var tbuts = $(this).find('.task-buttons');
        var but = '<div class="actButs noProp" style="margin:1px;"><button class="btn btn-yh btn-xxs addTask"><i class="fa fa-plus-circle"> </i> &nbsp;Add</button>';
        var link = '<span class="actButs noProp"><button class="btn btn-default btn-xxs addLink"><i class="fa fa-link"> </i> &nbsp;Link</button></span>';

        if(ucon_inv){ but += link;
        }else{ but +='</div>';}
        
        var addHtml = $(but);
        var tsDiv = $(this).find('.taskTs').parent();
        addHtml.hide().prependTo(tsDiv).show();
    });        

	$('#taskListWrap').on('mouseleave', '.task-panel-heading' , function(e){
		$(this).parents('.task-panel').find('.actButs').remove();
        var tbuts = $(this).find('.task-buttons');
		tbuts.show();
    });
   
	$('#taskListWrap').on('click', 'button.addTask', function(e){
        e.preventDefault();
        var options = [];
        var stime = $(this).parents('.task-panel').data('start_time');
        options.start = moment(stime);
        newAddTaskModal(options);
    });

    $('#taskListWrap').on('click', 'button.addLink, a.addLink', function(e){
    	//console.log('got add link click');
        var parent_task = $(this).parents('.task-panel').data('task_id');
        var parent_team = $(this).parents('.task-panel').data('team_id');
		var cin = $(this).parents('.task-panel').data('cin');
		var options = arr_cin = [];
		options.parent_task = parent_task;
		options.parent_team = parent_team;

        // TODO: This apparently fails in IE<9
        var numCIN = Object.keys(cin).length;

		if(numCIN == 1){
            options.lead_team = parseInt(Object.keys(cin)[0]);
            newAddTaskModal(options); 
        }else{
            $.each(cin, function(i,e){
            	arr_cin[arr_cin.length] = {text:e, value:i}; 
                //opts += '<option value=\"'+i+'\">'+e+'</option>';
            });
        
	        bootbox.prompt({
	    		title: "Which Team Would You Like to Link?",
				inputType: 'select',
				buttons: {
        			confirm: {
            			label: '<i class="fa fa-group"></i> Select Team',
            			className: 'btn-success'
        			},
			        cancel: {
			            label: '<i class="fa fa-close"></i> Cancel',
			            className: 'btn-danger'
			        }
    			},
	    		inputOptions: arr_cin,
	    		callback: function (result) {
	    			if(result){
		    			options.lead_team = result;  		
		        		newAddTaskModal(options);
	    			}else{ bootbox.hideAll(); }
	    		}
			});
        }
	});

	$('#ajax-content-load').on('click', '.task-panel-heading111111111111', function(e){
		var tid = $(this).parents('.task-panel').data('task_id');
		var opts = [];
		opts.task_id = tid;
		newViewTaskModal(opts);
	
	});

    // TT Button role changes from compile 
    $('#taskListWrap').on('click', 'span.tglTR:not(.ban-edit)', function(e){
    	//console.log('hit tt role change in c.js');
        e.stopPropagation();
        //e.preventDefault();
        var this_but = $(this);
        var tdheading_div = $(this).closest('.task-panel');
        var task_id = tdheading_div.data('task_id');
        var team_id = this_but.data('teamid');
        var role_id = '';
        var tbody_div = tdheading_div.find('.taskPanelBody');
        var spinner = $('#global-busy-indicator');

        if(this_but.hasClass('openTeam')){
            role_id = 4;
            this_but.removeClass('btn-danger openTeam').addClass('btn-success closeTeam');
        }else if(this_but.hasClass('closeTeam')){
            role_id = 2;
            this_but.removeClass('btn-success closeTeam').addClass('btn-ttrid2 pushTeam');
        }else if(this_but.hasClass('pushTeam')){
            role_id = 3;
            this_but.removeClass('btn-ttrid2 pushTeam').addClass('btn-danger openTeam');
        }    

        if((task_id!=null) && (team_id!=null) && (role_id!=null) ){                                            
            $.ajax({
                url: '/tasks_teams/chgRole/', type: 'post', dataType: 'json',
                data: {'task':task_id, 'team':team_id, 'role':role_id},                
                beforeSend:function () { spinner.fadeIn('fast'); },
                //success: function(){ 
                	//if(tbody_div.hasClass('is_vis')){ 
                		//getTaskDetails(task_id, tbody_div); 
            		//}
        		//},  	// If task details are visible, refresh to reflect changed state
                complete:function (XMLHttpRequest, textStatus) { spinner.fadeOut('fast'); },
                error: function(xhr, statusText, err){
                    var res_j = $.parseJSON(xhr.responseText);
                    var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+res_j.message+'</div>';
                    $('#cErrorStatus').html(msg).fadeIn('fast').delay(7000).fadeOut();
                },
            });
        }
    });

/*************** COMMENTS ********************/    
	function deleteComment(cid){
    	if(cid !=null){        
	        $.ajax( {
	            url: '/comments/delete/', data: {cid:cid}, type: 'post', dataType:'json',
	            success:function(data, textStatus) {
	                var tid = $('#ajax-content-load').find('#commentBody'+cid).data('tid');
	                $('#ajax-content-load').find('#commentBody'+cid).fadeOut();
	                $('#ajax-content-load').find('#commentBody'+cid).parents('.panel-tcom').parents('row').remove();
	                var cbody = $('#ajax-content-load').find('#commentBody'+cid).parents('.panel-body').find('.panel-tcom');
	            },
	        });
		}    
    }

    $('body').on('click', '#userTimeshiftPref', function(){
        prefbut = $(this);
        prefbut_span = prefbut.find('span:not(.hidden-sm)');
        if(prefbut.hasClass('tsOn')){
            tsid = 0;
            prefbut.removeClass('tsOn').addClass('tsOff');
            prefbut_span.text('OFF').removeClass('label-success').addClass('label-danger');
        }else{
            tsid = 1;
            prefbut.removeClass('tsOff').addClass('tsOn');
            prefbut_span.text('ON').removeClass('label-danger').addClass('label-success');
        }
        
        if(tsid == 1){
            $('.task-buttons').each(function(){
                if($(this).hasClass('canCollapse')){ $(this).hide(); }
            });
            $('.task-timeShift').removeClass('hide').fadeIn('fast');    
        }else{
            $('.task-buttons').removeClass('hide').fadeIn('fast');
            $('.task-timeShift').hide();
        }
    
        $.ajax({ url: '/users/setTimeshiftPref/'+tsid, type: 'post'});
        
        return false;
    });


	function newAddTaskModal(options){
		//console.log('options from newAddTaskModal');
		//console.log(options);
		var start = (options.start) || null;
		var lead_team = (options.lead_team) || null;
		var parent_task = (options.parent_task) || null;
		var parent_team = (options.parent_team) || null;
		var assignment = (options.assignment) || null;
		var source = (options.source) || null;
		var html;
		//var leadTeam = $('#qaLeadTeamSelect');
        
		$.get('/tasks/quickAdd/', function(data){
			var bb = bootbox.dialog({
				message:data,
				onEscape: true,
				size:'large',
				closeButton: false,
				callback: function(){
				},
			});
			
			bb.init(function(){
				var inStart = $(document).find('#qaStartTime');
		        var inEnd = $(document).find('#qaEndTime');
		        var inLead = $(document).find('#qaLeadTeamSelect');
		        var inTC = $(document).find('#qaTimeCtrl');
		        var inLP = $('#qaLinkedParentDiv').find('.linkableParentSelect');

		        if(start){
			        inStart.data('DateTimePicker').date(moment(start));
			        inEnd.data('DateTimePicker').date(moment(start));

			        updateSignature(inLead.val(), inLead, parent_team);
			        updateLinkableParents(inLead.val(), $('#qaLinkedParentDiv'), parent_task);
			        inLP.trigger('change');
			        inLead.trigger('change');

		        }else if(parent_task){
			        inLead.val(lead_team).trigger('change.select2');
			        updateSignature(lead_team, inLead, parent_team);
			        updateLinkableParents(lead_team, $('#qaLinkedParentDiv'), parent_task);
			        inLP.trigger('change');
			        inTC.trigger('change');
				}else{
			        inLead.trigger('change');
				}
			});
		});
	}        
 
	//Timeline view -- add tasks by clicking minute markers
	$('#taskListWrap').on('click', '.tl-mark', function(){
    	var this_min = $(this).data('min') || 0;
        var this_tlhr = $(this).data('tlhr') || 0;
        var opts=[];
        opts.start = moment(DB_EVENT_DATE).add(this_tlhr*60*60+this_min*60,'s').format('YYYY-MM-DD HH:mm:ss');
        newAddTaskModal(opts);
    });

    $('#taskListWrap').on('mouseenter', '.tl-mark', function(){
       $(this).css('background-color','#00861C'); 
    });

    $('#taskListWrap').on('mouseleave', '.tl-mark', function(){
    	var tm = $(this).data('min');
    	if(tm%15==0){ $(this).css('background-color','#ff0000');}
		else{$(this).css('background-color','#999');}
    });
    
	$(document).on('click', '#newTaskFromMenu', function(e){ //Authorized App Menu (top)
        e.preventDefault();
        e.stopPropagation();
		options = [];
		options.source = 'main_menu';
		newAddTaskModal(options);
	});


	function handleVOKeys(){
        var vo = $('#coViewChildren');
        if(vo.is(':checked')){
            vo.prop('checked',false);
        }
        else{
            vo.prop('checked',true);
        }        
        vo.trigger('change');
    }
        
    $(document).on('keydown', null, 'shift+z', handleVOKeys);

    
//************* EO Document.Ready();***********    
});
 
