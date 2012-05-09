<?php

namespace HotDesign\SimpleCatalogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use ConfigClasses\MyConfig;

class PicType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        if (MyConfig::$shared_hosting == true) {
            $type_file = 'file';
        } else {
            $type_file = NULL;
        }



        $builder
                ->add('title', null, array(
                    'label' => 'Título'
                ))
                ->add('file', $type_file, array(
                    'required' => true,
                    'label' => 'Imágen'
                ))
        // ->add('entity')
        ;
    }

    public function getName() {
        return 'hotdesign_simplecatalogbundle_pictype';
    }

}
