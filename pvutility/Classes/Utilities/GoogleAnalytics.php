<?php

namespace PV\Utility\Utilities;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('pvutility') . 'Resources/Public/PHP/google-api-php/vendor/autoload.php');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



/**
 * Description of GoogleAnalytics
 *
 * @author PV1
 */
class GoogleAnalytics {
    
  
    /**
     * Google Analytics Data
     * 
     */
    
    public function GAData($credential_file='',$profile_id,$optParams=array())
    {
        
        if( !empty($credential_file))
        {    
            $analytics = $this->initializeAnalytics($credential_file);
            //$profile = $this->getFirstProfileId($analytics);
            //DebuggerUtility::var_dump($profile);
            $results = $this->getResults($analytics, $profile_id, $optParams);
            return $results;
        } 
        else
        {
           throw new \Exception("No credential file");
            
        }    

        
    }        
    
    
    public function initializeAnalytics($credential_file)
    {
      
        // Create and configure a new client object.
        $client = new \Google_Client();
        $client->setApplicationName("Hello Analytics Reporting");
        $client->setAuthConfig($credential_file);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $analytics = new \Google_Service_Analytics($client);

        return $analytics;
    }

    private function getFirstProfileId($analytics) {
      // Get the user's first view (profile) ID.

      // Get the list of accounts for the authorized user.
      $accounts = $analytics->management_accounts->listManagementAccounts();

      if (count($accounts->getItems()) > 0) {
        $items = $accounts->getItems();
        $firstAccountId = $items[0]->getId();

        // Get the list of properties for the authorized user.
        $properties = $analytics->management_webproperties
            ->listManagementWebproperties($firstAccountId);

        if (count($properties->getItems()) > 0) {
          $items = $properties->getItems();
          $firstPropertyId = $items[0]->getId();

          // Get the list of views (profiles) for the authorized user.
          $profiles = $analytics->management_profiles
              ->listManagementProfiles($firstAccountId, $firstPropertyId);

          if (count($profiles->getItems()) > 0) {
            $items = $profiles->getItems();

            // Return the first view (profile) ID.
            return $items[0]->getId();

          } else {
            throw new Exception('No views (profiles) found for this user.');
          }
        } else {
          throw new Exception('No properties found for this user.');
        }
      } else {
        throw new Exception('No accounts found for this user.');
      }
      
    }

    private function getResults($analytics, $profileId, $optParams) {
     
        //DebuggerUtility::var_dump($profileId);
        
        $start_date = $optParams['start_date'];
        $end_date = $optParams['end_date'];
        
        unset($optParams['start_date']);
        unset($optParams['end_date']);

      return $analytics->data_ga->get(
         'ga:' . $profileId,
          $start_date,
          $end_date,
          'ga:sessions',
          $optParams);


    }




    private function printDataTable(&$results) {
      if (count($results->getRows()) > 0) {

            $table = '<table>';

        // Print headers.
        $table .= '<tr>';

        foreach ($results->getColumnHeaders() as $header) {
          $table .= '<th>' . $header->name . '</th>';
        }
        $table .= '</tr>';

        // Print table rows.
        foreach ($results->getRows() as $row) {
          $table .= '<tr>';
            foreach ($row as $cell) {
              $table .= '<td>'
                     . htmlspecialchars($cell, ENT_NOQUOTES)
                     . '</td>';
            }
          $table .= '</tr>';
        }
        $table .= '</table>';

      } else {
        $table .= '<p>No Results Found.</p>';
      }
      print $table;
    }

    
    
}
