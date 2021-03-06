<?php

namespace Ninfac\ContaBundle\Controller;

use Ninfac\ContaBundle\Service\ConsultaCatalogos;
use Ninfac\ContaBundle\Entity\ConPartidacontable;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContaController extends Controller
{
    /**
     *
     * @author julio castillo
     * analista programador
     */
    /**
     * @Route("/conta/partidacontable/create", name="conta_partidacontable_create", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function contaPartidacontableCreateAction(Request $request)
    {
        //instanciando Service
        $consultaCatalogos = $this->container->get('conta.consulta_catalogos');

        /*
        * instanciar variables y objetos de la tabla principal
        */
        $empresaId      = $this->get('session')->get('empresaId');
        $tipoPartidaId  = 1;
        $formapartidaId = 1;
        $anioId         = $this->get('session')->get('anioId')->getId();
        $userId         = $this->container->get('security.token_storage')->getToken()->getUser()->getId();

        $ultimaPartida  = $consultaCatalogos->getNumeroPartida($empresaId,$anioId,$tipoPartidaId);

        $empresa        = $consultaCatalogos->getEmpresa($empresaId);
        $formapartida   = $consultaCatalogos->getFormapartida($formapartidaId);
        $anio           = $consultaCatalogos->getAnio($anioId);
        $tipopartida    = $consultaCatalogos->getTipopartida($tipoPartidaId);

        if($ultimaPartida) //evaluar si hay una partida similar para determinar correlativos
        {
            $corrAnual      = $ultimaPartida[0]->getCorrAnual()+1;
            $corrMensual    = $ultimaPartida[0]->getCorrMensual()+1;
            $corrTipo       = $ultimaPartida[0]->getCorrTipo()+1;
        } else { // todos inician con 1 nuevo correlativo
            $corrAnual      = 1;
            $corrMensual    = 1;
            $corrTipo       = 1;
        }

        // asignar valores por defecto;
        $classForm = new ConPartidacontable();
        $classForm->setIdEmpresa($empresa);
        $classForm->setIdFormapartida($formapartida);
        $classForm->setIdAnio($anio);
        $classForm->setNumero(0);
        $classForm->setCorrAnual($corrAnual);
        $classForm->setCorrMensual($corrMensual);
        $classForm->setCorrTipo($corrTipo);
        $classForm->setIdTipopartida($tipopartida);
        $classForm->setFecha(new \DateTime());
        $classForm->setTotalDebe(0);
        $classForm->setTotalHaber(0);
        $classForm->setActivo(TRUE);
        $classForm->setCreatedBy($userId);
        $classForm->setCreatedAt(new \DateTime());

        $form = $this->createFormBuilder($classForm)
            ->add('corrAnual', IntegerType::class, array(
                'read_only' => TRUE,
                'label'     => 'Correlativo anual',
                'attr'      => array(
                        'style' => 'width:100px'
            )))
            ->add('corrMensual', IntegerType::class, array(
                'read_only' =>TRUE,
                'label'     => 'Correlativo mensual',
                'attr'      => array(
                        'style' => 'width:100px'
            )))
            ->add('corrTipo', IntegerType::class, array(
                'read_only' =>TRUE,
                'label'     => 'Correlativo tipo',
                'attr'      => array(
                        'style' => 'width:100px'
            )))
            ->add('idTipopartida', 'entity', array(
                'class'     => 'NinfacContaBundle:CtlTipopartida',
                'property'  => 'nombre',
                'label'     => 'Tipo partida',
                'attr'      => array(
                        'style' => 'width:200px'
            )))
            ->add('fecha', DateType::class)
            ->add('concepto', TextType::class,array(
                'attr'=> array(
                    'style' => 'width:350px'
            )))
            ->add('save', SubmitType::class, array(
                'label'     => 'Guardar',
                'attr'      => array(
                    'class'     => 'btn btn-success btn-block',
                    'style'     => 'width:200px'
            )))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //consultar correlativo anual//
            $em = $this->getDoctrine()->getEntityManager();

            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $data = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $em->persist($data);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('NinfacContaBundle:Conta:partidacontable_edit.html.twig', array(
            'form' => $form->createView(),
            'titulo' => 'Partida contable',
            'icon'   => 'fa fa-list-alt',
            'with'   => '860px'

        ));
    }


    /**
     *
     * @author julio castillo
     * analista programador
     */
    /**
     * @Route("/conta/partidacontable/edit", name="conta_partidacontable_edit", options={"expose"=true})
     * @Method({"GET", "POST"})
     */
    public function contaPartidacontableEditAction(Request $request)
    {
        //instanciando Service
        $consultaCatalogos = $this->container->get('conta.consulta_catalogos');

        /*
        * instanciar variables y objetos de la tabla principal
        */
        $empresaId      = $this->get('session')->get('empresaId');
        $tipoPartidaId  = 1;
        $formapartidaId = 1;
        $anioId         = $this->get('session')->get('anioId')->getId();
        $userId         = $this->container->get('security.token_storage')->getToken()->getUser()->getId();

        $ultimaPartida  = $consultaCatalogos->getNumeroPartida($empresaId,$anioId,$tipoPartidaId);

        $empresa        = $consultaCatalogos->getEmpresa($empresaId);
        $formapartida   = $consultaCatalogos->getFormapartida($formapartidaId);
        $anio           = $consultaCatalogos->getAnio($anioId);
        $tipopartida    = $consultaCatalogos->getTipopartida($tipoPartidaId);

        if($ultimaPartida) //evaluar si hay una partida similar para determinar correlativos
        {
            $corrAnual      = $ultimaPartida[0]->getCorrAnual()+1;
            $corrMensual    = $ultimaPartida[0]->getCorrMensual()+1;
            $corrTipo       = $ultimaPartida[0]->getCorrTipo()+1;
        } else { // todos inician con 1 nuevo correlativo
            $corrAnual      = 1;
            $corrMensual    = 1;
            $corrTipo       = 1;
        }

        // asignar valores por defecto;
        $classForm = new ConPartidacontable();
        $classForm->setIdEmpresa($empresa);
        $classForm->setIdFormapartida($formapartida);
        $classForm->setIdAnio($anio);
        $classForm->setNumero(0);
        $classForm->setCorrAnual($corrAnual);
        $classForm->setCorrMensual($corrMensual);
        $classForm->setCorrTipo($corrTipo);
        $classForm->setIdTipopartida($tipopartida);
        $classForm->setFecha(new \DateTime());
        $classForm->setTotalDebe(0);
        $classForm->setTotalHaber(0);
        $classForm->setActivo(TRUE);
        $classForm->setCreatedBy($userId);
        $classForm->setCreatedAt(new \DateTime());

        $form = $this->createFormBuilder($classForm)
            ->add('corrAnual', IntegerType::class, array(
                'read_only' => TRUE,
                'label'     => 'Correlativo anual',
                'attr'      => array(
                        'style' => 'width:100px'
            )))
            ->add('corrMensual', IntegerType::class, array(
                'read_only' =>TRUE,
                'label'     => 'Correlativo mensual',
                'attr'      => array(
                        'style' => 'width:100px'
            )))
            ->add('corrTipo', IntegerType::class, array(
                'read_only' =>TRUE,
                'label'     => 'Correlativo tipo',
                'attr'      => array(
                        'style' => 'width:100px'
            )))
            ->add('idTipopartida', 'entity', array(
                'class'     => 'NinfacContaBundle:CtlTipopartida',
                'property'  => 'nombre',
                'label'     => 'Tipo partida',
                'attr'      => array(
                        'style' => 'width:200px'
            )))
            ->add('fecha', DateType::class)
            ->add('concepto', TextType::class,array(
                'attr'=> array(
                    'style' => 'width:350px'
            )))
            ->add('save', SubmitType::class, array(
                'label'     => 'Guardar',
                'attr'      => array(
                    'class'     => 'btn btn-success btn-block',
                    'style'     => 'width:200px'
            )))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //consultar correlativo anual//
            $em = $this->getDoctrine()->getEntityManager();

            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $data = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $em->persist($data);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('NinfacContaBundle:Conta:partidacontable_edit.html.twig', array(
            'form' => $form->createView(),
            'titulo' => 'Partida contable',
            'icon'   => 'fa fa-list-alt',
            'with'   => '860px'

        ));

        // $em = $this->getDoctrine()->getEntityManager();
        // $empresas = $em->getRepository('NinfacContaBundle:CtlEmpresa')->findBy(array('activo'=>true));
        // return $this->render('NinfacContaBundle:Conta:partidacontable_edit.html.twig', array(
        //     'titulo' => 'Partida contable',
        //     'icon'   => 'glyphicon glyphicon-usd',
        //     'with'   => '860px'
        // ));
    }

}
