<?php
/**
 * Reviews plugin for Craft CMS 3.x
 *
 * product and store reviews
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2019 Kurious Agency
 */

namespace kuriousagency\reviews\services;

use kuriousagency\reviews\Reviews;
use kuriousagency\reviews\elements\Review;
use kuriousagency\emaileditor\EmailEditor;
use craft\mail\Message;

use Craft;
use craft\base\Component;

/**
 * @author    Kurious Agency
 * @package   Reviews
 * @since     0.0.1
 */
class ReviewsService extends Component
{
    // Public Methods
    // =========================================================================

	public function getReviewById(int $id, $siteId = null)
	{
		$review = Craft::$app->getElements()->getElementById($id, Review::class, $siteId);

		return $review;
	}

	public function sendEmail($review)
    {

		if($review->getEmail() != 'bob') {
			$error = Craft::t('reviews', 'Review with ID “{id}” does not have an email address.', [
				'id' => $review->id
			]);

			Craft::warning($error, __METHOD__);
			
			return false;
		}
		
		
		$renderVariables = [
            'review' => $review,
            'handle' => 'reviewReply'
        ];            

        if(Craft::$app->plugins->isPluginEnabled('email-editor')) {
            $templatePath = EmailEditor::$plugin->emails->getEmailByHandle($renderVariables['handle'])->template;
        } else {
            $templatePath = Reviews::$plugin->getSettings()->templatePath;
        }        

        $view = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();

        $view->setTemplateMode($view::TEMPLATE_MODE_SITE);

        if ($view->doesTemplateExist($templatePath)) {

            $fromName = Craft::$app->systemSettings->getEmailSettings()->fromEmail;
            $fromName = Craft::parseEnv($fromName);

            $newEmail = new Message();
            $newEmail->setTo($review->getEmail());
            $newEmail->setFrom($fromName);
            $newEmail->setSubject('Your review has a new reply');
            $newEmail->variables = $renderVariables;
            $body = $view->renderTemplate($templatePath, $renderVariables);
            $newEmail->setHtmlBody($body);
            // Craft::dd($newEmail);
            if (!Craft::$app->getMailer()->send($newEmail)) {
            
                $error = Craft::t('reviews', 'Email Error');
    
                Craft::error($error, __METHOD__);
                
                Craft::$app->language = $originalLanguage;
                $view->setTemplateMode($oldTemplateMode);

                return false;
            }

        } else {
            $error = Craft::t('reviews', 'Template not found for email with handle “{handle}”.', [
                'handle' => $renderVariables['handle']
            ]);

            Craft::error($error, __METHOD__);
        }
        
        $view->setTemplateMode($oldTemplateMode);

		return true;

    }
}
