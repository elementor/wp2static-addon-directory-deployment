<?php
// phpcs:disable Generic.Files.LineLength.MaxExceeded
// phpcs:disable Generic.Files.LineLength.TooLong

/**
 * @var mixed[] $view
 */
?>

<h2>Copy to Folder Options</h2>

<form
    name="wp2static-copy-save-options"
    method="POST"
    action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

    <?php wp_nonce_field( $view['nonce_action'] ); ?>
    <input name="action" type="hidden" value="wp2static_copy_save_options" />

<table class="widefat striped">
    <tbody>


        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['copyTargetFolder']->name; ?>"
                ><?php echo $view['options']['copyTargetFolder']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['copyTargetFolder']->name; ?>"
                    name="<?php echo $view['options']['copyTargetFolder']->name; ?>"
                    type="text" maxlength="255" size="64"
                    value="<?php echo $view['options']['copyTargetFolder']->value !== '' ? $view['options']['copyTargetFolder']->value : ''; ?>"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['copyRemoveTarget']->name; ?>"
                ><?php echo $view['options']['copyRemoveTarget']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['copyRemoveTarget']->name; ?>"
                    name="<?php echo $view['options']['copyRemoveTarget']->name; ?>"
                    value="1"
                    <?php echo (int) $view['options']['copyRemoveTarget']->value === 1 ? 'checked' : ''; ?>
                    type="checkbox"
                />
            </td>
        </tr>

        <tr>
            <td style="width:50%;">
                <label
                    for="<?php echo $view['options']['copyExtraFolder']->name; ?>"
                ><?php echo $view['options']['copyExtraFolder']->label; ?></label>
            </td>
            <td>
                <input
                    id="<?php echo $view['options']['copyExtraFolder']->name; ?>"
                    name="<?php echo $view['options']['copyExtraFolder']->name; ?>"
                    type="text" maxlength="255" size="64"
                    value="<?php echo $view['options']['copyExtraFolder']->value !== '' ? $view['options']['copyExtraFolder']->value : ''; ?>"
                />
            </td>
        </tr>

    </tbody>
</table>




<br>

    <button class="button btn-primary">Save Options</button>
</form>

