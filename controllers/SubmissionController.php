<?php
/**
 * DocRequest plugin for Craft CMS 3.x
 *
 * Document Request Logic
 *
 * @link      https://welove9am.com
 * @copyright Copyright (c) 2020 Andy Parsons
 */

namespace welove9am\docrequest\controllers;

use craft\db\Query;
use dolphiq\redirect\RedirectPlugin;
use welove9am\docrequest\DocRequest;

use Craft;
use craft\web\Controller;

/**
 * Submission Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Andy Parsons
 * @package   DocRequest
 * @since     1.0.0
 */
class SubmissionController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/doc-request/submission
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $customer = new \welove9am\docrequest\records\Requests();
        $customer->entryId = Craft::$app->request->post('entryId');
        $customer->request_ip = Craft::$app->request->getUserIP();

        $entry = Craft::$app->getEntries()->getEntryById($customer->entryId);
        $section = $entry->documentRequestType->one();
        $password =  $section->passwordForUser;
        $whitepaperId = $section->pdflink->one()->id;
        if (Craft::$app->request->post('password') === $password) {
            $customer->success = true;
            $customer->save();
            $asset = \craft\elements\Asset::find()
                ->id($whitepaperId)
                ->one();
            header("Content-type: ".$asset->getMimeType());
            header("Content-Disposition: inline; filename=".$asset->getFilename());
            echo $asset->getContents();
            return null;
        } else {
            $customer->success = false;
            $customer->save();
            $referrer = explode("?", Craft::$app->request->getReferrer())[0];
            return $this->redirect($referrer .'?error=true');
        }
    }
}
