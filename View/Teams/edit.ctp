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

	
	<div id="page-content" class="col-sm-8 col-sm-offset-2 well">

		<div class="teams form">
		
			<?php echo $this->Form->create('Team', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
				<fieldset>
					<h2><?php echo __('Edit Team'); ?></h2>
			<div class="form-group">
	
		<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('name', 'Team Name');?>
		<?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
	<?php echo $this->Form->label('code', 'Team Code');?>
		<?php echo $this->Form->input('code', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
    <?php echo $this->Form->label('zone', 'Zone');?>
        <?php echo $this->Form->input('zone', array('class' => 'form-control')); ?>
</div><!-- .form-group -->

<div class="form-group">
                    <?php echo $this->Form->label('Color', 'Task Color');?>
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
                        'options'=>$aPlusColors)); ?>
                </div><!-- .form-group -->


				</fieldset>
			<?php echo $this->Form->submit('Save Changes', array('class' => 'btn btn-large btn-yh')); ?>
<?php echo $this->Form->end(); ?>
			
		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->
