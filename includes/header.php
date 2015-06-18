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
                <li class="<?php echo ($page == "grab" ? "active": "")?>"><a href="grablist">Duty for Grab</a></li>
                <li class="<?php echo ($page == "announcement" ? "active": "")?>"><a href="announcement">Announcement</a></li>
                <li class="<?php echo ($page == "report" ? "active": "")?>"><a href="report">Problem Report</a></li>
                <li class="<?php echo ($page == "sign" ? "active": "")?>"><a href="sign">Sign In/Out</a></li>   
                <li class="<?php echo ($page == "members" ? "active": "")?>"><a href="members">Members</a></li>   
                <li class="<?php echo ($page == "guide" ? "active": "")?>"><a href="guide">Guide</a></li>   
                <li class="<?php echo ($page == "postEOD" ? "active": "")?>"><a href="postEOD">Post EOD </a></li>
                <li class="<?php echo ($page == "tracking" ? "active": "")?>"><a href="tracking">Money Tracking</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="<?php echo ($page == "user" ? "active": "")?>"><a href="profile"><?php echo $_SESSION['user_name'];?></a></li>
                <li><a href="logout">Logout</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>
