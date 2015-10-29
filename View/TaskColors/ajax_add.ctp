<?php

echo $this->Html->script('task-colors', array('inline'=>true));
$this->Js->buffer("

    
        
        
        
        
        
        
        
    
    
    
    
    
    
    
    
    
    
");


?>

<div id="page-container" class="row">
    <div id="page-content" class="col-sm-8 col-sm-offset-2 well">
        <div class="taskColors form">
            <?php echo $this->Form->create('TaskColor', array(
                'inputDefaults' => array(
                    'label' => false), 
                'role' => 'form',
                'id'=>'ajax-add-taskcolor',
                'novalidate'=>true)); ?>
            <fieldset>
                <h2><?php echo __('Add Task Colour'); ?></h2>
                <p>
                    Colours are used to group tasks in free-form ways.  Add colors here to allow more choices.
                </p>
                    <?php 
                        echo $this->Form->input('name', array(
                            'format' => array(
                                'label', 'between', 'after', 'input', 'error', 'before'),
                        
                            'type'=>'text',
                            'label'=>'Friendly Name of Colour*',
                            'between'=>'&nbsp;&nbsp;',
                            'before'=>'<br/><br/>',
                            'placeholder'=>'Choose a Name',
                            'after'=>'<span class="help-inline">E.g. Lime Green or Navy Blue</span>',
                            'class'=>'form-control',
                            'error' => array(
                                'attributes' => array(
                                    'wrap' => 'span', 
                                    'class' => 'help-inline text-danger bolder')))); 
                    ?>
            
            <?php 
                        echo $this->Form->input('code', array(
                            'format' => array(
                                'label', 'between', 'after', 'input', 'error', 'before'),
                            'type'=>'text',
                            'label'=>'Hex Colour Code*',
                            'between'=>'&nbsp;&nbsp;',
                            'before'=>'<br/><br/>',
                            'placeholder'=>'Color Code',
                            'after'=>'<span class="help-inline">6-Character Hex value, starting with #.  E.g. #CCCCCC</span>',
                            'class'=>'form-control',
                            'error' => array(
                                'attributes' => array(
                                    'wrap' => 'span', 
                                    'class' => 'help-inline text-danger bolder')))); 
                    ?>
                
                    
            </fieldset>
            <?php echo $this->Form->submit('Submit', array('id'=>'tc-add', 'class' => 'add-tc-button btn btn-large btn-primary')); ?>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.form -->
    </div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->

<?php //echo $this->Js->writeBuffer();?>
