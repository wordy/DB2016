<?php
    //echo $this->Html->script('compile');
    
    if (AuthComponent::user('id')) {
        $userRole = AuthComponent::user('user_role_id');
        $userTeams = AuthComponent::user('Teams');
        $userTeamList = AuthComponent::user('TeamsList');
        $userTeamByZone = AuthComponent::user('TeamsByZone');
    }
    
    // Figure out # of controlled teams a user has. Show team selection as readonly if control = 1
    $control_team_count = count($userTeamList);
    //$controlled_teams = Hash::extract($controlled_teams,'{s}');
    
    $readonly = $team_input_readonly = $singleTeamControl = false;
    
    $singleTeamControlled = null;
    
    if ($control_team_count >= 2) {
        $team_input_empty = true;
    }
    elseif ($control_team_count == 1) {
        $team_input_readonly = 'readonly';
        $team_input_empty = false;
        $ar_k = array_keys($userTeamList);
        $singleTeamControlled = $ar_k[0];
        $singleTeamControl = true;
    }
    
    else {
        $team_input_empty = false;
    }
    
    if(isset($assignments)){
        //$test = array_intersect($assignments, $userTeamList);
        //debug($assignments);debug($userTeamList);
        //debug($test);
        $this->request->data['Assignments'] = $assignments;
    }

    $this -> Js -> buffer("
        bindToSelect2($('.linkableParentSelect, #qaAssignSelect'));
        bindStartEndToDTP($('.inputStartTime, .inputEndTime'));
        bindSummernoteStd($('#qaInputDetails'));

        // Task Duration
        $('.inputStartTime, .inputEndTime').on('dp.change', function(){
            var inStart = $(this).parents('form').find('.inputStartTime');
            var inEnd = $(this).parents('form').find('.inputEndTime');
            var startTime = inStart.data('DateTimePicker').date();
            var endTime = inEnd.data('DateTimePicker').date();
            var diff = endTime-startTime;

            if((diff > 0) && (diff <86400000)){
                var dstr = moment.utc(diff).format('HH:mm:ss');
                var diff_msg = '(<b>Duration: '+dstr+'</b>)';
                $(this).parents('form').find('.endTimeLabel').html(diff_msg);
            }
            else if(diff >= 86400000){
                $(this).parents('form').find('.endTimeLabel').html('&nbsp;&nbsp;(<b>Duration: \>1 Day</b>)');
            }
            else{
                $(this).parents('form').find('.endTimeLabel').html('&nbsp;&nbsp;(<b>Duration: None</b>)');
            }
        });

        // TRIGGERS
        $('#qaReqAllBut, #qaPushAllBut').trigger('change');
        $('#qaStartTime').trigger('dp.change');
       
        // EVENTS
        $('#qaStartTime').on('click', 'li, a', function (e) {
            trigger('dp.change');
            //console.log('got change');
        });

        $('#qaStartTime').on('dp.change', function (e) {
            var inStart = $(this).parents('form').find('.inputStartTime');
            var inEnd = $(this).parents('form').find('.inputEndTime');
            var startTime = inStart.data('DateTimePicker').date();
            var endTime = inEnd.data('DateTimePicker').date();
            var diff = endTime.diff(startTime);
            var old_start = e.oldDate;
            var new_start = e.date;
            //var delta = new_start.diff(old_start);
            //var delta = e.date.diff(e.oldDate);
            var delta = new_start.diff(old_start);
            var old_dur = endTime.diff(old_start);

            // Enforce end after start
            inEnd.data('DateTimePicker').minDate(startTime);

            if(old_dur != 0){
                //console.log('olddur ' + old_dur);
                inEnd.data('DateTimePicker').date(endTime.add(delta));
                inEnd.data('DateTimePicker').minDate(startTime);
            }

            if(delta != 0){
                //inEnd.data('DateTimePicker').date(endTime.add(delta));
            }
        });


        
        
        
    
            $('div.bhoechie-tab-menu>div.list-group>a').click(function(e) {
                e.preventDefault();
                $(this).siblings('a.active').removeClass('active');
                $(this).addClass('active');
                var index = $(this).index();
                $('div.bhoechie-tab>div.bhoechie-tab-content').removeClass('active');
                $('div.bhoechie-tab>div.bhoechie-tab-content').eq(index).addClass('active');
            });
    
        
        
        
        
        


    ");

    $now_min = date('Y-m-d H:00:00');
    
    $this -> request -> data('Task.start_time', $now_min);
    $this -> request -> data('Task.end_time', $now_min);
    
    echo $this -> Form -> create('Task', array('class' => 'formAddTask', 'id' => 'qaForm', 'url' => array('action' => 'add'), 'novalidate' => true, 'inputDefaults' => array('label' => false), 'role' => 'form'));
?>
<style>
    /*  bhoechie tab */
div.bhoechie-tab-container{
  z-index: 10;
  background-color: #ffff00;
  padding: 0 !important;
  border-radius: 4px;
  -moz-border-radius: 4px;
  border:1px solid #ddd;
  margin-top: 5px;
  margin-left: 5px;
  -webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
  box-shadow: 0 6px 12px rgba(0,0,0,.175);
  -moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
  background-clip: padding-box;
  opacity: 0.97;
  filter: alpha(opacity=97);
}
div.bhoechie-tab-menu{
  padding-right: 0;
  padding-left: 0;
  padding-bottom: 0;
}
div.bhoechie-tab-menu div.list-group{
  margin-bottom: 0;
}
div.bhoechie-tab-menu div.list-group>a{
  margin-bottom: 0;
}
div.bhoechie-tab-menu div.list-group>a .glyphicon,
div.bhoechie-tab-menu div.list-group>a .fa {
  color: #5A55A3;
}
div.bhoechie-tab-menu div.list-group>a:first-child{
  border-top-right-radius: 0;
  -moz-border-top-right-radius: 0;
}
div.bhoechie-tab-menu div.list-group>a:last-child{
  border-bottom-right-radius: 0;
  -moz-border-bottom-right-radius: 0;
}
div.bhoechie-tab-menu div.list-group>a.active,
div.bhoechie-tab-menu div.list-group>a.active .glyphicon,
div.bhoechie-tab-menu div.list-group>a.active .fa{
  background-color: #5A55A3;
  background-image: #5A55A3;
  color: #ffffff;
}
div.bhoechie-tab-menu div.list-group>a.active:after{
  content: '';
  position: absolute;
  left: 100%;
  top: 50%;
  margin-top: -13px;
  border-left: 0;
  border-bottom: 13px solid transparent;
  border-top: 13px solid transparent;
  border-left: 10px solid #5A55A3;
}

div.bhoechie-tab-content{
  background-color: #ffffff;
  /* border: 1px solid #eeeeee; */
  padding-left: 20px;
  padding-top: 10px;
}

div.bhoechie-tab div.bhoechie-tab-content:not(.active){
  display: none;
}
</style>
<div class="row">
    <div class="col-xs-6 col-md-12">
    <div class="row">
        <div class="col-lg-5 col-md-12 col-sm-12 col-xs-9 bhoechie-tab-container">
            <div class="col-lg-3 col-md-3 col-sm-2 col-xs-3 bhoechie-tab-menu">
              <div class="list-group">
                <a href="#" class="list-group-item active text-center">
                  <h4 class="glyphicon glyphicon-plane"></h4><br/>Flight
                </a>
                <a href="#" class="list-group-item text-center">
                  <h4 class="glyphicon glyphicon-road"></h4><br/>Train
                </a>
                <a href="#" class="list-group-item text-center">
                  <h4 class="glyphicon glyphicon-home"></h4><br/>Hotel
                </a>
                <a href="#" class="list-group-item text-center">
                  <h4 class="glyphicon glyphicon-cutlery"></h4><br/>Restaurant
                </a>
                <a href="#" class="list-group-item text-center">
                  <h4 class="glyphicon glyphicon-credit-card"></h4><br/>Credit Card
                </a>
              </div>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-10 col-xs-9 bhoechie-tab">
                <!-- flight section -->
                <div class="bhoechie-tab-content active">
                    <center>
                      <h1 class="glyphicon glyphicon-plane" style="font-size:14em;color:#55518a"></h1>
                      <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                      <h3 style="margin-top: 0;color:#55518a">Flight Reservation</h3>
                    </center>
                </div>
                <!-- train section -->
                <div class="bhoechie-tab-content">
                    <center>
                      <h1 class="glyphicon glyphicon-road" style="font-size:12em;color:#55518a"></h1>
                      <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                      <h3 style="margin-top: 0;color:#55518a">Train Reservation</h3>
                    </center>
                </div>
    
                <!-- hotel search -->
                <div class="bhoechie-tab-content">
                    <center>
                      <h1 class="glyphicon glyphicon-home" style="font-size:12em;color:#55518a"></h1>
                      <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                      <h3 style="margin-top: 0;color:#55518a">Hotel Directory</h3>
                    </center>
                </div>
                <div class="bhoechie-tab-content">
                    <center>
                      <h1 class="glyphicon glyphicon-cutlery" style="font-size:12em;color:#55518a"></h1>
                      <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                      <h3 style="margin-top: 0;color:#55518a">Restaurant Diirectory</h3>
                    </center>
                </div>
                <div class="bhoechie-tab-content">
                    <center>
                      <h1 class="glyphicon glyphicon-credit-card" style="font-size:12em;color:#55518a"></h1>
                      <h2 style="margin-top: 0;color:#55518a">Cooming Soon</h2>
                      <h3 style="margin-top: 0;color:#55518a">Credit Card</h3>
                    </center>
                </div>
            </div>
        </div>
  </div>
        
    </div>

<?php
    echo $this -> Form -> end();
    echo $this -> Js -> writeBuffer();
?>
</div>







