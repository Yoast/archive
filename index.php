<h2>Yoast Support</h2>

<?php
if(!class_exists('SupportFramework')){
    include("class-support.php");
    $yoast_support      =   new SupportFramework();
}
?>

<p><?php echo __('You need help? We are there for you. Before you ask us, check our %s. Please fill in this field and we will answer your question soon.', '<a href="https://yoast.com/support/" target="_blank">Support page</a>'); ?></p>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" accept-charset="UTF-8">

    <table class="form-table">
        <tr>
            <th scope="row"><label for="yoast-support-question">Your question:</label><br/><small>(Please provide all information)</small></th>
            <td><textarea cols="50" rows="5" id="yoast-support-question" name="yoast_support[question]"></textarea></td>
        </tr>
    </table>

    <p><input type="submit" class="button-primary" name="getsupport" value="Get support"></p>

</form>

<hr>
<pre>
<?php
print_r($yoast_support->getSupportInfo());
?>
</pre>