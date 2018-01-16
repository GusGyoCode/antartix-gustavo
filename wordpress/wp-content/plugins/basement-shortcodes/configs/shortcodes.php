<?php return array(
	'dummy' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Dummy',
		'title' => __( 'Dummy shortcode', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/dummy/dummy.php' )
	),
	'breakline' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Breakline',
		'title' => __( 'Break Line', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/breakline/breakline.php' )
	),
	'nonbreakablespace' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Nonbreakablespace',
		'title' => __( 'Non-Breakable Space', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/nonbreakablespace/nonbreakablespace.php' )
	),
	'mark' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Mark',
		'title' => __( 'Mark', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/mark/mark.php' )
	),

	'resetlist' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Reset_List',
		'title' => __( 'Reset List', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/resetlist/resetlist.php' )
	),
	'dl' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Dl_Tag',
		'title' => __( 'Horizontal List', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/dltag/dltag.php' )
	),
	'dt' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Dt_Tag',
		'title' => __( 'Horizontal List - Term/name (insert <dt> tag)', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/dttag/dttag.php' )
	),
	'dd' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Dd_Tag',
		'title' => __( 'Horizontal List - Describe (insert <dd> tag)', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/ddtag/ddtag.php' )
	),
	'blockquote' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Blockquote_Tag',
		'title' => __( 'Blockquote', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/blockquote/blockquote.php' )
	),
	'footer' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_QFooter_Tag',
		'title' => __( 'Blockquote Footer', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/qfooter/qfooter.php' )
	),
	'cite' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Cite_Tag',
		'title' => __( 'Blockquote Cite', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/cite/cite.php' )
	),
	'table_responsive' => array(
		'group' => 'default',
		'class' => 'Basement_Shortcode_Table_Responsive',
		'title' => __( 'Responsive tables', BASEMENT_SHORTCODES_TEXTDOMAIN ),
		'path' => realpath( dirname( __FILE__ ) . '/../shortcodes/table-responsive/table-responsive.php' )
	)
);