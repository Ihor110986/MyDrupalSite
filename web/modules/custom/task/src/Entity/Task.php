<?php

namespace Drupal\task\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\task\TaskInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the task entity class.
 *
 * @ContentEntityType(
 *   id = "task",
 *   label = @Translation("Task"),
 *   label_collection = @Translation("Tasks"),
 *   label_singular = @Translation("task"),
 *   label_plural = @Translation("tasks"),
 *   label_count = @PluralTranslation(
 *     singular = "@count tasks",
 *     plural = "@count tasks",
 *   ),
 *   bundle_label = @Translation("Task type"),
 *   handlers = {
 *     "list_builder" = "Drupal\task\TaskListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\task\TaskAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\task\Form\TaskForm",
 *       "edit" = "Drupal\task\Form\TaskForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "task",
 *   data_table = "task_field_data",
 *   revision_table = "task_revision",
 *   revision_data_table = "task_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   admin_permission = "administer task types",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "langcode" = "langcode",
 *     "bundle" = "bundle",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log",
 *   },
 *   links = {
 *     "collection" = "/admin/content/task",
 *     "add-form" = "/task/add/{task_type}",
 *     "add-page" = "/task/add",
 *     "canonical" = "/task/{task}",
 *     "edit-form" = "/task/{task}/edit",
 *     "delete-form" = "/task/{task}/delete",
 *   },
 *   bundle_entity_type = "task_type",
 *   field_ui_base_route = "entity.task_type.edit_form",
 * )
 */
class Task extends RevisionableContentEntityBase implements TaskInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the task was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setTranslatable(TRUE)
      ->setDescription(t('The time that the task was last edited.'));

     $fields['start_date'] = BaseFieldDefinition::create('datetime')
       ->setLabel(t('Start date'))
       ->setSetting('datetime_type', 'date')
       ->setRequired(TRUE)
       ->setDisplayOptions('view', [
         'label' => 'inline',
         'settings' => [
           'format_type' => 'html_date',
         ],
         'weight' => 0,
       ])
       ->setDisplayOptions('form', ['weight' => 10]);

       $fields['end_date'] = BaseFieldDefinition::create('datetime')
       ->setLabel(t('End date'))->setSetting('datetime_type', 'date')
       ->setRequired(TRUE)
       ->setDisplayOptions('view', [
         'label' => 'inline',
         'settings' => [
           'format_type' => 'html_date',
         ],
         'weight' => 0,
       ])
       ->setDisplayOptions('form', ['weight' => 10]);

//    $fields['supervisors'] = BaseFieldDefinition::create('entity_reference')
//      ->setLabel(t('Supervisors'))
//      ->setDescription(t('The vocabulary to which the term is assigned.'))
//      ->setSetting('target_type', 'taxonomy_vocabulary')
//      ->setDisplayOptions('form', [
//      'type' => 'select',
//      'tags' => TRUE]);

//    $fields['supervisors'] = BaseFieldDefinition::create('entity_reference')
//      ->setLabel(t('Supervisors'))
//      ->setDescription(t('What was this tagged with.'))
//      ->setSetting('target_type', 'taxonomy_term')
//      ->setSetting('handler_settings', ['task' => ['task']])
//      ->setDisplayOptions('view', array(
//        'label' => 'above',
//        'type' => 'author',
//        'weight' => -3,
//      ))
//      ->setDisplayOptions('form', array(
//        'type' => 'entity_reference_autocomplete',
//        'settings' => array(
//          'match_operator' => 'CONTAINS',
//          'size' => 60,
//          'placeholder' => '',
//        ),
//        'weight' => -3,
//      ))
//      ->setDisplayConfigurable('form', TRUE)
//      ->setDisplayConfigurable('view', TRUE);
//    $definition = BaseFieldDefinition::create('entity_reference')
//      ->setSetting('target_type', 'taxonomy_term')
//      ->setSetting('handler_settings', ['target_bundles' => ['custom_vocabulary' => 'custom_vocabulary']]);

//    $fields['my_custom_vocabulary'] = BaseFieldDefinition::create('entity_reference')
//      ->setLabel(t('Vocabulary'))
//      ->setDescription(t('What was this tagged with.'))
//      ->setSetting('target_type', 'custom_vocabulary')
//      ->setSetting('handler', 'default')
//      ->setDisplayOptions('view', array(
//        'label' => 'above',
//        'type' => 'author',
//        'weight' => -3,
//      ))
//      ->setDisplayOptions('form', array(
//        'type' => 'entity_reference_autocomplete',
//        'settings' => array(
//          'match_operator' => 'CONTAINS',
//          'size' => 60,
//          'placeholder' => '',
//        ),
//        'weight' => -3,
//      ))
//      ->setDisplayConfigurable('form', TRUE)
//      ->setDisplayConfigurable('view', TRUE);

//    $definition = BaseFieldDefinition::create('entity_reference')
//      ->setSetting('target_type', 'taxonomy_term')
//      ->setSetting('handler_settings', ['target_bundles' => ['custom_vocabulary' => 'custom_vocabulary']]);

    $termStorage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $ids = $termStorage->getQuery()
      ->condition('vid', 'task')
      ->execute();

    $categories = [];
    foreach ($termStorage->loadMultiple($ids) as $item) {
      $categories[$item->id()] = $item->label();
    }

    $fields['supervisors'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Supervisors'))
      ->setSettings([
        'allowed_values' =>$categories

      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
      ]);
    return $fields;
  }

}
