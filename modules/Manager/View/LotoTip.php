<div class="pg-title form-title">Thêm lô tô tip</div>
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
			<form id="loto-tip" data-parsley-validate="" class="form-horizontal form-label-left" action="<?php echo  Mava_Url::buildLink('manager/loto-tip')?>" method="post">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-6">
                        <div class="form-group">
                            <label class="control-label" for="first-name">Ngày <span class="required">*</span>
                            </label>
                            <input class="form-control xs-date-picker" required type="text" id="tip_date" name="tip_date" data-func="show_result_province_by_date" value="<?php echo $default_date ?>">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-6">
                        <div class="form-group">
                            <label class="control-label" for="first-name">Tỉnh thành <span class="required">*</span>
                            </label>
                            <select required class="form-control" id="xs_result_province" name="region_code">
                                <?php if(is_array($default_province) && count($default_province) > 0){
                                    foreach ($default_province as $k=>$v) {?>
                                    <option value="<?php echo ($v[3]=='truyen-thong'?'tt':$k) ?>"><?php echo $v[2] ?></option>
                                 <?php
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="first-name">Gói <span class="required">*</span>
                    </label>
                    <select required class="form-control" id="pack" name="pack">
                        <option value="1">Song thủ, bạch thủ VIP</option>
                        <option value="2">Song thủ, bạch thủ Siêu VIP</option>
                        <option value="3">Đầu đuôi giải Đặc Biệt</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-4 col-sm-4 col-4">
                        <div class="form-group">
                            <label class="control-label" for="first-name">Số 1 <span class="required">*</span>
                            </label>
                            <input required maxlength="2" type="text" id="num_1" name="num_1" value="" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-4">
                        <div class="form-group">
                            <label class="control-label" for="first-name">Số 2 <span class="required">*</span>
                            </label>
                            <input required maxlength="2" type="text" id="num_2" name="num_2" value="" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-4">
                        <div class="form-group">
                            <label class="control-label" for="first-name">Số 3 <span class="required">*</span>
                            </label>
                            <input required maxlength="2" type="text" id="num_3" name="num_3" value="" class="form-control">
                        </div>
                    </div>
                </div>
				<div class="form-group col-md-offset-3">
					<button type="submit" class="btn btn-success">Xác nhận</button>
					<a href="list-loto-tip"><button type="button" class="btn btn-primary">Hủy bỏ</button></a>
				</div>

			</form>
		</div>
	</div>
</div>