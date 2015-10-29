<?php



?>


<div class="row">
    <div class="col-md-12">
        <h2>
            <?php echo $this->fetch('page_title');?>
            <small><?php echo $this->fetch('page_title_sub');?></small>
            <?php echo $this->Html->image('ajax-team-menu-spinner.gif', array('class'=>'', 'id' =>'ajax-menu-spinner')); ?>
        </h2>
    </div>
</div>

<div class="row">
    <div id="task-view-nav" class="sm-bot-marg">
        <div class="container">
            <?php echo $this->element('/menu/task_view_nav_buttons'); ?>
        </div>
    </div>
</div>
        
<div class="row">
    <?php echo $this->fetch('content');?>
</div>

<?php echo $this->Js->writeBuffer(); ?>  