<?php
namespace KMGi\CommonBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator as DCG;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DoctrineCrudGenerator extends DCG
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct($this->container->get('filesystem'));
    }

    /**
     * {@inheritDoc}
     */
    protected function generateShowView($dir)
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        $controllerSubFolder = $this->container->getParameter('kmgi_common.controller.subfolder');
        if($controllerSubFolder)
        {
            $entity = trim($controllerSubFolder, '/') . '\\' . $entity;
        }
        return parent::generate($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);
    }

    /**
     * {@inheritDoc}
     */
    protected function generateControllerClass($forceOverwrite)
    {
        if($controllerRouteNamePrefix = $this->container->getParameter('kmgi_common.controller.route_name_prefix'))
        {
            $this->routeNamePrefix = $controllerRouteNamePrefix . '_' . $this->routeNamePrefix;
        }
        parent::generateControllerClass($forceOverwrite);
    }

    /**
     * {@inheritDoc}
     */
    protected function render($template, $parameters)
    {
        $templateLayoutSubfolder = $this->container->getParameter('kmgi_common.layout_template.subfolder');
        $templateLayoutFilename = $this->container->getParameter('kmgi_common.layout_template.filename');
        $controllerSubFolder = $this->container->getParameter('kmgi_common.controller.subfolder');
        $realEntity = $parameters['entity'];
        if($controllerSubFolder && array_key_exists('entity', $parameters))
        {
            $realEntity = str_replace(trim($controllerSubFolder, '/') . '\\', '', $realEntity);
        }
        return parent::render(
            $template,
            array_merge(
                $parameters,
                [
                    'layout_subfolder' => trim($templateLayoutSubfolder, '/'),
                    'layout_filename' => $templateLayoutFilename,
                    'real_entity' => $realEntity,
                ]
            )
        );
    }
}