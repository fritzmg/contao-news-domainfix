<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license GPL-2.0
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['newslist'] = str_replace( ';{template_legend', ';{redirect_legend},jumpTo;{template_legend', $GLOBALS['TL_DCA']['tl_module']['palettes']['newslist'] );
