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
        100=>'Recently Created',
        399=>'Assisting & Due Soon',     
        500=>'Action Items',
    );
 */
    
    $sort_by = array(
        0=>'Start Time Ascending',
        1=>'Start Time Descending',
    );
    
    $this->Js->buffer("
        var c_start = '".Configure::read('CompileStart')."';
        var c_end = '".Configure::read('CompileEnd')."';
        
        $('#coDateRange').daterangepicker({
        autoApply: true,
        locale: { format: 'YYYY-MM-DD' },
        ranges: {
            'All': [moment(c_start), moment(c_end)],
            'Today to Event': [moment(), moment(dbEventDate).endOf('day')],
            'Event': [moment(dbEventDate).startOf('day'), moment(dbEventDate).endOf('day')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
            'DB2015': [moment('2014-11-01').startOf('day'), moment('2015-03-31').endOf('day')],
        } 
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

<div class="panel-body" id="compileOptions">
    <div class="well">
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
                        'id' => 'coTeams'
                    ));

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
                        'class' => 'form-control coDateRange'));
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
                    <div class="col-xs-12" id="coViewOpts">
                        <label><b>View Options</b></label>
                        <a class="helpTTs" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="View Options" data-content="These options control how tasks are displayed. If you 'Compile Tasks' after setting View Options, they will be remembered until you change your Compile & View Options. <br><br><b>View Rundown (Default)</b><br> Shows each task individually. Emphasizes timing and order of tasks<br><br><b>View Threaded</b><br>Emphasizes relationships between tasks, regardless of when they occur.<br><br><b>View/Hide Linkages</b><br>Show or hide linkages (i.e. linked tasks). <br><br><b>View/Hide Details</b><br>Show/hide task details."><i class="fa fa-question-circle text-info"></i></a><br>
                        <button type="button" data-checked="<?php echo (!empty($view_threaded))? 1:0?>" id="coViewThreadedBut" class="btn btn-primary <?php echo ($view_threaded)? 'showing_threaded':'showing_rundown'; ?>">
                            <?php 
                                echo ($view_threaded)? '<i class="fa fa-list-ol"></i> View Rundown':'<i class="fa fa-indent"></i> View Threaded'; ?>                                   
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
                                'value'=>$view_details
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
                        ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>   
        <?php //echo $this -> Form -> submit('Compile Tasks', array('id' => 'co_compile-submit-button', 'class' => 'btn btn-large btn-yh pull-left'));?> 
</div>
             
<?php 
    echo $this -> Form -> end(); 
    //$this->Js->writeBuffer();
?>