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

namespace Gravity\TagBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Gravity\CmsBundle\NodeList\ListHandler;
use Gravity\TagBundle\Entity\Tag;
use Gravity\TagBundle\NodeList\TagListHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class TagNodeListController
 *
 * @author Andy Thorne <contrabandvr@gmail.com>
 */
class TagNodeListController extends Controller
{
    /**
     * @param Request $request
     * @param Tag $tag
     * @return Response
     */
    public function nodeListAction(Request $request, $tag)
    {
        $listHandler = TagListHandler::fromRequest($request);

        if (!$listHandler->getEntity()) {
            throw new BadRequestHttpException('You must specify the _entity key in the route defaults');
        }
        if (!$listHandler->getField()) {
            throw new BadRequestHttpException('You must specify the _field key in the route defaults');
        }

        /** @var EntityManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $listHandler->buildQuery($queryBuilder);

        $paginator = new Paginator($queryBuilder);

        return $this->render(
            $listHandler->getTemplate(),
            [
                'tag'        => $tag,
                'nodes'      => $paginator,
                'page_title' => $listHandler->getTitle(),
            ]
        );
    }
}