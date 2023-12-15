<?php
/**
 * @class vdc_carouselimage
 */
class vdc_carouselimage extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          	=> __('VD Carousel Image', 'fl-builder'),
			'description'   	=> __('', 'fl-builder'),
			'category'      	=> __('VD Modules', 'fl-builder'),
			'partial_refresh'	=> true
		));
	}

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('vdc_carouselimage', array(
	'columns'      => array(
		'title'         => __('List Image', 'fl-builder'),
		'sections'      => array(
			'general'       => array(
				'title'         => '',
				'fields'        => array(
                    'multiple_photos' => array(
                        'type'          => 'multiple-photos',
                        'label'         => __( 'Gallery', 'fl-builder' )
                      ),
                      'showdesktop' => array(
                          'type'        => 'unit',
                          'label'       => 'Show in desktop',
                          'default'     => '4',
                          'description' => '',
                          'responsive'  => false,
                      ),
                      'showmobile' => array(
                          'type'        => 'unit',
                          'label'       => 'Show in mobile',
                          'default'     => '2',
                          'description' => '',
                          'responsive'  => false,
                      ),
				)
			)
		)
	),
));
