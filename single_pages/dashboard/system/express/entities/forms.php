<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Element\Dashboard\Express\Menu;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Form as ExpressForm;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;

/** @var Entity $entity */
/** @var ExpressForm[] $forms */
?>

<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">

        <?php
        $manage = new Menu($entity);
        /** @noinspection PhpDeprecationInspection */
        $manage->render();
        ?>

        <a href="<?php echo (string)Url::to('/dashboard/system/express/entities/forms', 'add', $entity->getId()) ?>"
           class="btn btn-primary">
            <?php echo t("Add Form") ?>
        </a>
    </div>
</div>

<div class="row">
    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('dashboard/express/detail_navigation', ['entity' => $entity]) ?>

    <div class="col-md-8">
        <?php if (count($forms)) { ?>
            <ul class="item-select-list" id="ccm-stack-list">
                <?php foreach ($forms as $form) { ?>
                    <li>
                        <a href="<?php echo (string)Url::to('/dashboard/system/express/entities/forms', 'view_form_details', $form->getID()) ?>">
                            <i class="fas fa-list-alt"></i> <?php echo h($form->getName()) ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>
                <?php echo t('You have not created any forms.') ?>
            </p>
        <?php } ?>
    </div>
</div>
