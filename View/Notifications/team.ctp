<?php

    
    
    echo $this->Html->css('notification_list.css', array('inline'=>false));
    
    $num_teams = count($this->Session->read('Auth.User.Teams'));
    
    
    
    


   $this->Js->buffer("
   
       $('#teamSelect').on('change', function(){
          val = $(this).val();
          
          var url = '/notifications/team/'+val;
          
          window.location = url;
          
       });
   
   
       $('#note_list').on('click', '.del_note', function(){
           var butt = $(this);
           var result = confirm('Are you sure you want to delete this notification?');
           var nid = butt.data('nid'); 
           
           if(result){
               $.ajax( {
                    url: '/notifications/delete/',
                    data: {'note_id':+nid},
                    beforeSend:function () {
                        $('#ajaxProgress').fadeIn('fast');
                    },
                    success:function(data, textStatus) {
                        // var art = butt.closest('article');
                        //var archive_count = $('#nArchiveBadge').text();
                        //var new_arch = 1*archive_count-1;
                       
                        //$('#nArchiveBadge').text(new_arch);
                        butt.closest('tr').fadeOut();
                       
                        // art.fadeOut();
                        var tid = $('#teamSelect').val();
                        location.reload();
                    },
                    complete:function (XMLHttpRequest, textStatus) {
                        $('#ajaxProgress').fadeOut('fast');
                    }, 
                    type: 'post',
                    dataType:'json',
                });   
            }
        });
   
        $('#note_list').on('click', '.accept_a', function(){
           var butt = $(this);
           var nid = butt.data('nid'); 
           var tid = $('#teamSelect').val();
           var in_count = $('#nInboxBadge').text();
           var archive_count = $('#nArchiveBadge').text();
           
           if(in_count == null){
               $('#nInboxBadge').text(0);
           }
           
           $.ajax( {
                url: '/notifications/approve/',
                data: {'note_id':+nid},
                beforeSend:function () {
                    $('#ajaxProgress').fadeIn('fast');
                },
                success:function(data, textStatus) {
                   var art = butt.closest('article');
                   var new_arch = 1*archive_count+1;
                   
                   $('#nInboxBadge').text((in_count-1));
                   $('#nArchiveBadge').text(new_arch);
                   $('#userNoteCount').text(data.n_count).fadeIn();
                   
                   art.fadeOut(); 
                   
                   location.reload();
                },
                complete:function (XMLHttpRequest, textStatus) {
                    $('#ajaxProgress').fadeOut('fast');
                }, 
                type: 'post',
                dataType:'json',
            });   
       });
   
       $('#archiveBadge').click(function (e) {
            e.preventDefault();
            $('#noteInbox').removeClass('active');
            $('#noteApproved').addClass('active');
        });
       
        $('#inboxBadge').click(function (e) {
            e.preventDefault();
            $('#noteInbox').addClass('active');
            $('#noteApproved').removeClass('active');
        }); 
   
   
   
   ");




    
?>
<div id="note_list" class="container row">
    <h2>Notifications</h2>
    <p>Your team will be notified whenever another team asks for your assistance or links a response to your tasks.  You'll also see system messages.</p>
    
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#noteHelp" aria-expanded="false" aria-controls="noteHelp">
        Help With Notifications
    </button>
<div class="collapse" id="noteHelp">
    <div class="well well-sm">
    
    <p>
        <b>System Messages: </b>Messages broadcast to all teams.  Compiler updates will appear here also.
        
    </p>
    <p>
        <b>Task Linkages: </b>When teams link to your tasks, notifications will appear if the team was listed as "Assisting".
    </p>
    <ul>
        <li>If the linked task satisfies your request, mark it as "Approved." Doing so removes the assistance request for that team automatically. </li>
        <li>Only approve tasks if they actually satisfy what you asked for. If they don't, contact the team and clarify what you need.</li>           
    </ul>
    <p><b>Assistance Requests: </b>Generated whenever another team asks for your Team's assistance in a task</p>
    <p><b>Archived: </b>Tasks that were previously approved appear here.</p>
    </div>

</div>
<br/>
<br/>
    
    
    <div class="row">
        <div class="col-sm-3">
            <div class="list-group">
                <a href="#" class="list-group-item active">
                <?php 
                    if($num_teams>1){ 
                        echo $this->Form->input('TeamNotifications', array(
                            'type'=>'select',
                            'id'=>'teamSelect',
                            'class'=>'form-control',
                            'multiple'=>false,
                            'empty'=>true,
                            'options'=>$this->Session->read('Auth.User.TeamsByZone'),
                            'selected'=> (isset($team_id))? $team_id: '',
                            'label'=>'Select Team'
                        ));
                    }
                ?>
                </a>
                <a href="#noteInbox" data-toggle="tab" class="list-group-item" id="inboxBadge">
                    <span id="nInboxBadge" class="badge badge-success">
                        <?php echo (!empty($inbox_count))? $inbox_count: 0;?>
                    </span>
                    <i class="fa fa-inbox fa-lg"></i> Inbox
                </a>
                <a href="#noteApproved" data-toggle="tab" class="list-group-item" id="archiveBadge">
                    <span id="nArchiveBadge" class="badge">
                        <?php echo (!empty($archive_count))? $archive_count: 0;?>
                    </span>
                    <i class="fa fa-archive fa-lg"></i> Archived
                </a>
            </div>
        </div>

        <div class="col-sm-9">
            <div class="tab-content">
                <div class="tab-pane active" role="tabpanel" id="noteInbox">
                    <?php echo $this->element('notification/notification_list'); ?>
                    <?php //echo $this->element('notification/approved_by_team_list'); ?>
                </div>
                <div class="tab-pane" role="tabpanel" id="noteApproved">
                    <?php echo $this->element('notification/approved_by_team_list'); ?>
                    <?php //echo $this->element('notification/notification_list'); ?>
                </div>
            </div>
        </div>
    </div>    
</div>
<?php echo $this->Js->writeBuffer(); ?>

<!-- CREDIT FOR PAGE LAYOUT: http://www.bootsnipp.com/snippets/featured/single-column-timeline-dotted -->