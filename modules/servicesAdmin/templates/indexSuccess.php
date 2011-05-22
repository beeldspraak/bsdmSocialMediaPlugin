<?php use_stylesheet('admin.list') ?>
<?php //use_stylesheet('core.sprite16') ?>

<div id="sf_admin_container">

<?php if (count($connected_services) > 0): ?>
    <h2><?php echo __('Connected services') ?>:</h2>
    <div class="dm_list_action_bar dm_list_action_bar_top clearfix"></div>
    <div class="sf_admin_list">
        <table>
            <thead>
                <tr>
                	<th>&nbsp;</th>
                    <th><?php echo __('Name') ?></th>
                    <th><?php echo __('Actions') ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                	<th>&nbsp;</th>
                    <th><?php echo __('Name') ?></th>
                    <th><?php echo __('Actions') ?></th>
                </tr>
            </tfoot>            
            <tbody>
    		<?php foreach ($connected_services as $i => $service): $class = $i % 2 ? 'odd' : 'even'; ?>
		    <?php echo include_partial('servicesAdmin/list_tr_'.$service, array('connected' => true, 'class' => $class)) ?>    
		    <?php endforeach; ?>
            </tbody>
        </table>
     </div>
     <div class="dm_list_action_bar dm_list_action_bar_bottom clearfix"></div>
<?php endif ?>

<?php if (count($unconnected_services) > 0): ?>
	<p>&nbsp;</p>
    <h2><?php echo __('Unconnected services') ?>:</h2>
    <div class="dm_list_action_bar dm_list_action_bar_top clearfix"></div>
    <div class="sf_admin_list">
        <table>
            <thead>
                <tr>
               		<th>&nbsp;</th>
                    <th><?php echo __('Name') ?></th>
                    <th><?php echo __('Actions') ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                	<th>&nbsp;</th>
                    <th><?php echo __('Name') ?></th>
                    <th><?php echo __('Actions') ?></th>
                </tr>
            </tfoot>            
            <tbody>
		    <?php foreach ($unconnected_services as $i => $service): $class = $i % 2 ? 'odd' : 'even'; ?>
		    <?php echo include_partial('servicesAdmin/list_tr_'.$service, array('connected' => false, 'class' => $class)) ?>    
		    <?php endforeach; ?>
            </tbody>
        </table>
     </div>
     <div class="dm_list_action_bar dm_list_action_bar_bottom clearfix"></div>
<?php endif ?>

</div>