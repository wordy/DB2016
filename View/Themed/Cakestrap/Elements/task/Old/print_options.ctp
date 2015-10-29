<?php
    $show_pdf = (!isset($show_pdf))? false: true;

    $this->Js->buffer("
        $('#co_input-multiselect-teams').multiselect({
            includeSelectAllOption: true, 
            buttonClass: 'btn btn-info', 
            buttonWidth: '150%',
            numberDisplayed: 6,
        });
        
        $('.coDate').datetimepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayBtn: 'linked',
            todayHighlight: true,
            //minuteStep: 1,
            minView: 2,
            //showMeridian: true,
            startDate:'2014-11-01',
            forceParse:true,
            endDate:'2015-03-31',
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
        'url'=>array(
            'controller' => 'tasks', 
            'action' => 'compile'), 
        'inputDefaults' => array(
            'label' => false),
        'class'=>'form-inline', 
        'role' => 'form'));
    ?>

    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <?php
                            echo $this -> Form -> label('Compile.Teams', 'Show Teams');
                            echo '<br/>';
                            echo $this -> Form -> input('Compile.Teams', array(
                                'type' => 'select', 
                                'class' => 'co_input-multiselect-teams', 
                                'multiple' => 'true', 
                                'div' => false, 
                                'options' => $teams, 
                                'id' => 'co_input-multiselect-teams')
                                );
                        ?>
                    </div>
                    <div class="col-md-2">
                        <?php echo $this -> Form -> label('Compile.start_date', 'From'); ?>
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
                    <div class="col-md-2">
                        <?php echo $this -> Form -> label('Compile.end_date', 'To'); ?>
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
                    <div class="col-md-5">
                        <div class="well well-sm">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?php echo __('<b>View Options</b><br/>'); ?>
                                    <?php echo $this->Form->input('Compile.show_pushed', array(
                                        'label' => 'Pushed Tasks', 
                                        'type' => 'checkbox',
                                        'class' => 'input-control')); 
                                    ?>
                                    <br/>
                                    <?php echo $this->Form->input('Compile.show_details', array(
                                        'label' => 'Task Details', 
                                        'type' => 'checkbox', 
                                        'class' => 'input-control')); 
                                    ?>
                                </div>
                                <div class="col-sm-6">
                                    <?php echo __('<b>Filter Tasks</b><br/>'); ?>
                                    <?php echo $this->Form->input('Compile.filter_due_date', array(
                                        'label'=>'With Due Dates',
                                        //'class'=>'checkbox-inline'
                                        'type' => 'checkbox',
                                        'class' => 'input-control')); 
                                    ?>
                                    <br/>
                                    <?php echo $this->Form->input('Compile.filter_assist', array(
                                        'label' => 'Assisting Only', 
                                        'type' => 'checkbox',
                                        'class' => 'input-control')); 
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div><!--left col-->
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                    echo $this -> Form -> submit('Compile Tasks', array('id' => 'co_compile-submit-button', 'class' => 'btn btn-large btn-success pull-left'));
                    echo '&nbsp;&nbsp;';
                ?> 

                <?php
                    if ($show_pdf) {
                        echo $this->Html->link(
                            '<button type="button" class="btn btn-default"><i class="fa fa-file-pdf-o"></i> PDF</button>',
                            array(
                                'controller' => 'tasks',
                                'action' => 'compile/compile.pdf',
                                
                            ),
                            array(
                                'escape'=>false,
                            )
                        );

                        //echo $this -> Html -> image('pdf-dl.png', array('alt' => 'Download PDF', 'height' => '40px', 'width' => '40px', 'url' => array('controller' => 'tasks',
                        //'action'=>'compile/compile.pdf')));
                        //'action' => 'compile', 'pdf', 'ext' => 'pdf')));
                        //echo '&nbsp;&nbsp;<span class="text-default"><span class="label label-danger">NOTE</span> Large compiles may take ~2mins to start downloading. Don\'t close your browser window.</span>';
                        echo '&nbsp;&nbsp;<span class="text-info boot-popover" 
                                href="#" 
                                id="pop-publicprivate" 
                                data-placement="auto"
                                data-container="body" 
                                data-trigger="hover" 
                                data-toggle="popover" 
                                data-content="For large compiles (20+ pages), it can take up to 2 minutes before your download starts. 
                                <br /><br />
                                Don\'t close your browser window.
                                " 
                                ><i class="fa fa-lg fa-exclamation-triangle"></i>Note 
                                </span>';
                    }
               ?>
            </div>    
        </div><!--submit-->
    </div>
    <?php 
        echo $this -> Form -> end(); 
        //$this->Js->writeBuffer();
    ?>