<?php
/**
 * Implements Special:Unusedtemplates
 *
 * Copyright © 2006 Rob Church
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup SpecialPage
 * @author Rob Church <robchur@gmail.com>
 */

/**
 * A special page that lists unused templates
 *
 * @ingroup SpecialPage
 */
class UnusedtemplatesPage extends QueryPage {

	function getName() { return( 'Unusedtemplates' ); }
	function isExpensive() { return true; }
	function isSyndicated() { return false; }
	function sortDescending() { return false; }

	function getSQL() {
		$dbr = wfGetDB( DB_SLAVE );
		list( $page, $templatelinks) = $dbr->tableNamesN( 'page', 'templatelinks' );
		$sql = "SELECT 'Unusedtemplates' AS type, page_title AS title,
			page_namespace AS namespace, 0 AS value
			FROM $page
			LEFT JOIN $templatelinks
			ON page_namespace = tl_namespace AND page_title = tl_title
			WHERE page_namespace = 10 AND tl_from IS NULL
			AND page_is_redirect = 0";
		return $sql;
	}

	function formatResult( $skin, $result ) {
		$title = Title::makeTitle( NS_TEMPLATE, $result->title );
		$pageLink = $skin->linkKnown(
			$title,
			null,
			array(),
			array( 'redirect' => 'no' )
		);
		$wlhLink = $skin->linkKnown(
			SpecialPage::getTitleFor( 'Whatlinkshere' ),
			wfMsgHtml( 'unusedtemplateswlh' ),
			array(),
			array( 'target' => $title->getPrefixedText() )
		);
		return wfSpecialList( $pageLink, $wlhLink );
	}

	function getPageHeader() {
		return wfMsgExt( 'unusedtemplatestext', array( 'parse' ) );
	}

}

function wfSpecialUnusedtemplates() {
	list( $limit, $offset ) = wfCheckLimits();
	$utp = new UnusedtemplatesPage();
	$utp->doQuery( $offset, $limit );
}
