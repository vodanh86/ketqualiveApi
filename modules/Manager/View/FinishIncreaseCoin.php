<div class="pg-title form-title alert alert-success">Cộng COIN thành công </div>
<div class="pg-content form-content">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-12">
			<form id="incre-coin" data-parsley-validate="" class="form-horizontal form-label-left">
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
			      	<label class="control-label" for="first-name">Coin hiện tại</label>
			      	</label>
                    <input required readonly type="text" value="<?php echo $user['coin']?>" class="form-control">
			   	</div>
				<div class="form-group col-md-offset-3">
					<a href="<?php echo Mava_Url::getPageLink('manager/activity')?>"><button type="button" class="btn btn-primary">Hoạt động</button></a>
				</div>
			</form>
		</div>
	</div>
</div>