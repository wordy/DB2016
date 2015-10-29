<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

//$this->layout='default';
?>

    <div class="row">

        <div class="col-md-12">
            <h1><?php echo Configure::read('EventShortName');?> Operations Committee Execution Planner<small> FAQ</small></h1>
            <h3 class="text-success">Using the Website</h3>
                <dl>

                <dt class="bolder"></dt>
                <dd class="sm-bot-marg"></dd>

                <dt class="bolder">Technical Requirements</dt>
                <dd class="sm-bot-marg">
                    You will need an internet connection to access the site.
                    You'll also need a reasonably modern web browser (Internet Explorer 9+) <b>with Javascript enabled.</b>  The site
                    also works reasonably well on most iPhones, Androids and Blackberrys. 
                </dd> 
                
                    
                <dt class="bolder">How can I retrieve my password?</dt>
                <dd class="sm-bot-marg">
                    For security reasons, your passwords are not stored, so passwords cannot be retrieved.  Contact Brandon or Kitty
                    if you need to have your password reset.
                </dd>

                <dt class="bolder">How do I change my password?</dt>
                <dd class="sm-bot-marg">
                    Once logged in using your temporary password, click on your name (top right) and select Change Password.
                </dd>


                <dt class="bolder">Errors About Permissions</dt>
                <dd class="sm-bot-marg">
                    Your permissions are based on the groups you're set up to control.  For example, Team Leaders
                    generally control 1 team, while Group Managers usually control 2-4 teams. First, try reloading the page
                    to ensure the system hasn't automatically logged you out.  If you feel you should have access to something and you're getting an error
                        please report it to Brandon, including the URL you were trying to access.
                </dd>
                



            </dl>



            
            <h3 class="text-success">Building Plans</h3>

                    
                <dl>

                <dt class="bolder">Tasks List</dt>
                <dd class="sm-bot-marg">Tasks are always ordered by time ascending.  By default when you log in, you will see your team's plan starting with the last time you logged in.  You can control which teams' tasks you see and other parameters by changing your compile options.</dd>

                <dt class="bolder">When Should I add Teams to My Tasks?</dt>
                <dd class="sm-bot-marg">
                    Add teams either when you need something from them (as an Assisting Team) or
                    if they should be kept in the loop about your team's task add them as a Pushed Team.
                    Whenever you add a team, your task is pushed to their plan.
                </dd> 
                
                <dt class="bolder">Filters</dt>
                <dd class="sm-bot-marg">Pushed Tasks - Show/hide tasks pushed <em>to</em> my team from <em>other teams</em>.  Use this when you're interested in seeing ONLY tasks that your team created.</dd>
                <dd class="sm-bot-marg">With Due Dates - Show only tasks that have due dates</dd>
                <dd class="sm-bot-marg">Assisting Only - Show only tasks where my team is assisting others.  This is your team's "todo list" composed of all requests coming to you from other teams.</dd>
                

                <dt class="bolder"></dt>
                <dd class="sm-bot-marg"></dd>


            </dl>
             <h3 class="text-success">Printing</h3>

                    
                <dl>

                <dt class="bolder"></dt>
                <dd class="sm-bot-marg"></dd>

                <dt class="bolder">Printing Your Plan</dt>
                <dd class="sm-bot-marg">
                    In compile options, there's a button labelled "PDF."  Click this to generate a PDF copy of your current plan.
                </dd> 
            </dl>

            <h3 class="text-success">Terms Defined</h3>

                    
                <dl>

                <dt class="bolder">Due Date</dt>
                <dd class="sm-bot-marg">A date a team lead+ can set.  Any teams linked to the task will be notified when the due date is approaching (so they know when they'll need to do something by).</dd>

                <dt class="bolder">Lead Team</dt>
                <dd class="sm-bot-marg">The team that creates a task. Users from this team will have full control (i.e. edit/delete) all tasks they create.</dd>

                <dt class="bolder">Assisting Team</dt>
                <dd class="sm-bot-marg">A team that is being asked to provide material/information/assistance</dd>

                <dt class="bolder">Pushed Team</dt>
                    <dd class="sm-bot-marg">
                        If you add a team to any of your tasks, <em>your</em> task is "pushed" to <em>their</em> plan. Push tasks to other teams to keep them in the loop.
                    </dd>
                <dt class="bolder">Actionable Items</dt>
                <dd class="sm-bot-marg">
                    Actionable items are tasks that require the attention of many teams.  They are meant to mimic the Action Items list.  These are tasks that
                    the group generally wants to keep an eye on as they progress (i.e. collecting draft volunteer training docs).
                    GMs and CCs can make tasks Actionable to bring attention to them.  
                </dd> 
                
                <dt class="bolder">Actionable Type</dt>
                <dd class="sm-bot-marg">A flag that classifies Actionable Items.  Some examples from the usual Action Items list: IPR (in progress), On Hold, Not Started.
                </dd>
            </dl>
            
            
       
            
            
            <!--
            <h4>Building Your Team's Plan</h4>
            <p>You'll build your plan by adding tasks</p>            
            <table class="table table-striped table-condensed">
                <tr>
                    <th>Function</th>
                    <th>Description</th>
                    <th>Added</th>
                </tr>
                
                <tr>
                    <td><?php echo $this->Html->link('Build Plan', array('action'=>'aBuildPlan', 'controller'=>'tasks'));?></td>    
                    <td>Buils plan (non-ajax)</td>
                    <td>10/7</td>
                </tr>
                <tr>
                    <td><?php echo $this->Html->link('Find Orphans', array('action'=>'orphanedTasks', 'controller'=>'tasks'));?></td>    
                    <td>Finds tasks that have no pri/sec teams.  Needs new view</td>
                    <td>10/7</td>
                </tr>
                
                
                
                
            </table>
            
            
            -->
            
            
        </div>


        
    </div>
