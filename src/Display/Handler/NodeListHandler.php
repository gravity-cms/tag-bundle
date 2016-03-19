<?php
/**
 * Copyright (c) 2016 Gravity CMS.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Gravity\TagBundle\Display\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Gravity\CmsBundle\Display\Handler\DisplayHandlerInterface;
use Gravity\CmsBundle\Entity\FieldableEntity;
use Gravity\TagBundle\Entity\Tag;
use Gravity\TagBundle\NodeList\TagListHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NodeListHandler
 *
 * @author Andy Thorne <contrabandvr@gmail.com>
 */
class NodeListHandler implements DisplayHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * NodeListHandler constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'node_list';
    }

    /**
     * @inheritDoc
     */
    public function setOptions(OptionsResolver $optionsResolver, array $options = [])
    {
        $optionsResolver->setRequired('node_types');
        $optionsResolver->setAllowedTypes('node_types', 'array');
        $optionsResolver->setDefaults([
            'page_size' => 20,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function supportsRequest(Request $request)
    {
        return $request->attributes->get('_format') === 'html';
    }

    /**
     * @inheritDoc
     */
    public function getTemplate()
    {
        return 'GravityTagBundle:Node:list-by-tag.html.twig';
    }

    /**
     * @param Tag $entity
     * @param array $options
     * @return array
     */
    public function getTemplateOptions(FieldableEntity $entity, array $options = [])
    {
        foreach($options['node_types'] as $nodeClass => $nodeField){
            $listHandler = new TagListHandler($nodeClass);
            $listHandler->setField($nodeField);
            $listHandler->setTag($entity->getName());
            $listHandler->setPageSize($options['page_size']);
            $listHandler->setTitle($entity->getName());
        }

        $queryBuilder = new QueryBuilder($this->entityManager);
        $listHandler->buildQuery($queryBuilder);
        $nodes = $queryBuilder->getQuery()->execute();

        return [
            'tag'   => $entity->getName(),
            'nodes' => $nodes,
        ];
    }

}