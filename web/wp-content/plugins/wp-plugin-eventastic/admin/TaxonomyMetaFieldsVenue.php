<?php

namespace Eventastic\Admin;

/**
 * Taxonomy meta fields for venues
 *
 * GNU GENERAL PUBLIC LICENSE (Version 3, 29 June 2007)
 * 
 * Copyright (c) 2020 Madden Media
 */
 
require_once(__DIR__.'/MetaBoxAddress.php');
require_once(__DIR__.'/AbstractTaxonomyMetaFields.php');
require_once(__DIR__.'/../library/Constants.php');
require_once(__DIR__.'/../library/Utilities.php');

use Eventastic\Admin\AbstractTaxonomyMetaFields as AbstractTaxonomyMetaFields;
use Eventastic\Admin\MetaBoxAddress as MetaBoxAddress;
use Eventastic\Admin\MetaBoxContactInformation as MetaBoxContactInformation;
use Eventastic\Library\Constants as Constants;
use Eventastic\Library\Utilities as Utilities;

class TaxonomyMetaFieldsVenue extends AbstractTaxonomyMetaFields {
 
	public const META_KEY_PHONE =	array("key" => "phone", "label" => "Venue Phone", "icon" => MetaBoxContactInformation::META_KEY_PHONE["icon"]);
	public const META_KEY_EMAIL =	array("key" => "email", "label" => "Venue Email Address", "icon" => MetaBoxContactInformation::META_KEY_EMAIL["icon"]);
	public const META_KEY_URL =		array("key" => "url", "label" => "Venue URL", "icon" => MetaBoxContactInformation::META_KEY_URL["icon"]);
	
	/**
	 * Constructor
	 */
    public function __construct () {
	    parent::__construct(Utilities::getTaxonomyForClass(get_class($this)));

	    // build our fields out locally and then add actions in parent
	    $this->buildFields();
	    $this->addActions();
	}
	
	/**
	 * Builds out the taxonomy custom fields
	 *
	 * @return void
	 */
	public function buildFields () {
		
		// in this case, we can reuse some other fields from a meta view
		$this->fields = array(
			self::META_KEY_PHONE,
			self::META_KEY_EMAIL, 
			self::META_KEY_URL, 
			MetaBoxAddress::META_KEY_ADDRRESS_ONE, 
			MetaBoxAddress::META_KEY_ADDRRESS_TWO,
			MetaBoxAddress::META_KEY_CITY,
			MetaBoxAddress::META_KEY_STATE,
			MetaBoxAddress::META_KEY_ZIP
		);
	}
	
 }
