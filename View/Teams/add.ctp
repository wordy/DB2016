<?php 
    echo $this->Html->script('libs/jquery.simplecolorpicker', array('inline'=>false));
    
    echo $this->Html->css('libs/jquery.simplecolorpicker', array('inline'=>false)); 
    
    
    $this->Js->buffer("
    
    $('.color_picker_menu').simplecolorpicker({
        theme: 'fontawesome',
        picker: true
        });
    
    $('.boot-popover').hover(function () {
        $(this).popover({
            html: true
        }).popover('show');
            }, function () {
                $(this).popover('hide');
            });
    
    ");
    
?>

<div id="page-container" class="row">
	<div id="page-content" class="col-md-8 col-md-offset-2 well">
        <div class="teams form">
            <fieldset>
            <h2><?php echo __('Add Team'); ?></h2>
            <p>Use this to create new Ops teams</p>
		
            <?php echo $this->Form->create('Team', array(
                'novalidate'=>true, 
                'inputDefaults' => array(
                    'label' => false,
                    'class'=>'form-control',
                    'error'=>array(
                        'attributes'=>array(
                            'wrap'=>'span',
                            'class'=>'help-inline text-danger bolder'))), 
                'role' => 'form')); ?>

				

			

        	    <div class="form-group">
        	        <?php echo $this->Form->label('name', 'Team Name');?>
                    <?php echo $this->Form->input('name', array('class'=> 'form-control'));?>
                    <p class="help-block">I.e. "Registration"</p>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->label('code', 'Team Code');?>
                    <?php echo $this->Form->input('code', array('class' => 'form-control')); ?>
                    <p class="help-block">2 or 3 letters, with no spaces (i.e. REG or IT). Must be unique.</p>
                </div><!-- .form-group -->
                
                <div class="form-group">
                    <?php echo $this->Form->label('zone', 'Zone');?>

                    <?php 
                    echo $this->Form->input('zone', array(
                    'options'=>array(
                        'EXE' =>'EXE',
                        'GMS' =>'GMS',
                        'PRS' =>'PRS',
                        'ES' =>'ES',
                        'SS' =>'SS',
                        'PAS'=>'PAS'
                        )));?>
                    <p class="help-block">Zone as defined in the Org Chart</p>
                </div>
                
                <div class="form-group">
                    <?php echo $this->Form->label('Color', 'Color');?>
                    <a class="text-info boot-popover" href="#" 
                        id="pop-tcolor" 
                        data-placement="auto" 
                        data-trigger="hover" 
                        data-container="body" 
                        data-toggle="popover" 
                        title="Task Colours" 
                        data-content="Used to color all tasks by a team" 
                        data-original-title="Task Colors"><i class="fa fa-question-circle"></i></a>
                    <?php echo $this->Form->input('Color', array(
                        'class'=>'color_picker_menu',
                        'id'=>'task_color_picker', 
                        'options'=>$aColors)); ?>
                </div><!-- .form-group -->
      
                
                <?php echo $this->Form->submit('Save New Team', array('class' => 'btn btn-large btn-yh')); ?>
                <?php echo $this->Form->end(); ?>
            </fieldset>
		</div><!-- /.form -->
	</div><!-- /#page-content .col- -->
</div><!-- /#page-container .row-fluid -->

<?php echo $this->Js->writeBuffer();?>
