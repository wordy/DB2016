<?php
    $show_pdf = (!isset($show_pdf))? false: true;
    
    $ftypes = array(
        0=>'None',
        1=>'With Due Dates',
        2=>'Assisting Only',
        3=>'Action Items',
    );
    
    $view_types = array(
        0=>'Threaded View',
        1=>'Rundown View',
        2=>'With Due Dates',
        3=>'Assisting Only',
        //4=>'Assisting & Due Soon',
        5=>'Action Items',
        6=>'Recently Created',
       
    
    
    );
    
    $sort_by = array(
        0=>'Start Time Ascending',
        1=>'Start Time Descending',
        //2=>'Recently Created'
    
    );
    

    $this->Js->buffer("
    $('.viewRad').trigger('change');
    
    $('.viewRad').on('change', function(){
        var sorts = $('.coSort');
        var show_pushed = $('#coShowPushed');
            
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
    var c_start = '".Configure::read('CompileStart')."';
    var c_end = '".Configure::read('CompileEnd')."';
    
    $('#coDateRange').daterangepicker(
        {
            locale: {
                format: 'YYYY-MM-DD'
            },
            
        
         ranges: {
           'All': [moment(c_start), moment(c_end)],
           //'Today': [moment(), moment()],
          'Today to Event': [moment(), moment(dbEventDate).endOf('day')],
          'Event': [moment(dbEventDate).startOf('day'), moment(dbEventDate).endOf('day')],
           //'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Next Month': [moment().add(1, 'month').startOf('day'), moment().add(1, 'month').endOf('day')],
        }, 
        
        function(start, end, label) {
            alert('A new date range was chosen: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        },
        
      
    });
    
    $('#coDateRange').on('apply.daterangepicker', function(ev, picker) {
        console.log(ev);
        console.log(picker);
            
            new_s = $(this).data('daterangepicker').startDate;
            new_e = $(this).data('daterangepicker').endDate;
        //$('#daterange').val('');
        $('#coStartDate').val(moment(new_s).format('YYYY-MM-DD'));
        $('#coEndDate').val(moment(new_e).format('YYYY-MM-DD'));
        
    });
    
    
    $('#co_input-multiselect-teams').multiselect({
        includeSelectAllOption: true,
        enableClickableOptGroups: true, 
        buttonClass: 'btn btn-info', 
        buttonWidth: '100%',
        numberDisplayed: 5,
        maxHeight: 200,
    });
    $('.coDate').datetimepicker({
        //minDate:moment('".Configure::read('CompileStart')."'),
        //maxDate:moment('".Configure::read('CompileEnd')."'),
        sideBySide: true,
        showTodayButton: true,
        allowInputToggle: true,
        format: 'YYYY-MM-DD', 
    });
        

            
      $('.boot-popover').hover(function () {
        $(this).popover({
            html: true
        }).popover('show');
            }, function () {
                $(this).popover('hide');
        });
        
  
      
      
      
      
        
        
        
        
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
        <div class="row">
            <div class="col-md-4">
                        <?php
                            echo $this -> Form -> label('Compile.Teams', 'Show Teams');
                            echo '<br/>';
                            echo $this -> Form -> input('Compile.Teams', array(
                                'type' => 'select', 
                                'class' => 'co_input-multiselect-teams sm-bot-marg', 
                                'multiple' => 'true', 
                                'div' => false, 
                                'options' => $zoneTeamCodeList, 
                                'id' => 'co_input-multiselect-teams')
                                );
                        ?>
                        <?php
                        /*
                        <div class="sm-bot-marg sm-top-marg">
                        <?php echo $this -> Form -> label('Compile.start_date', 'From Date'); ?>
                        <?php echo $this -> Form -> input('Compile.start_date', array(
                            'empty' => true, 
                            'type' => 'text', 
                            'placeholder' => 'Set a lower date limit', 
                            'div' => array(
                                'class' => 'input-group', ), 
                            'after' => '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>', 
                            'class' => 'form-control coDate'));
                        ?>
                        </div>
                        <div class="sm-bot-marg"> 
                        <?php echo $this -> Form -> label('Compile.end_date', 'To Date'); ?>
                        <?php echo $this -> Form -> input('Compile.end_date', array(
                            'empty' => true,
                            'type' => 'text', 
                            'placeholder' => 'Set an upper date limit', 
                            'div' => array(
                                'class' => 'input-group',  ), 
                            'after' => '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>', 
                            'class' => 'form-control coDate')); 
                        ?>  
                        </div>
                         
                         */?>
                        <div class="sm-bot-marg sm-top-marg">
                        
                         
                         <?php 
                            
                            //$cs = Configure::read('CompileStart');
                            //$ce = Configure::read('CompileEnd');
                                //$start_date = 
                            
                            $cs = $this->Session->read('Auth.User.Compile.start_date');
                            $ce = $this->Session->read('Auth.User.Compile.end_date');
                            
                            $def_range = $cs.' - '.$ce;
                            
                        echo $this -> Form -> input('Compile.start_date', 
                            array(
                                'type' => 'hidden',
                                'id'=>'coStartDate', 
                            )
                        ); 
                        echo $this -> Form -> input('Compile.end_date', array(
                                'type' => 'hidden',
                                'id'=>'coEndDate', 
                            )
                        );
                        ?>  
                         
                         
                        <?php echo $this -> Form -> label('Compile.date_range', 'Date Range'); ?>
                        <?php echo $this -> Form -> input('Compile.date_range', array(
                            'empty' => true,
                            'id'=>'coDateRange', 
                            'type' => 'text',
                            'value'=>$def_range, 
                            'placeholder' => 'Set a lower date limit', 
                            'div' => array(
                                'class' => 'input-group', ), 
                            'after' => '<span class="input-group-addon"><i class="fa fa-calendar"></i></span>', 
                            'class' => 'form-control'));
                        ?>
                        </div>

                        
                        
                    </div>

            
        
            <div class="col-md-8">
                
                        <div class="well well-sm">
                            <div class="row">
                                <div class="col-xs-3">
                                    <?php echo __('<b>Tasks</b><br/>'); ?>
                                    
                                    <?php 
                                    
                                    
                                    echo $this->Form->input('Compile.view_type', array(
                                        'class' => 'coView viewRad',
                                        'label' => false,
                                        'type' => 'radio',
                                        
                                        'default'=> 0,
                                        'id'=>'coView',
                                        'legend' => false,
                                        'before' => '<div class="radio"><label>',
                                        'after' => '</label></div>',
                                        'separator' => '</label></div><div class="radio"><label>',
                                        'options' => $view_types
                                    
                                    
                                    ));
                                    ?>
                               
                                </div>
                                <div class="col-xs-9">

                                    <div id="coViewTypeHelp"></div>
                                        
                            </div>
                                </div>
                                <div class="row">
                                <div class="col-xs-6">
                                    <?php echo __('<b>View Options</b><br/>'); ?>
                                    <?php echo $this->Form->input('Compile.show_pushed', array(
                                        'label' => 'Pushed Tasks', 
                                        'type' => 'checkbox',
                                        'id'=>'coShowPushed',
                                        'before' => '<div class="checkbox"><label>',
                                        'after' => '</label></div>',
                                        'separator' => '</label></div><div class="checkbox"><label>',
                                      
                                        )); 
                                    ?>
                                    <?php echo $this->Form->input('Compile.show_details', array(
                                        'label' => 'Task Details', 
                                        'type' => 'checkbox',
                                        'id'=>'coShowDetails',
                                        'before' => '<div class="checkbox"><label>',
                                        'after' => '</label></div>',
                                        'separator' => '</label></div><div class="checkbox"><label>',
                                       
                                        )); 
                                    ?>
                                    

                                </div>
                                <div class="col-xs-6">
                                    <?php echo __('<b>Sort By</b><br/>'); 
                                    
                                    echo $this->Form->input('Compile.sort', array(
                                        'class' => 'coSort',
                                        'label' => false,
                                        'type' => 'radio',
                                        'id'=>'cSort',
                                        'default'=> 0,
                                        'legend' => false,
                                        'before' => '<div class="radio"><label>',
                                        'after' => '</label></div>',
                                        'separator' => '</label></div><div class="radio"><label>',
                                        'options' => $sort_by
                                    ));
                                    ?>
                                </div>
                                
                            </div>
                 
                        </div>
            </div>
            </div>
        
        
        
        
        
        
        
        

        <div class="row sm-bot-marg">
            <div class="col-md-12">
                <?php
                    echo $this -> Form -> submit('Compile Tasks', array('id' => 'co_compile-submit-button', 'class' => 'btn btn-large btn-success pull-left'));
                ?> 


            </div>    
        </div><!--submit-->
    </div>
    <?php 
        echo $this -> Form -> end(); 
        //$this->Js->writeBuffer();
    ?>