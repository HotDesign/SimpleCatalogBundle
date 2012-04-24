<?php


namespace HotDesign\SimpleCatalogBundle\Services;

use Doctrine\ORM\EntityManager;



use Pagerfanta\Pagerfanta,
    Pagerfanta\Adapter\DoctrineORMAdapter,
    Pagerfanta\Exception\NotValidCurrentPageException;
use HotDesign\SimpleCatalogBundle\Config\ItemTypes;
use HotDesign\SimpleCatalogBundle\Config\MyConfig;
/*


Para usarlo, dentro de cualquier controller:

	        $ItemService = $this->get('item.service');

        echo $ItemService->getBasicItem(2); die;

/*/


class ItemService {

	protected $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	public function getExtensions($item_id, $category_type) {

        //Obtenemos el array de las clases que extiende
        $class_extends = ItemTypes::getClassExtends($category_type);

        $extends = array();

        //Recuperamos toda la información de las clases a las cuales extiende.
        foreach ($class_extends as $extend) {
            $e = array();
            $e['class'] = $extend['class'];
            $e['bundle_name'] = $extend['bundle_name'];
            $e['object'] = $this->em->getRepository(
                             $extend['bundle_name'] . ':' . $extend['class'])
                     ->findOneBy(array('base_entity' => $item_id )
             );
            $extends[$e['class']] = $e;
        }
        return $extends;
	}

	public function getHomeFeatured($limit) {
		$query = 'SELECT c FROM \HotDesign\SimpleCatalogBundle\Entity\BaseEntity c WHERE c.enabled AND c.important_general = 1';
		$query = $this->em->createQuery($query)->setMaxResults($limit);

		$items = $query->getResult();

		$areturn = array();
		foreach ($items as $item) {
			$extends = $this->getExtensions($item->getId(), $item->getCategory()->getType() );
			$areturn[] = array('BaseEntity' => $item, 'extends' => $extends);
		}

		return $areturn;
	}

	//Ejemplos de metodos que se podrian crear: 
	public function getBasicItemListing($category, $count) {  }
	public function getFullItemListing($category, $max_per_page) {}
	public function getBasicItemListingblabla($category, $max_per_page) {}

	/**
	*	Devuelve la información básica de un Item (Sin Extensiones)
	*/
	public function getBasicItem($id) {
		$query = 'SELECT c FROM \HotDesign\SimpleCatalogBundle\Entity\BaseEntity c WHERE c.id = ' . $id;
		$query = $this->em->createQuery($query);

		echo '<pre>';
		print_r($query->getArrayResult());
		die;
	}	

	/**
	*	Devuelve la información completa de un producto
	*/
	public function getFullItem($id) {
		return 'This is a full Item';
		//Aqui programar logica de extensions.
	}



}
