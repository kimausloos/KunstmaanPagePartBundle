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
        foreach ($data as $methodName=>$value) {
            $method = 'set' . ucfirst(strtolower($methodName));
            if (!method_exists($entity, $method)) {
                $return['responseCode'] = 400;
                return new JsonResponse($return, 200, array('Content-Type' => 'application/json'));
            }

            $entity->{$method}($value);
            $return['resource'][strtolower($methodName)] = $value;
        }

        return $this->render('KunstmaanPagePartBundle:'.$className.':view.html.twig', $return);
    }

// unused at the moment. Need feedback: should this be done with an AJAX call or just hardcoded in the JS?

    /**
     * @Route("/nodes/modalEditorPagepart/", name="KunstmaanPagePartBundle_admin_modalEditorPagepart")
     *
     * @param Request $request
     *
     * @return array
     */
    public function modalEditorAction(Request $request) {
        $content = $request->request->get('content');
        $id = $request->request->get('id');
        $pagePartType = $request->request->get('id');
        return $this->render('KunstmaanPagePartBundle:PagePartAdminTwigExtension:modalEditor.html.twig', array('content' => $content, 'id' => $id, 'pagePartType' => $pagePartType));
    }

}
