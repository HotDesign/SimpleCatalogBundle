<?php


namespace HotDesign\SimpleCatalogBundle\Services;

use Doctrine\ORM\EntityManager;

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
