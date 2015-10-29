<?php
    //$show_pdf = (!isset($show_pdf))? false: true;
    $this->set('title_for_layout', 'Time Shift');
    
    $this->Js->buffer("
    
    $('#tsSec').on('change', function(){
        var secs = 1*$('#tsSec').val();
        var minsecs = 60 * $('#tsMin').val();
        minsecs += secs;
        
        $('#timeShiftTable tr.userTimeShift')
            .each(function( index, element ){
                var sstr = 1*$(this).find('span.stimebef').data('sstr');
                var estr = 1*$(this).find('span.etimebef').data('estr');

                var sstr_after = sstr+=minsecs;
                var estr_after = estr+=minsecs;
                
                var ns = new Date(sstr_after * 1000);
                var ne = new Date(estr_after * 1000);
                
                var new_start = ns.toLocaleTimeString();
                var new_end = ne.toLocaleTimeString();
                
                $(this).find('span.stimeaft').html(new_start); 
                $(this).find('span.etimeaft').html(new_end); 
            });
    }); 
        
        
    $('#tsMin').on('change', function(){
        $('#tsSec').trigger('change');
    });
    
    $('#timeShiftTable').on('click', '.remShift', function(event){
        var tid = $(this).data('tid');
        var cl_tr = $(this).closest('tr');
        
        $.ajax( {
                url: '/tasks/remShift',
                
                success:function(data, textStatus) {
                    console.log(data.ts_count);
                    $('#userTimeshiftCount').html(data.ts_count);
                    cl_tr.fadeOut('fast');
                    
                    if(data.ts_count == 0){
                        var no_t = '<div class=\"alert alert-info\" role=\"alert\"><b>No Tasks Selected</b><br/>You haven\'t picked up any tasks to time shift yet.  Select tasks from your compiled plan by clicking on their checkboxes.</div>';
                        $('#tsTable').html(no_t);    
                    }
                },
                
                error: function(xhr, statusText, err){
                    if(xhr.status == '401'){
                        var res_j = xhr.responseText;
                        var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>Weird, you\'re not allowed to remove that task ('+res_j.message+').  Please reload the page and try again.</div>';
                        $('#tsErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                    }
                    else{
                        var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                        $('#tsErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                    }
                },
                
                
                
                
                /*
                beforeSend:function () {
                    tdheading_div.append('<span class=\"tr_spin\">".$this->Html->image('ajax-loader_old.gif')."</span>');
                },
                
                complete:function (XMLHttpRequest, textStatus) {
                    tdheading_div.find('.tr_spin').remove();
                },
                error: function(xhr, textStatus, status){
                    $('body').html(xhr.responseText);
                },*/ 
                type: 'post',
                dataType:'json',
                data: {task: +tid},
            });
    });      
        
        
        
    ");
    
    echo $this->Form->create('Shift', array(
        'url'=>array(
            'controller' => 'tasks', 
            'action' => 'timeShift'), 
        'inputDefaults' => array(
            'label' => false),
        'class'=>'form-inline', 
        'role' => 'form'));
    
    
    ?>

    <div class="row">
    <div class="panel-body" id="tsBody">
        <div class="row">
            <div class="col-md-9">
                <h1>Time Shift Tasks</h1>
                <p>Allows you to pick up groups of tasks from your compiled plan and shift their times by a fixed amount.  If an end time is specified, it is also moved accordingly.</p>
                <br/>
                <div class="row">
                    <div class="col-md-8 col-md-offset-2" id="tsErrorStatus">
                    </div>
                </div>
                    <div id="tsTable">
                        <?php echo $this->element('task/time_shift_table'); ?>
                    </div>
            </div><!--left col-->
            <div class="col-md-3 well">
                <h3>Shift All Tasks</h3>
                <p class="help-block">Shift forward/backwards in time by using positive/negative numbers.</p>
                        <label>Minutes
                            <input id="tsMin" type="number" name = "data[Shift][min]" class="form-control" min="-300" max="300" placeholder="Minutes">
                        </label>
                        <label>Seconds
                            <input id="tsSec" type="number" name = "data[Shift][sec]" class="form-control" default="0" min="-60" max="60" placeholder="Seconds">
                        </label>
                    <?php
                        echo $this -> Form -> submit('Time Shift Tasks', array('id' => 'co_compile-submit-button', 'class' => 'btn btn-large btn-success pull-left'));
                        echo '&nbsp;&nbsp;';
                        echo $this -> Form -> end(); 
                        
                    ?> 
                   <br/><br/><br/>
                   <div class="alert alert-danger" role="alert">
                    <b>WARNING: </b> This will move your tasks even if it causes them to overlap with other tasks.

                    
                </div>         
                        
            </div>
        </div>
        
    </div>
    </div>
    <?php 
        echo $this->Js->writeBuffer();
    ?>