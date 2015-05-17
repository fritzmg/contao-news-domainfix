<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license GPL-2.0
 */


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseArticles'][] = array('NewsDomainfix','fixDomain');
