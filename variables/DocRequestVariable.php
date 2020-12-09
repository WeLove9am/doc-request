<?php
/**
 * Doc Request plugin for Craft CMS 3.x
 *
 * Doc Request
 *
 * @link      https://welove9am.com
 * @copyright Copyright (c) 2020 Andy Parsons
 */

namespace welove9am\docrequest\variables;

use craft\elements\Entry;
use welove9am\docrequest\DocRequest;

use Craft;

/**
 * Doc Request Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.docRequest }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Andy Parsons
 * @package   DocRequest
 * @since     1.0.0
 */
class DocRequestVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function getRequests($entryId)
    {
        $req = new \welove9am\docrequest\records\Requests();
        $html = <<<HTML
<table class="editable fullwidth">
    <thead>
        <tr>
            <th scope="col">Date</th>
            <th scope="col">IP</th>
            <th scope="col">Success</th>
        </tr>
    </thead>
    <tbody>
HTML;
        /**
         * @var \welove9am\docrequest\records\Requests $record
         */
        foreach($req->findBySql('SELECT * FROM docrequest_requests WHERE entryID = ' . $entryId)->all() as $record) {
            $success = $record->success ? 'true' : 'false';
            $html .= <<<HTML
<tr>
    <td class="undefined singleline-cell textual">
        {$record->dateCreated}
    </td>
    <td class="undefined singleline-cell textual">
        {$record->request_ip}
    </td>
    <td class="undefined singleline-cell textual">
        {$success}
    </td>
</tr>
HTML;
        }
        $html .= <<<HTML
</tbody>
</table>
HTML;


        return $html;
    }
}
