
<div id="page-container" class="row">
    <div id="page-content" class="col-sm-8 col-sm-offset-2 well">
        <div class="taskColors form">
            <?php echo $this->Form->create('TaskColor', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
            <fieldset>
                <h2><?php echo __('Add Task Color'); ?></h2>
                <p>
                    Colors are used to group tasks in free-form ways.  Add colors here to allow more choices.
                </p>
                <div class="form-group">
                    <?php echo $this->Form->label('name', 'Friendly Name of Color');?>
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                    <p class="help-block">E.g. Lime Green or Navy Blue</p>
                </div><!-- .form-group -->
                <div class="form-group">
                    <?php echo $this->Form->label('code', 'Hex Color Code');?>
                    <?php echo $this->Form->input('code', array('class' => 'form-control')); ?>
                    <p class="help-block">6-Character Hex value, starting with #.  E.g. #CCCCCC</p>
                </div><!-- .form-group -->
            </fieldset>
            <?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.form -->
    </div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->
