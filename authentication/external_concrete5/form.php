<?php defined('C5_EXECUTE') or die('Access denied.');

$name = trim((string) array_get($data, 'name', t('External concrete5')));
$leadingVowel = $name ? in_array(strtolower($name[0]), ['a', 'e', 'i', 'o', 'u'], true) : false;

if (isset($error)) {
    ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php

}
if (isset($message)) {
    ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php

}

$user = new User();

if ($user->isLoggedIn()) {
    ?>
    <div class="form-group">
        <span>
            <?= $leadingVowel ? t('Attach an %s account', $name) : t('Attach a %s account', $name) ?>
        </span>
        <hr>
    </div>
    <div class="form-group">
        <a href="<?= $attachUrl ?>" class="btn btn-success btn-login btn-attach btn-block">
            <img src="<?= $assetBase ?>/concrete/images/logo.svg" class="concrete5-icon"></i>
            <?= $leadingVowel ? t('Attach an %s account', $name) : t('Attach a %s account', $name) ?>
        </a>
    </div>
    <?php

} else {
    ?>
    <div class="form-group">
        <span>
            <?= $leadingVowel ? t('Sign in with an %s account', $name) : t('Sign in with a %s account', $name) ?>
        </span>
        <hr class="ccm-authentication-type-external-concrete5">
    </div>
    <div class="form-group">
        <a href="<?= $authUrl ?>" class="btn btn-success btn-login btn-block">
            <img src="<?= $assetBase ?>/concrete/images/logo.svg" class="concrete5-icon"></i>
            <?= t('Log in with ' . $name) ?>
        </a>
    </div>
    <?php

}
?>
<style>
    .ccm-ui .btn-community {
        border-width: 0px;
        background: rgb(31,186,232);
        background: -moz-linear-gradient(top, rgba(31,186,232,1) 0%, rgba(18,155,211,1) 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(31,186,232,1)), color-stop(100%,rgba(18,155,211,1)));
        background: -webkit-linear-gradient(top, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        background: -o-linear-gradient(top, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        background: -ms-linear-gradient(top, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        background: linear-gradient(to bottom, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1fbae8', endColorstr='#129bd3',GradientType=0 );
    }

    .ccm-concrete-authentication-type-svg > svg {
      width: 16px;
    }

    img.concrete5-icon {
        width: 20px;
        margin-right:5px;
    }
</style>
<script>
    (function() {
        var svg = $('.ccm-concrete-authentication-type-svg > svg');

        if (svg) {
            var img = new Image();
            img.onerror = function() {
                svg.parent().replaceWith('<i class="fa fa-user"></i>');
            };
            img.src = svg.parent().data('src');
            $(function() {

                if (svg.closest('li').hasClass('active')) {
                    var color = $('ul.auth-types li.active').css('color');
                    svg.attr('fill', color);
                } else {
                    svg.attr('fill', 'rgb(155,155,155)');
                }
                Concrete.event.bind('AuthenticationTypeSelected', function(e, handle) {
                    if (handle === 'community') {
                        var color = $('ul.auth-types li.active').css('color');
                        svg.attr('fill', color);
                    } else {
                        svg.attr('fill', 'rgb(155,155,155)');
                    }
                });

            });
        }
    }());
</script>
