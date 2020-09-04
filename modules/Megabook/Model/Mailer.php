<?php
class Megabook_Model_Mailer extends Mava_Model {
    public static function queue($agencyId, $title, $message){
        $email = '';
        /* @var $agencyModel Megabook_Model_Agency */
        $agencyModel = Mava_Model::create('Megabook_Model_Agency');
        if($agency = $agencyModel->getById($agencyId)){
            if($agency['email'] != "" && Mava_String::isEmail($agency['email'])){
                $email = $agency['email'];
            }else{
                /* @var $userModel Mava_Model_User */
                $userModel = Mava_Model::create('Mava_Model_User');
                if($user = $userModel->getUserById($agency['user_id'])){
                    $email = $user['email'];
                }else{
                    // nothing
                }
            }
        }else{
            // nothing
        }
        if($email != ""){
            /* @var $emailQueueDW Mava_DataWriter_EmailQueue */
            $emailQueueDW = Mava_DataWriter::create('Mava_DataWriter_EmailQueue');
            $emailQueueDW->bulkSet(array(
                    'type' => Mava_Model_EmailQueue::TYPE_GENERAL,
                    'email' => $email,
                    'content' => json_encode(array(
                            'title' => $title,
                            'body' => $message
                        )),
                    'created_date' => time()
                ));
            $emailQueueDW->save();
        }else{
            // nothing
        }
    }
}