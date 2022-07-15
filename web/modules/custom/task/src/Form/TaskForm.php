<?php

namespace Drupal\task\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the task entity edit forms.
 */
class TaskForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New task %label has been created.', $message_arguments));
        $this->logger('task')->notice('Created new task %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The task %label has been updated.', $message_arguments));
        $this->logger('task')->notice('Updated task %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.task.canonical', ['task' => $entity->id()]);

    return $result;
  }

}
