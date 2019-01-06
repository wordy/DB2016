$(document).ready(function () {
	
		// Handlers for save task and cancel buttons


	$('body').on('click', 'button.qaSubmitButton', function(e){ //QA modal
		var action = $(this).data('action');
		var opts = [];
		opts.button = $(this);
		
		if(action == 'addAndClose'){
			opts.after = 'close';
			ajaxAddTask(opts);
		}else if(action == 'addAndReset'){
			opts.after = 'reset';
			ajaxAddTask(opts);
		}
		return false;
	})

	$('body').on('click', '.qaCancelButton', function(e){  //QA modal
		bootbox.hideAll();
		return false;
	})
 

 
 
 
 
	// Save task via ajax
	function ajaxAddTask(options){
		//console.log('hit addTask');
		var after = (options.after) || null;
		var button = (options.button) || form.find('.qaSubmitButton');
		var button_txt = button.text();
		var form = $('#qaForm');
        var valCont = form.find('.qaValidationContent');
        var spinner = $('#global-busy-indicator');
		
        $('#qaNewTeamsList').find('.tt-btn').each(function(){
            var nteam = $(this).data('team_id');
            var ntr = $(this).data('tr_id');
            $('<input>').attr({type: 'hidden', name: 'data[TeamRoles]['+nteam+']', value: ntr}).appendTo('#qaForm');
        });
        
        $.ajax( {
            url: form.attr('action'), type: 'post', data: form.serialize(), dataType:'html',
            beforeSend:function () {
                button.html('<i class="fa fa-cog fa-spin"></i> Saving...').attr('disabled', true);
                valCont.fadeOut('fast');                
            },
            success:function(data, textStatus) {
        		var get_url = '/tasks/compile?scr=action';
        		if(after == 'close'){
	                $('#taskListWrap').load(get_url, function(response, status, xhr){ // Refresh tasks list
	                    if(status == 'success'){ $('#taskListWrap').html(response);}
                    });
        			bootbox.hideAll();	
        			$('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');
        		}else if(after == 'reset'){
        			$('#qaForm').trigger('reset');	
	                $('#qaInputDetails').code('');
	                var dstr = moment().format('YYYY-MM-DD HH:00:00');
	                var dmom = moment(dstr, 'YYYY-MM-DD HH:mm:ss');
	                $('#qaStartTime').data('DateTimePicker').date(moment(dmom));
	                $('#qaEndTime').data('DateTimePicker').date(moment(dmom));
	                $('#qaLeadTeamSelect').trigger('change');
	                $('#qaAssignSelect').select2("val", "");
	              	$('#qaForm').find('.linkableParentSelect').val(0).trigger('change');
					valCont.html('<div class="col-xs-6 col-xs-offset-3"><div class="alert alert-success"><b><i class="fa fa-check"></i> OK</b> task was saved.</div></div>').fadeIn().delay(3000).fadeOut('fast');
        		}
            },
            error: function(xhr, statusText, err){
                valCont.html(xhr.responseText).fadeIn('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                button.html('<i class="fa fa-plus"></i> '+button_txt).prop('disabled', false);
            },
        });		
	}
	
	//Start-End
    $('body').on('dp.change', '.inputStartTime, .inputEndTime', function(e){
    	//console.log('hit dp.chg of s/e time in at.js');
        var inStart = $(this).parents('form').find('.inputStartTime');
        var inEnd = $(this).parents('form').find('.inputEndTime');
        
        if(inStart.data('DateTimePicker') != undefined && inEnd.data('DateTimePicker') != undefined){
	        var startTime = inStart.data('DateTimePicker').date();
	        var endTime = inEnd.data('DateTimePicker').date();
	        var diff = endTime.diff(startTime); 
			if(diff == 0){ $(this).parents('form').find('.endTimeLabel').html('&nbsp;(<b>Duration: None</b>)');
		    }else if(diff > 0 && diff <86400000){
	            //var dstr = moment.utc(diff).format('HH:mm:ss');
				var hmspace = '';
	            var dhr = moment.utc(diff).format('HH');
	            var dmin = moment.utc(diff).format('mm');
	            var hourtext = dhr+' hr';
	            var mintext = dmin+' min';
	            
	            if(dhr == 0){hourtext = '';}
            	else if(dhr == 1){hourtext = dhr+' hr';}
	            else if(dhr > 1){hourtext = dhr+' hrs';}
	            
	            if(dmin == 0){mintext = '';}
            	else if(dmin == 1){mintext = dmin+' min';}
	            else if(dmin > 1){mintext = dmin+' mins';}
	            
	            if(dmin != 0){hmspace = ' ';}
				
				if(dmin == 0 && dhr == 0){ hmspace = 'None'; }
	            //var diff_msg = '(<b>Duration: '+dstr+'</b>)';
	            var diff_msg = '&nbsp;(<b>Duration: '+hourtext+hmspace+mintext+'</b>)';
	            $(this).parents('form').find('.endTimeLabel').html(diff_msg);
	            }else if(diff >= 86400000){ $(this).parents('form').find('.endTimeLabel').html('&nbsp;(<b>Duration: \>1 Day</b>)'); }
        }
    });
    
    $('body').on('dp.change', '.inputStartTime', function (e) {
    	//console.log('got dp.change inputStart in at.js');
        var inStart = $(this).parents('form').find('.inputStartTime');
        var inEnd = $(this).parents('form').find('.inputEndTime');

        if(inStart.data('DateTimePicker') != undefined && inEnd.data('DateTimePicker') != undefined){
	        var startTime = inStart.data('DateTimePicker').date();
	        var endTime = inEnd.data('DateTimePicker').date();
	        var diff = endTime-startTime;
	        
	        if(e.oldDate != undefined){
		        var old_start = e.oldDate;
		        var new_start = e.date;
		        if(new_start && old_start){
			        var delta = new_start.diff(old_start);
			        var old_dur = endTime.diff(e.oldDate);
			        if(old_dur != 0){
			            inEnd.data('DateTimePicker').minDate(e.date);
			            inEnd.data('DateTimePicker').date(endTime.add(delta));      
			        }
		        }
            }
			if(endTime){ inEnd.data('DateTimePicker').minDate($(this).data('DateTimePicker').date()); }
		}
    });   
    
    $('body').on('dp.change', '.inputEndTime', function (e) {
    	//console.log('got dp.change inputEnd in at.js');
        var inStart = $(this).parents('form').find('.inputStartTime');
        var inEnd = $(this).parents('form').find('.inputEndTime');

        if(inStart.data('DateTimePicker') != undefined && inEnd.data('DateTimePicker') != undefined){
	        var startTime = inStart.data('DateTimePicker').date();
	        var endTime = inEnd.data('DateTimePicker').date();
	        var diff = endTime-startTime;
	        
	        if(e.oldDate != undefined){
		        var old_end = e.oldDate;
		        var new_end = e.date;
		        var delta = new_end.diff(old_end);
		        var old_dur = e.oldDate.diff(startTime);
		
		        if(old_dur != 0){
		            inEnd.data('DateTimePicker').minDate(startTime);      
		        }
	        }
		}
    });   

    //Time Control
    $('body').on('change', '.inputTC', function(){
    	//console.log('got chg of inputTC in at.js');
        var form = $(this).parents('form');
        var startTime = form.find('.inputStartTime');
        var endTime = form.find('.inputEndTime');
        var parentTask = form.find('.linkableParentSelect');
        var pt_sel = parentTask.find('option:selected');
        var in_off_min = form.find('.inputOffMin');
        var in_off_type = form.find('.inputOffType');
        var to_min = in_off_min.val();
        var to_type = in_off_type.val();
		var toVal = 60*parseInt(to_min);
        var ost = moment(startTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var oet = moment(endTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var odur = oet.diff(ost)
		var stHelp = form.find('.stHelpWhenTC');
        var pt_start_val = pt_sel.data('stime') || null;    
        var pt_end_val = pt_sel.data('etime') || null;    

        if(to_type ==-1 || to_type ==0){
	        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s');
	        var net = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s').add(odur, 'ms');
        }else if (to_type == -2) {
	        var nst = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s');
	        var net = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s').add(odur, 'ms');
        }else if (to_type == 1){
	        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
	        var net = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s').add(odur, 'ms');
        }else if(to_type == 2){
	        var nst = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
	        var net = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s').add(odur, 'ms');
        }
        
        if($(this).prop('checked') && pt_start_val){
            //console.log('tc checked and start val');
            startTime.prop('readonly', true);
            if(startTime.data('DateTimePicker') != undefined){
            	startTime.data('DateTimePicker').date(moment(nst));
            }
            if(endTime.data('DateTimePicker') != undefined){
	            endTime.data('DateTimePicker').minDate(moment(nst)).date(moment(net));
            }

            in_off_min.prop('disabled', false);
            in_off_type.prop('disabled', false);
            stHelp.removeClass('collapse');
        }else{
            in_off_min.val(0).prop('disabled', true);
            in_off_type.prop('disabled', true);
            startTime.prop('readonly', false);
            stHelp.addClass('collapse');
        }
    });

    // Change Start/End/Offset
    $('body').on('change', '.inputOffSec, .inputOffMin, .inputOffType', function(){
    	//console.log("got chg of inoff in at.js");
        var form = $(this).parents('form');
        var in_off_min = form.find('.inputOffMin');
        //var in_off_sec = form.find('.inputOffSec');
        var in_off_type = form.find('.inputOffType');
        var to_min = in_off_min.val();
        //var to_sec = in_off_sec.val();
        var to_type = in_off_type.val();
        var timeCtrl = form.find('.inputTC');
        var timeOffset = $(this);
        var startTime = form.find('.inputStartTime');
        var endTime = form.find('.inputEndTime');
        //var toVal =  60*parseInt(to_min) + parseInt(to_sec);
        var toVal =  60*parseInt(to_min);
        //console.log('toval ' +toVal);
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
        }else if (to_type == -2) {
	        var nst = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').subtract(toVal,'s');
	        var net = moment(nst).add(odur, 'ms');
        }else if (to_type == 1){
	        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
	        var net = moment(nst).add(odur, 'ms');
        }else if(to_type == 2){
	        var nst = moment(pt_end_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
	        var net = moment(nst).add(odur, 'ms');
        }

        if(isTc){
            startTime.data('DateTimePicker').date(moment(nst));
            endTime.data('DateTimePicker').minDate(moment(nst)).date(moment(net));
        }
    });    

    //Change lead
    $('body').on('change','.inputLeadTeam', function(e){
        var leadt = $(this).val();
        var form = $(this).parents('form');
        var ea_tlist = form.find('.teamsList');
        var inLP = form.find('.linkableParentSelect');
        var lpValue = inLP.val();
        var spinner = $('#global-busy-indicator');
        var partask_list = form.find('.linkedParentDiv');
        
        if(leadt){
	        $.ajax( {
	            url: '/tasks_teams/updateSig/', data: {team:leadt}, type: 'post', dataType:'html',
	            beforeSend:function () {
	                spinner.fadeIn('fast');
	            },
	            success:function(data, textStatus) {
	                ea_tlist.html(data).fadeIn('fast');
					//console.log('updating sig due to lead change in at.js');
					//console.log('old selected val: '+lpValue);
                    updateLinkableParents(leadt, partask_list, lpValue);
	            },
	            error: function(xhr, statusText, err){
	                var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
	                $('#eaErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
	            },                
	            complete:function (XMLHttpRequest, textStatus) {
	                spinner.fadeOut('fast');
	            },
	        });

	        inLP.prop('disabled', false);
        }else{
        	ea_tlist.html('');
        	inLP.val(0);
        	inLP.prop('disabled', true);
        	partask_list.find('button.pidClearBut').trigger('click');
        }
    });    

    //Change Linked Parent
    $('body').on('change','.linkableParentSelect', function(e){
        //console.log('chg linkedparentselect chg in at.js');
        var this_sel = $(this);
        var sel_par = $(this).val();
        //console.log('selected parent '+sel_par);
        var form = this_sel.parents('form');
        var this_clear_parent_but = $(this).parents('.form-group').find('.pidClearBut');
        var tc = form.find('.inputTC');
        var start = form.find('.inputStartTime');
        var stHelp = form.find('.stHelpWhenTC');
        var to_min = form.find('.inputOffMin');
        var to_sec = form.find('.inputOffSec');
        var to_type = form.find('.inputOffType');
        var advpid = form.find('.advancedParent');
        var curTID =  form.data('tid');
        var partask_label = $(this).parents('.form-group').find('label');

        to_min.val(0).prop('disabled', true);
        to_sec.val(0).prop('disabled', true);        
        to_type.prop('disabled', true);
        tc.prop('checked', false);
        
        if(!sel_par){
            advpid.addClass('collapse');
            start.prop('readonly',false);
            this_clear_parent_but.addClass('disabled');
            stHelp.addClass('collapse');
            $(this).prop('readonly', false);
            to_min.val(0).prop('disabled', true);
            to_sec.val(0).prop('disabled', true);        
        }else{
            advpid.removeClass('collapse');
            this_clear_parent_but.removeClass('disabled');
            start.prop('readonly', false);
            stHelp.addClass('collapse');
            var no_tc_html = '<div class=\"alert alert-danger par_disallow\"><i class=\"fa fa-lg fa-exclamation-triangle\"></i> <b>Cannot Link to The Selected Task</b><br/>The task you\'re attempting to link to already links <u>back</u> to this task, possibly through one or more intermediate tasks. Please select a different task to link to. <br><br>This can often be resolved by changing the linked task to something with higher priority, like a Production item or Chair task.</div>';
            tc.prop('checked', false);
            tc.prop('disabled', false);
                            
            if(sel_par && curTID){
                //console.log('check PID from compile');
                $.ajax( {
                    url: '/tasks/checkPid/', data: {task:curTID, parent:sel_par}, type: 'post', dataType:'json',
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
                            to_type.prop('disabled', true);
                            advpid.parent().append(no_tc_html);
                        }else{ tc.prop('disabled', false);}
                    },
                    complete:function (XMLHttpRequest, textStatus) {
                        partask_label.find('.tr_spin').remove();
                    },
                });
            }else{ // No curTID (i.e. adding new) or parent val selected
                to_min.val(0).prop('disabled', true);
                to_sec.val(0).prop('disabled', true);        
                to_type.prop('disabled', true);
            }
        }
    });

	// Unlink a parent task
	$('body').on('click', 'button.pidClearBut', function(e){
        e.preventDefault();
        if(!$(this).hasClass('disabled')){
            var p_sel = $(this).parents('div.form-group').find('.linkableParentSelect');
            p_sel.val('').trigger('change');
        }
    });

    // Edit Task
    $('body').on('submit','form.formEditTask', function(e){
        //console.log('form edit task');         
        e.preventDefault();
        var thisform = $(this);
        var subBut = thisform.find('.eaSubmitButton');
        var valCont = $(this).find('.eaValidationContent');
        var spinner = $('#global-busy-indicator');
        var viewSingle = $('#singleTask').html();
		var is_single = $.urlParam('task'); 
        if(is_single){
    		//window.location = '/tasks/compile/';
    		var get_url = '/tasks/compile?task='+is_single;	
    	}else{
    		var get_url = '/tasks/compile?scr=action';
    	}

        // Grab state from teams' buttons; save as hidden inputs            
        $(this).find('.tt-btn').each(function(){
            var nteam = $(this).data('team_id');
            var ntr = $(this).data('tr_id');
            $('<input>').attr({ type: 'hidden', name: 'data[TeamRoles]['+nteam+']', value: ntr}).appendTo(thisform);
        });
            
        $.ajax( {
            url: $(this).attr('action'), type: 'post', data: $(this).serialize(), dataType:'html',
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

	// Tasks-Teams -- Push and Request from all
	$('body').on('click', '.tt-push-all', function(e){
		e.preventDefault();
		var rabut = $(this).parent('div').find('.tt-request-all');
		var ptxt = $(this).find('span.pushTxt');
		var rtxt = rabut.find('span.reqTxt');        

        if($(this).prop('disabled') == false || $(this).prop('disabled') == undefined){
            if(ptxt.text() == 'ALL'){
                rtxt.text('ALL');
                ptxt.text('NONE');
                $(this).parents('div').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 2).removeClass('btn-danger btn-success btn-ttrid0').addClass('btn-ttrid2');
                });
            }else {
                ptxt.text('ALL');
                rtxt.text('ALL');
                $(this).parents('div').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 0).removeClass('btn-danger btn-success btn-default btn-ttrid2').addClass('btn-ttrid0');
                });
            }
        }
    });

	$('body').on('click', '.tt-request-all', function(e){
		e.preventDefault();
		var pabut = $(this).parent('div').find('.tt-push-all');
		var rtxt = $(this).find('span.reqTxt');
		var ptxt = pabut.find('span.pushTxt');        

        if($(this).prop('disabled') == false || $(this).prop('disabled') == undefined){
            if(rtxt.text() == 'ALL'){
                rtxt.text('NONE');
                ptxt.text('ALL');
                $(this).parents('div').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 3).removeClass('btn-default btn-success btn-ttrid0 btn-ttrid2').addClass('btn-danger');
                });
            }else {
                rtxt.text('ALL');
                $(this).parents('div').find('.tt-btn:not(.btn-ttrid1)').each(function(k, val){
                    $(this).data('tr_id', 0).removeClass('btn-danger btn-success btn-default btn-ttrid2').addClass('btn-ttrid0');
                });
            }
        }
    });

    // TTButtons - display only toggle
    $('body').on('click','div.teamsList .tt-btn', function(){
    	//console.log('got tt-btn in c.js');
        var trid = $(this).data('tr_id');
        if(trid == 0){
            if($(this).hasClass('btn-ttrid0')){
                $(this).removeClass('btn-ttrid0').addClass('btn-ttrid2').data('tr_id', 2);
            }
        }else if(trid == 2){
            if($(this).hasClass('btn-ttrid2')){
                $(this).removeClass('btn-ttrid2').addClass('btn-danger').data('tr_id', 3);    
            }
        }else if(trid == 3){
            if($(this).hasClass('btn-danger')){
                $(this).removeClass('btn-danger').addClass('btn-success').data('tr_id', 4);    
            }
        }else if(trid == 4){
            if($(this).hasClass('btn-success')){
                $(this).removeClass('btn-success').addClass('btn-ttrid0').data('tr_id', 0);    
            }
        }
    });

    // Timeshift Unit
    $('#taskListWrap').on('click', 'button.tsUnitBtn', function(){
        curVal = $(this).text();
        newVal = '';
        
        if(curVal == 'Min'){
            $(this).text('Hr')
            newVal = 'Hr';            
        }else if(curVal == 'Hr'){
            $(this).text('Min');
            newVal = 'Min';
        }
        $.ajax({url: '/users/setTimeshiftUnit/'+newVal, type: 'post'});
    });
	
	// Save Timeshift Change
    $('#taskListWrap').on('click', 'button.tsSaveBtn', function(){
        //console.log('got TS save but');
        var thisBtn = $(this);
        var tsMagIn = $(this).closest('.task-timeShift').find('input');
        var tsUnitBtn = $(this).closest('.task-timeShift').find('button.tsUnitBtn');
        var tsUnitBtnVal = tsUnitBtn.text();
        var taskid = $(this).closest('.task-panel').data('task_id');
        var shift_secs = 0;
       
        if(tsUnitBtnVal == 'Min'){ shift_secs = 60*tsMagIn.val();
        }else if(tsUnitBtnVal == 'Hr'){ shift_secs = 60*60*tsMagIn.val(); }
        
        if(taskid > 0 && shift_secs != 0){
            $.ajax({
                url: '/tasks/timeshiftTask', data: {'task_id':taskid, 'secs':shift_secs}, type: 'post',
                beforeSend:function () {
                    thisBtn.prop('disabled', true);
                    thisBtn.html('<i class=\"fa fa-cog fa-spin fa-lg\"></i> Saving...');
                },
                success: function(){
                    $('#taskListWrap').load('/tasks/compile?src=ajax', function(response, status, xhr){
                        if(status == 'success'){
                            $('#taskListWrap').html(response);
                        }
                    });
                }
            });
        }
    });    

    // Delete Task - From Edit - Button handler
    $('body').on('click', '.eaTaskDeleteButton', function(){
    	//console.log('got delete click in at.js');
        var tid = $(this).data('tid') || null;
        var desc = $(this).data('desc') || null;
        var opts = [];
        
        if(tid && desc){
	        opts.task_id = tid;
	        opts.task_description = desc;
			newDeleteTaskModal(opts);        	
        }
	});

	function newDeleteTaskModal(options){
		//console.log('options from newDeleteTaskModal');
		//console.log(options);
		var task_id = (options.task_id) || null;
		var task_description = (options.task_description) || null;
		var html='<p>Are you sure you wish to delete this task:</p><p><strong>'+task_description+'</strong></p><p>If you choose to continue:</p><ul><li>All teams &amp; assignments will be removed</li><li>Any linked tasks will be unlinked (but not deleted)</li><li>Any requests (open or closed) to other teams will be removed</li></ul><p>Deleting tasks is <b>permanent</b> and <u>cannot be undone!</u></p><button type="button" class="btn btn-lg btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button> <button type="button" data-tid="'+task_id+'" class="btn btn-lg btn-danger btn-doTaskDelete"><i class="fa fa-trash-o"></i> Delete Task</button></p>';

			var bb = bootbox.dialog({
				title: '<i class="fa fa-warning fa-lg"></i> Task Deletion Warning',
				message:html,
				onEscape: true,
				backdrop: true,
				size:'small',
				closeButton: false,
			});
	}
		
	$('body').on('click', 'button.btn-doTaskDelete', function(e){
		//console.log('got del');
		var tid = $(this).data('tid');
		//console.log(tid);
		deleteTask(tid);
		bootbox.hideAll();
	});

    function deleteTask(tid){
    	var spinner = $('#global-busy-indicator');
        if(tid>0){
	        $.ajax( {
	            url: '/tasks/delete/'+tid, type: 'post', dataType:'json', 
	            beforeSend:function () { spinner.fadeIn('fast'); },
	            success:function(data, textStatus) {
	                var msg_html = '<div class=\"alert alert-success\" role=\"alert\">'+data.message+'</div>';
	                $('#taskListWrap').load('/tasks/compile?src=ajax', function(response, status, xhr){
	                    if(status == 'success'){
	                    	//console.log('doing refresh after delete');
	                        $('#taskListWrap').html(response);
	                    }
	                });
		            $('#cErrorStatus').html(msg_html).fadeIn().delay(3000).fadeOut('fast');
	            },
	            complete:function (XMLHttpRequest, textStatus) { spinner.fadeOut('fast'); }, 
	        });        
        }
        return false;
    }



	
