<?php

namespace Drupal\Tests\big_pipe\Unit\Render;

use Drupal\big_pipe\Render\BigPipe;
use Drupal\big_pipe\Render\BigPipeResponse;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Render\HtmlResponse;
use Drupal\Core\Render\RendererInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ManyPlaceholderTest.
 *
 * @package Drupal\Tests\big_pipe\Unit\Render\Placeholder
 *
 * @coversDefaultClass \Drupal\big_pipe\Render\BigPipe
 *
 * @group big_pipe
 */
class ManyPlaceholderTest extends UnitTestCase {

  /**
   * Minimal testcase for the fragment extraction with many placeholders.
   */
  public function testManyPlaceHolders() {
    // Mock all the dependencies.
    $renderer = $this->createMock(RendererInterface::class);
    $session = $this->createMock(SessionInterface::class);
    $requestStack = new RequestStack();
    $kernel = $this->createMock(HttpKernelInterface::class);
    $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $bigpipe = new BigPipe($renderer, $session, $requestStack, $kernel, $eventDispatcher, $configFactory);
    $htmlResponse = HtmlResponse::create();
    $response = new BigPipeResponse($htmlResponse);

    // Add many placeholders.
    $manyPlaceholders = [];
    for ($i = 0; $i < 400; $i++) {
      $manyPlaceholders[$this->randomMachineName(80)] = $this->randomMachineName(80);
    }
    $attachments = [
      'library' => [],
      'big_pipe_nojs_placeholders' => $manyPlaceholders,
    ];
    $response->setAttachments($attachments);

    // Construct html with all required tags.
    $content = '<html><body>content<drupal-big-pipe-scripts-bottom-marker>script-bottom<drupal-big-pipe-scripts-bottom-marker></body></html>';
    $response->setContent($content);

    // Capture the result to avoid PHPUnit complaining.
    ob_start();
    $bigpipe->sendContent($response);
    $result = ob_get_clean();

    $this->assertNotEmpty($result);
  }

}
