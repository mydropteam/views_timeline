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

    private $displayTypes;

    private $dateTypes;

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
        $this->displayTypes = $this->displayTypes();
        $this->dateTypes = $this->dateTypes();
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
            '#options' => $this->displayTypes,
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

        $form['timeline_date_type'] = array(
            '#type' => 'select',
            '#title' => $this->t('Type date'),
            '#default_value' => $this->options['timeline_date_type'],
            //'#description' => $this->t('Choice your field date.'),
            '#states' => array(
                'invisible' => array(
                    ':input[name="style_options[timeline_type]"]' => array('value' => 'vertical'),
                ),
            ),
            '#options' => $this->dateTypes,
        );
    }

    /**
     * Timeline display types.
     * @return array
     */
    public static function displayTypes() {
        return ['vertical' => t('Vertical'), 'horizontal' => t('Horizontal')];
    }

    /**
     *
     * @return array
     */
    public static function dateTypes(){
        return ['d/m/Y' => t('By day (DD/MM/YYYY)'), 'd/m/YTH:i' => t('By day with hours (DD/MM/YYYYTHH:MM)'), 'H:i' => t('By hours only (HH:MM)')];
    }

    /**
     * To render horizontal or vertical timeline
     * @return array
     */
    public function render()
    {
        $output = [
            '#view' => $this->view,
            '#options' => $this->options,
            '#rows' => $this->view->result,
        ];

        if($this->options['timeline_type'] == 'vertical'){

            $this->definition['theme'] = 'timeline_vertical';
        }
        elseif($this->options['timeline_type'] == 'horizontal'){

            $date = array();
            $this->definition['theme'] = 'timeline_horizontal';

            foreach($this->view->result as $id => $result){

                $value = end($result->_entity->get($this->options['timeline_date_field'])->getValue());

                if(isset($value['value']) && !empty($value['value'])){

                    $timestamp = strtotime($value['value']);
                    $date_render = format_date($timestamp, 'custom', $this->options['timeline_date_type']);
                }
                $date[$id] = $date_render;
            }

            $output['#dates'] = $date;
        }
        $output['#theme'] = $this->themeFunctions();

        return $output;
    }
}