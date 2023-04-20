<?php
require_once('corelib/templateMerge.php');

$template = new templateMerge('theme/template2.html');

$template->pageData['main'] = "Hello World";

echo $template->render();
