<div class="row">
    <div class="col-md-12">
        <h1>Operations Committee Compiler<small> FAQ</small></h1>

        <h2 class="text-success">What is the Compiler?</h2>
        <p>The compiler is an interactive tool to help the Ops Team organize and plan for Dragonball. It is also the primary way that 
            individual Teams communicate their plan to the Ops Team as a whole. For Teams, the compiler:
            <ul>
                <li>Aids in laying out tasks and planning execution of each team's roles &amp; responsibilities</li>
                <li>Helps teams plan the activities necessary to complete their role leading up the event and the minute-by-minute execution on Event Day.</li>
                <li>Helps teams get help when their tasks require the input of other teams</li>
                <li>Helps teams track what they owe other teams and what other teams owe them.</li>
                <li>Shows connections between tasks, and provides context</li>
                <li>Helps teams track task due dates and deadlines</li>
            </ul>
        </p>
        
        <h2 class="text-success">Using the Website</h2>
        <h3>Technical Requirements</h3>
        <p>
            You will need an internet connection to access the site.
            You'll also need a reasonably modern web browser (Internet Explorer 9+) <b>with Javascript enabled.</b>  The compiler is 
            reasonably mobile friendly and works well on most iPhones/Androids. (HINT: Try landscape rather than portrait) 
        </p>
        
        <h3>How can I retrieve my password?</h3>
        <p>For security reasons, passwords are not stored and cannot be retrieved. If you know the email address associated with your compiler account, you can <?php echo $this->Html->link('click here ', array('controller'=>'users', 'action'=>'forgotPassword')); ?>  to reset your password.</p>

        <h3>How do I change my password?</h3>
        <p>Once logged in, click on your name in the main menu (top right) and select "Preferences."</p>

        <h3>Errors About Permissions</h3>
        <p>Your permissions are based on the groups you're set up to control.  For example, Team Leaders
            generally control 1 team, while Group Managers usually control 2-4 teams. First, try reloading the page
            to ensure the system hasn't automatically logged you out.  If you feel you should have access to something and you're getting an error
            please report it to Brandon, including the URL you were trying to access.
        </p>

        <h2 class="text-success">Building Plans</h2>

        <h3>When Should I Add Teams to My Tasks?</h3>
        <ul>
            <li>If another team should know about your task, but you don't require anything from them, <b>Push</b> your task to them (a "heads up")</li>
            <li>If you require (information/materials/resources/people/etc.) add an <b>Open Request</b> to the team.</li>
        </ul>

        <h3>Requesting Help from Other Teams</h3>
        <p>All you have to do to request help from another team is to add them with an <b>Open Request</b>.</p>


        <h2 class="text-success">Linking Tasks</h2>

        <h3>When Should I Link My Task To Another Task?</h3>
        <p>Link tasks when they're related. For example, if you created meeting notes during an Ops meeting, link them to related meeting. Or, if you're responding to another team's request, link to it.</p>

        <h3>When Should I <u>Time Link</u> My Task To Another Task?</h3>
        <p>Use a Time Link when the start of your task depends on the one you're linking to. For example, 
            if your task is related to something from the Production, ultimately the Production dictates when
            you'll need to perform your task.  In that case, Time Linking helps since your task moves
            automatically if the Production element changes.
        </p>

        <h3>What's an Offset</h3>
        <p>Offsets only relate to <b>Time Linked</b> tasks. If you time link to another task, you lose control
            of when your task starts (it's controlled by the linked task). However, if you know that you need
            your task to start 10 minutes before the linked task, you can simply set an Offset of 
            "10 Minutes Before Linked Task".  If you do so, the Offset is always maintained, even if the linked task moves.
        </p>

        <h2 class="text-success">Controlling Which Tasks You See</h2>

        <h3>Changing Options</h3>
        <p>Use the <b>Compile &amp; View Options</b> to change the tasks you're 
            currently viewing. There are several pre-defined "views" to give you access to key information.  
            For example, there are views for Recent Tasks, Action Items, Open Requests (incoming and outgoing), etc.
        </p>

        <h3>Changing Dates</h3>
        <p>By default the compiler shows tasks from the start of the Ops planning for the year until the post-event review.
            If you'd like to focus on certain time periods (i.e "now to event", "event day", "last week", etc.) there are pre-defined settings
            in the Compile &amp; View Options, or you can always set you own range by selecting the start and end days.
        </p>
     
        <h3>View Toggles</h3>
        <ul>
            <li><b>View Rundown</b> - (Default) Shows every task on a single line. Useful for looking at the order and timing of tasks.</li>
            <li><b>View Threaded</b> - Shows incoming links below a task (rather than in their own line). Emphasizes <b>relationships</b> between tasks, even if they take place at different times.</li>
            <li><b>View/Hide Linkages</b> - View or hide linkages (links to other tasks).</li>
            <li><b>View/Hide Details</b> - Use this to ignore details.</li>

        </ul>
        
        <h2 class="text-success">Printing or Exporting Your Plan</h2>
    
        <h3>Printing Your Plan</h3>
        <p>From the main menu (top) choose "Print."</p> 

        <h3>Customizing Your Printed Plan</h3>
        <p>The compiler remembers print preferences for all users even after you log out. From the Print Preferences page, you can select which
            tasks you want to hide entirely and/or which tasks you'd like to hide only the details for.
        </p> 

        <h3>Print/Export Options</h3>
        <p>Once you've set your preferences, you can choose how you'd like to export your plan. 
            <ul>
                <li><b>PDF</b> - Compiler generates a PDF file with your plan. Ideal for printing directly.</li>
                <li><b>Plain HTML</b> - Compiler gives you a plain HTML version of your plan. This is useful if you're
                    pasting into Microsoft Word, or otherwise manipulating your plan before printing.
                </li>
                <li><b>Plain Text</b> - Most formatting is stripped from your plan. This option is ideal for pasting
                    into Excel, for example.</li>
            </ul>
        </p>

        <h3 class="text-success">Terms Defined</h3>
        <dl>
            <dt class="bolder">Due Date</dt>
            <dd class="sm-bot-marg">A date a team lead+ can set.  Any teams linked to the task will be notified when the due date is approaching (so they know when they'll need to do something by).</dd>

            <dt class="bolder">Lead Team</dt>
            <dd class="sm-bot-marg">The team that creates a task. Users from this team will have full control (i.e. edit/delete) all tasks they create.</dd>

            <dt class="bolder">Pushed Team</dt>
            <dd class="sm-bot-marg">Team that is being notified about a task, but no response is required (a "heads up")</dd>

            <dt class="bolder">Open Request Team</dt>
            <dd class="sm-bot-marg">A team that is being asked to provide material/information/assistance</dd>

            <dt class="bolder">Closed Request Team</dt>
            <dd class="sm-bot-marg">A team that was asked to provide material/information/assistance and <b>already did so</b></dd>

            <dt class="bolder">Linked Task</dt>
            <dd class="sm-bot-marg">The "parent task" a task links to. Each task can link to one other task that it is related to.</dd>

            <dt class="bolder">Time Linked Task</dt>
            <dd class="sm-bot-marg">A "parent task" that also controls the start time of the "child" tasks.</dd>

            <dt class="bolder">Action Items</dt>
            <dd class="sm-bot-marg">
                Actionable items are tasks that require the attention of many teams.  They are meant to mimic the Action Items list.  These are tasks that
                the group generally wants to keep an eye on as they progress (i.e. collecting draft volunteer training docs).
                GMs and CCs can make tasks Actionable to bring attention to them.  
            </dd> 
            <dt class="bolder">Actionable Type</dt>
            <dd class="sm-bot-marg">A flag that classifies Actionable Items.  Some examples from the usual Action Items list: IPR (in progress), On Hold, Not Started.</dd>
        </dl>
    </div>
</div>
