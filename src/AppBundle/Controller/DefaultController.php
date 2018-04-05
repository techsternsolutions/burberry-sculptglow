<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Submission;
use AppBundle\Form\PictureType;
use AppBundle\Form\SubmissionType;
use Imagine\Filter\Basic\Autorotate;
use Imagine\Filter\Transformation;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Metadata\ExifMetadataReader;
use Imagine\Image\Point;
use JMS\DiExtraBundle\Annotation\Inject;
use Liip\ImagineBundle\Controller\ImagineController;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

class DefaultController extends Controller
{
    /**
     * @Route("/locale/{_locale}", name="locale")
     */
    public function localeAction( $_locale )
    {
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/", name="home")
     * @Template("default/index.html.twig")
     */
    public function indexAction(  )
    {
        return [];
    }

    /**
     * @Route("/product", name="product")
     * @Template("default/product.html.twig")
     */
    public function productAction()
    {
        return [];
    }

    /**
     * @Route("/share", name="share")
     * @Template("default/share.html.twig")
     */
    public function shareAction()
    {
        $debug = 1;

        $items = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Submission')
            ->findApproved()
        ;

        return [
            'gallery' => $items,
            'picture' => $this->createForm(new PictureType())->createView(),
            'submission' => $this->createForm(new SubmissionType())->createView()
        ];
    }

    /**
     * @Route("/upload", name="upload")
     * @Template("default/crop.html.twig")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function uploadAction(Request $request)
    {
        $file = null;
        $form = $this->createForm(new  PictureType());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var File $file */
            $file = $form['picture']->getData();

            $file = $file->move(
                $this->getParameter('kernel.root_dir') . '/../web/media',
                uniqid('o-', true) . '.' .  $file->guessExtension()
            );

            /*
             * Autorotate using exif data ....
             * TODO: resize for old android when we run into issues
             */
            (new Transformation())
                ->add(new Autorotate())
                ->apply(
                    (new Imagine())
                        ->setMetadataReader(new ExifMetadataReader())
                        ->open($file->getPathname())
                )->save()
            ;
        }

        return [
            'file' => $file,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/like/{id}", name="like")
     *
     * @param Submission $submission
     * @return string
     */
    public function likeAction(Submission $submission)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $liked = $session->get('liked', []);

        if (in_array($submission->getId(), $liked)){
            return new JsonResponse(['error' => 'Already liked this selfie'], Response::HTTP_I_AM_A_TEAPOT);
        }

        $submission->setLikes($submission->getLikes() + 1);

        $em->persist($submission);
        $em->flush();

        //TODO: saner way of tracking who liked what.
        $liked[] = $submission->getId();
        $session->set('liked', $liked);
        $session->save();

        return new JsonResponse(['likes' => $submission->getLikes()]);
    }

    /**
     * @Route("/selfie", name="selfie")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function selfieAction(Request $request)
    {
        $submission = new Submission();

        if ($this->getParameter('automatic_approval')){
            $submission->setApproved(true);
        }

        $form = $this->createForm(new  SubmissionType(), $submission);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //TODO: Custom validator for this json array
            $crop = json_decode($submission->getPath(), true);

            $file = realpath($this->getParameter('kernel.root_dir') . '/../web/media/' . basename($crop['source']));

            (new Imagine())
                ->open($file)
                ->crop(
                    new Point($crop['x'], $crop['y']),
                    new Box($crop['w'], $crop['h'])
                )
                ->save(
                    $file = $this->getParameter('kernel.root_dir') .
                    '/../web/uploads/' .
                    str_replace('o-', 's-', basename($file))
                )
            ;

            $submission->setPath(basename($file));

            $em->persist($submission);
            $em->flush();
        }

        return $this->redirectToRoute('share');
    }

    /**
     * @Route("/practice", name="practice")
     * @Template("default/practice.html.twig")
     */
    public function practiceAction()
    {
        return [];
    }
}
