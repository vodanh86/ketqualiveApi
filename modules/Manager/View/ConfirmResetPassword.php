<div class="pg-title form-title">Xác nhận đặt lại mật khẩu</div>
<div class="pg-content form-content">
	<?php if(isset($error) && $error > 0){
	  echo '<div class="row">
	            <div class="col-md-12 col-sm-12 col-12">
	                <div class="alert alert-danger alert-dismissible fade show" role="alert">
	                  '.$message.'
	                </div>
	            </div>
	        </div>';
	}?>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-12">
			<form id="reset-pass" data-parsley-validate="" class="form-horizontal form-label-left" action="<?php echo Mava_Url::buildLink('manager/confirm-reset-password?user_id='.$userId.'&password='.$password)?>" method="post">
				<div class="form-group">
			      	<label class="control-label" for="first-name">UserID</label>
			      	</label>
                    <input required readonly type="text" id="user_id" name="user_id" value="<?php echo $userId > 0 ? $userId : '' ?>" class="form-control">
			   	</div>
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">Tên</label>
			      	</label>
                    <input required readonly type="text" value="<?php echo $user['custom_title']?>" class="form-control">
			   	</div>
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">Email</label>
			      	</label>
                    <input required readonly type="text" value="<?php echo $user['email']?>" class="form-control">
			   	</div>
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">Phone</label>
			      	</label>
                    <input required readonly type="text" value="<?php echo $user['phone']?>" class="form-control">
			   	</div>
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">Mật khẩu mới</label>
			      	</label>
			        <input required readonly type="text" id="password" name="password" value="<?php echo $password?>" class="form-control">
			   	</div>
				<div class="form-group col-md-offset-3">
					<button type="submit" class="btn btn-success">Đặt lại</button>
					<a href="<?php echo Mava_Url::getPageLink('manager/reset-password?user_id='.$userId.'&password='.$password)?>"><button type="button" class="btn btn-primary">Quay lại</button></a>
				</div>
			</form>
		</div>
	</div>
</div>