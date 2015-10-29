<div id="page-container" class="row">
    <div id="page-content" class="col-sm-8 col-sm-offset-2 well">
        <div class="taskTypes form">
            <?php echo $this->Form->create('ActionableType', array('inputDefaults' => array('label' => false), 'role' => 'form')); ?>
            <fieldset>
                <h2><?php echo __('Add Actionable Type'); ?></h2>
                <p>
                    Actionable types are classes of actionable items.  Some pre-made ones include IPR (In progress), follow-up, etc.
                    If you need to add new types, do so here.
                </p>
                <div class="form-group">
                    <?php echo $this->Form->label('name', 'Actionable Type');?>
                    <?php echo $this->Form->input('name', array('class' => 'form-control')); ?>
                    <p class="help-block">E.g. In-Progress or Team-Followup</p>
                </div><!-- .form-group -->
                <div class="form-group">
                    <?php echo $this->Form->label('description', 'Description');?>
                    <?php echo $this->Form->input('description', array('class' => 'form-control')); ?>
                </div><!-- .form-group -->
            </fieldset>
            <?php echo $this->Form->submit('Submit', array('class' => 'btn btn-large btn-primary')); ?>
            <?php echo $this->Form->end(); ?>
        </div><!-- /.form -->
    </div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->
