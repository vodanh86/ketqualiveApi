<!doctype html>
<html>
<head>
    <meta name="robots" content="nofollow, noindex" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <title>
        <?php echo Mava_Application::get('seo/title'); ?>
    </title>
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('dialog.default'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('dropdown.default'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('select.default'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('manager/manager_mini') .'?v='. time(); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('bootstrap/css/bootstrap.min'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('font-awesome/css/font-awesome.min'); ?>" />
    <link rel="stylesheet" href="<?php echo Mava_View::getCssUrl('dateranger/daterangepicker'); ?>" />
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('global'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('popper'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('bootstrap/js/bootstrap.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('app'); ?>?v=21"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('modal'); ?>?v=21"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('highcharts/highcharts'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('dateranger/moment.min'); ?>"></script>
    <script type="text/javascript" src="<?php echo Mava_View::getJsUrl('dateranger/daterangepicker'); ?>"></script>
    <script type="text/javascript">
        var DOMAIN = '<?php echo Mava_Url::getDomainUrl(); ?>';
        var CSRF = '<?php echo Mava_Session::getCSRF(); ?>';
    </script>
</head>
<body>
  <div class="container-fluid">
      <div class="page-header text-center">
         <img src="../template/css/images/logo.png">
         <h4 class="page-title pt-4"><?php echo __('Hệ thống vận hành XS');?></h4>
      </div>
      <?php echo $pageContent; ?>
  </div>   
</body>
<?php if(Mava_Session::get('otm') != ""){?>
      <!-- one time message -->
      <script type="text/javascript">
          MP.notice.show('<?php echo Mava_Session::get('otm'); ?>', 'success', 5);
      </script>
<?php }
Mava_Session::set('otm', "");

if(Mava_Session::get('otem') != ""){ ?>
      <!-- one time error message -->
      <script type="text/javascript">
          MP.notice.show('<?php echo Mava_Session::get('otem'); ?>', 'danger', 5);
      </script>
<?php }
Mava_Session::set('otem', "");
?>
</html>