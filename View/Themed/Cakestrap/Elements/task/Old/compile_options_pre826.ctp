<?php
//    $show_pdf = (!isset($show_pdf))? false: true;
        $view_type = $this->Session->read('Auth.User.Compile.view_type');
        $view_details = $this->Session->read('Auth.User.Compile.view_details');
        $view_threaded = $this->Session->read('Auth.User.Compile.view_threaded');
        $view_links = $this->Session->read('Auth.User.Compile.view_links');
    
/*
    $view_types = array(
        0=>'Threaded View',
        1=>'Rundown View',
        2=>'Lead Only',
        3=>'Open Requests',
        4=>'Action Items',
        5=>'Recently Created',
        10=>'Assisting & Due Soon',       
    );
 */
    
    $sort_by = array(
        0=>'Start Time Ascending',
        1=>'Start Time Descending',
    );
    

    if(isset($view_type)){
        $this->Js->buffer("
            $('#coViewDetailsBut').on('click', function(){
                var in_vl = $('#coViewDetails');
                var this_val = $(this).data('checked');
                console.log(this_val);
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
                
                console.log(in_vl.val());
                var shtml = '<i class=\"fa fa-eye\"></i> Show Details';
                var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Details';                                   
                
                if($(this).hasClass('details_hidden')){
                        console.log('has details_hidden');
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
                    
            //$('div.isChild').hide();
            //$('div.taskLinkages').hide();
            
            $('#coViewThreadedBut').on('click', function(){
                var in_vl = $('#coViewThreaded');
                var this_val = $(this).data('checked');
                var nval;
                if(this_val == 0){
                    nval = 1;
                }
                else{
                    nval=0;
                }
                in_vl.val(nval);
                in_vl.trigger('change');
                var hhtml = '<i class=\"fa fa-list-ol\"></i> View Rundown';
                var shtml = '<i class=\"fa fa-indent\"></i> View Threaded';                                   
                
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

            
            //$('div.taskLinkages').hide();
    
            $('#coViewLinksBut').on('click', function(){
                var in_vl = $('#coViewLinks');
                var this_val = $(this).data('checked');
                var nval;
                if(this_val == 0){
                    nval = 1;
                }
                else{
                    nval=0;
                }
                in_vl.val(nval);
                in_vl.trigger('change');
                
                var shtml = '<i class=\"fa fa-eye\"></i> Show Linkages';
                var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Linkages';   

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
            
        ");
    }


    
    $this->Js->buffer("
    
    /*
    $('#cForm input, #cForm select').on('change', function(){
        var form = $('#cForm');
        
        console.log('got change');
        var data =  form.serialize();
        
        $.ajax( {
            url: form.attr('href'),
            data: data,
            type: 'post',
            dataType:'html',
            beforeSend:function () {
                
            },
            success:function(data, textStatus) {
                $('#taskListWrap').html(data).fadeIn('fast');
            },
            error: function(xhr, statusText, err){
                var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                $('#eaErrorStatus').stop().html(msg).fadeIn('fast').delay(3000).fadeOut('fast');
            },                
            complete:function (XMLHttpRequest, textStatus) {
                
            },
        });
        
    });
    
    
    $('#coViewLinksBut').on('click', function(e){
        var cval = $(this).data('checked');
        var nval = 0;
        
        if($(this).data('checked') == 1){
            $(this).data('checked', 0);
            nval = 0
        }
        else{
            $(this).data('checked', 1);
            nval = 1;
        }
        var in_vl = $('#coViewLinks');
        in_vl.val(nval);
    });
    
    $('#coViewThreadedBut').on('click', function(e){
        var cval = $(this).data('checked');
        var nval = 0;
        
        if($(this).data('checked') == 1){
            $(this).data('checked', 0);
            nval = 0
        }
        else{
            $(this).data('checked', 1);
            nval = 1;
        }
        var in_vl = $('#coViewDetails');
        in_vl.val(nval);
    });

    $('#coViewDetailsBut').on('click', function(e){
        var cval = $(this).data('checked');
        var nval = 0;
        
        if($(this).data('checked') == 1){
            $(this).data('checked', 0);
            nval = 0
        }
        else{
            $(this).data('checked', 1);
            nval = 1;
        }
        var in_vl = $('#coViewDetails');
        in_vl.val(nval);
    });

    
    
    $('.viewRad').trigger('change');
    
    $('.viewRad').on('change', function(){
        var sorts = $('.coSort');
        //var show_pushed = $('#coShowPushed');
            
        if(sorts.eq(0).prop('disabled')){
            sorts.each(function(){
                $(this).prop('disabled',false);
            });
        }
        
        if(show_pushed.prop('disabled')){
           show_pushed.prop('disabled', false); 
        }
        
        var view_val = $(this).val();


        if(view_val ==  2 || view_val == 4 || view_val == 5){
            $('.coSort').each(function(index,element){
                $(this).prop('disabled','disabled');
            });        
        }
        
        if(view_val != 1){
            $('#coShowPushed').prop('checked','checked').prop('disabled','disabled');
            
        }
            
    });
    */
    var c_start = '".Configure::read('CompileStart')."';
    var c_end = '".Configure::read('CompileEnd')."';

    $('.coDate').datetimepicker({
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD', 
    });

    $('#coDateRange').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        },
        ranges: {
            'All': [moment(c_start), moment(c_end)],
            'Today to Event': [moment(), moment(dbEventDate).endOf('day')],
            'Event': [moment(dbEventDate).startOf('day'), moment(dbEventDate).endOf('day')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('day'), moment().add(1, 'month').endOf('day')],
        } 
    });
    
    $('#coDateRange').on('apply.daterangepicker', function(ev, picker) {
        new_s = $(this).data('daterangepicker').startDate;
        new_e = $(this).data('daterangepicker').endDate;
        $('#coStartDate').val(moment(new_s).format('YYYY-MM-DD'));
        $('#coEndDate').val(moment(new_e).format('YYYY-MM-DD'));
    });
    
    $('#coTeams').multiselect({
        includeSelectAllOption: true,
        enableClickableOptGroups: true, 
        buttonClass: 'btn btn-info', 
        buttonWidth: '100%',
        numberDisplayed: 5,
        maxHeight: 200,
        /*        
        onChange: function(element, checked) {
            $('#coStartDate').trigger('change');
        }
        */
    });
    
    $('.boot-popover').hover(function () {
        $(this).popover({
            html: true
        }).popover('show');
            }, function () {
                $(this).popover('hide');
    });
        
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

    $('#coViewList input').on('change', function(){
        var help = $('body').find('#coViewTypeHelp');
        var help_str = '';
        //$('#coViewType').val($(this).val()).trigger('change');
        //var c_sort = $('.coSort');
        if($(this).val() == 1){
            help_str+= '<b>Rundown View</b><ul><li>Default setting. Shows any tasks involving the selected Teams, sorted by ascending or descending Start Time.</li>';
            help_str+= '<li>Toggle Linked Tasks on/off to view links to other tasks.</li></ul>';
            sortState('enable');        
        }
        else if($(this).val() == 10){
            help_str+= '<b>Lead Only</b><ul><li>Shows only tasks where the selected Team(s) are the lead.</li>';
            help_str+= '<li>Hides tasks from all other teams.</li><li>Useful for focusing on a single team\'s tasks.</li></ul>';
            sortState('enable');        
        }
        else if($(this).val() == 30){
            help_str+= '<b>Open Requests <u>From</u> Other Teams</b><ul><li>Listing of everything owed to other teams by the selected Teams.</li>';
            help_str+= '<li>Useful for tracking what your team owes other teams.</li></ul>';
            sortState('enable');        
        }
        else if($(this).val() == 31){
            help_str+= '<b>Open Requests <u>To</u> Other Teams</b><ul><li>Tasks where you\'ve have requested help from other teams, but you\'re still waiting on a reply.</li><li>Tasks owned by the selected teams that have open requests to <b>other</b> teams.</li>';
            help_str+= '<li>Useful for tracking what other teams owe your team.</li></ul>';
            
            sortState('enable');        
        }
        else if($(this).val() == 500){
            help_str+= '<b>Action Items</b><ul><li>Tasks that are important to the entire Ops Team.</li>';
            help_str+= '<li>Often these tasks take place over multiple weeks and progress needs to be tracked.</li><li><u>Examples:</u> Calling volunteers, submitting inventory requests, gathering volunteer feedback</li><li>Ordered by <u>ascending due date</u> to highlight upcoming due tasks.</li></ul>';
            sortState('disable');        
        }
        else if($(this).val() == 100){
            help_str+= '<b>Recently Created</b><ul><li>Shows most recently created tasks, regardless of when the task is scheduled to start (e.g. created today, but happening on event day).</li>';
            help_str+= '<li>Useful to see recent changes to your team\'s plan.</li><li>Ordered by <u>descending created date</u> (newest tasks first).</li></ul>';
            sortState('disable');
        }
        help.html(help_str);
        
    });

    $('#coViewList input:checked').trigger('change');
        
        
    ");
    
    echo $this->Form->create('Compile', array(
        'id'=>'cForm',
        'url'=>array(
            'controller' => 'tasks', 
            'action' => 'compile'), 
        'inputDefaults' => array(
            'label' => false),
        'class'=>'form', 
        'role' => 'form'));
    ?>

<div class="panel-body">
    <div class="well">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4 sm-top-marg">
                        <?php
                            echo $this -> Form -> label('Compile.Teams', 'Show Teams');
                            echo '<br/>';
                            echo $this -> Form -> input('Compile.Teams', array(
                                'type' => 'select', 
                                'class' => 'co_input-multiselect-teams sm-bot-marg', 
                                'multiple' => 'true', 
                                'div' => false, 
                                'options' => $zoneNameTeamList, 
                                'id' => 'coTeams')
                                );

                                $cs = $this->Session->read('Auth.User.Compile.start_date');
                                $ce = $this->Session->read('Auth.User.Compile.end_date');
                                $def_range = $cs.' - '.$ce;
                                
                                echo '<br/>';
                                echo $this -> Form -> label('Compile.date_range', 'Date Range', array('class'=>'sm-top-marg')); 
                                echo '<br/>';
            
                                echo $this -> Form -> input('Compile.date_range', array(
                                    'empty' => true,
                                    'id'=>'coDateRange', 
                                    'type' => 'text',
                                    'value'=>$def_range, 
                                    'placeholder' => 'Set a date range', 
                                    'div' => array(
                                    'class' => 'input-group', ), 
                                    'after' => '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>', 
                                    'class' => 'form-control'));
                                    
                                echo '<br/>';
  
                                    echo __('<b>Sort By</b>'); 
                                echo $this->Form->input('Compile.sort', array(
                                    'class' => 'coSort',
                                    'label' => false,
                                    'type' => 'radio',
                                    'id'=>'coSort',
                                    'default'=> 0,
                                    'legend' => false,
                                    'before' => '<div class="radio"><label>',
                                    'after' => '</label></div>',
                                    'separator' => '</label></div><div class="radio"><label>',
                                    'options' => $sort_by
                                ));
                            ?>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-xs-12 sm-top-marg">
                                <label><b>Filter Type</b></label><br>
                                <div id="coViewList" class="btn-group xs-bot-marg" data-toggle="buttons">
                                    <label class="btn btn-primary xs-bot-marg  <?php echo ($view_type == 1)? 'active':null; ?>">
                                        <input type="radio" name="data[Compile][view_type]" value="1" id="option2" autocomplete="off" <?php echo ($view_type == 1)? 'checked':null; ?>> Rundown
                                    </label>
                                    <label class="btn btn-darkgrey xs-bot-marg  <?php echo ($view_type == 10)? 'active':null; ?>">
                                        <input type="radio" name="data[Compile][view_type]" value="10" id="option3" autocomplete="off" <?php echo ($view_type == 10)? 'checked':null; ?>> Lead
                                    </label>
                                    <label class="btn btn-danger xs-bot-marg  <?php echo ($view_type == 30)? 'active':null; ?>">
                                        <input type="radio" name="data[Compile][view_type]" value="30" id="option3" autocomplete="off" <?php echo ($view_type == 30)? 'checked':null; ?>> Owing
                                    </label>
                                    <label class="btn btn-danger xs-bot-marg  <?php echo ($view_type == 31)? 'active':null; ?>">
                                        <input type="radio" name="data[Compile][view_type]" value="31" id="option3" autocomplete="off" <?php echo ($view_type == 31)? 'checked':null; ?>> Waiting
                                    </label>
                                    <label class="btn btn-yh xs-bot-marg  <?php echo ($view_type == 500)? 'active':null; ?>">
                                        <input type="radio" name="data[Compile][view_type]" value="500" id="option3" autocomplete="off" <?php echo ($view_type == 500)? 'checked':null; ?>> Action Items
                                    </label>
                                    <label class="btn btn-success xs-bot-marg  <?php echo ($view_type == 100)? 'active':null; ?>">
                                        <input type="radio" name="data[Compile][view_type]" value="100" id="option3" autocomplete="off" <?php echo ($view_type == 100)? 'checked':null; ?>> Recent
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="alert alert-info" id="coViewTypeHelp"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <label><b>View Options</b></label><br>
                                <button type="button" data-checked="<?php echo (!empty($view_threaded))? 1:0?>" id="coViewThreadedBut" class="btn btn-primary <?php echo ($view_threaded)? 'showing_threaded':'showing_rundown'; ?>">
                                    <?php echo ($view_threaded)? '<i class="fa fa-list-ol"></i> View Rundown':'<i class="fa fa-indent"></i> View Threaded'; ?>                                   
                                </button>
                                <button type="button" data-checked="<?php echo (!empty($view_links))? 1:0?>" id="coViewLinksBut" class="btn btn-primary <?php echo ($view_links)? 'links_shown':'links_hidden';?>">
                                    <?php echo ($view_links)? '<i class="fa fa-eye-slash"></i> Hide Linkages': '<i class="fa fa-eye"></i> Show Linkages'; ?> 
                                </button>
                                <button type="button" data-checked="<?php echo (!empty($view_details))? 1:0?>" id="coViewDetailsBut" class="btn btn-primary <?php echo ($view_details)? 'details_shown':'details_hidden';?>">
                                    <?php echo ($view_details)? '<i class="fa fa-eye-slash"></i> Hide Details':'<i class="fa fa-eye"></i> Show Details'; ?> 
                                </button>
                                <?php 
                                    echo $this->Form->input('Compile.view_details', array(
                                        'type' => 'hidden',
                                        'id'=>'coViewDetails',
                                        'value'=>$view_details,
                                        )); 
                        
                                    echo $this->Form->input('Compile.view_links', array(
                                        'type' => 'hidden',
                                        'id'=>'coViewLinks',
                                        'value'=>$view_links,
                                        )); 
                
                                    echo $this->Form->input('Compile.view_threaded', array(
                                        'type' => 'hidden',
                                        'id'=>'coViewThreaded',
                                        'value'=>$view_threaded,
                                        ));
                                        
                                    echo $this -> Form -> input('Compile.start_date', array(
                                        'type' => 'hidden',
                                        'id'=>'coStartDate'));
                                         
                                    echo $this -> Form -> input('Compile.end_date', array(
                                        'type' => 'hidden',
                                        'id'=>'coEndDate'));
                                         
                                            /*
                                    echo $this->Form->input('Compile.view_type', array(
                                                'type' => 'hidden',
                                                'id'=>'coViewType',
                                                'value'=>$view_type,
                                                )
                                            );*/ 
                                ?> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
    <div>
        <?php echo $this -> Form -> submit('Compile Tasks', array('id' => 'co_compile-submit-button', 'class' => 'btn btn-large btn-yh pull-left'));?> 
    </div><!--submit-->
</div>
             
<?php 
    echo $this -> Form -> end(); 
    //$this->Js->writeBuffer();
?>