<?php
/**
 * @package   Gantry5
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   Dual License: MIT or GNU/GPLv2 and later
 *
 * http://opensource.org/licenses/MIT
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Gantry Framework code that extends GPL code is considered GNU/GPLv2 and later
 */

namespace Gantry\Admin\Controller\Html;

use Gantry\Component\Config\ConfigFileFinder;
use Gantry\Component\Controller\HtmlController;
use Gantry\Component\Request\Request;
use Gantry\Component\Response\HtmlResponse;
use Gantry\Component\Response\JsonResponse;
use Gantry\Component\Response\Response;
use Gantry\Component\Layout\Layout as LayoutObject;
use Gantry\Framework\Outlines as OutlinesObject;
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;

class Configurations extends HtmlController
{
    protected $httpVerbs = [
        'GET' => [
            '/'                 => 'index',
            '/*'                => 'forward',
            '/*/delete'         => 'confirmDeletion',
            '/*/**'             => 'forward',
        ],
        'POST' => [
            '/'                 => 'undefined',
            '/*'                => 'undefined',
            '/create'           => 'createForm',
            '/create/new'       => 'create',
            '/*/rename'         => 'rename',
            '/*/duplicate'      => 'duplicateForm',
            '/*/duplicate/new'  => 'duplicate',
            '/*/delete'         => 'undefined',
            '/*/delete/confirm' => 'delete',
            '/*/**'             => 'forward',
        ],
        'PUT'    => [
            '/'   => 'undefined',
            '/**' => 'forward'
        ],
        'PATCH'  => [
            '/'   => 'undefined',
            '/**' => 'forward'
        ]
    ];

    public function index()
    {
        /** @var UniformResourceLocator $locator */
        $locator = $this->container['locator'];

        $finder = new ConfigFileFinder;
        $files = $finder->getFiles($locator->findResources('gantry-layouts://'));
        $layouts = array_keys($files);
        sort($layouts);

        $layouts_user = array_filter($layouts, function($val) { return strpos($val, 'presets/') !== 0 && substr($val, 0, 1) !== '_'; });
        $layouts_core = array_filter($layouts, function($val) { return strpos($val, 'presets/') !== 0 && substr($val, 0, 1) === '_'; });
        $this->params['layouts'] = ['user' => $layouts_user, 'core' => $layouts_core];

        return $this->container['admin.theme']->render('@gantry-admin/pages/configurations/configurations.html.twig', $this->params);
    }

    public function createForm()
    {
        if (!$this->container->authorize('outline.create')) {
            $this->forbidden();
        }

        $params = [
            'presets' => LayoutObject::presets(),
            'outlines' => $this->container['outlines']
        ];

        $response = ['html' => $this->container['admin.theme']->render('@gantry-admin/ajax/outline-new.html.twig', $params)];

        return new JsonResponse($response);
    }

    public function create()
    {
        // Check if we want to duplicate outline instead.
        if ($this->request->post['from'] === 'outline') {
            return $this->duplicate($this->request->post['outline']);
        }

        if (!$this->container->authorize('outline.create')) {
            $this->forbidden();
        }

        /** @var OutlinesObject $outlines */
        $outlines = $this->container['outlines'];
        $title = $this->request->post['title'];
        $preset = $this->request->post['preset'];

        $id = $outlines->create(null, $title, $preset);

        $html = $this->container['admin.theme']->render(
            '@gantry-admin/layouts/outline.html.twig',
            ['name' => $id, 'title' => $outlines[$id]]
        );

        return new JsonResponse(['html' => 'Outline created.', 'id' => "outline-{$id}", 'outline' => $html]);
    }

    public function rename($outline)
    {
        if (!$this->container->authorize('outline.rename')) {
            $this->forbidden();
        }

        /** @var OutlinesObject $outlines */
        $outlines = $this->container['outlines'];
        $title = $this->request->post['title'];

        $id = $outlines->rename($outline, $title);

        $html = $this->container['admin.theme']->render(
            '@gantry-admin/layouts/outline.html.twig',
            ['name' => $id, 'title' => $outlines[$id]]
        );

        return new JsonResponse(['html' => 'Outline renamed.', 'id' => "outline-{$outline}", 'outline' => $html]);
    }

    public function duplicateForm($outline)
    {
        if (!$this->container->authorize('outline.create')) {
            $this->forbidden();
        }

        $params = [
            'outlines' => $this->container['outlines'],
            'outline' => $outline,
            'duplicate' => true
        ];

        $response = ['html' => $this->container['admin.theme']->render('@gantry-admin/ajax/outline-new.html.twig', $params)];

        return new JsonResponse($response);
    }

    public function duplicate($outline)
    {
        if (!$this->container->authorize('outline.create')) {
            $this->forbidden();
        }

        /** @var OutlinesObject $outlines */
        $outlines = $this->container['outlines'];
        $title = $this->request->post['title'];
        $inherit = in_array($this->request->post['inherit'], ['1', 'true']);

        $id = $outlines->duplicate($outline, $title, $inherit);

        $html = $this->container['admin.theme']->render(
            '@gantry-admin/layouts/outline.html.twig',
            ['name' => $id, 'title' => $outlines[$id]]
        );

        return new JsonResponse(['html' => 'Outline duplicated.', 'id' => $id, 'outline' => $html]);
    }

    public function delete($outline)
    {
        if (!$this->container->authorize('outline.delete')) {
            $this->forbidden();
        }

        /** @var OutlinesObject $outlines */
        $outlines = $this->container['outlines'];
        $list = $outlines->user();

        if (!isset($list[$outline])) {
            $this->forbidden();
        }

        $outlines->delete($outline);

        return new JsonResponse(['html' => 'Outline deleted.', 'outline' => $outline]);
    }

    /**
     * @return JsonResponse
     */
    public function confirmDeletion($id)
    {
        $params = [
            'id' => $id,
            'page_type' => 'OUTLINE',
            'outline' => $this->container['outlines']->title($id),
            'inherited' => $this->container['outlines']->getInheritingOutlines($id)
        ];

        return new JsonResponse(
            ['html' => $this->container['admin.theme']->render('@gantry-admin/pages/configurations/confirm-deletion.html.twig', $params)]
        );
    }

    public function forward()
    {
        $path = func_get_args();

        $outlines = $this->container['outlines']->toArray();

        $outline = isset($outlines[$path[0]]) ? array_shift($path) : 'default';

        $this->container['configuration'] = $outline;

        $method = $this->params['method'];
        $page = (array_shift($path) ?: 'styles');
        $resource = $this->params['location'] . '/'. $page;

        $this->params['configuration'] = $outline;
        $this->params['location'] = $resource;
        $this->params['configuration_page'] = $page;
        $this->params['navbar'] = !empty($this->request->get['navbar']);

        return $this->executeForward($resource, $method, $path, $this->params);
    }

    protected function executeForward($resource, $method = 'GET', $path, $params = [])
    {
        $class = '\\Gantry\\Admin\\Controller\\Html\\' . strtr(ucwords(strtr($resource, '/', ' ')), ' ', '\\');
        if (!class_exists($class)) {
            throw new \RuntimeException('Outline not found', 404);
        }

        /** @var HtmlController $controller */
        $controller = new $class($this->container);

        // Execute action.
        $response = $controller->execute($method, $path, $params);

        if (!$response instanceof Response) {
            $response = new HtmlResponse($response);
        }

        return $response;
    }
}
