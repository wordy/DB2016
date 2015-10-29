<div id="page-container" class="row">
    <div id="page-content" class="col-sm-8 col-sm-offset-2 well">
    	<div class="changes form">
    		<?php echo $this->Form->create('Change', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
    		
			<fieldset>
				<h2><?php echo __('Add Change'); ?></h2>
                <p>
                    You generally shouldn't need to add changes manually (they are done automatically when tasks are changed/created)
                    If you need to manually add them, you can do so here.
                </p>
                <div class="form-group">
                    <?php echo $this->Form->label('task_id', 'Task');?>
                    <?php echo $this->Form->input('task_id', array('class' => 'form-control')); ?>
                </div><!-- .form-group -->

                <div class="form-group">
                    <?php echo $this->Form->label('user_id', 'User Making Change');?>
                    <?php echo $this->Form->input('user_id', array('class' => 'form-control')); ?>
                </div><!-- .form-group -->

                <div class="form-group">
                    <?php echo $this->Form->label('change_type_id', 'Change Type');?>
                    <?php echo $this->Form->input('change_type_id', array('class' => 'form-control')); ?>
                </div><!-- .form-group -->

                <div class="form-group">
                    <?php echo $this->Form->label('text', 'Text');?>
                    <?php echo $this->Form->input('text', array('class' => 'form-control')); ?>
                    <p class="help-block">Describe what the change was i.e. "FMM was added as a Contributing Team"</p>
                </div><!-- .form-group -->
			</fieldset>
			<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
            <?php echo $this->Form->end(); ?>
		</div><!-- /.form -->
	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->
