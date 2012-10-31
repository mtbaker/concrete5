<?php

defined('C5_EXECUTE') or die(_("Access Denied.")); 
$bObj = $controller;

?>

<div class="ccm-ui">
    <div class="form-horizontal">

        <div class="control-group">
            <label class="control-label" for="inputTitle">Title</label>
            <div class="controls">
                <input type="text" id="inputTitle" placeholder="Snow Report" value="<?php echo $bObj->title; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="inputHost">Host</label>
            <div class="controls">
                <input type="text" id="inputHost" placeholder="IP address" value="<?php echo $bObj->host; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="inputTable">Table</label>
            <div class="controls">
                <input type="text" id="inputTable" placeholder="Database table name" value="<?php echo $bObj->table; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="inputUsername">Username</label>
            <div class="controls">
                <input type="text" id="inputUsername" placeholder="" value="<?php echo $bObj->username; ?>">
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="inputPassword">Password</label>
            <div class="controls">
                <input type="text" id="inputPassword" placeholder="" value="<?php echo $bObj->password; ?>">
            </div>
        </div>

    </div>
</div>