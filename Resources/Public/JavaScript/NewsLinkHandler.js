/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Module: TYPO3/CMS/NewsLinkhandler/NewsLinkHandler
 * URL link interaction
 */
define(['jquery', 'TYPO3/CMS/Recordlist/LinkBrowser', 'TYPO3/CMS/Recordlist/ElementBrowser'], function($, LinkBrowser, ElementBrowser) {
	'use strict';

	/**
	 *
	 * @type {{}}
	 * @exports TYPO3/CMS/NewsLinkhandler/NewsLinkHandler
	 */
	var NewsLinkHandler = {};

	/**
	 *
	 * @param {Event} event
	 */
	NewsLinkHandler.link = function(event) {
		event.preventDefault();

		var data = $(this).parents('span').data();
		LinkBrowser.finalizeFunction('news:' + data.uid);
	};


	$(function() {
		$('[data-close]').on('click', NewsLinkHandler.link);
	});

	return NewsLinkHandler;
});
