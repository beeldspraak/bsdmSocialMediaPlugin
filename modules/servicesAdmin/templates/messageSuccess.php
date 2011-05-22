<?php use_stylesheet('admin.form') ?>

<div id="sf_admin_container">
    <h1><?php echo __('New')." ".$service." ".__('message'); ?></h1>
        
    <div class="sf_admin_content">
      <div class="sf_admin_form">
        <form action="<?php echo url_for('servicesAdmin/message?service='.$service); ?>" method="post">
          <div class="dm_form_action_bar dm_form_action_bar_top clearfix">
			<ul class="sf_admin_actions clearfix">
  			  <li class="sf_admin_action_list"><?php echo _link('servicesAdmin/index')->text(__('Back to list'))->set('.s16.s16_arrow_left'); ?></li>
  			  <li class="sf_admin_action_save"><input type="submit" value="<?php echo __('Send') ?>" class="green"></li>
  			</ul>    
    	  </div>
          <div class="sf_admin_form_inner ui-widget ui-accordion">
          	<fieldset id="sf_fieldset_none">
          		<div class="fieldset_none fieldset_content ui-widget-content ui-corner-all ui-accordion-content-active">
          			<div class="fieldset_content_inner clearfix">
          				<div class="even last sf_admin_form_row sf_admin_text sf_widget_form_textarea<?php $form['status_message']->hasError() and print ' errors' ?>">
          				    <?php if ($form['status_message']->hasError()): ?>
						      <div class="error">
						        <div class="s16 s16_error"><?php echo __((string) $form['status_message']->getError(), array(), 'dm') ?></div>
						      </div>
						    <?php endif; ?>
          					<div class="sf_admin_form_row_inner clearfix">
          						<div class="label_wrap"><?php echo $form['status_message']->renderLabel() ?></div>
          						<div class="content"><?php echo $form['status_message'] ?></div>
          					</div>
          				</div>
          			</div>
          		</div>
          	</fieldset>
          </div>
          
          <div class="dm_form_action_bar dm_form_action_bar_top clearfix">
			<ul class="sf_admin_actions clearfix">
  			  <li class="sf_admin_action_list"><?php echo _link('servicesAdmin/index')->text(__('Back to list'))->set('.s16.s16_arrow_left'); ?></li>
  			  <li class="sf_admin_action_save"><input type="submit" value="<?php echo __('Send') ?>" class="green"></li>
  			</ul>    
    	  </div>
        </form>
      </div>
    </div>
    
</div>