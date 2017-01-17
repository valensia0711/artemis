<div class="navbar navbar-inverse navbar-static-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index"><img class="img-responsive logo" height="50px" src="includes/img/nussu-commit-logo.png"/></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="<?php echo ($page == "home" ? "active": "")?>"><a href="index">Home</a></li>
                <li class="<?php echo ($page == "grab" ? "active": "")?>"><a href="grablist">Grab Duty</a></li>
                <li class="<?php echo ($page == "announcement" ? "active": "")?>"><a href="announcement">Announcement</a></li>
                <li class="<?php echo ($page == "report" ? "active": "")?>"><a href="reportlist">Problem Report</a></li>
				<li><a target="_blank" href="https://drive.google.com/open?id=0B5GXlJznMxIGWnotemxSa0laZ0k">Timesheet</a></li>
                <li class="<?php echo ($page == "members" ? "active": "")?>"><a href="members">Members</a></li>
                <li class="<?php echo ($page == "guide" ? "active": "")?>"><a href="guide">Guide</a></li>
                <li class="<?php echo ($page == "postEOD" ? "active": "")?>"><a href="postEOD">EOD</a></li>


            </ul>
            <ul class="nav navbar-nav navbar-right">
			<li class="dropdown <?php echo (($page == "tracking" || $page == "scheduleavailability" || $page == "user") ? "active": "")?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user" aria-hidden="true"> </span><?php echo $_SESSION['user_name'];?><span class="caret"></span></a>
                <ul class="dropdown-menu">
				<li><a href="profile"><span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span> My Profile</a></li>
				<li><a href="scheduleavailability"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Schedule Availability</a></li>
				<li><a href="tracking"><span class="glyphicon glyphicon-usd" aria-hidden="true"></span> Money Tracking</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="logout">Logout</a></li>
				</ul>
			</li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
