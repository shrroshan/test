<?php

namespace App;

class Functions
{
    protected $country;
    protected $abbrCountry;
    protected $enquirySource;
    protected $intake;
    protected $enquiryFor;
    protected $program;
    protected $interestedInToeflVouchers;

    public function __construct()
    {        
    }

    public function sanitize_text($str) {
        $string = trim($str);        
        $string = stripslashes($string);
        $string = htmlspecialchars($string);        

        $badWords = array("/delete/i", "/select/i", "/update/i", "/union/i", "/insert/i", "/drop/i", "/--/i");
        $string = preg_replace($badWords, "", $string);

        return trim($string);
    }

    public function url_title($str)
	{
		$separator = '_';

		$q_separator = preg_quote($separator, '#');

		$trans = array(
			'&.+?;'			=> '',
			'[^\w\d _-]'		=> '',
			'\s+'			=> $separator,
			'('.$q_separator.')+'	=> $separator
		);

		$str = strip_tags($str);
		foreach ($trans as $key => $val)
		{
			$str = preg_replace('#'.$key.'#i'.(TRUE ? 'u' : ''), $val, $str);
		}

		$str = strtolower(str_replace("-", "_", $str));

		return trim(trim($str, $separator));
	}

    public function mapAbbrCountry($country) {
        $this->abbrCountry = "";
        $country = $this->url_title($country);

        switch($country) {
            case 'australia':
            case 'au':
                $this->abbrCountry = "AU";
                break;

            case 'canada':
            case 'ca':
                $this->abbrCountry = "CA";
                break;

            case 'dubai':
            case 'united_arab_emirates':
            case 'ae':
                $this->abbrCountry = "AE";
                break;                

            case 'germany':
            case 'de':
                $this->abbrCountry = "DE";
                break;   
                
            case 'hungary':
            case 'hu':
                $this->abbrCountry = "HU";
                break; 
                
            case 'india':
            case 'in':
                $this->abbrCountry = "IN";
                break; 
                
            case 'ireland':
            case 'ie':
                $this->abbrCountry = "IE";
                break; 
                
            case 'nepal':
            case 'np':
                $this->abbrCountry = "NP";
                break;
                
            case 'new_zealand':
            case 'nz':
                $this->abbrCountry = "NZ";
                break;   
                
            case 'singapore':
            case 'sg':
                $this->abbrCountry = "SG";
                break;   
                
            case 'united_kingdom':
            case 'uk':
            case 'gb':
                $this->abbrCountry = "GB";
                break;   
                
            case 'united_states':
            case 'usa':
            case 'us':
                $this->abbrCountry = "US";
                break;   
        }

        return $this->abbrCountry;
    }

    public function mapEnquirySource($source) {
        $this->enquirySource = "";
        $source = $this->url_title($source);

        switch($source) {
            case 'fb':
            case 'facebook':
                $this->enquirySource = "facebook";
                break;

            case 'google':
                $this->enquirySource = "google-ads";
                break; 

            case 'ig':
            case 'instagram':
                $this->enquirySource = "instagram";
                break; 

            case 'linkedin':
                $this->enquirySource = "linked-in";
                break;
                
            case 'twitter':
                $this->enquirySource = "twitter";
                break;
                
            case 'youtube':
                $this->enquirySource = "you-tube";
                break;

            case 'website':
                $this->enquirySource = "website";
                break;
        }

        return $this->enquirySource;
    }

