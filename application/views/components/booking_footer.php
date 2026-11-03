<?php
/**
 * Local variables.
 *
 * @var bool $display_login_button
 * @var string $legal_notice_url
 * @var string $imprint_url
 */
?>

<div id="frame-footer" class="p-3 text-center border-top">
    <small class="d-block d-md-flex">
        <span class="footer-powered-by small d-block w-100 w-md-50 text-center text-md-start p-1 pe-md-0">
            Powered By
            <a href="https://easyappointments.org" target="_blank">Easy!Appointments</a>

            <?php if (!empty($legal_notice_url)): ?>
                <span>|</span>
                <a href="<?= e($legal_notice_url) ?>" target="_blank"><?= lang('legal_notice') ?></a>
            <?php endif; ?>

            <?php if (!empty($imprint_url)): ?>
                <span>|</span>
                <a href="<?= e($imprint_url) ?>" target="_blank"><?= lang('imprint') ?></a>
            <?php endif; ?>
        </span>

        <span class="footer-options d-block w-100 w-md-50 text-center text-md-end">
 <span>Lovinkstraat 39, Kampen</span>&nbsp;&nbsp;<a href="https://www.pedicureinkampen.nl" target="_top">Terug naar website</a>
            </span>
        </span>
    </small>
</div>
