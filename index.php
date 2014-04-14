<h2 id="wpseo-title"><?php echo __('Yoast Support'); ?></h2>

<?php
if(!class_exists('Support_Framework')){
    include("class-support.php");
    $yoast_support      =   new Support_Framework();
}

if(isset($_GET['admin'])){
    if($_GET['admin']=='sent'){
        if($yoast_support->create_admin_details()){
            add_settings_error( 'yoast_support-notices', 'yoast_support-error', __('The user is created successfully!', 'yoast_support'), 'updated' );
        }
        else{
            add_settings_error( 'yoast_support-notices', 'yoast_support-error', __('There was an error while creating the new user', 'yoast_support'), 'error' );
        }
    }
    elseif($_GET['admin']=='remove'){
        if($yoast_support->remove_admin_details()){
            add_settings_error( 'yoast_support-notices', 'yoast_support-error', __('The user is removed successfully!', 'yoast_support'), 'updated' );
        }
        else{
            add_settings_error( 'yoast_support-notices', 'yoast_support-error', __('The user couldn\'t be removed', 'yoast_support'), 'error' );
        }
    }
}

if(isset($_POST['getsupport'])){
    $data       =   $_POST;
    if($yoast_support->validate($data)){
        $type       =   'updated';
        $message    =   __('Your question is succesfully submitted to <a href="http://www.yoast.com" target="_blank">Yoast</a>.');
    }
    else {
        $type       =   'error';
        $message    =   __($yoast_support->get_error(), 'support_framework' );
    }

    add_settings_error( 'yoast_support-notices', 'yoast_support-error', __($message, 'yoast_support'), $type );

}

$user   =   $yoast_support->find_admin_user();
settings_errors( 'yoast_support-notices' );
add_action('admin_notices', 'yoast_support_admin_messages');
?>


<p><?php echo __('Support blocks will show up here'); ?></p>

<hr>

<p class="desc"><?php echo __('Let Yoast help you if we need admin access to your site.'); ?></p>

<table class="form-table">
    <tr>
        <th scope="row"><label for="yoast-support-question"><?php echo __('Send admin details to us'); ?></label></th>
        <td>
        <?php if(isset($user->ID)): ?>
            <button class="button" name="adminAccess" onclick="location.href='?page=wpseo_support&admin=remove';"><?php echo __('Remove Yoast admin account'); ?></button>
        <?php else: ?>
            <button class="button" name="adminAccess" onclick="location.href='?page=wpseo_support&admin=sent';"><?php echo __('Create new admin account and send details to Yoast'); ?></button>
        <?php endif; ?></td>
    </tr>
</table>
<p>

</p>

<hr>


<p><?php echo __('You need help? We are there for you. Before you ask us, check our <a href="https://yoast.com/support/" target="_blank">Support page</a>. Please fill in this field and we will answer your question soon.'); ?></p>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" accept-charset="UTF-8">

    <table class="form-table">
        <tr>
            <th scope="row"><label for="yoast-support-question">Your question:</label><br/><small>(Please provide all information)</small></th>
            <td><textarea cols="50" rows="15" id="yoast-support-question" name="yoast_support[question]" placeholder="<?php echo $yoast_support->support_message(); ?>"></textarea></td>
        </tr>
    </table>

    <p><input type="submit" class="button-primary" name="getsupport" value="Get support"></p>

</form>