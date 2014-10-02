<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" accept-charset="UTF-8">
    <table class="form-table">
        <tr>
            <th scope="row"><label for="yoast-support-question"><?php echo __('Your question', 'yoast-support-framework'); ?>:</label><br/><small>(<?php echo __('Please provide all information', 'yoast-support-framework'); ?></small></th>
            <td><textarea cols="50" rows="15" id="yoast-support-question" name="yoast_support[question]" placeholder="<?php echo $yoast_support->support_message(); ?>"></textarea></td>
        </tr>
    </table>

    <p><input type="submit" class="button-primary" name="getsupport" value="Get support"></p>
</form>