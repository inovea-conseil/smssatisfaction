<?php
/* SMS Satisfaction
 * Copyright (C) 2017       Inovea-conseil.com     <info@inovea-conseil.com>
 */

/**
 * \file    lib/smssatisfaction.lib.php
 * \ingroup smssatisfaction
 * \brief   ActionsSmssatisfaction
 *
 * Show admin header
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function smssatisfactionAdminPrepareHead()
{
	global $langs, $conf;

	$langs->load("smssatisfaction@smssatisfaction");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/smssatisfaction/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	
        
	complete_head_from_modules($conf, $langs, $object, $head, $h, 'smssatisfaction');

	return $head;
}