//*****************************    
}); // EO Document.Ready();
//****************************
	// Binding of form inputs to plugins
	function bindToSelect2(element, placeholder){
		//console.log('hit b2select2 in add_task.js');
		$(element).select2({ theme: 'bootstrap', 'width':'100%', placeholder: placeholder, //minimumResultsForSearch: Infinity,
			//'allowClear': true,
       });
	}
	
	function bindStartEndToDTP(element){
		$(element).datetimepicker({ sideBySide: true, showTodayButton: true, allowInputToggle: true, format: 'YYYY-MM-DD HH:mm', 
	        //format: 'YYYY-MM-DD HH:mm:ss',
    	}); 
	}

	function bindDateOnlyToDTP(element){
		$(element).datetimepicker({
	        sideBySide: true, showTodayButton: true, allowInputToggle: true, format: 'YYYY-MM-DD', 
    	}); 
	}

	function bindSummernoteStd(element){
		//console.log('hit bind summernote in add_task.js');
		if(element){
			var ele = $(element);
			ele.summernote({
				//disableDragAndDrop: true,
	            height: 100, placeholder:'Task details',
	            toolbar: [
	                ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
	                ['para', ['ul', 'ol']],
	                ['insert', ['link']],
	                ['misc', ['undo','redo','help']],
	            ]
			});
		}
	}



	//TODO: this should be done in JS
    function updateSignature(team, in_lead, pushed){
    	//console.log('hit update sig in at.js');
    	if(!team || !in_lead){return false;}
        var tlist = $(in_lead).parents('form').find('.teamsList');
        var spinner = $('#global-busy-indicator');

    	$.ajax( {
            url: '/tasks_teams/updateSig/',  data: {team:team},  type: 'post', dataType:'html',
            beforeSend:function () { spinner.fadeIn('fast'); },
            success:function(data, textStatus) {
                tlist.html(data).fadeIn('fast');
                $('#qaReqAllBut, #qaPushAllBut').trigger('change');
                if(pushed){ setParentTeamAsPushed(pushed, $('#qaNewTeamsList')); }
            },
            error: function(xhr, statusText, err){ $('#eaErrorStatus').stop().html('<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>').fadeIn('fast').delay(3000).fadeOut('fast'); },                
            complete:function (XMLHttpRequest, textStatus) { spinner.fadeOut('fast'); },
        });
    }

	// Update list of linkable tasks when changing lead
	function updateLinkableParents(team, update_div, current_sel, child){
		if(!team || !update_div){return false;}
		//console.log('got up link par in at.js');
        $.ajax( {
            url: '/tasks/linkable/', data: {team:team, current:current_sel, child:child}, type: 'post', dataType:'html',
            success: function(data, textStatus){
                update_div.html(data).fadeIn('fast');
                var new_lps = update_div.find('.linkableParentSelect');
                bindToSelect2(new_lps);
                new_lps.val(current_sel);
                new_lps.trigger('change');
            },
        });
    }

    function setParentTeamAsPushed(par_team_id, tlist){
        var lpt_but = $(tlist).find('span[data-team_id='+par_team_id+']');
        if(lpt_but.hasClass('btn-ttrid0')){ lpt_but.removeClass('btn-ttrid0').addClass('btn-ttrid2').data('tr_id', 2); }
    }

	$.urlParam = function(name){ 	// Awesome: http://stackoverflow.com/a/25359264/1279639
	    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
	    if (results==null){ 
	    	return null;
    	}else{ return results[1] || 0; }
	}

	

	function newViewTaskModal(options){
		//console.log('options from newViewTaskModal');
		//console.log(options);
		var task_id = (options.task_id) || null;
		var view = (options.view) || null;
		var source = (options.source) || null;
		var html;

		if(task_id){
			$.get('/tasks/details/'+task_id, function(data){
				var bb = bootbox.dialog({
					message:data,
					onEscape: true,
					backdrop:true,
					size:'large',
					closeButton: true,
					callback: function(){
					},
				});
			
			bb.init(function(){});
		});			
		}
	}
	







