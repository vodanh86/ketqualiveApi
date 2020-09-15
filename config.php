<?php

$config = array();

    $config['default_controller'] = 'Index_Controller_Index';

    $config['default_action'] = 'index';

    $config['environment'] = 'dev';

    $config['debug'] = true;

    $config['router'] = array(
        'update-result-loto-tip' => 'Loto_Controller_Index/updateResultLotoTip',
        'refund-coin-loto-tip' => 'Loto_Controller_Index/refundCoin',
        'update-result-football-tip' => 'Football_Controller_Index/updateResultFootballTip',
        'refund-coin-football-tip' => 'Football_Controller_Index/refundCoin',
        'get-latest-loto-result' => 'Index_Controller_Index/getLatestResult',
        'callback-card' => 'API_Controller_User/callBackCard',
        'add-question' => 'Index_Controller_Ask/add',
        'add-subscribe' => 'Index_Controller_Subscribe/index',
        'cron' => 'Mava_Controller_Cronjob/index',
        'admin-phrase.js' => 'Mava_Controller_Site/phraseAdmin',
        'phrase.js' => 'Mava_Controller_Site/phrase',
        'admin' => 'Admin_Controller_Index/index',
        'signup' => 'Mava_Controller_User/signup',
        'register' => 'Mava_Controller_User/signup',
        'login' => 'Mava_Controller_User/login',
        'ajax_login' => 'Mava_Controller_User/ajaxLogin',
        'ajax_signup' => 'Mava_Controller_User/ajaxSignup',
        'change-password' => 'Profile_Controller_Profile/password',
        'logout' => 'Mava_Controller_User/logout',
        'login_facebook_success' => 'Mava_Controller_User/loginFacebookSuccess',
        'login_with_facebook' => 'Mava_Controller_User/loginWithFacebook',
        'login_with_google' => 'Mava_Controller_User/loginWithGoogle',
        'signup_with_facebook' => 'Mava_Controller_User/signupWithFacebook',
        'signup_with_google' => 'Mava_Controller_User/signupWithGoogle',
        'forgotpassword' => 'Mava_Controller_User/forgotpassword',
        'forgotpassword_confirm' => 'Mava_Controller_User/forgotpasswordConfirm',
        'active_account' => 'Mava_Controller_User/activeAccount',
        'phone_active' => 'Mava_Controller_User/activePhone',
        'resend_email_active' => 'Mava_Controller_User/resendActiveAccount',
        'resend_phone_active' => 'Mava_Controller_User/resendActivePhone',
        'upload_image' => 'Mava_Controller_Site/upload_image',
        'upload_thumbnail_image' => 'Mava_Controller_Site/upload_thumbnail',
        'hoi-dap' => 'Index_Controller_Ask/index',
        'thumb_(width:number)_(height:number)_(zc:number)/(src:any)' => 'Mava_Controller_Site/thumb',
        'search' => 'Index_Controller_Search/index',
        'crawl' => 'Loto_Controller_Index/crawl',

        'football/api' => 'Football_Controller_Index/api',
        'football/crawl-date' => 'Football_Controller_Index/crawlDate',
        'football/crawl-league' => 'Football_Controller_Index/crawlLeague',
        'football/crawl-round' => 'Football_Controller_Index/crawlRound',
        'football/crawl-match' => 'Football_Controller_Index/crawlMatch',
        'football/crawl-remain-match' => 'Football_Controller_Index/crawlRemainMatch',
        'football/crawl-not-finish' => 'Football_Controller_Index/crawlNotFinish',
        'football/crawl-live' => 'Football_Controller_Index/crawlLive',
        'football/crawl-remain-date' => 'Football_Controller_Index/crawlRemainDate',
        'football/crawl-today' => 'Football_Controller_Index/crawlToday',
        'api/(_type:any)/(_action:any)' => 'API_Controller_(_type:c)/(_action:l)',
        'league-icon/icon-(file_name:any)' => 'Football_Controller_Index/leagueIcon',
        'ket-qua-xo-so-truyen-thong-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'tt']],
        'ket-qua-xo-so-dien-toan-123-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => '123']],
        'ket-qua-xo-so-dien-toan-636-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => '636']],
        'ket-qua-xo-so-than-tai-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'tht']],
        'ket-qua-xo-so-binh-dinh-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'bd']],
        'ket-qua-xo-so-da-nang-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'dn']],
        'ket-qua-xo-so-dak-lak-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'dl']],
        'ket-qua-xo-so-dak-nong-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'dng']],
        'ket-qua-xo-so-gia-lai-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'gl']],
        'ket-qua-xo-so-khanh-hoa-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'kh']],
        'ket-qua-xo-so-kon-tum-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'kt']],
        'ket-qua-xo-so-ninh-thuan-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'nt']],
        'ket-qua-xo-so-quang-binh-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'qb']],
        'ket-qua-xo-so-quang-ngai-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'qn']],
        'ket-qua-xo-so-quang-nam-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'qna']],
        'ket-qua-xo-so-quang-tri-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'qt']],
        'ket-qua-xo-so-thua-thien-hue-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'tth']],
        'ket-qua-xo-so-phu-yen-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'py']],
        'ket-qua-xo-so-an-giang-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'ag']],
        'ket-qua-xo-so-bac-lieu-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'bl']],
        'ket-qua-xo-so-ben-tre-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'bt']],
        'ket-qua-xo-so-binh-duong-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'bdu']],
        'ket-qua-xo-so-binh-phuoc-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'bp']],
        'ket-qua-xo-so-binh-thuan-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'bth']],
        'ket-qua-xo-so-ca-mau-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'cm']],
        'ket-qua-xo-so-can-tho-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'ct']],
        'ket-qua-xo-so-da-lat-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'dla']],
        'ket-qua-xo-so-dong-nai-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'dna']],
        'ket-qua-xo-so-dong-thap-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'dt']],
        'ket-qua-xo-so-hau-giang-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'hg']],
        'ket-qua-xo-so-ho-chi-minh-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'hcm']],
        'ket-qua-xo-so-kien-giang-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'kg']],
        'ket-qua-xo-so-long-an-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'la']],
        'ket-qua-xo-so-soc-trang-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'st']],
        'ket-qua-xo-so-tay-ninh-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'tn']],
        'ket-qua-xo-so-tien-giang-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'tg']],
        'ket-qua-xo-so-tra-vinh-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'tv']],
        'ket-qua-xo-so-vinh-long-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'vl']],
        'ket-qua-xo-so-vung-tau-ngay-{day:number}-{month:number}-{year:number}' => ['action' => 'Index_Controller_Index/index','params' => ['pv' => 'vt']],
        
        'nap-tien' => ['action' => 'Topup_Controller_Index/index','params' =>['pv' => 'tt']],
        'ket-qua-xo-so-truyen-thong' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'tt']],
        'ket-qua-xo-so-truyen-thong-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'tt']], 
        'ket-qua-xo-so-binh-dinh' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'bd']],
        'ket-qua-xo-so-binh-dinh-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'bd']], 
        'ket-qua-xo-so-da-nang' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'dn']],
        'ket-qua-xo-so-da-nang-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'dn']], 
        'ket-qua-xo-so-dak-lak' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'dl']],
        'ket-qua-xo-so-dak-lak-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'dl']], 
        'ket-qua-xo-so-dak-nong' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'dng']],
        'ket-qua-xo-so-dak-nong-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'dng']], 
        'ket-qua-xo-so-gia-lai' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'gl']],
        'ket-qua-xo-so-gia-lai-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'gl']], 
        'ket-qua-xo-so-khanh-hoa' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'kh']],
        'ket-qua-xo-so-khanh-hoa-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'kh']], 
        'ket-qua-xo-so-kon-tum' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'kt']],
        'ket-qua-xo-so-kon-tum-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'kt']], 
        'ket-qua-xo-so-ninh-thuan' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'nt']],
        'ket-qua-xo-so-ninh-thuan-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'nt']], 
        'ket-qua-xo-so-quang-binh' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'qb']],
        'ket-qua-xo-so-quang-binh-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'qb']], 
        'ket-qua-xo-so-quang-ngai' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'qn']],
        'ket-qua-xo-so-quang-ngai-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'qn']], 
        'ket-qua-xo-so-quang-nam' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'qna']],
        'ket-qua-xo-so-quang-nam-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'qna']], 
        'ket-qua-xo-so-quang-tri' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'qt']],
        'ket-qua-xo-so-quang-tri-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'qt']], 
        'ket-qua-xo-so-thua-thien-hue' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'tth']],
        'ket-qua-xo-so-thua-thien-hue-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'tth']], 
        'ket-qua-xo-so-phu-yen' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'py']],
        'ket-qua-xo-so-phu-yen-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'py']], 
        'ket-qua-xo-so-an-giang' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'ag']],
        'ket-qua-xo-so-an-giang-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'ag']], 
        'ket-qua-xo-so-bac-lieu' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'bl']],
        'ket-qua-xo-so-bac-lieu-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'bl']], 
        'ket-qua-xo-so-ben-tre' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'bt']],
        'ket-qua-xo-so-ben-tre-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'bt']], 
        'ket-qua-xo-so-binh-duong' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'bdu']],
        'ket-qua-xo-so-binh-duong-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'bdu']], 
        'ket-qua-xo-so-binh-phuoc' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'bp']],
        'ket-qua-xo-so-binh-phuoc-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'bp']], 
        'ket-qua-xo-so-binh-thuan' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'bth']],
        'ket-qua-xo-so-binh-thuan-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'bth']], 
        'ket-qua-xo-so-ca-mau' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'cm']],
        'ket-qua-xo-so-ca-mau-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'cm']], 
        'ket-qua-xo-so-can-tho' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'ct']],
        'ket-qua-xo-so-can-tho-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'ct']], 
        'ket-qua-xo-so-da-lat' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'dla']],
        'ket-qua-xo-so-da-lat-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'dla']], 
        'ket-qua-xo-so-dong-nai' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'dna']],
        'ket-qua-xo-so-dong-nai-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'dna']], 
        'ket-qua-xo-so-dong-thap' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'dt']],
        'ket-qua-xo-so-dong-thap-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'dt']], 
        'ket-qua-xo-so-hau-giang' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'hg']],
        'ket-qua-xo-so-hau-giang-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'hg']], 
        'ket-qua-xo-so-ho-chi-minh' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'hcm']],
        'ket-qua-xo-so-ho-chi-minh-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'hcm']], 
        'ket-qua-xo-so-kien-giang' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'kg']],
        'ket-qua-xo-so-kien-giang-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'kg']], 
        'ket-qua-xo-so-long-an' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'la']],
        'ket-qua-xo-so-long-an-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'la']], 
        'ket-qua-xo-so-soc-trang' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'st']],
        'ket-qua-xo-so-soc-trang-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'st']], 
        'ket-qua-xo-so-tay-ninh' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'tn']],
        'ket-qua-xo-so-tay-ninh-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'tn']], 
        'ket-qua-xo-so-tien-giang' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'tg']],
        'ket-qua-xo-so-tien-giang-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'tg']], 
        'ket-qua-xo-so-tra-vinh' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'tv']],
        'ket-qua-xo-so-tra-vinh-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'tv']], 
        'ket-qua-xo-so-vinh-long' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'vl']],
        'ket-qua-xo-so-vinh-long-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'vl']], 
        'ket-qua-xo-so-vung-tau' => ['action' => 'Loto_Controller_Index/result','params' =>['pv' => 'vt']],
        'ket-qua-xo-so-vung-tau-tu-ngay-{from:any}-den-ngay-{to:any}' => ['action' => 'Loto_Controller_Index/result','params' => ['pv' => 'vt']],

        'do-ve-so-(province_slug:any)-ngay-(date:any)-so-(number:any)' => 'Index_Controller_Index/doVeSo',

        'soi-cau-theo-so' => 'Loto_Controller_Stats/soiCauTheoSo',
        'soi-cau-theo-so-(number:any)' => 'Loto_Controller_Stats/soiCauTheoSo',

        'soi-cau-theo-tinh' => 'Loto_Controller_Stats/soiCauTheoTinh',
        'soi-cau-theo-tinh-(province_slug:any)-ngay-(date:any)' => 'Loto_Controller_Stats/soiCauTheoTinh',

        'chi-tiet-cau-lo-to-so-(number:any)-tai-vi-tri-dau-(dau:any)-cuoi-(cuoi:any)' => 'Loto_Controller_Stats/chiTietCauLotoTheoSo',

        'chi-tiet-cau-lo-to-theo-tinh-(province_slug:any)-ngay-(date:any)-so-(number:any)-tai-vi-tri-dau-(dau:any)-cuoi-(cuoi:any)' => 'Loto_Controller_Stats/chiTietCauLotoTheoTinh',

        'soi-cau-bach-thu' => 'Loto_Controller_Stats/soiCauBachThu',
        'soi-cau-bach-thu-ngay-(date:any)' => 'Loto_Controller_Stats/soiCauBachThu',

        'chi-tiet-cau-lo-to-bach-thu-ngay-(date:any)-so-(number:any)-tai-vi-tri-dau-(dau:any)-cuoi-(cuoi:any)' => 'Loto_Controller_Stats/chiTietCauLotoBachThu',

        'soi-cau-ve-hai-nhay' => 'Loto_Controller_Stats/soiCauVeHaiNhay',
        'soi-cau-ve-hai-nhay-ngay-(date:any)' => 'Loto_Controller_Stats/soiCauVeHaiNhay',

        'chi-tiet-cau-lo-to-ve-hai-nhay-ngay-(date:any)-so-(number:any)-tai-vi-tri-dau-(dau:any)-cuoi-(cuoi:any)' => 'Loto_Controller_Stats/chiTietCauLotoVeHaiNhay',

        'soi-cau-dac-biet' => 'Loto_Controller_Stats/soiCauDacBiet',
        'soi-cau-dac-biet-ngay-(date:any)' => 'Loto_Controller_Stats/soiCauDacBiet',

        'chi-tiet-cau-dac-biet-ngay-(date:any)-so-(number:any)-tai-vi-tri-dau-(dau:any)-cuoi-(cuoi:any)' => 'Loto_Controller_Stats/chiTietCauDacBiet',

        'thong-ke-lo-gan-theo-tinh' => 'Loto_Controller_Stats/thongKeLoGan',
        'thong-ke-lo-gan-(province_slug:any)' => 'Loto_Controller_Stats/thongKeLoGan',

        'thong-ke-tan-suat-lo' => 'Loto_Controller_Stats/thongKeTanSuatLo',
        'thong-ke-tan-suat-lo-(province_slug:any)-bo-so-(num:any)-voi-bien-do-(volumn:number)' => 'Loto_Controller_Stats/thongKeTanSuatLo',

        'thong-ke-theo-tong' => 'Loto_Controller_Stats/thongKeTheoTong',
        'thong-ke-theo-tong-(province_slug:any)-tu-ngay-(start_time:any)-den-ngay-(end_time:any)-voi-tong-(sum:number)' => 'Loto_Controller_Stats/thongKeTheoTong',

        'thong-ke-lo-ve-nhieu-ve-it' => 'Loto_Controller_Stats/thongKeLoVeNhieuVeIt',
        'thong-ke-lo-(province_slug:any)-loai-(type:any)-voi-bien-do-(volumn:number)' => 'Loto_Controller_Stats/thongKeLoVeNhieuVeIt',

        'thong-ke-lo-roi' => 'Loto_Controller_Stats/thongKeLoRoi',
        'thong-ke-lo-roi-(province_slug:any)' => 'Loto_Controller_Stats/thongKeLoRoi',

        'thong-ke-giai-dac-biet' => 'Loto_Controller_Stats/thongKeGiaiDacBiet',
        'thong-ke-giai-dac-biet-(province_slug:any)' => 'Loto_Controller_Stats/thongKeGiaiDacBiet',

        'thong-ke-chu-ky' => 'Loto_Controller_Stats/thongKeChuKy',
        'thong-ke-chu-ky-bo-so-(nums:any)' => 'Loto_Controller_Stats/thongKeChuKy',
        'thong-ke-chu-ky-dan-lo-to' => 'Loto_Controller_Stats/thongKeChuKyDanLoTo',
        'thong-ke-chu-ky-dan-lo-to-(nums:any)-tu-ngay-(start_time:any)-den-ngay-(end_time:any)' => 'Loto_Controller_Stats/thongKeChuKyDanLoTo',
        'thong-ke-chu-ky-dan-lo-to-theo-tinh' => 'Loto_Controller_Stats/thongKeChuKyDanLoTheoTinh',
        'thong-ke-nhanh' => 'Loto_Controller_Stats/thongKeNhanh',
        'tong-hop-chu-ky-dac-biet' => 'Loto_Controller_Stats/tongHopChuKyDacBiet',
        'thong-ke-chu-ky-dan-dac-biet' => 'Loto_Controller_Stats/thongKeChuKyDanDacBiet',
        'thong-ke-giai-dac-biet-gan' => 'Loto_Controller_Stats/thongKeGiaiDacBietGan',
        'thong-ke-cap-so-anh-em' => 'Loto_Controller_Stats/thongKeCapSoAnhEm',
        'thong-ke-theo-ngay' => 'Loto_Controller_Stats/thongKeTheoNgay',
        'ket-qua-giai-dac-biet-cho-ngay-mai' => 'Loto_Controller_Stats/ketQuaGiaiDacBietChoNgayMai',
        
        'thong-ke-chu-ky-gan-theo-tinh' => 'Loto_Controller_Stats/thongKeChuKyGanTheoTinh',
        'thong-ke-chu-ky-gan-(province_slug:any)-bo-so-(nums:any)' => 'Loto_Controller_Stats/thongKeChuKyGanTheoTinh',
        'thong-ke-nhanh-xo-so-(province_slug:any)-bo-so-(nums:any)-tu-ngay-(start_time:any)-den-ngay-(end_time:any)' => 'Loto_Controller_Stats/thongKeNhanh',
        'tong-hop-chu-ky-dac-biet-tu-ngay-(start_time:any)' => 'Loto_Controller_Stats/tongHopChuKyDacBiet',
        'thong-ke-chu-ky-dan-dac-biet-bo-so-(nums:any)-tu-ngay-(start_time:any)-den-ngay-(end_time:any)' => 'Loto_Controller_Stats/tongHopChuKyDanDacBiet',
        'vietlott-mega-crawl' => 'Vietlott_Controller_Index/crawlMega',
        'vietlott-max4d-crawl' => 'Vietlott_Controller_Index/crawlMax4D',
        'vietlott-power-crawl' => 'Vietlott_Controller_Index/crawlPower',
        'manager/login' => 'Manager_Controller_Manager/login',
        'manager/logout' => 'Manager_Controller_Manager/logout',
        'manager/(action:any)' => 'Manager_Controller_Index/(action:l)',

        'ket-qua-vietlott-mega' => 'Vietlott_Controller_Result/getResultMega',
        'ket-qua-vietlott-max4d' => 'Vietlott_Controller_Result/getResultMax4d',
        'ket-qua-vietlott-max4d-prev-(id:any)' => 'Vietlott_Controller_Result/getResultPrevMax4d',
        'ket-qua-vietlott-max4d-next-(id:any)' => 'Vietlott_Controller_Result/getResultNextMax4d',
        'ket-qua-vietlott-power' => 'Vietlott_Controller_Result/getResultPower'
    );

    $config['uploadImage'] = array(
        'folder' => 'data/images',
        'ext' => array('jpg','gif','jpeg','png','bmp'),
        'maxsize' => 2048
    );

    $config['price'] = array(
        'starter' => 500000,
        'business' => 650000,
        'professional' => 2000000
    );

    $config['franchise'] = array(
        'percent_start' => 30
    );

    $config['passwordMinLength'] = 5;
    $config['passwordMaxLength'] = 32;
    $config['topup'] = array(
        20000 => 3000,
        50000 => 9000,
        100000 => 20000,
        200000 => 45000,
        300000 => 70000,
        500000 => 150000,
        1000000 => 350000
    );

    $config['defaultTimeZone'] = 'Asia/Bangkok';

    $config['defaultLanguage'] = 'vi-VN';

    $config['defaultCurrency'] = 'USD';

    $config['superAdmins'] = '1,2'; // 1,7,5,...

    $config['coin_rate'] = [
        '20k' => 3000,
        '50k' => 9000,
        '100k' => 20000,
        '200k' => 45000,
        '300k' => 70000,
        '500k' => 150000,
        '1000k' => 350000
    ];

    $config['price_buy_vip'] = 4900;

    $config['max_feedback'] = 5;

    $config['loto_package_price'] = [
        'package_1' => 2999,
        'package_2' => 9999,
        'package_3' => 8888
    ];

    $config['football_package_price'] = [
        'package_1' => 0,
        'package_2' => 39999,
        'package_3' => 59999
    ];

    $config['X_RapidAPI_Endpoint'] = 'https://api-football-v1.p.rapidapi.com/v2/';
    $config['X_RapidAPI_Host'] = 'api-football-v1.p.rapidapi.com';
    $config['X_RapidAPI_Key'] = '7785fbc573mshbb0cd2766e2ece1p110abbjsnd6cfba7e502e';

    $config['supervip_rate'] = 0.15;

    $config['supervip_coin'] = 5000000;

    $config['manager'] = [
        ['id' => 1, 'username' => 'vip', 'password' => '5c0dcd4ebb6b53123e0ecc11da23c23a'],
        ['id' => 2, 'username' => 'thend', 'password' => '5c0dcd4ebb6b53123e0ecc11da23c23a'],
        ['id' => 3, 'username' => 'hoanh', 'password' => '5c0dcd4ebb6b53123e0ecc11da23c23a'],
        ['id' => 4, 'username' => 'cuongdm', 'password' => 'e10adc3949ba59abbe56e057f20f883e'],
    ];

    $config['loto_schedule'] = [
        'T2' => [
            'tt' => ['18:15', '18:25', 'Truyền Thống', 'truyen-thong'],
            'tth'=> ['17:15', '17:35', 'Thừa Thiên Huế', 'thua-thien-hue'],
            'py' => ['17:15', '17:35', 'Phú Yên', 'phu-yen'],
            'hcm'=> ['16:15', '16:35', 'Hồ Chí Minh', 'ho-chi-minh'],
            'dt' => ['16:15', '16:35', 'Đồng Tháp', 'dong-thap'],
            'cm' => ['16:15', '16:35', 'Cà Mau', 'ca-mau'],
        ],
        'T3' => [
            'tt' => ['18:15', '18:25', 'Truyền Thống', 'truyen-thong'],
            'qna'=> ['17:15', '17:35', 'Quảng Nam', 'quang-nam'],
            'dl' => ['17:15', '17:35', 'Đắk Lắk', 'dak-lak'],
            'bt' => ['16:15', '16:35', 'Bến Tre', 'ben-tre'],
            'vt' => ['16:15', '16:35', 'Vũng Tàu', 'vung-tau'],
            'bl' => ['16:15', '16:35', 'Bạc Liêu', 'bac-lieu'],
        ],
        'T4' => [
            'tt' => ['18:15', '18:25', 'Truyền Thống', 'truyen-thong'],
            'dn' => ['17:15', '17:35', 'Đà Nẵng', 'da-nang'],
            'kh' => ['17:15', '17:35', 'Khánh Hoà', 'khanh-hoa'],
            'dna'=> ['16:15', '16:35', 'Đồng Nai', 'dong-nai'],
            'ct' => ['16:15', '16:35', 'Cần Thơ', 'can-tho'],
            'st' => ['16:15', '16:35', 'Sóc Trăng', 'soc-trang'],
        ],
        'T5' => [
            'tt' => ['18:15', '18:25', 'Truyền Thống', 'truyen-thong'],
            'bd' => ['17:15', '17:35', 'Bình Định', 'binh-dinh'],
            'qb' => ['17:15', '17:35', 'Quảng Bình', 'quang-binh'],
            'qt' => ['17:15', '17:35', 'Quảng Trị', 'quang-tri'],
            'tn' => ['16:15', '16:35', 'Tây Ninh', 'tay-ninh'],
            'ag' => ['16:15', '16:35', 'An Giang', 'an-giang'],
            'bt' => ['16:15', '16:35', 'Bình Thuận', 'binh-thuan'],
        ],
        'T6' => [
            'tt' => ['18:15', '18:25', 'Truyền Thống', 'truyen-thong'],
            'gl' => ['17:15', '17:35', 'Gia Lai', 'gia-lai'],
            'nt' => ['17:15', '17:35', 'Ninh Thuận', 'ninh-thuan'],
            'vl' => ['16:15', '16:35', 'Vĩnh Long', 'vinh-long'],
            'bdu'=> ['16:15', '16:35', 'Bình Dương', 'binh-duong'],
            'tv' => ['16:15', '16:35', 'Trà Vinh', 'tra-vinh'],
        ],
        'T7' => [
            'tt' => ['18:15', '18:25', 'Truyền Thống', 'truyen-thong'],
            'dn' => ['17:15', '17:35', 'Đà Nẵng', 'da-nang'],
            'qn' => ['17:15', '17:35', 'Quảng Ngãi', 'quang-ngai'],
            'dng'=> ['17:15', '17:35', 'Đắk Nông', 'dak-nong'],
            'hcm'=> ['16:15', '16:35', 'Hồ Chí Minh', 'ho-chi-minh'],
            'la' => ['16:15', '16:35', 'Long An', 'long-an'],
            'hg' => ['16:15', '16:35', 'Hậu Giang', 'hau-giang'],
            'bp' => ['16:15', '16:35', 'Bình Phước', 'binh-phuoc'],
        ],
        'T1' => [
            'tt' => ['18:15', '18:25', 'Truyền Thống', 'truyen-thong'],
            'kh' => ['17:15', '17:35', 'Khánh Hoà', 'khanh-hoa'],
            'kt' => ['17:15', '17:35', 'Kon Tum', 'kon-tum'],
            'tg' => ['16:15', '16:35', 'Tiền Giang', 'tien-giang'],
            'kg' => ['16:15', '16:35', 'Kiên Giang', 'kien-giang'],
            'dla'=> ['16:15', '16:35', 'Đà Lạt', 'da-lat'],
        ]
    ];

    $config['vietlott_schedule'] = [
        'T2' => [
            '3d' => ['18:00', '18:30', 'Max-3D'],
        ],
        'T3' => [
            '4d' => ['18:00', '18:30', 'Max-4D'],
            'power' => ['18:00', '18:30', 'Power'],
        ],
        'T4' => [
            '3d' => ['18:00', '18:30', 'Max-3D'],
            'mega' => ['18:00', '18:30', 'Mega']
        ],
        'T5' => [
            '4d' => ['18:00', '18:30', 'Max-4D'],
            'power' => ['18:00', '18:30', 'Power'],
        ],
        'T6' => [
            '3d' => ['18:00', '18:30', 'Max-3D'],
            'mega' => ['18:00', '18:30', 'Mega']
        ],
        'T7' => [
            '4d' => ['18:00', '18:30', 'Max-4D'],
            'power' => ['18:00', '18:30', 'Power'],
        ],
        'T1' => [
            'mega' => ['18:00', '18:30', 'Mega']
        ],
    ];
    $config['loto_live_offset_minute'] = 10;

    $config['loto_auto_tip'] = false;

    $config['guest_token'] = '75171fa2cccf675d19d9dc5586ee54c7';

    $config['thecao24h_account'] = [
        "username" => "bluray",
        "password" => "hdmi"
    ];

    $config['league_country'] = [
        'Vietnam',
        'World',
        'England',
        'Spain',
        'France',
        'Germany',
        'Italy',
        'Brazil',
        'Japan',
        'Argentina',
        'China',
        'Australia'
    ];

    $config['active_pay'] = 1;

    $config['xboom_info'] = [
        "token" => "c460c6a4-1ecb-3980-af78-bb5e750855f1",
        "url_api" => "https://cv3.xboom.net",
        "path_url_charge_card" => "/restapi/v3.0.1/partner/card"
    ];