<div class="pg-title form-title">Khóa tài khoản</div>
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
			<form id="lock-account" data-parsley-validate="" class="form-horizontal form-label-left" action="<?php echo $userId > 0 ? Mava_Url::buildLink('manager/lock-account?user_id='.$userId) :  Mava_Url::buildLink('manager/lock-account')?>" method="post">
				<div class="form-group">
			      	<label class="control-label" for="first-name">UserID <span class="required">*</span>
			      	</label>
                    <input required type="text" id="user_id" name="user_id" value="<?php echo $userId > 0 ? $userId : '' ?>" class="form-control">
			   	</div>
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">Số ngày <span class="required">*</span>
			      	</label>
			        <input required type="text" id="day" name="day" value="<?php echo $day > 0 ? $day : '' ?>" class="form-control">
			   	</div>
				<div class="form-group col-md-offset-3">
					<button type="submit" class="btn btn-success">Xác nhận</button>
					<a href="user"><button type="button" class="btn btn-primary">Hủy bỏ</button></a>
				</div>
			</form>
		</div>
	</div>
</div>