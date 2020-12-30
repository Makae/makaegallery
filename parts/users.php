<?php

use ch\makae\makaegallery\Utils;

if (!defined('DOC_ROOT'))
    die();
global $App;

$users = $App->getAuth()->getUsers();
?>
<table class="table">
    <thead>
    <tr>
        <th>Name</th>
        <th>AccessLevel</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user) { ?>
        <tr>
            <td><?= $user['name']; ?></td>
            <td><?= Utils::getAccessLevelName($user['level']); ?></td>
        </tr>
    <?php } ?>
    </tbody>

</table>
