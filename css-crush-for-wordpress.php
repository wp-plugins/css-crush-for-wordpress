<?php
/*
Plugin Name: 		CSS Crush for WordPress
Version: 			0.3
Description: 		Integrates an extensible CSS preprocessor for WordPress: upload, activate, and you're done. No further configuration needed. 
Author: 			Codepress
Author URI: 		http://www.codepress.nl
Plugin URI: 		http://wordpress.org/extend/plugins/css-crush-for-wordpress/
Text Domain: 		css-crush-for-wordpress
Domain Path: 		/languages
License:			GPLv2

Copyright 2012  Codepress  info@codepress.nl

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'CPCC_VERSION', 	'0.3' );
define( 'CPCC_TEXTDOMAIN',  'css-crush-for-wordpress' );
define( 'CPCC_SLUG', 		'css-crush' );
define( 'CPCC_URL', 		plugins_url('', __FILE__) );

/**
 * Codepress_Crush_CSS
 *
 * @since     0.1
 *
 */
class Codepress_Crush_CSS
{	
	/**
	 * Constructor
	 *
	 * @since     0.1
	 */
	function __construct()
	{		
		// translations
		load_plugin_textdomain( CPCC_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		// crush enqueued styles
		add_filter( 'style_loader_src', array( $this, 'crush_css'), 10, 1);
		
		// crush theme's stylesheet
		add_filter( 'stylesheet_uri', array( $this, 'crush_css'), 10, 1);
		
		// register ui settings
		add_action( 'admin_menu', array( $this, 'settings_menu') );		
		add_action( 'admin_init', array( $this, 'register_settings') );		
		add_filter( 'plugin_action_links',  array( $this, 'add_settings_link'), 1, 2);		
	}
	
	/**
	 *	Crush CSS
	 *
     * @since     0.1
	 */
	function crush_css( $src ) 
	{
		$options = get_option('cpcc_options');
		
		// Only crush the theme's frontend CSS
		if ( is_admin() || strstr($src, get_bloginfo('template_url') ) === false || !$this->is_enabled() )
			return $src;
		
		// set caching & debug mode
		$cache = isset($options['cache']) && $options['cache'] == 'on' ? true : false ;
		$debug = isset($options['debug']) && $options['debug'] == 'on' ? true : false ;
		
		require_once dirname( __FILE__ ) . "/crush/CssCrush.php";
		
		// get version
		wp_parse_str($src, $out);
		$ver = current($out);
		
		// remove version from querystring
		$src_no_ver = remove_query_arg('ver',$src);
		
		// get local path and crush it!
		$src_local = str_replace(WP_CONTENT_URL, WP_CONTENT_DIR, $src_no_ver);
		
		// remove possible crushed stylesheets
		if ( strstr($src, '.crush.css') )
			return $src;
		
		$src = csscrush::file( $src_local, array(
			'cache'	=> $cache,
			'debug'	=> $debug,
		)); 		

		// get root url
		$parse 		= parse_url(get_bloginfo('url'));
		$port 		= !empty($parse['port']) ? ":{$parse['port']}" : '';
		$root_url  	= "{$parse['scheme']}://{$parse['host']}{$port}";
	
		return $root_url.$src;	
	}
	


	
	/**
	 * Enabled?
	 *
	 * @since     0.1
	 */
	private function is_enabled() 
	{
		$options = get_option('cpcc_options');
		
		if ( isset($options['enable']) && $options['enable'] == 'on' )
			return true;
			
		return false;		
	}
	
	/**
	 * Admin Menu.
	 *
	 * Create the admin menu link for the settings page.
	 *
	 * @since     0.1
	 */
	public function settings_menu() 
	{
		// options page; title, menu title, capability, slug, callback
		$page = add_options_page(
			__( 'CSS Crush', CPCC_TEXTDOMAIN ), 
			__( 'CSS Crush', CPCC_TEXTDOMAIN ), 
			'manage_options',
			CPCC_SLUG,
			array( $this, 'plugin_settings_page')
		);		

		// set admin page
		$this->admin_page = $page;
		
		// settings page specific styles and scripts
		add_action( "admin_print_styles-$page", array( $this, 'admin_styles') );
	}
	
	/**
	 * Register admin css
	 *
	 * @since     0.1
	 */
	public function admin_styles()
	{
		wp_enqueue_style( 'cpcc-admin', CPCC_URL.'/assets/css/admin.css', array(), CPCC_VERSION, 'all' );	
	}
	
	/**
	 * Add Settings link to plugin page
	 *
	 * @since     0.1
	 */
	public function add_settings_link( $links, $file ) 
	{
		if ( $file != plugin_basename( __FILE__ ))
			return $links;

		array_unshift( $links, '<a href="' .  admin_url("admin.php") . '?page=' . CPCC_SLUG . '">' . __( 'Settings', CPCC_TEXTDOMAIN) . '</a>' );
		
		return $links;
	}
	
	/**
	 * Register plugin options
	 *
	 * @since     0.1
	 */
	public function register_settings() 
	{
		// If we have no options in the database, let's add them now.
		if ( false === get_option('cpcc_options') ) {
			add_option( 'cpcc_options', $this->get_default_plugin_options() );
		}

		register_setting( 'cpcc-settings-group', 'cpcc_options' );
	}	

	/**
	 * Returns the default plugin options.
	 *
	 * @since     0.1
	 */
	public function get_default_plugin_options() 
	{
		$default_plugin_options = array();
		foreach ( $this->get_options() as $option ) {
			$id = $option['id'];
			
			$default_plugin_options[$id] = $option['default'] ? 'on' : '';
		}
	
		return apply_filters( 'cpcc_default_plugin_options', $default_plugin_options );
	}
	
	/**
	 * Get options
	 *
	 * @since     0.1
	 */
	public function get_options() 
	{
		$options = array(
			array(
				'id'		=> 'enable',
				'label'		=> __('CSS Crush', CPCC_TEXTDOMAIN),
				'descr'		=> __('Enable CSS Crush', CPCC_TEXTDOMAIN),
				'default' 	=> true
			),
			array(
				'id'		=> 'cache',
				'label'		=> __('Caching', CPCC_TEXTDOMAIN),
				'descr'		=> __('Turn caching on or off', CPCC_TEXTDOMAIN),
				'note'		=> __('When turned on a cached file is returned when a file has not been modified. This increases performance. Default is <code>on</code>.', CPCC_TEXTDOMAIN),
				'default' 	=> true
			),
			array(
				'id'		=> 'debug',
				'label'		=> __('Debug Mode', CPCC_TEXTDOMAIN),
				'descr'		=> __('Turn debug mode on or off', CPCC_TEXTDOMAIN),
				'note'		=> __('You can disable minification by running in debug mode. Default is <code>off</code>.', CPCC_TEXTDOMAIN),
				'default' 	=> false
			)			
		);
		return $options;
	}
	
	/**
	 * Settings Page Template.
	 *
	 * This function in conjunction with others usei the WordPress
	 * Settings API to create a settings page where users can adjust
	 * the behaviour of this plugin. 
	 *
	 * @since     0.1
	 */
	public function plugin_settings_page() 
	{		
		$saved = get_option('cpcc_options');
		
		$rows = '';
		foreach ( $this->get_options() as $input ) {
			$id = $input['id'];
			
			$checked = isset($saved[$id]) && $saved[$id] == 'on' ? " checked='checked'" : '';
			$descr	 = !empty($input['descr']) ? $input['descr'] : '';
			$note	 = !empty($input['note']) ? "<p class='description'>{$input['note']}</p>" : '';
			
			$rows .= "
				<tr valign='top'>
					<th scope='row'>
						<label for='cpcc-input-{$input['id']}'> {$input['label']}</label>
					</th>
					<td>
						<label for='cpcc-input-{$input['id']}'>
							<input type='checkbox' id='cpcc-input-{$id}' name='cpcc_options[{$id}]'{$checked}>
							{$descr}{$note}
						</label>
					</td>
				</tr>
			";
		}
		
	?>
	<div id="cpcc" class="wrap">
		<?php screen_icon(CPCC_SLUG) ?>
		<h2><?php _e('CSS Crush', CPCC_TEXTDOMAIN); ?></h2>

		<form method="post" action="options.php">
		
		<?php settings_fields( 'cpcc-settings-group' ); ?>
			<table class="form-table">
				<tbody>
					<?php echo $rows ?>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>			
		</form>	
	</div>
	<?php
	}	
}

new Codepress_Crush_CSS();

?>