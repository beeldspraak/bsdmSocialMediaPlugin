<table>
	<thead>
		<tr>
			<th>&nbsp;</th>
			<th>Id</th>
			<th><?php echo __('Date/time') ?></th>
			<th><?php echo __('Text') ?></th>
			<th><?php echo __('Source') ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>&nbsp;</th>
			<th>Id</th>
			<th><?php echo __('Date/time') ?></th>
			<th><?php echo __('Text') ?></th>
			<th><?php echo __('Source') ?></th>
		</tr>
	</tfoot>
	<tbody>
	<?php foreach ( $statuses as $i => $status ): $class = $i % 2 ? 'odd' : 'even'; ?>
		<tr class="sf_admin_row <?php echo $class ?>">
			<td>&nbsp;</td>
			<td><?php echo $status->id?></td>
			<td><?php echo $status->created_at?></td>
			<td><?php echo $status->text?></td>
			<td><?php echo $status->source?></td>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>