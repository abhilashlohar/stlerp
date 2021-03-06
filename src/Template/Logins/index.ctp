<?php
$this->Form->templates([
    'inputContainer' => '{{content}}'
]);
?>
<!-- BEGIN LOGIN FORM -->
	<form  method="post">
	<?= $this->Form->create($login,['url'=>'/Logins/index']) ?>
		<h3 class="form-title">Login to your account</h3>
        <div class="alert alert-danger display-hide">
			<button class="close" data-close="alert"></button>
			<span>
			Enter your login ID and password. </span>
		</div>
		<?php if($number==0){?>
			<div class="alert alert-danger ">
				<span>Enter Correct login ID and password. </span>
			</div>
		<?php } ?>
         <?php
		if(!empty($wrong))
		{
		?>
        <div class="alert alert-danger">
			<button class="close" data-close="alert"></button>
			<span>
			<?php echo $wrong; ?> </span>
		</div>
        <?php
		}
		?>
		<div class="form-group">
			<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
			<label class="control-label visible-ie8 visible-ie9">Username</label>
			<div class="input-icon">
				<i class="fa fa-user"></i>
				<?php echo $this->Form->input('username', ['label'=>false,'class' => 'form-control','placeholder'=>'Username']); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<div class="input-icon">
				<i class="fa fa-lock"></i>
				<?php echo $this->Form->input('password', ['label'=>false,'class' => 'form-control','placeholder'=>'Password']); ?>
			</div>
		</div>
        
		<div class="form-actions">
			<label class="checkbox">
			<input type="hidden" name="remember" value="1"/> </label>
			<button type="submit" name="login_submit" class="btn green-haze pull-right">
			Login <i class="m-icon-swapright m-icon-white"></i>
			</button>
		</div>
	<?= $this->Form->end() ?>
	<!-- END LOGIN FORM -->