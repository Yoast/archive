<p class="desc"><?php echo __('Let Yoast help you if we need admin access to your site.', 'yoast-support-framework'); ?></p>

<table class="form-table">
    <tr>
        <th scope="row"><label for="yoast-support-question"><?php echo __('Send admin details to us', 'yoast-support-framework'); ?></label></th>
        <td>
            <?php if(isset($user->ID)): ?>
                <button class="button" name="adminAccess" onclick="location.href='?page=wpseo_support&admin=remove';"><?php echo __('Remove Yoast admin account', 'yoast-support-framework'); ?></button>
            <?php else: ?>
                <button class="button" name="adminAccess" onclick="location.href='?page=wpseo_support&admin=sent';"><?php echo __('Create new admin account and send details to Yoast', 'yoast-support-framework'); ?></button>
            <?php endif; ?></td>
    </tr>
</table>