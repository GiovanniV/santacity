<?php


namespace Drupal\md_slider\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Drupal\md_slider\MDSlider;
use Drupal\md_slider\MDSliderDataBase;
use Drupal\md_slider\MDCommon;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\file\Entity\File;


class MDSliderController extends ControllerBase {

  /**
   *
   */
  public function listMDSlider() {
    $all_slider = MDSliderDataBase::loadAll('md_sliders');
    $header = array(
      'slider-name' => t('Slider Name'),
      'slider-description' => t('Description'),
      'slider-operations' => t('Operations')
    );
    $rows = array();
    foreach ($all_slider as $index => $slide) {
      $rows[$index]['slider-name'] = $slide->title;
      $rows[$index]['slider-description'] = $slide->description;
      $operations = array(
        '#type' => 'operations',
        '#links' => array(
          'config' => array(
            'url' => new Url('md_slider.admin.configure', array('slider' => $slide->machine_name)),
            'title' => 'Configuration'
          ),
          'edit' => array(
            'url' => new Url('md_slider.admin.edit', array('slider' => $slide->machine_name)),
            'title' => 'Edit '
          ),
          'delete' => array(
            'url' => new Url('md_slider.admin.delete', array('slider' => $slide->machine_name)),
            'title' => 'Delete'
          ),
          'clone' => array(
            'url' => new Url('md_slider.admin.clone', array('slider' => $slide->machine_name)),
            'title' => 'Clone'
          ),
        )
      );
      $rows[$index]['slider-operations'] = array('data' => $operations);
    }
    return array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => t('No MegaSlider available. <a href="@link">Add MegaSlider</a>.', array('@link' => \Drupal::url('md_slider.admin.add'))),
      '#attributes' => array('id' => 'md-slider'),
    );
  }

  /**
   * @param $slider
   * @return array
   */
  public function configMDSlider($slider) {
    $MDSlider = new MDSlider();
    $MDSlider->getDataSlider($slider);
    $form = \Drupal::formBuilder()->getForm('\Drupal\md_slider\Form\MDSliderConfigForm', $MDSlider);
    return $form;
  }


  /**
   * @param $slider
   */
  public function editMDSlider($slider) {
    $MDSlider = new MDSlider();
    $MDSlider->getDataSlider($slider);
    $build['form'] = \Drupal::formBuilder()->getForm('\Drupal\md_slider\Form\MDSliderEditForm', $MDSlider);
    $build['slide_settings'] = array(
      '#theme' => 'slide_settings',
    );
    $json_url = \Drupal::url('md_slider.admin.json_response', array(), array('absolute' => TRUE));
    $html_url = \Drupal::url('md_slider.admin.html_response', array(), array('absolute' => TRUE));
    $image_url = \Drupal::url('md_slider.admin.image', array(), array('absolute' => TRUE));
    $build['#attached']['drupalSettings']['MDSlider']['jsonConfigURL'] = $json_url;
    $build['#attached']['drupalSettings']['MDSlider']['htmlConfigURL'] = $html_url;
    $build['#attached']['drupalSettings']['MDSlider']['imageConfigURL'] = $image_url;
    return $build;
  }

  public function jsonResponse() {
    $action = isset($_POST['action']) ? $_POST['action'] : NULL;
    $response = '';
    switch ($action) {
      case 'bgSlider':
        $fid = -1;
        $slid = -1;
        if (isset($_POST['fid'])) {
          $fid = $_POST['fid'];
        }

        if (isset($_POST['slider_id'])) {
          $slid = urldecode($_POST['slider_id']);
        }

        if ($fid == -1 || $slid == -1) {
          $response = '';
        }

        else {
          $file = File::load($fid);
          if ($file)
            $response = $file->url();
        }
        break;

      case 'getVideoInfo':
        if (isset($_POST['url']) && $_POST['url'] != '') {
          $video = new MDCommon();
          $ret = $video->processVideoUrl($_POST['url']);
          $response = $video->getVideoData($ret['type'], $ret['id']);
        }

        break;

      case 'deleteSlide':
        $sid = $_POST['sid'];
        $condition = array(
          'sid' => $sid,
        );

        $response = MDSliderDataBase::delete('md_slides', $condition);
        $response = $response ? 'OK' : 'FALSE';

        break;
    }

    return new JsonResponse($response);
  }

  public function htmlResponse() {
    $action = isset($_POST['action']) ? $_POST['action'] : NULL;
    $response = '';
    switch ($action) {
      case 'formVideoSettings':
        $form = array(
          '#theme' => 'video_setting_form',
          '#show_change' => $_POST['change']
        );
        $response = \Drupal::service('renderer')
          ->render($form)
          ->__toString();

        break;
    }

    return new Response($response);
  }

}