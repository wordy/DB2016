<?php
    $this->Js->buffer("
       var offset = 420;
        var duration = 500;
        $(window).scroll(function() {
            if ($(this).scrollTop() > offset) {
                $('.back-to-top').fadeIn(duration);
            } else {
                $('.back-to-top').fadeOut(duration);
            }
        });
                    
        $('.back-to-top').click(function(event) {
            event.preventDefault();
            $('html, body').animate({scrollTop: 0}, duration);
            return false;
        });
    ");
    
    if(isset($search_term)){
        $this->Js->buffer("
            $('body').highlight('".$search_term."');
        ");
    }
?>

<div class="row">
    <div class="col-xs-12">        
        <a href="#" class="back-to-top"><i class="fa fa-2x fa-arrow-circle-o-up"></i> <span class="h4">Top</span></a>
        <h1><i class="fa fa-search"></i> Search Results <?php echo 'for "'.strtoupper($search_term).'"'; ?></h1>
        <?php if(!empty($tasks)): ?>
        <div class="alert alert-info" role="alert">
            <div class="row">
                <div class="col-md-7">Viewing search results for <b>"<?php echo $search_term; ?>"</b> from <b><?php echo date('M j', strtotime($start_date));?></b> to <b><?php echo date('M j', strtotime($end_date));?></b> ordered by <b>ascending</b> start time.</div>
                <div class="col-md-5 hidden-print">
                    <div class="pull-right">
                        <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'compile'))?>" class="btn btn-default ai_hidden">
                            <i class="fa fa-gears"></i> Back to Compiled Tasks                                   
                        </a>
                        <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'pdfFromSearch', $search_term))?>" class="btn btn-primary ai_hidden">
                            <i class="fa fa-file-pdf-o"></i> Download Results as PDF                                   
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif;?>
        <div id="page-content" class="row">
            <div class="col-xs-12">
                <?php echo $this->element('task/task_search', array('tasks'=>$tasks)); ?>
            </div>
        </div>
    </div>    
</div>

<?php echo $this->Js->writeBuffer(); ?>