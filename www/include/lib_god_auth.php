<?php

	# Hey look. Running code.

	if (! auth_has_role('staff')){
		error_404();
	}

?>
