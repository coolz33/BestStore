<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = '02e0ffc511ef7ba8b9ba6b74e583b3da';
    private $api_key_secret = 'da6101104d0561205a0df11dc95c510b';

    public function send($to_email,$to_name,$subject,$content)
    {
        $mj = new Client($this->api_key,$this->api_key_secret,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "coolz33@yahoo.fr",
                        'Name' => "Sebastien Agier"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 3942859,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() ;
    }
}