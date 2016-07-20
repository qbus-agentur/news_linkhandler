<?php
namespace Qbus\NewsLinkhandler\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Service\TypoLinkCodecService;

/**
 * TypolinkLinkHandler
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TypolinkLinkHandler
{
    /**
     * TypolinkLinkHandler for news – e.g. news:1
     *
     * Requires the following typoscript (replace 9 with your news detailPid)
     * plugin.tx_news_linkhandler.detailPid = 9
     *
     * @param string                $linkText
     * @param array                 $typoLinkConfiguration TypoLink Configuration array
     * @param string                $linkHandlerKeyword
     * @param string                $linkHandlerValue
     * @param string                $mixedLinkParameter    destination data like "15,13 _blank myclass &more=1" used to create the link
     * @param ContentObjectRenderer $contentObjectRenderer
     *
     * @return string
     */
    public function main(
        $linkText,
        array $typoLinkConfiguration,
        $linkHandlerKeyword,
        $linkHandlerValue,
        $mixedLinkParameter,
        ContentObjectRenderer $contentObjectRenderer
    ) {
        $typoScriptConfiguration = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_news_linkhandler.'];

        // Link parameter value = first part
        $linkParameterParts = GeneralUtility::makeInstance(TypoLinkCodecService::class)->decode($mixedLinkParameter);

        $conf = array(
            'parameter' => $typoScriptConfiguration['detailPid'],
            'additionalParams' => '&tx_news_pi1[controller]=News&tx_news_pi1[action]=detail&tx_news_pi1[news]=' . intval($linkHandlerValue),
            'useCacheHash' => true,
        );

        if (!empty($linkParameterParts['additionalParams'])) {
            $conf['additionalParams'] .= '&' . $linkParameterParts['additionalParams'];
        }

        $cObj = clone $contentObjectRenderer;

        return array(
            'href'   => $cObj->typolink_URL($conf),
            'target' => $linkParameterParts['target'],
            'class'  => $linkParameterParts['class'],
            'title'  => $linkParameterParts['title']
        );
    }
}
