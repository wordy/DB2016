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
        <h1>Search Results <?php echo 'for "'.strtoupper($search_term).'"'; ?></h1>
        <div class="alert alert-info" role="alert">
            <div class="row">
                <div class="col-md-9">Viewing search results from <b>ANY</b> date ordered by <b>ascending</b> start time.</div>
                <div class="col-md-3 hidden-print">
                    <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'compile'))?>" class="btn btn-default ai_hidden pull-right">
                        <i class="fa fa-gears"></i> Back to Compiled Tasks                                   
                    </a>
                </div>
            </div>
        </div>
        
        <div id="page-content" class="row">
            <div class="col-xs-12">
                <?php echo $this->element('task/task_search',array('tasks'=>$tasks)); ?>
            </div>
        </div>
    </div>    
</div>

<?php echo $this->Js->writeBuffer(); ?>