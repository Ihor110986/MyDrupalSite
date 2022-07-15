<?php

/**
 * Implements hook_views_data().
 */
function task_views_data() {




  $data = [];


  $data['task_table'] = [];
  $data['task_table']['table'] = [];
  $data['task_table']['table']['group'] = t('Task table');
  $data['task_table']['table']['provider'] = 'task_module';

  $data['task_table']['table']['base'] = [

    'field' => 'id',
    'title' => t('Task table'),
    'help' => t('Task table contains example content and can be related to nodes.'),
    'weight' => -10,
  ];


  $data['task']['table']['join'] = [

    'node_field_data' => [
      'left_field' => 'id',
      'field' => 'id',
      'extra' => [
        0 => [
          'field' => 'published',
          'value' => TRUE,
        ],
        1 => [
          'left_field' => 'age',
          'value' => 1,
          'numeric' => TRUE,
        ],
        2 => [
          'field' => 'published',
          'left_field' => 'is_active',
          'operator' => '!=',
        ],
      ],
    ],
  ];


  $data['task_table']['table']['join']['node_field_data'] = [

    'left_table' => 'foo',
    'left_field' => 'id',
    'field' => 'id',
    'extra' => [
      ['left_field' => 'project_code', 'field' => 'project_code'],
      ['field' => 'age', 'value' => 0, 'numeric' => TRUE, 'operator' => '>'],
    ],
  ];


  $data['task_table']['id'] = [
    'title' => t('Example content'),
    'help' => t('Relate example content to the node content'),

    'relationship' => [
      'base' => 'node_field_data',
      'base field' => 'id',
      'id' => 'standard',
      'label' => t('Example node'),
    ],
  ];


  $data['task_table']['name'] = [
    'title' => t('Name'),
    'help' => t('Just a Name field.'),
    'field' => [
      'id' => 'standard',
    ],

    'sort' => [
      'id' => 'standard',
    ],

    'filter' => [
      'id' => 'string',
    ],

    'argument' => [
      'id' => 'string',
    ],
  ];


  $data['task_table']['project_code'] = [
    'title' => t('Project Code'),
    'help' => t('Just a Project code field.'),
    'field' => [
      'id' => 'standard',
    ],

    'sort' => [
      'id' => 'standard',
    ],

    'filter' => [
      'id' => 'string',
    ],

    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['task_table']['age'] = [
    'title' => t('Age'),
    'help' => t('Just a numeric field.'),

    'field' => [
      'id' => 'numeric',
    ],

    'sort' => [
      'id' => 'standard',
    ],

    'filter' => [
      'id' => 'numeric',
    ],

    'argument' => [
      'id' => 'numeric',
    ],
  ];


  $data['task_table']['is_active'] = [
    'title' => t('Is Active'),
    'help' => t('Just an on/off field.'),

    'field' => [
      'id' => 'boolean',
    ],

    'sort' => [
      'id' => 'standard',
    ],

    'filter' => [
      'id' => 'boolean',
      'label' => t('Published'),
      'type' => 'yes-no',
      'use_equal' => TRUE,
    ],
  ];


  $data['task_table']['timestamp'] = [
    'title' => t('Timestamp'),
    'help' => t('Just a timestamp field.'),

    'field' => [
      'id' => 'date',
    ],

    'sort' => [
      'id' => 'date',
    ],

    'filter' => [
      'id' => 'date',
    ],
  ];


  $data['views']['area'] = [
    'title' => t('Text area'),
    'help' => t('Provide markup text for the area.'),
    'area' => [
      'id' => 'text',
    ],
  ];

  return $data;
}
