<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0"/>
    <meta http-equiv="Content-Language" content="vi" />
    <meta name="revisit-after" content="1 days" />
    <meta name="apple-mobile-web-app-title" content="<?php echo __('site_name'); ?>" />
    <meta name="description" content="<?php echo htmlspecialchars(Mava_Application::get('seo/description')); ?>" />
    <meta name="keywords" content="<?php echo htmlspecialchars(Mava_Application::get('seo/keywords')); ?>" />
    <meta name="robots" content="<?php echo Mava_Application::get('seo/robots'); ?>" />
    <link href="<?php echo Mava_Application::get('seo/canonical'); ?>" rel="canonical" />
    <meta property="og:type" content="website" />
    <meta property="og:image" itemprop="thumbnailUrl" content="<?php echo Mava_Application::get('seo/image'); ?>" />
    <meta property="og:url" content="<?php echo Mava_Application::get('seo/canonical'); ?>" />
    <meta property="og:title" content="<?php echo Mava_Application::get('seo/title'); ?>" />
    <meta property="og:description" content="<?php echo Mava_Application::get('seo/description'); ?>" />
    <title><?php echo Mava_Application::get('seo/title'); ?></title>
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('bootstrap/css/bootstrap.min'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('font-awesome/css/font-awesome.min'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('jquery.fancybox'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('global'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('app'); ?>" />
    <script type="text/javascript">
        var DOMAIN = '<?php echo Mava_Url::getDomainUrl(); ?>';
        var CSRF = '<?php echo Mava_Session::getCSRF(); ?>';
        var IS_LOGIN = <?php echo is_login()?1:0; ?>;
        var USER_NAME = '<?php echo Mava_Visitor::getInstance()->get('custom_title'); ?>';
        var USER_PHONE = '<?php echo Mava_Visitor::getInstance()->get('phone'); ?>';
    </script>
    <link rel="shortcut icon" href="<?php echo Mava_Url::getDomainUrl(); ?>/favicon.ico" type="image/x-icon" >
    <script type="text/javascript" src="<?php echo Mava_Url::getPageLink('phrase.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('global'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('bootstrap/js/bootstrap.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('app'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('jquery.fancybox-full'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('jquery.elevateZoom.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('json2'); ?>"></script>
    <style>
        <?php echo __('extra_css'); ?>
    </style>
</head>
<body>
<div id="page_container">
    <div id="page_container_inner">
<?php echo $pageContent; ?>
        </div>
        </div>
</body>
</html>
