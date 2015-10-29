<?php 

if ($team_id == 0 ){
    
    echo 'Please Select a Team to View their Home Page';
}

else{?>
    
<h1><?php echo $tcode;?> Team Home Page</h1>




<div class="row">
    <div class="col-xs-6">
        <ul>
            <li>New Requests from Other Teams</li>
            <li>New Replies</li>
            <li>Changes in Linked tasks</li>
            <li>Upcoming due</li>
            <li></li>
            <li></li>
        </ul>
    </div>
    <div class="col-xs-6"></div>
</div>





<div class="row">
    <div class="col-xs-12 col-sm-6 emphasis">
        <h2><strong> <?php echo $team_id;?> </strong></h2>                    
        <p><small>Changes</small></p>
    </div>
    <div class="col-xs-12 col-sm-6 emphasis">
        <h2><strong></strong></h2>                    
        <p><small>Comments</small></p>
    </div>
</div>


<?php 
} // End of team_id !=0;
?>                