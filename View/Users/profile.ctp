<style>
    .profile 
{
    min-height: 355px;
    display: inline-block;
    }
figcaption.ratings
{
    margin-top:20px;
    }
figcaption.ratings a
{
    color:#f1c40f;
    font-size:11px;
    }
figcaption.ratings a:hover
{
    color:#f39c12;
    text-decoration:none;
    }
.divider 
{
    border-top:1px solid rgba(0,0,0,0.1);
    }
.emphasis 
{
    border-top: 4px solid transparent;
    }
.emphasis:hover 
{
    border-top: 4px solid #1abc9c;
    }
.emphasis h2
{
    margin-bottom:0;
    }
span.tags 
{
    background: #1abc9c;
    border-radius: 2px;
    color: #f5f5f5;
    font-weight: bold;
    padding: 2px 4px;
    }
    
    /*
.dropdown-menu 
{
    background-color: #34495e;    
    box-shadow: none;
    -webkit-box-shadow: none;
    width: 250px;
    margin-left: -125px;
    left: 50%;
    }
.dropdown-menu .divider 
{
    background:none;    
    }
.dropdown-menu>li>a
{
    color:#f5f5f5;
    }
.dropup .dropdown-menu 
{
    margin-bottom:10px;
    }
.dropup .dropdown-menu:before 
{
    content: "";
    border-top: 10px solid #34495e;
    border-right: 10px solid transparent;
    border-left: 10px solid transparent;
    position: absolute;
    bottom: -10px;
    left: 50%;
    margin-left: -10px;
    z-index: 10;
    }
    */
</style>

<?php 
//debug($user);
    if (AuthComponent::user('id')){
        $user_id = AuthComponent::user('id');
        $userRole = AuthComponent::user('user_role_id');
        //$userTeamList = AuthComponent::user('TeamsList');
    }


$changeCount = !empty($user['Change'])? count($user['Change']): 0;

$commentCount = !empty($user['Comment'])? count($user['Comment']): 0;

$hasEmail = !empty($user['User']['email'])? true:false;

if(!empty($user)){

?>
<div class="row">
        <div class="col-md-offset-1 col-md-10 col-lg-offset-1 col-lg-10">
         <div class="well profile">
            <div class="col-sm-12">
                <div class="col-xs-12 col-sm-9">
                    <h2><?php echo h($user['User']['handle']); ?></h2>
                    
                    <div class="row">
                        <div class="col-xs-4"><p><strong>UID: </strong> <?php echo h($user['User']['id']); ?></p></div>
                        <div class="col-xs-4"><p><strong>Access: </strong><?php echo $user['User']['user_role'].' ('.$user['User']['user_role_id'].')'; ?></p></div>
                        <div class="col-xs-4"><p><strong>Status: </strong><?php echo $user['User']['status']; ?></p></div>
                    </div>

                    
                    
                      
                    <p><strong>Username: </strong><?php echo $user['User']['username']; ?></p>
                    <p><strong>Email: </strong> <?php 
                    
                    echo $hasEmail? h($user['User']['email']): "None Set"; ?></p>
                    <p><strong>Teams: </strong><br>
                        <?php 
              if (!empty($user['TeamsUser'])){
              $t_arr = array();
                 
             
                  
                  
              foreach($user['TeamsUser'] as $key=>$tcode){
                   $t_arr[] = $tcode['team_code']; } 
              
              
              //echo implode(', ', $t_arr);
              
              foreach ($t_arr as $team_code){
                 echo '<span class="btn btn-ttrid1 btn-xxs">'.$team_code.'</span>';     
               }
             
             if (count($user['TeamsUser'])>5){
             echo ' ('.count($user['TeamsUser']).' teams) ';
             }
              }
           ?>
                    </p>
                </div>             
                <div class="col-xs-12 col-sm-3 text-center">
                    <figure>
                        <!--<img src="http://placehold.it/350x150" alt="" class="img-circle img-responsive">-->
                        <?php
                            echo $this->Html->image('user-avatar-placeholder.png', array('class'=>'img-circle img-responsive', 'alt' => 'User Avatar'));
                        ?>
                    </figure><br/>
                    <?php
                    /*
                        if($user['User']['id'] == $user_id || $userRole > 200){
                            <a href="<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'userPrefs', $user_id))?>" class="btn btn-danger btn-block"><span class="fa fa-gear"></span> Account Preferences</a>
                            <br/>
                        endif;
                     
                     */
                    ?>
                </div>
            </div>            
            <div class="col-xs-12 divider text-center">
                <div class="col-xs-12 col-sm-6 emphasis">
                    <h2><strong> <?php echo $changeCount;?> </strong></h2>                    
                    <p><small>Changes</small></p>
                </div>
                <div class="col-xs-12 col-sm-6 emphasis">
                    <h2><strong><?php echo $commentCount;?></strong></h2>                    
                    <p><small>Comments</small></p>
                </div>
            </div>
                <div class="col-xs-12 text-center">

                <div class="col-xs-12 <?php echo $hasEmail? 'col-sm-4':'col-sm-6'?>">
                    <button class="btn btn-success btn-block disabled"><span class="fa fa-exchange"></span> View Changes </button>
                </div>
                <div class="col-xs-12 <?php echo $hasEmail? 'col-sm-4':'col-sm-6'?>">
                    <button class="btn btn-primary btn-block disabled"><span class="fa fa-comment-o"></span> View Comments </button>

                </div>

                <?php if($hasEmail){
                ?>
                <div class="col-xs-12 col-sm-4">
                    <a class="btn btn-default btn-block" href="mailto:<?php echo h($user['User']['email']); ?>"><span class="fa fa-envelope-o"></span> Send Email</a>
                </div>
                <?php 
                }
                ?>
                
                    
                    
<!--                        
                        <span class="fa fa-envelope pull-right"></span> Email <?php echo h($user['User']['handle']); ?></a>
                    <button class="btn btn-info btn-block"><span class="fa fa-user"></span> Send Email </button>
                    <div class="btn-group dropup btn-block">
                      <button type="button" class="btn btn-primary"><span class="fa fa-envelope-o"></span> Contact </button>
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <ul class="dropdown-menu text-left" role="menu">
                        <li><a href="mailto:<?php echo h($user['User']['email']); ?>"><span class="fa fa-envelope pull-right"></span> Email <?php echo h($user['User']['handle']); ?></a></li>
                        <li class="divider"></li>
                      </ul>-->

                </div>
            </div>
         </div>                 
        </div>


<?php 

}

else {
    ?>    
    <div class="alert alert-danger" role="alert"><strong>User Not Found!</strong> Verify the ID of the supplied user and try again.</div>        
<?php    


}
?>
<div id="page-container" class="row">


	
	<div id="page-content" class="col-sm-12">
		
			

					
			

			
	</div><!-- /#page-content .span9 -->

</div><!-- /#page-container .row-fluid -->
