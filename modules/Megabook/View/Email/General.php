<div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">
    <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">
        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">
            <tbody>
            <tr>
                <td style="vertical-align: middle;" align="left">
                    <a href="<?php echo Mava_Url::getPageLink(''); ?>" target="_blank">
                        <img src="http://megabook.vn/data/images/banner/2017/08/02/82ebe91b3300445f08e65d1abf510b4e_1501691174.png" alt="<?php echo __('site_name'); ?>" style="border:none;">
                    </a>
                </td>
                <td style="vertical-align: middle;font-size: 17px;font-weight: bold;color: #F90;" align="right"><?php echo __('hotline') .': '. Mava_Application::getOptions()->phone_support; ?></td>
            </tr>
            </tbody>
        </table>
        <div style="padding: 40px; background: #fff;border: 1px solid #cccccc;">
            <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                <tbody>
                <tr>
                    <td>
                        <?php echo $message; ?>
                        <p><b>- <?php echo __('site_name'); ?></b></p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px">
            <p> © <?php echo date('Y') .' '. __('site_name'); ?> <br>
        </div>
    </div>
</div>