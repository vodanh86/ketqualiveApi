<div class="pg-title form-title">Xác nhận khóa tài khoản</div>
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
			<form id="lock-account" data-parsley-validate="" class="form-horizontal form-label-left" action="<?php echo Mava_Url::buildLink('manager/confirm-lock-account?user_id='.$userId.'&day='.$day)?>" method="post">
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
			      	<label class="control-label" for="first-name">Thời hạn khóa hiện tại</label>
			      	</label>
                    <input required readonly type="text" value="<?php echo $user['lock_account'] > 0 ? date('d-m-Y',$user['lock_account']) : ''?>" class="form-control">
			   	</div>
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">Số ngày</label>
			      	</label>
			        <input required readonly type="text" id="day" name="day" value="<?php echo $day > 0 ? $day : '' ?>" class="form-control">
			   	</div>
				<div class="form-group col-md-offset-3">
					<button type="submit" class="btn btn-success">Khóa</button>
					<a href="<?php echo Mava_Url::getPageLink('manager/upgrade-vip?user_id='.$userId.'&day='.$day)?>"><button type="button" class="btn btn-primary">Quay lại</button></a>
				</div>
			</form>
		</div>
	</div>
</div>