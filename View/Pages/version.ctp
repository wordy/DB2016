<?php

//$this->layout="public";

//echo $this->Html->css('eventinfo.css', array('inline'=>false));
?>
<div id="page-container" class="row">
	<div id="page-content" class="col-md-12">
		<div class="users index">
			<h1>Compiler Version History</h1>
            v3.3 (Feb 2017)
            <ul>
                <li>Add: Assignments - assign tasks to `Actors` who can be placeholders or Compiler users</li>
                <li>Add: Timeline View - View hour-by-hour plan for a team, broken down by `Actor`</li>
                <li>New: (Model) `Actors`</li>
                <li>New: (Model) `Assignments`</li>
            </ul>

			v3.2 (Aug 2016)
            <ul>
                <li>Change: Timeshifting is now per task from main `compile` screen</li>
                <li>Removed: Timeshifting multiple tasks</li>
                <li>Removed: /tasks/timeShift action</li>
            </ul>
			
			v3.1 (Nov 2015)
			<ul>
			    <li>New: Quickly switch teams (if you control >1)</li>
			    <li>New: PDF From Search - Generate PDF from any search term.</li>
			    <li>New: Event Info is editable by anyone with a Compiler account</li>
			    <li>New: (Model) `Event_Info`</li>
			    <li>New (Admin) Manage Digest - Check contents per team &amp; send to team/individual</li>
			</ul>
            v3.0 (Aug 2015)
            <ul>
                <li>New: Tracking of open/closed requests</li>
                <li>New: Time Linking of tasks</li>
                <li>New: Team Dashboard</li>
                <li>New: Email support</li>
                <li>New: Opt-in weekly digest of tasks</li>
                <li>New: Automated password resets via email</li>
                <li>New: Comment on tasks
                <li>New: (Somewhat) Automated Org Chart generation</li>
                <li>New: Support for page numbers in PDF</li>
                <li>New: (Model) `Zone` (Teams belong to Zones)</li>
                <li>New: (Model) `Comments`</li>
                <li>Change: Email address now required to create user.</li>
                <li>Change: `Change` model re-written. Handles more diverse changes</li>
                <li>Change: `Task` model changes to support task linking/time linking</li>
                <li>Removed: Notifications per team. Use improved `Changes` instead.</li>
                <li>Dependencies (Add): <a href="https://github.com/dangrossman/bootstrap-daterangepicker">dangrossman/bootstrap-daterange</a> - Used for Compile Options date range</li>
                <li>Dependencies (Add): <a href="https://github.com/ablanco/jquery.pwstrength.bootstrap">ablanco/jquery.pwstrength.bootstrap</a> - Enforce safer user passwords</li>
                <li>Dependencies (Change): PDF plugin changed to <a href="https://github.com/segy/Mpdf">Segy/MPdf</a> from FriendsOfCake/CakePDF</li>
                <li>Dependencies (Change): HTML editor changed to <a href="https://github.com/summernote/summernote/">Summernote</a> from bootstrap-wysihtml5 -- Requires IE > 9</li>
                <li>Dependencies (Upgrade): Select2 upgraded to v4.0.0</li>
            </ul>

			v2.2 (Feb 2015)
            <ul>
                <li>New: Edit task's parent, or unlink entirely</li>
                <li>Change: Team listed as lead can no longer be added as any other role</li>
            </ul>
			v2.12 (Jan 2015)
			<ul>
                <li>New: Plain text version of compiled plan added</li>
			    <li>Change: Changes recorded in parent &amp; child tasks when linkages are made/broken</li>
			    <li>Change: Improved removal of Notifications when tasks are deleted or assistance requests are revoked</li>
                <li>Change: Notifications whenenver assistance is requested</li>
                <li>Change: Pre-set task filters (due/assisting/action items) ignore date range; pull all relevant tasks</li>
		        <li>New: Notification system (system messages &amp; when assistance is received)</li>
			    <li>Change: Task linking re-added</li>
			</ul>
            v2.0 (Nov 2014)
            <ul>
                <li>Software Re-write</li>
                <li>New: Print prefs by user</li>
                <li>New: Admin tools (i.e. user password reset)</li>
                <li>New: Compile options with pre-defined views (due, action items, etc)</li>
                <li>New: Time shifting tasks</li>
                <li>Change: Visual upgrade to Twitter Bootstrap 3.3</li>
                <li>Change: AJAX everywhere. IE6+ browser required now.</li>
                <li>Change: Assisting &amp; Pushed team roles</li>
                <li>Change: PDF Generation using new engine (MPDF)</li>
                <li>Change: Tasks no longer have colors. Colors belong to teams, and all their tasks get that color.</li>
                <li>Removed: Attachments</li>
                <li>Removed: Task linking</li>
                <li>Removed: Actionable Date from tasks (use due date instead)</li>
            </ul>
            
			v1.2 (Jan 2014)
            <ul>
                <li>New: PDF Generation (using DomPDF)</li>
                <li>New: Attachment support</li>
                <li>New: Track changes in tasks (`Change` model)</li>
                <li>New: Restrict files to team only (security)</li>
                <li>New: User password hashing</li>
            </ul>

			v1.0 (Nov 2013)<br/>
			<ul>
			    <li>Initial release
			    <ul>
			        <li>Models: Team, Task, Task_Team, User, Teams_Users</li>
			        <li>Task Functions: CRUD tasks, link tasks together</li>
			        <li>User Functions: CRUD users, add users to teams</li>
			    </ul>    
		        </li>
			</ul>
		</div><!-- /.index -->
	</div><!-- /#page-content-->
</div><!-- /#page-container-->

