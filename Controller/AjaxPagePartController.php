<?php
namespace Kunstmaan\PagePartBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for the pagepart administration
 */
class AjaxPagePartController extends Controller
{
    /**
     * @Route("/nodes/renderPagepart/", name="KunstmaanPagePartBundle_admin_editpagepart")
     *
     * @param Request $request
     *
     * @return array
     */
    public function editPagePartAction(Request $request)
    {
        $content = $request->request->get('content');
        $type = $request->request->get('type');
        $data = $request->request->get('data');

        $className = $type . 'PagePart';
        $classPath = 'Kunstmaan\PagePartBundle\Entity\\' . $className;

        if (!class_exists($classPath)) {
            $return['responseCode'] = 400;
            return new JsonResponse($return, 200, array('Content-Type' => 'application/json'));
        }

        $entity = new $classPath();
        $return['resource'] = array();
        foreach ($data as $input) {
            $parts = explode('_', $input['name']);
            $methodName = (strtolower(end($parts)));
            $method = 'set' . ucfirst($methodName);
            if (!method_exists($entity, $method)) {
                $return['responseCode'] = 400;
                return new JsonResponse($return, 200, array('Content-Type' => 'application/json'));
            }

            $entity->{$method}($input['value']);
            $return['resource'][strtolower($methodName)] = $input['value'];
        }

        return $this->render('KunstmaanPagePartBundle:'.$className.':view.html.twig', $return);
    }
}
