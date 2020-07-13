<?php
/**
 * Sudhanshu Kumar
 * 07-12-2020
 */
namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\File;



class PostController extends Controller
{
    /**
     * @Route("/post", name="view_posts_route")
     */
    public function showAllPostsAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();
        return $this->render('pages/index.html.twig', ['posts' => $posts]);
    }

    /**
     * @Route("/create", name="create_post_route")
     */
    public function createPostAction(Request $request)
    {
        $post = new Post;
        $form = $this->createFormBuilder()
            ->add('firstName', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('lastName', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('streetAndNumber', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('zip', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('city', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('country', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('phoneNumber', IntegerType::class, array('attr' => array('class' => 'form-control')))
            ->add('birthday', DateType::class, array('attr' => array('class' => 'form-control')))
            ->add('emailAddress', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('picture', FileType::class, array('constraints' => [
                new File(['maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid Image File',
                ])
            ], 'attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Save','attr' => array('class' => 'btn-primary', 'style' => 'margin-top:20px;margin-bottom:20px')))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $firstName = $form['firstName']->getData();
            $lastName = $form['lastName']->getData();
            $streetAndNumber = $form['streetAndNumber']->getData();
            $zip = $form['zip']->getData();
            $city = $form['city']->getData();
            $country = $form['country']->getData();
            $phoneNumber = $form['phoneNumber']->getData();
            $birthday = $form['birthday']->getData();
            $emailAddress = $form['emailAddress']->getData();
            $picture = $form['picture']->getData();
            if ($picture) {
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . uniqid() . '.' . $picture->guessExtension();
                try {
                    $picture->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
            $post->setFirstName($firstName);
            $post->setLastName($lastName);
            $post->setStreetAndNumber($streetAndNumber);
            $post->setZip($zip);
            $post->setCity($city);
            $post->setCountry($country);
            $post->setPhoneNumber($phoneNumber);
            $post->setBirthday($birthday);
            $post->setEmailAddress($emailAddress);
            $post->setPicture($newFilename);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            $this->addFlash('message', 'New Address Added Successfully');
            return $this->redirectToRoute('view_posts_route');
        }

            return $this->render('pages/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/view/{id}", name="view_post_route")
     */
    public function viewPostAction($id)
    {
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
        return $this->render('pages/view.html.twig', ['posts' => $posts]);
    }

    /**
     * @Route("/edit/{id}", name="edit_post_route")
     */
    public function editPostAction(Request $request, $id)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
        $post->setFirstName($post->getfirstName());
        $post->setLastName($post->getlastName());
        $post->setStreetAndNumber($post->getstreetAndNumber());
        $post->setZip($post->getzip());
        $post->setCity($post->getcity());
        $post->setCountry($post->getcountry());
        $post->setPhoneNumber($post->getphoneNumber());
        $post->setBirthday($post->getbirthday());
        $post->setEmailAddress($post->getemailAddress());
        $post->setPicture($post->getpicture());
        $form = $this->createFormBuilder($post)
            ->add('firstName', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('lastName', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('streetAndNumber', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('zip', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('city', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('country', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('phoneNumber', IntegerType::class, array('attr' => array('class' => 'form-control')))
            ->add('birthday', DateType::class, array('attr' => array('class' => 'form-control')))
            ->add('emailAddress', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('picture', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('save', SubmitType::class, array('label' => 'Update', 'attr' => array('class' => 'btn-primary', 'style' => 'margin-top:20px;margin-bottom:20px')))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $firstName = $form['firstName']->getData();
            $lastName = $form['lastName']->getData();
            $streetAndNumber = $form['streetAndNumber']->getData();
            $zip = $form['zip']->getData();
            $city = $form['city']->getData();
            $country = $form['country']->getData();
            $phoneNumber = $form['phoneNumber']->getData();
            $birthday = $form['birthday']->getData();
            $emailAddress = $form['emailAddress']->getData();
            $picture = $form['picture']->getData();
            if ($picture) {
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . uniqid() . '.' . $picture->guessExtension();
                try {
                    $picture->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
            $em = $this->getDoctrine()->getManager();
            $post = $em->getRepository('AppBundle:Post')->find($id);

            $post->setFirstName($firstName);
            $post->setLastName($lastName);
            $post->setStreetAndNumber($streetAndNumber);
            $post->setZip($zip);
            $post->setCity($city);
            $post->setCountry($country);
            $post->setPhoneNumber($phoneNumber);
            $post->setBirthday($birthday);
            $post->setEmailAddress($emailAddress);
            $post->setPicture($newFilename);

            $em->flush();
            $this->addFlash('message', 'address Updated Successfully');
            return $this->redirectToRoute('view_posts_route');

        }
        return $this->render('pages/edit.html.twig', ['form' => $form->createView()]);

    }

    /**
     * @Route("/delete/{id}", name="delete_post_route")
     */
    public function deletePostAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:Post')->find($id);
        $em->remove($post);
        $em->flush();
        $this->addFlash('message', 'address Deleted Successfully');
        return $this->redirectToRoute('view_posts_route');

    }

}
