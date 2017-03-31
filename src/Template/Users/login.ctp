<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New User'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="users form large-9 medium-8 columns content">
	<?= $this->Flash->render('auth') ?>
	<?= $this->Form->create() ?>
		<fieldset>
			<legend><?= __('Please enter your username and password') ?></legend>
			<?= $this->Form->input('username') ?>
			<?= $this->Form->input('password') ?>
		</fieldset>
		<?= $this->Form->button(__('Login')); ?>
	<?= $this->Form->end() ?>
</div>