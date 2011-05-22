<?php
/**
 * servicesAdmin actions.
 *
 * @package    social-media
 * @subpackage social_media
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class servicesAdminActions extends sfActions
{

  public function preExecute()
  {
    if ( $this->getActionName() !== 'index' ) {
      $this->forward404Unless($this->getRequest()->hasParameter('service'));
      $this->service = $this->getRequest()->getParameter('service');
    }
  }

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->connected_services = $this->getUser()->getConnectedOAuthClients();
    $this->unconnected_services = $this->getUser()->getUnConnectedOAuthClients();
  }

  /**
   * Executes sync action
   *
   * @param sfRequest $request A request object
   */
  public function executeSync(sfWebRequest $request)
  {
    switch ($this->service) {
      case 'twitter':
        if ( $count = Doctrine_Core::getTable('TwitterTweet')->sync() ) {
          $this->getUser()->setFlash('notice', sprintf($this->getContext()->getI18N()->__('Synced twitter tweets, %s tweets added.'), $count));
        } else {
          $this->getUser()->setFlash('notice', $this->getContext()->getI18N()->__('Synced twitter tweets, no tweets added.'));
        }
        break;
      case 'hyves':
        if ( $count = Doctrine_Core::getTable('HyvesWww')->sync() ) {
          $this->getUser()->setFlash('notice', sprintf($this->getContext()->getI18N()->__("Synced hyves WieWatWaar's, %s WieWatWaar's added."), $count));
        } else {
          $this->getUser()->setFlash('notice', $this->getContext()->getI18N()->__("Synced hyves WieWatWaar's, no WieWatWaar's added."));
        }
        break;
    }
    $this->redirect('servicesAdmin/index');
  }

  public function executeCallback(sfWebRequest $request)
  {
    if ( $this->service == 'twitter' ) {
      $twitter = $this->getUser()->getOAuthClient($this->service, $request->getGetParameters());
      $this->screen_name = '';
    } else {
      $this->screen_name = '';
    }
  }

  public function executeUser_timeline(sfWebRequest $request)
  {
    $client = $this->getUser()->getOAuthClient($this->service, $request->getGetParameters());
    $this->screen_name = '';
    $this->statuses = array();
    switch ($this->service) {
      case 'twitter':
        $this->screen_name = '';
        $response = $client->get('statuses/user_timeline.json');
        $this->setVar('statuses', json_decode($response['body']), true);
        break;
      case 'facebook':
        $response = $client->get('me');
        $this->screen_name = '';
        $this->statuses = array();
        break;
      case 'hyves':
        $response = $client->get('', array(
          'ha_method' => 'users.getLoggedin'
        ));
        if ( isset($response['body']) ) {
          $responseXml = new SimpleXMLElement($response['body']);
          $response = $client->get('', array(
            'userid' => $responseXml->user->userid, 
            'ha_method' => 'wwws.getByUser'
          ));
          $xml = new DOMDocument();
          $xml->loadXML($response['body']);
          $xpath = new DOMXPath($xml);
          $wwws = $xpath->query('//wwws_getByUser_result/www');
          foreach ($wwws as $www) {
            $status = array();
            foreach ($www->childNodes as $field) {
              $status[$field->nodeName] = $field->nodeValue;
            }
            $this->statuses[] = $status;
          }
        }
        break;
      default:
        $this->screen_name = '';
        $this->statuses = array();
    }
  }

  public function executeMessage(sfWebRequest $request)
  {
    // new form
    $this->form = new sfForm();
    $this->form->setWidgets(array(
      'status_message' => new sfWidgetFormTextarea()
    ));
    $this->form->setValidators(array(
      'status_message' => new sfValidatorString(array(
        'min_length' => 3, 
        'max_length' => 140
      ))
    ));
    $this->form->getWidgetSchema()->setNameFormat('oauth_php[%s]');
    $this->form->getWidgetSchema()->setFormFormatterName('list');
    if ( $request->isMethod('post') ) {
      // bind posted form
      $this->form->bind($request->getParameter('oauth_php'));
      if ( $this->form->isValid() ) {
        $statusMessage = $this->form->getValue('status_message');
        // send status message
        switch ($this->service) {
          case 'twitter':
            try {
              $tweet = Doctrine_Core::getTable('TwitterTweet')->send($statusMessage);
              $this->result = true;
              $this->getUser()->setFlash('notice', sprintf($this->getContext()->getI18N()->__('Tweet message is succesfully send with the API and saved, it has id: %s'), $tweet->getTweetId()));
            } catch (Exception $e) {
              $this->getUser()->setFlash('error', $e->getMessage());
            }
            break;
          case 'hyves':
            try {
              $www = Doctrine_Core::getTable('HyvesWww')->send($statusMessage);
              $this->result = true;
              $this->getUser()->setFlash('notice', sprintf($this->getContext()->getI18N()->__('WieWatWaar message is succesfully send with the API and saved, it has id: %s'), $www->getWwwId()));
            } catch (Exception $e) {
              $this->getUser()->setFlash('error', $e->getMessage());
            }
            break;
        }
      }
    }
  }

  public function executeUnconnect(sfWebRequest $request)
  {
    if ( $this->getUser()->isOAuthConnected($this->service) ) {
      $this->getUser()->getOAuthClient($this->service, $request->getGetParameters())->unconnect();
    }
    $this->redirect('servicesAdmin/index');
  }
}
