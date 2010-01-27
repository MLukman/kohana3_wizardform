<?php if (!empty($wizard['title'])): ?>
<h2><?php echo $wizard['title']; ?></h2>
<?php endif; ?>

<?php if (!empty($wizard['subtitle'])): ?>
<h3><?php echo $wizard['subtitle']; ?></h3>
<?php endif; ?>

<?php if (!empty($wizard['errormsg'])): ?>
<div class="error">
<?php echo $wizard['errormsg']; ?>
</div>
<?php endif; ?>

<form action="<?php echo htmlentities($wizard['formaction']); ?>" method="post">

<?php echo $wizard['content']; ?>

<div class="buttons">
<?php if ($wizard['save_enabled']): ?>
	<input type="submit" class="button button_save" style="float:right;" name="wizard:save" value="<?php echo htmlentities(Arr::get($wizard, 'savelabel', 'Save')); ?>" />
<?php endif; ?>
<?php if ($wizard['step'] < $wizard['steps'] - 1): ?>
	<input type="submit" class="button button_next" style="float:right;" name="wizard:next" value="<?php echo htmlentities(Arr::get($wizard, 'nextlabel', 'Next >')); ?>" />
<?php endif; ?>
<?php if ($wizard['step'] > 0): ?>
	<input type="submit" class="button button_prev" name="wizard:prev" value="<?php echo htmlentities(Arr::get($wizard, 'prevlabel', '< Previous')); ?>" />
<?php endif; ?>
</div>
										  
<input type="hidden" name="wizard:step" value="<?php echo $wizard['step']; ?>" />
<input type="hidden" name="wizard:wizid" value="<?php echo $wizard['wizid']; ?>" />
</form>