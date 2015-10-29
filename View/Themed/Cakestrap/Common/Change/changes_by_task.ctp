<?php



?>


<div class="row">
    <div class="col-md-12">
        <div class="col-md-12">
            <h1><?php echo $this->fetch('page_title');?>
            <small><?php echo $this->fetch('page_title_sub');?></small>
        </div>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php echo $this->fetch('content');?>
    </div>
</div>

<?php echo $this->Js->writeBuffer(); ?>  