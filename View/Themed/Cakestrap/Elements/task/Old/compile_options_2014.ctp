<?php
    $this->Js->buffer("
        
        $('#co_input-multiselect-teams').multiselect({
            includeSelectAllOption: false, 
            buttonClass: 'btn btn-info', 
            buttonWidth: '250px',
            numberDisplayed: 6,
        
            onChange: function(element, checked) {
                if(checked == false) {
                    $('#co_input-multiselect-private').multiselect('deselect', element.val());
                    return false;
                }
            }
        });
    
        $('#co_input-multiselect-private').multiselect({
            includeSelectAllOption: false, 
            buttonClass: 'btn btn-default',
            numberDisplayed: 4, 
            buttonWidth: '250px',
            maxHeight: '300px',
       
            onChange: function(element, checked) {
                if(checked == true) {
                     //If we view private, we assume you should also be viewing public
                    $('#co_input-multiselect-teams').multiselect('select', element.val());
                   return false;
                }
            }
        });
    
        $('#co_input-teams-toggle').on('click',function(e){
            e.preventDefault();
            multiselect_toggle($('#co_input-multiselect-teams'), $(this));
        });
        
        $('button#co_input-private-toggle').on('click', function(e) {
            e.preventDefault();
            multiselect_toggle($('#co_input-multiselect-private'), $(this));
            alert($(this));
            $('button#co_input-multiselect-teams').multiselect('refresh');
        });
        
    
    ");
?>

<div class="row">
    <div id="search-container" class="col-md-12">
        <div class="row">
            <div class="panel panel-dark">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-cogs"></i>&nbsp;<b>Compile Options</b></h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php 
                                echo $this->Form->create('Task', array('controller'=>'tasks', 'action'=>'compile','inputDefaults' => array('label' => false), 'role' => 'form'));
                                echo $this->Form->label('Task.Teams', 'Show Teams'); echo '<br/>';
                                echo $this->Form->input('Task.Teams', array(
                                    'type'=>'select', 
                                    'class'=>'co_input-multiselect-teams', 
                                    'multiple'=>'true', 
                                    'div'=>false, 
                                    'options'=>$teams,
                                    'id' => 'co_input-multiselect-teams'));
                                echo '<br/><br/>';
                            
                                echo $this->Form->label('Task.Private', 'Include Private For'); echo '<br/>';
                                echo $this->Form->input('Task.Private', array(
                                    'type'=>'select',
                                    'class'=>'co_input-multiselect-teams', 
                                    'multiple'=>'true', 
                                    'div'=>false, 
                                    'options'=>$teams, 
                                    'id' => 'co_input-multiselect-private'));
                                            
                            ?>  
                        </div>
                        <div class="col-md-3">
                            <?php echo $this->Form->label('Task.StartDate', 'From'); ?>
                            <?php echo $this->Form->input('Task.StartDate', array(
                                'empty'=>true,
                                'type'=>'text',
                                'placeholder'=>'Set a lower date limit',
                                'div'=>array(
                                    'class'=>'input-group',
                                ),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span>',
                                'class'=>'form-control input-date-notime')); 
                                echo '<br/>';
                                ?>  
                                  
                            <?php echo $this->Form->label('Task.EndDate', 'To'); ?>
                            <?php echo $this->Form->input('Task.EndDate', array(
                                'empty'=>true,
                                'type'=>'text',
                                'placeholder'=>'Set an upper date limit',
                                'div'=>array(
                                    'class'=>'input-group',
                                ),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span>',
                                'class'=>'form-control input-date-notime')); ?>  
                        </div>

                        <div class="col-md-5">
                            <div class="col-md-12 well well-sm sm-bot-marg">
                                <div class="row">
                                    <div class="col-md-5">
                                        <?php echo __('<b>Task Types</b>'); ?>
                                        <?php echo $this->Form->input('show_linked', array(
                                            'label'=>'Show Linked Tasks', 
                                            'type'=>'checkbox', 
                                            'class' => 'input-control')); ?>
                        
                                        <?php echo __('<b>Filter Tasks</b>'); ?>

                                        <?php echo $this->Form->input('filter_due_date', array(
                                            'label'=>'With Due Dates', 
                                            'type'=>'checkbox',
                                            'class' => 'input-control')); ?>
                                    </div>
                                
                                    <div class="col-md-7">
                                        <?php echo __('<b>View Options</b>'); ?>
                                        
                                        <?php echo $this->Form->input('color_act_pri', array(
                                            'label'=>'Color Action Items/Private/Due', 
                                            'type'=>'checkbox',
                                            'class' => 'input-control')); ?>
                                        
                                        <?php echo $this->Form->input('color_team', array(
                                            'label'=>'Team Colours', 
                                            'type'=>'checkbox', 
                                            'class' => 'input-control')); ?>
                                        
                                        <?php echo $this->Form->input('show_details', array(
                                            'label'=>'Task Details', 
                                            'type'=>'checkbox', 
                                            'class' => 'input-control')); ?>
                                        
                                        <?php echo $this->Form->input('show_threaded', array(
                                            'label'=>'Group Subtasks By Task', 
                                            'type'=>'checkbox', 
                                            'class' => 'input-control')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--row-->
                    <div class="row sm-bot-marg">
                        <div class="col-md-12 sm-bot-marg">
                            <?php 
                                echo $this->Form->submit('Compile Plan', array('id'=>'co_compile-submit-button', 'class' => 'btn btn-large btn-success pull-left'));
                                echo '&nbsp;&nbsp;';
                                echo $this->Form->end(); 
                            ?> 

                            <?php 
                                if($show_pdf){
                                    echo $this->Html->image('pdf-dl.png', array(
                                        'alt' => 'Download PDF',
                                        'height'=>'40px',
                                        'width'=>'40px', 
                                        'url'=>array(
                                            'controller'=>'tasks', 
                                            //'action'=>'compile/compile.pdf')));
                                            'action'=>'compile','pdf',
                                            'ext'=>'pdf')));
                                    //echo '&nbsp;&nbsp;<span class="text-default"><span class="label label-danger">NOTE</span> Large compiles may take ~2mins to start downloading. Don\'t close your browser window.</span>';
                                    echo '&nbsp;&nbsp;<span class="text-info boot-popover" 
                                    href="#" 
                                    id="pop-publicprivate" 
                                    data-placement="auto"
                                    data-container="body" 
                                    data-trigger="hover" 
                                    data-toggle="popover" 
                                    title="Generate PDF Plan" 
                                    data-content="For large compiles (20+ pages), it can take up to 2 minutes before your download starts. 
                                    <br /><br />
                                    Don\'t close your browser window.
                                    " 
                                    ><i class="fa fa-lg fa-exclamation-triangle"></i>Downloading PDF 
                                </span>';
                                } 
                           ?>
                       </div>
                   </div>
                </div>
            </div><!--panel-->
        </div>
    </div><!--search container-->
</div>
<?php 

//$this->Js->writeBuffer(); ?>
