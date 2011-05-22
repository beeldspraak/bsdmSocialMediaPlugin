<?php $service = 'twitter' ?>
<?php if ($connected): ?>
<tr class="sf_admin_row <?php echo $class ?>">
	<td><?php echo _media('/bsdmSocialMediaPlugin/images/32px/'.$service.'.png') ?></td>
	<td><?php echo $service ?></td>
	<td>
		<ul class="sf_admin_td_actions">
			<li class="sf_admin_action"><?php echo _link('servicesAdmin/unconnect?service='.$service)->text(__('Disconnect'))->set('.s16.s16_signout'); ?></li>
			<li class="sf_admin_action"><?php echo _link('servicesAdmin/user_timeline?service='.$service)->text(__('View user timeline'))->set('.s16.s16_rss'); ?></li>
			<li class="sf_admin_action"><?php echo _link('servicesAdmin/message?service='.$service)->text(__('New tweet'))->set('.s16.s16_add'); ?></li>
			<li class="sf_admin_action"><?php echo _link('servicesAdmin/sync?service='.$service)->text(__('Synchronise tweets'))->set('.s16.s16_clear'); ?></li>
		</ul>
	</td>
</tr>
<?php else: ?>
<tr class="sf_admin_row <?php echo $class ?>">
	<td><?php echo _media('/bsdmSocialMediaPlugin/images/32px/'.$service.'.png') ?></td>
	<td><?php echo $service ?></td>
	<td>
		<ul class="sf_admin_td_actions">
			<li class="sf_admin_action_edit"><?php echo _link('servicesAdmin/user_timeline?service='.$service)->text(__('Connect'))->set('.s16.s16_status_ok'); ?></li>
		</ul>
	</td>
</tr>
<?php endif; ?>