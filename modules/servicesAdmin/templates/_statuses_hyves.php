<table>
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>Id</th>
			<th><?php echo __('Date/time') ?></th>
			<th><?php echo __('Text') ?></th>
			<th><?php echo __('Where') ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>&nbsp;</th>
			<th>Id</th>
			<th><?php echo __('Date/time') ?></th>
			<th><?php echo __('Text') ?></th>
			<th><?php echo __('Where') ?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php foreach ( $statuses as $i => $status ): $class = $i % 2 ? 'odd' : 'even'; ?>
		<tr class="sf_admin_row <?php echo $class; ?>">
			<td>&nbsp;</td>
			<td><?php echo $status['wwwid']?></td>
			<td><?php echo date ( 'r', ( int ) $status['created'] )?></td>
			<td><?php echo $status['emotion']?></td>
			<td><?php echo $status['where']?></td>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>