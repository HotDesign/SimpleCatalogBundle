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

	/*
	* Esta funcion se encarga de dado un id de BaseEntity
	* Busca todas las instancias de extension y las devuelve en un array
	* de la forma:
	*	$extends['NombreClase'] = array(
				'class' => NombreClase,
				'bundle_name' => NombreBundle,
				'object' => (Instancia al objeto extension)
			)
	*/


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


	/*
	*	Dado un item_id : Buscamos y devolvemos TODAS sus pics.
	*/
	public function getItemPics($item_id) {
		   return $this->em->getRepository('SimpleCatalogBundle:Pic')
		   ->findBy(array('entity' => $item_id));
	}


	/*
	*	Funcion que devuelve los items que esten activos y que esten destacados
	*	en la página principal.... Con limite. Los devuelve CON EXTENSIONS.
	*/
	public function getHomeFeatured($limit) {
		$query = 'SELECT c FROM \HotDesign\SimpleCatalogBundle\Entity\BaseEntity c WHERE c.enabled = 1 AND c.important_general = 1';
		$query = $this->em->createQuery($query)->setMaxResults($limit);

		$items = $query->getResult();

		$out = array();
		foreach ($items as $item) {
			//Obtengo las extensiones
			$extends = $this->getExtensions($item->getId(), $item->getCategory()->getType() );
			$out[] = array(
					'BaseEntity' => $item, //Aqui viaja el objeto BaseEntity (Info genérica)
					'extends' => $extends //Aqui viaja el array de objetos extension.
					);
		}
		return $out;
	}

	/*
	*	Funcion utilizada en Detalle. Dado un id devuelve la BaseEntity 
	*	y sus respectivas extensiones.
	*/
	public function getFullItem($item_slug) {
        $repo = $this->em->getRepository('SimpleCatalogBundle:BaseEntity');
        $entity = $repo->findOneBySlug($item_slug);
       
        if (!$entity || !$entity->getEnabled()) {
        	return false;
        }

		$out['BaseEntity'] = $entity;
		$out['pics'] = $this->getItemPics($entity->getId());
		$out['extends'] = $this->getExtensions($entity->getId(), $entity->getCategory()->getType() );
		return $out;
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

}
