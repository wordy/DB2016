<?php

//$this->layout="public";

//echo $this->Html->css('eventinfo.css', array('inline'=>false));
?>
<div id="page-container" class="row">

	<div id="page-content" class="col-md-12">

		<div class="users index">
			<h2>Compiler Version History</h2>
            v2.3 (Aug 2015)
            <ul>
                <li>New: (Feature) Tracking of open/closed requests</li>
                <li>New: (Feature) Time Linking of tasks</li>
                <li>New: (Feature) Team Dashboard</li>
                <li>New: (Feature) Email support
                    <ul>
                        <li>New: Automated password resets via email</li>
                        <li>New: Opt-in weekly digest of tasks</li>
                        <li>Change: Email address now required to create user.</li>
                    </ul>    
                </li>
                <li>New: (Feature) Comment on tasks
                <li>New: (Feature) Org Chart</li>
                <li>New: Support for page numbers in PDF</li>
                <li>New: (Model) `Zone` (Teams belong to Zones)</li>
                <li>New: (Model) `Comments`</li>
                <li>Change: `Change` model re-written. Handles more diverse changes</li>
                <li>Change: `Task` model changes to support task linking/time linking</li>
                <li>Removed: Notifications per team. Use improved `Changes` instead.</li>
                <li>Dependencies (Added): bootstrap-daterange - Used for Compile Options date range</li>
                <li>Dependencies (Change): PDF plugin changed to Segy/MPdf from FriendsOfCake/CakePDF</li>
                <li>Dependencies (Change): HTML editor changed to Summernote from bootstrap-wysihtml5 -- Requires IE > 8</li>
                <li>Dependencies (Upgrade): select2 upgraded to v4.0.0</li>
            </ul>

			v2.2 (Feb 2015)
            <ul>
                <li>New: Edit task's parent, or unlink entirely</li>
                <li>Change: Team listed as lead can no longer be added as either assisting or pushed</li>
            </ul>
			v2.12 (Jan 30 2015)
			<ul>
                <li>New: (Feature) Plain text version of compiled plan added</li>
			    <li>Change: Changes recorded in parent & child tasks when linkages are made/broken</li>
			    <li>Change: Improved removal of Notifications when tasks are deleted or assistance requests are revoked</li>
			</ul>
			v2.11 (Jan 25 2015)
            <ul>
                <li>Change: Notifications whenenver assistance is requested</li>
                <li>Change: Pre-set task filters (due/assisting/action items) ignore date range; pull all relevant tasks</li>
            </ul>
			v2.1 (Jan 16 2015)
			<ul>
		        <li>New: (Feature) Notification system (system messages & when assistance is received)</li>
			    <li>Change: (Feature) Task linking re-added</li>
			</ul>
            v2.0 (Nov 2014)
            <ul>
                <li>Software Re-write</li>
                <li>New: (Feature) Print prefs by user</li>
                <li>New: (Feature) Admin tools (i.e. user password reset)</li>
                <li>New: (Feature) Compile options with pre-defined views (due, action items, etc)</li>
                <li>New: (Feature) Time shifting tasks</li>
                <li>Removed: Attachments</li>
                <li>Removed: Task linking</li>
                <li>Removed: Actionable Date from tasks (use due date instead)</li>
                <li>Change: Visual upgrade to Twitter Bootstrap 3.3</li>
                <li>Change: AJAX everywhere. IE6+ browser required now.</li>
                <li>Change: Assisting & Pushed team roles</li>
                <li>Change: PDF Generation using new engine (MPDF)</li>
                <li>Change: Tasks no longer have colors. Colors belong to teams, and all their tasks get that color.</li>
            </ul>
            
			v1.2 (Jan 2014)
            <ul>
                <li>New: (Feature) PDF Generation (using DomPDF)</li>
            </ul>
            
			v1.1 (Dec 2013)
            <ul>
                <li>New: (Feature) Attachment support</li>
                <li>New: (Feature) Track changes in tasks (`Change` model)</li>
                <li>New: Restrict files to team only (security)</li>
                <li>New: User password hashing</li>
            </ul>
			v1.0 (November 2013)<br/>
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
	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->

