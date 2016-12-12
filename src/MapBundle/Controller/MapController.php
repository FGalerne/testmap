<?php

namespace MapBundle\Controller;

use MapBundle\Entity\Map;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Map controller.
 *
 */
class MapController extends Controller
{
    /**
     * Lists all map entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $maps = $em->getRepository('MapBundle:Map')->findAll();

        return $this->render('MapBundle:map:index.html.twig', array(
            'maps' => $maps,
        ));
    }

    /**
     * Creates a new map entity.
     *
     */
    public function newAction(Request $request)
    {


        $map = new Map();
        $form = $this->createForm('MapBundle\Form\MapType', $map);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->getDoctrine()->getManager();
            $em->persist($map);
            $em->flush($map);

            /* API */
            $repository = $this
                ->getDoctrine()
                ->getRepository('MapBundle:Map')
            ;

            $recup = $repository->findOneBy(array(
                'id'=>'33',
            ));
            echo '<pre>';
            var_dump($recup);
            echo '</pre>';
            $adresse = $map->getAdresse();
            $cp = $map->getCp();
            $ville = $map->getVille();
            $adresse_totale = $adresse .' '. $cp .' '. $ville;
            echo '<pre>';
            var_dump($adresse_totale);
            echo '</pre>';
            die;
            /*$request = $this->get('request');
            echo '<pre>';
            var_dump($this->get(['request'][0]['mapbundle_map']));
            echo '</pre>';
            $address = $request->get('adresse').' '.$request->get('cp').' '.$request->get('ville');*/
            $url = "https://maps.google.com/maps/api/geocode/json?address=".$adresse_totale."&key=AIzaSyBSFjZGurwwEtOnMOg1mKgJgS3WcP8ucrk";

// get the json response
            $resp_json = file_get_contents($url);

// decode the json
            $resp = json_decode($resp_json, true);

// response status will be 'OK', if able to geocode given address
            if ($resp['status'] == 'OK') {

                // get the important data
                $lat = $resp['results'][0]['geometry']['location']['lat'];
                $lgt = $resp['results'][0]['geometry']['location']['lng'];


                // verify if data is complete
                if ($lat && $lgt) {
                    $map->setLat($lat);
                    $map->setLgt($lgt);


                    $em = $this->getDoctrine()->getManager();
                    $em->persist($map);
                    $em->flush($map);

                }
                /*FIN API*/

            }

            return $this->redirectToRoute('map_show', array('id' => $map->getId()));
        }

        return $this->render('MapBundle:map:new.html.twig', array(
            'map' => $map,
            'form' => $form->createView(),
        ));
    }
        /*----------------------------------- fin du code généré----------------------------*/


        /**
         * Finds and displays a map entity.
         *
         */
        public function showAction(Map $map)
    {
        $deleteForm = $this->createDeleteForm($map);

        return $this->render('MapBundle:map:show.html.twig', array(
            'map' => $map,
            'delete_form' => $deleteForm->createView(),
        ));
    }

        /**
         * Displays a form to edit an existing map entity.
         *
         */
        public function editAction(Request $request, Map $map)
    {
        $deleteForm = $this->createDeleteForm($map);
        $editForm = $this->createForm('MapBundle\Form\MapType', $map);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('map_edit', array('id' => $map->getId()));
        }

        return $this->render('MapBundle:map:edit.html.twig', array(
            'map' => $map,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

        /**
         * Deletes a map entity.
         *
         */
        public function deleteAction(Request $request, Map $map)
    {
        $form = $this->createDeleteForm($map);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($map);
            $em->flush($map);
        }

        return $this->redirectToRoute('map_index');
    }

        /**
         * Creates a form to delete a map entity.
         *
         * @param Map $map The map entity
         *
         * @return \Symfony\Component\Form\Form The form
         */
        private function createDeleteForm(Map $map)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('map_delete', array('id' => $map->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
    }
