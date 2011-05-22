<?php use_helper('Date') ?>
<?php use_stylesheet('admin.list') ?>

<div id="sf_admin_container">
    <h1><?php echo ucfirst($service)." ".__('user timeline')." ".$screen_name ?></h1>

    <?php if (count($statuses) > 0): ?>
    <div class="dm_list_action_bar dm_list_action_bar_top clearfix">
		<ul class="sf_admin_actions clearfix">
  			<li class="sf_admin_action_list"><?php echo _link('servicesAdmin/index')->text(__('Back to list'))->set('.s16.s16_arrow_left'); ?></li>
  		</ul>    
    </div>
    <div class="sf_admin_list">
	<?php echo include_partial('servicesAdmin/statuses_'.$service, array('statuses' => $statuses)) ?>
    </div>
    <div class="dm_list_action_bar dm_list_action_bar_bottom clearfix">
		<ul class="sf_admin_actions clearfix">
  			<li class="sf_admin_action_list"><?php echo _link('servicesAdmin/index')->text(__('Back to list'))->set('.s16.s16_arrow_left'); ?></li>
  		</ul>     
    </div>
    <?php else: ?>
        <p><?php echo __('No status messages found') ?></p>
    <?php endif; ?>
</div>