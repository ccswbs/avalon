<?php

namespace Drupal\ug_theme\Plugin\Form;

use Drupal\bootstrap\Annotation\BootstrapForm;
use Drupal\bootstrap\Plugin\Form\SearchBlockForm as BootstrapSearchBlockForm;
use Drupal\bootstrap\Utility\Element;
use Drupal\Core\Form\FormStateInterface;

/**
 * @BootstrapForm("search_block_form")
 */
class SearchBlockForm extends BootstrapSearchBlockForm {

  /**
   * {@inheritdoc}
   */
  public function alterFormElement(Element $form, FormStateInterface $form_state, $form_id = NULL) {
    parent::alterFormElement($form, $form_state, $form_id);
    // Changing placeholder text.
    $title = $this->t('Search this site');
    /** @var \Drupal\bootstrap\Utility\Element $form->keys */
    $form->keys->setAttribute('placeholder', $title);
    $form->keys->setProperty('title', $title);

    $form->actions->submit->addClass('btn-default');
  }
}
