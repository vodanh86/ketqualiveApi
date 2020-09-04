<div class="pg-title form-title">Cộng COIN</div>
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
			<form id="incre-coin" data-parsley-validate="" class="form-horizontal form-label-left" action="<?php echo $userId > 0 ? Mava_Url::buildLink('manager/increase-coin?user_id='.$userId) :  Mava_Url::buildLink('manager/increase-coin')?>" method="post">
				<div class="form-group">
			      	<label class="control-label" for="first-name">UserID <span class="required">*</span>
			      	</label>
                    <input required type="text" id="user_id" name="user_id" value="<?php echo $userId > 0 ? $userId : '' ?>" class="form-control">
			   	</div>
			   	<div class="form-group">
			      	<label class="control-label" for="first-name">Số coin <span class="required">*</span>
			      	</label>
			        <input required type="text" id="coin" name="coin" value="<?php echo $coin > 0 ? $coin : '' ?>" class="form-control">
			   	</div>
				<div class="form-group col-md-offset-3">
					<button type="submit" class="btn btn-success">Xác nhận</button>
					<a href="user"><button type="button" class="btn btn-primary">Hủy bỏ</button></a>
				</div>
			</form>
		</div>
	</div>
</div>