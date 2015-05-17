<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license GPL-2.0
 */


/**
 * Utility class for parseArticles hook
 *
 * @author Fritz Michael Gschwantner <https://github.com/fritzmg>
 */
class NewsDomainfix extends \Controller
{
	private function generateInternalLink( $strHref, $strTitle, $strText, $blnIsReadMore = false )
	{
		return sprintf('<a href="%s" title="%s">%s%s</a>',
							$strHref,
							specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $strTitle), true),
							$strText,
							($blnIsReadMore ? ' <span class="invisible">'.$strTitle.'</span>' : ''));
	}

	public function fixDomain( $objTemplate, $arrArticle, $objModule )
	{
		// check for internal source
		if( $arrArticle['source'] != 'internal' )
			return;

		// check for target page
		if( ( $objTarget = \PageModel::findById( $arrArticle['jumpTo'] ) ) === null )
			return;

		// load the page details
		$objTarget->current()->loadDetails();

		// check domain
		if( $objTarget->domain == '' || $objTarget->domain == \Environment::get('host') )
			return;

		// check the target page language
		$strForceLang = null;
		if( \Config::get('addLanguageToUrl') )
			$strForceLang = $objTarget->language;

		// build the href
		$strHref = '';
		if( version_compare( VERSION, '3.3', '<' ) )
		{
			$strHref = $this->generateFrontendUrl($objTarget->row(), null, $strForceLang);
			$strHref = ($objTarget->rootUseSSL ? 'https://' : 'http://') . $objTarget->domain . TL_PATH . '/' . $strHref;
		}
		else
		{
			$strHref = $this->generateFrontendUrl($objTarget->row(), null, $strForceLang, true);
		}

		// update links
		$objTemplate->link = $strHref;
		$objTemplate->linkHeadline = $this->generateInternalLink( $strHref, $arrArticle['headline'], $arrArticle['headline'] );
		$objTemplate->more = $this->generateInternalLink( $strHref, $arrArticle['headline'], $GLOBALS['TL_LANG']['MSC']['more'], true );
	}
}
