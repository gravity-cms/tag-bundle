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

namespace Gravity\TagBundle\NodeList;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Gravity\CmsBundle\NodeList\ListHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TagListHandler
 *
 * @author Andy Thorne <contrabandvr@gmail.com>
 */
class TagListHandler extends ListHandler
{
    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string
     */
    protected $field;

    /**
     * @param Request $request
     * @return TagListHandler
     */
    public static function fromRequest(Request $request)
    {
        /** @var TagListHandler $listHandler */
        $listHandler = parent::fromRequest($request);
        $listHandler->setField($request->attributes->get('_field'));
        $listHandler->setTag($request->attributes->get('tag'));

        if(!$request->attributes->has('_list_template')){
            $listHandler->setTemplate('GravityTagBundle:Node:list-by-tag.html.twig');
        }

        return $listHandler;
    }

    /**
     * @inheritDoc
     */
    public function buildQuery(QueryBuilder $queryBuilder)
    {
        parent::buildQuery($queryBuilder);

        $queryBuilder->innerJoin('e.'.$this->field, 't', Join::WITH, 't.name = :tag')
            ->setParameter('tag', $this->tag);
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }
}