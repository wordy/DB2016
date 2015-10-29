	function updateLinkableParents(team, update_div, current, child){
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

    function deleteTask(tid){
        if(!tid){return false;}

        $.ajax( {
            url: '/tasks/delete/'+tid,
            type: 'post',
            dataType:'json',
            beforeSend:function () {
                $('#ajaxProgress').fadeIn('fast');
            },
            success:function(data, textStatus) {
                var msg_html = '<div class=\"alert alert-success\" role=\"alert\">'+data.message+'</div>';
                $('#taskListWrap').load('/tasks/compile?src=ajax', function(response, status, xhr){
                    if(status == 'success'){
                        $('#taskListWrap').html(response);
                    }
                });
	            $('#cErrorStatus').html(msg_html).fadeIn().delay(3000).fadeOut('fast');
            },
            complete:function (XMLHttpRequest, textStatus) {
                $('#ajaxProgress').fadeOut('fast');
            }, 
        });        
    }

    function updateSignature(team, in_lead, pushed){
    	console.log('hit update sig in c.js');
    	if(!team || !in_lead){return false;}

        var lead_label = $(in_lead).parents('div.form-group').find('label');
        var partask_label = $(in_lead).parents('form').find('.linkedParentDiv').parents('div.form-group').find('label');
        var partask_list = $(in_lead).parents('form').find('.linkedParentDiv');
        var ea_tlist = $(in_lead).parents('form').find('.teamsList');
    	
    	$.ajax( {
            url: '/tasks_teams/updateSig/',
            data: {team:team},
            type: 'post',
            dataType:'html',
            beforeSend:function () {
                lead_label.append('<span class=\"ajaxSmSpin\"><img src=\"/img/ajax-sm.gif\"/></span>');
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
                lead_label.find('.ajaxSmSpin').remove();
            },
        });
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
            console.log('got to col_add had class collapse');
        }

        $('html, body').animate({
            scrollTop: $(document).find('#ajax-content-load').offset().top
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

	function bindToSelect2(element, placeholder){
		if(!placeholder){ 
			placeholder = 'Select a task to link to';
		}
		$(element).select2({
			theme: 'bootstrap',
			'width':'100%',
			//'allowClear': true,
			placeholder: placeholder,
       });
	}
	
    function getOffset(min,sec,sign){
        var offset = 60*parseInt(min) + parseInt(sec);
        if (sign == '-'){
            offset = (-1)*parseInt(offset);
        }
        return offset;
    }

    function setParentTeamAsPushed(par_team_id, tlist){
        var lpt_but = $(tlist).find('span[data-team_id='+par_team_id+']');
        if(lpt_but.hasClass('btn-ttrid0')){
            lpt_but.removeClass('btn-ttrid0').addClass('btn-ttrid2').data('tr_id', 2); 
        }
    }

	// Generic Refresh of Tasks after Compile Options change
    function updateCo(){
        var form = $('#cForm');
        var form_data = form.serialize();

		if(form){
	        $.ajax( {
	            url: '/tasks/compile/',
	            data: form_data,                
	            type: 'post',
	            dataType:'html',
	            beforeSend: function(){
	            	//form.find('.ajax_spin').fadeIn('fast');
	            	$('#coViewOpts').append('<span class="tr_spin"><img src="/img/ajax-loader_old.gif"/></span>');

	            },
	            success:function(data, textStatus){
	                $('#taskListWrap').html(data).fadeIn('fast');
	            },
	            complete: function(){
	            	$('#coViewOpts').parent().find('.tr_spin').remove();
	            }
	        });
		}
    }




$(document).ready(function () {
		
	
    $('#ajax-content-load').find('.boot-popover').hover(function(){
    	$(this).popover({
                html: true
            }).popover('show');
        }, function () {
        	$(this).popover('hide');
        });


	$('#taskListWrap').on('mouseenter', '.task-panel-heading' , function(e){
        var ucon_inv = $(this).data('uconinv');
        var addHtml = $('<span class=\"actButs\"><button style=\"margin:-5px 5px 0px 0px;\" class=\"btn btn-yh btn-xs addTask\"><i class=\"fa fa-plus-circle\"> </i> &nbsp;Add</button></span>');
        var linkHtml = $('<span class=\"actButs\"><button style=\"margin:-5px 0px 0px 0px;\" class=\"btn btn-yh btn-xs addLink\"><i class=\"fa fa-link\"> </i> &nbsp;Link</button></span>');
        var tsDiv = $(this).find('.taskTs').parent();
        addHtml.hide().appendTo(tsDiv).fadeIn('fast');
        if(ucon_inv){
            linkHtml.hide().appendTo(tsDiv).fadeIn('fast'); 
        }
    });        

	$('#taskListWrap').on('mouseleave', '.task-panel-heading' , function(e){
       $(this).parents('.task-panel').find('.actButs').remove();
    });
    
	$('#taskListWrap').on('click', 'button.addTask', function(e){
        e.preventDefault();
        var stime = $(this).parents('.task-panel').find('.task-panel-heading').data('stime');
        var s_mom = moment(stime);
        addNewTask(stime);
    });
    
    $('#taskListWrap').on('click', 'button.addLink, a.addLink', function(e){
        var tid = $(this).parents('.task-panel').find('.task-panel-heading').data('tid');
        var tm_id = $(this).parents('.task-panel').find('.task-panel-heading').data('team_id');
        var jsonCIN = $(this).parents('.task-panel').find('.jsonCIN').html();
        var jqCIN = $.parseJSON(jsonCIN);
        var opts = '';

        $('#teamAddLinkedModal').find('span.hiddenParentId').html(tm_id);
        $('#teamAddLinkedModal').find('span.hiddenTid').html(tid);
        
        // TODO: This apparently fails in IE<9
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
    });
    
	$('#taskListWrap').on('click', 'div.task-panel-heading button', function(e){
        e.stopPropagation();
    });
		
    // Fetch task details
    $('#ajax-content-load').on('click', '.task-panel-heading', function(e){
        e.preventDefault();        
        var tdheading_div = $(this);
        var tid = $(this).attr('data-tid');
        var tbody_div = $(this).parent('.task-panel').find('.taskPanelBody');
        var tbd_c = 0;
        
        if(tbody_div.html()){
            tbd_c = tbody_div.html().length;
        }
        // Details haven't been fetched
        if(tbd_c < 100){
            $.ajax( {
            url: '/tasks/details/'+tid,
                type: 'post',
                dataType:'html',
                beforeSend:function () {
                    tdheading_div.find('.tr_spin').remove();
                    tdheading_div.append('<span class="tr_spin"><img src="/img/ajax-loader_old.gif"/></span>');
                },
                success:function(data, textStatus) {
                    tbody_div.html(data).addClass('is_vis').slideDown(300);
                    var new_lpts = tbody_div.find('.linkableParentSelect');
                    bindToSelect2(new_lpts);
                },
                complete:function (XMLHttpRequest, textStatus) {
                    tdheading_div.find('.tr_spin').remove();
                },
                error: function(xhr, statusText, err){
                    var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                    $('#cErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                },
            });
        }  // Details were previously fetched, just show it again
        else if (tbd_c > 100 && !tbody_div.hasClass('is_vis')){
            tbody_div.addClass('is_vis').slideDown('slow');
        }  
        else {  //Details are visible, roll it up
            tbody_div.removeClass('is_vis').slideUp(300);
        }
        return false;
    });

    // Edit Task
    $('body').on('submit','form.formEditTask', function(e){
        e.preventDefault();
        var thisform = $(this);
        var subBut = $(this).find('.eaSubmitButton');
        var valCont = $(this).find('.eaValidationContent');
        var spinner = $(this).find('.eaSpinner');
        var pageNum = $('#pageNum').html();
        var viewSingle = $('#singleTask').html();

        if(pageNum && viewSingle == 0){
            var afterURL = '/tasks/compile?src=edit&page='+pageNum; 
        }
        else if(viewSingle != 0){
        	var afterURL = '/tasks/compile?src=edit&task='+viewSingle;
        }
        else{
            var afterURL = '/tasks/compile';
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
                subBut.val('Saving...').attr('disabled', true);
                valCont.fadeOut('fast');                
                spinner.fadeIn();
            },
            success:function(data, textStatus) {
                spinner.fadeOut('fast');
                $('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');
                // Refresh tasks list
                $('#taskListWrap').load(afterURL, function(response, status, xhr){
                    if(status == 'success'){
                        $('#taskListWrap').html(response);
                    }
                });
            },
            error: function(xhr, statusText, err){
                valCont.html(xhr.responseText).fadeIn('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                spinner.fadeOut('fast');
                subBut.val('Save Changes').attr('disabled', false);
            },
        });
        return false;
    });

    // Time Shifting
    $('#taskListWrap').on('click', '.taskTs', function(event){
        event.stopPropagation();
    });
    
    $('#taskListWrap').on('click', 'input.tsCheck', function(event){
        var this_check = $(this);
        var tid = $(this).data('tid');
        var act = 'addShift';
        
        if($(this).hasClass('checked')){
            act = 'remShift';
        }

        $.ajax( {
            url: '/tasks/'+act,
            type: 'post',
            dataType:'json',
            data: {task: +tid},
            success:function(data, textStatus) {
                if(data.success){
                    $('#userTimeshiftCount').html(data.ts_count).css({opacity: 0});
                    $('#userTimeshiftCount').animate({opacity:1},700);
                    this_check.toggleClass('checked');    
                }
            },
            error: function(xhr, statusText, err){
                var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                $('#cErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
            }
        });
    });

    // Offset Sec to Min Rollover    
    $('#ajax-content-load').on('keyup input', '.inputOffSec', function(e){
        var form = $(this).parents('form');
        var off_min = form.find('.inputOffMin');
        var off_sec = form.find('.inputOffSec');
        var off_sign = form.find('.inputOffSign');

        if(off_sec.val()==60){
            var old_val = parseInt(off_min.val());
            if(!old_val){old_val = 0;}
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
    
    // Offset
    $('#ajax-content-load').on('change', '.inputOffSec, .inputOffMin, .inputOffSign', function(){
        //console.log('got input change');
        var form = $(this).parents('form');
        var in_off_min = form.find('.inputOffMin');
        var in_off_sec = form.find('.inputOffSec');
        var in_off_sign = form.find('.inputOffSign');
        var o_m = in_off_min.val();
        var o_s = in_off_sec.val();
        var o_sign = in_off_sign.val();
        var timeCtrl = form.find('.inputTC');
        var timeOffset = $(this);
        var startTime = form.find('.inputStartTime');
        var endTime = form.find('.inputEndTime');
        var toVal = getOffset(o_m, o_s, o_sign);
        var parentTask = form.find('.linkableParentSelect');
        var pt_sel = parentTask.find('option:selected');
        var pt_start_val = pt_sel.data('stime');
        var ost = moment(startTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var oet = moment(endTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var odur = oet.diff(ost);
        var opst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss');
        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
        var net = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s').add(odur, 'ms');
        var isTc = timeCtrl.prop('checked');
        
        if(isTc){
            startTime.data('DateTimePicker').date(moment(nst));
            endTime.data('DateTimePicker').minDate(moment(nst)).date(moment(net));
        }
    });    

    //Time Ctrl
    $('#ajax-content-load').on('change', '.inputTC', function(){
        //console.log('TC change in compile');
        var form = $(this).parents('form');
        var startTime = form.find('.inputStartTime');
        var endTime = form.find('.inputEndTime');
        var to_min = form.find('.inputOffMin');
        var to_sec = form.find('.inputOffSec');
        var to_sign = form.find('.inputOffSign');
        var toVal = getOffset(to_min.val(),to_sec.val(),to_sign.val());
        var ost = moment(startTime.val());
        var parentTask = form.find('.linkableParentSelect');
        var pt_sel = parentTask.find('option:selected');
        var pt_start_val = null;
        
        if(pt_sel.data('stime')){
            pt_start_val = pt_sel.data('stime');    
        }
        var ost = moment(startTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var oet = moment(endTime.val(), 'YYYY-MM-DD HH:mm:ss');
        var odur = oet.diff(ost)
        var nst = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s');
        var net = moment(pt_start_val, 'YYYY-MM-DD HH:mm:ss').add(toVal,'s').add(odur, 'ms');
        var stHelp = form.find('.stHelpWhenTC');
        
        if($(this).prop('checked') && pt_start_val){
            //console.log('tc checked and start val');
            startTime.prop('readonly', true);
            startTime.data('DateTimePicker').date(moment(nst));
            endTime.data('DateTimePicker').minDate(moment(nst)).date(moment(net));
            to_min.prop('disabled', false);
            to_sec.prop('disabled', false);
            to_sign.prop('disabled', false);
            stHelp.removeClass('collapse');
        }
        else{
            to_min.val(0).prop('disabled', true);
            to_sec.val(0).prop('disabled', true);
            to_sign.val('-').prop('disabled', true);
            startTime.prop('readonly', false);
            stHelp.addClass('collapse');
        }
    });

    //Change Linked Parent
    $('#ajax-content-load').on('change','.linkableParentSelect', function(e){
        //console.log('chg lpt_id from compile');
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
                        partask_label.append('<span class="tr_spin"><img src="/img/ajax-loader_old.gif"/></span>');
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
    
    //Change lead
    $('#ajax-content-load').on('change','.inputLeadTeam', function(){
        //console.log('got lead change in compile');
        var leadt = $(this).val();
        var thisT = $(this); 
        var form = $(this).parents('form');
        var lead_label = $(this).parents('div.form-group').find('label');
        var partask_label = form.find('.linkedParentDiv').parents('div.form-group').find('label');
        var partask_list = form.find('.linkedParentDiv');
        var ea_tlist = form.find('.teamsList');
        var curPID =  form.find('.hiddenParId').text();
        var curTID =  form.find('.hiddenTaskId').text();    

        $.ajax( {
            url: '/tasks_teams/updateSig/',
            data: {team:leadt},
            type: 'post',
            dataType:'html',
            beforeSend:function () {
                lead_label.append('<span class="tr_spin"><img src="/img/ajax-loader_old.gif"/></span>');
            },
            success:function(data, textStatus) {
                ea_tlist.html(data).fadeIn('fast');
                $('#qaReqAllBut, #qaPushAllBut').trigger('change');
                if(leadt){
                    updateLinkableParents(leadt, partask_list, curPID, curTID);
                    console.log('updating sig due to lead change in compile.js'); 
                }
            },
            error: function(xhr, statusText, err){
                var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                $('#eaErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                lead_label.find('.tr_spin').remove();
            },
        });
    });    
    
    // TTButtons
    $('body').on('click','div.teamsList .tt-btn', function(){
        var trid = $(this).data('tr_id');
        if(trid == 0){
            if($(this).hasClass('btn-ttrid0')){
                $(this).removeClass('btn-ttrid0').addClass('btn-ttrid2').data('tr_id', 2);
            }
        }
        else if(trid == 2){
            if($(this).hasClass('btn-ttrid2')){
                $(this).removeClass('btn-ttrid2').addClass('btn-danger').data('tr_id', 3);    
            }
        }
        else if(trid == 3){
            if($(this).hasClass('btn-danger')){
                $(this).removeClass('btn-danger').addClass('btn-success').data('tr_id', 4);    
            }
        }
        else if(trid == 4){
            if($(this).hasClass('btn-success')){
                $(this).removeClass('btn-success').addClass('btn-ttrid0').data('tr_id', 0);    
            }
        }
    });

    $('#ajax-content-load').on('click', '.ban-edit', function(e){
        e.preventDefault();
        e.stopPropagation();
    });
    
    
	$('#ajax-content-load').on('mouseenter', 'label.taskTimeshift', function(){
        var val = $(this).parent().find('input').prop('disabled');
        if(val)
            $(this).css('cursor', 'not-allowed');
    });    
    
    // TT Button role changes from compile 
    $('#ajax-content-load').on('click', 'span.btn-xxs:not(.ban-edit)', function(e){
    	console.log('hit tt role change in c.js');
        e.stopPropagation();
        var this_but = $(this);
        var tdheading_div = $(this).closest('div.task-panel-heading');
        var task_id = tdheading_div.data('tid');
        var team_id = this_but.data('teamid');
        var role_id = '';
            
        if(this_but.hasClass('openTeam')){
            role_id = 4;
            this_but.removeClass('btn-danger openTeam').addClass('btn-success closeTeam');
        }
        else if(this_but.hasClass('closeTeam')){
            role_id = 2;
            this_but.removeClass('btn-success closeTeam').addClass('btn-ttrid2 pushTeam');
        }    
        else if(this_but.hasClass('pushTeam')){
            role_id = 3;
            this_but.removeClass('btn-ttrid2 pushTeam').addClass('btn-danger openTeam');
        }    

        if((task_id!=null) && (team_id!=null) && (role_id!=null) ){                                            
            $.ajax( {
                url: '/tasks_teams/chgRole/',
                data: {'task':task_id, 'team':team_id, 'role':role_id},                
                type: 'post',
                dataType:'json',
                beforeSend:function () {
                    tdheading_div.append('<span class="tr_spin"><img src="/img/ajax-loader_old.gif"/></span>');
                },
                complete:function (XMLHttpRequest, textStatus) {
                    tdheading_div.find('.tr_spin').remove();
                },
                error: function(xhr, statusText, err){
                    var res_j = $.parseJSON(xhr.responseText);
                    var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+res_j.message+'</div>';
                    $('#cErrorStatus').html(msg).fadeIn('fast').delay(7000).fadeOut();
                },
            });
        }
    });
    
    /*
     * COMPILE OPTIONS
     */
    function sortState(state){
        var c_sort = $('.coSort');
        var s_disabled = false;
        if(state == 'disable'){
            s_disabled = true;
        }
        c_sort.each(function(i,e){
            $(e).prop('disabled', s_disabled);
        });    
    }

    $('#coTeams').multiselect({
        includeSelectAllOption: true,
        enableClickableOptGroups: true, 
        buttonClass: 'btn btn-info', 
        buttonWidth: '100%',
        numberDisplayed: 5,
        maxHeight: 200,
        onChange: function(element, checked) {
            //$('#coStartDate').trigger('change');
            //updateCo();
        }
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
            help_str+= '<b>Rundown View</b><ul><li>Default setting. Shows any tasks involving the selected Teams, sorted by ascending or descending Start Time.</li>';
            help_str+= '<li>Use View Options to control what\'s shown.</li><li>Ordered by <u>ascending</u> or <u>descending</u> Start Time</li></ul>';
            sortState('enable');        
        }
        else if($(this).val() == 10){
            help_str+= '<b>Lead Only</b><ul><li>Shows only tasks where the selected Team(s) are the lead.</li>';
            help_str+= '<li>Hides tasks from all other teams.</li><li>Useful for focusing on a single team\'s tasks.</li><li>Ordered by <u>ascending</u> or <u>descending</u> Start Time</li></ul>';
            sortState('enable');        
        }
        else if($(this).val() == 30){
            help_str+= '<b>Open Requests <u>From</u> Other Teams (Owing)</b><ul><li>Listing of everything owed to other teams by the selected Teams.</li>';
            help_str+= '<li>Useful for tracking what your team owes other teams.</li><li>Fetches tasks from <u>all dates</u>.</li><li>Ordered by <u>ascending</u> or <u>descending</u> Start Time</li></ul>';
            sortState('enable');        
        }
        else if($(this).val() == 31){
            help_str+= '<b>Open Requests <u>To</u> Other Teams (Waiting)</b><ul><li>Tasks where selected Teams requested help from other teams, and request is still Open.</li>';
            help_str+= '<li>Useful for tracking what other teams owe your team.</li><li>Fetches tasks from <u>all dates</u>.</li><li>Ordered by <u>ascending</u> or <u>descending</u> Start Time</li></ul>';
            sortState('enable');        
        }
        else if($(this).val() == 500){
            help_str+= '<b>Action Items</b><ul><li>Tasks that are important to the entire Ops Team.</li>';
            help_str+= '<li>Often take place over multiple weeks and progress needs to be tracked. Ex: Calling volunteers/submitting inventory requests</li><li>Fetches tasks from <u>all dates</u>.</li><li>Ordered by <u>ascending due date</u> to highlight upcoming due tasks.</li></ul>';
            sortState('disable');        
        }
        else if($(this).val() == 100){
            help_str+= '<b>Recently Modified</b><ul><li>Shows most recently modified tasks.</li>';
            help_str+= '<li>Useful to see recent changes to your team\'s plan.</li><li>Fetches tasks from <u>all dates</u>.</li><li>Ordered by <u>descending modified date</u> (most recently changed first).</li></ul>';
            sortState('disable');
        }
        help.html(help_str);
    });   
    
    $('#coViewList input:checked').trigger('change');
    $('#cForm').on('change', 'input:not(.coDateRange)', updateCo);
    
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
        var hhtml = '<i class=\"fa fa-list-ol\"></i> View Rundown';
        var shtml = '<i class=\"fa fa-indent\"></i> View Threaded';                                   
        
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
    
    
    /*
     * Quick Add
     */
    
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
    
	$('#qaInputDetails').summernote({
		height: 150,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']],
            ['misc', ['undo','redo','help']],
        ]
    });    
    
$('#qaForm').on('submit', function(e){
            e.preventDefault();
            var subBut = $(this).find('.qaSubmitButton');
            var valCont = $(this).find('.qaValidationContent');
            var spinner = $(this).find('.qaSpinner');
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
            
            $.ajax( {
                url: $(this).attr('action'),
                type: 'post',
                data: $(this).serialize(),
                dataType:'html',
                beforeSend:function () {
                    subBut.val('Saving...');
                    valCont.fadeOut('fast');                
                    spinner.fadeIn();
                },
                success:function(data, textStatus) {
                    $('#qaForm').trigger('reset');
                    $('#qaInputDetails').code('');
                    var dstr = moment().format('YYYY-MM-DD HH:mm:00');
                    var dmom = moment(dstr, 'YYYY-MM-DD HH:mm:ss');
                    $('#qaStartTime').data('DateTimePicker').date(moment(dmom));
                    $('#qaLeadTeamSelect').trigger('change');  
                    spinner.fadeOut('fast');
                    $('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');
    
                    // Refresh tasks list
                    $('#taskListWrap').load('/tasks/compile?src=action', function(response, status, xhr){
                        if(status == 'success'){
                            $('#taskListWrap').html(response);
                        }
                    });
                },
                error: function(xhr, statusText, err){
                    valCont.html(xhr.responseText).fadeIn('fast');
                },                
                complete:function (XMLHttpRequest, textStatus) {
                    spinner.fadeOut('fast');
                    subBut.val('Add Task');
                },
            });
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
            if(!lteam)
            {
                $(this).attr('disabled', 'disabled');
            }else{
                $(this).removeAttr('disabled');
            }
        });        
    
 

	
/*
 * Modals
 * 
 */
   
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

    $('#deleteTaskModal').on('show.bs.modal', function(e) {
        var doDel = $(this).find('.btn-doDelete');
        var task_id_span = $(this).find('#deleteTaskId');
        var task_id = task_id_span.html();
        //var pageNum = $('body').find('#pageNum').html();
        
        doDel.on('click', function(e){
            if(task_id){
                deleteTask(task_id);
                $('#deleteTaskModal').modal('hide');
            }
        });
    });
 

/*
 * EDIT
 * 
 */    
   
	
    
    
// EO Document.Ready();    
    

});
