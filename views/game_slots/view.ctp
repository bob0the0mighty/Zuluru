<?php
$this->Html->addCrumb (__('Game Slot', true));
$this->Html->addCrumb (__('View', true));
?>

<div class="gameSlots view">
<h2><?php  __('Game Slot');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Field'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->element('fields/block', array('field' => $gameSlot['Field'], 'display_field' => 'long_name')); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Game Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->ZuluruTime->date ($gameSlot['GameSlot']['game_date']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Game Start'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->ZuluruTime->time ($gameSlot['GameSlot']['game_start']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Game End'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->ZuluruTime->time ($gameSlot['GameSlot']['display_game_end']); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Game'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($gameSlot['GameSlot']['game_id'], array('controller' => 'games', 'action' => 'view', 'game' => $gameSlot['GameSlot']['game_id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(sprintf(__('Edit %s', true), __('Game Slot', true)), array('action' => 'edit', 'slot' => $gameSlot['GameSlot']['id'])); ?> </li>
	</ul>
</div>

<?php if (!empty($gameSlot['DivisionGameslotAvailability'])):?>
<div class="related">
	<h3><?php __('Available to Divisions');?></h3>
	<table class="list">
	<?php
		$i = 0;
		foreach ($gameSlot['DivisionGameslotAvailability'] as $division):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $this->Html->link($division['Division']['full_league_name'], array('controller' => 'divisions', 'action' => 'view', 'division' => $division['Division']['id']));?></td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>