    public function getBranchDetails($nearest_office)
    {
        $nearest_office = $this->url_title($nearest_office);
        
        $arrOffice = [
            'ahmedabad' => [
                'branch_id' => 7793,
                'branch_name' => 'Ahmedabad Ajay Kumar Bhatia',
                'owning_user' => 'Ajay Bhatia',
                'owning_email' => 'businesshead.gujarat@studysquare.com',
                'country' => 'IN',
                'region' => 'GJ',
                'locality' => 'Ahmedabad'
            ],
            'surat' => [
                'branch_id' => 7793,
                'branch_name' => 'Ahmedabad Ajay Kumar Bhatia',
                'owning_user' => 'Ajay Bhatia',
                'owning_email' => 'businesshead.gujarat@studysquare.com',
                'country' => 'IN',
                'region' => 'GJ',
                'locality' => 'Surat'
            ],
            'vadodara' => [
                'branch_id' => 7793,
                'branch_name' => 'Ahmedabad Ajay Kumar Bhatia',
                'owning_user' => 'Ajay Bhatia',
                'owning_email' => 'businesshead.gujarat@studysquare.com',
                'country' => 'IN',
                'region' => 'GJ',
                'locality' => 'Vadodara'
            ],
            'indore' => [
                'branch_id' => 7912,
                'branch_name' => 'Indore Prayas Tarani',
                'owning_user' => 'Prayas Tarani',
                'owning_email' => 'branchhead.indore@studysquare.com',
                'country' => 'IN',
                'region' => 'MP',
                'locality' => 'Indore'
            ],
            'ujjain' => [
                'branch_id' => 7912,
                'branch_name' => 'Indore Prayas Tarani',
                'owning_user' => 'Prayas Tarani',
                'owning_email' => 'branchhead.indore@studysquare.com',
                'country' => 'IN',
                'region' => 'MP',
                'locality' => 'Ujjain'
            ],
            'chandigarh' => [
                'branch_id' => 7794,
                'branch_name' => 'Chandigarh Ashish Kundra',
                'owning_user' => 'Ashish Kundra',
                'owning_email' => 'branchhead.chd@studysquare.com',
                'country' => 'IN',
                'region' => 'PB',
                'locality' => 'Chandigarh'
            ],
            'rajpura' => [
                'branch_id' => 7801,
                'branch_name' => 'Rajpura Harjot Kaur',
                'owning_user' => 'Harjot Kaur',
                'owning_email' => 'study.rajpura@studysquare.com',
                'country' => 'IN',
                'region' => 'PB',
                'locality' => 'Rajpura'
            ],
            'ludhiana' => [
                'branch_id' => 7928,
                'branch_name' => 'Ludhiana Siddharth Sharma',
                'owning_user' => 'Siddharth Sharma',
                'owning_email' => 'branchhead.ldh@studysquare.com',
                'country' => 'IN',
                'region' => 'PB',
                'locality' => 'Ludhiana'
            ],
            'jalandhar' => [
                'branch_id' => 7919,
                'branch_name' => 'Jalandhar Rahul Dev',
                'owning_user' => 'Rahul Dev Thakur',
                'owning_email' => 'branchhead.jln@studysquare.com',
                'country' => 'IN',
                'region' => 'PB',
                'locality' => 'Jalandhar'
            ],
            'hyderabad' => [
                'branch_id' => 7799,
                'branch_name' => 'Hyderabad Sandeep Reddy Nagireddy',
                'owning_user' => 'Sandeep Reddy',
                'owning_email' => 'businesshead.hyd@studysquare.com',
                'country' => 'IN',
                'region' => 'TG',
                'locality' => 'Hyderabad'
            ],
            'kathmandu' => [
                'branch_id' => 7923,
                'branch_name' => 'Kathmandu Ritesh Acharya',
                'owning_user' => 'Ritesh Acharya',
                'owning_email' => 'visahead.ktm@studysquare.com',
                'country' => 'NP',
                'region' => 'BA',
                'locality' => 'Kathmandu'
            ],
            'melbourne' => [
                'branch_id' => 7915,
                'branch_name' => 'Jaipur Ashish Mathur',
                'owning_user' => 'Ashish Mathur',
                'owning_email' => 'operations.in@studysquare.com',
                'country' => 'AU',
                'region' => 'VIC',
                'locality' => 'Melbourne'
            ],
            'amritsar' => [
                'branch_id' => 7919,
                'branch_name' => 'Jalandhar Rahul Dev',
                'owning_user' => 'Rahul Dev Thakur',
                'owning_email' => 'branchhead.jln@studysquare.com',
                'country' => 'IN',
                'region' => 'PB',
                'locality' => 'Amritsar'
            ],
            'kochi' => [
                'branch_id' => 8318,
                'branch_name' => 'Kochi Anit Joy',
                'owning_user' => 'Anit Joy',
                'owning_email' => 'teamleadcanada.kochi@studysquare.com',
                'country' => 'IN',
                'region' => 'KL',
                'locality' => 'Kochi'
            ]
        ];

        return $arrOffice[$nearest_office];
    }

    public function mapIntake($intake)
    {
        $this->intake = "";
        $intake = $this->url_title($intake);

        switch($intake) {
            case 'q1___jan_feb_mar':
                $this->intake = 'q1---jan-feb-mar';
                break;

            case 'q2___apr_may_jun':
                $this->intake = 'q2---apr-may-jun';
                break;

            case 'q3___jul_aug_sep':
                $this->intake = 'q3---jul-aug-sep';
                break;

            case 'q4___oct_nov_dec':
                $this->intake = 'q4---oct-nov-dec';
                break;
        }

        return $this->intake;
    }

    public function mapEnquiryFor($enquiryFor)
    {
        $this->enquiryFor = "";
        $enquiryFor = $this->url_title($enquiryFor);

        switch($enquiryFor) {
            case 'university_admission_abroad':
                $this->enquiryFor = 'university-admission-abroad';
                break;

            case 'english_test_prep_ielts_pte_toefl':
                $this->enquiryFor = 'english-test-prep-ielts-pte-toefl';
                break;

            case 'buy_toefl_exam_discounted_vouchers':
                $this->enquiryFor = 'buy-toefl-exam-discounted-vouchers';
                break;

            case 'student_visa_lodgment':
                $this->enquiryFor = 'student-visa-lodgment';
                break;
        }

        return $this->enquiryFor;
    }

    public function mapProgram($program)
    {
        $this->program = "";
        $program = $this->url_title($program);

        switch($program) {
            case 'diploma':
                $this->program = 'diploma';
                break;

            case 'bachelors':
                $this->program = 'bachelors';
                break;

            case 'masters':
                $this->program = 'masters';
                break;
        }

        return $this->program;
    }

    public function mapInterestedVoucher($interestedInToeflVouchers)
    {
        $this->interestedInToeflVouchers = "";
        $interestedInToeflVouchers = $this->url_title($interestedInToeflVouchers);

        switch($interestedInToeflVouchers) {
            case 'yes':
                $this->interestedInToeflVouchers = 'yes';
                break;

            case 'no':
                $this->interestedInToeflVouchers = 'no';
                break;            
        }

        return $this->interestedInToeflVouchers;
    }

    public function unicode_urldecode($url) {
        preg_match_all('/%u([[:alnum:]]{4})/', $url, $a);
    
        foreach ($a[1] as $uniord) {
            $dec = hexdec($uniord);
            $utf = '';
        
            if ($dec < 128)
            {
                $utf = chr($dec);
            }
            else if ($dec < 2048)
            {
                $utf = chr(192 + (($dec - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
            else
            {
                $utf = chr(224 + (($dec - ($dec % 4096)) / 4096));
                $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
                $utf .= chr(128 + ($dec % 64));
            }
        
            $url = str_replace('%u'.$uniord, $utf, $url);
        }

        $url = str_replace("&amp;", "&", $url);
    
        return urldecode($url);
    }
}


