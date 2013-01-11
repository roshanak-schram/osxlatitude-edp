<?php
function system_call($data) {
	ob_start();
	passthru("$data");
	ob_flush();
}
?>


