<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CoolBrew Column Helpers
 *
 * @package		CoolBrew
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Jim Applegate
 * @link		
 */

// ------------------------------------------------------------------------

/**
 * Country Array
 *
 * Returns an array of countries to be used in a pulldown menu
 *
 * @access	public
 * @return	array
 */	
function country_array()
{
   $countries = array(
      'United States' => 'United States',
      'Canada' => 'Canada',
      'Denmark' => 'Denmark',
      'France' => 'France',
      'Italy' => 'Italy',
      'Japan' => 'Japan',
      'Mexico' => 'Mexico',
      'Spain' => 'Spain',
      'United Kingdom' => 'United Kingdom',
      '' => '-------------',
      'Afghanistan' => 'Afghanistan',
      'Aland Islands' => 'Aland Islands',
      'Albania' => 'Albania',
      'Algeria' => 'Algeria',
      'American Samoa' => 'American Samoa',
      'Andorra' => 'Andorra',
      'Angola' => 'Angola',
      'Anguilla' => 'Anguilla',
      'Antarctica' => 'Antarctica',
      'Antigua And Barbuda' => 'Antigua And Barbuda',
      'Argentina' => 'Argentina',
      'Armenia' => 'Armenia',
      'Aruba' => 'Aruba',
      'Australia' => 'Australia',
      'Austria' => 'Austria',
      'Azerbaijan' => 'Azerbaijan',
      'Bahamas' => 'Bahamas',
      'Bahrain' => 'Bahrain',
      'Bangladesh' => 'Bangladesh',
      'Barbados' => 'Barbados',
      'Belarus' => 'Belarus',
      'Belgium' => 'Belgium',
      'Belize' => 'Belize',
      'Benin' => 'Benin',
      'Bermuda' => 'Bermuda',
      'Bhutan' => 'Bhutan',
      'Bolivia' => 'Bolivia',
      'Bosnia and Herzegowina' => 'Bosnia and Herzegowina',
      'Botswana' => 'Botswana',
      'Bouvet Island' => 'Bouvet Island',
      'Brazil' => 'Brazil',
      'British Indian Ocean Territory' => 'British Indian Ocean Territory',
      'Brunei Darussalam' => 'Brunei Darussalam',
      'Bulgaria' => 'Bulgaria',
      'Burkina Faso' => 'Burkina Faso',
      'Burundi' => 'Burundi',
      'Cambodia' => 'Cambodia',
      'Cameroon' => 'Cameroon',
      'Cape Verde' => 'Cape Verde',
      'Cayman Islands' => 'Cayman Islands',
      'Central African Republic' => 'Central African Republic',
      'Chad' => 'Chad',
      'Chile' => 'Chile',
      'China' => 'China',
      'Christmas Island' => 'Christmas Island',
      'Cocos (Keeling) Islands' => 'Cocos (Keeling) Islands',
      'Colombia' => 'Colombia',
      'Comoros' => 'Comoros',
      'Congo' => 'Congo',
      'Congo, the Democratic Republic of the' => 'Congo, the Democratic Republic of the',
      'Cook Islands' => 'Cook Islands',
      'Costa Rica' => 'Costa Rica',
      'Cote d\'Ivoire' => 'Cote d\'Ivoire',
      'Croatia' => 'Croatia',
      'Cuba' => 'Cuba',
      'Cyprus' => 'Cyprus',
      'Czech Republic' => 'Czech Republic',
      'Djibouti' => 'Djibouti',
      'Dominica' => 'Dominica',
      'Dominican Republic' => 'Dominican Republic',
      'Ecuador' => 'Ecuador',
      'Egypt' => 'Egypt',
      'El Salvador' => 'El Salvador',
      'Equatorial Guinea' => 'Equatorial Guinea',
      'Eritrea' => 'Eritrea',
      'Estonia' => 'Estonia',
      'Ethiopia' => 'Ethiopia',
      'Falkland Islands (Malvinas)' => 'Falkland Islands (Malvinas)',
      'Faroe Islands' => 'Faroe Islands',
      'Fiji' => 'Fiji',
      'Finland' => 'Finland',
      'French Guiana' => 'French Guiana',
      'French Polynesia' => 'French Polynesia',
      'French Southern Territories' => 'French Southern Territories',
      'Gabon' => 'Gabon',
      'Gambia' => 'Gambia',
      'Georgia' => 'Georgia',
      'Germany' => 'Germany',
      'Ghana' => 'Ghana',
      'Gibraltar' => 'Gibraltar',
      'Greece' => 'Greece',
      'Greenland' => 'Greenland',
      'Grenada' => 'Grenada',
      'Guadeloupe' => 'Guadeloupe',
      'Guam' => 'Guam',
      'Guatemala' => 'Guatemala',
      'Guernsey' => 'Guernsey',
      'Guinea' => 'Guinea',
      'Guinea-Bissau' => 'Guinea-Bissau',
      'Guyana' => 'Guyana',
      'Haiti' => 'Haiti',
      'Heard and McDonald Islands' => 'Heard and McDonald Islands',
      'Holy See (Vatican City State)' => 'Holy See (Vatican City State)',
      'Honduras' => 'Honduras',
      'Hong Kong' => 'Hong Kong',
      'Hungary' => 'Hungary',
      'Iceland' => 'Iceland',
      'India' => 'India',
      'Indonesia' => 'Indonesia',
      'Iran, Islamic Republic of' => 'Iran, Islamic Republic of',
      'Iraq' => 'Iraq',
      'Ireland' => 'Ireland',
      'Isle of Man' => 'Isle of Man',
      'Israel' => 'Israel',
      'Jamaica' => 'Jamaica',
      'Jersey' => 'Jersey',
      'Jordan' => 'Jordan',
      'Kazakhstan' => 'Kazakhstan',
      'Kenya' => 'Kenya',
      'Kiribati' => 'Kiribati',
      'Korea, Democratic People\'s Republic of' => 'Korea, Democratic People\'s Republic of',
      'Korea, Republic of' => 'Korea, Republic of',
      'Kuwait' => 'Kuwait',
      'Kyrgyzstan' => 'Kyrgyzstan',
      'Lao People\'s Democratic Republic' => 'Lao People\'s Democratic Republic',
      'Latvia' => 'Latvia',
      'Lebanon' => 'Lebanon',
      'Lesotho' => 'Lesotho',
      'Liberia' => 'Liberia',
      'Libyan Arab Jamahiriya' => 'Libyan Arab Jamahiriya',
      'Liechtenstein' => 'Liechtenstein',
      'Lithuania' => 'Lithuania',
      'Luxembourg' => 'Luxembourg',
      'Macao' => 'Macao',
      'Macedonia, The Former Yugoslav Republic Of' => 'Macedonia, The Former Yugoslav Republic Of',
      'Madagascar' => 'Madagascar',
      'Malawi' => 'Malawi',
      'Malaysia' => 'Malaysia',
      'Maldives' => 'Maldives',
      'Mali' => 'Mali',
      'Malta' => 'Malta',
      'Marshall Islands' => 'Marshall Islands',
      'Martinique' => 'Martinique',
      'Mauritania' => 'Mauritania',
      'Mauritius' => 'Mauritius',
      'Mayotte' => 'Mayotte',
      'Micronesia, Federated States of' => 'Micronesia, Federated States of',
      'Moldova, Republic of' => 'Moldova, Republic of',
      'Monaco' => 'Monaco',
      'Mongolia' => 'Mongolia',
      'Montenegro' => 'Montenegro',
      'Montserrat' => 'Montserrat',
      'Morocco' => 'Morocco',
      'Mozambique' => 'Mozambique',
      'Myanmar' => 'Myanmar',
      'Namibia' => 'Namibia',
      'Nauru' => 'Nauru',
      'Nepal' => 'Nepal',
      'Netherlands' => 'Netherlands',
      'Netherlands Antilles' => 'Netherlands Antilles',
      'New Caledonia' => 'New Caledonia',
      'New Zealand' => 'New Zealand',
      'Nicaragua' => 'Nicaragua',
      'Niger' => 'Niger',
      'Nigeria' => 'Nigeria',
      'Niue' => 'Niue',
      'Norfolk Island' => 'Norfolk Island',
      'Northern Mariana Islands' => 'Northern Mariana Islands',
      'Norway' => 'Norway',
      'Oman' => 'Oman',
      'Pakistan' => 'Pakistan',
      'Palau' => 'Palau',
      'Palestinian Territory, Occupied' => 'Palestinian Territory, Occupied',
      'Panama' => 'Panama',
      'Papua New Guinea' => 'Papua New Guinea',
      'Paraguay' => 'Paraguay',
      'Peru' => 'Peru',
      'Philippines' => 'Philippines',
      'Pitcairn' => 'Pitcairn',
      'Poland' => 'Poland',
      'Portugal' => 'Portugal',
      'Puerto Rico' => 'Puerto Rico',
      'Qatar' => 'Qatar',
      'Reunion' => 'Reunion',
      'Romania' => 'Romania',
      'Russian Federation' => 'Russian Federation',
      'Rwanda' => 'Rwanda',
      'Saint Barthelemy' => 'Saint Barthelemy',
      'Saint Helena' => 'Saint Helena',
      'Saint Kitts and Nevis' => 'Saint Kitts and Nevis',
      'Saint Lucia' => 'Saint Lucia',
      'Saint Pierre and Miquelon' => 'Saint Pierre and Miquelon',
      'Saint Vincent and the Grenadines' => 'Saint Vincent and the Grenadines',
      'Samoa' => 'Samoa',
      'San Marino' => 'San Marino',
      'Sao Tome and Principe' => 'Sao Tome and Principe',
      'Saudi Arabia' => 'Saudi Arabia',
      'Senegal' => 'Senegal',
      'Serbia' => 'Serbia',
      'Seychelles' => 'Seychelles',
      'Sierra Leone' => 'Sierra Leone',
      'Singapore' => 'Singapore',
      'Slovakia' => 'Slovakia',
      'Slovenia' => 'Slovenia',
      'Solomon Islands' => 'Solomon Islands',
      'Somalia' => 'Somalia',
      'South Africa' => 'South Africa',
      'South Georgia and the South Sandwich Islands' => 'South Georgia and the South Sandwich Islands',
      'Sri Lanka' => 'Sri Lanka',
      'Sudan' => 'Sudan',
      'Suriname' => 'Suriname',
      'Svalbard and Jan Mayen' => 'Svalbard and Jan Mayen',
      'Swaziland' => 'Swaziland',
      'Sweden' => 'Sweden',
      'Switzerland' => 'Switzerland',
      'Syrian Arab Republic' => 'Syrian Arab Republic',
      'Taiwan, Province of China' => 'Taiwan, Province of China',
      'Tajikistan' => 'Tajikistan',
      'Tanzania, United Republic of' => 'Tanzania, United Republic of',
      'Thailand' => 'Thailand',
      'Timor-Leste' => 'Timor-Leste',
      'Togo' => 'Togo',
      'Tokelau' => 'Tokelau',
      'Tonga' => 'Tonga',
      'Trinidad and Tobago' => 'Trinidad and Tobago',
      'Tunisia' => 'Tunisia',
      'Turkey' => 'Turkey',
      'Turkmenistan' => 'Turkmenistan',
      'Turks and Caicos Islands' => 'Turks and Caicos Islands',
      'Tuvalu' => 'Tuvalu',
      'Uganda' => 'Uganda',
      'Ukraine' => 'Ukraine',
      'United Arab Emirates' => 'United Arab Emirates',
      'United States Minor Outlying Islands' => 'United States Minor Outlying Islands',
      'Uruguay' => 'Uruguay',
      'Uzbekistan' => 'Uzbekistan',
      'Vanuatu' => 'Vanuatu',
      'Venezuela' => 'Venezuela',
      'Viet Nam' => 'Viet Nam',
      'Virgin Islands, British' => 'Virgin Islands, British',
      'Virgin Islands, U.S.' => 'Virgin Islands, U.S.',
      'Wallis and Futuna' => 'Wallis and Futuna',
      'Western Sahara' => 'Western Sahara',
      'Yemen' => 'Yemen',
      'Zambia' => 'Zambia',
      'Zimbabwe' => 'Zimbabwe',
   );
   
   return $countries;
}

