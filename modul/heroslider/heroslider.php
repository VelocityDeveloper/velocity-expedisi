<?php
/**
 * @class vdc_heroslider
 */
class vdc_heroslider extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          	=> __('VD Hero Slider', 'fl-builder'),
			'description'   	=> __('', 'fl-builder'),
			'category'      	=> __('VD Modules', 'fl-builder'),
			'partial_refresh'	=> true
		));
	}

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('vdc_heroslider', array(
	'columns'      => array(
		'title'         => __('List Image', 'fl-builder'),
		'sections'      => array(
			'general'       => array(
				'title'         => '',
				'fields'        => array(                    
					'slider_columns'   => array(
						'type'         => 'form',
						'label'        => __('Sliders', 'fl-builder'),
						'form'         => 'vdc_heroslider_form',
						'preview_text' => 'title',
						'multiple'     => true
					),
				)
			)
		)
	),
));


FLBuilder::register_settings_form('vdc_heroslider_form', array(
	'title' => __( 'Add Slider', 'fl-builder' ),
	'tabs'  => array(
		'general'      => array(
			'title'         => __('General', 'fl-builder'),
			'sections'      => array(
				'title'       => array(
					'title'         => __( 'Title', 'fl-builder' ),
					'fields'        => array(
						'title'          => array(
							'type'          => 'text',
							'label'         => __('Title', 'fl-builder'),
						),
                        'desc' => array(
                            'type'          => 'editor',
                            'label'         => __('Deskripsi', 'fl-builder'),
                            'default'       => '',
                            'placeholder'   => __('', 'fl-builder'),
                            'rows'          => '10',
                            'max'           => '300',
                            'preview'         => array(
                                'type'             => 'text',
                                'selector'         => '.heroslide-caption'  
                            )
                        ),
                        'img'    => array(
                            'type'          => 'photo',
                            'label'         => __('Image', 'fl-builder')
                        ),
					),
				),
			)
		),
	)
));