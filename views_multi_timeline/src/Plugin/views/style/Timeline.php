<?php

/**
 * @file
 * Contains \Drupal\views_multi_timeline\Plugin\views\style\Timeline.
 */

namespace Drupal\views_multi_timeline\Plugin\views\style;

use Drupal\views\Views;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class VerticalTimeline
 * @package Drupal\views_multi_timeline\Plugin\views
 *
@ViewsStyle(
 *   id = "timeline",
 *   title = @Translation("Timeline"),
 *   help = @Translation("Present view results as a Timeline."),
 *   theme = "timeline_base",
 *   display_types = {"normal"},
 *   even_empty = TRUE
 * )
 */
class Timeline extends StylePluginBase {

    /**
     * Constructs a PluginBase object.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static($configuration, $plugin_id, $plugin_definition);
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(&$form, FormStateInterface $form_state) {
        parent::buildOptionsForm($form, $form_state);
        $options = $this->displayHandler->getFieldLabels(TRUE);
        $form['timeline_type'] = array(
            '#title' => $this->t('Timeline type'),
            '#type' => 'select',
            '#description' => $this->t('Select the timeline time period for this display.'),
            '#default_value' => $this->options['timeline_type'],
            '#options' => $this->displayTypes(),
        );

        $form['timeline_date_field'] = array(
            '#type' => 'select',
            '#title' => $this->t('Field date'),
            '#default_value' => $this->options['timeline_date_field'],
            //'#description' => $this->t('Choice your field date.'),
            '#states' => array(
                'invisible' => array(
                    ':input[name="style_options[timeline_type]"]' => array('value' => 'vertical'),
                ),
            ),
            '#options' => $options,
        );
    }

    /**
     * Timeline display types.
     */
    public static function displayTypes() {
        return ['vertical' => t('Vertical'), 'horizontal' => t('Horizontal')];
    }

    /**
     * To render horizontal or vertical timeline
     * @return array
     */
    public function render()
    {
        if($this->options['timeline_type'] == 'vertical'){

            $this->definition['theme'] = 'timeline_vertical';
        }
        elseif($this->options['timeline_type'] == 'horizontal'){

            $this->definition['theme'] = 'timeline_horizontal';
        }

        $output = [
            '#theme' => $this->themeFunctions(),
            '#view' => $this->view,
            '#options' => $this->options,
            '#rows' => $this->view->result,
        ];

        return $output;
    }
}