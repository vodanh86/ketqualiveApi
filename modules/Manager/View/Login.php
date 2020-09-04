<?php if(isset($error) && $error > 0){
  echo '<div class="row">
            <div class="col-md-4 mx-auto">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  '.$message.'
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
                </div>
            </div>
         </div>';
}?>

<div class="row">
    <div class="col-md-4 mx-auto">
        <!-- form card login -->
        <div class="card">
            <div class="card-body">
                <form class="form" role="form" id="formLogin" action="<?php echo Mava_Url::buildLink('manager/login'); ?>" method="POST">
                    <div class="form-group">
                        <label for="uname1"><?= __('Tài khoản');?></label>
                        <input type="text" class="form-control form-control-lg" name="username" id="username" value="<?= isset($username) ? $username : '' ?>">
                    </div>
                    <div class="form-group">
                        <label><?= __('Mật khẩu');?></label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password">
                    </div>                         
                     <button type="submit" class="btn btn-primary btn-lg btn-block" id="btnLogin"><?= __('Đăng nhập');?></button>
                </form>
            </div>
            <!--/card-block-->
        </div>
        <!-- /form card login -->
    </div>
</div>
<!--/row-->

<script type="text/javascript">
    $(document).ready(function() {
        
        $('#formLogin').submit(function() {
            if ($('#username').val() == '') {
                $('#username').focus();
                return false;
            } else if ($('#password').val() == '') {
                $('#password').focus();
                return false;
            } else {
                return true;
            }
            return false;
        });

    });
</script>