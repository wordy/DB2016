<?php
    if (AuthComponent::user('id')){
        $controlled_teams = AuthComponent::user('Teams');
        $user_role = AuthComponent::user('user_role_id');
    }


$this->Js->buffer("

    $('#view_nav_details').on('click', function(event){
        $.ajax( {
            url:'".$this->Html->url(array('controller'=>'tasks', 'action'=>'view', $task['Task']['id']))."',
            beforeSend:function () {
                $('#ajax-menu-spinner').fadeIn();},
            success:function(data, textStatus) {
                $('#ajax-content-load').html(data);},
            complete:function (XMLHttpRequest, textStatus) {
                $('#ajax-menu-spinner').fadeOut();}, 
            type: 'post',
            dataType:'html',
      });
      return false;
    });
    
    /*
    $('#view_nav_attachments').on('click', function(event){
        $.ajax( {
            url:'".$this->Html->url(array('controller'=>'attachments', 'action'=>'pageAttachments', $task['Task']['id']))."',
            beforeSend:function () {
                $('#ajax-menu-spinner').fadeIn();},
            success:function(data, textStatus) {
                $('#ajax-content-load').html(data);},
            complete:function (XMLHttpRequest, textStatus) {
                 $('#ajax-menu-spinner').fadeOut();}, 
            type: 'post',
            dataType:'html',
        });
      return false;
    });
    */

    $('#view_nav_changes').on('click', function(event){
        $.ajax( {
            url:'".$this->Html->url(array('controller'=>'changes', 'action'=>'pageChanges', $task['Task']['id']))."',
            beforeSend:function () {
                $('#ajax-menu-spinner').fadeIn();},
            success:function(data, textStatus) {
                $('#ajax-content-load').html(data);},
            complete:function (XMLHttpRequest, textStatus) {
                $('#ajax-menu-spinner').fadeOut();}, 
            type: 'post',
            dataType:'html',
          });
          return false;
      });
/*
    $('#view_nav_edit').on('click', function(event){
        $.ajax( {
            url:'".$this->Html->url(array('controller'=>'tasks', 'action'=>'edit', $task['Task']['id']))."',
            beforeSend:function () {
                $('#ajax-menu-spinner').fadeIn();},
            success:function(data, textStatus) {
                $('#ajax-content-load').html(data);
            },
            complete:function (XMLHttpRequest, textStatus) {
                $('#ajax-menu-spinner').fadeOut();}, 
            type: 'get',
            dataType:'html',
          });
          return false;
      });
*/
");


?>

<div class="row">
    <div class="col-md-12">
        <div class="row sm-top-marg">
            <div class="btn-group btn-group-justified">
                <a class="btn btn-default" id="view_nav_details"><i class="fa fa-bookmark-o"></i>&nbsp;&nbsp;&nbsp;Details</a>
                
                <?php //<a class="btn btn-default" id="view_nav_comments"><i class="fa fa-comments-o"></i>&nbsp;&nbsp;&nbsp;Comments</a>?>
                <a class="btn btn-default" id="view_nav_changes"><i class="fa fa-exchange"></i>&nbsp;&nbsp;&nbsp;Changes</a>
                <!--<a class="btn btn-default" id="view_nav_attachments"><i class="fa fa-paperclip"></i>&nbsp;&nbsp;&nbsp;Attachments</a>-->
                <?php if(in_array($task['Task']['team_id'], $controlled_teams)):?> 
                    <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'edit',$task['Task']['id']))?>" class="btn btn-default" id="view_nav_edit"><i class="fa fa-cog"></i>&nbsp;&nbsp;&nbsp;Edit</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php //echo $this->Js->writeBuffer();?>
