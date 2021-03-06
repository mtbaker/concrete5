<?php

defined('C5_EXECUTE') or die(_("Access Denied.")); 
$bObj = $controller;

?>

<div class="ccm-ui">
    <div class="form-horizontal">

        <div class="control-group">
            <label class="control-label" for="inputTitle">Title</label>
            <div class="controls">
                <input type="text" id="inputTitle" placeholder="Snow Report" name="title" value="<?php echo $bObj->title; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="inputHost">Host</label>
            <div class="controls">
                <input type="text" id="inputHost" placeholder="IP address" name="host" value="<?php echo $bObj->host; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="inputDatabasename">Database</label>
            <div class="controls">
                <input type="text" id="inputDatabasename" placeholder="Database name" name="databasename" value="<?php echo $bObj->databasename; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="inputUsername">Username</label>
            <div class="controls">
                <input type="text" id="inputUsername" placeholder="" name="username" value="<?php echo $bObj->username; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="inputPassword">Password</label>
            <div class="controls">
                <input type="text" id="inputPassword" placeholder="" name="password" value="<?php echo $bObj->password; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="inputFailurecontacts">Failure Contacts</label>
            <div class="controls">
                <input type="text" id="inputFailurecontacts" placeholder="email list, comma separated" name="failurecontacts" value="<?php echo $bObj->failurecontacts; ?>">
            </div>
        </div>

    </div>
</div>