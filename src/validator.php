<?php
/*
 * validator.php is called from an ajax request which POSTs the HTML of the document and the document type.
 * A new instance of the validator class is instantiated and the output of the validate() method is sent back to
 * the page.
 */
include_once 'validator.class.php';
$v=new validator();
print $v->validate($_POST['html'], $_POST['doctype']);