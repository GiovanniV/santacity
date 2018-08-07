<?php

/**
 * @file
 * Contains \Drupal\views\Controller\ViewAjaxController.
 */

namespace Drupal\datatables\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\EventSubscriber\AjaxResponseSubscriber;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\views\Ajax\ViewAjaxResponse;
use Drupal\views\Plugin\views\field\Markup;
use Drupal\views\ViewExecutableFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Defines a controller to load a view via AJAX.
 */
class DatatablesViewAjaxController implements ContainerInjectionInterface {

  /**
   * The entity storage for views.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The factory to load a view executable with.
   *
   * @var \Drupal\views\ViewExecutableFactory
   */
  protected $executableFactory;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The current path.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * The redirect destination.
   *
   * @var \Drupal\Core\Routing\RedirectDestinationInterface
   */
  protected $redirectDestination;

  /**
   * Constructs a ViewAjaxController object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage for views.
   * @param \Drupal\views\ViewExecutableFactory $executable_factory
   *   The factory to load a view executable with.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   The current path.
   * @param \Drupal\Core\Routing\RedirectDestinationInterface $redirect_destination
   *   The redirect destination.
   */
  public function __construct(EntityStorageInterface $storage, ViewExecutableFactory $executable_factory, RendererInterface $renderer, CurrentPathStack $current_path, RedirectDestinationInterface $redirect_destination) {
    $this->storage = $storage;
    $this->executableFactory = $executable_factory;
    $this->renderer = $renderer;
    $this->currentPath = $current_path;
    $this->redirectDestination = $redirect_destination;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('view'),
      $container->get('views.executable'),
      $container->get('renderer'),
      $container->get('path.current'),
      $container->get('redirect.destination')
    );
  }

  /**
   * Loads and renders a view via AJAX.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *  The current request object.
   *
   * @return \Drupal\views\Ajax\ViewAjaxResponse
   *  The view response as ajax response.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Thrown when the view was not found.
   */
  public function ajaxView(Request $request) {
    $name = $request->request->has('view_name') ? $request->request->get('view_name') : $request->query->get('view_name');
    $display_id = $request->request->get('view_display_id') ? $request->request->get('view_display_id') : $request->query->get('view_display_id');
    if (isset($name) && isset($display_id)) {
      // Add all POST data, because AJAX is always a post and many things,
      // such as tablesorts, exposed filters and paging assume GET.
      $request_all = $request->request->all();
      $query_all = $request->query->all();
      $request->query->replace($request_all + $query_all);

      // From now on we only check in $request->query.
      $args = $request->query->get('view_args');
      $args = isset($args) && $args !== '' ? explode('/', $args) : array();

      // Arguments can be empty, make sure they are passed on as NULL so that
      // argument validation is not triggered.
      $args = array_map(function ($arg) {
        return ($arg == '' ? NULL : $arg);
      }, $args);

      // Datatable specific variables
      $offset = $request->query->get('start');
      $limit = $request->query->get('length');
      $search = $request->query->get('search');
      $draw = $request->query->get('draw');
      $total_items = $request->query->get('total_items');

      $path = $request->query->get('view_path');
      $dom_id = $request->query->get('view_dom_id');
      $dom_id = isset($dom_id) ? preg_replace('/[^a-zA-Z0-9_-]+/', '-', $dom_id) : NULL;
      $pager_element = $request->query->get('pager_element');
      $pager_element = isset($pager_element) ? intval($pager_element) : NULL;

      $response = new ViewAjaxResponse();

      // Remove all of this stuff from the query of the request so it doesn't
      // end up in pagers and tablesort URLs.
      foreach (array('view_name', 'view_display_id', 'view_args', 'view_path', 'view_dom_id', 'pager_element', 'view_base_path', AjaxResponseSubscriber::AJAX_REQUEST_PARAMETER) as $key) {
        $request->query->remove($key);
        $request->query->remove($key);
      }

      // Load the view.
      if (!$entity = $this->storage->load($name)) {
        throw new NotFoundHttpException();
      }
      $view = $this->executableFactory->get($entity);
      if ($view && $view->access($display_id)) {
        $response->setView($view);
        // Fix the current path for paging.
        if (!empty($path)) {
          $this->currentPath->setPath('/' . $path, $request);
        }

        // Add all POST data, because AJAX is always a post and many things,
        // such as tablesorts, exposed filters and paging assume GET.
        $request_all = $request->request->all();
        $query_all = $request->query->all();
        $request->query->replace($request_all + $query_all);

        // Overwrite the destination.
        // @see the redirect.destination service.
        $origin_destination = $path;
        $query = UrlHelper::buildQuery($request->query->all());
        if ($query != '') {
          $origin_destination .= '?' . $query;
        }
        $this->redirectDestination->set($origin_destination);

        // Override the display's pager_element with the one actually used.
        if (isset($pager_element)) {
          $view->displayHandlers->get($display_id)->setOption('pager_element', $pager_element);
        }
        // Reuse the same DOM id so it matches that in drupalSettings.
        $view->dom_id = $dom_id;

        $context = new RenderContext();
        $preview = $this->renderer->executeInRenderContext($context, function() use ($view, $display_id, $args) {
          return $view->preview($display_id, $args);
        });
        if (!$context->isEmpty()) {
          $bubbleable_metadata = $context->pop();
          BubbleableMetadata::createFromRenderArray($preview)
            ->merge($bubbleable_metadata)
            ->applyTo($preview);
        }

        $rendered_fields = $view->style_plugin->getRenderedFields();
        $items = array();

        foreach($rendered_fields as $index => $row){
            $item = [];
          foreach ($row as $key => $field) {
            array_push($item, $field);
          }
          array_push($items, $item);
        }

        $output = [];
        $output['draw'] = (int) $draw;
        $output['recordsTotal'] = $view->total_rows;
        $output['recordsFiltered'] = $view->total_rows;
        $output['data'] = $items;

        return new \Symfony\Component\HttpFoundation\JsonResponse($output);

      }
      else {
        throw new AccessDeniedHttpException();
      }
    }
    else {
      throw new NotFoundHttpException();
    }
  }

}
