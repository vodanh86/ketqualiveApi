<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 3/24/16
 * @Time: 4:52 PM
 */
use Aws\Ses\SesClient;
class Mava_Mail {
    public static function send($to, $title, $body, $from = 'Hodela <noreply@hodela.com>', $replyTo = array('Hodela <support@hodela.com>'), $returnTo = 'noreply@hodela.com'){
        $client = SesClient::factory(array(
            'region' => 'us-east-1',
            'key'    => 'AKIAJNPZDMO5XJPB6DHA',
            'secret' => '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'
        ));
        return $client->sendEmail(array(
            'Source' => $from,
            'Destination' => array(
                'ToAddresses' => (is_array($to)?$to:array($to))
            ),
            'Message' => array(
                'Subject' => array(
                    'Data' => $title,
                    'Charset' => 'UTF-8',
                ),
                'Body' => array(
                    'Text' => array(
                        'Data' => strip_tags($body),
                        'Charset' => 'UTF-8',
                    ),
                    'Html' => array(
                        'Data' => $body,
                        'Charset' => 'UTF-8',
                    ),
                ),
            ),
            'ReplyToAddresses' => $replyTo,
            'ReturnPath' => $returnTo,
        ));
    }
}