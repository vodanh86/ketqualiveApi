<div class="pg-title form-title">Nhập kèo bóng đá</div>
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
			<form id="football-tip" data-parsley-validate="" class="form-horizontal form-label-left" action="<?php echo  Mava_Url::buildLink('manager/football-tip?fixture='.$fixture_id)?>" method="post">
				
				<div class="row">
					<div class="col-md-6 col-sm-6 col-6">
						<div class="form-group">
					      	<label class="control-label" for="first-name">Gói <span class="required">*</span>
					      	</label>
					      	<select required class="form-control" id="pack" name="pack">
					      		<option value="1">Tip free</option>
					      		<option value="2">Super win</option>
					      		<option value="3">Sure win</option>
					      	</select>
					   	</div>
					</div>
					<div class="col-md-6 col-sm-6 col-6">
						<div class="form-group">
					      	<label class="control-label" for="first-name">Ngày <span class="required">*</span>
					      	</label>
					      	<input class="form-control xs-date-picker" required readonly type="text" id="tip_date" name="tip_date" value="<?php echo date('d-m-Y', time()) ?>">
					   	</div>
					</div>
				</div>
				
				<div class="tip-first" id="form_block">
					<div class="form-group">
				      	<label class="control-label" for="first-name">Ngày giờ thi đấu <span class="required">*</span>
				      	</label>
				        <input required readonly type="text" id="time" name="time" value="<?php echo (isset($fixtureData['time']) ? $fixtureData['time']: '') ?>" class="form-control">
				   	</div>

				   	<div class="row">
						<div class="col-md-6 col-sm-6 col-6">
							<div class="form-group">
						      	<label class="control-label" for="first-name">Đội nhà <span class="required">*</span>
						      	</label>
						        <input required readonly type="text" id="home" name="home" value="<?php echo (isset($fixtureData['home']) ? $fixtureData['home']: '') ?>" class="form-control">
						   	</div>
						</div>
						<div class="col-md-6 col-sm-6 col-6">
							<div class="form-group">
						      	<label class="control-label" for="first-name">Đội khách <span class="required">*</span>
						      	</label>
						        <input required readonly type="text" id="away" name="away" value="<?php echo (isset($fixtureData['away']) ? $fixtureData['away']: '') ?>" class="form-control">
						   	</div>
						</div>
					</div>
				   	
				   	<div class="row">
						<div class="col-md-6 col-sm-6 col-6">
							<div class="form-group">
						      	<label class="control-label" for="first-name">Kèo tài xỉu <span class="required">*</span>
						      	</label>
						      	<select required class="form-control" id="taixiu" name="taixiu">
						      		<option value="Tài">Tài</option>
						      		<option value="Xỉu">Xỉu</option>
					      		</select>
						   	</div>
						</div>
						<div class="col-md-6 col-sm-6 col-6">
							<div class="form-group">
						      	<label class="control-label" for="first-name">Nhập số <span class="required">*</span>
						      	</label>
						        <input required type="number" id="num" name="num" value="" class="form-control">
						   	</div>
						</div>
					</div>

				   	<div class="form-group">
				      	<label class="control-label" for="first-name">Tỉ số dự đoán <span class="required">*</span>
				      	</label>
				        <input required type="text" id="ft" name="ft" value="" class="form-control">
				   	</div>
				</div>

<!--				<div class="form-group block-add">-->
<!--						<i class="fa fa-plus-square" aria-hidden="true"></i><a href="javascript:void(0);" id="add_form_block"> Thêm kèo</a>-->
<!--				</div>-->
			   	

				<div class="form-group col-md-offset-3">
					<button type="submit" class="btn btn-success">Xác nhận</button>
					<a href="list-football-tip"><button type="button" class="btn btn-primary">Hủy bỏ</button></a>
				</div>

			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var formBlockHtml = '<div id="form_block">'+
					'<div class="form-group block-remove">'+
						'<i class="fa fa-minus-square" aria-hidden="true"></i><a href="javascript:void(0);" id="remove_form_block"> Xóa kèo này</a>'+
					'</div>'+
					'<div class="form-group">'+
				      	'<label class="control-label" for="first-name">Ngày giờ thi đấu <span class="required">*</span>'+
				      	'</label>'+
				        '<input required type="text" id="time" name="time[]" value="" class="form-control">'+
				   	'</div>'+

				   	'<div class="row">'+
						'<div class="col-md-6 col-sm-6 col-6">'+
							'<div class="form-group">'+
						      	'<label class="control-label" for="first-name">Đội nhà <span class="required">*</span>'+
						      	'</label>'+
						        '<input required type="text" id="home" name="home[]" value="" class="form-control">'+
						   '	</div>'+
						'</div>'+
						'<div class="col-md-6 col-sm-6 col-6">'+
							'<div class="form-group">'+
						      	'<label class="control-label" for="first-name">Đội khách <span class="required">*</span>'+
						      	'</label>'+
						        '<input required type="text" id="away" name="away[]" value="" class="form-control">'+
						   	'</div>'+
						'</div>'+
					'</div>'+

				   	'<div class="row">'+
						'<div class="col-md-6 col-sm-6 col-6">'+
							'<div class="form-group">'+
						      	'<label class="control-label" for="first-name">Kèo tài xỉu <span class="required">*</span>'+
						      	'</label>'+
						        '<select required class="form-control" id="taixiu" name="taixiu[]">'+
						      		'<option value="Tài">Tài</option>'+
						      		'<option value="Xỉu">Xỉu</option>'+
					      		'</select>'+
						   	'</div>'+
						'</div>'+
						'<div class="col-md-6 col-sm-6 col-6">'+
							'<div class="form-group">'+
						      	'<label class="control-label" for="first-name">Nhập số <span class="required">*</span>'+
						      	'</label>'+
						        '<input required type="text" id="num" name="num[]" value="" class="form-control">'+
						   	'</div>'+
						'</div>'+
					'</div>'+

				   	'<div class="form-group">'+
				      	'<label class="control-label" for="first-name">Tỉ số dự đoán <span class="required">*</span>'+
				      	'</label>'+
				        '<input required type="text" id="ft" name="ft[]" value="" class="form-control">'+
				   	'</div>'+
				'</div>';

    	$('#add_form_block').click(function(){
            $('#form_block').append(formBlockHtml);
        });

        $('#form_block').on('click', '#remove_form_block', function(e){
            e.preventDefault();
            $(this).parent('div').parent('div').remove();
        });

    })
</script>