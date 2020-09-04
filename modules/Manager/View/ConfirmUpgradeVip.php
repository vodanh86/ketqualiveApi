<div class="pg-title form-title">Xác nhận nâng cấp VIP</div>
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
			<form id="upgrade-vip" data-parsley-validate="" class="form-horizontal form-label-left" action="<?php echo Mava_Url::buildLink('manager/confirm-upgrade-vip?user_id='.$userId.'&num='.$num)?>" method="post">
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
			      	<label class="control-label" for="first-name">Thời hạn Vip hiện tại</label>
			      	</label>
                    <input required readonly type="text" value="<?php echo $user['expired_vip'] > 0 ? date('d-m-Y',$user['expired_vip']) : ''?>" class="form-control">
			   	</div>
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">Số ngày</label>
			      	</label>
			        <input required readonly type="text" id="num" name="num" value="<?php echo $num > 0 ? $num : '' ?>" class="form-control">
			   	</div>
				<div class="form-group col-md-offset-3">
					<button type="submit" class="btn btn-success">Nâng cấp</button>
					<a href="<?php echo Mava_Url::getPageLink('manager/upgrade-vip?user_id='.$userId.'&num='.$num)?>"><button type="button" class="btn btn-primary">Quay lại</button></a>
				</div>
			</form>
		</div>
	</div>
</div>