<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Submission;
use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class SubmissionAdmin
 */
class SubmissionAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }


    /**
     * Form for editing
     *
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $options = [
            'required' => false,
            'disabled' => true
        ];

        /** @var Submission $submission */
        if ($submission = $this->getSubject()) {

            $container = $this->getConfigurationPool()->getContainer();

            $imagePath = implode('/', [
                $container->get('request')->getBasePath(),
                $submission->getWebPath(),
                $submission->getPath()
            ]);

            $options['help'] = '<img src="'.$imagePath.'" class="admin-preview" />';
        }

        $form
            ->add('approved', 'checkbox', [
                'label' => 'Approved',
                'required' => false
            ])
            ->add('name', 'text')
            ->add('location', 'text', ['label' => 'City'])
            ->add('path', 'text', $options)
        ;
    }

    /**
     * Filtering
     *
     * @param DatagridMapper $datagrid
     */
    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('name')
            ->add('location')
            ->add('approved')
        ;
    }

    /**
     * Table list
     *
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('id')
            ->add('name')
            ->add('location')
            ->add('approved')
        ;
    }
}