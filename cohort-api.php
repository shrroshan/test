<?php

require_once realpath(__DIR__ . '/vendor/autoload.php');

use App\Functions;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

date_default_timezone_set('Asia/Kolkata');

// the default date format is "Y-m-d\TH:i:sP"
$dateFormat = "Y-m-d g:i a";

// the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
// we now change the default output format according our needs.
$output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";

// finally, create a formatter
$formatter = new LineFormatter($output, $dateFormat);

// Create a handler
$stream = new StreamHandler(dirname(__FILE__) . '/log/info.log', Logger::DEBUG);
$stream->setFormatter($formatter);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$crmObj = new Functions();


if ((! isset($_SERVER['PHP_AUTH_USER'])) || (! isset($_SERVER['PHP_AUTH_PW'])))
{
	header("WWW-Authenticate: Basic realm=\"Private Area\"");
	header('HTTP/1.0 401 Unauthorized');
	print("Sorry, you need proper credentials");
	exit;
}
else
{
	if (($_SERVER['PHP_AUTH_USER'] == $_ENV['AUTH_USERNAME']) && ($_SERVER['PHP_AUTH_PW'] == $_ENV['AUTH_PASSWORD']))
    {
		if ($_SERVER['REQUEST_METHOD'] == "POST")
        {
			header('Content-type: application/json; charset=UTF-8');
            
            $arrJsonData = json_decode(file_get_contents('php://input'), true);

            $errFields = [];

            $crmData = "{" . PHP_EOL;
            $crmData .= "\t\"student\": {";
            $crmData .= "\r\n\t\t\"title\": \"NA\",";

            if (array_key_exists('first_name', $arrJsonData['student'])) {
                $crmData .= "\r\n\t\t\"given_name\": \"" . $crmObj->sanitize_text($arrJsonData['student']['first_name']) . "\",";
            } else {
                $errFields['first_name'] = "can't be blank";
            }

            if (array_key_exists('last_name', $arrJsonData['student'])) {
                $crmData .= "\r\n\t\t\"family_name\": \"" . $crmObj->sanitize_text($arrJsonData['student']['last_name']) . "\",";
            } else {
                $errFields['last_name'] = "can't be blank";
            }

            if (array_key_exists('email', $arrJsonData['student'])) {
                $crmData .= "\r\n\t\t\"email\": \"" . $crmObj->sanitize_text($arrJsonData['student']['email']) . "\",";
            } else {
                $errFields['email'] = "can't be blank";
            }

            if (array_key_exists('mobile', $arrJsonData['student'])) {
                $crmData .= "\r\n\t\t\"mobile\": \"" . $crmObj->sanitize_text($arrJsonData['student']['mobile']) . "\",";
            } else {
                $errFields['mobile'] = "can't be blank";
            }

            if (array_key_exists('home_country', $arrJsonData['student'])) {
                $crmData .= "\r\n\t\t\"home_country\": \"" . $crmObj->mapAbbrCountry($crmObj->sanitize_text($arrJsonData['student']['home_country'])) . "\",";
            } else {
                $errFields['home_country'] = "can't be blank";
            }

            if (array_key_exists('destination_country', $arrJsonData['student'])) {
                $crmData .= "\r\n\t\t\"destination_country\": \"" . $crmObj->mapAbbrCountry($crmObj->sanitize_text($arrJsonData['student']['destination_country'])) . "\",";
            } else {
                $errFields['destination_country'] = "can't be blank";
            }

            if (array_key_exists('nearest_office', $arrJsonData['student'])) {
                $branchDetails = $crmObj->getBranchDetails($arrJsonData['student']['nearest_office']);
                $crmData .= "\r\n\t\t\"home_address\": {";
                $crmData .= "\r\n\t\t\t\"country\": \"" . $crmObj->sanitize_text($branchDetails['country']) ."\",";
                $crmData .= "\r\n\t\t\t\"region\": \"" . $crmObj->sanitize_text($branchDetails['region']) ."\",";
                $crmData .= "\r\n\t\t\t\"locality\": \"" . $crmObj->sanitize_text($branchDetails['locality']) ."\",";
                $crmData .= "\r\n\t\t\t\"street_address\": \"NA\",";
                $crmData .= "\r\n\t\t\t\"sub_locality\": \"NA\",";
                $crmData .= "\r\n\t\t\t\"postcode\": \"NA\"";
                $crmData .= "\r\n\t\t},";
                $crmData .= "\r\n\t\t\"user\": {";
                $crmData .= "\r\n\t\t\t\"email\": \"" . $crmObj->sanitize_text($branchDetails['owning_email']) ."\"";
                $crmData .= "\r\n\t\t},";  
                $crmData .= "\r\n\t\t\"branch\": {";
                $crmData .= "\r\n\t\t\t\"id\": " . $crmObj->sanitize_text($branchDetails['branch_id']);
                $crmData .= "\r\n\t\t},";                
            } else {
                $errFields['nearest_office'] = "can't be blank";
            }  

            if (! array_key_exists('additional_information', $arrJsonData['student'])) {
                $errFields['additional_information'] = "can't be empty";
            } else {
                $crmData .= "\r\n\t\t\"additional_information\": {";            

                if (array_key_exists('enquiry_source', $arrJsonData['student']['additional_information'])) {
                    $crmData .= "\r\n\t\t\t\"enquiry-source\": \"" . $crmObj->mapEnquirySource($crmObj->sanitize_text($arrJsonData['student']['additional_information']['enquiry_source'])) . "\",";
                } else {
                    $errFields['enquiry_source'] = "can't be blank";
                }

                if (array_key_exists('campaign_name', $arrJsonData['student']['additional_information'])) {
                    $crmData .= "\r\n\t\t\t\"campaign-name\": \"" . urldecode($crmObj->sanitize_text($arrJsonData['student']['additional_information']['campaign_name'])) . "\",";
                }

                if (array_key_exists('intake', $arrJsonData['student']['additional_information'])) {
                    $crmData .= "\r\n\t\t\t\"intake\": \"" . $crmObj->mapIntake($crmObj->sanitize_text($arrJsonData['student']['additional_information']['intake'])) . "\",";
                }
    
                if (array_key_exists('program', $arrJsonData['student']['additional_information'])) {
                    $crmData .= "\r\n\t\t\t\"program\": \"" . $crmObj->mapProgram($crmObj->sanitize_text($arrJsonData['student']['additional_information']['program'])) . "\",";
                }
    
                if (array_key_exists('intake_year', $arrJsonData['student']['additional_information'])) {
                    $crmData .= "\r\n\t\t\t\"intake-year\": \"" . $crmObj->sanitize_text($arrJsonData['student']['additional_information']['intake_year']) . "\",";
                }

                if (array_key_exists('interested_in_toefl_vouchers', $arrJsonData['student']['additional_information'])) {
                    $crmData .= "\r\n\t\t\t\"interested-in-discounted-toefl-i-bt-voucher\": \"" . $crmObj->mapInterestedVoucher($crmObj->sanitize_text($arrJsonData['student']['additional_information']['interested_in_toefl_vouchers'])) . "\",";
                }
                
                if (array_key_exists('enquiry_for', $arrJsonData['student']['additional_information'])) {
                    $crmData .= "\r\n\t\t\t\"enquiry-for\": \"" . $crmObj->mapEnquiryFor($crmObj->sanitize_text($arrJsonData['student']['additional_information']['enquiry_for'])) . "\",";
                }

                if (array_key_exists('page_source', $arrJsonData['student']['additional_information'])) {
                    $crmData .= "\r\n\t\t\t\"page-source\": \"" . $crmObj->unicode_urldecode($crmObj->sanitize_text($arrJsonData['student']['additional_information']['page_source'])) . "\",";
                }
    
                $crmData .= "\r\n\t\t\t\"enquiry-date\": \"" . date('Y-m-d') . "\"";
    
                $crmData .= "\r\n\t\t}";
            }                      

            $crmData .= "\r\n\t}";

            $crmData .= "\r\n}";

            if ($errFields)
            {
                exit(json_encode(['errors' => $errFields]));
            }
            else
            {
                $client = new Client([
                    'base_uri' => $_ENV['CRM_BASE_URI'],
                    'auth' => [$_ENV['CRM_USERNAME'], $_ENV['CRM_PASSWORD']]
                ]);
                
                try
				{
					$rsp_insert = $client->request('POST', '/api/v1/students', [		
					    'headers'  => [
					     	'Content-Type' => 'application/json', 
					     	'Accept' => 'application/json'
					    ],
					    'body' => $crmData
					]);

					if ($rsp_insert->getStatusCode() == 200) 
					{
						$body = $rsp_insert->getBody();
			            $arr_body = json_decode($body);

                        $log = new Logger('custom-pool-api');
						$log->pushHandler($stream);
						$log->info("Record Created", ['id' => $arr_body->id, 'portal_url' => $arr_body->portal_url]);

						exit(json_encode($arr_body));
					}
					else
					{
						$response = $e->getResponse()->getBody()->getContents();
						$arrResponse = json_decode($response, true);

						$log = new Logger('custom-pool-api');
						$log->pushHandler($stream);
						$log->error('Error while creating lead with status not matching 200.');

                        exit(json_encode(['errors' => 'Error while creating lead.']));
					}
				}
				catch (\Exception $e)
				{
					$response = $e->getResponse()->getBody()->getContents();
					$arrResponse = json_decode($response, true);

					$log = new Logger('custom-pool-api');
					$log->pushHandler($stream);
					$log->error('Error while creating lead.', $arrResponse);

					exit(json_encode(['errors' => 'Error while creating lead.']));
				}	
            }
		}
        else
        {
			print("Sorry, you need proper request method");
			exit;
		}		
	}
    else
    {
		header("WWW-Authenticate: Basic realm=\"Private Area\"");
		header('HTTP/1.0 401 Unauthorized');
		print("Sorry, you need proper credentials");
		exit;
	}
}