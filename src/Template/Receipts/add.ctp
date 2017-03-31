<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Receipts'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('Logout'), ['action' => '../users/logout']) ?></li>
    </ul>
</nav>
<div class="receipts form large-9 medium-8 columns content">
    <?= $this->Form->create($receipt, ['type' => 'file']) ?>
    <fieldset>
        <legend><?= __('Add Receipt') ?></legend>
        <?php
            echo $this->Form->control('payment');
            echo $this->Form->control('fileOne', ['type' => 'file', 'required']);
            echo $this->Form->control('fileTwo', ['type' => 'file', 'required']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
