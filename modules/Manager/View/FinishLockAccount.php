<div class="pg-title form-title alert alert-success">Khóa tài khoản thành công </div>
<div class="pg-content form-content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-12">
			<form id="lock-account" data-parsley-validate="" class="form-horizontal form-label-left">
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">UserID</label>
			      	</label>
                    <input required readonly type="text" value="<?php echo $user['user_id']?>" class="form-control">
			   	</div>
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">Tên</label>
			      	</label>
                    <input required readonly type="text" value="<?php echo $user['custom_title']?>" class="form-control">
			   	</div>
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">Ngày hết hạn</label>
			      	</label>
                    <input required readonly type="text" value="<?php echo $user['lock_account'] > 0 ? date('d-m-Y',$user['lock_account']) : ''?>" class="form-control">
			   	</div>
				<div class="form-group col-md-offset-3">
					<a href="<?php echo Mava_Url::getPageLink('manager/activity')?>"><button type="button" class="btn btn-primary">Hoạt động</button></a>
				</div>
			</form>
		</div>
	</div>
</div>