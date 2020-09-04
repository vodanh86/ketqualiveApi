<?php
/**
 * @Package:
 * @Author: nguyen dinh the
 * @Date: 5/11/15
 * @Time: 11:43 PM
 */
use Aws\Ses\SesClient;
class Mava_Controller_Cronjob extends Mava_Controller {
    public function indexAction(){
        $type = Mava_Url::getParam('type');
        // email, email_active, email_forgot, email_course, sms, notify
        switch($type){
            case 'send_mail':
                $emailQueueId = (int)Mava_Url::getParam('email_id');
                $token = Mava_Url::getParam('token');

                if($emailQueueId > 0 && (Mava_Url::isValidCSRF() || Mava_String::validateToken($emailQueueId,$token))){
                    $emailQueueModel = $this->_getEmailQueueModel();
                    $email = $emailQueueModel->getEmailQueueById($emailQueueId);
                    if($email){
                        $content = json_decode($email['content'], true);
                        if(is_array($content) && isset($content['title']) && isset($content['body'])){
                            $result = $this->_sendMail($email['email'], $content['title'], $content['body'],__('site_name') .' <noreply@hodela.vn>');
                            if(isset($result['MessageId']) && $result['MessageId'] != ""){
                                // OK
                                $emailQueueDW = $this->_getEmailQueueDataWriter();
                                $emailQueueDW->setExistingData($emailQueueId);
                                $emailQueueDW->delete();
                            }
                        }
                    }
                }
                Mava_Url::responseGifNull();
                break;
            case 'email':
                //TODO cronjob gui email
                Mava_Application::setCache('mail_sending', 1, 300);
                $emailQueueModel = $this->_getEmailQueueModel();
                $emails = $emailQueueModel->getEmailQueueList();
                $count = 0;
                if(is_array($emails) && count($emails) > 0){
                    foreach($emails as $email){
                        $content = json_decode($email['content'], true);
                        if(is_array($content) && isset($content['title']) && isset($content['body'])){
                            $result = $this->_sendMail($email['email'], $content['title'], $content['body'],__('site_name') .' <contact@hodela.com>',array('lienhe.megabook@gmail.com'));
                            if(isset($result['MessageId']) && $result['MessageId'] != ""){
                                // OK
                                $count++;
                                $emailQueueDW = $this->_getEmailQueueDataWriter();
                                $emailQueueDW->setExistingData($email['queue_id']);
                                $emailQueueDW->delete();
                            }
                        }
                    }
                }
                Mava_Application::delCache('mail_sending');
                return $this->responseJson(array('status' => 1, 'sent' => $count));
                break;
            case 'email_active':

                return $this->responseJson(array('status' => 1));
                break;
        }
    }

    /**
     * @desc cron chạy bắn notify trực tiếp
     */
    public function notifyAction(){
        $type = (int)Mava_Url::getParam('type');
        // learn_upcoming_tomorrow: nhắc ngày mai có tiết học
        // learn_upcoming_today: nhắc hôm nay có tiết học
        // learn_upcoming_hour: nhắc trước tiết học 1 tiếng
        // checkout_day: nhắc nhở nộp học phí
        // lesson_uncomplete: nhắc nhở bài học chưa hoàn thành
    }

    /**
     * @param $to
     * @param string $title
     * @param string $body
     * @param string $from
     * @param array $replyTo
     * @param string $returnTo
     */
    protected function _sendMail($to, $title, $body, $from = 'Hodela <noreply@hodela.vn>', $replyTo = array('Hodela <hotro@hodela.vn>'), $returnTo = 'noreply@hodela.vn'){
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

    /**
     * @return Mava_Model_EmailQueue
     */
    protected function _getEmailQueueModel(){
        return $this->getModelFromCache("Mava_Model_EmailQueue");
    }

    /**
     * @return Mava_DataWriter_EmailQueue
     * @throws Mava_Exception
     */
    protected function _getEmailQueueDataWriter(){
        return Mava_DataWriter::create('Mava_DataWriter_EmailQueue');
    }
}