<h2 id="wpseo-title">Yoast Support</h2>

<?php
if(!class_exists('SupportFramework')){
    include("class-support.php");
    $yoast_support      =   new SupportFramework();
}

if(isset($_POST['getsupport'])){
    $data       =   $_POST;
    if($yoast_support->validate($data)){
        $type       =   'updated';
        $message    =   __('Your question is succesfully submitted to <a href="http://www.yoast.com" target="_blank">Yoast</a>.');
    }
    else {
        $type       =   'error';
        $message    =   __( 'Please fill in your question!', 'support_framework' );
    }

    add_settings_error( 'yoast_support-notices', 'yoast_support-error', __($message, 'yoast_support'), $type );

}
settings_errors( 'yoast_support-notices' );
add_action('admin_notices', 'yoast_support_admin_messages');
?>

<p><?php echo __('You need help? We are there for you. Before you ask us, check our <a href="https://yoast.com/support/" target="_blank">Support page</a>. Please fill in this field and we will answer your question soon.'); ?></p>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" accept-charset="UTF-8">

    <table class="form-table">
        <tr>
            <th scope="row"><label for="yoast-support-question">Your question:</label><br/><small>(Please provide all information)</small></th>
            <td><textarea cols="50" rows="15" id="yoast-support-question" name="yoast_support[question]" placeholder="<?php echo $yoast_support->__SupportMessage(); ?>"></textarea></td>
        </tr>
    </table>

    <p><input type="submit" class="button-primary" name="getsupport" value="Get support"></p>

</form>