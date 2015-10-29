<?php
    //echo $this->Html->css('bootstrap-wysihtml5', array('inline'=>false));
    //echo $this->Html->script('bootstrap3-wysihtml5', array('inline'=>false));

    if (AuthComponent::user('id')){
        //$controlled_teams = AuthComponent::user('Teams');
        $user_role = AuthComponent::user('user_role_id');
    }
        
    $show_details = $this->request->data('Task.show_details');
    
    //$tlist = Hash::combine($teams,'{s}.{n}','{s}.{n}');
    
    $tlist=array();
    foreach ($teams as $zone){
        foreach($zone as $tid=>$code){
            $tlist[$tid] = $code;
        } 
    }
    
    //debug($cSettings);
    
    $cs_teams=array();
    
    if(!empty($cSettings['Teams'])){
        foreach ($cSettings['Teams'] as $tid){
            $cs_teams[] = $tlist[$tid];
        }
    }
    
    //debug($tlist3);
    
        $this->Js->buffer("
        
        $('span.btn-xxs').on('click', function(e){
            return false;
        });
        
        $('span.btn-xxs').on('click', function(e){
            //tdheading_div.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
            //var spin = tdheading_div.find('span.tr_spin');
            //spin.delay(3000).fadeOut();
            
            
                
            var tdheading_div = $(this).closest('div.task-panel-heading');
            var task_id = tdheading_div.data('tid');
            var team_id = $(this).data('teamid');
            var role = '';
                
            if($(this).hasClass('openTeam')){
                role_id = 4;
                $(this).removeClass('btn-danger openTeam').addClass('btn-success closeTeam');
            }
             
            else if($(this).hasClass('closeTeam')){
                role_id = 2;
                $(this).removeClass('btn-success closeTeam').addClass('btn-default pushTeam');
            }    

            else if($(this).hasClass('pushTeam')){
                role_id = 3;
                $(this).removeClass('btn-default pushTeam').addClass('btn-danger openTeam');
            }    
            
            if(task_id && team_id && role_id){                                            
                $.ajax( {
                    url: '/tasks_teams/chgRole/',
                    data: {'task':task_id, 'team':team_id, 'role':role_id},
                    beforeSend:function () {
                        console.log(team_id);
                        console.log(role_id);
                        tdheading_div.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
                    },
                    //success:function(data, textStatus) {
                        //var msg_html = '<div class=\"alert alert-success\" role=\"alert\">'+data.message+'</div>';
                        //$('#cErrorStatus').html(msg_html).fadeIn().delay(3000).fadeOut('fast');
                    //},
                    complete:function (XMLHttpRequest, textStatus) {
                        tdheading_div.find('.tr_spin').remove();
                    },
                    error: function(xhr, statusText, err){
                        if(xhr.status == '401'){
                            var res_j = $.parseJSON(xhr.responseText);
                            //var res_j = xhr.responseText;
                            var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>' +res_j.message+ ' Please try again.</div>';
                            $('#cErrorStatus').html(msg).fadeIn('fast').delay(7000).fadeOut();
                        }
                        else{
                            var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                            $('#cErrorStatus').html(msg).fadeIn('fast').delay(7000).fadeOut();
                        }
                    },
                    type: 'post',
                    dataType:'json',
                });
            }
        });
        
        
    ");

    
    
        
    $this->Js->buffer("
       $('body').on('submit','form.formEditTask', function(e){
            var subBut = $(this).find('.eaSubmitButton');
            var valCont = $(this).find('.eaValidationContent');
            var spinner = $(this).find('.eaSpinner');
            var pageNum = $('#pageNum').html();
            var thisform = $(this);
    //console.log(thisform);return false;        
        e.preventDefault();
                    
        $('.new_tt').each(function(){
            var nteam = $(this).data('team_id');
            var ntr = $(this).data('tr_id');
            
            $('<input>').attr({
                type: 'hidden',
                name: 'data[TeamsRoles]['+nteam+']',
                value: ntr,
            }).appendTo(thisform);
        });
            
        $.ajax( {
            url: $(this).attr('action'),
            type: 'post',
            data: $(this).serialize(),
            dataType:'json',
            beforeSend:function () {
                subBut.val('Saving...');
                valCont.fadeOut('fast');                
                spinner.fadeIn();
            },
            success:function(data, textStatus) {
                //$('#qaForm').trigger('reset');  
                spinner.fadeOut('fast');
                //$('#cErrorStatus').html(data).fadeIn().delay(3000).fadeOut('fast');
                // Refresh tasks list
                   /* 
                $('#taskListWrap').load('/tasks/compileUser', function(response, status, xhr){
                    if(status == 'success'){
                        $('#taskListWrap').html(response);
                    }
                });*/
            },
            error: function(xhr, statusText, err){
                valCont.html(xhr.responseText).fadeIn('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                spinner.fadeOut('fast');
                subBut.val('Save Changes');
            },
        });
                        
            
            return false;
        });
        
        $('#taskListWrap').on('click', '.task-panel-heading', function(event){
            var tdheading_div = $(this);
            var tid = $(this).attr('data-tid');
            var tbody_div = $(this).closest('.task-panel').children('.taskPanelBody');
            var tbd_c = tbody_div.html().length;
    
            // Details haven't been fetched
            if(tbd_c < 100){
                $.ajax( {
                    url: '/tasks/details/'+tid,
                    beforeSend:function () {
                        tdheading_div.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
                    },
                    success:function(data, textStatus) {
                        tbody_div.html(data).addClass('is_vis').slideDown(300);
                    },
                    complete:function (XMLHttpRequest, textStatus) {
                        tdheading_div.find('.tr_spin').remove();
                    },
                    error: function(xhr, statusText, err){
                        if(xhr.status == '401'){
                            var res_j = $.parseJSON(xhr.responseText);
                            var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>Weird, you aren\'t allowed to view the task details ('+res_j.message+') Please refresh the page.</div>';
                            $('#cErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                        }
                        else{
                            var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                            $('#cErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                        }
                    },
                    type: 'post',
                    dataType:'html',
                });
            }  // Details were previously fetched, just show it again
            else if (tbd_c > 100 && !tbody_div.hasClass('is_vis')){
                tbody_div.addClass('is_vis').slideDown('slow');
            }  
            else {  //Details are visible, roll it up
                tbody_div.removeClass('is_vis').slideUp(300);
            }
            event.preventDefault();        
            return false;
        });
        
        $('#taskListWrap').on('click', '.astRow', function(event){
            var tid = $(this).attr('data-tid');
            var astRow = $(this);
            var astHeadingDiv = $(this).find('.astHeading');    
            var astBodyDiv = $(this).find('.astBody');
            var astBodyCont = astBodyDiv.html().length;
                
            // Details haven't been fetched
            if(astBodyCont < 100){
                $.ajax( {
                    url: '/tasks/details/'+tid,
                    beforeSend:function () {
                        astRow.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
                    },
                    success:function(data, textStatus) {
                        astBodyDiv.html('<br>'+data).addClass('is_vis').slideDown(300);
                    },
                    complete:function (XMLHttpRequest, textStatus) {
                        astRow.find('.tr_spin').remove();
                    },
                    error: function(xhr, statusText, err){
                        if(xhr.status == '401'){
                            var res_j = $.parseJSON(xhr.responseText);
                            var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>Weird, you aren\'t allowed to view the task details ('+res_j.message+') Please refresh the page.</div>';
                            $('#cErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                        }
                        else{
                            var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                            $('#cErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                        }
                    },
                    type: 'post',
                    dataType:'html',
                });
            }  // Details were previously fetched, just show it again
            else if (astBodyCont > 100 && !astBodyDiv.hasClass('is_vis')){
                astBodyDiv.addClass('is_vis').slideDown('slow');
            }  
            else {  //Details are visible, roll it up
                astBodyDiv.removeClass('is_vis').slideUp(300);
            }
            event.preventDefault();        
            return false;
        });
        
        
        
        
      $('#taskListWrap').on('click', '.eaTaskDeleteButton', function(){
            var tid = $(this).data('tid');
            var result = confirm('Are you sure you want to delete this?');
            
            if(result){
                $.ajax( {
                    url: '/tasks/delete/'+tid,
                    beforeSend:function () {
                        $('#ajaxProgress').fadeIn('fast');
                    },
                    success:function(data, textStatus) {
                        var msg_html = '<div class=\"alert alert-success\" role=\"alert\">'+data.message+'</div>';
                        
                        // Refresh tasks list
                            $('#taskListWrap').load('/tasks/compileUser', function(response, status, xhr){
                                if(status == 'success'){
                                    $('#taskListWrap').html(response);
                                }
                            });
                        
                        $('#cErrorStatus').html(msg_html).fadeIn().delay(3000).fadeOut('fast');
                    },
                    complete:function (XMLHttpRequest, textStatus) {
                        $('#ajaxProgress').fadeOut('fast');
                    }, 
                    type: 'post',
                    dataType:'json',
                });
            }
        });

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
                        var msg = '<div class=\"alert alert-success\" role=\"alert\"><b>Success: </b>'+data.message+'</div>';
                        $('#cErrorStatus').html(msg).stop().fadeIn('fast').delay(3000).fadeOut('fast');
                        $('#userTimeshiftCount').html(data.ts_count);
                        this_check.toggleClass('checked');    
                    }
                },
                error: function(xhr, statusText, err){
                    if(xhr.status == 401){
                        var res_j = $.parseJSON(xhr.responseText);
                        var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+res_j.message+'</div>';
                        $('#cErrorStatus').html(msg).stop().fadeIn('fast').delay(3000).fadeOut('fast');
                    }
                    else{
                        var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                        $('#cErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
                    }
                }
                /*
                complete:function (XMLHttpRequest, textStatus) {
                    tdheading_div.find('.tr_spin').remove();
                    console.log(textStatus);
                    console.log(XMLHttpRequest);
                },
                */ 
            });
        });
        
        $('div.flash-success').delay(3000).fadeOut();
        
        /*
        $('.one, .two, .three').click(function() {                             
            this.className = {
                three : 'one', one: 'two', two: 'three'
            }[this.className];
        });
        
        $('.is-oreq, .is-creq, .is-pushed').click(function(){
            var cls = $(this).attr('class');
            //alert(cls);
        });
        */
        
    ");
    
    if(isset($search_term)){
        $this->Js->buffer("
            $('body').highlight('".$search_term."');
            
        
        ");
        
    }


?>
    
    <div class="row">
        <h2>
            Compile Tasks
            <span id="ajaxProgress" style="display: none; margin-left: 10px; vertical-align: top;">
                <?php echo $this->Html->image('ajax-loader_old.gif'); ?>                    
            </span>
        </h2>
    </div>



    <div class="row">
        <div class="col-md-8 col-md-offset-2" id="cErrorStatus">
            <?php 
                echo $this->Session->flash('compile');
            ?>
        </div>
    </div>
        
        <div class="row">
        <?php 
            echo $this->element('task/urgent_tasks',array(
                'utasks'=>$tasks,
                'show_details'=>$show_details
                ));
        ?>
        </div>
 

    <div id="page-content" class="row">
        <div id="taskListWrap">
            <?php 
                echo $this->element('task/compile_screen',array(
                    'tasks'=>$tasks,
                    'show_details'=>$show_details
                    ));
            ?>
        </div>
        
                <?php 
            echo $this->element('task/task_legend');
        ?>
            
        </div>
        
        
    </div>

   

<?php echo $this->Js->writeBuffer(); ?>