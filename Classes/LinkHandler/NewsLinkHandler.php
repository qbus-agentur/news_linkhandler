<?php
namespace Qbus\NewsLinkhandler\LinkHandler;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\RecordList\ElementBrowserRecordList;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Recordlist\LinkHandler\AbstractLinkHandler;
use TYPO3\CMS\Recordlist\LinkHandler\LinkHandlerInterface;
use TYPO3\CMS\Recordlist\Tree\View\LinkParameterProviderInterface;

/**
 * NewsLinkHandler
 *
 * @author Benjamin Franzke <bfr@qbus.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class NewsLinkHandler extends AbstractLinkHandler implements LinkHandlerInterface, LinkParameterProviderInterface
{
    /**
     * Parts of the current link
     *
     * @var array
     */
    protected $linkParts = [];

    /**
     * We don't support updates (yet).
     *
     * @var bool
     */
    protected $updateSupported = false;

    /**
     * Checks if this is the handler for the given link
     *
     * The handler may store this information locally for later usage.
     *
     * @param array $linkParts Link parts as returned from TypoLinkCodecService
     *
     * @return bool
     */
    public function canHandleLink(array $linkParts)
    {
        if (!$linkParts['url']) {
            return false;
        }
        $url = rawurldecode($linkParts['url']);
        if (StringUtility::beginsWith($url, 'news:')) {
            $rel = substr($url, 5);

            // TODO: check if the news is actually available(?)

            $this->linkParts = $linkParts;
            $this->linkParts['uid'] = $rel;

            return true;
        }

        return false;
    }

    /**
     * Format the current link for HTML output
     *
     * @return string
     */
    public function formatCurrentUrl()
    {
        return $this->linkParts['url'];
    }

    /**
     * @return void
     */
    protected function getNewsListing($storagePid)
    {
        $tablesArr = ['tx_news_domain_model_news'];

        $backendUser = $this->getBackendUser();
        $permsClause = $backendUser->getPagePermsClause(1);
        $pageInfo = BackendUtility::readPageAccess($storagePid, $permsClause);

        /* @var $dbList ElementBrowserRecordList */
        $dbList = GeneralUtility::makeInstance(ElementBrowserRecordList::class);
        $dbList->setOverrideUrlParameters($this->getUrlParameters([]));
        $dbList->thisScript = $this->thisScript;
        $dbList->thumbs = false;
        $dbList->localizationView = true;
        $dbList->setIsEditable(false);
        $dbList->calcPerms = $backendUser->calcPerms($pageInfo);
        $dbList->noControlPanels = true;
        $dbList->clickMenuEnabled = false;
        $dbList->tableList = implode(',', $tablesArr);

        $dbList->start(
            $storagePid,
            GeneralUtility::_GP('table'),
            MathUtility::forceIntegerInRange(GeneralUtility::_GP('pointer'), 0, 100000),
            GeneralUtility::_GP('search_field'),
            GeneralUtility::_GP('search_levels'),
            GeneralUtility::_GP('showLimit')
        );

        $dbList->setDispFields();
        $dbList->generateList();

        $out .= $dbList->getSearchBox();

        // Add the HTML for the record list to output variable:
        $out .= $dbList->HTMLcode;

        // Add support for fieldselectbox in singleTableMode
        if ($dbList->table) {
            GeneralUtility::makeInstance(PageRenderer::class)->loadRequireJsModule('TYPO3/CMS/Recordlist/FieldSelectBox');
            $out .= $dbList->fieldSelectBox($dbList->table);
        }

        return $out;
    }

    /**
     * Render the link handler
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function render(ServerRequestInterface $request)
    {
        GeneralUtility::makeInstance(PageRenderer::class)->loadRequireJsModule('TYPO3/CMS/NewsLinkhandler/NewsLinkHandler');
        /* TODO: retrieve from TSconfig */
        $storagePid = 4;

        return $this->getNewsListing($storagePid);
    }

    /**
     * @return string[] Array of body-tag attributes
     */
    public function getBodyTagAttributes()
    {
        return [];
    }

    /**
     * Returns the URL of the current script
     *
     * @return string
     */
    public function getScriptUrl()
    {
        return $this->linkBrowser->getScriptUrl();
    }

    /**
     * @param array $values Array of values to include into the parameters or which might influence the parameters
     *
     * @return string[] Array of parameters which have to be added to URLs
     */
    public function getUrlParameters(array $values)
    {
        return $this->linkBrowser->getUrlParameters($values);
    }

    /**
     * @param array $values Values to be checked
     *
     * @return bool Returns TRUE if the given values match the currently selected item
     */
    public function isCurrentlySelectedItem(array $values)
    {
        return !empty($this->linkParts) && (int)$this->linkParts['uid'] === (int)$values['uid'];
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
