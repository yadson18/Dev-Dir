<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Receipt'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('Logout'), ['action' => '../users/logout']) ?></li>
    </ul>
</nav>
<div class="receipts view large-9 medium-8 columns content">
    <h3><?= __('Receipts') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('siape') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('payment') ?></th>
                <th scope="col"><?= $this->Paginator->sort('send') ?></th>
                <th scope="col"><?= $this->Paginator->sort('fileOne') ?></th>
                <th scope="col"><?= $this->Paginator->sort('fileTwo') ?></th>
                <th scope="col"><?= $this->Paginator->sort('aproved') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($receipts as $receipt): ?>
            <tr>
                <td><?= h($receipt->user->username) ?></td>
                <td><?= h($receipt->user->name) ?></td>
                <td><?= h($receipt->payment) ?></td>
                <td><?= h($receipt->send) ?></td>
                <?php foreach ($receipt->files as $file): ?>
                    <td>
                        <?= $this->Form->postLink(
                                $this->Html->image('/img/pdf.png', ['alt' => __('View Pdf')]),
                                ['action' => '../uploads/files/', $file['name']],
                                ['escape' => false, 'target' => '_blank']);   
                        ?>
                        <?= $this->Form->postLink(
                                $this->Html->image('/img/download.png', ['alt' => __('Download Pdf')]),
                                ['action' => 'download', $file['name']],
                                ['escape' => false]);    
                        ?>
                    </td> 
                <?php endforeach; ?>
                <td>
                    <?php 
                        if($receipt->aproved === true){
                            if(strcmp($role, "servidor") == 0){
                                //echo $this->Html->link(__(' Aproved'), ['action' => '#'], ["class" => "fa fa-check-circle-o green"]);
                                echo "<p class='fa fa-check-circle-o green'> Aproved</p>";
                            }
                            else{
                                echo $this->Form->postLink(
                                __(' Aproved'),
                                ['action' => 'disaprove', $receipt->id],
                                ['class' => 'fa fa-check-circle-o green'],
                                ['escape' => false]);
                            }
                        }
                        else{
                            if(strcmp($role, "servidor") == 0){
                                //echo $this->Html->link(__(' Not Aproved'), ['action' => '#'], ["class" => "fa fa-times-circle-o red"]);
                                echo "<p class='fa fa-times-circle-o red'> Not Aproved</p>";
                            }
                            else{
                                echo $this->Form->postLink(
                                __(' Click To Aprove'),
                                ['action' => 'aprove', $receipt->id, $receipt->user->email],
                                ['class' => 'fa fa-times-circle-o red'],
                                ['escape' => false]);
                            }
                        }

                    ?>
                </td>
                <?php 
                    /*<td class="actions">
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $receipt->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $receipt->id], ['confirm' => __('Are you sure you want to delete # {0}?', $receipt->id)]) ?>
                    </td>*/
                ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
