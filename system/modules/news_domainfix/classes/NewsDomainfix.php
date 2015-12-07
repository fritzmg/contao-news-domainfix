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
		if( $arrArticle['source'] != 'internal' && $arrArticle['source'] != 'default' )
			return;

		// get current page object
		global $objPage;

		// prepare new href
		$strHref = '';

		// case for internal source
		if( $arrArticle['source'] == 'internal')
		{
			// check for target page
			if( ( $objTarget = \PageModel::findById( $arrArticle['jumpTo'] ) ) === null )
				return;

			// load the page details
			$objTarget->current()->loadDetails();

			// check domain and language
			if( ( $objTarget->domain == '' || $objTarget->domain == \Environment::get('host') ) && !( \Config::get('addLanguageToUrl') && $objTarget->rootLanguage != $objPage->rootLanguage ) )
				return;

			// build the href
			$strHref = '';
			if( version_compare( VERSION, '3.3', '<' ) )
			{
				$strHref = $this->generateFrontendUrl( $objTarget->row(), null, $objTarget->rootLanguage );
				$strHref = ($objTarget->rootUseSSL ? 'https://' : 'http://') . $objTarget->domain . TL_PATH . '/' . $strHref;
			}
			else
			{
				$strHref = $this->generateFrontendUrl( $objTarget->row(), null, $objTarget->rootLanguage, true );
			}
		}

		// case for default source
		elseif( $arrArticle['source'] == 'default' )
		{
			// determine the target
			$objTarget = null;

			// check if module has a target set
			if( $objModule->jumpTo )
				$objTarget = \PageModel::findByPk( $objModule->jumpTo );

			if( $objTarget === null && ( $objArchive = \NewsArchiveModel::findByPk( $arrArticle['pid'] ) ) !== null )
				$objTarget = \PageModel::findByPk( $objArchive->jumpTo );

			// check for target page
			if( $objTarget === null )
				return;

			// load the page details
			$objTarget->current()->loadDetails();

			// check domain
			if( !$objModule->jumpTo && ( $objTarget->domain == '' || $objTarget->domain == \Environment::get('host') ) && !( \Config::get('addLanguageToUrl') && $objTarget->rootLanguage != $objPage->rootLanguage ) )
				return;

			// build the href
			if( version_compare( VERSION, '3.3', '<' ) )
			{
				$strHref = $this->generateFrontendUrl( $objTarget->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $arrArticle['alias'] != '') ? $arrArticle['alias'] : $arrArticle['id']), $objTarget->rootLanguage );
				$strHref = ($objTarget->rootUseSSL ? 'https://' : 'http://') . $objTarget->domain . TL_PATH . '/' . $strHref;
			}
			else
			{
				$strHref = $this->generateFrontendUrl( $objTarget->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $arrArticle['alias'] != '') ? $arrArticle['alias'] : $arrArticle['id']), $objTarget->rootLanguage, true );
			}
		}

		// encode href
		$strHref = ampersand( $strHref );

		// update links
		$objTemplate->link = $strHref;
		$objTemplate->linkHeadline = $this->generateInternalLink( $strHref, $arrArticle['headline'], $arrArticle['headline'] );
		$objTemplate->more = $this->generateInternalLink( $strHref, $arrArticle['headline'], $GLOBALS['TL_LANG']['MSC']['more'], true );
	}

}