function timezone_array()
{
   $timezones = array(
      'Hawaii' => '(GMT-10:00) Hawaii',
      'Alaska' => '(GMT-09:00) Alaska',
      'Pacific' => '(GMT-08:00) Pacific Time (US &amp; Canada)',
      'Arizona' => '(GMT-07:00) Arizona',
      'Mountain' => '(GMT-07:00) Mountain Time (US &amp; Canada)',
      'Central' => '(GMT-06:00) Central Time (US &amp; Canada)',
      'Eastern' => '(GMT-05:00) Eastern Time (US &amp; Canada)',
      'Indiana' => '(GMT-05:00) Indiana (East)',
      '' => '-------------',
      'International Date Line West' => '(GMT-11:00) International Date Line West',
      'Midway Island' => '(GMT-11:00) Midway Island',
      'Samoa' => '(GMT-11:00) Samoa',
      'Tijuana' => '(GMT-08:00) Tijuana',
      'Chihuahua' => '(GMT-07:00) Chihuahua',
      'Mazatlan' => '(GMT-07:00) Mazatlan',
      'Central America' => '(GMT-06:00) Central America',
      'Guadalajara' => '(GMT-06:00) Guadalajara',
      'Mexico City' => '(GMT-06:00) Mexico City',
      'Monterrey' => '(GMT-06:00) Monterrey',
      'Saskatchewan' => '(GMT-06:00) Saskatchewan',
      'Bogota' => '(GMT-05:00) Bogota',
      'Lima' => '(GMT-05:00) Lima',
      'Quito' => '(GMT-05:00) Quito',
      'Atlantic' => '(GMT-04:00) Atlantic Time (Canada)',
      'Caracas' => '(GMT-04:00) Caracas',
      'La Paz' => '(GMT-04:00) La Paz',
      'Santiago' => '(GMT-04:00) Santiago',
      'Newfoundland' => '(GMT-03:30) Newfoundland',
      'Brasilia' => '(GMT-03:00) Brasilia',
      'Buenos Aires' => '(GMT-03:00) Buenos Aires',
      'Georgetown' => '(GMT-03:00) Georgetown',
      'Greenland' => '(GMT-03:00) Greenland',
      'Mid-Atlantic' => '(GMT-02:00) Mid-Atlantic',
      'Azores' => '(GMT-01:00) Azores',
      'Cape Verde Is.' => '(GMT-01:00) Cape Verde Is.',
      'Casablanca' => '(GMT) Casablanca',
      'Dublin' => '(GMT) Dublin',
      'Edinburgh' => '(GMT) Edinburgh',
      'Lisbon' => '(GMT) Lisbon',
      'London' => '(GMT) London',
      'Monrovia' => '(GMT) Monrovia',
      'Amsterdam' => '(GMT+01:00) Amsterdam',
      'Belgrade' => '(GMT+01:00) Belgrade',
      'Berlin' => '(GMT+01:00) Berlin',
      'Bern' => '(GMT+01:00) Bern',
      'Bratislava' => '(GMT+01:00) Bratislava',
      'Brussels' => '(GMT+01:00) Brussels',
      'Budapest' => '(GMT+01:00) Budapest',
      'Copenhagen' => '(GMT+01:00) Copenhagen',
      'Ljubljana' => '(GMT+01:00) Ljubljana',
      'Madrid' => '(GMT+01:00) Madrid',
      'Paris' => '(GMT+01:00) Paris',
      'Prague' => '(GMT+01:00) Prague',
      'Rome' => '(GMT+01:00) Rome',
      'Sarajevo' => '(GMT+01:00) Sarajevo',
      'Skopje' => '(GMT+01:00) Skopje',
      'Stockholm' => '(GMT+01:00) Stockholm',
      'Vienna' => '(GMT+01:00) Vienna',
      'Warsaw' => '(GMT+01:00) Warsaw',
      'West Central Africa' => '(GMT+01:00) West Central Africa',
      'Zagreb' => '(GMT+01:00) Zagreb',
      'Athens' => '(GMT+02:00) Athens',
      'Bucharest' => '(GMT+02:00) Bucharest',
      'Cairo' => '(GMT+02:00) Cairo',
      'Harare' => '(GMT+02:00) Harare',
      'Helsinki' => '(GMT+02:00) Helsinki',
      'Istanbul' => '(GMT+02:00) Istanbul',
      'Jerusalem' => '(GMT+02:00) Jerusalem',
      'Kyev' => '(GMT+02:00) Kyev',
      'Minsk' => '(GMT+02:00) Minsk',
      'Pretoria' => '(GMT+02:00) Pretoria',
      'Riga' => '(GMT+02:00) Riga',
      'Sofia' => '(GMT+02:00) Sofia',
      'Tallinn' => '(GMT+02:00) Tallinn',
      'Vilnius' => '(GMT+02:00) Vilnius',
      'Baghdad' => '(GMT+03:00) Baghdad',
      'Kuwait' => '(GMT+03:00) Kuwait',
      'Moscow' => '(GMT+03:00) Moscow',
      'Nairobi' => '(GMT+03:00) Nairobi',
      'Riyadh' => '(GMT+03:00) Riyadh',
      'St. Petersburg' => '(GMT+03:00) St. Petersburg',
      'Volgograd' => '(GMT+03:00) Volgograd',
      'Tehran' => '(GMT+03:30) Tehran',
      'Abu Dhabi' => '(GMT+04:00) Abu Dhabi',
      'Baku' => '(GMT+04:00) Baku',
      'Muscat' => '(GMT+04:00) Muscat',
      'Tbilisi' => '(GMT+04:00) Tbilisi',
      'Yerevan' => '(GMT+04:00) Yerevan',
      'Kabul' => '(GMT+04:30) Kabul',
      'Ekaterinburg' => '(GMT+05:00) Ekaterinburg',
      'Islamabad' => '(GMT+05:00) Islamabad',
      'Karachi' => '(GMT+05:00) Karachi',
      'Tashkent' => '(GMT+05:00) Tashkent',
      'Chennai' => '(GMT+05:30) Chennai',
      'Kolkata' => '(GMT+05:30) Kolkata',
      'Mumbai' => '(GMT+05:30) Mumbai',
      'New Delhi' => '(GMT+05:30) New Delhi',
      'Kathmandu' => '(GMT+05:45) Kathmandu',
      'Almaty' => '(GMT+06:00) Almaty',
      'Astana' => '(GMT+06:00) Astana',
      'Dhaka' => '(GMT+06:00) Dhaka',
      'Novosibirsk' => '(GMT+06:00) Novosibirsk',
      'Sri Jayawardenepura' => '(GMT+06:00) Sri Jayawardenepura',
      'Rangoon' => '(GMT+06:30) Rangoon',
      'Bangkok' => '(GMT+07:00) Bangkok',
      'Hanoi' => '(GMT+07:00) Hanoi',
      'Jakarta' => '(GMT+07:00) Jakarta',
      'Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
      'Beijing' => '(GMT+08:00) Beijing',
      'Chongqing' => '(GMT+08:00) Chongqing',
      'Hong Kong' => '(GMT+08:00) Hong Kong',
      'Irkutsk' => '(GMT+08:00) Irkutsk',
      'Kuala Lumpur' => '(GMT+08:00) Kuala Lumpur',
      'Perth' => '(GMT+08:00) Perth',
      'Singapore' => '(GMT+08:00) Singapore',
      'Taipei' => '(GMT+08:00) Taipei',
      'Ulaan Bataar' => '(GMT+08:00) Ulaan Bataar',
      'Urumqi' => '(GMT+08:00) Urumqi',
      'Osaka' => '(GMT+09:00) Osaka',
      'Sapporo' => '(GMT+09:00) Sapporo',
      'Seoul' => '(GMT+09:00) Seoul',
      'Tokyo' => '(GMT+09:00) Tokyo',
      'Yakutsk' => '(GMT+09:00) Yakutsk',
      'Adelaide' => '(GMT+09:30) Adelaide',
      'Darwin' => '(GMT+09:30) Darwin',
      'Brisbane' => '(GMT+10:00) Brisbane',
      'Canberra' => '(GMT+10:00) Canberra',
      'Guam' => '(GMT+10:00) Guam',
      'Hobart' => '(GMT+10:00) Hobart',
      'Melbourne' => '(GMT+10:00) Melbourne',
      'Port Moresby' => '(GMT+10:00) Port Moresby',
      'Sydney' => '(GMT+10:00) Sydney',
      'Vladivostok' => '(GMT+10:00) Vladivostok',
      'Magadan' => '(GMT+11:00) Magadan',
      'New Caledonia' => '(GMT+11:00) New Caledonia',
      'Solomon Is.' => '(GMT+11:00) Solomon Is.',
      'Auckland' => '(GMT+12:00) Auckland',
      'Fiji' => '(GMT+12:00) Fiji',
      'Kamchatka' => '(GMT+12:00) Kamchatka',
      'Marshall Is.' => '(GMT+12:00) Marshall Is.',
      'Wellington' => '(GMT+12:00) Wellington',
      'Nuku\'alofa' => '(GMT+13:00) Nuku\'alofa',
   );
   
   return $timezones;
}

?>