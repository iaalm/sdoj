<?php
/**
 * Sharif Judge online judge
 * @file delete_user.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'users')); ?>
<div id="main_container">
	<div id="page_title"><img src="<?php echo base_url('assets/images/icons/delete_user.png') ?>"/> <span><?php echo $title ?></span></div>
	<div id="main_content">
		<p>Are you sure you want to delete this user?</p>
		<?php echo form_open('users/delete/'.$id); ?>
		<input type="hidden" name="delete" value="delete"/>
		<p class="input_p">
			<input type="checkbox" name="delete_submissions"/> Also delete this user's submissions and submitted codes.
		</p>
		<p class="input_p">
			<input type="submit" class="sharif_input" value="Yes, I'm Sure"/> <?php echo anchor('users',"No, I'm not") ?>
		</p>
		</form>
	</div>
</div>